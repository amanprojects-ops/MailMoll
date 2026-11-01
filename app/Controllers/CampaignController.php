<?php

namespace App\Controllers;

use App\Helpers\View;
use App\Helpers\Session;
use App\Helpers\Response;
use App\Helpers\Validator;
use App\Models\Campaign;
use App\Models\SmtpAccount;
use App\Models\SmtpGroup;
use App\Models\ContactList;
use App\Models\EmailTemplate;
use App\Models\AuditLog;
use App\Services\CampaignDispatcherService;
use App\Services\SmtpManagerService;

class CampaignController
{
    private CampaignDispatcherService $dispatcher;
    private SmtpManagerService $smtpManager;

    public function __construct()
    {
        $this->dispatcher = new CampaignDispatcherService();
        $this->smtpManager = new SmtpManagerService();
    }

    public function index(): void
    {
        $campaigns = Campaign::all('id DESC');
        View::render('campaigns/index', [
            'title' => 'Campaigns Management',
            'activeNav' => 'campaigns',
            'campaigns' => $campaigns,
        ]);
    }

    public function create(): void
    {
        $smtpAccounts = SmtpAccount::where('status', 'active');
        $smtpGroups = SmtpGroup::all();
        $contactLists = ContactList::allWithCounts();
        $templates = EmailTemplate::all();

        View::render('campaigns/wizard', [
            'title' => 'New Campaign Wizard',
            'activeNav' => 'campaigns',
            'smtpAccounts' => $smtpAccounts,
            'smtpGroups' => $smtpGroups,
            'contactLists' => $contactLists,
            'templates' => $templates,
        ]);
    }

    public function store(): void
    {
        $validator = Validator::make($_POST, [
            'title' => 'required|min:3',
            'subject' => 'required',
            'sender_name' => 'required',
            'sender_email' => 'required|email',
            'body_html' => 'required',
            'target_lists' => 'required',
        ]);

        if ($validator->fails()) {
            Session::flashOldInput($_POST);
            Session::setFlash('error', $validator->firstError());
            Response::redirect('/campaigns/new');
        }

        $listIds = $_POST['target_lists'] ?? [];
        if (is_string($listIds)) {
            $listIds = explode(',', $listIds);
        }

        $routingType = $_POST['routing_type'] ?? 'group';
        $smtpGroupId = $routingType === 'group' ? (int)($_POST['smtp_group_id'] ?? 0) : null;
        $smtpAccountId = $routingType === 'single_smtp' ? (int)($_POST['smtp_account_id'] ?? 0) : null;

        $scheduledAt = null;
        if (!empty($_POST['schedule_time'])) {
            $scheduledAt = date('Y-m-d H:i:s', strtotime($_POST['schedule_time']));
        }

        $campaignId = Campaign::create([
            'title' => trim($_POST['title']),
            'subject' => trim($_POST['subject']),
            'sender_name' => trim($_POST['sender_name']),
            'sender_email' => trim($_POST['sender_email']),
            'reply_to' => !empty($_POST['reply_to']) ? trim($_POST['reply_to']) : null,
            'template_id' => !empty($_POST['template_id']) ? (int)$_POST['template_id'] : null,
            'routing_type' => $routingType,
            'smtp_group_id' => $smtpGroupId,
            'smtp_account_id' => $smtpAccountId,
            'target_type' => 'lists',
            'target_list_ids' => json_encode($listIds),
            'body_html' => $_POST['body_html'],
            'body_plain' => $_POST['body_plain'] ?? strip_tags($_POST['body_html']),
            'status' => 'draft',
            'scheduled_at' => $scheduledAt,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        AuditLog::log('create_campaign', 'campaigns', $campaignId, ['title' => $_POST['title']]);

        // If user clicked "Launch Now"
        if (isset($_POST['launch_immediately']) && $_POST['launch_immediately'] == '1') {
            try {
                $count = $this->dispatcher->dispatchCampaign($campaignId);
                Session::setFlash('success', "Campaign launched! Queued {$count} personalized messages.");
            } catch (\Throwable $e) {
                Session::setFlash('error', "Campaign saved as draft, but launch failed: " . $e->getMessage());
            }
        } else {
            Session::setFlash('success', "Campaign '{$_POST['title']}' created as draft.");
        }

        Response::redirect('/campaigns');
    }

    public function launch(int $id): void
    {
        try {
            $count = $this->dispatcher->dispatchCampaign($id);
            AuditLog::log('launch_campaign', 'campaigns', $id, ['recipients_queued' => $count]);
            Session::setFlash('success', "Campaign successfully dispatched! {$count} emails queued for processing.");
        } catch (\Throwable $e) {
            Session::setFlash('error', "Launch error: " . $e->getMessage());
        }
        Response::redirect('/campaigns');
    }

    public function pause(int $id): void
    {
        Campaign::update($id, ['status' => 'paused', 'updated_at' => date('Y-m-d H:i:s')]);
        AuditLog::log('pause_campaign', 'campaigns', $id);
        Session::setFlash('success', "Campaign paused.");
        Response::redirect('/campaigns');
    }

    public function resume(int $id): void
    {
        Campaign::update($id, ['status' => 'queued', 'updated_at' => date('Y-m-d H:i:s')]);
        AuditLog::log('resume_campaign', 'campaigns', $id);
        Session::setFlash('success', "Campaign resumed.");
        Response::redirect('/campaigns');
    }

    public function delete(int $id): void
    {
        Campaign::delete($id);
        AuditLog::log('delete_campaign', 'campaigns', $id);
        Session::setFlash('success', "Campaign deleted.");
        Response::redirect('/campaigns');
    }

    public function testSend(): void
    {
        $testEmail = trim($_POST['test_email'] ?? '');
        $subject = trim($_POST['subject'] ?? 'Test Message');
        $html = $_POST['body_html'] ?? '<p>Test email from MailMoll</p>';
        $smtpId = (int)($_POST['smtp_account_id'] ?? 0);

        if (!filter_var($testEmail, FILTER_VALIDATE_EMAIL)) {
            Response::error('Invalid test recipient email.');
        }

        $account = SmtpAccount::find($smtpId);
        if (!$account) {
            // Find first active
            $active = SmtpAccount::where('status', 'active');
            $account = $active[0] ?? null;
        }

        if (!$account) {
            Response::error('No active SMTP account configured to send test.');
        }

        $result = $this->smtpManager->send(
            $account,
            $testEmail,
            'Test Recipient',
            '[TEST] ' . $subject,
            $html,
            strip_tags($html)
        );

        if ($result['success']) {
            Response::success("Test email successfully sent to {$testEmail}!");
        } else {
            Response::error("Test email failed: " . $result['message']);
        }
    }
}
