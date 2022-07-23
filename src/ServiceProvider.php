<?php

namespace MyriadDataStore;

use MyriadDataStore\Console\Commands\DownloadAllMyriadOrdersBasic;
use MyriadDataStore\Console\Commands\DownloadLatestMyriadContacts;
use MyriadDataStore\Console\Commands\DownloadMyriadContact;
use MyriadDataStore\Console\Commands\DownloadMyriadContactOrdersBasic;
use MyriadDataStore\Console\Commands\DownloadMyriadContacts;
use MyriadDataStore\Console\Commands\DownloadMyriadContactTypes;
use MyriadDataStore\Console\Commands\DownloadMyriadDespatchTypes;
use MyriadDataStore\Console\Commands\DownloadMyriadIssues;
use MyriadDataStore\Console\Commands\DownloadMyriadOrderPackageTypes;
use MyriadDataStore\Console\Commands\DownloadMyriadOrdersBasicForContacts;
use MyriadDataStore\Console\Commands\DownloadMyriadOrderStatusTypes;
use MyriadDataStore\Console\Commands\DownloadMyriadProductTypes;
use MyriadDataStore\Console\Commands\DownloadMyriadTitles;
use MyriadDataStore\Console\Commands\SetIssuesDatesForPackages;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/myriad-data-store.php' => config_path('myriad-data-store.php'),
            ], 'config');

            $this->commands([
                DownloadMyriadContact::class,
                DownloadMyriadContacts::class,
                DownloadMyriadProductTypes::class,
                DownloadMyriadDespatchTypes::class,
                DownloadMyriadOrderPackageTypes::class,
                DownloadMyriadOrderStatusTypes::class,
                DownloadMyriadTitles::class,
                DownloadMyriadIssues::class,
                DownloadMyriadContactOrdersBasic::class,
                DownloadMyriadOrdersBasicForContacts::class,
                DownloadMyriadContactTypes::class,
                DownloadLatestMyriadContacts::class,
                SetIssuesDatesForPackages::class,
                DownloadAllMyriadOrdersBasic::class,
            ]);

            $this->registerMigrations();
        }
    }

    /**
     * @inheritDoc
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/myriad-data-store.php', 'myriad-data-store');
    }

    /**
     * Register the package migrations.
     *
     * @return void
     */
    protected function registerMigrations()
    {
        if (MyriadDataDownloader::$runsMigrations) {
            $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        }
    }
}
