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

class CreateAdminPassword extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:make';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create the "admin" user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        intro('Creating admin user for Outline Admin...');

        if (User::whereUsername('admin')->exists()) {
            error('Admin user already exists.');

            return;
        }

        $password = password(label: 'Enter password', required: true);
        $confirmPassword = password(label: 'Confirm password', required: true);

        if ($password !== $confirmPassword) {
            error('Passwords do not match. Please try again.');

            return;
        }

        spin(fn () => User::create([
            'username' => 'admin',
            'name' => 'Administrator',
            'password' => Hash::make($password),
        ]), 'Please wait...');

        outro('Admin user has been created successfully.');
    }
}
