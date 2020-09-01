<?php

namespace App\Http\Controllers\Shops;

use App\Services\JSONAPIService;
use App\Http\Controllers\Controller;
use App\Models\Shop\ShopType;

class ShopTypesShopsRelatedController extends Controller
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
        // return as a collection
        return $this->service->fetchRelated($shopType, 'shops');
    }
}
