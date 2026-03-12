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

        <!-- ============================================ -->
        <!-- Section Mode Maintenance -->
        <!-- ============================================ -->
        @php
            $maintenanceMode = $settings->where('key', 'maintenance_mode')->first();
            $maintenanceMessage = $settings->where('key', 'maintenance_message')->first();
            $maintenanceBypass = $settings->where('key', 'maintenance_bypass_token')->first();
        @endphp

        <div class="mb-8 bg-white rounded-2xl shadow-xl overflow-hidden border {{ $maintenanceMode && $maintenanceMode->value == '1' ? 'border-red-300 ring-2 ring-red-200' : 'border-gray-100' }}">
            <!-- Header de la section -->
            <div class="px-8 py-6 {{ $maintenanceMode && $maintenanceMode->value == '1' ? 'bg-gradient-to-r from-red-500 to-red-600' : 'bg-gradient-to-r from-gray-700 to-gray-800' }} flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                        <i class="fas fa-tools text-white text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-white">Mode Maintenance</h2>
                        <p class="text-sm text-white/70">Bloquez l'accès au site pour tous sauf l'administrateur</p>
                    </div>
                </div>
                @if($maintenanceMode && $maintenanceMode->value == '1')
                    <span class="px-4 py-2 bg-white/20 rounded-full text-white text-sm font-bold flex items-center gap-2 animate-pulse">
                        <span class="w-2.5 h-2.5 bg-white rounded-full"></span>
                        ACTIVÉ
                    </span>
                @else
                    <span class="px-4 py-2 bg-white/10 rounded-full text-white/60 text-sm font-bold flex items-center gap-2">
                        <span class="w-2.5 h-2.5 bg-white/40 rounded-full"></span>
                        DÉSACTIVÉ
                    </span>
                @endif
            </div>

            <form action="{{ route('admin.settings.update') }}" method="POST">
                @csrf
                <div class="p-8 space-y-6">
                    <!-- Alerte quand activé -->
                    @if($maintenanceMode && $maintenanceMode->value == '1')
                    <div class="bg-red-50 border border-red-200 rounded-xl p-4 flex items-start gap-3">
                        <i class="fas fa-exclamation-triangle text-red-500 text-lg mt-0.5"></i>
                        <div>
                            <p class="text-sm font-bold text-red-800">Attention : Le site est actuellement en maintenance !</p>
                            <p class="text-xs text-red-600 mt-1">Tous les utilisateurs (compagnies, agents, chauffeurs, caissières, hôtesses, clients) sont bloqués. Seuls les administrateurs peuvent accéder au site.</p>
                        </div>
                    </div>
                    @endif

                    <!-- Toggle Maintenance -->
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-6 bg-gray-50 rounded-xl border border-gray-200">
                        <div>
                            <h3 class="text-base font-bold text-gray-900">Activer le mode maintenance</h3>
                            <p class="text-sm text-gray-500 mt-1">{{ $maintenanceMode->label ?? 'Active le mode maintenance sur le site' }}</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="hidden" name="maintenance_mode" value="0">
                            <input type="checkbox" name="maintenance_mode" value="1" {{ $maintenanceMode && $maintenanceMode->value == '1' ? 'checked' : '' }} class="sr-only peer" id="maintenance-toggle">
                            <div class="w-16 h-8 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[4px] after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-red-500 transition-colors"></div>
                        </label>
                    </div>

                    <!-- Message de maintenance -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="p-6 bg-gray-50 rounded-xl border border-gray-200">
                            <label class="block text-base font-bold text-gray-900 mb-2">
                                <i class="fas fa-comment-alt text-gray-400 mr-2"></i>
                                Message de maintenance
                            </label>
                            <p class="text-sm text-gray-500 mb-3">Affiché aux visiteurs quand le site est bloqué.</p>
                            <textarea name="maintenance_message" rows="3" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-400 focus:border-transparent resize-none text-sm"
                                placeholder="Ex: Notre site est en maintenance...">{{ $maintenanceMessage->value ?? '' }}</textarea>
                        </div>

                        <div class="p-6 bg-gray-50 rounded-xl border border-gray-200">
                            <label class="block text-base font-bold text-gray-900 mb-2">
                                <i class="fas fa-key text-gray-400 mr-2"></i>
                                Code Secret (Bypass)
                            </label>
                            <p class="text-sm text-gray-500 mb-3">Utilisez <code class="bg-gray-200 px-1 rounded">?maintenance_bypass=votre_code</code> pour accéder au site.</p>
                            <input type="text" name="maintenance_bypass_token" value="{{ $maintenanceBypass->value ?? '' }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-400 focus:border-transparent text-sm"
                                placeholder="Ex: dev2024">
                        </div>
                    </div>
                </div>

                <div class="px-8 pb-6">
                    <button type="submit" class="w-full sm:w-auto px-8 py-3 bg-gradient-to-r from-gray-800 to-gray-900 text-white font-bold rounded-xl hover:from-gray-700 hover:to-gray-800 transform hover:-translate-y-0.5 transition-all duration-200 shadow-lg hover:shadow-xl flex items-center justify-center gap-2">
                        <i class="fas fa-save"></i>
                        Enregistrer la configuration maintenance
                    </button>
                </div>
            </form>
        </div>

        <!-- ============================================ -->
        <!-- Section Paramètres Généraux (existants) -->
        <!-- ============================================ -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
            <div class="px-8 py-6 bg-gradient-to-r from-[#e94f1b] to-[#e89116] flex items-center gap-4">
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                    <i class="fas fa-cog text-white text-xl"></i>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-white">Paramètres Généraux</h2>
                    <p class="text-sm text-white/70">Configuration des fonctionnalités de la plateforme</p>
                </div>
            </div>

            <form action="{{ route('admin.settings.update') }}" method="POST" class="divide-y divide-gray-100">
                @csrf
                
                @foreach($settings->whereNotIn('key', ['maintenance_mode', 'maintenance_message', 'maintenance_bypass_token']) as $setting)
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

        <!-- Info Maintenance -->
        <div class="mt-4 bg-orange-50 border-l-4 border-orange-400 p-6 rounded-2xl">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-hard-hat text-orange-400 text-xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-bold text-orange-800 uppercase tracking-wider mb-1">À propos du mode "Maintenance"</h3>
                    <div class="text-sm text-orange-700 leading-relaxed">
                        <p>Lorsque le mode maintenance est <strong>activé</strong> :</p>
                        <ul class="list-disc ml-5 mt-2 space-y-1">
                            <li>Tous les utilisateurs sont redirigés vers une page de maintenance.</li>
                            <li>Seuls les <strong>administrateurs</strong> conservent l'accès complet.</li>
                            <li>Les routes API mobiles sont également bloquées.</li>
                            <li>Le message personnalisé est affiché sur la page de maintenance.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
