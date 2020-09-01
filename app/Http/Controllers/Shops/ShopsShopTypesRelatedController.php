<?php

namespace App\Http\Controllers\Shops;

use App\Models\Shop\Shop;
use App\Services\JSONAPIService;
use App\Http\Controllers\Controller;

class ShopsShopTypesRelatedController extends Controller
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
        // return as a collection
        return $this->service->fetchRelated($shop, 'shop-types');
    }
}
