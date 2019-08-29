<?php

namespace Entanet\Insights;

use Illuminate\Support\ServiceProvider;

class InsightsServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/files/EntanetInsights.php' => base_path('console/commands/EntanetInsights.php')
        ]);
    }
}
