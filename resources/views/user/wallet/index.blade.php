@extends('user.layouts.template')

@section('content')
<div class="min-h-screen bg-gray-50 py-8 px-4 sm:px-6 lg:px-8">
    <div class="mx-auto" style="width: 95%;">

        <!-- Header -->
        <div class="mb-10 flex flex-col md:flex-row md:items-center md:justify-between gap-6">
            <div class="flex items-center gap-5">
                <div class="w-16 h-16 bg-gradient-to-tr from-orange-600 to-orange-400 rounded-2xl shadow-lg flex items-center justify-center text-white text-2xl font-black">
                    <i class="fas fa-wallet"></i>
                </div>
                <div>
                    <h1 class="text-3xl font-black text-gray-900 tracking-tight">CarPAY</h1>
                    <p class="text-gray-500 font-medium">Gérez votre solde et vos transactions</p>
                </div>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('user.dashboard') }}" class="px-6 py-3 bg-white text-gray-900 border border-gray-200 rounded-xl shadow-sm hover:bg-gray-50 transition-all font-bold flex items-center gap-2">
                    <i class="fas fa-arrow-left"></i> Retour au tableau de bord
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
            <!-- Formulaire de rechargement -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-3xl p-8 shadow-xl border border-gray-100 h-full">
                    <h3 class="text-xl font-black text-gray-900 mb-6 flex items-center gap-2">
                        <i class="fas fa-plus-circle text-green-600"></i> Recharger mon compte
                    </h3>

                    <div class="bg-green-50 rounded-2xl p-6 mb-6 border border-green-100">
                        <form id="rechargeForm" class="space-y-4">
                            @csrf
                            <div>
                                <label for="amount" class="block text-sm font-bold text-gray-700 mb-2">Montant à recharger (FCFA)</label>
                                <div class="relative">
                                    <input type="number" id="amount" name="amount" min="100" step="100" placeholder="Ex: 5000" required
                                        class="w-full px-5 py-4 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all font-bold text-lg">
                                    <div class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 font-bold">FCFA</div>
                                </div>
                                <p class="text-xs text-gray-500 mt-2 font-medium">Minimum: 100 FCFA</p>
                            </div>

                            <button type="submit" id="btnRecharge" class="w-full py-4 bg-green-600 text-white rounded-xl shadow-lg hover:bg-green-700 transition-all font-bold text-lg flex items-center justify-center gap-2">
                                <span id="btnText">Recharger maintenant</span>
                                <i class="fas fa-bolt"></i>
                            </button>
                        </form>
                    </div>

                    <div class="flex items-center gap-4 text-sm text-gray-500 font-medium bg-gray-50 p-4 rounded-xl border border-gray-100">
                        <i class="fas fa-shield-alt text-xl text-gray-400"></i>
                        <p>Paiement sécurisé via CinetPay (Orange Money, MTN Money, Wave, etc.)</p>
                    </div>
                </div>
            </div>

            <!-- Carte Solde -->
            <div>
                <div class="bg-gradient-to-br from-gray-900 to-gray-800 rounded-3xl p-8 shadow-2xl text-white relative overflow-hidden h-full flex flex-col justify-center">
                    <div class="absolute top-0 right-0 p-4 opacity-10">
                        <i class="fas fa-coins text-9xl text-white"></i>
                    </div>
                    
                    <p class="text-gray-400 text-sm font-bold uppercase tracking-widest mb-2">Solde Actuel</p>
                    <h2 class="text-5xl font-black mb-6 tracking-tight">
                        {{ number_format($user->solde, 0, ',', ' ') }} <span class="text-2xl text-gray-400">FCFA</span>
                    </h2>

                    <div class="mt-auto pt-6 border-t border-gray-700">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-gray-700 flex items-center justify-center">
                                <i class="fas fa-user text-gray-300"></i>
                            </div>
                            <div>
                                <p class="font-bold">{{ $user->name }} {{ $user->prenom }}</p>
                                <p class="text-xs text-gray-400">{{ $user->email }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Historique -->
        <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-50 bg-gray-50 flex justify-between items-center">
                <h3 class="text-lg font-black text-gray-900">Historique des transactions</h3>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-black text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-4 text-left text-xs font-black text-gray-500 uppercase tracking-wider">Référence</th>
                            <th class="px-6 py-4 text-left text-xs font-black text-gray-500 uppercase tracking-wider">Description</th>
                            <th class="px-6 py-4 text-left text-xs font-black text-gray-500 uppercase tracking-wider">Montant</th>
                            <th class="px-6 py-4 text-left text-xs font-black text-gray-500 uppercase tracking-wider">Statut</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($transactions as $transaction)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-medium">
                                {{ $transaction->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-bold">
                                {{ $transaction->reference }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                {{ $transaction->description }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-black {{ $transaction->type == 'credit' ? 'text-green-600' : 'text-red-600' }}">
                                {{ $transaction->type == 'credit' ? '+' : '-' }} {{ number_format($transaction->amount, 0, ',', ' ') }} FCFA
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($transaction->status == 'completed')
                                    <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-bold">Validé</span>
                                @elseif($transaction->status == 'pending')
                                    <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-bold">En attente</span>
                                @else
                                    <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-xs font-bold">Échoué</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-400">
                                <i class="fas fa-inbox text-4xl mb-3"></i>
                                <p>Aucune transaction pour le moment</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($transactions->hasPages())
            <div class="p-4 border-t border-gray-50">
                {{ $transactions->links() }}
            </div>
            @endif
        </div>

    </div>
</div>

{{-- Script CinetPay --}}
<script src="https://cdn.cinetpay.com/seamless/main.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // Variable globale pour stocker l'ID
    window.lastTransactionId = null;

    document.getElementById('rechargeForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const btn = document.getElementById('btnRecharge');
        const btnText = document.getElementById('btnText');
        const amount = document.getElementById('amount').value;
        const csrfToken = document.querySelector('input[name="_token"]').value;

        // Loading state
        btn.disabled = true;
        btnText.innerText = 'Initialisation...';
        btn.classList.add('opacity-75');

        try {
            // 1. Initialiser le paiement côté serveur
            const response = await fetch("{{ route('user.wallet.recharge') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ amount: amount })
            });

            const data = await response.json();

            if (!response.ok) throw new Error(data.message || 'Erreur lors de l\'initialisation');

            // Stocker l'ID immédiatement
            window.lastTransactionId = data.checkout_data.transaction_id;

            // 2. Ouvrir le popup CinetPay
            CinetPay.setConfig(data.cinetpay_config);
            CinetPay.getCheckout(data.checkout_data);

            // --- C'EST ICI LA CORRECTION IMPORTANTE ---
            CinetPay.waitResponse(function(data) {
                console.log('CinetPay Response:', data);
                
                if (data.status === "ACCEPTED") {
                    // Le paiement est bon, on lance la vérification TOUT DE SUITE
                    // On n'attend pas la fermeture du modal
                    Swal.fire({
                        title: 'Paiement en cours de validation',
                        text: 'Veuillez patienter...',
                        icon: 'info',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    
                    // Appel de la vérification serveur
                    checkStatus(window.lastTransactionId);
                } 
                else if (data.status === "REFUSED") {
                    Swal.fire('Erreur', 'Le paiement a été refusé.', 'error');
                    resetBtn();
                }
            });

            CinetPay.onError(function(data) {
                console.error('CinetPay Error:', data);
                Swal.fire('Erreur', 'Une erreur est survenue lors du paiement.', 'error');
                resetBtn();
            });

            // On garde onClose comme sécurité au cas où l'utilisateur ferme la fenêtre sans payer
            CinetPay.onClose(function(data) {
                console.log('Modal fermé');
                resetBtn();
            });

        } catch (error) {
            console.error(error);
            Swal.fire('Erreur', error.message, 'error');
            resetBtn();
        }
    });

    async function checkStatus(transactionId) {
        if(!transactionId) return;

        try {
            const csrfToken = document.querySelector('input[name="_token"]').value;
            const response = await fetch("{{ route('user.wallet.verify') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ transaction_id: transactionId })
            });
            const result = await response.json();

            if (result.status === 'success') {
                Swal.fire({
                    title: 'Succès !',
                    text: 'Votre solde a été mis à jour.',
                    icon: 'success',
                    timer: 3000
                }).then(() => {
                    window.location.reload(); // Rechargement de la page
                });
            } else if (result.status === 'pending') {
                 // Si c'est encore en attente, on peut réessayer ou informer l'utilisateur
                 Swal.fire('En attente', 'Votre paiement est en cours de traitement par l\'opérateur. Votre solde sera mis à jour automatiquement.', 'info')
                 .then(() => window.location.reload());
            } else {
                 Swal.fire('Echec', 'La vérification du paiement a échoué.', 'error');
                 resetBtn();
            }
        } catch(e) {
            console.error(e);
            resetBtn();
        }
    }

    function resetBtn() {
        const btn = document.getElementById('btnRecharge');
        const btnText = document.getElementById('btnText');
        btn.disabled = false;
        btnText.innerText = 'Recharger maintenant';
        btn.classList.remove('opacity-75');
    }
</script>
@endsection
