@extends('onpc.layouts.app')

@section('title', 'Mon profil')

@section('content')
    <div class="max-w-3xl mx-auto">
        <h1 class="text-2xl md:text-3xl font-black text-gray-900 mb-6">Mon profil</h1>

        <form action="{{ route('onpc.profile.update') }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-6">
            @csrf
            <div class="flex items-center gap-4">
                @if($onpc->photo_path)
                    <img src="{{ Storage::url($onpc->photo_path) }}" class="w-20 h-20 rounded-full object-cover">
                @else
                    <div class="w-20 h-20 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-2xl">
                        {{ strtoupper(substr($onpc->name, 0, 1)) }}
                    </div>
                @endif
                <div>
                    <input type="file" name="photo_path" accept="image/*"
                        class="block py-2 px-3 bg-gray-50 border border-gray-200 rounded-xl file:mr-4 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700">
                    <p class="text-xs text-gray-500 mt-1">PNG / JPG, 2 Mo max</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="text-sm font-semibold text-gray-700">Nom</label>
                    <input name="name" value="{{ old('name', $onpc->name) }}" required
                        class="block w-full mt-1 px-4 py-2.5 bg-gray-50 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 focus:bg-white">
                </div>
                <div>
                    <label class="text-sm font-semibold text-gray-700">Email</label>
                    <input value="{{ $onpc->email }}" disabled class="block w-full mt-1 px-4 py-2.5 bg-gray-100 border-gray-200 rounded-xl text-gray-500">
                </div>
                <div>
                    <label class="text-sm font-semibold text-gray-700">Téléphone</label>
                    <input name="contact" value="{{ old('contact', $onpc->contact) }}" required
                        type="tel" inputmode="numeric" pattern="\d{10}" maxlength="10" minlength="10"
                        title="10 chiffres exactement"
                        oninput="this.value=this.value.replace(/[^0-9]/g,'').slice(0,10)"
                        class="block w-full mt-1 px-4 py-2.5 bg-gray-50 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 focus:bg-white">
                    <p class="text-[11px] text-gray-400 mt-1">Format : 10 chiffres</p>
                    @error('contact') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="text-sm font-semibold text-gray-700">Localisation</label>
                    <input name="localisation" value="{{ old('localisation', $onpc->localisation) }}" required
                        class="block w-full mt-1 px-4 py-2.5 bg-gray-50 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 focus:bg-white">
                </div>
            </div>

            <div class="border-t border-gray-100 pt-6">
                <h3 class="font-bold text-gray-900 mb-3">Changer le mot de passe</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-semibold text-gray-700">Nouveau mot de passe</label>
                        <input type="password" name="password" minlength="8"
                            class="block w-full mt-1 px-4 py-2.5 bg-gray-50 border-gray-200 rounded-xl">
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-gray-700">Confirmation</label>
                        <input type="password" name="password_confirmation" minlength="8"
                            class="block w-full mt-1 px-4 py-2.5 bg-gray-50 border-gray-200 rounded-xl">
                    </div>
                </div>
            </div>

            <button type="submit" class="w-full py-3 rounded-xl bg-blue-700 hover:bg-blue-800 text-white font-bold">
                <i class="fas fa-save mr-2"></i> Enregistrer
            </button>
        </form>
    </div>
@endsection
