<!-- Modal Détails -->
<style>
    #modalDetails.hidden {
        display: none !important;
    }

    #modalDetails {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        z-index: 9999;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 10px;
    }

    .modal-overlay {
        position: absolute;
        inset: 0;
        background: rgba(0, 0, 0, 0.6);
        backdrop-filter: blur(4px);
    }

    .modal-container {
        position: relative;
        background: #fff;
        width: 100%;
        max-width: 650px;
        border-radius: 28px;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        overflow: hidden;
        max-height: 95vh;
        display: flex;
        flex-direction: column;
        animation: modalFadeIn 0.3s ease-out;
    }

    @keyframes modalFadeIn {
        from {
            opacity: 0;
            transform: scale(0.95);
        }

        to {
            opacity: 1;
            transform: scale(1);
        }
    }

    .modal-header-premium {
        background: linear-gradient(135deg, #f97316 0%, #fb923c 100%);
        padding: 24px 30px;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .modal-body-premium {
        padding: 30px;
        overflow-y: auto;
        background: #f9fafb;
    }

    .info-card-p {
        background: #fff;
        padding: 18px;
        border-radius: 20px;
        border: 1px solid #f1f5f9;
        margin-bottom: 20px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);
    }

    .info-label-p {
        font-size: 10px;
        font-weight: 800;
        color: #f97316;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 6px;
    }

    .info-value-p {
        font-weight: 800;
        color: #0f172a;
        font-size: 15px;
    }

    .trajet-box-p {
        background: #1e293b;
        color: #fff;
        border-radius: 24px;
        padding: 24px;
        margin-bottom: 24px;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }

    .trajet-point-p {
        text-align: center;
        flex: 1;
    }

    .trajet-point-p p:first-child {
        font-size: 9px;
        color: #fb923c;
        font-weight: 900;
        margin-bottom: 4px;
        text-transform: uppercase;
    }

    .trajet-point-p p:last-child {
        font-size: 14px;
        font-weight: 800;
        text-transform: uppercase;
    }

    .btn-close-modal-p {
        background: rgba(255, 255, 255, 0.15);
        border: none;
        color: #fff;
        width: 36px;
        height: 36px;
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-close-modal-p:hover {
        background: rgba(255, 255, 255, 0.25);
        transform: rotate(90deg);
    }

    .status-check {
        padding: 4px 10px;
        background: #ecfdf5;
        color: #059669;
        font-size: 10px;
        font-weight: 800;
        border-radius: 8px;
        border: 1px solid #d1fae5;
        text-transform: uppercase;
        display: inline-block;
        margin-top: 4px;
    }
</style>

<div id="modalDetails" class="hidden">
    <div class="modal-overlay" onclick="closeModal()"></div>
    <div class="modal-container" id="modalContent">
        <div class="modal-header-premium">
            <div style="display: flex; align-items: center; gap: 15px;">
                <div
                    style="width: 46px; height: 46px; background: rgba(255,255,255,0.2); border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 18px;">
                    <i class="fas fa-id-card"></i>
                </div>
                <div>
                    <h2
                        style="margin: 0; font-size: 18px; font-weight: 900; text-transform: uppercase; letter-spacing: -0.5px;">
                        Détails Passager</h2>
                    <p id="modalRef" style="margin: 0; font-size: 11px; font-weight: 600; opacity: 0.9;"></p>
                </div>
            </div>
            <button onclick="closeModal()" class="btn-close-modal-p"><i class="fas fa-times"></i></button>
        </div>

        <div class="modal-body-premium">
            <div style="display: flex; gap: 24px; margin-bottom: 30px; align-items: center;">
                <div id="passagerInitial"
                    style="width: 80px; height: 80px; border-radius: 20px; background: linear-gradient(135deg, #f97316, #ea580c); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 32px; font-weight: 950; box-shadow: 0 10px 15px -3px rgba(249, 115, 22, 0.3);">
                    ?</div>
                <img id="passagerPhoto" src="" class="hidden"
                    style="width: 80px; height: 80px; border-radius: 20px; object-fit: cover; border: 3px solid #fff; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);">
                <div style="flex: 1;">
                    <p
                        style="margin: 0; font-size: 9px; color: #f97316; font-weight: 900; text-transform: uppercase; letter-spacing: 1px;">
                        Identité du voyageur</p>
                    <p id="passagerNom"
                        style="margin: 0; font-size: 22px; font-weight: 900; color: #0f172a; line-height: 1.1;"></p>
                    <span class="status-check">Vérifié <i class="fas fa-check-circle"></i></span>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 24px;">
                <div class="info-card-p">
                    <p class="info-label-p"><i class="fas fa-phone-alt"></i> Téléphone</p>
                    <p class="info-value-p" id="passagerTel"></p>
                </div>
                <div class="info-card-p">
                    <p class="info-label-p"><i class="fas fa-ambulance"></i> Urgence</p>
                    <p class="info-value-p" id="passagerUrgence" style="font-size: 13px;"></p>
                </div>
            </div>

            <p
                style="font-size: 10px; font-weight: 900; color: #94a3b8; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 12px; border-left: 3px solid #f97316; padding-left: 10px;">
                Détails du Trajet</p>

            <div class="trajet-box-p">
                <div
                    style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding: 0 10px;">
                    <div class="trajet-point-p">
                        <p>Départ</p>
                        <p id="trajetDepart" style="color: #fff;"></p>
                    </div>
                    <div style="display: flex; flex-direction: column; align-items: center;">
                        <i class="fas fa-bus" style="color: #f97316; font-size: 20px;"></i>
                        <div style="width: 40px; height: 1px; background: rgba(255,255,255,0.2); margin: 6px 0;"></div>
                    </div>
                    <div class="trajet-point-p">
                        <p>Arrivée</p>
                        <p id="trajetArrivee" style="color: #fff;"></p>
                    </div>
                </div>
                <div
                    style="display: flex; justify-content: space-between; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 15px;">
                    <div>
                        <p
                            style="font-size: 9px; color: rgba(255,255,255,0.4); font-weight: 900; text-transform: uppercase; margin-bottom: 2px;">
                            Date & Heure</p>
                        <p id="trajetDate" style="font-size: 13px; margin: 0; font-weight: 700; color: #fff;"></p>
                    </div>
                    <div style="text-align: right;">
                        <p
                            style="font-size: 9px; color: rgba(255,255,255,0.4); font-weight: 900; text-transform: uppercase; margin-bottom: 2px;">
                            Siège</p>
                        <p id="trajetSiege"
                            style="font-size: 22px; margin: 0; color: #f97316; font-weight: 950; line-height: 1;"></p>
                    </div>
                </div>
            </div>

            <div id="voyageInfo" class="hidden" style="margin-bottom: 24px;">
                <p
                    style="font-size: 10px; font-weight: 900; color: #94a3b8; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 12px; border-left: 3px solid #f97316; padding-left: 10px;">
                    Véhicule & Chauffeur</p>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div class="info-card-p"
                        style="background: #fff; border-left: 4px solid #f97316; margin-bottom: 0;">
                        <p class="info-label-p" style="color: #64748b;">Bus / Véhicule</p>
                        <p id="voyageVehicule" class="info-value-p"></p>
                    </div>
                    <div class="info-card-p"
                        style="background: #fff; border-left: 4px solid #f97316; margin-bottom: 0;">
                        <p class="info-label-p" style="color: #64748b;">Conducteur</p>
                        <p id="voyageChauffeur" class="info-value-p"></p>
                    </div>
                </div>
            </div>

            <div class="info-card-p"
                style="display: flex; justify-content: space-between; align-items: center; border-style: dashed; margin-top: 10px; background: #fff;">
                <div>
                    <p class="info-label-p">Montant de la transaction</p>
                    <p id="paiementMontant" class="info-value-p" style="font-size: 20px; color: #0f172a;"></p>
                </div>
                <div style="text-align: right;">
                    <p class="info-label-p">Méthode</p>
                    <p id="paiementMethode" class="info-value-p"
                        style="font-size: 11px; background: #f1f5f9; padding: 6px 12px; border-radius: 10px; display: inline-block;">
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function showDetails(id) {
        const modal = document.getElementById('modalDetails');
        const content = document.getElementById('modalContent');
        const modalRef = document.getElementById('modalRef');
        const passagerNom = document.getElementById('passagerNom');
        const passagerTel = document.getElementById('passagerTel');
        const passagerUrgence = document.getElementById('passagerUrgence');
        const photoImg = document.getElementById('passagerPhoto');
        const initialBox = document.getElementById('passagerInitial');
        const trajetDepart = document.getElementById('trajetDepart');
        const trajetArrivee = document.getElementById('trajetArrivee');
        const trajetDate = document.getElementById('trajetDate');
        const trajetSiege = document.getElementById('trajetSiege');
        const paiementMontant = document.getElementById('paiementMontant');
        const paiementMethode = document.getElementById('paiementMethode');
        const voyageInfo = document.getElementById('voyageInfo');
        const voyageVehicule = document.getElementById('voyageVehicule');
        const voyageChauffeur = document.getElementById('voyageChauffeur');

        modal.classList.remove('hidden');

        // Reset fields while loading
        modalRef.textContent = 'Chargement...';
        passagerNom.textContent = 'Chargement...';
        passagerTel.textContent = 'Chargement...';
        passagerUrgence.textContent = 'Chargement...';
        trajetDepart.textContent = 'Chargement...';
        trajetArrivee.textContent = 'Chargement...';
        trajetDate.textContent = 'Chargement...';
        trajetSiege.textContent = 'Chargement...';
        paiementMontant.textContent = 'Chargement...';
        paiementMethode.textContent = 'Chargement...';
        voyageInfo.style.display = 'none';
        photoImg.style.display = 'none';
        initialBox.style.display = 'flex';
        initialBox.textContent = '?';

        const path = window.location.pathname;
        let fetchUrl = '';

        if (path.includes('/gare-espace')) {
            fetchUrl = `/gare-espace/reservations/${id}`;
        } else if (path.includes('/company')) {
            fetchUrl = `/company/booking/reservations/${id}`;
        } else {
            fetchUrl = `/company/booking/reservations/${id}`;
        }

        fetch(fetchUrl, {
                headers: {
                    Accept: 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}`);
                }
                return response.json();
            })
            .then(res => {
                if (!res.success) {
                    throw new Error(res.message || 'Aucune donnée retournée');
                }

                const data = res.data;
                modalRef.textContent = `Nº Commande: ${data.reference}`;
                passagerNom.textContent = data.passager.nom;
                passagerTel.textContent = data.passager.telephone;
                passagerUrgence.textContent = data.passager.urgence;

                if (data.passager.photo) {
                    photoImg.src = data.passager.photo;
                    photoImg.style.display = 'block';
                    initialBox.style.display = 'none';
                } else {
                    photoImg.style.display = 'none';
                    initialBox.style.display = 'flex';
                    initialBox.textContent = data.passager.nom.charAt(0).toUpperCase();
                }

                trajetDepart.textContent = data.trajet.depart;
                trajetArrivee.textContent = data.trajet.arrivee;
                trajetDate.textContent = `${data.trajet.date} à ${data.trajet.heure}`;
                trajetSiege.textContent = data.trajet.siege;
                paiementMontant.textContent = data.paiement.montant;
                paiementMethode.textContent = data.paiement.methode;

                if (data.voyage) {
                    voyageInfo.style.display = 'block';
                    voyageVehicule.textContent = data.voyage.vehicule;
                    voyageChauffeur.textContent = data.voyage.chauffeur;
                } else {
                    voyageInfo.style.display = 'none';
                }
            })
            .catch(error => {
                console.error('Erreur showDetails:', error);
                modalRef.textContent = 'Erreur de chargement';
                passagerNom.textContent = 'Non disponible';
                passagerTel.textContent = 'Non disponible';
                passagerUrgence.textContent = 'Non disponible';
                trajetDepart.textContent = 'Non disponible';
                trajetArrivee.textContent = 'Non disponible';
                trajetDate.textContent = 'Non disponible';
                trajetSiege.textContent = 'Non disponible';
                paiementMontant.textContent = 'Non disponible';
                paiementMethode.textContent = 'Non disponible';
                voyageInfo.style.display = 'none';
            });
    }

    function closeModal() {
        const modal = document.getElementById('modalDetails');
        modal.classList.add('hidden');
    }

    document.addEventListener('keydown', function(event) {
        if (event.key === "Escape") closeModal();
    });
</script>
