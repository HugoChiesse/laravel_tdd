<?php

namespace Tests\Feature\App\Repository\Eloquent;

use App\Models\User;
use App\Repository\Eloquent\UserRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Interfaces\Eloquent\UserRepositoryInterface;
use Exception;
use Illuminate\Database\QueryException;

class UserRepositoryTest extends TestCase
{
    protected $repository;

    protected function setUp(): void
    {
        $this->repository = new UserRepository(new User());

        parent::setUp();
    }
    public function test_implements_interfaces()
    {
        $this->assertInstanceOf(
            UserRepositoryInterface::class,
            $this->repository
        );
    }
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_find_all_empty()
    {
        $response = $this->repository->findAll();

        $this->assertIsArray($response);
        $this->assertCount(0, $response);
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_find_all()
    {
        User::factory()->count(100)->create();
        $response = $this->repository->findAll();

        $this->assertIsArray($response);
        $this->assertCount(100, $response);
    }

    public function test_create()
    {
        $data = [
            'name' => 'Hugo Ferreira Chiesse',
            'email' => 'hugochiesse@gmail.com',
            'password' => bcrypt('Vasco@5247')
        ];
        $response = $this->repository->create($data);

        $this->assertNotNull($response);
        $this->assertIsObject($response);
        $this->assertDatabaseHas('users', [
            'email' => 'hugochiesse@gmail.com'
        ]);
    }

    public function test_create_exception()
    {
        $this->expectException(QueryException::class);
        
        $data = [
            'name' => 'Hugo Ferreira Chiesse',
            'password' => bcrypt('Vasco@5247')
        ];
        $response = $this->repository->create($data);
    }

    public function test_update()
    {
        $user = User::factory()->create();

        $data = ['name' => 'new name'];

        $response = $this->repository->update($user->email, $data);

        $this->assertNotNull($response);
        $this->assertIsObject($response);
        $this->assertDatabaseHas('users', [
            'name' => 'new name'
        ]);
    }

    public function test_delete()
    {
        $user = User::factory()->create();

        $deleted = $this->repository->delete($user->email);

        $this->assertTrue($deleted);
        $this->assertDatabaseMissing('users', [
            'email' => $user->email
        ]);
    }

    public function test_delete_not_found()
    {
        try {
            $deleted = $this->repository->delete('fake_email');

            $this->assertTrue(false);
        } catch (\Throwable $th) {
            $this->assertInstanceOf(\Exception::class, $th);
        }
        
        
    }
}