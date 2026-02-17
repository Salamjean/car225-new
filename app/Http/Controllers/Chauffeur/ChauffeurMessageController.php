<?php

namespace App\Http\Controllers\Chauffeur;

use App\Http\Controllers\Controller;
use App\Models\CompanyMessage;
use App\Models\Personnel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChauffeurMessageController extends Controller
{
    public function index()
    {
        $chauffeur = Auth::guard('chauffeur')->user();
        
        $messages = CompanyMessage::where('recipient_type', Personnel::class)
            ->where('recipient_id', $chauffeur->id)
            ->latest()
            ->paginate(10);

        return view('chauffeur.messages.index', compact('messages'));
    }

    public function show($id)
    {
        $chauffeur = Auth::guard('chauffeur')->user();
        
        $message = CompanyMessage::where('recipient_type', Personnel::class)
            ->where('recipient_id', $chauffeur->id)
            ->findOrFail($id);

        if (!$message->is_read) {
            $message->update(['is_read' => true]);
        }

        return view('chauffeur.messages.show', compact('message'));
    }
}
