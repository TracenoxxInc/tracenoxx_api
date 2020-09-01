<?php

return [
    'resources' => [
        'users' => [
            'allowedSorts' => [
                'name',
                'email',
                'created_at',
                'updated_at'
            ],
            'allowedIncludes' => [],
            'allowedFilters' => [],
            'validationRules' => [
                'create' => [
                    'data.attributes.name' => 'required|string|max:255',
                    'data.attributes.email' => 'required|email',
                    'data.attributes.password' => 'required|string|min:8|max:255|confirmed',
                ],
                'update' => [
                    'data.attributes.name' => 'sometimes|required|string|max:255',
                    'data.attributes.email' => 'sometimes|required|email',
                    'data.attributes.password' => 'sometimes|required|string|min:8|max:255|confirmed'
                ]
            ],
            'relationships' => []
        ],
        'sellers' => [
            'allowedIncludes' => [
                'shops',
                'users'
            ],
            'validationRules' => [
                'create' => [
                    'data.attributes.user_id' => 'required|unique:sellers,user_id|string'
                ],
                'update' => [
                    'data.attributes.user_id' => 'sometimes|required|unique:sellers,user_id|string'
                ]
            ],
            'relationships' => [
                [
                    'type' => 'shops',
                    'method' => 'shops',
                    'route_id' => 'seller'
                ],
                [
                    'type' => 'users',
                    'method' => 'users',
                    'route_id' => 'seller'
                ]
            ]
        ],
        'shops' => [
            'allowedSorts' => [
                'name',
                'created_at',
                'updated_at'
            ],
            'allowedIncludes' => [
                'sellers',
                'shop-types'
            ],
            'allowedFilters' => [],
            'validationRules' => [
                'create' => [
                    'data.attributes.name' => 'required|string|max:50'
                ],
                'update' => [
                    'data.attributes.name' => 'sometimes|required|string|max:50'
                ]
            ],
            'relationships' => [
                [
                    'type' => 'sellers',
                    'method' => 'sellers',
                    'route_id' => 'shop'
                ],
                [
                    'type' => 'shop-types',
                    'method' => 'shopTypes',
                    'route_id' => 'shop'
                ]
            ]
        ],
        'shop-types' => [
            'allowedSorts' => [
                'name',
                'created_at',
                'updated_at'
            ],
            'allowedIncludes' => [
                'shops'
            ],
            'validationRules' => [
                'create' => [
                    'data.attributes.name' => 'required|unique:shop_types,name|string|max:100',
                    'data.attributes.image' => 'string'
                ],
                'update' => [
                    'data.attributes.name' => 'sometimes|required|unique:shop_types,name|string|max:100',
                    'data.attributes.image' => 'string'
                ]
            ],
            'relationships' => [
                [
                    'type' => 'shops',
                    'method' => 'shops',
                    'route_id' => 'shopType'
                ]
            ]
        ],
        'brands' => [
            'allowedSorts' => [
                'name'
            ],
            'validationRules' => [
                'create' => [
                    'data.attributes.name' => 'required|unique:brands,name|string|max:100'
                ],
                'update' => [
                    'data.attributes.name' => 'sometimes|required|unique:brands,name|string|max:100'
                ]
            ]
        ],
        'product-units' => [
            'allowedSorts' => [
                'name'
            ],
            'validationRules' => [
                'create' => [
                    'data.attributes.name' => 'required|unique:product_units,name|string|max:100',
                    'data.attributes.multiplier' => 'integer'
                ],
                'update' => [
                    'data.attributes.name' => 'sometimes|required|unique:product_units,name|string|max:100',
                    'data.attributes.multiplier' => 'integer'
                ]
            ]
        ]
    ]
];
