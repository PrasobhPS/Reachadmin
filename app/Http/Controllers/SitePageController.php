<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\ReachSitePage;
use App\Models\ReachPageTitle;
use App\Rules\UniqueDisplayOrder;
use App\Models\ReachHomeCms;
use Illuminate\Validation\Rule;
use App\Models\ReachMembershipPage;

class SitePageController extends Controller
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
     * Show the list of site pages.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $sitepages = ReachSitePage::where('site_page_type', 'S')->get();
        return view('sitepages/list', ['sitepages' => $sitepages]);
    }


    /**
     * Form for adding new site page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function add_site_page()
    {
        return view('sitepages/add_site_page');
    }

    /**
     * Save site page to table.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function save_site_page(Request $request)
    {
        $requestData = $request->all();

        $requestData['site_page_status'] = 'A';
        $requestData['expert_call_title'] = trim(strip_tags($requestData['expert_call_title']));
        $requestData['site_page_header'] = trim(strip_tags($requestData['site_page_header']));

        $validator = Validator::make($requestData, [
            'site_page_header' => 'required|string|max:255',
            'site_page_slug' => 'required|string|max:255',
            'site_page_details' => 'required|string|max:200000',
            'site_page_images' => 'nullable|mimes:jpeg,png,jpg,gif|max:20480',
            // Add more validation rules as needed
        ]);


        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        // Handle profile picture upload
        if ($request->hasFile('site_page_images')) {
            $profilePicturePath = $request->file('site_page_images')->store('site-page/images', 'public');
            $requestData['site_page_images'] = $profilePicturePath;
        }

        $partner = ReachSitePage::create($requestData);

        // Redirect to the site page list page
        return redirect()->route('site-pages')->with('success', 'Page created successfully!');
    }

    /**
     * Display the form for editing a specific site page.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\View\View
     */

    public function edit_site_page($id)
    {
        $sitepage = ReachSitePage::find($id);
        // Pass the member data to the view
        return view('sitepages/edit_site_page', ['sitepage' => $sitepage]);
    }


    /**
     * Update the specified site page details.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update_site_page(Request $request, $id)
    {

        $requestData = $request->all();

        $requestData['site_page_status'] = 'A';
        if (isset($requestData['expert_call_title'])) {
            $requestData['expert_call_title'] = trim(strip_tags($requestData['expert_call_title']));
        }
        if (isset($requestData['cruz_title'])) {
            $requestData['cruz_title'] = trim(strip_tags($requestData['cruz_title']));
        }
        $requestData['site_page_header'] = trim(strip_tags($requestData['site_page_header']));
        $validator = Validator::make($requestData, [
            'site_page_header' => 'required|string|max:255',
            'site_page_details' => 'required|string|max:200000',
            'site_page_images' => 'nullable|mimes:jpeg,png,jpg,gif|max:25600',
            // Add more validation rules as needed
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Handle profile picture upload
        if ($request->hasFile('site_page_images')) {
            $profilePicturePath = $request->file('site_page_images')->store('site-page/images', 'public');
            $requestData['site_page_images'] = $profilePicturePath;
        }

        $partner = ReachSitePage::findOrFail($id);
        $partner->update($requestData);

        return redirect()->route('site-pages')->with('success', 'Page updated successfully!');
    }

    /**
     * Delete site page by its Id.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\View\View
     */
    public function delete_site_page($id)
    {
        // Find the site page by id
        $sitepage = ReachSitePage::find($id);

        // Check if the site page exists
        if ($sitepage) {
            // If the site page exists, delete it
            $sitepage->delete();
            return redirect()->route('site-pages')->with('success', 'Page deleted successfully!');
        } else {
            // If the site page doesn't exist, return an error message or handle it as appropriate
            return redirect()->route('site-pages')->with('success', 'Page not found!');
        }
    }

    public function other_pages()
    {
        return view('sitepages/other_pages');
    }

    public function edit_other_page($id)
    {
        $sitepage = ReachPageTitle::where('page_id', $id)->get();
        return view('sitepages/edit_other_page', ['sitepage' => $sitepage]);
    }

    public function update_other_page(Request $request)
    {
        $pageIds = $request->input('page_id');
        $pageTitles = $request->input('page_title');
        $pageDescs = $request->input('page_desc');

        // Loop through each page and update the database
        for ($i = 0; $i < count($pageIds); $i++) {
            $page = ReachPageTitle::find($pageIds[$i]);
            if ($page) {
                $page->page_title = $pageTitles[$i];
                $page->page_desc = $pageDescs[$i];
                $page->save();
            }
        }

        return redirect()->route('other-pages')->with('success', 'Page details updated successfully!');
    }






    /* App Home Page*/
    public function appHome()
    {
        $appages = ReachSitePage::where('site_page_type', 'A')->get();
        return view('sitepages/app_home_page', ['appages' => $appages]);
    }

    public function addappPage()
    {
        return view('sitepages/add_app_page');
    }

    public function saveappPage(Request $request)
    {
        $requestData = $request->all();

        $requestData['site_page_status'] = 'A';
        $validator = Validator::make($requestData, [
            'site_page_header' => 'required|string|max:255',
            'site_page_slug' => 'required|in:home_expert,home_charter,home_cruz,home_chandlery',
            'order' => [
                'required',
                'integer',
                Rule::unique('reach_site_pages', 'order')->where(function ($query) {
                    return $query->where('site_page_type', 'A');
                }),
            ],
        ], [
            'order.unique' => 'The order must be unique for site page type A.',
        ]);



        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $percentage = $request->input('site_chandlery_percentage');
        if ($percentage === '' || $percentage == null) {
            $requestData['site_chandlery_percentage'] = null;
        } else {
            $requestData['site_chandlery_percentage'] = str_replace('%', '', $percentage);
        }

        // Handle profile picture upload
        if ($request->hasFile('site_chandlery_logo')) {
            $chandleryLogo = $request->file('site_chandlery_logo')->store('site-page/images', 'public');
            $requestData['site_chandlery_logo'] = $chandleryLogo;
        }

        if ($request->hasFile('site_page_images')) {
            $profilePicturePath = $request->file('site_page_images')->store('site-page/images', 'public');
            $requestData['site_page_images'] = $profilePicturePath;
        }

        // Handle video upload
        if ($request->hasFile('site_page_video')) {
            $videoPath = $request->file('site_page_video')->store('site-page/videos', 'public');
            $requestData['site_page_video'] = $videoPath;
        }

        $partner = ReachSitePage::create($requestData);

        // Redirect to the site page list page
        return redirect()->route('app-pages')->with('success', 'Page created successfully!');
    }

    public function editappPage($id)
    {
        $appage = ReachSitePage::find($id);
        // Pass the member data to the view
        return view('sitepages/edit_app_page', ['sitepage' => $appage]);
    }



    public function updateappPage(Request $request, $id)
    {

        $requestData = $request->all();

        $requestData['site_page_status'] = 'A';
        $requestData['site_page_header'] = trim(strip_tags($requestData['site_page_header']));
        $requestData['site_page_details'] = trim(strip_tags($requestData['site_page_details']));
        $validator = Validator::make($requestData, [
            'site_page_header' => 'required|string|max:255',
            //  'site_page_slug' => 'required|in:home_expert,home_charter,home_cruz,home_chandlery',
            'order' => [
                'required',
                'integer',
                Rule::unique('reach_site_pages', 'order')->where(function ($query) {
                    return $query->where('site_page_type', 'A');
                })->ignore($id), // Assuming $id is the ID of the record being updated
            ],
        ], [
            'order.unique' => 'The order must be unique for site page type A.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $percentage = $request->input('site_chandlery_percentage');
        if ($percentage === '' || $percentage == null) {
            $requestData['site_chandlery_percentage'] = null;
        } else {
            $requestData['site_chandlery_percentage'] = str_replace('%', '', $percentage);
        }
        $home_app = ReachSitePage::findOrFail($id);
        $oldImagePath = $home_app->site_page_images;
        $oldVideoPath = $home_app->site_page_video;
        // Handle profile picture upload
        if ($request->hasFile('site_page_images')) {
            $filePath = storage_path('app/public/' . $oldImagePath);
            if (file_exists($filePath)) {
                unlink($filePath);
            }

            $profilePicturePath = $request->file('site_page_images')->store('site-page/images', 'public');
            $requestData['site_page_images'] = $profilePicturePath;
        }

        if ($request->hasFile('site_chandlery_logo')) {
            $chandleryLogo = $request->file('site_chandlery_logo')->store('site-page/images', 'public');
            $requestData['site_chandlery_logo'] = $chandleryLogo;
        }

        // Handle video upload
        if ($request->hasFile('site_page_video')) {
            if ($oldVideoPath != '') {
                $filePath2 = storage_path('app/public/' . $oldVideoPath);
                if (file_exists($filePath2)) {
                    unlink($filePath2);
                }
            }
            $videoPath = $request->file('site_page_video')->store('site-page/videos', 'public');
            $requestData['site_page_video'] = $videoPath;
        }

        $partner = ReachSitePage::findOrFail($id);
        $partner->update($requestData);

        return redirect()->route('app-pages')->with('success', 'Page updated successfully!');
    }


    public function deleteappPage($id)
    {
        // Find the site page by id
        $sitepage = ReachSitePage::find($id);

        // Check if the site page exists
        if ($sitepage) {
            // If the site page exists, delete it
            $sitepage->delete();
            return redirect()->route('app-pages')->with('success', 'Page deleted successfully!');
        } else {
            // If the site page doesn't exist, return an error message or handle it as appropriate
            return redirect()->route('app-pages')->with('success', 'Page not found!');
        }
    }

    public function deleteapphomeVideo($id)
    {

        $sitepage = ReachSitePage::find($id);

        if ($sitepage['site_page_video']) {
            $filePath = storage_path('app/public/' . $sitepage['site_page_video']);
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            $requestData['site_page_video'] = '';

            $sitepage->update($requestData);
            return redirect()->route('app-pages')->with('success', 'Video deleted successfully!');
        } else {
            // If the site page doesn't exist, return an error message or handle it as appropriate
            return redirect()->route('app-pages')->with('success', 'Page not found!');
        }
    }
    public function home_page()
    {
        $details = ReachHomeCms::where('home_page_section_status', 'A')->get();
        return view('sitepages/home_page', ['details' => $details]);
    }
    public function edit_home_page_cms($id)
    {
        $edithomepage = ReachHomeCms::find($id);
        return view('sitepages/edit_home_page', ['sitepage' => $edithomepage]);
    }
    public function update_home_page_cms(Request $request, $id)
    {
        $requestData = $request->all();
        /* $validator = Validator::make($requestData, [
             'home_page_section_header' => 'required|string|max:255',
             'home_page_section_type' => 'required',
             'home_page_section_details' => 'required|string|max:2000',
             // 'site_page_images' => 'nullable|mimes:jpeg,png,jpg,gif|max:2560',
             // Add more validation rules as needed
         ]);*/
        $requestData['home_page_section_header'] = trim(strip_tags($requestData['home_page_section_header']));
        $requestData['home_page_section_button'] = trim(strip_tags($requestData['home_page_section_button']));
        $requestData['home_page_section_button_link'] = trim(strip_tags($requestData['home_page_section_button_link']));
        $validator = Validator::make($requestData, [
            'home_page_section_header' => 'required|string|max:255',
            'home_page_section_type' => 'required',
            'home_page_section_details' => 'required|string|max:2000',
            'order' => [
                'required',
                'integer',
                Rule::unique('reach_home_page_cms', 'order')->ignore($id), // Replace $id with the ID of the record being updated.
            ],
        ], [
            'home_page_section_header.required' => 'The home page section header is required.',
            'home_page_section_header.string' => 'The home page section header must be a string.',
            'home_page_section_header.max' => 'The home page section header may not be greater than 255 characters.',
            'home_page_section_type.required' => 'The home page section type is required.',
            'home_page_section_details.required' => 'The home page section type is required.',
            'order.required' => 'The order is required.',
            'order.integer' => 'The order must be an integer.',
            'order.unique' => 'The order must be unique.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $home_cms = ReachHomeCms::findOrFail($id);
        $oldImagePath = $home_cms->site_page_images;
        $oldVideoPath = $home_cms->site_page_video;
        if ($request->hasFile('site_page_images')) {
            if (!empty($oldImagePath)) {
                $filePath = storage_path('app/public/' . $oldImagePath);
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }
            $profilePicturePath = $request->file('site_page_images')->store('home-page', 'public');
            $requestData['home_page_section_images'] = $profilePicturePath;
        }
        // Handle video upload
        if ($request->hasFile('home_page_video')) {
            if ($oldVideoPath != '') {
                $filePath2 = storage_path('app/public/' . $oldVideoPath);
                if (file_exists($filePath2)) {
                    unlink($filePath2);
                }
            }
            $videoPath = $request->file('home_page_video')->store('home-page/videos', 'public');
            $requestData['home_page_video'] = $videoPath;
        }
        $update_cms = ReachHomeCms::findOrFail($id);
        $update_cms->update($requestData);
        return redirect()->route('home-page')->with('success', 'Home Page updated successfully!');
    }
    public function addHomePage()
    {
        return view('sitepages/add_home_page');
    }

    public function saveHomepage(Request $request)
    {
        $requestData = $request->all();
        $requestData['site_page_status'] = 'A';
        /* $validator = Validator::make($requestData, [
             'home_page_section_header' => 'required|string|max:255',
             'home_page_section_type' => 'required',
         ]);*/
        $validator = Validator::make($requestData, [
            'home_page_section_header' => 'required|string|max:255',
            'home_page_section_type' => 'required',
            'order' => [
                'required',
                'integer',
                Rule::unique('reach_home_page_cms', 'order'),
            ],
        ], [
            'home_page_section_header.required' => 'The home page section header is required.',
            'home_page_section_header.string' => 'The home page section header must be a string.',
            'home_page_section_header.max' => 'The home page section header may not be greater than 255 characters.',
            'home_page_section_type.required' => 'The home page section type is required.',
            'order.required' => 'The order is required.',
            'order.integer' => 'The order must be an integer.',
            'order.unique' => 'The order must be unique.',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        if ($request->hasFile('site_page_images')) {
            $profilePicturePath = $request->file('site_page_images')->store('home-page/images', 'public');
            $requestData['home_page_section_images'] = $profilePicturePath;
        }

        $partner = ReachHomeCms::create($requestData);
        return redirect()->route('home-page')->with('success', 'Page created successfully!');
    }

    public function deletehomePage($id)
    {
        $homepage = ReachHomeCms::find($id);
        if ($homepage) {
            $homepage->delete();
            return redirect()->route('home-page')->with('success', 'Home Page section deleted successfully!');
        } else {
            // If the site page doesn't exist, return an error message or handle it as appropriate
            return redirect()->route('home-pages')->with('success', 'Page not found!');
        }
    }
    public function deletehomeVideo($id)
    {

        $sitepage = ReachHomeCms::find($id);

        if ($sitepage['home_page_video']) {
            $filePath = storage_path('app/public/' . $sitepage['home_page_video']);
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            $requestData['home_page_video'] = '';

            $sitepage->update($requestData);
            return redirect()->route('home-page')->with('success', 'Video deleted successfully!');
        } else {
            // If the site page doesn't exist, return an error message or handle it as appropriate
            return redirect()->route('homme-page')->with('success', 'Page not found!');
        }
    }
    public function reachMembershipPageList()
    {
        $membershippages = ReachMembershipPage::get();
        return view('sitepages/reachMembershipList', ['membershippages' => $membershippages]);
    }
    public function addReachMembershipPage()
    {
        return view('sitepages/add_reach_membership_page');
    }
    public function saveReachMembershippage(Request $request)
    {
        $requestData = $request->all();
        // print("<PRE>");print_r($requestData);die();
        $requestData['status'] = 'A';

        $validator = Validator::make($requestData, [
            'membership_title' => 'required|string|max:255',
            'membership_description' => 'required',
            'membership_button' => 'required',
        ], [
            'membership_title.required' => 'Title is required.',
            'membership_description.string' => 'Description is required.',
            'membership_button.max' => 'Button Name is required.',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }


        $membership = ReachMembershipPage::create($requestData);
        return redirect()->route('reach-membership-page')->with('success', 'Reach Mebership Page created successfully!');
    }
    public function edit_membership_page($id)
    {
        $editmembershippage = ReachMembershipPage::find($id);
        return view('sitepages/edit_membership_page', ['membership' => $editmembershippage]);
    }
    public function update_membership_page(Request $request, $id)
    {
        $requestData = $request->all();
        //$requestData['status'] = 'A';
        $requestData['membership_title'] = trim(strip_tags($requestData['membership_title']));
        $requestData['membership_button'] = trim(strip_tags($requestData['membership_button']));
        $validator = Validator::make($requestData, [
            'membership_title' => 'required|string|max:255',
            'membership_description' => 'required|string',
            'membership_button' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }



        $membership = ReachMembershipPage::findOrFail($id);
        $membership->update($requestData);

        return redirect()->route('reach-membership-page')->with('success', 'Membership Page updated successfully!');
    }
    public function deleteMemberShipPage($id)
    {
        $membershippage = ReachMembershipPage::find($id);
        if ($membershippage) {
            $membershippage->delete();
            return redirect()->route('reach-membership-page')->with('success', 'Membership section deleted successfully!');
        } else {
            return redirect()->route('reach-membership-page')->with('success', 'Page not found!');
        }
    }
}
