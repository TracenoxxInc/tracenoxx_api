<?php

namespace App\Http\Controllers\Products;

use Illuminate\Http\Request;
use App\Services\JSONAPIService;
use App\Models\Product\ProductUnit;
use App\Http\Controllers\Controller;
use App\Http\Requests\JSONAPIRequest;

class ProductUnitsController extends Controller
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
        return $this->service->fetchResources(ProductUnit::class, 'product-units');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\JSONAPIRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(JSONAPIRequest $request)
    {
        // store the product unit data
        return $this->service->createResource(
            ProductUnit::class,
            $request->getParams()->input('data.attributes')
        );
    }

    /**
     * Display the specified resource.
     * 
     * @param  integer  $productUnit
     * @return \Illuminate\Http\Response
     */
    public function show($productUnit)
    {
        // return as a product unit resource object
        return $this->service->fetchResource(ProductUnit::class, $productUnit, 'product-units');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product\ProductUnit  $productUnit
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ProductUnit $productUnit)
    {
        // update the product unit data
        return $this->service->updateResource(
            $productUnit,
            $request->input('data.attributes')
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product\ProductUnit  $productUnit
     * @return \Illuminate\Http\Response
     */
    public function destroy(ProductUnit $productUnit)
    {
        //
    }
}
