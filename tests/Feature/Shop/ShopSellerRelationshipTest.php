<?php

use App\Models\Seller;
use App\Models\Shop\Shop;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use function Tests\passportActingAs;

uses(DatabaseMigrations::class);


beforeEach(function () {
    // authenticated user
    $this->user = passportActingAs();
});


it('returns a relationship to sellers adhering to json api spec', function () {
    // create some sellers
    $sellers = factory(Seller::class, 3)->create();
    // create a shop
    $shop = factory(Shop::class)->create();
    // sync those sellers to this shop
    $shop->sellers()->sync($sellers->pluck('id'));

    // assert
    $this->getJson('/api/v1/shops/1?include=sellers', [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json'
    ])
        ->assertStatus(200)
        ->assertJson([
            'data' => [
                'id' => '1',
                'type' => 'shops',
                'relationships' => [
                    'sellers' => [
                        'links' => [
                            'self' => route('shops.relationships.sellers', [
                                'shop' => $shop->id
                            ]),
                            'related' => route('shops.sellers', [
                                'shop' => $shop->id
                            ])
                        ],
                        'data' => [
                            [
                                'id' => '1',
                                'type' => 'sellers'
                            ],
                            [
                                'id' => '2',
                                'type' => 'sellers'
                            ],
                            [
                                'id' => '3',
                                'type' => 'sellers'
                            ]
                        ]
                    ]
                ]
            ]
        ]);
});


test('a relationship link to sellers returns all related sellers as resource id object', function () {
    // create some sellers
    $sellers = factory(Seller::class, 3)->create();
    // create a shop
    $shop = factory(Shop::class)->create();
    // sync those sellers to this shop
    $shop->sellers()->sync($sellers->pluck('id'));

    // assert
    $this->getJson('/api/v1/shops/1/relationships/sellers', [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json'
    ])
        ->assertStatus(200)
        ->assertJson([
            'data' => [
                [
                    'id' => '1',
                    'type' => 'sellers'
                ],
                [
                    'id' => '2',
                    'type' => 'sellers'
                ],
                [
                    'id' => '3',
                    'type' => 'sellers'
                ]
            ]
        ]);
});


it('can modify relationships to sellers and add new relationships', function () {
    // create some sellers
    $sellers = factory(Seller::class, 10)->create();
    // create a shop
    $shop = factory(Shop::class)->create();

    // assert
    $this->patchJson('/api/v1/shops/1/relationships/sellers', [
        'data' => [
            [
                'id' => '5',
                'type' => 'sellers'
            ],
            [
                'id' => '6',
                'type' => 'sellers'
            ]
        ]
    ], [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json'
    ])->assertStatus(204);

    $this->assertDatabaseHas('seller_shop', [
        'seller_id' => 5,
        'shop_id' => 1
    ])->assertDatabaseHas('seller_shop', [
        'seller_id' => 6,
        'shop_id' => 1
    ]);
});


it('can modify relationships to sellers and remove relationships', function () {
    // create some sellers
    $sellers = factory(Seller::class, 5)->create();
    // create a shop
    $shop = factory(Shop::class)->create();
    // sync with sellers
    $shop->sellers()->sync($sellers->pluck('id'));

    // assert
    $this->patchJson('/api/v1/shops/1/relationships/sellers', [
        'data' => [
            [
                'id' => '1',
                'type' => 'sellers'
            ],
            [
                'id' => '2',
                'type' => 'sellers'
            ],
            [
                'id' => '5',
                'type' => 'sellers'
            ]
        ]
    ], [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json'
    ])->assertStatus(204);

    $this->assertDatabaseHas('seller_shop', [
        'seller_id' => 1,
        'shop_id' => 1
    ])->assertDatabaseHas('seller_shop', [
        'seller_id' => 2,
        'shop_id' => 1
    ])->assertDatabaseHas('seller_shop', [
        'seller_id' => 5,
        'shop_id' => 1
    ])->assertDatabaseMissing('seller_shop', [
        'seller_id' => 3,
        'shop_id' => 1
    ])->assertDatabaseMissing('seller_shop', [
        'seller_id' => 4,
        'shop_id' => 1
    ]);
});


it('can remove all relationships to sellers with an empty collection', function () {
    // create some sellers
    $sellers = factory(Seller::class, 3)->create();
    // create a shop
    $shop = factory(Shop::class)->create();
    // sync with sellers
    $shop->sellers()->sync($sellers->pluck('id'));

    // assert
    $this->patchJson('/api/v1/shops/1/relationships/sellers', [
        'data' => []
    ], [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json'
    ])->assertStatus(204);

    $this->assertDatabaseMissing('seller_shop', [
        'seller_id' => 1,
        'shop_id' => 1
    ])->assertDatabaseMissing('seller_shop', [
        'seller_id' => 2,
        'shop_id' => 1
    ])->assertDatabaseMissing('seller_shop', [
        'seller_id' => 3,
        'shop_id' => 1
    ]);
});


it('returns a 404 not found when trying to add relationship to a non existing object', function () {
    // create some sellers
    $sellers = factory(Seller::class, 3)->create();
    // create a shop
    $shop = factory(Shop::class)->create();

    // assert
    $this->patchJson('/api/v1/shops/1/relationships/sellers', [
        'data' => [
            [
                'id' => '5',
                'type' => 'sellers'
            ],
            [
                'id' => '6',
                'type' => 'sellers'
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


it('validates that the id member is given when updating a relationship', function () {
    // create some sellers
    $sellers = factory(Seller::class, 5)->create();
    // create a shop
    $shop = factory(Shop::class)->create();

    // assert
    $this->patchJson('/api/v1/shops/1/relationships/sellers', [
        'data' => [
            [
                'type' => 'sellers'
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
})->group('validate_shop_relation');


it('validates that the id member is a string when updating a relationship', function () {
    // create some sellers
    $sellers = factory(Seller::class, 5)->create();
    // create a shop
    $shop = factory(Shop::class)->create();

    // assert
    $this->patchJson('/api/v1/shops/1/relationships/sellers', [
        'data' => [
            [
                'id' => 5,
                'type' => 'sellers'
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
})->group('validate_shop_relation');


it('validates that the type member is a string when updating a relationship', function () {
    // create some sellers
    $sellers = factory(Seller::class, 5)->create();
    // create a shop
    $shop = factory(Shop::class)->create();

    // assert
    $this->patchJson('/api/v1/shops/1/relationships/sellers', [
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
})->group('validate_shop_relation');


it('validates that the type member has a value of sellers when updating a relationship', function () {
    // create some sellers
    $sellers = factory(Seller::class, 5)->create();
    // create a shop
    $shop = factory(Shop::class)->create();

    // assert
    $this->patchJson('/api/v1/shops/1/relationships/sellers', [
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
})->group('validate_shop_relation');


it('can get all related sellers as resource objects from related link', function () {
    // create some sellers
    $sellers = factory(Seller::class, 3)->create();
    // create a shop
    $shop = factory(Shop::class)->create();
    // sync the sellers with shop
    $shop->sellers()->sync($sellers->pluck('id'));

    // assert
    $this->getJson('/api/v1/shops/1/sellers', [
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
                        // 'name' => $sellers[0]->name,
                        // 'email' => $sellers[0]->email,
                        'user_id' => $sellers[0]->user_id,
                        'created_at' => $sellers[0]->created_at->toJSON(),
                        'updated_at' => $sellers[0]->updated_at->toJSON()
                    ]
                ],
                [
                    'id' => '2',
                    'type' => 'sellers',
                    'attributes' => [
                        // 'name' => $sellers[1]->name,
                        // 'email' => $sellers[1]->email,
                        'user_id' => $sellers[1]->user_id,
                        'created_at' => $sellers[1]->created_at->toJSON(),
                        'updated_at' => $sellers[1]->updated_at->toJSON()
                    ]
                ],
                [
                    'id' => '3',
                    'type' => 'sellers',
                    'attributes' => [
                        // 'name' => $sellers[2]->name,
                        // 'email' => $sellers[2]->email,
                        'user_id' => $sellers[2]->user_id,
                        'created_at' => $sellers[2]->created_at->toJSON(),
                        'updated_at' => $sellers[2]->updated_at->toJSON()
                    ]
                ]
            ]
        ]);
})->group('validate_shop_related');


it('includes related resource objects for sellers when an include query param to sellers is given', function () {
    // create some sellers
    $sellers = factory(Seller::class, 3)->create();
    // create a shop
    $shop = factory(Shop::class)->create();
    // sync the sellers with shop
    $shop->sellers()->sync($sellers->pluck('id'));

    // assert
    $this->getJson('/api/v1/shops/1?include=sellers', [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json'
    ])
        ->assertStatus(200)
        ->assertJson([
            'data' => [
                'id' => '1',
                'type' => 'shops',
                'relationships' => [
                    'sellers' => [
                        'links' => [
                            'self' => route('shops.relationships.sellers', [
                                'shop' => $shop->id
                            ]),
                            'related' => route('shops.sellers', [
                                'shop' => $shop->id
                            ])
                        ],
                        'data' => [
                            [
                                'id' => '1',
                                'type' => 'sellers'
                            ],
                            [
                                'id' => '2',
                                'type' => 'sellers'
                            ],
                            [
                                'id' => '3',
                                'type' => 'sellers'
                            ]
                        ]
                    ]
                ]
            ],
            'included' => [
                [
                    'id' => '1',
                    'type' => 'sellers',
                    'attributes' => [
                        // 'name' => $sellers[0]->name,
                        // 'email' => $sellers[0]->email,
                        'user_id' => $sellers[0]->user_id,
                        'created_at' => $sellers[0]->created_at->toJSON(),
                        'updated_at' => $sellers[0]->updated_at->toJSON()
                    ]
                ],
                [
                    'id' => '2',
                    'type' => 'sellers',
                    'attributes' => [
                        // 'name' => $sellers[1]->name,
                        // 'email' => $sellers[1]->email,
                        'user_id' => $sellers[1]->user_id,
                        'created_at' => $sellers[1]->created_at->toJSON(),
                        'updated_at' => $sellers[1]->updated_at->toJSON()
                    ]
                ],
                [
                    'id' => '3',
                    'type' => 'sellers',
                    'attributes' => [
                        // 'name' => $sellers[2]->name,
                        // 'email' => $sellers[2]->email,
                        'user_id' => $sellers[2]->user_id,
                        'created_at' => $sellers[2]->created_at->toJSON(),
                        'updated_at' => $sellers[2]->updated_at->toJSON()
                    ]
                ]
            ]
        ]);
})->group('validate_shop_related');


it('does not include related resource objects for sellers when an include query param to sellers is not given', function () {
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
})->group('validate_shop_related');


it('includes related resource objects for a collection when an include query param is given', function () {
    // create some sellers
    $sellers = factory(Seller::class, 3)->create();
    // create shops
    $shops = factory(Shop::class, 3)->create();
    // sync the sellers with shop
    $shops->each(function ($shop, $key) use ($sellers) {
        if ($key === 0) {
            $shop->sellers()->sync($sellers->pluck('id'));
        }
    });

    // assert
    $this->getJson('/api/v1/shops?include=sellers', [
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
                        'updated_at' => $shops[0]->updated_at->toJSON()
                    ],
                    'relationships' => [
                        'sellers' => [
                            'links' => [
                                'self' => route('shops.relationships.sellers', [
                                    'shop' => $shops[0]->id
                                ]),
                                'related' => route('shops.sellers', [
                                    'shop' => $shops[0]->id
                                ])
                            ],
                            'data' => [
                                [
                                    'id' => (string) $sellers->get(0)->id,
                                    'type' => 'sellers'
                                ],
                                [
                                    'id' => (string) $sellers->get(1)->id,
                                    'type' => 'sellers'
                                ],
                                [
                                    'id' => (string) $sellers->get(2)->id,
                                    'type' => 'sellers'
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
                    ],
                    'relationships' => [
                        'sellers' => [
                            'links' => [
                                'self' => route('shops.relationships.sellers', [
                                    'shop' => $shops[1]->id
                                ]),
                                'related' => route('shops.sellers', [
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
                    ],
                    'relationships' => [
                        'sellers' => [
                            'links' => [
                                'self' => route('shops.relationships.sellers', [
                                    'shop' => $shops[2]->id
                                ]),
                                'related' => route('shops.sellers', [
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
                    'type' => 'sellers',
                    'attributes' => [
                        // 'name' => $sellers[0]->name,
                        // 'email' => $sellers[0]->email,
                        'user_id' => $sellers[0]->user_id,
                        'created_at' => $sellers[0]->created_at->toJSON(),
                        'updated_at' => $sellers[0]->updated_at->toJSON(),
                    ]
                ],
                [
                    'id' => '2',
                    'type' => 'sellers',
                    'attributes' => [
                        // 'name' => $sellers[1]->name,
                        // 'email' => $sellers[1]->email,
                        'user_id' => $sellers[1]->user_id,
                        'created_at' => $sellers[1]->created_at->toJSON(),
                        'updated_at' => $sellers[1]->updated_at->toJSON(),
                    ]
                ],
                [
                    'id' => '3',
                    'type' => 'sellers',
                    'attributes' => [
                        // 'name' => $sellers[2]->name,
                        // 'email' => $sellers[2]->email,
                        'user_id' => $sellers[2]->user_id,
                        'created_at' => $sellers[2]->created_at->toJSON(),
                        'updated_at' => $sellers[2]->updated_at->toJSON(),
                    ]
                ],
            ]
        ]);
})->group('validate_shop_related');


it('only includes a related resource object once for a collection', function () {
    // create some sellers
    $sellers = factory(Seller::class, 3)->create();
    // create shops
    $shops = factory(Shop::class, 3)->create();
    // sync the sellers with shop
    $shops->each(function ($shop, $key) use ($sellers) {
        $shop->sellers()->sync($sellers->pluck('id'));
    });

    $this->get('/api/v1/shops?include=sellers', [
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
                        'updated_at' => $shops[0]->updated_at->toJSON()
                    ],
                    'relationships' => [
                        'sellers' => [
                            'links' => [
                                'self' => route('shops.relationships.sellers', [
                                    'shop' => $shops[0]->id
                                ]),
                                'related' => route('shops.sellers', [
                                    'shop' => $shops[0]->id
                                ])
                            ],
                            'data' => [
                                [
                                    'id' => (string) $sellers->get(0)->id,
                                    'type' => 'sellers'
                                ],
                                [
                                    'id' => (string) $sellers->get(1)->id,
                                    'type' => 'sellers'
                                ],
                                [
                                    'id' => (string) $sellers->get(2)->id,
                                    'type' => 'sellers'
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
                    ],
                    'relationships' => [
                        'sellers' => [
                            'links' => [
                                'self' => route('shops.relationships.sellers', [
                                    'shop' => $shops[1]->id
                                ]),
                                'related' => route('shops.sellers', [
                                    'shop' => $shops[1]->id
                                ])
                            ],
                            'data' => [
                                [
                                    'id' => (string) $sellers->get(0)->id,
                                    'type' => 'sellers'
                                ],
                                [
                                    'id' => (string) $sellers->get(1)->id,
                                    'type' => 'sellers'
                                ],
                                [
                                    'id' => (string) $sellers->get(2)->id,
                                    'type' => 'sellers'
                                ]
                            ]
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
                    ],
                    'relationships' => [
                        'sellers' => [
                            'links' => [
                                'self' => route('shops.relationships.sellers', [
                                    'shop' => $shops[2]->id
                                ]),
                                'related' => route('shops.sellers', [
                                    'shop' => $shops[2]->id
                                ])
                            ],
                            'data' => [
                                [
                                    'id' => (string) $sellers->get(0)->id,
                                    'type' => 'sellers'
                                ],
                                [
                                    'id' => (string) $sellers->get(1)->id,
                                    'type' => 'sellers'
                                ],
                                [
                                    'id' => (string) $sellers->get(2)->id,
                                    'type' => 'sellers'
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            'included' => [
                [
                    'id' => '1',
                    'type' => 'sellers',
                    'attributes' => [
                        // 'name' => $sellers[0]->name,
                        // 'email' => $sellers[0]->email,
                        'user_id' => $sellers[0]->user_id,
                        'created_at' => $sellers[0]->created_at->toJSON(),
                        'updated_at' => $sellers[0]->updated_at->toJSON(),
                    ]
                ],
                [
                    'id' => '2',
                    'type' => 'sellers',
                    'attributes' => [
                        // 'name' => $sellers[1]->name,
                        // 'email' => $sellers[1]->email,
                        'user_id' => $sellers[1]->user_id,
                        'created_at' => $sellers[1]->created_at->toJSON(),
                        'updated_at' => $sellers[1]->updated_at->toJSON(),
                    ]
                ],
                [
                    'id' => '3',
                    'type' => 'sellers',
                    'attributes' => [
                        // 'name' => $sellers[2]->name,
                        // 'email' => $sellers[2]->email,
                        'user_id' => $sellers[2]->user_id,
                        'created_at' => $sellers[2]->created_at->toJSON(),
                        'updated_at' => $sellers[2]->updated_at->toJSON(),
                    ]
                ],
            ]
        ])->assertJsonMissing([
            'included' => [
                [
                    "id" => '1',
                    "type" => "sellers",
                    "attributes" => [
                        // 'name' => $sellers[0]->name,
                        // 'email' => $sellers[0]->email,
                        'user_id' => $sellers[0]->user_id,
                        'created_at' => $sellers[0]->created_at->toJSON(),
                        'updated_at' => $sellers[0]->updated_at->toJSON(),
                    ]
                ],
                [
                    "id" => '2',
                    "type" => "sellers",
                    "attributes" => [
                        // 'name' => $sellers[1]->name,
                        // 'email' => $sellers[1]->email,
                        'user_id' => $sellers[1]->user_id,
                        'created_at' => $sellers[1]->created_at->toJSON(),
                        'updated_at' => $sellers[1]->updated_at->toJSON(),
                    ]
                ],
                [
                    "id" => '3',
                    "type" => "sellers",
                    "attributes" => [
                        // 'name' => $sellers[2]->name,
                        // 'email' => $sellers[2]->email,
                        'user_id' => $sellers[2]->user_id,
                        'created_at' => $sellers[2]->created_at->toJSON(),
                        'updated_at' => $sellers[2]->updated_at->toJSON(),
                    ]
                ],
                [
                    "id" => '1',
                    "type" => "sellers",
                    "attributes" => [
                        // 'name' => $sellers[0]->name,
                        // 'email' => $sellers[0]->email,
                        'user_id' => $sellers[0]->user_id,
                        'created_at' => $sellers[0]->created_at->toJSON(),
                        'updated_at' => $sellers[0]->updated_at->toJSON(),
                    ]
                ],
                [
                    "id" => '2',
                    "type" => "sellers",
                    "attributes" => [
                        // 'name' => $sellers[1]->name,
                        // 'email' => $sellers[1]->email,
                        'user_id' => $sellers[1]->user_id,
                        'created_at' => $sellers[1]->created_at->toJSON(),
                        'updated_at' => $sellers[1]->updated_at->toJSON(),
                    ]
                ],
                [
                    "id" => '3',
                    "type" => "sellers",
                    "attributes" => [
                        // 'name' => $sellers[2]->name,
                        // 'email' => $sellers[2]->email,
                        'user_id' => $sellers[2]->user_id,
                        'created_at' => $sellers[2]->created_at->toJSON(),
                        'updated_at' => $sellers[2]->updated_at->toJSON(),
                    ]
                ],
                [
                    "id" => '1',
                    "type" => "sellers",
                    "attributes" => [
                        // 'name' => $sellers[0]->name,
                        // 'email' => $sellers[0]->email,
                        'user_id' => $sellers[0]->user_id,
                        'created_at' => $sellers[0]->created_at->toJSON(),
                        'updated_at' => $sellers[0]->updated_at->toJSON(),
                    ]
                ],
                [
                    "id" => '2',
                    "type" => "sellers",
                    "attributes" => [
                        // 'name' => $sellers[1]->name,
                        // 'email' => $sellers[1]->email,
                        'user_id' => $sellers[1]->user_id,
                        'created_at' => $sellers[1]->created_at->toJSON(),
                        'updated_at' => $sellers[1]->updated_at->toJSON(),
                    ]
                ],
                [
                    "id" => '3',
                    "type" => "sellers",
                    "attributes" => [
                        // 'name' => $sellers[2]->name,
                        // 'email' => $sellers[2]->email,
                        'user_id' => $sellers[2]->user_id,
                        'created_at' => $sellers[2]->created_at->toJSON(),
                        'updated_at' => $sellers[2]->updated_at->toJSON(),
                    ]
                ]
            ]
        ]);
})->group('validate_shop_related');


it('does not include related resource objects for a collection when an include query param is not given', function () {
    // create shops
    $shops = factory(Shop::class, 3)->create();

    $this->get('/api/v1/shops', [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json',
    ])
        ->assertStatus(200)
        ->assertJsonMissing([
            'included' => [],
        ]);
})->group('validate_shop_related');


it('can soft delete shops when associated seller has been deleted through a delete request', function () {
    // create a seller
    $seller = factory(Seller::class)->create();

    // create some shops for that seller
    $shops = factory(Shop::class, 3)->create();
    // sync the sellers with shop
    $shops->each(function ($shop, $key) use ($seller) {
        $shop->sellers()->sync($seller->pluck('id'));
    });

    // assert for seller
    $this->delete('/api/v1/sellers/1', [],  [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json',
    ])->assertStatus(204);

    // check the database doesn't have the row
    $deletedTime = now()->setMilliseconds(0)->toJSON();
    $this->assertDatabaseHas('sellers', [
        'id' => '1',
        'user_id' => $seller->user_id,
        'deleted_at' => $deletedTime
    ]);

    // assert the shops are also deleted
    $this->assertDatabaseHas('shops', [
        'id' => '1',
        'deleted_at' => $deletedTime
    ]);
    $this->assertDatabaseHas('shops', [
        'id' => '2',
        'deleted_at' => $deletedTime
    ]);
    $this->assertDatabaseHas('shops', [
        'id' => '3',
        'deleted_at' => $deletedTime
    ]);
});


it('can restore soft deleted shops when associated seller has been deleted', function () {
    // create a seller
    $seller = factory(Seller::class)->create();

    // create some shops for that seller
    $shops = factory(Shop::class, 3)->create();
    // sync the sellers with shop
    $shops->each(function ($shop, $key) use ($seller) {
        $shop->sellers()->sync($seller->pluck('id'));
    });

    // delete the seller
    $seller->delete();

    // check the database doesn't have the row
    $deletedTime = now()->setMilliseconds(0)->toJSON();
    $this->assertDatabaseHas('sellers', [
        'id' => '1',
        'user_id' => $seller->user_id,
        'deleted_at' => $deletedTime
    ]);

    // assert the shops are also deleted
    $this->assertDatabaseHas('shops', [
        'id' => '1',
        'deleted_at' => $deletedTime
    ]);
    $this->assertDatabaseHas('shops', [
        'id' => '2',
        'deleted_at' => $deletedTime
    ]);
    $this->assertDatabaseHas('shops', [
        'id' => '3',
        'deleted_at' => $deletedTime
    ]);

    // restore the seller
    Seller::withTrashed()->where('id', $seller->id)->restore();

    // assert for seller
    $this->assertDatabaseHas('sellers', [
        'id' => $seller->id,
        'deleted_at' => null
    ]);

    // assert for the shops
    $this->assertDatabaseHas('shops', [
        'id' => '1',
        'deleted_at' => null
    ]);
    $this->assertDatabaseHas('shops', [
        'id' => '2',
        'deleted_at' => null
    ]);
    $this->assertDatabaseHas('shops', [
        'id' => '3',
        'deleted_at' => null
    ]);
});
