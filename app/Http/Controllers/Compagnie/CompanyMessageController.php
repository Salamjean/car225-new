<?php

namespace App\Http\Controllers\Compagnie;

use App\Notifications\NewInternalMessageNotification;
use App\Services\FcmService;
use App\Http\Controllers\Controller;
use App\Models\Agent;
use App\Models\Caisse;
use App\Models\CompanyMessage;
use App\Models\Gare;
use App\Models\GareMessage;
use App\Models\Personnel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompanyMessageController extends Controller
{
    public function index(Request $request)
    {
        $compagnie = Auth::guard('compagnie')->user();
        $query = $compagnie->sentMessages()->with('recipient')->latest();

        if ($request->has('type')) {
            switch ($request->type) {
                case 'agent':
                    $query->where('recipient_type', Agent::class);
                    break;
                case 'caisse':
                    $query->where('recipient_type', Caisse::class);
                    break;
                case 'personnel':
                    $query->where('recipient_type', Personnel::class);
                    break;
            }
        }

        $messages = $query->paginate(10);

        // Messages reçus des gares
        $receivedQuery = GareMessage::where('recipient_type', 'App\Models\Compagnie')
            ->where('recipient_id', $compagnie->id);
            
        $unreadReceivedCount = (clone $receivedQuery)->where('is_read', false)->count();

        $receivedMessages = $receivedQuery->with('gare')
            ->latest()
            ->paginate(10, ['*'], 'received_page');

        return view('compagnie.messages.index', compact('messages', 'receivedMessages', 'unreadReceivedCount'));
    }

    public function show(CompanyMessage $message)
    {
        if ($message->compagnie_id !== Auth::guard('compagnie')->id()) {
            abort(403);
        }
        
        return view('compagnie.messages.show', compact('message'));
    }

    public function showReceived($id)
    {
        $compagnie = Auth::guard('compagnie')->user();
        $message = GareMessage::where('recipient_type', 'App\Models\Compagnie')
            ->where('recipient_id', $compagnie->id)
            ->with('gare')
            ->findOrFail($id);

        if (!$message->is_read) {
            $message->update(['is_read' => true]);
        }

        $message->sender_name = $message->gare->nom_gare ?? 'La Gare';
        $message->sender_type_label = 'Gare';
        $message->sender_icon = 'fa-warehouse';
        $message->source = 'gare';

        return view('compagnie.messages.show-received', compact('message'));
    }

    public function create()
    {
        return view('compagnie.messages.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'recipient_type' => 'required|in:agent,caisse,personnel,gare',
            'recipient_id' => 'required',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $compagnie = Auth::guard('compagnie')->user();
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
            case 'gare':
                $modelClass = Gare::class;
                break;
        }

        $recipient = $modelClass::where('id', $request->recipient_id)
            ->where('compagnie_id', $compagnie->id)
            ->firstOrFail();

        $message = new CompanyMessage([
            'compagnie_id' => $compagnie->id,
            'subject' => $request->subject,
            'message' => $request->message,
            'is_read' => false,
        ]);
        
        $message->recipient()->associate($recipient);
        $message->save();

        try {
            $recipient->notify(new NewInternalMessageNotification($message));

            if (!empty($recipient->fcm_token)) {
                $fcmService = app(FcmService::class);
                $title = 'Nouveau Message direction 📩';
                $body = "Sujet : {$message->subject}";
                
                $fcmService->sendNotification($recipient->fcm_token, $title, $body, [
                    'type' => 'internal_message',
                    'message_id' => $message->id,
                ]);
            }
        } catch (\Exception $e) {
            \Log::error("Erreur d'envoi de notification: " . $e->getMessage());
        }

        return redirect()->route('compagnie.messages.index')->with('success', 'Message envoyé avec succès');
    }

    public function getRecipients(Request $request)
    {
        $type = $request->query('type');
        $compagnie = Auth::guard('compagnie')->user();
        $recipients = [];

        switch ($type) {
            case 'agent':
                $recipients = Agent::where('compagnie_id', $compagnie->id)->get(['id', 'name', 'prenom']);
                break;
            case 'caisse':
                $recipients = Caisse::where('compagnie_id', $compagnie->id)->get(['id', 'name', 'prenom']);
                break;
            case 'personnel':
                $recipients = Personnel::where('compagnie_id', $compagnie->id)->get(['id', 'name', 'prenom', 'type_personnel']);
                break;
            case 'gare':
                $recipients = Gare::where('compagnie_id', $compagnie->id)->get(['id', 'nom_gare as name']);
                break;
        }

        return response()->json($recipients);
    }
}
