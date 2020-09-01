<?php

use App\Models\Shop\ShopType;
use Illuminate\Foundation\Testing\DatabaseMigrations;

use function Pest\Laravel\withoutExceptionHandling;
use function Tests\passportActingAs;

uses(DatabaseMigrations::class);

beforeEach(function () {
    // authenticated user
    $this->user = passportActingAs();
});


it('returns a shop type as a resource object', function () {

    // create a shop type 
    $shopType = factory(ShopType::class)->create();

    // assert
    $this->getJson('/api/v1/shop-types/1', [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json'
    ])
        ->assertStatus(200)
        ->assertJson([
            'data' => [
                'id' => '1',
                'type' => 'shop-types',
                'attributes' => [
                    'name' => $shopType->name,
                    'description' => $shopType->description,
                    'image' => $shopType->image,
                    'created_at' => $shopType->created_at->toJSON(),
                    'updated_at' => $shopType->updated_at->toJSON()
                ]
            ]
        ]);
});


it('returns all shop types as a collection of resource objects', function () {
    // create 3 shop types
    $shopTypes = factory(ShopType::class, 3)->create();

    // assert
    $this->getJson('/api/v1/shop-types', [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json'
    ])
        ->assertStatus(200)
        ->assertJson([
            'data' => [
                [
                    "id" => "1",
                    "type" => "shop-types",
                    "attributes" => [
                        'name' => $shopTypes[0]->name,
                        'description' => $shopTypes[0]->description,
                        'image' => $shopTypes[0]->image,
                        'created_at' => $shopTypes[0]->created_at->toJSON(),
                        'updated_at' => $shopTypes[0]->updated_at->toJSON()
                    ]
                ],
                [
                    "id" => "2",
                    "type" => "shop-types",
                    "attributes" => [
                        'name' => $shopTypes[1]->name,
                        'description' => $shopTypes[1]->description,
                        'image' => $shopTypes[1]->image,
                        'created_at' => $shopTypes[1]->created_at->toJSON(),
                        'updated_at' => $shopTypes[1]->updated_at->toJSON()
                    ]
                ],
                [
                    "id" => "3",
                    "type" => "shop-types",
                    "attributes" => [
                        'name' => $shopTypes[2]->name,
                        'description' => $shopTypes[2]->description,
                        'image' => $shopTypes[2]->image,
                        'created_at' => $shopTypes[2]->created_at->toJSON(),
                        'updated_at' => $shopTypes[2]->updated_at->toJSON()
                    ]
                ]
            ]
        ]);
});


it('can sort shop types by name through a sort query parameter', function () {
    // create some shop types 
    $shopTypes = collect([
        'My shop',
        'Awesome shop',
        'Cool shop'
    ])->map(function ($name) {
        return factory(ShopType::class)->create([
            'name' => $name
        ]);
    });

    // assert
    $this->getJson('/api/v1/shop-types?sort=name', [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json'
    ])
        ->assertStatus(200)
        ->assertJson([
            'data' => [
                [
                    'id' => '2',
                    'type' => 'shop-types',
                    'attributes' => [
                        'name' => 'Awesome shop',
                        'description' => $shopTypes[1]->description,
                        'image' => $shopTypes[1]->image,
                        'created_at' => $shopTypes[1]->created_at->toJSON(),
                        'updated_at' => $shopTypes[1]->updated_at->toJSON()
                    ]
                ],
                [
                    'id' => '3',
                    'type' => 'shop-types',
                    'attributes' => [
                        'name' => 'Cool shop',
                        'description' => $shopTypes[2]->description,
                        'image' => $shopTypes[2]->image,
                        'created_at' => $shopTypes[2]->created_at->toJSON(),
                        'updated_at' => $shopTypes[2]->updated_at->toJSON()
                    ]
                ],
                [
                    'id' => '1',
                    'type' => 'shop-types',
                    'attributes' => [
                        'name' => 'My shop',
                        'description' => $shopTypes[0]->description,
                        'image' => $shopTypes[0]->image,
                        'created_at' => $shopTypes[0]->created_at->toJSON(),
                        'updated_at' => $shopTypes[0]->updated_at->toJSON()
                    ]
                ]
            ]
        ]);
})->group('sort_shop_types');


it('can sort shop types by multiple attributes through a sort query parameter', function () {
    // create some shop types
    $shopTypes = collect([
        'My shop',
        'Awesome shop',
        'Cool shop'
    ])->map(function ($name) {
        // add 3 seconds delay for this shop type
        if ($name === 'Cool shop') {
            return factory(ShopType::class)->create([
                'name' => $name,
                'created_at' => now()->addSeconds(3)
            ]);
        }
        // create shops as usual
        return factory(ShopType::class)->create([
            'name' => $name
        ]);
    });

    // assert
    $this->get('/api/v1/shop-types?sort=created_at,name', [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json'
    ])
        ->assertStatus(200)
        ->assertJson([
            'data' => [
                [
                    'id' => '2',
                    'type' => 'shop-types',
                    'attributes' => [
                        'name' => 'Awesome shop',
                        'description' => $shopTypes[1]->description,
                        'image' => $shopTypes[1]->image,
                        'created_at' => $shopTypes[1]->created_at->toJSON(),
                        'updated_at' => $shopTypes[1]->updated_at->toJSON()
                    ]
                ],
                [
                    'id' => '1',
                    'type' => 'shop-types',
                    'attributes' => [
                        'name' => 'My shop',
                        'description' => $shopTypes[0]->description,
                        'image' => $shopTypes[0]->image,
                        'created_at' => $shopTypes[0]->created_at->toJSON(),
                        'updated_at' => $shopTypes[0]->updated_at->toJSON()
                    ]
                ],
                [
                    'id' => '3',
                    'type' => 'shop-types',
                    'attributes' => [
                        'name' => 'Cool shop',
                        'description' => $shopTypes[2]->description,
                        'image' => $shopTypes[2]->image,
                        'created_at' => $shopTypes[2]->created_at->toJSON(),
                        'updated_at' => $shopTypes[2]->updated_at->toJSON()
                    ]
                ]
            ]
        ]);
})->group('sort_shop_types');


it('can sort shop types by multiple attributes in descending order through a sort query parameter', function () {
    // create some shops with names
    $shopTypes = collect([
        'My shop',
        'Awesome shop',
        'Cool shop'
    ])->map(function ($name) {
        // add 3 seconds delay for this shop type
        if ($name === 'Cool shop') {
            return factory(ShopType::class)->create([
                'name' => $name,
                'created_at' => now()->addSeconds(3)
            ]);
        }
        // create shops as usual
        return factory(ShopType::class)->create([
            'name' => $name
        ]);
    });

    // assert
    $this->get('/api/v1/shop-types?sort=-created_at,name', [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json'
    ])
        ->assertStatus(200)
        ->assertJson([
            'data' => [
                [
                    'id' => '3',
                    'type' => 'shop-types',
                    'attributes' => [
                        'name' => 'Cool shop',
                        'description' => $shopTypes[2]->description,
                        'image' => $shopTypes[2]->image,
                        'created_at' => $shopTypes[2]->created_at->toJSON(),
                        'updated_at' => $shopTypes[2]->updated_at->toJSON()
                    ]
                ],
                [
                    'id' => '2',
                    'type' => 'shop-types',
                    'attributes' => [
                        'name' => 'Awesome shop',
                        'description' => $shopTypes[1]->description,
                        'image' => $shopTypes[1]->image,
                        'created_at' => $shopTypes[1]->created_at->toJSON(),
                        'updated_at' => $shopTypes[1]->updated_at->toJSON()
                    ]
                ],
                [
                    'id' => '1',
                    'type' => 'shop-types',
                    'attributes' => [
                        'name' => 'My shop',
                        'description' => $shopTypes[0]->description,
                        'image' => $shopTypes[0]->image,
                        'created_at' => $shopTypes[0]->created_at->toJSON(),
                        'updated_at' => $shopTypes[0]->updated_at->toJSON()
                    ]
                ]
            ]
        ]);
})->group('sort_shop_types');


it('can paginate shop types through a page query parameter', function () {
    // create 10 shop types
    $shopTypes = factory(ShopType::class, 10)->create();

    // assert for per page = 5 and page number = 1
    $this->get('/api/v1/shop-types?page[size]=5&page[number]=1', [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json'
    ])
        ->assertStatus(200)
        ->assertJson([
            'data' => [
                [
                    'id' => '1',
                    'type' => 'shop-types',
                    'attributes' => [
                        'name' => $shopTypes[0]->name,
                        'description' => $shopTypes[0]->description,
                        'image' => $shopTypes[0]->image,
                        'created_at' => $shopTypes[0]->created_at->toJSON(),
                        'updated_at' => $shopTypes[0]->updated_at->toJSON()
                    ]
                ],
                [
                    'id' => '2',
                    'type' => 'shop-types',
                    'attributes' => [
                        'name' => $shopTypes[1]->name,
                        'description' => $shopTypes[1]->description,
                        'image' => $shopTypes[1]->image,
                        'created_at' => $shopTypes[1]->created_at->toJSON(),
                        'updated_at' => $shopTypes[1]->updated_at->toJSON()
                    ]
                ],
                [
                    'id' => '3',
                    'type' => 'shop-types',
                    'attributes' => [
                        'name' => $shopTypes[2]->name,
                        'description' => $shopTypes[2]->description,
                        'image' => $shopTypes[2]->image,
                        'created_at' => $shopTypes[2]->created_at->toJSON(),
                        'updated_at' => $shopTypes[2]->updated_at->toJSON()
                    ]
                ],
                [
                    'id' => '4',
                    'type' => 'shop-types',
                    'attributes' => [
                        'name' => $shopTypes[3]->name,
                        'description' => $shopTypes[3]->description,
                        'image' => $shopTypes[3]->image,
                        'created_at' => $shopTypes[3]->created_at->toJSON(),
                        'updated_at' => $shopTypes[3]->updated_at->toJSON()
                    ]
                ],
                [
                    'id' => '5',
                    'type' => 'shop-types',
                    'attributes' => [
                        'name' => $shopTypes[4]->name,
                        'description' => $shopTypes[4]->description,
                        'image' => $shopTypes[4]->image,
                        'created_at' => $shopTypes[4]->created_at->toJSON(),
                        'updated_at' => $shopTypes[4]->updated_at->toJSON()
                    ]
                ],
            ],
            'links' => [
                'first' => route('shop-types.index', ['page[size]' => 5, 'page[number]' => 1]),
                'last' => route('shop-types.index', ['page[size]' => 5, 'page[number]' => 2]),
                'prev' => null,
                'next' => route('shop-types.index', ['page[size]' => 5, 'page[number]' => 2]),
            ]
        ]);
});


it('can create a shop type from a resource object', function () {
    // assert
    $response = $this->postJson('/api/v1/shop-types', [
        'data' => [
            'type' => 'shop-types',
            'attributes' => [
                'name' => 'Cool Shop',
                'description' => 'Lorem ipsum dolor emet',
                'image' => 'http://something.com/example.png'
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
                'type' => 'shop-types',
                'attributes' => [
                    'name' => 'Cool Shop',
                    'description' => 'Lorem ipsum dolor emet',
                    'image' => 'http://something.com/example.png',
                    'created_at' => $createdTime,
                    'updated_at' => $updatedTime
                ]
            ]
        ])->assertHeader('Location', url('/api/v1/shop-types/1'));

    // assert the database has the shop type recored
    $this->assertDatabaseHas('shop_types', [
        'id' => '1',
        'name' => 'Cool Shop',
        'description' => 'Lorem ipsum dolor emet',
        'image' => 'http://something.com/example.png'
    ]);
});


it('validates that the type member is given when creating a shop type', function () {
    // assert
    $this->postJson('/api/v1/shop-types', [
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

    // assert the database has the shop type
    $this->assertDatabaseMissing('shop_types', [
        'id' => '1',
        'name' => 'Cool Shop'
    ]);
})->group('validate_create_shop_types');


it('validates that the type member has the value of shop-types when creating a shop type', function () {
    // assert
    $this->postJson('/api/v1/shop-types', [
        'data' => [
            'type' => 'shop-type',
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

    // assert the database has the shop type
    $this->assertDatabaseMissing('shop_types', [
        'id' => '1',
        'name' => 'Cool Shop'
    ]);
})->group('validate_create_shop_types');


it('validates that the attributes member has been given when creating a shop type', function () {
    // assert
    $this->postJson('/api/v1/shop-types', [
        'data' => [
            'type' => 'shop-types'
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

    // assert the database has the shop type
    $this->assertDatabaseMissing('shop_types', [
        'id' => '1',
        'name' => 'Cool Shop'
    ]);
})->group('validate_create_shop_types');


it('validates that the attributes member is an object given when creating a shop type', function () {
    // assert
    $this->postJson('/api/v1/shop-types', [
        'data' => [
            'type' => 'shop-types',
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

    // assert the database has the shop type 
    $this->assertDatabaseMissing('shop_types', [
        'id' => '1',
        'name' => 'Cool Shop'
    ]);
})->group('validate_create_shop_types');


it('validates that a name attribute is given when creating a shop type', function () {
    // assert
    $this->postJson('/api/v1/shop-types', [
        'data' => [
            'type' => 'shop-types',
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

    // assert the database has the shop type
    $this->assertDatabaseMissing('shop_types', [
        'id' => '1',
        'name' => 'Cool Shop'
    ]);
})->group('validate_create_shop_types');


it('validates that a name attribute is a string when creating a shop type', function () {
    // assert
    $this->postJson('/api/v1/shop-types', [
        'data' => [
            'type' => 'shop-types',
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
    $this->assertDatabaseMissing('shop_types', [
        'id' => '1',
        'name' => 'Cool Shop'
    ]);
})->group('validate_create_shop_types');


it('validates that a name attribute is not more than 255 characters when creating a shop type', function () {
    // assert
    $this->postJson('/api/v1/shop-types', [
        'data' => [
            'type' => 'shop-types',
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
                    'details' => 'The data.attributes.name may not be greater than 100 characters.',
                    'source' => [
                        'pointer' => '/data/attributes/name'
                    ]
                ]
            ]
        ]);

    // assert the database has the shop's recored
    $this->assertDatabaseMissing('shop_types', [
        'id' => '1',
        'name' => 'Cool Shop'
    ]);
})->group('validate_create_shop_types');


it('validates that a shop type name attribute is unique when creating a shop type', function () {

    // create a shop type 
    $shopType = factory(ShopType::class)->create();

    // now create another shop type with the same shop type name 
    $this->postJson('/api/v1/shop-types', [
        'data' => [
            'type' => 'shop-types',
            'attributes' => [
                'name' => $shopType->name,
                'description' => 'Lorem ipsum dolor emet',
                'image' => 'http://something.com/example.png'
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
                    'details' => 'The data.attributes.name has already been taken.',
                    'source' => [
                        'pointer' => '/data/attributes/name'
                    ]
                ]
            ]
        ]);
})->group('validate_create_shop_types');


it('can update a shop type from a resource object', function () {
    // create a shop type
    $shopType = factory(ShopType::class)->create();

    // get the timestamp for now
    $creationTimestamp = $shopType->created_at;
    sleep(5);

    // assert
    $response = $this->patchJson('/api/v1/shop-types/1', [
        'data' => [
            'id' => '1',
            'type' => 'shop-types',
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
                'type' => 'shop-types',
                'attributes' => [
                    'name' => 'Awesome Shop',
                    'created_at' => $creationTimestamp->toJSON(),
                    'updated_at' => $updatedTime
                ]
            ]
        ]);

    $this->assertDatabaseHas('shop_types', [
        'id' => '1',
        'name' => 'Awesome Shop'
    ]);
});


it('validates that an id member is given when updating a shop type', function () {
    // create a shop type
    $shopType = factory(ShopType::class)->create();

    // assert
    $this->patchJson('/api/v1/shop-types/1', [
        'data' => [
            'type' => 'shop-types',
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

    // assert the database has the shop type
    $this->assertDatabaseHas('shop_types', [
        'id' => '1',
        'name' => $shopType->name
    ]);
})->group('validate_update_shop_types');


it('validates that an id member is a string when updating a shop type', function () {
    // create a shop type
    $shopType = factory(ShopType::class)->create();

    // assert
    $this->patchJson('/api/v1/shop-types/1', [
        'data' => [
            'id' => 1,
            'type' => 'shop-types',
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

    // assert the database has the shop type
    $this->assertDatabaseHas('shop_types', [
        'id' => '1',
        'name' => $shopType->name
    ]);
})->group('validate_update_shop_types');


it('validates that the type member is given when updating a shop type', function () {
    // create a shop type
    $shopType = factory(ShopType::class)->create();

    // assert
    $this->patchJson('/api/v1/shop-types/1', [
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

    // assert the database has the shop type
    $this->assertDatabaseHas('shop_types', [
        'id' => '1',
        'name' => $shopType->name
    ]);
})->group('validate_update_shop_types');


it('validates that the type member has the value of shop-types when updating a shop type', function () {
    // create a shop type
    $shopType = factory(ShopType::class)->create();

    // assert
    $this->patchJson('/api/v1/shop-types/1', [
        'data' => [
            'id' => '1',
            'type' => 'shop-type',
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

    // assert the database has the shop type
    $this->assertDatabaseHas('shop_types', [
        'id' => '1',
        'name' => $shopType->name
    ]);
})->group('validate_update_shop_types');


it('validates that the attributes member has been given when updating a shop type', function () {
    // create a shop type
    $shopType = factory(ShopType::class)->create();

    // assert
    $this->patchJson('/api/v1/shop-types/1', [
        'data' => [
            'id' => '1',
            'type' => 'shop-types'
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

    // assert the database has the shop type
    $this->assertDatabaseHas('shop_types', [
        'id' => '1',
        'name' => $shopType->name
    ]);
})->group('validate_update_shop_types');


it('validates that the attributes member is an object given when updating a shop type', function () {
    // create a shop type
    $shopType = factory(ShopType::class)->create();

    // assert
    $this->patchJson('/api/v1/shop-types/1', [
        'data' => [
            'id' => '1',
            'type' => 'shop-types',
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

    // assert the database has the shop type
    $this->assertDatabaseHas('shop_types', [
        'id' => '1',
        'name' => $shopType->name
    ]);
})->group('validate_update_shop_types');


it('validates that a name attribute is a string when updating a shop type', function () {
    // create a shop type
    $shopType = factory(ShopType::class)->create();

    // assert
    $this->patchJson('/api/v1/shop-types/1', [
        'data' => [
            'id' => '1',
            'type' => 'shop-types',
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

    // assert the database has the shop type
    $this->assertDatabaseHas('shop_types', [
        'id' => '1',
        'name' => $shopType->name
    ]);
})->group('validate_update_shop_types');


it('validates that a name attribute is not more than 255 characters when updating a shop', function () {
    // create a shop type
    $shopType = factory(ShopType::class)->create();

    // assert
    $this->patchJson('/api/v1/shop-types/1', [
        'data' => [
            'id' => '1',
            'type' => 'shop-types',
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
                    'details' => 'The data.attributes.name may not be greater than 100 characters.',
                    'source' => [
                        'pointer' => '/data/attributes/name'
                    ]
                ]
            ]
        ]);

    // assert the database has the shop type
    $this->assertDatabaseHas('shop_types', [
        'id' => '1',
        'name' => $shopType->name
    ]);
})->group('validate_update_shop_types');


it('validates that a name attribute is unique when updating a shop type', function () {

    // create a shop type
    $shopType1 = factory(ShopType::class)->create();
    // create another shop type
    $shopType2 = factory(ShopType::class)->create();

    // now update the shopType2 with the same name of the shopType1
    $this->patchJson("/api/v1/shop-types/{$shopType2->id}", [
        'data' => [
            'id' => (string) $shopType2->id,
            'type' => 'shop-types',
            'attributes' => [
                'name' => $shopType1->name,
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
                    'details' => 'The data.attributes.name has already been taken.',
                    'source' => [
                        'pointer' => '/data/attributes/name'
                    ]
                ]
            ]
        ]);
})->group('validate_update_shop_types');


it('can delete a shop type through a delete request', function () {
    // create a shop type
    $shopType = factory(ShopType::class)->create();

    // assert 
    $this->delete('/api/v1/shop-types/1', [],  [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json',
    ])->assertStatus(204);

    // check the database doesn't have the row
    $this->assertDatabaseMissing('shop_types', [
        'id' => '1',
        'name' => $shopType->name
    ]);
});
