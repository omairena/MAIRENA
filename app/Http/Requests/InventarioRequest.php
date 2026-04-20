<?php

namespace App\Http\Requests;

use App\Productos;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Auth;

class InventarioRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'tipo_movimiento' => [
                'required'
            ],
            'condicion_movimiento' => [
                'required'
            ],
            'idcliente' => [
                'required'
            ],
            'observaciones' => [
                'required', 'min:2', 'max:200'
            ],
            'num_documento' => [
                'required'
            ],
        ];
    }
}
