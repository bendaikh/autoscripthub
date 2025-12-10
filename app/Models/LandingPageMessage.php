<?php

namespace Fickrr\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class LandingPageMessage extends Model
{
    protected $table = 'landing_page_messages';
    protected $primaryKey = 'lpm_id';
    
    protected $fillable = [
        'lp_id',
        'lpm_name',
        'lpm_email',
        'lpm_phone',
        'lpm_subject',
        'lpm_message',
        'lpm_status',
    ];

    /**
     * Get all messages
     */
    public static function getAllMessages()
    {
        return DB::table('landing_page_messages')
            ->join('landing_pages', 'landing_page_messages.lp_id', '=', 'landing_pages.lp_id')
            ->select('landing_page_messages.*', 'landing_pages.lp_title', 'landing_pages.lp_slug')
            ->orderBy('landing_page_messages.created_at', 'desc')
            ->get();
    }

    /**
     * Get messages by landing page ID
     */
    public static function getMessagesByLandingPage($lp_id)
    {
        return DB::table('landing_page_messages')
            ->where('lp_id', $lp_id)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get unread messages count
     */
    public static function getUnreadCount()
    {
        return DB::table('landing_page_messages')
            ->where('lpm_status', 0)
            ->count();
    }

    /**
     * Save message
     */
    public static function saveMessage($data)
    {
        return DB::table('landing_page_messages')->insert($data);
    }

    /**
     * Update message status
     */
    public static function updateStatus($lpm_id, $status)
    {
        return DB::table('landing_page_messages')
            ->where('lpm_id', $lpm_id)
            ->update(['lpm_status' => $status, 'updated_at' => now()]);
    }

    /**
     * Delete message
     */
    public static function deleteMessage($lpm_id)
    {
        return DB::table('landing_page_messages')
            ->where('lpm_id', $lpm_id)
            ->delete();
    }
}
