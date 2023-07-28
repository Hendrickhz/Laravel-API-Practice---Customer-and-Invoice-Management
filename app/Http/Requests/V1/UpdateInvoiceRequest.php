<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInvoiceRequest extends FormRequest
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
            "customerId"=>"nullable|exists:customers,id",
            'billedDate' => 'nullable|date',
            'amount' => 'nullable|integer|min:10',
            'paidDate' => 'nullable|date',
            'status' => 'nullable|string|in:P,B, V',
        ];
    }
}
