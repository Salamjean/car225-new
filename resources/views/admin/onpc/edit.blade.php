@extends('admin.layouts.template')

@section('content')
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-5xl mx-auto">

        <div class="mb-8 bg-white rounded-3xl p-6 shadow-sm border border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-blue-100 text-blue-700 rounded-2xl flex items-center justify-center text-xl">
                    <i class="fas fa-edit"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-black text-gray-900 tracking-tight">Modifier {{ $onpc->name }}</h1>
                    <p class="text-sm text-gray-500">{{ $onpc->email }}</p>
                </div>
            </div>
            <a href="{{ route('admin.onpc.index') }}" class="flex items-center gap-2 px-5 py-2.5 bg-gray-50 hover:bg-gray-100 border border-gray-200 text-gray-700 rounded-xl font-bold">
                <i class="fas fa-arrow-left"></i> Liste
            </a>
        </div>

        @if(session('error'))
            <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4">{{ session('error') }}</div>
        @endif

        <form action="{{ route('admin.onpc.update', $onpc->id) }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1.5">Nom complet</label>
                    <input name="name" value="{{ old('name', $onpc->name) }}" required
                        class="block w-full px-4 py-3 bg-gray-50 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 focus:bg-white">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1.5">Email</label>
                    <input type="email" name="email" value="{{ old('email', $onpc->email) }}" required
                        class="block w-full px-4 py-3 bg-gray-50 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 focus:bg-white">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1.5">Téléphone</label>
                    <input name="contact" value="{{ old('contact', $onpc->contact) }}" required
                        type="tel" inputmode="numeric" pattern="\d{10}" maxlength="10" minlength="10"
                        title="10 chiffres exactement"
                        oninput="this.value=this.value.replace(/[^0-9]/g,'').slice(0,10)"
                        class="block w-full px-4 py-3 bg-gray-50 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 focus:bg-white">
                    <p class="text-[11px] text-gray-400 mt-1">Format : 10 chiffres</p>
                    @error('contact') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1.5">Localisation</label>
                    <input name="localisation" value="{{ old('localisation', $onpc->localisation) }}" required
                        class="block w-full px-4 py-3 bg-gray-50 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 focus:bg-white">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1.5">Statut</label>
                    <select name="statut" class="block w-full px-4 py-3 bg-gray-50 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 focus:bg-white">
                        <option value="actif" @selected($onpc->statut === 'actif')>Actif</option>
                        <option value="desactive" @selected($onpc->statut === 'desactive')>Désactivé</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1.5">Photo</label>
                    @if($onpc->photo_path)
                        <div class="mb-2">
                            <img src="{{ Storage::url($onpc->photo_path) }}" alt="" class="w-16 h-16 rounded-full object-cover">
                        </div>
                    @endif
                    <input type="file" name="photo_path" accept="image/*"
                        class="block w-full py-2.5 px-3 bg-gray-50 border border-gray-200 rounded-xl file:mr-4 file:py-1.5 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                </div>
            </div>

            <button type="submit" class="w-full flex items-center justify-center gap-3 py-4 bg-blue-700 hover:bg-blue-800 text-white rounded-2xl font-black text-lg shadow-lg">
                <i class="fas fa-save"></i> Enregistrer les modifications
            </button>
        </form>
    </div>
@endsection
