<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Http\Controllers\NotifyController;
use App\Http\Requests\Admin\DevRequest;
use App\Mail\DevMessage;
use App\Mail\NewRequest;
use App\Mail\NoteProject;
use App\Mail\TaskStatusNotification;
use App\Models\DevNotes;
use App\Models\MmRequestNotes;
use App\Models\NpdPlannerRequestNotes;
use App\Models\NpdPoRequestNotes;
use App\Models\ProjectNotes;
use App\Models\ProjectTaskIndex;
use App\Models\ProjectTypeTaskAttachments;
use App\Models\TaskTypeMmRequest;
use App\Models\TaskTypeNpdPoRequest;
use App\Models\User;
use App\Repositories\Admin\AssetNotificationUserRepository;
use App\Repositories\Admin\AssetOwnerAssetsRepository;
use App\Repositories\Admin\BrandRepository;
use App\Repositories\Admin\MmRequestNotesRepository;
use App\Repositories\Admin\MmRequestRepository;
use App\Repositories\Admin\NpdPlannerRequestNotesRepository;
use App\Repositories\Admin\NpdPoRequestNotesRepository;
use App\Repositories\Admin\TaskTypeNpdPlannerRequestRepository;
use App\Repositories\Admin\TaskTypeNpdPoRequestRepository;
use App\Repositories\Admin\PlantRepository;
use App\Repositories\Admin\ProjectNotesRepository;
use App\Repositories\Admin\ProjectRepository;
use App\Repositories\Admin\ProjectTaskFileAttachmentsRepository;
use App\Repositories\Admin\ProjectTaskIndexRepository;
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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NpdPoRequestController extends Controller
{
    Private $projectRepository;
    Private $projectTaskIndexRepository;
    Private $taskTypeNpdPoRequestRepository;
    Private $taskTypeNpdPlannerRequestRepository;
    private $projectTaskFileAttachmentsRepository;
    Private $projectNotesRepository;
    Private $mmRequestNotesRepository;
    Private $npdPoRequestNotesRepository;
    Private $npdPlannerRequestNotesRepository;
    Private $mmRequestRepository;
    Private $teamRepository;
    Private $plantRepository;
    Private $brandRepository;
    private $userRepository;


    public function __construct(
        ProjectRepository $projectRepository,
        ProjectTaskIndexRepository $projectTaskIndexRepository,
        TaskTypeMmRequestRepository $taskTypeMmRequestRepository,
        TaskTypeNpdPoRequestRepository $taskTypeNpdPoRequestRepository,
        TaskTypeNpdPlannerRequestRepository $taskTypeNpdPlannerRequestRepository,
        ProjectTaskFileAttachmentsRepository $projectTaskFileAttachmentsRepository,
        ProjectNotesRepository $projectNotesRepository,
        MmRequestNotesRepository $mmRequestNotesRepository,
        NpdPoRequestNotesRepository $npdPoRequestNotesRepository,
        NpdPlannerRequestNotesRepository $npdPlannerRequestNotesRepository,
        MmRequestRepository $mmRequestRepository,
        TeamRepository $teamRepository,
        PlantRepository $plantRepository,
        BrandRepository $brandRepository,
        UserRepository $userRepository)
    {
        parent::__construct();

        $this->projectRepository = $projectRepository;
        $this->projectTaskIndexRepository = $projectTaskIndexRepository;
        $this->taskTypeMmRequestRepository = $taskTypeMmRequestRepository;
        $this->taskTypeNpdPoRequestRepository = $taskTypeNpdPoRequestRepository;
        $this->taskTypeNpdPlannerRequestRepository = $taskTypeNpdPlannerRequestRepository;
        $this->projectTaskFileAttachmentsRepository = $projectTaskFileAttachmentsRepository;
        $this->projectNotesRepository = $projectNotesRepository;
        $this->mmRequestNotesRepository = $mmRequestNotesRepository;
        $this->npdPoRequestNotesRepository = $npdPoRequestNotesRepository;
        $this->npdPlannerRequestNotesRepository = $npdPlannerRequestNotesRepository;
        $this->mmRequestRepository = $mmRequestRepository;
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
        $this->data['currentAdminMenu'] = 'npd_po_request';

        $user = auth()->user();

        if($user->team == 'Purchasing' || $user->team == 'Admin') {
            $cur_user = ' ';
        }else{
            $cur_user = $this->userRepository->getPageAccess($user);
        }

        if(isset($_GET[''])) {
            $buyer = $param['buyer'];
        }else{
            $buyer = !empty($param['buyer']) ? $param['buyer'] : '';
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

        $this->data['task_list_action_requested'] = $this->taskTypeNpdPoRequestRepository->get_action_requested_list($cur_user, $buyer, $team, $brand);
        $this->data['task_list_in_progress'] = $this->taskTypeNpdPoRequestRepository->get_in_progress_list($cur_user, $buyer, $team, $brand);
        $this->data['task_list_action_review'] = $this->taskTypeNpdPoRequestRepository->get_action_review_list($cur_user, $buyer, $team, $brand);
        $this->data['task_list_action_completed'] = $this->taskTypeNpdPoRequestRepository->get_action_completed_list($cur_user, $buyer, $team, $brand);

        $this->data['buyer'] = $buyer;
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

        $this->data['npd_po_request_buyer_list'] = $this->userRepository->getNpdPoBuyerList();
        $this->data['teams'] =$this->teamRepository->findAll($team_options);
        $this->data['brands'] =$this->brandRepository->findAll($brand_options);

        return view('admin.npd_po_request.index', $this->data);
    }

    public function index_list(Request $request)
    {
        $param = $request->all();
        $this->data['currentAdminMenu'] = 'npd_po_request_list';

        $user = auth()->user();
        if($user->team == 'Purchasing' || $user->team == 'Admin') {
            $cur_user = ' ';
        }else{
            $cur_user = $this->userRepository->getPageAccess($user);
        }
        $str = !empty($param['q']) ? $param['q'] : '';
        if(isset($_GET[''])) {
            $buyer = $param['buyer'];
        }else{
            $buyer = !empty($param['buyer']) ? $param['buyer'] : '';
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

        $this->data['task_list'] = $this->taskTypeNpdPoRequestRepository->get_task_list($cur_user, $str, $buyer, $team, $status);

        $this->data['buyer'] = $buyer;
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
        $this->data['npd_po_request_buyer_list'] = $this->userRepository->getNpdPoBuyerList();
        $this->data['teams'] =$this->teamRepository->findAll($team_options);
        $this->data['status_list'] = [
            'action_requested',
            'in_progress',
            'action_review',
            'action_completed'
        ];

        return view('admin.npd_po_request.index_list', $this->data);
    }

    public function index_temp_list(Request $request)
    {
        $param = $request->all();
        $this->data['currentAdminMenu'] = 'npd_po_request_temp_list';

        $user = auth()->user();
        if($user->team == 'Purchasing' || $user->team == 'Admin') {
            $cur_user = ' ';
        }else{
            $cur_user = $this->userRepository->getPageAccess($user);
        }

        $this->data['task_list'] = $this->taskTypeNpdPoRequestRepository->get_task_temp_list($cur_user);

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
        $this->data['npd_po_request_buyer_list'] = $this->userRepository->getNpdPoBuyerList();
        $this->data['teams'] =$this->teamRepository->findAll($team_options);
        $this->data['status_list'] = [
            'action_requested',
            'in_progress',
            'action_review',
            'action_completed'
        ];

        return view('admin.npd_po_request.index_temp_list', $this->data);
    }


    public function edit($id)
    {
        $this->data['currentAdminMenu'] = 'npd_po_request';

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

        $rs = $this->taskTypeNpdPoRequestRepository->get_task_id_for_npd_po($id);

        if($rs){
            // if npd_po_request exist
            $this->data['npd_po_request_list'] = $npd_po_request_list = $this->taskTypeNpdPoRequestRepository->get_npd_po_request_list_by_task_id($rs->id);

            // task_detail
            if(sizeof($npd_po_request_list)>0){
                foreach ($npd_po_request_list as $k => $npd_po_request){
                    $p_id = $npd_po_request->project_id;
                    $t_id = $rs->id;
//                    $task_detail = $this->taskTypeNpdPoRequestRepository->findById($t_id);
//                    $task_detail = $this->projectTaskIndexRepository->get_task_detail($p_id, $t_id, 'npd_po_request');
                    $npd_po_request_list[$k]->detail = $npd_po_request_list;
                    $task_files = $this->projectTaskFileAttachmentsRepository->findAllByTaskId($t_id);
                    $npd_po_request_list[$k]->files = $task_files;
                }
            }
            $this->data['task_status'] = $npd_po_request_list[0]->status;

            // Project_notes
            $options = [
                'id' => $rs->id,
                'order' => [
                    'created_at' => 'desc',
                ]
            ];

            $correspondences = $this->npdPoRequestNotesRepository->findAll($options);
            $this->data['correspondences'] = $correspondences;

        }else{

            // if qc_request not exist
            $this->data['npd_po_request_list'] = null;
            $this->data['correspondences'] = null;
            $this->data['task_status'] = null;
        }

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


        /////////// NPD PO Request Task ////////////////////////////////////////////
        $this->data['priorities'] = [
            'Normal', 'Urgent'
        ];
        $this->data['yes_or_no_list'] = [
            'YES',
            'NO'
        ];
        $this->data['price_set_up_list'] = [
            'Final Price',
            'Temporary Price (Approved by Division Leader)'
        ];
        $this->data['set_up_plants_list'] = $this->plantRepository->get_set_up_plants();

        $this->data['po_buyer_list'] = $this->userRepository->getPoBuyerList();
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
        return view('admin.npd_po_request.form', $this->data);
    }

    public function add_npd_po_request(Request $request)
    {
        $param = $request->request->all();

        // add project_task_index
        $projectTaskIndex = new ProjectTaskIndex();
        $projectTaskIndex['project_id'] = $param['npd_po_request_p_id'];
        $projectTaskIndex['type'] = $param['npd_po_request_task_type'];
        $projectTaskIndex['status'] = 'action_requested';

        $user = auth()->user(); // asset_author_id
        $projectTaskIndex['author_id'] = $user->id;
        $projectTaskIndex->save();
        $task_id = $projectTaskIndex->id;

        // add task_type_npd_po_request
        $taskTypeNpdPoRequest = new TaskTypeNpdPoRequest();
        $taskTypeNpdPoRequest['id'] = $param['npd_po_request_p_id']; //project_id
        $taskTypeNpdPoRequest['author_id'] = $param['npd_po_request_author_id'];
        $taskTypeNpdPoRequest['type'] = $param['npd_po_request_task_type'];
        $taskTypeNpdPoRequest['request_detail'] = $param['npd_po_request_request_detail'];
        $taskTypeNpdPoRequest['priority'] = $param['npd_po_request_priority'];
        if($taskTypeNpdPoRequest['priority'] == 'Urgent'){
            $taskTypeNpdPoRequest['due_date_urgent'] = $param['npd_po_request_due_date_urgent'];
            $taskTypeNpdPoRequest['urgent_reason'] = $param['npd_po_request_urgent_reason'];
        }
        $taskTypeNpdPoRequest['due_date'] = $param['npd_po_request_due_date'];
        $taskTypeNpdPoRequest['source_list_completion'] = $param['npd_po_request_source_list_completion'];
        $taskTypeNpdPoRequest['info_record_completion'] = $param['npd_po_request_info_record_completion'];
        $taskTypeNpdPoRequest['price_set_up'] = $param['npd_po_request_price_set_up'];
        $taskTypeNpdPoRequest['forecast_completion'] = $param['npd_po_request_forecast_completion'];
        $taskTypeNpdPoRequest['materials'] = $param['npd_po_request_materials'];
        $taskTypeNpdPoRequest['total_sku_count'] = $param['npd_po_request_total_sku_count'];

        if (isset($request['npd_po_request_set_up_plant'])) {
            $taskTypeNpdPoRequest['set_up_plant'] = implode(',', $param['npd_po_request_set_up_plant']);
        } else {
            $taskTypeNpdPoRequest['set_up_plant'] = '';
        }

        $taskTypeNpdPoRequest['vendor_code'] = $param['npd_po_request_vendor_code'];
        $taskTypeNpdPoRequest['vendor_name'] = $param['npd_po_request_vendor_name'];
        $taskTypeNpdPoRequest['second_vendor_code'] = $param['npd_po_request_second_vendor_code'];
        $taskTypeNpdPoRequest['second_vendor_name'] = $param['npd_po_request_second_vendor_name'];
        $taskTypeNpdPoRequest['est_ready_date'] = $param['npd_po_request_est_ready_date'];
        $taskTypeNpdPoRequest['buyer'] = $param['npd_po_request_buyer'];

        $taskTypeNpdPoRequest['created_at'] = Carbon::now();
        $taskTypeNpdPoRequest['task_id'] = $task_id;
        $taskTypeNpdPoRequest->save();

        // new correspondence when adding asset
        $this->correspondence_add_new_task($projectTaskIndex['project_id'], 'NPD PO Request', $projectTaskIndex);

        // Correspondence for Onsite QC Request Type
        $this->correspondence_new_npd_po_request($projectTaskIndex['project_id'], 'NPD PO Request', $projectTaskIndex);

        // add campaign_type_asset_attachments
        if($request->file('npd_po_request_p_attachment')){
            foreach ($request->file('npd_po_request_p_attachment') as $file) {

                $project_type_task_attachments = new ProjectTypeTaskAttachments();

//                $fileName = $file->storeAs('campaigns/'.$request['qc_request_c_id'].'/'.$asset_id, $originalName);
                $fileName = $this->file_exist_check($file, $param['npd_po_request_p_id'], $task_id);

                $project_type_task_attachments['project_id'] = $param['npd_po_request_p_id'];
                $project_type_task_attachments['task_id'] = $task_id;
                $project_type_task_attachments['type'] = 'attachment_file_' . $file->getMimeType();
                $project_type_task_attachments['author_id'] = $param['npd_po_request_author_id'];
                $project_type_task_attachments['attachment'] = '/' . $fileName;
                $project_type_task_attachments['file_ext'] = pathinfo($fileName, PATHINFO_EXTENSION);
                $project_type_task_attachments['file_type'] = $file->getMimeType();
                $project_type_task_attachments['file_size'] = $file->getSize();
                $project_type_task_attachments['created_at'] = Carbon::now();
                $project_type_task_attachments->save();
            }
        }

        // Send Notification
//        $this->send_notification_action_request($user, $param['npd_po_request_p_id'], $taskTypeNpdPoRequest, $task_id);

        return redirect('admin/npd_po_request/'.$param['npd_po_request_p_id'].'/edit#'.$task_id)
            ->with('success', __('Added the NPD PO Request Task : ' . $task_id));

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
            'mail_subject'      => 'Action Requested : NPD PO Request',
            'template'          => 'emails.task.new_request',
            'receiver'          => "Purchasing Team",
            'title'             => "Action Requested : NPD PO Request",
            'body'              => 'You got a new request from ' . $user->team . ', ' . $user->first_name . ' ' . $user->last_name . '. ',
            'project_id'        => $project_id,
            'project_title'     => $project_obj->name,
            'request_id'        => $request_type_id,
            'request_type'      => '',
            'priority'          => $priority_mail,
            'due_date'          => $due_date_mail,
            'url'               => '/admin/npd_po_request/'.$project_id.'/edit#'.$request_type_id,
        ];

        $group_rs = $this->userRepository->get_receiver_emails_by_team('Admin');
        foreach ($group_rs as $team_user) {
            $receiver_list[] = $team_user['email'];
        }

        /// Send to receivers
        Mail::to($receiver_list)->send(new NewRequest($details));
    }

    public function edit_npd_po_request(Request $request, $task_id)
    {
        $npd_po_request = $this->taskTypeNpdPoRequestRepository->findById($task_id);

        $param = $request->request->all();

        if (isset($param['set_up_plant'])) {
            $param['set_up_plant'] = implode(',', $param['set_up_plant']);
        } else {
            $param['set_up_plant'] = '';
        }

        // Permission_check
//        if(!$this->permission_check($param)){
//            return redirect('admin/project/' . $npd_po_request->id . '/edit#'.$task_id)
//                ->with('error', __('This action is no longer permitted. Please contact an Administrator.'));
//        }

        if($this->taskTypeNpdPoRequestRepository->update($task_id, $param)){
            $user = auth()->user();
            // insert into campaign note for correspondence
            $this->add_correspondence('NPD PO REQUEST', $param, $npd_po_request, $user);

            if($request->file('p_attachment')){
                foreach ($request->file('p_attachment') as $file) {
                    $project_type_task_attachments = new ProjectTypeTaskAttachments();

//                    $fileName = $file->storeAs('campaigns/'.$npd_po_request->id.'/'.$asset_id, $file_name);
                    $fileName = $this->file_exist_check($file, $npd_po_request->id, $task_id);

                    $project_type_task_attachments['project_id'] = $npd_po_request->id;
                    $project_type_task_attachments['task_id'] = $task_id;
                    $project_type_task_attachments['type'] = 'attachment_file_' . $file->getMimeType();
                    $project_type_task_attachments['author_id'] = $user->id;
                    $project_type_task_attachments['attachment'] = '/' . $fileName;
                    $project_type_task_attachments['file_ext'] = pathinfo($fileName, PATHINFO_EXTENSION);
                    $project_type_task_attachments['file_type'] = $file->getMimeType();
                    $project_type_task_attachments['file_size'] = $file->getSize();
                    $project_type_task_attachments['created_at'] = Carbon::now();
                    $project_type_task_attachments->save();

                    // insert file attachment correspondence
                    $this->add_file_correspondence_for_task($npd_po_request, $user, $fileName, 'npd_po_request');
                }
            }

            return redirect('admin/npd_po_request/'.$npd_po_request->id.'/edit#'.$task_id)
                ->with('success', __('MM Request ('.$task_id.') - Update Success'));
        }

        return redirect('admin/npd_po_request/'.$npd_po_request->id.'/edit#'.$task_id)
            ->with('error', __('Update Failed'));
    }

    public function actionReSubmit($id)
    {
        $project_task_index = $this->projectTaskIndexRepository->findById($id);
        $param['status'] = 'action_requested';
        $param['updated_at'] = Carbon::now();
        $t_id = $project_task_index->id;
        $project_id = $project_task_index->project_id;
        if($this->projectTaskIndexRepository->update($id, $param)){

            $npdPoRequest_obj = $this->taskTypeNpdPoRequestRepository->get_npd_po_request_by_task_id($t_id);
            $current_revision_cnt = $npdPoRequest_obj['revision_cnt'];

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

            $this->taskTypeNpdPoRequestRepository->update($t_id, $t_param);

            $this->npd_po_status_correspondence($t_id, $project_id, 'Action Request (Re-Submit)');
            echo '/admin/npd_po_request/'.$project_id.'/edit#'.$id;
        }else{
            echo 'fail';
        }
    }

    public function actionFinalPrice($id)
    {
        $project_task_index = $this->projectTaskIndexRepository->findById($id);

        $t_id = $project_task_index->id;
        $project_id = $project_task_index->project_id;

        $t_param['price_set_up'] = "Final Price";
        $t_param['updated_at'] = Carbon::now();

        $this->taskTypeNpdPoRequestRepository->update($t_id, $t_param);

        $this->npd_po_status_correspondence($t_id, $project_id, 'Price Set Up Update To Final Price');
        echo '/admin/npd_po_request/'.$project_id.'/edit#'.$id;

    }


    public function actionInProgress($id)
    {
        $project_task_index = $this->projectTaskIndexRepository->findById($id);
        $param['status'] = 'in_progress';
        $param['updated_at'] = Carbon::now();
        $t_id = $project_task_index->id;
        $project_id = $project_task_index->project_id;
        if($this->projectTaskIndexRepository->update($id, $param)){
            $this->npd_po_status_correspondence($t_id, $project_id, 'In Progress');
            echo '/admin/npd_po_request/'.$project_id.'/edit#'.$id;
        }else{
            echo 'fail';
        }
    }

    public function revision_reason(Request $request)
    {
        $param = $request->all();
        $task_id = $param['request_type_id'];
        $revision_reason = $param['revision_reason'];
        $revision_reason_note = $param['revision_reason_note'];
        $project_id = $this->projectTaskIndexRepository->get_project_id_by_task_id($task_id);

        $params['status'] = 'action_review';
        $params['updated_at'] = Carbon::now();
        $params['revision_reason'] = $revision_reason;
        $params['revision_reason_note'] = $revision_reason_note;
        if($this->projectTaskIndexRepository->update($task_id, $params)){

            $user = auth()->user();
            $change_line  = "<p>$user->first_name updated the status to <b>Revision</b> for <b style='color: #b91d19;'>NPD PO REQUEST</b><b>(#$task_id)</b>
                            <br> <b style='color: black;'>Revision Reason : $revision_reason </b>
                            <br> <b style='color: black;'>$revision_reason_note </b>
                            </p>";
            $note = new NpdPoRequestNotes();
            $note['id'] = $project_id;
            $note['user_id'] = $user->id;
            $note['npd_po_request_type_id'] = 0;
            $note['task_id'] = $task_id;
            $note['project_id'] = $project_id;
            $note['note'] = $change_line;
            $note['created_at'] = Carbon::now();
            $note->save();

            return redirect('admin/npd_po_request/'.$project_id.'/edit#'.$task_id)
                ->with('success', __('Data has been Updated.'));
        }

        return redirect('admin/npd_po_request/'.$project_id.'/edit#'.$task_id)
            ->with('error', __('Data updates Failed'));
    }

    public function actionReview($id)
    {
        $project_task_index = $this->projectTaskIndexRepository->findById($id);
        $param['status'] = 'action_review';
        $param['updated_at'] = Carbon::now();
        $t_id = $project_task_index->id;
        $project_id = $project_task_index->project_id;
        if($this->projectTaskIndexRepository->update($id, $param)){
            $this->npd_po_status_correspondence($t_id, $project_id, 'Revision');
            echo '/admin/npd_po_request/'.$project_id.'/edit#'.$id;
        }else{
            echo 'fail';
        }
    }

    public function actionComplete($id)
    {
        $project_task_index = $this->projectTaskIndexRepository->findById($id);
        $param['status'] = 'action_completed';
        $param['updated_at'] = Carbon::now();
        $t_id = $project_task_index->id;
        $project_id = $project_task_index->project_id;
        if($this->projectTaskIndexRepository->update($id, $param)){
            $this->npd_po_status_correspondence($t_id, $project_id, 'Action Completed');
            echo '/admin/npd_po_request/'.$project_id.'/edit#'.$id;
        }else{
            echo 'fail';
        }
    }

    public function npd_po_status_correspondence($t_id, $p_id, $status)
    {
        // Insert into Project note for correspondence
        $user = auth()->user();

        $change_line  = "<p>$user->first_name updated the status to <b>$status</b> for <b style='color: #b91d19;'>NPD PO REQUEST</b><b>(#$t_id)</b></p>";

        $note = new NpdPoRequestNotes();
        $note['id'] = $t_id;
        $note['user_id'] = $user->id;
        $note['npd_po_request_type_id'] = 0;
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

    public function correspondence_new_npd_po_request($p_id, $task_name, $projectTaskIndex)
    {
        $user = auth()->user();
        $task_name = strtoupper($task_name);
        $change_line = "<p><b style='color: #b91d19;'>$task_name</b> <b>(#$projectTaskIndex->id)</b> has been created by $user->first_name. </p>";
        $qra_request_note = new NpdPoRequestNotes();
        $qra_request_note['id'] = $projectTaskIndex->id;
        $qra_request_note['user_id'] = $user->id;
        $qra_request_note['npd_po_request_type_id'] = 0;
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
                    if($key == 'assignee' || $key == 'buyer'){

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

        $change_line  = "<p>$user->first_name made a change to a <b style='color: #b91d19;'>$task_type </b><b>(#$origin_param->task_id)</b></p>";

        if(!empty($changed)){
            foreach ($changed as $label => $change) {

                $label = strtoupper(str_replace('_', ' ', $label));
                $from  = trim($change['original']); // Remove strip tags
                $to    = trim($change['new']);      // Remove strip tags

                $change_line .= "<div class='change_label'><p>$label:</p></div>"
                    . "<div class='change_to'><p>$to</p></div>"
                    . "<div class='change_from'><del><p>$from</p></del></div>";
            }

            $qra_request_note = new NpdPoRequestNotes();
            $qra_request_note['id'] = $origin_param->id;
            $qra_request_note['user_id'] = $user->id;
            $qra_request_note['npd_po_request_type_id'] = 0;
            $qra_request_note['task_id'] = $origin_param->task_id;
            $qra_request_note['project_id'] = $origin_param->id;
            $qra_request_note['note'] = $change_line;
            $qra_request_note['created_at'] = Carbon::now();
            $qra_request_note->save();

        }
    }

    public function get_task_param($task_type, $data)
    {
        if ($task_type == 'NPD PO REQUEST') {
            $new = array(
                'buyer' => $data['buyer'],
                'request_detail' => $data['request_detail'],
                'priority' => $data['priority'],
                'due_date' => $data['due_date'],
                'source_list_completion' => $data['source_list_completion'],
                'info_record_completion' => $data['info_record_completion'],
                'price_set_up' => $data['price_set_up'],
                'forecast_completion' => $data['forecast_completion'],
                'materials' => $data['materials'],
                'total_sku_count' => $data['total_sku_count'],
                'set_up_plant' => $data['set_up_plant'],
                'vendor_code' => $data['vendor_code'],
                'second_vendor_code' => $data['second_vendor_code'],
                'est_ready_date' => $data['est_ready_date'],
                'po' => $data['po'],
            );
            return $new;
        }
    }

    public function add_file_correspondence_for_task($qc_request, $user, $file_type, $task_type)
    {
        // Insert into project note for correspondence (attachment file)
        $task_type_ =  strtoupper(str_replace('_', ' ', $task_type));

        $change_line  = "<p>$user->first_name has added a new attachment <br><b>$file_type</b><br>to <b style='color: #b91d19;'>$task_type_</b> <b>(#$qc_request->task_id)</b></p>";

        $qra_request_note = new NpdPoRequestNotes();
        $qra_request_note['id'] = $qc_request->id;
        $qra_request_note['user_id'] = $user->id;
        $qra_request_note['npd_po_request_type_id'] = 0;
        $qra_request_note['task_id'] = $qc_request->task_id;
        $qra_request_note['project_id'] = $qc_request->id;
        $qra_request_note['note'] = $change_line;
        $qra_request_note['created_at'] = Carbon::now();
        $qra_request_note->save();

    }

    public function file_exist_check($file, $project_id, $task_id)
    {
        $originalName = $file->getClientOriginalName();
        $destinationFolder = 'storage/npd_po_request/'.$project_id.'/'.$task_id.'/'.$originalName;

        // If exist same name file, add numberning for version control
        if(file_exists($destinationFolder)){
            if ($pos = strrpos($originalName, '.')) {
                $new_name = substr($originalName, 0, $pos);
                $ext = substr($originalName, $pos);
            }
            $newpath = 'storage/npd_po_request/'.$project_id.'/'.$task_id.'/'.$originalName;
            $uniq_no = 1;
            while (file_exists($newpath)) {
                $tmp_name = $new_name .'_v'. $uniq_no . $ext;
                $newpath = 'storage/npd_po_request/'.$project_id.'/'.$task_id.'/'.$tmp_name;
                $uniq_no++;
            }
            $file_name = $tmp_name;
        }else{
            $file_name = $originalName;
        }

        $fileName =$file->storeAs('npd_po_request/'.$project_id.'/'.$task_id, $file_name);
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

            $request_note = new NpdPoRequestNotes();
            $request_note['id'] = $task_id; // task_id
            $request_note['user_id'] = $user->id;
            $request_note['npd_po_request_type_id'] = 0;
            $request_note['task_id'] = $task_id; // task_id
            $request_note['note'] = $change_line;
            $request_note['created_at'] = Carbon::now();
            $request_note->save();

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

        $note = new NpdPoRequestNotes();
        $note['id'] = $t_id;
        $note['user_id'] = $user->id;
        $note['npd_po_request_type_id'] = 0;
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
                'url' => '/admin/npd_po_request/' . $p_id . '/edit#' . $t_id,
            ];
            $receiver_list = explode(',', $email_list);
            Mail::to($receiver_list)->send(new TaskStatusNotification($details));
        }

        return redirect('admin/npd_po_request/'.$p_id.'/edit')
            ->with('success', __('Data has been Updated.'));
    }

    public function requestTypeRemovePermissionCheck($request_type_id){

        $user = auth()->user();
        if($user->role == 'Admin') return true; // admin okay

        $task_author_id = $this->projectTaskIndexRepository->get_author_id_by_task_id($request_type_id);
        if($task_author_id != $user->id){
            return false;
        }
        return true;
    }

    public function requestTypeRemove($request_type_id, $type)
    {
        $obj = $this->projectTaskIndexRepository->findById($request_type_id);
        $p_id = $obj->project_id;

        if($this->requestTypeRemovePermissionCheck($request_type_id)){
            // Delete from sub_qra_request_index, sub_qra_request_type tables
            $this->projectTaskIndexRepository->delete($request_type_id);
            $this->taskTypeNpdPoRequestRepository->delete($request_type_id);

            // Add correspondence for asset Removed
            $this->npd_po_remove_correspondence($request_type_id, $p_id, $type);

            echo '/admin/qc_request/'.$p_id.'/edit';
        }else{
            echo 'fail';
        }
    }

    public function npd_po_remove_correspondence($t_id, $p_id, $task_type)
    {
        // Insert into Project note for correspondence (attachment file)
        $user = auth()->user();
        $task_type_ =  strtoupper(str_replace('_', ' ', $task_type));

        $change_line  = "<b style='color: #b91d19;'>$task_type_ </b><b>(#$t_id)</b> has been removed by $user->first_name";

        $note = new NpdPoRequestNotes();
        $note['id'] = $t_id;
        $note['user_id'] = $user->id;
        $note['npd_po_request_type_id'] = $t_id;
        $note['task_id'] = $t_id;
        $note['project_id'] = $p_id;
        $note['note'] = $change_line;
        $note['created_at'] = Carbon::now();
        $note->save();
    }

}
