<?php

namespace App\Http\Requests;

use App\Cliente;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Auth;

class ClienteRequest extends FormRequest
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
            'tipo_id' => [
                'required', 'min:2','max:2'
            ],
            'num_id' => [
    'required',
    'regex:/^[A-Za-z0-9]+$/',
    'min:5',
    'max:20',
    Rule::unique('clientes')->where('idconfigfact', Auth::user()->idconfigfact)
],
            'nombre' => [
                'required', 'min:3', 'max:200'
            ],
            'email' => [
                'required', 'email'
            ],
            'telefono' => [
                'required', 'min:8', 'max:20'
            ],
            
            'direccion' => [
                'required', 'min:8','max:100'
            ],
            'tipo_cliente' => [
                'required', 'min:1', 'max:1'
            ],
            'razon_social' => [
                'required', 'min:1', 'max:150'
            ],
            'codigo_actividad' => [
                'required', 'min:1', 'max:6'
            ],
            //'tipo_clasificacion' => [
                //'required', 'min:1', 'max:1'
            //],
        ];
    }
}
