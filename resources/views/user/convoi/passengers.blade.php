@extends('user.layouts.template')

@section('title', 'Passagers du Convoi')

@section('content')
    <div class="max-w-6xl mx-auto space-y-6">
        <div class="inline-flex bg-white border border-gray-100 rounded-2xl p-1">
            <a href="{{ route('user.convoi.create') }}" class="px-4 py-2 rounded-xl text-xs font-black uppercase tracking-wider bg-[#e94f1b] text-white">
                Nouveau convoi
            </a>
            <a href="{{ route('user.convoi.index') }}" class="px-4 py-2 rounded-xl text-xs font-black uppercase tracking-wider text-gray-600">
                Mes convois
            </a>
        </div>

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-2xl sm:text-3xl font-black text-[#1A1D1F] tracking-tight">
                    Réservation <span class="text-[#e94f1b]">Convoi</span>
                </h1>
                <p class="text-sm text-gray-500 font-medium">Étape 2/2 : renseignez les passagers du convoi.</p>
            </div>
            <div class="text-xs font-bold text-gray-500 bg-white border border-gray-100 rounded-xl px-4 py-2">
                {{ $compagnie->name }} - {{ $nombrePersonnes }} personne(s)
            </div>
        </div>
        @if (!empty($itineraire))
            <div class="text-xs font-bold text-gray-600 bg-white border border-gray-100 rounded-xl px-4 py-2 inline-flex">
                Itinéraire: {{ $itineraire->point_depart }} -> {{ $itineraire->point_arrive }}
            </div>
        @endif

        @if (session('error'))
            <div class="rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-red-700 font-semibold text-sm">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('user.convoi.store') }}" method="POST" class="space-y-6">
            @csrf

            <div class="bg-white rounded-[28px] border border-gray-100 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-gray-50 border-b border-gray-100">
                            <tr>
                                <th class="px-4 py-4 text-[10px] font-black uppercase tracking-wider text-gray-500">#</th>
                                <th class="px-4 py-4 text-[10px] font-black uppercase tracking-wider text-gray-500">Nom</th>
                                <th class="px-4 py-4 text-[10px] font-black uppercase tracking-wider text-gray-500">Prénoms</th>
                                <th class="px-4 py-4 text-[10px] font-black uppercase tracking-wider text-gray-500">Contact</th>
                                <th class="px-4 py-4 text-[10px] font-black uppercase tracking-wider text-gray-500">Email (optionnel)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @for ($i = 0; $i < $nombrePersonnes; $i++)
                                <tr>
                                    <td class="px-4 py-3 text-sm font-black text-gray-600">{{ $i + 1 }}</td>
                                    <td class="px-4 py-3">
                                        <input type="text" name="passagers[{{ $i }}][nom]"
                                            value="{{ old("passagers.$i.nom") }}"
                                            class="w-full px-3 py-2.5 bg-gray-50 border border-gray-100 rounded-xl focus:ring-2 focus:ring-[#e94f1b] focus:bg-white outline-none text-sm font-semibold"
                                            placeholder="Nom">
                                        @error("passagers.$i.nom")
                                            <p class="text-xs font-semibold text-red-600 mt-1">{{ $message }}</p>
                                        @enderror
                                    </td>
                                    <td class="px-4 py-3">
                                        <input type="text" name="passagers[{{ $i }}][prenoms]"
                                            value="{{ old("passagers.$i.prenoms") }}"
                                            class="w-full px-3 py-2.5 bg-gray-50 border border-gray-100 rounded-xl focus:ring-2 focus:ring-[#e94f1b] focus:bg-white outline-none text-sm font-semibold"
                                            placeholder="Prénoms">
                                        @error("passagers.$i.prenoms")
                                            <p class="text-xs font-semibold text-red-600 mt-1">{{ $message }}</p>
                                        @enderror
                                    </td>
                                    <td class="px-4 py-3">
                                        <input type="text" name="passagers[{{ $i }}][contact]"
                                            value="{{ old("passagers.$i.contact") }}"
                                            class="w-full px-3 py-2.5 bg-gray-50 border border-gray-100 rounded-xl focus:ring-2 focus:ring-[#e94f1b] focus:bg-white outline-none text-sm font-semibold"
                                            placeholder="Contact">
                                        @error("passagers.$i.contact")
                                            <p class="text-xs font-semibold text-red-600 mt-1">{{ $message }}</p>
                                        @enderror
                                    </td>
                                    <td class="px-4 py-3">
                                        <input type="email" name="passagers[{{ $i }}][email]"
                                            value="{{ old("passagers.$i.email") }}"
                                            class="w-full px-3 py-2.5 bg-gray-50 border border-gray-100 rounded-xl focus:ring-2 focus:ring-[#e94f1b] focus:bg-white outline-none text-sm font-semibold"
                                            placeholder="email@exemple.com">
                                        @error("passagers.$i.email")
                                            <p class="text-xs font-semibold text-red-600 mt-1">{{ $message }}</p>
                                        @enderror
                                    </td>
                                </tr>
                            @endfor
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-3">
                <a href="{{ route('user.convoi.create') }}"
                    class="inline-flex justify-center items-center gap-2 px-6 py-3.5 rounded-2xl bg-gray-100 text-gray-700 font-black text-xs uppercase tracking-widest hover:bg-gray-200 transition-all">
                    <i class="fas fa-arrow-left"></i>
                    Retour
                </a>
                <button type="submit"
                    class="inline-flex justify-center items-center gap-2 px-8 py-3.5 rounded-2xl bg-[#e94f1b] text-white font-black text-xs uppercase tracking-widest shadow-lg shadow-[#e94f1b]/20 hover:bg-[#d44518] transition-all">
                    Enregistrer le convoi
                    <i class="fas fa-check"></i>
                </button>
            </div>
        </form>
    </div>
@endsection

