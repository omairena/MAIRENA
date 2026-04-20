<?php

namespace App\Http\Requests;

use App\Configuracion;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class ConfiguracionRequest extends FormRequest
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
            'ruta_certificado' => [
                'required'
            ],
            'clave_certificado' => [
                'required','min:4', 'max:4'
            ],
            'credenciales_conexion' => [
                'required','min:20', 'max:70'
            ],
            'clave_conexion' => [
                'required','min:20', 'max:70'
            ],
            'client_id' => [
                'required', 'integer'
            ],
            'nombre_emisor' => [
                'required', 'max:100'
            ],
            'tipo_id_emisor' => [
                'required', 'min:2','max:2'
            ],
            'numero_id_emisor' => [
                'required', 'integer','digits_between:9,12'
            ],
            'telefono_emisor' => [
                'required', 'min:8','max:20'
            ],
            'email_emisor' => [
                'required', 'email'
            ],
            'provincia_emisor' => [
                'required', 'integer','digits_between:1,1'
            ],
            'canton_emisor' => [
                'required'
            ],
            'distrito_emisor' => [
                'required'
            ],
            'direccion_emisor' => [
                'required', 'min:8','max:100'
            ],
            'sucursal' => [
                'required', 'min:3','max:3'
            ],
            'factor_receptor' => [
                'required'
            ],
        ];
    }
}
