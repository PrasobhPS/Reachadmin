<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\ReachChandlery;
use App\Models\ReachChandleryCouponCodes;

class ChandleryController extends Controller
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
        $chandlery = ReachChandlery::orderBy('chandlery_order')->paginate(10);
        return view('chandlery/list', ['chandlery' => $chandlery]);
    }


    /**
     * Form for adding new chandlery.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function add_chandlery()
    {
        return view('chandlery/add_chandlery');
    }

    /**
     * Save chandlery to table.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function save_chandlery(Request $request)
    {
        $requestData = $request->all();

        if (isset($requestData['chandlery_status'])) {
            $requestData['chandlery_status'] = 'A';
        } else {
            $requestData['chandlery_status'] = 'I';
        }
        if (isset($requestData['show_coupon_code'])) {
            $requestData['show_coupon_code'] = '1';
        } else {
            $requestData['show_coupon_code'] = '0';
        }
        $validator = Validator::make($requestData, [
            'chandlery_name' => 'required',
            'chandlery_website' => 'required',
            'chandlery_discount' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Handle profile picture upload
        if ($request->hasFile('chandlery_image')) {
            $profilePicturePath = $request->file('chandlery_image')->store('chandlery', 'public');
            $requestData['chandlery_image'] = $profilePicturePath;
        }

        if ($request->hasFile('chandlery_logo')) {
            $logoPath = $request->file('chandlery_logo')->store('chandlery', 'public');
            $requestData['chandlery_logo'] = $logoPath;
        }

        $chandlery = ReachChandlery::create($requestData);
        $id = $chandlery->id;
        $coupon_code = $requestData['coupon_code'];
        $coupons = explode(',', $coupon_code);
        $data = [];
        foreach ($coupons as $code) {
            $code = trim($code);
            $existingCoupon = ReachChandleryCouponCodes::where('chandlery_id', $id)
                ->where('coupon_code', $code)
                ->exists();
            if (!$existingCoupon) {
                // Add coupon code to data if it does not exist
                $data[] = [
                    'coupon_code' => $code,
                    'chandlery_id' => $id,
                    'status' => 'A',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }
        if (!empty($data)) {

            ReachChandleryCouponCodes::insert($data);
        }

        return redirect()->route('chandlery')->with('success', 'Chandlery created successfully!');
    }

    /**
     * Display the form for editing a specific chandlery.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\View\View
     */

    public function edit_chandlery($id)
    {
        $chandlery = ReachChandlery::find($id);
        $couponCodes = ReachChandleryCouponCodes::where('chandlery_id', $id)
            ->pluck('coupon_code')
            ->toArray();
        $couponCodesString = implode(', ', $couponCodes);
        return view('chandlery/edit_chandlery', ['chandlery' => $chandlery, 'couponCodesString' => $couponCodesString]);
    }


    /**
     * Update the specified chandlery details.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update_chandlery(Request $request, $id)
    {

        $requestData = $request->all();

        if (isset($requestData['chandlery_status'])) {
            $requestData['chandlery_status'] = 'A';
        } else {
            $requestData['chandlery_status'] = 'I';
        }

        if (isset($requestData['show_coupon_code'])) {
            $requestData['show_coupon_code'] = '1';
        } else {
            $requestData['show_coupon_code'] = '0';
        }
        $validator = Validator::make($requestData, [
            'chandlery_name' => 'required',
            'chandlery_website' => 'required',
            'chandlery_discount' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $requestData['coupon_code'] = trim(strip_tags($requestData['coupon_code']));
        // Handle profile picture upload
        if ($request->hasFile('chandlery_image')) {
            $profilePicturePath = $request->file('chandlery_image')->store('chandlery', 'public');
            $requestData['chandlery_image'] = $profilePicturePath;
        }

        if ($request->hasFile('chandlery_logo')) {
            $logoPath = $request->file('chandlery_logo')->store('chandlery', 'public');
            $requestData['chandlery_logo'] = $logoPath;
        }

        $chandlery = ReachChandlery::findOrFail($id);
        $chandlery->update($requestData);

        $coupon_code = $requestData['coupon_code'];
        $coupons = explode(',', $coupon_code);
        $data = [];
        foreach ($coupons as $code) {
            $code = trim($code);
            $existingCoupon = ReachChandleryCouponCodes::where('chandlery_id', $id)
                ->where('coupon_code', $code)
                ->exists();
            if (!$existingCoupon) {
                // Add coupon code to data if it does not exist
                $data[] = [
                    'coupon_code' => $code,
                    'chandlery_id' => $id,
                    'status' => 'A',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }
        if (!empty($data)) {

            ReachChandleryCouponCodes::insert($data);
        }

        return redirect()->route('chandlery')->with('success', 'Chandlery updated successfully!');
    }

    /**
     * Delete chandlery by its Id.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\View\View
     */
    public function delete_chandlery($id)
    {
        $chandlery = ReachChandlery::find($id);

        if ($chandlery) {

            $chandlery->delete();
            return redirect()->route('chandlery')->with('success', 'chandlery deleted successfully!');
        } else {
            // If the chandlery doesn't exist, return an error message or handle it as appropriate
            return redirect()->route('chandlery')->with('success', 'Chandlery not found!');
        }

    }

    /**
     * Show the list of category type.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function category_list()
    {
        $category = ReachCategory::all();
        return view('settings/category_list', ['category' => $category]);
    }

    /**
     * Form for adding new category.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function category_add()
    {
        return view('settings/add_category');
    }

    /**
     * Save data to category table.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function save_category(Request $request)
    {
        $requestData = $request->all();

        if (isset($requestData['position_status'])) {
            $requestData['position_status'] = 'A';
        } else {
            $requestData['position_status'] = 'I';
        }
        $validator = Validator::make($requestData, [
            'position_name' => 'required|string|max:255',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $position = ReachCategory::create($requestData);

        return redirect()->route('category')->with('success', 'Category created successfully!');
    }

    /**
     * Display the form for editing a category.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\View\View
     */

    public function edit_category($id)
    {
        $category = ReachCategory::find($id);
        // Pass the member data to the view
        return view('settings/edit_category', ['category' => $category]);
    }

    /**
     * Update the specified category details.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update_category(Request $request, $id)
    {

        $requestData = $request->all();

        if (isset($requestData['position_status'])) {
            $requestData['position_status'] = 'A';
        } else {
            $requestData['position_status'] = 'I';
        }
        $validator = Validator::make($requestData, [
            'position_name' => 'required|string|max:255',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $position = ReachCategory::findOrFail($id);
        $position->update($requestData);

        return redirect()->route('category')->with('success', 'Category updated successfully!');
    }

    /**
     * Delete category by its Id.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\View\View
     */
    public function delete_category($id)
    {
        $position = ReachCategory::find($id);

        if ($position) {
            //$position->delete();
            return redirect()->route('category')->with('success', 'Category deleted successfully!');
        } else {
            return redirect()->route('category')->with('success', 'Page not found!');
        }
    }


}
