<?php

namespace App\Http\Controllers\Admin;

use App\Authorizable;
use App\Http\Controllers\Controller;
use App\Http\Controllers\NotifyController;
use App\Mail\MyDemoMail;
use App\Mail\NewProject;
use App\Mail\NewRequest;
use App\Mail\NoteProject;
use App\Mail\SendMail;
use App\Mail\TaskStatusNotification;
use App\Models\CampaignNotes;

use App\Models\ProjectNotes;
use App\Models\ProjectTaskIndex;
use App\Models\ProjectTypeTaskAttachments;
use App\Models\RaRequestNotes;
use App\Models\RaRequestTypeAttachments;
use App\Models\SubRaRequestIndex;

use App\Models\SubRaRequestType;
use App\Models\User;

use App\Repositories\Admin\BrandRepository;
use App\Repositories\Admin\ProjectNotesRepository;

use App\Repositories\Admin\RaRequestRepository;
use App\Repositories\Admin\RaRequestNotesRepository;
use App\Repositories\Admin\RaRequestTypeFileAttachmentsRepository;

use App\Repositories\Admin\SubRaRequestIndexRepository;
use App\Repositories\Admin\SubRaRequestTypeRepository;
use App\Repositories\Admin\TaskTypeConceptDevelopmentRepository;

use App\Repositories\Admin\ProjectRepository;
use App\Repositories\Admin\ProjectTaskIndexRepository;
use App\Repositories\Admin\ProjectTaskFileAttachmentsRepository;
use App\Repositories\Admin\TaskTypeLegalRequestRepository;
use App\Repositories\Admin\TaskTypeProductBriefRepository;
use App\Repositories\Admin\TeamRepository;
use App\Repositories\Admin\UserRepository;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class RaRequestController extends Controller
{
    Private $projectRepository;

    Private $subRaRequestIndexRepository;
    Private $subRaRequestTypeRepository;

    Private $raRequestRepository;

    Private $projectTaskIndexRepository;
    private $projectTaskFileAttachmentsRepository;
    Private $projectNotesRepository;

    Private $raRequestTypeFileAttachmentsRepository;
    Private $raRequestNotesRepository;
    Private $teamRepository;
    private $userRepository;
    Private $brandRepository;

    public function __construct(ProjectRepository $projectRepository,

                                subRaRequestIndexRepository $subRaRequestIndexRepository,
                                SubRaRequestTypeRepository $subRaRequestTypeRepository,
                                RaRequestRepository $raRequestRepository,

                                ProjectTaskIndexRepository $projectTaskIndexRepository,
                                TaskTypeConceptDevelopmentRepository $taskTypeConceptDevelopmentRepository,
                                TaskTypeLegalRequestRepository $taskTypeLegalRequestRepository,
                                TaskTypeProductBriefRepository $taskTypeProductBriefRepository,
                                ProjectTaskFileAttachmentsRepository $projectTaskFileAttachmentsRepository,
                                ProjectNotesRepository $projectNotesRepository,

                                RaRequestTypeFileAttachmentsRepository $raRequestTypeFileAttachmentsRepository,
                                RaRequestNotesRepository $raRequestNotesRepository,

                                BrandRepository $brandRepository,
                                TeamRepository $teamRepository,
                                UserRepository $userRepository)
    {
        parent::__construct();

        $this->projectRepository = $projectRepository;

        $this->subRaRequestIndexRepository = $subRaRequestIndexRepository;
        $this->subRaRequestTypeRepository = $subRaRequestTypeRepository;

        $this->raRequestRepository = $raRequestRepository;
        $this->subRaRequestIndexRepository = $subRaRequestIndexRepository;

        $this->projectTaskIndexRepository = $projectTaskIndexRepository;
        $this->taskTypeConceptDevelopmentRepository = $taskTypeConceptDevelopmentRepository;
        $this->taskTypeLegalRequestRepository = $taskTypeLegalRequestRepository;
        $this->taskTypeProductBriefRepository = $taskTypeProductBriefRepository;
        $this->projectTaskFileAttachmentsRepository = $projectTaskFileAttachmentsRepository;
        $this->projectNotesRepository = $projectNotesRepository;

        $this->raRequestNotesRepository = $raRequestNotesRepository;
        $this->raRequestTypeFileAttachmentsRepository = $raRequestTypeFileAttachmentsRepository;

        $this->brandRepository = $brandRepository;
        $this->teamRepository = $teamRepository;
        $this->userRepository = $userRepository;
    }

    public function index(Request $request)
    {
        $params = $request->all();
        $params['status'] = 'active';
        $this->data['currentAdminMenu'] = 'ra_request';

        $user = auth()->user();
        if($user->team == 'Legal RA' || $user->team == 'Admin') {
            $params['cur_user'] = '';
        }else{
            $params['cur_user'] = $this->userRepository->user_array_for_access($user);
        }
        $options = [
            'per_page' => $this->perPage,
            'order' => [
                'id' => 'asc',
            ],
            'filter' => $params,
        ];
        $this->data['filter'] = $params;
        $this->data['projects'] = $this->raRequestRepository->findAll($options);
        $this->data['id'] = !empty($params['id']) ? $params['id'] : '';

        return view('admin.ra_request.index', $this->data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->data['currentAdminMenu'] = 'ra_board';
        $project = $this->projectRepository->findById($id);
        $this->data['project'] = $project;
        $team_options = [
            'order' => [
                'id' => 'asc',
            ],
            'filter' => [
                'is_active' => 'yes',
                'npd' => 'YES'
            ],
        ];
        $this->data['teams'] =$this->teamRepository->findAll($team_options);
        $this->data['project_types'] = [
            'New',
            'Soft Change',
            'Hard Change',
            'SKU Extension'
        ];
        $this->data['project_year_list'] = [
            '2024','2025','2026',
            '2027', '2028', '2029',
            '2030', '2031', '2032',
        ];
        $this->data['sales_plan_list'] = [
            'YES','NO',
        ];
        $this->data['team'] = $project->team;
        $this->data['brand'] = $project->brand;
        $this->data['project_type'] = $project->project_type;
        $this->data['kiss_users'] = $this->userRepository->getKissUsers();

        $author_obj = User::find($project->author_id);
        if($author_obj){
            $this->data['author_name'] = $author_obj['first_name'] . " " . $author_obj['last_name'];
        }else{
            $this->data['author_name'] = 'N/A';
        }

        // Task_id
        $task_id = $this->raRequestRepository->get_task_id_for_ra($id);
        $this->data['task_id']= $task_id;

        // Request Type list
        $this->data['request_type_list'] = $request_type_list = $this->raRequestRepository->get_request_type_list_by_task_id($task_id);

        // task_detail
        if(sizeof($request_type_list)>0){
            foreach ($request_type_list as $k => $request_type){
                $ra_request_type_id = $request_type->ra_request_type_id;
                $task_files = $this->raRequestTypeFileAttachmentsRepository->findAllByRequestTypeId($ra_request_type_id);
                $request_type_list[$k]->files = $task_files;
            }
        }

        // Project_notes
        $options = [
            'id' => $task_id,
            'order' => [
                'created_at' => 'desc',
            ]
        ];
        $correspondences = $this->raRequestNotesRepository->findAll($options);
        $this->data['correspondences'] = $correspondences;

        $this->data['ra_assignee_list'] = $this->userRepository->getRaAssigneeList();

        // Launch Date History
        $launch_date_history = $this->projectNotesRepository->get_launch_date_history($id);
        $launch_date_history_text = '';

        if(sizeof($launch_date_history)>0) {
            foreach ($launch_date_history as $row){
                $note_package = $row->note;
                $note_package_array = explode("<div class='change_label'>", $note_package);
                $needle = 'Launch Date:';
                foreach ($note_package_array as $line){
                    if (strpos($line, $needle) !== false) {
                        $launch_date_history_text .=
                            '<div class="note">
                                <ul class="list-unstyled list-unstyled-border list-unstyled-noborder">
                                    <li class="media">
                                        <div class="media-body">
                                            <div class="media-title-note" style="margin-bottom: -25px;">
                                                <div class="media-right"><div class="text-time">'.$row->created_at.'</div></div>
                                            </div>
                                            <div class="media-description text-muted" style="padding: 15px;">
                                                <p>Data has been changed by <b style="color: black;">'.$row->author_name.'</b> </p>
                                                    <div style="margin: 5px 0 0 0;">'.$line.'
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>';
                    }
                }
            }
        }
        $this->data['launch_date_history_text'] = $launch_date_history_text;

        /////////// RA Request Task ////////////////////////////////////////////
        $this->data['product_type_list'] = [
            'Leave On',
            'Rinse Off'
        ];
        $this->data['product_form_list'] = [
            'Solid',
            'Liquid',
            'Spray (Pump)',
            'Spray (Aerosol)',
            'Pre-moistened pad / Towelette',
            'Others'
        ];
        $this->data['area_of_application_list'] = [
            'Hair',
            'Face',
            'Lips',
            'Eyes',
            'Hand',
            'Feet',
            'Body',
            'Nails',
            'Others'
        ];
        $this->data['market_list'] = [
            'U.S. Retailer',
            'E-Commerce Only',
            'Retailer + E Commerce',
            'Professionals (e.g., Beauty Salon)',
        ];
        $this->data['compliant_regions_list'] = [
            'US',
            'Canada',
            'EU',
            'UK'
        ];
        $this->data['on_going_registrations_list'] = [
            'WERCS',
            'SmarterX',
            'MoCRA',
            'CSCP'
        ];

        $this->data['revision_reason_list'] = [
            'Budget Constraints',
            'Change in Project Scope',
            'Correct Errors or Mistakes',
            'Does Not Meet Requirements',
            'Inaccurate Information',
            'Management Direction',
            'Market Trends or Customer Needs',
            'Missing Required Information',
            'Timeline Adjustments',
            'Unspecified or Vague Details',
        ];

        return view('admin.ra_request.form', $this->data);
    }

    public function board(Request $request)
    {
        $param = $request->all();
        $this->data['currentAdminMenu'] = 'ra_board';

        $user = auth()->user();

        if($user->team == 'Legal RA' || $user->team == 'Admin') {
            $cur_user = ' ';
        }else{
            $cur_user = $this->userRepository->getPageAccess($user);
        }

        if(isset($_GET[''])) {
            $request_type = $param['request_type'];
        }else{
            $request_type = !empty($param['request_type']) ? $param['request_type'] : '';
        }
        if(isset($_GET[''])) {
            $assignee = $param['assignee'];
        }else{
            $assignee = !empty($param['assignee']) ? $param['assignee'] : '';
        }
        if(isset($_GET[''])) {
            $team = $param['team'];
        }else{
            $team = !empty($param['team']) ? $param['team'] : '';
        }
        $this->data['task_list_action_requested'] = $this->subRaRequestTypeRepository->get_action_requested_list($cur_user, $request_type, $assignee, $team);
        $this->data['task_list_in_progress'] = $this->subRaRequestTypeRepository->get_in_progress_list($cur_user, $request_type, $assignee, $team);
        $this->data['task_list_action_review'] = $this->subRaRequestTypeRepository->get_action_review_list($cur_user, $request_type, $assignee, $team);
        $this->data['task_list_action_completed'] = $this->subRaRequestTypeRepository->get_action_completed_list($cur_user, $request_type, $assignee, $team);

        $this->data['request_type'] = $request_type;
        $this->data['request_type_list'] = [
            'formula_review' => 'Formula Review',
            'label_review' => 'Label Review',
            'us_launch' => 'US Launch',
            'canada_launch' => 'CANADA Launch',
            'eu_launch' => 'EU Launch',
            'uk_launch' => 'UK Launch',
//            'latam_launch' => 'LATAM Launch'
        ];

        $this->data['assignee'] = $assignee;
        $this->data['assignee_list'] = $this->userRepository->getRaAssigneeList();

        $this->data['team'] = $team;
        $team_options = [
            'order' => [
                'id' => 'asc',
            ],
            'filter' => [
                'is_active' => 'yes',
                'npd' => 'YES'
            ],
        ];
        $this->data['team_list'] =$this->teamRepository->findAll($team_options);

        return view('admin.ra_request.board', $this->data);

    }

    public function request_list(Request $request)
    {
        $param = $request->all();
        $this->data['currentAdminMenu'] = 'ra_request_list';
        $user = auth()->user();
        if($user->team == 'Legal RA' || $user->team == 'Admin') {
            $cur_user = ' ';
        }else{
            $cur_user = $this->userRepository->getPageAccess($user);
        }
        if(isset($_GET[''])) {
            $assignee = $param['assignee'];
        }else{
            $assignee = !empty($param['assignee']) ? $param['assignee'] : '';
        }
        if(isset($_GET[''])) {
            $team = $param['team'];
        }else{
            $team = !empty($param['team']) ? $param['team'] : '';
        }
        if(isset($_GET[''])) {
            $status = $param['status'];
        }else{
            $status = !empty($param['status']) ? $param['status'] : '';
        }

        $this->data['task_list'] = $this->subRaRequestIndexRepository->get_task_list_request($cur_user, $assignee, $team, $status);

        $this->data['assignee'] = $assignee;
        $this->data['team'] = $team;
        $this->data['status'] = $status;
        $this->data['filter'] = $param;

        $team_options = [
            'order' => [
                'id' => 'asc',
            ],
            'filter' => [
                'is_active' => 'yes',
                'npd' => 'YES'
            ],
        ];
        $brand_options = [
            'order' => [
                'id' => 'asc',
            ],
            'filter' => [
                'is_active' => 'yes'
            ],
        ];
        $this->data['status_list'] = [
            'action_requested',
            'in_progress',
            'action_review',
            'action_completed'
        ];
        $this->data['ra_request_assignee_list'] = $this->userRepository->getRaAssigneeList();
        $this->data['teams'] =$this->teamRepository->findAll($team_options);
        $this->data['brands'] =$this->brandRepository->findAll($brand_options);

        return view('admin.ra_request.request_list', $this->data);
    }

    public function registration_list(Request $request)
    {
        $param = $request->all();
        $this->data['currentAdminMenu'] = 'ra_registration_list';
        $user = auth()->user();
        if($user->team == 'Legal RA' || $user->team == 'Admin') {
            $cur_user = ' ';
        }else{
            $cur_user = $this->userRepository->getPageAccess($user);
        }
        if(isset($_GET[''])) {
            $assignee = $param['assignee'];
        }else{
            $assignee = !empty($param['assignee']) ? $param['assignee'] : '';
        }
        if(isset($_GET[''])) {
            $team = $param['team'];
        }else{
            $team = !empty($param['team']) ? $param['team'] : '';
        }
        if(isset($_GET[''])) {
            $status = $param['status'];
        }else{
            $status = !empty($param['status']) ? $param['status'] : '';
        }

        $this->data['task_list'] = $this->subRaRequestIndexRepository->get_task_list_registration($cur_user, $assignee, $team, $status);

        $this->data['assignee'] = $assignee;
        $this->data['team'] = $team;
        $this->data['status'] = $status;
        $this->data['filter'] = $param;

        $team_options = [
            'order' => [
                'id' => 'asc',
            ],
            'filter' => [
                'is_active' => 'yes',
                'npd' => 'YES'
            ],
        ];
        $brand_options = [
            'order' => [
                'id' => 'asc',
            ],
            'filter' => [
                'is_active' => 'yes'
            ],
        ];
        $this->data['status_list'] = [
            'action_requested',
            'in_progress',
            'action_review',
            'action_completed'
        ];
        $this->data['ra_request_assignee_list'] = $this->userRepository->getRaAssigneeList();
        $this->data['teams'] =$this->teamRepository->findAll($team_options);
        $this->data['brands'] =$this->brandRepository->findAll($brand_options);

        return view('admin.ra_request.registration_list', $this->data);
    }

    public function add_formula_review(Request $request){

        $user = auth()->user();

        $sub_ra_request_index = new SubRaRequestIndex();
        $sub_ra_request_index['task_id'] = $request['formula_review_t_id'];
        $sub_ra_request_index['request_type'] = $request['formula_review_request_type'];
        $sub_ra_request_index['author_id'] = $user->id;
        $sub_ra_request_index['status'] = 'action_requested';
        $sub_ra_request_index->save();

        $ra_request_type_id = $sub_ra_request_index->id;

        $subRaRequestType = new SubRaRequestType();
        $subRaRequestType['id'] = $request['formula_review_t_id'];
        $subRaRequestType['author_id'] = $user->id;
        $subRaRequestType['type'] = 'formula_review';
        $subRaRequestType['ra_request_type_id'] = $ra_request_type_id;
        $subRaRequestType['due_date'] = $request['formula_review_due_date'];
        $subRaRequestType['vendor_code'] = $request['formula_review_vendor_code'];
        $subRaRequestType['vendor_name'] = $request['formula_review_vendor_name'];
        $subRaRequestType['product_type'] = $request['formula_review_product_type'];
        $subRaRequestType['product_form'] = $request['formula_review_product_form'];
        $subRaRequestType['if_other_product_form'] = $request['formula_review_if_other_product_form'];
        // multi checkbox
        if (isset($request['formula_review_area_of_application'])) {
            $subRaRequestType['area_of_application'] = implode(',', $request['formula_review_area_of_application']);
        } else {
            $subRaRequestType['area_of_application'] = '';
        }
        $subRaRequestType['if_other_area_of_application'] = $request['formula_review_if_other_area_of_application'];
        // one checkbox
        if (isset($request['formula_review_fragrance'])) {
            $subRaRequestType['fragrance'] = 'on';
        } else {
            $subRaRequestType['fragrance'] = null;
        }
        $subRaRequestType['if_other_fragrance'] = $request['formula_review_if_other_fragrance'];

        $subRaRequestType->save();

        $this->correspondence_add_ra_reqeust_type($ra_request_type_id, 'Formula Review', $sub_ra_request_index);

        // add campaign_type_asset_attachments
        if($request->file('formula_review_attachment')){
            foreach ($request->file('formula_review_attachment') as $file) {
                $attachments = new RaRequestTypeAttachments();

//                $fileName = $file->storeAs('campaigns/'.$request['legal_request_c_id'].'/'.$asset_id, $originalName);
                $fileName = $this->file_exist_check_ra($file, $request['formula_review_t_id'], $ra_request_type_id);

                $attachments['task_id'] = $request['formula_review_t_id'];
                $attachments['type'] = 'attachment_file_' . $file->getMimeType();
                $attachments['author_id'] = $user->id;
                $attachments['attachment'] = '/' . $fileName;
                $attachments['ra_request_type_id'] = $ra_request_type_id;
                $attachments['file_ext'] = pathinfo($fileName, PATHINFO_EXTENSION);
                $attachments['file_type'] = $file->getMimeType();
                $attachments['file_size'] = $file->getSize();
                $attachments['created_at'] = Carbon::now();
                $attachments->save();

                $this->add_file_correspondence_for_ra($ra_request_type_id, $subRaRequestType->id, $user, $fileName, 'formula_review');
            }
        }

        $project_id = $this->projectTaskIndexRepository->get_project_id_by_task_id($request['formula_review_t_id']);

        // Send Notification
//        $this->send_notification_action_request($user, $project_id, $subRaRequestType, $ra_request_type_id);

        return redirect('admin/ra_request/'.$project_id.'/edit#'.$ra_request_type_id)
            ->with('success', __('Added the Formula Review Type : ' . $ra_request_type_id));
    }

    function send_notification_action_request($user, $project_id, $subRequestType, $request_type_id)
    {
        $project_obj = $this->projectRepository->findById($project_id);
        if($subRequestType['priority'] == 'Urgent'){
            $due_date_mail = $subRequestType['due_date_urgent'];
            $priority_mail = 'Urgent';
        }else{
            $due_date_mail = $subRequestType['due_date'];
            $priority_mail = 'Normal';
        }

        $details = [
            'mail_subject'      => 'Action Requested : RA Request',
            'template'          => 'emails.task.new_request',
            'receiver'          => "RA Team",
            'title'             => "Action Requested : RA Request",
            'body'              => 'You got a new request from ' . $user->team . ', ' . $user->first_name . ' ' . $user->last_name . '. ',
            'project_id'        => $project_id,
            'project_title'     => $project_obj->name,
            'request_id'        => $request_type_id,
            'request_type'      => $subRequestType['type'],
            'priority'          => $priority_mail,
            'due_date'          => $due_date_mail,
            'url'               => '/admin/ra_request/'.$project_id.'/edit#'.$request_type_id,
        ];

        $group_rs = $this->userRepository->get_receiver_emails_by_team('Admin');
        foreach ($group_rs as $team_user) {
            $receiver_list[] = $team_user['email'];
        }

        /// Send to receivers
        Mail::to($receiver_list)->send(new NewRequest($details));
    }

    public function edit_formula_review(Request $request, $ra_request_type_id)
    {
        $param = $request->all();

        if (isset($param['area_of_application'])) {
            $param['area_of_application'] = implode(',', $param['area_of_application']);
        } else {
            $param['area_of_application'] = '';
        }

        if (isset($param['fragrance'])) {
            $param['fragrance'] = 'on';
        } else {
            $param['fragrance'] = null;
        }

        if (isset($param['compliant_regions'])) {
            $param['compliant_regions'] = implode(',', $param['compliant_regions']);
        } else {
            $param['compliant_regions'] = '';
        }

        // Permission_check
//        if(!$this->permission_check($param)){
//            return redirect('admin/project/' . $concept_development->id . '/edit#'.$task_id)
//                ->with('error', __('This action is no longer permitted. Please contact an Administrator.'));
//        }

        $project_id = $this->projectTaskIndexRepository->get_project_id_by_task_id($param['formula_review_t_id']);
        $subRaRequestType = $this->subRaRequestTypeRepository->findById($ra_request_type_id);

        if($this->subRaRequestTypeRepository->update($ra_request_type_id, $param)){

            $user = auth()->user();
            // insert into campaign note for correspondence
            $this->correspondence_update_ra_request_type('formula_review', $param, $subRaRequestType, $user);

            if($request->file('attachment')){
                foreach ($request->file('attachment') as $file) {
                    $attachments = new RaRequestTypeAttachments();

                    $fileName = $this->file_exist_check_ra($file, $subRaRequestType->id, $ra_request_type_id);

                    $attachments['task_id'] = $subRaRequestType->id; // task_id
                    $attachments['type'] = 'attachment_file_' . $file->getMimeType();
                    $attachments['author_id'] = $user->id;
                    $attachments['attachment'] = '/' . $fileName;
                    $attachments['ra_request_type_id'] = $ra_request_type_id;
                    $attachments['file_ext'] = pathinfo($fileName, PATHINFO_EXTENSION);
                    $attachments['file_type'] = $file->getMimeType();
                    $attachments['file_size'] = $file->getSize();
                    $attachments['created_at'] = Carbon::now();
                    $attachments->save();

                    // insert file attachment correspondence
                    $this->add_file_correspondence_for_ra($ra_request_type_id, $subRaRequestType->id, $user, $fileName, 'formula_review');
                }
            }

            return redirect('admin/ra_request/'.$project_id.'/edit#'.$ra_request_type_id)
                ->with('success', __('Formula Review ('.$ra_request_type_id.') - Update Success'));
        }

        return redirect('admin/ra_request/'.$project_id.'/edit#'.$ra_request_type_id)
            ->with('error', __('Update Failed'));
    }

    public function add_label_review(Request $request){

        $user = auth()->user();

        $sub_ra_request_index = new SubRaRequestIndex();
        $sub_ra_request_index['task_id'] = $request['label_review_t_id'];
        $sub_ra_request_index['request_type'] = $request['label_review_request_type'];
        $sub_ra_request_index['author_id'] = $user->id;
        $sub_ra_request_index['status'] = 'action_requested';
        $sub_ra_request_index->save();

        $ra_request_type_id = $sub_ra_request_index->id;

        $subRaRequestType = new SubRaRequestType();
        $subRaRequestType['id'] = $request['label_review_t_id'];
        $subRaRequestType['author_id'] = $user->id;
        $subRaRequestType['type'] = 'label_review';
        $subRaRequestType['ra_request_type_id'] = $ra_request_type_id;
        $subRaRequestType['due_date'] = $request['label_review_due_date'];

        // one checkbox
        if (isset($request['label_review_formula_review'])) {
            $subRaRequestType['formula_review'] = 'on';
        } else {
            $subRaRequestType['formula_review'] = null;
        }

        $subRaRequestType->save();

        $this->correspondence_add_ra_reqeust_type($ra_request_type_id, 'Label Review', $sub_ra_request_index);

        // add campaign_type_asset_attachments
        if($request->file('label_review_attachment')){
            foreach ($request->file('label_review_attachment') as $file) {
                $attachments = new RaRequestTypeAttachments();

//                $fileName = $file->storeAs('campaigns/'.$request['legal_request_c_id'].'/'.$asset_id, $originalName);
                $fileName = $this->file_exist_check_ra($file, $request['label_review_t_id'], $ra_request_type_id);

                $attachments['task_id'] = $request['label_review_t_id'];
                $attachments['type'] = 'attachment_file_' . $file->getMimeType();
                $attachments['author_id'] = $user->id;
                $attachments['attachment'] = '/' . $fileName;
                $attachments['ra_request_type_id'] = $ra_request_type_id;
                $attachments['file_ext'] = pathinfo($fileName, PATHINFO_EXTENSION);
                $attachments['file_type'] = $file->getMimeType();
                $attachments['file_size'] = $file->getSize();
                $attachments['created_at'] = Carbon::now();
                $attachments->save();

                $this->add_file_correspondence_for_ra($ra_request_type_id, $subRaRequestType->id, $user, $fileName, 'label_review');
            }
        }

        $project_id = $this->projectTaskIndexRepository->get_project_id_by_task_id($request['label_review_t_id']);

        // Send Notification
//        $this->send_notification_action_request($user, $project_id, $subRaRequestType, $ra_request_type_id);

        return redirect('admin/ra_request/'.$project_id.'/edit#'.$ra_request_type_id)
            ->with('success', __('Added the Label Review Type : ' . $ra_request_type_id));
    }

    public function edit_label_review(Request $request, $ra_request_type_id)
    {
        $param = $request->all();

        // checkbox
        if (isset($param['formula_review'])) {
            $param['formula_review'] = 'on';
        } else {
            $param['formula_review'] = null;
        }

        // Permission_check
//        if(!$this->permission_check($param)){
//            return redirect('admin/project/' . $concept_development->id . '/edit#'.$task_id)
//                ->with('error', __('This action is no longer permitted. Please contact an Administrator.'));
//        }

        $project_id = $this->projectTaskIndexRepository->get_project_id_by_task_id($param['label_review_t_id']);
        $subRaRequestType = $this->subRaRequestTypeRepository->findById($ra_request_type_id);

        if($this->subRaRequestTypeRepository->update($ra_request_type_id, $param)){

            $user = auth()->user();
            // insert into campaign note for correspondence
            $this->correspondence_update_ra_request_type('label_review', $param, $subRaRequestType, $user);

            if($request->file('attachment')){
                foreach ($request->file('attachment') as $file) {
                    $attachments = new RaRequestTypeAttachments();

                    $fileName = $this->file_exist_check_ra($file, $subRaRequestType->id, $ra_request_type_id);

                    $attachments['task_id'] = $subRaRequestType->id; // task_id
                    $attachments['type'] = 'attachment_file_' . $file->getMimeType();
                    $attachments['author_id'] = $user->id;
                    $attachments['attachment'] = '/' . $fileName;
                    $attachments['ra_request_type_id'] = $ra_request_type_id;
                    $attachments['file_ext'] = pathinfo($fileName, PATHINFO_EXTENSION);
                    $attachments['file_type'] = $file->getMimeType();
                    $attachments['file_size'] = $file->getSize();
                    $attachments['created_at'] = Carbon::now();
                    $attachments->save();

                    // insert file attachment correspondence
                    $this->add_file_correspondence_for_ra($ra_request_type_id, $subRaRequestType->id, $user, $fileName, 'label_review');
                }
            }

            return redirect('admin/ra_request/'.$project_id.'/edit#'.$ra_request_type_id)
                ->with('success', __('Label Review ('.$ra_request_type_id.') - Update Success'));
        }

        return redirect('admin/ra_request/'.$project_id.'/edit#'.$ra_request_type_id)
            ->with('error', __('Update Failed'));
    }

    public function add_us_launch(Request $request){

        $user = auth()->user();

        $sub_ra_request_index = new SubRaRequestIndex();
        $sub_ra_request_index['task_id'] = $request['us_launch_t_id'];
        $sub_ra_request_index['request_type'] = $request['us_launch_request_type'];
        $sub_ra_request_index['author_id'] = $user->id;
        $sub_ra_request_index['status'] = 'action_requested';
        $sub_ra_request_index->save();

        $ra_request_type_id = $sub_ra_request_index->id;

        $subRaRequestType = new SubRaRequestType();
        $subRaRequestType['id'] = $request['us_launch_t_id'];
        $subRaRequestType['author_id'] = $user->id;
        $subRaRequestType['type'] = 'us_launch';
        $subRaRequestType['ra_request_type_id'] = $ra_request_type_id;

        $subRaRequestType['due_date'] = $request['us_launch_due_date'];
        $subRaRequestType['market'] = $request['us_launch_market'];
        $subRaRequestType['bulk_vendor_code'] = $request['us_launch_bulk_vendor_code'];
        $subRaRequestType['bulk_vendor_name'] = $request['us_launch_bulk_vendor_name'];
        $subRaRequestType['filling_vendor_code'] = $request['us_launch_filling_vendor_code'];
        $subRaRequestType['filling_vendor_name'] = $request['us_launch_filling_vendor_name'];
        $subRaRequestType['packaging_vendor_code'] = $request['us_launch_packaging_vendor_code'];
        $subRaRequestType['packaging_vendor_name'] = $request['us_launch_packaging_vendor_name'];

        $subRaRequestType->save();

        $this->correspondence_add_ra_reqeust_type($ra_request_type_id, 'US Launch', $sub_ra_request_index);

        // add campaign_type_asset_attachments
        if($request->file('us_launch_attachment')){
            foreach ($request->file('us_launch_attachment') as $file) {
                $attachments = new RaRequestTypeAttachments();

//                $fileName = $file->storeAs('campaigns/'.$request['legal_request_c_id'].'/'.$asset_id, $originalName);
                $fileName = $this->file_exist_check_ra($file, $request['us_launch_t_id'], $ra_request_type_id);

                $attachments['task_id'] = $request['us_launch_t_id'];
                $attachments['type'] = 'attachment_file_' . $file->getMimeType();
                $attachments['author_id'] = $user->id;
                $attachments['attachment'] = '/' . $fileName;
                $attachments['ra_request_type_id'] = $ra_request_type_id;
                $attachments['file_ext'] = pathinfo($fileName, PATHINFO_EXTENSION);
                $attachments['file_type'] = $file->getMimeType();
                $attachments['file_size'] = $file->getSize();
                $attachments['created_at'] = Carbon::now();
                $attachments->save();

                $this->add_file_correspondence_for_ra($ra_request_type_id, $subRaRequestType->id, $user, $fileName, 'us_launch');
            }
        }

        $project_id = $this->projectTaskIndexRepository->get_project_id_by_task_id($request['us_launch_t_id']);

        // Send Notification
//        $this->send_notification_action_request($user, $project_id, $subRaRequestType, $ra_request_type_id);

        return redirect('admin/ra_request/'.$project_id.'/edit#'.$ra_request_type_id)
            ->with('success', __('Added the US Launch Type : ' . $ra_request_type_id));
    }

    public function edit_us_launch(Request $request, $ra_request_type_id)
    {
        $param = $request->all();

        // Permission_check
//        if(!$this->permission_check($param)){
//            return redirect('admin/project/' . $concept_development->id . '/edit#'.$task_id)
//                ->with('error', __('This action is no longer permitted. Please contact an Administrator.'));
//        }

        if (isset($param['on_going_registrations'])) {
            $param['on_going_registrations'] = implode(',', $param['on_going_registrations']);
        } else {
            $param['on_going_registrations'] = '';
        }

        $project_id = $this->projectTaskIndexRepository->get_project_id_by_task_id($param['us_launch_t_id']);
        $subRaRequestType = $this->subRaRequestTypeRepository->findById($ra_request_type_id);

        if($this->subRaRequestTypeRepository->update($ra_request_type_id, $param)){

            $user = auth()->user();
            // insert into campaign note for correspondence
            $this->correspondence_update_ra_request_type('us_launch', $param, $subRaRequestType, $user);

            if($request->file('attachment')){
                foreach ($request->file('attachment') as $file) {
                    $attachments = new RaRequestTypeAttachments();

                    $fileName = $this->file_exist_check_ra($file, $subRaRequestType->id, $ra_request_type_id);

                    $attachments['task_id'] = $subRaRequestType->id; // task_id
                    $attachments['type'] = 'attachment_file_' . $file->getMimeType();
                    $attachments['author_id'] = $user->id;
                    $attachments['attachment'] = '/' . $fileName;
                    $attachments['ra_request_type_id'] = $ra_request_type_id;
                    $attachments['file_ext'] = pathinfo($fileName, PATHINFO_EXTENSION);
                    $attachments['file_type'] = $file->getMimeType();
                    $attachments['file_size'] = $file->getSize();
                    $attachments['created_at'] = Carbon::now();
                    $attachments->save();

                    // insert file attachment correspondence
                    $this->add_file_correspondence_for_ra($ra_request_type_id, $subRaRequestType->id, $user, $fileName, 'us_launch');
                }
            }

            return redirect('admin/ra_request/'.$project_id.'/edit#'.$ra_request_type_id)
                ->with('success', __('US Launch ('.$ra_request_type_id.') - Update Success'));
        }

        return redirect('admin/ra_request/'.$project_id.'/edit#'.$ra_request_type_id)
            ->with('error', __('Update Failed'));
    }

    public function add_canada_launch(Request $request){

        $user = auth()->user();

        $sub_ra_request_index = new SubRaRequestIndex();
        $sub_ra_request_index['task_id'] = $request['canada_launch_t_id'];
        $sub_ra_request_index['request_type'] = $request['canada_launch_request_type'];
        $sub_ra_request_index['author_id'] = $user->id;
        $sub_ra_request_index['status'] = 'action_requested';
        $sub_ra_request_index->save();

        $ra_request_type_id = $sub_ra_request_index->id;

        $subRaRequestType = new SubRaRequestType();
        $subRaRequestType['id'] = $request['canada_launch_t_id'];
        $subRaRequestType['author_id'] = $user->id;
        $subRaRequestType['type'] = 'canada_launch';
        $subRaRequestType['ra_request_type_id'] = $ra_request_type_id;
        $subRaRequestType['due_date'] = $request['canada_launch_due_date'];

        $subRaRequestType->save();

        $this->correspondence_add_ra_reqeust_type($ra_request_type_id, 'CANADA Launch', $sub_ra_request_index);

        // add campaign_type_asset_attachments
        if($request->file('canada_launch_attachment')){
            foreach ($request->file('canada_launch_attachment') as $file) {
                $attachments = new RaRequestTypeAttachments();

//                $fileName = $file->storeAs('campaigns/'.$request['legal_request_c_id'].'/'.$asset_id, $originalName);
                $fileName = $this->file_exist_check_ra($file, $request['canada_launch_t_id'], $ra_request_type_id);

                $attachments['task_id'] = $request['canada_launch_t_id'];
                $attachments['type'] = 'attachment_file_' . $file->getMimeType();
                $attachments['author_id'] = $user->id;
                $attachments['attachment'] = '/' . $fileName;
                $attachments['ra_request_type_id'] = $ra_request_type_id;
                $attachments['file_ext'] = pathinfo($fileName, PATHINFO_EXTENSION);
                $attachments['file_type'] = $file->getMimeType();
                $attachments['file_size'] = $file->getSize();
                $attachments['created_at'] = Carbon::now();
                $attachments->save();

                $this->add_file_correspondence_for_ra($ra_request_type_id, $subRaRequestType->id, $user, $fileName, 'canada_launch');
            }
        }

        $project_id = $this->projectTaskIndexRepository->get_project_id_by_task_id($request['canada_launch_t_id']);

        // Send Notification
//        $this->send_notification_action_request($user, $project_id, $subRaRequestType, $ra_request_type_id);

        return redirect('admin/ra_request/'.$project_id.'/edit#'.$ra_request_type_id)
            ->with('success', __('Added the CANADA Launch Type : ' . $ra_request_type_id));
    }

    public function edit_canada_launch(Request $request, $ra_request_type_id)
    {
        $param = $request->all();

        // Permission_check
//        if(!$this->permission_check($param)){
//            return redirect('admin/project/' . $concept_development->id . '/edit#'.$task_id)
//                ->with('error', __('This action is no longer permitted. Please contact an Administrator.'));
//        }

        $project_id = $this->projectTaskIndexRepository->get_project_id_by_task_id($param['canada_launch_t_id']);
        $subRaRequestType = $this->subRaRequestTypeRepository->findById($ra_request_type_id);

        if($this->subRaRequestTypeRepository->update($ra_request_type_id, $param)){

            $user = auth()->user();
            // insert into campaign note for correspondence
            $this->correspondence_update_ra_request_type('canada_launch', $param, $subRaRequestType, $user);

            if($request->file('attachment')){
                foreach ($request->file('attachment') as $file) {
                    $attachments = new RaRequestTypeAttachments();

                    $fileName = $this->file_exist_check_ra($file, $subRaRequestType->id, $ra_request_type_id);

                    $attachments['task_id'] = $subRaRequestType->id; // task_id
                    $attachments['type'] = 'attachment_file_' . $file->getMimeType();
                    $attachments['author_id'] = $user->id;
                    $attachments['attachment'] = '/' . $fileName;
                    $attachments['ra_request_type_id'] = $ra_request_type_id;
                    $attachments['file_ext'] = pathinfo($fileName, PATHINFO_EXTENSION);
                    $attachments['file_type'] = $file->getMimeType();
                    $attachments['file_size'] = $file->getSize();
                    $attachments['created_at'] = Carbon::now();
                    $attachments->save();

                    // insert file attachment correspondence
                    $this->add_file_correspondence_for_ra($ra_request_type_id, $subRaRequestType->id, $user, $fileName, 'canada_launch');
                }
            }

            return redirect('admin/ra_request/'.$project_id.'/edit#'.$ra_request_type_id)
                ->with('success', __('CANADA Launch ('.$ra_request_type_id.') - Update Success'));
        }

        return redirect('admin/ra_request/'.$project_id.'/edit#'.$ra_request_type_id)
            ->with('error', __('Update Failed'));
    }

    public function add_eu_launch(Request $request){

        $user = auth()->user();

        $sub_ra_request_index = new SubRaRequestIndex();
        $sub_ra_request_index['task_id'] = $request['eu_launch_t_id'];
        $sub_ra_request_index['request_type'] = $request['eu_launch_request_type'];
        $sub_ra_request_index['author_id'] = $user->id;
        $sub_ra_request_index['status'] = 'action_requested';
        $sub_ra_request_index->save();

        $ra_request_type_id = $sub_ra_request_index->id;

        $subRaRequestType = new SubRaRequestType();
        $subRaRequestType['id'] = $request['eu_launch_t_id'];
        $subRaRequestType['author_id'] = $user->id;
        $subRaRequestType['type'] = 'eu_launch';
        $subRaRequestType['ra_request_type_id'] = $ra_request_type_id;
        $subRaRequestType['due_date'] = $request['eu_launch_due_date'];

        $subRaRequestType->save();

        $this->correspondence_add_ra_reqeust_type($ra_request_type_id, 'EU Launch', $sub_ra_request_index);

        // add campaign_type_asset_attachments
        if($request->file('eu_launch_attachment')){
            foreach ($request->file('eu_launch_attachment') as $file) {
                $attachments = new RaRequestTypeAttachments();

//                $fileName = $file->storeAs('campaigns/'.$request['legal_request_c_id'].'/'.$asset_id, $originalName);
                $fileName = $this->file_exist_check_ra($file, $request['eu_launch_t_id'], $ra_request_type_id);

                $attachments['task_id'] = $request['eu_launch_t_id'];
                $attachments['type'] = 'attachment_file_' . $file->getMimeType();
                $attachments['author_id'] = $user->id;
                $attachments['attachment'] = '/' . $fileName;
                $attachments['ra_request_type_id'] = $ra_request_type_id;
                $attachments['file_ext'] = pathinfo($fileName, PATHINFO_EXTENSION);
                $attachments['file_type'] = $file->getMimeType();
                $attachments['file_size'] = $file->getSize();
                $attachments['created_at'] = Carbon::now();
                $attachments->save();

                $this->add_file_correspondence_for_ra($ra_request_type_id, $subRaRequestType->id, $user, $fileName, 'eu_launch');
            }
        }

        $project_id = $this->projectTaskIndexRepository->get_project_id_by_task_id($request['eu_launch_t_id']);

        // Send Notification
//        $this->send_notification_action_request($user, $project_id, $subRaRequestType, $ra_request_type_id);

        return redirect('admin/ra_request/'.$project_id.'/edit#'.$ra_request_type_id)
            ->with('success', __('Added the EU Launch Type : ' . $ra_request_type_id));
    }

    public function edit_eu_launch(Request $request, $ra_request_type_id)
    {
        $param = $request->all();

        // Permission_check
//        if(!$this->permission_check($param)){
//            return redirect('admin/project/' . $concept_development->id . '/edit#'.$task_id)
//                ->with('error', __('This action is no longer permitted. Please contact an Administrator.'));
//        }
        if (isset($param['on_going_registrations'])) {
            $param['on_going_registrations'] = implode(',', $param['on_going_registrations']);
        } else {
            $param['on_going_registrations'] = '';
        }


        $project_id = $this->projectTaskIndexRepository->get_project_id_by_task_id($param['eu_launch_t_id']);
        $subRaRequestType = $this->subRaRequestTypeRepository->findById($ra_request_type_id);

        if($this->subRaRequestTypeRepository->update($ra_request_type_id, $param)){

            $user = auth()->user();
            // insert into campaign note for correspondence
            $this->correspondence_update_ra_request_type('eu_launch', $param, $subRaRequestType, $user);

            if($request->file('attachment')){
                foreach ($request->file('attachment') as $file) {
                    $attachments = new RaRequestTypeAttachments();

                    $fileName = $this->file_exist_check_ra($file, $subRaRequestType->id, $ra_request_type_id);

                    $attachments['task_id'] = $subRaRequestType->id; // task_id
                    $attachments['type'] = 'attachment_file_' . $file->getMimeType();
                    $attachments['author_id'] = $user->id;
                    $attachments['attachment'] = '/' . $fileName;
                    $attachments['ra_request_type_id'] = $ra_request_type_id;
                    $attachments['file_ext'] = pathinfo($fileName, PATHINFO_EXTENSION);
                    $attachments['file_type'] = $file->getMimeType();
                    $attachments['file_size'] = $file->getSize();
                    $attachments['created_at'] = Carbon::now();
                    $attachments->save();

                    // insert file attachment correspondence
                    $this->add_file_correspondence_for_ra($ra_request_type_id, $subRaRequestType->id, $user, $fileName, 'eu_launch');
                }
            }

            return redirect('admin/ra_request/'.$project_id.'/edit#'.$ra_request_type_id)
                ->with('success', __('EU Launch ('.$ra_request_type_id.') - Update Success'));
        }

        return redirect('admin/ra_request/'.$project_id.'/edit#'.$ra_request_type_id)
            ->with('error', __('Update Failed'));
    }

    public function add_uk_launch(Request $request){

        $user = auth()->user();

        $sub_ra_request_index = new SubRaRequestIndex();
        $sub_ra_request_index['task_id'] = $request['uk_launch_t_id'];
        $sub_ra_request_index['request_type'] = $request['uk_launch_request_type'];
        $sub_ra_request_index['author_id'] = $user->id;
        $sub_ra_request_index['status'] = 'action_requested';
        $sub_ra_request_index->save();

        $ra_request_type_id = $sub_ra_request_index->id;

        $subRaRequestType = new SubRaRequestType();
        $subRaRequestType['id'] = $request['uk_launch_t_id'];
        $subRaRequestType['author_id'] = $user->id;
        $subRaRequestType['type'] = 'uk_launch';
        $subRaRequestType['ra_request_type_id'] = $ra_request_type_id;
        $subRaRequestType['due_date'] = $request['uk_launch_due_date'];

        $subRaRequestType->save();

        $this->correspondence_add_ra_reqeust_type($ra_request_type_id, 'UK Launch', $sub_ra_request_index);

        // add campaign_type_asset_attachments
        if($request->file('uk_launch_attachment')){
            foreach ($request->file('uk_launch_attachment') as $file) {
                $attachments = new RaRequestTypeAttachments();

//                $fileName = $file->storeAs('campaigns/'.$request['legal_request_c_id'].'/'.$asset_id, $originalName);
                $fileName = $this->file_exist_check_ra($file, $request['uk_launch_t_id'], $ra_request_type_id);

                $attachments['task_id'] = $request['uk_launch_t_id'];
                $attachments['type'] = 'attachment_file_' . $file->getMimeType();
                $attachments['author_id'] = $user->id;
                $attachments['attachment'] = '/' . $fileName;
                $attachments['ra_request_type_id'] = $ra_request_type_id;
                $attachments['file_ext'] = pathinfo($fileName, PATHINFO_EXTENSION);
                $attachments['file_type'] = $file->getMimeType();
                $attachments['file_size'] = $file->getSize();
                $attachments['created_at'] = Carbon::now();
                $attachments->save();

                $this->add_file_correspondence_for_ra($ra_request_type_id, $subRaRequestType->id, $user, $fileName, 'uk_launch');
            }
        }

        $project_id = $this->projectTaskIndexRepository->get_project_id_by_task_id($request['uk_launch_t_id']);

        // Send Notification
//        $this->send_notification_action_request($user, $project_id, $subRaRequestType, $ra_request_type_id);

        return redirect('admin/ra_request/'.$project_id.'/edit#'.$ra_request_type_id)
            ->with('success', __('Added the UK Launch Type : ' . $ra_request_type_id));
    }

    public function edit_uk_launch(Request $request, $ra_request_type_id)
    {
        $param = $request->all();

        // Permission_check
//        if(!$this->permission_check($param)){
//            return redirect('admin/project/' . $concept_development->id . '/edit#'.$task_id)
//                ->with('error', __('This action is no longer permitted. Please contact an Administrator.'));
//        }

        if (isset($param['on_going_registrations'])) {
            $param['on_going_registrations'] = implode(',', $param['on_going_registrations']);
        } else {
            $param['on_going_registrations'] = '';
        }

        $project_id = $this->projectTaskIndexRepository->get_project_id_by_task_id($param['uk_launch_t_id']);
        $subRaRequestType = $this->subRaRequestTypeRepository->findById($ra_request_type_id);

        if($this->subRaRequestTypeRepository->update($ra_request_type_id, $param)){

            $user = auth()->user();
            // insert into campaign note for correspondence
            $this->correspondence_update_ra_request_type('uk_launch', $param, $subRaRequestType, $user);

            if($request->file('attachment')){
                foreach ($request->file('attachment') as $file) {
                    $attachments = new RaRequestTypeAttachments();

                    $fileName = $this->file_exist_check_ra($file, $subRaRequestType->id, $ra_request_type_id);

                    $attachments['task_id'] = $subRaRequestType->id; // task_id
                    $attachments['type'] = 'attachment_file_' . $file->getMimeType();
                    $attachments['author_id'] = $user->id;
                    $attachments['attachment'] = '/' . $fileName;
                    $attachments['ra_request_type_id'] = $ra_request_type_id;
                    $attachments['file_ext'] = pathinfo($fileName, PATHINFO_EXTENSION);
                    $attachments['file_type'] = $file->getMimeType();
                    $attachments['file_size'] = $file->getSize();
                    $attachments['created_at'] = Carbon::now();
                    $attachments->save();

                    // insert file attachment correspondence
                    $this->add_file_correspondence_for_ra($ra_request_type_id, $subRaRequestType->id, $user, $fileName, 'uk_launch');
                }
            }

            return redirect('admin/ra_request/'.$project_id.'/edit#'.$ra_request_type_id)
                ->with('success', __('UK Launch ('.$ra_request_type_id.') - Update Success'));
        }

        return redirect('admin/ra_request/'.$project_id.'/edit#'.$ra_request_type_id)
            ->with('error', __('Update Failed'));
    }

    public function add_latam_launch(Request $request){

        $user = auth()->user();

        $sub_ra_request_index = new SubRaRequestIndex();
        $sub_ra_request_index['task_id'] = $request['latam_launch_t_id'];
        $sub_ra_request_index['request_type'] = $request['latam_launch_request_type'];
        $sub_ra_request_index['author_id'] = $user->id;
        $sub_ra_request_index['status'] = 'action_requested';
        $sub_ra_request_index->save();

        $ra_request_type_id = $sub_ra_request_index->id;

        $subRaRequestType = new SubRaRequestType();
        $subRaRequestType['id'] = $request['latam_launch_t_id'];
        $subRaRequestType['author_id'] = $user->id;
        $subRaRequestType['type'] = 'latam_launch';
        $subRaRequestType['ra_request_type_id'] = $ra_request_type_id;
        $subRaRequestType['due_date'] = $request['latam_launch_due_date'];

        $subRaRequestType->save();

        $this->correspondence_add_ra_reqeust_type($ra_request_type_id, 'LATAM Launch', $sub_ra_request_index);

        // add campaign_type_asset_attachments
        if($request->file('latam_launch_attachment')){
            foreach ($request->file('latam_launch_attachment') as $file) {
                $attachments = new RaRequestTypeAttachments();

//                $fileName = $file->storeAs('campaigns/'.$request['legal_request_c_id'].'/'.$asset_id, $originalName);
                $fileName = $this->file_exist_check_ra($file, $request['latam_launch_t_id'], $ra_request_type_id);

                $attachments['task_id'] = $request['latam_launch_t_id'];
                $attachments['type'] = 'attachment_file_' . $file->getMimeType();
                $attachments['author_id'] = $user->id;
                $attachments['attachment'] = '/' . $fileName;
                $attachments['ra_request_type_id'] = $ra_request_type_id;
                $attachments['file_ext'] = pathinfo($fileName, PATHINFO_EXTENSION);
                $attachments['file_type'] = $file->getMimeType();
                $attachments['file_size'] = $file->getSize();
                $attachments['created_at'] = Carbon::now();
                $attachments->save();

                $this->add_file_correspondence_for_ra($ra_request_type_id, $subRaRequestType->id, $user, $fileName, 'latam_launch');
            }
        }

        $project_id = $this->projectTaskIndexRepository->get_project_id_by_task_id($request['latam_launch_t_id']);

        // Send Notification
//        $this->send_notification_action_request($user, $project_id, $subRaRequestType, $ra_request_type_id);

        return redirect('admin/ra_request/'.$project_id.'/edit#'.$ra_request_type_id)
            ->with('success', __('Added the LATAM Launch Type : ' . $ra_request_type_id));
    }

    public function edit_latam_launch(Request $request, $ra_request_type_id)
    {
        $param = $request->all();

        // Permission_check
//        if(!$this->permission_check($param)){
//            return redirect('admin/project/' . $concept_development->id . '/edit#'.$task_id)
//                ->with('error', __('This action is no longer permitted. Please contact an Administrator.'));
//        }

        $project_id = $this->projectTaskIndexRepository->get_project_id_by_task_id($param['latam_launch_t_id']);
        $subRaRequestType = $this->subRaRequestTypeRepository->findById($ra_request_type_id);

        if($this->subRaRequestTypeRepository->update($ra_request_type_id, $param)){

            $user = auth()->user();
            // insert into campaign note for correspondence
            $this->correspondence_update_ra_request_type('latam_launch', $param, $subRaRequestType, $user);

            if($request->file('attachment')){
                foreach ($request->file('attachment') as $file) {
                    $attachments = new RaRequestTypeAttachments();

                    $fileName = $this->file_exist_check_ra($file, $subRaRequestType->id, $ra_request_type_id);

                    $attachments['task_id'] = $subRaRequestType->id; // task_id
                    $attachments['type'] = 'attachment_file_' . $file->getMimeType();
                    $attachments['author_id'] = $user->id;
                    $attachments['attachment'] = '/' . $fileName;
                    $attachments['ra_request_type_id'] = $ra_request_type_id;
                    $attachments['file_ext'] = pathinfo($fileName, PATHINFO_EXTENSION);
                    $attachments['file_type'] = $file->getMimeType();
                    $attachments['file_size'] = $file->getSize();
                    $attachments['created_at'] = Carbon::now();
                    $attachments->save();

                    // insert file attachment correspondence
                    $this->add_file_correspondence_for_ra($ra_request_type_id, $subRaRequestType->id, $user, $fileName, 'latam_launch');
                }
            }

            return redirect('admin/ra_request/'.$project_id.'/edit#'.$ra_request_type_id)
                ->with('success', __('LATAM Launch ('.$ra_request_type_id.') - Update Success'));
        }

        return redirect('admin/ra_request/'.$project_id.'/edit#'.$ra_request_type_id)
            ->with('error', __('Update Failed'));
    }


    public function file_exist_check_ra($file, $task_id, $ra_request_type_id)
    {
        $originalName = $file->getClientOriginalName();
        $destinationFolder = 'storage/ra_request/'.$task_id.'/'.$ra_request_type_id.'/'.$originalName;

        // If exist same name file, add numberning for version control
        if(file_exists($destinationFolder)){
            if ($pos = strrpos($originalName, '.')) {
                $new_name = substr($originalName, 0, $pos);
                $ext = substr($originalName, $pos);
            }
            $newpath = 'storage/ra_request/'.$task_id.'/'.$ra_request_type_id.'/'.$originalName;
            $uniq_no = 1;
            while (file_exists($newpath)) {
                $tmp_name = $new_name .'_v'. $uniq_no . $ext;
                $newpath = 'storage/ra_request/'.$task_id.'/'.$ra_request_type_id.'/'.$tmp_name;
                $uniq_no++;
            }
            $file_name = $tmp_name;
        }else{
            $file_name = $originalName;
        }

        $fileName =$file->storeAs('ra_request/'.$task_id.'/'.$ra_request_type_id, $file_name);
        return $fileName;
    }

    public function correspondence_add_ra_reqeust_type($ra_request_type_id, $type_name, $sub_ra_request_index)
    {
        $user = auth()->user();
        $type_name = strtoupper($type_name);
        $change_line = "<p>$user->first_name requested <b style='color: #b91d19'>$type_name</b> <b>(#$ra_request_type_id)</b></p>";

        $ra_request_note = new RaRequestNotes();
        $ra_request_note['id'] = $sub_ra_request_index->task_id;
        $ra_request_note['user_id'] = $user->id;
        $ra_request_note['ra_request_type_id'] = $ra_request_type_id;
        $ra_request_note['task_id'] = $sub_ra_request_index->task_id;
        $ra_request_note['project_id'] = 0;
        $ra_request_note['note'] = $change_line;
        $ra_request_note['created_at'] = Carbon::now();
        $ra_request_note->save();
    }

    public function add_file_correspondence_for_ra($ra_request_type_id, $task_id, $user, $file_type, $request_type)
    {
        // Insert into project note for correspondence (attachment file)
        $request_type_ =  strtoupper(str_replace('_', ' ', $request_type));

        $change_line  = "<p>$user->first_name has added a new attachment ($file_type) to <b style='color: #b91d19'>$request_type_</b> <b>(#$ra_request_type_id)</b></p>";

        $ra_request_note = new RaRequestNotes();
        $ra_request_note['id'] = $task_id;
        $ra_request_note['user_id'] = $user->id;
        $ra_request_note['ra_request_type_id'] = $ra_request_type_id;
        $ra_request_note['task_id'] = $task_id;
        $ra_request_note['note'] = $change_line;
        $ra_request_note['created_at'] = Carbon::now();
        $ra_request_note->save();

    }

    public function correspondence_update_ra_request_type($task_type, $new_param, $origin_param, $user)
    {
        // Insert into ra_reqeust_note for correspondence
        $new = $this->get_request_type_param($task_type, $new_param);
        $origin = $origin_param->toArray();
        foreach ($new as $key => $value) {
            if (array_key_exists($key, $origin)) {
                if (html_entity_decode($new[$key]) != html_entity_decode($origin[$key])) {
                    if($key == 'assignee'){

                        if($origin[$key]){
                            $origin_user_obj = $this->userRepository->findById($origin[$key]);
                            $changed[$key]['original'] = $origin_user_obj->first_name. ' ' . $origin_user_obj->last_name;
                        }else{
                            $changed[$key]['original'] = '';
                        }
                        $new_user_obj = $this->userRepository->findById($new[$key]);
                        $changed[$key]['new'] = $new_user_obj->first_name . ' ' . $new_user_obj->last_name;

                    }else {
                        $changed[$key]['new'] = $new[$key];
                        $changed[$key]['original'] = $origin[$key];
                    }
                }
            }
        }
        $task_type_ = strtoupper(str_replace('_', ' ', $task_type));
        $change_line  = "<p>$user->first_name made a change to a <b style='color: #b91d19'>$task_type_</b> <b>(#$origin_param->ra_request_type_id)</b></p>";

        if(!empty($changed)){
            foreach ($changed as $label => $change) {

                $label = strtoupper(str_replace('_', ' ', $label));
                $from  = trim($change['original']); // Remove strip tags
                $to    = trim($change['new']);      // Remove strip tags

                $change_line .= "<div class='change_label'><p>$label:</p></div>"
                    . "<div class='change_to'><p>$to</p></div>"
                    . "<div class='change_from'><del><p>$from</p></del></div>";
            }
            $ra_request_note = new RaRequestNotes();
            $ra_request_note['id'] = $origin_param->id; // task_id
            $ra_request_note['user_id'] = $user->id;
            $ra_request_note['ra_request_type_id'] = $origin_param->ra_request_type_id;
            $ra_request_note['task_id'] = $origin_param->id; // task_id
            $ra_request_note['project_id'] = 0;
            $ra_request_note['note'] = $change_line;
            $ra_request_note['created_at'] = Carbon::now();
            $ra_request_note->save();
        }
    }

    public function get_request_type_param($task_type, $data)
    {
        if($task_type == 'formula_review') {
            $new = array(
                'due_date' => $data['due_date'],
                'vendor_code' => $data['vendor_code'],
                'product_type' => $data['product_type'],
                'product_form' => $data['product_form'],
                'area_of_application' => $data['area_of_application'],
                'fragrance' => $data['fragrance'],
                'compliant_regions' => $data['compliant_regions'],
                'formula' => $data['formula'],
                'ra_remarks' => $data['ra_remarks'],
                'assignee' => $data['assignee'],
            );
            return $new;
        }else if($task_type == 'label_review'){
            $new = array(
                'due_date' => $data['due_date'],
                'formula_review' => $data['formula_review'],
                'formula' => $data['formula'],
                'ra_remarks' => $data['ra_remarks'],
                'assignee' => $data['assignee'],
            );
            return $new;
        }else if($task_type == 'us_launch'){
            $new = array(
                'registration_due_date' => $data['registration_due_date'],
                'market' => $data['market'],
                'bulk_vendor_code' => $data['bulk_vendor_code'],
                'filling_vendor_code' => $data['filling_vendor_code'],
                'packaging_vendor_code' => $data['packaging_vendor_code'],
                'on_going_registrations' => $data['on_going_registrations'],
                'registration_number' => $data['registration_number'],
                'formula' => $data['formula'],
                'ra_remarks' => $data['ra_remarks'],
                'assignee' => $data['assignee'],
            );
            return $new;
        }else if($task_type == 'canada_launch'){
            $new = array(
                'registration_due_date' => $data['registration_due_date'],
                'registration_number' => $data['registration_number'],
                'formula' => $data['formula'],
                'ra_remarks' => $data['ra_remarks'],
                'assignee' => $data['assignee'],
            );
            return $new;
        }else if($task_type == 'eu_launch'){
            $new = array(
                'registration_due_date' => $data['registration_due_date'],
                'on_going_registrations' => $data['on_going_registrations'],
                'cpnp_stage' => $data['cpnp_stage'],
                'registration_number' => $data['registration_number'],
                'formula' => $data['formula'],
                'ra_remarks' => $data['ra_remarks'],
                'assignee' => $data['assignee'],
            );
            return $new;
        }else if($task_type == 'uk_launch'){
            $new = array(
                'registration_due_date' => $data['registration_due_date'],
                'on_going_registrations' => $data['on_going_registrations'],
                'cpnp_stage' => $data['cpnp_stage'],
                'registration_number' => $data['registration_number'],
                'formula' => $data['formula'],
                'ra_remarks' => $data['ra_remarks'],
                'assignee' => $data['assignee'],
            );
            return $new;
        }else if($task_type == 'latam_launch'){
            $new = array(
                'registration_due_date' => $data['registration_due_date'],
                'registration_number' => $data['registration_number'],
                'formula' => $data['formula'],
                'ra_remarks' => $data['ra_remarks'],
                'assignee' => $data['assignee'],
            );
            return $new;
        }

    }

    public function actionInProgress($id)
    {
        $sub_ra_request_index = $this->subRaRequestIndexRepository->findById($id);
        $param['status'] = 'in_progress';
        $param['updated_at'] = Carbon::now();
        $t_id = $sub_ra_request_index->task_id;
        $project_id = $this->projectTaskIndexRepository->get_project_id_by_task_id($t_id);
        if($this->subRaRequestIndexRepository->update($id, $param)){
            $this->ra_status_correspondence($t_id, $project_id, $sub_ra_request_index->request_type, $sub_ra_request_index->id, 'In Progress');
            echo '/admin/ra_request/'.$project_id.'/edit#'.$id;
        }else{
            echo 'fail';
        }
    }

    public function raRevision($id)
    {
        $sub_ra_request_index = $this->subRaRequestIndexRepository->findById($id);
        $param['status'] = 'action_review';
        $param['updated_at'] = Carbon::now();
        $t_id = $sub_ra_request_index->task_id;
        $project_id = $this->projectTaskIndexRepository->get_project_id_by_task_id($t_id);
        if($this->subRaRequestIndexRepository->update($id, $param)){
            $this->ra_status_correspondence($t_id, $project_id, $sub_ra_request_index->request_type, $sub_ra_request_index->id, 'RA Revision');
            echo '/admin/ra_request/'.$project_id.'/edit#'.$id;
        }else{
            echo 'fail';
        }
    }

    public function revision_reason(Request $request)
    {
        $param = $request->all();
        $request_type_id = $param['request_type_id'];
        $revision_reason = $param['revision_reason'];
        $revision_reason_note = $param['revision_reason_note'];
        $sub_ra_request_index = $this->subRaRequestIndexRepository->findById($request_type_id);
        $t_id = $sub_ra_request_index->task_id;
        $project_id = $this->projectTaskIndexRepository->get_project_id_by_task_id($t_id);
        $params['status'] = 'action_review';
        $params['updated_at'] = Carbon::now();
        $params['revision_reason'] = $revision_reason;
        $params['revision_reason_note'] = $revision_reason_note;
        if($this->subRaRequestIndexRepository->update($request_type_id, $params)){

            $user = auth()->user();
            $task_type_ =  strtoupper(str_replace('_', ' ', $sub_ra_request_index->request_type));
            $change_line  = "<p>$user->first_name updated the status to <b>RA Revision</b> for <b style='color: #b91d19;'>$task_type_ </b><b>(#$request_type_id)</b>
                            <br> <b style='color: black;'>Revision Reason : $revision_reason </b>
                            <br> <b style='color: black;'>$revision_reason_note </b>
                            </p>";
            $note = new RaRequestNotes();
            $note['id'] = $t_id;
            $note['user_id'] = $user->id;
            $note['ra_request_type_id'] = $request_type_id;
            $note['task_id'] = $t_id;
            $note['project_id'] = $project_id;
            $note['note'] = $change_line;
            $note['created_at'] = Carbon::now();
            $note->save();

            return redirect('admin/ra_request/'.$project_id.'/edit#'.$request_type_id)
                ->with('success', __('Data has been Updated.'));
        }

        return redirect('admin/ra_request/'.$project_id.'/edit#'.$request_type_id)
            ->with('error', __('Data updates Failed'));
    }

    public function reviewComplete($id)
    {
        $sub_ra_request_index = $this->subRaRequestIndexRepository->findById($id);
        $param['status'] = 'action_completed';
        $param['updated_at'] = Carbon::now();
        $t_id = $sub_ra_request_index->task_id;
        $project_id = $this->projectTaskIndexRepository->get_project_id_by_task_id($t_id);
        if($this->subRaRequestIndexRepository->update($id, $param)){
            $this->ra_status_correspondence($t_id, $project_id, $sub_ra_request_index->request_type, $sub_ra_request_index->id, 'Action Completed');
            echo '/admin/ra_request/'.$project_id.'/edit#'.$id;
        }else{
            echo 'fail';
        }
    }

    public function raResubmit($id)
    {
        $sub_ra_request_index = $this->subRaRequestIndexRepository->findById($id);
        $param['status'] = 'action_requested';
        $param['updated_at'] = Carbon::now();
        $t_id = $sub_ra_request_index->task_id;
        $project_id = $this->projectTaskIndexRepository->get_project_id_by_task_id($t_id);
        if($this->subRaRequestIndexRepository->update($id, $param)){
            $subRaRequest_obj = $this->subRaRequestTypeRepository->get_sub_ra_request_by_ra_request_type_id($id);
            $current_revision_cnt = $subRaRequest_obj['revision_cnt'];
            ////////////// Due Date Revision formula //////////

            $type = $subRaRequest_obj['type'];

            $now = new \DateTime();
            $currentHour = (int)$now->format('H');
            if($type == 'formula_review'){ // 16시 이전이면 5일, 이후면 6일을 더함
                $daysToAdd = ($currentHour < 16) ? 5 : 6;
            }else if($type == 'label_review'){
                $daysToAdd = ($currentHour < 16) ? 5 : 6;
            }else if($type == 'us_launch'){
                $daysToAdd = ($currentHour < 16) ? 5 : 6;
            }else if($type == 'canada_launch'){ // 16시 이전이면 3일, 이후면 4일을 더함
                $daysToAdd = ($currentHour < 16) ? 3 : 4;
            }else if($type == 'eu_launch'){ // 16시 이전이면 30일, 이후면 31일을 더함
                $daysToAdd = ($currentHour < 16) ? 30 : 31;
            }else if($type == 'uk_launch'){
                $daysToAdd = ($currentHour < 16) ? 30 : 31;
            }else if($type == 'latam_launch'){
                $daysToAdd = ($currentHour < 16) ? 5 : 6;
            }

            // 현재 날짜와 시간에서 필요한 날짜만큼 더함
            while ($daysToAdd > 0) {
                $now->modify('+1 day');
                // 요일을 숫자로 가져오기 (1 = 월요일, 7 = 일요일)
                $dayOfWeek = (int)$now->format('N');
                // 주말 제외 (월-금만 카운트)
                if ($dayOfWeek < 6) {
                    $daysToAdd--;
                }
            }
            $due_date_revision = $now->format('Y-m-d');
            ///////////////
            $t_param['due_date_revision'] = $due_date_revision;
            $t_param['revision_cnt'] = $current_revision_cnt + 1;
            $t_param['updated_at'] = Carbon::now();
            $this->subRaRequestTypeRepository->update($id, $t_param);
            $this->ra_status_correspondence($t_id, $project_id, $sub_ra_request_index->request_type, $sub_ra_request_index->id, 'RA Resubmit');
            echo '/admin/ra_request/'.$project_id.'/edit#'.$id;
        }else{
            echo 'fail';
        }
    }

//    public function actionReSubmit($id)
//    {
//        $sub_legal_request_index = $this->subLegalRequestIndexRepository->findById($id);
//        $param['status'] = 'action_requested';
//        $param['updated_at'] = Carbon::now();
//        $t_id = $sub_legal_request_index->task_id;
//        $project_id = $this->projectTaskIndexRepository->get_project_id_by_task_id($t_id);
//        if($this->subLegalRequestIndexRepository->update($id, $param)){
//            $subLegalRequest_obj = $this->subLegalRequestTypeRepository->get_sub_legal_request_by_legal_request_type_id($id);
//            $current_revision_cnt = $subLegalRequest_obj['revision_cnt'];
//            ////////////// Due Date Revision formula //////////
//            $now = new \DateTime();
//            $currentHour = (int)$now->format('H');
//            // 16시 이전이면 2일, 이후면 3일을 더함
//            $daysToAdd = ($currentHour < 16) ? 2 : 3;
//            // 현재 날짜와 시간에서 필요한 날짜만큼 더함
//            while ($daysToAdd > 0) {
//                $now->modify('+1 day');
//                // 요일을 숫자로 가져오기 (1 = 월요일, 7 = 일요일)
//                $dayOfWeek = (int)$now->format('N');
//                // 주말 제외 (월-금만 카운트)
//                if ($dayOfWeek < 6) {
//                    $daysToAdd--;
//                }
//            }
//            $due_date_revision = $now->format('Y-m-d');
//            ///////////////
//            $t_param['due_date_revision'] = $due_date_revision;
//            $t_param['revision_cnt'] = $current_revision_cnt + 1;
//            $t_param['updated_at'] = Carbon::now();
//            $this->subLegalRequestTypeRepository->update($id, $t_param);
//            $this->legal_status_correspondence($t_id, $project_id, $sub_legal_request_index->request_type, $id, 'Action Requested (Revision)');
//            echo '/admin/legal_request/'.$project_id.'/edit#'.$id;
//        }else{
//            echo 'fail';
//        }
//    }

    public function raComplete($id)
    {
        $sub_ra_request_index = $this->subRaRequestIndexRepository->findById($id);
        $param['status'] = 'action_completed';
        $param['updated_at'] = Carbon::now();
        $t_id = $sub_ra_request_index->task_id;
        $project_id = $this->projectTaskIndexRepository->get_project_id_by_task_id($t_id);
        if($this->subRaRequestIndexRepository->update($id, $param)){
            $this->ra_status_correspondence($t_id, $project_id, $sub_ra_request_index->request_type, $sub_ra_request_index->id, 'Action Review');
            echo '/admin/ra_request/'.$project_id.'/edit#'.$id;
        }else{
            echo 'fail';
        }
    }

    public function actionReview($id)
    {
        $sub_ra_request_index = $this->subRaRequestIndexRepository->findById($id);
        $param['status'] = 'action_review';
        $param['updated_at'] = Carbon::now();
        $t_id = $sub_ra_request_index->task_id;
        $project_id = $this->projectTaskIndexRepository->get_project_id_by_task_id($t_id);
        if($this->subRaRequestIndexRepository->update($id, $param)){
            $this->ra_status_correspondence($t_id, $project_id, $sub_ra_request_index->request_type, $sub_ra_request_index->id, 'Action Review');
            echo '/admin/ra_request/'.$project_id.'/edit#'.$id;
        }else{
            echo 'fail';
        }
    }

    public function actionComplete($id)
    {
        $sub_ra_request_index = $this->subRaRequestIndexRepository->findById($id);
        $param['status'] = 'action_completed';
        $param['updated_at'] = Carbon::now();
        $t_id = $sub_ra_request_index->task_id;
        $project_id = $this->projectTaskIndexRepository->get_project_id_by_task_id($t_id);
        if($this->subRaRequestIndexRepository->update($id, $param)){
            $this->ra_status_correspondence($t_id, $project_id, $sub_ra_request_index->request_type, $sub_ra_request_index->id, 'Action Completed');
            echo '/admin/ra_request/'.$project_id.'/edit#'.$id;
        }else{
            echo 'fail';
        }
    }

    public function fileRemove($id)
    {
        $attachment_obj = $this->raRequestTypeFileAttachmentsRepository->findById($id);
        $file_name = $attachment_obj->attachment;
        $task_id = $attachment_obj->task_id;
        $ra_request_type_id = $attachment_obj->ra_request_type_id;
        $user = auth()->user();
        if($attachment_obj->delete()){
            $requestTypeIndex = $this->subRaRequestIndexRepository->findById($ra_request_type_id);
            $request_type =  ucwords(str_replace('_', ' ', $requestTypeIndex->request_type));
            $change_line = "<p>$user->first_name removed a attachment ($file_name) on <b style='color: #b91d19'>$request_type</b> <b>(#$ra_request_type_id)</b></p>";

            $ra_request_note = new RaRequestNotes();
            $ra_request_note['id'] = $task_id; // task_id
            $ra_request_note['user_id'] = $user->id;
            $ra_request_note['ra_request_type_id'] = $ra_request_type_id;
            $ra_request_note['task_id'] = $task_id; // task_id
            $ra_request_note['note'] = $change_line;
            $ra_request_note['created_at'] = Carbon::now();
            $ra_request_note->save();

            echo 'success';
        }else{
            echo 'fail';
        }
    }

    public function add_note(Request $request)
    {
        $param = $request->all();
        $user = auth()->user();

        $p_id = $param['p_id'];
        $t_id = $param['t_id'];
        $p_title = $param['p_title'];
        $email_list = $param['email_list'];

        $note = new RaRequestNotes();
        $note['id'] = $t_id;
        $note['user_id'] = $user->id;
        $note['ra_request_type_id'] = 0;
        $note['project_id'] = $p_id;
        $note['task_id'] = $t_id;
        $note['note'] = $param['create_note'];
        $note->save();

        if($email_list) {
            $details = [
                'mail_subject' => 'New Message',
                'template' => 'emails.task.message',
                'receiver' => 'You got a new message from ' . $user->first_name . ' ', $user->last_name . ',',
                'title' => $p_title,
                'message' => $param['create_note'],
                'url' => '/admin/ra_request/' . $p_id . '/edit#' . $t_id,
            ];
            $receiver_list = explode(',', $email_list);
            Mail::to($receiver_list)->send(new TaskStatusNotification($details));
        }

        return redirect('admin/ra_request/'.$p_id.'/edit')
            ->with('success', __('Data has been Updated.'));
    }

    public function requestTypeRemovePermissionCheck($request_type_id){

        $user = auth()->user();
        if($user->role == 'Admin') return true; // admin okay

        $obj = $this->subRaRequestIndexRepository->findById($request_type_id);
        if($obj->author_id != $user->id){
            return false;
        }
        return true;
    }

    public function requestTypeRemove($request_type_id, $type)
    {
        $obj = $this->subRaRequestIndexRepository->findById($request_type_id);
        $t_id = $obj->task_id;
        $p_id = $this->projectTaskIndexRepository->get_project_id_by_task_id($t_id);
        if($this->requestTypeRemovePermissionCheck($request_type_id)){
            // Delete from sub_ra_request_index, sub_ra_request_type tables
            $this->subRaRequestIndexRepository->delete($request_type_id);
            $this->subRaRequestTypeRepository->delete($request_type_id);

            // Add correspondence for asset Removed
            $this->ra_correspondence($t_id, $p_id, $type, $request_type_id, 'Removed the Task ');

            echo '/admin/ra_request/'.$p_id.'/edit';
        }else{
            echo 'fail';
        }
    }

    public function ra_status_correspondence($t_id, $p_id, $task_type, $ra_request_type_id, $status)
    {
        // Insert into Project note for correspondence (attachment file)
        $user = auth()->user();
        $task_type_ =  strtoupper(str_replace('_', ' ', $task_type));
        $change_line  = "<p>$user->first_name updated the status to <b>$status</b> for <b style='color: #b91d19;'>$task_type_ </b><b>(#$ra_request_type_id)</b></p>";

        $note = new RaRequestNotes();
        $note['id'] = $t_id;
        $note['user_id'] = $user->id;
        $note['ra_request_type_id'] = $ra_request_type_id;
        $note['task_id'] = $t_id;
        $note['project_id'] = $p_id;
        $note['note'] = $change_line;
        $note['created_at'] = Carbon::now();
        $note->save();
    }

    public function ra_correspondence($t_id, $p_id, $task_type, $ra_request_type_id, $status)
    {
        // Insert into Project note for correspondence (attachment file)
        $user = auth()->user();
        $task_type_ =  strtoupper(str_replace('_', ' ', $task_type));
        $change_line  = "<p>$user->first_name $status for <b style='color: #b91d19;'>$task_type_ </b><b>(#$ra_request_type_id)</b></p>";

        $note = new RaRequestNotes();
        $note['id'] = $t_id;
        $note['user_id'] = $user->id;
        $note['ra_request_type_id'] = $ra_request_type_id;
        $note['task_id'] = $t_id;
        $note['project_id'] = $p_id;
        $note['note'] = $change_line;
        $note['created_at'] = Carbon::now();
        $note->save();
    }

}
