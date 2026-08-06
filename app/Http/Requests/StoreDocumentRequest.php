<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'type' => ['required', 'in:Document,Vidéo,Audio,Guide'],
            'category_id' => ['required', 'exists:categories,id'],
            'author' => ['nullable', 'string', 'max:255'],
            'file' => ['nullable', 'file', 'max:20480', 'mimes:pdf,doc,docx,mp4,mp3,wav,ppt,pptx,jpg,png'],
            'external_url' => ['nullable', 'url'],
            'keywords' => ['nullable', 'array'],
            'keywords.*' => ['string', 'max:50'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Le titre est obligatoire.',
            'type.in' => 'Le type doit être : Document, Vidéo, Audio ou Guide.',
            'category_id.exists' => "La thématique sélectionnée n'existe pas.",
            'file.mimes' => "Format de fichier non pris en charge.",
        ];
    }
}
