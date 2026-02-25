<?php

namespace App\Http\Controllers\Api\Chauffeur;

use App\Http\Controllers\Controller;
use App\Models\CompanyMessage;
use App\Models\GareMessage;
use App\Models\Personnel;
use Illuminate\Http\Request;
use Carbon\Carbon;

class MessageApiController extends Controller
{
    /**
     * Liste de tous les messages (compagnie + gare)
     */
    public function index(Request $request)
    {
        $chauffeur = $request->user();

        // Messages de la compagnie
        $companyMessages = CompanyMessage::where('recipient_type', Personnel::class)
            ->where('recipient_id', $chauffeur->id)
            ->get()
            ->map(function ($msg) {
                return [
                    'id' => $msg->id,
                    'subject' => $msg->subject,
                    'message' => $msg->message,
                    'sender_name' => $msg->compagnie->name ?? 'La Direction',
                    'sender_type' => 'Compagnie',
                    'source' => 'company',
                    'is_read' => $msg->is_read,
                    'created_at' => $msg->created_at->format('d/m/Y H:i'),
                ];
            });

        // Messages de la gare
        $gareMessages = GareMessage::where('recipient_type', Personnel::class)
            ->where('recipient_id', $chauffeur->id)
            ->get()
            ->map(function ($msg) {
                return [
                    'id' => $msg->id,
                    'subject' => $msg->subject,
                    'message' => $msg->message,
                    'sender_name' => $msg->gare->nom_gare ?? 'La Gare',
                    'sender_type' => 'Gare',
                    'source' => 'gare',
                    'is_read' => $msg->is_read,
                    'created_at' => $msg->created_at->format('d/m/Y H:i'),
                ];
            });

        $allMessages = $companyMessages->concat($gareMessages)->sortByDesc('created_at')->values();

        // Pagination manuelle
        $page = $request->get('page', 1);
        $perPage = 15;
        $total = $allMessages->count();
        $paginated = $allMessages->forPage($page, $perPage)->values();

        $unreadCount = $allMessages->where('is_read', false)->count();

        return response()->json([
            'success' => true,
            'unread_count' => $unreadCount,
            'messages' => $paginated,
            'pagination' => [
                'current_page' => (int)$page,
                'last_page' => (int)ceil($total / $perPage),
                'per_page' => $perPage,
                'total' => $total,
            ],
        ]);
    }

    /**
     * Détails d'un message (+ marquer comme lu)
     */
    public function show(Request $request, $id)
    {
        $chauffeur = $request->user();
        $source = $request->query('source', 'company');

        if ($source === 'gare') {
            $message = GareMessage::where('recipient_type', Personnel::class)
                ->where('recipient_id', $chauffeur->id)
                ->findOrFail($id);
        } else {
            $message = CompanyMessage::where('recipient_type', Personnel::class)
                ->where('recipient_id', $chauffeur->id)
                ->findOrFail($id);
        }

        if (!$message->is_read) {
            $message->update(['is_read' => true]);
        }

        return response()->json([
            'success' => true,
            'message' => [
                'id' => $message->id,
                'subject' => $message->subject,
                'message' => $message->message,
                'sender_name' => $source === 'gare'
                    ? ($message->gare->nom_gare ?? 'La Gare')
                    : ($message->compagnie->name ?? 'La Direction'),
                'sender_type' => $source === 'gare' ? 'Gare' : 'Compagnie',
                'source' => $source,
                'is_read' => true,
                'created_at' => $message->created_at->format('d/m/Y H:i'),
            ],
        ]);
    }

    /**
     * Envoyer un message à la gare
     */
    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $chauffeur = $request->user();

        if (!$chauffeur->gare_id) {
            return response()->json([
                'success' => false,
                'message' => 'Vous n\'êtes rattaché à aucune gare.',
            ], 400);
        }

        GareMessage::create([
            'gare_id' => $chauffeur->gare_id,
            'sender_type' => Personnel::class,
            'sender_id' => $chauffeur->id,
            'recipient_type' => \App\Models\Gare::class,
            'recipient_id' => $chauffeur->gare_id,
            'subject' => $request->subject,
            'message' => $request->message,
            'is_read' => false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Message envoyé à la gare avec succès.',
        ], 201);
    }
}
