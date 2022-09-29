<?php

namespace App\Repository\Eloquent;

use App\Interfaces\Eloquent\UserRepositoryInterface;
use App\Models\User;
use Exception;

class UserRepository implements UserRepositoryInterface
{
    protected $model;
    public function __construct(User $user)
    {
        $this->model = $user;
    }

    public function findAll(): array
    {
        return $this->model->get()->toArray();
    }

    public function create(array $data): object
    {
        return $this->model->create($data);
    }

    public function update(string $email, array $data): object
    {
        $user = $this->model->whereEmail($email)->first();
        $user->update($data);

        $user->refresh();

        return $user;
    }

    public function delete(string $email): bool
    {
        $user = $this->model->whereEmail($email)->first();
        if (!$user) {
            throw new Exception("User not found");
        }
        return $user->delete();
    }
}
