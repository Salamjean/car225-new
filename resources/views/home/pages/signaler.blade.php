@extends('home.layouts.template')

@section('content')

<!-- Hero section with modern gradients and animations -->
<section class="relative pt-32 pb-20 overflow-hidden bg-slate-50">
    <div class="container mx-auto px-4">
        <div class="max-w-3xl mx-auto text-center mb-16" data-aos="fade-up">
            <h1 class="text-4xl md:text-5xl font-black text-slate-900 mb-6">
                Signaler un <span class="text-emerald-600">problème</span>
            </h1>
            <p class="text-lg text-slate-600 leading-relaxed">
                Rencontrez-vous un problème? Nous sommes ici pour vous aider<br class="hidden md:block"> rapidement et efficacement.
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Form Column -->
            <div class="lg:col-span-2" data-aos="fade-right">
                <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/50 p-8 md:p-10 border border-slate-100">
                    <h3 class="text-2xl font-bold text-slate-800 mb-8 flex align-items-center gap-3">
                        <div class="w-10 h-10 bg-emerald-100 rounded-xl flex items-center justify-center">
                            <i class="bi bi-pencil-square text-emerald-600"></i>
                        </div>
                        Décrivez votre problème
                    </h3>

                    @if(session('success'))
                        <div class="mb-6 p-4 rounded-2xl bg-emerald-50 text-emerald-700 border border-emerald-200">
                            <div class="flex items-center gap-3">
                                <i class="bi bi-check-circle-fill text-xl"></i>
                                <span class="font-medium">{{ session('success') }}</span>
                            </div>
                        </div>
                    @endif

                    <form action="{{ route('home.signaler.store') }}" method="POST" class="space-y-6">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-sm font-semibold text-slate-700">Numéro de billet (optionne)</label>
                                <input type="text" name="billet" placeholder="Ex: BCI-2025-001234" 
                                    class="w-full px-5 py-4 bg-slate-50 border border-slate-100 rounded-2xl focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all text-slate-600">
                            </div>
                            <div class="space-y-2">
                                <label class="text-sm font-semibold text-slate-700">Type de problème *</label>
                                <select name="type" class="w-full px-5 py-4 bg-slate-50 border border-slate-100 rounded-2xl focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all text-slate-600 appearance-none bg-[url('data:image/svg+xml;charset=utf-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20fill%3D%22none%22%20viewBox%3D%220%200%2020%2020%22%3E%3Cpath%20stroke%3D%22%236b7280%22%20stroke-linecap%3D%22round%22%20stroke-linejoin%3D%22round%22%20stroke-width%3D%221.5%22%20d%3D%22m6%208%204%204%204-4%22%2F%3E%3C%2Fsvg%3E')] bg-[length:24px] bg-[right_1rem_center] bg-no-repeat" required>
                                    <option value="" disabled selected>Sélectionnez un type</option>
                                    <option value="retard">Car en retard</option>
                                    <option value="annule">Car annulé</option>
                                    <option value="paiement">Problème de paiement</option>
                                    <option value="billet">Problème avec mon billet</option>
                                    <option value="autre">Autre</option>
                                </select>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-slate-700">Description détaillée *</label>
                            <textarea name="description" rows="4" placeholder="Décrivez en détail ce qui s'est passé..." 
                                class="w-full px-5 py-4 bg-slate-50 border border-slate-100 rounded-2xl focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all text-slate-600 resize-none" required></textarea>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-sm font-semibold text-slate-700">Email *</label>
                                <input type="email" name="email" placeholder="Votre email" 
                                    class="w-full px-5 py-4 bg-slate-50 border border-slate-100 rounded-2xl focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all text-slate-600" required>
                            </div>
                            <div class="space-y-2">
                                <label class="text-sm font-semibold text-slate-700">Téléphone</label>
                                <input type="tel" name="telephone" placeholder="Votre téléphone" minlength="10" maxlength="10"
                                    class="w-full px-5 py-4 bg-slate-50 border border-slate-100 rounded-2xl focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all text-slate-600">
                            </div>
                        </div>

                        <button type="submit" class="w-full bg-[#f15a24] hover:bg-[#d84e1b] text-white font-black py-4 px-8 rounded-2xl shadow-lg shadow-orange-200 transition-all transform hover:-translate-y-1 active:scale-95 text-lg uppercase tracking-wider">
                            Soumettre le signalement
                        </button>
                    </form>
                </div>
            </div>

            <!-- Side Column -->
            <div class="space-y-8" data-aos="fade-left">
                <!-- Contact Rapide -->
                <div class="bg-amber-50 rounded-3xl p-8 border border-amber-100">
                    <h4 class="text-xl font-bold text-slate-800 mb-6">Contact rapide</h4>
                    <div class="space-y-4">
                        <a href="#" class="flex items-center gap-4 bg-white p-4 rounded-2xl border border-white hover:border-emerald-200 hover:shadow-md transition-all group">
                            <div class="w-12 h-12 bg-emerald-50 rounded-xl flex items-center justify-center group-hover:bg-emerald-100 transition-colors">
                                <i class="bi bi-whatsapp text-emerald-600 text-xl"></i>
                            </div>
                            <div>
                                <div class="text-sm font-bold text-slate-800">WhatsApp</div>
                                <div class="text-xs text-slate-500">Réponse en quelques minutes</div>
                            </div>
                        </a>
                        <a href="tel:+22501020304" class="flex items-center gap-4 bg-white p-4 rounded-2xl border border-white hover:border-orange-200 hover:shadow-md transition-all group">
                            <div class="w-12 h-12 bg-orange-50 rounded-xl flex items-center justify-center group-hover:bg-orange-100 transition-colors">
                                <i class="bi bi-telephone text-orange-600 text-xl"></i>
                            </div>
                            <div>
                                <div class="text-sm font-bold text-slate-800">Téléphone</div>
                                <div class="text-xs text-slate-500">+225 01 02 03 04 (24/24)</div>
                            </div>
                        </a>
                        <a href="mailto:contact@car225.com" class="flex items-center gap-4 bg-white p-4 rounded-2xl border border-white hover:border-blue-200 hover:shadow-md transition-all group">
                            <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center group-hover:bg-blue-100 transition-colors">
                                <i class="bi bi-envelope text-blue-600 text-xl"></i>
                            </div>
                            <div>
                                <div class="text-sm font-bold text-slate-800">Email</div>
                                <div class="text-xs text-slate-500">contact@car225.com</div>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- Problèmes courants -->
                <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/50 p-8 border border-slate-100">
                    <h4 class="text-xl font-bold text-slate-800 mb-6 flex items-center gap-2">
                        <i class="bi bi-exclamation-circle text-orange-500"></i>
                        Problèmes courants
                    </h4>
                    <div class="space-y-4">
                        <div class="p-4 bg-orange-50 rounded-2xl border border-orange-100">
                            <div class="text-sm font-bold text-slate-800 mb-1">Car en retard</div>
                            <div class="text-xs text-slate-600 italic">Solution : Nous vous offrirons un crédit de 50% sur votre prochain trajet</div>
                        </div>
                        <div class="p-4 bg-orange-50 rounded-2xl border border-orange-100">
                            <div class="text-sm font-bold text-slate-800 mb-1">Car annulé</div>
                            <div class="text-xs text-slate-600 italic">Solution : Remboursement complet ou changement de trajet sans frais</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Solutions section -->
<section class="py-20 bg-white">
    <div class="container mx-auto px-4">
        <h2 class="text-3xl font-black text-slate-900 text-center mb-16" data-aos="fade-up">
            Solutions aux problèmes courants
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Card 1 -->
            <div class="bg-white border-2 border-slate-100 rounded-3xl p-6 hover:shadow-xl transition-all group" data-aos="fade-up" data-aos-delay="100">
                <div class="w-12 h-12 bg-orange-100 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                    <i class="bi bi-exclamation-triangle-fill text-orange-500"></i>
                </div>
                <h4 class="text-lg font-bold text-slate-900 mb-2">Car en retard</h4>
                <p class="text-sm text-slate-500 mb-6">Votre bus n'a pas quitté l'arrêt à l'heure prévue</p>
                <div class="p-4 bg-emerald-50 rounded-2xl border border-emerald-100">
                    <div class="text-xs font-bold text-emerald-800 mb-1">Solution</div>
                    <div class="text-xs text-emerald-700">Nous vous offrirons un crédit 50 % sur votre prochain trajet</div>
                </div>
            </div>

            <!-- Card 2 -->
            <div class="bg-white border-2 border-slate-100 rounded-3xl p-6 hover:shadow-xl transition-all group" data-aos="fade-up" data-aos-delay="200">
                <div class="w-12 h-12 bg-red-100 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                    <i class="bi bi-x-circle-fill text-red-500"></i>
                </div>
                <h4 class="text-lg font-bold text-slate-900 mb-2">Car annulé</h4>
                <p class="text-sm text-slate-500 mb-6">Votre trajet a été annulé par la compagnie</p>
                <div class="p-4 bg-emerald-50 rounded-2xl border border-emerald-100">
                    <div class="text-xs font-bold text-emerald-800 mb-1">Solution</div>
                    <div class="text-xs text-emerald-700">Remboursement complet ou changement de trajet sans frais</div>
                </div>
            </div>

            <!-- Card 3 -->
            <div class="bg-white border-2 border-slate-100 rounded-3xl p-6 hover:shadow-xl transition-all group" data-aos="fade-up" data-aos-delay="300">
                <div class="w-12 h-12 bg-blue-100 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                    <i class="bi bi-credit-card-fill text-blue-500"></i>
                </div>
                <h4 class="text-lg font-bold text-slate-900 mb-2">Problème de paiement</h4>
                <p class="text-sm text-slate-500 mb-6">vous avez rencontré une erreur lors du paiement</p>
                <div class="p-4 bg-emerald-50 rounded-2xl border border-emerald-100">
                    <div class="text-xs font-bold text-emerald-800 mb-1">Solution</div>
                    <div class="text-xs text-emerald-700">Nous travaillons avec votre opérateur pour résoudre le problème</div>
                </div>
            </div>

            <!-- Card 4 -->
            <div class="bg-white border-2 border-slate-100 rounded-3xl p-6 hover:shadow-xl transition-all group" data-aos="fade-up" data-aos-delay="400">
                <div class="w-12 h-12 bg-indigo-100 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                    <i class="bi bi-ticket-perforated-fill text-indigo-500"></i>
                </div>
                <h4 class="text-lg font-bold text-slate-900 mb-2">Problème avec mon billet</h4>
                <p class="text-sm text-slate-500 mb-6">Votre billet n'a pas été reçu ou est invalide</p>
                <div class="p-4 bg-emerald-50 rounded-2xl border border-emerald-100">
                    <div class="text-xs font-bold text-emerald-800 mb-1">Solution</div>
                    <div class="text-xs text-emerald-700">Nous enverrons un nouveau billet immédiatement</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Process section -->
<section class="py-20 bg-slate-50 overflow-hidden">
    <div class="container mx-auto px-4">
        <h2 class="text-3xl font-black text-slate-900 text-center mb-20" data-aos="fade-up">
            Comment nous traitons votre signalement
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 relative">
            <!-- Connector Line (desktop) -->
            <div class="hidden lg:block absolute top-12 left-0 w-full h-0.5 bg-slate-200 -z-10"></div>

            <!-- Step 1 -->
            <div class="text-center group" data-aos="fade-up" data-aos-delay="100">
                <div class="w-20 h-20 bg-[#f15a24] text-white rounded-full flex items-center justify-center mx-auto mb-8 shadow-xl shadow-orange-200 relative group-hover:scale-110 transition-transform">
                    <span class="text-2xl font-black">1</span>
                    <div class="absolute -top-2 -right-2 w-8 h-8 bg-white rounded-full flex items-center justify-center shadow-md">
                        <i class="bi bi-box-arrow-in-down text-orange-500"></i>
                    </div>
                </div>
                <h4 class="text-xl font-bold text-slate-900 mb-2">Réception</h4>
                <p class="text-slate-500">Votre signalement est reçu</p>
            </div>

            <!-- Step 2 -->
            <div class="text-center group" data-aos="fade-up" data-aos-delay="200">
                <div class="w-20 h-20 bg-[#f15a24] text-white rounded-full flex items-center justify-center mx-auto mb-8 shadow-xl shadow-orange-200 relative group-hover:scale-110 transition-transform">
                    <span class="text-2xl font-black">2</span>
                    <div class="absolute -top-2 -right-2 w-8 h-8 bg-white rounded-full flex items-center justify-center shadow-md">
                        <i class="bi bi-search text-orange-500"></i>
                    </div>
                </div>
                <h4 class="text-xl font-bold text-slate-900 mb-2">Vérification</h4>
                <p class="text-slate-500">Notre équipe vérifie les détails</p>
            </div>

            <!-- Step 3 -->
            <div class="text-center group" data-aos="fade-up" data-aos-delay="300">
                <div class="w-20 h-20 bg-[#f15a24] text-white rounded-full flex items-center justify-center mx-auto mb-8 shadow-xl shadow-orange-200 relative group-hover:scale-110 transition-transform">
                    <span class="text-2xl font-black">3</span>
                    <div class="absolute -top-2 -right-2 w-8 h-8 bg-white rounded-full flex items-center justify-center shadow-md">
                        <i class="bi bi-check2-circle text-orange-500"></i>
                    </div>
                </div>
                <h4 class="text-xl font-bold text-slate-900 mb-2">Résolution</h4>
                <p class="text-slate-500">Nous trouvons une solution</p>
            </div>

            <!-- Step 4 -->
            <div class="text-center group" data-aos="fade-up" data-aos-delay="400">
                <div class="w-20 h-20 bg-[#f15a24] text-white rounded-full flex items-center justify-center mx-auto mb-8 shadow-xl shadow-orange-200 relative group-hover:scale-110 transition-transform">
                    <span class="text-2xl font-black">4</span>
                    <div class="absolute -top-2 -right-2 w-8 h-8 bg-white rounded-full flex items-center justify-center shadow-md">
                        <i class="bi bi-bell text-orange-500"></i>
                    </div>
                </div>
                <h4 class="text-xl font-bold text-slate-900 mb-2">Suivi</h4>
                <p class="text-slate-500">Vous êtes informé du résultat</p>
            </div>
        </div>
    </div>
</section>

@endsection

@push('styles')
<style>
    /* Custom spacing for fixed header */
    body {
        padding-top: 0 !important;
    }
    .main-wrapper {
        padding-top: 70px;
    }
    
    /* Animation for the connector dots if needed */
    @media (min-width: 1024px) {
        .lg\:col-span-2 {
            position: relative;
        }
    }
</style>
@endpush
