<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class BulkStoreInvoiceRequest extends FormRequest
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
        // Define the validation rules for each attribute in the bulk data array
        return [
            "*.customerId"=>"required|integer",
            '*.amount' => 'required|integer',
            '*.status' => 'required|string|in:P,B,V',
            '*.billedDate' => 'required|date',
            '*.paidDate' => 'nullable|date',

        ];
    }
    /**
     * Prepare the incoming data for validation.
     * This method modifies the request data to match the validation rules' attribute names.
     * It renames attributes like 'customerId' to 'customer_id' and 'billedDate' to 'billed_date'.
     */
    protected function prepareForValidation()
    {
        $data = [];
        // Loop through each object in the request data
        foreach ($this->toArray() as $obj) {
            // Rename 'customerId' to 'customer_id' and 'billedDate' to 'billed_date'
            $obj['customer_id'] = $obj['customerId'] ?? null;
            $obj['billed_date'] = $obj['billedDate'] ?? null;
            $obj['paid_date'] = $obj['paidDate'] ?? null;

            // Add the modified object to the $data array
            $data[] = $obj;
        }

        // Merge the modified data array back into the request data
        $this->merge($data);
    }
}
