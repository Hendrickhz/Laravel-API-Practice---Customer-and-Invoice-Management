<?php

namespace App\Http\Controllers\Api\V1;

use App\Filters\V1\InvoiceFilter;
use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Http\Requests\V1\StoreInvoiceRequest;
use App\Http\Requests\V1\UpdateInvoiceRequest;
use App\Http\Resources\V1\InvoiceCollection;
use App\Http\Resources\V1\InvoiceResource;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filter= new InvoiceFilter();
        $queryInvoices= $filter->transform($request);
        $invoices = Invoice::when(count($queryInvoices),function($query) use ($queryInvoices) {
                $query->where($queryInvoices);
        })->paginate(10)->withQueryString();

        return new InvoiceCollection($invoices);
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreInvoiceRequest $request)
    {
        //
    }

    public function bulkStore(Request $request)
    {

    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $invoice= Invoice::find($id);
        return new InvoiceResource($invoice);
        //  return  $invoice;
        //
    }



    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateInvoiceRequest $request, Invoice $invoice)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Invoice $invoice)
    {
        //
    }
}
