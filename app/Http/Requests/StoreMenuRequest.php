<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMenuRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\Menu::class);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'route' => ['nullable', 'string', 'max:255'],
            'active_pattern' => ['nullable', 'string', 'max:255'],
            'icon_path' => ['nullable', 'string'],
            'color' => ['nullable', 'string', 'max:50'],
            'parent_id' => ['nullable', 'exists:menus,id'],
            'order' => ['required', 'integer', 'min:0'],
            'roles' => ['nullable', 'string', 'max:255'],
        ];
    }
}