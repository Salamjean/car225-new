<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AgentRequest extends FormRequest
{ /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
           'name' => 'required|string|min:3|max:255',
           'prenom' => 'required|string|min:3|max:255',
           'email' => 'required|email|unique:agents,email',
           'contact' => 'required|string|max:10|exists:agents,contact',
           'commune' => 'required|string|max:255',
           'cas_urgence' => 'required|string|max:10',
           'profile_picture' => 'nullable|image|max:2048',
        ];
    }

    public function messages(){
        return [
             'name.required' => 'Le nom est obligatoire.',
             'name.min' => 'Le nom doit avoir au moins 3 caractères.',
             'name.max' => 'Le nom doit avoir au plus 255 caractères.',
             'contact.exists'  => 'Le contact doit exister dans la base de données.',
            'prenom.required' => 'Le prénom est obligatoire.',
            'prenom.min' => 'Le prénom doit avoir au moins 3 caractères.',
            'prenom.max' => 'Le prénom doit avoir au plus 255 caractères.',

            'email.required' => 'L\'adresse e-mail est obligatoire.',
            'email.email' => 'L\'adresse e-mail n\'est pas valide.',
            'email.unique' => 'Cette adresse e-mail est déjà associée à un compte.',
            'contact.required' => 'Le contact est obligatoire.',
            'contact.max' => 'Le contact doit avoir au plus 10 chiffres.',
            'contact.min' => 'Le contact doit avoir au moins 10 chiffres.',
            'commune.required' => 'La commune est obligatoire.',
            'cas_urgence.required' => 'La personne à contacter est obligatoire.',
            'profile_picture.image' => 'Le fichier doit être une image.',
            'profile_picture.mimes' => 'L\'image doit être au format jpeg, png, jpg, gif ou svg.',
            'profile_picture.max' => 'L\'image ne doit pas dépasser 2048 KB.',
        ];
    }
}
