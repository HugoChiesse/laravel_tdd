<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use SebastianBergmann\RecursionContext\InvalidArgumentException;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\Exception;
use Tests\TestCase;
use Throwable;

class UserApiTest extends TestCase
{
    protected string $endpoint = '/api/users';
    public function test_paginate_empty()
    {
        $response = $this->getJson($this->endpoint);
        // $response->assertStatus(Response::HTTP_OK);
        $response->assertOk();
        $response->assertJsonCount(0, 'data');
        $response->assertJsonStructure([
            'meta' => [
                'total',
                'current_page',
                'first_page',
                'per_page',
            ],
            'data'
        ]);
        $response->assertJsonFragment([
            'total' => 0
        ]);
    }
    /**
     * 
     * @dataProvider dataProviderPaginator
     */
    public function test_paginate(
        int $total,
        int $page = 1,
        int $totalPage = 15
    ) {
        User::factory()->count($total)->create();

        $response = $this->getJson("{$this->endpoint}?page={$page}");
        // $response->assertStatus(Response::HTTP_OK);
        $response->assertOk();
        $response->assertJsonCount($totalPage, 'data');
        $response->assertJsonStructure([
            'meta' => [
                'total',
                'current_page',
                'first_page',
                'per_page',
            ],
            'data'
        ]);

        $response->assertJsonFragment([
            'total' => $total,
            'current_page' => $page,
        ]);
    }

    public function test_paginate_two()
    {
        User::factory()->count(20)->create();

        $response = $this->getJson("{$this->endpoint}?page=2");
        // $response->assertStatus(Response::HTTP_OK);
        $response->assertOk();
        $response->assertJsonCount(5, 'data');

        $response->assertJsonFragment([
            'total' => 20,
            'current_page' => 2,
        ]);
    }

    /**
     *@dataProvider dataProviderCreateUser
     */
    public function test_create(
        array $payload,
        int $status_code,
        array $structureResponse
    ) {
        $response = $this->postJson($this->endpoint, $payload);
        // $response->assertCreated();
        $response->assertStatus($status_code);
        $response->assertJsonStructure($structureResponse);
    }

    public function test_Find()
    {
        $user = User::factory()->create();
        $response = $this->getJson("{$this->endpoint}/{$user->email}");
        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                'name',
                'email'
            ],
        ]);
    }

    public function dataProviderPaginator(): array
    {
        return [
            'test total user 90, page 1, totalPage 15' => ['total' => 90, 'page' => 1, 'totalPage' => 15],
            'test total user 90, page 2, totalPage 15' => ['total' => 90, 'page' => 2, 'totalPage' => 15],
            'test total user 90, page 3, totalPage 15' => ['total' => 90, 'page' => 3, 'totalPage' => 15],
            'test total user 90, page 4, totalPage 15' => ['total' => 90, 'page' => 4, 'totalPage' => 15],
            'test total user 90, page 5, totalPage 15' => ['total' => 90, 'page' => 5, 'totalPage' => 15],
            'test total user 190, page 12, totalPage 15' => ['total' => 190, 'page' => 12, 'totalPage' => 15],
            'test total user 190, page 13, totalPage 15' => ['total' => 190, 'page' => 13, 'totalPage' => 10],
        ];
    }

    public function dataProviderCreateUser(): array
    {
        return [
            'test created' => [
                'payload' => [
                    'name' => 'Hugo Chiesse',
                    'email' => 'hugochiesse@gmail.com',
                    'password' => '33225247',
                ],
                'status_code' => Response::HTTP_CREATED,
                'structureResponse' => [
                    'data' => [
                        'name',
                        'email',
                    ],
                ]
            ],
            'test validation' => [
                'payload' => [],
                'status_code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'structureResponse' => [
                    'errors' => [
                        'name'
                    ]
                ]
            ],
        ];
    }
}
