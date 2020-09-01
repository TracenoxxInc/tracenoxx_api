<?php

namespace App\Http\Controllers\Sellers;

use App\Models\Seller;
use App\Services\JSONAPIService;
use App\Http\Controllers\Controller;
use App\Http\Requests\JSONAPIRequest;

class SellersController extends Controller
{
    private $service;

    public function __construct(JSONAPIService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // return as a collection
        return $this->service->fetchResources(Seller::class, 'sellers');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\JSONAPIRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(JSONAPIRequest $request)
    {
        // store the shop data
        return $this->service->createResource(
            Seller::class,
            $request->getParams()->input('data.attributes')
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  Integer $seller
     * @return \Illuminate\Http\Response
     */
    public function show($seller)
    {
        // return as a seller resource object
        return $this->service->fetchResource(Seller::class, $seller, 'sellers');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\JSONAPIRequest $request
     * @param  \App\Models\Seller\Seller  $seller
     * @return \Illuminate\Http\Response
     */
    public function update(JSONAPIRequest $request, Seller $seller)
    {
        // update the seller data
        return $this->service->updateResource(
            $seller,
            $request->getParams()->input('data.attributes')
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Seller  $seller
     * @return \Illuminate\Http\Response
     */
    public function destroy(Seller $seller)
    {
        // delete the seller
        return $this->service->deleteResource($seller);
    }
}
