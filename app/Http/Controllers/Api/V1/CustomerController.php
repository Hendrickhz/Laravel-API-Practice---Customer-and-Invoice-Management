<?php

namespace App\Http\Controllers\Api\V1;

use App\Filters\V1\CustomerFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\StoreCustomerRequest;
use App\Http\Requests\V1\UpdateCustomerRequest;
use App\Http\Resources\V1\CustomerCollection;
use App\Http\Resources\V1\CustomerResource;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    /**
     * Fetch a paginated list of customers based on the provided filter criteria.
     * Optionally include invoices for each customer if requested.
     */
    public function index(Request $request)
    {
        // Create a new CustomerFilter instance and apply transformations based on the request
        $filter = new CustomerFilter();
        $queryCustomers = $filter->transform($request);

        // Check if 'includeInvoices' query parameter is present in the request
        $includeInvoices = $request->query('includeInvoices');

        // Fetch customers from the database based on the filter and includeInvoices parameter
        $customers = Customer::when(count($queryCustomers), function ($query) use ($queryCustomers) {
                $query->where($queryCustomers);
            })
            ->when($includeInvoices, function ($query) {
                $query->with('invoices');
            })
            ->latest("id")
            ->paginate(10)
            ->withQueryString();

        // Return a JSON response with the paginated list of customers
        return new CustomerCollection($customers);
    }

    /**
     * Store a newly created customer resource in the storage.
     */
    public function store(StoreCustomerRequest $request)
    {
        // Create a new customer in the database with the provided data
        $customer = Customer::create([
            "name" => $request->name,
            "type" => $request->type,
            "email" => $request->email,
            "address" => $request->address,
            "city" => $request->city,
            "state" => $request->state,
            "postal_code" => $request->postalCode,
        ]);

        // Return a JSON response with the newly created customer resource
        return response()->json([
            'data' => new CustomerResource($customer),
        ]);
    }

    /**
     * Display the specified customer resource.
     * Optionally include invoices if requested.
     */
    public function show($id, Request $request)
    {
        // Check if 'includeInvoices' query parameter is present in the request
        $includeInvoices = $request->query('includeInvoices');

        // Fetch the customer from the database by its ID and optionally include invoices
        $customer = Customer::where("id", $id)
            ->when($includeInvoices == "true", function ($query) {
                $query->with('invoices');
            })
            ->first();

        // Return a JSON response with the requested customer resource
        return response()->json([
            'data' => new CustomerResource($customer),
        ]);
    }

    /**
     * Update the specified customer resource in the storage.
     */
    public function update(UpdateCustomerRequest $request, $id)
    {
        // Find the customer by its ID
        $customer = Customer::find($id);

        // Check if the customer exists
        if (!$customer) {
            return response()->json([
                'success' => false,
                'message' => "Invalid Customer",
            ], 404);
        }

        // Update customer data based on the provided request fields
        if ($request->has('name')) {
            $customer->name = $request->name;
        }
        // Continue updating other fields here...

        // Save the updated customer record
        $customer->update();

        // Return a JSON response with the updated customer resource
        return response()->json([
            'data' => new CustomerResource($customer),
        ]);
    }

    /**
     * Remove the specified customer resource from storage.
     */
    public function destroy($id)
    {
        // Find the customer by its ID
        $customer = Customer::find($id);

        // Check if the customer exists
        if (!$customer) {
            return response()->json([
                'success' => false,
                'message' => "Invalid Customer",
            ], 404);
        }

        // Delete the customer record from the database
        $customer->delete();

        // Return an empty JSON response with HTTP status code 204 (No Content)
        return response()->json([], 204);
    }

}
