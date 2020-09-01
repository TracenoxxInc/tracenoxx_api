<?php

namespace App\Http\Controllers\Shops;

use Illuminate\Http\Request;
use App\Models\Shop\ShopType;
use App\Services\JSONAPIService;
use App\Http\Controllers\Controller;
use App\Http\Requests\JSONAPIRequest;

class ShopTypesController extends Controller
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
        return $this->service->fetchResources(ShopType::class, 'shop-types');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\JSONAPIRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(JSONAPIRequest $request)
    {
        // store the shop type data
        return $this->service->createResource(
            ShopType::class,
            $request->getParams()->input('data.attributes')
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $shopType
     * @return \Illuminate\Http\Response
     */
    public function show($shopType)
    {
        // return as a shop resource object
        return $this->service->fetchResource(ShopType::class, $shopType, 'shop-types');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\JSONAPIRequest  $request
     * @param  \App\Models\Shop\ShopType  $shopType
     * @return \Illuminate\Http\Response
     */
    public function update(JSONAPIRequest $request, ShopType $shopType)
    {
        // update the shop type data
        return $this->service->updateResource(
            $shopType,
            $request->getParams()->input('data.attributes')
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Shop\ShopType  $shopType
     * @return \Illuminate\Http\Response
     */
    public function destroy(ShopType $shopType)
    {
        // delete the shop type
        return $this->service->deleteResource($shopType);
    }
}
