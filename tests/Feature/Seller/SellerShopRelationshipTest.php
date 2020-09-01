<?php

use App\User;
use App\Models\Seller;
use App\Models\Shop\Shop;
use function Tests\passportActingAs;
use Illuminate\Foundation\Testing\DatabaseMigrations;

uses(DatabaseMigrations::class);


beforeEach(function () {
    // authenticated user
    $this->user = passportActingAs();
});


it('returns a relationship to shops adhering to json api spec', function () {
    // create some shops
    $shops = factory(Shop::class, 3)->create();
    // create a shop
    $seller = factory(Seller::class)->create([
        'user_id' => $this->user->id
    ]);
    // sync those shops to this seller
    $seller->shops()->sync($shops->pluck('id'));

    // assert
    $this->getJson('/api/v1/sellers/1?include=shops', [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json'
    ])
        ->assertStatus(200)
        ->assertJson([
            'data' => [
                'id' => '1',
                'type' => 'sellers',
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
                    ]
                ]
            ]
        ]);
});


it('returns a relationship to both shops and users adhering to json api spec', function () {
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
    $this->getJson('/api/v1/sellers/1?include=shops,users', [
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


test('a relationship link to shops returns all related shops as resource id object', function () {
    // create some shops
    $shops = factory(Shop::class, 3)->create();
    // create a seller
    $seller = factory(Seller::class)->create([
        'user_id' => $this->user->id
    ]);
    // sync those shops to this seller
    $seller->shops()->sync($shops->pluck('id'));

    // assert
    $this->getJson('/api/v1/sellers/1/relationships/shops', [
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


it('can modify relationships to shops and add new relationships', function () {
    // create some shops
    $shops = factory(Shop::class, 10)->create();
    // create a seller
    $seller = factory(Seller::class)->create([
        'user_id' => $this->user->id
    ]);

    // assert
    $this->patchJson('/api/v1/sellers/1/relationships/shops', [
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

    $this->assertDatabaseHas('seller_shop', [
        'shop_id' => 5,
        'seller_id' => 1
    ])->assertDatabaseHas('seller_shop', [
        'shop_id' => 6,
        'seller_id' => 1
    ]);
});


it('can modify relationships to shops and remove relationships', function () {
    // create some shops
    $shops = factory(Shop::class, 5)->create();
    // create a seller
    $seller = factory(Seller::class)->create([
        'user_id' => $this->user->id
    ]);
    // sync with sellers
    $seller->shops()->sync($shops->pluck('id'));

    // assert
    $this->patchJson('/api/v1/sellers/1/relationships/shops', [
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

    $this->assertDatabaseHas('seller_shop', [
        'shop_id' => 1,
        'seller_id' => 1
    ])->assertDatabaseHas('seller_shop', [
        'shop_id' => 2,
        'seller_id' => 1
    ])->assertDatabaseHas('seller_shop', [
        'shop_id' => 5,
        'seller_id' => 1
    ])->assertDatabaseMissing('seller_shop', [
        'shop_id' => 3,
        'seller_id' => 1
    ])->assertDatabaseMissing('seller_shop', [
        'shop_id' => 4,
        'seller_id' => 1
    ]);
});


it('can remove all relationships to shops with an empty collection', function () {
    // create some shops
    $shops = factory(Shop::class, 5)->create();
    // create a seller
    $seller = factory(Seller::class)->create([
        'user_id' => $this->user->id
    ]);
    // sync with sellers
    $seller->shops()->sync($shops->pluck('id'));

    // assert
    $this->patchJson('/api/v1/sellers/1/relationships/shops', [
        'data' => []
    ], [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json'
    ])->assertStatus(204);

    $this->assertDatabaseMissing('seller_shop', [
        'shop_id' => 1,
        'seller_id' => 1
    ])->assertDatabaseMissing('seller_shop', [
        'shop_id' => 2,
        'seller_id' => 1
    ])->assertDatabaseMissing('seller_shop', [
        'shop_id' => 3,
        'seller_id' => 1
    ]);
});


it('returns a 404 not found when trying to add relationship to a non existing object', function () {
    // create some shops
    $shops = factory(Shop::class, 5)->create();
    // create a seller
    $seller = factory(Seller::class)->create([
        'user_id' => $this->user->id
    ]);

    // assert
    $this->patchJson('/api/v1/sellers/1/relationships/shops', [
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


it('validates that the id member is given when updating a relationship', function () {
    // create some shops
    $shops = factory(Shop::class, 5)->create();
    // create a seller
    $seller = factory(Seller::class)->create([
        'user_id' => $this->user->id
    ]);

    // assert
    $this->patchJson('/api/v1/sellers/1/relationships/shops', [
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
})->group('validate_seller_relation');


it('validates that the id member is a string when updating a relationship', function () {
    // create some shops
    $shops = factory(Shop::class, 5)->create();
    // create a seller
    $seller = factory(Seller::class)->create([
        'user_id' => $this->user->id
    ]);

    // assert
    $this->patchJson('/api/v1/sellers/1/relationships/shops', [
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
})->group('validate_seller_relation');


it('validates that the type member is a string when updating a relationship', function () {
    // create some shops
    $shops = factory(Shop::class, 5)->create();
    // create a seller
    $seller = factory(Seller::class)->create([
        'user_id' => $this->user->id
    ]);

    // assert
    $this->patchJson('/api/v1/sellers/1/relationships/shops', [
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
})->group('validate_seller_relation');


it('validates that the type member has a value of shops when updating a relationship', function () {
    // create some shops
    $shops = factory(Shop::class, 5)->create();
    // create a seller
    $seller = factory(Seller::class)->create([
        'user_id' => $this->user->id
    ]);

    // assert
    $this->patchJson('/api/v1/sellers/1/relationships/shops', [
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
})->group('validate_seller_relation');


it('can get all related shops as resource objects from related link', function () {
    // create some shops
    $shops = factory(Shop::class, 3)->create();
    // create a seller
    $seller = factory(Seller::class)->create([
        'user_id' => $this->user->id
    ]);
    // sync with sellers
    $seller->shops()->sync($shops->pluck('id'));

    // assert
    $this->getJson('/api/v1/sellers/1/shops', [
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
})->group('validate_seller_related');


it('includes related resource objects for shops when an include query param to shops is given', function () {
    // create some shops
    $shops = factory(Shop::class, 3)->create();
    // create a seller
    $seller = factory(Seller::class)->create([
        'user_id' => $this->user->id
    ]);
    // sync with sellers
    $seller->shops()->sync($shops->pluck('id'));

    // assert
    $this->getJson('/api/v1/sellers/1?include=shops', [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json'
    ])
        ->assertStatus(200)
        ->assertJson([
            'data' => [
                'id' => '1',
                'type' => 'sellers',
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
})->group('validate_seller_related');


it('does not include related resource objects for shops when an include query param to shops is not given', function () {
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
})->group('validate_seller_related');


it('includes related resource objects for a collection when an include query param is given', function () {
    // create some sellers
    $sellers = factory(Seller::class, 3)->create();
    // create shops
    $shops = factory(Shop::class, 3)->create();
    // sync the shops with seller
    $sellers->each(function ($seller, $key) use ($shops) {
        if ($key === 0) {
            $seller->shops()->sync($shops->pluck('id'));
        }
    });

    // assert
    $this->getJson('/api/v1/sellers?include=shops', [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json',
    ])
        ->assertStatus(200)
        ->assertJson([
            'data' => [
                [
                    'id' => (string) $sellers[0]->id,
                    'type' => "sellers",
                    'attributes' => [
                        'user_id' => $sellers[0]->user_id,
                        'created_at' => $sellers[0]->created_at->toJSON(),
                        'updated_at' => $sellers[0]->updated_at->toJSON()
                    ],
                    'relationships' => [
                        'shops' => [
                            'links' => [
                                'self' => route('sellers.relationships.shops', [
                                    'seller' => $sellers[0]->id
                                ]),
                                'related' => route('sellers.shops', [
                                    'seller' => $sellers[0]->id
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
                [
                    'id' => (string) $sellers[1]->id,
                    'type' => 'sellers',
                    'attributes' => [
                        'user_id' => $sellers[1]->user_id,
                        'created_at' => $sellers[1]->created_at->toJSON(),
                        'updated_at' => $sellers[1]->updated_at->toJSON(),
                    ],
                    'relationships' => [
                        'shops' => [
                            'links' => [
                                'self' => route('sellers.relationships.shops', [
                                    'seller' => $sellers[1]->id
                                ]),
                                'related' => route('sellers.shops', [
                                    'seller' => $sellers[1]->id
                                ])
                            ],
                        ]
                    ]
                ],
                [
                    'id' => (string) $sellers[2]->id,
                    'type' => 'sellers',
                    'attributes' => [
                        'user_id' => $sellers[2]->user_id,
                        'created_at' => $sellers[2]->created_at->toJSON(),
                        'updated_at' => $sellers[2]->updated_at->toJSON(),
                    ],
                    'relationships' => [
                        'shops' => [
                            'links' => [
                                'self' => route('sellers.relationships.shops', [
                                    'seller' => $sellers[2]->id
                                ]),
                                'related' => route('sellers.shops', [
                                    'seller' => $sellers[2]->id
                                ])
                            ],
                        ]
                    ]
                ],
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
})->group('validate_seller_related');


it('only includes a related resource object once for a collection', function () {
    // create some sellers
    $sellers = factory(Seller::class, 3)->create();
    // create shops
    $shops = factory(Shop::class, 3)->create();
    // sync the shops with seller
    $sellers->each(function ($seller, $key) use ($shops) {
        $seller->shops()->sync($shops->pluck('id'));
    });

    // assert
    $this->getJson('/api/v1/sellers?include=shops', [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json',
    ])
        ->assertStatus(200)
        ->assertJson([
            'data' => [
                [
                    'id' => (string) $sellers[0]->id,
                    'type' => "sellers",
                    'attributes' => [
                        'user_id' => (string) $sellers[0]->user_id,
                        'created_at' => $sellers[0]->created_at->toJSON(),
                        'updated_at' => $sellers[0]->updated_at->toJSON()
                    ],
                    'relationships' => [
                        'shops' => [
                            'links' => [
                                'self' => route('sellers.relationships.shops', [
                                    'seller' => $sellers[0]->id
                                ]),
                                'related' => route('sellers.shops', [
                                    'seller' => $sellers[0]->id
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
                [
                    'id' => (string) $sellers[1]->id,
                    'type' => "sellers",
                    'attributes' => [
                        'user_id' => (string) $sellers[1]->user_id,
                        'created_at' => $sellers[1]->created_at->toJSON(),
                        'updated_at' => $sellers[1]->updated_at->toJSON()
                    ],
                    'relationships' => [
                        'shops' => [
                            'links' => [
                                'self' => route('sellers.relationships.shops', [
                                    'seller' => $sellers[1]->id
                                ]),
                                'related' => route('sellers.shops', [
                                    'seller' => $sellers[1]->id
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
                [
                    'id' => (string) $sellers[2]->id,
                    'type' => "sellers",
                    'attributes' => [
                        'user_id' => (string) $sellers[2]->user_id,
                        'created_at' => $sellers[2]->created_at->toJSON(),
                        'updated_at' => $sellers[2]->updated_at->toJSON()
                    ],
                    'relationships' => [
                        'shops' => [
                            'links' => [
                                'self' => route('sellers.relationships.shops', [
                                    'seller' => $sellers[2]->id
                                ]),
                                'related' => route('sellers.shops', [
                                    'seller' => $sellers[2]->id
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
        ])->assertJsonMissing([
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
                ],
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
})->group('validate_seller_related');


it('does not include related resource objects for a collection when an include query param is not given', function () {
    // create sellers
    $sellers = factory(Seller::class, 3)->create();

    $this->get('/api/v1/sellers', [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json',
    ])
        ->assertStatus(200)
        ->assertJsonMissing([
            'included' => [],
        ]);
})->group('validate_seller_related');


it('can soft delete a seller when associated user has been deleted through a delete request', function () {
    // create a user
    $user = factory(User::class)->create();

    // create a seller
    $seller = factory(Seller::class)->create([
        'user_id' => $user->id
    ]);

    // assert 
    $this->delete("/api/v1/users/{$user->id}", [],  [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json',
    ])->assertStatus(204);

    // check users doesn't have the row - soft deletes
    $deletedTime = now()->setMilliseconds(0)->toJSON();
    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'deleted_at' => $deletedTime
    ]);
    // check sellers doesn't have the row - soft deletes
    $this->assertDatabaseHas('sellers', [
        'id' => '1',
        'user_id' => $seller->user_id,
        'deleted_at' => $deletedTime
    ]);
});


it('can restore a deleted seller when associated user has been deleted', function () {
    // create a user
    $user = factory(User::class)->create();

    // create a seller
    $seller = factory(Seller::class)->create([
        'user_id' => $user->id
    ]);

    // delete the user
    $user->delete();

    // check users doesn't have the row - soft deletes
    $deletedTime = now()->setMilliseconds(0)->toJSON();
    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'deleted_at' => $deletedTime
    ]);
    // check sellers doesn't have the row - soft deletes
    $this->assertDatabaseHas('sellers', [
        'id' => '1',
        'user_id' => $seller->user_id,
        'deleted_at' => $deletedTime
    ]);

    // restore the user
    User::withTrashed()->where('id', $user->id)->restore();

    // assert restored in users
    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'deleted_at' => null
    ]);
    // assert restored in sellers
    $this->assertDatabaseHas('sellers', [
        'id' => $seller->id,
        'deleted_at' => null
    ]);
});
