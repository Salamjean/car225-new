<!-- Modal Confirmation Passager -->
<div class="modal fade" id="confirmModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius: 12px; overflow: hidden; box-shadow: 0 10px 40px rgba(0,0,0,0.3);">
            <div class="modal-header" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%); color: white; border: none; padding: 20px;">
                <h5 class="modal-title d-flex align-items-center">
                    <i class="material-icons mr-2">person_check</i>
                    Confirmer l'embarquement
                </h5>
                <button type="button" class="close text-white" id="closeConfirmModal" style="opacity: 1;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-0" id="confirmModalBody">
                <!-- Contenu injecté dynamiquement par JavaScript -->
            </div>
            <div class="modal-footer" style="border: none; padding: 15px 20px; background: #f8f9fa;">
                <button type="button" class="btn btn-outline-secondary" id="cancelConfirmBtn">
                    <i class="material-icons mr-1" style="font-size: 18px; vertical-align: middle;">close</i>
                    Annuler
                </button>
                <button type="button" class="btn btn-success btn-lg px-4" id="confirmEmbarquementBtn">
                    <i class="material-icons mr-1" style="font-size: 20px; vertical-align: middle;">check</i>
                    Confirmer l'embarquement
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    #confirmModal .passenger-info-card {
        background: white;
        padding: 25px;
    }
    
    #confirmModal .passenger-avatar {
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 15px;
    }
    
    #confirmModal .passenger-avatar i {
        font-size: 40px;
        color: white;
    }
    
    #confirmModal .passenger-name {
        font-size: 1.5rem;
        font-weight: 600;
        color: #333;
        text-align: center;
        margin-bottom: 20px;
    }
    
    #confirmModal .info-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }
    
    #confirmModal .info-table tr {
        border-bottom: 1px solid #eee;
    }
    
    #confirmModal .info-table tr:last-child {
        border-bottom: none;
    }
    
    #confirmModal .info-table td {
        padding: 12px 15px;
        vertical-align: middle;
    }
    
    #confirmModal .info-table td:first-child {
        font-weight: 500;
        color: #666;
        width: 120px;
        text-align: right;
        padding-right: 20px;
    }
    
    #confirmModal .info-table td:last-child {
        color: #333;
    }
    
    #confirmModal .seat-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
        color: white;
        font-size: 1.2rem;
        font-weight: bold;
        border-radius: 8px;
    }
    
    #confirmModal .reference-badge {
        display: inline-block;
        padding: 5px 12px;
        background: #17a2b8;
        color: white;
        font-size: 0.85rem;
        font-weight: 500;
        border-radius: 20px;
    }
</style>
