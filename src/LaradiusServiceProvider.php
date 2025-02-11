<?php

namespace Edwinrtoha\Laradius;

use Composer\InstalledVersions;
use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Console\AboutCommand;
use Illuminate\Routing\Route;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\Compilers\BladeCompiler;
use Spatie\Permission\Contracts\Permission as PermissionContract;
use Spatie\Permission\Contracts\Role as RoleContract;

class LaradiusServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->offerPublishing();
    }

    /**
     * Returns existing migration file if found, else uses the current timestamp.
     */
    protected function getMigrationFileName(string $migrationFileName): string
    {
        $timestamp = date('Y_m_d_His');

        $filesystem = $this->app->make(Filesystem::class);

        return Collection::make([$this->app->databasePath().DIRECTORY_SEPARATOR.'migrations'.DIRECTORY_SEPARATOR])
            ->flatMap(fn ($path) => $filesystem->glob($path.'*_'.$migrationFileName))
            ->push($this->app->databasePath()."/migrations/{$timestamp}_{$migrationFileName}")
            ->first();
    }

    protected function offerPublishing(): void
    {
        if (! $this->app->runningInConsole()) {
            return;
        }

        if (! function_exists('config_path')) {
            // function not available and 'publish' not relevant in Lumen
            return;
        }

        $this->publishes([
            __DIR__.'/database/migrations/create_radacct_table.php.stub' => $this->getMigrationFileName('create_radacct_table.php'),
            __DIR__.'/database/migrations/create_radcheck_table.php.stub' => $this->getMigrationFileName('create_radcheck_table.php'),
            __DIR__.'/database/migrations/create_radgroupcheck_table.php.stub' => $this->getMigrationFileName('create_radgroupcheck_table.php'),
            __DIR__.'/database/migrations/create_radgroupreply_table.php.stub' => $this->getMigrationFileName('create_radgroupreply_table.php'),
            __DIR__.'/database/migrations/create_radreply_table.php.stub' => $this->getMigrationFileName('create_radreply_table.php'),
            __DIR__.'/database/migrations/create_radusergroup_table.php.stub' => $this->getMigrationFileName('create_radusergroup_table.php'),
            __DIR__.'/database/migrations/create_radpostauth_table.php.stub' => $this->getMigrationFileName('create_radpostauth_table.php'),
            __DIR__.'/database/migrations/create_nas_table.php.stub' => $this->getMigrationFileName('create_nas_table.php'),
            __DIR__.'/database/migrations/create_nasreload_table.php.stub' => $this->getMigrationFileName('create_nasreload_table.php'),
            __DIR__.'/database/migrations/create_radius_users_table.php.stub' => $this->getMigrationFileName('create_radius_users_table.php'),
            __DIR__.'/database/migrations/create_radius_groups_table.php.stub' => $this->getMigrationFileName('create_radius_groups_table.php'),
        ], 'freeradius-schema-migrations');
    }
}
