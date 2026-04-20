<?php

namespace App\Http\Requests;

use App\Cajas;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Auth;

class CajasRequest extends FormRequest
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
            'idconfigfact' => [
                'required'
            ],
            'nombre_caja' => [
                'required', 'min:3'
            ],
            'codigo_unico' => [
                'required','integer','min:1', 'max:999', 'digits_between:1,5', Rule::unique('cajas')->where('idconfigfact', Auth::user()->idconfigfact)
            ],
            'monto_fondo' => [
                'required'
            ],
        ];
    }
}
