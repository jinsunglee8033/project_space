<?php

namespace App\Http\Controllers\Admin;

use App\Authorizable;
use App\Http\Controllers\Controller;
use App\Http\Controllers\NotifyController;
use App\Http\Requests\Admin\CampaignRequest;
use App\Http\Requests\Admin\ProjectRequest;
use App\Http\Requests\Admin\TaskConceptDevelopmentRequest;
use App\Http\Requests\Admin\TaskLegalRequestRequest;
use App\Http\Requests\Admin\UserRequest;
use App\Mail\MyDemoMail;
use App\Mail\NewProject;
use App\Mail\NewRequest;
use App\Mail\NoteProject;
use App\Mail\Revision;
use App\Mail\SendMail;
use App\Mail\TaskStatusNotification;
use App\Models\AssetOwnerAssets;
use App\Models\CampaignAssetIndex;
use App\Models\CampaignNotes;

use App\Models\LegalRequestNotes;
use App\Models\LegalRequestTypeAttachments;
use App\Models\ProjectNotes;
use App\Models\ProjectTaskIndex;
use App\Models\ProjectTypeTaskAttachments;
use App\Models\QraRequestNotes;
use App\Models\QraRequestTypeAttachments;
use App\Models\SubLegalRequestIndex;
use App\Models\SubLegalRequestType;
use App\Models\SubQraRequestIndex;
use App\Models\SubQraRequestType;
use App\Models\TaskTypeConceptDevelopment;
use App\Models\TaskTypeLegalRequest;
use App\Models\TaskTypeProductBrief;
use App\Models\User;


use App\Repositories\Admin\BrandRepository;
use App\Repositories\Admin\ProjectNotesRepository;
use App\Repositories\Admin\QraRequestNotesRepository;
use App\Repositories\Admin\LegalRequestNotesRepository;
use App\Repositories\Admin\QraRequestRepository;
use App\Repositories\Admin\LegalRequestRepository;
use App\Repositories\Admin\QraRequestTypeFileAttachmentsRepository;
use App\Repositories\Admin\LegalRequestTypeFileAttachmentsRepository;
use App\Repositories\Admin\SubLegalRequestIndexRepository;
use App\Repositories\Admin\SubLegalRequestTypeRepository;
use App\Repositories\Admin\SubQraRequestIndexRepository;
use App\Repositories\Admin\SubQraRequestTypeRepository;
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

class LegalRequestController extends Controller
{
    Private $projectRepository;
    Private $qraRequestRepository;
    Private $legalRequestRepository;
    Private $subQraRequestTypeRepository;
    Private $subLegalRequestTypeRepository;
    Private $subQraRequestIndexRepository;
    Private $subLegalRequestIndexRepository;
    Private $projectTaskIndexRepository;
    Private $taskTypeConceptDevelopmentRepository;
    Private $taskTypeLegalRequestRepository;
    Private $taskTypeProductBriefRepository;
    private $projectTaskFileAttachmentsRepository;
    private $qraRequestTypeFileAttachmentsRepository;
    private $legalRequestTypeFileAttachmentsRepository;
    Private $projectNotesRepository;
    Private $qraRequestNotesRepository;
    Private $legalRequestNotesRepository;
    Private $brandRepository;
    Private $teamRepository;
    private $userRepository;

    public function __construct(ProjectRepository $projectRepository,
                                QraRequestRepository $qraRequestRepository,
                                LegalRequestRepository $legalRequestRepository,
                                SubQraRequestTypeRepository $subQraRequestTypeRepository,
                                SubLegalRequestTypeRepository $subLegalRequestTypeRepository,
                                SubQraRequestIndexRepository $subQraRequestIndexRepository,
                                SubLegalRequestIndexRepository $subLegalRequestIndexRepository,
                                ProjectTaskIndexRepository $projectTaskIndexRepository,
                                TaskTypeConceptDevelopmentRepository $taskTypeConceptDevelopmentRepository,
                                TaskTypeLegalRequestRepository $taskTypeLegalRequestRepository,
                                TaskTypeProductBriefRepository $taskTypeProductBriefRepository,
                                ProjectTaskFileAttachmentsRepository $projectTaskFileAttachmentsRepository,
                                QraRequestTypeFileAttachmentsRepository $qraRequestTypeFileAttachmentsRepository,
                                LegalRequestTypeFileAttachmentsRepository $legalRequestTypeFileAttachmentsRepository,
                                ProjectNotesRepository $projectNotesRepository,
                                QraRequestNotesRepository $qraRequestNotesRepository,
                                LegalRequestNotesRepository $legalRequestNotesRepository,
                                BrandRepository $brandRepository,
                                TeamRepository $teamRepository,
                                UserRepository $userRepository)
    {
        parent::__construct();

        $this->projectRepository = $projectRepository;
        $this->qraRequestRepository = $qraRequestRepository;
        $this->legalRequestRepository = $legalRequestRepository;
        $this->subQraRequestTypeRepository = $subQraRequestTypeRepository;
        $this->subLegalRequestTypeRepository = $subLegalRequestTypeRepository;
        $this->subQraRequestIndexRepository = $subQraRequestIndexRepository;
        $this->subLegalRequestIndexRepository = $subLegalRequestIndexRepository;
        $this->projectTaskIndexRepository = $projectTaskIndexRepository;
        $this->taskTypeConceptDevelopmentRepository = $taskTypeConceptDevelopmentRepository;
        $this->taskTypeLegalRequestRepository = $taskTypeLegalRequestRepository;
        $this->taskTypeProductBriefRepository = $taskTypeProductBriefRepository;
        $this->projectTaskFileAttachmentsRepository = $projectTaskFileAttachmentsRepository;
        $this->qraRequestTypeFileAttachmentsRepository = $qraRequestTypeFileAttachmentsRepository;
        $this->legalRequestTypeFileAttachmentsRepository = $legalRequestTypeFileAttachmentsRepository;
        $this->projectNotesRepository = $projectNotesRepository;
        $this->qraRequestNotesRepository = $qraRequestNotesRepository;
        $this->legalRequestNotesRepository = $legalRequestNotesRepository;
        $this->brandRepository = $brandRepository;
        $this->teamRepository = $teamRepository;
        $this->userRepository = $userRepository;
    }

    public function index(Request $request)
    {
        $params = $request->all();
        $params['status'] = 'active';
        $this->data['currentAdminMenu'] = 'legal_request';

        $user = auth()->user();

        if($user->team == 'Legal' || $user->team == 'Admin') {
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
        $this->data['projects'] = $this->legalRequestRepository->findAll($options);
        $this->data['id'] = !empty($params['id']) ? $params['id'] : '';

        return view('admin.legal_request.index', $this->data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // Access check (Legal)
        $user = auth()->user();
        if ( !(in_array($user->team, ['Legal', 'Admin']) || in_array($user->role, ['Project Manager', 'Team Lead'])) ) {
            return view('admin.security.permission', $this->data);
        }

        $this->data['currentAdminMenu'] = 'legal_board';
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
        $task_id = $this->legalRequestRepository->get_task_id_for_legal($id);
        $this->data['task_id']= $task_id;

        // Request Type list
        $this->data['request_type_list'] = $request_type_list = $this->legalRequestRepository->get_request_type_list_by_task_id($task_id);

        // task_detail
        if(sizeof($request_type_list)>0){
            foreach ($request_type_list as $k => $request_type){
                $legal_request_type_id = $request_type->legal_request_type_id;
                $task_files = $this->legalRequestTypeFileAttachmentsRepository->findAllByRequestTypeId($legal_request_type_id);
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

        $correspondences = $this->legalRequestNotesRepository->findAll($options);
        $this->data['correspondences'] = $correspondences;

        $this->data['legal_assignee_list'] = $this->userRepository->getLegalAssigneeList();

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


        /////////////////////////////////////////////////////////////////////////

        /////////// Legal Request Task ////////////////////////////////////////////

        $this->data['trademark_owner_list'] = [
            'Kiss Nail Products, Inc.',
            'Ivy Enterprises, Inc.',
            'Red Beauty, Inc.',
            'AST Systems, LLC',
            'Dae Do, Inc. (Vivace)'
        ];
        $this->data['claim_types'] = [
            'Package','Website','Display Artwork', 'Others'
        ];
        $this->data['contract_categories'] = [
            'NDA','Vendor Contract','Influencer Contract', 'Lab Contract', 'Others'
        ];
        $this->data['claim_categories'] = [
            'Package','Website','Display Artwork', 'Others'
        ];
        $this->data['priorities'] = [
            'Normal', 'Urgent'
        ];
        $this->data['target_regions'] = [
            'U.S.','Canada','EU','UK','Brazil','Mexico','China','Japan','Korea','Others'
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

        $this->data['request_type'] = null;

        /////////////////////////////////////////////////////////////////////////

        return view('admin.legal_request.form', $this->data);
    }

    public function board(Request $request)
    {
        $param = $request->all();
        $this->data['currentAdminMenu'] = 'legal_board';

        $user = auth()->user();

        if($user->team == 'Legal' || $user->team == 'Admin') {
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

        $this->data['task_list_action_requested'] = $this->subLegalRequestTypeRepository->get_action_requested_list($cur_user, $request_type, $assignee);
        $this->data['task_list_in_progress'] = $this->subLegalRequestTypeRepository->get_in_progress_list($cur_user, $request_type, $assignee);
        $this->data['task_list_action_review'] = $this->subLegalRequestTypeRepository->get_action_review_list($cur_user, $request_type, $assignee);
        $this->data['task_list_action_completed'] = $this->subLegalRequestTypeRepository->get_action_completed_list($cur_user, $request_type, $assignee);

        $this->data['request_type'] = $request_type;
        $this->data['request_type_list'] = [
            'trademark',
            'patent',
            'claim',
            'contract'
        ];

        $this->data['assignee'] = $assignee;
        $this->data['assignee_list'] = $this->userRepository->getLegalAssigneeList();

        return view('admin.legal_request.board', $this->data);

    }

    public function registration_list(Request $request)
    {
        $param = $request->all();
        $this->data['currentAdminMenu'] = 'legal_registration_list';

        $user = auth()->user();
        if($user->team == 'Legal' || $user->team == 'Admin') {
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

        $this->data['task_list'] = $this->taskTypeLegalRequestRepository->get_task_list($cur_user, $assignee, $team, $status);

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
        $this->data['legal_request_assignee_list'] = $this->userRepository->getLegalAssigneeList();
        $this->data['teams'] =$this->teamRepository->findAll($team_options);
        $this->data['brands'] =$this->brandRepository->findAll($brand_options);

        return view('admin.legal_request.registration_list', $this->data);
    }

    public function add_contract(Request $request){

        $user = auth()->user();

        $sub_legal_request_index = new SubLegalRequestIndex();
        $sub_legal_request_index['task_id'] = $request['contract_t_id'];
        $sub_legal_request_index['request_type'] = $request['contract_request_type'];
        $sub_legal_request_index['author_id'] = $user->id;
        $sub_legal_request_index['status'] = 'action_requested';
        $sub_legal_request_index->save();

        $legal_request_type_id = $sub_legal_request_index->id;

        $subLegalRequestType = new SubLegalRequestType();
        $subLegalRequestType['id'] = $request['contract_t_id'];
        $subLegalRequestType['author_id'] = $user->id;
        $subLegalRequestType['type'] = 'contract';
        $subLegalRequestType['legal_request_type_id'] = $legal_request_type_id;
        $subLegalRequestType['request_category'] = $request['contract_request_category'];
        if(isset($request['contract_request_category']) && $request['contract_request_category'] == 'Others'){
            $subLegalRequestType['if_other_request_category'] = $request['contract_if_other_request_category'];
        }else{
            $subLegalRequestType['if_other_request_category'] = null;
        }
        $subLegalRequestType['request_description'] = $request['contract_request_description'];
        $subLegalRequestType['priority'] = $request['contract_priority'];
        if(isset($request['contract_priority']) && ($request['contract_priority'] == 'Normal')){
            $subLegalRequestType['due_date_urgent'] = null;
            $subLegalRequestType['urgent_reason'] = null;
        }else{
            $subLegalRequestType['due_date_urgent'] = $request['contract_due_date_urgent'];
            $subLegalRequestType['urgent_reason'] = $request['contract_urgent_reason'];
        }
        $subLegalRequestType['vendor_code'] = $request['contract_vendor_code'];
        $subLegalRequestType['vendor_name'] = $request['contract_vendor_name'];
        $subLegalRequestType['vendor_location'] = $request['contract_vendor_location'];
        $subLegalRequestType['due_date'] = $request['contract_due_date'];
        $subLegalRequestType['target_region'] = $request['contract_target_region'];
        if (isset($request['contract_target_region'])) {
            $subLegalRequestType['target_region'] = implode(',', $request['contract_target_region']);
        } else {
            $subLegalRequestType['target_region'] = '';
        }
        $subLegalRequestType['if_selected_others'] = $request['contract_if_selected_others'];

        $subLegalRequestType->save();

        $this->correspondence_add_legal_request_type($legal_request_type_id, 'Contract', $sub_legal_request_index);

        // add campaign_type_asset_attachments
        if($request->file('contract_attachment')){
            foreach ($request->file('contract_attachment') as $file) {
                $attachments = new LegalRequestTypeAttachments();

//                $fileName = $file->storeAs('campaigns/'.$request['legal_request_c_id'].'/'.$asset_id, $originalName);
                $fileName = $this->file_exist_check_legal($file, $request['contract_t_id'], $legal_request_type_id);

                $attachments['task_id'] = $request['contract_t_id'];
                $attachments['type'] = 'attachment_file_' . $file->getMimeType();
                $attachments['author_id'] = $user->id;
                $attachments['attachment'] = '/' . $fileName;
                $attachments['legal_request_type_id'] = $legal_request_type_id;
                $attachments['file_ext'] = pathinfo($fileName, PATHINFO_EXTENSION);
                $attachments['file_type'] = $file->getMimeType();
                $attachments['file_size'] = $file->getSize();
                $attachments['created_at'] = Carbon::now();
                $attachments->save();

                $this->add_file_correspondence_for_legal($legal_request_type_id, $subLegalRequestType->id, $user, $fileName, 'contract');
            }
        }

        $project_id = $this->projectTaskIndexRepository->get_project_id_by_task_id($request['contract_t_id']);

        // Send Notification
        $this->send_notification_action_request($project_id, $subLegalRequestType, $legal_request_type_id);

        return redirect('admin/legal_request/'.$project_id.'/edit#'.$legal_request_type_id)
            ->with('success', __('Added the Contract Type : ' . $legal_request_type_id));
    }

    public function send_notification_action_request($project_id, $subRequestType, $mm_request_type_id)
    {
        // From : Division
        // Receiver : Legal Team
        $project_obj = $this->projectRepository->findById($project_id);
        if($subRequestType['priority'] == 'Normal'){
            $due_date_mail = $subRequestType['due_date'];
        }else{
            $due_date_mail = $subRequestType['due_date_urgent'];
        }

        // Task Creator
        $receiver_author_name = $subRequestType->author_obj->first_name . ' ' . $subRequestType->author_obj->last_name;

        $details = [
            'template'          => 'emails.task.new_request',
            'mail_subject'      => 'Action Requested : Legal Request',
            'receiver'          => "Hello Legal Team,",
            'message'           => 'You got a new request from ' . $receiver_author_name . ".",
            'title'             => "Action Requested : Legal Request",
            'project_id'        => $project_id,
            'project_title'     => $project_obj->name,
            'request_id'        => $mm_request_type_id,
            'request_type'      => $subRequestType['type'],
            'priority'          => $subRequestType['priority'],
            'due_date'          => $due_date_mail,
            'url'               => '/admin/legal_request/'.$project_id.'/edit#'.$mm_request_type_id,
        ];

        $group_rs = $this->userRepository->get_receiver_emails_by_team('Legal');
        foreach ($group_rs as $team_user) {
            $receiver_list[] = $team_user['email'];
        }

        /// Send to receivers
        Mail::to($receiver_list)->send(new TaskStatusNotification($details));

    }

    public function edit_contract(Request $request, $legal_request_type_id)
    {
        $param = $request->all();

        if (isset($param['target_region'])) {
            $param['target_region'] = implode(',', $param['target_region']);
        } else {
            $param['target_region'] = '';
        }

        // Permission_check
//        if(!$this->permission_check($param)){
//            return redirect('admin/project/' . $concept_development->id . '/edit#'.$task_id)
//                ->with('error', __('This action is no longer permitted. Please contact an Administrator.'));
//        }

        $project_id = $this->projectTaskIndexRepository->get_project_id_by_task_id($param['contract_t_id']);
        $subLegalRequestType = $this->subLegalRequestTypeRepository->findById($legal_request_type_id);

        if($this->subLegalRequestTypeRepository->update($legal_request_type_id, $param)){

            $user = auth()->user();
            // insert into campaign note for correspondence
            $this->correspondence_update_legal_request_type('contract', $param, $subLegalRequestType, $user);

            if($request->file('attachment')){
                foreach ($request->file('attachment') as $file) {
                    $attachments = new legalRequestTypeAttachments();

                    $fileName = $this->file_exist_check_legal($file, $subLegalRequestType->id, $legal_request_type_id);

                    $attachments['task_id'] = $subLegalRequestType->id; // task_id
                    $attachments['type'] = 'attachment_file_' . $file->getMimeType();
                    $attachments['author_id'] = $user->id;
                    $attachments['attachment'] = '/' . $fileName;
                    $attachments['legal_request_type_id'] = $legal_request_type_id;
                    $attachments['file_ext'] = pathinfo($fileName, PATHINFO_EXTENSION);
                    $attachments['file_type'] = $file->getMimeType();
                    $attachments['file_size'] = $file->getSize();
                    $attachments['created_at'] = Carbon::now();
                    $attachments->save();

                    // insert file attachment correspondence
                    $this->add_file_correspondence_for_legal($legal_request_type_id, $subLegalRequestType->id, $user, $fileName, 'contract');
                }
            }

            return redirect('admin/legal_request/'.$project_id.'/edit#'.$legal_request_type_id)
                ->with('success', __('Contract ('.$legal_request_type_id.') - Update Success'));
        }

        return redirect('admin/legal_request/'.$project_id.'/edit#'.$legal_request_type_id)
            ->with('error', __('Update Failed'));
    }

    public function add_claim(Request $request){

        $user = auth()->user();

        $sub_legal_request_index = new SubLegalRequestIndex();
        $sub_legal_request_index['task_id'] = $request['claim_t_id'];
        $sub_legal_request_index['request_type'] = $request['claim_request_type'];
        $sub_legal_request_index['author_id'] = $user->id;
        $sub_legal_request_index['status'] = 'action_requested';
        $sub_legal_request_index->save();

        $legal_request_type_id = $sub_legal_request_index->id;

        $subLegalRequestType = new SubLegalRequestType();
        $subLegalRequestType['id'] = $request['claim_t_id'];
        $subLegalRequestType['author_id'] = $user->id;
        $subLegalRequestType['type'] = 'claim';
        $subLegalRequestType['legal_request_type_id'] = $legal_request_type_id;
        $subLegalRequestType['request_category'] = $request['claim_request_category'];
        if(isset($request['claim_request_category']) && $request['claim_request_category'] == 'Others'){
            $subLegalRequestType['if_other_request_category'] = $request['claim_if_other_request_category'];
        }else{
            $subLegalRequestType['if_other_request_category'] = null;
        }
        $subLegalRequestType['request_description'] = $request['claim_request_description'];

        $subLegalRequestType['priority'] = $request['claim_priority'];
        if(isset($request['claim_priority']) && ($request['claim_priority'] == 'Normal')){
            $subLegalRequestType['due_date_urgent'] = null;
            $subLegalRequestType['urgent_reason'] = null;
        }else{
            $subLegalRequestType['due_date_urgent'] = $request['claim_due_date_urgent'];
            $subLegalRequestType['urgent_reason'] = $request['claim_urgent_reason'];
        }

        $subLegalRequestType['due_date'] = $request['claim_due_date'];
        $subLegalRequestType['target_region'] = $request['claim_target_region'];
        if (isset($request['claim_target_region'])) {
            $subLegalRequestType['target_region'] = implode(',', $request['claim_target_region']);
        } else {
            $subLegalRequestType['target_region'] = '';
        }
        $subLegalRequestType['if_selected_others'] = $request['claim_if_selected_others'];

        $subLegalRequestType->save();

        $this->correspondence_add_legal_request_type($legal_request_type_id, 'claim', $sub_legal_request_index);

        // add campaign_type_asset_attachments
        if($request->file('claim_attachment')){
            foreach ($request->file('claim_attachment') as $file) {
                $attachments = new LegalRequestTypeAttachments();

//                $fileName = $file->storeAs('campaigns/'.$request['legal_request_c_id'].'/'.$asset_id, $originalName);
                $fileName = $this->file_exist_check_legal($file, $request['claim_t_id'], $legal_request_type_id);

                $attachments['task_id'] = $request['claim_t_id'];
                $attachments['type'] = 'attachment_file_' . $file->getMimeType();
                $attachments['author_id'] = $user->id;
                $attachments['attachment'] = '/' . $fileName;
                $attachments['legal_request_type_id'] = $legal_request_type_id;
                $attachments['file_ext'] = pathinfo($fileName, PATHINFO_EXTENSION);
                $attachments['file_type'] = $file->getMimeType();
                $attachments['file_size'] = $file->getSize();
                $attachments['created_at'] = Carbon::now();
                $attachments->save();

                $this->add_file_correspondence_for_legal($legal_request_type_id, $subLegalRequestType->id, $user, $fileName, 'claim');
            }
        }

        $project_id = $this->projectTaskIndexRepository->get_project_id_by_task_id($request['claim_t_id']);

        // Send Notification
        $this->send_notification_action_request($project_id, $subLegalRequestType, $legal_request_type_id);

        return redirect('admin/legal_request/'.$project_id.'/edit#'.$legal_request_type_id)
            ->with('success', __('Added the Claim Type : ' . $legal_request_type_id));
    }

    public function edit_claim(Request $request, $legal_request_type_id)
    {
        $param = $request->all();

        if (isset($param['target_region'])) {
            $param['target_region'] = implode(',', $param['target_region']);
        } else {
            $param['target_region'] = '';
        }

        // Permission_check
//        if(!$this->permission_check($param)){
//            return redirect('admin/project/' . $concept_development->id . '/edit#'.$task_id)
//                ->with('error', __('This action is no longer permitted. Please contact an Administrator.'));
//        }

        $project_id = $this->projectTaskIndexRepository->get_project_id_by_task_id($param['claim_t_id']);
        $subLegalRequestType = $this->subLegalRequestTypeRepository->findById($legal_request_type_id);

        if($this->subLegalRequestTypeRepository->update($legal_request_type_id, $param)){

            $user = auth()->user();
            // insert into campaign note for correspondence
            $this->correspondence_update_legal_request_type('claim', $param, $subLegalRequestType, $user);

            if($request->file('attachment')){
                foreach ($request->file('attachment') as $file) {
                    $attachments = new legalRequestTypeAttachments();

                    $fileName = $this->file_exist_check_legal($file, $subLegalRequestType->id, $legal_request_type_id);

                    $attachments['task_id'] = $subLegalRequestType->id; // task_id
                    $attachments['type'] = 'attachment_file_' . $file->getMimeType();
                    $attachments['author_id'] = $user->id;
                    $attachments['attachment'] = '/' . $fileName;
                    $attachments['legal_request_type_id'] = $legal_request_type_id;
                    $attachments['file_ext'] = pathinfo($fileName, PATHINFO_EXTENSION);
                    $attachments['file_type'] = $file->getMimeType();
                    $attachments['file_size'] = $file->getSize();
                    $attachments['created_at'] = Carbon::now();
                    $attachments->save();

                    // insert file attachment correspondence
                    $this->add_file_correspondence_for_legal($legal_request_type_id, $subLegalRequestType->id, $user, $fileName, 'claim');
                }
            }

            return redirect('admin/legal_request/'.$project_id.'/edit#'.$legal_request_type_id)
                ->with('success', __('Claim ('.$legal_request_type_id.') - Update Success'));
        }

        return redirect('admin/legal_request/'.$project_id.'/edit#'.$legal_request_type_id)
            ->with('error', __('Update Failed'));
    }

    public function add_trademark(Request $request){

        $user = auth()->user();

        $sub_legal_request_index = new SubLegalRequestIndex();
        $sub_legal_request_index['task_id'] = $request['trademark_t_id'];
        $sub_legal_request_index['request_type'] = $request['trademark_request_type'];
        $sub_legal_request_index['author_id'] = $user->id;
        $sub_legal_request_index['status'] = 'action_requested';
        $sub_legal_request_index->save();

        $legal_request_type_id = $sub_legal_request_index->id;

        $subLegalRequestType = new SubLegalRequestType();
        $subLegalRequestType['id'] = $request['trademark_t_id'];
        $subLegalRequestType['author_id'] = $user->id;
        $subLegalRequestType['type'] = 'trademark';
        $subLegalRequestType['legal_request_type_id'] = $legal_request_type_id;
        $subLegalRequestType['request_description'] = $request['trademark_request_description'];
        $subLegalRequestType['trademark_owner'] = $request['trademark_trademark_owner'];
        $subLegalRequestType['description_of_goods'] = $request['trademark_description_of_goods'];
        $subLegalRequestType['priority'] = $request['trademark_priority'];
        if(isset($request['trademark_priority']) && ($request['trademark_priority'] == 'Normal')){
            $subLegalRequestType['due_date_urgent'] = null;
            $subLegalRequestType['urgent_reason'] = null;
        }else{
            $subLegalRequestType['due_date_urgent'] = $request['trademark_due_date_urgent'];
            $subLegalRequestType['urgent_reason'] = $request['trademark_urgent_reason'];
        }
        $subLegalRequestType['due_date'] = $request['trademark_due_date'];
        $subLegalRequestType['target_region'] = $request['trademark_target_region'];
        if (isset($request['trademark_target_region'])) {
            $subLegalRequestType['target_region'] = implode(',', $request['trademark_target_region']);
        } else {
            $subLegalRequestType['target_region'] = '';
        }
        $subLegalRequestType['if_selected_others'] = $request['trademark_if_selected_others'];

        $subLegalRequestType->save();

        $this->correspondence_add_legal_request_type($legal_request_type_id, 'trademark', $sub_legal_request_index);

        // add campaign_type_asset_attachments
        if($request->file('trademark_attachment')){
            foreach ($request->file('trademark_attachment') as $file) {
                $attachments = new LegalRequestTypeAttachments();

//                $fileName = $file->storeAs('campaigns/'.$request['legal_request_c_id'].'/'.$asset_id, $originalName);
                $fileName = $this->file_exist_check_legal($file, $request['trademark_t_id'], $legal_request_type_id);

                $attachments['task_id'] = $request['trademark_t_id'];
                $attachments['type'] = 'attachment_file_' . $file->getMimeType();
                $attachments['author_id'] = $user->id;
                $attachments['attachment'] = '/' . $fileName;
                $attachments['legal_request_type_id'] = $legal_request_type_id;
                $attachments['file_ext'] = pathinfo($fileName, PATHINFO_EXTENSION);
                $attachments['file_type'] = $file->getMimeType();
                $attachments['file_size'] = $file->getSize();
                $attachments['created_at'] = Carbon::now();
                $attachments->save();

                $this->add_file_correspondence_for_legal($legal_request_type_id, $subLegalRequestType->id, $user, $fileName, 'trademark');
            }
        }

        $project_id = $this->projectTaskIndexRepository->get_project_id_by_task_id($request['trademark_t_id']);

        // Send Notification
        $this->send_notification_action_request($project_id, $subLegalRequestType, $legal_request_type_id);

        return redirect('admin/legal_request/'.$project_id.'/edit#'.$legal_request_type_id)
            ->with('success', __('Added the Trademark Type : ' . $legal_request_type_id));
    }

    public function edit_trademark(Request $request, $legal_request_type_id)
    {
        $param = $request->all();

        if (isset($param['target_region'])) {
            $param['target_region'] = implode(',', $param['target_region']);
        } else {
            $param['target_region'] = '';
        }
        // Permission_check
//        if(!$this->permission_check($param)){
//            return redirect('admin/project/' . $concept_development->id . '/edit#'.$task_id)
//                ->with('error', __('This action is no longer permitted. Please contact an Administrator.'));
//        }

        $project_id = $this->projectTaskIndexRepository->get_project_id_by_task_id($param['trademark_t_id']);
        $subLegalRequestType = $this->subLegalRequestTypeRepository->findById($legal_request_type_id);

        if($this->subLegalRequestTypeRepository->update($legal_request_type_id, $param)){

            $user = auth()->user();
            // insert into campaign note for correspondence
            $this->correspondence_update_legal_request_type('trademark', $param, $subLegalRequestType, $user);

            if($request->file('attachment')){
                foreach ($request->file('attachment') as $file) {
                    $attachments = new legalRequestTypeAttachments();

                    $fileName = $this->file_exist_check_legal($file, $subLegalRequestType->id, $legal_request_type_id);

                    $attachments['task_id'] = $subLegalRequestType->id; // task_id
                    $attachments['type'] = 'attachment_file_' . $file->getMimeType();
                    $attachments['author_id'] = $user->id;
                    $attachments['attachment'] = '/' . $fileName;
                    $attachments['legal_request_type_id'] = $legal_request_type_id;
                    $attachments['file_ext'] = pathinfo($fileName, PATHINFO_EXTENSION);
                    $attachments['file_type'] = $file->getMimeType();
                    $attachments['file_size'] = $file->getSize();
                    $attachments['created_at'] = Carbon::now();
                    $attachments->save();

                    // insert file attachment correspondence
                    $this->add_file_correspondence_for_legal($legal_request_type_id, $subLegalRequestType->id, $user, $fileName, 'trademark');
                }
            }

            return redirect('admin/legal_request/'.$project_id.'/edit#'.$legal_request_type_id)
                ->with('success', __('Trademark ('.$legal_request_type_id.') - Update Success'));
        }

        return redirect('admin/legal_request/'.$project_id.'/edit#'.$legal_request_type_id)
            ->with('error', __('Update Failed'));
    }

    public function add_patent(Request $request){

        $user = auth()->user();

        $sub_legal_request_index = new SubLegalRequestIndex();
        $sub_legal_request_index['task_id'] = $request['patent_t_id'];
        $sub_legal_request_index['request_type'] = $request['patent_request_type'];
        $sub_legal_request_index['author_id'] = $user->id;
        $sub_legal_request_index['status'] = 'action_requested';
        $sub_legal_request_index->save();

        $legal_request_type_id = $sub_legal_request_index->id;

        $subLegalRequestType = new SubLegalRequestType();
        $subLegalRequestType['id'] = $request['patent_t_id'];
        $subLegalRequestType['author_id'] = $user->id;
        $subLegalRequestType['type'] = 'patent';
        $subLegalRequestType['legal_request_type_id'] = $legal_request_type_id;
        $subLegalRequestType['request_description'] = $request['patent_request_description'];
        $subLegalRequestType['description_of_goods'] = $request['patent_description_of_goods'];
        $subLegalRequestType['priority'] = $request['patent_priority'];
        if(isset($request['patent_priority']) && ($request['patent_priority'] == 'Normal')){
            $subLegalRequestType['due_date_urgent'] = null;
            $subLegalRequestType['urgent_reason'] = null;
        }else{
            $subLegalRequestType['due_date_urgent'] = $request['patent_due_date_urgent'];
            $subLegalRequestType['urgent_reason'] = $request['patent_urgent_reason'];
        }
        $subLegalRequestType['due_date'] = $request['patent_due_date'];
        $subLegalRequestType['target_region'] = $request['patent_target_region'];
        if (isset($request['patent_target_region'])) {
            $subLegalRequestType['target_region'] = implode(',', $request['patent_target_region']);
        } else {
            $subLegalRequestType['target_region'] = '';
        }
        $subLegalRequestType['if_selected_others'] = $request['patent_if_selected_others'];

        $subLegalRequestType->save();

        $this->correspondence_add_legal_request_type($legal_request_type_id, 'patent', $sub_legal_request_index);

        // add campaign_type_asset_attachments
        if($request->file('patent_attachment')){
            foreach ($request->file('patent_attachment') as $file) {
                $attachments = new LegalRequestTypeAttachments();

//                $fileName = $file->storeAs('campaigns/'.$request['legal_request_c_id'].'/'.$asset_id, $originalName);
                $fileName = $this->file_exist_check_legal($file, $request['patent_t_id'], $legal_request_type_id);

                $attachments['task_id'] = $request['patent_t_id'];
                $attachments['type'] = 'attachment_file_' . $file->getMimeType();
                $attachments['author_id'] = $user->id;
                $attachments['attachment'] = '/' . $fileName;
                $attachments['legal_request_type_id'] = $legal_request_type_id;
                $attachments['file_ext'] = pathinfo($fileName, PATHINFO_EXTENSION);
                $attachments['file_type'] = $file->getMimeType();
                $attachments['file_size'] = $file->getSize();
                $attachments['created_at'] = Carbon::now();
                $attachments->save();

                $this->add_file_correspondence_for_legal($legal_request_type_id, $subLegalRequestType->id, $user, $fileName, 'patent');
            }
        }

        $project_id = $this->projectTaskIndexRepository->get_project_id_by_task_id($request['patent_t_id']);

        // Send Notification
        $this->send_notification_action_request($project_id, $subLegalRequestType, $legal_request_type_id);

        return redirect('admin/legal_request/'.$project_id.'/edit#'.$legal_request_type_id)
            ->with('success', __('Added the patent Type : ' . $legal_request_type_id));
    }

    public function edit_patent(Request $request, $legal_request_type_id)
    {
        $param = $request->all();

        if (isset($param['target_region'])) {
            $param['target_region'] = implode(',', $param['target_region']);
        } else {
            $param['target_region'] = '';
        }

        // Permission_check
//        if(!$this->permission_check($param)){
//            return redirect('admin/project/' . $concept_development->id . '/edit#'.$task_id)
//                ->with('error', __('This action is no longer permitted. Please contact an Administrator.'));
//        }

        $project_id = $this->projectTaskIndexRepository->get_project_id_by_task_id($param['patent_t_id']);
        $subLegalRequestType = $this->subLegalRequestTypeRepository->findById($legal_request_type_id);

        if($this->subLegalRequestTypeRepository->update($legal_request_type_id, $param)){

            $user = auth()->user();
            // insert into campaign note for correspondence
            $this->correspondence_update_legal_request_type('patent', $param, $subLegalRequestType, $user);

            if($request->file('attachment')){
                foreach ($request->file('attachment') as $file) {
                    $attachments = new legalRequestTypeAttachments();

                    $fileName = $this->file_exist_check_legal($file, $subLegalRequestType->id, $legal_request_type_id);

                    $attachments['task_id'] = $subLegalRequestType->id; // task_id
                    $attachments['type'] = 'attachment_file_' . $file->getMimeType();
                    $attachments['author_id'] = $user->id;
                    $attachments['attachment'] = '/' . $fileName;
                    $attachments['legal_request_type_id'] = $legal_request_type_id;
                    $attachments['file_ext'] = pathinfo($fileName, PATHINFO_EXTENSION);
                    $attachments['file_type'] = $file->getMimeType();
                    $attachments['file_size'] = $file->getSize();
                    $attachments['created_at'] = Carbon::now();
                    $attachments->save();

                    // insert file attachment correspondence
                    $this->add_file_correspondence_for_legal($legal_request_type_id, $subLegalRequestType->id, $user, $fileName, 'patent');
                }
            }

            return redirect('admin/legal_request/'.$project_id.'/edit#'.$legal_request_type_id)
                ->with('success', __('patent ('.$legal_request_type_id.') - Update Success'));
        }

        return redirect('admin/legal_request/'.$project_id.'/edit#'.$legal_request_type_id)
            ->with('error', __('Update Failed'));
    }

    public function file_exist_check_legal($file, $task_id, $legal_request_type_id)
    {
        $originalName = $file->getClientOriginalName();
        $destinationFolder = 'storage/legal_request/'.$task_id.'/'.$legal_request_type_id.'/'.$originalName;

        // If exist same name file, add numberning for version control
        if(file_exists($destinationFolder)){
            if ($pos = strrpos($originalName, '.')) {
                $new_name = substr($originalName, 0, $pos);
                $ext = substr($originalName, $pos);
            }
            $newpath = 'storage/legal_request/'.$task_id.'/'.$legal_request_type_id.'/'.$originalName;
            $uniq_no = 1;
            while (file_exists($newpath)) {
                $tmp_name = $new_name .'_v'. $uniq_no . $ext;
                $newpath = 'storage/legal_request/'.$task_id.'/'.$legal_request_type_id.'/'.$tmp_name;
                $uniq_no++;
            }
            $file_name = $tmp_name;
        }else{
            $file_name = $originalName;
        }

        $fileName =$file->storeAs('legal_request/'.$task_id.'/'.$legal_request_type_id, $file_name);
        return $fileName;
    }

    public function correspondence_add_legal_request_type($legal_request_type_id, $type_name, $sub_legal_request_index)
    {
        $user = auth()->user();
        $type_name = strtoupper($type_name);
        $change_line = "<p>$user->first_name requested <b style='color: #b91d19'>$type_name</b> <b>(#$legal_request_type_id)</b></p>";

        $legal_request_note = new LegalRequestNotes();
        $legal_request_note['id'] = $sub_legal_request_index->task_id;
        $legal_request_note['user_id'] = $user->id;
        $legal_request_note['legal_request_type_id'] = $legal_request_type_id;
        $legal_request_note['task_id'] = $sub_legal_request_index->task_id;
        $legal_request_note['project_id'] = 0;
        $legal_request_note['note'] = $change_line;
        $legal_request_note['created_at'] = Carbon::now();
        $legal_request_note->save();
    }

    public function add_file_correspondence_for_legal($legal_request_type_id, $task_id, $user, $file_type, $request_type)
    {
        // Insert into project note for correspondence (attachment file)
        $request_type_ =  strtoupper(str_replace('_', ' ', $request_type));

        $change_line  = "<p>$user->first_name has added a new attachment ($file_type) to <b style='color: #b91d19'>$request_type_</b> <b>(#$legal_request_type_id)</b></p>";

        $legal_request_note = new LegalRequestNotes();
        $legal_request_note['id'] = $task_id;
        $legal_request_note['user_id'] = $user->id;
        $legal_request_note['legal_request_type_id'] = $legal_request_type_id;
        $legal_request_note['task_id'] = $task_id;
        $legal_request_note['note'] = $change_line;
        $legal_request_note['created_at'] = Carbon::now();
        $legal_request_note->save();

    }

    public function correspondence_update_legal_request_type($task_type, $new_param, $origin_param, $user)
    {
        // Insert into legal_request_note for correspondence
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
        $change_line  = "<p>$user->first_name made a change to a <b style='color: #b91d19'>$task_type_</b> <b>(#$origin_param->legal_request_type_id)</b></p>";

        if(!empty($changed)){
            foreach ($changed as $label => $change) {

                $label = strtoupper(str_replace('_', ' ', $label));
                $from  = trim($change['original']); // Remove strip tags
                $to    = trim($change['new']);      // Remove strip tags

                $change_line .= "<div class='change_label'><p>$label:</p></div>"
                    . "<div class='change_to'><p>$to</p></div>"
                    . "<div class='change_from'><del><p>$from</p></del></div>";
            }
            $legal_request_note = new LegalRequestNotes();
            $legal_request_note['id'] = $origin_param->id; // task_id
            $legal_request_note['user_id'] = $user->id;
            $legal_request_note['legal_request_type_id'] = $origin_param->legal_request_type_id;
            $legal_request_note['task_id'] = $origin_param->id; // task_id
            $legal_request_note['project_id'] = 0;
            $legal_request_note['note'] = $change_line;
            $legal_request_note['created_at'] = Carbon::now();
            $legal_request_note->save();
        }
    }

    public function get_request_type_param($task_type, $data)
    {
        if($task_type == 'contract') {
            $new = array(
                'request_category' => $data['request_category'],
                'if_other_request_category' => isset($data['if_other_request_category']) ? $data['if_other_request_category'] : '',
                'request_description' => $data['request_description'],
                'priority' => $data['priority'],
//                'vendor_name' => $data['vendor_name'],
//                'vendor_location' => $data['vendor_location'],
                'due_date' => $data['due_date'],
                'target_region' => $data['target_region'],
                'if_selected_others' => $data['if_selected_others'],
                'assignee' => $data['assignee'],
                'legal_remarks' => $data['legal_remarks'],
            );
            return $new;
        }else if($task_type == 'claim'){
            $new = array(
                'request_category' => $data['request_category'],
                'if_other_request_category' => isset($data['if_other_request_category']) ? $data['if_other_request_category'] : '',
                'request_description' => $data['request_description'],
                'priority' => $data['priority'],
                'due_date' => $data['due_date'],
                'target_region' => $data['target_region'],
                'if_selected_others' => $data['if_selected_others'],
                'assignee' => $data['assignee'],
                'legal_remarks' => $data['legal_remarks'],
            );
            return $new;
        }else if($task_type == 'trademark'){
            $new = array(
                'request_description' => $data['request_description'],
                'trademark_owner' => $data['trademark_owner'],
                'description_of_goods' => $data['description_of_goods'],
                'priority' => $data['priority'],
                'due_date' => $data['due_date'],
                'target_region' => $data['target_region'],
                'if_selected_others' => $data['if_selected_others'],
                'assignee' => $data['assignee'],
                'legal_remarks' => $data['legal_remarks'],
                'registration' => $data['registration'],
            );
            return $new;
        }else if($task_type == 'patent'){
            $new = array(
                'request_description' => $data['request_description'],
                'priority' => $data['priority'],
                'due_date' => $data['due_date'],
                'target_region' => $data['target_region'],
                'if_selected_others' => $data['if_selected_others'],
                'assignee' => $data['assignee'],
                'legal_remarks' => $data['legal_remarks'],
                'registration' => $data['registration'],
            );
            return $new;
        }

    }

    public function revision_reason(Request $request)
    {
        $param = $request->all();
        $request_type_id = $param['request_type_id'];
        $revision_reason = $param['revision_reason'];
        $revision_reason_note = $param['revision_reason_note'];
        $sub_legal_request_index = $this->subLegalRequestIndexRepository->findById($request_type_id);
        $t_id = $sub_legal_request_index->task_id;
        $project_id = $this->projectTaskIndexRepository->get_project_id_by_task_id($t_id);

        $params['status'] = 'action_requested';
        $params['updated_at'] = Carbon::now();
        $params['revision_reason'] = $revision_reason;
        $params['revision_reason_note'] = $revision_reason_note;
        if($this->subLegalRequestIndexRepository->update($request_type_id, $params)){

            $subLegalRequest_obj = $this->subLegalRequestTypeRepository->get_sub_legal_request_by_legal_request_type_id($request_type_id);
            $current_revision_cnt = $subLegalRequest_obj['revision_cnt'];
            ////////////// Due Date Revision formula //////////
            $now = new \DateTime();
            $currentHour = (int)$now->format('H');
            // 16시 이전이면 2일, 이후면 3일을 더함
            $daysToAdd = ($currentHour < 16) ? 2 : 3;
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
            $this->subLegalRequestTypeRepository->update($request_type_id, $params, $t_param);

            $user = auth()->user();
            $task_type_ =  strtoupper(str_replace('_', ' ', $sub_legal_request_index->request_type));
            $change_line  = "<p>$user->first_name updated the status to <b>Legal Revision</b> for <b style='color: #b91d19;'>$task_type_ </b><b>(#$request_type_id)</b>
                            <br> <b style='color: black;'>Revision Reason : $revision_reason </b>
                            <br> <b style='color: black;'>$revision_reason_note <b>
                            </p>";
            $note = new LegalRequestNotes();
            $note['id'] = $t_id;
            $note['user_id'] = $user->id;
            $note['legal_request_type_id'] = $request_type_id;
            $note['task_id'] = $t_id;
            $note['project_id'] = $project_id;
            $note['note'] = $change_line;
            $note['created_at'] = Carbon::now();
            $note->save();

            // Send Notification
            $subRequestType = $this->subLegalRequestTypeRepository->findById($request_type_id);
            $this->send_notification_revision($project_id, $subRequestType, $params, $t_param);

            return redirect('admin/legal_request/'.$project_id.'/edit#'.$request_type_id)
                ->with('success', __('Data has been Updated.'));
        }

        return redirect('admin/legal_request/'.$project_id.'/edit#'.$request_type_id)
            ->with('error', __('Data updates Failed'));
    }

    public function send_notification_revision($project_id, $subRequestType, $params, $t_param)
    {
        // From : Task Request
        // Receiver : Legal Assignee

        $project_obj = $this->projectRepository->findById($project_id);
        $due_date_mail = $t_param['due_date_revision'];

        // Assignee Name
        $assignee_id = $subRequestType['assignee'];
        if($assignee_id != null) {
            $assignee_obj = $this->userRepository->findById($assignee_id);
            $assignee_name = $assignee_obj->first_name . ' ' . $assignee_obj->last_name;
        }else{
            $assignee_name = "N/A";
        }

        // Task Creator
        $task_author_name = $subRequestType->author_obj->first_name . ' ' . $subRequestType->author_obj->last_name;

        $details = [
            'mail_subject'      => 'Action Requested (Revision) : Legal Request',
            'template'          => 'emails.task.revision',
            'receiver'          => 'Hello ' . $assignee_name . ',',
            'message'           => "You got a new request from " . $task_author_name . ".",
            'title'             => 'Action Requested (Revision) : Legal Request',
            'project_id'        => $project_id,
            'project_title'     => $project_obj->name,
            'request_id'        => $subRequestType['legal_request_type_id'],
            'request_type'      => $subRequestType['type'],
            'priority'          => 'Revision',
            'due_date'          => $due_date_mail,
            'reason'            => $params['revision_reason'],
            'note'              => $params['revision_reason_note'],
            'url'               => '/admin/mm_request/'.$project_id.'/edit#'.$subRequestType['lega_request_type_id'],
        ];

        $receiver_list[] = $subRequestType->assignee_obj->email;
        Mail::to($receiver_list)->send(new TaskStatusNotification($details));
    }

    public function actionReSubmit($id)
    {
        $sub_legal_request_index = $this->subLegalRequestIndexRepository->findById($id);
        $param['status'] = 'action_requested';
        $param['updated_at'] = Carbon::now();
        $t_id = $sub_legal_request_index->task_id;
        $project_id = $this->projectTaskIndexRepository->get_project_id_by_task_id($t_id);
        if($this->subLegalRequestIndexRepository->update($id, $param)){
            $subLegalRequest_obj = $this->subLegalRequestTypeRepository->get_sub_legal_request_by_legal_request_type_id($id);
            $current_revision_cnt = $subLegalRequest_obj['revision_cnt'];
            ////////////// Due Date Revision formula //////////
            $now = new \DateTime();
            $currentHour = (int)$now->format('H');
            // 16시 이전이면 2일, 이후면 3일을 더함
            $daysToAdd = ($currentHour < 16) ? 2 : 3;
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
            $this->subLegalRequestTypeRepository->update($id, $t_param);
            $this->legal_status_correspondence($t_id, $project_id, $sub_legal_request_index->request_type, $id, 'Action Requested (Revision)');
            echo '/admin/legal_request/'.$project_id.'/edit#'.$id;
        }else{
            echo 'fail';
        }
    }

    public function actionInProgress($id)
    {
        $sub_legal_request_index = $this->subLegalRequestIndexRepository->findById($id);
        $param['status'] = 'in_progress';
        $param['updated_at'] = Carbon::now();
        $user = auth()->user();
        $param_type['assignee'] = $user->id;
        if($this->subLegalRequestIndexRepository->update($id, $param)){
            $this->subLegalRequestTypeRepository->update($id, $param_type);
            $t_id = $sub_legal_request_index->task_id;
            $project_id = $this->projectTaskIndexRepository->get_project_id_by_task_id($t_id);
            $this->legal_status_correspondence($t_id, $project_id, $sub_legal_request_index->request_type, $sub_legal_request_index->id, 'In Progress');
            echo '/admin/legal_request/'.$project_id.'/edit#'.$id;
        }else{
            echo 'fail';
        }
    }

    public function actionReview($id)
    {
        $sub_legal_request_index = $this->subLegalRequestIndexRepository->findById($id);
        $param['status'] = 'action_review';
        $param['updated_at'] = Carbon::now();
        $t_id = $sub_legal_request_index->task_id;
        $project_id = $this->projectTaskIndexRepository->get_project_id_by_task_id($t_id);
        if($this->subLegalRequestIndexRepository->update($id, $param)){
            $this->legal_status_correspondence($t_id, $project_id, $sub_legal_request_index->request_type, $sub_legal_request_index->id, 'Action Review');

            // Send Notification
            $subRequestType = $this->subLegalRequestTypeRepository->findById($sub_legal_request_index->id);
            $this->send_notification_review($project_id, $subRequestType);

            echo '/admin/legal_request/'.$project_id.'/edit#'.$id;
        }else{
            echo 'fail';
        }
    }

    public function send_notification_review($project_id, $subRequestType)
    {
        // From : Legal Team
        // Receiver : Task Requester

        $project_obj = $this->projectRepository->findById($project_id);
        if($subRequestType['priority'] == 'Normal'){
            $due_date_mail = $subRequestType['due_date'];
        }else{
            $due_date_mail = $subRequestType['due_date_urgent'];
        }

        // Assignee Name
        $assignee_id = $subRequestType['assignee'];
        if($assignee_id != null) {
            $assignee_obj = $this->userRepository->findById($assignee_id);
            $assignee_name = $assignee_obj->first_name . ' ' . $assignee_obj->last_name;
        }else{
            $assignee_name = "N/A";
        }

        // Task Creator
        $task_author_name = $subRequestType->author_obj->first_name . ' ' . $subRequestType->author_obj->last_name;

        $details = [
            'template'          => 'emails.task.review',
            'mail_subject'      => 'Action Review : Legal Request',
            'receiver'          => 'Hello ' . $task_author_name . ',',
            'message'           => "You got a new request from Legal Team.",
            'title'             => 'Action Required : Legal Request',
            'project_id'        => $project_id,
            'project_title'     => $project_obj->name,
            'request_id'        => $subRequestType['legal_request_type_id'],
            'request_type'      => $subRequestType['type'],
            'priority'          => $subRequestType['priority'],
            'assignee'          => $assignee_name,
            'due_date'          => $due_date_mail,
            'url'               => '/admin/mm_request/'.$project_id.'/edit#'.$subRequestType['legal_request_type_id'],
        ];

        $receiver_list[] = $subRequestType->author_obj->email;
//        $group_rs = $this->userRepository->get_receiver_emails_by_team('Admin');
//        foreach ($group_rs as $team_user) {
//            $receiver_list[] = $team_user['email'];
//        }

        /// Send to receivers
        Mail::to($receiver_list)->send(new TaskStatusNotification($details));

    }

    public function actionComplete($id)
    {
        $sub_legal_request_index = $this->subLegalRequestIndexRepository->findById($id);
        $param['status'] = 'action_completed';
        $param['updated_at'] = Carbon::now();
        $t_id = $sub_legal_request_index->task_id;
        $project_id = $this->projectTaskIndexRepository->get_project_id_by_task_id($t_id);
        if($this->subLegalRequestIndexRepository->update($id, $param)){
            $this->legal_status_correspondence($t_id, $project_id, $sub_legal_request_index->request_type, $sub_legal_request_index->id, 'Action Completed');
            echo '/admin/legal_request/'.$project_id.'/edit#'.$id;
        }else{
            echo 'fail';
        }
    }

    public function fileRemove($id)
    {
        $attachment_obj = $this->legalRequestTypeFileAttachmentsRepository->findById($id);
        $file_name = $attachment_obj->attachment;
        $task_id = $attachment_obj->task_id;
        $legal_request_type_id = $attachment_obj->legal_request_type_id;
        $user = auth()->user();
        if($attachment_obj->delete()){
            $requestTypeIndex = $this->subLegalRequestIndexRepository->findById($legal_request_type_id);
            $request_type =  ucwords(str_replace('_', ' ', $requestTypeIndex->request_type));
            $change_line = "<p>$user->first_name removed a attachment ($file_name) on <b style='color: #b91d19'>$request_type</b> <b>(#$legal_request_type_id)</b></p>";

            $legal_request_note = new LegalRequestNotes();
            $legal_request_note['id'] = $task_id; // task_id
            $legal_request_note['user_id'] = $user->id;
            $legal_request_note['legal_request_type_id'] = $legal_request_type_id;
            $legal_request_note['task_id'] = $task_id; // task_id
            $legal_request_note['note'] = $change_line;
            $legal_request_note['created_at'] = Carbon::now();
            $legal_request_note->save();

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

        $note = new LegalRequestNotes();
        $note['id'] = $t_id;
        $note['user_id'] = $user->id;
        $note['legal_request_type_id'] = 0;
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
                'url' => '/admin/legal_request/' . $p_id . '/edit#' . $t_id,
            ];
            $receiver_list = explode(',', $email_list);
            Mail::to($receiver_list)->send(new TaskStatusNotification($details));
        }

        return redirect('admin/legal_request/'.$p_id.'/edit')
            ->with('success', __('Data has been Updated.'));
    }

    public function requestTypeRemovePermissionCheck($request_type_id){

        $user = auth()->user();
        if($user->role == 'Admin') return true; // admin okay

        $obj = $this->subLegalRequestIndexRepository->findById($request_type_id);
        if($obj->author_id != $user->id){
            return false;
        }
        return true;
    }

    public function requestTypeRemove($request_type_id, $type)
    {
        $obj = $this->subLegalRequestIndexRepository->findById($request_type_id);
        $t_id = $obj->task_id;
        $p_id = $this->projectTaskIndexRepository->get_project_id_by_task_id($t_id);
        if($this->requestTypeRemovePermissionCheck($request_type_id)){
            // Delete from sub_qra_request_index, sub_qra_request_type tables
            $this->subLegalRequestIndexRepository->delete($request_type_id);
            $this->subLegalRequestTypeRepository->delete($request_type_id);

            // Add correspondence for asset Removed
            $this->legal_remove_correspondence($t_id, $p_id, $type, $request_type_id);

            echo '/admin/legal_request/'.$p_id.'/edit';
        }else{
            echo 'fail';
        }
    }

    public function legal_status_correspondence($t_id, $p_id, $task_type, $legal_request_type_id, $status)
    {
        // Insert into Project note for correspondence (attachment file)
        $user = auth()->user();
        $task_type_ =  strtoupper(str_replace('_', ' ', $task_type));
        $change_line  = "<p>$user->first_name updated the status to <b>$status</b> for <b style='color: #b91d19;'>$task_type_ </b><b>(#$legal_request_type_id)</b></p>";

        $note = new LegalRequestNotes();
        $note['id'] = $t_id;
        $note['user_id'] = $user->id;
        $note['legal_request_type_id'] = $legal_request_type_id;
        $note['task_id'] = $t_id;
        $note['project_id'] = $p_id;
        $note['note'] = $change_line;
        $note['created_at'] = Carbon::now();
        $note->save();
    }

    public function legal_remove_correspondence($t_id, $p_id, $task_type, $request_type_id)
    {
        // Insert into Project note for correspondence (attachment file)
        $user = auth()->user();
        $task_type_ =  strtoupper(str_replace('_', ' ', $task_type));

        $change_line  = "<b style='color: #b91d19;'>$task_type_ </b><b>(#$request_type_id)</b> has been removed by $user->first_name";

        $note = new LegalRequestNotes();
        $note['id'] = $t_id;
        $note['user_id'] = $user->id;
        $note['legal_request_type_id'] = $request_type_id;
        $note['task_id'] = $t_id;
        $note['project_id'] = $p_id;
        $note['note'] = $change_line;
        $note['created_at'] = Carbon::now();
        $note->save();
    }

}
