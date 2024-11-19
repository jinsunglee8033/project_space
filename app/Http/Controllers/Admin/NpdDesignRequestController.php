<?php

namespace App\Http\Controllers\Admin;

use App\Authorizable;
use App\Http\Controllers\Controller;
use App\Http\Controllers\NotifyController;
use App\Http\Requests\Admin\ProjectRequest;
use App\Http\Requests\Admin\UserRequest;
use App\Mail\MyDemoMail;
use App\Mail\NewProject;
use App\Mail\NoteProject;
use App\Mail\SendMail;

use App\Mail\TaskStatusNotification;
use App\Models\LegalRequestNotes;
use App\Models\LegalRequestTypeAttachments;
use App\Models\NpdDesignRequestNotes;
use App\Models\NpdDesignRequestTypeAttachments;
use App\Models\PeRequestNotes;
use App\Models\PeRequestTypeAttachments;
use App\Models\ProjectNotes;
use App\Models\ProjectTaskIndex;
use App\Models\SubNpdDesignRequestIndex;
use App\Models\SubNpdDesignRequestType;
use App\Models\SubPeRequestIndex;
use App\Models\SubPeRequestType;
use App\Models\TaskTypeNpdDesignRequest;
use App\Models\User;


use App\Repositories\Admin\BrandRepository;
use App\Repositories\Admin\NpdDesignRequestNotesRepository;
use App\Repositories\Admin\NpdDesignRequestTypeFileAttachmentsRepository;
use App\Repositories\Admin\PeRequestNotesRepository;
use App\Repositories\Admin\PeRequestRepository;
use App\Repositories\Admin\PeRequestTypeFileAttachmentsRepository;
use App\Repositories\Admin\ProjectNotesRepository;
use App\Repositories\Admin\SubNpdDesignRequestIndexRepository;
use App\Repositories\Admin\SubNpdDesignRequestTypeRepository;
use App\Repositories\Admin\SubPeRequestTypeRepository;
use App\Repositories\Admin\SubPeRequestIndexRepository;
use App\Repositories\Admin\ProjectRepository;
use App\Repositories\Admin\ProjectTaskIndexRepository;
use App\Repositories\Admin\ProjectTaskFileAttachmentsRepository;
use App\Repositories\Admin\TaskTypeNpdDesignRequestRepository;
use App\Repositories\Admin\TaskTypePeRequestRepository;
use App\Repositories\Admin\TeamRepository;
use App\Repositories\Admin\UserRepository;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class NpdDesignRequestController extends Controller
{
    Private $projectRepository;

    Private $subPeRequestIndexRepository;
    Private $subNpdDesignRequestIndexRepository;
    Private $subPeRequestTypeRepository;
    Private $subNpdDesignRequestTypeRepository;
    Private $peRequestTypeFileAttachmentsRepository;
    Private $npdDesignRequestTypeFileAttachmentsRepository;
    Private $peRequestNotesRepository;
    Private $npdDesignRequestNotesRepository;
    Private $taskTypePeRequestRepository;
    Private $taskTypeNpdDesignRequestRepository;

    Private $projectTaskFileAttachmentsRepository;
    Private $projectTaskIndexRepository;
    Private $projectNotesRepository;

    Private $brandRepository;
    Private $teamRepository;
    private $userRepository;

    public function __construct(ProjectRepository $projectRepository,

                                PeRequestRepository $peRequestRepository,
                                SubPeRequestTypeRepository $subPeRequestTypeRepository,
                                SubNpdDesignRequestTypeRepository $subNpdDesignRequestTypeRepository,
                                SubPeRequestIndexRepository $subPeRequestIndexRepository,
                                SubNpdDesignRequestIndexRepository $subNpdDesignRequestIndexRepository,
                                TaskTypePeRequestRepository $taskTypePeRequestRepository,
                                TaskTypeNpdDesignRequestRepository $taskTypeNpdDesignRequestRepository,
                                PeRequestTypeFileAttachmentsRepository $peRequestTypeFileAttachmentsRepository,
                                NpdDesignRequestTypeFileAttachmentsRepository $npdDesignRequestTypeFileAttachmentsRepository,
                                PeRequestNotesRepository $peRequestNotesRepository,
                                NpdDesignRequestNotesRepository $npdDesignRequestNotesRepository,

                                ProjectTaskIndexRepository $projectTaskIndexRepository,
                                ProjectTaskFileAttachmentsRepository $projectTaskFileAttachmentsRepository,
                                ProjectNotesRepository $projectNotesRepository,

                                BrandRepository $brandRepository,
                                TeamRepository $teamRepository,
                                UserRepository $userRepository)
    {
        parent::__construct();

        $this->projectRepository = $projectRepository;

        $this->peRequestRepository = $peRequestRepository;
        $this->subPeRequestTypeRepository = $subPeRequestTypeRepository;
        $this->subNpdDesignRequestTypeRepository = $subNpdDesignRequestTypeRepository;
        $this->subPeRequestIndexRepository = $subPeRequestIndexRepository;
        $this->subNpdDesignRequestIndexRepository = $subNpdDesignRequestIndexRepository;
        $this->taskTypePeRequestRepository = $taskTypePeRequestRepository;
        $this->taskTypeNpdDesignRequestRepository = $taskTypeNpdDesignRequestRepository;
        $this->peRequestTypeFileAttachmentsRepository = $peRequestTypeFileAttachmentsRepository;
        $this->npdDesignRequestTypeFileAttachmentsRepository= $npdDesignRequestTypeFileAttachmentsRepository;
        $this->peRequestNotesRepository = $peRequestNotesRepository;
        $this->npdDesignRequestNotesRepository = $npdDesignRequestNotesRepository;

        $this->projectTaskIndexRepository = $projectTaskIndexRepository;
        $this->projectTaskFileAttachmentsRepository = $projectTaskFileAttachmentsRepository;

        $this->projectNotesRepository = $projectNotesRepository;
        $this->brandRepository = $brandRepository;
        $this->teamRepository = $teamRepository;
        $this->userRepository = $userRepository;
    }

    public function index(Request $request)
    {
        $params = $request->all();
        $params['status'] = 'active';
        $this->data['currentAdminMenu'] = 'npd_design_request';

        $user = auth()->user();
        if($user->role == 'Project Manager'){
            $cur_user = $user->id;
            $params['cur_user'] = $cur_user;
        }
        $options = [
            'per_page' => $this->perPage,
            'order' => [
                'id' => 'asc',
            ],
            'filter' => $params,
        ];
        $this->data['filter'] = $params;
        $this->data['projects'] = $this->taskTypeNpdDesignRequestRepository->findAll($options);
        $this->data['id'] = !empty($params['id']) ? $params['id'] : '';

        return view('admin.npd_design_request.index', $this->data);
    }

    public function assign_page(Request $request)
    {
        $param = $request->all();
        $this->data['currentAdminMenu'] = 'npd_design_assign';

        $user = auth()->user();
        if($user->team == 'Admin') {
            $cur_user = ' ';
        }else if($user->function == 'Design'){ // 디비전이면서 디자이너
            if($user->role == 'Team Lead'){ // 디비전 디자이너 팀리더
                if($user->team == 'Kiss Nail (ND)'){
                    $cur_user = ' and t.design_group ="Kiss Nail" ';
                }else if ($user->team == 'Kiss Lash (LD)'){
                    $cur_user = ' and t.design_group ="Kiss Lash" ';
                }else if($user->team == 'Kiss Hair Care (C&H)'){
                    $cur_user = ' and t.design_group ="Kiss Hair Care" ';
                }else {
                    $cur_user = ' and t.design_group ="' . $user->team . '" ';
                }
            }
        }else if($user->team == 'Brand Design' || $user->team == 'Production Design' || $user->team == 'Industrial Design'){ // 팀이 디자인
            if($user->role == 'Team Lead'){ // 디자인팀 팀리더
                $cur_user = ' and t.design_group ="' . $user->team . '" ';
            }
        }

        if(isset($_GET[''])) {
            $design_group = $param['design_group'];
        }else{
            $design_group = !empty($param['design_group']) ? $param['design_group'] : '';
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
            $assignee = $param['assignee'];
        }else{
            $assignee = !empty($param['assignee']) ? $param['assignee'] : '';
        }

        $this->data['task_list'] = $this->subNpdDesignRequestTypeRepository->get_action_requested_list($cur_user, $design_group, $team, $brand, $assignee);

        $this->data['design_group'] = $design_group;
        $this->data['team'] = $team;
        $this->data['brand'] = $brand;
        $this->data['assignee'] = $assignee;

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
        $this->data['design_group_list'] = [
            'Kiss Nail',
            'Kiss Lash',
            'Kiss Hair Care',
            'Brand Design',
            'Production Design',
            'Industrial Design'
        ];
        $this->data['teams'] = $this->teamRepository->findAll($team_options);
        $this->data['brands'] = $this->brandRepository->findAll($brand_options);


        return view('admin.npd_design_request.assign_page', $this->data);
    }

    public function board(Request $request)
    {
        $param = $request->all();
        $this->data['currentAdminMenu'] = 'npd_design_board';

        $user = auth()->user();
        if($user->team == 'Admin') {
            $cur_user = ' ';
        }else if($user->function == 'Design'){ // 디비전이면서 디자이너
            if($user->role == 'Team Lead'){ // 디비전 디자이너 팀리더
                if($user->team == 'Kiss Nail (ND)'){
                    $cur_user = ' and t.design_group ="Kiss Nail" ';
                }else if ($user->team == 'Kiss Lash (LD)'){
                    $cur_user = ' and t.design_group ="Kiss Lash" ';
                }else if($user->team == 'Kiss Hair Care (C&H)'){
                    $cur_user = ' and t.design_group ="Kiss Hair Care" ';
                }else {
                    $cur_user = ' and t.design_group ="' . $user->team . '" ';
                }
            }else{ // 디비전 디자이너
                $cur_user = ' and t.assignee ="' . $user->id . '" ';
            }
        }else if($user->team == 'Brand Design' || $user->team == 'Production Design' || $user->team == 'Industrial Design'){ // 팀이 디자인
            if($user->role == 'Team Lead'){ // 디자인팀 팀리더
                $cur_user = ' and t.design_group ="' . $user->team . '" ';
            }else{ // 다지인팀 디자이너
                $cur_user = ' and t.assignee ="' . $user->id . '" ';
            }
        }else{
            $cur_user = $this->userRepository->getPageAccess($user);
        }
        if(isset($_GET[''])) {
            $design_group = $param['design_group'];
        }else{
            $design_group = !empty($param['design_group']) ? $param['design_group'] : '';
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
            $assignee = $param['assignee'];
        }else{
            $assignee = !empty($param['assignee']) ? $param['assignee'] : '';
        }

        $this->data['task_list_action_requested'] = $this->subNpdDesignRequestTypeRepository->get_to_do_list($cur_user, $design_group, $team, $brand, $assignee);
        $this->data['task_list_in_progress'] = $this->subNpdDesignRequestTypeRepository->get_in_progress_list($cur_user, $design_group, $team, $brand, $assignee);
        $this->data['task_list_action_review'] = $this->subNpdDesignRequestTypeRepository->get_action_review_list($cur_user, $design_group, $team, $brand, $assignee);
        $this->data['task_list_action_completed'] = $this->subNpdDesignRequestTypeRepository->get_action_completed_list($cur_user, $design_group, $team, $brand, $assignee);

        $this->data['design_group'] = $design_group;
        $this->data['team'] = $team;
        $this->data['brand'] = $brand;
        $this->data['assignee'] = $assignee;

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
        $this->data['design_group_list'] = [
            'Kiss Nail',
            'Kiss Lash',
            'Kiss Hair Care',
            'Brand Design',
            'Production Design',
            'Industrial Design'
        ];
        $this->data['teams'] = $this->teamRepository->findAll($team_options);
        $this->data['brands'] = $this->brandRepository->findAll($brand_options);


        return view('admin.npd_design_request.board', $this->data);

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if(basename(url()->previous()) == 'npd_design_request'){
            $this->data['currentAdminMenu'] = 'npd_design_request';
        }else{
            $this->data['currentAdminMenu'] = 'npd_design_board';
        }

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
        $task_id = $this->subNpdDesignRequestIndexRepository->get_task_id_for_npd_design($id);
        $this->data['task_id']= $task_id;

        // Request Type list
        $this->data['request_type_list'] = $request_type_list = $this->subNpdDesignRequestIndexRepository->get_request_type_list_by_task_id($task_id);

        // task_detail
        if(sizeof($request_type_list)>0){
            foreach ($request_type_list as $k => $request_type){

                $npd_design_request_type_id = $request_type->npd_design_request_type_id;

                $task_files = $this->npdDesignRequestTypeFileAttachmentsRepository->findAllByRequestTypeId($npd_design_request_type_id);
                $request_type_list[$k]->files = $task_files;
            }
        }

        $this->data['designer_list_kiss_nail'] = $this->userRepository->getDesignerListKissNail();
        $this->data['designer_list_kiss_lash'] = $this->userRepository->getDesignerListKissLash();
        $this->data['designer_list_kiss_hair'] = $this->userRepository->getDesignerListKissHair();
        $this->data['designer_list_others'] = $this->userRepository->getDesignerListOthers();
        $this->data['designer_list_industrial_design'] = $this->userRepository->getDesignerListIndustrialDesign();
        $this->data['designer_list_production_design'] = $this->userRepository->getDesignerListProductionDesign();

        $this->data['priorities'] = [
            'Normal', 'Urgent'
        ];

        $this->data['scope_list'] = [
            'Sample (Review or Register Purpose)',
            'Mock-up (Internal/Sales Meeting Purpose)',
            'Production (Release to Outside Vendor)',
        ];

        $this->data['artwork_type_list'] = [
            'New',
            'Revamp',
            'Minor Update',
            'Update',
        ];

        $this->data['sales_channel_list'] = [
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

        // Assignee_list
        $this->data['pe_assignee_list'] = $this->userRepository->getPeAssigneeList();

        // Sample type list
        $this->data['sample_type_list'] = [
            'Fabricate',
            '3D Printer',
            'V-Forming'
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
        $this->data['decline_reason_list'] = [
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
        // Project_notes
        $options = [
            'id' => $task_id,
            'order' => [
                'created_at' => 'desc',
            ]
        ];

        $correspondences = $this->npdDesignRequestNotesRepository->findAll($options);
        $this->data['correspondences'] = $correspondences;
        $this->data['request_type'] = null;

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

        return view('admin.npd_design_request.form', $this->data);
    }


    public function add_npd_design_request(Request $request){

        $user = auth()->user();

        $sub_npd_design_request_index = new SubNpdDesignRequestIndex();
        $sub_npd_design_request_index['task_id'] = $request['npd_design_request_t_id'];
        $sub_npd_design_request_index['request_type'] = $request['request_type'];
        $sub_npd_design_request_index['author_id'] = $user->id;
        $sub_npd_design_request_index['status'] = 'action_requested';
        $sub_npd_design_request_index->save();

        $npd_design_request_type_id = $sub_npd_design_request_index->id;

        $subNpdDesignRequestType = new SubNpdDesignRequestType();
        $subNpdDesignRequestType['id'] = $request['npd_design_request_t_id'];
        $subNpdDesignRequestType['author_id'] = $user->id;
        $subNpdDesignRequestType['type'] = 'npd_design_request_type';
        $subNpdDesignRequestType['npd_design_request_type_id'] = $npd_design_request_type_id;

        $subNpdDesignRequestType['request_type'] = $request['request_type'];

        if($request['request_type'] == 'New Packages'
            || $request['request_type'] == 'Brand Guide Lines'
            || $request['request_type'] == 'Collab Packages / Displays'
            || $request['request_type'] == 'Brand Events: MKT Materials'
            || $request['request_type'] == 'Mailer Box / Insert Cards'
            || $request['request_type'] == 'AD'
            || $request['request_type'] == 'Sign/Light box/Wall graphics'
            || $request['request_type'] == 'Brochure'
            || $request['request_type'] == 'Graphic Bullnose / POP / POG'
            || $request['request_type'] == 'Presentation Board'
            || $request['request_type'] == 'Sales Sheet'
            || $request['request_type'] == 'New Instructions') {

            if($user->team == 'Kiss Nail (ND)'){
                $design_group = 'Kiss Nail';
            }else if($user->team == 'Kiss Lash (LD)'){
                $design_group = 'Kiss Lash';
            }else if($user->team == 'Kiss Hair Care (C&H)' || $user->team == 'Kiss A&A (Red)'){
                $design_group = 'Kiss Hair Care';
            }else{
                $design_group = 'Brand Design';
            }
        } else if($request['request_type'] =="3D Nail Mold Development"
                || $request['request_type'] =="3D Product Design Development"
                || $request['request_type'] =="3D Trade Show Booth Design"
                || $request['request_type'] =="3D Display Design"
                || $request['request_type'] =="3D Motion Graphic"
                || $request['request_type'] =="3D Package Design"
                || $request['request_type'] =="3D Collateral (Rendering & Animation)"
                || $request['request_type'] =="Other 3D Design") {
            $design_group = 'Industrial Design';
        }else{

            $design_group = 'Production Design';
        }
        $subNpdDesignRequestType['design_group'] = $design_group;

        $subNpdDesignRequestType['objective'] = $request['objective'];
        $subNpdDesignRequestType['priority'] = $request['priority'];
        if(isset($request['priority']) && ($request['priority'] == 'Normal')){
            $subNpdDesignRequestType['due_date_urgent'] = null;
            $subNpdDesignRequestType['urgent_reason'] = null;
        }else{
            $subNpdDesignRequestType['due_date_urgent'] = $request['due_date_urgent'];
            $subNpdDesignRequestType['urgent_reason'] = $request['urgent_reason'];
        }
        $subNpdDesignRequestType['due_date'] = $request['due_date'];
        $subNpdDesignRequestType['scope'] = $request['scope'];
        $subNpdDesignRequestType['artwork_type'] = $request['artwork_type'];

//        $subNpdDesignRequestType['sales_channel'] = $request['sales_channel'];

        if (isset($param['sales_channel'])) {
            $subNpdDesignRequestType['sales_channel'] = implode(', ', $param['sales_channel']);
        } else {
            $subNpdDesignRequestType['sales_channel'] = '';
        }


        $subNpdDesignRequestType['if_others_sales_channel'] = $request['if_others_sales_channel'];
        $subNpdDesignRequestType['target_audience'] = $request['target_audience'];
        $subNpdDesignRequestType['head_copy'] = $request['head_copy'];
        $subNpdDesignRequestType['reference'] = $request['reference'];
        $subNpdDesignRequestType['material_number'] = $request['material_number'];
        $subNpdDesignRequestType['component_number'] = $request['component_number'];

        $subNpdDesignRequestType->save();

        $this->correspondence_add_npd_design_request_type($npd_design_request_type_id, $request['request_type'], $sub_npd_design_request_index);

        // add campaign_type_asset_attachments
        if($request->file('npd_design_request_attachment')){
            foreach ($request->file('npd_design_request_attachment') as $file) {
                $attachments = new NpdDesignRequestTypeAttachments();

//                $fileName = $file->storeAs('campaigns/'.$request['npd_design_request_c_id'].'/'.$asset_id, $originalName);
                $fileName = $this->file_exist_check_npd_design($file, $request['npd_design_request_t_id'], $npd_design_request_type_id);

                $attachments['task_id'] = $request['npd_design_request_t_id'];
                $attachments['type'] = 'attachment_file_' . $file->getMimeType();
                $attachments['author_id'] = $user->id;
                $attachments['attachment'] = '/' . $fileName;
                $attachments['npd_design_request_type_id'] = $npd_design_request_type_id;
                $attachments['file_ext'] = pathinfo($fileName, PATHINFO_EXTENSION);
                $attachments['file_type'] = $file->getMimeType();
                $attachments['file_size'] = $file->getSize();
                $attachments['created_at'] = Carbon::now();
                $attachments->save();

                $this->add_file_correspondence_for_npd_design_request($npd_design_request_type_id, $sub_npd_design_request_index->task_id, $user, $fileName, $request['request_type']);
            }
        }

        $project_id = $this->projectTaskIndexRepository->get_project_id_by_task_id($request['npd_design_request_t_id']);

        // Send Notification to Design Team Lead
        $this->send_notification_action_request_assign($project_id, $subNpdDesignRequestType, $npd_design_request_type_id);

        return redirect('admin/npd_design_request/'.$project_id.'/edit#'.$npd_design_request_type_id)
            ->with('success', __('Added the '.$request['request_type']. ' Type : ' . $npd_design_request_type_id));
    }

    public function send_notification_action_request_assign($project_id, $subRequestType, $npd_design_request_type_id)
    {
        // From : Division
        // Receiver : Design Group Team Lead

        $project_obj = $this->projectRepository->findById($project_id);

        if($subRequestType['priority'] == 'Normal'){
            $due_date_mail = $subRequestType['due_date'];
        }else{
            $due_date_mail = $subRequestType['due_date_urgent'];
        }

        // Design Group
        $design_group = $subRequestType['design_group'];

        // Receiver
        if($design_group == 'Kiss Nail'){
            // Team : Kiss Nail (ND)
            // Function : Design
            // Role : Team Lead
            // geunho.kang@kissusa.com
        }else if($design_group == 'Kiss Lash'){
            // Team : Kiss Lash (LD)
            // Function : Design
            // Role : Team Lead
            // arhan@kissusa.com
        }else if($design_group == 'Kiss Hair Care'){
            // Team : Kiss Hair Care (C&H) || Kiss A&A (Red)
            // Function : Design
            // Role : Team Lead
            // flori.ohm@kissusa.com
        }else if($design_group == 'Brand Design'){
            // Team : Brand Design
            // Function :
            // Role : Team Lead
            // sung@kissusa.com
            // taehee.lee@kissusa.com
        }else if($design_group == 'Industrial Design'){
            // Team : Industrial Design
            // Function :
            // Role : Team Lead
            // sec@kissusa.com
        }else if($design_group == 'Production Design'){
            // Team : Production Design
            // Function :
            // Role : Team Lead
            // jaehee.chun@kissusa.com
            // keko@kissusa.com
        }


        // Task Creator
        $task_author_name = $subRequestType->author_obj->first_name . ' ' . $subRequestType->author_obj->last_name;

        $details = [
            'template'          => 'emails.task.assign_request',
            'mail_subject'      => 'Action Requested : MM Request',
            'receiver'          => "Hello MDM Team,",
            'message'           => 'You got a new request from ' . $task_author_name . ".",
            'title'             => "Action Requested : MM Request",
            'project_id'        => $project_id,
            'project_title'     => $project_obj->name,
            'request_id'        => $npd_design_request_type_id,
            'request_type'      => $subRequestType['type'],
            'priority'          => $subRequestType['priority'],
            'due_date'          => $due_date_mail,
            'url'               => '/admin/mm_request/'.$project_id.'/edit#'.$npd_design_request_type_id,
        ];

        $group_rs = $this->userRepository->get_receiver_emails_by_team('MDM');
        foreach ($group_rs as $team_user) {
            $receiver_list[] = $team_user['email'];
        }

        /// Send to receivers
        Mail::to($receiver_list)->send(new TaskStatusNotification($details));

    }


    public function edit_npd_design_request(Request $request, $npd_design_request_type_id)
    {
        $param = $request->all();
//        ddd($param);
        // Permission_check
//        if(!$this->permission_check($param)){
//            return redirect('admin/project/' . $concept_development->id . '/edit#'.$task_id)
//                ->with('error', __('This action is no longer permitted. Please contact an Administrator.'));
//        }

        if (isset($param['sales_channel'])) {
            $param['sales_channel'] = implode(', ', $param['sales_channel']);
        } else {
            $param['sales_channel'] = '';
        }

        if (isset($param['multiple_assignees'])) {
            $param['multiple_assignees'] = implode(', ', $param['multiple_assignees']);
        } else {
            $param['multiple_assignees'] = '';
        }

        $project_id = $this->projectTaskIndexRepository->get_project_id_by_task_id($param['t_id']);
        $npdDesignRequestType = $this->subNpdDesignRequestTypeRepository->findById($npd_design_request_type_id);
        if($this->subNpdDesignRequestTypeRepository->update($npd_design_request_type_id, $param)){
            $user = auth()->user();
            // insert into campaign note for correspondence
            $this->correspondence_update_npd_design_request_type($npdDesignRequestType->request_type, $param, $npdDesignRequestType, $user);
            if($request->file('attachment')){
                foreach ($request->file('attachment') as $file) {
                    $attachments = new NpdDesignRequestTypeAttachments();
                    $fileName = $this->file_exist_check_npd_design($file, $npdDesignRequestType->id, $npd_design_request_type_id);
                    $attachments['task_id'] = $npdDesignRequestType->id; // task_id
                    $attachments['type'] = 'attachment_file_' . $file->getMimeType();
                    $attachments['author_id'] = $user->id;
                    $attachments['attachment'] = '/' . $fileName;
                    $attachments['npd_design_request_type_id'] = $npd_design_request_type_id;
                    $attachments['file_ext'] = pathinfo($fileName, PATHINFO_EXTENSION);
                    $attachments['file_type'] = $file->getMimeType();
                    $attachments['file_size'] = $file->getSize();
                    $attachments['created_at'] = Carbon::now();
                    $attachments->save();
                    // insert file attachment correspondence
                    $this->add_file_correspondence_for_npd_design_request($npd_design_request_type_id, $npdDesignRequestType->id, $user, $fileName, $npdDesignRequestType->request_type);
                }
            }
            return redirect('admin/npd_design_request/'.$project_id.'/edit#'.$npd_design_request_type_id)
                ->with('success', __($npdDesignRequestType->request_type . ' ('.$npd_design_request_type_id.') - Update Success'));
        }
        return redirect('admin/npd_design_request/'.$project_id.'/edit#'.$npd_design_request_type_id)
            ->with('error', __('Update Failed'));
    }



    public function file_exist_check_npd_design($file, $task_id, $npd_design_request_type_id)
    {
        $originalName = $file->getClientOriginalName();
        $destinationFolder = 'storage/npd_design_request/'.$task_id.'/'.$npd_design_request_type_id.'/'.$originalName;

        // If exist same name file, add numberning for version control
        if(file_exists($destinationFolder)){
            if ($pos = strrpos($originalName, '.')) {
                $new_name = substr($originalName, 0, $pos);
                $ext = substr($originalName, $pos);
            }
            $newpath = 'storage/npd_design_request/'.$task_id.'/'.$npd_design_request_type_id.'/'.$originalName;
            $uniq_no = 1;
            while (file_exists($newpath)) {
                $tmp_name = $new_name .'_v'. $uniq_no . $ext;
                $newpath = 'storage/npd_design_request/'.$task_id.'/'.$npd_design_request_type_id.'/'.$tmp_name;
                $uniq_no++;
            }
            $file_name = $tmp_name;
        }else{
            $file_name = $originalName;
        }

        $fileName =$file->storeAs('npd_design_request/'.$task_id.'/'.$npd_design_request_type_id, $file_name);
        return $fileName;
    }

    public function correspondence_add_npd_design_request_type($npd_design_request_type_id, $type_name, $sub_npd_design_request_index)
    {
        $user = auth()->user();
        $type_name = strtoupper($type_name);
        $change_line = "<p>$user->first_name requested <b style='color: #b91d19'>$type_name</b> <b>(#$npd_design_request_type_id)</b></p>";

        $npd_design_request_note = new NpdDesignRequestNotes();
        $npd_design_request_note['id'] = $sub_npd_design_request_index->task_id;
        $npd_design_request_note['user_id'] = $user->id;
        $npd_design_request_note['npd_design_request_type_id'] = $npd_design_request_type_id;
        $npd_design_request_note['task_id'] = $sub_npd_design_request_index->task_id;
        $npd_design_request_note['project_id'] = 0;
        $npd_design_request_note['note'] = $change_line;
        $npd_design_request_note['created_at'] = Carbon::now();
        $npd_design_request_note->save();
    }

    public function add_file_correspondence_for_npd_design_request($npd_design_request_type_id, $task_id, $user, $file_type, $request_type)
    {
        // Insert into project note for correspondence (attachment file)
        $request_type_ =  strtoupper(str_replace('_', ' ', $request_type));

        $change_line  = "<p>$user->first_name has added a new attachment ($file_type) to <b style='color: #b91d19'>$request_type_</b> <b>(#$npd_design_request_type_id)</b></p>";

        $npd_design_request_note = new NpdDesignRequestNotes();
        $npd_design_request_note['id'] = $task_id;
        $npd_design_request_note['user_id'] = $user->id;
        $npd_design_request_note['npd_design_request_type_id'] = $npd_design_request_type_id;
        $npd_design_request_note['task_id'] = $task_id;
        $npd_design_request_note['note'] = $change_line;
        $npd_design_request_note['created_at'] = Carbon::now();
        $npd_design_request_note->save();

    }

    public function correspondence_update_npd_design_request_type($task_type, $new_param, $origin_param, $user)
    {
        // Insert into npd_design_request_note for correspondence
        $new = $this->get_request_type_param($new_param);
        $origin = $origin_param->toArray();
        foreach ($new as $key => $value) {
            if (array_key_exists($key, $origin)) {
                if($new[$key] != null) {
                    if (html_entity_decode($new[$key]) != html_entity_decode($origin[$key])) {
                        if ($key == 'assignee') {
                            if ($origin[$key]) {
                                $origin_user_obj = $this->userRepository->findById($origin[$key]);
                                $changed[$key]['original'] = $origin_user_obj->first_name . ' ' . $origin_user_obj->last_name;
                            } else {
                                $changed[$key]['original'] = '';
                            }
                            $new_user_obj = $this->userRepository->findById($new[$key]);
                            $changed[$key]['new'] = $new_user_obj->first_name . ' ' . $new_user_obj->last_name;
                        } else {
                            $changed[$key]['new'] = $new[$key];
                            $changed[$key]['original'] = $origin[$key];
                        }
                    }
                }
            }
        }
        $task_type_ = strtoupper(str_replace('_', ' ', $task_type));
        $change_line  = "<p>$user->first_name made a change to a <b style='color: #b91d19'>$task_type_</b> <b>(#$origin_param->npd_design_request_type_id)</b></p>";

        if(!empty($changed)){
            foreach ($changed as $label => $change) {

                $label = strtoupper(str_replace('_', ' ', $label));
                $from  = trim($change['original']); // Remove strip tags
                $to    = trim($change['new']);      // Remove strip tags

                $change_line .= "<div class='change_label'><p>$label:</p></div>"
                    . "<div class='change_to'><p>$to</p></div>"
                    . "<div class='change_from'><del><p>$from</p></del></div>";
            }
            $npd_design_request_note = new NpdDesignRequestNotes();
            $npd_design_request_note['id'] = $origin_param->id; // task_id
            $npd_design_request_note['user_id'] = $user->id;
            $npd_design_request_note['npd_design_request_type_id'] = $origin_param->npd_design_request_type_id;
            $npd_design_request_note['task_id'] = $origin_param->id; // task_id
            $npd_design_request_note['project_id'] = 0;
            $npd_design_request_note['note'] = $change_line;
            $npd_design_request_note['created_at'] = Carbon::now();
            $npd_design_request_note->save();
        }
    }

    public function get_request_type_param($data)
    {
        $new = array(
            'objective' => $data['objective'],
            'priority' => $data['priority'],
            'due_date' => $data['due_date'],
            'scope' => $data['scope'],
            'artwork_type' => $data['artwork_type'],
            'sales_channel' => $data['sales_channel'],
            'if_others_sales_channel' => $data['if_others_sales_channel'],
            'target_audience' => $data['target_audience'],
            'head_copy' => $data['head_copy'],
            'reference' => $data['reference'],
            'material_number' => $data['material_number'],
            'component_number' => $data['component_number'],
            'assignee' => isset($data['assignee']) ? $data['assignee'] : null,
            'multiple_assignees' => isset($data['multiple_assignees']) ? $data['multiple_assignees'] : null,
        );

        return $new;

    }

    public function actionReSubmit($id)
    {
        $sub_npd_design_request_index = $this->subNpdDesignRequestIndexRepository->findById($id);
        $param['status'] = 'action_requested';
        $param['updated_at'] = Carbon::now();
        $t_id = $sub_npd_design_request_index->task_id;
        $project_id = $this->projectTaskIndexRepository->get_project_id_by_task_id($t_id);
        if($this->subNpdDesignRequestIndexRepository->update($id, $param)){
            $subNpdDesignRequest_obj = $this->subNpdDesignRequestTypeRepository->get_sub_request_by_npd_design_request_type_id($id);
            $current_revision_cnt = $subNpdDesignRequest_obj['revision_cnt'];
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
            $this->subNpdDesignRequestTypeRepository->update($id, $t_param);
            $this->npd_design_status_correspondence($t_id, $project_id, $sub_npd_design_request_index->request_type, $id, 'Action Requested (Revision)');
            echo '/admin/npd_design_request/'.$project_id.'/edit#'.$id;
        }else{
            echo 'fail';
        }
    }

    public function revision_reason_action_decline(Request $request)
    {
        $param = $request->all();

        $request_type_id = $param['request_type_id'];
        $decline_reason = $param['decline_reason'];
        $decline_reason_note = $param['decline_reason_note'];
        $sub_npd_design_request_index = $this->subNpdDesignRequestIndexRepository->findById($request_type_id);
        $t_id = $sub_npd_design_request_index->task_id;
        $project_id = $this->projectTaskIndexRepository->get_project_id_by_task_id($t_id);

        $params['status'] = 'action_requested';
        $params['updated_at'] = Carbon::now();
        $params['decline_reason'] = $decline_reason;
        $params['decline_reason_note'] = $decline_reason_note;
        if($this->subNpdDesignRequestIndexRepository->update($request_type_id, $params)){

            $subNpdDesignRequest_obj = $this->subNpdDesignRequestTypeRepository->get_sub_request_by_npd_design_request_type_id($request_type_id);
            $current_revision_cnt = $subNpdDesignRequest_obj['revision_cnt'];
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
            $this->subNpdDesignRequestTypeRepository->update($request_type_id, $t_param);

            $user = auth()->user();
            $task_type_ =  strtoupper(str_replace('_', ' ', $sub_npd_design_request_index->request_type));
            $change_line  = "<p>$user->first_name updated the status to <b>Design Decline</b> for <b style='color: #b91d19;'>$task_type_ </b><b>(#$request_type_id)</b>
                            <br> <b style='color: black;'>Decline Reason : $decline_reason </b>
                            <br> <b style='color: black;'>$decline_reason_note </b>
                            </p>";
            $note = new NpdDesignRequestNotes();
            $note['id'] = $t_id;
            $note['user_id'] = $user->id;
            $note['npd_design_request_type_id'] = $request_type_id;
            $note['task_id'] = $t_id;
            $note['project_id'] = $project_id;
            $note['note'] = $change_line;
            $note['created_at'] = Carbon::now();
            $note->save();

            return redirect('admin/npd_design_request/'.$project_id.'/edit#'.$request_type_id)
                ->with('success', __('Data has been Updated.'));
        }

        return redirect('admin/npd_design_request/'.$project_id.'/edit#'.$request_type_id)
            ->with('error', __('Data updates Failed'));
    }

    public function actionDecline($id)
    {
        $sub_npd_design_request_index = $this->subNpdDesignRequestIndexRepository->findById($id);
        $param['status'] = 'action_requested';
        $param['updated_at'] = Carbon::now();
        $t_id = $sub_npd_design_request_index->task_id;
        $project_id = $this->projectTaskIndexRepository->get_project_id_by_task_id($t_id);
        if($this->subNpdDesignRequestIndexRepository->update($id, $param)){
            $subNpdDesignRequest_obj = $this->subNpdDesignRequestTypeRepository->get_sub_request_by_npd_design_request_type_id($id);
            $current_revision_cnt = $subNpdDesignRequest_obj['revision_cnt'];
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
            $this->subNpdDesignRequestTypeRepository->update($id, $t_param);
            $this->npd_design_status_correspondence($t_id, $project_id, $sub_npd_design_request_index->request_type, $id, 'Action Requested (Decline)');
            echo '/admin/npd_design_request/'.$project_id.'/edit#'.$id;
        }else{
            echo 'fail';
        }
    }

    public function actionInProgress($id)
    {
        $param['status'] = 'in_progress';
        $param['updated_at'] = Carbon::now();
        if($this->subNpdDesignRequestIndexRepository->update($id, $param)){
            $sub_npd_design_request_index = $this->subNpdDesignRequestIndexRepository->findById($id);
            $t_id = $sub_npd_design_request_index->task_id;
            $project_id = $this->projectTaskIndexRepository->get_project_id_by_task_id($t_id);
            $this->npd_design_status_correspondence($t_id, $project_id, $sub_npd_design_request_index->request_type, $sub_npd_design_request_index->id, 'In Progress');
            echo '/admin/npd_design_request/'.$project_id.'/edit#'.$id;
        }else{
            echo 'fail';
        }
    }

    public function revision_reason_update_request(Request $request)
    {
        $param = $request->all();
        $request_type_id = $param['request_type_id'];
        $revision_reason = $param['revision_reason'];
        $revision_reason_note = $param['revision_reason_note'];
        $sub_npd_design_request_index = $this->subNpdDesignRequestIndexRepository->findById($request_type_id);
        $t_id = $sub_npd_design_request_index->task_id;
        $project_id = $this->projectTaskIndexRepository->get_project_id_by_task_id($t_id);

        $params['status'] = 'update_required';
        $params['updated_at'] = Carbon::now();
        $params['revision_reason'] = $revision_reason;
        $params['revision_reason_note'] = $revision_reason_note;

        if($this->subNpdDesignRequestIndexRepository->update($request_type_id, $params)){

            $user = auth()->user();
            $task_type_ =  strtoupper(str_replace('_', ' ', $sub_npd_design_request_index->request_type));
            $change_line  = "<p>$user->first_name updated the status to <b>Update Required</b> for <b style='color: #b91d19;'>$task_type_ </b><b>(#$request_type_id)</b>
                            <br> <b style='color: black;'>Revision Reason : $revision_reason </b>
                            <br> <b style='color: black;'>$revision_reason_note </b>
                            </p>";
            $note = new NpdDesignRequestNotes();
            $note['id'] = $t_id;
            $note['user_id'] = $user->id;
            $note['npd_design_request_type_id'] = $request_type_id;
            $note['task_id'] = $t_id;
            $note['project_id'] = $project_id;
            $note['note'] = $change_line;
            $note['created_at'] = Carbon::now();
            $note->save();

            return redirect('admin/npd_design_request/'.$project_id.'/edit#'.$request_type_id)
                ->with('success', __('Data has been Updated.'));
        }

        return redirect('admin/npd_design_request/'.$project_id.'/edit#'.$request_type_id)
            ->with('error', __('Data updates Failed'));

    }

    public function updateRequired($id)
    {
        $sub_npd_design_request_index = $this->subNpdDesignRequestIndexRepository->findById($id);
        $param['status'] = 'update_required';
        $param['updated_at'] = Carbon::now();
        $t_id = $sub_npd_design_request_index->task_id;
        $project_id = $this->projectTaskIndexRepository->get_project_id_by_task_id($t_id);
        if($this->subNpdDesignRequestIndexRepository->update($id, $param)){
            $this->npd_design_status_correspondence($t_id, $project_id, $sub_npd_design_request_index->request_type, $sub_npd_design_request_index->id, 'Update Required');
            echo '/admin/npd_design_request/'.$project_id.'/edit#'.$id;
        }else{
            echo 'fail';
        }
    }

    public function actionReview($id)
    {
        $sub_npd_design_request_index = $this->subNpdDesignRequestIndexRepository->findById($id);
        $param['status'] = 'action_review';
        $param['updated_at'] = Carbon::now();
        $t_id = $sub_npd_design_request_index->task_id;
        $project_id = $this->projectTaskIndexRepository->get_project_id_by_task_id($t_id);
        if($this->subNpdDesignRequestIndexRepository->update($id, $param)){
            $this->npd_design_status_correspondence($t_id, $project_id, $sub_npd_design_request_index->request_type, $sub_npd_design_request_index->id, 'Action Review');
            echo '/admin/npd_design_request/'.$project_id.'/edit#'.$id;
        }else{
            echo 'fail';
        }
    }

    public function actionComplete($id)
    {
        $sub_npd_design_request_index = $this->subNpdDesignRequestIndexRepository->findById($id);
        $param['status'] = 'action_completed';
        $param['updated_at'] = Carbon::now();
        $t_id = $sub_npd_design_request_index->task_id;
        $project_id = $this->projectTaskIndexRepository->get_project_id_by_task_id($t_id);
        if($this->subNpdDesignRequestIndexRepository->update($id, $param)){
            $this->npd_design_status_correspondence($t_id, $project_id, $sub_npd_design_request_index->request_type, $sub_npd_design_request_index->id, 'Action Completed');
            echo '/admin/npd_design_request/'.$project_id.'/edit#'.$id;
        }else{
            echo 'fail';
        }
    }

    public function fileRemove($id)
    {
        $attachment_obj = $this->npdDesignRequestTypeFileAttachmentsRepository->findById($id);
        $file_name = $attachment_obj->attachment;
        $task_id = $attachment_obj->task_id;
        $npd_design_request_type_id = $attachment_obj->npd_design_request_type_id;
        $user = auth()->user();
        if($attachment_obj->delete()){
            $requestTypeIndex = $this->subNpdDesignRequestIndexRepository->findById($npd_design_request_type_id);
            $request_type =  ucwords(str_replace('_', ' ', $requestTypeIndex->request_type));
            $change_line = "<p>$user->first_name removed a attachment ($file_name) on <b style='color: #b91d19'>$request_type</b> <b>(#$npd_design_request_type_id)</b></p>";

            $npd_design_request_note = new NpdDesignRequestNotes();
            $npd_design_request_note['id'] = $task_id; // task_id
            $npd_design_request_note['user_id'] = $user->id;
            $npd_design_request_note['npd_design_request_type_id'] = $npd_design_request_type_id;
            $npd_design_request_note['task_id'] = $task_id; // task_id
            $npd_design_request_note['note'] = $change_line;
            $npd_design_request_note['created_at'] = Carbon::now();
            $npd_design_request_note->save();

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

        $note = new NpdDesignRequestNotes();
        $note['id'] = $t_id;
        $note['user_id'] = $user->id;
        $note['npd_design_request_type_id'] = 0;
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
                'url' => '/admin/npd_design_request/' . $p_id . '/edit#' . $t_id,
            ];
            $receiver_list = explode(',', $email_list);
            Mail::to($receiver_list)->send(new TaskStatusNotification($details));
        }
        return redirect('admin/npd_design_request/'.$p_id.'/edit')
            ->with('success', __('Data has been Updated.'));
    }

    public function requestTypeRemovePermissionCheck($request_type_id){

        $user = auth()->user();
        if($user->role == 'Admin') return true; // admin okay

        $obj = $this->subNpdDesignRequestIndexRepository->findById($request_type_id);
        if($obj->author_id != $user->id){
            return false;
        }
        return true;
    }

    public function requestTypeRemove($request_type_id, $type)
    {
        $obj = $this->subNpdDesignRequestIndexRepository->findById($request_type_id);
        $t_id = $obj->task_id;
        $p_id = $this->projectTaskIndexRepository->get_project_id_by_task_id($t_id);
        if($this->requestTypeRemovePermissionCheck($request_type_id)){
            $this->subNpdDesignRequestIndexRepository->delete($request_type_id);

            // Add correspondence for asset Removed
            $this->npd_design_remove_correspondence($t_id, $p_id, $type, $request_type_id);

            echo '/admin/npd_design_request/'.$p_id.'/edit';
        }else{
            echo 'fail';
        }
    }

    public function npd_design_remove_correspondence($t_id, $p_id, $task_type, $request_type_id)
    {
        // Insert into Project note for correspondence (attachment file)
        $user = auth()->user();
        $task_type_ =  strtoupper(str_replace('_', ' ', $task_type));

        $change_line  = "<b style='color: #b91d19;'>$task_type_ </b><b>(#$request_type_id)</b> has been removed by $user->first_name";

        $note = new NpdDesignRequestNotes();
        $note['id'] = $t_id;
        $note['user_id'] = $user->id;
        $note['npd_design_request_type_id'] = $request_type_id;
        $note['task_id'] = $t_id;
        $note['project_id'] = $p_id;
        $note['note'] = $change_line;
        $note['created_at'] = Carbon::now();
        $note->save();
    }

    public function npd_design_status_correspondence($t_id, $p_id, $task_type, $npd_design_request_type_id, $status)
    {
        // Insert into Project note for correspondence (attachment file)
        $user = auth()->user();
        $task_type_ =  strtoupper(str_replace('_', ' ', $task_type));
        $change_line  = "<p>$user->first_name updated the status to <b>$status</b> for <b style='color: #b91d19;'>$task_type_ </b><b>(#$npd_design_request_type_id)</b></p>";

        $note = new NpdDesignRequestNotes();
        $note['id'] = $t_id;
        $note['user_id'] = $user->id;
        $note['npd_design_request_type_id'] = $npd_design_request_type_id;
        $note['task_id'] = $t_id;
        $note['project_id'] = $p_id;
        $note['note'] = $change_line;
        $note['created_at'] = Carbon::now();
        $note->save();
    }

    public function npd_design_correspondence($t_id, $p_id, $task_type, $npd_design_request_type_id, $status)
    {
        // Insert into Project note for correspondence (attachment file)
        $user = auth()->user();
        $task_type_ =  strtoupper(str_replace('_', ' ', $task_type));
        $change_line  = "<p>$user->first_name $status for <b style='color: #b91d19;'>$task_type_ </b><b>(#$npd_design_request_type_id)</b></p>";

        $note = new NpdDesignRequestNotes();
        $note['id'] = $t_id;
        $note['user_id'] = $user->id;
        $note['npd_design_request_type_id'] = $npd_design_request_type_id;
        $note['task_id'] = $t_id;
        $note['project_id'] = $p_id;
        $note['note'] = $change_line;
        $note['created_at'] = Carbon::now();
        $note->save();
    }

}
