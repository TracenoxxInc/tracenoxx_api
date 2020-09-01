<?php

use App\Models\Seller;

use function Tests\passportActingAs;
use Laravel\Lumen\Testing\DatabaseMigrations;

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
    $this->get('/api/v1/sellers/1', [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json'
    ])
        ->seeStatusCode(200)
        ->seeJson([
            'data' => [
                'id' => '1',
                'type' => 'sellers',
                'attributes' => [
                    'user_id' => $seller->user_id,
                    'created_at' => $seller->created_at->toJSON(),
                    'deleted_at' => null,
                    'updated_at' => $seller->updated_at->toJSON()
                ],
                'relationships' => [
                    'shops' => [
                        'data' => [],
                        'links' => [
                            'self' => route('sellers.relationships.shops', [
                                'seller' => $seller->id
                            ]),
                            'related' => route('sellers.shops', [
                                'seller' => $seller->id
                            ])
                        ]
                    ],
                    'users' => [
                        'data' => [],
                        'links' => [
                            'self' => route('sellers.relationships.users', [
                                'seller' => $seller->id
                            ]),
                            'related' => route('sellers.users', [
                                'seller' => $seller->id
                            ])
                        ]
                    ]
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
        ->seeStatusCode(200)
        ->seeJson([
            'data' => [
                [
                    'id' => '1',
                    'type' => 'sellers',
                    'attributes' => [
                        'user_id' => $sellers[0]->user_id,
                        'created_at' => $sellers[0]->created_at->toJSON(),
                        'deleted_at' => null,
                        'updated_at' => $sellers[0]->updated_at->toJSON()
                    ],
                    'relationships' => [
                        'shops' => [
                            'data' => [],
                            'links' => [
                                'self' => route('sellers.relationships.shops', [
                                    'seller' => $sellers[0]->id
                                ]),
                                'related' => route('sellers.shops', [
                                    'seller' => $sellers[0]->id
                                ])
                            ]
                        ],
                        'users' => [
                            'data' => [],
                            'links' => [
                                'self' => route('sellers.relationships.users', [
                                    'seller' => $sellers[0]->id
                                ]),
                                'related' => route('sellers.users', [
                                    'seller' => $sellers[0]->id
                                ])
                            ]
                        ]
                    ]
                ],
                [
                    "id" => "2",
                    "type" => "sellers",
                    "attributes" => [
                        'user_id' => $sellers[1]->user_id,
                        'created_at' => $sellers[1]->created_at->toJSON(),
                        'deleted_at' => null,
                        'updated_at' => $sellers[1]->updated_at->toJSON()
                    ],
                    'relationships' => [
                        'shops' => [
                            'data' => [],
                            'links' => [
                                'self' => route('sellers.relationships.shops', [
                                    'seller' => $sellers[1]->id
                                ]),
                                'related' => route('sellers.shops', [
                                    'seller' => $sellers[1]->id
                                ])
                            ]
                        ],
                        'users' => [
                            'data' => [],
                            'links' => [
                                'self' => route('sellers.relationships.users', [
                                    'seller' => $sellers[1]->id
                                ]),
                                'related' => route('sellers.users', [
                                    'seller' => $sellers[1]->id
                                ])
                            ]
                        ]
                    ]
                ],
                [
                    "id" => "3",
                    "type" => "sellers",
                    "attributes" => [
                        'user_id' => $sellers[2]->user_id,
                        'created_at' => $sellers[2]->created_at->toJSON(),
                        'deleted_at' => null,
                        'updated_at' => $sellers[2]->updated_at->toJSON()
                    ],
                    'relationships' => [
                        'shops' => [
                            'data' => [],
                            'links' => [
                                'self' => route('sellers.relationships.shops', [
                                    'seller' => $sellers[2]->id
                                ]),
                                'related' => route('sellers.shops', [
                                    'seller' => $sellers[2]->id
                                ])
                            ]
                        ],
                        'users' => [
                            'data' => [],
                            'links' => [
                                'self' => route('sellers.relationships.users', [
                                    'seller' => $sellers[2]->id
                                ]),
                                'related' => route('sellers.users', [
                                    'seller' => $sellers[2]->id
                                ])
                            ]
                        ]
                    ]
                ]
            ]
        ]);
});


it('it_can_paginate_sellers_through_a_page_query_parameter', function () {
    // create 6 shops for that user
    $sellers = factory(Seller::class, 6)->create();

    // assert for per page = 3 and page number = 1
    $this->get('/api/v1/sellers?page[size]=3&page[number]=1', [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json'
    ])
        ->seeStatusCode(200)
        ->seeJsonEquals([
            'data' => [
                [
                    'id' => '1',
                    'type' => 'sellers',
                    'attributes' => [
                        'user_id' => $sellers[0]->user_id,
                        'created_at' => $sellers[0]->created_at->toJSON(),
                        'deleted_at' => null,
                        'updated_at' => $sellers[0]->updated_at->toJSON()
                    ],
                    'relationships' => [
                        'shops' => [
                            'data' => [],
                            'links' => [
                                'self' => route('sellers.relationships.shops', [
                                    'seller' => $sellers[0]->id
                                ]),
                                'related' => route('sellers.shops', [
                                    'seller' => $sellers[0]->id
                                ])
                            ]
                        ],
                        'users' => [
                            'data' => [],
                            'links' => [
                                'self' => route('sellers.relationships.users', [
                                    'seller' => $sellers[0]->id
                                ]),
                                'related' => route('sellers.users', [
                                    'seller' => $sellers[0]->id
                                ])
                            ]
                        ]
                    ]
                ],
                [
                    "id" => "2",
                    "type" => "sellers",
                    "attributes" => [
                        'user_id' => $sellers[1]->user_id,
                        'created_at' => $sellers[1]->created_at->toJSON(),
                        'deleted_at' => null,
                        'updated_at' => $sellers[1]->updated_at->toJSON()
                    ],
                    'relationships' => [
                        'shops' => [
                            'data' => [],
                            'links' => [
                                'self' => route('sellers.relationships.shops', [
                                    'seller' => $sellers[1]->id
                                ]),
                                'related' => route('sellers.shops', [
                                    'seller' => $sellers[1]->id
                                ])
                            ]
                        ],
                        'users' => [
                            'data' => [],
                            'links' => [
                                'self' => route('sellers.relationships.users', [
                                    'seller' => $sellers[1]->id
                                ]),
                                'related' => route('sellers.users', [
                                    'seller' => $sellers[1]->id
                                ])
                            ]
                        ]
                    ]
                ],
                [
                    "id" => "3",
                    "type" => "sellers",
                    "attributes" => [
                        'user_id' => $sellers[2]->user_id,
                        'created_at' => $sellers[2]->created_at->toJSON(),
                        'deleted_at' => null,
                        'updated_at' => $sellers[2]->updated_at->toJSON()
                    ],
                    'relationships' => [
                        'shops' => [
                            'data' => [],
                            'links' => [
                                'self' => route('sellers.relationships.shops', [
                                    'seller' => $sellers[2]->id
                                ]),
                                'related' => route('sellers.shops', [
                                    'seller' => $sellers[2]->id
                                ])
                            ]
                        ],
                        'users' => [
                            'data' => [],
                            'links' => [
                                'self' => route('sellers.relationships.users', [
                                    'seller' => $sellers[2]->id
                                ]),
                                'related' => route('sellers.users', [
                                    'seller' => $sellers[2]->id
                                ])
                            ]
                        ]
                    ]
                ]
            ],
            'links' => [
                'first' => route('sellers.index', ['page[size]' => 3, 'page[number]' => 1]),
                'last' => route('sellers.index', ['page[size]' => 3, 'page[number]' => 2]),
                'prev' => null,
                'next' => route('sellers.index', ['page[size]' => 3, 'page[number]' => 2]),
            ],
            'meta' => [
                "current_page" => 1,
                "from" => 1,
                "last_page" =>  2,
                "path" => route('sellers.index'),
                "per_page" =>  3,
                "to" =>  3,
                "total" => 6
            ]
        ]);
})->skip();


it('can delete a seller through a delete request', function () {
    // create a seller
    $seller = factory(Seller::class)->create([
        'user_id' => $this->user->id
    ]);

    // assert 
    $this->delete('/api/v1/sellers/1', [],  [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json',
    ])->seeStatusCode(204);

    $deletedTime = now()->setMilliseconds(0)->toJSON();

    // check the database doesn't have the row
    $this->seeInDatabase('sellers', [
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
    $this->seeInDatabase('sellers', [
        'id' => '1',
        'user_id' => $seller->user_id,
        'deleted_at' => $deletedTime
    ]);

    // restore the seller
    Seller::withTrashed()->where('id', $seller->id)->restore();
    // assert restored
    $this->seeInDatabase('sellers', [
        'id' => $seller->id,
        'deleted_at' => null
    ]);
});


it('can create a seller from a resource object', function () {
    // assert
    $response = $this->json('POST', '/api/v1/sellers', [
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
    $createdTime = $response->response['data']['attributes']['created_at'];
    $updatedTime = $response->response['data']['attributes']['updated_at'];
    $sellerId = $response->response['data']['id'];

    $response->seeStatusCode(201)
        ->seeJson([
            'data' => [
                'id' => '1',
                'type' => 'sellers',
                'attributes' => [
                    'user_id' => $this->user->id,
                    'created_at' => $createdTime,
                    'updated_at' => $updatedTime
                ],
                'relationships' => [
                    'shops' => [
                        'data' => [],
                        'links' => [
                            'self' => route('sellers.relationships.shops', [
                                'seller' => $sellerId
                            ]),
                            'related' => route('sellers.shops', [
                                'seller' => $sellerId
                            ])
                        ]
                    ],
                    'users' => [
                        'data' => [],
                        'links' => [
                            'self' => route('sellers.relationships.users', [
                                'seller' => $sellerId
                            ]),
                            'related' => route('sellers.users', [
                                'seller' => $sellerId
                            ])
                        ]
                    ]
                ]
            ]
        ]);

    // assert the database has the seller's record
    $this->seeInDatabase('sellers', [
        'id' => '1',
        'user_id' => $this->user->id
    ]);
});


it('validates that the type member is given when creating a seller', function () {
    // assert
    $this->json('POST', '/api/v1/sellers', [
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
        ->seeStatusCode(422)
        ->seeJson([
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

    // assert the database has the seller's record
    $this->missingFromDatabase('sellers', [
        'id' => '1',
        'user_id' => $this->user->id
    ]);
})->group('validate_create_sellers');


it('validates that the type member has the value of users when creating a seller', function () {
    // assert
    $this->json('POST', '/api/v1/sellers', [
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
        ->seeStatusCode(422)
        ->seeJson([
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

    // assert the database has the seller's record
    $this->missingFromDatabase('sellers', [
        'id' => '1',
        'user_id' => $this->user->id
    ]);
})->group('validate_create_sellers');


it('validates that the attributes member has been given when creating a seller', function () {
    // assert
    $this->json('POST', '/api/v1/sellers', [
        'data' => [
            'type' => 'sellers'
        ]
    ],  [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json',
    ])
        ->seeStatusCode(422)
        ->seeJson([
            'errors' => [
                [
                    'title' => 'Validation Error',
                    'details' => 'The data.attributes field is required.',
                    'source' => [
                        'pointer' => '/data/attributes'
                    ]
                ],
                [
                    'title' => 'Validation Error',
                    'details' => 'The data.attributes.user id field is required.',
                    'source' => [
                        'pointer' => '/data/attributes/user_id'
                    ]
                ]
            ]
        ]);

    // assert the database has the seller's record
    $this->missingFromDatabase('sellers', [
        'id' => '1',
        'user_id' => $this->user->id
    ]);
})->group('validate_create_sellers');


it('validates that the attributes member is an object given when creating a seller', function () {
    // assert
    $this->json('POST', '/api/v1/sellers', [
        'data' => [
            'type' => 'sellers',
            'attributes' => 'not an object'
        ]
    ],  [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json',
    ])
        ->seeStatusCode(422)
        ->seeJson([
            'errors' => [
                [
                    'title' => 'Validation Error',
                    'details' => 'The data.attributes must be an array.',
                    'source' => [
                        'pointer' => '/data/attributes'
                    ]
                ],
                [
                    'title' => 'Validation Error',
                    'details' => 'The data.attributes.user id field is required.',
                    'source' => [
                        'pointer' => '/data/attributes/user_id'
                    ]
                ]
            ]
        ]);

    // assert the database has the seller's record
    $this->missingFromDatabase('sellers', [
        'id' => '1',
        'user_id' => $this->user->id
    ]);
})->group('validate_create_sellers');


it('validates that a user id attribute is given when creating a seller', function () {
    // assert
    $this->json('POST', '/api/v1/sellers', [
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
        ->seeStatusCode(422)
        ->seeJson([
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

    // assert the database has the seller's record
    $this->missingFromDatabase('sellers', [
        'id' => '1',
        'user_id' => $this->user->id
    ]);
})->group('validate_create_sellers');


it('validates that a user id attribute is unique when creating a seller', function () {

    // create a seller
    $seller = factory(Seller::class)->create();

    // now create another seller with the same user id
    $this->json('POST', '/api/v1/sellers', [
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
        ->seeStatusCode(422)
        ->seeJson([
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
    $this->json("PATCH", "/api/v1/sellers/{$seller2->id}", [
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
        ->seeStatusCode(422)
        ->seeJson([
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
