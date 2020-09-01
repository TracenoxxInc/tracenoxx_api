<?php

use App\Models\Brand\Brand;
use Laravel\Lumen\Testing\DatabaseMigrations;

use function Tests\passportActingAs;

uses(DatabaseMigrations::class);


beforeEach(function () {
    // authenticated user
    $this->user = passportActingAs();
});


it('returns a brand as a resource object', function () {

    // create a brand
    $brand = factory(Brand::class)->create();

    // assert
    $this->get('/api/v1/brands/1', [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json'
    ])
        ->seeStatusCode(200)
        ->seeJson([
            'data' => [
                'id' => '1',
                'type' => 'brands',
                'attributes' => [
                    'name' => $brand->name
                ]
            ]
        ]);
    // TODO: fix later
    // ->seeJsonMissing([
    //     'data' => [
    //         'id' => '1',
    //         'type' => 'brands',
    //         'attributes' => [
    //             'name' => $brand->name,
    //             'created_at' => null,
    //             'updated_at' => null
    //         ]
    //     ]
    // ]);
});


it('returns all brands as a collection of resource objects', function () {
    // create 3 brands for that user
    $brands = factory(Brand::class, 3)->create();

    // assert
    $this->get('/api/v1/brands', [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json'
    ])
        ->seeStatusCode(200)
        ->seeJson([
            'data' => [
                [
                    "id" => "1",
                    "type" => "brands",
                    "attributes" => [
                        'name' => $brands[0]->name
                    ]
                ],
                [
                    "id" => "2",
                    "type" => "brands",
                    "attributes" => [
                        'name' => $brands[1]->name
                    ]
                ],
                [
                    "id" => "3",
                    "type" => "brands",
                    "attributes" => [
                        'name' => $brands[2]->name
                    ]
                ]
            ]
        ]);
});


it('can sort brands by name through a sort query parameter', function () {
    // create some brands with names
    $brands = collect([
        'My shop',
        'Awesome shop',
        'Cool shop'
    ])->map(function ($name) {
        return factory(Brand::class)->create([
            'name' => $name
        ]);
    });

    // assert
    $this->get('/api/v1/brands?sort=name', [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json'
    ])
        ->seeStatusCode(200)
        ->seeJson([
            'data' => [
                [
                    'id' => '2',
                    'type' => 'brands',
                    'attributes' => [
                        'name' => 'Awesome shop'
                    ]
                ],
                [
                    'id' => '3',
                    'type' => 'brands',
                    'attributes' => [
                        'name' => 'Cool shop'
                    ]
                ],
                [
                    'id' => '1',
                    'type' => 'brands',
                    'attributes' => [
                        'name' => 'My shop'
                    ]
                ]
            ]
        ]);
})->group('sort_brands');


it('can sort brands by name in descending order through a sort query parameter', function () {
    // create some brands with names
    $brands = collect([
        'My shop',
        'Awesome shop',
        'Cool shop'
    ])->map(function ($name) {
        return factory(Brand::class)->create([
            'name' => $name
        ]);
    });

    // assert
    $this->get('/api/v1/brands?sort=-name', [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json'
    ])
        ->seeStatusCode(200)
        ->seeJson([
            'data' => [
                [
                    'id' => '1',
                    'type' => 'brands',
                    'attributes' => [
                        'name' => 'My shop'
                    ]
                ],
                [
                    'id' => '3',
                    'type' => 'brands',
                    'attributes' => [
                        'name' => 'Cool shop'
                    ]
                ],
                [
                    'id' => '2',
                    'type' => 'brands',
                    'attributes' => [
                        'name' => 'Awesome shop'
                    ]
                ]
            ]
        ]);
})->group('sort_brands');


it('can paginate brands through a page query parameter', function () {
    // create 10 brands for that user
    $brands = factory(Brand::class, 10)->create();

    // assert for per page = 5 and page number = 1
    $this->get('/api/v1/brands?page[size]=5&page[number]=1', [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json'
    ])
        ->seeStatusCode(200)
        ->seeJsonEquals([
            'data' => [
                [
                    'id' => '1',
                    'type' => 'brands',
                    'attributes' => [
                        'name' => $brands[0]->name
                    ]
                ],
                [
                    'id' => '2',
                    'type' => 'brands',
                    'attributes' => [
                        'name' => $brands[1]->name
                    ]
                ],
                [
                    'id' => '3',
                    'type' => 'brands',
                    'attributes' => [
                        'name' => $brands[2]->name
                    ]
                ],
                [
                    'id' => '4',
                    'type' => 'brands',
                    'attributes' => [
                        'name' => $brands[3]->name
                    ]
                ],
                [
                    'id' => '5',
                    'type' => 'brands',
                    'attributes' => [
                        'name' => $brands[4]->name
                    ]
                ],
            ],
            'links' => [
                'first' => route('brands.index', ['page[size]' => 5, 'page[number]' => 1]),
                'last' => route('brands.index', ['page[size]' => 5, 'page[number]' => 2]),
                'prev' => null,
                'next' => route('brands.index', ['page[size]' => 5, 'page[number]' => 2]),
            ],
            'meta' => [
                "current_page" => 1,
                "from" => 1,
                "last_page" =>  2,
                "path" => route('brands.index'),
                "per_page" =>  5,
                "to" =>  5,
                "total" => 10
            ]
        ]);
})->skip();


it('can create a brand from a resource object', function () {
    // assert
    $response = $this->json('POST', '/api/v1/brands', [
        'data' => [
            'type' => 'brands',
            'attributes' => [
                'name' => 'Cool Shop'
            ]
        ]
    ],  [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json',
    ]);

    $response->seeStatusCode(201)
        ->seeJson([
            'data' => [
                'id' => '1',
                'type' => 'brands',
                'attributes' => [
                    'name' => 'Cool Shop'
                ]
            ]
        ]);
    // TODO: fix later
    // ->assertHeader('Location', url('/api/v1/brands/1'));

    // assert the database has the brand's recored
    $this->seeInDatabase('brands', [
        'id' => '1',
        'name' => 'Cool Shop'
    ]);
});


it('validates that the type member is given when creating a brand', function () {
    // assert
    $this->json('POST', '/api/v1/brands', [
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

    // assert the database has the brand's recored
    $this->missingFromDatabase('brands', [
        'id' => '1',
        'name' => 'Cool Shop'
    ]);
})->group('validate_create_brands');


it('validates that the type member has the value of brands when creating a brand', function () {
    // assert
    $this->json('POST', '/api/v1/brands', [
        'data' => [
            'type' => 'brand',
            'attributes' => [
                'name' => 'Cool Shop'
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
                    'details' => 'The selected data.type is invalid.',
                    'source' => [
                        'pointer' => '/data/type'
                    ]
                ]
            ]
        ]);

    // assert the database has the brand's recored
    $this->missingFromDatabase('brands', [
        'id' => '1',
        'name' => 'Cool Shop'
    ]);
})->group('validate_create_brands');


it('validates that the attributes member has been given when creating a brands', function () {
    // assert
    $this->json('POST', '/api/v1/brands', [
        'data' => [
            'type' => 'brands'
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
                    'details' => 'The data.attributes.name field is required.',
                    'source' => [
                        'pointer' => '/data/attributes/name'
                    ]
                ]
            ]
        ]);

    // assert the database has the brand's recored
    $this->missingFromDatabase('brands', [
        'id' => '1',
        'name' => 'Cool Shop'
    ]);
})->group('validate_create_brands');


it('validates that the attributes member is an object given when creating a brand', function () {
    // assert
    $this->json('POST', '/api/v1/brands', [
        'data' => [
            'type' => 'brands',
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
                    'details' => 'The data.attributes.name field is required.',
                    'source' => [
                        'pointer' => '/data/attributes/name'
                    ]
                ]
            ]
        ]);

    // assert the database has the brand's recored
    $this->missingFromDatabase('brands', [
        'id' => '1',
        'name' => 'Cool Shop'
    ]);
})->group('validate_create_brands');


it('validates that a name attribute is given when creating a brand', function () {
    // assert
    $this->json('POST', '/api/v1/brands', [
        'data' => [
            'type' => 'brands',
            'attributes' => [
                'name' => ''
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
                    'details' => 'The data.attributes.name field is required.',
                    'source' => [
                        'pointer' => '/data/attributes/name'
                    ]
                ]
            ]
        ]);

    // assert the database has the brand's recored
    $this->missingFromDatabase('brands', [
        'id' => '1',
        'name' => 'Cool Shop'
    ]);
})->group('validate_create_brands');


it('validates that a name attribute is a string when creating a brand', function () {
    // assert
    $this->json('POST', '/api/v1/brands', [
        'data' => [
            'type' => 'brands',
            'attributes' => [
                'name' => 33
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
                    'details' => 'The data.attributes.name must be a string.',
                    'source' => [
                        'pointer' => '/data/attributes/name'
                    ]
                ]
            ]
        ]);

    // assert the database has the brand's recored
    $this->missingFromDatabase('brands', [
        'id' => '1',
        'name' => 'Cool Shop'
    ]);
})->group('validate_create_brands');


it('validates that a name attribute is not more than 100 characters when creating a brand', function () {
    // assert
    $this->json('POST', '/api/v1/brands', [
        'data' => [
            'type' => 'brands',
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
        ->seeStatusCode(422)
        ->seeJson([
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

    // assert the database has the brand's recored
    $this->missingFromDatabase('brands', [
        'id' => '1',
        'name' => 'Cool Shop'
    ]);
})->group('validate_create_brands');


it('validates that name attribute is unique when creating a brand', function () {

    // create a brand 
    $brand = factory(Brand::class)->create();

    // now create another brand with the same brand name 
    $this->json('POST', '/api/v1/brands', [
        'data' => [
            'type' => 'brands',
            'attributes' => [
                'name' => $brand->name,
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
                    'details' => 'The data.attributes.name has already been taken.',
                    'source' => [
                        'pointer' => '/data/attributes/name'
                    ]
                ]
            ]
        ]);
})->group('validate_create_brands');


it('can update a brand from a resource object', function () {
    // create a brand
    $brand = factory(Brand::class)->create();

    // assert
    $response = $this->json("PATCH", '/api/v1/brands/1', [
        'data' => [
            'id' => '1',
            'type' => 'brands',
            'attributes' => [
                'name' => 'Awesome Shop'
            ]
        ]
    ],  [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json',
    ]);

    $response->seeStatusCode(200)
        ->seeJson([
            'data' => [
                'id' => '1',
                'type' => 'brands',
                'attributes' => [
                    'name' => 'Awesome Shop'
                ]
            ]
        ]);

    $this->seeInDatabase('brands', [
        'id' => '1',
        'name' => 'Awesome Shop'
    ]);
});


it('validates that an id member is given when updating a brand', function () {
    // create a brand
    $brand = factory(Brand::class)->create();

    // assert
    $this->json("PATCH", '/api/v1/brands/1', [
        'data' => [
            'type' => 'brands',
            'attributes' => [
                'name' => 'Awesome Shop'
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
                    'details' => 'The data.id field is required.',
                    'source' => [
                        'pointer' => '/data/id'
                    ]
                ]
            ]
        ]);

    // assert the database has the brand's recored
    $this->seeInDatabase('brands', [
        'id' => '1',
        'name' => $brand->name
    ]);
})->group('validate_update_brands');


it('validates that an id member is a string when updating a brand', function () {
    // create a brand
    $brand = factory(Brand::class)->create();

    // assert
    $this->json("PATCH", '/api/v1/brands/1', [
        'data' => [
            'id' => 1,
            'type' => 'brands',
            'attributes' => [
                'name' => 'Awesome Shop'
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
                    'details' => 'The data.id must be a string.',
                    'source' => [
                        'pointer' => '/data/id'
                    ]
                ]
            ]
        ]);

    // assert the database has the brand's recored
    $this->seeInDatabase('brands', [
        'id' => '1',
        'name' => $brand->name
    ]);
})->group('validate_update_brands');


it('validates that the type member is given when updating a brand', function () {
    // create a brand
    $brand = factory(Brand::class)->create();

    // assert
    $this->json("PATCH", '/api/v1/brands/1', [
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

    // assert the database has the brand's recored
    $this->seeInDatabase('brands', [
        'id' => '1',
        'name' => $brand->name
    ]);
})->group('validate_update_brands');


it('validates that the type member has the value of brands when updating a brand', function () {
    // create a brand
    $brand = factory(Brand::class)->create();

    // assert
    $this->json("PATCH", '/api/v1/brands/1', [
        'data' => [
            'id' => '1',
            'type' => 'brand',
            'attributes' => [
                'name' => 'Awesome Shop'
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
                    'details' => 'The selected data.type is invalid.',
                    'source' => [
                        'pointer' => '/data/type'
                    ]
                ]
            ]
        ]);

    // assert the database has the brand's recored
    $this->seeInDatabase('brands', [
        'id' => '1',
        'name' => $brand->name
    ]);
})->group('validate_update_brands');


it('validates that the attributes member has been given when updating a brand', function () {
    // create a brand
    $brand = factory(Brand::class)->create();

    // assert
    $this->json("PATCH", '/api/v1/brands/1', [
        'data' => [
            'id' => '1',
            'type' => 'brands'
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
                    'details' => 'The data.attributes.name field is required.',
                    'source' => [
                        'pointer' => '/data/attributes/name'
                    ]
                ]
            ]
        ]);

    // assert the database has the brand's recored
    $this->seeInDatabase('brands', [
        'id' => '1',
        'name' => $brand->name
    ]);
})->group('validate_update_brands');


it('validates that the attributes member is an object given when updating a brand', function () {
    // create a brand
    $brand = factory(Brand::class)->create();

    // assert
    $this->json("PATCH", '/api/v1/brands/1', [
        'data' => [
            'id' => '1',
            'type' => 'brands',
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
                    'details' => 'The data.attributes.name field is required.',
                    'source' => [
                        'pointer' => '/data/attributes/name'
                    ]
                ]
            ]
        ]);

    // assert the database has the brand's recored
    $this->seeInDatabase('brands', [
        'id' => '1',
        'name' => $brand->name
    ]);
})->group('validate_update_brands');


it('validates that a name attribute is a string when updating a brand', function () {
    // create a brand
    $brand = factory(Brand::class)->create();

    // assert
    $this->json("PATCH", '/api/v1/brands/1', [
        'data' => [
            'id' => '1',
            'type' => 'brands',
            'attributes' => [
                'name' => 33
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
                    'details' => 'The data.attributes.name must be a string.',
                    'source' => [
                        'pointer' => '/data/attributes/name'
                    ]
                ]
            ]
        ]);

    // assert the database has the brand's recored
    $this->seeInDatabase('brands', [
        'id' => '1',
        'name' => $brand->name
    ]);
})->group('validate_update_brands');


it('validates that a name attribute is not more than 100 characters when updating a brand', function () {
    // create a brand
    $brand = factory(Brand::class)->create();

    // assert
    $this->json("PATCH", '/api/v1/brands/1', [
        'data' => [
            'id' => '1',
            'type' => 'brands',
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
        ->seeStatusCode(422)
        ->seeJson([
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

    // assert the database has the brand's recored
    $this->seeInDatabase('brands', [
        'id' => '1',
        'name' => $brand->name
    ]);
})->group('validate_update_brands');


it('validates that a name attribute is unique when updating a brand', function () {

    // create a brand 
    $brand1 = factory(Brand::class)->create();
    // create another brand 
    $brand2 = factory(Brand::class)->create();

    // now update the brand2 with the same name of the brand1
    $this->json("PATCH", "/api/v1/brands/{$brand2->id}", [
        'data' => [
            'id' => (string) $brand2->id,
            'type' => 'brands',
            'attributes' => [
                'name' => $brand1->name,
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
                    'details' => 'The data.attributes.name has already been taken.',
                    'source' => [
                        'pointer' => '/data/attributes/name'
                    ]
                ]
            ]
        ]);
})->group('validate_update_brands');


it('can delete a brand through a delete request', function () {
    // create a brand
    $brand = factory(Brand::class)->create();

    // assert 
    $this->delete('/api/v1/brands/1', [],  [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json',
    ])->seeStatusCode(204);

    // check the database doesn't have the row
    $this->missingFromDatabase('brands', [
        'id' => '1',
        'name' => $brand->name
    ]);
});
