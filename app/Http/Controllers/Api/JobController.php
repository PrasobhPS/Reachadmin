<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Hash;
use App\Models\ReachMember;
use App\Models\ReachJob;
use App\Models\ReachCountry;
use App\Models\JobDetails\ReachJobRole;
use App\Models\JobDetails\ReachBoatType;
use App\Models\JobDetails\ReachJobDuration;
use App\Models\JobDetails\ReachBoatLocation;
use App\Models\ReachQualifications;
use App\Models\ReachVesselType;
use App\Models\ReachEmployeeDetails;
use App\Models\ReachJobSearch;
use App\Models\ReachJobMedia;
use App\Models\ReachLanguages;
use App\Models\ReachEmployeeMedia;
use App\Models\ReachPageTitle;
use App\Models\ReachLikedmatch;
use App\Models\ReachJobSeen;
use App\Models\Notification;
use Illuminate\Support\Facades\DB;

class JobController extends Controller
{
	protected $notificationService;
	public function __construct(NotificationService $notificationService)
	{
		$this->notificationService = $notificationService;
	}
	public function postJob(Request $request)
	{
		$requestData = $request->all();


		$validator = Validator::make($requestData, [
			'job_role' => 'required'
		], [
			'job_role.required' => 'The job role is required.'
		]);

		if ($validator->fails()) {
			return response()->json(['errors' => $validator->errors()], 422);
		} else {

			$member = $request->user();
			//print("<PRE>");print_R($member);die();

			$requestData['member_id'] = $member->id;

			try {

				// if(isset($requestData['job_start_date'])) {
				// 	$requestData['job_start_date'] = $requestData['job_start_date']['dobYear'] . '-' . 
				//         str_pad($requestData['job_start_date']['dobMonth'], 2, '0', STR_PAD_LEFT) . '-' . 
				//         str_pad($requestData['job_start_date']['dobDay'], 2, '0', STR_PAD_LEFT);
				// 	unset($requestData['job_start_date']);
				// }

				if (isset($requestData['salary_picker'])) {
					$requestData['job_currency'] = $requestData['salary_picker']['currency'];
					$requestData['job_salary_amount'] = $requestData['salary_picker']['amount'];
					unset($requestData['salary_picker']);
				}
				if (isset($requestData['job_start_date'])) {
					$requestData['job_start_date'] = date('Y-m-d', strtotime($requestData['job_start_date']));
				}
				$job_images = [];
				if (isset($requestData['upload_media']) && !empty($requestData['upload_media'])) {
					$job_images = $requestData['upload_media'];
					$requestData['job_images'] = $job_images[0];
				} else {
					unset($requestData['job_images']);
				}
				if (isset($requestData['id'])) {

					$job = ReachJob::find($requestData['id']);
					if ($job) {
						$job->update($requestData);
						$jobId = $job->id;
					} else {
						return response()->json(['error' => 'Job not found'], 404);
					}
				} else {
					$job = ReachJob::where('job_role', $requestData['job_role'])
						->where('member_id', $requestData['member_id'])
						->first();

					$job = ReachJob::create($requestData);
					$jobId = $job->id;
				}

				if (!empty($job_images)) {
					foreach ($job_images as $file) {
						ReachJobMedia::updateOrCreate(
							['job_id' => $jobId, 'media_file' => $file],
							[]
						);
					}
				}

				$searchData = [
					'employee_qualification' => $requestData['search_qualification'] ?? null,
					'employee_visa' => $requestData['search_visa'] ?? null,
					'employee_age' => $requestData['search_age'] ?? null,
					'employee_gender' => $requestData['search_gender'] ?? null,
					'employee_languages' => $requestData['search_language'] ?? null,
				];

				ReachJobSearch::where('job_id', $jobId)->forceDelete();

				foreach ($searchData as $searchParameterName => $searchValues) {
					if ($searchValues !== null) {
						foreach ($searchValues as $searchKey => $searchValue) {
							ReachJobSearch::create([
								'job_id' => $jobId,
								'search_parameter_name' => $searchParameterName,
								'search_value' => $searchValue,
							]);
						}
					}
				}

				return response()->json(['success' => true, 'message' => isset($requestData['id']) ? 'Job updated successfully' : 'Job created successfully', 'data' => ['JobId' => $jobId, 'jobStatus' => $job->job_status]], 200);
			} catch (\Exception $e) {

				return response()->json(['error' => 'Failed to create job: ' . $e->getMessage()], 500);
			}
		}
	}

	public function getJobRole()
	{
		try {

			$roles = ReachJobRole::where('job_role_status', 'A')->pluck('job_role', 'id')->toArray();

			return response()->json([
				'success' => true,
				'message' => 'OK',
				'data' => $roles
			], 200);
		} catch (\Exception $e) {

			return response()->json([
				'success' => false,
				'message' => 'An error occurred while fetching data',
				'error' => $e->getMessage()
			], 500);
		}
	}

	public function getJobDuration()
	{
		try {

			$durations = ReachJobDuration::where('job_duration_status', 'A')->pluck('job_duration')->toArray();

			return response()->json([
				'success' => true,
				'message' => 'OK',
				'data' => $durations
			], 200);
		} catch (\Exception $e) {

			return response()->json([
				'success' => false,
				'message' => 'An error occurred while fetching data',
				'error' => $e->getMessage()
			], 500);
		}
	}

	public function getBoatLocation()
	{
		try {

			$locations = ReachBoatLocation::where('boat_location_status', 'A')->pluck('boat_location')->toArray();

			return response()->json([
				'success' => true,
				'message' => 'OK',
				'data' => $locations
			], 200);
		} catch (\Exception $e) {

			return response()->json([
				'success' => false,
				'message' => 'An error occurred while fetching data',
				'error' => $e->getMessage()
			], 500);
		}
	}

	public function getBoatType()
	{
		try {

			$boatTypes = ReachBoatType::where('boat_type_status', 'A')->pluck('boat_type')->toArray();

			return response()->json([
				'success' => true,
				'message' => 'OK',
				'data' => $boatTypes
			], 200);
		} catch (\Exception $e) {

			return response()->json([
				'success' => false,
				'message' => 'An error occurred while fetching data',
				'error' => $e->getMessage()
			], 500);
		}
	}

	public function getPostJobDetails(Request $request)
	{
		$member = $request->user();
		try {

			// $campaigns = ReachJob::with(['role'])
			// 	->where('member_id', $member->id)
			// 	->orderBy('id', 'desc')
			// 	->get()
			// 	->mapWithKeys(function ($job) {
			// 		return [
			// 			$job->job_role => $job->role ? $job->role->job_role : '',
			// 		];
			// 	})
			// 	->toArray();
			$campaigns = [];

			$jobRoles = ReachJobRole::where('job_role_status', 'A')->pluck('job_role', 'id')->toArray();
			$jobDurations = ReachJobDuration::where('job_duration_status', 'A')->pluck('job_duration', 'id')->toArray();
			$boatLocations = ReachBoatLocation::where('boat_location_status', 'A')->pluck('boat_location', 'id')->toArray();
			$boatTypes = ReachBoatType::where('boat_type_status', 'A')->pluck('boat_type', 'id')->toArray();
			$vesselTypes = ReachVesselType::where('vessel_status', 'A')->pluck('vessel_type', 'vessel_id')->toArray();
			$qualifications = ReachQualifications::where('qualification_status', 'A')->pluck('qualification_name', 'qualification_id')->toArray();
			$countries = ReachCountry::where('country_status', 'A')->pluck('country_name', 'id')->toArray();
			$languages = ReachLanguages::where('language_status', 'A')->pluck('language_name', 'lang_id')->toArray();
			$age_range = ['20-30' => '20-30', '30-40' => '30-40', '40-50' => '40-50', '50-60' => '50-60', '60-70' => '60-70'];
			$gender = ['Male' => 'Male', 'Female' => 'Female'];
			$salary = ['Annually' => 'Annually', 'Monthly' => 'Monthly', 'Daily' => 'Daily'];
			$currency = ['€' => '€', '$' => '$', '£' => '£'];
			$salaryType = ['Depending on experience' => 'Depending on experience'];
			$pageDetails = ReachPageTitle::where('page_id', 1)
				->select('page_title', 'page_desc')
				->get()
				->toArray();

			$data = [
				'steps' => [
					[
						'title' => 'STEP 1 - JOB DETAILS',
						'index' => '1',
						'questions' => [
							[
								'title' => $pageDetails[0]['page_title'],
								'sub_title' => $pageDetails[0]['page_desc'],
								'type' => 'radio',
								'name' => 'job_role',
								'options' => $jobRoles,
								'multiple' => false,
								'validation' => 'Please select a job role',
								'sub_index' => '1',
								'exclude_jobrole' => $campaigns,

							],
							[
								'title' => $pageDetails[1]['page_title'],
								'sub_title' => $pageDetails[1]['page_desc'],
								'type' => 'radio',
								'name' => 'boat_type',
								'options' => $boatTypes,
								'multiple' => false,
								'validation' => 'Please select a boat type',
								'sub_index' => '2',
							],
							[
								'title' => $pageDetails[2]['page_title'],
								'sub_title' => $pageDetails[2]['page_desc'],
								'type' => 'radio',
								'name' => 'job_duration',
								'options' => $jobDurations,
								'multiple' => false,
								'validation' => 'Please select a job duration',
								'sub_index' => '3',
							],
							[
								'title' => $pageDetails[3]['page_title'],
								'sub_title' => $pageDetails[3]['page_desc'],
								'type' => 'salary',
								'validation' => 'Please enter job salary',
								'sub_index' => '4',
								'sub_questions' => [
									[
										'title' => '',
										'type' => 'radio',
										'name' => 'salary_type',
										'options' => $salaryType,
										'multiple' => false,
									],
									[
										'title' => '',
										'type' => 'radio',
										'name' => 'job_salary_type',
										'options' => $salary,
										'multiple' => false,
									],

									[
										'title' => '',
										'type' => 'salary_picker',
										'name' => 'salary_picker',
										'options' => $currency,
										'multiple' => false,
									],
								],
							],
							[
								'title' => $pageDetails[4]['page_title'],
								'sub_title' => $pageDetails[4]['page_desc'],
								'type' => 'date',
								'name' => 'job_start_date',
								'multiple' => false,
								'validation' => 'Please select a job start date',
								'sub_index' => '5',
							],
						],
					],
					[
						'title' => 'STEP 2 - THE BOAT',
						'index' => '2',
						'questions' => [
							[
								'title' => $pageDetails[5]['page_title'],
								'sub_title' => $pageDetails[5]['page_desc'],
								'type' => 'textarea',
								'name' => 'vessel_desc',
								'placeholder' => 'Describe the boat here.',
								'multiple' => false,
								'validation' => 'Please enter vessel description',
								'sub_index' => '1',
							],
							[
								'title' => $pageDetails[6]['page_title'],
								'sub_title' => $pageDetails[6]['page_desc'],
								'type' => 'radio',
								'name' => 'job_location',
								'options' => $boatLocations,
								'multiple' => false,
								'validation' => 'Please select a location',
								'sub_index' => '2',
							],
							[
								'title' => $pageDetails[8]['page_title'],
								'sub_title' => $pageDetails[8]['page_desc'],
								'type' => 'number',
								'name' => 'vessel_size',
								'label' => 'Meters',
								'placeholder' => 'M',
								'multiple' => false,
								'validation' => 'Please enter vessel size',
								'sub_index' => '3',
							],
						],
					],
					[
						'title' => 'STEP 3 - SUMMARY',
						'index' => '3',
						'questions' => [
							[
								'title' => $pageDetails[9]['page_title'],
								'sub_title' => $pageDetails[9]['page_desc'],
								'type' => 'textarea',
								'name' => 'job_summary',
								'placeholder' => 'Type details of the job here, dates, responsibilities, salary, location, experience required etc.',
								'multiple' => false,
								'validation' => 'Please enter job summary',
								'sub_index' => '1',
							],
							[
								'title' => $pageDetails[10]['page_title'],
								'sub_title' => $pageDetails[10]['page_desc'],
								'type' => 'file',
								'name' => 'upload_media',
								'multiple' => false,
								'validation' => '',
								'sub_index' => '2',
							],
						],
					],
					[
						'title' => 'STEP 4 - REQUIREMENTS',
						'index' => '4',
						'questions' => [
							[
								'title' => $pageDetails[11]['page_title'],
								'sub_title' => $pageDetails[11]['page_desc'],
								'sub_index' => '1',
								'sub_questions' => [
									[

										'title' => 'Required Qualifications',
										'type' => 'select',
										'name' => 'search_qualification',
										'options' => $qualifications,
										'multiple' => true,
										//'validation' => 'Please select at least one qualification',
										'validation' => '',
									],
									[
										'title' => 'Age Range',
										'type' => 'select',
										'name' => 'search_age',
										'options' => $age_range,
										'multiple' => true,
										//'validation' => 'Please select a age Range',
										'validation' => '',
									],
									[
										'title' => 'Gender',
										'type' => 'select',
										'name' => 'search_gender',
										'options' => $gender,
										'multiple' => true,
										//'validation' => 'Please select a gender',
										'validation' => '',
									],
									[

										'title' => 'Languages Spoken',
										'type' => 'select',
										'name' => 'search_language',
										'options' => $languages,
										'multiple' => true,
										//'validation' => 'Please select at least one language',
										'validation' => '',
									],
								],
							],
						],
					],
				],

			];



			return response()->json([
				'success' => true,
				'data' => $data
			], 200);
		} catch (\Exception $e) {
			return response()->json([
				'success' => false,
				'message' => 'An error occurred while fetching data',
				'error' => $e->getMessage()
			], 500);
		}
	}

	public function getLiveCampaigns(Request $request)
	{
		$member = $request->user();

		if ($member) {

			$campaigns = ReachJob::with(['role', 'location', 'duration'])
				->whereIn('job_status', ['A', 'I'])
				->where('member_id', $member->id)
				->orderBy('id', 'desc')
				->get()
				->map(function ($job) {
					return [
						'id' => $job->id,
						'job_role' => $job->role ? $job->role->job_role : '',
						'role_id' => $job->job_role,
						'vessel_size' => !empty($job->vessel_size) ? $job->vessel_size . 'M' : '',
						'boat_type' => $job->boat ? $job->boat->boat_type : '',
						'job_duration' => $job->duration ? $job->duration->job_duration : '',
						'job_location' => $job->location ? $job->location->boat_location : '',
						'job_seen_count' => $job->job_seen_count,
						'job_images' => $job->job_images,
						'job_status' => $job->job_status,
						'is_deleted' => $job->is_deleted,
						'created_at' => $job->created_at->format('d F Y')
					];
				});

			$campaigns_with_matches = [];
			if ($campaigns) {
				foreach ($campaigns as $job) {

					$employee_ids = [];


					$likedMatches = ReachLikedmatch::join('reach_employee_details', 'reach_liked_match.employee_id', '=', 'reach_employee_details.employee_id')
						->join('reach_members', 'reach_employee_details.member_id', '=', 'reach_members.id')
						->where('reach_liked_match.job_id', $job['id'])
						->where('reach_members.members_status', 'A')
						->where(function ($query) {
							$query->where('reach_liked_match.liked_by', 'EMPLOYEE')
								->orWhere('reach_liked_match.liked_by', 'EMPLOYER');
						})
						->get()->groupBy('employee_id');
					$matchingCount = 0;
					foreach ($likedMatches as $employeeId => $matches) {
						$likedBy = $matches->pluck('liked_by')->toArray();
						if (in_array('EMPLOYEE', $likedBy) && in_array('EMPLOYER', $likedBy)) {
							$matchingCount++;
						}
					}

					$searchParameters = ReachJobSearch::where('job_id', $job['id'])
						->select('search_parameter_name', 'search_value')
						->get()
						->mapWithKeys(function ($searchParameter) {
							return [$searchParameter->search_parameter_name => $searchParameter->search_value];
						})
						->toArray();


					// Retrieve employee IDs that are liked by the employer
					$likedEmployeeIds = ReachLikedmatch::where('job_id', $job['id'])
						->where(function ($query) {
							$query->where('liked_by', 'EMPLOYER')
								->orWhere('unliked_by', 'EMPLOYER');
						})
						->pluck('employee_id')
						->toArray();

					// Retrieve matching employees using search parameters
					$matchingEmployeesQuery = ReachEmployeeDetails::join('reach_members', 'reach_employee_details.member_id', '=', 'reach_members.id')
						->where('reach_members.members_status', 'A')
						->where('reach_employee_details.employee_status', 'A')
						->where('reach_employee_details.member_id', '!=', $member->id)
						->whereRaw('FIND_IN_SET(?, reach_employee_details.employee_role)', [$job['role_id']])
						->whereNotIn('reach_employee_details.employee_id', $likedEmployeeIds);

					if ($searchParameters) {
						// Dynamically apply search parameters to the query		
						$matchingEmployeesQuery->where(function ($query) use ($searchParameters) {
							foreach ($searchParameters as $paramName => $paramValue) {
								if ($paramName == "employee_age") {
									$ageRange = explode('-', $paramValue);
									if (count($ageRange) == 2) {
										$query->orwhereBetween('employee_age', [$ageRange[0], $ageRange[1]]);
									}
								} else {
									//$query->orWhere($paramName, $paramValue);
									$query->orWhereRaw("FIND_IN_SET(?, $paramName)", [$paramValue]);
								}
							}
						});
					}

					//$matchingCount = $matchingEmployeesQuery->count();

					$matchingEmployeesQuery->get()
						->each(function ($employee) use (&$employee_ids) {
							$employee_ids[] = $employee->employee_id;
						});

					$job['matches'] = $matchingCount;
					$job['employee_ids'] = $employee_ids;
					$job['employee_count'] = count($employee_ids);

					$campaigns_with_matches[] = $job;
				}
			}

			return response()->json(['success' => true, 'data' => $campaigns_with_matches, 'filePath' => url('storage')], 200);
		} else {

			return response()->json(['error' => 'Campaign not found'], 404);
		}
	}

	public function myMatchesListold($job_id)
	{
		$campaign = ReachJob::with(['role', 'location', 'duration'])
			->where('job_status', 'A')
			->where('id', $job_id)
			->first();

		if ($campaign) {



			// Retrieve and map search parameters
			$searchParameters = ReachJobSearch::where('job_id', $campaign->id)
				->select('search_parameter_name', 'search_value')
				->get()
				->mapWithKeys(function ($searchParameter) {
					return [$searchParameter->search_parameter_name => $searchParameter->search_value];
				})
				->toArray();

			$matchingEmployees = [];
			if ($searchParameters) {

				// Retrieve employee IDs that are liked by the employer
				$likedEmployeeIds = ReachLikedmatch::where('job_id', $campaign->id)
					->where(function ($query) {
						$query->where('liked_by', 'EMPLOYER')
							->orWhere('unliked_by', 'EMPLOYER');
					})
					->pluck('employee_id')
					->toArray();

				// Retrieve matching employees using search parameters
				$matchingEmployeesQuery = ReachEmployeeDetails::with('member')
					->where('employee_status', 'A')
					->where('member_id', '!=', $campaign->member_id)
					->whereNotIn('employee_id', $likedEmployeeIds);


				$matchingEmployeesQuery->where(function ($query) use ($searchParameters) {
					foreach ($searchParameters as $paramName => $paramValue) {
						$query->orWhere($paramName, $paramValue);
					}
				});

				$matchingEmployees = $matchingEmployeesQuery->get()
					->map(function ($employee) {
						return [
							'employee_id' => $employee->employee_id,
							'employee_role' => $employee->jobRole->job_role,
							'members_name' => $employee->member->members_fname . ' ' . $employee->member->members_lname,
							'members_profile_picture' => $employee->member->members_profile_picture,
							'date' => date("d F Y", strtotime($employee->created_at)),
						];
					})->toArray();
			}

			return response()->json(['success' => true, 'data' => $matchingEmployees], 200);
		} else {
			return response()->json(['error' => 'Campaign not found'], 404);
		}
	}

	public function myMatchesList($job_id)
	{
		$campaign = ReachJob::where('job_status', 'A')
			->where('id', $job_id)
			->first();

		if ($campaign) {

			$likedMatches = ReachLikedmatch::where('job_id', $campaign->id)
				->where(function ($query) {
					$query->where('liked_by', 'EMPLOYEE')
						->orWhere('liked_by', 'EMPLOYER');
				})
				->orderBy('id', 'desc')
				->get()
				->groupBy('employee_id');

			$matchingEmployees = [];

			foreach ($likedMatches as $employeeId => $matches) {
				$likedBy = $matches->pluck('liked_by')->toArray();

				if (in_array('EMPLOYEE', $likedBy) && in_array('EMPLOYER', $likedBy)) {

					$employees = ReachEmployeeDetails::with('member')->withActiveMembers()
						->where('employee_status', 'A')
						->where('employee_id', $employeeId)
						->get()
						->map(function ($employee) {

							$roleIds = explode(',', $employee->employee_role);
							$jobRoles = ReachJobRole::whereIn('id', $roleIds)->pluck('job_role')->toArray();
							$jobRoleNames = implode(', ', $jobRoles);

							return [
								'id' => $employee->employee_id,
								'member_id' => $employee->member->id,
								'employee_role' => $jobRoleNames,
								'member_name' => $employee->member->members_fname . ' ' . $employee->member->members_lname,
								'members_profile_picture' => $employee->member->members_profile_picture,
								'date' => date("d F Y", strtotime($employee->created_at)),
							];
						});

					$matchingEmployees = array_merge($matchingEmployees, $employees->toArray());
				}
			}

			return response()->json(['success' => true, 'data' => $matchingEmployees], 200);
		} else {
			return response()->json(['error' => 'Campaign not found'], 404);
		}
	}

	public function reviewJobDetails($id)
	{
		$jobDetails = ReachJob::getJobDetailsById($id);

		if ($jobDetails) {

			$data['jobDetails'] = $jobDetails;
			$data['jobDetails']['job_start_date'] = date("d F Y", strtotime($jobDetails['job_start_date']));
			$data['mediaDetails'] = [];
			$data['searchParameters'] = [];
			if ($jobDetails) {

				$searchParameters = ReachJobSearch::where('job_id', $jobDetails->id)
					->select('search_parameter_name', 'search_value')
					->get()
					->toArray();

				foreach ($searchParameters as $param) {
					if ($param['search_parameter_name'] == "employee_languages") {
						$language = ReachLanguages::where('lang_id', $param['search_value'])->value('language_name');

						$data['searchParameters'][$param['search_parameter_name']][] = $language;
					} elseif ($param['search_parameter_name'] == "employee_qualification") {
						$qualification = ReachQualifications::where('qualification_id', $param['search_value'])->value('qualification_name');

						$data['searchParameters'][$param['search_parameter_name']][] = $qualification;
					} elseif ($param['search_parameter_name'] == "employee_visa") {
						$visa = ReachCountry::where('country_status', 'A')->where('id', $param['search_value'])->value('country_iso');

						$data['searchParameters'][$param['search_parameter_name']][] = $visa;
					} else {
						$data['searchParameters'][$param['search_parameter_name']][] = $param['search_value'];
					}
				}

				$data['mediaDetails'] = ReachJobMedia::where('job_id', $jobDetails->id)->select('id', 'media_file')->get();
			}


			return response()->json(['success' => true, 'data' => $data], 200);
		} else {

			return response()->json(['error' => 'Unauthorized'], 401);
		}
	}

	public function previewJobAdvert($id)
	{
		$jobDetails = ReachJob::getJobDetailsById($id);

		if ($jobDetails) {

			$data['jobDetails'] = $jobDetails;
			//$data['jobDetails']['vessel_size'] = $jobDetails['vessel_size'] . 'm';
			$data['jobDetails']['vessel_size'] = !empty($jobDetails['vessel_size']) ? $jobDetails['vessel_size'] . 'm' : '';
			$data['jobDetails']['job_start_date'] = !empty($jobDetails['job_start_date']) ? date("d F Y", strtotime($jobDetails['job_start_date'])) : '';
			$data['mediaDetails'] = [];
			if ($jobDetails) {
				$data['mediaDetails'] = ReachJobMedia::where('job_id', $jobDetails->id)->select('id', 'media_file')->get();
			}

			return response()->json(['success' => true, 'data' => $data], 200);
		} else {

			return response()->json(['error' => 'Unauthorized'], 401);
		}
	}

	public function editPostJobDetails($job_id)
	{
		$userId = auth()->id();
		//$job = ReachJob::select('id', 'job_role', 'boat_type', 'job_duration', 'job_start_date', 'job_salary_type', 'job_currency', 'job_salary_amount', 'job_location', 'vessel_type', 'vessel_size', 'job_summary', 'vessel_desc', 'salary_type')->find($job_id);
		$job = ReachJob::where('id', $job_id)
			->where('member_id', $userId) // Ensure the job belongs to the logged-in user
			->select(
				'id',
				'job_role',
				'boat_type',
				'job_duration',
				'job_start_date',
				'job_salary_type',
				'job_currency',
				'job_salary_amount',
				'job_location',
				'vessel_type',
				'vessel_size',
				'job_summary',
				'vessel_desc',
				'salary_type'
			)
			->first();
		if ($job) {


			$salary_picker['currency'] = $job['job_currency'];
			$salary_picker['amount'] = $job['job_salary_amount'];
			// if ($job['job_start_date']) {
			// 	$job['job_start_date'] = date('d-m-Y', strtotime($job['job_start_date']));
			// }

			$job_images = ReachJobMedia::where('job_id', $job_id)->pluck('media_file')->toArray();


			$job['upload_media'] = $job_images;
			$job['salary_picker'] = $salary_picker;

			$searchLanguage = [];
			$searchQualification = [];
			$searchVisa = [];
			$searchGender = [];
			$searchAge = [];
			$searchParameter = [];

			$searchParameters = ReachJobSearch::where('job_id', $job['id'])
				->select('search_parameter_name', 'search_value')
				->get()
				->toArray();

			foreach ($searchParameters as $param) {
				if ($param['search_parameter_name'] == "employee_languages") {
					$language = ReachLanguages::where('lang_id', $param['search_value'])->value('lang_id');

					$searchLanguage[] = $language;
				} elseif ($param['search_parameter_name'] == "employee_qualification") {
					$qualification = ReachQualifications::where('qualification_id', $param['search_value'])->value('qualification_id');

					$searchQualification[] = $qualification;
				} elseif ($param['search_parameter_name'] == "employee_visa") {
					$visa = ReachCountry::where('country_status', 'A')->where('id', $param['search_value'])->value('id');

					$searchVisa[] = $visa;
				} elseif ($param['search_parameter_name'] == "employee_gender") {

					$searchGender[] = $param['search_value'];
				} elseif ($param['search_parameter_name'] == "employee_age") {

					$searchAge[] = $param['search_value'];
				} else {
					$searchParameter[$param['search_parameter_name']][$param['search_value']] = $param['search_value'];
				}
			}

			$job['search_language'] = $searchLanguage;
			$job['search_qualification'] = $searchQualification;
			$job['search_visa'] = $searchVisa;
			$job['search_gender'] = $searchGender;
			$job['search_age'] = $searchAge;

			return response()->json([
				'success' => true,
				'data' => $job
			], 200);
		} else {
			return response()->json(['error' => 'Campaign not found'], 404);
		}
	}

	public function editSearchParameters($job_id)
	{
		$searchLanguage = [];
		$searchQualification = [];
		$searchVisa = [];
		$searchGender = [];
		$searchAge = [];
		$searchParameters = [];

		$jobSearch = ReachJobSearch::where('job_id', $job_id)
			->select('search_parameter_name', 'search_value')
			->get()
			->toArray();

		foreach ($jobSearch as $param) {
			if ($param['search_parameter_name'] == "employee_languages") {
				$language = ReachLanguages::where('lang_id', $param['search_value'])->value('language_name');

				$searchLanguage[$param['search_value']] = $language;
			} elseif ($param['search_parameter_name'] == "employee_qualification") {
				$qualification = ReachQualifications::where('qualification_id', $param['search_value'])->value('qualification_name');

				$searchQualification[$param['search_value']] = $qualification;
			} elseif ($param['search_parameter_name'] == "employee_visa") {
				$visa = ReachCountry::where('country_status', 'A')->where('id', $param['search_value'])->value('country_name');

				$searchVisa[$param['search_value']] = $visa;
			} elseif ($param['search_parameter_name'] == "employee_gender") {

				$searchGender[$param['search_value']] = $param['search_value'];
			} elseif ($param['search_parameter_name'] == "employee_age") {

				$searchAge[$param['search_value']] = $param['search_value'];
			} else {
				$searchParameters[$param['search_parameter_name']][$param['search_value']] = $param['search_value'];
			}
		}

		$searchParameters['search_language'] = $searchLanguage;
		$searchParameters['search_qualification'] = $searchQualification;
		$searchParameters['search_visa'] = $searchVisa;
		$searchParameters['search_gender'] = $searchGender;
		$searchParameters['search_age'] = $searchAge;

		return response()->json(['success' => true, 'data' => $searchParameters], 200);
	}

	public function pauseCampaign($job_id, $status)
	{
		$job = ReachJob::find($job_id);
		if ($job) {

			$job->job_status = $status;
			$job->save();

			$message = $status == 'I' ? 'Campaign paused successfully' : 'Campaign unpaused successfully';
			return response()->json(['success' => true, 'message' => $message], 200);
		} else {
			return response()->json(['error' => 'Campaign not found'], 404);
		}
	}

	public function cloneCampaign($job_id)
	{
		$job = ReachJob::find($job_id);

		if ($job) {
			// Clone the job attributes
			$newJob = $job->replicate();
			$newJob->created_at = now();
			$newJob->updated_at = now();
			$newJob->save();

			// Clone the search parameters associated with the job
			$searchParameters = ReachJobSearch::where('job_id', $job_id)->get();
			foreach ($searchParameters as $searchParameter) {
				$newSearchParameter = $searchParameter->replicate();
				$newSearchParameter->job_id = $newJob->id;
				$newSearchParameter->save();
			}

			return response()->json(['success' => true, 'message' => 'Campaign cloned successfully', 'data' => ['JobId' => $newJob->id]], 200);
		} else {
			return response()->json(['error' => 'Campaign not found'], 404);
		}
	}


	public function deleteCampaign($job_id)
	{
		$job = ReachJob::find($job_id);
		if ($job) {

			ReachJobSeen::where('job_id', $job_id)->delete();
			ReachLikedmatch::where('job_id', $job_id)->delete();
			$job->job_status = 'D';
			$job->is_deleted = 'Y';
			$job->save();

			$job->delete();
			return response()->json(['success' => true, 'message' => 'Campaign deleted successfully'], 200);
		} else {
			return response()->json(['error' => 'Campaign not found'], 404);
		}
	}

	public function removeCampaign($job_id)
	{
		$job = ReachJob::withTrashed()->find($job_id);
		if ($job) {
			$job->job_status = 'R';
			$job->is_deleted = 'Y';
			$job->save();
			$job->delete();
			return response()->json(['success' => true, 'message' => 'Campaign deleted successfully'], 200);
		} else {
			return response()->json(['error' => 'Campaign not found'], 404);
		}
	}

	public function activateCampaign($job_id)
	{
		$job = ReachJob::withTrashed()->find($job_id);

		if ($job) {
			$job->job_status = 'D';
			$job->is_deleted = 'N';
			$job->save();

			$job->restore();
			return response()->json(['success' => true, 'message' => 'Campaign Reactivated successfully'], 200);
		} else {
			return response()->json(['error' => 'Campaign not found'], 404);
		}
	}


	public function campaignMatchesList(Request $request)
	{
		$requestData = $request->all();

		$job_id = $requestData['job_id'];
		$employee_ids = $requestData['employee_ids'];
		$page = $requestData['page'] ?? 1;
		$limit = 1;

		$campaign = ReachJob::where('job_status', 'A')
			->where('id', $job_id)
			->first();

		if ($campaign) {

			$employeeDetails = ReachEmployeeDetails::getEmployeeDetailsIds($employee_ids, $limit, $page)->toArray();

			$is_match = ReachLikedmatch::where('job_id', $job_id)
				->where('employee_id', $employeeDetails['data'][0]['employee_id'])
				->where('liked_by', 'EMPLOYER')
				->first();


			$is_liked = ReachLikedmatch::where('job_id', $job_id)
				->where('employee_id', $employeeDetails['data'][0]['employee_id'])
				->where('liked_by', 'EMPLOYEE')
				->first();

			$data["member_id"] = $employeeDetails['data'][0]['member_id'];
			$data["member_fname"] = $employeeDetails['data'][0]['members_name'];
			$data["member_lname"] = $employeeDetails['data'][0]['members_lname'];
			$data["member_profile_picture"] = $employeeDetails['data'][0]['members_profile_picture'];
			$data["active"] = true;
			$data["joined_at"] = date("Y-m-d", strtotime($employeeDetails['data'][0]['created_at']));
			$data["pending_message_count"] = 0;
			$data["last_message_time"] = "";

			$employeeDetails['data'][0]['is_match'] = $is_match && $is_liked ? "Y" : "N";
			$employeeDetails['data'][0]['is_liked'] = $is_liked ? "Y" : "N";
			$employeeDetails['data'][0]['member'] = $data;

			$matchingEmployeesQuery = ReachEmployeeDetails::with([
				'member' => function ($query) {
					$query->where('members_status', 'A');
				}
			])
				->where('employee_status', 'A')
				->whereIn('employee_id', $employee_ids);

			$matchingEmployees = $matchingEmployeesQuery->paginate($limit, ['*'], 'page', $page);



			return response()->json([
				'success' => true,
				'data' => $employeeDetails['data'],
				'employee_ids' => $employee_ids,
				'current_page' => $matchingEmployees->currentPage(),
				'next_page' => $matchingEmployees->currentPage() + 1,
				'total_pages' => $matchingEmployees->lastPage()
			], 200);
		} else {
			return response()->json(['error' => 'Campaign not found'], 404);
		}
	}

	public function getDraftCampaigns(Request $request)
	{
		$member = $request->user();

		if ($member) {

			$campaigns = ReachJob::with(['role', 'location', 'duration'])
				->where('job_status', 'D')
				->where('member_id', $member->id)
				->orderBy('id', 'desc')
				->get()
				->map(function ($job) {
					return [
						'id' => $job->id,
						'job_role' => $job->role ? $job->role->job_role : '',
						'vessel_size' => !empty($job->vessel_size) ? $job->vessel_size . 'M' : '',
						'boat_type' => $job->boat ? $job->boat->boat_type : '',
						'job_duration' => $job->duration ? $job->duration->job_duration : '',
						'job_location' => $job->location ? $job->location->boat_location : '',
						'job_seen_count' => $job->job_seen_count,
						'job_images' => $job->job_images,
						'job_status' => $job->job_status,
						'is_deleted' => $job->is_deleted,
						'created_at' => $job->created_at->format('d F Y')
					];
				});

			return response()->json(['success' => true, 'data' => $campaigns, 'filePath' => url('storage')], 200);
		} else {

			return response()->json(['error' => 'Campaign not found'], 404);
		}
	}

	public function getArchiveCampaigns(Request $request)
	{
		$member = $request->user();

		if ($member) {

			$campaigns = ReachJob::with(['role', 'location', 'duration'])
				->where('member_id', $member->id)
				->where('job_status', 'D')
				->where('is_deleted', 'Y')
				->withTrashed()
				->orderBy('id', 'desc')
				->get()
				->map(function ($job) {
					return [
						'id' => $job->id,
						'job_role' => $job->role ? $job->role->job_role : '',
						'vessel_size' => !empty($job->vessel_size) ? $job->vessel_size . 'M' : '',
						'boat_type' => $job->boat ? $job->boat->boat_type : '',
						'job_duration' => $job->duration ? $job->duration->job_duration : '',
						'job_location' => $job->location ? $job->location->boat_location : '',
						'job_seen_count' => $job->job_seen_count,
						'job_images' => $job->job_images,
						'job_status' => $job->job_status,
						'is_deleted' => $job->is_deleted,
						'created_at' => $job->created_at->format('d F Y')
					];
				});

			return response()->json(['success' => true, 'data' => $campaigns, 'filePath' => url('storage')], 200);
		} else {

			return response()->json(['error' => 'Campaign not found'], 404);
		}
	}

	public function likeCampaign(Request $request)
	{
		$requestData = $request->all();
		$job_id = $requestData['job_id'];
		$employee_id = $requestData['employee_id'];
		$employee = ReachEmployeeDetails::find($employee_id);

		$is_match = "N";
		$data = [];

		if ($employee) {

			$match = ReachLikedmatch::where('job_id', $job_id)->where('employee_id', $employee_id)->where('liked_by', 'EMPLOYEE')->first();
			if ($match) {
				$is_match = "Y";

				$memberDts = ReachEmployeeDetails::with('member')->withActiveMembers()
					->where('employee_id', $employee_id)
					->first()
					->member;

				$data["member_id"] = $memberDts->id;
				$data["member_fname"] = $memberDts->members_fname;
				$data["member_lname"] = $memberDts->members_lname;
				$data["member_profile_picture"] = $memberDts->members_profile_picture;
				$data["active"] = true;
				$data["joined_at"] = date("Y-m-d", strtotime($memberDts->created_at));
				$data["pending_message_count"] = 0;
				$data["last_message_time"] = "";
			}

			ReachLikedmatch::updateOrCreate(
				[
					'job_id' => $job_id,
					'employee_id' => $employee_id,
					'unliked_by' => 'EMPLOYER',
				],
				['liked_by' => 'EMPLOYER', 'unliked_by' => NULL]
			);


			//for notification
			$job = ReachJob::find($job_id);
			$member = $request->user();
			$employeeDetails = ReachEmployeeDetails::getEmployeeDetailsById($employee_id);
			$member_name = $member->members_fname . ' ' . $member->members_lname;
			$jobRole = ReachJobRole::find($job->job_role)->job_role ?? 'Job Role';
			$message = $member_name . ' liked Your Profile ' . $jobRole;
			if ($is_match == 'Y') {
				$url_keyword = 'Employee Match';
			} else {
				$url_keyword = 'Employee';
			}
			$this->notificationService->new_notification($employee_id, $job_id, $member->id, $employeeDetails['member_id'], $message, $url_keyword, $jobRole);
			//for end notification
			return response()->json(['success' => true, 'data' => $data, 'is_match' => $is_match], 200);
		} else {
			return response()->json(['error' => 'Employee details not found'], 404);
		}
	}

	public function removeImage(Request $request)
	{
		$requestData = $request->all();
		$filePath = $requestData['file_path'] ?? null;
		$fileType = $requestData['file_type'] ?? null;

		if ($filePath) {
			// Check if the file exists before attempting to delete
			if (Storage::disk('public')->exists($filePath)) {
				// Delete the file from the storage
				Storage::disk('public')->delete($filePath);
			}

			// Delete the record from the database
			if ($fileType == "job-images") {
				ReachJobMedia::where('media_file', $filePath)->delete();
			} elseif ($fileType == "employee") {
				ReachEmployeeMedia::where('media_file', $filePath)->delete();
			}

			return response()->json(['success' => true, 'message' => 'Image removed successfully'], 200);
		} else {
			return response()->json(['error' => 'File path not provided'], 400);
		}
	}

	public function myLikedList($job_id)
	{
		$likedMatches = ReachLikedmatch::where('job_id', $job_id)
			->where('liked_by', 'EMPLOYER')
			->orderBy('id', 'desc')
			->get()
			->groupBy('employee_id');

		$matchingEmployees = [];

		foreach ($likedMatches as $employeeId => $matches) {

			$employees = ReachEmployeeDetails::with('member')->withActiveMembers()
				->where('employee_status', 'A')
				->where('employee_id', $employeeId)
				->get()
				->map(function ($employee) {
					return [
						'id' => $employee->employee_id,
						'member_id' => $employee->member->id,
						'employee_role' => $employee->jobRole ? $employee->jobRole->job_role : '',
						'member_name' => $employee->member->members_fname . ' ' . $employee->member->members_lname,
						'members_profile_picture' => $employee->member->members_profile_picture,
						'date' => date("d F Y", strtotime($employee->created_at)),
					];
				});

			$matchingEmployees = array_merge($matchingEmployees, $employees->toArray());
		}

		return response()->json(['success' => true, 'data' => $matchingEmployees], 200);
	}

	public function unlikeCampaign(Request $request)
	{
		$requestData = $request->all();

		$job_id = $requestData['job_id'];
		$employee_id = $requestData['employee_id'];
		$employee = ReachEmployeeDetails::find($employee_id);

		if ($employee) {

			ReachLikedmatch::updateOrCreate(
				[
					'job_id' => $job_id,
					'employee_id' => $employee_id,
					'liked_by' => 'EMPLOYER',
				],
				['unliked_by' => 'EMPLOYER', 'liked_by' => NULL]
			);

			return response()->json(['success' => true, 'message' => 'Unlike Employee'], 200);
		} else {
			return response()->json(['error' => 'Employee details not found'], 404);
		}
	}

	public function myDislikedList($job_id)
	{
		$likedMatches = ReachLikedmatch::where('job_id', $job_id)
			->where('unliked_by', 'EMPLOYER')
			->orderBy('id', 'desc')
			->get()
			->groupBy('employee_id');

		$matchingEmployees = [];

		foreach ($likedMatches as $employeeId => $matches) {

			$employees = ReachEmployeeDetails::with('member')->withActiveMembers()
				->where('employee_status', 'A')
				->where('employee_id', $employeeId)
				->get()
				->map(function ($employee) {
					return [
						'id' => $employee->employee_id,
						'member_id' => $employee->member->id,
						'employee_role' => $employee->jobRole ? $employee->jobRole->job_role : '',
						'member_name' => $employee->member->members_fname . ' ' . $employee->member->members_lname,
						'members_profile_picture' => $employee->member->members_profile_picture,
						'date' => date("d F Y", strtotime($employee->created_at)),
					];
				});

			$matchingEmployees = array_merge($matchingEmployees, $employees->toArray());
		}

		return response()->json(['success' => true, 'data' => $matchingEmployees], 200);
	}
}
