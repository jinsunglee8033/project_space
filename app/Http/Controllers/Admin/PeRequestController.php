<?php

namespace App\Http\Controllers\Admin;

use App\Authorizable;
use App\Http\Controllers\Controller;
use App\Http\Controllers\NotifyController;
use App\Http\Requests\Admin\ProjectRequest;
use App\Http\Requests\Admin\UserRequest;
use App\Mail\MyDemoMail;
use App\Mail\NewProject;
use App\Mail\NewRequest;
use App\Mail\NoteProject;
use App\Mail\SendMail;

use App\Mail\TaskStatusNotification;
use App\Models\LegalRequestNotes;
use App\Models\LegalRequestTypeAttachments;
use App\Models\PeRequestNotes;
use App\Models\PeRequestTypeAttachments;
use App\Models\ProjectNotes;
use App\Models\ProjectTaskIndex;
use App\Models\SubPeRequestIndex;
use App\Models\SubPeRequestType;
use App\Models\User;


use App\Repositories\Admin\BrandRepository;
use App\Repositories\Admin\PeRequestNotesRepository;
use App\Repositories\Admin\PeRequestRepository;
use App\Repositories\Admin\PeRequestTypeFileAttachmentsRepository;
use App\Repositories\Admin\ProjectNotesRepository;
use App\Repositories\Admin\SubPeRequestTypeRepository;
use App\Repositories\Admin\SubPeRequestIndexRepository;
use App\Repositories\Admin\ProjectRepository;
use App\Repositories\Admin\ProjectTaskIndexRepository;
use App\Repositories\Admin\ProjectTaskFileAttachmentsRepository;
use App\Repositories\Admin\TaskTypePeRequestRepository;
use App\Repositories\Admin\TeamRepository;
use App\Repositories\Admin\UserRepository;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class PeRequestController extends Controller
{
    Private $projectRepository;

    Private $subPeRequestIndexRepository;
    Private $subPeRequestTypeRepository;
    Private $peRequestTypeFileAttachmentsRepository;
    Private $peRequestNotesRepository;
    Private $taskTypePeRequestRepository;

    Private $projectTaskFileAttachmentsRepository;
    Private $projectTaskIndexRepository;
    Private $projectNotesRepository;

    Private $brandRepository;
    Private $teamRepository;
    private $userRepository;

    public function __construct(ProjectRepository $projectRepository,

                                PeRequestRepository $peRequestRepository,
                                SubPeRequestTypeRepository $subPeRequestTypeRepository,
                                SubPeRequestIndexRepository $subPeRequestIndexRepository,
                                TaskTypePeRequestRepository $taskTypePeRequestRepository,
                                PeRequestTypeFileAttachmentsRepository $peRequestTypeFileAttachmentsRepository,
                                PeRequestNotesRepository $peRequestNotesRepository,

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
        $this->subPeRequestIndexRepository = $subPeRequestIndexRepository;
        $this->taskTypePeRequestRepository = $taskTypePeRequestRepository;
        $this->peRequestTypeFileAttachmentsRepository = $peRequestTypeFileAttachmentsRepository;
        $this->peRequestNotesRepository = $peRequestNotesRepository;

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
        $this->data['currentAdminMenu'] = 'pe_request';
        $user = auth()->user();
        if($user->team == 'Display (D&P)' || $user->team == 'PE (D&P)' || $user->team == 'Admin') {
            $params['cur_user'] = '';
        }else{
            $params['cur_user'] = $this->userRepository->get_users_by_teams($user);
        }
        $options = [
            'per_page' => $this->perPage,
            'order' => [
                'id' => 'asc',
            ],
            'filter' => $params,
        ];
        $this->data['filter'] = $params;
        $this->data['projects'] = $this->peRequestRepository->findAll($options);
        $this->data['id'] = !empty($params['id']) ? $params['id'] : '';

        return view('admin.pe_request.index', $this->data);
    }

    public function board(Request $request)
    {
        $param = $request->all();
        $this->data['currentAdminMenu'] = 'pe_board';

        $user = auth()->user();
        if($user->team == 'Display (D&P)' || $user->team == 'PE (D&P)' || $user->team == 'Admin') {
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
        $this->data['task_list_action_requested'] = $this->subPeRequestTypeRepository->get_action_requested_list($cur_user, $request_type, $assignee, $team);
        $this->data['task_list_in_progress'] = $this->subPeRequestTypeRepository->get_in_progress_list($cur_user, $request_type, $assignee, $team);
        $this->data['task_list_action_review'] = $this->subPeRequestTypeRepository->get_action_review_list($cur_user, $request_type, $assignee, $team);
        $this->data['task_list_action_completed'] = $this->subPeRequestTypeRepository->get_action_completed_list($cur_user, $request_type, $assignee, $team);

        $this->data['request_type'] = $request_type;
        $this->data['request_type_list'] = [
            'display',
            'engineering_drawing',
            'sample',
            'mold'
        ];
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
        $this->data['team'] = $team;
        $this->data['assignee'] = $assignee;
        $this->data['assignee_list'] = $this->userRepository->getPeAndDisplayAssigneeList();

        return view('admin.pe_request.board', $this->data);

    }

    public function assign_page(Request $request)
    {
        $param = $request->all();
        $this->data['currentAdminMenu'] = 'pe_assign';

        $user = auth()->user();
        if($user->team == 'Display (D&P)' || $user->team == 'PE (D&P)' || $user->team == 'Admin') {
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

        $this->data['task_list'] = $this->subPeRequestTypeRepository->get_assign_list($cur_user, $request_type, $team);

        $this->data['request_type'] = $request_type;
        $this->data['request_type_list'] = [
            'display',
            'engineering_drawing',
            'sample',
            'mold'
        ];
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
        $this->data['team'] = $team;
        $this->data['assignee'] = $assignee;
        $this->data['assignee_list'] = $this->userRepository->getPeAssigneeList();


        return view('admin.pe_request.assign_page', $this->data);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if(basename(url()->previous()) == 'board'){
            $this->data['currentAdminMenu'] = 'pe_board';
        }else{
            $this->data['currentAdminMenu'] = 'pe_request';
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
        $task_id = $this->peRequestRepository->get_task_id_for_pe($id);
        $this->data['task_id']= $task_id;

        // Request Type list
        $this->data['request_type_list'] = $request_type_list = $this->peRequestRepository->get_request_type_list_by_task_id($task_id);

        // task_detail
        if(sizeof($request_type_list)>0){
            foreach ($request_type_list as $k => $request_type){
                $pe_request_type_id = $request_type->pe_request_type_id;

                $task_files = $this->peRequestTypeFileAttachmentsRepository->findAllByRequestTypeId($pe_request_type_id);
                $request_type_list[$k]->files = $task_files;
            }
        }
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
        $this->data['request_category_list'] = [
            'Display Only',
            'Sample Only',
            'Show Display'
        ];
        $this->data['show_type_list'] = [
            'Trade Show',
            'Marketing Show',
            'Consumer Show',
            'Others'
        ];
        $this->data['product_category_list'] = [
            'Nail',
            'Lash',
            'Cosmetic',
            'Hair Care',
            'Appliance',
            'Accessories'
        ];
        $this->data['display_type_list'] = [
            'Vac-Tray',
            'Corrugated',
            'Fabrication',
            'Permanent'
        ];
        $this->data['display_style_list'] = [
            'Counter Top',
            'Floor Stand',
            'Side Kick',
            'Clip Strip',
            'End Cap',
            'Others'
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
            'Production (In-house)',
            'Production (Outside-Local)',
            'Production (Outside-Oversea)',
            'In Transit',
            'Display Received'
        ];
        // Project_notes
        $options = [
            'id' => $task_id,
            'order' => [
                'created_at' => 'desc',
            ]
        ];

        $correspondences = $this->peRequestNotesRepository->findAll($options);
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

        return view('admin.pe_request.form', $this->data);
    }


    public function add_rendering(Request $request){

        $user = auth()->user();

        $sub_pe_request_index = new SubPeRequestIndex();
        $sub_pe_request_index['task_id'] = $request['rendering_t_id'];
        $sub_pe_request_index['request_type'] = $request['rendering_request_type'];
        $sub_pe_request_index['author_id'] = $user->id;
        $sub_pe_request_index['status'] = 'action_requested';
        $sub_pe_request_index->save();

        $pe_request_type_id = $sub_pe_request_index->id;

        $subPeRequestType = new SubPeRequestType();
        $subPeRequestType['id'] = $request['rendering_t_id'];
        $subPeRequestType['author_id'] = $user->id;
        $subPeRequestType['type'] = 'rendering';
        $subPeRequestType['pe_request_type_id'] = $pe_request_type_id;

        $subPeRequestType['request_detail'] = $request['rendering_request_detail'];
        $subPeRequestType['due_date'] = $request['rendering_due_date'];

        $subPeRequestType->save();

        $this->correspondence_add_pe_request_type($pe_request_type_id, 'Rendering', $sub_pe_request_index);

        // add campaign_type_asset_attachments
        if($request->file('rendering_attachment')){
            foreach ($request->file('rendering_attachment') as $file) {
                $attachments = new PeRequestTypeAttachments();

//                $fileName = $file->storeAs('campaigns/'.$request['pe_request_c_id'].'/'.$asset_id, $originalName);
                $fileName = $this->file_exist_check_pe($file, $request['rendering_t_id'], $pe_request_type_id);

                $attachments['task_id'] = $request['rendering_t_id'];
                $attachments['type'] = 'attachment_file_' . $file->getMimeType();
                $attachments['author_id'] = $user->id;
                $attachments['attachment'] = '/' . $fileName;
                $attachments['pe_request_type_id'] = $pe_request_type_id;
                $attachments['file_ext'] = pathinfo($fileName, PATHINFO_EXTENSION);
                $attachments['file_type'] = $file->getMimeType();
                $attachments['file_size'] = $file->getSize();
                $attachments['created_at'] = Carbon::now();
                $attachments->save();

                $this->add_file_correspondence_for_pe($pe_request_type_id, $subPeRequestType->id, $user, $fileName, 'rendering');
            }
        }

        $project_id = $this->projectTaskIndexRepository->get_project_id_by_task_id($request['rendering_t_id']);

        // Send Notification
//        $this->send_notification_action_request($user, $project_id, $subPeRequestType, $pe_request_type_id);

        return redirect('admin/pe_request/'.$project_id.'/edit#'.$pe_request_type_id)
            ->with('success', __('Added the Rendering Type : ' . $pe_request_type_id));
    }

    public function edit_rendering(Request $request, $pe_request_type_id)
    {
        $param = $request->all();
        // Permission_check
//        if(!$this->permission_check($param)){
//            return redirect('admin/project/' . $concept_development->id . '/edit#'.$task_id)
//                ->with('error', __('This action is no longer permitted. Please contact an Administrator.'));
//        }
        $project_id = $this->projectTaskIndexRepository->get_project_id_by_task_id($param['rendering_t_id']);
        $subPeRequestType = $this->subPeRequestTypeRepository->findById($pe_request_type_id);
        if($this->subPeRequestTypeRepository->update($pe_request_type_id, $param)){
            $user = auth()->user();
            // insert into campaign note for correspondence
            $this->correspondence_update_pe_request_type('rendering', $param, $subPeRequestType, $user);
            if($request->file('attachment')){
                foreach ($request->file('attachment') as $file) {
                    $attachments = new PeRequestTypeAttachments();
                    $fileName = $this->file_exist_check_pe($file, $subPeRequestType->id, $pe_request_type_id);
                    $attachments['task_id'] = $subPeRequestType->id; // task_id
                    $attachments['type'] = 'attachment_file_' . $file->getMimeType();
                    $attachments['author_id'] = $user->id;
                    $attachments['attachment'] = '/' . $fileName;
                    $attachments['pe_request_type_id'] = $pe_request_type_id;
                    $attachments['file_ext'] = pathinfo($fileName, PATHINFO_EXTENSION);
                    $attachments['file_type'] = $file->getMimeType();
                    $attachments['file_size'] = $file->getSize();
                    $attachments['created_at'] = Carbon::now();
                    $attachments->save();
                    // insert file attachment correspondence
                    $this->add_file_correspondence_for_pe($pe_request_type_id, $subPeRequestType->id, $user, $fileName, 'contract');
                }
            }
            return redirect('admin/pe_request/'.$project_id.'/edit#'.$pe_request_type_id)
                ->with('success', __('Contract ('.$pe_request_type_id.') - Update Success'));
        }
        return redirect('admin/pe_request/'.$project_id.'/edit#'.$pe_request_type_id)
            ->with('error', __('Update Failed'));
    }


    public function add_display(Request $request){

        $user = auth()->user();

        $sub_pe_request_index = new SubPeRequestIndex();
        $sub_pe_request_index['task_id'] = $request['display_t_id'];
        $sub_pe_request_index['request_type'] = $request['display_request_type'];
        $sub_pe_request_index['author_id'] = $user->id;
        $sub_pe_request_index['status'] = 'action_requested';
        $sub_pe_request_index->save();

        $pe_request_type_id = $sub_pe_request_index->id;

        $subPeRequestType = new SubPeRequestType();
        $subPeRequestType['id'] = $request['display_t_id'];
        $subPeRequestType['author_id'] = $user->id;
        $subPeRequestType['type'] = 'display';
        $subPeRequestType['pe_request_type_id'] = $pe_request_type_id;
        $subPeRequestType['request_category'] = $request['display_request_category'];
        if($subPeRequestType['request_type'] == 'Show Display'){
            $subPeRequestType['show_type'] = $request['display_request_show_type'];
            $subPeRequestType['show_location'] = $request['display_request_show_location'];
        }
        if (isset($request['display_product_category'])) {
            $subPeRequestType['product_category'] = implode(', ', $request['display_product_category']);
        } else {
            $subPeRequestType['product_category'] = '';
        }
        $subPeRequestType['display_type'] = $request['display_display_type'];
        $subPeRequestType['display_style'] = $request['display_display_style'];
        $subPeRequestType['specify_display_style'] = $request['display_specify_display_style'];
        $subPeRequestType['display'] = $request['display_display'];
        $subPeRequestType['total_display_qty'] = $request['display_total_display_qty'];
        $subPeRequestType['display_budget_per_ea'] = $request['display_display_budget_per_ea'];
        $subPeRequestType['display_budget_code'] = $request['display_display_budget_code'];
        $subPeRequestType['due_date'] = $request['display_due_date'];
        $subPeRequestType['display_ready_date'] = $request['display_display_ready_date'];
        if (isset($request['display_account'])) {
            $subPeRequestType['account'] = implode(', ', $request['display_account']);
        } else {
            $subPeRequestType['account'] = '';
        }
        $subPeRequestType['specify_account'] = $request['display_specify_account'];
        $subPeRequestType['additional_information'] = $request['display_additional_information'];

        $subPeRequestType->save();

        $this->correspondence_add_pe_request_type($pe_request_type_id, 'display', $sub_pe_request_index);

        // add campaign_type_asset_attachments
        if($request->file('display_attachment')){
            foreach ($request->file('display_attachment') as $file) {
                $attachments = new PeRequestTypeAttachments();

//                $fileName = $file->storeAs('campaigns/'.$request['pe_request_c_id'].'/'.$asset_id, $originalName);
                $fileName = $this->file_exist_check_pe($file, $request['display_t_id'], $pe_request_type_id);

                $attachments['task_id'] = $request['display_t_id'];
                $attachments['type'] = 'attachment_file_' . $file->getMimeType();
                $attachments['author_id'] = $user->id;
                $attachments['attachment'] = '/' . $fileName;
                $attachments['pe_request_type_id'] = $pe_request_type_id;
                $attachments['file_ext'] = pathinfo($fileName, PATHINFO_EXTENSION);
                $attachments['file_type'] = $file->getMimeType();
                $attachments['file_size'] = $file->getSize();
                $attachments['created_at'] = Carbon::now();
                $attachments->save();

                $this->add_file_correspondence_for_pe($pe_request_type_id, $subPeRequestType->id, $user, $fileName, 'display');
            }
        }

        $project_id = $this->projectTaskIndexRepository->get_project_id_by_task_id($request['display_t_id']);

        // Send Notification
//        $this->send_notification_action_request($user, $project_id, $subPeRequestType, $pe_request_type_id);

        return redirect('admin/pe_request/'.$project_id.'/edit#'.$pe_request_type_id)
            ->with('success', __('Added the Display Type : ' . $pe_request_type_id));
    }

    public function edit_display(Request $request, $pe_request_type_id)
    {
        $param = $request->all();
        // Permission_check
//        if(!$this->permission_check($param)){
//            return redirect('admin/project/' . $concept_development->id . '/edit#'.$task_id)
//                ->with('error', __('This action is no longer permitted. Please contact an Administrator.'));
//        }
        $project_id = $this->projectTaskIndexRepository->get_project_id_by_task_id($param['display_t_id']);
        $subPeRequestType = $this->subPeRequestTypeRepository->findById($pe_request_type_id);

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

        if($this->subPeRequestTypeRepository->update($pe_request_type_id, $param)){
            $user = auth()->user();
            // insert into campaign note for correspondence
            $this->correspondence_update_pe_request_type('display', $param, $subPeRequestType, $user);
            if($request->file('attachment')){
                foreach ($request->file('attachment') as $file) {
                    $attachments = new PeRequestTypeAttachments();
                    $fileName = $this->file_exist_check_pe($file, $subPeRequestType->id, $pe_request_type_id);
                    $attachments['task_id'] = $subPeRequestType->id; // task_id
                    $attachments['type'] = 'attachment_file_' . $file->getMimeType();
                    $attachments['author_id'] = $user->id;
                    $attachments['attachment'] = '/' . $fileName;
                    $attachments['pe_request_type_id'] = $pe_request_type_id;
                    $attachments['file_ext'] = pathinfo($fileName, PATHINFO_EXTENSION);
                    $attachments['file_type'] = $file->getMimeType();
                    $attachments['file_size'] = $file->getSize();
                    $attachments['created_at'] = Carbon::now();
                    $attachments->save();
                    // insert file attachment correspondence
                    $this->add_file_correspondence_for_pe($pe_request_type_id, $subPeRequestType->id, $user, $fileName, 'display');
                }
            }
            return redirect('admin/pe_request/'.$project_id.'/edit#'.$pe_request_type_id)
                ->with('success', __('Contract ('.$pe_request_type_id.') - Update Success'));
        }
        return redirect('admin/pe_request/'.$project_id.'/edit#'.$pe_request_type_id)
            ->with('error', __('Update Failed'));
    }


    public function add_engineering_drawing(Request $request){

        $user = auth()->user();

        $sub_pe_request_index = new SubPeRequestIndex();
        $sub_pe_request_index['task_id'] = $request['engineering_drawing_t_id'];
        $sub_pe_request_index['request_type'] = $request['engineering_drawing_request_type'];
        $sub_pe_request_index['author_id'] = $user->id;
        $sub_pe_request_index['status'] = 'action_requested';
        $sub_pe_request_index->save();

        $pe_request_type_id = $sub_pe_request_index->id;

        $subPeRequestType = new SubPeRequestType();
        $subPeRequestType['id'] = $request['engineering_drawing_t_id'];
        $subPeRequestType['author_id'] = $user->id;
        $subPeRequestType['type'] = 'engineering_drawing';
        $subPeRequestType['pe_request_type_id'] = $pe_request_type_id;

        $subPeRequestType['request_detail'] = $request['engineering_drawing_request_detail'];
        $subPeRequestType['due_date'] = $request['engineering_drawing_due_date'];

        $subPeRequestType->save();

        $this->correspondence_add_pe_request_type($pe_request_type_id, 'engineering_drawing', $sub_pe_request_index);

        // add campaign_type_asset_attachments
        if($request->file('engineering_drawing_attachment')){
            foreach ($request->file('engineering_drawing_attachment') as $file) {
                $attachments = new PeRequestTypeAttachments();

//                $fileName = $file->storeAs('campaigns/'.$request['pe_request_c_id'].'/'.$asset_id, $originalName);
                $fileName = $this->file_exist_check_pe($file, $request['engineering_drawing_t_id'], $pe_request_type_id);

                $attachments['task_id'] = $request['engineering_drawing_t_id'];
                $attachments['type'] = 'attachment_file_' . $file->getMimeType();
                $attachments['author_id'] = $user->id;
                $attachments['attachment'] = '/' . $fileName;
                $attachments['pe_request_type_id'] = $pe_request_type_id;
                $attachments['file_ext'] = pathinfo($fileName, PATHINFO_EXTENSION);
                $attachments['file_type'] = $file->getMimeType();
                $attachments['file_size'] = $file->getSize();
                $attachments['created_at'] = Carbon::now();
                $attachments->save();

                $this->add_file_correspondence_for_pe($pe_request_type_id, $subPeRequestType->id, $user, $fileName, 'engineering_drawing');
            }
        }

        $project_id = $this->projectTaskIndexRepository->get_project_id_by_task_id($request['engineering_drawing_t_id']);

        // Send Notification
//        $this->send_notification_action_request($user, $project_id, $subPeRequestType, $pe_request_type_id);

        return redirect('admin/pe_request/'.$project_id.'/edit#'.$pe_request_type_id)
            ->with('success', __('Added the Engineering Drawing Type : ' . $pe_request_type_id));
    }

    public function edit_engineering_drawing(Request $request, $pe_request_type_id)
    {
        $param = $request->all();
        // Permission_check
//        if(!$this->permission_check($param)){
//            return redirect('admin/project/' . $concept_development->id . '/edit#'.$task_id)
//                ->with('error', __('This action is no longer permitted. Please contact an Administrator.'));
//        }
        $project_id = $this->projectTaskIndexRepository->get_project_id_by_task_id($param['engineering_drawing_t_id']);
        $subPeRequestType = $this->subPeRequestTypeRepository->findById($pe_request_type_id);
        if($this->subPeRequestTypeRepository->update($pe_request_type_id, $param)){
            $user = auth()->user();
            // insert into campaign note for correspondence
            $this->correspondence_update_pe_request_type('engineering_drawing', $param, $subPeRequestType, $user);
            if($request->file('attachment')){
                foreach ($request->file('attachment') as $file) {
                    $attachments = new PeRequestTypeAttachments();
                    $fileName = $this->file_exist_check_pe($file, $subPeRequestType->id, $pe_request_type_id);
                    $attachments['task_id'] = $subPeRequestType->id; // task_id
                    $attachments['type'] = 'attachment_file_' . $file->getMimeType();
                    $attachments['author_id'] = $user->id;
                    $attachments['attachment'] = '/' . $fileName;
                    $attachments['pe_request_type_id'] = $pe_request_type_id;
                    $attachments['file_ext'] = pathinfo($fileName, PATHINFO_EXTENSION);
                    $attachments['file_type'] = $file->getMimeType();
                    $attachments['file_size'] = $file->getSize();
                    $attachments['created_at'] = Carbon::now();
                    $attachments->save();
                    // insert file attachment correspondence
                    $this->add_file_correspondence_for_pe($pe_request_type_id, $subPeRequestType->id, $user, $fileName, 'engineering_drawing');
                }
            }
            return redirect('admin/pe_request/'.$project_id.'/edit#'.$pe_request_type_id)
                ->with('success', __('Engineering Drawing ('.$pe_request_type_id.') - Update Success'));
        }
        return redirect('admin/pe_request/'.$project_id.'/edit#'.$pe_request_type_id)
            ->with('error', __('Update Failed'));
    }

    public function add_sample(Request $request){

        $user = auth()->user();

        $sub_pe_request_index = new SubPeRequestIndex();
        $sub_pe_request_index['task_id'] = $request['sample_t_id'];
        $sub_pe_request_index['request_type'] = $request['sample_request_type'];
        $sub_pe_request_index['author_id'] = $user->id;
        $sub_pe_request_index['status'] = 'action_requested';
        $sub_pe_request_index->save();

        $pe_request_type_id = $sub_pe_request_index->id;

        $subPeRequestType = new SubPeRequestType();
        $subPeRequestType['id'] = $request['sample_t_id'];
        $subPeRequestType['author_id'] = $user->id;
        $subPeRequestType['type'] = 'sample';
        $subPeRequestType['pe_request_type_id'] = $pe_request_type_id;

        $subPeRequestType['request_detail'] = $request['sample_request_detail'];
        $subPeRequestType['total_quantity'] = $request['sample_total_quantity'];
        $subPeRequestType['item_number'] = $request['sample_item_number'];
        $subPeRequestType['color_pattern'] = $request['sample_color_pattern'];
        $subPeRequestType['tooling_budget_code'] = $request['sample_tooling_budget_code'];
        $subPeRequestType['due_date'] = $request['sample_due_date'];

        $subPeRequestType->save();

        $this->correspondence_add_pe_request_type($pe_request_type_id, 'sample', $sub_pe_request_index);

        // add campaign_type_asset_attachments
        if($request->file('sample_attachment')){
            foreach ($request->file('sample_attachment') as $file) {
                $attachments = new PeRequestTypeAttachments();

//                $fileName = $file->storeAs('campaigns/'.$request['pe_request_c_id'].'/'.$asset_id, $originalName);
                $fileName = $this->file_exist_check_pe($file, $request['sample_t_id'], $pe_request_type_id);

                $attachments['task_id'] = $request['contract_t_id'];
                $attachments['type'] = 'attachment_file_' . $file->getMimeType();
                $attachments['author_id'] = $user->id;
                $attachments['attachment'] = '/' . $fileName;
                $attachments['pe_request_type_id'] = $pe_request_type_id;
                $attachments['file_ext'] = pathinfo($fileName, PATHINFO_EXTENSION);
                $attachments['file_type'] = $file->getMimeType();
                $attachments['file_size'] = $file->getSize();
                $attachments['created_at'] = Carbon::now();
                $attachments->save();

                $this->add_file_correspondence_for_pe($pe_request_type_id, $subPeRequestType->id, $user, $fileName, 'sample');
            }
        }

        $project_id = $this->projectTaskIndexRepository->get_project_id_by_task_id($request['sample_t_id']);

        // Send Notification
//        $this->send_notification_action_request($user, $project_id, $subPeRequestType, $pe_request_type_id);

        return redirect('admin/pe_request/'.$project_id.'/edit#'.$pe_request_type_id)
            ->with('success', __('Added the Sample Type : ' . $pe_request_type_id));
    }

    public function edit_sample(Request $request, $pe_request_type_id)
    {
        $param = $request->all();
        // Permission_check
//        if(!$this->permission_check($param)){
//            return redirect('admin/project/' . $concept_development->id . '/edit#'.$task_id)
//                ->with('error', __('This action is no longer permitted. Please contact an Administrator.'));
//        }
        $project_id = $this->projectTaskIndexRepository->get_project_id_by_task_id($param['sample_t_id']);
        $subPeRequestType = $this->subPeRequestTypeRepository->findById($pe_request_type_id);
        if($this->subPeRequestTypeRepository->update($pe_request_type_id, $param)){
            $user = auth()->user();
            // insert into campaign note for correspondence
            $this->correspondence_update_pe_request_type('sample', $param, $subPeRequestType, $user);
            if($request->file('attachment')){
                foreach ($request->file('attachment') as $file) {
                    $attachments = new PeRequestTypeAttachments();
                    $fileName = $this->file_exist_check_pe($file, $subPeRequestType->id, $pe_request_type_id);
                    $attachments['task_id'] = $subPeRequestType->id; // task_id
                    $attachments['type'] = 'attachment_file_' . $file->getMimeType();
                    $attachments['author_id'] = $user->id;
                    $attachments['attachment'] = '/' . $fileName;
                    $attachments['pe_request_type_id'] = $pe_request_type_id;
                    $attachments['file_ext'] = pathinfo($fileName, PATHINFO_EXTENSION);
                    $attachments['file_type'] = $file->getMimeType();
                    $attachments['file_size'] = $file->getSize();
                    $attachments['created_at'] = Carbon::now();
                    $attachments->save();
                    // insert file attachment correspondence
                    $this->add_file_correspondence_for_pe($pe_request_type_id, $subPeRequestType->id, $user, $fileName, 'sample');
                }
            }
            return redirect('admin/pe_request/'.$project_id.'/edit#'.$pe_request_type_id)
                ->with('success', __('Sample ('.$pe_request_type_id.') - Update Success'));
        }
        return redirect('admin/pe_request/'.$project_id.'/edit#'.$pe_request_type_id)
            ->with('error', __('Update Failed'));
    }

    public function add_mold(Request $request){

        $user = auth()->user();

        $sub_pe_request_index = new SubPeRequestIndex();
        $sub_pe_request_index['task_id'] = $request['mold_t_id'];
        $sub_pe_request_index['request_type'] = $request['mold_request_type'];
        $sub_pe_request_index['author_id'] = $user->id;
        $sub_pe_request_index['status'] = 'action_requested';
        $sub_pe_request_index->save();

        $pe_request_type_id = $sub_pe_request_index->id;

        $subPeRequestType = new SubPeRequestType();
        $subPeRequestType['id'] = $request['mold_t_id'];
        $subPeRequestType['author_id'] = $user->id;
        $subPeRequestType['type'] = 'mold';
        $subPeRequestType['pe_request_type_id'] = $pe_request_type_id;

        $subPeRequestType['request_detail'] = $request['mold_request_detail'];
        $subPeRequestType['total_quantity'] = $request['mold_total_quantity'];
        $subPeRequestType['item_number'] = $request['mold_item_number'];
        $subPeRequestType['color_pattern'] = $request['mold_color_pattern'];
        $subPeRequestType['tooling_budget_code'] = $request['mold_tooling_budget_code'];
        $subPeRequestType['due_date'] = $request['mold_due_date'];

        $subPeRequestType->save();

        $this->correspondence_add_pe_request_type($pe_request_type_id, 'mold', $sub_pe_request_index);

        // add campaign_type_asset_attachments
        if($request->file('mold_attachment')){
            foreach ($request->file('mold_attachment') as $file) {
                $attachments = new PeRequestTypeAttachments();

//                $fileName = $file->storeAs('campaigns/'.$request['pe_request_c_id'].'/'.$asset_id, $originalName);
                $fileName = $this->file_exist_check_pe($file, $request['mold_t_id'], $pe_request_type_id);

                $attachments['task_id'] = $request['mold_t_id'];
                $attachments['type'] = 'attachment_file_' . $file->getMimeType();
                $attachments['author_id'] = $user->id;
                $attachments['attachment'] = '/' . $fileName;
                $attachments['pe_request_type_id'] = $pe_request_type_id;
                $attachments['file_ext'] = pathinfo($fileName, PATHINFO_EXTENSION);
                $attachments['file_type'] = $file->getMimeType();
                $attachments['file_size'] = $file->getSize();
                $attachments['created_at'] = Carbon::now();
                $attachments->save();

                $this->add_file_correspondence_for_pe($pe_request_type_id, $subPeRequestType->id, $user, $fileName, 'mold');
            }
        }

        $project_id = $this->projectTaskIndexRepository->get_project_id_by_task_id($request['mold_t_id']);

        // Send Notification
//        $this->send_notification_action_request($user, $project_id, $subPeRequestType, $pe_request_type_id);

        return redirect('admin/pe_request/'.$project_id.'/edit#'.$pe_request_type_id)
            ->with('success', __('Added the Mold Type : ' . $pe_request_type_id));
    }

    public function edit_mold(Request $request, $pe_request_type_id)
    {
        $param = $request->all();
        // Permission_check
//        if(!$this->permission_check($param)){
//            return redirect('admin/project/' . $concept_development->id . '/edit#'.$task_id)
//                ->with('error', __('This action is no longer permitted. Please contact an Administrator.'));
//        }
        $project_id = $this->projectTaskIndexRepository->get_project_id_by_task_id($param['mold_t_id']);
        $subPeRequestType = $this->subPeRequestTypeRepository->findById($pe_request_type_id);
        if($this->subPeRequestTypeRepository->update($pe_request_type_id, $param)){
            $user = auth()->user();
            // insert into campaign note for correspondence
            $this->correspondence_update_pe_request_type('mold', $param, $subPeRequestType, $user);
            if($request->file('attachment')){
                foreach ($request->file('attachment') as $file) {
                    $attachments = new PeRequestTypeAttachments();
                    $fileName = $this->file_exist_check_pe($file, $subPeRequestType->id, $pe_request_type_id);
                    $attachments['task_id'] = $subPeRequestType->id; // task_id
                    $attachments['type'] = 'attachment_file_' . $file->getMimeType();
                    $attachments['author_id'] = $user->id;
                    $attachments['attachment'] = '/' . $fileName;
                    $attachments['pe_request_type_id'] = $pe_request_type_id;
                    $attachments['file_ext'] = pathinfo($fileName, PATHINFO_EXTENSION);
                    $attachments['file_type'] = $file->getMimeType();
                    $attachments['file_size'] = $file->getSize();
                    $attachments['created_at'] = Carbon::now();
                    $attachments->save();
                    // insert file attachment correspondence
                    $this->add_file_correspondence_for_pe($pe_request_type_id, $subPeRequestType->id, $user, $fileName, 'mold');
                }
            }
            return redirect('admin/pe_request/'.$project_id.'/edit#'.$pe_request_type_id)
                ->with('success', __('Mold ('.$pe_request_type_id.') - Update Success'));
        }
        return redirect('admin/pe_request/'.$project_id.'/edit#'.$pe_request_type_id)
            ->with('error', __('Update Failed'));
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
            'mail_subject'      => 'Action Requested : Display & PE Request',
            'template'          => 'emails.task.new_request',
            'receiver'          => "Display & PE Team",
            'title'             => "Action Requested : Display & PE Request",
            'body'              => 'You got a new request from ' . $user->team . ', ' . $user->first_name . ' ' . $user->last_name . '. ',
            'project_id'        => $project_id,
            'project_title'     => $project_obj->name,
            'request_id'        => $request_type_id,
            'request_type'      => $subRequestType['type'],
            'priority'          => $priority_mail,
            'due_date'          => $due_date_mail,
            'url'               => '/admin/pe_request/'.$project_id.'/edit#'.$request_type_id,
        ];

        $group_rs = $this->userRepository->get_receiver_emails_by_team('Admin');
        foreach ($group_rs as $team_user) {
            $receiver_list[] = $team_user['email'];
        }

        /// Send to receivers
        Mail::to($receiver_list)->send(new NewRequest($details));
    }

    public function file_exist_check_pe($file, $task_id, $pe_request_type_id)
    {
        $originalName = $file->getClientOriginalName();
        $destinationFolder = 'storage/pe_request/'.$task_id.'/'.$pe_request_type_id.'/'.$originalName;

        // If exist same name file, add numberning for version control
        if(file_exists($destinationFolder)){
            if ($pos = strrpos($originalName, '.')) {
                $new_name = substr($originalName, 0, $pos);
                $ext = substr($originalName, $pos);
            }
            $newpath = 'storage/pe_request/'.$task_id.'/'.$pe_request_type_id.'/'.$originalName;
            $uniq_no = 1;
            while (file_exists($newpath)) {
                $tmp_name = $new_name .'_v'. $uniq_no . $ext;
                $newpath = 'storage/pe_request/'.$task_id.'/'.$pe_request_type_id.'/'.$tmp_name;
                $uniq_no++;
            }
            $file_name = $tmp_name;
        }else{
            $file_name = $originalName;
        }

        $fileName =$file->storeAs('pe_request/'.$task_id.'/'.$pe_request_type_id, $file_name);
        return $fileName;
    }

    public function correspondence_add_pe_request_type($pe_request_type_id, $type_name, $sub_pe_request_index)
    {
        $user = auth()->user();
        $type_name = strtoupper($type_name);
        $change_line = "<p>$user->first_name requested <b style='color: #b91d19'>$type_name</b> <b>(#$pe_request_type_id)</b></p>";

        $pe_request_note = new PeRequestNotes();
        $pe_request_note['id'] = $sub_pe_request_index->task_id;
        $pe_request_note['user_id'] = $user->id;
        $pe_request_note['pe_request_type_id'] = $pe_request_type_id;
        $pe_request_note['task_id'] = $sub_pe_request_index->task_id;
        $pe_request_note['project_id'] = 0;
        $pe_request_note['note'] = $change_line;
        $pe_request_note['created_at'] = Carbon::now();
        $pe_request_note->save();
    }

    public function add_file_correspondence_for_pe($pe_request_type_id, $task_id, $user, $file_type, $request_type)
    {
        // Insert into project note for correspondence (attachment file)
        $request_type_ =  strtoupper(str_replace('_', ' ', $request_type));

        $change_line  = "<p>$user->first_name has added a new attachment ($file_type) to <b style='color: #b91d19'>$request_type_</b> <b>(#$pe_request_type_id)</b></p>";

        $pe_request_note = new PeRequestNotes();
        $pe_request_note['id'] = $task_id;
        $pe_request_note['user_id'] = $user->id;
        $pe_request_note['pe_request_type_id'] = $pe_request_type_id;
        $pe_request_note['task_id'] = $task_id;
        $pe_request_note['note'] = $change_line;
        $pe_request_note['created_at'] = Carbon::now();
        $pe_request_note->save();

    }

    public function correspondence_update_pe_request_type($task_type, $new_param, $origin_param, $user)
    {
        // Insert into pe_request_note for correspondence
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
        $change_line  = "<p>$user->first_name made a change to a <b style='color: #b91d19'>$task_type_</b> <b>(#$origin_param->pe_request_type_id)</b></p>";

        if(!empty($changed)){
            foreach ($changed as $label => $change) {

                $label = strtoupper(str_replace('_', ' ', $label));
                $from  = trim($change['original']); // Remove strip tags
                $to    = trim($change['new']);      // Remove strip tags

                $change_line .= "<div class='change_label'><p>$label:</p></div>"
                    . "<div class='change_to'><p>$to</p></div>"
                    . "<div class='change_from'><del><p>$from</p></del></div>";
            }
            $pe_request_note = new PeRequestNotes();
            $pe_request_note['id'] = $origin_param->id; // task_id
            $pe_request_note['user_id'] = $user->id;
            $pe_request_note['pe_request_type_id'] = $origin_param->pe_request_type_id;
            $pe_request_note['task_id'] = $origin_param->id; // task_id
            $pe_request_note['project_id'] = 0;
            $pe_request_note['note'] = $change_line;
            $pe_request_note['created_at'] = Carbon::now();
            $pe_request_note->save();
        }
    }

    public function get_request_type_param($task_type, $data)
    {
        if($task_type == 'display'){
            $new = array(
                'request_category' => $data['request_category'],
                'product_category' => $data['product_category'],
                'display_type' => $data['display_type'],
                'display_style' => $data['display_style'],
                'display' => $data['display'],
                'total_display_qty' => $data['total_display_qty'],
                'display_budget_per_ea' => $data['display_budget_per_ea'],
                'display_budget_code' => $data['display_budget_code'],
                'account' => $data['account'],
                'specify_account' => $data['specify_account'],
                'additional_information' => $data['additional_information'],
                'assignee' => $data['assignee'],
                'display_ready_date' => $data['display_ready_date'],
                'task_category' => $data['task_category'],
                'kdc_delivery_due_date' => $data['kdc_delivery_due_date'],
            );
            return $new;
        }else if($task_type == 'engineering_drawing'){
            $new = array(
                'request_detail' => $data['request_detail'],
                'due_date' => $data['due_date'],
                'assignee' => $data['assignee'],
            );
            return $new;
        }else if($task_type == 'sample'){
            $new = array(
                'request_detail' => $data['request_detail'],
                'total_quantity' => $data['total_quantity'],
                'item_number' => $data['item_number'],
                'color_pattern' => $data['color_pattern'],
                'tooling_budget_code' => $data['tooling_budget_code'],
                'due_date' => $data['due_date'],
                'assignee' => $data['assignee'],
                'design_start_date' => $data['design_start_date'],
                'design_finish_date' => $data['design_finish_date'],
                'sample_start_date' => $data['sample_start_date'],
                'sample_finish_date' => $data['sample_finish_date'],
                'sample_type' => $data['sample_type'],
                'sample_quantity' => $data['sample_quantity']
            );
            return $new;
        }else if($task_type == 'mold'){
            $new = array(
                'request_detail' => $data['request_detail'],
                'total_quantity' => $data['total_quantity'],
                'item_number' => $data['item_number'],
                'color_pattern' => $data['color_pattern'],
                'tooling_budget_code' => $data['tooling_budget_code'],
                'due_date' => $data['due_date'],
                'assignee' => $data['assignee'],
                'mold_design_start_date' => $data['mold_design_start_date'],
                'mold_design_finish_date' => $data['mold_design_finish_date'],
                'cam_start_date' => $data['cam_start_date'],
                'cam_finish_date' => $data['cam_finish_date'],
                'machining_start_date' => $data['machining_start_date'],
                'machining_finish_date' => $data['machining_finish_date'],
                'machining_cost' => $data['machining_cost']
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
        $sub_pe_request_index = $this->subPeRequestIndexRepository->findById($request_type_id);
        $t_id = $sub_pe_request_index->task_id;
        $sub_task_type = strtoupper($sub_pe_request_index->request_type);
        $project_id = $this->projectTaskIndexRepository->get_project_id_by_task_id($t_id);

        $params['status'] = 'action_requested';
        $params['updated_at'] = Carbon::now();
        $params['revision_reason'] = $revision_reason;
        $params['revision_reason_note'] = $revision_reason_note;
        if($this->subPeRequestIndexRepository->update($request_type_id, $params)){

            $subPeRequestType_obj = $this->subPeRequestTypeRepository->get_sub_pe_request_by_pe_request_type_id($request_type_id);
            $current_revision_cnt = $subPeRequestType_obj['revision_cnt'];
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
            $this->subPeRequestTypeRepository->update($request_type_id, $t_param);

            $user = auth()->user();
            $change_line  = "<p>$user->first_name updated the status to <b>DISPLAY & PE Revision</b> for <b style='color: #b91d19;'>$sub_task_type </b><b>(#$request_type_id)</b>
                            <br> <b style='color: black;'>Revision Reason : $revision_reason <b>
                            <br> <b style='color: black;'>$revision_reason_note </b>
                            </p>";
            $note = new PeRequestNotes();
            $note['id'] = $t_id;
            $note['user_id'] = $user->id;
            $note['pe_request_type_id'] = $request_type_id;
            $note['task_id'] = $t_id;
            $note['project_id'] = $project_id;
            $note['note'] = $change_line;
            $note['created_at'] = Carbon::now();
            $note->save();

            return redirect('admin/pe_request/'.$project_id.'/edit#'.$request_type_id)
                ->with('success', __('Data has been Updated.'));
        }

        return redirect('admin/pe_request/'.$project_id.'/edit#'.$request_type_id)
            ->with('error', __('Data updates Failed'));
    }

    public function actionReSubmit($id)
    {
        $sub_pe_request_index = $this->subPeRequestIndexRepository->findById($id);
        $param['status'] = 'action_requested';
        $param['updated_at'] = Carbon::now();
        $t_id = $sub_pe_request_index->task_id;
        $project_id = $this->projectTaskIndexRepository->get_project_id_by_task_id($t_id);
        if($this->subPeRequestIndexRepository->update($id, $param)){
            $subPeRequest_obj = $this->subPeRequestTypeRepository->get_sub_pe_request_by_pe_request_type_id($id);
            $current_revision_cnt = $subPeRequest_obj['revision_cnt'];
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
            $this->subPeRequestTypeRepository->update($id, $t_param);
            $this->pe_status_correspondence($t_id, $project_id, $sub_pe_request_index->request_type, $id, 'Action Requested (Revision)');
            echo '/admin/pe_request/'.$project_id.'/edit#'.$id;
        }else{
            echo 'fail';
        }
    }

    public function actionInProgress($id)
    {
        $param['status'] = 'in_progress';
        $param['updated_at'] = Carbon::now();
        $user = auth()->user();
        $param_type['assignee'] = $user->id;
        if($this->subPeRequestIndexRepository->update($id, $param)){
            $this->subPeRequestTypeRepository->update($id, $param_type);
            $sub_pe_request_index = $this->subPeRequestIndexRepository->findById($id);
            $t_id = $sub_pe_request_index->task_id;
            $project_id = $this->projectTaskIndexRepository->get_project_id_by_task_id($t_id);
            $this->pe_status_correspondence($t_id, $project_id, $sub_pe_request_index->request_type, $sub_pe_request_index->id, 'In Progress');
            echo '/admin/pe_request/'.$project_id.'/edit#'.$id;
        }else{
            echo 'fail';
        }
    }

    public function actionReview($id)
    {
        $sub_pe_request_index = $this->subPeRequestIndexRepository->findById($id);
        $param['status'] = 'action_review';
        $param['updated_at'] = Carbon::now();
        $t_id = $sub_pe_request_index->task_id;
        $project_id = $this->projectTaskIndexRepository->get_project_id_by_task_id($t_id);
        if($this->subPeRequestIndexRepository->update($id, $param)){
            $this->pe_status_correspondence($t_id, $project_id, $sub_pe_request_index->request_type, $sub_pe_request_index->id, 'Action Review');
            echo '/admin/pe_request/'.$project_id.'/edit#'.$id;
        }else{
            echo 'fail';
        }
    }

    public function actionComplete($id)
    {
        $sub_pe_request_index = $this->subPeRequestIndexRepository->findById($id);
        $param['status'] = 'action_completed';
        $param['updated_at'] = Carbon::now();
        $t_id = $sub_pe_request_index->task_id;
        $project_id = $this->projectTaskIndexRepository->get_project_id_by_task_id($t_id);
        if($this->subPeRequestIndexRepository->update($id, $param)){
            $this->pe_status_correspondence($t_id, $project_id, $sub_pe_request_index->request_type, $sub_pe_request_index->id, 'Action Completed');
            echo '/admin/pe_request/'.$project_id.'/edit#'.$id;
        }else{
            echo 'fail';
        }
    }

    public function actionApprove($id)
    {
        $sub_pe_request_index = $this->subPeRequestIndexRepository->findById($id);
        $param['status'] = 'action_requested';
        $param['updated_at'] = Carbon::now();
        $t_id = $sub_pe_request_index->task_id;
        $project_id = $this->projectTaskIndexRepository->get_project_id_by_task_id($t_id);
        if($this->subPeRequestIndexRepository->update($id, $param)){
            $this->pe_status_correspondence($t_id, $project_id, $sub_pe_request_index->request_type, $sub_pe_request_index->id, 'Action Completed');
            echo '/admin/pe_request/'.$project_id.'/edit#'.$id;
        }else{
            echo 'fail';
        }
    }

    public function fileRemove($id)
    {
        $attachment_obj = $this->peRequestTypeFileAttachmentsRepository->findById($id);
        $file_name = $attachment_obj->attachment;
        $task_id = $attachment_obj->task_id;
        $pe_request_type_id = $attachment_obj->pe_request_type_id;
        $user = auth()->user();
        if($attachment_obj->delete()){
            $requestTypeIndex = $this->subPeRequestIndexRepository->findById($pe_request_type_id);
            $request_type =  ucwords(str_replace('_', ' ', $requestTypeIndex->request_type));
            $change_line = "<p>$user->first_name removed a attachment ($file_name) on <b style='color: #b91d19'>$request_type</b> <b>(#$pe_request_type_id)</b></p>";

            $pe_request_note = new PeRequestNotes();
            $pe_request_note['id'] = $task_id; // task_id
            $pe_request_note['user_id'] = $user->id;
            $pe_request_note['pe_request_type_id'] = $pe_request_type_id;
            $pe_request_note['task_id'] = $task_id; // task_id
            $pe_request_note['note'] = $change_line;
            $pe_request_note['created_at'] = Carbon::now();
            $pe_request_note->save();

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

        $note = new PeRequestNotes();
        $note['id'] = $p_id;
        $note['project_id'] = $p_id;
        $note['pe_request_type_id'] = 0;
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
                'url' => '/admin/pe_request/' . $p_id . '/edit#' . $t_id,
            ];
            $receiver_list = explode(',', $email_list);
            Mail::to($receiver_list)->send(new TaskStatusNotification($details));
        }
        $this->data['currentAdminMenu'] = 'pe_board';

        return redirect('admin/pe_request/'.$p_id.'/edit')
            ->with('success', __('Data has been Updated.'));
    }



    public function requestTypeRemovePermissionCheck($request_type_id){

        $user = auth()->user();
        if($user->role == 'Admin') return true; // admin okay

        $obj = $this->subPeRequestIndexRepository->findById($request_type_id);
        if($obj->author_id != $user->id){
            return false;
        }
        return true;
    }

    public function requestTypeRemove($request_type_id, $type)
    {
        $obj = $this->subPeRequestIndexRepository->findById($request_type_id);
        $t_id = $obj->task_id;
        $p_id = $this->projectTaskIndexRepository->get_project_id_by_task_id($t_id);
        if($this->requestTypeRemovePermissionCheck($request_type_id)){
            // Delete from sub_qra_request_index, sub_qra_request_type tables
            $this->subPeRequestIndexRepository->delete($request_type_id);
            $this->subPeRequestTypeRepository->delete($request_type_id);

            // Add correspondence for asset Removed
            $this->pe_remove_correspondence($t_id, $p_id, $type, $request_type_id);

            echo '/admin/pe_request/'.$p_id.'/edit';
        }else{
            echo 'fail';
        }
    }

    public function pe_status_correspondence($t_id, $p_id, $task_type, $pe_request_type_id, $status)
    {
        // Insert into Project note for correspondence (attachment file)
        $user = auth()->user();
        $task_type_ =  strtoupper(str_replace('_', ' ', $task_type));
        $change_line  = "<p>$user->first_name updated the status to <b>$status</b> for <b style='color: #b91d19;'>$task_type_ </b><b>(#$pe_request_type_id)</b></p>";

        $note = new PeRequestNotes();
        $note['id'] = $t_id;
        $note['user_id'] = $user->id;
        $note['pe_request_type_id'] = $pe_request_type_id;
        $note['task_id'] = $t_id;
        $note['project_id'] = $p_id;
        $note['note'] = $change_line;
        $note['created_at'] = Carbon::now();
        $note->save();
    }

    public function pe_remove_correspondence($t_id, $p_id, $task_type, $request_type_id)
    {
        // Insert into Project note for correspondence (attachment file)
        $user = auth()->user();
        $task_type_ =  strtoupper(str_replace('_', ' ', $task_type));
        $change_line  = "<b style='color: #b91d19;'>$task_type_ </b><b>(#$request_type_id)</b> has been removed by $user->first_name";

        $note = new PeRequestNotes();
        $note['id'] = $t_id;
        $note['user_id'] = $user->id;
        $note['pe_request_type_id'] = $request_type_id;
        $note['task_id'] = $t_id;
        $note['project_id'] = $p_id;
        $note['note'] = $change_line;
        $note['created_at'] = Carbon::now();
        $note->save();
    }

}
