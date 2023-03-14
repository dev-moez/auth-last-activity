<?php
namespace DevMoez\AuthLastActivity\Actions;

use Illuminate\Http\Request;
use DevMoez\AuthLastActivity\Models\AuthLastActivity;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class CreateAuthLastActivityAction {

    private array $data;
    private array $user;

    public function __construct(public readonly Request $request)
    {
        $this->data = $this->getAuthLastActivityData();
        $this->user = $this->getUserRequest();
    }
    

    public function execute(): AuthLastActivity
    {
        $AuthLastActivity =  AuthLastActivity::query()->updateOrCreate($this->user, $this->data);
        $this->registerOnlineAuth();
        return $AuthLastActivity;
    }


    private function registerOnlineAuth(): void
    {
        Cache::put(
            'online-auth-'.$this->request->user()->getAuthenticatableName().'-'.$this->request->user()->id, 
            true, 
            Carbon::now()->addSeconds(config('auth-last-activity.online-period'))
        );
    }

    private function getUserRequest(): array
    {
        return [
            'authenticatable_type' => get_class($this->request->user()),
            'authenticatable_id' => $this->request->user()->id
        ];
    }

    private function getAuthLastActivityData(): array
    {
        return [
            'last_activity_url' => $this->request->getUri(),
            'last_activity_time' => Carbon::now()->setTimezone(config('auth-last-activity.timezone')),
            'previous_url' => url()->previous(),
            'headers' => json_encode($this->request->headers->all()),
            'ip_address' => $this->request->getClientIp(),
            'user_agent' => $this->request->userAgent(),
            'is_mobile' => $this->isMobile(),
            'request_source' => $this->isApi() ? 'api' : 'web',
        ];
    }
    
    private function isApi(): bool
    {
        return $this->request->route()->getPrefix() === 'api' || $this->request->hasHeader('Authorization');
    }
    
    private function isMobile(): bool
    {
        return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"] ?? false);
    }
}