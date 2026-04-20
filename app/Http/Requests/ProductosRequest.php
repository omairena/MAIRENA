<?php

namespace App\Http\Requests;

use App\Productos;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Auth;

class ProductosRequest extends FormRequest
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
            'tipo_producto' => [
                'required'
            ],
            'codigo_producto' => [
                'required','min:2', 'max:20', Rule::unique('productos')->where('idconfigfact', Auth::user()->idconfigfact)
            ],
            'nombre_producto' => [
                'required','min:5', 'max:100'
            ],
            'idunidadmedida' => [
                'required','min:1'
            ],
            'impuesto_iva' => [
                'required', 'min:2', 'max:2'
            ],
            'costo' => [
                'required'
            ],
        ];
    }
}
