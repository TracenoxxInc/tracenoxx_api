<?php

namespace App\Http\Controllers\Shops;

use App\Models\Shop\Shop;
use App\Services\JSONAPIService;
use App\Http\Controllers\Controller;
use App\Http\Requests\JSONAPIRequest;

class ShopsController extends Controller
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
        return $this->service->fetchResources(Shop::class, 'shops');
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
            Shop::class,
            $request->getParams()->input('data.attributes')
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  Integer  $shop
     * @return \Illuminate\Http\Response
     */
    public function show($shop)
    {
        // return as a shop resource object
        return $this->service->fetchResource(Shop::class, $shop, 'shops');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\JSONAPIRequest $request
     * @param  \App\Models\Shop\Shop  $shop
     * @return \Illuminate\Http\Response
     */
    public function update(JSONAPIRequest $request, Shop $shop)
    {
        // update the shop data
        return $this->service->updateResource(
            $shop,
            $request->getParams()->input('data.attributes')
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Shop\Shop  $shop
     * @return \Illuminate\Http\Response
     */
    public function destroy(Shop $shop)
    {
        // delete the shop
        return $this->service->deleteResource($shop);
    }
}
