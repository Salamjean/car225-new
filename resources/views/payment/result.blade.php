<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Résultat du paiement - Car225</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .card {
            background: white;
            border-radius: 20px;
            padding: 40px;
            max-width: 400px;
            width: 100%;
            text-align: center;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        .icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 40px;
        }
        .icon.success { background: #10b981; color: white; }
        .icon.error { background: #ef4444; color: white; }
        .icon.pending { background: #f59e0b; color: white; }
        h1 { font-size: 24px; color: #1f2937; margin-bottom: 10px; }
        p { color: #6b7280; margin-bottom: 20px; }
        .transaction-id {
            background: #f3f4f6;
            padding: 10px 15px;
            border-radius: 8px;
            font-family: monospace;
            font-size: 12px;
            color: #4b5563;
            word-break: break-all;
        }
        .btn {
            display: inline-block;
            margin-top: 20px;
            padding: 12px 30px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
        }
        .btn:hover { background: #5a67d8; }
    </style>
</head>
<body>
    <div class="card">
        @if($success)
            <div class="icon success">✓</div>
            <h1>Paiement Réussi !</h1>
        @else
            @if(str_contains($message, 'attente'))
                <div class="icon pending">⏳</div>
                <h1>En attente...</h1>
            @else
                <div class="icon error">✕</div>
                <h1>Paiement Échoué</h1>
            @endif
        @endif
        
        <p>{{ $message }}</p>
        
        @if($transaction_id)
            <div class="transaction-id">
                Réf: {{ $transaction_id }}
            </div>
        @endif
        
        <a href="car225://payment?success={{ $success ? 'true' : 'false' }}&transactionId={{ $transaction_id }}" class="btn">
            Retour à l'application
        </a>
    </div>
</body>
</html>
