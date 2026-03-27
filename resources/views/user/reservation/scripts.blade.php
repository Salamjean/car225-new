<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Passenger Details Popup
        $('.view-passenger-details-btn').on('click', function() {
            const data = $(this).data();
            Swal.fire({
                title: '<span class="text-xl font-black uppercase tracking-tight">Détails du Passager</span>',
                html: `
                    <div class="text-left space-y-4 py-4">
                        <div class="flex items-center gap-4 bg-gray-50 p-4 rounded-2xl">
                            <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center shadow-sm text-[#e94f1b]">
                                <i class="fas fa-user text-xl"></i>
                            </div>
                            <div>
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none mb-1">Nom Complet</p>
                                <p class="text-lg font-black text-gray-900 leading-none">${data.nom} ${data.prenom}</p>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 gap-4">
                            <div class="bg-gray-50 p-4 rounded-2xl">
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Téléphone</p>
                                <p class="font-black text-gray-900">${data.telephone}</p>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-2xl">
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Email</p>
                                <p class="font-black text-gray-900">${data.email}</p>
                            </div>
                            <div class="bg-red-50 p-4 rounded-2xl border border-red-100">
                                <p class="text-[10px] font-black text-red-400 uppercase tracking-widest mb-1">Contact d'urgence</p>
                                <p class="font-black text-red-600">${data.urgence}</p>
                            </div>
                        </div>
                    </div>
                `,
                showConfirmButton: true,
                confirmButtonText: 'Fermer',
                confirmButtonColor: '#1A1D1F',
                width: '450px',
                padding: '2rem',
                customClass: {
                    container: 'font-outfit',
                    popup: 'rounded-[32px]',
                    confirmButton: 'rounded-xl px-8 py-3 font-black uppercase tracking-widest text-xs'
                }
            });
        });

        // =========================================
        // CANCELLATION LOGIC
        // =========================================
        $(document).off('click', '.cancel-btn:not([disabled])').on('click', '.cancel-btn:not([disabled])', function() {
            const reservationId = $(this).data('id');
            const reference = $(this).data('reference');

            Swal.fire({
                title: '<span class="text-lg font-black uppercase tracking-tight">Annulation de réservation</span>',
                html: '<div class="flex items-center justify-center py-8"><div class="animate-spin w-8 h-8 border-4 border-[#e94f1b] border-t-transparent rounded-full"></div></div><p class="text-sm text-gray-500">Calcul du remboursement...</p>',
                showConfirmButton: false,
                allowOutsideClick: false,
                width: '480px',
                customClass: { popup: 'rounded-[32px]' }
            });

            $.get(`/user/booking/reservations/${reservationId}/refund-preview`)
                .done(function(data) {
                    if (!data.can_cancel) {
                        Swal.fire({
                            icon: 'error',
                            title: '<span class="text-lg font-black uppercase tracking-tight text-red-600">Action impossible</span>',
                            html: '<p class="text-sm text-gray-600">L\'annulation est impossible moins de 15 minutes avant le départ.</p>',
                            confirmButtonText: 'Compris',
                            confirmButtonColor: '#1A1D1F',
                            customClass: { popup: 'rounded-[32px]', confirmButton: 'rounded-xl px-8 py-3 font-black uppercase tracking-widest text-xs' }
                        });
                        return;
                    }

                    let color = '#22c55e';
                    if (data.percentage <= 20) color = '#ef4444';
                    else if (data.percentage <= 40) color = '#f97316';
                    else if (data.percentage <= 70) color = '#eab308';

                    const title = data.is_round_trip 
                        ? '<span class="text-lg font-black uppercase tracking-tight">Confirmer l\'annulation aller-retour</span>'
                        : '<span class="text-lg font-black uppercase tracking-tight">Confirmer l\'annulation</span>';

                    let referencesHtml = '';
                    if (data.is_round_trip) {
                        referencesHtml = `
                            <div class="bg-orange-50 p-4 rounded-2xl border border-orange-100">
                                <p class="text-[10px] font-black text-orange-600 uppercase tracking-widest mb-2"><i class="fas fa-info-circle mr-1"></i> Aller-Retour</p>
                                <p class="text-xs text-gray-700 mb-2">Les deux billets seront annulés :</p>
                                <div class="flex gap-2">
                                    <span class="text-xs font-bold text-[#e94f1b] bg-white px-3 py-1 rounded-lg">${data.reference}</span>
                                    <span class="text-xs font-bold text-[#e94f1b] bg-white px-3 py-1 rounded-lg">${data.paired_reference}</span>
                                </div>
                            </div>
                        `;
                    } else {
                        referencesHtml = `
                            <div class="bg-gray-50 p-4 rounded-2xl">
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Réservation</p>
                                <p class="text-sm font-black text-[#e94f1b]">${data.reference}</p>
                            </div>
                        `;
                    }

                    Swal.fire({
                        title: title,
                        html: `
                            <div class="text-left space-y-4 py-4">
                                ${referencesHtml}
                                <div class="bg-gray-50 p-4 rounded-2xl">
                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Délai avant départ</p>
                                    <p class="text-sm font-bold text-gray-900">${data.time_remaining}</p>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="p-4 rounded-2xl border-2" style="border-color: ${color}; background: ${color}10">
                                        <p class="text-[10px] font-black uppercase tracking-widest mb-1" style="color: ${color}">Remboursement</p>
                                        <p class="text-2xl font-black" style="color: ${color}">
                                            ${data.percentage !== null ? data.percentage + '%' : Number(data.refund_amount).toLocaleString('fr-FR')}
                                        </p>
                                        <p class="text-xs font-bold text-gray-500">${data.percentage !== null ? Number(data.refund_amount).toLocaleString('fr-FR') + ' FCFA' : 'Montant final'}</p>
                                    </div>
                                    <div class="bg-red-50 p-4 rounded-2xl border-2 border-red-200">
                                        <p class="text-[10px] font-black text-red-400 uppercase tracking-widest mb-1">Frais / Pénalité</p>
                                        <p class="text-2xl font-black text-red-500">
                                            ${data.percentage !== null ? (100 - data.percentage) + '%' : '-' + Number(data.penalty || (data.montant_original - data.refund_amount)).toLocaleString('fr-FR')}
                                        </p>
                                        <p class="text-xs font-bold text-gray-500">${data.percentage !== null ? Number(data.montant_original - data.refund_amount).toLocaleString('fr-FR') + ' FCFA' : 'Déduit'}</p>
                                    </div>
                                </div>
                                <div class="bg-blue-50 p-4 rounded-2xl border border-blue-100">
                                    <p class="text-[10px] font-black text-blue-400 uppercase tracking-widest mb-1">Montant crédité sur votre Wallet</p>
                                    <p class="text-xl font-black text-blue-600">${Number(data.refund_amount).toLocaleString('fr-FR')} FCFA</p>
                                    ${data.is_round_trip ? '<p class="text-xs text-gray-500 mt-1">Remboursement total pour les deux billets</p>' : ''}
                                </div>
                            </div>
                        `,
                        showCancelButton: true,
                        confirmButtonText: '<i class="fas fa-check mr-2"></i> Confirmer l\'annulation',
                        cancelButtonText: 'Annuler',
                        confirmButtonColor: '#ef4444',
                        cancelButtonColor: '#6b7280',
                        width: '520px',
                        padding: '2rem',
                        customClass: {
                            popup: 'rounded-[32px]',
                            confirmButton: 'rounded-xl px-6 py-3 font-black uppercase tracking-widest text-xs',
                            cancelButton: 'rounded-xl px-6 py-3 font-black uppercase tracking-widest text-xs'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            Swal.fire({
                                title: '<span class="text-lg font-black uppercase tracking-tight">Annulation en cours...</span>',
                                html: '<div class="flex items-center justify-center py-8"><div class="animate-spin w-8 h-8 border-4 border-red-500 border-t-transparent rounded-full"></div></div>',
                                showConfirmButton: false,
                                allowOutsideClick: false,
                                customClass: { popup: 'rounded-[32px]' }
                            });

                            $.ajax({
                                url: `/user/booking/reservations/${reservationId}/cancel`,
                                method: 'POST',
                                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                                data: { reason: 'Annulé par l\'utilisateur' },
                                success: function(result) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: '<span class="text-lg font-black uppercase tracking-tight text-green-600">Annulation réussie !</span>',
                                        html: `<p class="text-sm text-gray-600 mb-2">${result.message}</p>`,
                                        confirmButtonText: 'OK',
                                        confirmButtonColor: '#22c55e',
                                        customClass: { popup: 'rounded-[32px]', confirmButton: 'rounded-xl px-8 py-3 font-black uppercase tracking-widest text-xs' }
                                    }).then(() => window.location.reload());
                                },
                                error: function(xhr) {
                                    const error = xhr.responseJSON;
                                    Swal.fire({
                                        icon: 'error',
                                        title: '<span class="text-lg font-black uppercase tracking-tight text-red-600">Erreur</span>',
                                        html: `<p class="text-sm text-gray-600">${error?.message || 'Une erreur est survenue.'}</p>`,
                                        confirmButtonText: 'OK',
                                        confirmButtonColor: '#1A1D1F',
                                        customClass: { popup: 'rounded-[32px]', confirmButton: 'rounded-xl px-8 py-3 font-black uppercase tracking-widest text-xs' }
                                    });
                                }
                            });
                        }
                    });
                })
                .fail(function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erreur',
                        text: 'Impossible de récupérer les détails du remboursement.',
                        confirmButtonColor: '#1A1D1F',
                        customClass: { popup: 'rounded-[32px]' }
                    });
                });
        });

        // =========================================
        // MODIFICATION LOGIC
        // =========================================
        let modifState = {
            resId: null,
            residualValue: 0,
            userSolde: 0,
            isRoundTrip: false,
            current: {
                progId: null,
                date: null,
                time: null,
                seat: null,
                retProgId: null,
                retDate: null,
                retTime: null,
                retSeat: null 
            }
        };

        $(document).off('click', '.modify-btn:not([disabled])').on('click', '.modify-btn:not([disabled])', async function() {
            const resId = $(this).data('id');
            modifState.resId = resId;

            Swal.fire({
                title: 'Chargement...',
                html: '<div class="flex flex-col items-center"><div class="animate-spin w-8 h-8 border-4 border-[#e94f1b] border-t-transparent rounded-full mb-2"></div><span class="text-sm text-gray-500">Récupération des données...</span></div>',
                showConfirmButton: false,
                allowOutsideClick: false,
                width: '450px',
                customClass: { popup: 'rounded-[32px]' }
            });

            try {
                const response = await $.get(`/user/booking/reservations/${resId}/modification-data`);
                if(!response.success) throw new Error(response.message);

                modifState.residualValue = response.residual_value;
                modifState.userSolde = response.user_solde;
                modifState.isRoundTrip = response.is_aller_retour;

                modifState.current.progId = response.reservation.programme_id;
                modifState.current.date = response.formatted_date_aller;
                const heureDepart = response.reservation.heure_depart || (response.reservation.programme && response.reservation.programme.heure_depart) || '00:00:00';
                
                console.log("=== DEBUG: MODIFICATION DATA ===");
                console.log("Full Response:", response);
                console.log("Original reservation.heure_depart:", response.reservation.heure_depart);
                console.log("Programme heure_depart:", response.reservation.programme ? response.reservation.programme.heure_depart : null);
                console.log("Final computed heureDepart:", heureDepart);

                modifState.current.time = heureDepart.substring(0, 5); 
                modifState.current.seat = response.reservation.seat_number;

                if(modifState.isRoundTrip && response.return_details) {
                    modifState.current.retProgId = response.return_details.prog_id;
                    modifState.current.retDate = response.return_details.date;
                    modifState.current.retTime = response.return_details.time ? response.return_details.time.substring(0, 5) : null;
                    modifState.current.retSeat = response.return_details.seat;
                }

                let routeOptions = '';
                response.available_routes.forEach(route => {
                    const isSelected = route.unique_key === response.current_route_key ? 'selected' : '';
                    routeOptions += `<option value="${route.id}" ${isSelected} 
                        data-depart="${route.depart}" 
                        data-arrive="${route.arrive}" 
                        data-compagnie="${route.compagnie_id}"
                        data-prix="${route.prix}">
                        ${route.name} - ${route.compagnie}
                    </option>`;
                });

                let returnHtml = '';
                if(modifState.isRoundTrip) {
                    returnHtml = `
                        <div class="mt-4 pt-4 border-t border-gray-100">
                            <div class="flex items-center gap-2 mb-3">
                                <span class="px-2 py-1 bg-orange-100 text-orange-600 text-[10px] font-black rounded uppercase">Voyage Retour</span>
                                <p class="text-xs font-bold text-gray-700">Informations de retour</p>
                            </div>
                            <div class="grid grid-cols-2 gap-4 mb-3">
                                <div>
                                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1">Date Retour</label>
                                    <input type="date" id="mod-ret-date" onkeydown="return false" class="w-full p-3 bg-white border border-gray-200 rounded-xl text-sm font-bold outline-none focus:ring-2 focus:ring-orange-500" min="${new Date().toISOString().split('T')[0]}">
                                </div>
                                <div>
                                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1">Heure Retour</label>
                                    <select id="mod-ret-time" class="w-full p-3 bg-white border border-gray-200 rounded-xl text-sm font-bold outline-none focus:ring-2 focus:ring-orange-500">
                                        <option value="">Chargement...</option>
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1">Place Retour</label>
                                <div id="mod-ret-seat-container" class="mt-1 p-3 bg-gray-50 border border-gray-100 rounded-xl min-h-[50px] flex items-center justify-center text-xs text-gray-400">
                                    En attente...
                                </div>
                                <input type="hidden" id="mod-ret-seat-input">
                            </div>
                        </div>
                    `;
                }

                const modalHtml = `
                    <div class="text-left font-outfit space-y-5">
                        <div class="bg-blue-50 p-4 rounded-2xl border border-blue-100">
                            <div class="flex justify-between items-center mb-2">
                                <div>
                                    <p class="text-[10px] font-black text-blue-400 uppercase tracking-widest leading-none mb-1">Ancien Billet</p>
                                    <p class="text-sm font-black text-gray-700 leading-none">${Number(response.total_old_price).toLocaleString()} FCFA</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-[10px] font-black text-red-400 uppercase tracking-widest leading-none mb-1">Moins Pénalité</p>
                                    <p class="text-sm font-black text-red-500 leading-none">- ${Number(response.total_old_price - response.residual_value).toLocaleString()} FCFA</p>
                                </div>
                            </div>
                            <div class="pt-2 border-t border-blue-200/50 flex justify-between items-center">
                                <span class="text-[10px] font-black text-blue-600 uppercase tracking-widest">Valeur à deduire</span>
                                <span class="text-lg font-black text-blue-700">${Number(response.residual_value).toLocaleString()} FCFA</span>
                            </div>
                            <p class="text-[9px] text-blue-400 mt-1 font-medium"><i class="fas fa-info-circle mr-1"></i> ${response.penalty_info}</p>
                        </div>
                        <div class="bg-gray-50 p-3 rounded-xl border border-gray-100">
                            <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1">Trajet</label>
                            <select id="mod-route" ${modifState.isRoundTrip ? 'disabled style="background-color: #f3f4f6; cursor: not-allowed;"' : ''} class="w-full p-2 bg-white border border-gray-200 rounded-lg text-sm font-bold outline-none focus:ring-2 focus:ring-[#e94f1b]">
                                ${routeOptions}
                            </select>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Voyage Aller</p>
                            <div class="grid grid-cols-2 gap-4 mb-3">
                                <div>
                                    <label class="text-[9px] font-bold text-gray-400 uppercase block mb-1">Date</label>
                                    <input type="date" id="mod-date" onkeydown="return false" class="w-full p-3 bg-white border border-gray-200 rounded-xl text-sm font-bold outline-none focus:ring-2 focus:ring-[#e94f1b]" min="${new Date().toISOString().split('T')[0]}">
                                </div>
                                <div>
                                    <label class="text-[9px] font-bold text-gray-400 uppercase block mb-1">Heure</label>
                                    <select id="mod-time" class="w-full p-3 bg-white border border-gray-200 rounded-xl text-sm font-bold outline-none focus:ring-2 focus:ring-[#e94f1b]">
                                        <option value="">Chargement...</option>
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label class="text-[9px] font-bold text-gray-400 uppercase block mb-1">Place</label>
                                <div id="mod-seat-container" class="mt-1 p-3 bg-gray-50 border border-gray-100 rounded-xl min-h-[50px] flex items-center justify-center text-xs text-gray-400">
                                    En attente...
                                </div>
                                <input type="hidden" id="mod-seat-input">
                            </div>
                        </div>
                        ${returnHtml}
                        <div id="delta-box" class="hidden bg-gray-900 text-white p-5 rounded-[24px] mt-6 shadow-xl shadow-gray-200">
                            <div class="space-y-3">
                                <div class="flex justify-between items-center opacity-60">
                                    <span class="text-[10px] font-black uppercase tracking-widest">Nouveau prix total</span>
                                    <span id="new-total-display" class="text-sm font-black">0 FCFA</span>
                                </div>
                                <div class="flex justify-between items-center opacity-60">
                                    <span class="text-[10px] font-black uppercase tracking-widest">Avoir (Après pénalité)</span>
                                    <span id="residual-display" class="text-sm font-black">0 FCFA</span>
                                </div>
                                <div class="pt-3 border-t border-white/10 flex justify-between items-center">
                                    <span id="delta-label" class="text-xs font-black uppercase tracking-widest text-[#e94f1b]">Total à payer</span>
                                    <span id="delta-amount" class="text-2xl font-black text-white">0 FCFA</span>
                                </div>
                            </div>
                            <p id="wallet-error" class="text-[10px] font-bold text-red-400 mt-2 bg-red-400/10 p-2 rounded-lg text-center hidden"><i class="fas fa-exclamation-triangle mr-1"></i> Solde insuffisant sur votre portefeuille</p>
                        </div>
                    </div>
                `;

                Swal.fire({
                    title: '<span class="text-xl font-black uppercase tracking-tight">Modifier Réservation</span>',
                    html: modalHtml,
                    showCancelButton: true,
                    confirmButtonText: 'Confirmer',
                    cancelButtonText: 'Annuler',
                    confirmButtonColor: '#1A1D1F',
                    cancelButtonColor: '#f3f4f6',
                    width: '600px',
                    padding: '2rem',
                    customClass: { popup: 'rounded-[32px]', confirmButton: 'rounded-xl px-8 py-3', cancelButton: 'rounded-xl px-8 py-3 text-gray-800' },
                    didOpen: async () => {
                        initEvents();
                        setTimeout(async () => {
                            if(modifState.current.date) {
                                $('#mod-date').val(modifState.current.date);
                                await preloadAllerData();
                            }
                            if(modifState.isRoundTrip && modifState.current.retDate) {
                                $('#mod-ret-date').val(modifState.current.retDate);
                                await preloadRetourData();
                            }
                        }, 50);
                    },
                    preConfirm: handleModificationSubmit
                }).then((result) => {
                    if (result.isConfirmed && result.value && result.value.success) {
                        Swal.fire({
                            icon: 'success',
                            title: '<span class="text-lg font-black uppercase tracking-tight text-green-600">Modification réussie !</span>',
                            text: result.value.message || 'Votre réservation a été mise à jour.',
                            confirmButtonText: 'Excellent',
                            confirmButtonColor: '#22c55e',
                            customClass: { popup: 'rounded-[32px]', confirmButton: 'rounded-xl px-8 py-3 font-black uppercase tracking-widest text-xs' }
                        }).then(() => window.location.reload());
                    }
                });

            } catch (error) {
                Swal.fire('Erreur', error.message, 'error');
            }
        });

        async function preloadAllerData() {
            const date = $('#mod-date').val();
            await loadTimes('aller', date, modifState.current.time);
            const time = $('#mod-time').val();
            if(time) {
                const progId = $('#mod-time option:selected').data('prog-id');
                await loadSeats('aller', progId, date, time, modifState.current.seat);
            }
        }

        async function preloadRetourData() {
            const date = $('#mod-ret-date').val();
            await loadTimes('retour', date, modifState.current.retTime);
            const time = $('#mod-ret-time').val();
            if(time) {
                const progId = $('#mod-ret-time option:selected').data('prog-id');
                await loadSeats('retour', progId, date, time, modifState.current.retSeat); 
            }
        }

        async function loadTimes(type, date, preSelectedTime = null) {
            const routeOption = $('#mod-route option:selected');
            let depart = type === 'retour' ? routeOption.data('arrive') : routeOption.data('depart');
            let arrive = type === 'retour' ? routeOption.data('depart') : routeOption.data('arrive');
            const compagnie = routeOption.data('compagnie');
            const selector = type === 'retour' ? '#mod-ret-time' : '#mod-time';

            $(selector).html('<option value="">Chargement...</option>').prop('disabled', true);

            try {
                const res = await $.get('/user/booking/api/route-schedules', {
                    point_depart: depart,
                    point_arrive: arrive,
                    compagnie_id: compagnie,
                    date: date
                });
                
                if(res.success && res.schedules.length > 0) {
                    let opts = '<option value="">-- Choisir Heure --</option>';
                    res.schedules.forEach(sch => {
                        const heureDepart = sch.heure_depart || '00:00:00';
                        if (!sch.heure_depart) {
                            console.warn("=== DEBUG: SCHEDULE HAS NULL HEURE_DEPART ===", sch);
                        }
                        const schDisplay = heureDepart.substring(0, 5);
                        const schTime = heureDepart;
                        const preTime = preSelectedTime ? preSelectedTime.substring(0, 5) : '';
                        const isSelected = (preTime && schDisplay === preTime) ? 'selected' : '';
                        opts += `<option value="${schTime}" ${isSelected} data-prog-id="${sch.id}" data-prix="${sch.montant_billet}">${schDisplay}</option>`;
                    });
                    $(selector).html(opts).prop('disabled', false);
                } else {
                    $(selector).html('<option value="">Aucun départ</option>');
                }
            } catch(e) {
                $(selector).html('<option>Erreur</option>');
            }
        }

        async function loadSeats(type, progId, date, time, preSelectedSeat = null) {
            const container = type === 'retour' ? '#mod-ret-seat-container' : '#mod-seat-container';
            const input = type === 'retour' ? '#mod-ret-seat-input' : '#mod-seat-input';

            $(container).html('<div class="flex justify-center p-2"><div class="animate-spin w-4 h-4 border-2 border-gray-400 border-t-transparent rounded-full"></div></div>');

            try {
                const res = await $.get(`/user/booking/programmes/${progId}/seats`, {
                    date: date,
                    heure: time
                });
                
                if(res.success) {
                    let html = '<div class="grid grid-cols-7 gap-2">';
                    res.seats.forEach(seat => {
                        const isMine = (preSelectedSeat && seat.number == preSelectedSeat);
                        let css = 'bg-gray-100 text-gray-300 cursor-not-allowed';
                        let action = '';

                        if (seat.available) {
                            css = 'bg-white border border-gray-200 text-gray-700 hover:border-[#e94f1b] hover:text-[#e94f1b] cursor-pointer';
                            action = `onclick="selectSeat('${type}', this, ${seat.number})"`;
                        } else if (isMine) {
                            css = 'bg-[#e94f1b] text-white border border-[#e94f1b] cursor-pointer shadow-md transform scale-105';
                            action = `onclick="selectSeat('${type}', this, ${seat.number})"`;
                            $(input).val(seat.number);
                        }

                        html += `<div ${action} class="seat-item-${type} h-8 rounded-lg flex items-center justify-center text-xs font-bold transition-all ${css}">${seat.number}</div>`;
                    });
                    html += '</div>';
                    $(container).html(html);
                    if($(input).val()) calculateTotal();
                }
            } catch(e) {
                $(container).text('Erreur chargement');
            }
        }

        function initEvents() {
            $('#mod-route').change(function() {
                $('#mod-time, #mod-ret-time').html('<option value="">Date requise</option>').prop('disabled', true);
                $('#mod-seat-container, #mod-ret-seat-container').html('<span class="text-xs text-gray-400">Sélectionnez une heure</span>');
                $('#mod-seat-input, #mod-ret-seat-input').val('');
                $('#delta-box').addClass('hidden');
                if($('#mod-date').val()) loadTimes('aller', $('#mod-date').val());
                if(modifState.isRoundTrip && $('#mod-ret-date').val()) loadTimes('retour', $('#mod-ret-date').val());
            });

            $('#mod-date').change(function() { 
                const val = $(this).val();
                loadTimes('aller', val); 
                if(modifState.isRoundTrip) {
                    $('#mod-ret-date').attr('min', val);
                    if($('#mod-ret-date').val() < val) {
                        $('#mod-ret-date').val(val);
                        loadTimes('retour', val);
                    }
                }
            });
            $('#mod-ret-date').change(function() { loadTimes('retour', $(this).val()); });

            $('#mod-time').change(function() {
                const progId = $(this).find(':selected').data('prog-id');
                if(progId) loadSeats('aller', progId, $('#mod-date').val(), $(this).val());
            });

            $('#mod-ret-time').change(function() {
                const progId = $(this).find(':selected').data('prog-id');
                if(progId) loadSeats('retour', progId, $('#mod-ret-date').val(), $(this).val());
            });
        }

        window.selectSeat = function(type, el, number) {
            $(`.seat-item-${type}`).removeClass('bg-[#e94f1b] text-white border-[#e94f1b]').addClass('bg-white text-gray-700');
            $(el).removeClass('bg-white text-gray-700').addClass('bg-[#e94f1b] text-white border-[#e94f1b]');
            $(type === 'retour' ? '#mod-ret-seat-input' : '#mod-seat-input').val(number);
            calculateTotal();
        };

        async function calculateTotal() {
            const progIdAller = $('#mod-time option:selected').data('prog-id');
            const seatAller = $('#mod-seat-input').val();
            if(!progIdAller || !seatAller) return;
            
            let data = {
                new_programme_id: progIdAller,
                new_date_aller: $('#mod-date').val(),
                _token: $('meta[name="csrf-token"]').attr('content')
            };

            if(modifState.isRoundTrip) {
                const progIdRetour = $('#mod-ret-time option:selected').data('prog-id');
                const seatRet = $('#mod-ret-seat-input').val();
                if(!progIdRetour || !seatRet) return;
                data.new_return_programme_id = progIdRetour;
                data.new_return_date = $('#mod-ret-date').val();
            }

            try {
                const res = await $.post(`/user/booking/reservations/${modifState.resId}/calculate-delta`, data);
                $('#delta-box').removeClass('hidden opacity-50');
                $('#new-total-display').text(Number(res.new_total).toLocaleString() + ' FCFA');
                $('#residual-display').text(Number(res.residual_value).toLocaleString() + ' FCFA');
                $('#delta-amount').text(Number(res.delta).toLocaleString() + ' FCFA');
                
                const btn = Swal.getConfirmButton();
                if(res.action === 'pay') {
                    $('#delta-label').text('Reste à payer');
                    $('#delta-amount').removeClass('text-green-400').addClass('text-red-400');
                    if(!res.can_afford) { $('#wallet-error').removeClass('hidden'); btn.disabled = true; } 
                    else { $('#wallet-error').addClass('hidden'); btn.disabled = false; }
                } else if(res.action === 'refund') {
                    $('#delta-label').text('Crédit à rembourser');
                    $('#delta-amount').removeClass('text-red-400').addClass('text-green-400');
                    btn.disabled = false;
                }
            } catch(e) {}
        }

        async function handleModificationSubmit() {
            const seatAller = $('#mod-seat-input').val();
            const progIdAller = $('#mod-time option:selected').data('prog-id');
            if(!seatAller || !progIdAller) {
                Swal.showValidationMessage('Veuillez sélectionner le voyage aller complet');
                return false;
            }

            let payload = {
                programme_id: progIdAller,
                date_voyage: $('#mod-date').val(),
                heure_depart: $('#mod-time').val(),
                seat_number: seatAller,
                _token: $('meta[name="csrf-token"]').attr('content')
            };

            if(modifState.isRoundTrip) {
                payload.return_programme_id = $('#mod-ret-time option:selected').data('prog-id');
                payload.return_date_voyage = $('#mod-ret-date').val();
                payload.return_heure_depart = $('#mod-ret-time').val();
                payload.return_seat_number = $('#mod-ret-seat-input').val();
            }

            try {
                const result = await $.post(`/user/booking/reservations/${modifState.resId}/modify`, payload);
                if (!result.success) {
                    Swal.showValidationMessage(result.message || 'Erreur lors de la modification.');
                    return false;
                }
                return result;
            } catch (error) {
                Swal.showValidationMessage(error.responseJSON?.message || 'Erreur technique');
                return false;
            }
        }

        function updateTimers() {
            document.querySelectorAll('.timer-display[data-arrival]').forEach(timer => {
                const arrivalTime = new Date(timer.dataset.arrival).getTime();
                const now = new Date().getTime();
                const distance = arrivalTime - now;
                if (distance < 0) {
                    timer.innerHTML = "Arrivée imminente";
                    timer.classList.replace('text-blue-600', 'text-emerald-600');
                    return;
                }
                const h = Math.floor(distance / 3600000);
                const m = Math.floor((distance % 3600000) / 60000);
                const s = Math.floor((distance % 60000) / 1000);
                timer.innerHTML = `${h.toString().padStart(2,'0')}:${m.toString().padStart(2,'0')}:${s.toString().padStart(2,'0')}`;
            });
        }
        if (document.querySelectorAll('.timer-display[data-arrival]').length > 0) {
            updateTimers();
            setInterval(updateTimers, 1000);
        }
    });
</script>
