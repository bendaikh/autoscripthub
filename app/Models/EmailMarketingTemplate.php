<?php

namespace Fickrr\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class EmailMarketingTemplate extends Model
{
    protected $table = 'email_marketing_templates';
    
    protected $fillable = [
        'name',
        'subject',
        'content',
        'status',
        'notes'
    ];

    /**
     * Get all templates
     */
    public static function getAllTemplates()
    {
        return self::orderBy('created_at', 'desc')->get();
    }

    /**
     * Get active templates
     */
    public static function getActiveTemplates()
    {
        return self::where('status', 'active')->orderBy('name', 'asc')->get();
    }

    /**
     * Get template by ID
     */
    public static function getTemplateById($id)
    {
        return self::find($id);
    }

    /**
     * Create new template
     */
    public static function createTemplate($data)
    {
        return self::create($data);
    }

    /**
     * Update template
     */
    public static function updateTemplate($id, $data)
    {
        return self::where('id', $id)->update($data);
    }

    /**
     * Delete template
     */
    public static function deleteTemplate($id)
    {
        return self::where('id', $id)->delete();
    }

    /**
     * Get campaigns using this template
     */
    public function campaigns()
    {
        return $this->hasMany(EmailMarketingCampaign::class, 'template_id');
    }
}

