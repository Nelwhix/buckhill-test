<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;

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
        $users = User::all();
        Artisan::call('migrate:fresh');

        foreach($users as $user) {
            if ($user->is_admin === 1) {
                    User::factory()->createOne($user->toArray());
                    continue;
            }
            User::factory()->create([
                ...$user->toArray(),
                'email' => fake()->email(),
                'password' => Hash::make('userpassword')
            ]);
        }

        $this->info('Database truncated and reseeded successfully.');
    }
}
