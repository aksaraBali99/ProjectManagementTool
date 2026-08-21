<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class NotificationController extends Controller
{
    /**
     * Mark-as-read on view: everything currently unread is marked read
     * once this page has been fetched — but the "was unread" state is
     * captured beforehand so the view can still visually distinguish
     * what was new in this visit.
     */
    public function index(): View
    {
        $user = auth()->user();

        $notifications = $user->notifications()->paginate(20);
        $originallyUnreadIds = $notifications->getCollection()->filter(fn ($n) => is_null($n->read_at))->pluck('id');

        $user->unreadNotifications->markAsRead();

        return view('notifications.index', [
            'notifications' => $notifications,
            'originallyUnreadIds' => $originallyUnreadIds,
        ]);
    }
}
