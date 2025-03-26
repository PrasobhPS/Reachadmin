<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification as Announcement;
use App\Models\ReachMember;
use App\Models\ReachGeneralAnnouncement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use App\Services\NotificationService;
use App\Services\CurrencyService;

class AnnouncementController extends Controller
{
    protected $currencyService;
    public function __construct(NotificationService $notificationService, CurrencyService $currencyService)
    {
        $this->notificationService = $notificationService;
        $this->currencyService = $currencyService;
    }
    /**
     * Display a listing of the announcements.
     */
    public function announcement_list()
    {
        $notification = ReachGeneralAnnouncement::orderBy('id', 'desc')->paginate(10);
        return view('announcement/list', ['notification' => $notification]);
    }
    public function add_announcement()
    {

        return view('announcement/add_announcement');
    }

    /**
     * Store a newly created announcement.
     */
    public function save_announcement(Request $request)
    {
        $request->validate([
            'members_type' => 'required|string|max:255',
            'message' => 'required|string',
        ]);
        $query = ReachMember::where('is_deleted', 'N')
            ->where('is_email_verified', 1)
            ->where('members_status', 'A');
        // ->pluck('id');

        if ($request->members_type !== 'All') {
            $query->where('members_type', $request->members_type);
        }

        $memberIds = $query->pluck('id');


        if ($memberIds->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'No members found.'], 404);
        }
        // Save announcement in `reach_general_announcements` table
        try {
            ReachGeneralAnnouncement::create([
                'member_type' => $request->members_type,
                'title' => $request->title,
                'message' => $request->message,
            ]);
        } catch (\Exception $e) {
            Log::error('General Announcement insert failed: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }

        foreach ($memberIds as $memberId) {


            try {
                Announcement::create([
                    'employee_id' => 0,
                    'job_id' => 0,
                    'notified_by' => 0,
                    'notified_to' => $memberId,
                    'message' => $request->message,
                    'is_read' => 0,
                    'notification_type' => 0,
                    'url_keyword' => 'alert',
                ]);

                $url_keyword = 'Alert';

                $this->notificationService->sendAlertNotification($memberId, $request->message, $url_keyword, '0', '0', '0', $request->title);
            } catch (\Exception $e) {
                Log::error('Announcement insert failed: ' . $e->getMessage());

                return response()->json(['success' => false, 'error' => $e->getMessage()]);
            }
        }


        return redirect()->route('announcement_list')->with('success', 'Announcement created successfully');
    }

    /**
     * Display a specific announcement.
     */
    public function show($id)
    {
        $announcement = Announcement::find($id);

        if (!$announcement) {
            return response()->json(['success' => false, 'message' => 'Announcement not found'], 404);
        }

        return response()->json(['success' => true, 'data' => $announcement]);
    }



    /**
     * Remove an announcement.
     */
    public function delete_announcement($id)
    {

        $announcement = ReachGeneralAnnouncement::find($id);

        if (!$announcement) {
            return redirect()->route('announcement_list')->with('success', 'Announcement not found!');
        }

        $announcement->delete();
        return redirect()->route('announcement_list')->with('success', 'Announcement deleted successfully!');
    }
}
