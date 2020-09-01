<?php

namespace App\Http\Controllers\Shops;

use App\Models\Shop\Shop;
use App\Services\JSONAPIService;
use App\Http\Controllers\Controller;
use App\Http\Requests\JSONAPIRelationshipRequest;

class ShopsSellersRelationshipsController extends Controller
{
    private $service;

    public function __construct(JSONAPIService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \App\Models\Shop\Shop $shop
     * @return \Illuminate\Http\Response
     */
    public function index(Shop $shop)
    {
        // return the shop relationships with sellers
        return $this->service->fetchRelationship($shop, 'sellers');
    }

    /**
     * Update the shops relationships with sellers
     * 
     * @param \App\Http\Requests\JSONAPIRelationshipRequest $request
     * @param \App\Models\Shop\Shop $shop
     * 
     * @return \Illuminate\Http\Response
     */
    public function update(JSONAPIRelationshipRequest $request, Shop $shop)
    {
        // return relationships to sellers
        return $this->service->updateManyToManyRelationships(
            $shop,
            'sellers',
            $request->input('data.*.id')
        );
    }
}
