<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TenantCommand extends Command
{
    protected $signature = 'tenant
        {action : create|list|activate|deactivate}
        {--name= : Tenant name}
        {--slug= : Tenant slug}
        {--subdomain= : Tenant subdomain}
        {--email= : Admin email}
        {--password= : Admin password}';

    protected $description = 'Manage multi-tenancy tenants';

    public function handle(): int
    {
        $action = $this->argument('action');

        return match ($action) {
            'create' => $this->createTenant(),
            'list' => $this->listTenants(),
            'activate' => $this->toggleStatus('active'),
            'deactivate' => $this->toggleStatus('inactive'),
            default => $this->error("Unknown action: {$action}"),
        };
    }

    private function createTenant(): int
    {
        $name = $this->option('name') ?? $this->ask('Tenant name');
        $slug = $this->option('slug') ?? Str::slug($name);
        $subdomain = $this->option('subdomain') ?? $this->ask('Subdomain');

        if (Tenant::where('subdomain', $subdomain)->exists()) {
            $this->error("Tenant with subdomain '{$subdomain}' already exists.");
            return Command::FAILURE;
        }

        $tenant = Tenant::create([
            'name' => $name,
            'slug' => $slug,
            'subdomain' => $subdomain,
            'domain' => null,
            'settings' => [
                'locale' => 'bn',
                'timezone' => 'Asia/Dhaka',
            ],
            'status' => 'active',
        ]);

        $this->info("Tenant '{$name}' created (subdomain: {$subdomain}).");

        $createAdmin = $this->confirm('Create an admin user for this tenant?', true);

        if ($createAdmin) {
            $email = $this->option('email') ?? $this->ask('Admin email', "admin@{$subdomain}.test");
            $password = $this->option('password') ?? $this->secret('Admin password');

            $user = User::create([
                'tenant_id' => $tenant->id,
                'name' => "{$name} Admin",
                'username' => "admin_{$slug}",
                'email' => $email,
                'password' => Hash::make($password ?: 'password'),
                'status' => 'active',
            ]);

            $user->assignRole('Super Admin');

            $this->info("Admin user created: {$email}");
        }

        return Command::SUCCESS;
    }

    private function listTenants(): int
    {
        $tenants = Tenant::all(['id', 'name', 'slug', 'subdomain', 'status', 'created_at']);

        if ($tenants->isEmpty()) {
            $this->info('No tenants found.');
            return Command::SUCCESS;
        }

        $this->table(
            ['ID', 'Name', 'Slug', 'Subdomain', 'Status', 'Created'],
            $tenants->toArray(),
        );

        return Command::SUCCESS;
    }

    private function toggleStatus(string $status): int
    {
        $subdomain = $this->option('subdomain') ?? $this->ask('Tenant subdomain');

        $tenant = Tenant::where('subdomain', $subdomain)->first();

        if (! $tenant) {
            $this->error("Tenant '{$subdomain}' not found.");
            return Command::FAILURE;
        }

        $tenant->update(['status' => $status]);
        $this->info("Tenant '{$subdomain}' is now {$status}.");

        return Command::SUCCESS;
    }
}
