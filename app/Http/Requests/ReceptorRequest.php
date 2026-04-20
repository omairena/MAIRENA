<?php

namespace App\Http\Requests;

use App\Receptor;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class ReceptorRequest extends FormRequest
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
            'procesar_doc' => [
                'required'
            ],
            'idcaja' => [
                'required'
            ],
            'actividad' => [
                'required'
            ],
            'detalle_mensaje' => [
                'required'
            ],
            'condicion_impuesto' => [
                'required'
            ],
            'numero_documento_receptor' => [
                'required'
            ],
            'cargar_documento' => [
                'required'
            ],
        ];
    }
}
