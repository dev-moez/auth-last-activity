<?php

namespace DevMoez\AuthLastActivity\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AuthLastActivity extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'authenticatable_type',
        'authenticatable_id',
        'last_activity_url',
        'last_activity_time',
        'user_agent',
        'ip_address',
        'headers',
        'is_mobile',
        'request_source',
        'previous_url'
    ];
    
    protected $casts = [
        'last_activity_time' => 'datetime',
        'is_mobile' => 'boolean',
        'headers' => 'array',
    ];

    /**
     * Get the authenticatable that belongs to the Log
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function authenticatable(): MorphTo
    {
        return $this->setConnection(config('user-activity-log.authenticatable-connections'))->morphTo();
    }

    public function getAuthName(): string
    {
        return class_basename($this->authenticatable);
    }
}
