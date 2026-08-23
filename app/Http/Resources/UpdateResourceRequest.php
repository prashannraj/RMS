<?php

namespace App\Http\Requests\Resource;

use App\Enums\ResourceStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateResourceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'category_id' => ['sometimes', 'exists:categories,id'],
            'status' => ['sometimes', Rule::enum(ResourceStatus::class)],
            'priority' => ['nullable', 'in:low,medium,high,critical'],
            'cost' => ['nullable', 'numeric', 'min:0'],
            'location' => ['nullable', 'string', 'max:255'],
            'serial_number' => ['nullable', 'string', 'max:100'],
            'purchase_date' => ['nullable', 'date'],
            'warranty_expiry' => ['nullable', 'date'],
            'metadata' => ['nullable', 'array'],
            'tags' => ['nullable', 'array'],
        ];
    }
}