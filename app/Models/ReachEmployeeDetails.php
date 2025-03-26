<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

use Laravel\Sanctum\HasApiTokens;
use App\Models\JobDetails\ReachJobRole;

class ReachEmployeeDetails extends Authenticatable
{
    use HasFactory, HasApiTokens;
    use SoftDeletes;

    protected $table = 'reach_employee_details';
    protected $primaryKey = 'employee_id';

    protected $fillable = [
        'member_id',
        'employee_role',
        'employee_passport',
        'employee_avilable',
        'employee_dob',
        'employee_gender',
        'employee_location',
        'employee_position',
        'employee_vessel',
        'employee_salary_expection',
        'employee_experience',
        'employee_qualification',
        'employee_languages',
        'employee_visa',
        'employee_interest',
        'employee_last_role',
        'employee_about',
        'employee_intro',
        'employee_status',
        'employee_age',
        'employee_avilable_date',
        'referrance'
    ];

    public function member()
    {
        return $this->belongsTo(ReachMember::class)
            ->where('members_status', 'A');
    }

    public function scopeWithActiveMembers($query)
    {
        return $query->whereHas('member', function ($query) {
            $query->where('members_status', 'A');
        });
    }

    public function position()
    {
        return $this->belongsTo(ReachPositions::class, 'employee_position', 'position_id');
    }

    public function jobRole()
    {
        return $this->belongsTo(ReachJobRole::class, 'employee_role', 'id');
    }

    public function country()
    {
        return $this->belongsTo(ReachCountry::class, 'employee_location', 'id');
    }

    public function passport()
    {
        return $this->belongsTo(ReachCountry::class, 'employee_passport', 'id');
    }

    public function visa()
    {
        return $this->belongsTo(ReachCountry::class, 'employee_visa', 'id');
    }

    public function qualifications()
    {
        return $this->belongsTo(ReachQualifications::class, 'employee_qualification', 'qualification_id');
    }

    public function languages()
    {
        return $this->belongsTo(ReachLanguages::class, 'employee_languages', 'lang_id');
    }

    public function experience()
    {
        return $this->belongsTo(ReachExperience::class, 'employee_experience', 'experience_id');
    }

    public function availability()
    {
        return $this->belongsTo(ReachAvailability::class, 'employee_avilable', 'availability_id');
    }

    public function expectations()
    {
        return $this->belongsTo(ReachSalaryExpectations::class, 'employee_salary_expection', 'expectation_id');
    }

    public function vessel()
    {
        return $this->belongsTo(ReachVesselType::class, 'employee_vessel', 'vessel_id');
    }

    public function media()
    {
        return $this->hasMany(ReachEmployeeMedia::class, 'employee_id', 'id');
    }

    public function search()
    {
        return $this->belongsTo(ReachEmployeeSearch::class, 'employee_id');
    }

    // Static method to get employee details with joins
    public static function getEmployeeDetailsByMemberId($memberId)
    {

        $employeeDetails = self::select([
            'reach_employee_details.employee_id',
            'reach_members.members_fname',
            'reach_members.members_lname',
            'passport.country_name as employee_passport',
            'countries.country_name as employee_location',
            'reach_experience.experience_name as employee_experience',
            'reach_current_availability.availability_name as employee_avilable',
            'reach_salary_expectations.expectation_name as employee_salary_expection',
            'reach_employee_details.employee_avilable_date',
            'reach_employee_details.employee_dob',
            'reach_employee_details.employee_gender',
            'reach_employee_details.employee_last_role',
            'reach_employee_details.employee_interest',
            'reach_employee_details.employee_about',
            'reach_employee_details.referrance',
            'reach_employee_details.employee_intro',

            DB::raw('GROUP_CONCAT(DISTINCT reach_job_roles.job_role SEPARATOR ", ") as employee_role'),
            DB::raw('GROUP_CONCAT(DISTINCT reach_qualifications.qualification_name SEPARATOR ", ") as employee_qualification'),
            DB::raw('GROUP_CONCAT(DISTINCT reach_languages.language_name SEPARATOR ", ") as employee_languages'),
            DB::raw('GROUP_CONCAT(DISTINCT reach_positions.position_name SEPARATOR ", ") as employee_position'),
            DB::raw('GROUP_CONCAT(DISTINCT reach_vessel_type.vessel_type SEPARATOR ", ") as employee_vessel'),
            DB::raw('GROUP_CONCAT(DISTINCT visa.visa SEPARATOR ", ") as employee_visa'),
        ])
            ->join('reach_members', 'reach_employee_details.member_id', '=', 'reach_members.id')
            ->leftJoin('reach_countries as passport', 'reach_employee_details.employee_passport', '=', 'passport.id')
            ->leftJoin('reach_countries as countries', 'reach_employee_details.employee_location', '=', 'countries.id')
            ->leftJoin('reach_experience', 'reach_employee_details.employee_experience', '=', 'reach_experience.experience_id')
            ->leftJoin('reach_current_availability', 'reach_employee_details.employee_avilable', '=', 'reach_current_availability.availability_id')
            ->leftJoin('reach_salary_expectations', 'reach_employee_details.employee_salary_expection', '=', 'reach_salary_expectations.expectation_id')
            ->leftJoin('reach_job_roles', function ($join) {
                $join->on(DB::raw('FIND_IN_SET(reach_job_roles.id, reach_employee_details.employee_role)'), '>', DB::raw('0'));
            })
            ->leftJoin('reach_qualifications', function ($join) {
                $join->on(DB::raw('FIND_IN_SET(reach_qualifications.qualification_id, reach_employee_details.employee_qualification)'), '>', DB::raw('0'));
            })
            ->leftJoin('reach_languages', function ($join) {
                $join->on(DB::raw('FIND_IN_SET(reach_languages.lang_id, reach_employee_details.employee_languages)'), '>', DB::raw('0'));
            })
            ->leftJoin('reach_positions', function ($join) {
                $join->on(DB::raw('FIND_IN_SET(reach_positions.position_id, reach_employee_details.employee_position)'), '>', DB::raw('0'));
            })
            ->leftJoin('reach_vessel_type', function ($join) {
                $join->on(DB::raw('FIND_IN_SET(reach_vessel_type.vessel_id, reach_employee_details.employee_vessel)'), '>', DB::raw('0'));
            })
            ->leftJoin('reach_visa as visa', function ($join) {
                $join->on(DB::raw('FIND_IN_SET(visa.id, reach_employee_details.employee_visa)'), '>', DB::raw('0'));
            })
            ->where('reach_employee_details.member_id', $memberId)
            ->groupBy([
                'reach_employee_details.employee_id',
                'reach_members.members_fname',
                'reach_members.members_lname',
                'passport.country_name',
                'countries.country_name',
                'reach_experience.experience_name',
                'reach_current_availability.availability_name',
                'reach_salary_expectations.expectation_name',
                'reach_employee_details.employee_avilable_date',
                'reach_employee_details.employee_dob',
                'reach_employee_details.employee_gender',
                'reach_employee_details.employee_last_role',
                'reach_employee_details.employee_interest',
                'reach_employee_details.employee_about',
                'reach_employee_details.employee_intro',
                'reach_employee_details.referrance'
            ])
            ->first();

        // If reference IDs are found, convert them to full names

        if ($employeeDetails && $employeeDetails->referrance) {
            $referenceValues = explode(',', $employeeDetails->referrance); // Split reference IDs

            // Get full names of references
            $references = ReachMember::whereIn('id', $referenceValues)
                ->get(['id', 'members_fname', 'members_lname']) // Get both ID and full name
                ->map(function ($member) {
                    return [
                        'id' => $member->id, // Reference ID
                        'name' => $member->members_fname . ' ' . $member->members_lname // Full name
                    ];
                })
                ->toArray();

            // Update referrance with full names
            $employeeDetails->referrance = $references;
        } else {
            // If referrance is empty or null, return an empty array
            $employeeDetails->referrance = [];
        }

        return $employeeDetails;
    }

    public static function getEmployeeDetailsIds($empIds, $limit = 10, $page = 1)
    {
        $employeeDetails = self::select([
            'reach_employee_details.employee_id',
            'reach_members.id as member_id',
            DB::raw('CONCAT(reach_members.members_fname, " ", reach_members.members_lname) as members_name'),
            'reach_members.members_fname',
            'reach_members.members_lname',
            'reach_members.members_profile_picture',
            'reach_members.created_at',
            'passport.country_name as employee_passport',
            'countries.country_name as employee_location',
            'reach_experience.experience_name as employee_experience',
            'reach_current_availability.availability_name as employee_avilable',
            'reach_salary_expectations.expectation_name as employee_salary_expection',
            'reach_employee_details.employee_avilable_date',
            'reach_employee_details.employee_dob',
            DB::raw('TIMESTAMPDIFF(YEAR, reach_employee_details.employee_dob, CURDATE()) as employee_age'),
            'reach_employee_details.employee_gender',
            'reach_employee_details.employee_last_role',
            'reach_employee_details.employee_interest',
            'reach_employee_details.employee_about',
            'reach_employee_details.referrance',
            'reach_employee_details.employee_intro',
            DB::raw('GROUP_CONCAT(DISTINCT reach_job_roles.job_role SEPARATOR ", ") as employee_role'),
            DB::raw('GROUP_CONCAT(DISTINCT reach_qualifications.qualification_name SEPARATOR ", ") as employee_qualification'),
            DB::raw('GROUP_CONCAT(DISTINCT reach_languages.language_name SEPARATOR ", ") as employee_languages'),
            DB::raw('GROUP_CONCAT(DISTINCT reach_positions.position_name SEPARATOR ", ") as employee_position'),
            DB::raw('GROUP_CONCAT(DISTINCT reach_vessel_type.vessel_type SEPARATOR ", ") as employee_vessel'),
            DB::raw('GROUP_CONCAT(DISTINCT visa.visa SEPARATOR ", ") as employee_visa'),
            DB::raw('GROUP_CONCAT(DISTINCT CONCAT(reach_employee_media.id, "|", reach_employee_media.media_file) SEPARATOR ", ") as employee_images') // Modified for processing
        ])
            ->join('reach_members', 'reach_employee_details.member_id', '=', 'reach_members.id')
            ->leftJoin('reach_countries as passport', 'reach_employee_details.employee_passport', '=', 'passport.id')
            ->leftJoin('reach_countries as countries', 'reach_employee_details.employee_location', '=', 'countries.id')
            ->leftJoin('reach_experience', 'reach_employee_details.employee_experience', '=', 'reach_experience.experience_id')
            ->leftJoin('reach_current_availability', 'reach_employee_details.employee_avilable', '=', 'reach_current_availability.availability_id')
            ->leftJoin('reach_salary_expectations', 'reach_employee_details.employee_salary_expection', '=', 'reach_salary_expectations.expectation_id')
            ->leftJoin('reach_job_roles', function ($join) {
                $join->on(DB::raw('FIND_IN_SET(reach_job_roles.id, reach_employee_details.employee_role)'), '>', DB::raw('0'));
            })
            ->leftJoin('reach_qualifications', function ($join) {
                $join->on(DB::raw('FIND_IN_SET(reach_qualifications.qualification_id, reach_employee_details.employee_qualification)'), '>', DB::raw('0'));
            })
            ->leftJoin('reach_languages', function ($join) {
                $join->on(DB::raw('FIND_IN_SET(reach_languages.lang_id, reach_employee_details.employee_languages)'), '>', DB::raw('0'));
            })
            ->leftJoin('reach_positions', function ($join) {
                $join->on(DB::raw('FIND_IN_SET(reach_positions.position_id, reach_employee_details.employee_position)'), '>', DB::raw('0'));
            })
            ->leftJoin('reach_vessel_type', function ($join) {
                $join->on(DB::raw('FIND_IN_SET(reach_vessel_type.vessel_id, reach_employee_details.employee_vessel)'), '>', DB::raw('0'));
            })
            ->leftJoin('reach_visa as visa', function ($join) {
                $join->on(DB::raw('FIND_IN_SET(visa.id, reach_employee_details.employee_visa)'), '>', DB::raw('0'));
            })
            ->leftJoin('reach_employee_media', 'reach_employee_details.employee_id', '=', 'reach_employee_media.employee_id')
            ->where('reach_members.members_status', 'A')
            ->where('reach_employee_details.employee_status', 'A')
            ->whereIn('reach_employee_details.employee_id', $empIds)
            ->groupBy([
                'reach_employee_details.employee_id',
                'reach_members.id',
                'reach_members.members_fname',
                'reach_members.members_lname',
                'reach_members.members_profile_picture',
                'reach_members.created_at',
                'passport.country_name',
                'countries.country_name',
                'reach_experience.experience_name',
                'reach_current_availability.availability_name',
                'reach_salary_expectations.expectation_name',
                'reach_employee_details.employee_avilable_date',
                'reach_employee_details.employee_dob',
                'reach_employee_details.employee_gender',
                'reach_employee_details.employee_last_role',
                'reach_employee_details.employee_interest',
                'reach_employee_details.employee_about',
                'reach_employee_details.employee_intro',
                'reach_employee_details.referrance'
            ])
            ->paginate($limit, ['*'], 'page', $page);

        $employeeDetails->getCollection()->transform(function ($employee) {
            // Process references
            if ($employee->referrance) {
                $referenceValues = explode(',', $employee->referrance);
                $references = ReachMember::whereIn('id', $referenceValues)
                    ->get(['id', 'members_fname', 'members_lname'])
                    ->map(function ($member) {
                        return [
                            'id' => $member->id,
                            'name' => $member->members_fname . ' ' . $member->members_lname
                        ];
                    })
                    ->toArray();
                $employee->referrance = $references;
            } else {
                $employee->referrance = [];
            }

            // Process employee_images
            if ($employee->employee_images) {
                $imageData = explode(',', $employee->employee_images);
                $employee->employee_images = array_map(function ($image) {
                    [$id, $media_file] = explode('|', $image);
                    return [
                        'id' => (int) $id,
                        'media_file' => $media_file
                    ];
                }, $imageData);
            } else {
                $employee->employee_images = [];
            }

            return $employee;
        });

        return $employeeDetails;
    }



    public static function getEmployeeDetailsById($employeeId)
    {
        return self::select([
            'reach_employee_details.employee_id',
            'reach_employee_details.member_id',
            'reach_members.members_fname',
            'reach_members.members_lname',
            'passport.country_name as passport_name',
            'reach_positions.position_name',
            'reach_job_roles.job_role',
            'countries.country_name as location_name',
            'reach_qualifications.qualification_name',
            'reach_languages.language_name',
            'reach_experience.experience_name',
            'reach_current_availability.availability_name',
            'reach_salary_expectations.expectation_name',
            'reach_vessel_type.vessel_type',
            'visa.country_name as visa_name',
            'reach_employee_details.employee_dob',
            'reach_employee_details.employee_gender',
            'reach_employee_details.employee_last_role',
            'reach_employee_details.employee_interest',
            'reach_employee_details.employee_about',
            'reach_employee_details.employee_intro',
        ])
            ->join('reach_members', 'reach_employee_details.member_id', '=', 'reach_members.id')
            ->leftJoin('reach_countries as passport', 'reach_employee_details.employee_passport', '=', 'passport.id')
            ->leftJoin('reach_positions', 'reach_employee_details.employee_position', '=', 'reach_positions.position_id')
            ->leftJoin('reach_job_roles', 'reach_employee_details.employee_role', '=', 'reach_job_roles.id')
            ->leftJoin('reach_countries as countries', 'reach_employee_details.employee_location', '=', 'countries.id')
            ->leftJoin('reach_qualifications', 'reach_employee_details.employee_qualification', '=', 'reach_qualifications.qualification_id')
            ->leftJoin('reach_languages', 'reach_employee_details.employee_languages', '=', 'reach_languages.lang_id')
            ->leftJoin('reach_experience', 'reach_employee_details.employee_experience', '=', 'reach_experience.experience_id')
            ->leftJoin('reach_current_availability', 'reach_employee_details.employee_avilable', '=', 'reach_current_availability.availability_id')
            ->leftJoin('reach_salary_expectations', 'reach_employee_details.employee_salary_expection', '=', 'reach_salary_expectations.expectation_id')
            ->leftJoin('reach_vessel_type', 'reach_employee_details.employee_vessel', '=', 'reach_vessel_type.vessel_id')
            ->leftJoin('reach_countries as visa', 'reach_employee_details.employee_visa', '=', 'visa.id')
            ->where('reach_employee_details.employee_id', $employeeId)
            ->first();
    }

}
