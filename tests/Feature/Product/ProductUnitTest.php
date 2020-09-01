<?php

use App\Models\Product\ProductUnit;
use Illuminate\Foundation\Testing\DatabaseMigrations;

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
    $this->getJson('/api/v1/product-units/1', [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json'
    ])
        ->assertStatus(200)
        ->assertJson([
            'data' => [
                'id' => '1',
                'type' => 'product-units',
                'attributes' => [
                    'name' => $productUnit->name,
                    'multiplier' => $productUnit->multiplier
                ]
            ]
        ])->assertJsonMissing([
            'data' => [
                'id' => '1',
                'type' => 'product-units',
                'attributes' => [
                    'name' => $productUnit->name,
                    'multiplier' => $productUnit->multiplier,
                    'created_at' => null,
                    'updated_at' => null
                ]
            ]
        ]);
});


it('returns all product units as a collection of resource objects', function () {
    // create 3 product units for that user
    $productUnits = factory(ProductUnit::class, 3)->create();

    // assert
    $this->getJson('/api/v1/product-units', [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json'
    ])
        ->assertStatus(200)
        ->assertJson([
            'data' => [
                [
                    "id" => "1",
                    "type" => "product-units",
                    "attributes" => [
                        'name' => $productUnits[0]->name
                    ]
                ],
                [
                    "id" => "2",
                    "type" => "product-units",
                    "attributes" => [
                        'name' => $productUnits[1]->name
                    ]
                ],
                [
                    "id" => "3",
                    "type" => "product-units",
                    "attributes" => [
                        'name' => $productUnits[2]->name
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
    $this->getJson('/api/v1/product-units?sort=name', [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json'
    ])
        ->assertStatus(200)
        ->assertJson([
            'data' => [
                [
                    'id' => '2',
                    'type' => 'product-units',
                    'attributes' => [
                        'name' => $productUnits[1]->name,
                        'multiplier' => $productUnits[1]->multiplier
                    ]
                ],
                [
                    'id' => '1',
                    'type' => 'product-units',
                    'attributes' => [
                        'name' => $productUnits[0]->name,
                        'multiplier' => $productUnits[0]->multiplier
                    ]
                ],
                [
                    'id' => '3',
                    'type' => 'product-units',
                    'attributes' => [
                        'name' => $productUnits[2]->name,
                        'multiplier' => $productUnits[2]->multiplier
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
    $this->getJson('/api/v1/product-units?sort=-name', [
        'accept' => 'application/vnd.api+json',
        'content-type' => 'application/vnd.api+json'
    ])
        ->assertStatus(200)
        ->assertJson([
            'data' => [
                [
                    'id' => '3',
                    'type' => 'product-units',
                    'attributes' => [
                        'name' => $productUnits[2]->name,
                        'multiplier' => $productUnits[2]->multiplier
                    ]
                ],
                [
                    'id' => '1',
                    'type' => 'product-units',
                    'attributes' => [
                        'name' => $productUnits[0]->name,
                        'multiplier' => $productUnits[0]->multiplier
                    ]
                ],
                [
                    'id' => '2',
                    'type' => 'product-units',
                    'attributes' => [
                        'name' => $productUnits[1]->name,
                        'multiplier' => $productUnits[1]->multiplier
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
        ->assertStatus(200)
        ->assertJson([
            'data' => [
                [
                    'id' => '1',
                    'type' => 'product-units',
                    'attributes' => [
                        'name' => $productUnits[0]->name,
                        'multiplier' => $productUnits[0]->multiplier
                    ]
                ],
                [
                    'id' => '2',
                    'type' => 'product-units',
                    'attributes' => [
                        'name' => $productUnits[1]->name,
                        'multiplier' => $productUnits[1]->multiplier
                    ]
                ],
                [
                    'id' => '3',
                    'type' => 'product-units',
                    'attributes' => [
                        'name' => $productUnits[2]->name,
                        'multiplier' => $productUnits[2]->multiplier
                    ]
                ],
                [
                    'id' => '4',
                    'type' => 'product-units',
                    'attributes' => [
                        'name' => $productUnits[3]->name,
                        'multiplier' => $productUnits[3]->multiplier
                    ]
                ],
                [
                    'id' => '5',
                    'type' => 'product-units',
                    'attributes' => [
                        'name' => $productUnits[4]->name,
                        'multiplier' => $productUnits[4]->multiplier
                    ]
                ],
            ],
            'links' => [
                'first' => route('product-units.index', ['page[size]' => 5, 'page[number]' => 1]),
                'last' => route('product-units.index', ['page[size]' => 5, 'page[number]' => 2]),
                'prev' => null,
                'next' => route('product-units.index', ['page[size]' => 5, 'page[number]' => 2]),
            ]
        ]);
});


it('can create a product unit from a resource object', function () {
    // assert
    $response = $this->postJson('/api/v1/product-units', [
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

    $response->assertStatus(201)
        ->assertJson([
            'data' => [
                'id' => '1',
                'type' => 'product-units',
                'attributes' => [
                    'name' => 'Kilograms',
                    'multiplier' => 1
                ]
            ]
        ])->assertHeader('Location', url('/api/v1/product-units/1'));

    // assert the database has the product unit's recored
    $this->assertDatabaseHas('product_units', [
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
    $response = $this->patchJson('/api/v1/product-units/1', [
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

    $response->assertStatus(200)
        ->assertJson([
            'data' => [
                'id' => '1',
                'type' => 'product-units',
                'attributes' => [
                    'name' => 'Pound'
                ]
            ]
        ]);

    $this->assertDatabaseHas('product_units', [
        'id' => '1',
        'name' => 'Pound'
    ])->assertDatabaseMissing('product_units', [
        'id' => '1',
        'name' => 'Kilogram'
    ]);
});
