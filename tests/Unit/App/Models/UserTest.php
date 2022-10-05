<?php

namespace Tests\Unit\App\Models;

use App\Models\User;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;

class UserTest extends ModelTestCase
{
    protected function model(): Model
    {
        return new User();
    }

    protected function traits(): array
    {
        return [
            HasApiTokens::class,
            HasFactory::class,
            Notifiable::class
        ];
    }

    protected function fillable(): array
    {
        return [
            'name', 'email', 'password'
        ];
    }

    protected function casts(): array
    {
        return [
            'id' => 'string',
            'email_verified_at' => 'datetime',
        ];
    }

    protected function hidden(): array
    {
        return [
            'password',
            'remember_token',
        ];
    }
}
