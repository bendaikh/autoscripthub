<?php

namespace Fickrr\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class EmailMarketingCampaign extends Model
{
    protected $table = 'email_marketing_campaigns';
    
    protected $fillable = [
        'campaign_name',
        'template_id',
        'recipient_type',
        'recipient_emails',
        'total_recipients',
        'emails_sent',
        'emails_failed',
        'status',
        'scheduled_at',
        'sent_at'
    ];

    protected $dates = [
        'scheduled_at',
        'sent_at'
    ];

    /**
     * Get all campaigns
     */
    public static function getAllCampaigns()
    {
        return self::with('template')->orderBy('created_at', 'desc')->get();
    }

    /**
     * Get campaign by ID
     */
    public static function getCampaignById($id)
    {
        return self::with('template')->find($id);
    }

    /**
     * Create new campaign
     */
    public static function createCampaign($data)
    {
        return self::create($data);
    }

    /**
     * Update campaign
     */
    public static function updateCampaign($id, $data)
    {
        return self::where('id', $id)->update($data);
    }

    /**
     * Delete campaign
     */
    public static function deleteCampaign($id)
    {
        return self::where('id', $id)->delete();
    }

    /**
     * Get template relationship
     */
    public function template()
    {
        return $this->belongsTo(EmailMarketingTemplate::class, 'template_id');
    }

    /**
     * Get pending campaigns
     */
    public static function getPendingCampaigns()
    {
        return self::where('status', 'draft')
                   ->orWhere('status', 'sending')
                   ->get();
    }
}

