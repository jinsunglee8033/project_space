<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Http\Controllers\NotifyController;
use App\Http\Requests\Admin\DevRequest;
use App\Mail\DevMessage;
use App\Mail\NoteProject;
use App\Mail\TaskStatusNotification;
use App\Models\DevNotes;
use App\Models\MmRequestNotes;
use App\Models\NpdPlannerRequestNotes;
use App\Models\NpdPlannerRequestTypeAttachments;
use App\Models\NpdPoRequestNotes;
use App\Models\ProjectNotes;
use App\Models\ProjectTaskIndex;
use App\Models\ProjectTypeTaskAttachments;
use App\Models\SubNpdPlannerRequestIndex;
use App\Models\SubNpdPlannerRequestType;
use App\Models\TaskTypeMmRequest;
use App\Models\TaskTypeNpdPlannerRequest;
use App\Models\TaskTypeNpdPoRequest;
use App\Models\User;
use App\Repositories\Admin\AssetNotificationUserRepository;
use App\Repositories\Admin\AssetOwnerAssetsRepository;
use App\Repositories\Admin\BrandRepository;
use App\Repositories\Admin\MmRequestNotesRepository;
use App\Repositories\Admin\MmRequestRepository;
use App\Repositories\Admin\NpdPlannerRequestNotesRepository;
use App\Repositories\Admin\NpdPlannerRequestTypeFileAttachmentsRepository;
use App\Repositories\Admin\NpdPoRequestNotesRepository;
use App\Repositories\Admin\SubNpdPlannerRequestIndexRepository;
use App\Repositories\Admin\SubNpdPlannerRequestTypeRepository;
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
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class NpdPlannerRequestController extends Controller
{
    Private $projectRepository;
    Private $projectTaskIndexRepository;
    Private $subNpdPlannerRequestIndexRepository;
    Private $subNpdPlannerRequestTypeRepository;
    Private $npdPlannerRequestTypeFileAttachmentsRepository;
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
        SubNpdPlannerRequestIndexRepository $subNpdPlannerRequestIndexRepository,
        SubNpdPlannerRequestTypeRepository $subNpdPlannerRequestTypeRepository,
        NpdPlannerRequestTypeFileAttachmentsRepository $npdPlannerRequestTypeFileAttachmentsRepository,
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
        $this->subNpdPlannerRequestIndexRepository = $subNpdPlannerRequestIndexRepository;
        $this->subNpdPlannerRequestTypeRepository = $subNpdPlannerRequestTypeRepository;
        $this->npdPlannerRequestTypeFileAttachmentsRepository = $npdPlannerRequestTypeFileAttachmentsRepository;
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
        $this->data['currentAdminMenu'] = 'npd_planner_request_red';
        
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

        $this->data['task_list_action_requested'] = $this->taskTypeNpdPlannerRequestRepository->get_action_requested_list($team, $brand);
        $this->data['task_list_in_progress'] = $this->taskTypeNpdPlannerRequestRepository->get_in_progress_list($team, $brand);
        $this->data['task_list_action_review'] = $this->taskTypeNpdPlannerRequestRepository->get_action_review_list($team, $brand);
        $this->data['task_list_action_completed'] = $this->taskTypeNpdPlannerRequestRepository->get_action_completed_list($team, $brand);

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

        $this->data['teams'] =$this->teamRepository->findAll($team_options);
        $this->data['brands'] =$this->brandRepository->findAll($brand_options);

        return view('admin.npd_planner_request.index', $this->data);
    }

    public function index_red(Request $request)
    {
        $param = $request->all();
        $this->data['currentAdminMenu'] = 'npd_planner_request_red_index';
        $user = auth()->user();

        if('Red Trade Marketing (A&A)' || $user->team == 'SOM' || $user->team == 'Admin') {
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
        $this->data['filter'] = $param;
        $this->data['projects'] = $this->taskTypeNpdPlannerRequestRepository->findAll($options);
        $this->data['id'] = !empty($params['id']) ? $params['id'] : '';

        return view('admin.npd_planner_request.index_red', $this->data);
    }

    public function board_red(Request $request)
    {
        $param = $request->all();
        $this->data['currentAdminMenu'] = 'npd_planner_request_red';

        $user = auth()->user();
        if($user->team == 'Red Trade Marketing (A&A)' || $user->team == 'SOM' || $user->team == 'Admin') {
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
            $team = $param['team'];
        }else{
            $team = !empty($param['team']) ? $param['team'] : '';
        }
        if(isset($_GET[''])) {
            $brand = $param['brand'];
        }else{
            $brand = !empty($param['brand']) ? $param['brand'] : '';
        }

        $this->data['task_list_action_requested'] = $this->taskTypeNpdPlannerRequestRepository->get_action_requested_list($cur_user, 'Red Trade Marketing (A&A)', $request_type, $team, $brand);
        $this->data['task_list_in_progress'] = $this->taskTypeNpdPlannerRequestRepository->get_in_progress_list($cur_user, 'Red Trade Marketing (A&A)', $request_type, $team, $brand);
        $this->data['task_list_action_review'] = $this->taskTypeNpdPlannerRequestRepository->get_action_review_list($cur_user, 'Red Trade Marketing (A&A)', $request_type, $team, $brand);
        $this->data['task_list_action_completed'] = $this->taskTypeNpdPlannerRequestRepository->get_action_completed_list($cur_user, 'Red Trade Marketing (A&A)', $request_type, $team, $brand);

        $this->data['request_type'] = $request_type;
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

        $this->data['request_type_list'] = [
          'project_planner',
          'presale_plan',
          'change_request'
        ];
        $this->data['teams'] =$this->teamRepository->findAll($team_options);
        $this->data['brands'] =$this->brandRepository->findAll($brand_options);

        return view('admin.npd_planner_request.board_red', $this->data);
    }

    public function index_ivy(Request $request)
    {
        $param = $request->all();
        $this->data['currentAdminMenu'] = 'npd_planner_request_ivy_index';

        $user = auth()->user();

        if($user->team == 'B2B Marketing' || $user->team == 'SOM' || $user->team == 'Admin') {
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
        $this->data['filter'] = $param;
        $this->data['projects'] = $this->taskTypeNpdPlannerRequestRepository->findAll_ivy($options);
        $this->data['id'] = !empty($params['id']) ? $params['id'] : '';

        return view('admin.npd_planner_request.index_ivy', $this->data);
    }

    public function board_ivy(Request $request)
    {
        $param = $request->all();
        $this->data['currentAdminMenu'] = 'npd_planner_request_ivy';

        $user = auth()->user();
        if($user->team == 'B2B Marketing' || $user->team == 'SOM' || $user->team == 'Admin') {
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
            $team = $param['team'];
        }else{
            $team = !empty($param['team']) ? $param['team'] : '';
        }
        if(isset($_GET[''])) {
            $brand = $param['brand'];
        }else{
            $brand = !empty($param['brand']) ? $param['brand'] : '';
        }

        $this->data['task_list_action_requested'] = $this->taskTypeNpdPlannerRequestRepository->get_action_requested_list($cur_user, 'B2B Marketing', $request_type, $team, $brand);
        $this->data['task_list_in_progress'] = $this->taskTypeNpdPlannerRequestRepository->get_in_progress_list($cur_user,'B2B Marketing', $request_type, $team, $brand);
        $this->data['task_list_action_review'] = $this->taskTypeNpdPlannerRequestRepository->get_action_review_list($cur_user,'B2B Marketing', $request_type, $team, $brand);
        $this->data['task_list_action_completed'] = $this->taskTypeNpdPlannerRequestRepository->get_action_completed_list($cur_user,'B2B Marketing', $request_type, $team, $brand);

        $this->data['request_type'] = $request_type;
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

        $this->data['request_type_list'] = [
            'project_planner',
            'presale_plan',
            'change_request'
        ];
        $this->data['teams'] =$this->teamRepository->findAll($team_options);
        $this->data['brands'] =$this->brandRepository->findAll($brand_options);

        return view('admin.npd_planner_request.board_ivy', $this->data);
    }

    public function list_ivy(Request $request)
    {
        $param = $request->all();
        $this->data['currentAdminMenu'] = 'npd_planner_request_list_lvy';

        $user = auth()->user();
        if($user->team == 'B2B Marketing' || $user->team == 'SOM' || $user->team == 'Admin') {
            $cur_user = ' ';
        }else{
            $cur_user = $this->userRepository->getPageAccess($user);
        }
//        if(isset($_GET[''])) {
//            $assignee = $param['assignee'];
//        }else{
//            $assignee = !empty($param['assignee']) ? $param['assignee'] : '';
//        }
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

        $this->data['task_list'] = $this->taskTypeNpdPlannerRequestRepository->get_task_list($cur_user, $team, $status);

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

        $this->data['teams'] =$this->teamRepository->findAll($team_options);
        $this->data['brands'] =$this->brandRepository->findAll($brand_options);

        return view('admin.npd_planner_request.list_ivy', $this->data);
    }


    public function edit($id)
    {

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
        $task_id = $this->subNpdPlannerRequestIndexRepository->get_task_id_for_npd_planner($id);
        $this->data['task_id']= $task_id;

        // Request Type list
        $this->data['npd_planner_request_list'] = $request_type_list = $this->subNpdPlannerRequestIndexRepository->get_request_type_list_by_task_id($task_id);

        if(sizeof($request_type_list)>0){
            foreach ($request_type_list as $k => $request_type){

                $npd_planner_request_type_id = $request_type->npd_planner_request_type_id;

                $task_files = $this->npdPlannerRequestTypeFileAttachmentsRepository->findAllByRequestTypeId($npd_planner_request_type_id);
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

        $correspondences = $this->npdPlannerRequestNotesRepository->findAll($options);
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

        if(sizeof($request_type_list)>0) {
            if ($request_type_list[0]->request_group == 'Red Trade Marketing (A&A)') {
                $this->data['currentAdminMenu'] = 'npd_planner_request_red';
            } elseif ($request_type_list[0]->request_group == 'B2B Marketing') {
                $this->data['currentAdminMenu'] = 'npd_planner_request_ivy';
            } else {
                $this->data['currentAdminMenu'] = 'project';
            }
        }

        /////////// NPD NPD Planner Request Task ////////////////////////////////////////////

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

        $this->data['display_plan_list'] = [
            'Clip Strip',
            'Corrugated',
            'Counter Top',
            'Cubby',
            'Floor',
            'Sampler Display',
            'Others',
            'No Display',
        ];

        $this->data['display_type_list'] = [
            'Permanent',
            'One Time',
            'No Display',
        ];

        $this->data['penetration_type_list'] = [
            'Pre-Prepack (1 External Vendor)',
            'Pre-Prepack (2 + External Vendor)',
            'Pre-Prepack (1 External Vendor + Individual Items)',
            'Pre-Prepack (2 + External Vendor + Individual Items)',
            'Individual Item Only (No Prepack)',
            'N/A',
            'Others',
        ];

        $this->data['tester_list'] = [
            'Tester Set Only',
            'Tester Set and Individual Testers',
            'Individual Testers Only',
            'No Testers',
        ];

        $this->data['promotion_items_list'] = [
            'Branded Makeup Bags',
            'Educational Materials',
            'Exclusive SKUS',
            'Flyers',
            'Limited Edition',
            'POP / Posters',
            'PR Video',
            'Promotional Coupons',
            'Sample Sizes',
            'Travel Kits',
            'Virtual Try-on Tool',
            'N/A',
            'Others',
        ];
        $this->data['return_plan_list'] = [
            'Return',
            'Phase in/out',
            'No Return',
            'TBD',
        ];
        $this->data['update_type_list'] = [
            'Postpone',
            'TBD (Discontinue)',
            'Additional Plan (Door)',
            'Change',
            'Revamp',
        ];
        $this->data['change_request_reason_list'] = [
            'Quality Control Issue',
            'Supplier Delays',
            'Change in Product Scope',
            'Logistics & Shipping Issue',
            'Environmental Factors',
            'Forecasting or Demand Planning Issues',
            'Management Direction Change',
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

        $this->data['red_marketing_assignee_list'] = $this->userRepository->getAssigneeListRedMarketing();
        $this->data['ivy_marketing_assignee_list'] = $this->userRepository->getAssigneeListIvyMarketing();
        $this->data['kiss_marketing_assignee_list'] = $this->userRepository->getAssigneeListKissMarketing();

        return view('admin.npd_planner_request.form', $this->data);
    }

    public function add_project_planner(Request $request)
    {
        $user = auth()->user();

        $sub_npd_planner_request_index = new SubNpdPlannerRequestIndex();
        $sub_npd_planner_request_index['task_id'] = $request['project_planner_t_id'];
        $sub_npd_planner_request_index['request_type'] = $request['project_planner_request_type'];
        $sub_npd_planner_request_index['author_id'] = $user->id;
        $sub_npd_planner_request_index['status'] = 'action_requested';
        $sub_npd_planner_request_index->save();

        $npd_planner_request_type_id = $sub_npd_planner_request_index->id;

        $subNpdPlannerRequestType = new SubNpdPlannerRequestType();
        $subNpdPlannerRequestType['id'] = $request['project_planner_t_id'];
        $subNpdPlannerRequestType['author_id'] = $user->id;
        $subNpdPlannerRequestType['type'] = 'npd_planner';
        $subNpdPlannerRequestType['npd_planner_request_type_id'] = $npd_planner_request_type_id;
        $subNpdPlannerRequestType['request_type'] = 'project_planner';
        if($request['project_planner_team'] == 'Red Appliance (A&A)'
            || $request['project_planner_team'] == 'Red Accessory & Jewelry (A&A)'
            || $request['project_planner_team'] == 'Red Fashion & Hair Cap (A&A)'
            || $request['project_planner_team'] == 'Red Brush & Implement (A&A)'){
            $subNpdPlannerRequestType['request_group'] = 'Red Trade Marketing (A&A)';
        }else if($request['project_planner_team'] == 'Ivy Nail (ND)'
            || $request['project_planner_team'] == 'Ivy Lash (LD)'
            || $request['project_planner_team'] == 'Kiss Nail (ND)'
            || $request['project_planner_team'] == 'Ivy Cosmetic (C&H)'
            || $request['project_planner_team'] == 'Ivy Hair Care (C&H)'){
            $subNpdPlannerRequestType['request_group'] = 'B2B Marketing';
        }else {
            $subNpdPlannerRequestType['request_group'] = 'CSS';
        }
        $subNpdPlannerRequestType['project_code'] = $request['project_planner_project_code'];
        $subNpdPlannerRequestType['due_date'] = $request['project_planner_due_date'];
        $subNpdPlannerRequestType['target_door_number'] = $request['project_planner_target_door_number'];
        $subNpdPlannerRequestType['ny_target_receiving_date'] = $request['project_planner_ny_target_receiving_date'];
        $subNpdPlannerRequestType['la_target_receiving_date'] = $request['project_planner_la_target_receiving_date'];
        $subNpdPlannerRequestType['ny_planned_launch_date'] = $request['project_planner_ny_planned_launch_date'];
        $subNpdPlannerRequestType['la_planned_launch_date'] = $request['project_planner_la_planned_launch_date'];
        $subNpdPlannerRequestType['nsp'] = $request['project_planner_nsp'];
        $subNpdPlannerRequestType['srp'] = $request['project_planner_srp'];
        if (isset($request['project_planner_sales_channel'])) {
            $subNpdPlannerRequestType['sales_channel'] = implode(', ', $request['project_planner_sales_channel']);
        } else {
            $subNpdPlannerRequestType['sales_channel'] = '';
        }
        $subNpdPlannerRequestType['if_others_sales_channel'] = $request['project_planner_if_others_sales_channel'];
        $subNpdPlannerRequestType['expected_reorder'] = $request['project_planner_expected_reorder'];
        $subNpdPlannerRequestType['expected_sales'] = $request['project_planner_expected_sales'];
        $subNpdPlannerRequestType['benchmark_item'] = $request['project_planner_benchmark_item'];
        $subNpdPlannerRequestType['actual_sales'] = $request['project_planner_actual_sales'];
        $subNpdPlannerRequestType['display_plan'] = $request['project_planner_display_plan'];
        $subNpdPlannerRequestType['if_others_display_plan'] = $request['project_planner_if_others_display_plan'];
        $subNpdPlannerRequestType['display_type'] = $request['project_planner_display_type'];
        $subNpdPlannerRequestType['penetration_type'] = $request['project_planner_penetration_type'];
        $subNpdPlannerRequestType['if_others_penetration_type'] = $request['project_planner_if_others_penetration_type'];
        $subNpdPlannerRequestType['tester'] = $request['project_planner_tester'];
        if (isset($request['project_planner_promotion_items'])) {
            $subNpdPlannerRequestType['promotion_items'] = implode(', ', $request['project_planner_promotion_items']);
        } else {
            $subNpdPlannerRequestType['promotion_items'] = '';
        }
        $subNpdPlannerRequestType['if_others_promotion_items'] = $request['project_planner_if_others_promotion_items'];
        $subNpdPlannerRequestType['return_plan'] = $request['project_planner_return_plan'];
        $subNpdPlannerRequestType['return_plan_description'] = $request['project_planner_return_plan_description'];

        $subNpdPlannerRequestType->save();

        // new correspondence when adding task
        $this->correspondence_add_npd_planner_request_type($npd_planner_request_type_id, 'PROJECT PLANNER', $sub_npd_planner_request_index);

        // add campaign_type_asset_attachments
        if($request->file('project_planner_attachment')){
            foreach ($request->file('project_planner_attachment') as $file) {
                $attachments = new NpdPlannerRequestTypeAttachments();

//                $fileName = $file->storeAs('campaigns/'.$request['legal_request_c_id'].'/'.$asset_id, $originalName);
                $fileName = $this->file_exist_check_npd_planner($file, $request['project_planner_t_id'], $npd_planner_request_type_id);

                $attachments['task_id'] = $request['project_planner_t_id'];
                $attachments['type'] = 'attachment_file_' . $file->getMimeType();
                $attachments['author_id'] = $user->id;
                $attachments['attachment'] = '/' . $fileName;
                $attachments['npd_planner_request_type_id'] = $npd_planner_request_type_id;
                $attachments['file_ext'] = pathinfo($fileName, PATHINFO_EXTENSION);
                $attachments['file_type'] = $file->getMimeType();
                $attachments['file_size'] = $file->getSize();
                $attachments['created_at'] = Carbon::now();
                $attachments->save();

                $this->add_file_correspondence_for_npd_planner($npd_planner_request_type_id, $subNpdPlannerRequestType->id, $user, $fileName, 'project_planner');
            }
        }

        $project_id = $this->projectTaskIndexRepository->get_project_id_by_task_id($request['project_planner_t_id']);

        return redirect('admin/npd_planner_request/'.$project_id.'/edit#'.$npd_planner_request_type_id)
            ->with('success', __('Added the Project Planner Type : ' . $npd_planner_request_type_id));
    }

    public function edit_project_planner(Request $request, $npd_planner_request_type_id)
    {
        $param = $request->all();

        if (isset($param['sales_channel'])) {
            $param['sales_channel'] = implode(', ', $param['sales_channel']);
        } else {
            $param['sales_channel'] = '';
        }
        if (isset($param['promotion_items'])) {
            $param['promotion_items'] = implode(', ', $param['promotion_items']);
        } else {
            $param['promotion_items'] = '';
        }

        // Permission_check
//        if(!$this->permission_check($param)){
//            return redirect('admin/project/' . $concept_development->id . '/edit#'.$task_id)
//                ->with('error', __('This action is no longer permitted. Please contact an Administrator.'));
//        }

        $project_id = $this->projectTaskIndexRepository->get_project_id_by_task_id($param['project_planner_t_id']);
        $subNpdPlannerRequestType = $this->subNpdPlannerRequestTypeRepository->findById($npd_planner_request_type_id);

        if($this->subNpdPlannerRequestTypeRepository->update($npd_planner_request_type_id, $param)){

            $user = auth()->user();
            // insert into campaign note for correspondence
            $this->correspondence_update_npd_planner_request_type('project_planner', $param, $subNpdPlannerRequestType, $user);

            if($request->file('attachment')){
                foreach ($request->file('attachment') as $file) {
                    $attachments = new NpdPlannerRequestTypeAttachments();

                    $fileName = $this->file_exist_check_npd_planner($file, $subNpdPlannerRequestType->id, $npd_planner_request_type_id);

                    $attachments['task_id'] = $subNpdPlannerRequestType->id; // task_id
                    $attachments['type'] = 'attachment_file_' . $file->getMimeType();
                    $attachments['author_id'] = $user->id;
                    $attachments['attachment'] = '/' . $fileName;
                    $attachments['npd_planner_request_type_id'] = $npd_planner_request_type_id;
                    $attachments['file_ext'] = pathinfo($fileName, PATHINFO_EXTENSION);
                    $attachments['file_type'] = $file->getMimeType();
                    $attachments['file_size'] = $file->getSize();
                    $attachments['created_at'] = Carbon::now();
                    $attachments->save();

                    // insert file attachment correspondence
                    $this->add_file_correspondence_for_npd_planner($npd_planner_request_type_id, $subNpdPlannerRequestType->id, $user, $fileName, 'project_planner');
                }
            }

            return redirect('admin/npd_planner_request/'.$project_id.'/edit#'.$npd_planner_request_type_id)
                ->with('success', __('Project Planner ('.$npd_planner_request_type_id.') - Update Success'));
        }

        return redirect('admin/npd_planner_request/'.$project_id.'/edit#'.$npd_planner_request_type_id)
            ->with('error', __('Update Failed'));
    }

    public function add_presale_plan(Request $request)
    {
        $user = auth()->user();

        $sub_npd_planner_request_index = new SubNpdPlannerRequestIndex();
        $sub_npd_planner_request_index['task_id'] = $request['presale_plan_t_id'];
        $sub_npd_planner_request_index['request_type'] = $request['presale_plan_request_type'];
        $sub_npd_planner_request_index['author_id'] = $user->id;
        $sub_npd_planner_request_index['status'] = 'action_requested';
        $sub_npd_planner_request_index->save();

        $npd_planner_request_type_id = $sub_npd_planner_request_index->id;

        $subNpdPlannerRequestType = new SubNpdPlannerRequestType();
        $subNpdPlannerRequestType['id'] = $request['presale_plan_t_id'];
        $subNpdPlannerRequestType['author_id'] = $user->id;
        $subNpdPlannerRequestType['type'] = 'npd_planner';
        $subNpdPlannerRequestType['npd_planner_request_type_id'] = $npd_planner_request_type_id;
        $subNpdPlannerRequestType['request_type'] = 'presale_plan';
        if($request['presale_plan_team'] == 'Red Appliance (A&A)'
            || $request['presale_plan_team'] == 'Red Accessory & Jewelry (A&A)'
            || $request['presale_plan_team'] == 'Red Fashion & Hair Cap (A&A)'
            || $request['presale_plan_team'] == 'Red Brush & Implement (A&A)'){
            $subNpdPlannerRequestType['request_group'] = 'Red Trade Marketing (A&A)';
        }else if($request['presale_plan_team'] == 'Ivy Nail (ND)'
            || $request['presale_plan_team'] == 'Ivy Lash (LD)'
            || $request['presale_plan_team'] == 'Ivy Cosmetic (C&H)'
            || $request['presale_plan_team'] == 'Ivy Hair Care (C&H)'){
            $subNpdPlannerRequestType['request_group'] = 'B2B Marketing';
        }else {
            $subNpdPlannerRequestType['request_group'] = 'CSS';
        }
        $subNpdPlannerRequestType['project_code'] = $request['presale_plan_project_code'];
        $subNpdPlannerRequestType['due_date'] = $request['presale_plan_due_date'];
        $subNpdPlannerRequestType['target_door_number'] = $request['presale_plan_target_door_number'];

        if (isset($request['presale_plan_promotion_items'])) {
            $subNpdPlannerRequestType['promotion_items'] = implode(', ', $request['presale_plan_promotion_items']);
        } else {
            $subNpdPlannerRequestType['promotion_items'] = '';
        }
        $subNpdPlannerRequestType['if_others_promotion_items'] = $request['presale_plan_if_others_promotion_items'];
        $subNpdPlannerRequestType['return_plan'] = $request['presale_plan_return_plan'];
        $subNpdPlannerRequestType['return_plan_description'] = $request['presale_plan_return_plan_description'];

        $subNpdPlannerRequestType['purpose'] = $request['presale_plan_purpose'];
        $subNpdPlannerRequestType['promotion_conditions'] = $request['presale_plan_promotion_conditions'];
        $subNpdPlannerRequestType['presale_start_date'] = $request['presale_plan_presale_start_date'];
        $subNpdPlannerRequestType['presale_end_date'] = $request['presale_plan_presale_end_date'];
        $subNpdPlannerRequestType['promotion_start_date'] = $request['presale_plan_promotion_start_date'];
        $subNpdPlannerRequestType['promotion_end_date'] = $request['presale_plan_promotion_end_date'];
        $subNpdPlannerRequestType['presale_initial_shipping_start_date'] = $request['presale_plan_presale_initial_shipping_start_date'];

        $subNpdPlannerRequestType->save();

        // new correspondence when adding task
        $this->correspondence_add_npd_planner_request_type($npd_planner_request_type_id, 'PRESALE PLAN', $sub_npd_planner_request_index);

        // add campaign_type_asset_attachments
        if($request->file('presale_plan_attachment')){
            foreach ($request->file('presale_plan_attachment') as $file) {
                $attachments = new NpdPlannerRequestTypeAttachments();

//                $fileName = $file->storeAs('campaigns/'.$request['legal_request_c_id'].'/'.$asset_id, $originalName);
                $fileName = $this->file_exist_check_npd_planner($file, $request['presale_plan_t_id'], $npd_planner_request_type_id);

                $attachments['task_id'] = $request['presale_plan_t_id'];
                $attachments['type'] = 'attachment_file_' . $file->getMimeType();
                $attachments['author_id'] = $user->id;
                $attachments['attachment'] = '/' . $fileName;
                $attachments['npd_planner_request_type_id'] = $npd_planner_request_type_id;
                $attachments['file_ext'] = pathinfo($fileName, PATHINFO_EXTENSION);
                $attachments['file_type'] = $file->getMimeType();
                $attachments['file_size'] = $file->getSize();
                $attachments['created_at'] = Carbon::now();
                $attachments->save();

                $this->add_file_correspondence_for_npd_planner($npd_planner_request_type_id, $subNpdPlannerRequestType->id, $user, $fileName, 'presale_plan');
            }
        }

        $project_id = $this->projectTaskIndexRepository->get_project_id_by_task_id($request['presale_plan_t_id']);

        return redirect('admin/npd_planner_request/'.$project_id.'/edit#'.$npd_planner_request_type_id)
            ->with('success', __('Added the Presale Plan Type : ' . $npd_planner_request_type_id));
    }

    public function edit_presale_plan(Request $request, $npd_planner_request_type_id)
    {
        $param = $request->all();

        if (isset($param['promotion_items'])) {
            $param['promotion_items'] = implode(', ', $param['promotion_items']);
        } else {
            $param['promotion_items'] = '';
        }

        // Permission_check
//        if(!$this->permission_check($param)){
//            return redirect('admin/project/' . $concept_development->id . '/edit#'.$task_id)
//                ->with('error', __('This action is no longer permitted. Please contact an Administrator.'));
//        }

        $project_id = $this->projectTaskIndexRepository->get_project_id_by_task_id($param['presale_plan_t_id']);
        $subNpdPlannerRequestType = $this->subNpdPlannerRequestTypeRepository->findById($npd_planner_request_type_id);

        if($this->subNpdPlannerRequestTypeRepository->update($npd_planner_request_type_id, $param)){

            $user = auth()->user();
            // insert into campaign note for correspondence
            $this->correspondence_update_npd_planner_request_type('presale_plan', $param, $subNpdPlannerRequestType, $user);

            if($request->file('attachment')){
                foreach ($request->file('attachment') as $file) {
                    $attachments = new NpdPlannerRequestTypeAttachments();

                    $fileName = $this->file_exist_check_npd_planner($file, $subNpdPlannerRequestType->id, $npd_planner_request_type_id);

                    $attachments['task_id'] = $subNpdPlannerRequestType->id; // task_id
                    $attachments['type'] = 'attachment_file_' . $file->getMimeType();
                    $attachments['author_id'] = $user->id;
                    $attachments['attachment'] = '/' . $fileName;
                    $attachments['npd_planner_request_type_id'] = $npd_planner_request_type_id;
                    $attachments['file_ext'] = pathinfo($fileName, PATHINFO_EXTENSION);
                    $attachments['file_type'] = $file->getMimeType();
                    $attachments['file_size'] = $file->getSize();
                    $attachments['created_at'] = Carbon::now();
                    $attachments->save();

                    // insert file attachment correspondence
                    $this->add_file_correspondence_for_npd_planner($npd_planner_request_type_id, $subNpdPlannerRequestType->id, $user, $fileName, 'presale_plan');
                }
            }

            return redirect('admin/npd_planner_request/'.$project_id.'/edit#'.$npd_planner_request_type_id)
                ->with('success', __('Presale Plan ('.$npd_planner_request_type_id.') - Update Success'));
        }

        return redirect('admin/npd_planner_request/'.$project_id.'/edit#'.$npd_planner_request_type_id)
            ->with('error', __('Update Failed'));
    }

    public function add_change_request(Request $request)
    {
        $user = auth()->user();

        $sub_npd_planner_request_index = new SubNpdPlannerRequestIndex();
        $sub_npd_planner_request_index['task_id'] = $request['change_request_t_id'];
        $sub_npd_planner_request_index['request_type'] = $request['change_request_request_type'];
        $sub_npd_planner_request_index['author_id'] = $user->id;
        $sub_npd_planner_request_index['status'] = 'action_requested';
        $sub_npd_planner_request_index->save();

        $npd_planner_request_type_id = $sub_npd_planner_request_index->id;

        $subNpdPlannerRequestType = new SubNpdPlannerRequestType();
        $subNpdPlannerRequestType['id'] = $request['change_request_t_id'];
        $subNpdPlannerRequestType['author_id'] = $user->id;
        $subNpdPlannerRequestType['type'] = 'npd_planner';
        $subNpdPlannerRequestType['npd_planner_request_type_id'] = $npd_planner_request_type_id;
        $subNpdPlannerRequestType['request_type'] = 'change_request';
        if($request['change_request_team'] == 'Red Appliance (A&A)'
            || $request['change_request_team'] == 'Red Accessory & Jewelry (A&A)'
            || $request['change_request_team'] == 'Red Fashion & Hair Cap (A&A)'
            || $request['change_request_team'] == 'Red Brush & Implement (A&A)'){
            $subNpdPlannerRequestType['request_group'] = 'Red Trade Marketing (A&A)';
        }else if($request['change_request_team'] == 'Ivy Nail (ND)'
            || $request['change_request_team'] == 'Ivy Lash (LD)'
            || $request['change_request_team'] == 'Ivy Cosmetic (C&H)'
            || $request['change_request_team'] == 'Ivy Hair Care (C&H)'){
            $subNpdPlannerRequestType['request_group'] = 'B2B Marketing';
        }else {
            $subNpdPlannerRequestType['request_group'] = 'CSS';
        }

        $subNpdPlannerRequestType['due_date'] = $request['change_request_due_date'];
        $subNpdPlannerRequestType['target_door_number'] = $request['change_request_target_door_number'];
        $subNpdPlannerRequestType['ny_target_receiving_date'] = $request['change_request_ny_target_receiving_date'];
        $subNpdPlannerRequestType['la_target_receiving_date'] = $request['change_request_la_target_receiving_date'];
        $subNpdPlannerRequestType['ny_planned_launch_date'] = $request['change_request_ny_planned_launch_date'];
        $subNpdPlannerRequestType['la_planned_launch_date'] = $request['change_request_la_planned_launch_date'];

        $subNpdPlannerRequestType['update_type'] = $request['change_request_update_type'];
        $subNpdPlannerRequestType['revised_target_door_number'] = $request['change_request_revised_target_door_number'];
        $subNpdPlannerRequestType['revised_ny_receiving_date'] = $request['change_request_revised_ny_receiving_date'];
        $subNpdPlannerRequestType['revised_la_receiving_date'] = $request['change_request_revised_la_receiving_date'];
        $subNpdPlannerRequestType['revised_ny_launch_date'] = $request['change_request_revised_ny_launch_date'];
        $subNpdPlannerRequestType['revised_la_launch_date'] = $request['change_request_revised_la_launch_date'];
        $subNpdPlannerRequestType['change_request_reason'] = $request['change_request_change_request_reason'];
        $subNpdPlannerRequestType['change_request_detail'] = $request['change_request_change_request_detail'];

        $subNpdPlannerRequestType->save();

        // new correspondence when adding task
        $this->correspondence_add_npd_planner_request_type($npd_planner_request_type_id, 'CHANGE REQUEST', $sub_npd_planner_request_index);

        // add campaign_type_asset_attachments
        if($request->file('change_request_attachment')){
            foreach ($request->file('change_request_attachment') as $file) {
                $attachments = new NpdPlannerRequestTypeAttachments();

//                $fileName = $file->storeAs('campaigns/'.$request['legal_request_c_id'].'/'.$asset_id, $originalName);
                $fileName = $this->file_exist_check_npd_planner($file, $request['change_request_t_id'], $npd_planner_request_type_id);

                $attachments['task_id'] = $request['change_request_t_id'];
                $attachments['type'] = 'attachment_file_' . $file->getMimeType();
                $attachments['author_id'] = $user->id;
                $attachments['attachment'] = '/' . $fileName;
                $attachments['npd_planner_request_type_id'] = $npd_planner_request_type_id;
                $attachments['file_ext'] = pathinfo($fileName, PATHINFO_EXTENSION);
                $attachments['file_type'] = $file->getMimeType();
                $attachments['file_size'] = $file->getSize();
                $attachments['created_at'] = Carbon::now();
                $attachments->save();

                $this->add_file_correspondence_for_npd_planner($npd_planner_request_type_id, $subNpdPlannerRequestType->id, $user, $fileName, 'change_request');
            }
        }

        $project_id = $this->projectTaskIndexRepository->get_project_id_by_task_id($request['change_request_t_id']);

        return redirect('admin/npd_planner_request/'.$project_id.'/edit#'.$npd_planner_request_type_id)
            ->with('success', __('Added the Change Request Type : ' . $npd_planner_request_type_id));
    }

    public function edit_change_request(Request $request, $npd_planner_request_type_id)
    {
        $param = $request->all();

        // Permission_check
//        if(!$this->permission_check($param)){
//            return redirect('admin/project/' . $concept_development->id . '/edit#'.$task_id)
//                ->with('error', __('This action is no longer permitted. Please contact an Administrator.'));
//        }

        $project_id = $this->projectTaskIndexRepository->get_project_id_by_task_id($param['change_request_t_id']);
        $subNpdPlannerRequestType = $this->subNpdPlannerRequestTypeRepository->findById($npd_planner_request_type_id);

        if($this->subNpdPlannerRequestTypeRepository->update($npd_planner_request_type_id, $param)){

            $user = auth()->user();
            // insert into campaign note for correspondence
            $this->correspondence_update_npd_planner_request_type('change_request', $param, $subNpdPlannerRequestType, $user);

            if($request->file('attachment')){
                foreach ($request->file('attachment') as $file) {
                    $attachments = new NpdPlannerRequestTypeAttachments();

                    $fileName = $this->file_exist_check_npd_planner($file, $subNpdPlannerRequestType->id, $npd_planner_request_type_id);

                    $attachments['task_id'] = $subNpdPlannerRequestType->id; // task_id
                    $attachments['type'] = 'attachment_file_' . $file->getMimeType();
                    $attachments['author_id'] = $user->id;
                    $attachments['attachment'] = '/' . $fileName;
                    $attachments['npd_planner_request_type_id'] = $npd_planner_request_type_id;
                    $attachments['file_ext'] = pathinfo($fileName, PATHINFO_EXTENSION);
                    $attachments['file_type'] = $file->getMimeType();
                    $attachments['file_size'] = $file->getSize();
                    $attachments['created_at'] = Carbon::now();
                    $attachments->save();

                    // insert file attachment correspondence
                    $this->add_file_correspondence_for_npd_planner($npd_planner_request_type_id, $subNpdPlannerRequestType->id, $user, $fileName, 'change_request');
                }
            }

            return redirect('admin/npd_planner_request/'.$project_id.'/edit#'.$npd_planner_request_type_id)
                ->with('success', __('Change Request ('.$npd_planner_request_type_id.') - Update Success'));
        }

        return redirect('admin/npd_planner_request/'.$project_id.'/edit#'.$npd_planner_request_type_id)
            ->with('error', __('Update Failed'));
    }


    public function correspondence_update_npd_planner_request_type($task_type, $new_param, $origin_param, $user)
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
        $change_line  = "<p>$user->first_name made a change to a <b style='color: #b91d19'>$task_type_</b> <b>(#$origin_param->npd_planner_request_type_id)</b></p>";

        if(!empty($changed)){
            foreach ($changed as $label => $change) {

                $label = strtoupper(str_replace('_', ' ', $label));
                $from  = trim($change['original']); // Remove strip tags
                $to    = trim($change['new']);      // Remove strip tags

                $change_line .= "<div class='change_label'><p>$label:</p></div>"
                    . "<div class='change_to'><p>$to</p></div>"
                    . "<div class='change_from'><del><p>$from</p></del></div>";
            }
            $npd_planner_request_note = new NpdPlannerRequestNotes();
            $npd_planner_request_note['id'] = $origin_param->id; // task_id
            $npd_planner_request_note['user_id'] = $user->id;
            $npd_planner_request_note['npd_planner_request_type_id'] = $origin_param->npd_planner_request_type_id;
            $npd_planner_request_note['task_id'] = $origin_param->id; // task_id
            $npd_planner_request_note['project_id'] = 0;
            $npd_planner_request_note['note'] = $change_line;
            $npd_planner_request_note['created_at'] = Carbon::now();
            $npd_planner_request_note->save();
        }
    }

    public function get_request_type_param($task_type, $data)
    {
        if($task_type == 'project_planner') {
            $new = array(
                'assignee' => isset($data['assignee']) ? $data['assignee'] : '',
                'project_code' => $data['project_code'],
                'due_date' => $data['due_date'],
                'target_door_number' => $data['target_door_number'],
                'ny_target_receiving_date' => isset($data['ny_target_receiving_date']) ? $data['ny_target_receiving_date'] : '',
                'la_target_receiving_date' => isset($data['la_target_receiving_date']) ? $data['la_target_receiving_date'] : '',
                'ny_planned_launch_date' => isset($data['ny_planned_launch_date']) ? $data['ny_planned_launch_date'] : '',
                'la_planned_launch_date' => isset($data['la_planned_launch_date']) ? $data['la_planned_launch_date'] : '',
                'nsp' => isset($data['nsp']) ? $data['nsp'] : '',
                'srp' => isset($data['srp']) ? $data['srp'] : '',
                'sales_channel' => $data['sales_channel'],
                'if_others_sales_channel' => isset($data['if_others_sales_channel']) ? $data['if_others_sales_channel'] : '',
                'expected_reorder' => isset($data['expected_reorder']) ? $data['expected_reorder'] : '',
                'expected_sales' => isset($data['expected_sales']) ? $data['expected_sales'] : '',
                'benchmark_item' => isset($data['benchmark_item']) ? $data['benchmark_item'] : '',
                'actual_sales' => isset($data['actual_sales']) ? $data['actual_sales'] : '',
                'display_plan' => $data['display_plan'],
                'if_others_display_plan' => isset($data['if_others_display_plan']) ? $data['if_others_display_plan'] : '',
                'display_type' => $data['display_type'],
                'penetration_type' => $data['penetration_type'],
                'if_others_penetration_type' => isset($data['if_others_penetration_type']) ? $data['if_others_penetration_type'] : '',
                'tester' => $data['tester'],
                'promotion_items' => $data['promotion_items'],
                'if_others_promotion_items' => isset($data['if_others_promotion_items']) ? $data['if_others_promotion_items'] : '',
                'return_plan' => $data['return_plan'],
                'return_plan_description' => isset($data['return_plan_description']) ? $data['return_plan_description'] : '',
            );
            return $new;
        }else if($task_type == 'presale_plan'){
            $new = array(
                'assignee' => isset($data['assignee']) ? $data['assignee'] : '',
                'project_code' => $data['project_code'],
                'due_date' => $data['due_date'],
                'target_door_number' => $data['target_door_number'],
                'promotion_items' => $data['promotion_items'],
                'if_others_promotion_items' => isset($data['if_others_promotion_items']) ? $data['if_others_promotion_items'] : '',
                'return_plan' => $data['return_plan'],
                'return_plan_description' => $data['return_plan_description'],
                'purpose' => $data['purpose'],
                'promotion_conditions' => $data['promotion_conditions'],
                'presale_start_date' => $data['presale_start_date'],
                'presale_end_date' => $data['presale_end_date'],
                'promotion_start_date' => $data['promotion_start_date'],
                'promotion_end_date' => $data['promotion_end_date'],
                'presale_initial_shipping_start_date' => $data['presale_initial_shipping_start_date'],
            );
            return $new;
        }else if($task_type == 'change_request'){
            $new = array(
                'assignee' => isset($data['assignee']) ? $data['assignee'] : '',
                'due_date' => $data['due_date'],
                'target_door_number' => $data['target_door_number'],
                'ny_planned_launch_date' => $data['ny_planned_launch_date'],
                'la_planned_launch_date' => $data['la_planned_launch_date'],
                'update_type' => $data['update_type'],
                'revised_target_door_number' => $data['revised_target_door_number'],
                'revised_ny_launch_date' => $data['revised_ny_launch_date'],
                'revised_la_launch_date' => $data['revised_la_launch_date'],
                'change_request_reason' => $data['change_request_reason'],
                'change_request_detail' => $data['change_request_detail'],
            );
            return $new;
        }
    }

    public function add_file_correspondence_for_npd_planner($npd_planner_request_type_id, $task_id, $user, $file_type, $request_type)
    {
        // Insert into project note for correspondence (attachment file)
        $request_type_ =  strtoupper(str_replace('_', ' ', $request_type));

        $change_line  = "<p>$user->first_name has added a new attachment ($file_type) to <b style='color: #b91d19'>$request_type_</b> <b>(#$npd_planner_request_type_id)</b></p>";

        $npd_planner_request_note = new NpdPlannerRequestNotes();
        $npd_planner_request_note['id'] = $task_id;
        $npd_planner_request_note['user_id'] = $user->id;
        $npd_planner_request_note['npd_planner_request_type_id'] = $npd_planner_request_type_id;
        $npd_planner_request_note['task_id'] = $task_id;
        $npd_planner_request_note['note'] = $change_line;
        $npd_planner_request_note['created_at'] = Carbon::now();
        $npd_planner_request_note->save();

    }

    public function file_exist_check_npd_planner($file, $task_id, $npd_planner_request_type_id)
    {
        $originalName = $file->getClientOriginalName();
        $destinationFolder = 'storage/npd_planner_request/'.$task_id.'/'.$npd_planner_request_type_id.'/'.$originalName;

        // If exist same name file, add numbering for version control
        if(file_exists($destinationFolder)){
            if ($pos = strrpos($originalName, '.')) {
                $new_name = substr($originalName, 0, $pos);
                $ext = substr($originalName, $pos);
            }
            $newpath = 'storage/npd_planner_request/'.$task_id.'/'.$npd_planner_request_type_id.'/'.$originalName;
            $uniq_no = 1;
            while (file_exists($newpath)) {
                $tmp_name = $new_name .'_v'. $uniq_no . $ext;
                $newpath = 'storage/npd_planner_request/'.$task_id.'/'.$npd_planner_request_type_id.'/'.$tmp_name;
                $uniq_no++;
            }
            $file_name = $tmp_name;
        }else{
            $file_name = $originalName;
        }

        $fileName =$file->storeAs('npd_planner_request/'.$task_id.'/'.$npd_planner_request_type_id, $file_name);
        return $fileName;
    }

    public function correspondence_add_npd_planner_request_type($npd_planner_request_type_id, $type_name, $sub_npd_planner_request_index)
    {
        $user = auth()->user();
        $type_name = strtoupper($type_name);
        $change_line = "<p>$user->first_name requested <b style='color: #b91d19'>$type_name</b> <b>(#$npd_planner_request_type_id)</b></p>";

        $npd_planner_request_note = new NpdPlannerRequestNotes();
        $npd_planner_request_note['id'] = $sub_npd_planner_request_index->task_id;
        $npd_planner_request_note['user_id'] = $user->id;
        $npd_planner_request_note['npd_planner_request_type_id'] = $npd_planner_request_type_id;
        $npd_planner_request_note['task_id'] = $sub_npd_planner_request_index->task_id;
        $npd_planner_request_note['project_id'] = 0;
        $npd_planner_request_note['note'] = $change_line;
        $npd_planner_request_note['created_at'] = Carbon::now();
        $npd_planner_request_note->save();
    }

    public function actionReSubmit($id)
    {
        $sub_npd_planner_request_index = $this->subNpdPlannerRequestIndexRepository->findById($id);
        $param['status'] = 'action_requested';
        $param['updated_at'] = Carbon::now();
        $t_id = $sub_npd_planner_request_index->task_id;
        $project_id = $this->projectTaskIndexRepository->get_project_id_by_task_id($t_id);
        if($this->subNpdPlannerRequestIndexRepository->update($id, $param)){
            $subNpdPlannerRequest_obj = $this->subNpdPlannerRequestTypeRepository->get_sub_npd_planner_request_by_npd_planner_request_type_id($id);
            $current_revision_cnt = $subNpdPlannerRequest_obj['revision_cnt'];
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
            $this->subNpdPlannerRequestTypeRepository->update($id, $t_param);
            $this->npd_planner_status_correspondence($t_id, $project_id, $sub_npd_planner_request_index->request_type, $id, 'Action Requested (Revision)');
            echo '/admin/npd_design_request/'.$project_id.'/edit#'.$id;
        }else{
            echo 'fail';
        }
    }

    public function actionInProgress($id)
    {
        $sub_npd_planner_request_index = $this->subNpdPlannerRequestIndexRepository->findById($id);
        $param['status'] = 'in_progress';
        $param['updated_at'] = Carbon::now();
        $t_id = $sub_npd_planner_request_index->task_id;
        $project_id = $this->projectTaskIndexRepository->get_project_id_by_task_id($t_id);
        if($this->subNpdPlannerRequestIndexRepository->update($id, $param)){
            $user = auth()->user();
            $param_type['assignee'] = $user->id;
            $this->subNpdPlannerRequestTypeRepository->update($id, $param_type);
            $this->npd_planner_status_correspondence($t_id, $project_id, $sub_npd_planner_request_index->request_type, $id, 'In Progress');
            echo '/admin/npd_planner_request/'.$project_id.'/edit#'.$id;
        }else{
            echo 'fail';
        }
    }

    public function npd_planner_status_correspondence($t_id, $p_id, $task_type, $npd_planner_request_type_id, $status)
    {
        $user = auth()->user();
        $task_type_ =  strtoupper(str_replace('_', ' ', $task_type));
        $change_line  = "<p>$user->first_name updated the status to <b>$status</b> for <b style='color: #b91d19;'>$task_type_ </b><b>(#$npd_planner_request_type_id)</b></p>";

        $note = new NpdPlannerRequestNotes();
        $note['id'] = $t_id;
        $note['user_id'] = $user->id;
        $note['npd_planner_request_type_id'] = $npd_planner_request_type_id;
        $note['task_id'] = $t_id;
        $note['project_id'] = $p_id;
        $note['note'] = $change_line;
        $note['created_at'] = Carbon::now();
        $note->save();
    }

    public function actionReview($id)
    {
        $sub_npd_planner_request_index = $this->subNpdPlannerRequestIndexRepository->findById($id);
        $param['status'] = 'action_review';
        $param['updated_at'] = Carbon::now();
        $t_id = $sub_npd_planner_request_index->task_id;
        $project_id = $this->projectTaskIndexRepository->get_project_id_by_task_id($t_id);
        if($this->subNpdPlannerRequestIndexRepository->update($id, $param)){
            $this->npd_planner_status_correspondence($t_id, $project_id, $sub_npd_planner_request_index->request_type, $id, 'Action Review');
            echo '/admin/npd_planner_request/'.$project_id.'/edit#'.$id;
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
        $sub_npd_planner_request_index = $this->subNpdPlannerRequestIndexRepository->findById($request_type_id);
        $t_id = $sub_npd_planner_request_index->task_id;
        $project_id = $this->projectTaskIndexRepository->get_project_id_by_task_id($t_id);

        $params['status'] = 'update_required';
        $params['updated_at'] = Carbon::now();
        $params['revision_reason'] = $revision_reason;
        $params['revision_reason_note'] = $revision_reason_note;
        if($this->subNpdPlannerRequestIndexRepository->update($request_type_id, $params)){

            $subNpdPlannerRequest_obj = $this->subNpdPlannerRequestTypeRepository->get_sub_npd_planner_request_by_npd_planner_request_type_id($request_type_id);
            $current_revision_cnt = $subNpdPlannerRequest_obj['revision_cnt'];
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
            $this->subNpdPlannerRequestTypeRepository->update($request_type_id, $t_param);

            $user = auth()->user();
            $task_type_ =  strtoupper(str_replace('_', ' ', $sub_npd_planner_request_index->request_type));
            $change_line  = "<p>$user->first_name updated the status to <b>NPD Planner Revision</b> for <b style='color: #b91d19;'>$task_type_ </b><b>(#$request_type_id)</b>
                            <br> <b style='color: black;'>Revision Reason : $revision_reason </b>
                            <br> <b style='color: black;'>$revision_reason_note <b>
                            </p>";
            $note = new NpdPlannerRequestNotes();
            $note['id'] = $t_id;
            $note['user_id'] = $user->id;
            $note['npd_planner_request_type_id'] = $request_type_id;
            $note['task_id'] = $t_id;
            $note['project_id'] = $project_id;
            $note['note'] = $change_line;
            $note['created_at'] = Carbon::now();
            $note->save();

            return redirect('admin/npd_planner_request/'.$project_id.'/edit#'.$request_type_id)
                ->with('success', __('Data has been Updated.'));
        }

        return redirect('admin/npd_planner_request/'.$project_id.'/edit#'.$request_type_id)
            ->with('error', __('Data updates Failed'));
    }

    public function revision_reason_action_decline(Request $request)
    {
        $param = $request->all();

        $request_type_id = $param['request_type_id'];
        $decline_reason = $param['decline_reason'];
        $decline_reason_note = $param['decline_reason_note'];
        $sub_npd_planner_request_index = $this->subNpdPlannerRequestIndexRepository->findById($request_type_id);
        $t_id = $sub_npd_planner_request_index->task_id;
        $project_id = $this->projectTaskIndexRepository->get_project_id_by_task_id($t_id);

        $params['status'] = 'action_requested';
        $params['updated_at'] = Carbon::now();
        $params['decline_reason'] = $decline_reason;
        $params['decline_reason_note'] = $decline_reason_note;
        if($this->subNpdPlannerRequestIndexRepository->update($request_type_id, $params)){

            $subNpdPlannerRequest_obj = $this->subNpdPlannerRequestTypeRepository->get_sub_npd_planner_request_by_npd_planner_request_type_id($request_type_id);
            $current_revision_cnt = $subNpdPlannerRequest_obj['revision_cnt'];
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
            $this->subNpdPlannerRequestTypeRepository->update($request_type_id, $t_param);

            $user = auth()->user();
            $task_type_ =  strtoupper(str_replace('_', ' ', $sub_npd_planner_request_index->request_type));
            $change_line  = "<p>$user->first_name updated the status to <b>Action Decline</b> for <b style='color: #b91d19;'>$task_type_ </b><b>(#$request_type_id)</b>
                            <br> <b style='color: black;'>Decline Reason : $decline_reason </b>
                            <br> <b style='color: black;'>$decline_reason_note </b>
                            </p>";
            $note = new NpdPlannerRequestNotes();
            $note['id'] = $t_id;
            $note['user_id'] = $user->id;
            $note['npd_planner_request_type_id'] = $request_type_id;
            $note['task_id'] = $t_id;
            $note['project_id'] = $project_id;
            $note['note'] = $change_line;
            $note['created_at'] = Carbon::now();
            $note->save();

            return redirect('admin/npd_planner_request/'.$project_id.'/edit#'.$request_type_id)
                ->with('success', __('Data has been Updated.'));
        }

        return redirect('admin/npd_planner_request/'.$project_id.'/edit#'.$request_type_id)
            ->with('error', __('Data updates Failed'));
    }

    public function actionComplete($id)
    {

        $sub_npd_planner_request_index = $this->subNpdPlannerRequestIndexRepository->findById($id);
        $param['status'] = 'action_completed';
        $param['updated_at'] = Carbon::now();
        $t_id = $sub_npd_planner_request_index->task_id;
        $project_id = $this->projectTaskIndexRepository->get_project_id_by_task_id($t_id);
        if($this->subNpdPlannerRequestIndexRepository->update($id, $param)){

            ////////////// Due Date formula for Data Upload //////////
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
            $due_date_upload = $now->format('Y-m-d');
            ///////////////
            $t_param['due_date_upload'] = $due_date_upload;
            $t_param['updated_at'] = Carbon::now();
            $this->subNpdPlannerRequestTypeRepository->update($id, $t_param);

            $this->npd_planner_status_correspondence($t_id, $project_id, $sub_npd_planner_request_index->request_type, $id, 'Action Completed');
            echo '/admin/npd_planner_request/'.$project_id.'/edit#'.$id;
        }else{
            echo 'fail';
        }

    }

    public function actionUpload($id)
    {
        $user = auth()->user();

        $param['uploaded_date'] = Carbon::now();
        $param['uploaded_user'] = $user->id;
        $param['updated_at'] = Carbon::now();

        if($this->subNpdPlannerRequestTypeRepository->update($id, $param)){

            $sub_npd_planner_request_index = $this->subNpdPlannerRequestIndexRepository->findById($id);
            $t_id = $sub_npd_planner_request_index->task_id;
            $project_id = $this->projectTaskIndexRepository->get_project_id_by_task_id($t_id);

            $this->npd_planner_status_correspondence($t_id, $project_id, $sub_npd_planner_request_index->request_type, $id, 'System Uploaded');

            echo '/admin/npd_planner_request/'.$project_id.'/edit#'.$id;
        }else{
            echo 'fail';
        }

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

    public function correspondence_new_npd_planner_request($p_id, $task_name, $projectTaskIndex)
    {
        $user = auth()->user();
        $task_name = strtoupper($task_name);
        $change_line = "<p><b style='color: #b91d19;'>$task_name</b> <b>(#$projectTaskIndex->id)</b> has been created by $user->first_name. </p>";
        $qra_request_note = new NpdPlannerRequestNotes();
        $qra_request_note['id'] = $projectTaskIndex->id;
        $qra_request_note['user_id'] = $user->id;
        $qra_request_note['npd_planner_request_type_id'] = 0;
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

            $qra_request_note = new NpdPlannerRequestNotes();
            $qra_request_note['id'] = $origin_param->id;
            $qra_request_note['user_id'] = $user->id;
            $qra_request_note['npd_planner_request_type_id'] = 0;
            $qra_request_note['task_id'] = $origin_param->task_id;
            $qra_request_note['project_id'] = $origin_param->id;
            $qra_request_note['note'] = $change_line;
            $qra_request_note['created_at'] = Carbon::now();
            $qra_request_note->save();

        }
    }

    public function get_task_param($task_type, $data)
    {
        if ($task_type == 'NPD PLANNER REQUEST') {
            $new = array(
                'assignee' => isset($data['assignee']) ? $data['assignee'] : null,
                'project_code' => isset($data['project_code']) ? $data['project_code'] : null,
                'due_date_review' => isset($data['due_date_review']) ? $data['due_date_review'] : null,
                'target_door_number' => isset($data['target_door_number']) ? $data['target_door_number'] : null,
                'sales_channel' => isset($data['sales_channel']) ? $data['sales_channel'] : null,
                'if_others_sales_channel' => isset($data['if_others_sales_channel']) ? $data['if_others_sales_channel'] : null,
                'expected_reorder_max' => isset($data['expected_reorder_max']) ? $data['expected_reorder_max'] : null,
//                'expected_reorder_low' => isset($data['expected_reorder_low']) ? $data['expected_reorder_low'] : null,
//                'expected_reorder_avg' => isset($data['expected_reorder_avg']) ? $data['expected_reorder_avg'] : null,
                'expected_sales' => isset($data['expected_sales']) ? $data['expected_sales'] : null,
                'benchmark_item' => isset($data['benchmark_item']) ? $data['benchmark_item'] : null,
                'actual_sales' => isset($data['actual_sales']) ? $data['actual_sales'] : null,
                'display_plan' => isset($data['display_plan']) ? $data['display_plan'] : null,
                'if_others_display_plan' => isset($data['if_others_display_plan']) ? $data['if_others_display_plan'] : null,
                'display_type' => isset($data['display_type']) ? $data['display_type'] : null,
                'penetration_type' => isset($data['penetration_type']) ? $data['penetration_type'] : null,
                'if_others_penetration_type' => isset($data['if_others_penetration_type']) ? $data['if_others_penetration_type'] : null,
                'tester' => isset($data['tester']) ? $data['tester'] : null,
                'promotion_items' => isset($data['promotion_items']) ? $data['promotion_items'] : null,
                'if_others_promotion_items' => isset($data['if_others_promotion_items']) ? $data['if_others_promotion_items'] : null,
                'return_plan' => isset($data['return_plan']) ? $data['return_plan'] : null,
                'return_plan_description' => isset($data['return_plan_description']) ? $data['return_plan_description'] : null,
            );
            return $new;
        }
    }

    public function add_file_correspondence_for_task($qc_request, $user, $file_type, $task_type)
    {
        // Insert into project note for correspondence (attachment file)
        $task_type_ =  strtoupper(str_replace('_', ' ', $task_type));

        $change_line  = "<p>$user->first_name has added a new attachment <br><b>$file_type</b><br>to <b style='color: #b91d19;'>$task_type_</b> <b>(#$qc_request->task_id)</b></p>";

        $qra_request_note = new NpdPlannerRequestNotes();
        $qra_request_note['id'] = $qc_request->id;
        $qra_request_note['user_id'] = $user->id;
        $qra_request_note['npd_planner_request_type_id'] = 0;
        $qra_request_note['task_id'] = $qc_request->task_id;
        $qra_request_note['project_id'] = $qc_request->id;
        $qra_request_note['note'] = $change_line;
        $qra_request_note['created_at'] = Carbon::now();
        $qra_request_note->save();

    }

    public function file_exist_check($file, $project_id, $task_id)
    {
        $originalName = $file->getClientOriginalName();
        $destinationFolder = 'storage/npd_planner_request/'.$project_id.'/'.$task_id.'/'.$originalName;

        // If exist same name file, add numberning for version control
        if(file_exists($destinationFolder)){
            if ($pos = strrpos($originalName, '.')) {
                $new_name = substr($originalName, 0, $pos);
                $ext = substr($originalName, $pos);
            }
            $newpath = 'storage/npd_planner_request/'.$project_id.'/'.$task_id.'/'.$originalName;
            $uniq_no = 1;
            while (file_exists($newpath)) {
                $tmp_name = $new_name .'_v'. $uniq_no . $ext;
                $newpath = 'storage/npd_planner_request/'.$project_id.'/'.$task_id.'/'.$tmp_name;
                $uniq_no++;
            }
            $file_name = $tmp_name;
        }else{
            $file_name = $originalName;
        }

        $fileName =$file->storeAs('npd_planner_request/'.$project_id.'/'.$task_id, $file_name);
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

            $request_note = new NpdPlannerRequestNotes();
            $request_note['id'] = $task_id; // task_id
            $request_note['user_id'] = $user->id;
            $request_note['npd_planner_request_type_id'] = 0;
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

        $obj = $this->subNpdPlannerRequestIndexRepository->findById($request_type_id);
        if($obj->author_id != $user->id){
            return false;
        }
        return true;
    }

    public function requestTypeRemove($request_type_id, $type)
    {
        $obj = $this->subNpdPlannerRequestIndexRepository->findById($request_type_id);
        $t_id = $obj->task_id;
        $p_id = $this->projectTaskIndexRepository->get_project_id_by_task_id($t_id);
        if($this->requestTypeRemovePermissionCheck($request_type_id)){
            // Delete from sub_qra_request_index, sub_qra_request_type tables
            $this->subNpdPlannerRequestIndexRepository->delete($request_type_id);
            $this->subNpdPlannerRequestTypeRepository->delete($request_type_id);

            // Add correspondence for asset Removed
            $this->npd_planner_remove_correspondence($t_id, $p_id, $type, $request_type_id);

            echo '/admin/npd_planner_request/'.$p_id.'/edit';
        }else{
            echo 'fail';
        }
    }

    public function npd_planner_remove_correspondence($t_id, $p_id, $task_type, $request_type_id)
    {
        // Insert into Project note for correspondence (attachment file)
        $user = auth()->user();
        $task_type_ =  strtoupper(str_replace('_', ' ', $task_type));

        $change_line  = "<b style='color: #b91d19;'>$task_type_ </b><b>(#$request_type_id)</b> has been removed by $user->first_name";

        $note = new NpdPlannerRequestNotes();
        $note['id'] = $t_id;
        $note['user_id'] = $user->id;
        $note['npd_planner_request_type_id'] = $request_type_id;
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

        $p_id = $param['p_id'];
        $t_id = $param['t_id'];
        $p_title = $param['p_title'];
        $email_list = $param['email_list'];

        $note = new NpdPlannerRequestNotes();
        $note['id'] = $p_id;
        $note['project_id'] = $p_id;
        $note['npd_planner_request_type_id'] = 0;
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
                'url' => '/admin/mm_request/' . $p_id . '/edit#' . $t_id,
            ];
            $receiver_list = explode(',', $email_list);
            Mail::to($receiver_list)->send(new TaskStatusNotification($details));
        }

        return redirect('admin/npd_planner_request/'.$p_id.'/edit')
            ->with('success', __('Data has been Updated.'));
    }

}
