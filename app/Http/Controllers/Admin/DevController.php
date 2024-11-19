<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Http\Controllers\NotifyController;
use App\Http\Requests\Admin\DevRequest;
use App\Mail\AssetMessage;
use App\Mail\DevMessage;
use App\Models\DevNotes;
use App\Repositories\Admin\DevFileAttachmentsRepository;
use App\Repositories\Admin\DevNotesRepository;
use App\Repositories\Admin\UserRepository;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Models\DevFileAttachments;
use App\Http\Requests\Admin\UserRequest;

use App\Repositories\Admin\CampaignBrandsRepository;
use App\Repositories\Admin\DevRepository;

use Illuminate\Support\Facades\Hash;
use Mail;
use Illuminate\Support\Facades\Log;

class DevController extends Controller
{
    private $devRepository;
    private $campaignBrandsRepository;
    private $fileAttachmentsRepository;
    private $devNotesRepository;
    private $userRepository;


    public function __construct(
        DevRepository $devRepository,
        CampaignBrandsRepository $campaignBrandsRepository,
        DevFileAttachmentsRepository $fileAttachmentsRepository,
        DevNotesRepository $devNotesRepository,
        UserRepository $userRepository) // phpcs:ignore
    {
        parent::__construct();

        $this->devRepository = $devRepository;
        $this->campaignBrandsRepository = $campaignBrandsRepository;
        $this->fileAttachmentsRepository = $fileAttachmentsRepository;
        $this->devNotesRepository = $devNotesRepository;
        $this->userRepository = $userRepository;

        $this->data['currentAdminMenu'] = 'dev';
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->data['tasks'] = $this->devRepository->findAll();

        return view('admin.dev.form', $this->data);
    }

    public function dev_approval(Request $request)
    {
        $this->data['currentAdminMenu'] = 'dev_approval';
        $params = $request->all();

        $this->data['dev_list'] = $this->devRepository->get_dev_approval_list();
        $this->data['filter'] = $params;

        return view('admin.dev.approval_list', $this->data);
    }

    public function dev_archives(Request $request)
    {
        $this->data['currentAdminMenu'] = 'dev_archives';
        $params = $request->all();

        $this->data['dev_list'] = $this->devRepository->get_dev_archives_list();
        $this->data['filter'] = $params;

        return view('admin.dev.archives_list', $this->data);
    }

    public function dev_jira(Request $request)
    {
        $param = $request->all();
        $this->data['currentAdminMenu'] = 'dev_jira';

        $this->data['developers'] = $this->userRepository->getdeveloperAssignee();
        if(isset($_GET['developer'])){
            $developer = $param['developer'];
        }else{
            $developer = !empty($param['developer']) ? $param['developer'] : '';
        }
        $this->data['developer'] = $developer;

        $this->data['priorities'] = [
          "Critical",
          "High",
          "Normal"
        ];
        if(isset($_GET['priority'])){
            $priority = $param['priority'];
        }else{
            $priority = !empty($param['priority']) ? $param['priority'] : '';
        }
        $this->data['priority'] = $priority;

        $this->data['filter'] = $param;

        $this->data['dev_requested_list'] = $this->devRepository->get_jira_dev_requested($priority, $developer);
        $this->data['dev_to_do_list'] = $this->devRepository->get_jira_dev_to_do($priority, $developer,);
        $this->data['dev_in_progress_list'] = $this->devRepository->get_jira_dev_in_progress($priority, $developer);
        $this->data['dev_review_list'] = $this->devRepository->get_jira_dev_review($priority, $developer);
        $this->data['dev_done_list'] = $this->devRepository->get_jira_dev_done($priority, $developer);

        return view('admin.dev.jira', $this->data);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        $this->data['brands'] = $this->campaignBrandsRepository->findAll();
        $this->data['domains'] = [
            'beautify.tips',
            'colorsandcare.com',
            'falscara.com',
            'impressbeauty.com',
            'joahbeauty.com',
            'kissdigital.group (KPM)',
            'kisseurope.de',
            'kisseurope.cz',
            'kisseurope.fr',
            'kisseurope.it',
            'kisseurope.pl',
            'kisseurope.sv',
            'kisseurope.uk',
            'kissmexico.mx',
            'kissusa.com',
            'meamora.co.uk',
            'thefarrah.com',
        ];
        $this->data['types'] = [
            'Bug Reports' => 'Notifications of issues or errors in the system that need to be addressed and fixed.',
            'New Feature Requests' => 'Suggestions for adding entirely new features or functionalities to enhance the system.',
            'Enhancement Requests' => 'Proposals to improve or enhance existing features or functionalities.',
            'UI Change Requests' => "Requests for modifications or improvements in the system's user interface.",
            'Performance Issues' => "Notifications of slow or inefficient system performance that requires attention.",
            'Access and Permissions Requests' => "Requests for changes in user access levels or permissions within the system.",
            'Training and Support Requests' => "Requests for additional training resources or support in using the system.",
            'Workflow Optimization' => "Suggestions for improving the overall workflow or usability of the system.",
            'General Feedback' => "General feedback on user experience, system functionality, or any other aspects of the system"
        ];
        $this->data['priorities'] = [
            'Normal' => "Normal",
            'High' => "High (48 ~ 72hr)",
            'Critical' => "Critical (~ 24hr)"
        ];

        $this->data['domain'] = null;
        $this->data['type'] = null;
        $this->data['priority'] = null;
        $this->data['dev'] = null;
        $this->data['dev_status'] = null;
        $this->data['kiss_users'] = $this->userRepository->getKissUsers();

        return view('admin.dev.form', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(DevRequest $request)
    {
        $param = $request->validated();

//        $param = $request->all();
        $param['request_by'] = auth()->user()->id;
        $param['status'] = 'dev_requested';

        if(!isset($param['domain'])){
            return redirect('admin/dev/create')
                ->with('error', 'Please fill out Domain field.');
        }

        if($param['description'] == null){
            return redirect('admin/dev/create')
                ->with('error', 'Please fill out Description field.');
        }


        if (isset($param['domain'])) {
//            $param['disabled_days'] = json_encode($param['disabled_days']);
            $param['domain'] = implode(',', $param['domain']);
        } else {
            $param['domain'] = '';
        }
        $dev = $this->devRepository->create($param);
        if ($dev) {
            $files = $request->file('c_attachment');
            if ($files) {
                foreach ($files as $file) {
                    $fileAttachments = new DevFileAttachments();
                    // file check if exist.
                    $originalName = $file->getClientOriginalName();
                    $fileName = $file->storeAs('images/dev/' . $dev->id, $originalName);
                    $fileAttachments['dev_id'] = $dev->id;
                    $fileAttachments['type'] = 'attachment_file_' . $file->getMimeType();
                    $fileAttachments['author_id'] = $param['request_by'];
                    $fileAttachments['attachment'] = '/' . $fileName;
                    $fileAttachments['file_ext'] = pathinfo($fileName, PATHINFO_EXTENSION);
                    $fileAttachments['file_type'] = $file->getMimeType();
                    $fileAttachments['file_size'] = $file->getSize();
                    $fileAttachments['date_created'] = Carbon::now();
                    $fileAttachments->save();
                }
            }

            $dev_note = new DevNotes();
            $dev_note['user_id'] = $param['request_by'];
            $dev_note['dev_id'] = $dev->id;
            $dev_note['type'] = 'dev';
            $dev_note['note'] = auth()->user()->first_name . " created a new dev request";
            $dev_note['created_at'] = Carbon::now();

            $dev_note->save();

            // send notification to developer manager
            $notify = new NotifyController();
            $notify->dev_request($dev);

            return redirect('admin/dev/'.$dev->id.'/edit')
                ->with('success', 'Success to create Dev Ticket');
        }else{
            return redirect('admin/dev/create')
                ->with('error', 'Fail to create new Dev Ticket');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $this->data['user'] = $this->userRepository->findById($id);

        return view('admin.users.show', $this->data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->data['dev'] = $dev = $this->devRepository->findById($id);
        $this->data['dev_status'] = $dev->status;

        $this->data['brands'] = $this->campaignBrandsRepository->findAll();
        $this->data['domains'] = [
            'beautify.tips',
            'colorsandcare.com',
            'falscara.com',
            'impressbeauty.com',
            'joahbeauty.com',
            'kissdigital.group (KPM)',
            'kisseurope.de',
            'kisseurope.cz',
            'kisseurope.fr',
            'kisseurope.it',
            'kisseurope.pl',
            'kisseurope.sv',
            'kisseurope.uk',
            'kissmexico.mx',
            'kissusa.com',
            'meamora.co.uk',
            'thefarrah.com',
        ];
        $this->data['types'] = [
            'Bug Reports' => 'Notifications of issues or errors in the system that need to be addressed and fixed.',
            'New Feature Requests' => 'Suggestions for adding entirely new features or functionalities to enhance the system.',
            'Enhancement Requests' => 'Proposals to improve or enhance existing features or functionalities.',
            'UI Change Requests' => "Requests for modifications or improvements in the system's user interface.",
            'Performance Issues' => "Notifications of slow or inefficient system performance that requires attention.",
            'Access and Permissions Requests' => "Requests for changes in user access levels or permissions within the system.",
            'Training and Support Requests' => "Requests for additional training resources or support in using the system.",
            'Workflow Optimization' => "Suggestions for improving the overall workflow or usability of the system.",
            'General Feedback' => "General feedback on user experience, system functionality, or any other aspects of the system"
        ];
        $this->data['priorities'] = [
            'Normal' => "Normal",
            'High' => "High (48 ~ 72hr)",
            'Critical' => "Critical (~ 24hr)"
        ];

        $this->data['developers'] = $this->userRepository->getDeveloperAssignee();
        $this->data['domain'] = $dev->domain;
        $this->data['type'] = $dev->type;
        $this->data['priority'] = $dev->priority;
        $this->data['kiss_users'] = $this->userRepository->getKissUsers();

        // Campaign_type_asset_attachments
        $options = [
            'id' => $id,
            'order' => [
                'date_created' => 'desc',
            ]
        ];
        $this->data['attach_files'] = $this->fileAttachmentsRepository->findAll($options);

        // Campaign_notes
        $options = [
            'id' => $id,
            'order' => [
                'created_at' => 'desc',
            ]
        ];
        $correspondences = $this->devNotesRepository->findAll($options);
        $this->data['correspondences'] = $correspondences;

        return view('admin.dev.form', $this->data);
    }

    public function dev_add_note(Request $request)
    {
        $param = $request->all();
        $user = auth()->user();

        $d_id = $param['d_id'];
        $d_title = $param['d_title'];
        $email_list = $param['email_list'];

        $dev_note = new DevNotes();
        $dev_note['user_id'] = $user->id;
        $dev_note['dev_id'] = $d_id;
        $dev_note['type'] = 'dev_note';
        $dev_note['note'] = $param['create_note'];
        $dev_note['created_at'] = Carbon::now();
        $dev_note->save();

        $new_note = preg_replace("/<p[^>]*?>/", "", $param['create_note']);
        $new_note = str_replace("</p>", "\r\n", $new_note);
        $new_note = html_entity_decode($new_note);

        if($email_list){
            $details = [
                'who' => $user->first_name,
                'd_id' => $d_id,
                'd_title' => $d_title,
                'message' => $new_note,
                'url' => '/admin/dev/'.$d_id.'/edit',
            ];
            //send to receivers
            $receiver_list = explode(',', $email_list);

            //check admin group//
            if( in_array('admingroup@kissusa.com', $receiver_list)){

                // add all admins to receiver
                $user_obj = new UserRepository();

                $adminGroup_rs = $user_obj->getAdminGroup();
                foreach ($adminGroup_rs as $user) {
                    if ('admingroup@kissusa.com' != $user['email']) {
                        $receiver_list[] = $user['email'];
                    }
                }
            }
            Mail::to($receiver_list)->send(new DevMessage($details));
        }

        $this->data['currentAdminMenu'] = 'campaign';

        return redirect('admin/dev/'.$d_id.'/edit')
            ->with('success', __('Data has been Updated.'));
    }

    public function dev_assign(request $request)
    {
        $param = $request->all();
        $params['assign_to'] = $param['developer'];
        $params['status'] = 'dev_to_do';
        $params['updated_at'] = Carbon::now();

        $dev = $this->devRepository->update($param['d_id'], $params);
        $stmt = " has assigned a ticket to ";
        if($dev){
            $this->add_dev_assign_correspondence($param['d_id'], $params['assign_to'], $stmt);
        }

        // send notification to developer
        $notify = new NotifyController();
        $notify->dev_to_do($param['d_id'], $dev->assign_to);

        return redirect('admin/dev/'.$param['d_id'].'/edit')
            ->with('success', 'The task has been assigned.');

    }

    public function dev_in_progress($id)
    {
        $params['status'] = 'dev_in_progress';
        $params['updated_at'] = Carbon::now();

        $dev = $this->devRepository->update($id, $params);

        if($dev){
            $stmt = " updated the status to Dev In Progress ";
            $this->add_dev_status_correspondence($id, $stmt);

            echo '/admin/dev/'.$id.'/edit';
        }else{
            echo 'fail';
        }
    }

    public function dev_review($id)
    {
        $params['status'] = 'dev_review';
        $params['updated_at'] = Carbon::now();
        $dev = $this->devRepository->update($id, $params);
        if($dev){
            $stmt = " updated the status to Dev Review ";
            $this->add_dev_status_correspondence($id, $stmt);

            // send notification to requester
            $notify = new NotifyController();
            $notify->dev_review($id, $dev->request_by);

            echo '/admin/dev/'.$id.'/edit';
        }else{
            echo 'fail';
        }
    }

    public function dev_done($id)
    {
        $params['status'] = 'dev_done';
        $params['updated_at'] = Carbon::now();
        $dev = $this->devRepository->update($id, $params);
        if($dev){
            $stmt = " updated the status to Dev Done ";
            $this->add_dev_status_correspondence($id, $stmt);

            echo '/admin/dev/'.$id.'/edit';
        }else{
            echo 'fail';
        }
    }

    public function add_dev_status_correspondence($dev_id, $stmt)
    {
        // Insert into campaign note for correspondence
        $user = auth()->user();

        $change_line  = "<p>$user->first_name". $stmt . "</p>";

        $dev_note = new DevNotes();
        $dev_note['user_id'] = $user->id;
        $dev_note['dev_id'] = $dev_id;
        $dev_note['type'] = 'dev_note';
        $dev_note['note'] = $change_line;
        $dev_note['created_at'] = Carbon::now();
        $dev_note->save();
    }

    public function add_dev_assign_correspondence($dev_id, $developer, $stmt)
    {
        // Insert into campaign note for correspondence
        $user = auth()->user();
        $developer_obj = $this->userRepository->findById($developer);

        $change_line  = "<p>$user->first_name". $stmt . $developer_obj->first_name."</p>";

        $dev_note = new DevNotes();
        $dev_note['user_id'] = $user->id;
        $dev_note['dev_id'] = $dev_id;
        $dev_note['type'] = 'dev_note';
        $dev_note['note'] = $change_line;
        $dev_note['created_at'] = Carbon::now();
        $dev_note->save();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(request $request, $id)
    {
        $param = $request->all();
        $user = auth()->user();
//        $param['request_by'] = $user->id;

        if (isset($param['domain'])) {
            $param['domain'] = implode(',', $param['domain']);
        } else {
            $param['domain'] = '';
        }

        $data = $request->request->all();
        $dev = $this->devRepository->findById($id);
        $new = array(
            'title'             => $data['title'],
            'type'              => $data['type'],
            'domain'            => $param['domain'],
            'description'       => $data['description'],
        );
//        ddd(htmlspecialchars_decode($data['description']));
        $origin = $dev->toArray();
        foreach ($new as $key => $value) {
            if (array_key_exists($key, $origin)) {
                if (html_entity_decode($new[$key]) != html_entity_decode($origin[$key])) {
                    $changed[$key]['new'] = $new[$key];
                    $changed[$key]['original'] = $origin[$key];
                }
            }
        }
        $change_line  = "<p>$user->first_name made a change to a Task</p>";
        if(!empty($changed)){
            foreach ($changed as $label => $change) {

                $label = strtoupper(str_replace('_', ' ', $label));
                $from  = trim($change['original']); // Remove strip tags
                $to    = trim($change['new']);      // Remove strip tags

                $change_line .= "<div class='change_label'><p>$label:</p></div>"
                    . "<div class='change_to'><p>$to</p></div>"
                    . "<div class='change_from'><del><p>$from</p></del></div>";
            }
            $dev_note = new DevNotes();
            $dev_note['dev_id'] = $dev->id;
            $dev_note['user_id'] = $user->id;
            $dev_note['type'] = 'dev';
            $dev_note['note'] = $change_line;
            $dev_note['created_at'] = Carbon::now();
            $dev_note->save();
        }


        if ($this->devRepository->update($id, $param)) {

            $files = $request->file('c_attachment');
            if ($files) {
                foreach ($files as $file) {
                    $fileAttachments = new DevFileAttachments();
                    // file check if exist.
                    $originalName = $file->getClientOriginalName();
                    $fileName = $file->storeAs('images/dev/' . $id, $originalName);
                    $fileAttachments['dev_id'] = $id;
                    $fileAttachments['type'] = 'attachment_file_' . $file->getMimeType();
                    $fileAttachments['author_id'] = $param['request_by'];
                    $fileAttachments['attachment'] = '/' . $fileName;
                    $fileAttachments['file_ext'] = pathinfo($fileName, PATHINFO_EXTENSION);
                    $fileAttachments['file_type'] = $file->getMimeType();
                    $fileAttachments['file_size'] = $file->getSize();
                    $fileAttachments['date_created'] = Carbon::now();
                    $fileAttachments->save();

                    // insert file attachment correspondence
                    $this->add_file_correspondence($dev, $user, $file->getMimeType(), $originalName);

                }
            }

            return redirect('admin/dev/'.$id.'/edit')
                ->with('success', 'Success to update Dev Ticket');
        }

        return redirect('admin/dev/'.$id.'/edit')
                ->with('error', 'Fail to update Dev Ticket');
    }

    public function add_file_correspondence($dev, $user, $file_type, $originalName)
    {
        // Insert into campaign note for correspondence (attachment file)
        $change_line  = "<p>$user->first_name add a file $originalName ($file_type) to task</p>";

        $dev_note = new DevNotes();
        $dev_note['user_id'] = $user->id;
        $dev_note['dev_id'] = $dev->id;
        $dev_note['type'] = 'dev';
        $dev_note['note'] = $change_line;
        $dev_note['created_at'] = Carbon::now();
        $dev_note->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = $this->userRepository->findById($id);

        if ($user->id == auth()->user()->id) {
            return redirect('admin/users')
                ->with('error', 'Could not delete yourself.');
        }

        if ($this->userRepository->delete($id)) {
            return redirect('admin/users')
                ->with('success', __('users.success_deleted_message', ['first_name' => $user->first_name]));
        }
        return redirect('admin/users')
                ->with('error', __('users.fail_to_delete_message', ['first_name' => $user->first_name]));
    }

    public function fileRemove($id)
    {
        $fileAttachment = $this->fileAttachmentsRepository->findById($id);

        $file_type = $fileAttachment->file_type;
        $campaign_id = $fileAttachment->id;
        $asset_id = $fileAttachment->asset_id;

        $user = auth()->user();

        if($fileAttachment->delete()){

//            if($asset_id != 0){
//
//                $assetIndex = $this->fileAttachment->findById($asset_id);
//                $asset_type =  strtoupper(str_replace('_', ' ', $assetIndex->type));
//
//                $change_line = "<p>$user->first_name removed a attachment ($file_type) on $asset_type (#$asset_id)</p>";
//                $campaign_note['type'] = $assetIndex->type;
//            }else {
//                $change_line = "<p>$user->first_name removed a attachment ($file_type) on campaign</p>";
//                $campaign_note['type'] = 'campaign';
//            }

//            $campaign_note = new CampaignNotes();
//            $campaign_note['id'] = $campaign_id;
//            $campaign_note['user_id'] = $user->id;
//            $campaign_note['asset_id'] = $asset_id;
//            $campaign_note['note'] = $change_line;
//            $campaign_note['date_created'] = Carbon::now();
//            $campaign_note->save();
//
            echo 'success';
        }else{
            echo 'fail';
        }
    }


}
