<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

use Illuminate\Support\Str;
use App\Models\ReachClubHouse;
use App\Models\ReachMember;
use App\Models\ReachModerator;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;
use Intervention\Image\ImageManager;


class ClubHouseController extends Controller
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
        $club_house = ReachClubHouse::orderBy('club_order')->paginate(10);
        $members = ReachMember::all();

        return view('club_house/list', ['club_house' => $club_house, 'members' => $members]);
    }


    /**
     * Form for adding new club house.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function add_club_house()
    {
        return view('club_house/add_club_house');
    }

    /**
     * Save club house to table.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function save_club_house(Request $request)
    {
        $requestData = $request->all();

        if (isset($requestData['club_status'])) {
            $requestData['club_status'] = 'A';
        } else {
            $requestData['club_status'] = 'I';
        }
        $requestData['club_name'] = trim(strip_tags($requestData['club_name']));
        $requestData['club_button_name'] = trim(strip_tags($requestData['club_button_name']));
        $requestData['club_order'] = trim(strip_tags($requestData['club_order']));
        $validator = Validator::make($requestData, [
            'club_name' => 'required',
            'club_short_desc' => 'required',
            'club_image' => 'required',
            'club_image_mob' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Handle profile picture upload
        if ($request->hasFile('club_image')) {
            $profilePicturePath = $request->file('club_image')->store('club_house', 'public');
            $requestData['club_image'] = $profilePicturePath;
            $image = Image::read($request->file('club_image'));

            // Resize the image
            $resizedImage = $image->scale(150);
            // Prepare the file name and path for the resized image
            $fileName = pathinfo($request->file('club_image')->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $request->file('club_image')->getClientOriginalExtension();
            $resizedPath = 'club_house/resized/' . $fileName . '_resized.' . $extension;

            // Ensure the 'resized' directory exists
            if (!Storage::exists('public/club_house/resized')) {
                Storage::makeDirectory('public/club_house/resized'); // Create the directory if it doesn't exist
            }

            // Save the resized image
            $resizedImage->save(Storage::path('public/' . $resizedPath));

            // Store the resized image path in the database
            $requestData['club_image_thumb'] = $resizedPath;
        }

        if ($request->hasFile('club_image_mob')) {
            $club_image_mob = $request->file('club_image_mob')->store('club_house', 'public');
            $requestData['club_image_mob'] = $club_image_mob;
        }

        $club_house = ReachClubHouse::create($requestData);

        return redirect()->route('club-house')->with('success', 'Club House created successfully!');
    }

    /**
     * Display the form for editing a specific Club House.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\View\View
     */

    public function edit_club_house($id)
    {
        $club_house = ReachClubHouse::find($id);

        return view('club_house/edit_club_house', ['club_house' => $club_house]);
    }


    /**
     * Update the specified club house details.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update_club_house(Request $request, $id)
    {

        $requestData = $request->all();

        if (isset($requestData['club_status'])) {
            $requestData['club_status'] = 'A';
        } else {
            $requestData['club_status'] = 'I';
        }
        $requestData['club_name'] = trim(strip_tags($requestData['club_name']));
        $requestData['club_button_name'] = trim(strip_tags($requestData['club_button_name']));
        $requestData['club_order'] = trim(strip_tags($requestData['club_order']));
        $validator = Validator::make($requestData, [
            'club_name' => 'required',
            'club_short_desc' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Handle profile picture upload
        if ($request->hasFile('club_image')) {
            $profilePicturePath = $request->file('club_image')->store('club_house', 'public');
            $requestData['club_image'] = $profilePicturePath;
            $image = Image::read($request->file('club_image'));

            // Resize the image
            $resizedImage = $image->scale(150);
            // Prepare the file name and path for the resized image
            $fileName = pathinfo($request->file('club_image')->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $request->file('club_image')->getClientOriginalExtension();
            $resizedPath = 'club_house/resized/' . $fileName . '_resized.' . $extension;

            // Ensure the 'resized' directory exists
            if (!Storage::exists('public/club_house/resized')) {
                Storage::makeDirectory('public/club_house/resized'); // Create the directory if it doesn't exist
            }

            // Save the resized image
            $resizedImage->save(Storage::path('public/' . $resizedPath));

            // Store the resized image path in the database
            $requestData['club_image_thumb'] = $resizedPath;
        }

        if ($request->hasFile('club_image_mob')) {
            $club_image_mob = $request->file('club_image_mob')->store('club_house', 'public');
            $requestData['club_image_mob'] = $club_image_mob;

        }

        $club_house = ReachClubHouse::findOrFail($id);
        $club_house->update($requestData);

        return redirect()->route('club-house')->with('success', 'Club House updated successfully!');
    }

    /**
     * Delete club house by its Id.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\View\View
     */
    public function delete_club_house($id)
    {
        $club_house = ReachClubHouse::find($id);

        if ($club_house) {

            $club_house->delete();
            return redirect()->route('club-house')->with('success', 'Club House deleted successfully!');
        } else {
            return redirect()->route('club-house')->with('success', 'Club House not found!');
        }
    }

    public function addModerator(Request $request, $clubId)
    {
        $validator = Validator::make(
            $request->all(),
            ['member_id' => 'required|exists:reach_members,id',],
            ['member_id.required' => 'Please select moderator.',]
        );

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 200);
        }

        $existingModerator = ReachModerator::where('club_id', $clubId)
            ->where('member_id', $request->member_id)
            ->first();

        if ($existingModerator) {
            return response()->json(['success' => false, 'message' => 'This member is already a moderator for this club.'], 200);
        }

        $moderator = ReachModerator::create([
            'club_id' => $clubId,
            'member_id' => $request->member_id,
        ]);

        return response()->json(['success' => true, 'message' => 'Moderator added successfully.']);
    }

    public function getModerators($clubId)
    {
        $moderators = ReachModerator::with('member')->where('club_id', $clubId)->get();

        return response()->json($moderators);
    }

    public function deleteModerator($id)
    {
        $moderator = ReachModerator::find($id);

        if ($moderator) {
            $requestData['is_deleted'] = 'Y';
            $moderator->update($requestData);

            $moderator->delete();

            return response()->json(['success' => true, 'club_id' => $moderator->club_id, 'message' => 'Moderator deleted successfully.']);
        } else {
            return response()->json(['success' => false, 'message' => 'Moderator not found.'], 404);
        }
    }

}
