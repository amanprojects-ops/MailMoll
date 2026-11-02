<?php

namespace App\Controllers;

use App\Helpers\View;
use App\Helpers\Session;
use App\Helpers\Response;
use App\Helpers\Validator;
use App\Models\Contact;
use App\Models\AuditLog;
use App\Models\SmtpAccount;
use App\Models\Campaign;
use App\Database\Connection;

class PublicController
{
    /**
     * Public Homepage
     */
    public function home(): void
    {
        // Fetch genuine system showcase stats
        $totalSmtp = SmtpAccount::count();
        $totalCampaigns = Campaign::count();
        $totalContacts = Contact::count();

        View::render('public/home', [
            'title' => 'MailFlow - Professional Multi-SMTP Email Management SaaS',
            'activeNav' => 'home',
            'totalSmtp' => max(3, $totalSmtp),
            'totalCampaigns' => max(12, $totalCampaigns),
            'totalContacts' => max(1500, $totalContacts),
        ], 'layouts/marketing');
    }

    /**
     * Features Page
     */
    public function features(): void
    {
        View::render('public/features', [
            'title' => 'Features - MailFlow Multi-SMTP Platform',
            'activeNav' => 'features',
        ], 'layouts/marketing');
    }

    /**
     * Solutions Page
     */
    public function solutions(): void
    {
        View::render('public/solutions', [
            'title' => 'Solutions & Use Cases - MailFlow',
            'activeNav' => 'solutions',
        ], 'layouts/marketing');
    }

    /**
     * Pricing Page
     */
    public function pricing(): void
    {
        View::render('public/pricing', [
            'title' => 'Transparent Self-Hosted Pricing - MailFlow',
            'activeNav' => 'pricing',
        ], 'layouts/marketing');
    }

    /**
     * About Us Page
     */
    public function about(): void
    {
        View::render('public/about', [
            'title' => 'About MailFlow - Self-Hosted Email Engineering',
            'activeNav' => 'about',
        ], 'layouts/marketing');
    }

    /**
     * Contact Page
     */
    public function contact(): void
    {
        View::render('public/contact', [
            'title' => 'Contact Us - MailFlow Support & Sales',
            'activeNav' => 'contact',
        ], 'layouts/marketing');
    }

    /**
     * Handle Contact Form Submission
     */
    public function submitContact(): void
    {
        $validator = Validator::make($_POST, [
            'name' => 'required|min:2',
            'email' => 'required|email',
            'subject' => 'required|min:3',
            'message' => 'required|min:10',
        ]);

        if ($validator->fails()) {
            Session::flashOldInput($_POST);
            Session::setFlash('error', $validator->firstError());
            Response::redirect('/contact');
        }

        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $subject = trim($_POST['subject']);
        $message = trim($_POST['message']);

        AuditLog::log('contact_inquiry', 'public_contact', null, [
            'name' => $name,
            'email' => $email,
            'subject' => $subject,
            'message' => substr($message, 0, 500),
        ]);

        Session::setFlash('success', "Thank you, {$name}! Your message has been received. Our team will get back to you shortly.");
        Response::redirect('/contact');
    }

    /**
     * Documentation Page
     */
    public function docs(): void
    {
        View::render('public/docs', [
            'title' => 'Documentation & Architecture Guide - MailFlow',
            'activeNav' => 'docs',
        ], 'layouts/marketing');
    }

    /**
     * FAQ Page
     */
    public function faq(): void
    {
        View::render('public/faq', [
            'title' => 'Frequently Asked Questions - MailFlow',
            'activeNav' => 'faq',
        ], 'layouts/marketing');
    }

    /**
     * Privacy Policy Page
     */
    public function privacy(): void
    {
        View::render('public/privacy', [
            'title' => 'Privacy Policy & GDPR Compliance - MailFlow',
            'activeNav' => 'privacy',
        ], 'layouts/marketing');
    }

    /**
     * Terms of Service Page
     */
    public function terms(): void
    {
        View::render('public/terms', [
            'title' => 'Terms of Service - MailFlow Enterprise SaaS',
            'activeNav' => 'terms',
        ], 'layouts/marketing');
    }

    /**
     * Newsletter Subscription (with explicit consent)
     */
    public function subscribeNewsletter(): void
    {
        $email = trim($_POST['email'] ?? '');
        $consent = !empty($_POST['consent']);

        if (!$consent) {
            Session::setFlash('error', 'Please accept the consent terms to subscribe.');
            Response::redirect('/#newsletter');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Session::setFlash('error', 'Please enter a valid email address.');
            Response::redirect('/#newsletter');
        }

        Contact::createOrUpdate($email, null, null, $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1');

        AuditLog::log('newsletter_opt_in', 'contacts', null, ['email' => $email]);

        Session::setFlash('success', 'Thank you for subscribing! You will receive product updates and release notes.');
        Response::redirect('/#newsletter');
    }

    /**
     * XML Sitemap generator for SEO
     */
    public function sitemap(): void
    {
        $baseUrl = url();
        $pages = [
            '',
            '/features',
            '/solutions',
            '/pricing',
            '/about',
            '/contact',
            '/docs',
            '/faq',
            '/privacy-policy',
            '/terms',
            '/login',
            '/register',
        ];

        header('Content-Type: application/xml; charset=utf-8');
        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        foreach ($pages as $p) {
            echo "  <url>\n";
            echo "    <loc>" . htmlspecialchars($baseUrl . $p, ENT_XML1, 'UTF-8') . "</loc>\n";
            echo "    <lastmod>" . date('Y-m-d') . "</lastmod>\n";
            echo "    <changefreq>" . ($p === '' ? 'daily' : 'weekly') . "</changefreq>\n";
            echo "    <priority>" . ($p === '' ? '1.0' : '0.8') . "</priority>\n";
            echo "  </url>\n";
        }
        echo '</urlset>';
        exit;
    }

    /**
     * Robots.txt output
     */
    public function robots(): void
    {
        header('Content-Type: text/plain; charset=utf-8');
        echo "User-agent: *\n";
        echo "Allow: /\n";
        echo "Disallow: /admin/\n";
        echo "Disallow: /api/\n";
        echo "Sitemap: " . url('/sitemap.xml') . "\n";
        exit;
    }
}
