<?php

namespace App\Http\Controllers\Caisse;

use App\Http\Controllers\Controller;
use App\Models\CompanyMessage;
use App\Models\Caisse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CaisseMessageController extends Controller
{
    public function index()
    {
        $caisse = Auth::guard('caisse')->user();
        
        $messages = CompanyMessage::where('recipient_type', Caisse::class)
            ->where('recipient_id', $caisse->id)
            ->latest()
            ->paginate(10);

        return view('caisse.messages.index', compact('messages'));
    }

    public function show($id)
    {
        $caisse = Auth::guard('caisse')->user();
        
        $message = CompanyMessage::where('recipient_type', Caisse::class)
            ->where('recipient_id', $caisse->id)
            ->findOrFail($id);

        if (!$message->is_read) {
            $message->update(['is_read' => true]);
        }

        return view('caisse.messages.show', compact('message'));
    }
}
