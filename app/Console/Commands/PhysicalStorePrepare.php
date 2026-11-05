<?php

namespace Fickrr\Console\Commands;

use Fickrr\Support\Store;
use Fickrr\Support\StoreDatabaseGuard;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;

class PhysicalStorePrepare extends Command
{
    protected $signature = 'physical-store:prepare {--seed-legal : Replace digital wording on the privacy, terms, refund, and FAQ pages}';

    protected $description = 'Check the esipres physical-store database and optionally refresh legal page copy';

    public function handle(): int
    {
        $database = StoreDatabaseGuard::currentDatabaseName();
        $violation = StoreDatabaseGuard::violation(
            $database,
            app()->environment('production') || (bool) config('store.enforce_separate_database')
        );

        $this->line('Store mode: ' . Store::mode());
        $this->line('Public URL: ' . Store::url());
        $this->line('Database: ' . ($database === '' ? '(empty)' : $database));
        $this->line('Stripe charges: ' . (\Fickrr\Services\Physical\StripeChargePolicy::chargesEnabled() ? 'on' : 'off (no card charge will be created)'));
        $this->line('PayPal charges: ' . (\Fickrr\Services\Physical\PayPalChargePolicy::chargesEnabled() ? 'on' : 'off (no PayPal payment will be created)'));

        if ($violation !== null) {
            $this->error($violation);
            return 1;
        }

        $this->info('Database name is allowed for the physical store.');
        $this->line('Run php artisan migrate on this database only. Do not point it at auto-script-hub.com.');
        $this->line('Create the first admin from the installer or registration screen on the empty database. This command does not copy users, orders, products, or files.');

        if ($this->option('seed-legal')) {
            $this->seedLegal();
        }

        return 0;
    }

    private function seedLegal(): void
    {
        if (!Schema::hasTable('pages')) {
            $this->warn('The pages table is not here yet. Migrate first, then run this command again with --seed-legal.');
            return;
        }

        $pages = \Fickrr\Support\PhysicalLegalCopy::pages();
        foreach ($pages as $slug => $page) {
            $existing = \DB::table('pages')->where('page_slug', $slug)->first();
            $payload = [
                'page_title' => $page['title'],
                'page_desc' => $page['html'],
                'page_slug' => $slug,
                'page_status' => 1,
                'footer_menu' => 1,
                'updated_at' => now(),
            ];

            $columns = array_flip(Schema::getColumnListing('pages'));
            $payload = array_intersect_key($payload, $columns);

            if ($existing) {
                \DB::table('pages')->where('page_slug', $slug)->update($payload);
                $this->line('Updated ' . $slug);
            } else {
                if (isset($columns['created_at'])) {
                    $payload['created_at'] = now();
                }
                \DB::table('pages')->insert($payload);
                $this->line('Created ' . $slug);
            }
        }
    }
}
