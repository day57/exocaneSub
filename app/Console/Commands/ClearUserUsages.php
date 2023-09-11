<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class ClearUserUsages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:clear-user-usages';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear the `users` database table';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        User::query()->update(['documents_month_count' => 0, 'words_month_count' => 0, 'images_month_count' => 0, 'chats_month_count', 'transcriptions_month_count']);

        return 0;
    }
}

/*
    This snippet is a Laravel console command that resets specific usage counters for all users in the users database table. Here's a detailed breakdown:

    It's a part of the App\Console\Commands namespace, which helps in organizing the code.
    The command signature is cron:clear-user-usages, so you would call it using php artisan cron:clear-user-usages.
    The description mentions that it will "Clear the users database table," but more precisely, it resets specific usage counts for all users in the table.
    In the handle method, it's using Laravel's Eloquent ORM to update all rows in the users table with the following fields set to zero:
    documents_month_count
    words_month_count
    images_month_count
    chats_month_count (Note: You have a typo here, missing the value to assign. This line will cause an error.)
    transcriptions_month_count (Note: Same as above, missing the value to assign. This line will also cause an error.)
    The method returns 0, indicating successful execution.

    To correct the code, you should update the last two fields to have values assigned to them, like so:

    User::query()->update([
        'documents_month_count' => 0,
        'words_month_count' => 0,
        'images_month_count' => 0,
        'chats_month_count' => 0, // corrected here
        'transcriptions_month_count' => 0 // corrected here
    ]);
  
    This corrected code will reset the specific counters to zero for all users, probably as part of a monthly usage reset or a similar mechanism.
    
 */