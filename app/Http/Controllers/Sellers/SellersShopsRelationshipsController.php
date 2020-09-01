<?php

namespace App\Http\Controllers\Sellers;

use App\Models\Seller;
use Illuminate\Http\Request;
use App\Services\JSONAPIService;
use App\Http\Controllers\Controller;
use App\Http\Requests\JSONAPIRelationshipRequest;

class SellersShopsRelationshipsController extends Controller
{
    private $service;

    public function __construct(JSONAPIService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \App\Models\Seller $seller
     * @return \Illuminate\Http\Response
     */
    public function index(Seller $seller)
    {
        // return the seller relationships with shops
        return $this->service->fetchRelationship($seller, 'shops');
    }

    /**
     * Update the sellers relationships with shops
     * 
     * @param \App\Http\Requests\JSONAPIRelationshipRequest $request
     * @param \App\Models\Seller $seller
     * 
     * @return \Illuminate\Http\Response
     */
    public function update(JSONAPIRelationshipRequest $request, Seller $seller)
    {
        // return relationships to sellers
        return $this->service->updateManyToManyRelationships(
            $seller,
            'shops',
            $request->input('data.*.id')
        );
    }
}
