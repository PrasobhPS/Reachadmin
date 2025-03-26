<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\ReachJob;
use App\Models\ReachBoat;
use App\Models\ReachCountry;
use App\Models\JobDetails\ReachJobRole;
use App\Models\JobDetails\ReachBoatType;
use App\Models\JobDetails\ReachJobDuration;
use App\Models\JobDetails\ReachBoatLocation;
use App\Models\ReachVesselType;
use App\Models\ReachMember;
use App\Models\ReachJobMedia;

class JobController extends Controller
{

    /**
     * Show the jobs list.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        $query = ReachJob::with('member', 'role', 'boat', 'vessel', 'location');

        // Filter by job role
        if ($request->has('job_role') && $request->job_role != '') {
            $query->where('job_role', $request->job_role);
        }

        if ($request->has('job_status') && !empty($request->job_status)) {
            $query->where('job_status', $request->job_status);
        }

        // Filter by member name
        if ($request->has('member_name') && $request->member_name != '') {
            $query->whereHas('member', function ($q) use ($request) {
                $q->whereRaw("CONCAT(members_fname, ' ', members_lname) LIKE ?", ["%{$request->member_name}%"]);
            });
        }

        $jobs = $query->orderBy('created_at', 'desc')->paginate(20);

        $jobs->appends($request->all());

        $jobRoles = ReachJobRole::where('job_role_status', 'A')->get();

        return view('jobs/list', ['jobs' => $jobs, 'jobRoles' => $jobRoles, 'filters' => $request->all()]);
    }

    /**
     * Form for adding new jobs.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function add_job()
    {
        $membersList = ReachMember::where('members_status', 'A')->get();
        $boatType = ReachBoatType::where('boat_type_status', 'A')->get();
        $jobRoles = ReachJobRole::where('job_role_status', 'A')->get();
        $jobDuration = ReachJobDuration::where('job_duration_status', 'A')->get();
        $boatLocation = ReachBoatLocation::where('boat_location_status', 'A')->get();
        $vesselType = ReachVesselType::where('vessel_status', 'A')->get();

        return view('jobs/add_job', ['membersList' => $membersList, 'jobRoles' => $jobRoles, 'boatType' => $boatType, 'jobDuration' => $jobDuration, 'boatLocation' => $boatLocation, 'vesselType' => $vesselType]);
    }

    /**
     * Save jobs and boat details to table.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function save_job(Request $request)
    {
        $requestData = $request->all();

        $requestData['job_start_date'] = date('Y-m-d', strtotime($requestData['job_start_date']));
        if (isset($requestData['job_status'])) {
            $requestData['job_status'] = 'A';
        } else {
            $requestData['job_status'] = 'I';
        }
        $validator = Validator::make($requestData, [
            'member_id' => 'required',
            'job_role' => 'required',
            'job_location' => 'required',
            'job_start_date' => 'required|date',
            'vessel_type' => 'required',
            'vessel_size' => 'required',
        ]);

        //Validate fields
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Handle job picture upload and save the first image path
        if ($request->hasFile('job_images')) {
            $files = $request->file('job_images');
            $files = array_reverse($files);
            if (!empty($files)) {
                // Save the first image to job_images column in ReachJob table
                $firstFilePath = $files[0]->store('job-images', 'public');
                $requestData['job_images'] = $firstFilePath;
            }
        }

        $job = ReachJob::create($requestData);

        // Handle multiple job images upload and save to ReachJobMedia table
        if (isset($files) && !empty($files)) {
            foreach ($files as $file) {
                if ($file->isValid()) {
                    $filePath = $file->store('job-images', 'public');
                    ReachJobMedia::create([
                        'job_id' => $job->id,
                        'media_file' => $filePath,
                    ]);
                }
            }
        }

        // Redirect to the jobs list page
        return redirect()->route('jobs')->with('success', 'Job created successfully!');
    }

    /**
     * Retrieves boat details from request data.
     *
     * @param array $requestData The array containing boat details.
     * @return array An array containing boat details.
     */
    private function getBoatDetails($requestData)
    {
        // Initialize an array to store boat details
        $boatData = [];

        // Extract boat details from request data
        $boatData['boat_vessel'] = $requestData['boat_vessel'];
        $boatData['boat_location'] = $requestData['boat_location'];
        $boatData['boat_type'] = $requestData['boat_type'];
        $boatData['boat_size'] = $requestData['boat_size'];
        if (isset($requestData['boat_images'])) {
            $boatData['boat_images'] = $requestData['boat_images'];
        }

        // Return the extracted boat details
        return $boatData;
    }

    /**
     * Display the form for editing a specific job.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\View\View
     */

    public function edit_job($id)
    {
        $job = ReachJob::find($id);
        $membersList = ReachMember::where('members_status', 'A')->get();
        $boatType = ReachBoatType::where('boat_type_status', 'A')->get();
        $jobRoles = ReachJobRole::where('job_role_status', 'A')->get();
        $jobDuration = ReachJobDuration::where('job_duration_status', 'A')->get();
        $boatLocation = ReachBoatLocation::where('boat_location_status', 'A')->get();
        $vesselType = ReachVesselType::where('vessel_status', 'A')->get();

        $job_images = ReachJobMedia::where('job_id', $job->id)->select('id', 'media_file')->get();

        // Pass the member data to the view
        return view('jobs/edit_job', ['job' => $job, 'job_images' => $job_images, 'membersList' => $membersList, 'jobRoles' => $jobRoles, 'boatType' => $boatType, 'jobDuration' => $jobDuration, 'boatLocation' => $boatLocation, 'vesselType' => $vesselType]);
    }

    /**
     * Update the specified job details.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update_job(Request $request, $id)
    {

        $requestData = $request->all();
        $requestData['job_start_date'] = date('Y-m-d', strtotime($requestData['job_start_date']));

        if (isset($requestData['job_status'])) {
            $requestData['job_status'] = 'A';
        } else {
            $requestData['job_status'] = 'I';
        }
        $validator = Validator::make($requestData, [
            // 'member_id' => 'required',
            'job_role' => 'required',
            'job_location' => 'required',
            'job_start_date' => 'required|date',
            'vessel_type' => 'required',
            'vessel_size' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Handle job picture upload and save the first image path
        if ($request->hasFile('job_images')) {
            $files = $request->file('job_images');
            $files = array_reverse($files);
            if (!empty($files)) {
                // Save the first image to job_images column in ReachJob table
                $firstFilePath = $files[0]->store('job-images', 'public');
                $requestData['job_images'] = $firstFilePath;
            }
        }

        // Update job details
        $job = ReachJob::findOrFail($id);
        $job->update($requestData);

        // Handle multiple job images upload and save to ReachJobMedia table
        if (isset($files) && !empty($files)) {
            foreach ($files as $file) {
                if ($file->isValid()) {
                    $filePath = $file->store('job-images', 'public');
                    ReachJobMedia::create([
                        'job_id' => $job->id,
                        'media_file' => $filePath,
                    ]);
                }
            }
        }

        return redirect()->route('jobs')->with('success', 'Job updated successfully!');
    }

    public function delete_job($id)
    {
        $job = ReachJob::find($id);

        if ($job) {
            $job->job_status = 'D';
            $job->is_deleted = 'Y';
            $job->save();

            $job->delete();
            return redirect()->route('jobs')->with('success', 'Job deleted successfully!');
        } else {
            return redirect()->route('jobs')->with('success', 'Job not found!');
        }
    }
}
