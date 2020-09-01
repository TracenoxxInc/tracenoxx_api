<?php

use App\User;
use App\Models\Seller;

use App\Models\Shop\Shop;
use function Tests\passportActingAs;
use function Pest\Laravel\withoutExceptionHandling;
use Illuminate\Foundation\Testing\DatabaseMigrations;

uses(DatabaseMigrations::class);


beforeEach(function () {
    // authenticated user
    $this->user = passportActingAs();
});


it('returns a relationship to user adhering to json api spec', function () {
    // create a user
    $user = factory(User::class)->create();
    // create a seller
    $seller = factory(Seller::class)->create([
        'user_id' => $user->id
    ]);

    // assert
    $this->getJson('/api/v1/sellers/1?include=users', [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json'
    ])
        ->assertStatus(200)
        ->assertJson([
            'data' => [
                'id' => '1',
                'type' => 'sellers',
                'attributes' => [
                    'user_id' => $seller->user_id,
                    'created_at' => $seller->created_at->toJSON(),
                    'updated_at' => $seller->updated_at->toJSON(),
                    'deleted_at' => $seller->deleted_at
                ],
                'relationships' => [
                    'users' => [
                        'links' => [
                            'self' => route('sellers.relationships.users', [
                                'seller' => $seller->id
                            ]),
                            'related' => route('sellers.users', [
                                'seller' => $seller->id
                            ])
                        ],
                        'data' => [
                            'id' => $user->id,
                            'type' => 'users'
                        ]
                    ]
                ]
            ],
            'included' => [
                [
                    'id' => $user->id,
                    'type' => 'users',
                    'attributes' => [
                        'name' => $user->name,
                        'email' => $user->email,
                        'created_at' => $user->created_at->toJSON(),
                        'updated_at' => $user->updated_at->toJSON(),
                        'deleted_at' => $user->deleted_at
                    ]
                ]
            ]
        ]);
});


it('returns a relationship to both user and shops adhering to json api spec', function () {
    // create a user
    $user = factory(User::class)->create();
    // create a seller
    $seller = factory(Seller::class)->create([
        'user_id' => $user->id
    ]);
    // create some shops
    $shops = factory(Shop::class, 3)->create();
    // sync those shops to this seller
    $seller->shops()->sync($shops->pluck('id'));

    // assert
    $this->getJson('/api/v1/sellers/1?include=users,shops', [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json'
    ])
        ->assertStatus(200)
        ->assertJson([
            'data' => [
                'id' => '1',
                'type' => 'sellers',
                'attributes' => [
                    'user_id' => $seller->user_id,
                    'created_at' => $seller->created_at->toJSON(),
                    'updated_at' => $seller->updated_at->toJSON()
                ],
                'relationships' => [
                    'shops' => [
                        'links' => [
                            'self' => route('sellers.relationships.shops', [
                                'seller' => $seller->id
                            ]),
                            'related' => route('sellers.shops', [
                                'seller' => $seller->id
                            ])
                        ],
                        'data' => [
                            [
                                'id' => $shops->get(0)->id,
                                'type' => 'shops'
                            ],
                            [
                                'id' => $shops->get(1)->id,
                                'type' => 'shops'
                            ],
                            [
                                'id' => $shops->get(2)->id,
                                'type' => 'shops'
                            ]
                        ]
                    ],
                    'users' => [
                        'links' => [
                            'self' => route('sellers.relationships.users', [
                                'seller' => $seller->id
                            ]),
                            'related' => route('sellers.users', [
                                'seller' => $seller->id
                            ])
                        ],
                        'data' => [
                            'id' => $user->id,
                            'type' => 'users'
                        ]
                    ]
                ]
            ]
        ]);
});


test('a relationship link to user with seller returns a user as resource id object', function () {
    // create a user
    $user = factory(User::class)->create();
    // create a seller
    $seller = factory(Seller::class)->create([
        'user_id' => $user->id
    ]);

    // assert
    $this->getJson('/api/v1/sellers/1/relationships/users', [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json'
    ])
        ->assertStatus(200)
        ->assertJson([
            'data' => [
                'id' => $user->id,
                'type' => 'users'
            ]
        ]);
});


it('can modify relationships to user and add new relationships', function () {
    // create a user
    $user1 = factory(User::class)->create();
    // create a seller
    $seller = factory(Seller::class)->create([
        'user_id' => $user1->id
    ]);
    // create another user
    $user2 = factory(User::class)->create();

    // assert
    $this->patchJson('/api/v1/sellers/1/relationships/users', [
        'data' => [
            'id' => $user2->id,
            'type' => 'users'
        ]
    ], [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json'
    ])->assertStatus(204);

    $this->assertDatabaseHas('sellers', [
        'id' => 1,
        'user_id' => $user2->id
    ]);
});


it('can modify relationships to user with sellers and remove relationships', function () {
    // create a user
    $user1 = factory(User::class)->create();
    // create a seller
    $seller = factory(Seller::class)->create([
        'user_id' => $user1->id
    ]);
    // create another user
    $user2 = factory(User::class)->create();

    // assert
    $this->patchJson('/api/v1/sellers/1/relationships/users', [
        'data' => [
            'id' => $user2->id,
            'type' => 'users'
        ]
    ], [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json'
    ])->assertStatus(204);

    $this->assertDatabaseHas('sellers', [
        'id' => 1,
        'user_id' => $user2->id
    ])->assertDatabaseMissing('sellers', [
        'id' => 1,
        'user_id' => $user1->id
    ]);
});


it('returns a 404 not found when trying to add relationship to a non existing user from sellers', function () {
    // create a user
    $user = factory(User::class)->create();
    // create a seller
    $seller = factory(Seller::class)->create([
        'user_id' => $user->id
    ]);

    // assert
    $this->patchJson('/api/v1/sellers/1/relationships/users', [
        'data' => [
            'id' => 'user-does-not-exist',
            'type' => 'users'
        ]
    ], [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json'
    ])
        ->assertStatus(404)
        ->assertJson([
            'errors' => [
                [
                    'title' => 'Not Found Http Exception',
                    'details' => 'Resource not found'
                ]
            ]
        ]);
});


it('validates that the id member is given when updating a relationship to user from sellers', function () {
    // create a user
    $user = factory(User::class)->create();
    // create a seller
    $seller = factory(Seller::class)->create([
        'user_id' => $user->id
    ]);

    // assert
    $this->patchJson('/api/v1/sellers/1/relationships/users', [
        'data' => [
            'type' => 'users'
        ]
    ], [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json'
    ])
        ->assertStatus(422)
        ->assertJson([
            'errors' => [
                [
                    'title' => 'Validation Error',
                    'details' => 'The data.id field is required.',
                    'source' => [
                        'pointer' => '/data/id'
                    ]
                ]
            ]
        ]);
})->group('validate_user_relation');


it('validates that the id member is a string when updating a relationship to user from sellers', function () {
    // create a user
    $user = factory(User::class)->create();
    // create a seller
    $seller = factory(Seller::class)->create([
        'user_id' => $user->id
    ]);

    // assert
    $this->patchJson('/api/v1/sellers/1/relationships/users', [
        'data' => [
            'id' => 5,
            'type' => 'shops'
        ]
    ], [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json'
    ])
        ->assertStatus(422)
        ->assertJson([
            'errors' => [
                [
                    'title' => 'Validation Error',
                    'details' => 'The data.id must be a string.',
                    'source' => [
                        'pointer' => '/data/id'
                    ]
                ]
            ]
        ]);
})->group('validate_user_relation');


it('validates that the type member is a string when updating a relationship to user from sellers', function () {
    // create a user
    $user = factory(User::class)->create();
    // create a seller
    $seller = factory(Seller::class)->create([
        'user_id' => $user->id
    ]);

    // assert
    $this->patchJson('/api/v1/sellers/1/relationships/users', [
        'data' => [
            'id' => '5'
        ]
    ], [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json'
    ])
        ->assertStatus(422)
        ->assertJson([
            'errors' => [
                [
                    'title' => 'Validation Error',
                    'details' => 'The data.type field is required.',
                    'source' => [
                        'pointer' => '/data/type'
                    ]
                ]
            ]
        ]);
})->group('validate_user_relation');


it('validates that the type member has a value of users when updating a relationship from sellers', function () {
    // create a user
    $user = factory(User::class)->create();
    // create a seller
    $seller = factory(Seller::class)->create([
        'user_id' => $user->id
    ]);

    // assert
    $this->patchJson('/api/v1/sellers/1/relationships/users', [
        'data' => [
            'id' => '5',
            'type' => 'random'
        ]
    ], [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json'
    ])
        ->assertStatus(422)
        ->assertJson([
            'errors' => [
                [
                    'title' => 'Validation Error',
                    'details' => 'The selected data.type is invalid.',
                    'source' => [
                        'pointer' => '/data/type'
                    ]
                ]
            ]
        ]);
})->group('validate_user_relation');


it('can get related user as resource objects from related link', function () {
    // create a user
    $user = factory(User::class)->create();
    // create a seller
    $seller = factory(Seller::class)->create([
        'user_id' => $user->id
    ]);

    // assert
    $this->getJson('/api/v1/sellers/1/users', [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json'
    ])
        ->assertStatus(200)
        ->assertJson([
            'data' => [
                'id' => $user->id,
                'type' => 'users',
                'attributes' => [
                    'name' => $user->name,
                    'email' => $user->email,
                    'created_at' => $user->created_at->toJSON(),
                    'updated_at' => $user->updated_at->toJSON()
                ]

            ]
        ]);
})->group('validate_user_related');


it('includes related resource object for user when an include query param to user is given from seller', function () {
    // create a user
    $user = factory(User::class)->create();
    // create a seller
    $seller = factory(Seller::class)->create([
        'user_id' => $user->id
    ]);

    // assert
    $this->getJson('/api/v1/sellers/1?include=users', [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json'
    ])
        ->assertStatus(200)
        ->assertJson([
            'data' => [
                'id' => '1',
                'type' => 'sellers',
                'relationships' => [
                    'users' => [
                        'links' => [
                            'self' => route('sellers.relationships.users', [
                                'seller' => $seller->id
                            ]),
                            'related' => route('sellers.users', [
                                'seller' => $seller->id
                            ])
                        ],
                        'data' => [
                            'id' => $user->id,
                            'type' => 'users'
                        ]
                    ]
                ]
            ],
            'included' => [
                [
                    'id' => $user->id,
                    'type' => 'users',
                    'attributes' => [
                        'name' => $user->name,
                        'email' => $user->email,
                        'created_at' => $user->created_at->toJSON(),
                        'updated_at' => $user->updated_at->toJSON()
                    ]
                ]
            ]
        ]);
})->group('validate_user_related');


it('does not include related resource objects for users when an include query param to users is not given from seller', function () {
    // create a seller
    $seller = factory(Seller::class)->create([
        'user_id' => $this->user->id
    ]);

    // assert
    $this->getJson('/api/v1/sellers/1', [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json'
    ])
        ->assertStatus(200)
        ->assertJsonMissing([
            'included' => []
        ]);
})->group('validate_user_related');
