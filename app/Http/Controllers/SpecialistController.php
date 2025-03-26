<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\ReachSpecialist;
use App\Models\ReachCountry;
use App\Models\JobDetails\ReachJobRole;
use App\Models\ReachSpecialistVideos;
use App\Models\Specialist_call_schedule;
use App\Models\ReachMember;
use FFMpeg;
use App\Libraries\SmsService;

class SpecialistController extends Controller
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

    /**
     * Show the list of spaecialists.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $specialists = ReachMember::where('is_specialist', 'Y')->orderBy('id', 'desc')->paginate(10);
        return view('specialists.list', compact('specialists'));
    }


    /**
     * Form for adding new specialist.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function add_specialist()
    {
        $members = ReachMember::all();
        $countries = ReachCountry::where('country_status', 'A')->get();
        $jobRoles = ReachJobRole::where('job_role_status', 'A')->get();
        return view('specialists/add_specialist', ['members' =>$members, 'countries' => $countries, 'jobRoles' =>$jobRoles]);
    }

    /**
     * Save specialist to table.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function save_specialist(Request $request)
    {
        $requestData = $request->all();

        $requestData['specialist_dob'] = date('Y-m-d', strtotime($requestData['specialist_dob']));
        if(isset($requestData['specialist_status'])) {
            $requestData['specialist_status'] = 'A';
        } else {
            $requestData['specialist_status'] = 'I';
        }
        $validator = Validator::make($requestData, [
            'specialist_fname' => 'required|string|max:255',
            'specialist_lname' => 'required|string|max:255',
            'specialist_country' => 'required|string|max:255',
            'specialist_title' => 'required',
            'specialist_email' => 'required|email|unique:reach_specialist,specialist_email',
            'specialist_dob' => 'required|date',
        ]);

        
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        } 

        // Handle video upload
        // if ($request->hasFile('specialist_video')) {
        //     $videoPath = $request->file('specialist_video')->store('videos');
        //     $requestData['specialist_video'] = $videoPath;
        // }

        // Handle profile picture upload
        if ($request->hasFile('specialist_profile_picture')) {
            $profilePicturePath = $request->file('specialist_profile_picture')->store('specialist-pictures', 'public');
            $requestData['specialist_profile_picture'] = $profilePicturePath;
        }

        $specialist = ReachSpecialist::create($requestData);

        // Redirect to the specialist list page
        return redirect()->route('specialists')->with('success', 'specialist created successfully!');
    }

    /**
    * Display the form for editing a specific specialist.
    *
    * @param  int  $id
    * @return \Illuminate\Contracts\View\View
    */

    public function edit_specialist($id)
    {
        $members = ReachMember::all();
        $specialist = ReachSpecialist::find($id);
        $countries = ReachCountry::where('country_status', 'A')->get();
        $jobRoles = ReachJobRole::where('job_role_status', 'A')->get();
        //$videoPath = $specialist->specialist_video;

        // Generate a thumbnail
        //$thumbnailPath = $this->generateThumbnail($videoPath);

        // Pass the member data to the view
        return view('specialists/edit_specialist', ['members' =>$members, 'specialist' => $specialist, 'countries' => $countries, 'jobRoles' => $jobRoles]);
    }

    private function generateThumbnail($videoPath) {
        // Generate a unique name for the thumbnail
        $thumbnailName = 'thumbnail_' . Str::random(10) . '.jpg';
        
        // Path where you want to store thumbnails
        $thumbnailDir = 'thumbnails';

        // Generate the thumbnail using FFmpeg
        FFMpeg::fromDisk('local')
                ->open($videoPath)
                ->getFrameFromSeconds(10) // Get the frame at 10 seconds
                ->export()
                ->toDisk('local')
                ->inFormat(new \FFMpeg\Format\Video\X264)
                ->save($thumbnailDir . '/' . $thumbnailName);

        // Return the path to the thumbnail
        return Storage::disk('local')->url($thumbnailDir . '/' . $thumbnailName);
    }

     /**
    * Update the specified specialist details.
    *
    * @param  \Illuminate\Http\Request  $request
    * @param  int  $id
    * @return \Illuminate\Http\RedirectResponse
    */
    public function update_specialist(Request $request, $id) {

        $requestData = $request->all();

        $requestData['specialist_dob'] = date('Y-m-d', strtotime($requestData['specialist_dob']));
        if(isset($requestData['specialist_status'])) {
            $requestData['specialist_status'] = 'A';
        } else {
            $requestData['specialist_status'] = 'I';
        }
        $validator = Validator::make($requestData, [
            'specialist_fname' => 'required|string|max:255',
            'specialist_lname' => 'required|string|max:255',
            'specialist_country' => 'required|string|max:255',
            'specialist_email' => 'required|email|unique:reach_specialist,specialist_email,'.$id,
            'specialist_dob' => 'required|date',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        } 

        // Handle video upload
        // if ($request->hasFile('specialist_video')) {
        //     $videoPath = $request->file('specialist_video')->store('videos');
        //     $requestData['specialist_video'] = $videoPath;
        // }

        // Handle profile picture upload
        if ($request->hasFile('specialist_profile_picture')) {
            $profilePicturePath = $request->file('specialist_profile_picture')->store('specialist-pictures', 'public');
            $requestData['specialist_profile_picture'] = $profilePicturePath;
        }

        $specialist = ReachSpecialist::findOrFail($id);
        $specialist->update($requestData);

        return redirect()->route('specialists')->with('success', 'specialist updated successfully!');
    }

    /**
    * Delete specialist by its Id.
    *
    * @param  int  $id
    * @return \Illuminate\Contracts\View\View
    */
    public function delete_specialist($id)
    {
        // Find the specialist by id
        $specialist = ReachSpecialist::find($id);

        // Check if the specialist exists
        if ($specialist) {
            // If the specialist exists, delete it
            $specialist->delete();
            return redirect()->route('specialists')->with('success', 'Specialist deleted successfully!');
        } else {
            // If the specialist doesn't exist, return an error message or handle it as appropriate
            return redirect()->route('specialists')->with('success', 'Specialist not found!');
        }

    }

    public function uploadChunk(Request $request)
    {
        $file = $request->file('file');
        $filename = $request->input('filename');
        $chunkNumber = $request->input('chunkNumber');
        $totalChunks = $request->input('totalChunks');
        $finalFilePath = "";

        // Store the chunk in the appropriate directory
        //$file->storeAs('temp-upload/' . $filename, "{$filename}_part{$chunkNumber}");
        $file->move(public_path('storage/temp-upload'), "{$filename}_part{$chunkNumber}");


        // Check if all chunks have been uploaded
        if ($chunkNumber == $totalChunks - 1) {
            sleep(2);
            // All chunks have been uploaded, combine them and move to final directory
            $tempPath = public_path('storage/temp-upload');
            $finalPath = public_path('storage/videos/' . $filename);
            $finalFilePath = 'videos/' . $filename;

            for ($i = 0; $i < $totalChunks; $i++) {
                $chunk = "{$tempPath}/{$filename}_part{$i}";
                file_put_contents($finalPath, file_get_contents($chunk), FILE_APPEND);
                unlink($chunk); // Delete the chunk after combining
            }
        }

        // Return response indicating success
        return response()->json(['success' => true, 'finalFilename' => $finalFilePath]);
    }

    public function add_specialist_videos($id)
    {
        $specialist = ReachMember::select('id', 'members_fname', 'members_lname')->find($id);

        return view('specialists/add_videos', ['specialist' => $specialist]);
    }

    public function edit_specialist_video($id)
    {
        $specialists = ReachMember::where('is_specialist', 'Y')->get();
        $videos = ReachSpecialistVideos::find($id);

        return view('specialists/edit_videos', ['specialists' => $specialists, 'videos' => $videos]);
    }

    public function specialists_videos($id)
    {
        $specialist = ReachMember::select('id', 'members_fname', 'members_lname')->find($id);

        $specialistVideos = new \App\Models\ReachSpecialistVideos();
        $videos = $specialistVideos->getAllVideosWithSpecialists($id);

        return view('specialists/videos_list', ['videos' => $videos, 'specialist' => $specialist]);
    }

    public function save_video(Request $request)
    {
        $requestData = $request->all();

        if(isset($requestData['video_status'])) {
            $requestData['video_status'] = 'A';
        } else {
            $requestData['video_status'] = 'I';
        }

        $validator = Validator::make($requestData, [
            'video_title' => 'required|string|max:255',
            'video_sub_title' => 'required|string|max:255',
            'member_id' => 'required',
            'video_file' => function ($attribute, $value, $fail) use ($requestData) {
                if ($requestData['video_file_type'] == 'File') {
                    $validator = Validator::make($requestData, [
                        'finalFilename' => 'required',
                    ]);
                    if ($validator->fails()) {
                        $fail('The video file is required and must be a valid file.');
                    }
                }
            },
            'video_url' => function ($attribute, $value, $fail) use ($requestData) {
                if ($requestData['video_file_type'] == 'Url') {
                    $validator = Validator::make($requestData, [
                        'video_url' => 'required|url',
                    ]);
                    if ($validator->fails()) {
                        $fail('The video URL is required and must be a valid URL.');
                    }
                }
            },
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        } 

        if($requestData['video_file_type']=='File') {
            $video_file = $requestData['finalFilename'];
        } else {
            $video_file = $requestData['video_url'];
        }

        // Handle profile picture upload
        if ($request->hasFile('video_thumb')) {
            $video_thumb_path = $request->file('video_thumb')->store('videos', 'public');
            $video_thumb = $video_thumb_path;
        } else{
            $video_thumb = $requestData['old_video_thumb'];
        }

        $arrayData = [
            'video_title'        => $requestData['video_title'],
            'video_sub_title'    => $requestData['video_sub_title'],
            'member_id'          => $requestData['member_id'],
            'video_description'  => $requestData['video_description'],
            'video_file_type'    => $requestData['video_file_type'],
            'video_file'         => $video_file,
            'video_thumb'        => $video_thumb,
            'video_status'       => $requestData['video_status'],
        ];
        $videos = ReachSpecialistVideos::create($arrayData);

        return redirect()->route('specialists-videos', ['id' => $requestData['member_id']])->with('success', 'Video created successfully!');

    }

    public function update_video(Request $request, $id) {

        $requestData = $request->all();

        if(isset($requestData['video_status'])) {
            $requestData['video_status'] = 'A';
        } else {
            $requestData['video_status'] = 'I';
        }

        $validator = Validator::make($requestData, [
            'video_title' => 'required|string|max:255',
            'video_sub_title' => 'required|string|max:255',
            'member_id' => 'required',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        } 

        if($requestData['video_file_type']=='File') {
            $video_file = $requestData['finalFilename'];
        } else {
            $video_file = $requestData['video_url'];
        }

        // Handle profile picture upload
        if ($request->hasFile('video_thumb')) {
            $video_thumb_path = $request->file('video_thumb')->store('videos', 'public');
            $video_thumb = $video_thumb_path;
        } else{
            $video_thumb = $requestData['old_video_thumb'];
        }

        $arrayData = [
            'video_title'        => $requestData['video_title'],
            'video_sub_title'    => $requestData['video_sub_title'],
            'member_id'          => $requestData['member_id'],
            'video_description'  => $requestData['video_description'],
            'video_file_type'    => $requestData['video_file_type'],
            'video_file'         => $video_file,
            'video_thumb'        => $video_thumb,
            'video_status'       => $requestData['video_status'],
        ];

        $videos = ReachSpecialistVideos::findOrFail($id);
        $videos->update($arrayData);

        return redirect()->route('specialists-videos', ['id' => $requestData['member_id']])->with('success', 'Video updated successfully!');
    }

    public function delete_video($id)
    {
        $video = ReachSpecialistVideos::find($id);

        if ($video) {
            $member_id = $video->member_id;
            $video->delete();
            return redirect()->route('specialists-videos', ['id' => $member_id])->with('success', 'Video deleted successfully!');
        } else {
            return redirect()->route('specialists-videos')->with('error', 'Video not found!');
        }
    }

    public function scheduled_call(Request $request)
    {
        //$query = Specialist_call_schedule::with('member');
        $query = Specialist_call_schedule::with(['member' => function ($q) {
            // Filter the members to include only active members
            $q->where('members_status', 'A');
        }]);
        // Filter by call status
        if ($request->has('call_status') && $request->call_status != '') {
            $query->where('call_status', $request->call_status);
        }

        // Filter by specialist name
        if ($request->has('specialist_name') && $request->specialist_name != '') {
            $query->whereHas('specialist', function ($q) use ($request) {
                $q->whereRaw("CONCAT(members_fname, ' ', members_lname) LIKE ?", ["%{$request->specialist_name}%"]);
            });
        }

        
        $schedule = $query->orderBy('call_scheduled_date', 'desc')->paginate(10);


        return view('specialists/scheduled_call', ['schedule' => $schedule, 'filters' => $request->all()]);
    }

    public function cancel_call(Request $request, $id)
    {
        $schedule = Specialist_call_schedule::find($id);

        if ($schedule) {

            $requestData['call_status'] = 'R';
            $requestData['cancel_reason'] = $request->input('reason');
            $requestData['cancelled_on'] = date("Y-m-d");
            $schedule->update($requestData);

            return response()->json(['success' => true, 'message' => 'Call cancelled successfully!']);
        } else {
            return response()->json(['success' => false, 'message' => 'Page not found!']);
        }
    }

    public function memberDetails($id) {
        $member = ReachMember::find($id);

        if (!$member) {
            return response()->json(['error' => 'Member not found'], 404);
        }

        return response()->json($member);
    }

    public function specialists_history($id)
    {
        $specialist = ReachMember::select('id', 'members_fname', 'members_lname')->find($id);

        $schedule = Specialist_call_schedule::with('member')
                    ->where('specialist_id', $id)
                    ->orderBy('created_at', 'desc')
                    ->paginate(10);

        return view('specialists/specialists_history', ['specialist' => $specialist, 'schedule' => $schedule]);
    }

    public function updateScheduledCall(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'call_scheduled_date' => 'required|date',
            'call_scheduled_time' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ]);
        }

        $schedule = Specialist_call_schedule::with(['member','specialist'])->find($id);

        if ($schedule) {
            $schedule->call_scheduled_date = date("Y-m-d", strtotime($request->call_scheduled_date));
            $schedule->call_scheduled_time = $request->call_scheduled_time;
            $schedule->uk_scheduled_time = $request->call_scheduled_time;
            $schedule->call_scheduled_timezone = 'Europe/London';
            $schedule->save();

            $member = $schedule->member;
            $specialist = $schedule->specialist;

            // Instantiate SmsService
            //$smsService = new SmsService();

            if ($member) {
                $members_phone = $member->members_phone_code . $member->members_phone;
                //$smsService->sendSms($members_phone, 'Your message for member');
            }

            if ($specialist) {
                $specialist_phone = $specialist->members_phone_code . $specialist->members_phone;
                //$smsService->sendSms($specialist_phone, 'Your message for specialist');
            }

            return response()->json([
                'success' => true,
                'message' => 'Call rescheduled successfully!'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Booking not found!'
            ]);
        }
    }

    public function addCallerId()
    {

        $smsService = new SmsService();
        $request = $smsService->addCallerId('+919746530365', 'Prasad');

        //$request = $smsService->listCallerIds();

        return response()->json(['success' => true, 'data' => $request]);
    }

}
