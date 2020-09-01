<?php

namespace App\Http\Controllers\Shops;

use App\Models\Shop\ShopType;
use App\Services\JSONAPIService;
use App\Http\Controllers\Controller;
use App\Http\Requests\JSONAPIRelationshipRequest;

class ShopTypesShopsRelationshipsController extends Controller
{
    private $service;

    public function __construct(JSONAPIService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \App\Models\Shop\ShopType $shopType
     * @return \Illuminate\Http\Response
     */
    public function index(ShopType $shopType)
    {
        // return the shop type relationships with shops
        return $this->service->fetchRelationship($shopType, 'shops');
    }

    /**
     * Update the shop type relationships with shops
     * 
     * @param \App\Http\Requests\JSONAPIRelationshipRequest $request
     * @param \App\Models\Shop\ShopType $shopType
     * 
     * @return \Illuminate\Http\Response
     */
    public function update(JSONAPIRelationshipRequest $request, ShopType $shopType)
    {
        // return relationships to shops
        return $this->service->updateManyToManyRelationships(
            $shopType,
            'shops',
            $request->input('data.*.id')
        );
    }
}
