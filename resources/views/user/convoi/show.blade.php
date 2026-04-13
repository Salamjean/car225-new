@extends('user.layouts.template')

@section('title', 'Détail Convoi')

@section('content')
    <div class="max-w-6xl mx-auto space-y-6">
        <div class="inline-flex bg-white border border-gray-100 rounded-2xl p-1">
            <a href="{{ route('user.convoi.create') }}" class="px-4 py-2 rounded-xl text-xs font-black uppercase tracking-wider text-gray-600">
                Nouveau convoi
            </a>
            <a href="{{ route('user.convoi.index') }}" class="px-4 py-2 rounded-xl text-xs font-black uppercase tracking-wider bg-[#e94f1b] text-white">
                Mes convois
            </a>
        </div>

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-2xl sm:text-3xl font-black text-[#1A1D1F] tracking-tight">
                    Détail <span class="text-[#e94f1b]">Convoi</span>
                </h1>
                <p class="text-sm text-gray-500 font-medium">Référence : {{ $convoi->reference }}</p>
            </div>
            <a href="{{ route('user.convoi.index') }}"
                class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl bg-gray-100 text-gray-700 text-xs font-black uppercase tracking-wider hover:bg-gray-200 transition-all">
                <i class="fas fa-arrow-left"></i>
                Mes convois
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white rounded-2xl border border-gray-100 p-5">
                <p class="text-[10px] font-black uppercase tracking-wider text-gray-500 mb-2">Compagnie</p>
                <p class="text-sm font-bold text-gray-900">{{ $convoi->compagnie->name ?? 'Compagnie' }}</p>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 p-5">
                <p class="text-[10px] font-black uppercase tracking-wider text-gray-500 mb-2">Nombre de personnes</p>
                <p class="text-sm font-bold text-gray-900">{{ $convoi->nombre_personnes }}</p>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 p-5">
                <p class="text-[10px] font-black uppercase tracking-wider text-gray-500 mb-2">Itinéraire</p>
                <p class="text-sm font-bold text-gray-900">
                    {{ $convoi->itineraire ? ($convoi->itineraire->point_depart . ' -> ' . $convoi->itineraire->point_arrive) : '-' }}
                </p>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 p-5">
                <p class="text-[10px] font-black uppercase tracking-wider text-gray-500 mb-2">Statut</p>
                <p class="text-sm font-bold text-gray-900">{{ ucfirst(str_replace('_', ' ', $convoi->statut)) }}</p>
            </div>
        </div>

        <div class="bg-white rounded-[28px] border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 bg-gray-50">
                <h3 class="text-sm font-black text-gray-800 uppercase tracking-wider">Passagers</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="px-5 py-4 text-[10px] font-black uppercase tracking-wider text-gray-500">#</th>
                            <th class="px-5 py-4 text-[10px] font-black uppercase tracking-wider text-gray-500">Nom</th>
                            <th class="px-5 py-4 text-[10px] font-black uppercase tracking-wider text-gray-500">Prénoms</th>
                            <th class="px-5 py-4 text-[10px] font-black uppercase tracking-wider text-gray-500">Contact</th>
                            <th class="px-5 py-4 text-[10px] font-black uppercase tracking-wider text-gray-500">Email</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach ($convoi->passagers as $index => $passager)
                            <tr>
                                <td class="px-5 py-4 text-xs font-black text-gray-700">{{ $index + 1 }}</td>
                                <td class="px-5 py-4 text-sm font-semibold text-gray-900">{{ $passager->nom }}</td>
                                <td class="px-5 py-4 text-sm font-semibold text-gray-900">{{ $passager->prenoms }}</td>
                                <td class="px-5 py-4 text-sm font-semibold text-gray-700">{{ $passager->contact }}</td>
                                <td class="px-5 py-4 text-sm font-semibold text-gray-500">{{ $passager->email ?: '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

