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
    public function index(Request $request)
    {
        $filter = new CustomerFilter();
        $queryCustomers = $filter->transform($request);

        $includeInvoices = $request->query('includeInvoices');
        $customers = Customer::when(count($queryCustomers), function ($query) use ($queryCustomers) {
            $query->where($queryCustomers);
        })
            ->when($includeInvoices, function ($query) {
                $query->with('invoices');
            })
            ->latest("id")
            ->paginate(10)
            ->withQueryString();

        return new CustomerCollection($customers);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCustomerRequest $request)
    {
        $customer = Customer::create([
            "name" => $request->name,
            "type" => $request->type,
            "email" => $request->email,
            "address" => $request->address,
            "city" => $request->city,
            "state" => $request->state,
            "postal_code" => $request->postalCode,
        ]);
        return response()->json([
            'data' => new CustomerResource($customer),
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show($id, Request $request)
    {
        $includeInvoices = $request->query('includeInvoices');
        $customer = Customer::where("id", $id)
            ->when($includeInvoices == "true", function ($query) {
                $query->with('invoices');
            })->first();
        return response()->json([
            'data' => new CustomerResource($customer),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCustomerRequest $request, $id)
    {
        $customer = Customer::find($id);
        if (!$customer) {
            return response()->json([
                'success' => false,
                'message' => "Invalid Customer",
            ], 404);
        }
        if ($request->has('name')) {
            $customer->name = $request->name;
        }
        if ($request->has('type')) {
            $customer->type = $request->type;
        }
        if ($request->has('email')) {
            $customer->email = $request->email;
        }
        if ($request->has('address')) {
            $customer->address = $request->address;
        }
        if ($request->has('city')) {
            $customer->city = $request->city;
        }
        if ($request->has('state')) {
            $customer->state = $request->state;
        }
        if ($request->has('postalCode')) {
            $customer->postal_code = $request->postalCode;
        }

        $customer->update();

        return response()->json([
            'data' => new CustomerResource($customer),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $customer = Customer::find($id);
        if (!$customer) {
            return response()->json([
                'success' => false,
                'message' => "Invalid Customer",
            ], 404);
        }
        $customer->delete();
        return response()->json([

        ], 204);
    }
}
