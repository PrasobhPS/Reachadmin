<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\ReachCountry;
use App\Models\ReachPartner;
use App\Rules\UniqueDisplayOrder;
use App\Models\ReachChandleryCouponCodes;

class PartnerController extends Controller
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
        $partners = ReachPartner::orderBy('partner_display_order')->paginate(10);
        return view('partners/list', ['partners' => $partners]);
    }


    /**
     * Form for adding new specialist.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function add_partner()
    {
        $countries = ReachCountry::where('country_status', 'A')->get();
        return view('partners/add_partner', ['countries' => $countries]);
    }

    /**
     * Save specialist to table.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function save_partner(Request $request)
    {
        $requestData = $request->all();

        if (isset($requestData['partner_status'])) {
            $requestData['partner_status'] = 'A';
        } else {
            $requestData['partner_status'] = 'I';
        }
        // if (isset($requestData['show_coupon_code'])) {
        //     $requestData['show_coupon_code'] = '1';
        // } else {
        //     $requestData['show_coupon_code'] = '0';
        // }
        if (isset($requestData['partner_name'])) {
            $requestData['partner_name'] = trim(strip_tags($requestData['partner_name']));
        }
        // if (isset($requestData['coupon_code'])) {
        //     $requestData['coupon_code'] = trim(strip_tags($requestData['coupon_code']));
        // }

        $validator = Validator::make($requestData, [
            'partner_name' => 'required',
            'partner_details' => 'required',
            'partner_cover_image' => 'nullable|mimes:jpeg,png,jpg,gif',
            'partner_side_image' => 'nullable|mimes:jpeg,png,jpg,gif',
            'partner_logo' => 'nullable|mimes:jpeg,png,jpg,gif',
            'partner_display_order' => ['required', 'integer', 'min:0', new UniqueDisplayOrder]
            // Add more validation rules as needed
        ]);


        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Handle profile picture upload
        if ($request->hasFile('partner_cover_image')) {
            $partner_cover_image = $request->file('partner_cover_image')->store('partner/cover-images', 'public');
            $requestData['partner_cover_image'] = $partner_cover_image;
        }
        if ($request->hasFile('partner_cover_image_mob')) {
            $partner_cover_image_mob = $request->file('partner_cover_image_mob')->store('partner/cover-images', 'public');
            $requestData['partner_cover_image_mob'] = $partner_cover_image_mob;
        }
        if ($request->hasFile('partner_side_image')) {
            $partner_side_image = $request->file('partner_side_image')->store('partner/side-images', 'public');
            $requestData['partner_side_image'] = $partner_side_image;
        }
        if ($request->hasFile('partner_side_image_mob')) {
            $partner_side_image_mob = $request->file('partner_side_image_mob')->store('partner/side-images', 'public');
            $requestData['partner_side_image_mob'] = $partner_side_image_mob;
        }
        if ($request->hasFile('partner_logo')) {
            $partner_logo = $request->file('partner_logo')->store('partner/logo', 'public');
            $requestData['partner_logo'] = $partner_logo;
        }
        if ($request->hasFile('partner_video_thumb')) {
            $partner_video_thumb = $request->file('partner_video_thumb')->store('partner/videos', 'public');
            $requestData['partner_video_thumb'] = $partner_video_thumb;
        }
        if ($request->hasFile('partner_side_video')) {
            $videoPath = $request->file('partner_side_video')->store('partner/side-videos', 'public');
            $requestData['partner_side_video'] = $videoPath;
        }
        if ($request->hasFile('partner_side_video_mob')) {
            $videoPath = $request->file('partner_side_video_mob')->store('partner/side-videos', 'public');
            $requestData['partner_side_video_mob'] = $videoPath;
        }

        if (isset($requestData['video_file_type']) && $requestData['video_file_type'] == 'File') {
            // Handle video upload
            if ($request->hasFile('partner_video')) {
                $videoPath = $request->file('partner_video')->store('partner/videos', 'public');
                $requestData['partner_video'] = $videoPath;
            }
        } else {
            $requestData['partner_video'] = $requestData['video_url'];
        }

        $partner = ReachPartner::create($requestData);
        //////insert into chandelery coupon codes////

        // if (array_key_exists('coupon_code', $requestData)) {
        //     $coupon_code = $requestData['coupon_code'];
        //     $coupons = explode(',', $coupon_code);
        //     $data = [];
        //     foreach ($coupons as $code) {
        //         $code = trim($code);
        //         $existingCoupon = ReachChandleryCouponCodes::where('chandlery_id', $partner->id)
        //             ->where('coupon_code', $code)
        //             ->exists();
        //         if (!$existingCoupon) {
        //             // Add coupon code to data if it does not exist
        //             $data[] = [
        //                 'coupon_code' => $code,
        //                 'chandlery_id' => $partner->id,
        //                 'status' => 'A',
        //                 'created_at' => now(),
        //                 'updated_at' => now(),
        //             ];
        //         }
        //     }
        // }

        // if (!empty($data)) {

        //     ReachChandleryCouponCodes::insert($data);
        // }
        // Redirect to the specialist list page
        return redirect()->route('partners')->with('success', 'Partner created successfully!');
    }

    /**
     * Display the form for editing a specific specialist.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\View\View
     */

    public function edit_partner($id)
    {
        $partner = ReachPartner::find($id);
        $couponCodes = ReachChandleryCouponCodes::where('chandlery_id', $id)
            ->pluck('coupon_code')
            ->toArray();
        // Convert coupon codes array to a comma-separated string
        $couponCodesString = implode(', ', $couponCodes);
        return view('partners/edit_partner', ['partner' => $partner, 'couponCodesString' => $couponCodesString,]);
    }


    /**
     * Update the specified specialist details.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update_partner(Request $request, $id)
    {

        $requestData = $request->all();

        if (isset($requestData['partner_status'])) {
            $requestData['partner_status'] = 'A';
        } else {
            $requestData['partner_status'] = 'I';
        }

        // if (isset($requestData['is_chandlery'])) {
        //     $requestData['is_chandlery'] = 'Y';
        // } else {
        //     $requestData['is_chandlery'] = 'N';
        // }
        // if (isset($requestData['show_coupon_code'])) {
        //     $requestData['show_coupon_code'] = '1';
        // } else {
        //     $requestData['show_coupon_code'] = '0';
        // }
        $requestData['partner_name'] = trim(strip_tags($requestData['partner_name']));
        //$requestData['coupon_code'] = trim(strip_tags($requestData['coupon_code']));
        $validator = Validator::make($requestData, [
            'partner_name' => 'required',
            'partner_details' => 'required',
            'partner_cover_image' => 'nullable|mimes:jpeg,png,jpg,gif',
            'partner_side_image' => 'nullable|mimes:jpeg,png,jpg,gif',
            'partner_logo' => 'nullable|mimes:jpeg,png,jpg,gif',
            'partner_display_order' => [
                'required',
                'integer',
                'min:0',
                new UniqueDisplayOrder($id) // Pass the partner ID being updated
            ],
            // Add more validation rules as needed
        ]);


        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Handle profile picture upload
        if ($request->hasFile('partner_cover_image')) {
            $partner_cover_image = $request->file('partner_cover_image')->store('partner/cover-images', 'public');
            $requestData['partner_cover_image'] = $partner_cover_image;
        }
        if ($request->hasFile('partner_cover_image_mob')) {
            $partner_cover_image_mob = $request->file('partner_cover_image_mob')->store('partner/cover-images', 'public');
            $requestData['partner_cover_image_mob'] = $partner_cover_image_mob;
        }
        if ($request->hasFile('partner_side_image')) {
            $partner_side_image = $request->file('partner_side_image')->store('partner/side-images', 'public');
            $requestData['partner_side_image'] = $partner_side_image;
        }
        if ($request->hasFile('partner_side_image_mob')) {
            $partner_side_image_mob = $request->file('partner_side_image_mob')->store('partner/side-images', 'public');
            $requestData['partner_side_image_mob'] = $partner_side_image_mob;
        }
        if ($request->hasFile('partner_logo')) {
            $partner_logo = $request->file('partner_logo')->store('partner/logo', 'public');
            $requestData['partner_logo'] = $partner_logo;
        }
        if ($request->hasFile('partner_video_thumb')) {
            $partner_video_thumb = $request->file('partner_video_thumb')->store('partner/videos', 'public');
            $requestData['partner_video_thumb'] = $partner_video_thumb;
        }
        if ($request->hasFile('partner_side_video')) {
            $videoPath = $request->file('partner_side_video')->store('partner/side-videos', 'public');
            $requestData['partner_side_video'] = $videoPath;
        }
        if ($request->hasFile('partner_side_video_mob')) {
            $videoPath = $request->file('partner_side_video_mob')->store('partner/side-videos', 'public');
            $requestData['partner_side_video_mob'] = $videoPath;
        }

        if (isset($requestData['video_file_type']) && $requestData['video_file_type'] == 'File') {
            // Handle video upload
            if ($request->hasFile('partner_video')) {
                $videoPath = $request->file('partner_video')->store('partner/videos', 'public');
                $requestData['partner_video'] = $videoPath;
            }
        } else {
            $requestData['partner_video'] = $requestData['video_url'];
        }

        $partner = ReachPartner::findOrFail($id);
        $partner->update($requestData);
        //////insert into chandelery coupon codes////

        // $coupon_code = $requestData['coupon_code'];
        // $coupons = explode(',', $coupon_code);
        // $data = [];
        // foreach ($coupons as $code) {
        //     $code = trim($code);
        //     $existingCoupon = ReachChandleryCouponCodes::where('chandlery_id', $id)
        //         ->where('coupon_code', $code)
        //         ->exists();
        //     if (!$existingCoupon) {
        //         // Add coupon code to data if it does not exist
        //         $data[] = [
        //             'coupon_code' => $code,
        //             'chandlery_id' => $id,
        //             'status' => 'A',
        //             'created_at' => now(),
        //             'updated_at' => now(),
        //         ];
        //     }
        // }
        // if (!empty($data)) {

        //     ReachChandleryCouponCodes::insert($data);
        // }
        //////end for  chandelery coupon codes//////
        return redirect()->route('partners')->with('success', 'Partner updated successfully!');
    }

    /**
     * Delete specialist by its Id.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\View\View
     */
    public function delete_partner($id)
    {
        // Find the specialist by id
        $specialist = ReachPartner::find($id);

        // Check if the specialist exists
        if ($specialist) {
            // If the specialist exists, delete it
            $specialist->delete();
            return redirect()->route('partners')->with('success', 'Partner deleted successfully!');
        } else {
            // If the specialist doesn't exist, return an error message or handle it as appropriate
            return redirect()->route('partners')->with('success', 'Partner not found!');
        }
    }

    public function chandlery()
    {
        $chandlery = ReachPartner::where('is_chandlery', 'Y')->orderBy('partner_display_order')->paginate(10);
        return view('partners/chandlery', ['chandlery' => $chandlery]);
    }

    public function deleteVideo($id)
    {

        $partner = ReachPartner::find($id);

        if ($partner['partner_video']) {
            $filePath = storage_path('app/public/partner/videos/' . $partner['partner_video']);
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            $requestData['partner_video'] = NULL;

            $partner->update($requestData);
            return redirect()->route('partner-edit', ['id' => $partner->id])->with('success', 'Video deleted successfully!');
        } else {
            // If the site page doesn't exist, return an error message or handle it as appropriate
            return redirect()->route('partner-edit', ['id' => $partner->id])->with('success', 'Page not found!');
        }
    }

    public function deleteImages($id, $name)
    {

        $partner = ReachPartner::find($id);

        if ($partner[$name]) {
            $filePath = storage_path('app/public/' . $partner[$name]);

            if (file_exists($filePath)) {
                unlink($filePath);
            }
            $requestData[$name] = NULL;

            $partner->update($requestData);
            return redirect()->route('partner-edit', ['id' => $partner->id])->with('success', 'Image deleted successfully!');
        } else {
            // If the site page doesn't exist, return an error message or handle it as appropriate
            return redirect()->route('partner-edit', ['id' => $partner->id])->with('success', 'Page not found!');
        }
    }
}
