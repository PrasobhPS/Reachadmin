<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

use App\Models\JobDetails\ReachJobRole;
use App\Models\JobDetails\ReachBoatType;
use App\Models\JobDetails\ReachJobDuration;
use App\Models\JobDetails\ReachBoatLocation;
use App\Models\ReachLanguages;
use App\Models\ReachQualifications;
use App\Models\ReachExperience;
use App\Models\ReachAvailability;
use App\Models\ReachPositions;
use App\Models\ReachSalaryExpectations;
use App\Models\ReachVesselType;
use App\Models\ReachCountry;
use App\Models\ReachJob;
use App\Models\ReachEmployeeDetails;
use App\Models\ReachVisa;
use Illuminate\Validation\Rule;
use App\Models\ChatRequests;
use Illuminate\Support\Facades\DB;


class SettingsController extends Controller
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
     * Show the list job roles.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function role_list()
    {
        $job_roles = ReachJobRole::all();
        $jobIds = ReachJob::pluck('job_role')->toArray();
        return view('settings/role_list', ['job_roles' => $job_roles, 'jobIds' => $jobIds]);
    }


    /**
     * Form for adding new job role.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function role_add()
    {
        return view('settings/add_role');
    }

    /**
     * Save job role to table.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function save_job_role(Request $request)
    {
        $requestData = $request->all();

        if (isset($requestData['job_role_status'])) {
            $requestData['job_role_status'] = 'A';
        } else {
            $requestData['job_role_status'] = 'I';
        }
        $requestData['job_role'] = trim(strip_tags($requestData['job_role']));
        /*$validator = Validator::make($requestData, [
            'job_role' => 'required|string|max:255',
            // Add more validation rules as needed
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }*/
        $validator = Validator::make($requestData, [
            'job_role' => 'required|string|max:255|unique:reach_job_roles,job_role',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $role = ReachJobRole::create($requestData);
        // Redirect to the site page list page
        return redirect()->route('job-role')->with('success', 'Role created successfully!');
    }

    /**
     * Display the form for editing a specific boat.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\View\View
     */

    public function edit_job_role($id)
    {
        $job_role = ReachJobRole::find($id);
        $jobIds = ReachJob::pluck('job_role')->toArray();
        // Pass the member data to the view
        return view('settings/edit_job_role', ['job_role' => $job_role, 'jobIds' => $jobIds]);
    }

    /**
     * Update the specified job type details.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update_job_role(Request $request, $id)
    {

        $requestData = $request->all();

        if (isset($requestData['job_role_status'])) {
            $requestData['job_role_status'] = 'A';
        } else {
            $requestData['job_role_status'] = 'I';
        }
        $requestData['job_role'] = trim(strip_tags($requestData['job_role']));
        // $validator = Validator::make($requestData, [
        //     'job_role' => 'required|string|max:255',
        //     // Add more validation rules as needed
        // ]);
        // if ($validator->fails()) {
        //     return redirect()->back()->withErrors($validator)->withInput();
        // }
        $validator = Validator::make($requestData, [
            'job_role' => [
                'required',
                'string',
                'max:255',
                'regex:/^[^<>]*$/', // Prevents HTML tags
                Rule::unique('reach_job_roles', 'job_role')->ignore($id),
            ],
        ], [
            'job_role.regex' => 'The job role field contains invalid characters (HTML tags are not allowed).',
            'job_role.unique' => 'This job role already exists. Please enter a different one.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $role = ReachJobRole::findOrFail($id);
        $role->update($requestData);

        return redirect()->route('job-role')->with('success', 'Job role updated successfully!');
    }

    /**
     * Delete job role by its Id.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\View\View
     */
    public function delete_job_role($id)
    {
        // Find the site page by id
        $role = ReachJobRole::find($id);

        // Check if the site page exists
        if ($role) {
            // If the site page exists, delete it
            $role->delete();
            return redirect()->route('job-role')->with('success', 'Job Role deleted successfully!');
        } else {
            // If the site page doesn't exist, return an error message or handle it as appropriate
            return redirect()->route('job-role')->with('success', 'Page not found!');
        }
    }


    /**
     * Show the list of boat types.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function boat_type_list()
    {
        $boat_types = ReachBoatType::all();
        $boat_type_id = ReachJob::pluck('boat_type')->toArray();
        return view('settings/boat_type_list', ['boat_types' => $boat_types, 'boattypeIds' => $boat_type_id]);
    }


    /**
     * Form for adding new boat type.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function boat_type_add()
    {
        return view('settings/add_boat_type');
    }

    /**
     * Save boat type to table.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function save_boat_type(Request $request)
    {
        $requestData = $request->all();

        if (isset($requestData['boat_type_status'])) {
            $requestData['boat_type_status'] = 'A';
        } else {
            $requestData['boat_type_status'] = 'I';
        }
        $requestData['boat_type'] = trim(strip_tags($requestData['boat_type']));
        // $validator = Validator::make($requestData, [
        //     'boat_type' => 'required|string|max:255',
        //     // Add more validation rules as needed
        // ]);
        // if ($validator->fails()) {
        //     return redirect()->back()->withErrors($validator)->withInput();
        // }
        $validator = Validator::make($requestData, [
            'boat_type' => 'required|string|max:255|unique:reach_boat_type,boat_type',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $boat = ReachBoatType::create($requestData);
        // Redirect to the site page list page
        return redirect()->route('boat-type')->with('success', 'Boat type created successfully!');
    }

    /**
     * Display the form for editing a specific boat.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\View\View
     */

    public function edit_boat_type($id)
    {
        $boat_type = ReachBoatType::find($id);
        // Pass the member data to the view
        $boat_type_id = ReachJob::pluck('boat_type')->toArray();
        return view('settings/edit_boat_type', ['boat_type' => $boat_type, 'boat_type_id' => $boat_type_id]);
    }

    /**
     * Update the specified boat type details.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update_boat_type(Request $request, $id)
    {

        $requestData = $request->all();

        if (isset($requestData['boat_type_status'])) {
            $requestData['boat_type_status'] = 'A';
        } else {
            $requestData['boat_type_status'] = 'I';
        }
        $requestData['boat_type'] = trim(strip_tags($requestData['boat_type']));
        // $validator = Validator::make($requestData, [
        //     'boat_type' => 'required|string|max:255',
        //     // Add more validation rules as needed
        // ]);
        // if ($validator->fails()) {
        //     return redirect()->back()->withErrors($validator)->withInput();
        // }
        $validator = Validator::make($requestData, [
            'boat_type' => [
                'required',
                'string',
                'max:255',
                'regex:/^[^<>]*$/', // Prevents HTML tags
                Rule::unique('reach_boat_type', 'boat_type')->ignore($id),
            ],
        ], [
            'boat_type.regex' => 'The Boat Type field contains invalid characters (HTML tags are not allowed).',
            'boat_type.unique' => 'This Boat Type  already exists. Please enter a different one.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $boat = ReachBoatType::findOrFail($id);
        $boat->update($requestData);

        return redirect()->route('boat-type')->with('success', 'Boat Type updated successfully!');
    }

    /**
     * Delete job role by its Id.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\View\View
     */
    public function delete_boat_type($id)
    {
        // Find the site page by id
        $role = ReachBoatType::find($id);

        // Check if the site page exists
        if ($role) {
            // If the site page exists, delete it
            $role->delete();
            return redirect()->route('boat-type')->with('success', 'Boat type deleted successfully!');
        } else {
            // If the site page doesn't exist, return an error message or handle it as appropriate
            return redirect()->route('boat-type')->with('success', 'Page not found!');
        }
    }

    /**
     * Show the list job durations.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function duration_list()
    {
        $job_durations = ReachJobDuration::all();
        $duration_id = ReachJob::pluck('job_duration')->toArray();
        return view('settings/duration_list', ['job_durations' => $job_durations, 'duration_id' => $duration_id]);
    }


    /**
     * Form for adding new job duration.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function duration_add()
    {
        return view('settings/add_duration');
    }

    /**
     * Save job duration to table.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function save_job_duration(Request $request)
    {
        $requestData = $request->all();

        if (isset($requestData['job_duration_status'])) {
            $requestData['job_duration_status'] = 'A';
        } else {
            $requestData['job_duration_status'] = 'I';
        }
        $requestData['job_duration'] = trim(strip_tags($requestData['job_duration']));
        // $validator = Validator::make($requestData, [
        //     'job_duration' => 'required|string|max:255',

        // ]);
        // if ($validator->fails()) {
        //     return redirect()->back()->withErrors($validator)->withInput();
        // }
        $validator = Validator::make($requestData, [
            'job_duration' => 'required|string|max:255|unique:reach_job_duration,job_duration',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $duration = ReachJobDuration::create($requestData);
        // Redirect to the site page list page
        return redirect()->route('job-duration')->with('success', 'Duration created successfully!');
    }

    /**
     * Display the form for editing a specific boat.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\View\View
     */

    public function edit_job_duration($id)
    {
        $job_duration = ReachJobDuration::find($id);
        $duration_id = ReachJob::pluck('job_duration')->toArray();
        // Pass the member data to the view
        return view('settings/edit_job_duration', ['job_duration' => $job_duration, 'duration_id' => $duration_id]);
    }

    /**
     * Update the specified job type details.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update_job_duration(Request $request, $id)
    {

        $requestData = $request->all();

        if (isset($requestData['job_duration_status'])) {
            $requestData['job_duration_status'] = 'A';
        } else {
            $requestData['job_duration_status'] = 'I';
        }
        $requestData['job_duration'] = trim(strip_tags($requestData['job_duration']));
        // $validator = Validator::make($requestData, [
        //     'job_duration' => 'required|string|max:255',
        //     // Add more validation rules as needed
        // ]);
        // if ($validator->fails()) {
        //     return redirect()->back()->withErrors($validator)->withInput();
        // }
        $validator = Validator::make($requestData, [
            'job_duration' => [
                'required',
                'string',
                'max:255',
                'regex:/^[^<>]*$/', // Prevents HTML tags
                Rule::unique('reach_job_duration', 'job_duration')->ignore($id),
            ],
        ], [
            'job_duration.regex' => 'The Job Duration field contains invalid characters (HTML tags are not allowed).',
            'job_duration.unique' => 'This Job Duration  already exists. Please enter a different one.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $duration = ReachJobduration::findOrFail($id);
        $duration->update($requestData);

        return redirect()->route('job-duration')->with('success', 'Job duration updated successfully!');
    }

    /**
     * Delete job duration by its Id.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\View\View
     */
    public function delete_job_duration($id)
    {
        // Find the site page by id
        $duration = ReachJobduration::find($id);

        // Check if the site page exists
        if ($duration) {
            // If the site page exists, delete it
            $duration->delete();
            return redirect()->route('job-duration')->with('success', 'Job duration deleted successfully!');
        } else {
            // If the site page doesn't exist, return an error message or handle it as appropriate
            return redirect()->route('job-duration')->with('success', 'Page not found!');
        }
    }

    /**
     * Show the list of boat locations.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function boat_location_list()
    {
        $boat_locations = ReachBoatLocation::all();
        $boat_location_id = ReachJob::pluck('job_location')->toArray();
        return view('settings/boat_location_list', ['boat_locations' => $boat_locations, 'boat_location_id' => $boat_location_id]);
    }


    /**
     * Form for adding new boat location.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function boat_location_add()
    {
        return view('settings/add_boat_location');
    }

    /**
     * Save boat location to table.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function save_boat_location(Request $request)
    {
        $requestData = $request->all();

        if (isset($requestData['boat_location_status'])) {
            $requestData['boat_location_status'] = 'A';
        } else {
            $requestData['boat_location_status'] = 'I';
        }
        $requestData['boat_location'] = trim(strip_tags($requestData['boat_location']));
        // $validator = Validator::make($requestData, [
        //     'boat_location' => 'required|string|max:255',

        // ]);
        // if ($validator->fails()) {
        //     return redirect()->back()->withErrors($validator)->withInput();
        // }
        $validator = Validator::make($requestData, [
            'boat_location' => 'required|string|max:255|unique:reach_boat_location,boat_location',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $boat = ReachBoatLocation::create($requestData);
        // Redirect to the site page list page
        return redirect()->route('boat-location')->with('success', 'Boat location created successfully!');
    }

    /**
     * Display the form for editing a specific boat.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\View\View
     */

    public function edit_boat_location($id)
    {
        $boat_location = ReachBoatLocation::find($id);
        $boat_location_id = ReachJob::pluck('job_location')->toArray();
        return view('settings/edit_boat_location', ['boat_location' => $boat_location, 'boat_location_id' => $boat_location_id]);
    }

    /**
     * Update the specified boat location details.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update_boat_location(Request $request, $id)
    {

        $requestData = $request->all();

        if (isset($requestData['boat_location_status'])) {
            $requestData['boat_location_status'] = 'A';
        } else {
            $requestData['boat_location_status'] = 'I';
        }
        $requestData['boat_location'] = trim(strip_tags($requestData['boat_location']));
        // $validator = Validator::make($requestData, [
        //     'boat_location' => 'required|string|max:255',
        //   ]);
        // if ($validator->fails()) {
        //     return redirect()->back()->withErrors($validator)->withInput();
        // }
        $validator = Validator::make($requestData, [
            'boat_location' => [
                'required',
                'string',
                'max:255',
                'regex:/^[^<>]*$/', // Prevents HTML tags
                Rule::unique('reach_boat_location', 'boat_location')->ignore($id),
            ],
        ], [
            'boat_location.regex' => 'The Boat Location field contains invalid characters (HTML tags are not allowed).',
            'boat_location.unique' => 'This Boat Location  already exists. Please enter a different one.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $boat = ReachBoatLocation::findOrFail($id);
        $boat->update($requestData);

        return redirect()->route('boat-location')->with('success', 'Boat location updated successfully!');
    }

    /**
     * Delete boat location by its Id.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\View\View
     */
    public function delete_boat_location($id)
    {
        // Find the site page by id
        $boat = ReachBoatLocation::find($id);

        // Check if the site page exists
        if ($boat) {
            // If the site page exists, delete it
            $boat->delete();
            return redirect()->route('boat-location')->with('success', 'Boat location deleted successfully!');
        } else {
            // If the site page doesn't exist, return an error message or handle it as appropriate
            return redirect()->route('boat-location')->with('success', 'Page not found!');
        }
    }

    /**
     * Show the list of Languages.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function languages_list()
    {
        $languages = ReachLanguages::all();
        $employee_languages = ReachEmployeeDetails::pluck('employee_languages')->toArray();
        $used_language_ids = collect($employee_languages)
            ->flatMap(function ($item) {
                return explode(',', $item);
            })
            ->map('trim')
            ->unique()
            ->values()
            ->toArray();

        return view('settings/languages_list', ['languages' => $languages, 'used_language_ids' => $used_language_ids]);
    }

    /**
     * Form for adding new Languages.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function languages_add()
    {
        return view('settings/add_languages');
    }

    /**
     * Save data to Languages table.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function save_languages(Request $request)
    {
        $requestData = $request->all();

        if (isset($requestData['language_status'])) {
            $requestData['language_status'] = 'A';
        } else {
            $requestData['language_status'] = 'I';
        }
        $requestData['language_name'] = trim(strip_tags($requestData['language_name']));
        // $validator = Validator::make($requestData, [
        //     'language_name' => 'required|string|max:255',
        //  ]);
        // if ($validator->fails()) {
        //     return redirect()->back()->withErrors($validator)->withInput();
        // }
        $validator = Validator::make($requestData, [
            'language_name' => 'required|string|max:255|unique:reach_languages,language_name',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $boat = ReachLanguages::create($requestData);
        // Redirect to the site page list page
        return redirect()->route('languages')->with('success', 'Language created successfully!');
    }

    /**
     * Display the form for editing a language.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\View\View
     */

    public function edit_language($id)
    {
        $language = ReachLanguages::find($id);
        $employee_languages = ReachEmployeeDetails::pluck('employee_languages')->toArray();
        $used_language_ids = collect($employee_languages)
            ->flatMap(function ($item) {
                return explode(',', $item);
            })
            ->map('trim')
            ->unique()
            ->values()
            ->toArray();
        return view('settings/edit_language', ['language' => $language, 'used_language_ids' => $used_language_ids]);
    }

    /**
     * Update the specified language details.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update_language(Request $request, $id)
    {

        $requestData = $request->all();

        if (isset($requestData['language_status'])) {
            $requestData['language_status'] = 'A';
        } else {
            $requestData['language_status'] = 'I';
        }
        $requestData['language_name'] = trim(strip_tags($requestData['language_name']));
        // $validator = Validator::make($requestData, [
        //     'language_name' => 'required|string|max:255',
        //     // Add more validation rules as needed
        // ]);
        // if ($validator->fails()) {
        //     return redirect()->back()->withErrors($validator)->withInput();
        // }
        $validator = Validator::make($requestData, [
            'language_name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[^<>]*$/', // Prevents HTML tags
                Rule::unique('reach_languages', 'language_name')->ignore($id, 'lang_id'),
            ],
        ], [
            'language_name.regex' => 'The Language field contains invalid characters (HTML tags are not allowed).',
            'language_name.unique' => 'This Language  already exists. Please enter a different one.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $boat = ReachLanguages::findOrFail($id);
        $boat->update($requestData);

        return redirect()->route('languages')->with('success', 'Language updated successfully!');
    }

    /**
     * Delete language by its Id.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\View\View
     */
    public function delete_language($id)
    {
        // Find the site page by id
        $boat = ReachLanguages::find($id);

        // Check if the site page exists
        if ($boat) {
            // If the site page exists, delete it
            $boat->delete();
            return redirect()->route('languages')->with('success', 'Language deleted successfully!');
        } else {
            // If the site page doesn't exist, return an error message or handle it as appropriate
            return redirect()->route('languages')->with('success', 'Page not found!');
        }
    }

    /**
     * Show the list of Qualifications.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function qualifications_list()
    {
        $qualifications = ReachQualifications::all();
        $employee_qualification = ReachEmployeeDetails::pluck('employee_qualification')->toArray();
        $used_qualification_ids = collect($employee_qualification)
            ->flatMap(function ($item) {
                return explode(',', $item);
            })
            ->map('trim')
            ->unique()
            ->values()
            ->toArray();
        return view('settings/qualifications_list', ['qualifications' => $qualifications, 'employee_qualification' => $used_qualification_ids]);
    }

    /**
     * Form for adding new qualifications.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function qualifications_add()
    {
        return view('settings/add_qualifications');
    }

    /**
     * Save data to qualifications table.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function save_qualifications(Request $request)
    {
        $requestData = $request->all();

        if (isset($requestData['qualification_status'])) {
            $requestData['qualification_status'] = 'A';
        } else {
            $requestData['qualification_status'] = 'I';
        }
        $requestData['qualification_name'] = trim(strip_tags($requestData['qualification_name']));
        // $validator = Validator::make($requestData, [
        //     'qualification_name' => 'required|string|max:255',

        // ]);
        // if ($validator->fails()) {
        //     return redirect()->back()->withErrors($validator)->withInput();
        // }
        $validator = Validator::make($requestData, [
            'qualification_name' => 'required|string|max:255|unique:reach_qualifications,qualification_name',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $boat = ReachQualifications::create($requestData);
        // Redirect to the site page list page
        return redirect()->route('qualifications')->with('success', 'Qualification created successfully!');
    }

    /**
     * Display the form for editing a qualification.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\View\View
     */

    public function edit_qualification($id)
    {
        $qualification = ReachQualifications::find($id);
        $employee_qualification = ReachEmployeeDetails::pluck('employee_qualification')->toArray();
        $used_qualification_ids = collect($employee_qualification)
            ->flatMap(function ($item) {
                return explode(',', $item);
            })
            ->map('trim')
            ->unique()
            ->values()
            ->toArray();
        return view('settings/edit_qualification', ['qualification' => $qualification, 'qualification_ids' => $used_qualification_ids]);
    }

    /**
     * Update the specified qualification details.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update_qualification(Request $request, $id)
    {

        $requestData = $request->all();

        if (isset($requestData['qualification_status'])) {
            $requestData['qualification_status'] = 'A';
        } else {
            $requestData['qualification_status'] = 'I';
        }
        $requestData['qualification_name'] = trim(strip_tags($requestData['qualification_name']));
        // $validator = Validator::make($requestData, [
        //     'qualification_name' => 'required|string|max:255',
        //     // Add more validation rules as needed
        // ]);
        // if ($validator->fails()) {
        //     return redirect()->back()->withErrors($validator)->withInput();
        // }
        $validator = Validator::make($requestData, [
            'qualification_name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[^<>]*$/', // Prevents HTML tags
                Rule::unique('reach_qualifications', 'qualification_name')->ignore($id, 'qualification_id'),
            ],
        ], [
            'qualification_name.regex' => 'The Qualification field contains invalid characters (HTML tags are not allowed).',
            'qualification_name.unique' => 'This Qualification  already exists. Please enter a different one.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $boat = ReachQualifications::findOrFail($id);
        $boat->update($requestData);

        return redirect()->route('qualifications')->with('success', 'Qualifications updated successfully!');
    }

    /**
     * Delete qualification by its Id.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\View\View
     */
    public function delete_qualification($id)
    {
        // Find the site page by id
        $boat = ReachQualifications::find($id);

        // Check if the site page exists
        if ($boat) {
            // If the site page exists, delete it
            $boat->delete();
            return redirect()->route('qualifications')->with('success', 'Qualification deleted successfully!');
        } else {
            // If the site page doesn't exist, return an error message or handle it as appropriate
            return redirect()->route('qualifications')->with('success', 'Page not found!');
        }
    }

    /**
     * Show the list of Experience.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function experience_list()
    {
        $experience = ReachExperience::all();
        $employee_experience = ReachEmployeeDetails::pluck('employee_experience')->toArray();
        $used_employee_ids = collect($employee_experience)
            ->flatMap(function ($item) {
                return explode(',', $item);
            })
            ->map('trim')
            ->unique()
            ->values()
            ->toArray();
        return view('settings/experience_list', ['experience' => $experience, 'employee_experience_id' => $used_employee_ids]);
    }

    /**
     * Form for adding new experience.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function experience_add()
    {
        return view('settings/add_experience');
    }

    /**
     * Save data to experience table.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function save_experience(Request $request)
    {
        $requestData = $request->all();

        if (isset($requestData['experience_status'])) {
            $requestData['experience_status'] = 'A';
        } else {
            $requestData['experience_status'] = 'I';
        }
        $requestData['experience_name'] = trim(strip_tags($requestData['experience_name']));
        // $validator = Validator::make($requestData, [
        //     'experience_name' => 'required|string|max:255',
        //     // Add more validation rules as needed
        // ]);
        // if ($validator->fails()) {
        //     return redirect()->back()->withErrors($validator)->withInput();
        // }
        $validator = Validator::make($requestData, [
            'experience_name' => 'required|string|max:255|unique:reach_experience,experience_name',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $boat = ReachExperience::create($requestData);
        // Redirect to the site page list page
        return redirect()->route('experience')->with('success', 'Experience created successfully!');
    }

    /**
     * Display the form for editing a experience.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\View\View
     */

    public function edit_experience($id)
    {
        $experience = ReachExperience::find($id);
        $employee_experience = ReachEmployeeDetails::pluck('employee_experience')->toArray();
        $used_employee_ids = collect($employee_experience)
            ->flatMap(function ($item) {
                return explode(',', $item);
            })
            ->map('trim')
            ->unique()
            ->values()
            ->toArray();
        return view('settings/edit_experience', ['experience' => $experience, 'employee_experience_id' => $used_employee_ids]);
    }

    /**
     * Update the specified experience details.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update_experience(Request $request, $id)
    {

        $requestData = $request->all();

        if (isset($requestData['experience_status'])) {
            $requestData['experience_status'] = 'A';
        } else {
            $requestData['experience_status'] = 'I';
        }
        $requestData['experience_name'] = trim(strip_tags($requestData['experience_name']));
        // $validator = Validator::make($requestData, [
        //     'experience_name' => 'required|string|max:255',
        //     // Add more validation rules as needed
        // ]);
        // if ($validator->fails()) {
        //     return redirect()->back()->withErrors($validator)->withInput();
        // }
        $validator = Validator::make($requestData, [
            'experience_name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[^<>]*$/', // Prevents HTML tags
                Rule::unique('reach_experience', 'experience_name')->ignore($id, 'experience_id'),
            ],
        ], [
            'experience_name.regex' => 'The Experience field contains invalid characters (HTML tags are not allowed).',
            'experience_name.unique' => 'This Experience  already exists. Please enter a different one.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $boat = ReachExperience::findOrFail($id);
        $boat->update($requestData);

        return redirect()->route('experience')->with('success', 'Experience updated successfully!');
    }

    /**
     * Delete experience by its Id.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\View\View
     */
    public function delete_experience($id)
    {
        $experience = ReachExperience::find($id);

        if ($experience) {
            $experience->delete();
            return redirect()->route('experience')->with('success', 'Experience deleted successfully!');
        } else {
            return redirect()->route('experience')->with('success', 'Page not found!');
        }
    }

    /**
     * Show the list of availability.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function availability_list()
    {
        $availability = ReachAvailability::all();
        $employee_availability = ReachEmployeeDetails::pluck('employee_avilable')->toArray();
        $used_availability_ids = collect($employee_availability)
            ->flatMap(function ($item) {
                return explode(',', $item);
            })
            ->map('trim')
            ->unique()
            ->values()
            ->toArray();
        return view('settings/availability_list', ['availability' => $availability, 'employee_availability_id' => $used_availability_ids]);
    }

    /**
     * Form for adding new availability.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function availability_add()
    {
        return view('settings/add_availability');
    }

    /**
     * Save data to availability table.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function save_availability(Request $request)
    {
        $requestData = $request->all();

        if (isset($requestData['availability_status'])) {
            $requestData['availability_status'] = 'A';
        } else {
            $requestData['availability_status'] = 'I';
        }
        $requestData['availability_name'] = trim(strip_tags($requestData['availability_name']));
        $validator = Validator::make($requestData, [
            'availability_name' => 'required|string|max:255|unique:reach_current_availability,availability_name',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $availability = ReachAvailability::create($requestData);

        return redirect()->route('availability')->with('success', 'Availability created successfully!');
    }

    /**
     * Display the form for editing a availability.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\View\View
     */

    public function edit_availability($id)
    {
        $availability = ReachAvailability::find($id);
        $employee_availability = ReachEmployeeDetails::pluck('employee_avilable')->toArray();
        $used_availability_ids = collect($employee_availability)
            ->flatMap(function ($item) {
                return explode(',', $item);
            })
            ->map('trim')
            ->unique()
            ->values()
            ->toArray();
        return view('settings/edit_availability', ['availability' => $availability, 'employee_availability_id' => $used_availability_ids]);
    }

    /**
     * Update the specified availability details.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update_availability(Request $request, $id)
    {

        $requestData = $request->all();

        if (isset($requestData['availability_status'])) {
            $requestData['availability_status'] = 'A';
        } else {
            $requestData['availability_status'] = 'I';
        }
        $requestData['availability_name'] = trim(strip_tags($requestData['availability_name']));
        // $validator = Validator::make($requestData, [
        //     'availability_name' => 'required|string|max:255',
        // ]);
        // if ($validator->fails()) {
        //     return redirect()->back()->withErrors($validator)->withInput();
        // }
        $validator = Validator::make($requestData, [
            'availability_name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[^<>]*$/', // Prevents HTML tags
                Rule::unique('reach_current_availability', 'availability_name')->ignore($id, 'availability_id'),
            ],
        ], [
            'availability_name.regex' => 'The Availability name field contains invalid characters (HTML tags are not allowed).',
            'availability_name.unique' => 'This Availability name  already exists. Please enter a different one.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $availability = ReachAvailability::findOrFail($id);
        $availability->update($requestData);

        return redirect()->route('availability')->with('success', 'Availability updated successfully!');
    }

    /**
     * Delete availability by its Id.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\View\View
     */
    public function delete_availability($id)
    {
        $availability = ReachAvailability::find($id);

        if ($availability) {
            $availability->delete();
            return redirect()->route('availability')->with('success', 'Availability deleted successfully!');
        } else {
            return redirect()->route('availability')->with('success', 'Page not found!');
        }
    }

    /**
     * Show the list of positions.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function positions_list()
    {
        $positions = ReachPositions::all();
        $employee_position = ReachEmployeeDetails::pluck('employee_position')->toArray();
        $used_position_ids = collect($employee_position)
            ->flatMap(function ($item) {
                return explode(',', $item);
            })
            ->map('trim')
            ->unique()
            ->values()
            ->toArray();
        return view('settings/positions_list', ['positions' => $positions, 'employee_position' => $used_position_ids]);
    }

    /**
     * Form for adding new positions.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function positions_add()
    {
        return view('settings/add_positions');
    }

    /**
     * Save data to positions table.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function save_positions(Request $request)
    {
        $requestData = $request->all();

        if (isset($requestData['position_status'])) {
            $requestData['position_status'] = 'A';
        } else {
            $requestData['position_status'] = 'I';
        }
        $requestData['position_name'] = trim(strip_tags($requestData['position_name']));
        // $validator = Validator::make($requestData, [
        //     'position_name' => 'required|string|max:255',
        // ]);
        // if ($validator->fails()) {
        //     return redirect()->back()->withErrors($validator)->withInput();
        // }
        $validator = Validator::make($requestData, [
            'position_name' => 'required|string|max:255|unique:reach_positions,position_name',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $position = ReachPositions::create($requestData);

        return redirect()->route('positions')->with('success', 'Position created successfully!');
    }

    /**
     * Display the form for editing a positions.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\View\View
     */

    public function edit_positions($id)
    {
        $positions = ReachPositions::find($id);
        $employee_position = ReachEmployeeDetails::pluck('employee_position')->toArray();
        $used_position_ids = collect($employee_position)
            ->flatMap(function ($item) {
                return explode(',', $item);
            })
            ->map('trim')
            ->unique()
            ->values()
            ->toArray();
        return view('settings/edit_positions', ['positions' => $positions, 'employee_position' => $used_position_ids]);
    }

    /**
     * Update the specified positions details.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update_positions(Request $request, $id)
    {

        $requestData = $request->all();

        if (isset($requestData['position_status'])) {
            $requestData['position_status'] = 'A';
        } else {
            $requestData['position_status'] = 'I';
        }
        $requestData['position_name'] = trim(strip_tags($requestData['position_name']));
        // $validator = Validator::make($requestData, [
        //     'position_name' => 'required|string|max:255',
        // ]);
        // if ($validator->fails()) {
        //     return redirect()->back()->withErrors($validator)->withInput();
        // }
        $validator = Validator::make($requestData, [
            'position_name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[^<>]*$/', // Prevents HTML tags
                Rule::unique('reach_positions', 'position_name')->ignore($id, 'position_id'),
            ],
        ], [
            'position_name.regex' => 'The Position field contains invalid characters (HTML tags are not allowed).',
            'position_name.unique' => 'This Position name  already exists. Please enter a different one.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $position = ReachPositions::findOrFail($id);
        $position->update($requestData);

        return redirect()->route('positions')->with('success', 'Position updated successfully!');
    }

    /**
     * Delete positions by its Id.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\View\View
     */
    public function delete_positions($id)
    {
        $position = ReachPositions::find($id);

        if ($position) {
            $position->delete();
            return redirect()->route('positions')->with('success', 'Position deleted successfully!');
        } else {
            return redirect()->route('positions')->with('success', 'Page not found!');
        }
    }

    /**
     * Show the list of salary expectations.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function salary_expectations_list()
    {
        $expectations = ReachSalaryExpectations::all();
        $employee_expectation = ReachEmployeeDetails::pluck('employee_salary_expection')->toArray();
        $used_expectation_ids = collect($employee_expectation)
            ->flatMap(function ($item) {
                return explode(',', $item);
            })
            ->map('trim')
            ->unique()
            ->values()
            ->toArray();
        return view('settings/salary_expectations_list', ['expectations' => $expectations, 'employee_salary_expection' => $used_expectation_ids]);
    }

    /**
     * Form for adding new salary expectations.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function salary_expectations_add()
    {
        return view('settings/add_salary_expectations');
    }

    /**
     * Save data to salary expectations table.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function save_salary_expectations(Request $request)
    {
        $requestData = $request->all();

        if (isset($requestData['expectation_status'])) {
            $requestData['expectation_status'] = 'A';
        } else {
            $requestData['expectation_status'] = 'I';
        }
        $requestData['expectation_name'] = trim(strip_tags($requestData['expectation_name']));
        // $validator = Validator::make($requestData, [
        //     'expectation_name' => 'required|string|max:255',
        // ]);
        // if ($validator->fails()) {
        //     return redirect()->back()->withErrors($validator)->withInput();
        // }
        $validator = Validator::make($requestData, [
            'expectation_name' => 'required|string|max:255|unique:reach_salary_expectations,expectation_name',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $expectation = ReachSalaryExpectations::create($requestData);

        return redirect()->route('salary-expectations')->with('success', 'Salary Expectation created successfully!');
    }

    /**
     * Display the form for editing a salary expectations.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\View\View
     */

    public function edit_salary_expectations($id)
    {
        $expectations = ReachSalaryExpectations::find($id);
        $employee_expectation = ReachEmployeeDetails::pluck('employee_salary_expection')->toArray();
        $used_expectation_ids = collect($employee_expectation)
            ->flatMap(function ($item) {
                return explode(',', $item);
            })
            ->map('trim')
            ->unique()
            ->values()
            ->toArray();
        return view('settings/edit_salary_expectations', ['expectations' => $expectations, 'employee_salary_expection' => $used_expectation_ids]);
    }

    /**
     * Update the specified salary expectations details.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update_salary_expectations(Request $request, $id)
    {

        $requestData = $request->all();

        if (isset($requestData['expectation_status'])) {
            $requestData['expectation_status'] = 'A';
        } else {
            $requestData['expectation_status'] = 'I';
        }
        $requestData['expectation_name'] = trim(strip_tags($requestData['expectation_name']));
        // $validator = Validator::make($requestData, [
        //     'expectation_name' => 'required|string|max:255',
        // ]);
        // if ($validator->fails()) {
        //     return redirect()->back()->withErrors($validator)->withInput();
        // }
        $validator = Validator::make($requestData, [
            'expectation_name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[^<>]*$/', // Prevents HTML tags
                Rule::unique('reach_salary_expectations', 'expectation_name')->ignore($id, 'expectation_id'),
            ],
        ], [
            'expectation_name.regex' => 'The Salary Expectations field contains invalid characters (HTML tags are not allowed).',
            'expectation_name.unique' => 'This Salary Expectations  already exists. Please enter a different one.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $expectation = ReachSalaryExpectations::findOrFail($id);
        $expectation->update($requestData);

        return redirect()->route('salary-expectations')->with('success', 'Salary Expectation updated successfully!');
    }

    /**
     * Delete salary expectations by its Id.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\View\View
     */
    public function delete_salary_expectations($id)
    {
        $expectation = ReachSalaryExpectations::find($id);

        if ($expectation) {
            $expectation->delete();
            return redirect()->route('salary-expectations')->with('success', 'Salary Expectation deleted successfully!');
        } else {

            return redirect()->route('salary-expectations')->with('success', 'Page not found!');
        }
    }

    /**
     * Show the list of vessel type.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function vessel_list()
    {
        $vessels = ReachVesselType::all();
        $vessel_type_id = ReachJob::pluck('vessel_type')->toArray();
        return view('settings/vessel_list', ['vessels' => $vessels, 'vessel_type_id' => $vessel_type_id]);
    }

    /**
     * Form for adding new vessels.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function vessel_add()
    {
        return view('settings/add_vessel');
    }

    /**
     * Save data to vessels table.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function save_vessel(Request $request)
    {
        $requestData = $request->all();

        if (isset($requestData['vessel_status'])) {
            $requestData['vessel_status'] = 'A';
        } else {
            $requestData['vessel_status'] = 'I';
        }
        $requestData['vessel_type'] = trim(strip_tags($requestData['vessel_type']));
        $validator = Validator::make($requestData, [
            'vessel_type' => 'required|string|max:255',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $vessel = ReachVesselType::create($requestData);

        return redirect()->route('vessels')->with('success', 'Vessel Type created successfully!');
    }

    /**
     * Display the form for editing a vessel.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\View\View
     */

    public function edit_vessel($id)
    {
        $vessel = ReachVesselType::find($id);
        $vessel_type_id = ReachJob::pluck('vessel_type')->toArray();
        // Pass the member data to the view
        return view('settings/edit_vessel', ['vessel' => $vessel, 'vessel_type_id' => $vessel_type_id]);
    }

    /**
     * Update the specified vessel details.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update_vessel(Request $request, $id)
    {

        $requestData = $request->all();

        if (isset($requestData['vessel_status'])) {
            $requestData['vessel_status'] = 'A';
        } else {
            $requestData['vessel_status'] = 'I';
        }
        $requestData['vessel_type'] = trim(strip_tags($requestData['vessel_type']));
        $validator = Validator::make($requestData, [
            'vessel_type' => 'required|string|max:255',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $vessel = ReachVesselType::findOrFail($id);
        $vessel->update($requestData);

        return redirect()->route('vessels')->with('success', 'Vessel Type updated successfully!');
    }

    /**
     * Delete vessel by its Id.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\View\View
     */
    public function delete_vessel($id)
    {
        $vessel = ReachVesselType::find($id);

        if ($vessel) {
            $vessel->delete();
            return redirect()->route('vessels')->with('success', 'Vessel Type deleted successfully!');
        } else {
            return redirect()->route('vessels')->with('success', 'Page not found!');
        }
    }

    public function countries_list()
    {
        $countries = ReachCountry::all();
        $selectedCountries = ReachCountry::where('country_status', 'A')->pluck('id')->toArray();
        return view('settings/countries_list', ['countries' => $countries, 'selectedCountries' => $selectedCountries]);
    }

    public function updateCountryStatus(Request $request)
    {
        if ($request->has('country_id')) {

            $allCountryIds = ReachCountry::pluck('id')->toArray();
            $uncheckedCountryIds = array_diff($allCountryIds, $request->country_id);

            ReachCountry::whereIn('id', $request->country_id)->update(['country_status' => 'A']);

            ReachCountry::whereIn('id', $uncheckedCountryIds)->update(['country_status' => 'I']);

            return redirect()->back()->with('success', 'Countries statuses updated successfully.');
        }

        return redirect()->back()->with('error', 'No countries selected.');
    }

    public function visa_list()
    {
        $visa = ReachVisa::all();

        $employee_visa = ReachEmployeeDetails::pluck('employee_visa')->toArray();
        $used_visa_ids = collect($employee_visa)
            ->flatMap(function ($item) {
                return explode(',', $item);
            })
            ->map('trim')
            ->unique()
            ->values()
            ->toArray();

        return view('settings/visa_list', ['visa' => $visa, 'employee_visa' => $used_visa_ids]);
    }

    public function visa_add()
    {
        return view('settings/add_visa');
    }
    public function save_visa(Request $request)
    {
        $requestData = $request->all();

        if (isset($requestData['status'])) {
            $requestData['status'] = 'A';
        } else {
            $requestData['status'] = 'I';
        }
        $requestData['visa'] = trim(strip_tags($requestData['visa']));
        $validator = Validator::make($requestData, [
            'visa' => 'required|string|max:255|unique:reach_visa,visa',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        /*$validator = Validator::make($requestData, [
            'visa' => 'required|string|max:255',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }*/
        $vessel = ReachVisa::create($requestData);

        return redirect()->route('visa')->with('success', 'Visa created successfully!');
    }
    public function edit_visa($id)
    {
        $visa = ReachVisa::find($id);
        $employee_visa = ReachEmployeeDetails::pluck('employee_visa')->toArray();
        $used_visa_ids = collect($employee_visa)
            ->flatMap(function ($item) {
                return explode(',', $item);
            })
            ->map('trim')
            ->unique()
            ->values()
            ->toArray();
        return view('settings/edit_visa', ['visa' => $visa, 'used_visa_ids' => $used_visa_ids]);
    }

    public function update_visa(Request $request, $id)
    {

        $requestData = $request->all();

        if (isset($requestData['status'])) {
            $requestData['status'] = 'A';
        } else {
            $requestData['status'] = 'I';
        }
        $requestData['visa'] = trim(strip_tags($requestData['visa']));
        $validator = Validator::make($requestData, [
            'visa' => [
                'required',
                'string',
                'max:255',
                'regex:/^[^<>]*$/',
                Rule::unique('reach_visa', 'visa')->ignore($id), // Unique check while ignoring the current record
            ],
        ], [
            'visa.regex' => 'The visa field contains invalid characters (HTML tags are not allowed).',
            'visa.unique' => 'The visa already exists.'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $visa = ReachVisa::findOrFail($id);
        $visa->update($requestData);

        return redirect()->route('visa')->with('success', 'Visa updated successfully!');
    }
    /**
     * Delete vessel by its Id.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\View\View
     */
    public function delete_visa($id)
    {
        $visa = ReachVisa::find($id);

        if ($visa) {
            $visa->delete();
            return redirect()->route('visa')->with('success', 'Visa deleted successfully!');
        } else {
            return redirect()->route('visa')->with('success', 'Page not found!');
        }
    }

    public function  reported_list()
    {
        $chatRequests = DB::table('chat_requests')
            ->join('reach_members as sender', 'chat_requests.sender_id', '=', 'sender.id')
            ->join('reach_members as receiver', 'chat_requests.receiver_id', '=', 'receiver.id')
            ->where('chat_requests.is_reported', 1)
            ->select(
                'chat_requests.*',
                'sender.members_fname as sender_first_name',
                'sender.members_lname as sender_last_name',
                'receiver.members_fname as receiver_first_name',
                'receiver.members_lname as receiver_last_name'
            )
            ->get();
        return view('settings/blocked_report_list', ['chatRequest' => $chatRequests]);
    }
    public function getLastMessages($receiverId, $senderID)
    {


        $messages = DB::table('private_chat_messages as pcm')
            ->join('reach_members as sender', 'pcm.sender_id', '=', 'sender.id')
            ->Where('pcm.sender_id', $senderID)
            ->Where('pcm.receiver_id', $receiverId)
            ->orderBy('pcm.id', 'desc')
            ->limit(5)
            ->select([
                'pcm.id',
                'pcm.content',
                'pcm.created_at',
                'pcm.reported_reason',

                'sender.members_fname as receiver_first_name',
                'sender.members_lname as receiver_last_name'
            ])
            ->get();


        if ($messages->isEmpty()) {
            return response()->json(['success' => false, 'messages' => []]);
        }

        $formattedMessages = $messages->map(function ($msg) {
            return [
                'sender_name' => $msg->receiver_first_name . ' ' . $msg->receiver_last_name,
                'message' => $msg->content,
                'time' => $msg->created_at ? \Carbon\Carbon::parse($msg->created_at)->format('d/m/Y H:i') : null,
            ];
        });

        return response()->json(['success' => true, 'messages' => $formattedMessages]);
    }
}
