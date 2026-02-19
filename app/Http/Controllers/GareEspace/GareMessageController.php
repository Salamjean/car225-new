<?php

namespace App\Http\Controllers\GareEspace;

use App\Http\Controllers\Controller;
use App\Models\Agent;
use App\Models\Caisse;
use App\Models\Compagnie;
use App\Models\GareMessage;
use App\Models\Personnel;
use App\Notifications\NewInternalMessageNotification;
use App\Services\FcmService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GareMessageController extends Controller
{
    public function index(Request $request)
    {
        $gare = Auth::guard('gare')->user();

        // Messages envoyés par la gare
        $sentQuery = GareMessage::where('gare_id', $gare->id)
            ->with('recipient')
            ->latest();

        if ($request->has('type') && $request->type !== 'all') {
            switch ($request->type) {
                case 'agent':
                    $sentQuery->where('recipient_type', Agent::class);
                    break;
                case 'caisse':
                    $sentQuery->where('recipient_type', Caisse::class);
                    break;
                case 'personnel':
                    $sentQuery->where('recipient_type', Personnel::class);
                    break;
                case 'compagnie':
                    $sentQuery->where('recipient_type', Compagnie::class);
                    break;
            }
        }

        $sentMessages = $sentQuery->paginate(10, ['*'], 'sent_page');

        // Messages reçus de la direction (compagnie)
        $receivedMessages = CompanyMessage::where('recipient_type', 'App\Models\Gare')
            ->where('recipient_id', $gare->id)
            ->with('compagnie')
            ->latest()
            ->paginate(10, ['*'], 'received_page');
        
        return view('gare-espace.messages.index', compact('sentMessages', 'receivedMessages'));
    }

    public function create()
    {
        return view('gare-espace.messages.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'recipient_type' => 'required|in:agent,caisse,personnel,compagnie',
            'recipient_id' => 'required',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $gare = Auth::guard('gare')->user();
        $modelClass = null;

        switch ($request->recipient_type) {
            case 'agent':
                $modelClass = Agent::class;
                break;
            case 'caisse':
                $modelClass = Caisse::class;
                break;
            case 'personnel':
                $modelClass = Personnel::class;
                break;
            case 'compagnie':
                $modelClass = Compagnie::class;
                break;
        }

        // For compagnie, verify it's the parent compagnie
        if ($request->recipient_type === 'compagnie') {
            $recipient = $modelClass::where('id', $request->recipient_id)
                ->where('id', $gare->compagnie_id)
                ->firstOrFail();
        } else {
            // Verify recipient belongs to the same gare or compagnie
            $query = $modelClass::where('id', $request->recipient_id)
                ->where('compagnie_id', $gare->compagnie_id);
            
            // For agent, caisse, personnel — also check gare_id if available
            if (in_array($request->recipient_type, ['agent', 'caisse', 'personnel'])) {
                $query->where('gare_id', $gare->id);
            }

            $recipient = $query->firstOrFail();
        }

        $message = new GareMessage([
            'gare_id' => $gare->id,
            'subject' => $request->subject,
            'message' => $request->message,
            'is_read' => false,
        ]);

        $message->recipient()->associate($recipient);
        $message->save();

        // Push notification if available
        try {
            if (!empty($recipient->fcm_token)) {
                $fcmService = app(FcmService::class);
                $title = "Nouveau message de la gare {$gare->nom_gare} 📩";
                $body = "Sujet : {$message->subject}";

                $fcmService->sendNotification($recipient->fcm_token, $title, $body, [
                    'type' => 'gare_message',
                    'message_id' => $message->id,
                ]);
            }
        } catch (\Exception $e) {
            \Log::error("Erreur d'envoi de notification gare: " . $e->getMessage());
        }

        return redirect()->route('gare-espace.messages.index')->with('success', 'Message envoyé avec succès');
    }

    public function show($id)
    {
        $gare = Auth::guard('gare')->user();
        $type = request()->query('type', 'sent');

        if ($type === 'received') {
            $message = CompanyMessage::where('recipient_type', 'App\Models\Gare')
                ->where('recipient_id', $gare->id)
                ->with('compagnie')
                ->findOrFail($id);
            
            if (!$message->is_read) {
                $message->update(['is_read' => true]);
            }
            
            $message->sender_name = $message->compagnie->name ?? 'La Direction';
            $message->sender_type_label = 'Compagnie';
            $message->sender_icon = 'fa-building';
            $message->source = 'received';
        } else {
            $message = GareMessage::where('gare_id', $gare->id)
                ->with('recipient')
                ->findOrFail($id);
            
            $message->sender_name = $gare->nom_gare;
            $message->sender_type_label = 'Ma Gare';
            $message->sender_icon = 'fa-warehouse';
            $message->source = 'sent';
        }

        return view('gare-espace.messages.show', compact('message'));
    }

    public function getRecipients(Request $request)
    {
        $type = $request->query('type');
        $gare = Auth::guard('gare')->user();
        $recipients = [];

        switch ($type) {
            case 'agent':
                $recipients = Agent::where('gare_id', $gare->id)->get(['id', 'name', 'prenom']);
                break;
            case 'caisse':
                $recipients = Caisse::where('gare_id', $gare->id)->get(['id', 'name', 'prenom']);
                break;
            case 'personnel':
                $recipients = Personnel::where('gare_id', $gare->id)->get(['id', 'name', 'prenom', 'type_personnel']);
                break;
            case 'compagnie':
                $compagnie = Compagnie::find($gare->compagnie_id);
                if ($compagnie) {
                    $recipients = [['id' => $compagnie->id, 'name' => $compagnie->name, 'prenom' => '']];
                }
                break;
        }

        return response()->json($recipients);
    }
}
