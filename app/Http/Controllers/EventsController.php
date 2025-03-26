<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\ReachEvents;
use App\Models\Event\ReachEventImage;

class EventsController extends Controller
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
     * Show the list of events.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $events = ReachEvents::all();
        return view('events/list', ['events' => $events]);
    }


    /**
     * Form for adding new event.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function add_event()
    {
        return view('events/add_event');
    }

    /**
     * Save event to table.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function save_event(Request $request)
    {
        $requestData = $request->all();

        $requestData['event_start_date'] = date('Y-m-d', strtotime(str_replace('-', '/', $requestData['event_start_date'])));;
        $requestData['event_end_date'] = date('Y-m-d', strtotime(str_replace('-', '/', $requestData['event_end_date'])));
        if(isset($requestData['event_status'])) {
            $requestData['event_status'] = 'A';
        } else {
            $requestData['event_status'] = 'I';
        }

        if(isset($requestData['event_members_only'])) {
            $requestData['event_members_only'] = 'Y';
        } else {
            $requestData['event_members_only'] = 'N';
        }
        $validator = Validator::make($requestData, [
            'event_name' => 'required|string|max:255',
            'event_start_date' => 'required|date',
            'event_end_date' => 'required|date',
            'event_allowed_members' => 'required',
            'event_picture' => 'nullable|mimes:jpeg,png,jpg,gif|max:2048'
            // Add more validation rules as needed
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        } 

        // Handle event picture upload
        if ($request->hasFile('event_picture')) {
            $profilePicturePath = $request->file('event_picture')->store('event-pictures', 'public');
            $requestData['event_picture'] = $profilePicturePath;
        }
        $event = ReachEvents::create($requestData);

        // Redirect to the event list page
        return redirect()->route('events')->with('success', 'Event created successfully!');
    }

    /**
    * Display the form for editing a specific event.
    *
    * @param  int  $id
    * @return \Illuminate\Contracts\View\View
    */

    public function edit_event($id)
    {
        $event = ReachEvents::find($id);

        // Pass the member data to the view
        return view('events/edit_event', compact('event'));
    }

     /**
    * Update the specified event details.
    *
    * @param  \Illuminate\Http\Request  $request
    * @param  int  $id
    * @return \Illuminate\Http\RedirectResponse
    */
    public function update_event(Request $request, $id) {

        $requestData = $request->all();

        $requestData['event_start_date'] = date('Y-m-d', strtotime(str_replace('-', '/', $requestData['event_start_date'])));;
        $requestData['event_end_date'] = date('Y-m-d', strtotime(str_replace('-', '/', $requestData['event_end_date'])));
        if(isset($requestData['event_status'])) {
            $requestData['event_status'] = 'A';
        } else {
            $requestData['event_status'] = 'I';
        }
        if(isset($requestData['event_members_only'])) {
            $requestData['event_members_only'] = 'Y';
        } else {
            $requestData['event_members_only'] = 'N';
        }
        $validator = Validator::make($requestData, [
            'event_name' => 'required|string|max:255',
            'event_start_date' => 'required|date',
            'event_end_date' => 'required|date',
            'event_allowed_members' => 'required',
            'event_picture' => 'nullable|mimes:jpeg,png,jpg,gif|max:2048'
            // Add more validation rules as needed
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        } 

        // Handle event picture upload
        if ($request->hasFile('event_picture')) {
            $profilePicturePath = $request->file('event_picture')->store('event-pictures', 'public');
            $requestData['event_picture'] = $profilePicturePath;
        }

        $event = ReachEvents::findOrFail($id);
        $event->update($requestData);

        return redirect()->route('events')->with('success', 'Event updated successfully!');
    }

    /**
    * Delete Event by its Id.
    *
    * @param  int  $id
    * @return \Illuminate\Contracts\View\View
    */
    public function delete_event($id)
    {
        // Find the event by id
        $event = ReachEvents::find($id);

        // Check if the event exists
        if ($event) {
            // If the event exists, delete it
            $event->delete();
            return redirect()->route('events')->with('success', 'Event deleted successfully!');
        } else {
            // If the event doesn't exist, return an error message or handle it as appropriate
            return redirect()->route('events')->with('success', 'Event not found!');
        }

    }

   
}
