<?php

namespace App\Controllers;

use App\Helpers\View;
use App\Helpers\Session;
use App\Helpers\Response;
use App\Helpers\Validator;
use App\Models\EmailTemplate;
use App\Models\AuditLog;

class TemplateController
{
    public function index(): void
    {
        $templates = EmailTemplate::all('id DESC');
        View::render('templates/index', [
            'title' => 'Email Templates',
            'activeNav' => 'templates',
            'templates' => $templates,
        ]);
    }

    public function create(): void
    {
        View::render('templates/editor', [
            'title' => 'Create Email Template',
            'activeNav' => 'templates',
            'template' => null,
        ]);
    }

    public function store(): void
    {
        $validator = Validator::make($_POST, [
            'name' => 'required|min:3',
            'body_html' => 'required',
        ]);

        if ($validator->fails()) {
            Session::flashOldInput($_POST);
            Session::setFlash('error', $validator->firstError());
            Response::redirect('/templates/new');
        }

        $id = EmailTemplate::create([
            'name' => trim($_POST['name']),
            'subject_default' => trim($_POST['subject_default'] ?? ''),
            'body_html' => $_POST['body_html'],
            'body_plain' => $_POST['body_plain'] ?? strip_tags($_POST['body_html']),
            'tags' => trim($_POST['tags'] ?? ''),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        AuditLog::log('create_template', 'email_templates', $id, ['name' => $_POST['name']]);
        Session::setFlash('success', "Template '{$_POST['name']}' created.");
        Response::redirect('/templates');
    }

    public function edit(int $id): void
    {
        $template = EmailTemplate::find($id);
        if (!$template) {
            Session::setFlash('error', 'Template not found.');
            Response::redirect('/templates');
        }

        View::render('templates/editor', [
            'title' => 'Edit Template - ' . $template['name'],
            'activeNav' => 'templates',
            'template' => $template,
        ]);
    }

    public function update(int $id): void
    {
        $template = EmailTemplate::find($id);
        if (!$template) {
            Session::setFlash('error', 'Template not found.');
            Response::redirect('/templates');
        }

        EmailTemplate::update($id, [
            'name' => trim($_POST['name']),
            'subject_default' => trim($_POST['subject_default'] ?? ''),
            'body_html' => $_POST['body_html'],
            'body_plain' => $_POST['body_plain'] ?? strip_tags($_POST['body_html']),
            'tags' => trim($_POST['tags'] ?? ''),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        AuditLog::log('update_template', 'email_templates', $id, ['name' => $_POST['name']]);
        Session::setFlash('success', "Template '{$_POST['name']}' updated.");
        Response::redirect('/templates');
    }

    public function delete(int $id): void
    {
        $template = EmailTemplate::find($id);
        if ($template) {
            EmailTemplate::delete($id);
            AuditLog::log('delete_template', 'email_templates', $id);
            Session::setFlash('success', "Template deleted.");
        }
        Response::redirect('/templates');
    }

    public function preview(int $id): void
    {
        $template = EmailTemplate::find($id);
        if (!$template) {
            echo "Template not found";
            exit;
        }

        $html = $template['body_html'];
        $dummy = [
            '{{first_name}}' => 'John',
            '{{last_name}}' => 'Doe',
            '{{email}}' => 'john.doe@example.com',
            '{{unsubscribe_url}}' => url('/unsubscribe?preview=1'),
        ];
        $previewHtml = str_replace(array_keys($dummy), array_values($dummy), $html);

        echo $previewHtml;
        exit;
    }
}
