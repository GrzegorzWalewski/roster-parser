<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\RosterParsingServiceInterface;
use App\Services\HtmlRosterParsingService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(RosterParsingServiceInterface::class, function ($app) {
            $fileType = $app['request']->file('file')->getClientOriginalExtension();
            
            switch ($fileType) {
                case 'html':
                    return new HtmlRosterParsingService();
                default:
                    throw new \Exception('Unknown file type: ' . $fileType);
            }
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
