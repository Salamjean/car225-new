@extends('admin.layouts.template')
@section('content')
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-blue-50 py-8 px-4">
        <div class=" mx-auto" style="width: 90%">
            <!-- En-tête -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 text-center sm:text-left">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Recharger les Compagnies</h1>
                    <p class="text-gray-600">Gérez le solde (crédit) de vos partenaires transporteurs</p>
                </div>
                <div class="mt-4 sm:mt-0">
                   <div class="inline-flex items-center px-4 py-2 bg-blue-100 text-blue-700 rounded-xl font-medium">
                       <i class="fas fa-wallet mr-2"></i>
                       Recharge Administrative
                   </div>
                </div>
            </div>

            <!-- Barre de recherche -->
            <div class="mb-6">
                <form method="GET" action="{{ route('compagnie.recharge.index') }}" class="relative max-w-md mx-auto sm:mx-0">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher une compagnie..."
                        class="w-full pl-12 pr-4 py-3 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-blue-500 focus:border-transparent shadow-sm transition-all duration-200">
                    <i class="fas fa-search text-gray-400 absolute left-4 top-1/2 -translate-y-1/2"></i>
                </form>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($compagnies as $compagnie)
                    <div class="bg-white rounded-3xl shadow-lg hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100 group">
                        <!-- Top part: Info -->
                        <div class="p-6 pb-4">
                            <div class="flex items-center mb-4">
                                <div class="flex-shrink-0 h-14 w-14 group-hover:scale-110 transition-transform duration-300">
                                    @if ($compagnie->path_logo)
                                        <img class="h-14 w-14 rounded-2xl object-cover border-2 border-gray-50"
                                            src="{{ asset('storage/' . $compagnie->path_logo) }}"
                                            alt="{{ $compagnie->name }}">
                                    @else
                                        <div class="h-14 w-14 rounded-2xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white font-bold text-xl shadow-lg">
                                            {{ substr($compagnie->name, 0, 2) }}
                                        </div>
                                    @endif
                                </div>
                                <div class="ml-4 overflow-hidden">
                                    <h3 class="text-lg font-bold text-gray-900 truncate">{{ $compagnie->name }}</h3>
                                    <span class="text-xs font-medium px-2 py-0.5 bg-gray-100 text-gray-600 rounded-full italic">{{ $compagnie->sigle ?? 'N/A' }}</span>
                                </div>
                            </div>

                            <!-- Balance -->
                            <div class="bg-blue-50 rounded-2xl p-4 flex justify-between items-center mb-6">
                                <span class="text-sm font-semibold text-blue-700">Solde Actuel</span>
                                <div class="text-right">
                                    <span class="text-xl font-black text-blue-800 tracking-tight">{{ number_format($compagnie->tickets ?? 0, 0, ',', ' ') }}</span>
                                    <span class="text-[10px] font-bold text-blue-500 uppercase block leading-none">FCFA</span>
                                </div>
                            </div>

                            <!-- Recharge Form -->
                            <form action="{{ route('compagnie.recharge.process', $compagnie) }}" method="POST" class="space-y-4">
                                @csrf
                                <div class="relative">
                                    <label class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-2 block pl-1">Montant à Recharger</label>
                                    <div class="relative">
                                        <input type="number" name="amount" required min="1" placeholder="Ex: 50000"
                                            class="w-full pl-4 pr-16 py-3 border-2 border-gray-100 rounded-xl focus:ring-0 focus:border-blue-500 transition-all duration-200 bg-gray-50 focus:bg-white text-lg font-bold">
                                        <div class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 font-bold">FCFA</div>
                                    </div>
                                </div>
                                <button type="submit" 
                                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-200 flex items-center justify-center gap-2">
                                    <i class="fas fa-plus-circle"></i>
                                    Confirmer la Recharge
                                </button>
                            </form>
                        </div>
                        
                        <!-- Footer part: Links -->
                        <div class="bg-gray-50 px-6 py-4 flex justify-between items-center border-t border-gray-100">
                            <span class="text-xs text-gray-400 font-medium">
                                <i class="fas fa-history mr-1"></i>
                                Historique disponible
                            </span>
                            <a href="{{ route('compagnie.show', $compagnie) }}" class="text-blue-500 hover:text-blue-700 text-xs font-bold transition-colors">
                                Voir Détails <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-20 bg-white rounded-3xl shadow-sm text-center">
                        <i class="fas fa-search text-5xl text-gray-200 mb-4"></i>
                        <h3 class="text-xl font-medium text-gray-500">Aucune compagnie ne correspond à votre recherche</h3>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $compagnies->appends(request()->query())->links() }}
            </div>
        </div>
    </div>

    <!-- Notifications -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        @if (Session::has('success'))
            Swal.fire({
                icon: 'success',
                title: 'Recharge effectuée',
                text: '{{ Session::get('success') }}',
                confirmButtonColor: '#2563eb',
                confirmButtonText: 'Parfait !',
                background: '#ffffff',
                customClass: {
                    popup: 'rounded-3xl'
                }
            });
        @endif

        @if (Session::has('error'))
            Swal.fire({
                icon: 'error',
                title: 'Oups !',
                text: '{{ Session::get('error') }}',
                confirmButtonColor: '#ef4444',
                confirmButtonText: 'Réessayer',
                customClass: {
                    popup: 'rounded-3xl'
                }
            });
        @endif

        // Validation simple
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function(e) {
                const amount = this.querySelector('input[name="amount"]').value;
                if (amount <= 0) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'warning',
                        title: 'Montant invalide',
                        text: 'Veuillez entrer un montant supérieur à 0.',
                        confirmButtonColor: '#2563eb'
                    });
                }
            });
        });
    </script>
@endsection
