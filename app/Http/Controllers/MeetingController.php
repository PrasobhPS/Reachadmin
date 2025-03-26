<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;

use App\Models\ReachMember;

class MeetingController extends Controller
{
    public function index($id, $meeting_id)
    {  
        $member = ReachMember::select('id', 'members_fname', 'members_lname', 'members_email', 'members_profile_picture')->find($id);

        if (!$member) {
            abort(404);
        }

        $data = [
            'meeting_id' => $meeting_id,
            'member' => $member
        ];

        return view('meeting/meeting', ['data'=>$data]);
    }


}
