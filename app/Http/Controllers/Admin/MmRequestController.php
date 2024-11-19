<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Http\Controllers\NotifyController;
use App\Http\Requests\Admin\DevRequest;
use App\Mail\DevMessage;
use App\Mail\NewRequest;
use App\Mail\NoteProject;
use App\Mail\Revision;
use App\Mail\TaskStatusNotification;
use App\Models\DevNotes;
use App\Models\MmRequestNotes;
use App\Models\MmRequestTypeAttachments;
use App\Models\ProjectNotes;
use App\Models\ProjectTaskIndex;
use App\Models\ProjectTypeTaskAttachments;
use App\Models\SubMmRequestIndex;
use App\Models\SubMmRequestType;
use App\Models\TaskTypeMmRequest;
use App\Models\User;
use App\Repositories\Admin\AssetNotificationUserRepository;
use App\Repositories\Admin\AssetOwnerAssetsRepository;
use App\Repositories\Admin\BrandRepository;
use App\Repositories\Admin\MmRequestNotesRepository;
use App\Repositories\Admin\MmRequestRepository;
use App\Repositories\Admin\MmRequestTypeFileAttachmentsRepository;
use App\Repositories\Admin\PlantRepository;
use App\Repositories\Admin\ProjectNotesRepository;
use App\Repositories\Admin\ProjectRepository;
use App\Repositories\Admin\ProjectTaskFileAttachmentsRepository;
use App\Repositories\Admin\ProjectTaskIndexRepository;
use App\Repositories\Admin\SubMmRequestIndexRepository;
use App\Repositories\Admin\SubMmRequestTypeRepository;
use App\Repositories\Admin\SubQraRequestIndexRepository;
use App\Repositories\Admin\TaskTypeConceptDevelopmentRepository;
use App\Repositories\Admin\TaskTypeLegalRequestRepository;
use App\Repositories\Admin\TaskTypeMmRequestRepository;
use App\Repositories\Admin\TaskTypeProductBriefRepository;
use App\Repositories\Admin\TeamRepository;
use App\Repositories\Admin\UserRepository;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Models\DevFileAttachments;
use App\Http\Requests\Admin\UserRequest;

use App\Repositories\Admin\CampaignBrandsRepository;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class MmRequestController extends Controller
{
    Private $projectRepository;
    Private $projectTaskIndexRepository;
    Private $taskTypeMmRequestRepository;
    Private $projectTaskFileAttachmentsRepository;
    Private $projectNotesRepository;
    Private $mmRequestNotesRepository;
    Private $mmRequestTypeAttachmentsRepository;
    Private $mmRequestRepository;
    Private $subMmRequestIndexRepository;
    Private $subMmRequestTypeRepository;
    Private $teamRepository;
    Private $plantRepository;
    Private $brandRepository;
    private $userRepository;


    public function __construct(
        ProjectRepository $projectRepository,
        ProjectTaskIndexRepository $projectTaskIndexRepository,
        TaskTypeMmRequestRepository $taskTypeMmRequestRepository,
        ProjectTaskFileAttachmentsRepository $projectTaskFileAttachmentsRepository,
        ProjectNotesRepository $projectNotesRepository,
        MmRequestNotesRepository $mmRequestNotesRepository,
        MmRequestTypeFileAttachmentsRepository $mmRequestTypeAttachmentsRepository,
        MmRequestRepository $mmRequestRepository,
        SubMmRequestIndexRepository $subMmRequestIndexRepository,
        SubMmRequestTypeRepository $subMmRequestTypeRepository,
        TeamRepository $teamRepository,
        PlantRepository $plantRepository,
        BrandRepository $brandRepository,
        UserRepository $userRepository)
    {
        parent::__construct();

        $this->projectRepository = $projectRepository;
        $this->projectTaskIndexRepository = $projectTaskIndexRepository;
        $this->taskTypeMmRequestRepository = $taskTypeMmRequestRepository;
        $this->projectTaskFileAttachmentsRepository = $projectTaskFileAttachmentsRepository;
        $this->projectNotesRepository = $projectNotesRepository;
        $this->mmRequestNotesRepository = $mmRequestNotesRepository;
        $this->mmRequestTypeAttachmentsRepository = $mmRequestTypeAttachmentsRepository;
        $this->mmRequestRepository = $mmRequestRepository;
        $this->subMmRequestIndexRepository = $subMmRequestIndexRepository;
        $this->subMmRequestTypeRepository = $subMmRequestTypeRepository;
        $this->teamRepository = $teamRepository;
        $this->plantRepository = $plantRepository;
        $this->brandRepository = $brandRepository;
        $this->userRepository = $userRepository;


    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $param = $request->all();
        $this->data['currentAdminMenu'] = 'mm_request';

        $user = auth()->user();
        if($user->team == 'MDM' || $user->team == 'Admin') {
            $param['cur_user'] = '';
        }else{
            $param['cur_user'] = $this->userRepository->user_array_for_access($user);
        }

        $options = [
            'per_page' => $this->perPage,
            'order' => [
                'id' => 'asc',
            ],
            'filter' => $param,
        ];
        $this->data['projects'] = $this->mmRequestRepository->findAll($options);
        $this->data['id'] = !empty($params['id']) ? $params['id'] : '';

        return view('admin.mm_request.index', $this->data);
    }

    public function board(Request $request)
    {
        $param = $request->all();
        $this->data['currentAdminMenu'] = 'mm_request_board';

        $user = auth()->user();

        if($user->team == 'MDM' || $user->team == 'Admin') {
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
            $brand = $param['brand'];
        }else{
            $brand = !empty($param['brand']) ? $param['brand'] : '';
        }

        $this->data['task_list_action_requested'] = $this->taskTypeMmRequestRepository->get_action_requested_list($cur_user, $assignee, $team, $brand);
        $this->data['task_list_in_progress'] = $this->taskTypeMmRequestRepository->get_in_progress_list($cur_user, $assignee, $team, $brand);
        $this->data['task_list_action_review'] = $this->taskTypeMmRequestRepository->get_action_review_list($cur_user, $assignee, $team, $brand);
        $this->data['task_list_action_completed'] = $this->taskTypeMmRequestRepository->get_action_completed_list($cur_user, $assignee, $team, $brand);

        $this->data['assignee'] = $assignee;
        $this->data['team'] = $team;
        $this->data['brand'] = $brand;

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
                'is_active' => 'yes',
            ],
        ];

        $this->data['mm_request_assignee_list'] = $this->userRepository->getMmSecondAssigneeList();
        $this->data['teams'] =$this->teamRepository->findAll($team_options);
        $this->data['brands'] =$this->brandRepository->findAll($brand_options);

        return view('admin.mm_request.board', $this->data);
    }

    public function index_list(Request $request)
    {
        $param = $request->all();
        $this->data['currentAdminMenu'] = 'mm_request_list';

        $user = auth()->user();

        if($user->team == 'MDM' || $user->team == 'Admin') {
            $cur_user = ' ';
        }else{
            $cur_user = $this->userRepository->getPageAccess($user);
        }

        $str = !empty($param['q']) ? $param['q'] : '';
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

        $this->data['task_list'] = $this->taskTypeMmRequestRepository->get_task_list($cur_user, $str, $assignee, $team, $status);

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
        $this->data['mm_request_assignee_list'] = $this->userRepository->getMmSecondAssigneeList();
        $this->data['teams'] =$this->teamRepository->findAll($team_options);
        $this->data['brands'] =$this->brandRepository->findAll($brand_options);

        return view('admin.mm_request.index_list', $this->data);
    }


    public function edit($id)
    {
        // Access check (MDM)
        $user = auth()->user();
        if ( !(in_array($user->team, ['MDM', 'Admin']) || in_array($user->role, ['Project Manager', 'Team Lead'])) ) {
            return view('admin.security.permission', $this->data);
        }

        $this->data['currentAdminMenu'] = 'mm_request_board';

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

        $author_obj = User::find($project->author_id);
        if($author_obj){
            $this->data['author_name'] = $author_obj['first_name'] . " " . $author_obj['last_name'];
        }else{
            $this->data['author_name'] = 'N/A';
        }

        $task_id = $this->mmRequestRepository->get_task_id_for_mm($id);
        $this->data['task_id']= $task_id;

        // Request Type list
        $this->data['request_type_list'] = $request_type_list = $this->mmRequestRepository->get_mm_request_list_by_task_id($task_id);
        // task_detail
        if(sizeof($request_type_list)>0){
            foreach ($request_type_list as $k => $request_type){
                $mm_request_type_id = $request_type->mm_request_type_id;
                $task_files = $this->mmRequestTypeAttachmentsRepository->findAllByRequestTypeId($mm_request_type_id);
                $request_type_list[$k]->files = $task_files;
            }
        }

        /////////// MM Request Task ////////////////////////////////////////////
        $this->data['performed_bys'] = [
            'SGS', 'QM QA', 'Sourcing'
        ];

        $this->data['priorities'] = [
            'Normal', 'Urgent'
        ];
        $this->data['urgent_reason_list'] = [
            'Sales/NPD plan-change',
            'Retailer Request/Plan-change',
            'PO issue (Preventing B/O)',
            'Defect/Production',
        ];
        $this->data['mm_request_types'] = [
            'New','Update','Dimensions/Logistics','Price'
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
//        $this->data['mm_request_set_up_plants'] = [
//            '1000_KISS','1010_Kiss Canada','1100_IVY','1110_IVY LA',
//            '1300_AST','1310_IVY E-commerce','1320_KISS E-commerce',
//            '1410_Red Beauty LA','1700_Kiss MX','4027_KGH','4021_KGH UK',
//            '4023_IIO UK','4028_BSH UK','G100_KISS GA','G110_IVY GA',
//            'G130_AST GA','G140_RED GA','G190_Vivace GA'
//        ];
        $this->data['mm_request_set_up_plants'] = $this->plantRepository->get_set_up_plants();

        $this->data['mm_request_assignee_list'] = $this->userRepository->getMmSecondAssigneeList();

        // Project_notes
        $options = [
            'id' => $task_id,
            'order' => [
                'created_at' => 'desc',
            ]
        ];

        $correspondences = $this->mmRequestNotesRepository->findAll($options);
        $this->data['correspondences'] = $correspondences;

        return view('admin.mm_request.form', $this->data);
    }

    public function add_new(Request $request){

        $user = auth()->user();
        $sub_mm_request_index = new SubMmRequestIndex();
        $sub_mm_request_index['task_id'] = $request['new_t_id'];
        $sub_mm_request_index['request_type'] = $request['new_request_type'];
        $sub_mm_request_index['author_id'] = $user->id;
        $sub_mm_request_index['status'] = 'action_requested';
        $sub_mm_request_index->save();
        $mm_request_type_id = $sub_mm_request_index->id;

        $subMmRequestType = new SubMmRequestType();
        $subMmRequestType['id'] = $request['new_t_id'];
        $subMmRequestType['author_id'] = $user->id;
        $subMmRequestType['type'] = 'new';
        $subMmRequestType['mm_request_type_id'] = $mm_request_type_id;
        $subMmRequestType['remark'] = $request['new_remark'];
        $subMmRequestType['materials'] = $request['new_materials'];
        $subMmRequestType['priority'] = $request['new_priority'];
        if(isset($request['new_priority']) && ($request['new_priority'] == 'Normal')){
            $subMmRequestType['due_date_urgent'] = null;
            $subMmRequestType['urgent_reason'] = null;
            $subMmRequestType['urgent_detail'] = null;
        }else{
            $subMmRequestType['due_date_urgent'] = $request['new_due_date_urgent'];
            $subMmRequestType['urgent_reason'] = $request['new_urgent_reason'];
            $subMmRequestType['urgent_detail'] = $request['new_urgent_detail'];
        }
        $subMmRequestType['due_date'] = $request['new_due_date'];
        if (isset($request['new_set_up_plant'])) {
            $subMmRequestType['set_up_plant'] = implode(',', $request['new_set_up_plant']);
        } else {
            $subMmRequestType['set_up_plant'] = '';
        }
        $subMmRequestType->save();
        $this->correspondence_add_mm_request_type($mm_request_type_id, 'new', $sub_mm_request_index);
        // add campaign_type_asset_attachments
        if($request->file('new_attachment')){
            foreach ($request->file('new_attachment') as $file) {
                $attachments = new MmRequestTypeAttachments();
//                $fileName = $file->storeAs('campaigns/'.$request['mm_request_c_id'].'/'.$asset_id, $originalName);
                $fileName = $this->file_exist_check_mm($file, $request['new_t_id'], $mm_request_type_id);
                $attachments['task_id'] = $request['new_t_id'];
                $attachments['type'] = 'attachment_file_' . $file->getMimeType();
                $attachments['author_id'] = $user->id;
                $attachments['attachment'] = '/' . $fileName;
                $attachments['mm_request_type_id'] = $mm_request_type_id;
                $attachments['file_ext'] = pathinfo($fileName, PATHINFO_EXTENSION);
                $attachments['file_type'] = $file->getMimeType();
                $attachments['file_size'] = $file->getSize();
                $attachments['created_at'] = Carbon::now();
                $attachments->save();
                $this->add_file_correspondence_for_mm($mm_request_type_id, $subMmRequestType->id, $user, $fileName, 'new');
            }
        }

        $project_id = $this->projectTaskIndexRepository->get_project_id_by_task_id($request['new_t_id']);

        // Send Notification
        $this->send_notification_action_request($project_id, $subMmRequestType, $mm_request_type_id);

        return redirect('admin/mm_request/'.$project_id.'/edit#'.$mm_request_type_id)
            ->with('success', __('Added the New Type : ' . $mm_request_type_id));
    }

    public function edit_new(Request $request, $mm_request_type_id)
    {
        $param = $request->all();
        if (isset($param['set_up_plant'])) {
            $param['set_up_plant'] = implode(',', $param['set_up_plant']);
        } else {
            $param['set_up_plant'] = '';
        }

        $project_id = $this->projectTaskIndexRepository->get_project_id_by_task_id($param['new_t_id']);
        $subMmRequestType = $this->subMmRequestTypeRepository->findById($mm_request_type_id);

        if($this->subMmRequestTypeRepository->update($mm_request_type_id, $param)){

            $user = auth()->user();
            // insert into campaign note for correspondence
            $this->correspondence_update_mm_request_type('new', $param, $subMmRequestType, $user);

            if($request->file('attachment')){
                foreach ($request->file('attachment') as $file) {
                    $attachments = new MmRequestTypeAttachments();

                    $fileName = $this->file_exist_check_mm($file, $subMmRequestType->id, $mm_request_type_id);

                    $attachments['task_id'] = $subMmRequestType->id; // task_id
                    $attachments['type'] = 'attachment_file_' . $file->getMimeType();
                    $attachments['author_id'] = $user->id;
                    $attachments['attachment'] = '/' . $fileName;
                    $attachments['mm_request_type_id'] = $mm_request_type_id;
                    $attachments['file_ext'] = pathinfo($fileName, PATHINFO_EXTENSION);
                    $attachments['file_type'] = $file->getMimeType();
                    $attachments['file_size'] = $file->getSize();
                    $attachments['created_at'] = Carbon::now();
                    $attachments->save();

                    // insert file attachment correspondence
                    $this->add_file_correspondence_for_mm($mm_request_type_id, $subMmRequestType->id, $user, $fileName, 'new');
                }
            }
            return redirect('admin/mm_request/'.$project_id.'/edit#'.$mm_request_type_id)
                ->with('success', __('New ('.$mm_request_type_id.') - Update Success'));
        }
        return redirect('admin/mm_request/'.$project_id.'/edit#'.$mm_request_type_id)
            ->with('error', __('Update Failed'));
    }

    public function add_update(Request $request){

        $user = auth()->user();
        $sub_mm_request_index = new SubMmRequestIndex();
        $sub_mm_request_index['task_id'] = $request['update_t_id'];
        $sub_mm_request_index['request_type'] = $request['update_request_type'];
        $sub_mm_request_index['author_id'] = $user->id;
        $sub_mm_request_index['status'] = 'action_requested';
        $sub_mm_request_index->save();
        $mm_request_type_id = $sub_mm_request_index->id;

        $subMmRequestType = new SubMmRequestType();
        $subMmRequestType['id'] = $request['update_t_id'];
        $subMmRequestType['author_id'] = $user->id;
        $subMmRequestType['type'] = 'update';
        $subMmRequestType['mm_request_type_id'] = $mm_request_type_id;
        $subMmRequestType['remark'] = $request['update_remark'];
        $subMmRequestType['materials'] = $request['update_materials'];
        $subMmRequestType['priority'] = $request['update_priority'];
        if(isset($request['update_priority']) && ($request['update_priority'] == 'Normal')){
            $subMmRequestType['due_date_urgent'] = null;
            $subMmRequestType['urgent_reason'] = null;
            $subMmRequestType['urgent_detail'] = null;
        }else{
            $subMmRequestType['due_date_urgent'] = $request['update_due_date_urgent'];
            $subMmRequestType['urgent_reason'] = $request['update_urgent_reason'];
            $subMmRequestType['urgent_detail'] = $request['update_urgent_detail'];
        }
        $subMmRequestType['due_date'] = $request['update_due_date'];
        if (isset($request['update_set_up_plant'])) {
            $subMmRequestType['set_up_plant'] = implode(',', $request['update_set_up_plant']);
        } else {
            $subMmRequestType['set_up_plant'] = '';
        }
        $subMmRequestType->save();
        $this->correspondence_add_mm_request_type($mm_request_type_id, 'update', $sub_mm_request_index);
        // add campaign_type_asset_attachments
        if($request->file('update_attachment')){
            foreach ($request->file('update_attachment') as $file) {
                $attachments = new MmRequestTypeAttachments();
//                $fileName = $file->storeAs('campaigns/'.$request['mm_request_c_id'].'/'.$asset_id, $originalName);
                $fileName = $this->file_exist_check_mm($file, $request['update_t_id'], $mm_request_type_id);
                $attachments['task_id'] = $request['update_t_id'];
                $attachments['type'] = 'attachment_file_' . $file->getMimeType();
                $attachments['author_id'] = $user->id;
                $attachments['attachment'] = '/' . $fileName;
                $attachments['mm_request_type_id'] = $mm_request_type_id;
                $attachments['file_ext'] = pathinfo($fileName, PATHINFO_EXTENSION);
                $attachments['file_type'] = $file->getMimeType();
                $attachments['file_size'] = $file->getSize();
                $attachments['created_at'] = Carbon::now();
                $attachments->save();
                $this->add_file_correspondence_for_mm($mm_request_type_id, $subMmRequestType->id, $user, $fileName, 'update');
            }
        }
        $project_id = $this->projectTaskIndexRepository->get_project_id_by_task_id($request['update_t_id']);

        // Send Notification
        $this->send_notification_action_request($project_id, $subMmRequestType, $mm_request_type_id);

        return redirect('admin/mm_request/'.$project_id.'/edit#'.$mm_request_type_id)
            ->with('success', __('Added the UPDATE Type : ' . $mm_request_type_id));
    }

    public function edit_update(Request $request, $mm_request_type_id)
    {
        $param = $request->all();
        if (isset($param['set_up_plant'])) {
            $param['set_up_plant'] = implode(',', $param['set_up_plant']);
        } else {
            $param['set_up_plant'] = '';
        }

        $project_id = $this->projectTaskIndexRepository->get_project_id_by_task_id($param['update_t_id']);
        $subMmRequestType = $this->subMmRequestTypeRepository->findById($mm_request_type_id);

        if($this->subMmRequestTypeRepository->update($mm_request_type_id, $param)){

            $user = auth()->user();
            // insert into campaign note for correspondence
            $this->correspondence_update_mm_request_type('update', $param, $subMmRequestType, $user);

            if($request->file('attachment')){
                foreach ($request->file('attachment') as $file) {
                    $attachments = new MmRequestTypeAttachments();

                    $fileName = $this->file_exist_check_mm($file, $subMmRequestType->id, $mm_request_type_id);

                    $attachments['task_id'] = $subMmRequestType->id; // task_id
                    $attachments['type'] = 'attachment_file_' . $file->getMimeType();
                    $attachments['author_id'] = $user->id;
                    $attachments['attachment'] = '/' . $fileName;
                    $attachments['mm_request_type_id'] = $mm_request_type_id;
                    $attachments['file_ext'] = pathinfo($fileName, PATHINFO_EXTENSION);
                    $attachments['file_type'] = $file->getMimeType();
                    $attachments['file_size'] = $file->getSize();
                    $attachments['created_at'] = Carbon::now();
                    $attachments->save();

                    // insert file attachment correspondence
                    $this->add_file_correspondence_for_mm($mm_request_type_id, $subMmRequestType->id, $user, $fileName, 'update');
                }
            }
            return redirect('admin/mm_request/'.$project_id.'/edit#'.$mm_request_type_id)
                ->with('success', __('New ('.$mm_request_type_id.') - Update Success'));
        }
        return redirect('admin/mm_request/'.$project_id.'/edit#'.$mm_request_type_id)
            ->with('error', __('Update Failed'));
    }

    public function add_dimensions(Request $request){

        $user = auth()->user();
        $sub_mm_request_index = new SubMmRequestIndex();
        $sub_mm_request_index['task_id'] = $request['dimensions_t_id'];
        $sub_mm_request_index['request_type'] = $request['dimensions_request_type'];
        $sub_mm_request_index['author_id'] = $user->id;
        $sub_mm_request_index['status'] = 'action_requested';
        $sub_mm_request_index->save();
        $mm_request_type_id = $sub_mm_request_index->id;

        $subMmRequestType = new SubMmRequestType();
        $subMmRequestType['id'] = $request['dimensions_t_id'];
        $subMmRequestType['author_id'] = $user->id;
        $subMmRequestType['type'] = 'dimensions';
        $subMmRequestType['mm_request_type_id'] = $mm_request_type_id;
        $subMmRequestType['remark'] = $request['dimensions_remark'];
        $subMmRequestType['materials'] = $request['dimensions_materials'];
        $subMmRequestType['priority'] = $request['dimensions_priority'];
        if(isset($request['dimensions_priority']) && ($request['dimensions_priority'] == 'Normal')){
            $subMmRequestType['due_date_urgent'] = null;
            $subMmRequestType['urgent_reason'] = null;
            $subMmRequestType['urgent_detail'] = null;
        }else{
            $subMmRequestType['due_date_urgent'] = $request['dimensions_due_date_urgent'];
            $subMmRequestType['urgent_reason'] = $request['dimensions_urgent_reason'];
            $subMmRequestType['urgent_detail'] = $request['dimensions_urgent_detail'];
        }
        $subMmRequestType['due_date'] = $request['dimensions_due_date'];
        if (isset($request['dimensions_set_up_plant'])) {
            $subMmRequestType['set_up_plant'] = implode(',', $request['dimensions_set_up_plant']);
        } else {
            $subMmRequestType['set_up_plant'] = '';
        }
        $subMmRequestType->save();
        $this->correspondence_add_mm_request_type($mm_request_type_id, 'dimensions', $sub_mm_request_index);
        // add campaign_type_asset_attachments
        if($request->file('dimensions_attachment')){
            foreach ($request->file('dimensions_attachment') as $file) {
                $attachments = new MmRequestTypeAttachments();
//                $fileName = $file->storeAs('campaigns/'.$request['mm_request_c_id'].'/'.$asset_id, $originalName);
                $fileName = $this->file_exist_check_mm($file, $request['dimensions_t_id'], $mm_request_type_id);
                $attachments['task_id'] = $request['dimensions_t_id'];
                $attachments['type'] = 'attachment_file_' . $file->getMimeType();
                $attachments['author_id'] = $user->id;
                $attachments['attachment'] = '/' . $fileName;
                $attachments['mm_request_type_id'] = $mm_request_type_id;
                $attachments['file_ext'] = pathinfo($fileName, PATHINFO_EXTENSION);
                $attachments['file_type'] = $file->getMimeType();
                $attachments['file_size'] = $file->getSize();
                $attachments['created_at'] = Carbon::now();
                $attachments->save();
                $this->add_file_correspondence_for_mm($mm_request_type_id, $subMmRequestType->id, $user, $fileName, 'dimensions');
            }
        }
        $project_id = $this->projectTaskIndexRepository->get_project_id_by_task_id($request['dimensions_t_id']);

        // Send Notification
        $this->send_notification_action_request($project_id, $subMmRequestType, $mm_request_type_id);

        return redirect('admin/mm_request/'.$project_id.'/edit#'.$mm_request_type_id)
            ->with('success', __('Added the Dimensions & Logistics Type : ' . $mm_request_type_id));
    }

    public function edit_dimensions(Request $request, $mm_request_type_id)
    {
        $param = $request->all();
        if (isset($param['set_up_plant'])) {
            $param['set_up_plant'] = implode(',', $param['set_up_plant']);
        } else {
            $param['set_up_plant'] = '';
        }

        $project_id = $this->projectTaskIndexRepository->get_project_id_by_task_id($param['dimensions_t_id']);
        $subMmRequestType = $this->subMmRequestTypeRepository->findById($mm_request_type_id);

        if($this->subMmRequestTypeRepository->update($mm_request_type_id, $param)){

            $user = auth()->user();
            // insert into campaign note for correspondence
            $this->correspondence_update_mm_request_type('dimensions', $param, $subMmRequestType, $user);

            if($request->file('attachment')){
                foreach ($request->file('attachment') as $file) {
                    $attachments = new MmRequestTypeAttachments();

                    $fileName = $this->file_exist_check_mm($file, $subMmRequestType->id, $mm_request_type_id);

                    $attachments['task_id'] = $subMmRequestType->id; // task_id
                    $attachments['type'] = 'attachment_file_' . $file->getMimeType();
                    $attachments['author_id'] = $user->id;
                    $attachments['attachment'] = '/' . $fileName;
                    $attachments['mm_request_type_id'] = $mm_request_type_id;
                    $attachments['file_ext'] = pathinfo($fileName, PATHINFO_EXTENSION);
                    $attachments['file_type'] = $file->getMimeType();
                    $attachments['file_size'] = $file->getSize();
                    $attachments['created_at'] = Carbon::now();
                    $attachments->save();

                    // insert file attachment correspondence
                    $this->add_file_correspondence_for_mm($mm_request_type_id, $subMmRequestType->id, $user, $fileName, 'dimensions');
                }
            }
            return redirect('admin/mm_request/'.$project_id.'/edit#'.$mm_request_type_id)
                ->with('success', __('New ('.$mm_request_type_id.') - Update Success'));
        }
        return redirect('admin/mm_request/'.$project_id.'/edit#'.$mm_request_type_id)
            ->with('error', __('Update Failed'));
    }

    public function add_price(Request $request){

        $user = auth()->user();
        $sub_mm_request_index = new SubMmRequestIndex();
        $sub_mm_request_index['task_id'] = $request['price_t_id'];
        $sub_mm_request_index['request_type'] = $request['price_request_type'];
        $sub_mm_request_index['author_id'] = $user->id;
        $sub_mm_request_index['status'] = 'action_requested';
        $sub_mm_request_index->save();
        $mm_request_type_id = $sub_mm_request_index->id;

        $subMmRequestType = new SubMmRequestType();
        $subMmRequestType['id'] = $request['price_t_id'];
        $subMmRequestType['author_id'] = $user->id;
        $subMmRequestType['type'] = 'price';
        $subMmRequestType['mm_request_type_id'] = $mm_request_type_id;
        $subMmRequestType['remark'] = $request['price_remark'];
        $subMmRequestType['materials'] = $request['price_materials'];
        $subMmRequestType['priority'] = $request['price_priority'];
        if(isset($request['price_priority']) && ($request['price_priority'] == 'Normal')){
            $subMmRequestType['due_date_urgent'] = null;
            $subMmRequestType['urgent_reason'] = null;
            $subMmRequestType['urgent_detail'] = null;
        }else{
            $subMmRequestType['due_date_urgent'] = $request['price_due_date_urgent'];
            $subMmRequestType['urgent_reason'] = $request['price_urgent_reason'];
            $subMmRequestType['urgent_detail'] = $request['price_urgent_detail'];
        }
        $subMmRequestType['due_date'] = $request['price_due_date'];
        if (isset($request['price_set_up_plant'])) {
            $subMmRequestType['set_up_plant'] = implode(',', $request['price_set_up_plant']);
        } else {
            $subMmRequestType['set_up_plant'] = '';
        }
        $subMmRequestType->save();
        $this->correspondence_add_mm_request_type($mm_request_type_id, 'price', $sub_mm_request_index);
        // add campaign_type_asset_attachments
        if($request->file('price_attachment')){
            foreach ($request->file('price_attachment') as $file) {
                $attachments = new MmRequestTypeAttachments();
//                $fileName = $file->storeAs('campaigns/'.$request['mm_request_c_id'].'/'.$asset_id, $originalName);
                $fileName = $this->file_exist_check_mm($file, $request['price_t_id'], $mm_request_type_id);
                $attachments['task_id'] = $request['price_t_id'];
                $attachments['type'] = 'attachment_file_' . $file->getMimeType();
                $attachments['author_id'] = $user->id;
                $attachments['attachment'] = '/' . $fileName;
                $attachments['mm_request_type_id'] = $mm_request_type_id;
                $attachments['file_ext'] = pathinfo($fileName, PATHINFO_EXTENSION);
                $attachments['file_type'] = $file->getMimeType();
                $attachments['file_size'] = $file->getSize();
                $attachments['created_at'] = Carbon::now();
                $attachments->save();
                $this->add_file_correspondence_for_mm($mm_request_type_id, $subMmRequestType->id, $user, $fileName, 'price');
            }
        }
        $project_id = $this->projectTaskIndexRepository->get_project_id_by_task_id($request['price_t_id']);

        // Send Notification
        $this->send_notification_action_request($project_id, $subMmRequestType, $mm_request_type_id);

        return redirect('admin/mm_request/'.$project_id.'/edit#'.$mm_request_type_id)
            ->with('success', __('Added the Price Type : ' . $mm_request_type_id));
    }

    public function edit_price(Request $request, $mm_request_type_id)
    {
        $param = $request->all();
        if (isset($param['set_up_plant'])) {
            $param['set_up_plant'] = implode(',', $param['set_up_plant']);
        } else {
            $param['set_up_plant'] = '';
        }

        $project_id = $this->projectTaskIndexRepository->get_project_id_by_task_id($param['price_t_id']);
        $subMmRequestType = $this->subMmRequestTypeRepository->findById($mm_request_type_id);

        if($this->subMmRequestTypeRepository->update($mm_request_type_id, $param)){

            $user = auth()->user();
            // insert into campaign note for correspondence
            $this->correspondence_update_mm_request_type('price', $param, $subMmRequestType, $user);

            if($request->file('attachment')){
                foreach ($request->file('attachment') as $file) {
                    $attachments = new MmRequestTypeAttachments();

                    $fileName = $this->file_exist_check_mm($file, $subMmRequestType->id, $mm_request_type_id);

                    $attachments['task_id'] = $subMmRequestType->id; // task_id
                    $attachments['type'] = 'attachment_file_' . $file->getMimeType();
                    $attachments['author_id'] = $user->id;
                    $attachments['attachment'] = '/' . $fileName;
                    $attachments['mm_request_type_id'] = $mm_request_type_id;
                    $attachments['file_ext'] = pathinfo($fileName, PATHINFO_EXTENSION);
                    $attachments['file_type'] = $file->getMimeType();
                    $attachments['file_size'] = $file->getSize();
                    $attachments['created_at'] = Carbon::now();
                    $attachments->save();

                    // insert file attachment correspondence
                    $this->add_file_correspondence_for_mm($mm_request_type_id, $subMmRequestType->id, $user, $fileName, 'price');
                }
            }
            return redirect('admin/mm_request/'.$project_id.'/edit#'.$mm_request_type_id)
                ->with('success', __('New ('.$mm_request_type_id.') - Update Success'));
        }
        return redirect('admin/mm_request/'.$project_id.'/edit#'.$mm_request_type_id)
            ->with('error', __('Update Failed'));
    }

    public function send_notification_action_request($project_id, $subRequestType, $mm_request_type_id)
    {
        // From : Division
        // Receiver : MDM Team

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
            'mail_subject'      => 'Action Requested : MM Request',
            'receiver'          => "Hello MDM Team,",
            'message'           => 'You got a new request from ' . $receiver_author_name . ".",
            'title'             => "Action Requested : MM Request",
            'project_id'        => $project_id,
            'project_title'     => $project_obj->name,
            'request_id'        => $mm_request_type_id,
            'request_type'      => $subRequestType['type'],
            'priority'          => $subRequestType['priority'],
            'due_date'          => $due_date_mail,
            'url'               => '/admin/mm_request/'.$project_id.'/edit#'.$mm_request_type_id,
        ];

        $group_rs = $this->userRepository->get_receiver_emails_by_team('MDM');
        foreach ($group_rs as $team_user) {
            $receiver_list[] = $team_user['email'];
        }

        /// Send to receivers
        Mail::to($receiver_list)->send(new TaskStatusNotification($details));

    }

    public function correspondence_update_mm_request_type($task_type, $new_param, $origin_param, $user)
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
        $change_line  = "<p>$user->first_name made a change to a <b style='color: #b91d19'>$task_type_</b> <b>(#$origin_param->mm_request_type_id)</b></p>";

        if(!empty($changed)){
            foreach ($changed as $label => $change) {

                $label = strtoupper(str_replace('_', ' ', $label));
                $from  = trim($change['original']); // Remove strip tags
                $to    = trim($change['new']);      // Remove strip tags

                $change_line .= "<div class='change_label'><p>$label:</p></div>"
                    . "<div class='change_to'><p>$to</p></div>"
                    . "<div class='change_from'><del><p>$from</p></del></div>";
            }
            $legal_request_note = new MmRequestNotes();
            $legal_request_note['id'] = $origin_param->id; // task_id
            $legal_request_note['user_id'] = $user->id;
            $legal_request_note['mm_request_type_id'] = $origin_param->mm_request_type_id;
            $legal_request_note['task_id'] = $origin_param->id; // task_id
            $legal_request_note['project_id'] = 0;
            $legal_request_note['note'] = $change_line;
            $legal_request_note['created_at'] = Carbon::now();
            $legal_request_note->save();
        }
    }

    public function get_request_type_param($task_type, $data)
    {
        if($task_type == 'new') {
            $new = array(
                'remark' => $data['remark'],
                'materials' => $data['materials'],
                'priority' => $data['priority'],
                'due_date' => $data['due_date'],
                'set_up_plant' => $data['set_up_plant'],
                'assignee' => isset($data['assignee']) ? $data['assignee'] : '',
            );
            return $new;
        }else if($task_type == 'update'){
            $new = array(
                'remark' => $data['remark'],
                'materials' => $data['materials'],
                'priority' => $data['priority'],
                'due_date' => $data['due_date'],
                'set_up_plant' => $data['set_up_plant'],
                'assignee' => isset($data['assignee']) ? $data['assignee'] : '',
            );
            return $new;
        }else if($task_type == 'dimensions'){
            $new = array(
                'remark' => $data['remark'],
                'materials' => $data['materials'],
                'priority' => $data['priority'],
                'due_date' => $data['due_date'],
                'set_up_plant' => $data['set_up_plant'],
                'assignee' => isset($data['assignee']) ? $data['assignee'] : '',
            );
            return $new;
        }else if($task_type == 'price'){
            $new = array(
                'remark' => $data['remark'],
                'materials' => $data['materials'],
                'priority' => $data['priority'],
                'due_date' => $data['due_date'],
                'set_up_plant' => $data['set_up_plant'],
                'assignee' => isset($data['assignee']) ? $data['assignee'] : '',
            );
            return $new;
        }

    }

    public function correspondence_add_mm_request_type($mm_request_type_id, $type_name, $sub_mm_request_index)
    {
        $user = auth()->user();
        $type_name = strtoupper($type_name);
        $change_line = "<p>$user->first_name requested <b style='color: #b91d19'>$type_name</b> <b>(#$mm_request_type_id)</b></p>";

        $mm_request_note = new MmRequestNotes();
        $mm_request_note['id'] = $sub_mm_request_index->task_id;
        $mm_request_note['user_id'] = $user->id;
        $mm_request_note['mm_request_type_id'] = $mm_request_type_id;
        $mm_request_note['task_id'] = $sub_mm_request_index->task_id;
        $mm_request_note['project_id'] = 0;
        $mm_request_note['note'] = $change_line;
        $mm_request_note['created_at'] = Carbon::now();
        $mm_request_note->save();
    }

    public function add_file_correspondence_for_mm($mm_request_type_id, $task_id, $user, $file_type, $request_type)
    {
        // Insert into project note for correspondence (attachment file)
        $request_type_ =  strtoupper(str_replace('_', ' ', $request_type));

        $change_line  = "<p>$user->first_name has added a new attachment ($file_type) to <b style='color: #b91d19'>$request_type_</b> <b>(#$mm_request_type_id)</b></p>";

        $mm_request_note = new MmRequestNotes();
        $mm_request_note['id'] = $task_id;
        $mm_request_note['user_id'] = $user->id;
        $mm_request_note['mm_request_type_id'] = $mm_request_type_id;
        $mm_request_note['task_id'] = $task_id;
        $mm_request_note['note'] = $change_line;
        $mm_request_note['created_at'] = Carbon::now();
        $mm_request_note->save();
    }

    public function file_exist_check_mm($file, $task_id, $mm_request_type_id)
    {
        $originalName = $file->getClientOriginalName();
        $destinationFolder = 'storage/mm_request/'.$task_id.'/'.$mm_request_type_id.'/'.$originalName;

        // If exist same name file, add numberning for version control
        if(file_exists($destinationFolder)){
            if ($pos = strrpos($originalName, '.')) {
                $new_name = substr($originalName, 0, $pos);
                $ext = substr($originalName, $pos);
            }
            $newpath = 'storage/mm_request/'.$task_id.'/'.$mm_request_type_id.'/'.$originalName;
            $uniq_no = 1;
            while (file_exists($newpath)) {
                $tmp_name = $new_name .'_v'. $uniq_no . $ext;
                $newpath = 'storage/mm_request/'.$task_id.'/'.$mm_request_type_id.'/'.$tmp_name;
                $uniq_no++;
            }
            $file_name = $tmp_name;
        }else{
            $file_name = $originalName;
        }

        $fileName =$file->storeAs('mm_request/'.$task_id.'/'.$mm_request_type_id, $file_name);
        return $fileName;
    }

    public function actionReSubmit($id)
    {
        $sub_mm_request_index = $this->subMmRequestIndexRepository->findById($id);
        $param['status'] = 'action_requested';
        $param['updated_at'] = Carbon::now();
        $t_id = $sub_mm_request_index->task_id;
        $project_id = $this->projectTaskIndexRepository->get_project_id_by_task_id($t_id);
        if($this->subMmRequestIndexRepository->update($id, $param)){
            $subMmRequest_obj = $this->subMmRequestTypeRepository->get_sub_mm_request_by_mm_request_type_id($id);
            $current_revision_cnt = $subMmRequest_obj['revision_cnt'];
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
            $this->subMmRequestTypeRepository->update($id, $t_param);
            $this->mm_status_correspondence($t_id, $project_id, $id, $sub_mm_request_index->request_type, 'Action Re-Submit');

            // Send Notification
            $this->send_notification_resubmit($project_id, $id, $t_param);

            echo '/admin/mm_request/'.$project_id.'/edit#'.$id;
        }else{
            echo 'fail';
        }
    }

    public function send_notification_resubmit($project_id, $mm_request_type_id, $t_param)
    {
        // From : Task Requester
        // Receiver : MDM Team Assignee

        $project_obj = $this->projectRepository->findById($project_id);
        $subRequestType = $this->subMmRequestTypeRepository->findById($mm_request_type_id);

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
            'template'          => 'emails.task.resubmit',
            'mail_subject'      => 'Action Review (Resubmit) : MM Request',
            'receiver'          => 'Hello ' . $task_author_name . ', ',
            'message'           => 'You got a new request from ' . $task_author_name,
            'title'             => "Action Review (Resubmit) : MM Request",
            'project_id'        => $project_id,
            'project_title'     => $project_obj->name,
            'request_id'        => $subRequestType['mm_request_type_id'],
            'request_type'      => $subRequestType['type'],
            'priority'          => 'Revision',
            'due_date'          => $t_param['due_date_revision'],
            'assignee'          => $assignee_name,
            'url'               => '/admin/mm_request/'.$project_id.'/edit#'.$subRequestType['mm_request_type_id'],
        ];

        $receiver_list[] = $subRequestType->assignee_obj->email;
        Mail::to($receiver_list)->send(new TaskStatusNotification($details));

    }

    public function actionInProgress($id)
    {
        $sub_mm_request_index = $this->subMmRequestIndexRepository->findById($id);
        $param['status'] = 'in_progress';
        $param['updated_at'] = Carbon::now();
        $user = auth()->user();
        $param_type['assignee'] = $user->id;
        if($this->subMmRequestIndexRepository->update($id, $param)){
            $this->subMmRequestTypeRepository->update($id, $param_type);
            $t_id = $sub_mm_request_index->task_id;
            $project_id = $this->projectTaskIndexRepository->get_project_id_by_task_id($t_id);
            $this->mm_status_correspondence($t_id, $project_id, $sub_mm_request_index->id, $sub_mm_request_index->request_type,'In Progress');
            echo '/admin/mm_request/'.$project_id.'/edit#'.$id;
        }else{
            echo 'fail';
        }
    }

    public function actionReview($id)
    {
        $project_task_index = $this->projectTaskIndexRepository->findById($id);
        $param['status'] = 'action_review';
        $param['updated_at'] = Carbon::now();
        $t_id = $project_task_index->id;
        $project_id = $project_task_index->project_id;
        if($this->projectTaskIndexRepository->update($id, $param)){
            $this->mm_status_correspondence($t_id, $project_id, 'Action Review');
            echo '/admin/mm_request/'.$project_id.'/edit#'.$id;
        }else{
            echo 'fail';
        }
    }

    public function actionComplete($id)
    {
        $sub_mm_request_index = $this->subMmRequestIndexRepository->findById($id);
        $param['status'] = 'action_completed';
        $param['updated_at'] = Carbon::now();
        $t_id = $sub_mm_request_index->task_id;
        $project_id = $this->projectTaskIndexRepository->get_project_id_by_task_id($t_id);
        if($this->subMmRequestIndexRepository->update($id, $param)){

            $this->mm_status_correspondence($t_id, $project_id, $sub_mm_request_index->id, $sub_mm_request_index->request_type, 'Action Completed');

            // Send Notification
            $this->send_notification_complete($project_id, $id);

            echo '/admin/mm_request/'.$project_id.'/edit#'.$id;
        }else{
            echo 'fail';
        }

    }

    public function send_notification_complete($project_id, $sub_request_id)
    {
        // From : MDM Team Assignee
        // Receiver :  Task Requester
        $project_obj = $this->projectRepository->findById($project_id);
        $subRequestType = $this->subMmRequestTypeRepository->findById($sub_request_id);

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
            'template'          => 'emails.task.completed',
            'mail_subject'      => 'Action Completed : MM Request',
            'receiver'          => 'Hello ' . $task_author_name . ', ',
            'message'           => 'The request has been successfully completed.',
            'title'             => "Action Completed : MM Request",
            'project_id'        => $project_id,
            'project_title'     => $project_obj->name,
            'request_id'        => $subRequestType['mm_request_type_id'],
            'request_type'      => $subRequestType['type'],
            'assignee'          => $assignee_name,
            'url'               => '/admin/mm_request/'.$project_id.'/edit#'.$subRequestType['mm_request_type_id'],
        ];

        $receiver_list[] = $subRequestType->author_obj->email;
        Mail::to($receiver_list)->send(new TaskStatusNotification($details));

    }

    public function revision_reason(Request $request)
    {
        $param = $request->all();
        $request_type_id = $param['request_type_id'];
        $revision_reason = $param['revision_reason'];
        $revision_reason_note = $param['revision_reason_note'];
        $sub_mm_request_index = $this->subMmRequestIndexRepository->findById($request_type_id);
        $t_id = $sub_mm_request_index->task_id;
        $sub_task_type = strtoupper($sub_mm_request_index->request_type);
        $project_id = $this->projectTaskIndexRepository->get_project_id_by_task_id($t_id);

        $params['status'] = 'action_review';
        $params['updated_at'] = Carbon::now();
        $params['revision_reason'] = $revision_reason;
        $params['revision_reason_note'] = $revision_reason_note;
        if($this->subMmRequestIndexRepository->update($request_type_id, $params)){

            $user = auth()->user();
            $change_line  = "<p>$user->first_name updated the status to <b>Revision</b> for <b style='color: #b91d19;'>$sub_task_type</b><b>(#$request_type_id)</b>
                            <br> <b style='color: black;'>Revision Reason : $revision_reason </b>
                            <br> <b style='color: black;'>$revision_reason_note </b>
                            </p>";
            $note = new MmRequestNotes();
            $note['id'] = $project_id;
            $note['user_id'] = $user->id;
            $note['mm_request_type_id'] = $request_type_id;
            $note['task_id'] = $t_id;
            $note['project_id'] = $project_id;
            $note['note'] = $change_line;
            $note['created_at'] = Carbon::now();
            $note->save();

            // Send Notification
            $subRequestType = $this->subMmRequestTypeRepository->findById($request_type_id);
            $this->send_notification_revision($project_id, $subRequestType, $param);

            return redirect('admin/mm_request/'.$project_id.'/edit#'.$request_type_id)
                ->with('success', __('Data has been Updated.'));
        }

        return redirect('admin/mm_request/'.$project_id.'/edit#'.$request_type_id)
            ->with('error', __('Data updates Failed'));
    }

    public function send_notification_revision($project_id, $subRequestType, $param)
    {
        // From : MDM Team
        // Receiver : Task Requester
        $project_obj = $this->projectRepository->findById($project_id);
        if($subRequestType['priority'] == 'Normal'){
            $due_date_mail = $subRequestType['due_date'];
        }else{
            $due_date_mail = $subRequestType['due_date_urgent'];
        }

        // Task Creator
        $task_author_name = $subRequestType->author_obj->first_name . ' ' . $subRequestType->author_obj->last_name;

        $details = [
            'mail_subject'      => 'Action Review (Revision) : MM Request',
            'template'          => 'emails.task.revision',
            'receiver'          => 'Hello ' . $task_author_name . ',',
            'message'           => "You got a new request from MDM Team.",
            'title'             => 'Action Review (Revision) : MM Request',
            'project_id'        => $project_id,
            'project_title'     => $project_obj->name,
            'request_id'        => $param['request_type_id'],
            'request_type'      => $subRequestType['type'],
            'priority'          => $subRequestType['priority'],
            'due_date'          => $due_date_mail,
            'reason'            => $param['revision_reason'],
            'note'              => $param['revision_reason_note'],
            'url'               => '/admin/mm_request/'.$project_id.'/edit#'.$param['request_type_id'],
        ];

        $receiver_list[] = $subRequestType->author_obj->email;
        Mail::to($receiver_list)->send(new TaskStatusNotification($details));
    }

    public function mm_status_correspondence($t_id, $p_id, $mm_request_type_id, $task_type, $status)
    {
        // Insert into Project note for correspondence (attachment file)
        $user = auth()->user();
        $task_type_ =  strtoupper(str_replace('_', ' ', $task_type));
        $change_line  = "<p>$user->first_name updated the status to <b>$status</b> for <b style='color: #b91d19;'>$task_type_</b><b>(#$mm_request_type_id)</b></p>";

        $note = new MmRequestNotes();
        $note['id'] = $t_id;
        $note['user_id'] = $user->id;
        $note['mm_request_type_id'] = $mm_request_type_id;
        $note['task_id'] = $t_id;
        $note['project_id'] = $p_id;
        $note['note'] = $change_line;
        $note['created_at'] = Carbon::now();
        $note->save();
    }

    public function correspondence_add_new_task($p_id, $task_name, $projectTaskIndex)
    {
        $user = auth()->user();
        $task_name = strtoupper($task_name);
        $change_line = "<p><b style='color: #b91d19;'>$task_name</b> <b>(#$projectTaskIndex->id)</b> has been created by $user->first_name. </p>";
        $project_note = new ProjectNotes();
        $project_note['id'] = $p_id;
        $project_note['user_id'] = $user->id;
        $project_note['task_id'] = $projectTaskIndex->id;
        $project_note['type'] = $projectTaskIndex->type;
        $project_note['note'] = $change_line;
        $project_note['created_at'] = Carbon::now();
        $project_note->save();
    }

    public function correspondence_new_mm_request($p_id, $task_name, $projectTaskIndex)
    {
        $user = auth()->user();
        $task_name = strtoupper($task_name);
        $change_line = "<p><b style='color: #b91d19;'>$task_name</b> <b>(#$projectTaskIndex->id)</b> has been created by $user->first_name. </p>";
        $qra_request_note = new MmRequestNotes();
        $qra_request_note['id'] = $projectTaskIndex->id;
        $qra_request_note['user_id'] = $user->id;
        $qra_request_note['mm_request_type_id'] = 0;
        $qra_request_note['task_id'] = $projectTaskIndex->id;
        $qra_request_note['project_id'] = $p_id;
        $qra_request_note['note'] = $change_line;
        $qra_request_note['created_at'] = Carbon::now();
        $qra_request_note->save();
    }

    public function add_correspondence($task_type, $new_param, $origin_param, $user)
    {
        // Insert into campaign note for correspondence

        $new = $this->get_task_param($task_type, $new_param);
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

                    }else{
                        $changed[$key]['new'] = $new[$key];
                        $changed[$key]['original'] = $origin[$key];
                    }
                }
            }
        }

        if( strpos($task_type, "_") !== false) {
            // task naming logic //
            $task_type_temp = explode("_", $task_type);
            $task_type_ = strtoupper($task_type_temp[0]) . " " . ucwords($task_type_temp[1]);
        }else{
            $task_type_= ucwords($task_type);
        }

        $task_type_ = strtoupper($task_type_);

        $change_line  = "<p>$user->first_name made a change to a <b style='color: #b91d19;'>$task_type_ </b><b>(#$origin_param->task_id)</b></p>";

        if(!empty($changed)){
            foreach ($changed as $label => $change) {

                $label = strtoupper(str_replace('_', ' ', $label));
                $from  = trim($change['original']); // Remove strip tags
                $to    = trim($change['new']);      // Remove strip tags

                $change_line .= "<div class='change_label'><p>$label:</p></div>"
                    . "<div class='change_to'><p>$to</p></div>"
                    . "<div class='change_from'><del><p>$from</p></del></div>";
            }

            $qra_request_note = new MmRequestNotes();
            $qra_request_note['id'] = $origin_param->id;
            $qra_request_note['user_id'] = $user->id;
            $qra_request_note['mm_request_type_id'] = 0;
            $qra_request_note['task_id'] = $origin_param->task_id;
            $qra_request_note['project_id'] = $origin_param->id;
            $qra_request_note['note'] = $change_line;
            $qra_request_note['created_at'] = Carbon::now();
            $qra_request_note->save();

        }
    }

    public function get_task_param($task_type, $data)
    {
        if ($task_type == 'mm_request') {
            $new = array(
                'assignee' => $data['assignee'],
                'materials' => $data['materials'],
                'priority' => $data['priority'],
                'due_date' => $data['due_date'],
                'request_type' => $data['request_type'],
                'set_up_plant' => $data['set_up_plant'],
                'remark' => $data['remark'],
            );
            return $new;
        }
    }

    public function add_file_correspondence_for_task($qc_request, $user, $file_type, $task_type)
    {
        // Insert into project note for correspondence (attachment file)
        $task_type_ =  strtoupper(str_replace('_', ' ', $task_type));

        $change_line  = "<p>$user->first_name has added a new attachment <br><b>$file_type</b><br>to <b style='color: #b91d19;'>$task_type_</b> <b>(#$qc_request->task_id)</b></p>";

        $qra_request_note = new MmRequestNotes();
        $qra_request_note['id'] = $qc_request->id;
        $qra_request_note['user_id'] = $user->id;
        $qra_request_note['mm_request_type_id'] = 0;
        $qra_request_note['task_id'] = $qc_request->task_id;
        $qra_request_note['project_id'] = $qc_request->id;
        $qra_request_note['note'] = $change_line;
        $qra_request_note['created_at'] = Carbon::now();
        $qra_request_note->save();

    }

    public function file_exist_check($file, $project_id, $task_id)
    {
        $originalName = $file->getClientOriginalName();
        $destinationFolder = 'storage/mm_request/'.$project_id.'/'.$task_id.'/'.$originalName;

        // If exist same name file, add numberning for version control
        if(file_exists($destinationFolder)){
            if ($pos = strrpos($originalName, '.')) {
                $new_name = substr($originalName, 0, $pos);
                $ext = substr($originalName, $pos);
            }
            $newpath = 'storage/mm_request/'.$project_id.'/'.$task_id.'/'.$originalName;
            $uniq_no = 1;
            while (file_exists($newpath)) {
                $tmp_name = $new_name .'_v'. $uniq_no . $ext;
                $newpath = 'storage/mm_request/'.$project_id.'/'.$task_id.'/'.$tmp_name;
                $uniq_no++;
            }
            $file_name = $tmp_name;
        }else{
            $file_name = $originalName;
        }

        $fileName =$file->storeAs('mm_request/'.$project_id.'/'.$task_id, $file_name);
        return $fileName;
    }

    public function fileRemove($id)
    {
        $projectTypeTaskAttachment = $this->projectTaskFileAttachmentsRepository->findById($id);

        $file_name = $projectTypeTaskAttachment->attachment;
        $task_id = $projectTypeTaskAttachment->task_id;

        $user = auth()->user();

        if($projectTypeTaskAttachment->delete()){

            $taskIndex = $this->projectTaskIndexRepository->findById($task_id);
            $task_type =  strtoupper(str_replace('_', ' ', $taskIndex->type));

            $change_line = "<p>$user->first_name has removed a attachment <br/><b>$file_name<b><br>on <b style='color: #b91d19'>$task_type</b> <b>(#$task_id)</b></p>";

            $request_note = new MmRequestNotes();
            $request_note['id'] = $task_id; // task_id
            $request_note['user_id'] = $user->id;
            $request_note['mm_request_type_id'] = 0;
            $request_note['task_id'] = $task_id; // task_id
            $request_note['note'] = $change_line;
            $request_note['created_at'] = Carbon::now();
            $request_note->save();

            echo 'success';
        }else{
            echo 'fail';
        }
    }

    public function requestTypeRemovePermissionCheck($request_type_id){

        $user = auth()->user();
        if($user->role == 'Admin') return true; // admin okay

        $obj = $this->subMmRequestIndexRepository->findById($request_type_id);
        if($obj->author_id != $user->id){
            return false;
        }
        return true;
    }

    public function requestTypeRemove($request_type_id, $type)
    {
        $obj = $this->subMmRequestIndexRepository->findById($request_type_id);
        $t_id = $obj->task_id;
        $p_id = $this->projectTaskIndexRepository->get_project_id_by_task_id($t_id);
        if($this->requestTypeRemovePermissionCheck($request_type_id)){
            // Delete from sub_qra_request_index, sub_qra_request_type tables
            $this->subMmRequestIndexRepository->delete($request_type_id);
            $this->subMmRequestTypeRepository->delete($request_type_id);

            // Add correspondence for asset Removed
            $this->mm_remove_correspondence($t_id, $p_id, $type, $request_type_id);

            echo '/admin/mm_request/'.$p_id.'/edit';
        }else{
            echo 'fail';
        }
    }

    public function mm_remove_correspondence($t_id, $p_id, $task_type, $request_type_id)
    {
        // Insert into Project note for correspondence (attachment file)
        $user = auth()->user();
        $task_type_ =  strtoupper(str_replace('_', ' ', $task_type));
        $change_line  = "<b style='color: #b91d19;'>$task_type_ </b><b>(#$request_type_id)</b> has been removed by $user->first_name";

        $note = new MmRequestNotes();
        $note['id'] = $t_id;
        $note['user_id'] = $user->id;
        $note['mm_request_type_id'] = $request_type_id;
        $note['task_id'] = $t_id;
        $note['project_id'] = $p_id;
        $note['note'] = $change_line;
        $note['created_at'] = Carbon::now();
        $note->save();
    }

    public function add_note(Request $request)
    {
        $param = $request->all();
        $user = auth()->user();

        $project_id = $param['p_id'];
        $t_id = $param['t_id'];
        $p_title = $param['p_title'];
        $email_list = $param['email_list'];

        $note = new MmRequestNotes();
        $note['id'] = $project_id;
        $note['project_id'] = $project_id;
        $note['mm_request_type_id'] = 0;
        $note['user_id'] = $user->id;
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
                'url' => '/admin/mm_request/' . $project_id . '/edit#' . $t_id,
            ];
            $receiver_list = explode(',', $email_list);
            Mail::to($receiver_list)->send(new TaskStatusNotification($details));
        }
        $this->data['currentAdminMenu'] = 'mm_request';

        return redirect('admin/mm_request/'.$project_id.'/edit')
            ->with('success', __('Data has been Updated.'));
    }

}
