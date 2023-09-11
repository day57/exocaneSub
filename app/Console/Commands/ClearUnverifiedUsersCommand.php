<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ClearUnverifiedUsersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:clear-unverified-users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear the unverified users from the `users` database table';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        DB::table('users')->where([['email_verified_at', '=', NULL], ['created_at', '<', Carbon::now()->subDays(30)]])->delete();

        return 0;
    }
}

/*
    namespace App\Console\Commands;: This sets the namespace for the class, helping to organize code and prevent naming conflicts.
    use statements: These import necessary classes and facades that the command will utilize.
    class ClearUnverifiedUsersCommand extends Command: This defines the class for the command, extending Laravel's Command class to inherit essential functionality.
    protected $signature = 'cron:clear-unverified-users';: This defines the signature or name used to call this command from the console (e.g., php artisan cron:clear-unverified-users).
    protected $description = 'Clear the unverified users from the users database table';: This sets a description for the command to make it more understandable when viewed in a list of available commands.
    public function handle(): This is the core method where the command's functionality is defined.
    Inside the handle method, the code uses the DB facade to delete users from the users table where the email_verified_at field is NULL and the created_at date is more than 30 days old.
    Finally, the method returns 0, indicating a successful execution of the command.

*/