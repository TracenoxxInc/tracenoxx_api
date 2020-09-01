<?php

use App\Models\Product\ProductUnit;
use Laravel\Lumen\Testing\DatabaseMigrations;

use function Tests\passportActingAs;

uses(DatabaseMigrations::class);


beforeEach(function () {
    // authenticated user
    $this->user = passportActingAs();
});


it('returns a product unit as a resource object', function () {
    // create a product unit
    $productUnit = factory(ProductUnit::class)->create();

    // assert
    $this->get('/api/v1/product-units/1', [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json'
    ])
        ->seeStatusCode(200)
        ->seeJson([
            'data' => [
                'id' => '1',
                'type' => 'product-units',
                'attributes' => [
                    'name' => $productUnit->name,
                    'multiplier' => (string) $productUnit->multiplier
                ]
            ]
        ]);
    // TODO: fix later
    // ->assertJsonMissing([
    //     'data' => [
    //         'id' => '1',
    //         'type' => 'product-units',
    //         'attributes' => [
    //             'name' => $productUnit->name,
    //             'multiplier' => $productUnit->multiplier,
    //             'created_at' => null,
    //             'updated_at' => null
    //         ]
    //     ]
    // ]);
});


it('returns all product units as a collection of resource objects', function () {
    // create 3 product units for that user
    $productUnits = factory(ProductUnit::class, 3)->create();

    // assert
    $this->get('/api/v1/product-units', [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json'
    ])
        ->seeStatusCode(200)
        ->seeJson([
            'data' => [
                [
                    "id" => "1",
                    "type" => "product-units",
                    "attributes" => [
                        'name' => $productUnits[0]->name,
                        'multiplier' => (string) $productUnits[0]->multiplier
                    ]
                ],
                [
                    "id" => "2",
                    "type" => "product-units",
                    "attributes" => [
                        'name' => $productUnits[1]->name,
                        'multiplier' => (string) $productUnits[1]->multiplier
                    ]
                ],
                [
                    "id" => "3",
                    "type" => "product-units",
                    "attributes" => [
                        'name' => $productUnits[2]->name,
                        'multiplier' => (string) $productUnits[2]->multiplier
                    ]
                ]
            ]
        ]);
});


it('can sort product units by name through a sort query parameter', function () {
    // create some product units with names
    $productUnits = collect([
        'Liter',
        'Kilogram',
        'Pound'
    ])->map(function ($name) {
        return factory(ProductUnit::class)->create([
            'name' => $name
        ]);
    });

    // assert
    $this->get('/api/v1/product-units?sort=name', [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json'
    ])
        ->seeStatusCode(200)
        ->seeJson([
            'data' => [
                [
                    'id' => '2',
                    'type' => 'product-units',
                    'attributes' => [
                        'name' => $productUnits[1]->name,
                        'multiplier' => (string) $productUnits[1]->multiplier
                    ]
                ],
                [
                    'id' => '1',
                    'type' => 'product-units',
                    'attributes' => [
                        'name' => $productUnits[0]->name,
                        'multiplier' => (string) $productUnits[0]->multiplier
                    ]
                ],
                [
                    'id' => '3',
                    'type' => 'product-units',
                    'attributes' => [
                        'name' => $productUnits[2]->name,
                        'multiplier' => (string) $productUnits[2]->multiplier
                    ]
                ]
            ]
        ]);
})->group('sort_product_units');


it('can sort product units by name in descending order through a sort query parameter', function () {
    // create some product units with names
    $productUnits = collect([
        'Liter',
        'Kilogram',
        'Pound'
    ])->map(function ($name) {
        return factory(ProductUnit::class)->create([
            'name' => $name
        ]);
    });

    // assert
    $this->get('/api/v1/product-units?sort=-name', [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json'
    ])
        ->seeStatusCode(200)
        ->seeJson([
            'data' => [
                [
                    'id' => '3',
                    'type' => 'product-units',
                    'attributes' => [
                        'name' => $productUnits[2]->name,
                        'multiplier' => (string) $productUnits[2]->multiplier
                    ]
                ],
                [
                    'id' => '1',
                    'type' => 'product-units',
                    'attributes' => [
                        'name' => $productUnits[0]->name,
                        'multiplier' => (string) $productUnits[0]->multiplier
                    ]
                ],
                [
                    'id' => '2',
                    'type' => 'product-units',
                    'attributes' => [
                        'name' => $productUnits[1]->name,
                        'multiplier' => (string) $productUnits[1]->multiplier
                    ]
                ]
            ]
        ]);
})->group('sort_product_units');


it('can paginate product units through a page query parameter', function () {
    // create 10 product units for that user
    $productUnits = factory(ProductUnit::class, 10)->create();

    // assert for per page = 5 and page number = 1
    $this->get('/api/v1/product-units?page[size]=5&page[number]=1', [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json'
    ])
        ->seeStatusCode(200)
        ->seeJsonEquals([
            'data' => [
                [
                    'id' => '1',
                    'type' => 'product-units',
                    'attributes' => [
                        'name' => $productUnits[0]->name,
                        'multiplier' => (string) $productUnits[0]->multiplier
                    ]
                ],
                [
                    'id' => '2',
                    'type' => 'product-units',
                    'attributes' => [
                        'name' => $productUnits[1]->name,
                        'multiplier' => (string) $productUnits[1]->multiplier
                    ]
                ],
                [
                    'id' => '3',
                    'type' => 'product-units',
                    'attributes' => [
                        'name' => $productUnits[2]->name,
                        'multiplier' => (string) $productUnits[2]->multiplier
                    ]
                ],
                [
                    'id' => '4',
                    'type' => 'product-units',
                    'attributes' => [
                        'name' => $productUnits[3]->name,
                        'multiplier' => (string) $productUnits[3]->multiplier
                    ]
                ],
                [
                    'id' => '5',
                    'type' => 'product-units',
                    'attributes' => [
                        'name' => $productUnits[4]->name,
                        'multiplier' => (string) $productUnits[4]->multiplier
                    ]
                ],
            ],
            'links' => [
                'first' => route('product-units.index', ['page[size]' => 5, 'page[number]' => 1]),
                'last' => route('product-units.index', ['page[size]' => 5, 'page[number]' => 2]),
                'prev' => null,
                'next' => route('product-units.index', ['page[size]' => 5, 'page[number]' => 2]),
            ],
            'meta' => [
                "current_page" => 1,
                "from" => 1,
                "last_page" =>  2,
                "path" => route('product-units.index'),
                "per_page" =>  5,
                "to" =>  5,
                "total" => 10
            ]
        ]);
})->skip();


it('can create a product unit from a resource object', function () {
    // assert
    $response = $this->json('POST', '/api/v1/product-units', [
        'data' => [
            'type' => 'product-units',
            'attributes' => [
                'name' => 'Kilograms',
                'multiplier' => 1
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
                'type' => 'product-units',
                'attributes' => [
                    'name' => 'Kilograms',
                    'multiplier' => 1
                ]
            ]
        ]);
    // TODO: fix later
    // ->assertHeader('Location', url('/api/v1/product-units/1'));

    // assert the database has the product unit's recored
    $this->seeInDatabase('product_units', [
        'id' => '1',
        'name' => 'Kilograms',
        'multiplier' => 1
    ]);
});


it('can update a product unit from a resource object', function () {
    // create a product unit
    $productUnit = factory(ProductUnit::class)->create([
        'name' => 'Kilograms'
    ]);

    // assert
    $response = $this->json('PATCH', '/api/v1/product-units/1', [
        'data' => [
            'id' => '1',
            'type' => 'product-units',
            'attributes' => [
                'name' => 'Pound'
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
                'type' => 'product-units',
                'attributes' => [
                    'name' => 'Pound',
                    'multiplier' => (string) $productUnit->multiplier
                ]
            ]
        ]);

    $this->seeInDatabase('product_units', [
        'id' => '1',
        'name' => 'Pound'
    ])->missingFromDatabase('product_units', [
        'id' => '1',
        'name' => 'Kilogram'
    ]);
});
