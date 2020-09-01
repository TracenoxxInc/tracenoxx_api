<?php

namespace App\Http\Controllers\Sellers;

use App\Models\Seller;
use App\Services\JSONAPIService;
use App\Http\Controllers\Controller;

class SellersUsersRelatedController extends Controller
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
        // return as a collection
        return $this->service->fetchRelated($seller, 'users');
    }
}
