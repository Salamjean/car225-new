<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class NotificationApiController extends Controller
{
    /**
     * GET /api/user/notifications
     * Liste toutes les notifications de l'utilisateur (paginées)
     */
    public function index(Request $request)
    {
        try {
            $user = Auth::user();
            $perPage = $request->get('per_page', 20);
            
            $notifications = $user->notifications()->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $notifications,
                'unread_count' => $user->unreadNotifications()->count()
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur liste notifications API: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des notifications',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET /api/user/notifications/unread-count
     * Obtenir le nombre de notifications non lues
     */
    public function unreadCount()
    {
        try {
            return response()->json([
                'success' => true,
                'unread_count' => Auth::user()->unreadNotifications()->count()
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * POST /api/user/notifications/{id}/mark-as-read
     * Marquer une notification spécifique comme lue
     */
    public function markAsRead($id)
    {
        try {
            $notification = Auth::user()->notifications()->findOrFail($id);
            $notification->markAsRead();

            return response()->json([
                'success' => true,
                'message' => 'Notification marquée comme lue'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Notification non trouvée ou erreur',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * POST /api/user/notifications/mark-all-as-read
     * Marquer toutes les notifications comme lues
     */
    public function markAllAsRead()
    {
        try {
            Auth::user()->unreadNotifications->markAsRead();

            return response()->json([
                'success' => true,
                'message' => 'Toutes les notifications ont été marquées comme lues'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'opération',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * DELETE /api/user/notifications/{id}
     * Supprimer une notification
     */
    public function destroy($id)
    {
        try {
            $notification = Auth::user()->notifications()->findOrFail($id);
            $notification->delete();

            return response()->json([
                'success' => true,
                'message' => 'Notification supprimée'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression',
                'error' => $e->getMessage()
            ], 404);
        }
    }
}
