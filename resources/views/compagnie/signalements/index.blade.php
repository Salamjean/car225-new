@extends('compagnie.layouts.template')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-red-50 py-8 px-4">
    <div class="mx-auto" style="width: 90%">
        <!-- En-tête -->
        <div class="text-center mb-12">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-red-600 rounded-2xl shadow-lg mb-4">
                <i class="fas fa-exclamation-triangle text-white text-2xl"></i>
            </div>
            <h1 class="text-4xl font-bold text-gray-900 mb-3">Gestion des Signalements</h1>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                Suivez et traitez les incidents signalés sur vos trajets
            </p>
        </div>

        <!-- Carte principale -->
        <div class="bg-white rounded-3xl shadow-xl overflow-hidden">
            <!-- En-tête de la carte -->
            <div class="px-8 py-6 bg-gradient-to-r from-red-600 to-red-500">
                <div class="flex flex-col sm:flex-row justify-between items-center">
                    <h2 class="text-2xl font-bold text-white mb-4 sm:mb-0">Signalements Reçus</h2>
                    <div class="flex items-center space-x-2">
                        <span class="px-4 py-2 bg-white/20 text-white rounded-lg font-bold backdrop-blur-sm">
                            Total: {{ $signalements->total() }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Contenu -->
            <div class="p-8">
                <!-- Tableau des signalements -->
                <div class="overflow-hidden rounded-2xl border border-gray-200">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Type</th>
                                <th class="px-6 py-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Véhicule</th>
                                <th class="px-6 py-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Trajet</th>
                                <th class="px-6 py-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Signalé par</th>
                                <th class="px-6 py-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Statut</th>
                                <th class="px-6 py-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($signalements as $signalement)
                            <tr class="hover:bg-gray-50 transition-colors duration-200">
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @php
                                        $colors = [
                                            'accident' => 'red',
                                            'panne' => 'orange',
                                            'retard' => 'yellow',
                                            'comportement' => 'purple',
                                            'autre' => 'gray'
                                        ];
                                        $color = $colors[$signalement->type] ?? 'gray';
                                    @endphp
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-{{ $color }}-100 text-{{ $color }}-800 capitalize border border-{{ $color }}-200">
                                        {{ ucfirst($signalement->type) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="text-sm font-bold text-gray-900">{{ $signalement->created_at->format('d/m/Y') }}</div>
                                    <div class="text-xs text-gray-500">{{ $signalement->created_at->format('H:i') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="text-sm font-mono font-bold text-gray-900">
                                        {{ $signalement->programme->vehicule->immatriculation ?? 'Non assigné' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="text-sm text-gray-900">
                                        {{ $signalement->programme->point_depart ?? '?' }} <i class="fas fa-arrow-right text-xs mx-1 text-gray-400"></i> {{ $signalement->programme->point_arrive ?? '?' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="flex items-center justify-center">
                                        <div class="h-8 w-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-500 font-bold mr-2">
                                            {{ substr($signalement->user->name ?? '?', 0, 1) }}
                                        </div>
                                        <div class="text-left">
                                            <div class="text-sm font-medium text-gray-900">{{ $signalement->user->name ?? 'Inconnu' }}</div>
                                            <div class="text-xs text-gray-500">{{ $signalement->user->telephone ?? '' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $signalement->statut === 'traite' ? 'bg-green-100 text-green-800 border border-green-200' : 'bg-red-100 text-red-800 border border-red-200' }} capitalize">
                                        <span class="w-2 h-2 mr-1.5 rounded-full {{ $signalement->statut === 'traite' ? 'bg-green-500' : 'bg-red-500' }}"></span>
                                        {{ ucfirst($signalement->statut) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <a href="{{ route('compagnie.signalements.show', $signalement->id) }}" 
                                       class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200 shadow-sm text-sm font-medium">
                                        <i class="fas fa-eye mr-2"></i> Détails
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center text-gray-500">
                                        <div class="bg-gray-100 p-4 rounded-full mb-4">
                                            <i class="fas fa-check-circle text-green-500 text-4xl"></i>
                                        </div>
                                        <p class="text-xl font-bold text-gray-700 mb-2">Aucun signalement</p>
                                        <p class="text-sm">Tout semble calme pour le moment. Bonne route !</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    {{ $signalements->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Styles spécifiques pour la pagination personnalisée si nécessaire, 
       sinon Tailwind gère bien les liens de pagination Laravel par défaut avec le bon provider */
    .pagination {
        display: flex;
        justify-content: center;
    }
</style>
@endsection