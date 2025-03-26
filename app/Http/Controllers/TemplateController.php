<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\ReachEmailTemplate;


class TemplateController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    public function index(Request $request)
    {
        $query = ReachEmailTemplate::query();

        // Filtering by member type
        if ($request->has('template_to_status') && !empty($request->template_to_status)) {
            $query->where('template_to_status', $request->template_to_status);
        }

        // Searching by first name and last name
        if ($request->has('search') && !empty($request->search)) {
            $query->where(function ($q) use ($request) {
                $q->where('template_title', 'LIKE', '%' . $request->search . '%')
                  ->orWhere('template_subject', 'LIKE', '%' . $request->search . '%');
            });
        }

        $templates = $query->orderBy('id', 'desc')->paginate(10);

        return view('templates/list', compact('templates'));
    }

    public function add_template()
    {
        return view('templates/add_template');
    }

    public function edit_template($id)
    {
        $template = ReachEmailTemplate::find($id);

        return view('templates/edit_template', ['template' => $template]);
    }

    public function save_template(Request $request)
    {
        $requestData = $request->all();

        $requestData['template_status'] = isset($requestData['template_status']) ? 'A' : 'I';

        $validator = Validator::make($requestData, [
            'template_title' => 'required',
            'template_subject' => 'required',
            'template_message' => 'required',
            'template_tags' => 'required',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        if (isset($requestData['template_id'])) {

            /*$response = $this->mailchimpService->updateTemplate(
                $requestData['mailchimp_id'],
                $requestData['template_title'],
                $requestData['template_message']
            );*/

            $template = ReachEmailTemplate::findOrFail($requestData['template_id']);
            $template->update($requestData);
            $message = 'Template updated successfully!';
        } else {

            $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '_', $requestData['template_title'])));
            $requestData['template_type'] = $slug;

            /*$response = $this->mailchimpService->addTemplate(
                $requestData['template_title'],
                $requestData['template_message'],
                "Email Templates"
            );
            $requestData['mailchimp_id'] = $response->id;*/

            $template = ReachEmailTemplate::create($requestData);
            $message = 'Template created successfully!';
        }

        return redirect()->route('templates')->with('success', $message);
    }

}
