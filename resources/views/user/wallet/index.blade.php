@extends('user.layouts.template')

@section('content')
<div class="bg-gray-50 py-8 px-4 sm:px-6 lg:px-8" style="margin-top:-20px">
    <div class="mx-auto" style="width: 95%;">

        <!-- Header -->
        <div class="mb-10 flex flex-col md:flex-row md:items-center md:justify-between gap-6">
            <div class="flex items-center gap-5">
                <div class="w-16 h-16 bg-white rounded-2xl shadow-lg flex items-center justify-center p-2">
                    <img src="{{ asset('assetsPoster/assets/images/logo_car225.png') }}" alt="CAR225" class="w-full h-full object-contain">
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

                            <!-- Affichage de la commission -->
                            <div id="commissionBox" class="bg-white/50 border border-green-100 rounded-xl p-4 hidden">
                                <div class="flex justify-between items-center text-sm mb-1">
                                    <span class="text-gray-500 font-bold">Rechargement :</span>
                                    <span id="displayAmount" class="font-black">0 FCFA</span>
                                </div>
                                <div class="flex justify-between items-center text-sm mb-2 text-blue-600">
                                    <span class="font-bold">Frais de service (2%) :</span>
                                    <span id="displayCommission" class="font-black">+ 0 FCFA</span>
                                </div>
                                <div class="border-t border-green-100 pt-2 flex justify-between items-center">
                                    <span class="text-gray-900 font-black">Total à payer :</span>
                                    <span id="displayTotal" class="text-lg font-black text-green-700">0 FCFA</span>
                                </div>
                            </div>

                            <button type="submit" id="btnRecharge" class="w-full py-4 bg-green-600 text-white rounded-xl shadow-lg hover:bg-green-700 transition-all font-bold text-lg flex items-center justify-center gap-2">
                                <span id="btnText">Recharger maintenant</span>
                                <i class="fas fa-bolt"></i>
                            </button>
                        </form>
                    </div>

                    <div class="flex items-center gap-4 text-sm text-gray-500 font-medium bg-gray-50 p-4 rounded-xl border border-gray-100">
                        <i class="fas fa-shield-alt text-xl text-gray-400"></i>
                        <p>Paiement sécurisé via Wave</p>
                    </div>
                </div>
            </div>

            <!-- Carte Solde -->
            <div>
                <div class="relative bg-gray-900 rounded-3xl p-8 shadow-2xl text-white overflow-hidden h-full flex flex-col justify-center border border-white/5">
                    <!-- Background Illustration -->
                    <div class="absolute inset-0 z-0 opacity-20">
                         <img src="{{ asset('assetsPoster/assets/images/logo_car225.png') }}" alt="" class="w-full h-full object-cover scale-150 rotate-12 blur-[2px]">
                    </div>
                    <!-- Overlay Gradient -->
                    <div class="absolute inset-0 bg-gradient-to-br from-gray-900 via-transparent to-[#e94f1b]/20 z-10"></div>
                    
                    <div class="relative z-20">
                        <p class="text-gray-400 text-[10px] font-black uppercase tracking-[0.2em] mb-2 opacity-70">Solde Actuel</p>
                        <h2 class="text-5xl font-black mb-6 tracking-tight flex items-baseline gap-2">
                            {{ number_format($user->solde, 0, ',', ' ') }} <span class="text-xl font-bold text-[#e94f1b]">FCFA</span>
                        </h2>

                        <!-- Bouton Retirer -->
                        <button onclick="openWithdrawModal()" class="w-full py-3 bg-[#e94f1b] text-white rounded-xl shadow-lg hover:bg-orange-700 transition-all font-bold flex items-center justify-center gap-2">
                            <i class="fas fa-money-bill-wave"></i>
                            Retirer votre argent
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== HISTORIQUE ===== --}}
        <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">

            {{-- Header du bloc avec bouton "Voir tout" --}}
            <div class="px-6 py-5 border-b border-gray-100 flex flex-row items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 bg-gray-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-list text-gray-500 text-sm"></i>
                    </div>
                    <h3 class="text-lg font-black text-gray-900">Historique des transactions</h3>
                    <span class="text-xs font-bold text-gray-400 bg-gray-100 px-2.5 py-1 rounded-full">5 dernières</span>
                </div>
                <a href="{{ route('user.wallet.recharges') }}"
                   style="display:inline-flex; align-items:center; gap:8px; background:#e94f1b; color:white; padding:8px 16px; border-radius:12px; font-weight:700; font-size:13px; text-decoration:none;">
                    <i class="fas fa-history"></i>
                    Voir tout
                    <i class="fas fa-arrow-right"></i>
                </a>
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
                            <td class="px-6 py-4 text-sm text-gray-700">
                                {{ Str::limit($transaction->description, 50) }}
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
                                <i class="fas fa-inbox text-4xl mb-3 block"></i>
                                <p>Aucune transaction pour le moment</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- MODAL DE RETRAIT -->
<div id="withdrawModal" class="fixed inset-0 z-50 hidden">
    <!-- Overlay -->
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeWithdrawModal()"></div>
    
    <!-- Contenu du modal -->
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md relative overflow-hidden transform transition-all" id="withdrawModalContent">
            <!-- Header du modal -->
            <div class="bg-gradient-to-r from-[#e94f1b] to-orange-600 px-8 py-6 text-white">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                            <i class="fas fa-money-bill-wave text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-black">Retirer de l'argent</h3>
                            <p class="text-white/80 text-sm">Vers votre mobile money</p>
                        </div>
                    </div>
                    <button onclick="closeWithdrawModal()" class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center hover:bg-white/30 transition-all">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>
            </div>

            <!-- Solde disponible -->
            <div class="px-8 pt-6">
                <div class="bg-gray-50 rounded-xl p-4 border border-gray-100 flex items-center justify-between">
                    <span class="text-sm text-gray-500 font-bold">Solde disponible</span>
                    <span class="text-lg font-black text-gray-900">{{ number_format($user->solde, 0, ',', ' ') }} FCFA</span>
                </div>
            </div>
            
            <!-- Formulaire -->
            <form id="withdrawForm" class="px-8 py-6 space-y-5">
                @csrf
                <div>
                    <label for="withdraw_amount" class="block text-sm font-bold text-gray-700 mb-2">Montant à retirer (FCFA)</label>
                    <div class="relative">
                        <input type="number" id="withdraw_amount" name="amount" min="100" step="100" placeholder="Ex: 5000" required
                            class="w-full px-5 py-4 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#e94f1b] focus:border-[#e94f1b] focus:bg-white transition-all font-bold text-lg">
                        <div class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 font-bold">FCFA</div>
                    </div>
                </div>

                <div>
                    <label for="withdraw_network" class="block text-sm font-bold text-gray-700 mb-2">Méthode de paiement</label>
                    <select id="withdraw_network" name="network" required
                        class="w-full px-5 py-4 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#e94f1b] focus:border-[#e94f1b] focus:bg-white transition-all font-bold text-lg">
                        <option value="" disabled selected>Choisir un réseau</option>
                        <option value="Orange">🟠 Orange Money</option>
                        <option value="MTN">🟡 MTN Mobile Money</option>
                        <option value="Wave">🔵 Wave</option>
                    </select>
                </div>

                <div>
                    <label for="withdraw_phone" class="block text-sm font-bold text-gray-700 mb-2">Numéro de téléphone</label>
                    <input type="tel" id="withdraw_phone" name="phone" placeholder="Ex: 0707070707" required
                        class="w-full px-5 py-4 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#e94f1b] focus:border-[#e94f1b] focus:bg-white transition-all font-bold text-lg">
                </div>

                <button type="submit" id="btnWithdraw" class="w-full py-4 bg-[#e94f1b] text-white rounded-xl shadow-lg hover:bg-orange-700 transition-all font-bold text-lg flex items-center justify-center gap-2">
                    <span id="btnWithdrawText">Confirmer le retrait</span>
                    <i class="fas fa-paper-plane"></i>
                </button>

                <div class="flex items-center gap-3 text-xs text-gray-400 font-medium">
                    <i class="fas fa-shield-alt"></i>
                    <p>Transfert sécurisé. Vous recevrez l'argent instantanément.</p>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- SweetAlert2 CDN --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // ============ CALCUL COMMISSION TEMPS RÉEL ============
    const amountInput = document.getElementById('amount');
    const commissionBox = document.getElementById('commissionBox');
    const displayAmount = document.getElementById('displayAmount');
    const displayCommission = document.getElementById('displayCommission');
    const displayTotal = document.getElementById('displayTotal');

    amountInput.addEventListener('input', function() {
        const amount = parseFloat(this.value);
        if (amount >= 100) {
            const commission = Math.round(amount * 0.02);
            const total = amount + commission;

            displayAmount.innerText = amount.toLocaleString('fr-FR') + ' FCFA';
            displayCommission.innerText = '+ ' + commission.toLocaleString('fr-FR') + ' FCFA';
            displayTotal.innerText = total.toLocaleString('fr-FR') + ' FCFA';
            commissionBox.classList.remove('hidden');
        } else {
            commissionBox.classList.add('hidden');
        }
    });

    // ============ MODAL RETRAIT ============
    function openWithdrawModal() {
        const modal = document.getElementById('withdrawModal');
        const content = document.getElementById('withdrawModalContent');
        modal.classList.remove('hidden');
        setTimeout(() => {
            content.classList.add('scale-100', 'opacity-100');
            content.classList.remove('scale-95', 'opacity-0');
        }, 10);
        document.body.style.overflow = 'hidden';
    }

    function closeWithdrawModal() {
        const modal = document.getElementById('withdrawModal');
        const content = document.getElementById('withdrawModalContent');
        content.classList.add('scale-95', 'opacity-0');
        content.classList.remove('scale-100', 'opacity-100');
        setTimeout(() => {
            modal.classList.add('hidden');
            document.body.style.overflow = '';
        }, 200);
    }

    // ============ RECHARGE (Nouvelle API v1 - Redirection) ============
    document.getElementById('rechargeForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const btn = document.getElementById('btnRecharge');
        const btnText = document.getElementById('btnText');
        const amount = document.getElementById('amount').value;
        const csrfToken = document.querySelector('input[name="_token"]').value;

        if (!amount || amount < 100) {
            Swal.fire('Erreur', 'Le montant minimum est de 100 FCFA', 'warning');
            return;
        }

        btn.disabled = true;
        btnText.innerText = 'Initialisation du paiement...';
        btn.classList.add('opacity-75');

        try {
            const response = await fetch("{{ route('user.wallet.recharge') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ amount: amount })
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || 'Erreur lors de l\'initialisation du paiement');
            }

            // Redirection vers la page de paiement Wave
            if (data.payment_url) {
                Swal.fire({
                    title: 'Redirection vers Wave',
                    html: 'Vous allez être redirigé vers la page de paiement sécurisée...',
                    icon: 'info',
                    timer: 2000,
                    timerProgressBar: true,
                    showConfirmButton: false,
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                }).then(() => {
                    // Rediriger vers la page de paiement Wave
                    window.location.href = data.payment_url;
                });
            } else {
                // Si pas de payment_url mais statut OK (ex: paiement direct)
                if (data.details && data.details.status === 'SUCCESS') {
                    Swal.fire({
                        title: 'Succès !',
                        text: 'Votre paiement a été effectué avec succès.',
                        icon: 'success',
                        timer: 3000
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    throw new Error('URL de paiement non disponible. Veuillez réessayer.');
                }
            }

        } catch (error) {
            console.error('Recharge Error:', error);
            Swal.fire('Erreur', error.message, 'error');
            resetBtn();
        }
    });

    function resetBtn() {
        const btn = document.getElementById('btnRecharge');
        const btnText = document.getElementById('btnText');
        btn.disabled = false;
        btnText.innerText = 'Recharger maintenant';
        btn.classList.remove('opacity-75');
    }

    // ============ RETRAIT ============
    document.getElementById('withdrawForm').addEventListener('submit', async function(e) {
        e.preventDefault();

        const btn = document.getElementById('btnWithdraw');
        const btnText = document.getElementById('btnWithdrawText');
        const amount = document.getElementById('withdraw_amount').value;
        const phone = document.getElementById('withdraw_phone').value;
        const network = document.getElementById('withdraw_network').value;
        const csrfToken = document.querySelector('input[name="_token"]').value;

        if(!amount || !phone || !network) {
            Swal.fire('Erreur', 'Veuillez remplir tous les champs', 'warning');
            return;
        }

        btn.disabled = true;
        btnText.innerText = 'Traitement en cours...';
        btn.classList.add('opacity-75');

        try {
            const response = await fetch("{{ route('user.wallet.withdraw') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ 
                    amount: amount,
                    phone: phone,
                    network: network
                })
            });

            const result = await response.json();

            if (response.ok && result.success) {
                closeWithdrawModal();
                Swal.fire({
                    title: 'Succès !',
                    text: result.message,
                    icon: 'success',
                    timer: 3000
                }).then(() => {
                    window.location.reload();
                });
            } else {
                throw new Error(result.message || 'Erreur lors du retrait');
            }
        } catch (error) {
            console.error(error);
            Swal.fire('Erreur', error.message, 'error');
        } finally {
            btn.disabled = false;
            btnText.innerText = 'Confirmer le retrait';
            btn.classList.remove('opacity-75');
        }
    });

    // ============ MESSAGES FLASH (après retour de CinetPay) ============
    @if(session('success'))
        Swal.fire({
            title: 'Succès !',
            text: "{{ session('success') }}",
            icon: 'success',
            timer: 5000,
            timerProgressBar: true,
        });
    @endif

    @if(session('error'))
        Swal.fire({
            title: 'Erreur',
            text: "{{ session('error') }}",
            icon: 'error',
        });
    @endif
</script>
@endsection
