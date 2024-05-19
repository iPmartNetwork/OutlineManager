<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

use function Laravel\Prompts\error;
use function Laravel\Prompts\intro;
use function Laravel\Prompts\outro;
use function Laravel\Prompts\password;
use function Laravel\Prompts\spin;

class UpdateAdminPassword extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:password';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset the "admin" user password';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        intro('Updating admin user password...');

        $newPassword = password(label: 'Enter new password', required: true);
        $confirmPassword = password(label: 'Confirm new password', required: true);

        if ($newPassword !== $confirmPassword) {
            error('Passwords do not match. Please try again.');

            return;
        }

        spin(fn () => User::whereUsername('admin')->update([
            'password' => Hash::make($newPassword),
        ]), 'Please wait...');

        outro('Admin password has been updated successfully.');
    }
}
