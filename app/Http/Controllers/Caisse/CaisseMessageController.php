<?php

namespace App\Http\Controllers\Caisse;

use App\Http\Controllers\Controller;
use App\Models\CompanyMessage;
use App\Models\GareMessage;
use App\Models\Caisse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CaisseMessageController extends Controller
{
    public function index()
    {
        $caisse = Auth::guard('caisse')->user();

        // Messages from compagnie
        $companyMessages = CompanyMessage::where('recipient_type', Caisse::class)
            ->where('recipient_id', $caisse->id)
            ->get()
            ->map(function ($msg) {
                $msg->sender_name = $msg->compagnie->name ?? 'La Direction';
                $msg->sender_type_label = 'Compagnie';
                $msg->sender_icon = 'fa-building';
                $msg->source = 'company';
                return $msg;
            });

        // Messages from gare
        $gareMessages = GareMessage::where('recipient_type', Caisse::class)
            ->where('recipient_id', $caisse->id)
            ->get()
            ->map(function ($msg) {
                $msg->sender_name = $msg->gare->nom_gare ?? 'La Gare';
                $msg->sender_type_label = 'Gare';
                $msg->sender_icon = 'fa-warehouse';
                $msg->source = 'gare';
                return $msg;
            });

        // Merge and sort
        $allMessages = $companyMessages->concat($gareMessages)->sortByDesc('created_at');

        $page = request()->get('page', 1);
        $perPage = 10;
        $messages = new \Illuminate\Pagination\LengthAwarePaginator(
            $allMessages->forPage($page, $perPage)->values(),
            $allMessages->count(),
            $perPage,
            $page,
            ['path' => request()->url()]
        );

        return view('caisse.messages.index', compact('messages'));
    }

    public function show($id)
    {
        $caisse = Auth::guard('caisse')->user();
        $source = request()->query('source', 'company');

        if ($source === 'gare') {
            $message = GareMessage::where('recipient_type', Caisse::class)
                ->where('recipient_id', $caisse->id)
                ->findOrFail($id);
        } else {
            $message = CompanyMessage::where('recipient_type', Caisse::class)
                ->where('recipient_id', $caisse->id)
                ->findOrFail($id);
        }

        if (!$message->is_read) {
            $message->update(['is_read' => true]);
        }

        if ($source === 'gare') {
            $message->sender_name = $message->gare->nom_gare ?? 'La Gare';
            $message->sender_type_label = 'Gare';
            $message->sender_icon = 'fa-warehouse';
            $message->source = 'gare';
        } else {
            $message->sender_name = $message->compagnie->name ?? 'La Direction';
            $message->sender_type_label = 'Compagnie';
            $message->sender_icon = 'fa-building';
            $message->source = 'company';
        }

        return view('caisse.messages.show', compact('message'));
    }
}
