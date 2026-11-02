<?php

namespace App\Services;

use App\Database\Connection;
use App\Models\Contact;
use App\Models\ContactList;
use App\Models\AuditLog;
use ZipArchive;
use SimpleXMLElement;
use Exception;

class ContactImportService
{
    /**
     * Inspect uploaded file and return detected headers, sample rows, and suggested mappings.
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

        // Determine if first row is header
        $hasHeader = $this->detectIfFirstRowIsHeader($rows);
        $headers = [];
        $dataRows = [];

        if ($hasHeader) {
            $headers = array_map('trim', $rows[0]);
            $dataRows = array_slice($rows, 1);
        } else {
            // Generate generic Column 1, Column 2...
            $colCount = count($rows[0]);
            for ($i = 0; $i < $colCount; $i++) {
                $headers[] = 'Column ' . ($i + 1);
            }
            $dataRows = $rows;
        }

        // Auto-detect mappings
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
     * Execute contact import from uploaded file with custom mappings and filters.
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

        // Resolve Destination List
        $listId = null;
        $listName = null;

        $newListName = trim($options['new_list_name'] ?? '');
        if (!empty($newListName)) {
            $listId = ContactList::create([
                'name' => $newListName,
                'description' => 'Created during bulk contact import on ' . date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            $listName = $newListName;
            AuditLog::log('create_contact_list', 'contact_lists', $listId, ['name' => $newListName]);
        } elseif (!empty($options['list_id']) && (int)$options['list_id'] > 0) {
            $listId = (int)$options['list_id'];
            $existingList = ContactList::find($listId);
            $listName = $existingList ? $existingList['name'] : "List #{$listId}";
        }

        // Resolve Column Indexes
        $suggested = $this->suggestMappings($headers, $dataRows);
        $emailCol = isset($options['email_column']) && $options['email_column'] !== 'auto' && $options['email_column'] !== ''
            ? (int)$options['email_column']
            : ($suggested['email_idx'] ?? null);

        $firstNameCol = isset($options['first_name_column']) && $options['first_name_column'] !== 'auto' && $options['first_name_column'] !== ''
            ? (int)$options['first_name_column']
            : ($suggested['first_name_idx'] ?? null);

        $lastNameCol = isset($options['last_name_column']) && $options['last_name_column'] !== 'auto' && $options['last_name_column'] !== ''
            ? (int)$options['last_name_column']
            : ($suggested['last_name_idx'] ?? null);

        $fullNameCol = isset($options['full_name_column']) && $options['full_name_column'] !== 'auto' && $options['full_name_column'] !== ''
            ? (int)$options['full_name_column']
            : ($suggested['full_name_idx'] ?? null);

        $duplicateMode = $options['duplicate_mode'] ?? 'update'; // 'update' or 'skip'
        $skipSuppressed = !isset($options['skip_suppressed']) || (bool)$options['skip_suppressed'];
        $extractEmailRegex = !isset($options['extract_regex']) || (bool)$options['extract_regex'];

        $stats = [
            'total_rows' => count($dataRows),
            'imported' => 0,
            'updated' => 0,
            'skipped_invalid' => 0,
            'skipped_duplicate' => 0,
            'skipped_suppressed' => 0,
            'list_id' => $listId,
            'list_name' => $listName,
            'errors' => [],
        ];

        foreach ($dataRows as $rowIdx => $row) {
            // 1. Resolve raw email candidate
            $rawEmail = '';
            if ($emailCol !== null && isset($row[$emailCol])) {
                $rawEmail = trim((string)$row[$emailCol]);
            } else {
                // If no column specified, search all columns in this row for an email
                foreach ($row as $cell) {
                    if (is_string($cell) && str_contains($cell, '@')) {
                        $rawEmail = trim($cell);
                        break;
                    }
                }
            }

            // 2. Dedicated Email Extraction & Filtering
            $cleanEmail = '';
            if ($extractEmailRegex && preg_match('/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/', $rawEmail, $matches)) {
                $cleanEmail = strtolower($matches[0]);
            } else {
                $cleanEmail = strtolower($rawEmail);
            }

            if (empty($cleanEmail) || !filter_var($cleanEmail, FILTER_VALIDATE_EMAIL)) {
                $stats['skipped_invalid']++;
                if (count($stats['errors']) < 5 && !empty($rawEmail)) {
                    $stats['errors'][] = "Row #" . ($rowIdx + 2) . ": Invalid email format '{$rawEmail}'";
                }
                continue;
            }

            // 3. Suppression Filtering
            if ($skipSuppressed && Contact::isSuppressed($cleanEmail)) {
                $stats['skipped_suppressed']++;
                continue;
            }

            // 4. Dedicated Name Extraction & Normalization
            $firstName = null;
            $lastName = null;

            if ($fullNameCol !== null && isset($row[$fullNameCol]) && trim((string)$row[$fullNameCol]) !== '') {
                $nameParts = preg_split('/\s+/', trim((string)$row[$fullNameCol]), 2);
                $firstName = $nameParts[0] ?? null;
                $lastName = $nameParts[1] ?? null;
            } else {
                if ($firstNameCol !== null && isset($row[$firstNameCol]) && trim((string)$row[$firstNameCol]) !== '') {
                    $firstName = trim((string)$row[$firstNameCol]);
                }
                if ($lastNameCol !== null && isset($row[$lastNameCol]) && trim((string)$row[$lastNameCol]) !== '') {
                    $lastName = trim((string)$row[$lastNameCol]);
                }
            }

            // 5. Existing Contact & Duplicate Handling
            $existing = Contact::findByEmail($cleanEmail);

            if ($existing) {
                if ($duplicateMode === 'skip') {
                    // Check if already in list
                    if ($listId) {
                        Contact::attachToList((int)$existing['id'], $listId);
                    }
                    $stats['skipped_duplicate']++;
                    continue;
                } else {
                    // Update existing contact
                    $contactId = Contact::createOrUpdate($cleanEmail, $firstName, $lastName);
                    if ($listId) {
                        Contact::attachToList($contactId, $listId);
                    }
                    $stats['updated']++;
                }
            } else {
                // New Contact
                $contactId = Contact::createOrUpdate($cleanEmail, $firstName, $lastName, $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1');
                if ($listId) {
                    Contact::attachToList($contactId, $listId);
                }
                $stats['imported']++;
            }
        }

        if ($listId) {
            AuditLog::log('import_contacts', 'contact_lists', $listId, [
                'format' => $parsed['format'],
                'imported' => $stats['imported'],
                'updated' => $stats['updated'],
                'skipped_invalid' => $stats['skipped_invalid'],
                'skipped_duplicate' => $stats['skipped_duplicate'],
                'skipped_suppressed' => $stats['skipped_suppressed'],
            ]);
        }

        return $stats;
    }

    /**
     * Parse file into 2D rows matrix based on detected format.
     */
    public function parseFileToMatrix(string $filePath, string $originalName): array
    {
        $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

        if (in_array($ext, ['xlsx', 'xlsm', 'xltx'])) {
            return [
                'format' => 'xlsx',
                'rows' => $this->parseXlsx($filePath),
            ];
        }

        if ($ext === 'xls') {
            return [
                'format' => 'xls',
                'rows' => $this->parseXls($filePath),
            ];
        }

        if ($ext === 'json') {
            return [
                'format' => 'json',
                'rows' => $this->parseJson($filePath),
            ];
        }

        // CSV, TSV, TXT, or generic text
        return [
            'format' => $ext === 'txt' ? 'txt' : 'csv',
            'rows' => $this->parseCsvOrTxt($filePath),
        ];
    }

    /**
     * Native XLSX parser using PHP's ZipArchive + SimpleXML (Zero external dependencies).
     */
    protected function parseXlsx(string $filePath): array
    {
        $zip = new ZipArchive();
        if ($zip->open($filePath) !== true) {
            throw new Exception("Unable to open Excel (.xlsx) file. File may be corrupted.");
        }

        // 1. Read Shared Strings (xl/sharedStrings.xml)
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

        // 2. Read Worksheet (xl/worksheets/sheet1.xml)
        $sheetXmlContent = $zip->getFromName('xl/worksheets/sheet1.xml');
        if ($sheetXmlContent === false) {
            // Try finding any sheet
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
            throw new Exception("No worksheet found inside Excel (.xlsx) archive.");
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

                if ($cellType === 's') { // shared string index
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
                // Fill any missing columns with empty string
                $filledRow = [];
                for ($c = 0; $c <= $maxColIdx; $c++) {
                    $filledRow[$c] = $rowData[$c] ?? '';
                }

                // Check if row has at least one non-empty value
                $hasData = false;
                foreach ($filledRow as $val) {
                    if ($val !== '') {
                        $hasData = true;
                        break;
                    }
                }
                if ($hasData) {
                    $matrix[] = $filledRow;
                }
            }
        }

        return $matrix;
    }

    /**
     * Parse XLS (Legacy, XML Spreadsheet 2003, or HTML table).
     */
    protected function parseXls(string $filePath): array
    {
        // 1. Check if it's actually an XLSX named as .xls
        try {
            $zip = new ZipArchive();
            if ($zip->open($filePath) === true) {
                $zip->close();
                return $this->parseXlsx($filePath);
            }
        } catch (\Throwable $e) {}

        $content = file_get_contents($filePath);
        if ($content === false || strlen($content) === 0) {
            return [];
        }

        // 2. Check for XML Spreadsheet 2003
        if (str_contains($content, '<?xml') && str_contains($content, 'urn:schemas-microsoft-com:office:spreadsheet')) {
            $xml = @simplexml_load_string($content);
            if ($xml) {
                $xml->registerXPathNamespace('ss', 'urn:schemas-microsoft-com:office:spreadsheet');
                $rows = $xml->xpath('//ss:Worksheet[1]//ss:Row');
                $matrix = [];
                foreach ($rows as $row) {
                    $cells = $row->xpath('ss:Cell');
                    $rowData = [];
                    foreach ($cells as $cell) {
                        $data = $cell->xpath('ss:Data');
                        $rowData[] = isset($data[0]) ? trim((string)$data[0]) : '';
                    }
                    if (!empty(array_filter($rowData, fn($v) => $v !== ''))) {
                        $matrix[] = $rowData;
                    }
                }
                if (!empty($matrix)) {
                    return $matrix;
                }
            }
        }

        // 3. Check for HTML Table (common export from CRMs named as .xls)
        if (stripos($content, '<table') !== false && stripos($content, '<tr>') !== false) {
            preg_match_all('/<tr[^>]*>(.*?)<\/tr>/is', $content, $trMatches);
            if (!empty($trMatches[1])) {
                $matrix = [];
                foreach ($trMatches[1] as $trHtml) {
                    preg_match_all('/<(?:td|th)[^>]*>(.*?)<\/(?:td|th)>/is', $trHtml, $tdMatches);
                    if (!empty($tdMatches[1])) {
                        $rowData = array_map(function ($cellHtml) {
                            return trim(html_entity_decode(strip_tags($cellHtml), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
                        }, $tdMatches[1]);

                        if (!empty(array_filter($rowData, fn($v) => $v !== ''))) {
                            $matrix[] = $rowData;
                        }
                    }
                }
                if (!empty($matrix)) {
                    return $matrix;
                }
            }
        }

        // 4. Fallback: Parse as CSV / Delimited or Regex Email Scan
        return $this->parseCsvOrTxt($filePath);
    }

    /**
     * Parse JSON (Array of objects, array of email strings, or wrapped dictionary).
     */
    protected function parseJson(string $filePath): array
    {
        $content = file_get_contents($filePath);
        if ($content === false) {
            return [];
        }

        // Strip BOM if present
        $content = $this->stripBom($content);

        $json = json_decode($content, true);
        if (!is_array($json)) {
            throw new Exception("Invalid JSON format. Expected JSON array or object.");
        }

        // If wrapped in an object like {"contacts": [...]}, {"data": [...]}, {"subscribers": [...]}
        if (!array_is_list($json)) {
            foreach (['contacts', 'data', 'subscribers', 'users', 'items', 'list'] as $key) {
                if (isset($json[$key]) && is_array($json[$key])) {
                    $json = $json[$key];
                    break;
                }
            }
        }

        if (empty($json) || !is_array($json)) {
            return [];
        }

        // Case A: Array of simple strings (e.g. ["a@b.com", "c@d.com"])
        if (!is_array(reset($json))) {
            $matrix = [['Email']];
            foreach ($json as $item) {
                if (is_scalar($item) && trim((string)$item) !== '') {
                    $matrix[] = [trim((string)$item)];
                }
            }
            return $matrix;
        }

        // Case B: Array of associative objects
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

        $matrix = [$allKeys]; // Header row

        foreach ($json as $item) {
            if (!is_array($item)) {
                continue;
            }
            $row = [];
            foreach ($allKeys as $k) {
                $val = $item[$k] ?? '';
                if (is_array($val)) {
                    $val = json_encode($val);
                }
                $row[] = trim((string)$val);
            }
            $matrix[] = $row;
        }

        return $matrix;
    }

    /**
     * Parse CSV, TSV, or Plain Text list.
     */
    protected function parseCsvOrTxt(string $filePath): array
    {
        $handle = fopen($filePath, 'r');
        if (!$handle) {
            throw new Exception("Unable to open uploaded text file.");
        }

        // Read first chunk to detect BOM and delimiter
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
            if ($line === '') {
                continue;
            }

            $row = str_getcsv($line, $delimiter);
            if (!empty($row)) {
                $row = array_map('trim', $row);
                // Check if row has any non-empty cell
                if (!empty(array_filter($row, fn($c) => $c !== ''))) {
                    $matrix[] = $row;
                }
            }
        }

        fclose($handle);

        // If no delimiter was found or single column with multiple emails, check regex fallback
        if (empty($matrix)) {
            $rawContent = file_get_contents($filePath);
            if (preg_match_all('/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/', $rawContent, $matches)) {
                $matrix = [['Email']];
                foreach (array_unique($matches[0]) as $mEmail) {
                    $matrix[] = [$mEmail];
                }
            }
        }

        return $matrix;
    }

    /**
     * Auto-detect CSV delimiter (comma, semicolon, tab, pipe).
     */
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
            // Check consistency across lines
            if (!empty($counts) && min($counts) > 1 && min($counts) === max($counts)) {
                $scores[$delim] = min($counts) * 10;
            } else {
                $scores[$delim] = array_sum($counts);
            }
        }

        arsort($scores);
        return array_key_first($scores) ?: ',';
    }

    /**
     * Strip UTF-8, UTF-16 BOM prefixes.
     */
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

    /**
     * Convert Excel column letter (A, B, AA, AB) to 0-based integer index.
     */
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

    /**
     * Detect if the first row is a header row or data.
     */
    protected function detectIfFirstRowIsHeader(array $rows): bool
    {
        if (empty($rows)) {
            return false;
        }

        $firstRow = $rows[0];
        $hasEmailKeyword = false;
        $hasActualEmail = false;

        foreach ($firstRow as $cell) {
            $c = strtolower(trim($cell));
            if (in_array($c, ['email', 'e-mail', 'email address', 'email_address', 'mail', 'first name', 'first_name', 'last name', 'last_name', 'name', 'full name'])) {
                $hasEmailKeyword = true;
            }
            if (filter_var($c, FILTER_VALIDATE_EMAIL)) {
                $hasActualEmail = true;
            }
        }

        if ($hasEmailKeyword) {
            return true;
        }
        if ($hasActualEmail) {
            return false;
        }

        // If second row exists and first row values are strings while second has email, it's a header
        if (isset($rows[1])) {
            foreach ($rows[1] as $cell) {
                if (filter_var(trim($cell), FILTER_VALIDATE_EMAIL)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Smart detection of email and name column indexes.
     */
    protected function suggestMappings(array $headers, array $dataRows): array
    {
        $emailIdx = null;
        $firstNameIdx = null;
        $lastNameIdx = null;
        $fullNameIdx = null;

        // 1. Check header keywords
        foreach ($headers as $idx => $header) {
            $h = strtolower(trim($header));

            // Email check
            if ($emailIdx === null && (preg_match('/^(e[-_]?mail|mail|email[-_]?address|contact[-_]?email)$/i', $h) || str_contains($h, 'email'))) {
                $emailIdx = $idx;
            }

            // First Name check
            if ($firstNameIdx === null && preg_match('/^(first[-_]?name|fname|given[-_]?name|first)$/i', $h)) {
                $firstNameIdx = $idx;
            }

            // Last Name check
            if ($lastNameIdx === null && preg_match('/^(last[-_]?name|lname|surname|family[-_]?name|last)$/i', $h)) {
                $lastNameIdx = $idx;
            }

            // Full Name check
            if ($fullNameIdx === null && preg_match('/^(name|full[-_]?name|contact[-_]?name|customer[-_]?name)$/i', $h)) {
                $fullNameIdx = $idx;
            }
        }

        // 2. If email column was not found by header keyword, inspect sample rows
        if ($emailIdx === null && !empty($dataRows)) {
            $colCounts = [];
            foreach (array_slice($dataRows, 0, 15) as $row) {
                foreach ($row as $idx => $val) {
                    if (filter_var(trim((string)$val), FILTER_VALIDATE_EMAIL) || preg_match('/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/', (string)$val)) {
                        $colCounts[$idx] = ($colCounts[$idx] ?? 0) + 1;
                    }
                }
            }
            if (!empty($colCounts)) {
                arsort($colCounts);
                $emailIdx = array_key_first($colCounts);
            }
        }

        // 3. If full name is found and no first/last name found, full name takes precedence
        if ($fullNameIdx !== null && $firstNameIdx === null) {
            $firstNameIdx = null;
        }

        return [
            'email_idx' => $emailIdx,
            'first_name_idx' => $firstNameIdx,
            'last_name_idx' => $lastNameIdx,
            'full_name_idx' => $fullNameIdx,
        ];
    }

    /**
     * Generate downloadable sample template files in CSV, XLSX, JSON, or TXT formats.
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
        $csv = "Email,First Name,Last Name,Company\r\n";
        $csv .= "john.doe@example.com,John,Doe,Acme Corporation\r\n";
        $csv .= "jane.smith@example.org,Jane,Smith,Global Tech\r\n";
        $csv .= "alex.wong@example.net,Alex,Wong,Venture Labs\r\n";
        $csv .= "priya.patel@example.com,Priya,Patel,Innovate Solutions\r\n";

        return [
            'content' => $csv,
            'filename' => 'mailmoll_sample_contacts.csv',
            'mime' => 'text/csv; charset=utf-8',
        ];
    }

    protected function generateSampleJson(): array
    {
        $data = [
            [
                'email' => 'john.doe@example.com',
                'first_name' => 'John',
                'last_name' => 'Doe',
                'company' => 'Acme Corporation',
            ],
            [
                'email' => 'jane.smith@example.org',
                'first_name' => 'Jane',
                'last_name' => 'Smith',
                'company' => 'Global Tech',
            ],
            [
                'email' => 'alex.wong@example.net',
                'first_name' => 'Alex',
                'last_name' => 'Wong',
                'company' => 'Venture Labs',
            ],
            [
                'email' => 'priya.patel@example.com',
                'first_name' => 'Priya',
                'last_name' => 'Patel',
                'company' => 'Innovate Solutions',
            ],
        ];

        return [
            'content' => json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
            'filename' => 'mailmoll_sample_contacts.json',
            'mime' => 'application/json; charset=utf-8',
        ];
    }

    protected function generateSampleTxt(): array
    {
        $txt = "# MailMoll Contact Import Sample List\n"
             . "# Accepted formats: raw email OR email, First Name, Last Name\n"
             . "john.doe@example.com, John, Doe\n"
             . "jane.smith@example.org, Jane, Smith\n"
             . "alex.wong@example.net, Alex, Wong\n"
             . "priya.patel@example.com, Priya, Patel\n"
             . "rahul.sharma@example.in, Rahul, Sharma\n";

        return [
            'content' => $txt,
            'filename' => 'mailmoll_sample_contacts.txt',
            'mime' => 'text/plain; charset=utf-8',
        ];
    }

    protected function generateSampleXlsx(): array
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'sample_xlsx_');
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
  <sheets><sheet name="Contacts" sheetId="1" r:id="rId1" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships"/></sheets>
</workbook>');

            $strings = [
                'Email Address', 'First Name', 'Last Name', 'Company',
                'john.doe@example.com', 'John', 'Doe', 'Acme Corporation',
                'jane.smith@example.org', 'Jane', 'Smith', 'Global Tech',
                'alex.wong@example.net', 'Alex', 'Wong', 'Venture Labs',
                'priya.patel@example.com', 'Priya', 'Patel', 'Innovate Solutions'
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
      <c r="A1" t="s"><v>0</v></c>
      <c r="B1" t="s"><v>1</v></c>
      <c r="C1" t="s"><v>2</v></c>
      <c r="D1" t="s"><v>3</v></c>
    </row>
    <row r="2">
      <c r="A2" t="s"><v>4</v></c>
      <c r="B2" t="s"><v>5</v></c>
      <c r="C2" t="s"><v>6</v></c>
      <c r="D2" t="s"><v>7</v></c>
    </row>
    <row r="3">
      <c r="A3" t="s"><v>8</v></c>
      <c r="B3" t="s"><v>9</v></c>
      <c r="C3" t="s"><v>10</v></c>
      <c r="D3" t="s"><v>11</v></c>
    </row>
    <row r="4">
      <c r="A4" t="s"><v>12</v></c>
      <c r="B4" t="s"><v>13</v></c>
      <c r="C4" t="s"><v>14</v></c>
      <c r="D4" t="s"><v>15</v></c>
    </row>
    <row r="5">
      <c r="A5" t="s"><v>16</v></c>
      <c r="B5" t="s"><v>17</v></c>
      <c r="C5" t="s"><v>18</v></c>
      <c r="D5" t="s"><v>19</v></c>
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
            'filename' => 'mailmoll_sample_contacts.xlsx',
            'mime' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ];
    }
}
