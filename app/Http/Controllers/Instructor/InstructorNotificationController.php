<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class InstructorNotificationController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $notifications = $user->notifications()
            ->latest()
            ->paginate(15);

        $unreadCount = $user->unreadNotifications()->count();

        return view('instructor.notifications.index', compact(
            'notifications',
            'unreadCount'
        ));
    }

    
    
    public function open($notificationId)
    {
        $notification = auth()->user()
            ->notifications()
            ->findOrFail($notificationId);

        $url = $notification->data['url'] ?? null;

        $notification->markAsRead();

        if (!$url) {
            return redirect()
                ->route('instructor.notifications')
                ->with('success', 'Notification marked as read.');
        }

        $parsedUrl = parse_url($url);

        if ($parsedUrl === false) {
            return redirect()->route('instructor.notifications');
        }

        // Extract only the internal URL path.
        $destination = $parsedUrl['path'] ?? '';

        // Only permit instructor lesson details destinations.
        if (!preg_match('#^/instructor/lessons/\d+$#', $destination)) {
            return redirect()
                ->route('instructor.notifications')
                ->with('error', 'The lesson destination is invalid.');
        }

        return redirect()->to($destination);
    }



    public function markAsRead($notificationId)
    {
        $notification = auth()->user()
            ->notifications()
            ->findOrFail($notificationId);

        $notification->markAsRead();

        return back()->with('success', 'Notification marked as read.');
    }

    public function markAllAsRead()
    {
        auth()->user()
        ->unreadNotifications()
        ->update(['read_at' => now()]);
        
        return back()->with('success', 'All notifications marked as read.');
    }
    
    
    

}
