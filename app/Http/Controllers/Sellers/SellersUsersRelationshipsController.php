<?php

namespace App\Http\Controllers\Sellers;

use App\Models\Seller;
use App\Services\JSONAPIService;
use App\Http\Controllers\Controller;
use App\Http\Requests\JSONAPIRelationshipRequest;

class SellersUsersRelationshipsController extends Controller
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
        // return the seller relationships with users
        return $this->service->fetchRelationship($seller, 'users');
    }

    /**
     * Update the sellers relationships with users
     * 
     * @param \App\Http\Requests\JSONAPIRelationshipRequest $request
     * @param \App\Models\Seller $seller
     * 
     * @return \Illuminate\Http\Response
     */
    public function update(JSONAPIRelationshipRequest $request, Seller $seller)
    {
        // return relationships to users
        return $this->service->updateToOneRelationship(
            $seller,
            'users',
            $request->input('data.id')
        );
    }
}
