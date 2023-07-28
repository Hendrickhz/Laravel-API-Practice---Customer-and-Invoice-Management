<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class StoreInvoiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        // Define the validation rules for each attribute in the request array
        return [
            "customerId"=>"required|exists:customers,id",
            'billedDate' => 'required|date',
            'amount' => 'required|integer|min:10',
            'paidDate' => 'nullable|date',
            'status' => 'required|string|in:P,B, V',
        ];
    }
}
