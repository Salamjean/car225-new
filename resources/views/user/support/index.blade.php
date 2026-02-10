@extends('user.layouts.template')

@section('content')
<div class="min-h-screen bg-[#F8F9FA] py-8 px-4 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-[1200px]">
        
        <!-- Header -->
        <div class="mb-10">
            <h1 class="text-3xl font-black text-[#1A1D1F] tracking-tight">Support Client</h1>
            <p class="text-gray-500 font-medium">Comment pouvons-nous vous aider aujourd'hui ?</p>
        </div>

        @if(session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-6 py-4 rounded-2xl flex items-center gap-3">
                <i class="fas fa-check-circle"></i>
                <span class="font-bold text-sm">{{ session('success') }}</span>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Category: Lost Bag -->
            <a href="{{ route('user.support.create', ['type' => 'bagage_perdu']) }}" class="bg-white p-8 rounded-[32px] border border-gray-100 shadow-sm hover:shadow-xl transition-all group">
                <div class="w-14 h-14 bg-orange-50 rounded-2xl flex items-center justify-center text-[#e94f1b] mb-6 group-hover:bg-[#e94f1b] group-hover:text-white transition-all">
                    <i class="fas fa-suitcase-rolling text-xl"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">Bagage Perdu</h3>
                <p class="text-xs text-gray-500 leading-relaxed">Vous n'avez pas retrouvé votre bagage à l'arrivée ? Signalez-le nous immédiatement.</p>
                <div class="mt-6 flex items-center text-[10px] font-black text-[#e94f1b] uppercase tracking-widest">
                    Déclarer <i class="fas fa-arrow-right ml-2 opacity-0 group-hover:opacity-100 group-hover:translate-x-1 transition-all"></i>
                </div>
            </a>

            <!-- Category: Forgot Object -->
            <a href="{{ route('user.support.create', ['type' => 'objet_oublie']) }}" class="bg-white p-8 rounded-[32px] border border-gray-100 shadow-sm hover:shadow-xl transition-all group">
                <div class="w-14 h-14 bg-blue-50 rounded-2xl flex items-center justify-center text-blue-500 mb-6 group-hover:bg-blue-500 group-hover:text-white transition-all">
                    <i class="fas fa-glasses text-xl"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">Objet Oublié</h3>
                <p class="text-xs text-gray-500 leading-relaxed">Vous avez oublié un téléphone, des clés ou un vêtement dans le bus ? Nous allons vérifier.</p>
                <div class="mt-6 flex items-center text-[10px] font-black text-blue-500 uppercase tracking-widest">
                    Déclarer <i class="fas fa-arrow-right ml-2 opacity-0 group-hover:opacity-100 group-hover:translate-x-1 transition-all"></i>
                </div>
            </a>

            <!-- Category: Refund -->
            <a href="{{ route('user.support.create', ['type' => 'remboursement']) }}" class="bg-white p-8 rounded-[32px] border border-gray-100 shadow-sm hover:shadow-xl transition-all group">
                <div class="w-14 h-14 bg-green-50 rounded-2xl flex items-center justify-center text-green-500 mb-6 group-hover:bg-green-500 group-hover:text-white transition-all">
                    <i class="fas fa-hand-holding-usd text-xl"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">Remboursement</h3>
                <p class="text-xs text-gray-500 leading-relaxed">Une erreur de paiement ou un voyage annulé ? Demandez un remboursement sur votre solde.</p>
                <div class="mt-6 flex items-center text-[10px] font-black text-green-500 uppercase tracking-widest">
                    Demander <i class="fas fa-arrow-right ml-2 opacity-0 group-hover:opacity-100 group-hover:translate-x-1 transition-all"></i>
                </div>
            </a>

            <!-- Category: Quality -->
            <a href="{{ route('user.support.create', ['type' => 'qualite']) }}" class="bg-white p-8 rounded-[32px] border border-gray-100 shadow-sm hover:shadow-xl transition-all group">
                <div class="w-14 h-14 bg-purple-50 rounded-2xl flex items-center justify-center text-purple-500 mb-6 group-hover:bg-purple-500 group-hover:text-white transition-all">
                    <i class="fas fa-star text-xl"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">Qualité de Service</h3>
                <p class="text-xs text-gray-500 leading-relaxed">Un problème avec le chauffeur, l'hotesse ou le confort du véhicule ? Dites-le nous.</p>
                <div class="mt-6 flex items-center text-[10px] font-black text-purple-500 uppercase tracking-widest">
                    Signaler <i class="fas fa-arrow-right ml-2 opacity-0 group-hover:opacity-100 group-hover:translate-x-1 transition-all"></i>
                </div>
            </a>

            <!-- Category: Account Help -->
            <a href="{{ route('user.support.create', ['type' => 'compte']) }}" class="bg-white p-8 rounded-[32px] border border-gray-100 shadow-sm hover:shadow-xl transition-all group">
                <div class="w-14 h-14 bg-gray-50 rounded-2xl flex items-center justify-center text-gray-500 mb-6 group-hover:bg-gray-800 group-hover:text-white transition-all">
                    <i class="fas fa-user-cog text-xl"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">Mon Compte</h3>
                <p class="text-xs text-gray-500 leading-relaxed">Problème d'accès, modification de profil ou erreur de solde portefeuille.</p>
                <div class="mt-6 flex items-center text-[10px] font-black text-gray-500 uppercase tracking-widest">
                    Aide <i class="fas fa-arrow-right ml-2 opacity-0 group-hover:opacity-100 group-hover:translate-x-1 transition-all"></i>
                </div>
            </a>

            <!-- Category: Other -->
            <a href="{{ route('user.support.create', ['type' => 'autre']) }}" class="bg-white p-8 rounded-[32px] border border-gray-100 shadow-sm hover:shadow-xl transition-all group">
                <div class="w-14 h-14 bg-red-50 rounded-2xl flex items-center justify-center text-red-500 mb-6 group-hover:bg-red-500 group-hover:text-white transition-all">
                    <i class="fas fa-question text-xl"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">Autre demande</h3>
                <p class="text-xs text-gray-500 leading-relaxed">Pour toute autre question ou suggestion non listée ci-dessus.</p>
                <div class="mt-6 flex items-center text-[10px] font-black text-red-500 uppercase tracking-widest">
                    Contacter <i class="fas fa-arrow-right ml-2 opacity-0 group-hover:opacity-100 group-hover:translate-x-1 transition-all"></i>
                </div>
            </a>
        </div>
    </div>
</div>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@400;700;900&display=swap');
    body {
        font-family: 'Outfit', sans-serif;
    }
</style>
@endsection
