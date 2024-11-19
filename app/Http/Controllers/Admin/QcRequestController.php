<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Mail\NewRequest;
use App\Mail\NoteProject;
use App\Mail\TaskStatusNotification;
use App\Models\ProjectNotes;
use App\Models\ProjectTaskIndex;
use App\Models\ProjectTypeTaskAttachments;
use App\Models\QcRequestNotes;
use App\Models\TaskTypeQcRequest;
use App\Models\User;
use App\Repositories\Admin\BrandRepository;
use App\Repositories\Admin\ProjectNotesRepository;
use App\Repositories\Admin\ProjectRepository;
use App\Repositories\Admin\ProjectTaskFileAttachmentsRepository;
use App\Repositories\Admin\ProjectTaskIndexRepository;
use App\Repositories\Admin\QcRequestNotesRepository;
use App\Repositories\Admin\QcRequestRepository;
use App\Repositories\Admin\SubQraRequestIndexRepository;
use App\Repositories\Admin\TaskTypeConceptDevelopmentRepository;
use App\Repositories\Admin\TaskTypeLegalRequestRepository;
use App\Repositories\Admin\TaskTypeMmRequestRepository;
use App\Repositories\Admin\TaskTypeProductBriefRepository;
use App\Repositories\Admin\TaskTypeQcRequestRepository;
use App\Repositories\Admin\TeamRepository;
use App\Repositories\Admin\UserRepository;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

use function Symfony\Component\VarDumper\Dumper\esc;

class QcRequestController extends Controller
{
    Private $projectRepository;
    Private $qcRequestRepository;
    Private $projectTaskIndexRepository;
    Private $taskTypeQcRequestRepository;
    Private $qcRequestNotesRepository;
    private $projectTaskFileAttachmentsRepository;
    Private $projectNotesRepository;
    Private $teamRepository;
    Private $brandRepository;
    private $userRepository;


    public function __construct(
        ProjectRepository $projectRepository,
        QcRequestRepository $qcRequestRepository,
        ProjectTaskIndexRepository $projectTaskIndexRepository,
        TaskTypeQcRequestRepository $taskTypeQcRequestRepository,
        QcRequestNotesRepository $qcRequestNotesRepository,
        ProjectTaskFileAttachmentsRepository $projectTaskFileAttachmentsRepository,
        ProjectNotesRepository $projectNotesRepository,
        TeamRepository $teamRepository,
        BrandRepository $brandRepository,
        UserRepository $userRepository)
    {
        parent::__construct();

        $this->projectRepository = $projectRepository;
        $this->qcRequestRepository = $qcRequestRepository;
        $this->projectTaskIndexRepository = $projectTaskIndexRepository;
        $this->taskTypeQcRequestRepository = $taskTypeQcRequestRepository;
        $this->qcRequestNotesRepository = $qcRequestNotesRepository;
        $this->projectTaskFileAttachmentsRepository = $projectTaskFileAttachmentsRepository;
        $this->projectNotesRepository = $projectNotesRepository;
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
        $this->data['currentAdminMenu'] = 'qc_request';
        $this->data['filter'] = $param;

        $user = auth()->user();
        if($user->team == 'QM QA' || $user->team == 'Admin') {
            $cur_user = ' ';
        }else{
            $cur_user = $this->userRepository->getPageAccess($user);
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
            $material = $param['material'];
        }else{
            $material = !empty($param['material']) ? $param['material'] : '';
        }
        if(isset($_GET[''])) {
            $vendor_code = $param['vendor_code'];
        }else{
            $vendor_code = !empty($param['vendor_code']) ? $param['vendor_code'] : '';
        }

        $this->data['task_list_action_requested'] = $this->taskTypeQcRequestRepository->get_action_requested_list($cur_user, $team, $brand, $material, $vendor_code);
        $this->data['task_list_in_progress'] = $this->taskTypeQcRequestRepository->get_in_progress_list($cur_user, $team, $brand, $material, $vendor_code);
        $this->data['task_list_action_review'] = $this->taskTypeQcRequestRepository->get_action_review_list($cur_user, $team, $brand, $material, $vendor_code);
        $this->data['task_list_action_completed'] = $this->taskTypeQcRequestRepository->get_action_completed_list($cur_user, $team, $brand, $material, $vendor_code);

        $this->data['team'] = $team;
        $this->data['brand'] = $brand;
        $this->data['material'] = $material;
        $this->data['vendor_code'] = $vendor_code;

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
        $brand_options = [
            'order' => [
                'id' => 'asc',
            ],
            'filter' => [
                'is_active' => 'yes'
            ],
        ];
        $this->data['brands'] =$this->brandRepository->findAll($brand_options);

        return view('admin.qc_request.index', $this->data);
    }

    public function edit($id)
    {
        $this->data['currentAdminMenu'] = 'qc_request';

        $project = $this->projectRepository->findById($id);
        $this->data['project'] = $project;
        $team_options = [
            'order' => [
                'id' => 'asc',
            ],
            'filter' => [
                'is_active' => 'yes'
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

        $author_obj = User::find($project->author_id);
        if($author_obj){
            $this->data['author_name'] = $author_obj['first_name'] . " " . $author_obj['last_name'];
        }else{
            $this->data['author_name'] = 'N/A';
        }

        $rs = $this->qcRequestRepository->get_task_id_for_qc($id);

        if($rs){
            // if qc_request exist
            $this->data['qc_request_list'] = $qc_request_list = $this->qcRequestRepository->get_qc_request_list_by_task_id($rs->id);

            // task_detail
            if(sizeof($qc_request_list)>0){
                foreach ($qc_request_list as $k => $qc_request){
                    $p_id = $qc_request->project_id;
                    $t_id = $rs->id;
                    $task_detail = $this->projectTaskIndexRepository->get_task_detail($p_id, $t_id, 'qc_request');
                    $qc_request_list[$k]->detail = $task_detail;
                    $task_files = $this->projectTaskFileAttachmentsRepository->findAllByTaskId($t_id);
                    $qc_request_list[$k]->files = $task_files;
                }
            }

            $this->data['task_status'] = $qc_request_list[0]->status;
            $this->data['author_name'] = $qc_request_list[0]->author_name;

            // Project_notes
            $options = [
                'id' => $rs->id,
                'order' => [
                    'created_at' => 'desc',
                ]
            ];

            $correspondences = $this->qcRequestNotesRepository->findAll($options);
            $this->data['correspondences'] = $correspondences;

        }else{
            // if qc_request not exist
            $this->data['qc_request_list'] = null;
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

        /////////// Onsite QC Request Task ////////////////////////////////////////////
        $this->data['work_type_list'] = [
            'Vendor Audit', 'Onsite QC', 'Lab Test'
        ];
        $this->data['performed_bys'] = [
            'QM 3rd Party', 'QM QA', 'Sourcing'
        ];
        $this->data['result_list'] = [
            'Pass', 'Reject'
        ];
        $this->data['decision_list'] = [
            'RELEASE',
            'OVERRIDE',
            'NO RELEASE (Corrective Action)'
        ];


        return view('admin.qc_request.form', $this->data);
    }

    public function add_qc_request(Request $request)
    {
        $param = $request->request->all();

        // add project_task_index
        $projectTaskIndex = new ProjectTaskIndex();
        $projectTaskIndex['project_id'] = $param['qc_request_p_id'];
        $projectTaskIndex['type'] = $param['qc_request_task_type'];
        $projectTaskIndex['status'] = 'action_requested';

        $user = auth()->user(); // asset_author_id
        $projectTaskIndex['author_id'] = $user->id;
        $projectTaskIndex->save();
        $task_id = $projectTaskIndex->id;

        // add task_type_qc_request
        $taskTypeQcRequest = new TaskTypeQcRequest();
        $taskTypeQcRequest['id'] = $param['qc_request_p_id']; //project_id
        $taskTypeQcRequest['author_id'] = $param['qc_request_author_id'];
        $taskTypeQcRequest['type'] = $param['qc_request_task_type'];
        $taskTypeQcRequest['created_at'] = Carbon::now();
        $taskTypeQcRequest['task_id'] = $task_id;

        $taskTypeQcRequest['work_type'] = $param['qc_request_work_type'];
        $taskTypeQcRequest['ship_date'] = $param['qc_request_ship_date'];
        $taskTypeQcRequest['qc_date'] = $param['qc_request_qc_date'];
        $taskTypeQcRequest['po'] = $param['qc_request_po'];
        $taskTypeQcRequest['po_usd'] = $param['qc_request_po_usd'];
        $taskTypeQcRequest['materials'] = $param['qc_request_materials'];
        $taskTypeQcRequest['item_type'] = $param['qc_request_item_type'];
        $taskTypeQcRequest['vendor_code'] = $param['qc_request_vendor_code'];
        $taskTypeQcRequest['vendor_name'] = $param['qc_request_vendor_name'];
        $taskTypeQcRequest['country'] = $param['qc_request_country'];
        $taskTypeQcRequest['vendor_primary_contact_name'] = $param['qc_request_vendor_primary_contact_name'];
        $taskTypeQcRequest['vendor_primary_contact_email'] = $param['qc_request_vendor_primary_contact_email'];
        $taskTypeQcRequest['vendor_primary_contact_phone'] = $param['qc_request_vendor_primary_contact_phone'];
        $taskTypeQcRequest['facility_address'] = $param['qc_request_facility_address'];
//        $taskTypeQcRequest['performed_by'] = $param['qc_request_performed_by'];
        $taskTypeQcRequest->save();

        // new correspondence when adding asset
        $this->correspondence_add_new_task($projectTaskIndex['project_id'], 'QA Request', $projectTaskIndex);

        // Correspondence for Onsite QC Request Type
        $this->correspondence_new_qc_request($projectTaskIndex['project_id'], 'QA Request', $projectTaskIndex);

        // add campaign_type_asset_attachments
        if($request->file('qc_request_p_attachment')){
            foreach ($request->file('qc_request_p_attachment') as $file) {

                $project_type_task_attachments = new ProjectTypeTaskAttachments();

//                $fileName = $file->storeAs('campaigns/'.$request['qc_request_c_id'].'/'.$asset_id, $originalName);
                $fileName = $this->file_exist_check($file, $param['qc_request_p_id'], $task_id);

                $project_type_task_attachments['project_id'] = $param['qc_request_p_id'];
                $project_type_task_attachments['task_id'] = $task_id;
                $project_type_task_attachments['type'] = 'attachment_file_' . $file->getMimeType();
                $project_type_task_attachments['author_id'] = $param['qc_request_author_id'];
                $project_type_task_attachments['attachment'] = '/' . $fileName;
                $project_type_task_attachments['file_ext'] = pathinfo($fileName, PATHINFO_EXTENSION);
                $project_type_task_attachments['file_type'] = $file->getMimeType();
                $project_type_task_attachments['file_size'] = $file->getSize();
                $project_type_task_attachments['created_at'] = Carbon::now();
                $project_type_task_attachments->save();
            }
        }

        // Send Notification
//        $this->send_notification_action_request($user, $param['qc_request_p_id'], $taskTypeQcRequest, $task_id);

        return redirect('admin/qc_request/'.$param['qc_request_p_id'].'/edit#'.$task_id)
            ->with('success', __('Added the QA Request Task : ' . $task_id));

    }

    function send_notification_action_request($user, $project_id, $subRequestType, $request_type_id)
    {
        $project_obj = $this->projectRepository->findById($project_id);
        if($subRequestType['priority'] == 'Urgent'){
            $due_date_mail = $subRequestType['due_date_urgent'];
            $priority_mail = 'Urgent';
        }else{
            $due_date_mail = $subRequestType['qc_date'];
            $priority_mail = 'Normal';
        }

        $details = [
            'mail_subject'      => 'Action Requested : QA Request',
            'template'          => 'emails.task.new_request',
            'receiver'          => "QM QA Team",
            'title'             => "Action Requested : QA Request",
            'body'              => 'You got a new request from ' . $user->team . ', ' . $user->first_name . ' ' . $user->last_name . '. ',
            'project_id'        => $project_id,
            'project_title'     => $project_obj->name,
            'request_id'        => $request_type_id,
            'request_type'      => '',
            'priority'          => $priority_mail,
            'due_date'          => $due_date_mail,
            'url'               => '/admin/qc_request/'.$project_id.'/edit#'.$request_type_id,
        ];

        $group_rs = $this->userRepository->get_receiver_emails_by_team('Admin');
        foreach ($group_rs as $team_user) {
            $receiver_list[] = $team_user['email'];
        }

        /// Send to receivers
        Mail::to($receiver_list)->send(new NewRequest($details));
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

    public function correspondence_new_qc_request($p_id, $task_name, $projectTaskIndex)
    {
        $user = auth()->user();
        $task_name = strtoupper($task_name);
        $change_line = "<p><b style='color: #b91d19;'>$task_name</b> <b>(#$projectTaskIndex->id)</b> has been created by $user->first_name. </p>";
        $qra_request_note = new QcRequestNotes();
        $qra_request_note['id'] = $projectTaskIndex->id;
        $qra_request_note['user_id'] = $user->id;
        $qra_request_note['qc_request_type_id'] = 0;
        $qra_request_note['task_id'] = $projectTaskIndex->id;
        $qra_request_note['project_id'] = $p_id;
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

    public function edit_qc_request(Request $request, $task_id)
    {
        $qc_request = $this->taskTypeQcRequestRepository->findById($task_id);

        $param = $request->request->all();

        // Permission_check
//        if(!$this->permission_check($param)){
//            return redirect('admin/project/' . $qc_request->id . '/edit#'.$task_id)
//                ->with('error', __('This action is no longer permitted. Please contact an Administrator.'));
//        }

        if($this->taskTypeQcRequestRepository->update($task_id, $param)){
            $user = auth()->user();
            // insert into campaign note for correspondence
            $this->add_correspondence('qc_request', $param, $qc_request, $user);

            if($request->file('p_attachment')){
                foreach ($request->file('p_attachment') as $file) {
                    $project_type_task_attachments = new ProjectTypeTaskAttachments();

//                    $fileName = $file->storeAs('campaigns/'.$qc_request->id.'/'.$asset_id, $file_name);
                    $fileName = $this->file_exist_check($file, $qc_request->id, $task_id);

                    $project_type_task_attachments['project_id'] = $qc_request->id;
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
                    $this->add_file_correspondence_for_task($qc_request, $user, $fileName, 'qc_request');
                }
            }
            return redirect('admin/qc_request/'.$qc_request->id.'/edit#'.$task_id)
                ->with('success', __('QA Request ('.$task_id.') - Update Success'));
        }
        return redirect('admin/qc_request/'.$qc_request->id.'/edit#'.$task_id)
            ->with('error', __('Update Failed'));
    }

    public function add_correspondence($task_type, $new_param, $origin_param, $user)
    {
        // Insert into campaign note for correspondence

        $new = $this->get_task_param($task_type, $new_param);
        $origin = $origin_param->toArray();

        foreach ($new as $key => $value) {
            if (array_key_exists($key, $origin)) {
                if (html_entity_decode($new[$key]) != html_entity_decode($origin[$key])) {
                    $changed[$key]['new'] = $new[$key];
                    $changed[$key]['original'] = $origin[$key];
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

        if($task_type_ == 'QC Request'){
            $task_type_ = 'QA Request';
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

            $qra_request_note = new QcRequestNotes();
            $qra_request_note['id'] = $origin_param->id;
            $qra_request_note['user_id'] = $user->id;
            $qra_request_note['qc_request_type_id'] = 0;
            $qra_request_note['task_id'] = $origin_param->task_id;
            $qra_request_note['project_id'] = $origin_param->id;
            $qra_request_note['note'] = $change_line;
            $qra_request_note['created_at'] = Carbon::now();
            $qra_request_note->save();

        }
    }

    public function add_file_correspondence_for_task($qc_request, $user, $file_type, $task_type)
    {
        // Insert into project note for correspondence (attachment file)
        if($task_type == 'qc_request'){
            $task_type = 'onsite_qc_request';
        }
        $task_type_ =  strtoupper(str_replace('_', ' ', $task_type));

        $change_line  = "<p>$user->first_name has added a new attachment ($file_type) to <b style='color: #b91d19;'>$task_type_</b> <b>(#$qc_request->task_id)</b></p>";

        $qra_request_note = new QcRequestNotes();
        $qra_request_note['id'] = $qc_request->id;
        $qra_request_note['user_id'] = $user->id;
        $qra_request_note['qc_request_type_id'] = 0;
        $qra_request_note['task_id'] = $qc_request->task_id;
        $qra_request_note['project_id'] = $qc_request->id;
        $qra_request_note['note'] = $change_line;
        $qra_request_note['created_at'] = Carbon::now();
        $qra_request_note->save();

    }

    public function get_task_param($task_type, $data)
    {
        if ($task_type == 'qc_request') {
            $new = array(
                'work_type' => $data['work_type'],
                'ship_date' => $data['ship_date'],
                'qc_date' => $data['qc_date'],
                'po' => $data['po'],
                'po_usd' => $data['po_usd'],
                'materials' => $data['materials'],
                'item_type' => $data['item_type'],
                'vendor_code' => $data['vendor_code'],
                'country' => $data['country'],
                'vendor_primary_contact_name' => $data['vendor_primary_contact_name'],
                'vendor_primary_contact_email' => $data['vendor_primary_contact_email'],
                'vendor_primary_contact_phone' => $data['vendor_primary_contact_phone'],
                'facility_address' => $data['facility_address'],
                'performed_by' => $data['performed_by'],
                'critical' => $data['critical'],
                'result' => $data['result'],
                'qc_completed_date' => $data['qc_completed_date'],
            );
            return $new;
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
            $this->qc_status_correspondence($t_id, $project_id, 'In Progress');
            echo '/admin/qc_request/'.$project_id.'/edit#'.$id;
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
            $this->qc_status_correspondence($t_id, $project_id, 'Action Review');
            echo '/admin/qc_request/'.$project_id.'/edit#'.$id;
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
            $this->qc_status_correspondence($t_id, $project_id, 'Action Completed');
            echo '/admin/qc_request/'.$project_id.'/edit#'.$id;
        }else{
            echo 'fail';
        }
    }

    public function revision_reason_action_decline(Request $request)
    {
        $param = $request->all();

        $task_id = $param['request_type_id'];
        $revision_reason = $param['revision_reason'];
        $revision_reason_note = $param['revision_reason_note'];
        $project_id = $this->projectTaskIndexRepository->get_project_id_by_task_id($task_id);

        $params['status'] = 'in_progress';
        $params['updated_at'] = Carbon::now();
        $params['revision_reason'] = $revision_reason;
        $params['revision_reason_note'] = $revision_reason_note;
        if($this->projectTaskIndexRepository->update($task_id, $params)){

            $user = auth()->user();
            $change_line  = "<p>$user->first_name updated the status to <b>Decline</b> for <b style='color: #b91d19;'>QA REQUEST</b><b>(#$task_id)</b>
                            <br> <b style='color: black;'>Decline Reason : $revision_reason </b>
                            <br> <b style='color: black;'>$revision_reason_note </b>
                            </p>";
            $note = new QcRequestNotes();
            $note['id'] = $project_id;
            $note['user_id'] = $user->id;
            $note['qc_request_type_id'] = 0;
            $note['task_id'] = $task_id;
            $note['project_id'] = $project_id;
            $note['note'] = $change_line;
            $note['created_at'] = Carbon::now();
            $note->save();

            return redirect('admin/qc_request/'.$project_id.'/edit#'.$task_id)
                ->with('success', __('Data has been Updated.'));
        }

        return redirect('admin/qc_request/'.$project_id.'/edit#'.$task_id)
            ->with('error', __('Data updates Failed'));
    }

    public function qc_status_correspondence($t_id, $p_id, $status)
    {
        // Insert into Project note for correspondence (attachment file)
        $user = auth()->user();

        $change_line  = "<p>$user->first_name updated the status to <b>$status</b> for <b style='color: #b91d19;'>QA REQUEST</b><b>(#$t_id)</b></p>";

        $note = new QcRequestNotes();
        $note['id'] = $t_id;
        $note['user_id'] = $user->id;
        $note['qc_request_type_id'] = 0;
        $note['task_id'] = $t_id;
        $note['project_id'] = $p_id;
        $note['note'] = $change_line;
        $note['created_at'] = Carbon::now();
        $note->save();
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

            $request_note = new QcRequestNotes();
            $request_note['id'] = $task_id; // task_id
            $request_note['user_id'] = $user->id;
            $request_note['qc_request_type_id'] = 0;
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

        $note = new QcRequestNotes();
        $note['id'] = $t_id;
        $note['user_id'] = $user->id;
        $note['qc_request_type_id'] = 0;
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
                'url' => '/admin/qc_request/' . $p_id . '/edit#' . $t_id,
            ];
            $receiver_list = explode(',', $email_list);
            Mail::to($receiver_list)->send(new TaskStatusNotification($details));
        }
        return redirect('admin/qc_request/'.$p_id.'/edit')
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
            $this->taskTypeQcRequestRepository->delete($request_type_id);

            // Add correspondence for asset Removed
            $this->qc_remove_correspondence($request_type_id, $p_id, $type);

            echo '/admin/qc_request/'.$p_id.'/edit';
        }else{
            echo 'fail';
        }
    }

    public function qc_remove_correspondence($t_id, $p_id, $task_type)
    {
        // Insert into Project note for correspondence (attachment file)
        $user = auth()->user();
        $task_type_ =  strtoupper(str_replace('_', ' ', $task_type));

        $change_line  = "<b style='color: #b91d19;'>$task_type_ </b><b>(#$t_id)</b> has been removed by $user->first_name";

        $note = new QcRequestNotes();
        $note['id'] = $t_id;
        $note['user_id'] = $user->id;
        $note['qc_request_type_id'] = $t_id;
        $note['task_id'] = $t_id;
        $note['project_id'] = $p_id;
        $note['note'] = $change_line;
        $note['created_at'] = Carbon::now();
        $note->save();
    }

}
