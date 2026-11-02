<?php

namespace App\Services;

use App\Database\Connection;
use App\Models\SmtpAccount;
use App\Models\SmtpGroup;
use App\Models\AuditLog;
use App\Services\CryptoService;
use ZipArchive;
use SimpleXMLElement;
use Exception;

class SmtpImportService
{
    private CryptoService $crypto;

    public function __construct()
    {
        $this->crypto = new CryptoService();
    }

    /**
     * Inspect uploaded SMTP file and return detected headers, sample rows, and suggested mappings.
     */
    public function inspect(string $filePath, string $originalName): array
    {
        $parsed = $this->parseFileToMatrix($filePath, $originalName);
        $rows = $parsed['rows'];
        $format = $parsed['format'];

        if (empty($rows)) {
            return [
                'success' => false,
                'format' => $format,
                'message' => 'The file appears to be empty or could not be parsed.',
                'headers' => [],
                'sample_rows' => [],
                'total_rows' => 0,
                'suggested' => [],
            ];
        }

        $hasHeader = $this->detectIfFirstRowIsHeader($rows);
        $headers = [];
        $dataRows = [];

        if ($hasHeader) {
            $headers = array_map('trim', $rows[0]);
            $dataRows = array_slice($rows, 1);
        } else {
            $colCount = count($rows[0]);
            for ($i = 0; $i < $colCount; $i++) {
                $headers[] = 'Column ' . ($i + 1);
            }
            $dataRows = $rows;
        }

        $suggested = $this->suggestMappings($headers, $dataRows);

        return [
            'success' => true,
            'format' => $format,
            'has_header' => $hasHeader,
            'headers' => $headers,
            'sample_rows' => array_slice($dataRows, 0, 5),
            'total_rows' => count($dataRows),
            'suggested' => $suggested,
        ];
    }

    /**
     * Execute bulk SMTP import with field mappings, defaults, and optional group assignment.
     */
    public function import(string $filePath, string $originalName, array $options): array
    {
        $parsed = $this->parseFileToMatrix($filePath, $originalName);
        $rows = $parsed['rows'];

        if (empty($rows)) {
            throw new Exception("The uploaded file contains no readable data.");
        }

        $hasHeader = isset($options['has_header']) ? (bool)$options['has_header'] : $this->detectIfFirstRowIsHeader($rows);
        $headers = [];
        $dataRows = [];

        if ($hasHeader) {
            $headers = array_map('trim', $rows[0]);
            $dataRows = array_slice($rows, 1);
        } else {
            $colCount = count($rows[0]);
            for ($i = 0; $i < $colCount; $i++) {
                $headers[] = 'Column ' . ($i + 1);
            }
            $dataRows = $rows;
        }

        if (empty($dataRows)) {
            throw new Exception("No data rows found in the uploaded file.");
        }

        // 1. Resolve Target SMTP Group
        $groupId = null;
        $groupName = null;

        $newGroupName = trim($options['new_group_name'] ?? '');
        if (!empty($newGroupName)) {
            $groupId = SmtpGroup::create([
                'name' => $newGroupName,
                'description' => 'Created during bulk SMTP import on ' . date('Y-m-d H:i:s'),
                'routing_strategy' => 'round_robin',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            $groupName = $newGroupName;
            AuditLog::log('create_smtp_group', 'smtp_groups', $groupId, ['name' => $newGroupName]);
        } elseif (!empty($options['group_id']) && (int)$options['group_id'] > 0) {
            $groupId = (int)$options['group_id'];
            $existingGroup = SmtpGroup::find($groupId);
            $groupName = $existingGroup ? $existingGroup['name'] : "Group #{$groupId}";
        }

        // 2. Resolve Column Indexes
        $suggested = $this->suggestMappings($headers, $dataRows);

        $resolveCol = function ($optKey, $sugKey) use ($options, $suggested) {
            if (isset($options[$optKey]) && $options[$optKey] !== 'auto' && $options[$optKey] !== '') {
                return (int)$options[$optKey];
            }
            return $suggested[$sugKey] ?? null;
        };

        $hostCol = $resolveCol('host_column', 'host_idx');
        $portCol = $resolveCol('port_column', 'port_idx');
        $usernameCol = $resolveCol('username_column', 'username_idx');
        $passwordCol = $resolveCol('password_column', 'password_idx');
        $fromEmailCol = $resolveCol('from_email_column', 'from_email_idx');
        $fromNameCol = $resolveCol('from_name_column', 'from_name_idx');
        $nameCol = $resolveCol('name_column', 'name_idx');
        $encryptionCol = $resolveCol('encryption_column', 'encryption_idx');
        $authTypeCol = $resolveCol('auth_type_column', 'auth_type_idx');
        $dailyLimitCol = $resolveCol('daily_limit_column', 'daily_limit_idx');
        $hourlyLimitCol = $resolveCol('hourly_limit_column', 'hourly_limit_idx');

        $defaultPort = !empty($options['default_port']) ? (int)$options['default_port'] : 587;
        $defaultEncryption = !empty($options['default_encryption']) ? strtolower($options['default_encryption']) : 'tls';
        $defaultDailyLimit = isset($options['default_daily_limit']) ? (int)$options['default_daily_limit'] : 500;
        $defaultHourlyLimit = isset($options['default_hourly_limit']) ? (int)$options['default_hourly_limit'] : 100;
        $defaultAuthType = !empty($options['default_auth_type']) ? strtoupper($options['default_auth_type']) : 'LOGIN';
        $duplicateMode = $options['duplicate_mode'] ?? 'update'; // 'update' or 'skip'

        $stats = [
            'total_rows' => count($dataRows),
            'imported' => 0,
            'updated' => 0,
            'skipped_invalid' => 0,
            'skipped_duplicate' => 0,
            'group_id' => $groupId,
            'group_name' => $groupName,
            'errors' => [],
        ];

        foreach ($dataRows as $rowIdx => $row) {
            $lineNum = $rowIdx + ($hasHeader ? 2 : 1);

            // Handle colon/pipe separated single line in text files: host:port:user:pass:from_email
            if (count($row) === 1 && (str_contains($row[0], ':') || str_contains($row[0], '|'))) {
                $delim = str_contains($row[0], '|') ? '|' : ':';
                $parts = explode($delim, $row[0]);
                if (count($parts) >= 4) {
                    $row = array_map('trim', $parts);
                    // Synthesize indices for colon/pipe syntax
                    $hostVal = $row[0] ?? '';
                    $portVal = $row[1] ?? $defaultPort;
                    $usernameVal = $row[2] ?? '';
                    $passwordVal = $row[3] ?? '';
                    $fromEmailVal = $row[4] ?? '';
                }
            }

            $hostVal = isset($hostVal) ? $hostVal : ($hostCol !== null && isset($row[$hostCol]) ? trim((string)$row[$hostCol]) : '');
            $usernameVal = isset($usernameVal) ? $usernameVal : ($usernameCol !== null && isset($row[$usernameCol]) ? trim((string)$row[$usernameCol]) : '');
            $passwordVal = isset($passwordVal) ? $passwordVal : ($passwordCol !== null && isset($row[$passwordCol]) ? trim((string)$row[$passwordCol]) : '');

            // Validation
            if (empty($hostVal) || empty($usernameVal) || empty($passwordVal)) {
                $stats['skipped_invalid']++;
                if (count($stats['errors']) < 5) {
                    $stats['errors'][] = "Row #{$lineNum}: Missing host, username, or password.";
                }
                unset($hostVal, $usernameVal, $passwordVal, $fromEmailVal);
                continue;
            }

            // Clean host
            $hostVal = preg_replace('#^https?://#i', '', $hostVal);
            $hostVal = trim($hostVal, '/');

            // Resolve Port
            $portVal = isset($portVal) ? $portVal : ($portCol !== null && isset($row[$portCol]) && is_numeric(trim((string)$row[$portCol])) ? (int)trim((string)$row[$portCol]) : $defaultPort);
            if ($portVal <= 0) {
                $portVal = $defaultPort;
            }

            // Resolve Encryption
            $encryptionVal = $encryptionCol !== null && isset($row[$encryptionCol]) && !empty(trim((string)$row[$encryptionCol]))
                ? strtolower(trim((string)$row[$encryptionCol]))
                : null;

            if (!$encryptionVal) {
                $encryptionVal = ($portVal === 465) ? 'ssl' : (($portVal === 587) ? 'tls' : $defaultEncryption);
            }
            if (!in_array($encryptionVal, ['tls', 'ssl', 'starttls', 'none'], true)) {
                $encryptionVal = 'tls';
            }

            // Resolve Auth Type
            $authTypeVal = $authTypeCol !== null && isset($row[$authTypeCol]) && !empty(trim((string)$row[$authTypeCol]))
                ? strtoupper(trim((string)$row[$authTypeCol]))
                : $defaultAuthType;
            if (!in_array($authTypeVal, ['LOGIN', 'PLAIN', 'CRAM-MD5'], true)) {
                $authTypeVal = 'LOGIN';
            }

            // Resolve From Email
            $fromEmailVal = isset($fromEmailVal) ? $fromEmailVal : ($fromEmailCol !== null && isset($row[$fromEmailCol]) ? trim((string)$row[$fromEmailCol]) : '');
            if (empty($fromEmailVal) || !filter_var($fromEmailVal, FILTER_VALIDATE_EMAIL)) {
                if (filter_var($usernameVal, FILTER_VALIDATE_EMAIL)) {
                    $fromEmailVal = $usernameVal;
                } else {
                    $fromEmailVal = 'noreply@' . preg_replace('/^smtp\./i', '', $hostVal);
                }
            }

            // Resolve From Name
            $fromNameVal = $fromNameCol !== null && isset($row[$fromNameCol]) && !empty(trim((string)$row[$fromNameCol]))
                ? trim((string)$row[$fromNameCol])
                : null;

            if (empty($fromNameVal)) {
                $fromNameVal = 'Sender ' . ucfirst(explode('@', $fromEmailVal)[0] ?? 'Relay');
            }

            // Resolve Account Display Name
            $nameVal = $nameCol !== null && isset($row[$nameCol]) && !empty(trim((string)$row[$nameCol]))
                ? trim((string)$row[$nameCol])
                : null;

            if (empty($nameVal)) {
                $nameVal = "{$hostVal} ({$usernameVal})";
            }

            // Resolve Limits
            $dailyLimitVal = $dailyLimitCol !== null && isset($row[$dailyLimitCol]) && is_numeric(trim((string)$row[$dailyLimitCol]))
                ? (int)trim((string)$row[$dailyLimitCol])
                : $defaultDailyLimit;

            $hourlyLimitVal = $hourlyLimitCol !== null && isset($row[$hourlyLimitCol]) && is_numeric(trim((string)$row[$hourlyLimitCol]))
                ? (int)trim((string)$row[$hourlyLimitCol])
                : $defaultHourlyLimit;

            // Encrypt Password
            $encryptedPassword = $this->crypto->encrypt($passwordVal);

            // Check if duplicate account exists
            $existing = SmtpAccount::firstWhere('username', $usernameVal);
            if ($existing && $existing['host'] === $hostVal) {
                if ($duplicateMode === 'skip') {
                    $stats['skipped_duplicate']++;
                    if ($groupId) {
                        SmtpGroup::addMember($groupId, (int)$existing['id']);
                    }
                    unset($hostVal, $usernameVal, $passwordVal, $fromEmailVal);
                    continue;
                } else {
                    // Update existing
                    SmtpAccount::update((int)$existing['id'], [
                        'name' => $nameVal,
                        'port' => $portVal,
                        'encryption' => $encryptionVal,
                        'auth_type' => $authTypeVal,
                        'password_encrypted' => $encryptedPassword,
                        'from_email' => $fromEmailVal,
                        'from_name' => $fromNameVal,
                        'daily_limit' => $dailyLimitVal,
                        'hourly_limit' => $hourlyLimitVal,
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
                    $accountId = (int)$existing['id'];
                    $stats['updated']++;
                }
            } else {
                // Create new
                $accountId = SmtpAccount::create([
                    'name' => $nameVal,
                    'provider' => $this->detectProvider($hostVal),
                    'host' => $hostVal,
                    'port' => $portVal,
                    'encryption' => $encryptionVal,
                    'auth_type' => $authTypeVal,
                    'username' => $usernameVal,
                    'password_encrypted' => $encryptedPassword,
                    'from_email' => $fromEmailVal,
                    'from_name' => $fromNameVal,
                    'daily_limit' => $dailyLimitVal,
                    'hourly_limit' => $hourlyLimitVal,
                    'priority' => 10,
                    'weight' => 1,
                    'status' => 'active',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
                $stats['imported']++;
            }

            // Link to SMTP Group if set
            if ($groupId && $accountId) {
                SmtpGroup::addMember($groupId, $accountId);
            }

            unset($hostVal, $usernameVal, $passwordVal, $fromEmailVal);
        }

        if ($groupId) {
            AuditLog::log('import_smtp_accounts', 'smtp_groups', $groupId, [
                'imported' => $stats['imported'],
                'updated' => $stats['updated'],
                'skipped_invalid' => $stats['skipped_invalid'],
                'skipped_duplicate' => $stats['skipped_duplicate'],
            ]);
        }

        return $stats;
    }

    /**
     * Generate downloadable sample SMTP templates in CSV, XLSX, JSON, or TXT formats.
     */
    public function generateSample(string $format): array
    {
        $format = strtolower(trim($format));

        switch ($format) {
            case 'xlsx':
            case 'excel':
                return $this->generateSampleXlsx();

            case 'json':
                return $this->generateSampleJson();

            case 'txt':
                return $this->generateSampleTxt();

            case 'csv':
            default:
                return $this->generateSampleCsv();
        }
    }

    protected function generateSampleCsv(): array
    {
        $csv = "Name,Host,Port,Encryption,Auth Type,Username,Password,From Email,From Name,Daily Limit,Hourly Limit\r\n";
        $csv .= "SendGrid Relay,smtp.sendgrid.net,587,tls,LOGIN,apikey,SG.sample_api_key_12345,alerts@mybusiness.com,Acme Alerts,1000,200\r\n";
        $csv .= "Google Workspace,smtp.gmail.com,465,ssl,LOGIN,marketing@domain.com,app_password_here,marketing@domain.com,Marketing Team,2000,500\r\n";
        $csv .= "Custom Dedicated,smtp.earnbro.site,465,ssl,LOGIN,info@earnbro.site,my_strong_password,info@earnbro.site,EarnBro Support,500,100\r\n";

        return [
            'content' => $csv,
            'filename' => 'mailmoll_sample_smtp.csv',
            'mime' => 'text/csv; charset=utf-8',
        ];
    }

    protected function generateSampleJson(): array
    {
        $data = [
            [
                'name' => 'SendGrid Relay',
                'host' => 'smtp.sendgrid.net',
                'port' => 587,
                'encryption' => 'tls',
                'auth_type' => 'LOGIN',
                'username' => 'apikey',
                'password' => 'SG.sample_api_key_12345',
                'from_email' => 'alerts@mybusiness.com',
                'from_name' => 'Acme Alerts',
                'daily_limit' => 1000,
                'hourly_limit' => 200,
            ],
            [
                'name' => 'Google Workspace',
                'host' => 'smtp.gmail.com',
                'port' => 465,
                'encryption' => 'ssl',
                'auth_type' => 'LOGIN',
                'username' => 'marketing@domain.com',
                'password' => 'app_password_here',
                'from_email' => 'marketing@domain.com',
                'from_name' => 'Marketing Team',
                'daily_limit' => 2000,
                'hourly_limit' => 500,
            ],
            [
                'name' => 'Custom Dedicated',
                'host' => 'smtp.earnbro.site',
                'port' => 465,
                'encryption' => 'ssl',
                'auth_type' => 'LOGIN',
                'username' => 'info@earnbro.site',
                'password' => 'my_strong_password',
                'from_email' => 'info@earnbro.site',
                'from_name' => 'EarnBro Support',
                'daily_limit' => 500,
                'hourly_limit' => 100,
            ],
        ];

        return [
            'content' => json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
            'filename' => 'mailmoll_sample_smtp.json',
            'mime' => 'application/json; charset=utf-8',
        ];
    }

    protected function generateSampleTxt(): array
    {
        $txt = "# MailMoll SMTP Import Sample List\n"
             . "# Supported format 1: Comma delimited (Name, Host, Port, Encryption, Auth, User, Pass, FromEmail, FromName)\n"
             . "SendGrid Relay,smtp.sendgrid.net,587,tls,LOGIN,apikey,SG.sample_api_key_12345,alerts@mybusiness.com,Acme Alerts\n"
             . "# Supported format 2: Colon delimited (Host:Port:User:Pass:FromEmail)\n"
             . "smtp.gmail.com:465:marketing@domain.com:app_password_here:marketing@domain.com\n"
             . "smtp.earnbro.site:465:info@earnbro.site:my_strong_password:info@earnbro.site\n";

        return [
            'content' => $txt,
            'filename' => 'mailmoll_sample_smtp.txt',
            'mime' => 'text/plain; charset=utf-8',
        ];
    }

    protected function generateSampleXlsx(): array
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'sample_smtp_xlsx_');
        $zip = new ZipArchive();
        if ($zip->open($tempFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
            $zip->addFromString('[Content_Types].xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
  <Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
  <Default Extension="xml" ContentType="application/xml"/>
  <Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>
  <Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>
  <Override PartName="/xl/sharedStrings.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sharedString+xml"/>
</Types>');

            $zip->addFromString('_rels/.rels', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>
</Relationships>');

            $zip->addFromString('xl/_rels/workbook.xml.rels', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>
  <Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/sharedStrings" Target="sharedStrings.xml"/>
</Relationships>');

            $zip->addFromString('xl/workbook.xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
  <sheets><sheet name="SMTP Servers" sheetId="1" r:id="rId1" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships"/></sheets>
</workbook>');

            $strings = [
                'Name', 'Host', 'Port', 'Encryption', 'Auth Type', 'Username', 'Password', 'From Email', 'From Name', 'Daily Limit', 'Hourly Limit',
                'SendGrid Relay', 'smtp.sendgrid.net', '587', 'tls', 'LOGIN', 'apikey', 'SG.sample_key_12345', 'alerts@mybusiness.com', 'Acme Alerts', '1000', '200',
                'Google Workspace', 'smtp.gmail.com', '465', 'ssl', 'LOGIN', 'user@domain.com', 'app_password_here', 'user@domain.com', 'Marketing Team', '2000', '500',
                'Custom Dedicated', 'smtp.earnbro.site', '465', 'ssl', 'LOGIN', 'info@earnbro.site', 'my_strong_password', 'info@earnbro.site', 'EarnBro Support', '500', '100'
            ];

            $sstXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><sst xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" count="' . count($strings) . '" uniqueCount="' . count($strings) . '">';
            foreach ($strings as $str) {
                $sstXml .= '<si><t>' . htmlspecialchars($str, ENT_XML1, 'UTF-8') . '</t></si>';
            }
            $sstXml .= '</sst>';
            $zip->addFromString('xl/sharedStrings.xml', $sstXml);

            $sheetXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
  <sheetData>
    <row r="1">
      <c r="A1" t="s"><v>0</v></c><c r="B1" t="s"><v>1</v></c><c r="C1" t="s"><v>2</v></c><c r="D1" t="s"><v>3</v></c><c r="E1" t="s"><v>4</v></c>
      <c r="F1" t="s"><v>5</v></c><c r="G1" t="s"><v>6</v></c><c r="H1" t="s"><v>7</v></c><c r="I1" t="s"><v>8</v></c><c r="J1" t="s"><v>9</v></c><c r="K1" t="s"><v>10</v></c>
    </row>
    <row r="2">
      <c r="A2" t="s"><v>11</v></c><c r="B2" t="s"><v>12</v></c><c r="C2" t="s"><v>13</v></c><c r="D2" t="s"><v>14</v></c><c r="E2" t="s"><v>15</v></c>
      <c r="F2" t="s"><v>16</v></c><c r="G2" t="s"><v>17</v></c><c r="H2" t="s"><v>18</v></c><c r="I2" t="s"><v>19</v></c><c r="J2" t="s"><v>20</v></c><c r="K2" t="s"><v>21</v></c>
    </row>
    <row r="3">
      <c r="A3" t="s"><v>22</v></c><c r="B3" t="s"><v>23</v></c><c r="C3" t="s"><v>24</v></c><c r="D3" t="s"><v>25</v></c><c r="E3" t="s"><v>26</v></c>
      <c r="F3" t="s"><v>27</v></c><c r="G3" t="s"><v>28</v></c><c r="H3" t="s"><v>29</v></c><c r="I3" t="s"><v>30</v></c><c r="J3" t="s"><v>31</v></c><c r="K3" t="s"><v>32</v></c>
    </row>
    <row r="4">
      <c r="A4" t="s"><v>33</v></c><c r="B4" t="s"><v>34</v></c><c r="C4" t="s"><v>35</v></c><c r="D4" t="s"><v>36</v></c><c r="E4" t="s"><v>37</v></c>
      <c r="F4" t="s"><v>38</v></c><c r="G4" t="s"><v>39</v></c><c r="H4" t="s"><v>40</v></c><c r="I4" t="s"><v>41</v></c><c r="J4" t="s"><v>42</v></c><c r="K4" t="s"><v>43</v></c>
    </row>
  </sheetData>
</worksheet>';
            $zip->addFromString('xl/worksheets/sheet1.xml', $sheetXml);
            $zip->close();
        }

        $content = file_get_contents($tempFile);
        @unlink($tempFile);

        return [
            'content' => $content,
            'filename' => 'mailmoll_sample_smtp.xlsx',
            'mime' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ];
    }

    /**
     * Parse file into matrix.
     */
    protected function parseFileToMatrix(string $filePath, string $originalName): array
    {
        $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

        if (in_array($ext, ['xlsx', 'xlsm', 'xltx'])) {
            return [
                'format' => 'xlsx',
                'rows' => $this->parseXlsx($filePath),
            ];
        }

        if ($ext === 'json') {
            return [
                'format' => 'json',
                'rows' => $this->parseJson($filePath),
            ];
        }

        return [
            'format' => $ext === 'txt' ? 'txt' : 'csv',
            'rows' => $this->parseCsvOrTxt($filePath),
        ];
    }

    protected function parseXlsx(string $filePath): array
    {
        $zip = new ZipArchive();
        if ($zip->open($filePath) !== true) {
            throw new Exception("Unable to open Excel (.xlsx) file.");
        }

        $sharedStrings = [];
        $stringsXmlContent = $zip->getFromName('xl/sharedStrings.xml');
        if ($stringsXmlContent !== false) {
            $xml = @simplexml_load_string($stringsXmlContent);
            if ($xml && isset($xml->si)) {
                foreach ($xml->si as $si) {
                    if (isset($si->t)) {
                        $sharedStrings[] = (string)$si->t;
                    } elseif (isset($si->r)) {
                        $fullText = '';
                        foreach ($si->r as $r) {
                            $fullText .= (string)($r->t ?? '');
                        }
                        $sharedStrings[] = $fullText;
                    } else {
                        $sharedStrings[] = '';
                    }
                }
            }
        }

        $sheetXmlContent = $zip->getFromName('xl/worksheets/sheet1.xml');
        if ($sheetXmlContent === false) {
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $stat = $zip->statIndex($i);
                if (preg_match('/xl\/worksheets\/sheet\d+\.xml/i', $stat['name'])) {
                    $sheetXmlContent = $zip->getFromIndex($i);
                    break;
                }
            }
        }

        $zip->close();

        if ($sheetXmlContent === false) {
            throw new Exception("No worksheet found inside Excel archive.");
        }

        $sheetXml = @simplexml_load_string($sheetXmlContent);
        if (!$sheetXml || !isset($sheetXml->sheetData->row)) {
            return [];
        }

        $matrix = [];
        foreach ($sheetXml->sheetData->row as $row) {
            $rowData = [];
            $maxColIdx = -1;

            foreach ($row->c as $cell) {
                $attr = $cell->attributes();
                $cellRef = (string)$attr['r'];
                preg_match('/^([A-Z]+)(\d+)$/', $cellRef, $matches);
                $colLetters = $matches[1] ?? 'A';
                $colIdx = $this->colLettersToIndex($colLetters);

                $cellType = (string)($attr['t'] ?? '');
                $val = '';

                if ($cellType === 's') {
                    $idx = (int)$cell->v;
                    $val = $sharedStrings[$idx] ?? '';
                } elseif ($cellType === 'inlineStr' && isset($cell->is->t)) {
                    $val = (string)$cell->is->t;
                } else {
                    $val = isset($cell->v) ? (string)$cell->v : '';
                }

                $rowData[$colIdx] = trim($val);
                if ($colIdx > $maxColIdx) {
                    $maxColIdx = $colIdx;
                }
            }

            if ($maxColIdx >= 0) {
                $filledRow = [];
                for ($c = 0; $c <= $maxColIdx; $c++) {
                    $filledRow[$c] = $rowData[$c] ?? '';
                }
                if (!empty(array_filter($filledRow, fn($v) => $v !== ''))) {
                    $matrix[] = $filledRow;
                }
            }
        }

        return $matrix;
    }

    protected function parseJson(string $filePath): array
    {
        $content = file_get_contents($filePath);
        if ($content === false) {
            return [];
        }
        $content = $this->stripBom($content);

        $json = json_decode($content, true);
        if (!is_array($json)) {
            throw new Exception("Invalid JSON format.");
        }

        if (!array_is_list($json)) {
            foreach (['smtp', 'accounts', 'servers', 'data', 'items'] as $k) {
                if (isset($json[$k]) && is_array($json[$k])) {
                    $json = $json[$k];
                    break;
                }
            }
        }

        if (empty($json) || !is_array($json)) {
            return [];
        }

        $allKeys = [];
        foreach ($json as $item) {
            if (is_array($item)) {
                foreach (array_keys($item) as $k) {
                    if (!in_array((string)$k, $allKeys, true)) {
                        $allKeys[] = (string)$k;
                    }
                }
            }
        }

        $matrix = [$allKeys];
        foreach ($json as $item) {
            if (!is_array($item)) continue;
            $row = [];
            foreach ($allKeys as $k) {
                $row[] = trim((string)($item[$k] ?? ''));
            }
            $matrix[] = $row;
        }

        return $matrix;
    }

    protected function parseCsvOrTxt(string $filePath): array
    {
        $handle = fopen($filePath, 'r');
        if (!$handle) {
            throw new Exception("Unable to open text file.");
        }

        $sample = fread($handle, 4096);
        rewind($handle);

        $sample = $this->stripBom($sample);
        $delimiter = $this->detectDelimiter($sample);

        $matrix = [];
        $lineNum = 0;

        while (($line = fgets($handle)) !== false) {
            $lineNum++;
            if ($lineNum === 1) {
                $line = $this->stripBom($line);
            }

            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }

            $row = str_getcsv($line, $delimiter);
            if (!empty($row)) {
                $row = array_map('trim', $row);
                if (!empty(array_filter($row, fn($c) => $c !== ''))) {
                    $matrix[] = $row;
                }
            }
        }

        fclose($handle);
        return $matrix;
    }

    protected function detectDelimiter(string $sample): string
    {
        $delimiters = [',', ';', "\t", '|'];
        $lines = preg_split('/\r\n|\r|\n/', trim($sample));
        $lines = array_slice(array_filter($lines), 0, 5);

        if (empty($lines)) {
            return ',';
        }

        $scores = [',' => 0, ';' => 0, "\t" => 0, '|' => 0];
        foreach ($delimiters as $delim) {
            $counts = [];
            foreach ($lines as $line) {
                $counts[] = count(str_getcsv($line, $delim));
            }
            if (!empty($counts) && min($counts) > 1 && min($counts) === max($counts)) {
                $scores[$delim] = min($counts) * 10;
            } else {
                $scores[$delim] = array_sum($counts);
            }
        }

        arsort($scores);
        return array_key_first($scores) ?: ',';
    }

    protected function stripBom(string $text): string
    {
        if (str_starts_with($text, "\xEF\xBB\xBF")) {
            return substr($text, 3);
        }
        if (str_starts_with($text, "\xFF\xFE") || str_starts_with($text, "\xFE\xFF")) {
            return substr($text, 2);
        }
        return $text;
    }

    protected function colLettersToIndex(string $letters): int
    {
        $letters = strtoupper($letters);
        $len = strlen($letters);
        $idx = 0;
        for ($i = 0; $i < $len; $i++) {
            $idx = $idx * 26 + (ord($letters[$i]) - ord('A') + 1);
        }
        return $idx - 1;
    }

    protected function detectIfFirstRowIsHeader(array $rows): bool
    {
        if (empty($rows)) {
            return false;
        }

        $firstRow = $rows[0];
        $keywords = ['host', 'server', 'smtp', 'port', 'username', 'user', 'password', 'pass', 'from', 'from_email', 'auth'];

        foreach ($firstRow as $cell) {
            $c = strtolower(trim($cell));
            if (in_array($c, $keywords, true)) {
                return true;
            }
        }

        return false;
    }

    protected function suggestMappings(array $headers, array $dataRows): array
    {
        $map = [
            'name_idx' => null,
            'host_idx' => null,
            'port_idx' => null,
            'encryption_idx' => null,
            'auth_type_idx' => null,
            'username_idx' => null,
            'password_idx' => null,
            'from_email_idx' => null,
            'from_name_idx' => null,
            'daily_limit_idx' => null,
            'hourly_limit_idx' => null,
        ];

        foreach ($headers as $idx => $h) {
            $header = strtolower(trim($h));

            if ($map['host_idx'] === null && preg_match('/^(host|server|smtp[-_]?host|smtp[-_]?server)$/i', $header)) {
                $map['host_idx'] = $idx;
            } elseif ($map['port_idx'] === null && preg_match('/^(port|smtp[-_]?port)$/i', $header)) {
                $map['port_idx'] = $idx;
            } elseif ($map['username_idx'] === null && preg_match('/^(username|user|smtp[-_]?user|smtp[-_]?username|login)$/i', $header)) {
                $map['username_idx'] = $idx;
            } elseif ($map['password_idx'] === null && preg_match('/^(password|pass|smtp[-_]?password|smtp[-_]?pass|secret)$/i', $header)) {
                $map['password_idx'] = $idx;
            } elseif ($map['from_email_idx'] === null && preg_match('/^(from[-_]?email|sender[-_]?email|from|sender)$/i', $header)) {
                $map['from_email_idx'] = $idx;
            } elseif ($map['from_name_idx'] === null && preg_match('/^(from[-_]?name|sender[-_]?name)$/i', $header)) {
                $map['from_name_idx'] = $idx;
            } elseif ($map['name_idx'] === null && preg_match('/^(name|account[-_]?name|title)$/i', $header)) {
                $map['name_idx'] = $idx;
            } elseif ($map['encryption_idx'] === null && preg_match('/^(encryption|security|ssl|tls)$/i', $header)) {
                $map['encryption_idx'] = $idx;
            } elseif ($map['auth_type_idx'] === null && preg_match('/^(auth[-_]?type|auth|authentication)$/i', $header)) {
                $map['auth_type_idx'] = $idx;
            } elseif ($map['daily_limit_idx'] === null && preg_match('/^(daily[-_]?limit|daily)$/i', $header)) {
                $map['daily_limit_idx'] = $idx;
            } elseif ($map['hourly_limit_idx'] === null && preg_match('/^(hourly[-_]?limit|hourly)$/i', $header)) {
                $map['hourly_limit_idx'] = $idx;
            }
        }

        // Secondary row inspection fallback
        if ($map['host_idx'] === null && !empty($dataRows)) {
            foreach ($dataRows[0] as $idx => $val) {
                if (preg_match('/^(smtp\.|mail\.|[a-z0-9-]+\.[a-z]{2,})/i', $val)) {
                    $map['host_idx'] = $idx;
                    break;
                }
            }
        }

        return $map;
    }

    protected function detectProvider(string $host): string
    {
        $h = strtolower($host);
        if (str_contains($h, 'gmail') || str_contains($h, 'google')) return 'gmail';
        if (str_contains($h, 'sendgrid')) return 'sendgrid';
        if (str_contains($h, 'mailgun')) return 'mailgun';
        if (str_contains($h, 'amazonaws') || str_contains($h, 'ses')) return 'ses';
        if (str_contains($h, 'brevo') || str_contains($h, 'sendinblue')) return 'brevo';
        if (str_contains($h, 'postmark')) return 'postmark';
        return 'generic';
    }
}
