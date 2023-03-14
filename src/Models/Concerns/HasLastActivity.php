<?php 
namespace DevMoez\AuthLastActivity\Models\Concerns;

use Illuminate\Database\Eloquent\Relations\HasOne;
use DevMoez\AuthLastActivity\Models\AuthLastActivity;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

trait HasLastActivity
{
    public function lastActivity(): HasOne
    {
        return $this->hasOne(AuthLastActivity::class, 'authenticatable_id', 'id');
    }

    public static function getAuthenticatableName(): string
    {
        return Str::kebab( strtolower( class_basename( get_called_class() ) ) );
        
    }

    public function isOnline(bool $viaCache = true): bool
    {
        if ($viaCache)
            return Cache::has('online-auth-'.static::getAuthenticatableName().'-'.$this->id);
        
        return ($this->lastActivity->last_activity_time > Carbon::now()
                        ->timezone(config('auth-last-activity.timezone') )
                        ->subSeconds( config('auth-last-activity.online-period') ));
    }

    public static function getOnline(): Builder
    {
        return self::whereHas('lastActivity', function($query) {
            $query->where(
                    'last_activity_time', 
                    '>', 
                    Carbon::now()
                        ->timezone(config('auth-last-activity.timezone') )
                        ->subSeconds( config('auth-last-activity.online-period') ) 
            );
        });
    }
    
    public static function getOffline(): Builder
    {
        return self::whereHas('lastActivity', function($query) {
            $query->where(
                    'last_activity_time', 
                    '<', 
                    Carbon::now()
                        ->timezone(config('auth-last-activity.timezone') )
                        ->subSeconds( config('auth-last-activity.online-period') ) 
            );
        });
    }
    
    public static function activeWithin(int $seconds): Builder
    {
        return self::whereHas('lastActivity', function($query) use ($seconds) {
            $query->where(
                    'last_activity_time', 
                    '>', 
                    Carbon::now()
                        ->timezone(config('auth-last-activity.timezone') )
                        ->subSeconds( $seconds ) 
            );
        });
    }
}