<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\GeneralNotification;
use App\Services\FcmService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class NotificationController extends Controller
{
    protected $fcmService;

    public function __construct(FcmService $fcmService)
    {
        $this->fcmService = $fcmService;
    }

    public function index()
    {
        $usersCount = User::count();
        return view('admin.notifications.index', compact('usersCount'));
    }

    public function send(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'required|in:info,warning,error,success',
            'target' => 'required|in:all,specific',
            'user_ids' => 'required_if:target,specific|array',
            'channels' => 'required|array',
            'channels.*' => 'in:web,mobile',
        ]);

        $title = $request->title;
        $message = $request->message;
        $type = $request->type;
        $channels = $request->channels;

        if ($request->target === 'all') {
            $users = User::all();
        } else {
            $users = User::whereIn('id', $request->user_ids)->get();
        }

        foreach ($users as $user) {
            // Web Notification (Database)
            if (in_array('web', $channels)) {
                $user->notify(new GeneralNotification($title, $message, $type));
            }

            // Mobile Notification (FCM)
            if (in_array('mobile', $channels) && $user->fcm_token) {
                $this->fcmService->sendNotification(
                    $user->fcm_token,
                    $title,
                    $message,
                    ['type' => $type]
                );
            }
        }

        return redirect()->back()->with('success', 'Notifications envoyées avec succès à ' . $users->count() . ' utilisateur(s).');
    }

    public function searchUsers(Request $request)
    {
        $search = $request->get('q');
        $users = User::where('name', 'like', "%$search%")
            ->orWhere('prenom', 'like', "%$search%")
            ->orWhere('email', 'like', "%$search%")
            ->limit(10)
            ->get(['id', 'name', 'prenom', 'email']);

        return response()->json($users);
    }
}
