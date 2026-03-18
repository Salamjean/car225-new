@extends('compagnie.layouts.template')

@section('page-title', 'Vérification Email')
@section('page-subtitle', 'Vérifiez la modification de votre adresse email')

@section('styles')
<style>
    .otp-container {
        max-width: 450px; margin: 40px auto;
    }
    .otp-card {
        background: var(--surface); border-radius: var(--radius);
        border: 1px solid var(--border); box-shadow: var(--shadow-md);
        padding: 32px; text-align: center;
    }
    .otp-icon-box {
        width: 60px; height: 60px; border-radius: 50%;
        background: var(--orange-light); color: var(--orange);
        display: flex; align-items: center; justify-content: center;
        font-size: 24px; margin: 0 auto 20px;
    }
    .otp-input {
        width: 100%; padding: 16px; border: 2px solid var(--border-strong);
        border-radius: var(--radius-sm); font-size: 24px; font-weight: 800;
        text-align: center; letter-spacing: 12px; background: var(--surface-2);
        color: var(--text-1); transition: 0.2s; margin-bottom: 24px;
    }
    .otp-input:focus {
        outline: none; border-color: var(--orange); background: var(--surface);
        box-shadow: 0 0 0 4px var(--orange-light);
    }
    
    .btn-submit {
        width: 100%; background: linear-gradient(135deg, var(--orange) 0%, var(--orange-dark) 100%);
        color: white; padding: 14px; border-radius: var(--radius-sm);
        font-weight: 800; font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px;
        border: none; cursor: pointer; box-shadow: 0 4px 15px rgba(249,115,22,0.3); transition: 0.2s;
    }
    .btn-submit:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(249,115,22,0.4); }

    .btn-cancel {
        width: 100%; display: block; text-align: center; padding: 12px;
        border: 1px solid var(--border-strong); border-radius: var(--radius-sm);
        background: var(--surface); color: var(--text-2); font-weight: 700;
        font-size: 13px; text-decoration: none; transition: 0.2s; margin-top: 16px;
    }
    .btn-cancel:hover { background: var(--surface-2); color: var(--text-1); text-decoration: none; }
</style>
@endsection

@section('content')
<div class="dashboard-page">
    <div class="otp-container">
        <div class="otp-card">
            <div class="otp-icon-box">
                <i class="fas fa-shield-alt"></i>
            </div>
            <h2 style="font-size: 20px; font-weight: 800; color: var(--text-1); margin-bottom: 10px;">Vérification de Sécurité</h2>
            <p style="font-size: 13px; color: var(--text-3); font-weight: 600; margin-bottom: 24px;">
                Un code à 6 chiffres a été envoyé à l'adresse :<br>
                <strong style="color: var(--text-1);">{{ $pendingData['email'] }}</strong>
            </p>

            <form action="{{ route('gare.verify-email-update.store', $gare->id) }}" method="POST">
                @csrf
                <label for="otp" style="font-size: 11px; font-weight: 800; color: var(--text-2); text-transform: uppercase; letter-spacing: 1px; display: block; margin-bottom: 10px;">Entrez votre code</label>
                <input id="otp" name="otp" type="text" maxlength="6" required class="otp-input" placeholder="000000">
                
                @error('otp')
                    <div class="alert alert-danger mb-4" style="font-size: 12px; font-weight: 700; border-radius: var(--radius-sm);">{{ $message }}</div>
                @enderror

                <button type="submit" class="btn-submit">
                    Valider la modification
                </button>
            </form>

            <div style="margin: 20px 0; position: relative;">
                <hr style="border-color: var(--border);">
                <span style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: var(--surface); padding: 0 10px; font-size: 12px; font-weight: 700; color: var(--text-3);">OU</span>
            </div>

            <a href="{{ route('gare.edit', $gare->id) }}" class="btn-cancel">
                Annuler la modification
            </a>
        </div>
    </div>
</div>
@endsection