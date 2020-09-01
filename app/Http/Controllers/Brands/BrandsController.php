<?php

namespace App\Http\Controllers\Brands;

use App\Models\Brand\Brand;
use App\Services\JSONAPIService;
use App\Http\Controllers\Controller;
use App\Http\Requests\JSONAPIRequest;

class BrandsController extends Controller
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
        return $this->service->fetchResources(Brand::class, 'brands');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\JSONAPIRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(JSONAPIRequest $request)
    {
        // store the brand data
        return $this->service->createResource(
            Brand::class,
            $request->getParams()->input('data.attributes')
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  integer  $brand
     * @return \Illuminate\Http\Response
     */
    public function show($brand)
    {
        // return as a brand resource object
        return $this->service->fetchResource(Brand::class, $brand, 'brands');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\JSONAPIRequest $request
     * @param  string  $brand
     * @return \Illuminate\Http\Response
     */
    public function update(JSONAPIRequest $request, $brand)
    {
        // find the brand
        $brand = Brand::findOrFail($brand);

        // update the brand data
        return $this->service->updateResource(
            $brand,
            $request->getParams()->input('data.attributes')
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  string  $brand
     * @return \Illuminate\Http\Response
     */
    public function destroy($brand)
    {
        // find the brand
        $brand = Brand::findOrFail($brand);

        // delete the brand
        return $this->service->deleteResource($brand);
    }
}
