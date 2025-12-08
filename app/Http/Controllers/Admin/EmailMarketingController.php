<?php

namespace Fickrr\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Fickrr\Http\Controllers\Controller;
use Fickrr\Models\EmailMarketingTemplate;
use Fickrr\Models\EmailMarketingCampaign;
use Fickrr\Models\LandingCustomer;
use Fickrr\Models\Settings;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Session;
use Helper;

class EmailMarketingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function custom()
    {
        $dw_v = Helper::version_no();
        $custom = Settings::customSettings();
        return $custom->$dw_v;
    }

    // ==================== TEMPLATES ====================

    /**
     * Display all email templates
     */
    public function templates()
    {
        if ($this->custom() != 0) {
            $sid = 1;
            $data['setting'] = Settings::editGeneral($sid);
            $data['templates'] = EmailMarketingTemplate::getAllTemplates();
            return view('admin.email-marketing.templates', $data);
        } else {
            return redirect('/admin/license');
        }
    }

    /**
     * Show create template form
     */
    public function createTemplate()
    {
        if ($this->custom() != 0) {
            $sid = 1;
            $data['setting'] = Settings::editGeneral($sid);
            return view('admin.email-marketing.create-template', $data);
        } else {
            return redirect('/admin/license');
        }
    }

    /**
     * Store new template
     */
    public function storeTemplate(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'content' => 'required',
            'status' => 'required|in:active,inactive'
        ]);

        EmailMarketingTemplate::createTemplate([
            'name' => $request->name,
            'subject' => $request->subject,
            'content' => $request->content,
            'status' => $request->status,
            'notes' => $request->notes
        ]);

        Session::flash('success', 'Email template created successfully!');
        return redirect()->route('admin.email-marketing.templates');
    }

    /**
     * Show edit template form
     */
    public function editTemplate($id)
    {
        if ($this->custom() != 0) {
            $sid = 1;
            $data['setting'] = Settings::editGeneral($sid);
            $data['template'] = EmailMarketingTemplate::getTemplateById($id);
            
            if (!$data['template']) {
                Session::flash('error', 'Template not found');
                return redirect()->route('admin.email-marketing.templates');
            }

            return view('admin.email-marketing.edit-template', $data);
        } else {
            return redirect('/admin/license');
        }
    }

    /**
     * Update template
     */
    public function updateTemplate(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'content' => 'required',
            'status' => 'required|in:active,inactive'
        ]);

        EmailMarketingTemplate::updateTemplate($id, [
            'name' => $request->name,
            'subject' => $request->subject,
            'content' => $request->content,
            'status' => $request->status,
            'notes' => $request->notes
        ]);

        Session::flash('success', 'Email template updated successfully!');
        return redirect()->route('admin.email-marketing.templates');
    }

    /**
     * Delete template
     */
    public function deleteTemplate($id)
    {
        $template = EmailMarketingTemplate::getTemplateById($id);
        
        if (!$template) {
            Session::flash('error', 'Template not found');
            return redirect()->back();
        }

        // Check if template is being used in campaigns
        $campaignsCount = $template->campaigns()->count();
        
        if ($campaignsCount > 0) {
            Session::flash('error', 'Cannot delete template. It is being used in ' . $campaignsCount . ' campaign(s).');
            return redirect()->back();
        }

        EmailMarketingTemplate::deleteTemplate($id);
        Session::flash('success', 'Template deleted successfully!');
        return redirect()->back();
    }

    // ==================== CAMPAIGNS ====================

    /**
     * Display all campaigns
     */
    public function campaigns()
    {
        if ($this->custom() != 0) {
            $sid = 1;
            $data['setting'] = Settings::editGeneral($sid);
            $data['campaigns'] = EmailMarketingCampaign::getAllCampaigns();
            return view('admin.email-marketing.campaigns', $data);
        } else {
            return redirect('/admin/license');
        }
    }

    /**
     * Show create campaign form
     */
    public function createCampaign()
    {
        if ($this->custom() != 0) {
            $sid = 1;
            $data['setting'] = Settings::editGeneral($sid);
            $data['templates'] = EmailMarketingTemplate::getActiveTemplates();
            
            // Get customer counts
            $data['regular_customers_count'] = DB::table('users')
                ->where('user_type', 'customer')
                ->where('drop_status', 'no')
                ->count();
            
            $data['landing_customers_count'] = LandingCustomer::count();
            $data['total_customers_count'] = $data['regular_customers_count'] + $data['landing_customers_count'];

            return view('admin.email-marketing.create-campaign', $data);
        } else {
            return redirect('/admin/license');
        }
    }

    /**
     * Store and send campaign
     */
    public function storeCampaign(Request $request)
    {
        $request->validate([
            'campaign_name' => 'required|string|max:255',
            'template_id' => 'required|exists:email_marketing_templates,id',
            'recipient_type' => 'required|in:all,regular_customers,landing_customers,specific'
        ]);

        // Get template
        $template = EmailMarketingTemplate::getTemplateById($request->template_id);
        
        if (!$template || $template->status != 'active') {
            Session::flash('error', 'Invalid or inactive template selected');
            return redirect()->back();
        }

        // Get recipients based on type
        $recipients = $this->getRecipients($request->recipient_type, $request->recipient_emails);

        if (empty($recipients)) {
            Session::flash('error', 'No recipients found for the selected criteria');
            return redirect()->back();
        }

        // Create campaign
        $campaign = EmailMarketingCampaign::createCampaign([
            'campaign_name' => $request->campaign_name,
            'template_id' => $request->template_id,
            'recipient_type' => $request->recipient_type,
            'recipient_emails' => $request->recipient_type == 'specific' ? $request->recipient_emails : null,
            'total_recipients' => count($recipients),
            'status' => 'sending'
        ]);

        // Send emails
        $emailsSent = 0;
        $emailsFailed = 0;

        foreach ($recipients as $recipient) {
            try {
                $this->sendMarketingEmail($recipient, $template);
                $emailsSent++;
            } catch (\Exception $e) {
                $emailsFailed++;
                \Log::error('Email Marketing Error: ' . $e->getMessage());
            }
        }

        // Update campaign status
        EmailMarketingCampaign::updateCampaign($campaign->id, [
            'emails_sent' => $emailsSent,
            'emails_failed' => $emailsFailed,
            'status' => $emailsFailed == 0 ? 'completed' : 'failed',
            'sent_at' => now()
        ]);

        Session::flash('success', "Campaign sent! {$emailsSent} emails sent successfully" . ($emailsFailed > 0 ? ", {$emailsFailed} failed" : ""));
        return redirect()->route('admin.email-marketing.campaigns');
    }

    /**
     * View campaign details
     */
    public function viewCampaign($id)
    {
        if ($this->custom() != 0) {
            $sid = 1;
            $data['setting'] = Settings::editGeneral($sid);
            $data['campaign'] = EmailMarketingCampaign::getCampaignById($id);
            
            if (!$data['campaign']) {
                Session::flash('error', 'Campaign not found');
                return redirect()->route('admin.email-marketing.campaigns');
            }

            return view('admin.email-marketing.view-campaign', $data);
        } else {
            return redirect('/admin/license');
        }
    }

    /**
     * Delete campaign
     */
    public function deleteCampaign($id)
    {
        EmailMarketingCampaign::deleteCampaign($id);
        Session::flash('success', 'Campaign deleted successfully!');
        return redirect()->back();
    }

    // ==================== HELPER METHODS ====================

    /**
     * Get recipients based on type
     */
    private function getRecipients($type, $specificEmails = null)
    {
        $recipients = [];

        switch ($type) {
            case 'all':
                // Get regular customers
                $regularCustomers = DB::table('users')
                    ->where('user_type', 'customer')
                    ->where('drop_status', 'no')
                    ->whereNotNull('email')
                    ->select('email', 'name')
                    ->get();
                
                foreach ($regularCustomers as $customer) {
                    $recipients[] = [
                        'email' => $customer->email,
                        'name' => $customer->name ?? 'Customer'
                    ];
                }

                // Get landing customers
                $landingCustomers = LandingCustomer::whereNotNull('email')->get();
                
                foreach ($landingCustomers as $customer) {
                    $recipients[] = [
                        'email' => $customer->email,
                        'name' => $customer->name ?? 'Customer'
                    ];
                }
                break;

            case 'regular_customers':
                $regularCustomers = DB::table('users')
                    ->where('user_type', 'customer')
                    ->where('drop_status', 'no')
                    ->whereNotNull('email')
                    ->select('email', 'name')
                    ->get();
                
                foreach ($regularCustomers as $customer) {
                    $recipients[] = [
                        'email' => $customer->email,
                        'name' => $customer->name ?? 'Customer'
                    ];
                }
                break;

            case 'landing_customers':
                $landingCustomers = LandingCustomer::whereNotNull('email')->get();
                
                foreach ($landingCustomers as $customer) {
                    $recipients[] = [
                        'email' => $customer->email,
                        'name' => $customer->name ?? 'Customer'
                    ];
                }
                break;

            case 'specific':
                if ($specificEmails) {
                    $emails = array_map('trim', explode(',', $specificEmails));
                    foreach ($emails as $email) {
                        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                            $recipients[] = [
                                'email' => $email,
                                'name' => 'Customer'
                            ];
                        }
                    }
                }
                break;
        }

        // Remove duplicates based on email
        $uniqueRecipients = [];
        $emails = [];
        
        foreach ($recipients as $recipient) {
            if (!in_array($recipient['email'], $emails)) {
                $emails[] = $recipient['email'];
                $uniqueRecipients[] = $recipient;
            }
        }

        return $uniqueRecipients;
    }

    /**
     * Send marketing email
     */
    private function sendMarketingEmail($recipient, $template)
    {
        $settings = Settings::editGeneral(1);
        
        // Replace placeholders in content
        $content = str_replace(
            ['{{name}}', '{{email}}', '{{site_name}}'],
            [$recipient['name'], $recipient['email'], $settings->site_title ?? 'Our Site'],
            $template->content
        );

        $emailData = [
            'subject' => $template->subject,
            'content' => $content,
            'recipient_name' => $recipient['name']
        ];

        Mail::send([], [], function ($message) use ($recipient, $emailData, $settings) {
            $message->to($recipient['email'], $recipient['name'])
                ->subject($emailData['subject'])
                ->from($settings->site_email ?? 'noreply@example.com', $settings->site_title ?? 'AutoScriptHub')
                ->html($emailData['content']);
        });
    }
}

