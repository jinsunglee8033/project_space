<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Http\Controllers\NotifyController;
use App\Http\Requests\Admin\DevRequest;
use App\Mail\DevMessage;
use App\Mail\NoteProject;
use App\Mail\TaskStatusNotification;
use App\Models\DevNotes;
use App\Models\DisplayRequestNotes;
use App\Models\MmRequestNotes;
use App\Models\ProjectNotes;
use App\Models\ProjectTaskIndex;
use App\Models\ProjectTypeTaskAttachments;
use App\Models\TaskTypeDisplayRequest;
use App\Models\TaskTypeMmRequest;
use App\Models\User;
use App\Repositories\Admin\AssetNotificationUserRepository;
use App\Repositories\Admin\AssetOwnerAssetsRepository;
use App\Repositories\Admin\BrandRepository;
use App\Repositories\Admin\DisplayRequestNotesRepository;
use App\Repositories\Admin\DisplayRequestRepository;
use App\Repositories\Admin\MmRequestNotesRepository;
use App\Repositories\Admin\MmRequestRepository;
use App\Repositories\Admin\ProjectNotesRepository;
use App\Repositories\Admin\ProjectRepository;
use App\Repositories\Admin\ProjectTaskFileAttachmentsRepository;
use App\Repositories\Admin\ProjectTaskIndexRepository;
use App\Repositories\Admin\SubQraRequestIndexRepository;
use App\Repositories\Admin\TaskTypeConceptDevelopmentRepository;
use App\Repositories\Admin\TaskTypeDisplayRequestRepository;
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

class DisplayRequestController extends Controller
{
    Private $projectRepository;
    Private $projectTaskIndexRepository;
    Private $taskTypeMmRequestRepository;
    Private $taskTypeDisplayRequestRepository;
    private $projectTaskFileAttachmentsRepository;
    Private $projectNotesRepository;
    Private $mmRequestNotesRepository;
    Private $mmRequestRepository;
    Private $displayRequestNotesRepository;
    Private $displayRequestRepository;
    Private $teamRepository;
    Private $brandRepository;
    private $userRepository;


    public function __construct(
        ProjectRepository $projectRepository,
        ProjectTaskIndexRepository $projectTaskIndexRepository,
        TaskTypeMmRequestRepository $taskTypeMmRequestRepository,
        TaskTypeDisplayRequestRepository $taskTypeDisplayRequestRepository,
        ProjectTaskFileAttachmentsRepository $projectTaskFileAttachmentsRepository,
        ProjectNotesRepository $projectNotesRepository,
        MmRequestNotesRepository $mmRequestNotesRepository,
        MmRequestRepository $mmRequestRepository,
        DisplayRequestNotesRepository $displayRequestNotesRepository,
        DisplayRequestRepository $displayRequestRepository,
        TeamRepository $teamRepository,
        BrandRepository $brandRepository,
        UserRepository $userRepository)
    {
        parent::__construct();

        $this->projectRepository = $projectRepository;
        $this->projectTaskIndexRepository = $projectTaskIndexRepository;
        $this->taskTypeMmRequestRepository = $taskTypeMmRequestRepository;
        $this->taskTypeDisplayRequestRepository = $taskTypeDisplayRequestRepository;
        $this->projectTaskFileAttachmentsRepository = $projectTaskFileAttachmentsRepository;
        $this->projectNotesRepository = $projectNotesRepository;
        $this->mmRequestNotesRepository = $mmRequestNotesRepository;
        $this->mmRequestRepository = $mmRequestRepository;
        $this->displayRequestNotesRepository = $displayRequestNotesRepository;
        $this->displayRequestRepository = $displayRequestRepository;
        $this->teamRepository = $teamRepository;
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
        $this->data['currentAdminMenu'] = 'display_board';

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

        if(isset($_GET[''])) {
            $request_type = $param['request_type'];
        }else{
            $request_type = !empty($param['request_type']) ? $param['request_type'] : '';
        }

        $this->data['task_list_action_requested'] = $this->taskTypeDisplayRequestRepository->get_action_requested_list($assignee, $team, $brand, $request_type);
        $this->data['task_list_in_progress'] = $this->taskTypeDisplayRequestRepository->get_in_progress_list($assignee, $team, $brand, $request_type);
        $this->data['task_list_action_review'] = $this->taskTypeDisplayRequestRepository->get_action_review_list($assignee, $team, $brand, $request_type);
        $this->data['task_list_action_completed'] = $this->taskTypeDisplayRequestRepository->get_action_completed_list($assignee, $team, $brand, $request_type);

        $this->data['assignee'] = $assignee;
        $this->data['team'] = $team;
        $this->data['brand'] = $brand;
        $this->data['request_type'] = $request_type;

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

        $this->data['display_assignee_list'] = $this->userRepository->getDisplayAssigneeList();
        $this->data['teams'] =$this->teamRepository->findAll($team_options);
        $this->data['brands'] =$this->brandRepository->findAll($brand_options);
        $this->data['request_type_list'] = [
            'Display Only',
            'Sample Only',
            'Show Display'
        ];

        return view('admin.display_request.index', $this->data);
    }

    public function edit($id)
    {
        $this->data['currentAdminMenu'] = 'display_board';

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

        $rs = $this->displayRequestRepository->get_task_id_for_display($id);

        if($rs){
            // if display_request exist
            $this->data['display_request_list'] = $display_request_list = $this->displayRequestRepository->get_display_request_list_by_task_id($rs->id);

            // task_detail
            if(sizeof($display_request_list)>0){
                foreach ($display_request_list as $k => $qc_request){
                    $t_id = $rs->id;
                    $task_detail = $this->taskTypeDisplayRequestRepository->findById($t_id);
                    $display_request_list[$k]->detail = $task_detail;
                    $task_files = $this->projectTaskFileAttachmentsRepository->findAllByTaskId($t_id);
                    $display_request_list[$k]->files = $task_files;
                }
            }

            $this->data['task_status'] = $display_request_list[0]->status;
            // Project_notes
            $options = [
                'id' => $rs->id,
                'order' => [
                    'created_at' => 'desc',
                ]
            ];

            $correspondences = $this->displayRequestNotesRepository->findAll($options);
            $this->data['correspondences'] = $correspondences;

        }else{
            // if qc_request not exist
            $this->data['display_request_list'] = null;
            $this->data['correspondences'] = null;
            $this->data['task_status'] = null;
        }


        /////////// Display Request Task ////////////////////////////////////////////
        $this->data['request_type_list'] = [
          'Display Only',
          'Sample Only',
          'Show Display'
        ];
        $this->data['product_category_list'] = [
            'Nail',
            'Lash',
            'Cosmetic',
            'Hair Care',
            'Appliance',
            'Accessories'
        ];
        $this->data['priorities'] = [
            'Normal', 'Urgent'
        ];
        $this->data['account_list'] = [
            'Retailer',
            'Domestic (BS)',
            'International',
            'Professional',
            'e-commerce'
//            'WMT - Walmart',
//            'TGT - Target',
//            'WAG - Walgreens',
//            'CVS - CVS',
//            'DG - Dollar General',
//            'FD - Family Dollar',
//            'RAD - Rite Aid',
//            'ULTA - Ulta',
//            'JC - Jean Coutu',
//            'LD - London Drug',
//            'SDM - Shopper Drug Mart',
//            'BS - Beauty Supply Stores (Domestic)',
//            'BS - Beauty Supply Stores (International)',
//            'Others',
//            'N/A (Not for sales purpose)'
        ];
        $this->data['show_type_list'] = [
            'Trade Show',
            'Marketing Show',
            'Consumer Show',
            'Others'
        ];
        $this->data['display_style_list'] = [
            'Counter Top',
            'Floor Stand',
            'Side Kick',
            'Clip Strip',
            'End Cap',
            'Others'
        ];
        $this->data['display_type_list'] = [
            'Vac-Tray',
            'Corrugated',
            'Fabrication',
            'Permanent'
        ];
        $this->data['display_assignee_list'] = $this->userRepository->getDisplayAssigneeList();
        $this->data['task_category_list'] = [
            'Rendering',
            'Engineering Drawing',
            'Sample/3D Mockup',
            'Final Artwork',
            'Quotation',
            'MMBOM / Forecast',
            'PO',
            'Mold Production',
            'Component Ready',
            'Production',
            'In Transit',
            'Display Received'
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

        return view('admin.display_request.form', $this->data);
    }

    public function add_display_request(Request $request)
    {
        $param = $request->request->all();

        // add project_task_index
        $projectTaskIndex = new ProjectTaskIndex();
        $projectTaskIndex['project_id'] = $param['display_request_p_id'];
        $projectTaskIndex['type'] = $param['display_request_task_type'];
        $projectTaskIndex['status'] = 'action_requested';

        $user = auth()->user(); // asset_author_id
        $projectTaskIndex['author_id'] = $user->id;
        $projectTaskIndex->save();
        $task_id = $projectTaskIndex->id;

        // add task_type_display_request
        $taskTypeDisplayRequest = new TaskTypeDisplayRequest();
        $taskTypeDisplayRequest['id'] = $param['display_request_p_id']; //project_id
        $taskTypeDisplayRequest['author_id'] = $param['display_request_author_id'];
        $taskTypeDisplayRequest['type'] = $param['display_request_task_type'];

        $taskTypeDisplayRequest['request_type'] = $param['display_request_request_type'];
        if($taskTypeDisplayRequest['request_type'] == 'Show Display'){
            $taskTypeDisplayRequest['show_type'] = $param['display_request_show_type'];
            $taskTypeDisplayRequest['show_location'] = $param['display_request_show_location'];
        }
        if (isset($param['display_request_product_category'])) {
            $taskTypeDisplayRequest['product_category'] = implode(', ', $param['display_request_product_category']);
        } else {
            $taskTypeDisplayRequest['product_category'] = '';
        }
//        $taskTypeDisplayRequest['priority'] = $param['display_request_priority'];
//        if($taskTypeDisplayRequest['priority'] == 'Urgent'){
//            $taskTypeDisplayRequest['due_date_urgent'] = $param['display_request_due_date_urgent'];
//            $taskTypeDisplayRequest['urgent_reason'] = $param['display_request_urgent_reason'];
//        }
        $taskTypeDisplayRequest['due_date'] = $param['display_request_due_date'];

//        $taskTypeDisplayRequest['account'] = $param['display_request_account'];
//        if($taskTypeDisplayRequest['account'] == 'Others'){
//            $taskTypeDisplayRequest['specify_account'] = $param['display_request_specify_account'];
//        }

        if (isset($param['display_request_account'])) {
            $taskTypeDisplayRequest['account'] = implode(', ', $param['display_request_account']);
        } else {
            $taskTypeDisplayRequest['account'] = '';
        }

        $taskTypeDisplayRequest['display_style'] = $param['display_request_display_style'];
        if($taskTypeDisplayRequest['display_style'] == 'Others'){
            $taskTypeDisplayRequest['specify_display_style'] = $param['display_request_specify_display_style'];
        }
        $taskTypeDisplayRequest['total_display_qty'] = $param['display_request_total_display_qty'];
        $taskTypeDisplayRequest['display_type'] = $param['display_request_display_type'];
        $taskTypeDisplayRequest['additional_information'] = $param['display_request_additional_information'];
        $taskTypeDisplayRequest['display'] = $param['display_request_display'];
        $taskTypeDisplayRequest['display_budget_per_ea'] = $param['display_request_display_budget_per_ea'];
        $taskTypeDisplayRequest['display_budget_code'] = $param['display_request_display_budget_code'];

        $taskTypeDisplayRequest['created_at'] = Carbon::now();
        $taskTypeDisplayRequest['task_id'] = $task_id;
        $taskTypeDisplayRequest->save();

        // new correspondence when adding asset
        $this->correspondence_add_new_task($projectTaskIndex['project_id'], 'Display Request', $projectTaskIndex);

        // Correspondence for Onsite QC Request Type
        $this->correspondence_new_display_request($projectTaskIndex['project_id'], 'Display Request', $projectTaskIndex);

        // add campaign_type_asset_attachments
        if($request->file('display_request_p_attachment')){
            foreach ($request->file('display_request_p_attachment') as $file) {

                $project_type_task_attachments = new ProjectTypeTaskAttachments();

//                $fileName = $file->storeAs('campaigns/'.$request['qc_request_c_id'].'/'.$asset_id, $originalName);
                $fileName = $this->file_exist_check($file, $param['display_request_p_id'], $task_id);

                $project_type_task_attachments['project_id'] = $param['display_request_p_id'];
                $project_type_task_attachments['task_id'] = $task_id;
                $project_type_task_attachments['type'] = 'attachment_file_' . $file->getMimeType();
                $project_type_task_attachments['author_id'] = $param['display_request_author_id'];
                $project_type_task_attachments['attachment'] = '/' . $fileName;
                $project_type_task_attachments['file_ext'] = pathinfo($fileName, PATHINFO_EXTENSION);
                $project_type_task_attachments['file_type'] = $file->getMimeType();
                $project_type_task_attachments['file_size'] = $file->getSize();
                $project_type_task_attachments['created_at'] = Carbon::now();
                $project_type_task_attachments->save();
            }
        }

        return redirect('admin/display_request/'.$param['display_request_p_id'].'/edit#'.$task_id)
            ->with('success', __('Added the Display Request Task : ' . $task_id));

    }

    public function edit_display_request(Request $request, $task_id)
    {
        $display_request = $this->taskTypeDisplayRequestRepository->findById($task_id);

        $param = $request->request->all();

        if (isset($param['priority'])) {
            $param['priority'] = $param['priority'];
        } else {
            $param['priority'] = null;
        }

        if (isset($param['product_category'])) {
            $param['product_category'] = implode(', ', $param['product_category']);
        } else {
            $param['product_category'] = '';
        }

        if (isset($param['account'])) {
            $param['account'] = implode(', ', $param['account']);
        } else {
            $param['account'] = '';
        }

        if (isset($param['show_type'])) {
            $param['show_type'] = $param['show_type'];
        } else {
            $param['show_type'] = null;
        }

        if (isset($param['show_location'])) {
            $param['show_location'] = $param['show_location'];
        } else {
            $param['show_location'] = null;
        }

        if (isset($param['due_date_urgent'])) {
            $param['due_date_urgent'] = $param['due_date_urgent'];
        } else {
            $param['due_date_urgent'] = null;
        }

        if (isset($param['urgent_reason'])) {
            $param['urgent_reason'] = $param['urgent_reason'];
        } else {
            $param['urgent_reason'] = null;
        }

        if (isset($param['specify_account'])) {
            $param['specify_account'] = $param['specify_account'];
        } else {
            $param['specify_account'] = null;
        }

        if (isset($param['specify_display_style'])) {
            $param['specify_display_style'] = $param['specify_display_style'];
        } else {
            $param['specify_display_style'] = null;
        }

        if (isset($param[$task_id.'_request_due_date'])) {
            $param['request_due_date'] = $param[$task_id.'_request_due_date'];
        } else {
            $param['request_due_date'] = null;
        }

        // Permission_check
//        if(!$this->permission_check($param)){
//            return redirect('admin/project/' . $display_request->id . '/edit#'.$task_id)
//                ->with('error', __('This action is no longer permitted. Please contact an Administrator.'));
//        }

        if($this->taskTypeDisplayRequestRepository->update($task_id, $param)){
            $user = auth()->user();
            // insert into campaign note for correspondence
            $this->add_correspondence('display_request', $param, $display_request, $user);

            if($request->file('p_attachment')){
                foreach ($request->file('p_attachment') as $file) {
                    $project_type_task_attachments = new ProjectTypeTaskAttachments();

//                    $fileName = $file->storeAs('campaigns/'.$display_request->id.'/'.$asset_id, $file_name);
                    $fileName = $this->file_exist_check($file, $display_request->id, $task_id);

                    $project_type_task_attachments['project_id'] = $display_request->id;
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
                    $this->add_file_correspondence_for_task($display_request, $user, $fileName, 'display_request');
                }
            }

            return redirect('admin/display_request/'.$display_request->id.'/edit#'.$task_id)
                ->with('success', __('display Request ('.$task_id.') - Update Success'));
        }

        return redirect('admin/display_request/'.$display_request->id.'/edit#'.$task_id)
            ->with('error', __('Update Failed'));
    }

    public function revision_reason(Request $request)
    {
        $param = $request->all();
        $task_id = $param['request_type_id'];
        $revision_reason = $param['revision_reason'];
        $revision_reason_note = $param['revision_reason_note'];
        $project_id = $this->projectTaskIndexRepository->get_project_id_by_task_id($task_id);

        $params['status'] = 'action_requested';
        $params['updated_at'] = Carbon::now();
        $params['revision_reason'] = $revision_reason;
        $params['revision_reason_note'] = $revision_reason_note;
        if($this->projectTaskIndexRepository->update($task_id, $params)){

            $user = auth()->user();
            $change_line  = "<p>$user->first_name updated the status to <b>Revision</b> for <b style='color: #b91d19;'>DISPLAY REQUEST</b><b>(#$task_id)</b>
                            <br> <b style='color: black;'>Revision Reason : $revision_reason </b>
                            <br> <b style='color: black;'>$revision_reason_note </b>
                            </p>";
            $note = new DisplayRequestNotes();
            $note['id'] = $project_id;
            $note['user_id'] = $user->id;
            $note['task_id'] = $task_id;
            $note['project_id'] = $project_id;
            $note['note'] = $change_line;
            $note['created_at'] = Carbon::now();
            $note->save();

            $displayRequest_obj = $this->displayRequestRepository->get_display_request_by_task_id($task_id);
            $current_revision_cnt = $displayRequest_obj['revision_cnt'];

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

            $this->taskTypeDisplayRequestRepository->update($task_id, $t_param);

            return redirect('admin/display_request/'.$project_id.'/edit#'.$task_id)
                ->with('success', __('Data has been Updated.'));
        }

        return redirect('admin/display_request/'.$project_id.'/edit#'.$task_id)
            ->with('error', __('Data updates Failed'));
    }

    public function actionReSubmit($id)
    {
        $project_task_index = $this->projectTaskIndexRepository->findById($id);
        $param['status'] = 'action_requested';
        $param['updated_at'] = Carbon::now();
        $t_id = $project_task_index->id;
        $project_id = $project_task_index->project_id;
        if($this->projectTaskIndexRepository->update($id, $param)){

            $displayRequest_obj = $this->displayRequestRepository->get_display_request_by_task_id($t_id);
            $current_revision_cnt = $displayRequest_obj['revision_cnt'];

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

            $this->taskTypeDisplayRequestRepository->update($t_id, $t_param);

            $this->display_status_correspondence($t_id, $project_id, 'Action Requested (Revision)');
            echo '/admin/display_request/'.$project_id.'/edit#'.$id;
        }else{
            echo 'fail';
        }
    }

    public function actionApprove($id)
    {
        $project_task_index = $this->projectTaskIndexRepository->findById($id);
        $param['status'] = 'action_requested';
        $param['updated_at'] = Carbon::now();
        $t_id = $project_task_index->id;
        $project_id = $project_task_index->project_id;
        if($this->projectTaskIndexRepository->update($id, $param)){

            $t_param['updated_at'] = Carbon::now();
            $this->taskTypeDisplayRequestRepository->update($t_id, $t_param);

            $this->display_status_correspondence($t_id, $project_id, 'Action Requested (Approve)');
            echo '/admin/display_request/'.$project_id.'/edit#'.$id;
        }else{
            echo 'fail';
        }
    }

    public function actionInProgress($id)
    {
        $project_task_index = $this->projectTaskIndexRepository->findById($id);
        $param['status'] = 'in_progress';
        $param['updated_at'] = Carbon::now();
        $t_id = $project_task_index->id;
        $project_id = $project_task_index->project_id;
        if($this->projectTaskIndexRepository->update($id, $param)){

            $user = auth()->user();
            $t_param['assignee'] = $user->id;
            $t_param['updated_at'] = Carbon::now();
            $this->taskTypeDisplayRequestRepository->update($t_id, $t_param);

            $this->display_status_correspondence($t_id, $project_id, 'In Progress');
            echo '/admin/display_request/'.$project_id.'/edit#'.$id;
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
            $this->display_status_correspondence($t_id, $project_id, 'Action Review');
            echo '/admin/display_request/'.$project_id.'/edit#'.$id;
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
            $this->display_status_correspondence($t_id, $project_id, 'Action Completed');
            echo '/admin/display_request/'.$project_id.'/edit#'.$id;
        }else{
            echo 'fail';
        }
    }

    public function display_status_correspondence($t_id, $p_id, $status)
    {
        // Insert into Project note for correspondence (attachment file)
        $user = auth()->user();

        $change_line  = "<p>$user->first_name updated the status to <b>$status</b> for <b style='color: #b91d19;'>DISPLAY REQUEST</b><b>(#$t_id)</b></p>";

        $note = new DisplayRequestNotes();
        $note['id'] = $t_id;
        $note['user_id'] = $user->id;
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

    public function correspondence_new_display_request($p_id, $task_name, $projectTaskIndex)
    {
        $user = auth()->user();
        $task_name = strtoupper($task_name);
        $change_line = "<p><b style='color: #b91d19;'>$task_name</b> <b>(#$projectTaskIndex->id)</b> has been created by $user->first_name. </p>";
        $qra_request_note = new DisplayRequestNotes();
        $qra_request_note['id'] = $projectTaskIndex->id;
        $qra_request_note['user_id'] = $user->id;
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

            $qra_request_note = new DisplayRequestNotes();
            $qra_request_note['id'] = $origin_param->id;
            $qra_request_note['user_id'] = $user->id;
            $qra_request_note['task_id'] = $origin_param->task_id;
            $qra_request_note['project_id'] = $origin_param->id;
            $qra_request_note['note'] = $change_line;
            $qra_request_note['created_at'] = Carbon::now();
            $qra_request_note->save();

        }
    }

    public function get_task_param($task_type, $data)
    {
        if ($task_type == 'display_request') {
            $new = array(
                'request_type' => $data['request_type'],
                'show_type' => $data['show_type'],
                'show_location' => $data['show_location'],
                'product_category' => $data['product_category'],
                'priority' => isset($data['priority']) ? $data['priority'] : '',
                'due_date_urgent' => isset($data['due_date_urgent']) ? $data['due_date_urgent'] : '',
                'urgent_reason' => isset($data['urgent_reason']) ? $data['urgent_reason'] : '',
                'account' => $data['account'],
                'specify_account' => $data['specify_account'],
                'display_style' => $data['display_style'],
                'specify_display_style' => $data['specify_display_style'],
                'display_type' => $data['display_type'],
                'additional_information' => $data['additional_information'],
                'display' => $data['display'],
                'total_display_qty' => $data['total_display_qty'],
                'display_budget_per_ea' => $data['display_budget_per_ea'],
                'display_budget_code' => $data['display_budget_code'],
                'assignee' => $data['assignee'],
                'task_category' => $data['task_category'],
            );
            return $new;
        }
    }

    public function add_file_correspondence_for_task($qc_request, $user, $file_type, $task_type)
    {
        // Insert into project note for correspondence (attachment file)
        $task_type_ =  strtoupper(str_replace('_', ' ', $task_type));

        $change_line  = "<p>$user->first_name has added a new attachment <br><b>$file_type</b><br>to <b style='color: #b91d19;'>$task_type_</b> <b>(#$qc_request->task_id)</b></p>";

        $qra_request_note = new DisplayRequestNotes();
        $qra_request_note['id'] = $qc_request->id;
        $qra_request_note['user_id'] = $user->id;
        $qra_request_note['task_id'] = $qc_request->task_id;
        $qra_request_note['project_id'] = $qc_request->id;
        $qra_request_note['note'] = $change_line;
        $qra_request_note['created_at'] = Carbon::now();
        $qra_request_note->save();

    }

    public function file_exist_check($file, $project_id, $task_id)
    {
        $originalName = $file->getClientOriginalName();
        $destinationFolder = 'storage/project/'.$project_id.'/'.$task_id.'/'.$originalName;

        // If exist same name file, add numberning for version control
        if(file_exists($destinationFolder)){
            if ($pos = strrpos($originalName, '.')) {
                $new_name = substr($originalName, 0, $pos);
                $ext = substr($originalName, $pos);
            }
            $newpath = 'storage/project/'.$project_id.'/'.$task_id.'/'.$originalName;
            $uniq_no = 1;
            while (file_exists($newpath)) {
                $tmp_name = $new_name .'_v'. $uniq_no . $ext;
                $newpath = 'storage/project/'.$project_id.'/'.$task_id.'/'.$tmp_name;
                $uniq_no++;
            }
            $file_name = $tmp_name;
        }else{
            $file_name = $originalName;
        }

        $fileName =$file->storeAs('project/'.$project_id.'/'.$task_id, $file_name);
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

            $request_note = new DisplayRequestNotes();
            $request_note['id'] = $task_id; // task_id
            $request_note['user_id'] = $user->id;
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

        $note = new DisplayRequestNotes();
        $note['id'] = $t_id;
        $note['user_id'] = $user->id;
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
                'url' => '/admin/display_request/' . $p_id . '/edit#' . $t_id,
            ];
            $receiver_list = explode(',', $email_list);
            Mail::to($receiver_list)->send(new TaskStatusNotification($details));
        }
        return redirect('admin/display_request/'.$p_id.'/edit')
            ->with('success', __('Data has been Updated.'));
    }

}
