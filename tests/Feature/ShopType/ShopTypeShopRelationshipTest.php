<?php

use App\Models\Shop\Shop;
use App\Models\Shop\ShopType;
use function Tests\passportActingAs;
use Illuminate\Foundation\Testing\DatabaseMigrations;

uses(DatabaseMigrations::class);


beforeEach(function () {
    // authenticated user
    $this->user = passportActingAs();
});


it('returns a relationship to shops with shop type adhering to json api spec', function () {
    // create some shops
    $shops = factory(Shop::class, 3)->create();
    // create a shop type
    $shopType = factory(ShopType::class)->create();
    // sync those shops to this shop type
    $shopType->shops()->sync($shops->pluck('id'));

    // assert
    $this->getJson('/api/v1/shop-types/1?include=shops', [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json'
    ])
        ->assertStatus(200)
        ->assertJson([
            'data' => [
                'id' => '1',
                'type' => 'shop-types',
                'relationships' => [
                    'shops' => [
                        'links' => [
                            'self' => route('shop-types.relationships.shops', [
                                'shopType' => $shopType->id
                            ]),
                            'related' => route('shop-types.shops', [
                                'shopType' => $shopType->id
                            ])
                        ],
                        'data' => [
                            [
                                'id' => '1',
                                'type' => 'shops'
                            ],
                            [
                                'id' => '2',
                                'type' => 'shops'
                            ],
                            [
                                'id' => '3',
                                'type' => 'shops'
                            ]
                        ]
                    ]
                ]
            ]
        ]);
});


test('a relationship link to shops with shop type returns all related shops as resource id object', function () {
    // create some shops
    $shops = factory(Shop::class, 3)->create();
    // create a shop type
    $shopType = factory(ShopType::class)->create();
    // sync those shops to this shop type
    $shopType->shops()->sync($shops->pluck('id'));

    // assert
    $this->getJson('/api/v1/shop-types/1/relationships/shops', [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json'
    ])
        ->assertStatus(200)
        ->assertJson([
            'data' => [
                [
                    'id' => '1',
                    'type' => 'shops'
                ],
                [
                    'id' => '2',
                    'type' => 'shops'
                ],
                [
                    'id' => '3',
                    'type' => 'shops'
                ]
            ]
        ]);
});


it('can modify relationships to shops with shop type and add new relationships', function () {
    // create some shops
    $shops = factory(Shop::class, 10)->create();
    // create a shop type
    $shopType = factory(ShopType::class)->create();

    // assert
    $this->patchJson('/api/v1/shop-types/1/relationships/shops', [
        'data' => [
            [
                'id' => '5',
                'type' => 'shops'
            ],
            [
                'id' => '6',
                'type' => 'shops'
            ]
        ]
    ], [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json'
    ])->assertStatus(204);

    $this->assertDatabaseHas('shop_shop_type', [
        'shop_id' => 5,
        'shop_type_id' => 1
    ])->assertDatabaseHas('shop_shop_type', [
        'shop_id' => 6,
        'shop_type_id' => 1
    ]);
});


it('can modify relationships to shops with shop type and remove relationships', function () {
    // create some shops
    $shops = factory(Shop::class, 5)->create();
    // create a shop type
    $shopType = factory(ShopType::class)->create();
    // sync with shops
    $shopType->shops()->sync($shops->pluck('id'));

    // assert
    $this->patchJson('/api/v1/shop-types/1/relationships/shops', [
        'data' => [
            [
                'id' => '1',
                'type' => 'shops'
            ],
            [
                'id' => '2',
                'type' => 'shops'
            ],
            [
                'id' => '5',
                'type' => 'shops'
            ]
        ]
    ], [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json'
    ])->assertStatus(204);

    $this->assertDatabaseHas('shop_shop_type', [
        'shop_id' => 1,
        'shop_type_id' => 1
    ])->assertDatabaseHas('shop_shop_type', [
        'shop_id' => 2,
        'shop_type_id' => 1
    ])->assertDatabaseHas('shop_shop_type', [
        'shop_id' => 5,
        'shop_type_id' => 1
    ])->assertDatabaseMissing('shop_shop_type', [
        'shop_id' => 3,
        'shop_type_id' => 1
    ])->assertDatabaseMissing('shop_shop_type', [
        'shop_id' => 4,
        'shop_type_id' => 1
    ]);
});


it('can remove all relationships to shops with shop type with an empty collection', function () {
    // create some shops
    $shops = factory(Shop::class, 3)->create();
    // create a shop type
    $shopType = factory(ShopType::class)->create();
    // sync with shops
    $shopType->shops()->sync($shops->pluck('id'));

    // assert
    $this->patchJson('/api/v1/shop-types/1/relationships/shops', [
        'data' => []
    ], [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json'
    ])->assertStatus(204);

    $this->assertDatabaseMissing('shop_shop_type', [
        'shop_id' => 1,
        'shop_type_id' => 1
    ])->assertDatabaseMissing('shop_shop_type', [
        'shop_id' => 2,
        'shop_type_id' => 1
    ])->assertDatabaseMissing('shop_shop_type', [
        'shop_id' => 3,
        'shop_type_id' => 1
    ]);
});


it('returns a 404 not found when trying to add relationship to a non existing object in shop type', function () {
    // create some shops
    $shops = factory(Shop::class, 3)->create();
    // create a shop type
    $shopType = factory(ShopType::class)->create();

    // assert
    $this->patchJson('/api/v1/shop-types/1/relationships/shops', [
        'data' => [
            [
                'id' => '5',
                'type' => 'shops'
            ],
            [
                'id' => '6',
                'type' => 'shops'
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


it('validates that the id member is given when updating a relationship in shop type', function () {
    // create some shops
    $shops = factory(Shop::class, 5)->create();
    // create a shop type
    $shopType = factory(ShopType::class)->create();

    // assert
    $this->patchJson('/api/v1/shop-types/1/relationships/shops', [
        'data' => [
            [
                'type' => 'shops'
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
})->group('validate_shop_type_relation');


it('validates that the id member is a string when updating a relationship in shop type', function () {
    // create some shops
    $shops = factory(Shop::class, 5)->create();
    // create a shop type
    $shopType = factory(ShopType::class)->create();

    // assert
    $this->patchJson('/api/v1/shop-types/1/relationships/shops', [
        'data' => [
            [
                'id' => 5,
                'type' => 'shops'
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
})->group('validate_shop_type_relation');


it('validates that the type member is a string when updating a relationship in shop type', function () {
    // create some shops
    $shops = factory(Shop::class, 5)->create();
    // create a shop type
    $shopType = factory(ShopType::class)->create();

    // assert
    $this->patchJson('/api/v1/shop-types/1/relationships/shops', [
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
})->group('validate_shop_type_relation');


it('validates that the type member has a value of shops when updating a relationship in shop type', function () {
    // create some shops
    $shops = factory(Shop::class, 5)->create();
    // create a shop type
    $shopType = factory(ShopType::class)->create();

    // assert
    $this->patchJson('/api/v1/shop-types/1/relationships/shops', [
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
})->group('validate_shop_type_relation');


it('can get all related shops as resource objects from related link in shop types', function () {
    // create some shops
    $shops = factory(Shop::class, 3)->create();
    // create a shop type
    $shopType = factory(ShopType::class)->create();
    // sync the shops with shop type
    $shopType->shops()->sync($shops->pluck('id'));

    // assert
    $this->getJson('/api/v1/shop-types/1/shops', [
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
                ]
            ]
        ]);
})->group('validate_shop_type_related');


it('includes related resource objects for shops when an include query param to shops is given in shop type', function () {
    // create some shops
    $shops = factory(Shop::class, 3)->create();
    // create a shop type
    $shopType = factory(ShopType::class)->create();
    // sync with shops
    $shopType->shops()->sync($shops->pluck('id'));

    // assert
    $this->getJson('/api/v1/shop-types/1?include=shops', [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json'
    ])
        ->assertStatus(200)
        ->assertJson([
            'data' => [
                'id' => '1',
                'type' => 'shop-types',
                'relationships' => [
                    'shops' => [
                        'links' => [
                            'self' => route('shop-types.relationships.shops', [
                                'shopType' => $shopType->id
                            ]),
                            'related' => route('shop-types.shops', [
                                'shopType' => $shopType->id
                            ])
                        ],
                        'data' => [
                            [
                                'id' => '1',
                                'type' => 'shops'
                            ],
                            [
                                'id' => '2',
                                'type' => 'shops'
                            ],
                            [
                                'id' => '3',
                                'type' => 'shops'
                            ]
                        ]
                    ]
                ]
            ],
            'included' => [
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
                ]
            ]
        ]);
})->group('validate_shop_type_related');


it('does not include related resource objects for shops when an include query param to shops is not given in shop type', function () {
    // create a shop type
    $shopType = factory(ShopType::class)->create();

    // assert
    $this->getJson('/api/v1/shop-types/1', [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json'
    ])
        ->assertStatus(200)
        ->assertJsonMissing([
            'included' => []
        ]);
})->group('validate_shop_type_related');


it('includes related resource objects for a collection when an include query param is given in shop type', function () {
    // create some shops
    $shops = factory(Shop::class, 3)->create();
    // create shop type
    $shopTypes = factory(ShopType::class, 3)->create();
    // sync the shops with shop type
    $shopTypes->each(function ($shopType, $key) use ($shops) {
        if ($key === 0) {
            $shopType->shops()->sync($shops->pluck('id'));
        }
    });

    // assert
    $this->getJson('/api/v1/shop-types?include=shops', [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json',
    ])
        ->assertStatus(200)
        ->assertJson([
            'data' => [
                [
                    'id' => '1',
                    'type' => "shop-types",
                    'attributes' => [
                        'name' => $shopTypes[0]->name,
                        'description' => $shopTypes[0]->description,
                        'image' => $shopTypes[0]->image,
                        'created_at' => $shopTypes[0]->created_at->toJSON(),
                        'updated_at' => $shopTypes[0]->updated_at->toJSON()
                    ],
                    'relationships' => [
                        'shops' => [
                            'links' => [
                                'self' => route('shop-types.relationships.shops', [
                                    'shopType' => $shopTypes[0]->id
                                ]),
                                'related' => route('shop-types.shops', [
                                    'shopType' => $shopTypes[0]->id
                                ])
                            ],
                            'data' => [
                                [
                                    'id' => (string) $shops->get(0)->id,
                                    'type' => 'shops'
                                ],
                                [
                                    'id' => (string) $shops->get(1)->id,
                                    'type' => 'shops'
                                ],
                                [
                                    'id' => (string) $shops->get(2)->id,
                                    'type' => 'shops'
                                ]
                            ]
                        ]
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
                    ],
                    'relationships' => [
                        'shops' => [
                            'links' => [
                                'self' => route('shop-types.relationships.shops', [
                                    'shopType' => $shopTypes[1]->id
                                ]),
                                'related' => route('shop-types.shops', [
                                    'shopType' => $shopTypes[1]->id
                                ])
                            ],
                        ]
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
                    ],
                    'relationships' => [
                        'shops' => [
                            'links' => [
                                'self' => route('shop-types.relationships.shops', [
                                    'shopType' => $shopTypes[2]->id
                                ]),
                                'related' => route('shop-types.shops', [
                                    'shopType' => $shopTypes[2]->id
                                ])
                            ],
                        ]
                    ]
                ]
            ],
            'included' => [
                [
                    'id' => '1',
                    'type' => 'shops',
                    'attributes' => [
                        'name' => $shops[0]->name,
                        'created_at' => $shops[0]->created_at->toJSON(),
                        'updated_at' => $shops[0]->updated_at->toJSON(),
                    ]
                ],
                [
                    'id' => '2',
                    'type' => 'shops',
                    'attributes' => [
                        'name' => $shops[1]->name,
                        'created_at' => $shops[1]->created_at->toJSON(),
                        'updated_at' => $shops[1]->updated_at->toJSON(),
                    ]
                ],
                [
                    'id' => '3',
                    'type' => 'shops',
                    'attributes' => [
                        'name' => $shops[2]->name,
                        'created_at' => $shops[2]->created_at->toJSON(),
                        'updated_at' => $shops[2]->updated_at->toJSON(),
                    ]
                ],
            ]
        ]);
})->group('validate_shop_type_related');


it('only includes a related resource object once for a collection in shop types', function () {
    // create some shops
    $shops = factory(Shop::class, 3)->create();
    // create shop types
    $shopTypes = factory(ShopType::class, 3)->create();
    // sync the shops with shop type
    $shopTypes->each(function ($shopType, $key) use ($shops) {
        $shopType->shops()->sync($shops->pluck('id'));
    });

    // assert
    $this->get('/api/v1/shop-types?include=shops', [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json',
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
                    ],
                    'relationships' => [
                        'shops' => [
                            'links' => [
                                'self' => route('shop-types.relationships.shops', [
                                    'shopType' => $shopTypes[0]->id
                                ]),
                                'related' => route('shop-types.shops', [
                                    'shopType' => $shopTypes[0]->id
                                ])
                            ],
                            'data' => [
                                [
                                    'id' => '1',
                                    'type' => 'shops'
                                ],
                                [
                                    'id' => '2',
                                    'type' => 'shops'
                                ],
                                [
                                    'id' => '3',
                                    'type' => 'shops'
                                ]
                            ]
                        ]
                    ],
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
                    ],
                    'relationships' => [
                        'shops' => [
                            'links' => [
                                'self' => route('shop-types.relationships.shops', [
                                    'shopType' => $shopTypes[1]->id
                                ]),
                                'related' => route('shop-types.shops', [
                                    'shopType' => $shopTypes[1]->id
                                ])
                            ],
                            'data' => [
                                [
                                    'id' => '1',
                                    'type' => 'shops'
                                ],
                                [
                                    'id' => '2',
                                    'type' => 'shops'
                                ],
                                [
                                    'id' => '3',
                                    'type' => 'shops'
                                ]
                            ]
                        ]
                    ],
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
                    ],
                    'relationships' => [
                        'shops' => [
                            'links' => [
                                'self' => route('shop-types.relationships.shops', [
                                    'shopType' => $shopTypes[2]->id
                                ]),
                                'related' => route('shop-types.shops', [
                                    'shopType' => $shopTypes[2]->id
                                ])
                            ],
                            'data' => [
                                [
                                    'id' => '1',
                                    'type' => 'shops'
                                ],
                                [
                                    'id' => '2',
                                    'type' => 'shops'
                                ],
                                [
                                    'id' => '3',
                                    'type' => 'shops'
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            'included' => [
                [
                    'id' => '1',
                    'type' => 'shops',
                    'attributes' => [
                        'name' => $shops[0]->name,
                        'created_at' => $shops[0]->created_at->toJSON(),
                        'updated_at' => $shops[0]->updated_at->toJSON(),
                        'deleted_at' => $shops[0]->deleted_at,
                    ]
                ],
                [
                    'id' => '2',
                    'type' => 'shops',
                    'attributes' => [
                        'name' => $shops[1]->name,
                        'created_at' => $shops[1]->created_at->toJSON(),
                        'updated_at' => $shops[1]->updated_at->toJSON(),
                        'deleted_at' => $shops[1]->deleted_at,
                    ]
                ],
                [
                    'id' => '3',
                    'type' => 'shops',
                    'attributes' => [
                        'name' => $shops[2]->name,
                        'created_at' => $shops[2]->created_at->toJSON(),
                        'updated_at' => $shops[2]->updated_at->toJSON(),
                        'deleted_at' => $shops[2]->deleted_at,
                    ]
                ],
            ]
        ])->assertJsonMissing([
            'included' => [
                [
                    "id" => '1',
                    "type" => "shops",
                    "attributes" => [
                        'name' => $shops[0]->name,
                        'created_at' => $shops[0]->created_at->toJSON(),
                        'updated_at' => $shops[0]->updated_at->toJSON(),
                        'deleted_at' => $shops[0]->deleted_at,
                    ]
                ],
                [
                    "id" => '2',
                    "type" => "shops",
                    "attributes" => [
                        'name' => $shops[1]->name,
                        'created_at' => $shops[1]->created_at->toJSON(),
                        'updated_at' => $shops[1]->updated_at->toJSON(),
                        'deleted_at' => $shops[1]->deleted_at,
                    ]
                ],
                [
                    "id" => '3',
                    "type" => "shops",
                    "attributes" => [
                        'name' => $shops[2]->name,
                        'created_at' => $shops[2]->created_at->toJSON(),
                        'updated_at' => $shops[2]->updated_at->toJSON(),
                        'deleted_at' => $shops[2]->deleted_at,
                    ]
                ],
                [
                    "id" => '1',
                    "type" => "shops",
                    "attributes" => [
                        'name' => $shops[0]->name,
                        'created_at' => $shops[0]->created_at->toJSON(),
                        'updated_at' => $shops[0]->updated_at->toJSON(),
                        'deleted_at' => $shops[0]->deleted_at,
                    ]
                ],
                [
                    "id" => '2',
                    "type" => "shops",
                    "attributes" => [
                        'name' => $shops[1]->name,
                        'created_at' => $shops[1]->created_at->toJSON(),
                        'updated_at' => $shops[1]->updated_at->toJSON(),
                        'deleted_at' => $shops[1]->deleted_at,
                    ]
                ],
                [
                    "id" => '3',
                    "type" => "shops",
                    "attributes" => [
                        'name' => $shops[2]->name,
                        'created_at' => $shops[2]->created_at->toJSON(),
                        'updated_at' => $shops[2]->updated_at->toJSON(),
                        'deleted_at' => $shops[2]->deleted_at,
                    ]
                ],
                [
                    "id" => '1',
                    "type" => "shops",
                    "attributes" => [
                        'name' => $shops[0]->name,
                        'created_at' => $shops[0]->created_at->toJSON(),
                        'updated_at' => $shops[0]->updated_at->toJSON(),
                        'deleted_at' => $shops[0]->deleted_at,
                    ]
                ],
                [
                    "id" => '2',
                    "type" => "shops",
                    "attributes" => [
                        'name' => $shops[1]->name,
                        'created_at' => $shops[1]->created_at->toJSON(),
                        'updated_at' => $shops[1]->updated_at->toJSON(),
                        'deleted_at' => $shops[1]->deleted_at,
                    ]
                ],
                [
                    "id" => '3',
                    "type" => "shops",
                    "attributes" => [
                        'name' => $shops[2]->name,
                        'created_at' => $shops[2]->created_at->toJSON(),
                        'updated_at' => $shops[2]->updated_at->toJSON(),
                        'deleted_at' => $shops[2]->deleted_at,
                    ]
                ],
            ]
        ]);
})->group('validate_shop_type_related');
