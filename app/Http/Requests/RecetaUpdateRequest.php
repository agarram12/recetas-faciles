<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Form Request para la validación al actualizar una receta.
 */
class RecetaUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'titulo'            => 'required|string|max:150',
            'descripcion'       => 'required|string',
            'categoria_id'      => 'required|exists:categorias,id',
            'url_imagen'        => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'tiempo_coccion'    => 'required|integer|min:1',
            'dificultad'        => 'required|in:Fácil,Media,Difícil',
            'pasos'             => 'required|array',
            'pasos.0'           => 'required|string',
            'imagenes_pasos.*'  => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'titulo.required'       => 'El título de la receta es obligatorio.',
            'titulo.max'            => 'El título es demasiado largo (máximo 150 letras).',
            'descripcion.required'  => 'Debes escribir una breve descripción.',
            'url_imagen.image'      => 'El archivo debe ser una imagen válida.',
            'url_imagen.mimes'      => 'La imagen debe ser JPEG, PNG, JPG o WebP.',
            'url_imagen.max'        => 'La imagen no puede superar los 2MB.',
            'tiempo_coccion.required' => 'Indica el tiempo de preparación.',
            'pasos.0.required'      => 'Debes escribir al menos el primer paso.',
            'imagenes_pasos.*.image' => 'Las imágenes de los pasos deben ser archivos de imagen válidos.',
            'imagenes_pasos.*.mimes' => 'Las imágenes de pasos deben ser JPEG, PNG, JPG o WebP.',
            'imagenes_pasos.*.max'   => 'Cada imagen de paso no puede superar los 2MB.',
        ];
    }
}
