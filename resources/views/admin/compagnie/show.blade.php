@extends('admin.layouts.template')

@section('content')
<div class="min-h-screen bg-gray-50 py-8 px-4">
    <div class="max-w-6xl mx-auto">
        <!-- fil d'ariane -->
        <nav class="flex mb-8" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3 text-sm">
                <li class="inline-flex items-center text-gray-400">
                    <a href="{{ route('compagnie.index') }}" class="hover:text-blue-600">Compagnies</a>
                </li>
                <li>
                    <div class="flex items-center text-gray-400">
                        <i class="fas fa-chevron-right mx-2 text-[10px]"></i>
                        <span>Détails de {{ $compagnie->name }}</span>
                    </div>
                </li>
            </ol>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Sidebar: Profil de la compagnie -->
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-white rounded-3xl shadow-xl overflow-hidden border border-gray-100">
                    <div class="h-32 bg-gradient-to-r from-blue-600 to-indigo-700"></div>
                    <div class="px-6 pb-6 mt-[-3rem]">
                        <div class="relative inline-block mb-4">
                            @if ($compagnie->path_logo)
                                <img class="h-24 w-24 rounded-2xl object-cover border-4 border-white shadow-lg bg-white"
                                    src="{{ asset('storage/' . $compagnie->path_logo) }}"
                                    alt="{{ $compagnie->name }}">
                            @else
                                <div class="h-24 w-24 rounded-2xl bg-white border-4 border-white shadow-lg flex items-center justify-center text-blue-600 font-bold text-3xl">
                                    {{ substr($compagnie->name, 0, 2) }}
                                </div>
                            @endif
                        </div>
                        
                        <h2 class="text-2xl font-black text-gray-900 leading-tight">{{ $compagnie->name }}</h2>
                        <span class="inline-block px-3 py-1 bg-blue-50 text-blue-600 rounded-full text-xs font-bold uppercase tracking-wider mb-4">
                            {{ $compagnie->sigle ?? 'N/A' }}
                        </span>

                        <div class="space-y-4 pt-4 border-t border-gray-100">
                            <div class="flex items-center text-gray-600">
                                <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center mr-3">
                                    <i class="fas fa-envelope text-blue-500"></i>
                                </div>
                                <span class="text-sm font-medium">{{ $compagnie->email }}</span>
                            </div>
                            <div class="flex items-center text-gray-600">
                                <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center mr-3">
                                    <i class="fas fa-phone text-blue-500"></i>
                                </div>
                                <span class="text-sm font-medium">{{ $compagnie->contact }}</span>
                            </div>
                            <div class="flex items-center text-gray-600">
                                <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center mr-3">
                                    <i class="fas fa-map-marker-alt text-blue-500"></i>
                                </div>
                                <span class="text-sm font-medium">{{ $compagnie->commune }}, {{ $compagnie->adresse }}</span>
                            </div>
                        </div>

                        <div class="mt-8 flex gap-3">
                            <a href="{{ route('compagnie.edit', $compagnie) }}" 
                                class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-800 font-bold py-3 rounded-xl text-center transition-all text-sm">
                                <i class="fas fa-edit mr-2"></i> Modifier
                            </a>
                            <a href="{{ route('compagnie.recharge.index') }}" 
                                class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-xl text-center shadow-lg shadow-blue-200 transition-all text-sm">
                                <i class="fas fa-plus-circle mr-2"></i> Recharger
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Card Solde -->
                <div class="bg-gradient-to-br from-gray-900 to-gray-800 rounded-3xl p-8 text-white shadow-2xl relative overflow-hidden">
                    <div class="relative z-10">
                        <p class="text-gray-400 font-bold text-xs uppercase tracking-widest mb-1">Solde de Billetterie</p>
                        <h3 class="text-4xl font-black mb-1">
                            {{ number_format($compagnie->tickets ?? 0, 0, ',', ' ') }}
                            <span class="text-lg text-blue-400">FCFA</span>
                        </h3>
                        <p class="text-xs text-gray-500">Utilisable pour les réservations clients</p>
                    </div>
                    <i class="fas fa-wallet absolute right-[-20px] bottom-[-20px] text-8xl text-white opacity-5"></i>
                </div>
            </div>

            <!-- Main: Historique des Rechargements -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-3xl shadow-xl overflow-hidden border border-gray-100 min-h-[600px]">
                    <div class="p-8 border-b border-gray-50 flex justify-between items-center">
                        <div>
                            <h3 class="text-xl font-black text-gray-900">Historique des Transactions</h3>
                            <p class="text-sm text-gray-500">Liste des derniers rechargements et débits</p>
                        </div>
                        <div class="flex gap-2">
                             <span class="px-3 py-1 bg-green-50 text-green-600 rounded-lg text-xs font-bold uppercase">Entrées</span>
                             <span class="px-3 py-1 bg-red-50 text-red-600 rounded-lg text-xs font-bold uppercase">Débits</span>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50/50 text-gray-400 text-[11px] font-black uppercase tracking-wider">
                                    <th class="px-8 py-4">Date</th>
                                    <th class="px-8 py-4">Motif</th>
                                    <th class="px-8 py-4 text-center">Quantité/Montant</th>
                                    <th class="px-8 py-4 text-right">Statut</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @forelse($historique as $item)
                                    <tr class="hover:bg-gray-50/80 transition-colors group">
                                        <td class="px-8 py-5">
                                            <span class="block font-bold text-gray-900 group-hover:text-blue-600 transition-colors">{{ $item->created_at->format('d M Y') }}</span>
                                            <span class="text-[10px] text-gray-400">{{ $item->created_at->format('H:i') }}</span>
                                        </td>
                                        <td class="px-8 py-5 text-sm font-medium text-gray-600">
                                            {{ $item->motif }}
                                        </td>
                                        <td class="px-8 py-5 text-center">
                                            @if($item->quantite > 0)
                                                <span class="inline-flex items-center px-3 py-1 bg-green-50 text-green-700 rounded-xl font-black text-sm">
                                                    +{{ number_format(abs($item->quantite), 0, ',', ' ') }}
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-3 py-1 bg-red-50 text-red-700 rounded-xl font-black text-sm">
                                                    -{{ number_format(abs($item->quantite), 0, ',', ' ') }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-8 py-5 text-right">
                                            <span class="inline-block w-2 h-2 rounded-full {{ $item->quantite > 0 ? 'bg-green-500' : 'bg-red-500' }} shadow-sm"></span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-8 py-20 text-center">
                                            <div class="opacity-30 mb-4">
                                                <i class="fas fa-file-invoice-dollar text-5xl"></i>
                                            </div>
                                            <p class="text-gray-400 font-medium">Aucune transaction enregistrée pour le moment.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($historique->hasPages())
                        <div class="p-8 border-t border-gray-50 bg-gray-50/30">
                            {{ $historique->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
