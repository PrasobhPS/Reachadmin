<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

use App\Models\ReachMember;
use App\Models\ReachSchedule;

class ScheduleController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index($id)
    {
        $specialist = ReachMember::select('id', 'members_fname', 'members_lname')->find($id);

        return view('schedule/add_schedule', compact('specialist'));
    }

    public function timeSlots(Request $request)
    {
        $postData = $request->all();
        $min = $postData['min'];
        $day = $postData['day'];

        // Generate time slots
        $startTime = strtotime('07:00');
        $endTime = strtotime('22:00');
        $interval = $min * 60; // minutes in seconds

        $morningSlots = [];
        $afternoonSlots = [];
        $eveningSlots = [];

        $currentTime = $startTime;

        while ($currentTime <= $endTime) {
            $formattedTime = date('h:i A', $currentTime);

            if ($currentTime < strtotime('12:00')) {
                $morningSlots[] = $formattedTime;
            } elseif ($currentTime < strtotime('17:00')) {
                $afternoonSlots[] = $formattedTime;
            } else {
                $eveningSlots[] = $formattedTime;
            }

            $currentTime += $interval;
        }

        $data = [
            'morningSlots'   => $morningSlots,
            'afternoonSlots' => $afternoonSlots,
            'eveningSlots'   => $eveningSlots,
        ];

        $schedule_time = ReachSchedule::where('member_id', $postData['member_id'])
            ->where('day', $day)
            ->get(['start_time'])
            ->toArray();

        $data['startTimes'] = array_column($schedule_time, 'start_time');

        $html = view('schedule/time_slots', $data)->render();
        return response()->json(['status' => true, 'html' => $html]);
    }

    public function saveSchedule(Request $request)
    {
        $postData = $request->all();

        $member_id = $postData['member_id'];
        $day = $postData['day'][0];

        ReachSchedule::where('member_id', $member_id)->where('day', $day)->delete();

        $scheduleTime = $postData['schedule_time'];
        foreach ($scheduleTime as $time) {
            $arrData = [
                "member_id"  => $member_id,
                "time_slot"  => $postData['time_slots'],
                "day"        => $day,
                "start_time" => $time,
            ];
            ReachSchedule::create($arrData);
        }

        return response()->json(['status' => true, 'msg' => 'Schedule timings created successfully.']);
    }

}
