<?php

use App\Models\Seller;
use App\User;

use function Tests\passportActingAs;
use Illuminate\Foundation\Testing\DatabaseMigrations;

uses(DatabaseMigrations::class);


beforeEach(function () {
    // authenticated user
    $this->user = passportActingAs();
});



it('returns a seller as a resource object', function () {
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
        ->assertJson([
            'data' => [
                'id' => '1',
                'type' => 'sellers',
                'attributes' => [
                    'user_id' => $seller->user_id,
                    'created_at' => $seller->created_at->toJSON(),
                    'updated_at' => $seller->updated_at->toJSON()
                ]
            ]
        ]);
});


it('returns all sellers as a collection of resource objects', function () {
    // create 3 sellers for that user
    $sellers = factory(Seller::class, 3)->create();

    // assert
    $this->get('/api/v1/sellers', [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json'
    ])
        ->assertStatus(200)
        ->assertJson([
            'data' => [
                [
                    'id' => '1',
                    'type' => 'sellers',
                    'attributes' => [
                        'user_id' => $sellers[0]->user_id,
                        'created_at' => $sellers[0]->created_at->toJSON(),
                        'updated_at' => $sellers[0]->updated_at->toJSON()
                    ]
                ],
                [
                    "id" => "2",
                    "type" => "sellers",
                    "attributes" => [
                        'user_id' => $sellers[1]->user_id,
                        'created_at' => $sellers[1]->created_at->toJSON(),
                        'updated_at' => $sellers[1]->updated_at->toJSON()
                    ]
                ],
                [
                    "id" => "3",
                    "type" => "sellers",
                    "attributes" => [
                        'user_id' => $sellers[2]->user_id,
                        'created_at' => $sellers[2]->created_at->toJSON(),
                        'updated_at' => $sellers[2]->updated_at->toJSON()
                    ]
                ]
            ]
        ]);
});


it('it_can_paginate_sellers_through_a_page_query_parameter', function () {
    // create 10 shops for that user
    $sellers = factory(Seller::class, 10)->create();

    // assert for per page = 5 and page number = 1
    $this->get('/api/v1/sellers?page[size]=5&page[number]=1', [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json'
    ])
        ->assertStatus(200)
        ->assertJson([
            'data' => [
                [
                    'id' => '1',
                    'type' => 'sellers',
                    'attributes' => [
                        'user_id' => $sellers[0]->user_id,
                        'created_at' => $sellers[0]->created_at->toJSON(),
                        'updated_at' => $sellers[0]->updated_at->toJSON()
                    ]
                ],
                [
                    'id' => '2',
                    'type' => 'sellers',
                    'attributes' => [
                        'user_id' => $sellers[1]->user_id,
                        'created_at' => $sellers[1]->created_at->toJSON(),
                        'updated_at' => $sellers[1]->updated_at->toJSON()
                    ]
                ],
                [
                    'id' => '3',
                    'type' => 'sellers',
                    'attributes' => [
                        'user_id' => $sellers[2]->user_id,
                        'created_at' => $sellers[2]->created_at->toJSON(),
                        'updated_at' => $sellers[2]->updated_at->toJSON()
                    ]
                ],
                [
                    'id' => '4',
                    'type' => 'sellers',
                    'attributes' => [
                        'user_id' => $sellers[3]->user_id,
                        'created_at' => $sellers[3]->created_at->toJSON(),
                        'updated_at' => $sellers[3]->updated_at->toJSON()
                    ]
                ],
                [
                    'id' => '5',
                    'type' => 'sellers',
                    'attributes' => [
                        'user_id' => $sellers[4]->user_id,
                        'created_at' => $sellers[4]->created_at->toJSON(),
                        'updated_at' => $sellers[4]->updated_at->toJSON()
                    ]
                ]
            ],
            'links' => [
                'first' => route('sellers.index', ['page[size]' => 5, 'page[number]' => 1]),
                'last' => route('sellers.index', ['page[size]' => 5, 'page[number]' => 2]),
                'prev' => null,
                'next' => route('sellers.index', ['page[size]' => 5, 'page[number]' => 2]),
            ]
        ]);
});


it('can delete a seller through a delete request', function () {
    // create a seller
    $seller = factory(Seller::class)->create([
        'user_id' => $this->user->id
    ]);

    // assert 
    $this->delete('/api/v1/sellers/1', [],  [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json',
    ])->assertStatus(204);

    $deletedTime = now()->setMilliseconds(0)->toJSON();

    // check the database doesn't have the row
    $this->assertDatabaseHas('sellers', [
        'id' => '1',
        'user_id' => $seller->user_id,
        'deleted_at' => $deletedTime
    ]);
});


it('can restore a deleted seller', function () {
    // create a seller
    $seller = factory(Seller::class)->create([
        'user_id' => $this->user->id
    ]);

    // delete the seller
    $seller->delete();

    $deletedTime = now()->setMilliseconds(0)->toJSON();

    // assert deleted
    $this->assertDatabaseHas('sellers', [
        'id' => '1',
        'user_id' => $seller->user_id,
        'deleted_at' => $deletedTime
    ]);

    // restore the seller
    Seller::withTrashed()->where('id', $seller->id)->restore();
    // assert restored
    $this->assertDatabaseHas('sellers', [
        'id' => $seller->id,
        'deleted_at' => null
    ]);
});


it('can create a seller from a resource object', function () {
    // assert
    $response = $this->postJson('/api/v1/sellers', [
        'data' => [
            'type' => 'sellers',
            'attributes' => [
                'user_id' => $this->user->id
            ]
        ]
    ],  [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json',
    ]);

    // get the created and updated at time
    $createdTime = $response['data']['attributes']['created_at'];
    $updatedTime = $response['data']['attributes']['updated_at'];

    $response->assertStatus(201)
        ->assertJson([
            'data' => [
                'id' => '1',
                'type' => 'sellers',
                'attributes' => [
                    'user_id' => $this->user->id,
                    'created_at' => $createdTime,
                    'updated_at' => $updatedTime
                ]
            ]
        ]);

    // assert the database has the sellers's recored
    $this->assertDatabaseHas('sellers', [
        'id' => '1',
        'user_id' => $this->user->id
    ]);
});


it('validates that the type member is given when creating a seller', function () {
    // assert
    $this->postJson('/api/v1/sellers', [
        'data' => [
            'type' => '',
            'attributes' => [
                'user_id' => $this->user->id
            ]
        ]
    ], [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json',
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

    // assert the database has the sellers's recored
    $this->assertDatabaseMissing('sellers', [
        'id' => '1',
        'user_id' => $this->user->id
    ]);
})->group('validate_create_sellers');


it('validates that the type member has the value of users when creating a seller', function () {
    // assert
    $this->postJson('/api/v1/sellers', [
        'data' => [
            'type' => 'other',
            'attributes' => [
                'user_id' => $this->user->id
            ]
        ]
    ], [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json',
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

    // assert the database has the sellers's recored
    $this->assertDatabaseMissing('sellers', [
        'id' => '1',
        'user_id' => $this->user->id
    ]);
})->group('validate_create_sellers');


it('validates that the attributes member has been given when creating a seller', function () {
    // assert
    $this->postJson('/api/v1/sellers', [
        'data' => [
            'type' => 'sellers'
        ]
    ],  [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json',
    ])
        ->assertStatus(422)
        ->assertJson([
            'errors' => [
                [
                    'title' => 'Validation Error',
                    'details' => 'The data.attributes field is required.',
                    'source' => [
                        'pointer' => '/data/attributes'
                    ]
                ]
            ]
        ]);

    // assert the database has the sellers's recored
    $this->assertDatabaseMissing('sellers', [
        'id' => '1',
        'user_id' => $this->user->id
    ]);
})->group('validate_create_sellers');


it('validates that the attributes member is an object given when creating a seller', function () {
    // assert
    $this->postJson('/api/v1/sellers', [
        'data' => [
            'type' => 'sellers',
            'attributes' => 'not an object'
        ]
    ],  [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json',
    ])
        ->assertStatus(422)
        ->assertJson([
            'errors' => [
                [
                    'title' => 'Validation Error',
                    'details' => 'The data.attributes must be an array.',
                    'source' => [
                        'pointer' => '/data/attributes'
                    ]
                ]
            ]
        ]);

    // assert the database has the sellers's recored
    $this->assertDatabaseMissing('sellers', [
        'id' => '1',
        'user_id' => $this->user->id
    ]);
})->group('validate_create_sellers');


it('validates that a user id attribute is given when creating a seller', function () {
    // assert
    $this->postJson('/api/v1/sellers', [
        'data' => [
            'type' => 'sellers',
            'attributes' => [
                'user_id' => ''
            ]
        ]
    ],  [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json',
    ])
        ->assertStatus(422)
        ->assertJson([
            'errors' => [
                [
                    'title' => 'Validation Error',
                    'details' => 'The data.attributes.user id field is required.',
                    'source' => [
                        'pointer' => '/data/attributes/user_id'
                    ]
                ]
            ]
        ]);

    // assert the database has the sellers's recored
    $this->assertDatabaseMissing('sellers', [
        'id' => '1',
        'user_id' => $this->user->id
    ]);
})->group('validate_create_sellers');


it('validates that a user id attribute is unique when creating a seller', function () {

    // create a seller
    $seller = factory(Seller::class)->create();

    // now create another seller with the same user id
    $this->postJson('/api/v1/sellers', [
        'data' => [
            'type' => 'sellers',
            'attributes' => [
                'user_id' => $seller->user_id,
            ]
        ]
    ],  [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json',
    ])
        ->assertStatus(422)
        ->assertJson([
            'errors' => [
                [
                    'title' => 'Validation Error',
                    'details' => 'The data.attributes.user id has already been taken.',
                    'source' => [
                        'pointer' => '/data/attributes/user_id'
                    ]
                ]
            ]
        ]);
})->group('validate_create_sellers');


it('validates that a user id attribute is unique when updating a seller', function () {

    // create a seller
    $seller1 = factory(Seller::class)->create();
    // create another seller
    $seller2 = factory(Seller::class)->create();

    // now update the seller2 with the same user id of the seller1
    $this->patchJson("/api/v1/sellers/{$seller2->id}", [
        'data' => [
            'id' => (string) $seller2->id,
            'type' => 'sellers',
            'attributes' => [
                'user_id' => $seller2->user_id,
            ]
        ]
    ],  [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json',
    ])
        ->assertStatus(422)
        ->assertJson([
            'errors' => [
                [
                    'title' => 'Validation Error',
                    'details' => 'The data.attributes.user id has already been taken.',
                    'source' => [
                        'pointer' => '/data/attributes/user_id'
                    ]
                ]
            ]
        ]);
})->group('validate_update_sellers');
