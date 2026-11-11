<?php

namespace Fickrr\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PruneSiteVisits extends Command
{
    protected $signature = 'visits:prune {--days=}';

    protected $description = 'Delete raw visit rows older than the configured retention window';

    public function handle()
    {
        $days = $this->option('days');
        $days = $days === null || $days === '' ? (int) config('visits.retain_days', 0) : (int) $days;
        if ($days < 1) {
            $this->info('Visit pruning is off. Set VISIT_RETAIN_DAYS or pass --days=90.');

            return 0;
        }
        $cut = date('Y-m-d H:i:s', time() - ($days * 86400));
        $deleted = DB::table('site_visits')->where('visited_at', '<', $cut)->delete();
        $this->info('Removed '.$deleted.' visit rows older than '.$days.' days.');

        return 0;
    }
}
