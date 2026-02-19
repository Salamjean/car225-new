<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\CompanyMessage;
use App\Models\GareMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AgentMessageController extends Controller
{
    public function index()
    {
        $agent = Auth::guard('agent')->user();

        // Messages from compagnie
        $companyMessages = CompanyMessage::where('recipient_id', $agent->id)
            ->where('recipient_type', 'App\Models\Agent')
            ->get()
            ->map(function ($msg) {
                $msg->sender_name = $msg->compagnie->name ?? 'La Direction';
                $msg->sender_type_label = 'Compagnie';
                $msg->sender_icon = 'fa-building';
                $msg->source = 'company';
                return $msg;
            });

        // Messages from gare
        $gareMessages = GareMessage::where('recipient_id', $agent->id)
            ->where('recipient_type', 'App\Models\Agent')
            ->get()
            ->map(function ($msg) {
                $msg->sender_name = $msg->gare->nom_gare ?? 'La Gare';
                $msg->sender_type_label = 'Gare';
                $msg->sender_icon = 'fa-warehouse';
                $msg->source = 'gare';
                return $msg;
            });

        // Merge and sort by date
        $allMessages = $companyMessages->concat($gareMessages)->sortByDesc('created_at');
        
        // Manual pagination
        $page = request()->get('page', 1);
        $perPage = 10;
        $total = $allMessages->count();
        $messages = new \Illuminate\Pagination\LengthAwarePaginator(
            $allMessages->forPage($page, $perPage)->values(),
            $total,
            $perPage,
            $page,
            ['path' => request()->url()]
        );

        return view('agent.messages.index', compact('messages'));
    }

    public function show($id)
    {
        $agent = Auth::guard('agent')->user();
        $source = request()->query('source', 'company');

        if ($source === 'gare') {
            $message = GareMessage::where('recipient_id', $agent->id)
                ->where('recipient_type', 'App\Models\Agent')
                ->findOrFail($id);
            $message->sender_name = $message->gare->nom_gare ?? 'La Gare';
            $message->sender_type_label = 'Gare';
            $message->sender_icon = 'fa-warehouse';
            $message->source = 'gare';
        } else {
            $message = CompanyMessage::where('recipient_id', $agent->id)
                ->where('recipient_type', 'App\Models\Agent')
                ->findOrFail($id);
            $message->sender_name = $message->compagnie->name ?? 'La Direction';
            $message->sender_type_label = 'Compagnie';
            $message->sender_icon = 'fa-building';
            $message->source = 'company';
        }

        if (!$message->is_read) {
            $message->update(['is_read' => true]);
        }

        return view('agent.messages.show', compact('message'));
    }

    public function markAsRead($id)
    {
        $agent = Auth::guard('agent')->user();
        $source = request()->query('source', 'company');

        if ($source === 'gare') {
            $message = GareMessage::where('recipient_id', $agent->id)
                ->where('recipient_type', 'App\Models\Agent')
                ->findOrFail($id);
        } else {
            $message = CompanyMessage::where('recipient_id', $agent->id)
                ->where('recipient_type', 'App\Models\Agent')
                ->findOrFail($id);
        }

        $message->update(['is_read' => true]);
        return back()->with('success', 'Message marqué comme lu');
    }
}
