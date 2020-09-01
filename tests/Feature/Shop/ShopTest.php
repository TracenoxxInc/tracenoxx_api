<?php

use App\Models\Shop\Shop;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use function Tests\passportActingAs;

uses(DatabaseMigrations::class);


beforeEach(function () {
    // authenticated user
    $this->user = passportActingAs();
});


it('returns a shop as a resource object', function () {

    // create a shop for that user
    $shop = factory(Shop::class)->create();

    // assert
    $this->getJson('/api/v1/shops/1', [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json'
    ])
        ->assertStatus(200)
        ->assertJson([
            'data' => [
                'id' => '1',
                'type' => 'shops',
                'attributes' => [
                    'name' => $shop->name,
                    'created_at' => $shop->created_at->toJSON(),
                    'updated_at' => $shop->updated_at->toJSON()
                ]
            ]
        ]);
});


it('returns all shops as a collection of resource objects', function () {
    // create 3 shops for that user
    $shops = factory(Shop::class, 3)->create();

    // assert
    $this->getJson('/api/v1/shops', [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json'
    ])
        ->assertStatus(200)
        ->assertJson([
            'data' => [
                [
                    "id" => "1",
                    "type" => "shops",
                    "attributes" => [
                        'name' => $shops[0]->name,
                        'created_at' => $shops[0]->created_at->toJSON(),
                        'updated_at' => $shops[0]->updated_at->toJSON()
                    ]
                ],
                [
                    "id" => "2",
                    "type" => "shops",
                    "attributes" => [
                        'name' => $shops[1]->name,
                        'created_at' => $shops[1]->created_at->toJSON(),
                        'updated_at' => $shops[1]->updated_at->toJSON()
                    ]
                ],
                [
                    "id" => "3",
                    "type" => "shops",
                    "attributes" => [
                        'name' => $shops[2]->name,
                        'created_at' => $shops[2]->created_at->toJSON(),
                        'updated_at' => $shops[2]->updated_at->toJSON()
                    ]
                ]
            ]
        ]);
});


it('can sort shops by name through a sort query parameter', function () {
    // create some shops with names
    $shops = collect([
        'My shop',
        'Awesome shop',
        'Cool shop'
    ])->map(function ($name) {
        return factory(Shop::class)->create([
            'name' => $name
        ]);
    });

    // assert
    $this->getJson('/api/v1/shops?sort=name', [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json'
    ])
        ->assertStatus(200)
        ->assertJson([
            'data' => [
                [
                    'id' => '2',
                    'type' => 'shops',
                    'attributes' => [
                        'name' => 'Awesome shop',
                        'created_at' => $shops[1]->created_at->toJSON(),
                        'updated_at' => $shops[1]->updated_at->toJSON()
                    ]
                ],
                [
                    'id' => '3',
                    'type' => 'shops',
                    'attributes' => [
                        'name' => 'Cool shop',
                        'created_at' => $shops[2]->created_at->toJSON(),
                        'updated_at' => $shops[2]->updated_at->toJSON()
                    ]
                ],
                [
                    'id' => '1',
                    'type' => 'shops',
                    'attributes' => [
                        'name' => 'My shop',
                        'created_at' => $shops[0]->created_at->toJSON(),
                        'updated_at' => $shops[0]->updated_at->toJSON()
                    ]
                ]
            ]
        ]);
})->group('sort_shops');


it('can sort shops by multiple attributes through a sort query parameter', function () {
    // create some shops with names
    $shops = collect([
        'My shop',
        'Awesome shop',
        'Cool shop'
    ])->map(function ($name) {
        // add 3 seconds delay for this shop
        if ($name === 'Cool shop') {
            return factory(Shop::class)->create([
                'name' => $name,
                'created_at' => now()->addSeconds(3)
            ]);
        }
        // create shops as usual
        return factory(Shop::class)->create([
            'name' => $name
        ]);
    });

    // assert
    $this->get('/api/v1/shops?sort=created_at,name', [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json'
    ])
        ->assertStatus(200)
        ->assertJson([
            'data' => [
                [
                    'id' => '2',
                    'type' => 'shops',
                    'attributes' => [
                        'name' => 'Awesome shop',
                        'created_at' => $shops[1]->created_at->toJSON(),
                        'updated_at' => $shops[1]->updated_at->toJSON()
                    ]
                ],
                [
                    'id' => '1',
                    'type' => 'shops',
                    'attributes' => [
                        'name' => 'My shop',
                        'created_at' => $shops[0]->created_at->toJSON(),
                        'updated_at' => $shops[0]->updated_at->toJSON()
                    ]
                ],
                [
                    'id' => '3',
                    'type' => 'shops',
                    'attributes' => [
                        'name' => 'Cool shop',
                        'created_at' => $shops[2]->created_at->toJSON(),
                        'updated_at' => $shops[2]->updated_at->toJSON()
                    ]
                ]
            ]
        ]);
})->group('sort_shops');


it('can sort shops by multiple attributes in descending order through a sort query parameter', function () {
    // create some shops with names
    $shops = collect([
        'My shop',
        'Awesome shop',
        'Cool shop'
    ])->map(function ($name) {
        // add 3 seconds delay for this shop
        if ($name === 'Cool shop') {
            return factory(Shop::class)->create([
                'name' => $name,
                'created_at' => now()->addSeconds(3)
            ]);
        }
        // create shops as usual
        return factory(Shop::class)->create([
            'name' => $name
        ]);
    });

    // assert
    $this->get('/api/v1/shops?sort=-created_at,name', [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json'
    ])
        ->assertStatus(200)
        ->assertJson([
            'data' => [
                [
                    'id' => '3',
                    'type' => 'shops',
                    'attributes' => [
                        'name' => 'Cool shop',
                        'created_at' => $shops[2]->created_at->toJSON(),
                        'updated_at' => $shops[2]->updated_at->toJSON()
                    ]
                ],
                [
                    'id' => '2',
                    'type' => 'shops',
                    'attributes' => [
                        'name' => 'Awesome shop',
                        'created_at' => $shops[1]->created_at->toJSON(),
                        'updated_at' => $shops[1]->updated_at->toJSON()
                    ]
                ],
                [
                    'id' => '1',
                    'type' => 'shops',
                    'attributes' => [
                        'name' => 'My shop',
                        'created_at' => $shops[0]->created_at->toJSON(),
                        'updated_at' => $shops[0]->updated_at->toJSON()
                    ]
                ]
            ]
        ]);
})->group('sort_shops');


it('can paginate shops through a page query parameter', function () {
    // create 10 shops for that user
    $shops = factory(Shop::class, 10)->create();

    // assert for per page = 5 and page number = 1
    $this->get('/api/v1/shops?page[size]=5&page[number]=1', [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json'
    ])
        ->assertStatus(200)
        ->assertJson([
            'data' => [
                [
                    'id' => '1',
                    'type' => 'shops',
                    'attributes' => [
                        'name' => $shops[0]->name,
                        'created_at' => $shops[0]->created_at->toJSON(),
                        'updated_at' => $shops[0]->updated_at->toJSON()
                    ]
                ],
                [
                    'id' => '2',
                    'type' => 'shops',
                    'attributes' => [
                        'name' => $shops[1]->name,
                        'created_at' => $shops[1]->created_at->toJSON(),
                        'updated_at' => $shops[1]->updated_at->toJSON()
                    ]
                ],
                [
                    'id' => '3',
                    'type' => 'shops',
                    'attributes' => [
                        'name' => $shops[2]->name,
                        'created_at' => $shops[2]->created_at->toJSON(),
                        'updated_at' => $shops[2]->updated_at->toJSON()
                    ]
                ],
                [
                    'id' => '4',
                    'type' => 'shops',
                    'attributes' => [
                        'name' => $shops[3]->name,
                        'created_at' => $shops[3]->created_at->toJSON(),
                        'updated_at' => $shops[3]->updated_at->toJSON()
                    ]
                ],
                [
                    'id' => '5',
                    'type' => 'shops',
                    'attributes' => [
                        'name' => $shops[4]->name,
                        'created_at' => $shops[4]->created_at->toJSON(),
                        'updated_at' => $shops[4]->updated_at->toJSON()
                    ]
                ],
            ],
            'links' => [
                'first' => route('shops.index', ['page[size]' => 5, 'page[number]' => 1]),
                'last' => route('shops.index', ['page[size]' => 5, 'page[number]' => 2]),
                'prev' => null,
                'next' => route('shops.index', ['page[size]' => 5, 'page[number]' => 2]),
            ]
        ]);
});


it('can create a shop from a resource object', function () {
    // assert
    $response = $this->postJson('/api/v1/shops', [
        'data' => [
            'type' => 'shops',
            'attributes' => [
                'name' => 'Cool Shop'
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
                'type' => 'shops',
                'attributes' => [
                    'name' => 'Cool Shop',
                    'created_at' => $createdTime,
                    'updated_at' => $updatedTime
                ]
            ]
        ])->assertHeader('Location', url('/api/v1/shops/1'));

    // assert the database has the shop's recored
    $this->assertDatabaseHas('shops', [
        'id' => '1',
        'name' => 'Cool Shop'
    ]);
});


it('validates that the type member is given when creating a shop', function () {
    // assert
    $this->postJson('/api/v1/shops', [
        'data' => [
            'type' => '',
            'attributes' => [
                'name' => 'Cool Shop'
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

    // assert the database has the shop's recored
    $this->assertDatabaseMissing('shops', [
        'id' => '1',
        'name' => 'Cool Shop'
    ]);
})->group('validate_create_shops');


it('validates that the type member has the value of shops when creating a shop', function () {
    // assert
    $this->postJson('/api/v1/shops', [
        'data' => [
            'type' => 'shop',
            'attributes' => [
                'name' => 'Cool Shop'
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
                    'details' => 'The selected data.type is invalid.',
                    'source' => [
                        'pointer' => '/data/type'
                    ]
                ]
            ]
        ]);

    // assert the database has the shop's recored
    $this->assertDatabaseMissing('shops', [
        'id' => '1',
        'name' => 'Cool Shop'
    ]);
})->group('validate_create_shops');


it('validates that the attributes member has been given when creating a shop', function () {
    // assert
    $this->postJson('/api/v1/shops', [
        'data' => [
            'type' => 'shops'
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

    // assert the database has the shop's recored
    $this->assertDatabaseMissing('shops', [
        'id' => '1',
        'name' => 'Cool Shop'
    ]);
})->group('validate_create_shops');


it('validates that the attributes member is an object given when creating a shop', function () {
    // assert
    $this->postJson('/api/v1/shops', [
        'data' => [
            'type' => 'shops',
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

    // assert the database has the shop's recored
    $this->assertDatabaseMissing('shops', [
        'id' => '1',
        'name' => 'Cool Shop'
    ]);
})->group('validate_create_shops');


it('validates that a name attribute is given when creating a shop', function () {
    // assert
    $this->postJson('/api/v1/shops', [
        'data' => [
            'type' => 'shops',
            'attributes' => [
                'name' => ''
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
                    'details' => 'The data.attributes.name field is required.',
                    'source' => [
                        'pointer' => '/data/attributes/name'
                    ]
                ]
            ]
        ]);

    // assert the database has the shop's recored
    $this->assertDatabaseMissing('shops', [
        'id' => '1',
        'name' => 'Cool Shop'
    ]);
})->group('validate_create_shops');


it('validates that a name attribute is a string when creating a shop', function () {
    // assert
    $this->postJson('/api/v1/shops', [
        'data' => [
            'type' => 'shops',
            'attributes' => [
                'name' => 33
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
                    'details' => 'The data.attributes.name must be a string.',
                    'source' => [
                        'pointer' => '/data/attributes/name'
                    ]
                ]
            ]
        ]);

    // assert the database has the shop's recored
    $this->assertDatabaseMissing('shops', [
        'id' => '1',
        'name' => 'Cool Shop'
    ]);
})->group('validate_create_shops');


it('validates that a name attribute is not more than 50 characters when creating a shop', function () {
    // assert
    $this->postJson('/api/v1/shops', [
        'data' => [
            'type' => 'shops',
            'attributes' => [
                'name' => 'It is a long established fact that a reader will be distracted by the readable' .
                    'content of a page when looking at its layout. The point of using Lorem Ipsum is that it ' .
                    'content of a page when looking at its layout. The point of using Lorem Ipsum is that it ' .
                    'content of a page when looking at its layout. The point of using Lorem Ipsum is that it ' .
                    'has a more-or-less normal distribution of letters'
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
                    'details' => 'The data.attributes.name may not be greater than 50 characters.',
                    'source' => [
                        'pointer' => '/data/attributes/name'
                    ]
                ]
            ]
        ]);

    // assert the database has the shop's recored
    $this->assertDatabaseMissing('shops', [
        'id' => '1',
        'name' => 'Cool Shop'
    ]);
})->group('validate_create_shops');


it('can update a shop from a resource object', function () {
    // create a shop
    $shop = factory(Shop::class)->create();

    // get the timestamp for now
    $creationTimestamp = $shop->created_at;
    sleep(5);

    // assert
    $response = $this->patchJson('/api/v1/shops/1', [
        'data' => [
            'id' => '1',
            'type' => 'shops',
            'attributes' => [
                'name' => 'Awesome Shop'
            ]
        ]
    ],  [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json',
    ]);

    // get the updated at time
    $updatedTime = $response['data']['attributes']['updated_at'];

    $response->assertStatus(200)
        ->assertJson([
            'data' => [
                'id' => '1',
                'type' => 'shops',
                'attributes' => [
                    'name' => 'Awesome Shop',
                    'created_at' => $creationTimestamp->toJSON(),
                    'updated_at' => $updatedTime
                ]
            ]
        ]);

    $this->assertDatabaseHas('shops', [
        'id' => '1',
        'name' => 'Awesome Shop'
    ]);
});


it('validates that an id member is given when updating a shop', function () {
    // create a shop
    $shop = factory(Shop::class)->create();

    // assert
    $this->patchJson('/api/v1/shops/1', [
        'data' => [
            'type' => 'shops',
            'attributes' => [
                'name' => 'Awesome Shop'
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
                    'details' => 'The data.id field is required.',
                    'source' => [
                        'pointer' => '/data/id'
                    ]
                ]
            ]
        ]);

    // assert the database has the shop's recored
    $this->assertDatabaseHas('shops', [
        'id' => '1',
        'name' => $shop->name
    ]);
})->group('validate_update_shops');


it('validates that an id member is a string when updating a shop', function () {
    // create a shop
    $shop = factory(Shop::class)->create();

    // assert
    $this->patchJson('/api/v1/shops/1', [
        'data' => [
            'id' => 1,
            'type' => 'shops',
            'attributes' => [
                'name' => 'Awesome Shop'
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
                    'details' => 'The data.id must be a string.',
                    'source' => [
                        'pointer' => '/data/id'
                    ]
                ]
            ]
        ]);

    // assert the database has the shop's recored
    $this->assertDatabaseHas('shops', [
        'id' => '1',
        'name' => $shop->name
    ]);
})->group('validate_update_shops');


it('validates that the type member is given when updating a shop', function () {
    // create a shop
    $shop = factory(Shop::class)->create();

    // assert
    $this->patchJson('/api/v1/shops/1', [
        'data' => [
            'id' => '1',
            'type' => '',
            'attributes' => [
                'name' => 'Awesome Shop'
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
                    'details' => 'The data.type field is required.',
                    'source' => [
                        'pointer' => '/data/type'
                    ]
                ]
            ]
        ]);

    // assert the database has the shop's recored
    $this->assertDatabaseHas('shops', [
        'id' => '1',
        'name' => $shop->name
    ]);
})->group('validate_update_shops');


it('validates that the type member has the value of shops when updating a shop', function () {
    // create a shop
    $shop = factory(Shop::class)->create();

    // assert
    $this->patchJson('/api/v1/shops/1', [
        'data' => [
            'id' => '1',
            'type' => 'shop',
            'attributes' => [
                'name' => 'Awesome Shop'
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
                    'details' => 'The selected data.type is invalid.',
                    'source' => [
                        'pointer' => '/data/type'
                    ]
                ]
            ]
        ]);

    // assert the database has the shop's recored
    $this->assertDatabaseHas('shops', [
        'id' => '1',
        'name' => $shop->name
    ]);
})->group('validate_update_shops');


it('validates that the attributes member has been given when updating a shop', function () {
    // create a shop
    $shop = factory(Shop::class)->create();

    // assert
    $this->patchJson('/api/v1/shops/1', [
        'data' => [
            'id' => '1',
            'type' => 'shops'
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

    // assert the database has the shop's recored
    $this->assertDatabaseHas('shops', [
        'id' => '1',
        'name' => $shop->name
    ]);
})->group('validate_update_shops');


it('validates that the attributes member is an object given when updating a shop', function () {
    // create a shop
    $shop = factory(Shop::class)->create();

    // assert
    $this->patchJson('/api/v1/shops/1', [
        'data' => [
            'id' => '1',
            'type' => 'shops',
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

    // assert the database has the shop's recored
    $this->assertDatabaseHas('shops', [
        'id' => '1',
        'name' => $shop->name
    ]);
})->group('validate_update_shops');


it('validates that a name attribute is a string when updating a shop', function () {
    // create a shop
    $shop = factory(Shop::class)->create();

    // assert
    $this->patchJson('/api/v1/shops/1', [
        'data' => [
            'id' => '1',
            'type' => 'shops',
            'attributes' => [
                'name' => 33
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
                    'details' => 'The data.attributes.name must be a string.',
                    'source' => [
                        'pointer' => '/data/attributes/name'
                    ]
                ]
            ]
        ]);

    // assert the database has the shop's recored
    $this->assertDatabaseHas('shops', [
        'id' => '1',
        'name' => $shop->name
    ]);
})->group('validate_update_shops');


it('validates that a name attribute is not more than 50 characters when updating a shop', function () {
    // create a shop
    $shop = factory(Shop::class)->create();

    // assert
    $this->patchJson('/api/v1/shops/1', [
        'data' => [
            'id' => '1',
            'type' => 'shops',
            'attributes' => [
                'name' => 'It is a long established fact that a reader will be distracted by the readable' .
                    'content of a page when looking at its layout. The point of using Lorem Ipsum is that it ' .
                    'content of a page when looking at its layout. The point of using Lorem Ipsum is that it ' .
                    'content of a page when looking at its layout. The point of using Lorem Ipsum is that it ' .
                    'has a more-or-less normal distribution of letters'
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
                    'details' => 'The data.attributes.name may not be greater than 50 characters.',
                    'source' => [
                        'pointer' => '/data/attributes/name'
                    ]
                ]
            ]
        ]);

    // assert the database has the shop's recored
    $this->assertDatabaseHas('shops', [
        'id' => '1',
        'name' => $shop->name
    ]);
})->group('validate_update_shops');


it('can delete a shop through a delete request', function () {
    // create a shop
    $shop = factory(Shop::class)->create();

    // assert 
    $this->delete('/api/v1/shops/1', [],  [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json',
    ])->assertStatus(204);

    $deletedTime = now()->setMilliseconds(0)->toJSON();

    // check the database does have the row
    $this->assertDatabaseHas('shops', [
        'id' => '1',
        'name' => $shop->name,
        'deleted_at' => $deletedTime
    ]);
});


it('can restore a deleted shop', function () {
    // create a shop
    $shop = factory(Shop::class)->create();

    // assert 
    $this->delete('/api/v1/shops/1', [],  [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json',
    ])->assertStatus(204);

    $deletedTime = now()->setMilliseconds(0)->toJSON();

    // check the database does have the row
    $this->assertDatabaseHas('shops', [
        'id' => '1',
        'name' => $shop->name,
        'deleted_at' => $deletedTime
    ]);

    // restore the shops
    Shop::withTrashed()->where('id', $shop->id)->restore();
    // assert restored
    $this->assertDatabaseHas('shops', [
        'id' => $shop->id,
        'deleted_at' => null
    ]);
});
