<?php

namespace App\Http\Controllers\Api;

use App\Services\NotificationService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

use DateTime;
use DateTimeZone;

use App\Models\ReachMember;
use App\Models\ReachEmployeeDetails;
use App\Models\ReachEmployeeMedia;
use App\Models\JobDetails\ReachJobRole;
use App\Models\ReachCountry;
use App\Models\ReachLanguages;
use App\Models\ReachQualifications;
use App\Models\ReachExperience;
use App\Models\ReachAvailability;
use App\Models\ReachSalaryExpectations;
use App\Models\ReachPositions;
use App\Models\JobDetails\ReachJobDuration;
use App\Models\ReachVesselType;
use App\Models\ReachEmployeeSearch;
use App\Models\ReachJob;
use App\Models\ReachJobMedia;
use App\Models\ReachPageTitle;
use App\Models\ReachLikedmatch;
use App\Models\ReachJobSeen;
use App\Models\Interview_schedule;
use App\Models\Notification;
use Carbon\Carbon;
use App\Models\ReachVisa;

class EmployeeController extends Controller
{
	protected $notificationService;
	public function __construct(NotificationService $notificationService)
	{
		$this->notificationService = $notificationService;
	}
	public function setUpProfile()
	{
		try {
			$jobRoles = ReachJobRole::where('job_role_status', 'A')->pluck('job_role', 'id')->toArray();
			$countries = ReachCountry::where('country_status', 'A')->pluck('country_name', 'id')->toArray();
			$languages = ReachLanguages::where('language_status', 'A')->pluck('language_name', 'lang_id')->toArray();
			$qualifications = ReachQualifications::where('qualification_status', 'A')->pluck('qualification_name', 'qualification_id')->toArray();
			$experience = ReachExperience::where('experience_status', 'A')->pluck('experience_name', 'experience_id')->toArray();
			$availability = ReachAvailability::where('availability_status', 'A')->pluck('availability_name', 'availability_id')->toArray();
			//$positions = ReachPositions::where('position_status', 'A')->pluck('position_name','position_id')->toArray();
			$jobDurations = ReachJobDuration::where('job_duration_status', 'A')->pluck('job_duration', 'id')->toArray();
			$expectations = ReachSalaryExpectations::where('expectation_status', 'A')->pluck('expectation_name', 'expectation_id')->toArray();
			$vesselTypes = ReachVesselType::where('vessel_status', 'A')->pluck('vessel_type', 'vessel_id')->toArray();
			$gender = ['Male' => 'Male', 'Female' => 'Female'];
			$vessel_size = ['<20' => '<20 Meter', '20-30' => '20-30 Meter', '30-40' => '30-40 Meter', '40-50' => '40-50 Meter', '>50' => '>50 Meter'];
			$visa = ReachVisa::where('status', 'A')->pluck('visa', 'id')->toArray();

			$pageDetails = ReachPageTitle::where('page_id', 2)
				->select('page_title', 'page_desc')
				->get()
				->toArray();


			$fullmembersList = ReachMember::where('members_type', 'M')
				->select(
					DB::raw("CONCAT(members_fname, ' ', members_lname) AS full_name"),
					'id'
				)
				->pluck('full_name', 'id')
				->toArray();
			$data = [
				'steps' => [
					[
						'title' => 'STEP 1 - PERSONAL DETAILS',
						'index' => '1',
						'questions' => [
							[
								'title' => $pageDetails[0]['page_title'],
								'sub_title' => $pageDetails[0]['page_desc'],
								'type' => 'checkbox',
								'name' => 'employee_role',
								'options' => $jobRoles,
								'multiple' => true,
								'search' => false,
								'theme' => 'default',
								'validation' => 'Please select a role',
								'sub_index' => '1',
							],
							[
								'title' => $pageDetails[1]['page_title'],
								'sub_title' => $pageDetails[1]['page_desc'],
								'type' => 'radio',
								'name' => 'employee_passport',
								'options' => $countries,
								'multiple' => false,
								'search' => true,
								'validation' => 'Please select a passport',
								'sub_index' => '2',
							],
							[
								'title' => $pageDetails[2]['page_title'],
								'sub_title' => $pageDetails[2]['page_desc'],
								'type' => 'radio',
								'name' => 'employee_avilable',
								'options' => $availability,
								'multiple' => false,
								'search' => false,
								'datepicker' => true,
								'placeholder' => 'Set Specific Date',
								'validation' => 'Please select current availability',
								'sub_index' => '3',
							],
							[
								'title' => $pageDetails[3]['page_title'],
								'sub_title' => $pageDetails[3]['page_desc'],
								'type' => 'date',
								'name' => 'employee_dob',
								'multiple' => false,
								'validation' => 'Please select date of birth',
								'sub_index' => '4',
							],
							[
								'title' => $pageDetails[4]['page_title'],
								'sub_title' => $pageDetails[4]['page_desc'],
								'type' => 'radio',
								'name' => 'employee_location',
								'options' => $countries,
								'multiple' => false,
								'search' => true,
								'validation' => 'Please select a location',
								'sub_index' => '5',
							],
							[
								'title' => $pageDetails[5]['page_title'],
								'sub_title' => $pageDetails[5]['page_desc'],
								'type' => 'radio',
								'name' => 'employee_gender',
								'options' => $gender,
								'multiple' => false,
								'search' => false,
								'validation' => 'Please select a gender',
								'sub_index' => '6',
							],
							[
								'title' => $pageDetails[6]['page_title'],
								'sub_title' => $pageDetails[6]['page_desc'],
								'type' => 'file',
								'name' => 'upload_media',
								'multiple' => true,
								'sub_index' => '7',
							],
						],
					],
					[
						'title' => 'STEP 2 - YOUR WORK',
						'index' => '2',
						'questions' => [
							[
								'title' => $pageDetails[7]['page_title'],
								'sub_title' => $pageDetails[7]['page_desc'],
								'type' => 'checkbox',
								'name' => 'employee_position',
								'options' => $jobDurations,
								'multiple' => true,
								'search' => false,
								'theme' => 'default',
								'validation' => 'Please select a duration',
								'sub_index' => '1',
							],
							[
								'title' => $pageDetails[8]['page_title'],
								'sub_title' => $pageDetails[8]['page_desc'],
								'type' => 'checkbox',
								'name' => 'employee_vessel',
								'options' => $vesselTypes,
								'multiple' => true,
								'search' => false,
								'theme' => 'default',
								'validation' => 'Please select a vessel type',
								'sub_index' => '2',
							],
							[
								'title' => $pageDetails[9]['page_title'],
								'sub_title' => $pageDetails[9]['page_desc'],
								'type' => 'radio',
								'name' => 'employee_salary_expection',
								'options' => $expectations,
								'multiple' => false,
								'search' => false,
								'validation' => 'Please select a salary expectation',
								'sub_index' => '3',
							],
						],
					],
					[
						'title' => 'STEP 3 - HISTORY',
						'index' => '3',
						'questions' => [
							[
								'title' => $pageDetails[10]['page_title'],
								'sub_title' => $pageDetails[10]['page_desc'],
								'type' => 'radio',
								'name' => 'employee_experience',
								'options' => $experience,
								'multiple' => false,
								'search' => false,
								'validation' => 'Please select a experience',
								'sub_index' => '1',
							],
							[
								'title' => $pageDetails[11]['page_title'],
								'sub_title' => $pageDetails[1]['page_desc'],
								'type' => 'textarea',
								'name' => 'employee_last_role',
								'multiple' => false,
								'placeholder' => 'Type details of the job here, dates, responsibilities, salary, location',
								'word_count' => '1000',
								'validation' => 'Please enter your last role',
								'sub_index' => '2',
							],
							[
								'title' => $pageDetails[12]['page_title'],
								'sub_title' => $pageDetails[12]['page_desc'],
								'type' => 'checkbox',
								'name' => 'employee_qualification',
								'options' => $qualifications,
								'multiple' => true,
								'search' => true,
								'theme' => 'minimal',
								'validation' => 'Please select a qualification',
								'sub_index' => '3',
							],
						],
					],
					[
						'title' => 'STEP 4 - OTHER INFORMATION',
						'index' => '4',
						'questions' => [
							[
								'title' => $pageDetails[13]['page_title'],
								'sub_title' => $pageDetails[13]['page_desc'],
								'type' => 'checkbox',
								'name' => 'employee_languages',
								'options' => $languages,
								'multiple' => true,
								'search' => true,
								'theme' => 'default',
								'validation' => 'Please select a language',
								'sub_index' => '1',
							],
							[
								'title' => $pageDetails[14]['page_title'],
								'sub_title' => $pageDetails[14]['page_desc'],
								'type' => 'checkbox',
								'name' => 'employee_visa',
								'options' => $visa,
								'multiple' => true,
								'search' => false,
								'theme' => 'default',
								'validation' => 'Please select a visa',
								'sub_index' => '2',
							],
							[
								'title' => $pageDetails[15]['page_title'],
								'sub_title' => $pageDetails[15]['page_desc'],
								'type' => 'text',
								'name' => 'employee_interest',
								'multiple' => true,
								'validation' => 'Please enter your interest',
								'sub_index' => '3',
							],
						],
					],
					[
						'title' => 'STEP 5 - ABOUT YOU',
						'index' => '5',
						'questions' => [
							[
								'title' => $pageDetails[16]['page_title'],
								'sub_title' => $pageDetails[16]['page_desc'],
								'type' => 'textarea',
								'name' => 'employee_about',
								'placeholder' => 'Your best moments, greatest achievements, goals and ambition, loves and loathes...',
								'multiple' => false,
								'validation' => 'Please enter a brief bio',
								'sub_index' => '1',
							],
							[
								'title' => $pageDetails[17]['page_title'],
								'sub_title' => $pageDetails[17]['page_desc'],
								'type' => 'textarea',
								'name' => 'employee_intro',
								'placeholder' => '20 words or less which is the intro to you.',
								'multiple' => false,
								'validation' => 'Please enter a introduction',
								'sub_index' => '2',
							],
						],
					],
					[
						'title' => 'COMPLETE!',
						'index' => '6',
						'questions' => [
							[
								'title' => $pageDetails[18]['page_title'],
								'sub_title' => $pageDetails[18]['page_desc'],
								'sub_index' => '1',
								'sub_questions' => [
									[

										'title' => 'Vessel Type',
										'type' => 'select',
										'name' => 'search_vessel_type',
										'options' => $vesselTypes,
										'multiple' => true,
										//'validation' => 'Please select a vessel type',
										'validation' => '',
									],
									[
										'title' => 'Duration',
										'type' => 'select',
										'name' => 'search_position',
										'options' => $jobDurations,
										'multiple' => true,
										//'validation' => 'Please select a duration',
										'validation' => '',
									],
									[
										'title' => 'Size',
										'type' => 'select',
										'name' => 'search_size',
										'options' => $vessel_size,
										'multiple' => true,
										//'validation' => 'Please select a vessel size',
										'validation' => '',
									],
								],
							],
							[
								'title' => 'Reference',
								'type' => 'dropdown',
								'name' => 'referrance',
								'options' => $fullmembersList,
								'multiple' => true,
								'validation' => 'Please select a vessel type',
							],
						],
					],
				]
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

	public function saveProfile(Request $request)
	{
		$requestData = $request->all();

		$validator = Validator::make($requestData, [
			'employee_role' => 'required'
		], [
			'employee_role.required' => 'The role is required.'
		]);

		if ($validator->fails()) {
			return response()->json(['errors' => $validator->errors()], 422);
		} else {

			$member = $request->user();

			$requestData['member_id'] = $member->id;

			try {

				// Calculate age from employee_dob
				if (isset($requestData['employee_dob'])) {

					$dob = new DateTime($requestData['employee_dob']);
					$now = new DateTime();
					$age = $now->diff($dob)->y;
					$requestData['employee_age'] = $age;
				}

				if (isset($requestData['employee_avilable'])) {

					$avilable_date = $requestData['employee_avilable'];
					$date = DateTime::createFromFormat('Y-m-d', $avilable_date);

					if ($date && $date->format('Y-m-d') === $avilable_date) {
						$requestData['employee_avilable_date'] = $avilable_date;
						unset($requestData['employee_avilable']);
					}
				}

				$fieldsToConvert = [
					'employee_role',
					'employee_position',
					'employee_vessel',
					'employee_visa',
					'employee_qualification',
					'employee_languages',
					'referrance'
				];

				foreach ($fieldsToConvert as $field) {

					if (isset($requestData[$field]) && is_array($requestData[$field])) {
						$requestData[$field] = implode(',', $requestData[$field]);
					}

					/*if (isset($requestData[$field]) && is_array($requestData[$field])) {
																																																$requestData[$field] = implode(',', array_keys($requestData[$field]));
																																															}*/
				}

				if (isset($requestData['employee_interest']) && is_array($requestData['employee_interest'])) {
					$interestValues = array_column($requestData['employee_interest'], 'value');
					$requestData['employee_interest'] = implode(',', $interestValues);
				}


				$employee = ReachEmployeeDetails::where('member_id', $member->id)->first();

				if ($employee) {
					$employee->update($requestData);
					$employeeId = $employee->employee_id;
				} else {

					$employee = ReachEmployeeDetails::create($requestData);
					$employeeId = $employee->employee_id;
				}

				if (!empty(isset($requestData['upload_media']))) {
					foreach ($requestData['upload_media'] as $file) {
						ReachEmployeeMedia::updateOrCreate(
							['employee_id' => $employeeId, 'media_file' => $file],
							[]
						);
					}
				}

				$searchData = [
					'vessel_type' => $requestData['search_vessel_type'] ?? null,
					'vessel_size' => $requestData['search_size'] ?? null,
					'job_duration' => $requestData['search_position'] ?? null,
					//'employee_visa' => $requestData['search_visa'] ?? null,
				];

				ReachEmployeeSearch::where('employee_id', $employeeId)->forceDelete();

				foreach ($searchData as $searchParameterName => $searchValues) {
					if ($searchValues !== null) {
						foreach ($searchValues as $searchKey => $searchValue) {
							ReachEmployeeSearch::create([
								'employee_id' => $employeeId,
								'search_parameter_name' => $searchParameterName,
								'search_value' => $searchValue,
							]);
						}
					}
				}

				return response()->json(['success' => true, 'message' => isset($requestData['employee_id']) ? 'Employee updated successfully' : 'Employee created successfully', 'data' => ['employeeId' => $employeeId]], 200);
			} catch (\Exception $e) {

				return response()->json(['error' => 'Failed to create employee: ' . $e->getMessage()], 500);
			}
		}
	}

	public function editsetUpProfile($employee_id)
	{
		$employee = ReachEmployeeDetails::select('employee_id', 'employee_role', 'employee_passport', 'employee_avilable', 'employee_avilable_date', 'employee_dob', 'employee_location', 'employee_gender', 'employee_experience', 'employee_position', 'employee_vessel', 'employee_salary_expection', 'employee_salary_expection', 'employee_qualification', 'employee_languages', 'employee_visa', 'employee_interest', 'employee_last_role', 'employee_about', 'employee_intro', 'referrance')
			->find($employee_id);

		if ($employee) {

			if ($employee->employee_avilable_date != '') {
				$employee->employee_avilable = $employee->employee_avilable_date;
			}

			$employee->employee_role = explode(',', $employee->employee_role);
			$employee->employee_position = explode(',', $employee->employee_position);
			$employee->employee_vessel = explode(',', $employee->employee_vessel);
			$employee->employee_visa = explode(',', $employee->employee_visa);
			$employee->employee_qualification = explode(',', $employee->employee_qualification);
			$employee->employee_languages = explode(',', $employee->employee_languages);
			//$employee->employee_interest = explode(',', $employee->employee_interest);
			$employee->employee_interest = collect(explode(',', $employee->employee_interest))
				->map(function ($item) {
					return ['value' => trim($item)];
				});
			$employee->referrance = explode(',', $employee->referrance);
			$employee->referrance = array_map('intval', $employee->referrance);
			$upload_media = ReachEmployeeMedia::where('employee_id', $employee_id)->pluck('media_file')->toArray();

			$employee['upload_media'] = $upload_media;

			$searchVesselType = [];
			$searchVesselSize = [];
			$searchPosition = [];
			$searchParameter = [];

			$searchParameters = ReachEmployeeSearch::where('employee_id', $employee['employee_id'])
				->select('search_parameter_name', 'search_value')
				->get()
				->toArray();

			foreach ($searchParameters as $param) {
				if ($param['search_parameter_name'] == "vessel_type") {
					$vesselType = ReachVesselType::where('vessel_id', $param['search_value'])->value('vessel_id');

					$searchVesselType[] = $vesselType;
				} elseif ($param['search_parameter_name'] == "job_duration") {
					//$position = ReachPositions::where('position_id', $param['search_value'])->value('position_id');
					$duration = ReachJobDuration::where('id', $param['search_value'])->value('id');

					$searchPosition[] = $duration;
				} elseif ($param['search_parameter_name'] == "vessel_size") {

					$searchVesselSize[] = $param['search_value'];
				} else {
					$searchParameter[$param['search_parameter_name']][] = $param['search_value'];
				}
			}

			$employee['search_vessel_type'] = $searchVesselType;
			$employee['search_position'] = $searchPosition;
			$employee['search_size'] = $searchVesselSize;

			return response()->json([
				'success' => true,
				'data' => $employee
			], 200);
		} else {
			return response()->json(['error' => 'Employee not found'], 404);
		}
	}

	public function reviewProfile(Request $request)
	{
		$member = $request->user();

		if ($member) {

			$employeeDetails = ReachEmployeeDetails::getEmployeeDetailsByMemberId($member->id);

			if ($employeeDetails->employee_avilable_date != '') {
				$employeeDetails->employee_avilable = date("d F Y", strtotime($employeeDetails->employee_avilable_date));
			}

			$data['employeeDetails'] = $employeeDetails;
			$data['mediaDetails'] = [];
			$data['searchParameters'] = [];
			$data['searchParameterName'] = [];
			//$data['referrance'] = [];

			if ($employeeDetails) {

				$searchParameters = ReachEmployeeSearch::where('employee_id', $employeeDetails->employee_id)
					->select('search_parameter_name', 'search_value')
					->get()
					->toArray();

				$vessel_types = [];
				$positonTypes = [];
				foreach ($searchParameters as $param) {
					if ($param['search_parameter_name'] == "vessel_type") {
						$vessels = ReachVesselType::where('vessel_id', $param['search_value'])->get(['vessel_id', 'vessel_type']);
						foreach ($vessels as $vessel) {
							$data['searchParameters'][$param['search_parameter_name']][] = $vessel->vessel_id;
							$vessel_types[$param['search_parameter_name']][] = $vessel->vessel_type;
						}
						$data['searchParameterName'][$param['search_parameter_name']] = implode(', ', $vessel_types[$param['search_parameter_name']]);
					} elseif ($param['search_parameter_name'] == "job_duration") {
						//$positions = ReachPositions::where('position_id', $param['search_value'])->get(['position_id','position_name']);
						$job_duration = ReachJobDuration::where('id', $param['search_value'])->get(['id', 'job_duration']);
						foreach ($job_duration as $duration) {
							$data['searchParameters'][$param['search_parameter_name']][] = $duration->id;
							$positonTypes[$param['search_parameter_name']][] = $duration->job_duration;
						}
						$data['searchParameterName'][$param['search_parameter_name']] = implode(', ', $positonTypes[$param['search_parameter_name']]);
					} else {
						$data['searchParameters'][$param['search_parameter_name']][] = $param['search_value'];
						$data['searchParameterName'][$param['search_parameter_name']][] = $param['search_value'];
					}
				}
				foreach ($data['searchParameterName'] as $key => $values) {
					if (is_array($values)) {

						$valuesWithM = array_map(fn($value) => $value . 'm', $values);
						$data['searchParameterName'][$key] = implode(', ', $valuesWithM);
					}
				}
				$data['mediaDetails'] = ReachEmployeeMedia::where('employee_id', $employeeDetails->employee_id)->select('id', 'media_file')->get();
			}

			return response()->json(['success' => true, 'data' => $data], 200);
		} else {

			return response()->json(['error' => 'Unauthorized'], 401);
		}
	}

	public function dashboard(Request $request)
	{
		$member = $request->user();

		$employeeDts = ReachEmployeeDetails::where('employee_status', 'A')
			->where('member_id', $member->id)
			->first();

		if ($employeeDts) {

			$job_ids = [];
			$job_images = [];

			// Get all liked matches for the job
			$likedMatches = ReachLikedmatch::where('employee_id', $employeeDts->employee_id)
				->where(function ($query) {
					$query->where('liked_by', 'EMPLOYEE')
						->orWhere('liked_by', 'EMPLOYER');
				})
				->orderBy('id', 'desc')
				->get()
				->groupBy('job_id');

			$matchingJobs = [];

			foreach ($likedMatches as $jobId => $matches) {
				$likedBy = $matches->pluck('liked_by')->toArray();

				if (in_array('EMPLOYEE', $likedBy) && in_array('EMPLOYER', $likedBy)) {

					$campaigns = ReachJob::with(['member', 'role', 'location'])->withActiveMembers()
						->where('job_status', 'A')
						->where('id', $jobId)
						->get()
						->map(function ($job) {
							return [
								'id' => $job->id,
								'member_id' => $job->member_id,
								'member_name' => $job->member->members_fname . ' ' . $job->member->members_lname,
								'job_role' => $job->role ? $job->role->job_role : '',
								'job_location' => $job->location ? $job->location->boat_location : '',
								'job_images' => $job->job_images ? $job->job_images : '',
								'date' => date("d F Y", strtotime($job->created_at)),
							];
						});

					$matchingJobs = array_merge($matchingJobs, $campaigns->toArray());
				}
			}

			// Retrieve and map search parameters
			$searchParameters = ReachEmployeeSearch::where('employee_id', $employeeDts->employee_id)
				->select('search_parameter_name', 'search_value')
				->get()
				->mapWithKeys(function ($searchParameter) {
					return [$searchParameter->search_parameter_name => $searchParameter->search_value];
				})
				->toArray();



			// Retrieve job IDs that are liked by the employee
			$likedJobIds = ReachLikedmatch::where('employee_id', $employeeDts->employee_id)
				->where(function ($query) {
					$query->where('liked_by', 'EMPLOYEE')
						->orWhere('unliked_by', 'EMPLOYEE');
				})
				->pluck('job_id')
				->toArray();

			// Retrieve matching job using search parameters
			$matchingJobQuery = ReachJob::with('member')->withActiveMembers()
				->where('job_status', 'A')
				->where('member_id', '!=', $member->id)
				->whereRaw('FIND_IN_SET(job_role, ?)', [$employeeDts->employee_role])
				->whereNotIn('id', $likedJobIds);
			if ($searchParameters) {
				/*$matchingJobQuery->where(function ($query) use ($searchParameters) {
																																									foreach ($searchParameters as $paramName => $paramValue) {
																																										$query->orWhere($paramName, $paramValue);
																																									}
																																								});	*/
				$matchingJobQuery->where(function ($query) use ($searchParameters) {
					foreach ($searchParameters as $paramName => $paramValue) {
						if ($paramName == "vessel_size") {
							if ($paramValue == '<20') {
								$query->orWhere('vessel_size', '<', 20);
							} elseif ($paramValue == '>50') {
								$query->orWhere('vessel_size', '>', 50);
							} else {
								$sizeRange = explode('-', $paramValue);
								if (count($sizeRange) == 2) {
									$query->orWhereBetween('vessel_size', [$sizeRange[0], $sizeRange[1]]);
								}
							}
						} else {
							$query->orWhere($paramName, $paramValue);
						}
					}
				});
			}
			$jobCount = $matchingJobQuery->count();

			$matchingJobQuery->get()
				->each(function ($job) use (&$job_ids, &$job_images) {

					$job_ids[] = $job->id;

					if ($job->job_images != '') {
						$job_images[] = $job->job_images;
					}
				});

			/*$matchingJobs = $matchingJobQuery->get()
																														 ->map(function ($job) {
																															 return [
																																 'job_id' => $job->id,
																																 'job_role' => $job->role ? $job->role->job_role : '',
																																 'members_name' => $job->member->members_fname.' '.$job->member->members_lname,
																																 'job_images' => $job->job_images ? $job->job_images : '',
																															 ];
																														 })->toArray();*/



			$employeeDetails = ReachEmployeeDetails::getEmployeeDetailsByMemberId($member->id);
			//print_r($employeeDetails);die();
			$data['searchParameters'] = [];

			if ($employeeDetails) {

				$searchParameters = ReachEmployeeSearch::where('employee_id', $employeeDetails->employee_id)
					->select('search_parameter_name', 'search_value')
					->get()
					->toArray();
				//print_r($searchParameters);die();
				$vessel_types = [];
				$positonTypes = [];
				foreach ($searchParameters as $param) {
					if ($param['search_parameter_name'] == "vessel_type") {
						$vessels = ReachVesselType::where('vessel_id', $param['search_value'])->get(['vessel_id', 'vessel_type']);
						foreach ($vessels as $vessel) {

							$vessel_types[$param['search_parameter_name']][] = $vessel->vessel_type;
						}
						$data['searchParameters'][$param['search_parameter_name']] = implode(', ', $vessel_types[$param['search_parameter_name']]);
					} elseif ($param['search_parameter_name'] == "job_duration") {
						//$positions = ReachPositions::where('position_id', $param['search_value'])->get(['position_id','position_name']);
						$job_duration = ReachJobDuration::where('id', $param['search_value'])->get(['id', 'job_duration']);
						foreach ($job_duration as $duration) {

							$positonTypes[$param['search_parameter_name']][] = $duration->job_duration;
						}
						$data['searchParameters'][$param['search_parameter_name']] = implode(', ', $positonTypes[$param['search_parameter_name']]);
					} else {
						$data['searchParameters'][$param['search_parameter_name']][] = $param['search_value'];
					}
				}

				foreach ($data['searchParameters'] as $key => $values) {
					if (is_array($values)) {
						//$data['searchParameterName'][$key] = implode(', ', $values);
						$valuesWithM = array_map(fn($value) => $value . 'M', $values);
						$data['searchParameters'][$key] = implode(', ', $valuesWithM);
					}
				}
				$data['searchParameters']['job_role'] = $employeeDetails['employee_role'];
			}
			// Limit the results to 3
			$matchingJobs = array_slice($matchingJobs, 0, 3);
			$job_images = array_slice($job_images, 0, 3);
			$data['member_id'] = $member->id;
			$data['matches'] = $matchingJobs;
			$data['job_count'] = $jobCount;
			$data['job_images'] = $job_images;
			$data['job_ids'] = $job_ids;

			return response()->json(['success' => true, 'data' => $data], 200);
		} else {

			return response()->json(['error' => 'Employee details not found'], 404);
		}
	}

	public function myMatchesList(Request $request)
	{
		$member = $request->user();
		$employeeDts = ReachEmployeeDetails::where('employee_status', 'A')
			->where('member_id', $member->id)
			->first();

		if ($employeeDts) {

			$likedMatches = ReachLikedmatch::where('employee_id', $employeeDts->employee_id)
				->where(function ($query) {
					$query->where('liked_by', 'EMPLOYEE')
						->orWhere('liked_by', 'EMPLOYER');
				})
				->join('reach_jobs', 'reach_liked_match.job_id', '=', 'reach_jobs.id') // Join ReachLikedmatch with ReachJob using job_id
				->join('reach_members', 'reach_jobs.member_id', '=', 'reach_members.id') // Join ReachJob with ReachMember using member_id
				->where('reach_members.members_status', 'A')
				->orderBy('reach_liked_match.id', 'desc')
				->get('reach_liked_match.*')
				->groupBy('job_id');


			$matchingJobs = [];

			foreach ($likedMatches as $jobId => $matches) {
				$likedBy = $matches->pluck('liked_by')->toArray();

				if (in_array('EMPLOYEE', $likedBy) && in_array('EMPLOYER', $likedBy)) {

					$campaigns = ReachJob::with(['member', 'role', 'location'])->withActiveMembers()
						->where('job_status', 'A')
						->where('id', $jobId)
						->whereHas('member', function ($query) {
							$query->where('members_status', 'A');
						})
						->get()
						->map(function ($job) {
							return [
								'id' => $job->id,
								'member_id' => $job->member_id,
								'member_name' => $job->member ? $job->member->members_fname . ' ' . $job->member->members_lname : '',
								// 'member_name' => $job->member->members_fname.' '.$job->member->members_lname,
								'job_role' => $job->role ? $job->role->job_role : '',
								'job_location' => $job->location ? $job->location->boat_location : '',
								'job_images' => $job->job_images ? $job->job_images : '',
								'date' => date("d F Y", strtotime($job->created_at)),
							];
						});

					$matchingJobs = array_merge($matchingJobs, $campaigns->toArray());
				}
			}

			return response()->json(['success' => true, 'data' => $matchingJobs], 200);
		} else {
			return response()->json(['error' => 'Matches not found'], 404);
		}
	}

	public function viewAvailableJobs(Request $request)
	{
		$member = $request->user();

		$employeeDts = ReachEmployeeDetails::where('employee_status', 'A')
			->where('member_id', $member->id)
			->first();

		if ($employeeDts) {

			$job_ids = [];

			// Retrieve and map search parameters
			$searchParameters = ReachEmployeeSearch::where('employee_id', $employeeDts->employee_id)
				->select('search_parameter_name', 'search_value')
				->get()
				->mapWithKeys(function ($searchParameter) {
					return [$searchParameter->search_parameter_name => $searchParameter->search_value];
				})
				->toArray();


			// Retrieve job IDs that are liked by the employee
			$likedJobIds = ReachLikedmatch::where('employee_id', $employeeDts->employee_id)
				->where(function ($query) {
					$query->where('liked_by', 'EMPLOYEE')
						->orWhere('unliked_by', 'EMPLOYEE');
				})
				->pluck('job_id')
				->toArray();

			// Retrieve matching job using search parameters
			$matchingJobQuery = ReachJob::with('member')->withActiveMembers()
				->where('job_status', 'A')
				->where('member_id', '!=', $member->id)
				->whereRaw('FIND_IN_SET(job_role, ?)', [$employeeDts->employee_role])
				->whereNotIn('id', $likedJobIds);

			if ($searchParameters) {
				$matchingJobQuery->where(function ($query) use ($searchParameters) {
					foreach ($searchParameters as $paramName => $paramValue) {
						if ($paramName == "vessel_size") {
							if ($paramValue == '<20') {
								$query->orWhere('vessel_size', '<', 20);
							} elseif ($paramValue == '>50') {
								$query->orWhere('vessel_size', '>', 50);
							} else {
								$sizeRange = explode('-', $paramValue);
								if (count($sizeRange) == 2) {
									$query->orWhereBetween('vessel_size', [$sizeRange[0], $sizeRange[1]]);
								}
							}
						} else {
							$query->orWhere($paramName, $paramValue);
						}
					}
				});
			}

			$jobCount = $matchingJobQuery->count();

			$matchingJobQuery->get()
				->each(function ($job) use (&$job_ids) {
					$job_ids[] = $job->id;
				});

			$data['matches'] = $jobCount;
			$data['job_ids'] = $job_ids;

			return response()->json(['success' => true, 'data' => $data], 200);
		} else {

			return response()->json(['error' => 'Employee details not found'], 404);
		}
	}

	public function availableJobsList(Request $request)
	{
		$requestData = $request->all();

		$employee_id = $requestData['employee_id'];
		$job_ids = $requestData['job_ids'];
		$page = $requestData['page'] ?? 1;
		$limit = 1;

		$employeeDts = ReachEmployeeDetails::where('employee_status', 'A')
			->where('employee_id', $employee_id)
			->first();

		if ($employeeDts) {

			// Retrieve matching employees using employee_ids
			$matchingJobsQuery = ReachJob::with('member')->withActiveMembers()
				->where('job_status', 'A')
				->whereIn('id', $job_ids);

			$matchingJobs = $matchingJobsQuery->paginate($limit, ['*'], 'page', $page);

			$formattedJobs = $matchingJobs->map(function ($job) use ($employee_id) {

				$is_match = ReachLikedmatch::where('job_id', $job->id)
					->where('employee_id', $employee_id)
					->where('liked_by', 'EMPLOYEE')
					->first();

				$is_liked = ReachLikedmatch::where('job_id', $job->id)
					->where('employee_id', $employee_id)
					->where('liked_by', 'EMPLOYER')
					->first();

				$data["member_id"] = $job->member->id;
				$data["member_fname"] = $job->member->members_fname;
				$data["member_lname"] = $job->member->members_lname;
				$data["member_profile_picture"] = $job->member->members_profile_picture;
				$data["active"] = true;
				$data["joined_at"] = date("Y-m-d", strtotime($job->member->created_at));
				$data["pending_message_count"] = 0;
				$data["last_message_time"] = "";

				$jobData = [
					'id' => $job->id,
					'job_role' => $job->role ? $job->role->job_role : '',
					'boat_type' => $job->boat ? $job->boat->boat_type : '',
					'job_duration' => $job->duration ? $job->duration->job_duration : '',
					'job_location' => $job->location ? $job->location->boat_location : '',
					'boat_location' => $job->location ? $job->location->boat_location : '',
					'vessel_desc' => $job->vessel_desc,
					'vessel_size' => $job->vessel_size . 'm',
					'vessel_type' => $job->vessel ? $job->vessel->vessel_type : '',
					'job_start_date' => date("d F Y", strtotime($job->job_start_date)),
					'job_summary' => $job->job_summary,
					'job_images' => ReachJobMedia::where('job_id', $job->id)->select('id', 'media_file')->get(),
					'is_match' => $is_match && $is_liked ? "Y" : "N",
					'is_liked' => $is_liked ? "Y" : "N",
					'member' => $data,


				];
				return $jobData;
			})->toArray();

			if (!empty($formattedJobs)) {
				$job = ReachJob::find($formattedJobs[0]['id']);
				if ($job) {

					//Job Seen Count
					ReachJobSeen::updateOrCreate(
						[
							'job_id' => $formattedJobs[0]['id'],
							'employee_id' => $employee_id,
						],
						[]
					);

					// Get total count of Job Seen by job_id
					$jobSeenCount = ReachJobSeen::where('job_id', $formattedJobs[0]['id'])->count();

					$job->update(['job_seen_count' => $jobSeenCount]);
				}
			}

			return response()->json([
				'success' => true,
				'data' => $formattedJobs,
				'job_ids' => $job_ids,
				'current_page' => $matchingJobs->currentPage(),
				'next_page' => $matchingJobs->currentPage() + 1,
				'total_pages' => $matchingJobs->lastPage()
			], 200);
		} else {

			return response()->json(['error' => 'Employee details not found'], 404);
		}
	}

	public function previewProfile($id)
	{
		$employeeDetails = ReachEmployeeDetails::getEmployeeDetailsById($id);

		if ($employeeDetails) {

			$data['employeeDetails'] = $employeeDetails;
			$data['mediaDetails'] = [];
			if ($employeeDetails) {
				$data['mediaDetails'] = ReachEmployeeMedia::where('employee_id', $employeeDetails->employee_id)->select('id', 'media_file')->get();
			}

			return response()->json(['success' => true, 'data' => $data], 200);
		} else {

			return response()->json(['error' => 'Unauthorized'], 401);
		}
	}

	public function deleteProfile(Request $request)
	{
		$member = $request->user();
		if ($member) {

			/*$employee->is_deleted = 'Y';
																														$employee->deleted_date = date('Y-m-d H:i:s');
																														$employee->save();*/

			return response()->json(['success' => true, 'message' => 'Profile deleted successfully'], 200);
		} else {

			return response()->json(['error' => 'Unauthorized'], 401);
		}
	}

	public function likeEmployee(Request $request)
	{
		$requestData = $request->all();

		$job_id = $requestData['job_id'];
		$employee_id = $requestData['employee_id'];

		$job = ReachJob::find($job_id);

		$is_match = "N";
		$data = [];
		$member = $request->user();

		if ($job) {

			$match = ReachLikedmatch::where('job_id', $job_id)->where('employee_id', $employee_id)->where('liked_by', 'EMPLOYER')->first();
			if ($match) {
				$is_match = "Y";

				$memberDts = ReachJob::with('member')->withActiveMembers()
					->where('id', $job_id)
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
					'unliked_by' => 'EMPLOYEE',
				],
				['liked_by' => 'EMPLOYEE', 'unliked_by' => NULL]
			);

			//for notification
			$memberDts = ReachJob::with('member')->withActiveMembers()
				->where('id', $job_id)
				->first()
				->member;
			$employeeDetails = ReachEmployeeDetails::getEmployeeDetailsById($employee_id);
			$employee_name = $employeeDetails['members_fname'] . ' ' . $employeeDetails['members_lname'];

			$jobRole = ReachJobRole::where('id', $job->job_role)->value('job_role');
			$message = $employee_name . ' liked your Job Role ' . $jobRole;
			if ($is_match == 'Y') {
				$url_keyword = 'Job Match';
			} else {
				$url_keyword = 'Job';
			}

			$this->notificationService->new_notification($employee_id, $job_id, $member->id, $memberDts->id, $message, $url_keyword, $jobRole);
			//for end notification
			return response()->json(['success' => true, 'data' => $data, 'is_match' => $is_match], 200);
		} else {
			return response()->json(['error' => 'Job details not found'], 404);
		}
	}

	public function employeeSetStatus(Request $request)
	{
		$member = $request->user();

		if ($member) {
			$requestData = $request->all();

			$employeeDts = ReachEmployeeDetails::where('member_id', $member->id)->first();

			if ($employeeDts) {
				$employeeDts->update($requestData);

				return response()->json(['success' => true, 'message' => 'OK'], 200);
			} else {
				return response()->json(['error' => 'Employee details not found'], 404);
			}
		} else {
			return response()->json(['error' => 'Unauthorized'], 401);
		}
	}

	public function myLikedList(Request $request)
	{

		$member = $request->user();

		$employeeDts = ReachEmployeeDetails::where('member_id', $member->id)
			->first();
		//print("<PRE>");print_r($employeeDts);die();	

		if ($employeeDts) {

			$likedJobIds = ReachLikedmatch::where('employee_id', $employeeDts->employee_id)
				->where('liked_by', 'EMPLOYER')
				->pluck('job_id')
				->toArray();

			$likedMatches = ReachLikedmatch::where('employee_id', $employeeDts->employee_id)
				->where('liked_by', 'EMPLOYEE')
				//->whereNotIn('job_id', $likedJobIds)
				->orderBy('id', 'desc')
				->get()
				->groupBy('job_id');


			$matchingJobs = [];

			foreach ($likedMatches as $jobId => $matches) {

				$campaigns = ReachJob::with(['member', 'role', 'location'])->withActiveMembers()
					->where('job_status', 'A')
					->where('id', $jobId)
					->get()
					->map(function ($job) {
						return [
							'id' => $job->id,
							'member_id' => $job->member_id,
							'member_name' => $job->member ? $job->member->members_fname . ' ' . $job->member->members_lname : '',
							'job_role' => $job->role ? $job->role->job_role : '',
							'job_images' => $job->job_images ? $job->job_images : '',
							'date' => date("d F Y", strtotime($job->created_at)),
						];
					});

				$matchingJobs = array_merge($matchingJobs, $campaigns->toArray());
			}

			return response()->json(['success' => true, 'data' => $matchingJobs], 200);
		} else {
			return response()->json(['error' => 'Unauthorized'], 401);
		}
	}

	public function unlikeEmployee(Request $request)
	{
		$requestData = $request->all();

		$job_id = $requestData['job_id'];
		$employee_id = $requestData['employee_id'];
		$job = ReachJob::find($job_id);

		if ($job) {

			ReachLikedmatch::updateOrCreate(
				[
					'job_id' => $job_id,
					'employee_id' => $employee_id,
					'liked_by' => 'EMPLOYEE',
				],
				['unliked_by' => 'EMPLOYEE', 'liked_by' => NULL]
			);

			return response()->json(['success' => true, 'message' => 'Unlike Campaign'], 200);
		} else {
			return response()->json(['error' => 'Job details not found'], 404);
		}
	}

	public function myDislikedList(Request $request)
	{
		$member = $request->user();
		$employeeDts = ReachEmployeeDetails::where('member_id', $member->id)
			->first();

		if ($employeeDts) {

			$likedMatches = ReachLikedmatch::where('employee_id', $employeeDts->employee_id)
				->where('unliked_by', 'EMPLOYEE')
				->orderBy('id', 'desc')
				->get()
				->groupBy('job_id');

			$matchingJobs = [];

			foreach ($likedMatches as $jobId => $matches) {

				$campaigns = ReachJob::with(['member', 'role', 'location'])->withActiveMembers()
					->where('job_status', 'A')
					->where('id', $jobId)
					->get()
					->map(function ($job) {
						return [
							'id' => $job->id,
							'member_id' => $job->member_id,
							'member_name' => $job->member->members_fname . ' ' . $job->member->members_lname,
							'job_role' => $job->role ? $job->role->job_role : '',
							'job_images' => $job->job_images ? $job->job_images : '',
							'date' => date("d F Y", strtotime($job->created_at)),
						];
					});

				$matchingJobs = array_merge($matchingJobs, $campaigns->toArray());
			}

			return response()->json(['success' => true, 'data' => $matchingJobs], 200);
		} else {
			return response()->json(['error' => 'Unauthorized'], 401);
		}
	}
	public function myInterviewList(Request $request)
	{
		$member = $request->user();
		$employeeDts = ReachEmployeeDetails::where('member_id', $member->id)->first();
		if (!empty($employeeDts)) {
			$employeeId = $employeeDts->employee_id;
			$interviewList = Interview_schedule::where('reach_interview_schedule.employee_id', $employeeId)
				->select(
					'reach_interview_schedule.id',
					'reach_interview_schedule.interview_date',
					'reach_interview_schedule.interview_uk_time as interview_time',
					'reach_interview_schedule.interview_status',
				)
				->orderByRaw("FIELD(reach_interview_schedule.interview_status, 'P', 'A', 'H', 'C')")
				->orderBy('reach_interview_schedule.interview_date', 'asc')
				->orderBy('reach_interview_schedule.interview_time', 'asc')
				->get();
			$currentUKTime = Carbon::now('Europe/London');
			foreach ($interviewList as $interview) {
				$interviewDateTime = Carbon::parse($interview->interview_date . ' ' . $interview->interview_time, 'Europe/London');
				if ($currentUKTime->greaterThan($interviewDateTime->addHour(1))) {
					Interview_schedule::where('id', $interview->id)->update([
						'interview_status' => 'H'
					]);
				}
			}
			$interviewList = Interview_schedule::where('reach_interview_schedule.employee_id', $employeeId)
				->join('reach_jobs', 'reach_interview_schedule.job_id', '=', 'reach_jobs.id')
				->join('reach_job_roles', 'reach_jobs.job_role', '=', 'reach_job_roles.id')
				->join('reach_boat_location', 'reach_jobs.job_location', '=', 'reach_boat_location.id')
				->join('reach_members', 'reach_jobs.member_id', '=', 'reach_members.id')
				->select(
					'reach_interview_schedule.id',
					'reach_interview_schedule.interview_date',
					'reach_interview_schedule.interview_uk_time as interview_time',
					'reach_interview_schedule.interview_status',
					'reach_jobs.member_id',
					'reach_jobs.job_summary',
					'reach_job_roles.job_role',
					'reach_boat_location.boat_location as location',
					'reach_members.members_profile_picture',
					'reach_interview_schedule.employee_id',
					'reach_interview_schedule.job_id',
					'reach_interview_schedule.meeting_id',
					\DB::raw("CONCAT(reach_members.members_fname, ' ', reach_members.members_lname) AS full_name")
				)
				->whereNotIn('interview_status', ['C', 'H'])
				->orderByRaw("FIELD(reach_interview_schedule.interview_status, 'P', 'A', 'H', 'C')")
				->orderBy('reach_interview_schedule.interview_date', 'asc')
				->orderBy('reach_interview_schedule.interview_time', 'asc')
				->get();
			return response()->json(['success' => true, 'data' => $interviewList], 200);
		} else {
			return response()->json(['success' => false, 'data' => []], 200);
		}
		//print_r($interviewList);die();
	}

	public function jobInterviewList(Request $request)
	{
		$member = $request->user();
		$campaigns = ReachJob::where('member_id', $member->id)->withTrashed()->get();
		$jobIds = $campaigns->pluck('id')->toArray();
		$jobList = Interview_schedule::whereIn('job_id', $jobIds)
			->select(
				'reach_interview_schedule.id',
				'reach_interview_schedule.interview_date',
				'reach_interview_schedule.interview_uk_time as interview_time',
				'reach_interview_schedule.interview_status'

			)

			->orderByRaw("FIELD(reach_interview_schedule.interview_status, 'P', 'A', 'H', 'C')")
			->orderBy('reach_interview_schedule.interview_date', 'asc')
			->orderBy('reach_interview_schedule.interview_time', 'asc')
			->get();

		$currentUKTime = Carbon::now('Europe/London');
		foreach ($jobList as $interview) {
			$interviewDateTime = Carbon::parse($interview->interview_date . ' ' . $interview->interview_time, 'Europe/London');
			if ($currentUKTime->greaterThan($interviewDateTime->addHour(1))) {
				Interview_schedule::where('id', $interview->id)->update([
					'interview_status' => 'H'
				]);
			}
		}
		$jobList = Interview_schedule::whereIn('job_id', $jobIds)

			->join('reach_jobs', 'reach_interview_schedule.job_id', '=', 'reach_jobs.id')
			->join('reach_job_roles', 'reach_jobs.job_role', '=', 'reach_job_roles.id')
			->join('reach_boat_location', 'reach_jobs.job_location', '=', 'reach_boat_location.id')
			->join('reach_employee_details', 'reach_employee_details.employee_id', '=', 'reach_interview_schedule.employee_id')
			->join('reach_members', 'reach_employee_details.member_id', '=', 'reach_members.id')
			->select(
				'reach_interview_schedule.id',
				'reach_interview_schedule.interview_date',
				'reach_interview_schedule.interview_uk_time as interview_time',
				'reach_interview_schedule.interview_status',
				'reach_employee_details.member_id',
				'reach_jobs.job_summary',
				'reach_job_roles.job_role',
				'reach_boat_location.boat_location as location',
				'reach_members.members_profile_picture',
				'reach_interview_schedule.employee_id',
				'reach_interview_schedule.job_id',
				\DB::raw("CONCAT(reach_members.members_fname, ' ', reach_members.members_lname) AS full_name"),
				'reach_interview_schedule.meeting_id',
			)
			->whereNotIn('interview_status', ['C', 'H'])
			->orderByRaw("FIELD(reach_interview_schedule.interview_status, 'P', 'A', 'H', 'C')")
			->orderBy('reach_interview_schedule.interview_date', 'asc')
			->orderBy('reach_interview_schedule.interview_time', 'asc')
			->get();
		return response()->json(['success' => true, 'data' => $jobList], 200);
	}

	public function jobInterviewCount(Request $request)
	{
		$member = $request->user();
		$employeeDts = ReachEmployeeDetails::where('member_id', $member->id)->first();

		if (!$employeeDts) {
			return response()->json(['success' => false, 'message' => 'Employee details not found.'], 404);
		}
		$employeeId = $employeeDts->employee_id;
		$currentUKDate = Carbon::now('Europe/London')->toDateString();
		$currentUKTime = Carbon::now('Europe/London')->toTimeString();
		$interviewCount = Interview_schedule::where('reach_interview_schedule.employee_id', $employeeId)
			->where(function ($query) use ($currentUKDate, $currentUKTime) {
				$query->where('reach_interview_schedule.interview_date', '>', $currentUKDate)
					->orWhere(function ($query) use ($currentUKDate, $currentUKTime) {
						$query->where('reach_interview_schedule.interview_date', '=', $currentUKDate)
							->where('reach_interview_schedule.interview_uk_time', '>', $currentUKTime);
					});
			})
			->whereNotIn('interview_status', ['C', 'H'])
			->count();
		$campaigns = ReachJob::where('member_id', $member->id)->withTrashed()->get();
		$jobIds = $campaigns->pluck('id')->toArray();
		$jobCount = Interview_schedule::whereIn('job_id', $jobIds)
			->where(function ($query) use ($currentUKDate, $currentUKTime) {
				$query->where('reach_interview_schedule.interview_date', '>', $currentUKDate)
					->orWhere(function ($query) use ($currentUKDate, $currentUKTime) {
						$query->where('reach_interview_schedule.interview_date', '=', $currentUKDate)
							->where('reach_interview_schedule.interview_uk_time', '>', $currentUKTime);
					});
			})
			->whereNotIn('interview_status', ['C', 'H'])

			->count();
		return response()->json([
			'success' => true,
			'interview_count' => $interviewCount,
			'job_count' => $jobCount,
			'total_count' => $interviewCount + $jobCount // Optional: total count of interviews and jobs
		], 200);
	}

	public function getAvailableInterviewSlots(Request $request)
	{
		$member = $request->user();

		$requestData = $request->all();

		$validator = Validator::make($requestData, [
			'member_id' => 'required',
			'interview_date' => 'required',
		], [
			'member_id.required' => 'The specialist id is required.',
			'interview_date.required' => 'The schedule date is required.',
		]);

		$memberId = $requestData['member_id'];
		$employeeDts = ReachEmployeeDetails::where('member_id', $memberId)->first();

		// if (!$employeeDts) {
		// 	return response()->json(['success' => false, 'message' => 'Employee details not found.'], 404);
		// }
		$employeeId = '';
		if (isset($employeeDts->employee_id)) {
			$employeeId = $employeeDts->employee_id;
		}

		//for logged in member have any interview scheduled cheking
		$loggedInMemberId = $member['id'];
		$loggedInEmployee = ReachEmployeeDetails::where('member_id', $loggedInMemberId)->first();
		$loggedInEmployeeId = $loggedInEmployee ? $loggedInEmployee->employee_id : null;

		$campaigns = ReachJob::where('member_id', $memberId)->withTrashed()->get();
		$jobIds = $campaigns->pluck('id')->toArray();

		//for logged in member have any job scheduled cheking
		$campaigns_logged_member = ReachJob::where('member_id', $loggedInMemberId)->withTrashed()->get();
		$jobIds_logged_member = $campaigns_logged_member->pluck('id')->toArray();
		$allJobIds = array_unique(array_merge($jobIds, $jobIds_logged_member));
		// Set default time slots for that day if no working hours are available
		$availableTimeSlots = [];
		// Set default time range
		$defaultStart = '09:00 AM';
		$defaultEnd = '07:00 PM';
		// Convert to timestamps for easier manipulation
		$startTimestamp = strtotime($defaultStart);
		$endTimestamp = strtotime($defaultEnd);
		// Create 30-minute increments for default time slots
		while ($startTimestamp < $endTimestamp) {
			$timeSlot = date('H:i', $startTimestamp);
			$availableTimeSlots[] = $timeSlot; // Add to available time slots
			$startTimestamp += (30 * 60); // Increment by 30 minutes
		}

		$schedule = Interview_schedule::whereNotIn('interview_status', ['C', 'H'])
			->where(function ($query) use ($allJobIds, $loggedInEmployeeId, $employeeId) {
				$query->whereIn('job_id', $allJobIds)
					->orWhereIn('employee_id', array_filter([$loggedInEmployeeId, $employeeId]));
			})
			//->whereIn('job_id', $jobIds)
			//->orWhereIn('employee_id', [$employeeId])
			->where('interview_date', $requestData['interview_date'])
			->get(['interview_time', 'interview_date'])
			->toArray();

		$blockedSlots = [];

		foreach ($schedule as $slot) {
			$time = $slot['interview_time'];
			try {
				// Parse the scheduled time
				$startTime = Carbon::createFromFormat('H:i:s', $time);
				// Block current time and the next 30-minute interval
				$blockedSlots[] = $startTime->format('H:i');
				$blockedSlots[] = $startTime->copy()->addMinutes(30)->format('H:i');
			} catch (\Exception $e) {
				error_log('Time format error: ' . $e->getMessage());
			}
		}

		return response()->json([
			'status' => 'success',
			'available_time_slots' => $availableTimeSlots,
			'schedule_time' => $blockedSlots
		]);
	}
}
