<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\CompanyMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AgentMessageController extends Controller
{
    public function index()
    {
        $agent = Auth::guard('agent')->user();
        $messages = $agent->messages()->with('compagnie')->latest()->paginate(10);
        return view('agent.messages.index', compact('messages'));
    }

    public function show($id)
    {
        $message = CompanyMessage::where('recipient_id', Auth::guard('agent')->id())
            ->where('recipient_type', 'App\Models\Agent')
            ->findOrFail($id);
        
        if (!$message->is_read) {
            $message->update(['is_read' => true]);
        }

        return view('agent.messages.show', compact('message'));
    }
    
    public function markAsRead($id)
    {
        $message = CompanyMessage::where('recipient_id', Auth::guard('agent')->id())
            ->where('recipient_type', 'App\Models\Agent')
            ->findOrFail($id);
        $message->update(['is_read' => true]);
        
        return back()->with('success', 'Message marqué comme lu');
    }
}
