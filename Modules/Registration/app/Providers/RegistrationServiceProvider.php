<?php

namespace Modules\Registration\Providers;

use Nwidart\Modules\Support\ModuleServiceProvider;
use Illuminate\Console\Scheduling\Schedule;

class RegistrationServiceProvider extends ModuleServiceProvider
{
    /**
     * The name of the module.
     */
    protected string $name = 'Registration';

    /**
     * The lowercase version of the module name.
     */
    protected string $nameLower = 'registration';

    /**
     * Command classes to register.
     *
     * @var string[]
     */
    // protected array $commands = [];

    /**
     * Provider classes to register.
     *
     * @var string[]
     */
    protected array $providers = [
        EventServiceProvider::class,
        RouteServiceProvider::class,
    ];

    /**
     * Define module schedules.
     * 
     * @param $schedule
     */
    // protected function configureSchedules(Schedule $schedule): void
    // {
    //     $schedule->command('inspire')->hourly();
    // }

    public function register(): void
{
    parent::register();

    $this->app->bind(
        \Modules\Registration\Contracts\CarryOverProvider::class,
        \Modules\Assessments\Services\AssessmentsCarryOverProvider::class,
    );
    $this->app->bind(
        \Modules\Registration\Contracts\RegistrationDirectory::class,
        \Modules\Registration\Services\RegistrationDirectoryService::class,
    );
}
}
