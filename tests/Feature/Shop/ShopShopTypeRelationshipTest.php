<?php

use App\Models\Shop\Shop;
use App\Models\Shop\ShopType;

use function Pest\Laravel\withoutExceptionHandling;
use function Tests\passportActingAs;
use Illuminate\Foundation\Testing\DatabaseMigrations;

uses(DatabaseMigrations::class);


beforeEach(function () {
    // authenticated user
    $this->user = passportActingAs();
});


it('returns a relationship to shop types with shop adhering to json api spec', function () {

    // create some shop types
    $shopTypes = factory(ShopType::class, 3)->create();
    // create a shop
    $shop = factory(Shop::class)->create();
    // sync those shop types to this shop
    $shop->shopTypes()->sync($shopTypes->pluck('id'));

    // assert
    $this->getJson('/api/v1/shops/1?include=shop-types', [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json'
    ])
        ->assertStatus(200)
        ->assertJson([
            'data' => [
                'id' => '1',
                'type' => 'shops',
                'relationships' => [
                    'shop-types' => [
                        'links' => [
                            'self' => route('shops.relationships.shop-types', [
                                'shop' => $shop->id
                            ]),
                            'related' => route('shops.shop-types', [
                                'shop' => $shop->id
                            ])
                        ],
                        'data' => [
                            [
                                'id' => '1',
                                'type' => 'shop-types'
                            ],
                            [
                                'id' => '2',
                                'type' => 'shop-types'
                            ],
                            [
                                'id' => '3',
                                'type' => 'shop-types'
                            ]
                        ]
                    ]
                ]
            ]
        ]);
});


test('a relationship link to shop types with shop returns all related shop types as resource id object', function () {

    // create some shop types
    $shopTypes = factory(ShopType::class, 3)->create();
    // create a shop
    $shop = factory(Shop::class)->create();
    // sync those shop types to this shop
    $shop->shopTypes()->sync($shopTypes->pluck('id'));

    // assert
    $this->getJson('/api/v1/shops/1/relationships/shop-types', [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json'
    ])
        ->assertStatus(200)
        ->assertJson([
            'data' => [
                [
                    'id' => '1',
                    'type' => 'shop-types'
                ],
                [
                    'id' => '2',
                    'type' => 'shop-types'
                ],
                [
                    'id' => '3',
                    'type' => 'shop-types'
                ]
            ]
        ]);
});


it('can modify relationships to shop types with shops and add new relationships', function () {

    // create some shop types
    $shopTypes = factory(ShopType::class, 10)->create();
    // create a shop
    $shop = factory(Shop::class)->create();

    // assert
    $this->patchJson('/api/v1/shops/1/relationships/shop-types', [
        'data' => [
            [
                'id' => '5',
                'type' => 'shop-types'
            ],
            [
                'id' => '6',
                'type' => 'shop-types'
            ]
        ]
    ], [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json'
    ])->assertStatus(204);

    $this->assertDatabaseHas('shop_shop_type', [
        'shop_type_id' => 5,
        'shop_id' => 1
    ])->assertDatabaseHas('shop_shop_type', [
        'shop_type_id' => 6,
        'shop_id' => 1
    ]);
});


it('can modify relationships to shop types with shop and remove relationships', function () {

    // create some shop types
    $shopTypes = factory(ShopType::class, 5)->create();
    // create a shop
    $shop = factory(Shop::class)->create();
    // sync those shop types to this shop
    $shop->shopTypes()->sync($shopTypes->pluck('id'));

    // assert
    $this->patchJson('/api/v1/shops/1/relationships/shop-types', [
        'data' => [
            [
                'id' => '1',
                'type' => 'shop-types'
            ],
            [
                'id' => '2',
                'type' => 'shop-types'
            ],
            [
                'id' => '5',
                'type' => 'shop-types'
            ]
        ]
    ], [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json'
    ])->assertStatus(204);

    $this->assertDatabaseHas('shop_shop_type', [
        'shop_type_id' => 1,
        'shop_id' => 1
    ])->assertDatabaseHas('shop_shop_type', [
        'shop_type_id' => 2,
        'shop_id' => 1
    ])->assertDatabaseHas('shop_shop_type', [
        'shop_type_id' => 5,
        'shop_id' => 1
    ])->assertDatabaseMissing('shop_shop_type', [
        'shop_type_id' => 3,
        'shop_id' => 1
    ])->assertDatabaseMissing('shop_shop_type', [
        'shop_type_id' => 4,
        'shop_id' => 1
    ]);
});


it('can remove all relationships to shop types with shop with an empty collection', function () {
    // create some shop types
    $shopTypes = factory(ShopType::class, 3)->create();
    // create a shop
    $shop = factory(Shop::class)->create();
    // sync those shop types to this shop
    $shop->shopTypes()->sync($shopTypes->pluck('id'));

    // assert
    $this->patchJson('/api/v1/shops/1/relationships/shop-types', [
        'data' => []
    ], [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json'
    ])->assertStatus(204);

    $this->assertDatabaseMissing('shop_shop_type', [
        'shop_type_id' => 1,
        'shop_id' => 1
    ])->assertDatabaseMissing('shop_shop_type', [
        'shop_type_id' => 2,
        'shop_id' => 1
    ])->assertDatabaseMissing('shop_shop_type', [
        'shop_type_id' => 3,
        'shop_id' => 1
    ]);
});


it('returns a 404 not found when trying to add relationship to a non existing object in shops with shop type', function () {
    // create some shop types
    $shopTypes = factory(ShopType::class, 3)->create();
    // create a shop
    $shop = factory(Shop::class)->create();

    // assert
    $this->patchJson('/api/v1/shops/1/relationships/shop-types', [
        'data' => [
            [
                'id' => '5',
                'type' => 'shop-types'
            ],
            [
                'id' => '6',
                'type' => 'shop-types'
            ]
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


it('validates that the id member is given when updating a relationship in shops with shop type', function () {

    // create some shop types
    $shopTypes = factory(ShopType::class, 5)->create();
    // create a shop
    $shop = factory(Shop::class)->create();

    // assert
    $this->patchJson('/api/v1/shops/1/relationships/shop-types', [
        'data' => [
            [
                'type' => 'shop-types'
            ]
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
                    'details' => 'The data.0.id field is required.',
                    'source' => [
                        'pointer' => '/data/0/id'
                    ]
                ]
            ]
        ]);
})->group('validate_shops_relation');


it('validates that the id member is a string when updating a relationship in shop with shop type', function () {
    // create some shop types
    $shopTypes = factory(ShopType::class, 5)->create();
    // create a shop
    $shop = factory(Shop::class)->create();

    // assert
    $this->patchJson('/api/v1/shops/1/relationships/shop-types', [
        'data' => [
            [
                'id' => 5,
                'type' => 'shop-types'
            ]
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
                    'details' => 'The data.0.id must be a string.',
                    'source' => [
                        'pointer' => '/data/0/id'
                    ]
                ]
            ]
        ]);
})->group('validate_shops_relation');


it('validates that the type member is a string when updating a relationship in shop with shop type', function () {
    // create some shop types
    $shopTypes = factory(ShopType::class, 5)->create();
    // create a shop
    $shop = factory(Shop::class)->create();

    // assert
    $this->patchJson('/api/v1/shops/1/relationships/shop-types', [
        'data' => [
            [
                'id' => '5'
            ]
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
                    'details' => 'The data.0.type field is required.',
                    'source' => [
                        'pointer' => '/data/0/type'
                    ]
                ]
            ]
        ]);
})->group('validate_shops_relation');


it('validates that the type member has a value of shop types when updating a relationship in shops', function () {
    // create some shop types
    $shopTypes = factory(ShopType::class, 5)->create();
    // create a shop
    $shop = factory(Shop::class)->create();

    // assert
    $this->patchJson('/api/v1/shops/1/relationships/shop-types', [
        'data' => [
            [
                'id' => '5',
                'type' => 'random'
            ]
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
                    'details' => 'The selected data.0.type is invalid.',
                    'source' => [
                        'pointer' => '/data/0/type'
                    ]
                ]
            ]
        ]);
})->group('validate_shops_relation');


it('can get all related shop types as resource objects from related link in shop', function () {

    // create some shop types
    $shopTypes = factory(ShopType::class, 3)->create();
    // create a shop
    $shop = factory(Shop::class)->create();
    // sync those shop types to this shop
    $shop->shopTypes()->sync($shopTypes->pluck('id'));

    // assert
    $this->getJson('/api/v1/shops/1/shop-types', [
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
                ]
            ]
        ]);
})->group('validate_shops_related');


it('includes related resource objects for shop types when an include query param to shop types is given in shops', function () {

    // create some shop types
    $shopTypes = factory(ShopType::class, 3)->create();
    // create a shop
    $shop = factory(Shop::class)->create();
    // sync those shop types to this shop
    $shop->shopTypes()->sync($shopTypes->pluck('id'));

    // assert
    $this->getJson('/api/v1/shops/1?include=shop-types', [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json'
    ])
        ->assertStatus(200)
        ->assertJson([
            'data' => [
                'id' => '1',
                'type' => 'shops',
                'relationships' => [
                    'shop-types' => [
                        'links' => [
                            'self' => route('shops.relationships.shop-types', [
                                'shop' => $shop->id
                            ]),
                            'related' => route('shops.shop-types', [
                                'shop' => $shop->id
                            ])
                        ],
                        'data' => [
                            [
                                'id' => '1',
                                'type' => 'shop-types'
                            ],
                            [
                                'id' => '2',
                                'type' => 'shop-types'
                            ],
                            [
                                'id' => '3',
                                'type' => 'shop-types'
                            ]
                        ]
                    ]
                ]
            ],
            'included' => [
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
                ]
            ]
        ]);
})->group('validate_shops_related');


it('does not include related resource objects for shop types when an include query param to shop types is not given in shops', function () {
    // create a shop
    $shop = factory(Shop::class)->create();

    // assert
    $this->getJson('/api/v1/shops/1', [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json'
    ])
        ->assertStatus(200)
        ->assertJsonMissing([
            'included' => []
        ]);
})->group('validate_shops_related');


it('includes related resource objects for a collection when an include query param is given in shops in shop type', function () {
    // create some shop types
    $shopTypes = factory(ShopType::class, 3)->create();
    // create some shops
    $shops = factory(Shop::class, 3)->create();
    // sync the shop types with shops
    $shops->each(function ($shop, $key) use ($shopTypes) {
        if ($key === 0) {
            $shop->shopTypes()->sync($shopTypes->pluck('id'));
        }
    });

    // assert
    $this->getJson('/api/v1/shops?include=shop-types', [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json',
    ])
        ->assertStatus(200)
        ->assertJson([
            'data' => [
                [
                    'id' => '1',
                    'type' => "shops",
                    'attributes' => [
                        'name' => $shops[0]->name,
                        'created_at' => $shops[0]->created_at->toJSON(),
                        'updated_at' => $shops[0]->updated_at->toJSON(),
                        'deleted_at' => $shops[0]->deleted_at
                    ],
                    'relationships' => [
                        'shop-types' => [
                            'links' => [
                                'self' => route('shops.relationships.shop-types', [
                                    'shop' => $shops[0]->id
                                ]),
                                'related' => route('shops.shop-types', [
                                    'shop' => $shops[0]->id
                                ])
                            ],
                            'data' => [
                                [
                                    'id' => (string) $shopTypes->get(0)->id,
                                    'type' => 'shop-types'
                                ],
                                [
                                    'id' => (string) $shopTypes->get(1)->id,
                                    'type' => 'shop-types'
                                ],
                                [
                                    'id' => (string) $shopTypes->get(2)->id,
                                    'type' => 'shop-types'
                                ]
                            ]
                        ]
                    ]
                ],
                [
                    'id' => '2',
                    'type' => 'shops',
                    'attributes' => [
                        'name' => $shops[1]->name,
                        'created_at' => $shops[1]->created_at->toJSON(),
                        'updated_at' => $shops[1]->updated_at->toJSON(),
                        'deleted_at' => $shops[1]->deleted_at
                    ],
                    'relationships' => [
                        'shop-types' => [
                            'links' => [
                                'self' => route('shops.relationships.shop-types', [
                                    'shop' => $shops[1]->id
                                ]),
                                'related' => route('shops.shop-types', [
                                    'shop' => $shops[1]->id
                                ])
                            ],
                        ]
                    ]
                ],
                [
                    'id' => '3',
                    'type' => 'shops',
                    'attributes' => [
                        'name' => $shops[2]->name,
                        'created_at' => $shops[2]->created_at->toJSON(),
                        'updated_at' => $shops[2]->updated_at->toJSON(),
                        'deleted_at' => $shops[2]->deleted_at
                    ],
                    'relationships' => [
                        'shop-types' => [
                            'links' => [
                                'self' => route('shops.relationships.shop-types', [
                                    'shop' => $shops[2]->id
                                ]),
                                'related' => route('shops.shop-types', [
                                    'shop' => $shops[2]->id
                                ])
                            ],
                        ]
                    ]
                ]
            ],
            'included' => [
                [
                    'id' => '1',
                    'type' => 'shop-types',
                    'attributes' => [
                        'name' => $shopTypes[0]->name,
                        'description' => $shopTypes[0]->description,
                        'image' => $shopTypes[0]->image,
                        'created_at' => $shopTypes[0]->created_at->toJSON(),
                        'updated_at' => $shopTypes[0]->updated_at->toJSON(),
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
                        'updated_at' => $shopTypes[1]->updated_at->toJSON(),
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
                        'updated_at' => $shopTypes[2]->updated_at->toJSON(),
                    ]
                ],
            ]
        ]);
})->group('validate_shops_related');


it('only includes a related resource object once for a collection in shops for shop types', function () {
    // create some shop types
    $shopTypes = factory(ShopType::class, 3)->create();
    // create shops
    $shops = factory(Shop::class, 3)->create();
    // sync the shops with shop type
    $shops->each(function ($shop, $key) use ($shopTypes) {
        $shop->shopTypes()->sync($shopTypes->pluck('id'));
    });

    // assert
    $this->get('/api/v1/shops?include=shop-types', [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json',
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
                        'updated_at' => $shops[0]->updated_at->toJSON(),
                        'deleted_at' => $shops[0]->deleted_at
                    ],
                    'relationships' => [
                        'shop-types' => [
                            'links' => [
                                'self' => route('shops.relationships.shop-types', [
                                    'shop' => $shops[0]->id
                                ]),
                                'related' => route('shops.shop-types', [
                                    'shop' => $shops[0]->id
                                ])
                            ],
                            'data' => [
                                [
                                    'id' => '1',
                                    'type' => 'shop-types'
                                ],
                                [
                                    'id' => '2',
                                    'type' => 'shop-types'
                                ],
                                [
                                    'id' => '3',
                                    'type' => 'shop-types'
                                ]
                            ]
                        ]
                    ],
                ],
                [
                    'id' => '2',
                    'type' => 'shops',
                    'attributes' => [
                        'name' => $shops[1]->name,
                        'created_at' => $shops[1]->created_at->toJSON(),
                        'updated_at' => $shops[1]->updated_at->toJSON(),
                        'deleted_at' => $shops[1]->deleted_at
                    ],
                    'relationships' => [
                        'shop-types' => [
                            'links' => [
                                'self' => route('shops.relationships.shop-types', [
                                    'shop' => $shops[1]->id
                                ]),
                                'related' => route('shops.shop-types', [
                                    'shop' => $shops[1]->id
                                ])
                            ],
                            'data' => [
                                [
                                    'id' => '1',
                                    'type' => 'shop-types'
                                ],
                                [
                                    'id' => '2',
                                    'type' => 'shop-types'
                                ],
                                [
                                    'id' => '3',
                                    'type' => 'shop-types'
                                ]
                            ]
                        ]
                    ],
                ],
                [
                    'id' => '3',
                    'type' => 'shops',
                    'attributes' => [
                        'name' => $shops[2]->name,
                        'created_at' => $shops[2]->created_at->toJSON(),
                        'updated_at' => $shops[2]->updated_at->toJSON(),
                        'deleted_at' => $shops[2]->deleted_at
                    ],
                    'relationships' => [
                        'shop-types' => [
                            'links' => [
                                'self' => route('shops.relationships.shop-types', [
                                    'shop' => $shops[2]->id
                                ]),
                                'related' => route('shops.shop-types', [
                                    'shop' => $shops[2]->id
                                ])
                            ],
                            'data' => [
                                [
                                    'id' => '1',
                                    'type' => 'shop-types'
                                ],
                                [
                                    'id' => '2',
                                    'type' => 'shop-types'
                                ],
                                [
                                    'id' => '3',
                                    'type' => 'shop-types'
                                ]
                            ]
                        ]
                    ],
                ]
            ],
            'included' => [
                [
                    'id' => '1',
                    'type' => 'shop-types',
                    'attributes' => [
                        'name' => $shopTypes[0]->name,
                        'description' => $shopTypes[0]->description,
                        'image' => $shopTypes[0]->image,
                        'created_at' => $shopTypes[0]->created_at->toJSON(),
                        'updated_at' => $shopTypes[0]->updated_at->toJSON(),
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
                        'updated_at' => $shopTypes[1]->updated_at->toJSON(),
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
                        'updated_at' => $shopTypes[2]->updated_at->toJSON(),
                    ]
                ],
            ]
        ])->assertJsonMissing([
            'included' => [
                [
                    'id' => '1',
                    'type' => 'shop-types',
                    'attributes' => [
                        'name' => $shopTypes[0]->name,
                        'description' => $shopTypes[0]->description,
                        'image' => $shopTypes[0]->image,
                        'created_at' => $shopTypes[0]->created_at->toJSON(),
                        'updated_at' => $shopTypes[0]->updated_at->toJSON(),
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
                        'updated_at' => $shopTypes[1]->updated_at->toJSON(),
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
                        'updated_at' => $shopTypes[2]->updated_at->toJSON(),
                    ]
                ],
                [
                    'id' => '1',
                    'type' => 'shop-types',
                    'attributes' => [
                        'name' => $shopTypes[0]->name,
                        'description' => $shopTypes[0]->description,
                        'image' => $shopTypes[0]->image,
                        'created_at' => $shopTypes[0]->created_at->toJSON(),
                        'updated_at' => $shopTypes[0]->updated_at->toJSON(),
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
                        'updated_at' => $shopTypes[1]->updated_at->toJSON(),
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
                        'updated_at' => $shopTypes[2]->updated_at->toJSON(),
                    ]
                ],
                [
                    'id' => '1',
                    'type' => 'shop-types',
                    'attributes' => [
                        'name' => $shopTypes[0]->name,
                        'description' => $shopTypes[0]->description,
                        'image' => $shopTypes[0]->image,
                        'created_at' => $shopTypes[0]->created_at->toJSON(),
                        'updated_at' => $shopTypes[0]->updated_at->toJSON(),
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
                        'updated_at' => $shopTypes[1]->updated_at->toJSON(),
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
                        'updated_at' => $shopTypes[2]->updated_at->toJSON(),
                    ]
                ],
            ]
        ]);
})->group('validate_shops_related');
