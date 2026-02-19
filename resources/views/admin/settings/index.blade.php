@extends('admin.layouts.template')

@section('content')
<div class="min-h-screen bg-gray-50 py-8 px-4">
    <div class="max-w-4xl mx-auto">
        <!-- En-tête -->
        <div class="mb-8">
            <h1 class="text-3xl font-extrabold text-gray-900 border-l-4 border-[#e94f1b] pl-4">Paramètres du Système</h1>
            <p class="mt-2 text-gray-600 pl-5">Configurez les comportements globaux de la plateforme.</p>
        </div>

        @if(session('success'))
        <div class="mb-6 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-sm flex items-center">
            <i class="fas fa-check-circle mr-3"></i>
            {{ session('success') }}
        </div>
        @endif

        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
            <form action="{{ route('admin.settings.update') }}" method="POST" class="divide-y divide-gray-100">
                @csrf
                
                @foreach($settings as $setting)
                <div class="p-8 hover:bg-gray-50 transition-colors duration-200">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6">
                        <div class="flex-1">
                            <h3 class="text-lg font-bold text-gray-900 mb-1 capitalize">{{ str_replace('_', ' ', $setting->key) }}</h3>
                            <p class="text-sm text-gray-500">{{ $setting->label ?? 'Pas de description.' }}</p>
                        </div>
                        
                        <div class="flex items-center">
                            @if($setting->type === 'boolean')
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="hidden" name="{{ $setting->key }}" value="0">
                                    <input type="checkbox" name="{{ $setting->key }}" value="1" {{ $setting->value == '1' ? 'checked' : '' }} class="sr-only peer">
                                    <div class="w-14 h-7 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-[#e94f1b]"></div>
                                </label>
                            @else
                                <input type="text" name="{{ $setting->key }}" value="{{ $setting->value }}" class="w-full sm:w-64 px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#e94f1b] focus:border-transparent">
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach

                <div class="p-8 bg-gray-50 flex justify-end">
                    <button type="submit" class="px-10 py-4 bg-[#e94f1b] text-white font-bold rounded-xl hover:bg-[#e89116] transform hover:-translate-y-1 transition-all duration-200 shadow-lg hover:shadow-xl flex items-center">
                        <i class="fas fa-save mr-2"></i>
                        Enregistrer les modifications
                    </button>
                </div>
            </form>
        </div>

        <!-- Section Information -->
        <div class="mt-8 bg-blue-50 border-l-4 border-blue-400 p-6 rounded-2xl">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-info-circle text-blue-400 text-xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-bold text-blue-800 uppercase tracking-wider mb-1">À propos du mode "Ticket"</h3>
                    <div class="text-sm text-blue-700 leading-relaxed">
                        <p>Lorsque le système de tickets est <strong>désactivé</strong> :</p>
                        <ul class="list-disc ml-5 mt-2 space-y-1">
                            <li>Les recharges ne sont plus obligatoires pour les compagnies.</li>
                            <li>Les réservations ne déduisent plus de solde aux compagnies.</li>
                            <li>Les contrôles de provision sont ignorés sur l'ensemble de la plateforme.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
