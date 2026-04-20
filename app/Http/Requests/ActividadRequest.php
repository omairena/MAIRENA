<?php

namespace App\Http\Requests;

use App\Actividad;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class ActividadRequest extends FormRequest
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
            'hidden_descripcion' => [
                'required', 'min:3'
            ],
            'codigo_actividad' => [
                'required', 'max:6'
            ],
            'idconfigfact' => [
                'required'
            ],
        ];
    }
}
