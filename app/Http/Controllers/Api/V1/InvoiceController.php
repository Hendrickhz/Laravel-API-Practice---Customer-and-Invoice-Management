<?php

namespace App\Http\Controllers\Api\V1;

use App\Filters\V1\InvoiceFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\BulkStoreInvoiceRequest;
use App\Http\Requests\V1\StoreInvoiceRequest;
use App\Http\Requests\V1\UpdateInvoiceRequest;
use App\Http\Resources\V1\InvoiceCollection;
use App\Http\Resources\V1\InvoiceResource;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    /**
     * Fetch a paginated list of invoices based on the provided filter criteria.
     */
    public function index(Request $request)
    {
        // Create a new InvoiceFilter instance and apply transformations based on the request
        $filter = new InvoiceFilter();
        $queryInvoices = $filter->transform($request);

        // Fetch invoices from the database based on the filter
        $invoices = Invoice::when(count($queryInvoices), function ($query) use ($queryInvoices) {
            $query->where($queryInvoices);
        })->latest("id")->paginate(10)->withQueryString();

        // Return a JSON response with the paginated list of invoices
        return new InvoiceCollection($invoices);
    }

    /**
     * Store a newly created invoice resource in the storage.
     */
    public function store(StoreInvoiceRequest $request)
    {
        // Create a new invoice in the database with the provided data
        $invoice = Invoice::create([
            "customer_id" => $request->customerId,
            "billed_date" => $request->billedDate,
            "amount" => $request->amount,
            "paid_date" => $request->paidDate,
            "status" => $request->status
        ]);

        // Return a new InvoiceResource instance with the newly created invoice data
        return new InvoiceResource($invoice);
    }

    /**
     * Bulk store multiple invoice resources in the storage.
     */
    public function bulkStore(BulkStoreInvoiceRequest $request)
    {
        // Extract relevant data from the request and exclude unnecessary fields
        $bulk = collect($request->all())->map(function ($arr, $key) {
            return Arr::except($arr, ['customerId', 'billedDate', 'paidDate']);
        });

        // Insert the bulk data into the 'invoices' table
        Invoice::insert($bulk->toArray());

        // Return a JSON response indicating the success of the bulk insert operation
        return response()->json([
            "success" => true,
            "message" => "Bulk insert successful"
        ]);
    }

    /**
     * Display the specified invoice resource.
     */
    public function show($id)
    {
        // Find the invoice by its ID
        $invoice = Invoice::find($id);

        // Check if the invoice exists
        if (is_null($invoice)) {
            return response()->json([
                "success" => false,
                "message" => "Invoice Not Found"
            ], 404);
        }

        // Return a new InvoiceResource instance with the requested invoice data
        return new InvoiceResource($invoice);
    }

    /**
     * Update the specified invoice resource in storage.
     */
    public function update(UpdateInvoiceRequest $request, $id)
    {
        // Find the invoice by its ID
        $invoice = Invoice::find($id);

        // Check if the invoice exists
        if (is_null($invoice)) {
            return response()->json([
                "success" => false,
                "message" => "Invoice Not Found"
            ], 404);
        }

        // Update invoice data based on the provided request fields
        if ($request->has('customerId')) {
            $invoice->customer_id = $request->customerId;
        }
        // Continue updating other fields here...

        // Save the updated invoice record
        $invoice->update();

        // Return a new InvoiceResource instance with the updated invoice data
        return new InvoiceResource($invoice);
    }

    /**
     * Remove the specified invoice resource from storage.
     */
    public function destroy($id)
    {
        // Find the invoice by its ID
        $invoice = Invoice::find($id);

        // Check if the invoice exists
        if (is_null($invoice)) {
            return response()->json([
                "success" => false,
                "message" => "Invoice Not Found"
            ], 404);
        }

        // Delete the invoice record from the database
        $invoice->delete();

        // Return an empty JSON response with HTTP status code 204 (No Content)
        return response()->json([], 204);
    }

}
