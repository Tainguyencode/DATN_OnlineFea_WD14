<?php

namespace App\Console\Commands;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CreateAdminUser extends Command
{
    protected $signature = 'admin:create
                            {email : Email quản trị viên}
                            {name : Họ tên}
                            {--password= : Mật khẩu (mặc định: password)}
                            {--super : Tạo Super Admin}';

    protected $description = 'Tạo tài khoản Admin hoặc Super Admin';

    public function handle(): int
    {
        $email = $this->argument('email');
        $name = $this->argument('name');
        $password = $this->option('password') ?: 'password';
        $role = $this->option('super') ? UserRole::SuperAdmin->value : UserRole::Admin->value;

        $validator = Validator::make(
            ['email' => $email, 'name' => $name, 'password' => $password],
            [
                'email' => 'required|email|unique:users,email',
                'name' => 'required|string|max:255',
                'password' => 'required|min:8',
            ]
        );

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'role' => $role,
            'status' => UserStatus::Active->value,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $this->info("Đã tạo {$role}: {$email}");

        return self::SUCCESS;
    }
}
