<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class TruncateDbAndReseed extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:truncate-reseed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Truncate and Reseed Database';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        Artisan::call('migrate:fresh --seed');

        $this->info('Database truncated and reseeded successfully.');
    }
}
