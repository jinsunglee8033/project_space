<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\ProductReceivingNotes;
use App\Models\ProjectNotes;
use App\Models\ProjectTaskIndex;
use App\Models\ProjectTypeTaskAttachments;
use App\Models\QcRequestNotes;
use App\Models\TaskTypeProductReceiving;
use App\Models\TaskTypeQcRequest;
use App\Models\User;
use App\Repositories\Admin\ProductReceivingNotesRepository;
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
use App\Repositories\Admin\TaskTypeProductReceivingRepository;
use App\Repositories\Admin\TaskTypeQcRequestRepository;
use App\Repositories\Admin\TeamRepository;
use App\Repositories\Admin\UserRepository;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;


use Mail;
use function Symfony\Component\VarDumper\Dumper\esc;

class ProductReceivingController extends Controller
{
    Private $projectRepository;
    Private $qcRequestRepository;
    Private $projectTaskIndexRepository;
    Private $taskTypeQcRequestRepository;
    Private $taskTypeProductReceivingRepository;
    Private $qcRequestNotesRepository;
    Private $productReceivingNotesRepository;
    private $projectTaskFileAttachmentsRepository;
    Private $projectNotesRepository;
    Private $teamRepository;
    private $userRepository;


    public function __construct(
        ProjectRepository $projectRepository,
        QcRequestRepository $qcRequestRepository,
        ProjectTaskIndexRepository $projectTaskIndexRepository,
        TaskTypeQcRequestRepository $taskTypeQcRequestRepository,
        TaskTypeProductReceivingRepository $taskTypeProductReceivingRepository,
        QcRequestNotesRepository $qcRequestNotesRepository,
        ProductReceivingNotesRepository $productReceivingNotesRepository,
        ProjectTaskFileAttachmentsRepository $projectTaskFileAttachmentsRepository,
        ProjectNotesRepository $projectNotesRepository,
        TeamRepository $teamRepository,
        UserRepository $userRepository)
    {
        parent::__construct();

        $this->projectRepository = $projectRepository;
        $this->qcRequestRepository = $qcRequestRepository;
        $this->projectTaskIndexRepository = $projectTaskIndexRepository;
        $this->taskTypeQcRequestRepository = $taskTypeQcRequestRepository;
        $this->taskTypeProductReceivingRepository = $taskTypeProductReceivingRepository;
        $this->qcRequestNotesRepository = $qcRequestNotesRepository;
        $this->productReceivingNotesRepository = $productReceivingNotesRepository;
        $this->projectTaskFileAttachmentsRepository = $projectTaskFileAttachmentsRepository;
        $this->projectNotesRepository = $projectNotesRepository;
        $this->teamRepository = $teamRepository;
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
        $this->data['currentAdminMenu'] = 'product_receiving';

        if(isset($_GET[''])) {
            $team = $param['team'];
        }else{
            $team = !empty($param['team']) ? $param['team'] : '';
        }

        $this->data['task_list_action_requested'] = $this->taskTypeProductReceivingRepository->get_action_requested_list($team);
        $this->data['task_list_in_progress'] = $this->taskTypeProductReceivingRepository->get_in_progress_list($team);
        $this->data['task_list_action_review'] = $this->taskTypeProductReceivingRepository->get_action_review_list($team);
        $this->data['task_list_action_completed'] = $this->taskTypeProductReceivingRepository->get_action_completed_list($team);


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
        $this->data['teams'] =$this->teamRepository->findAll($team_options);

        return view('admin.product_receiving.index', $this->data);
    }

    public function edit($id)
    {
        $this->data['currentAdminMenu'] = 'product_receiving';

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

        $rs = $this->taskTypeProductReceivingRepository->get_task_id_for_product_receiving($id);

        if($rs){
            // if qc_request exist
            $task_id = $rs->id;
            $this->data['product_receiving_list'] = $product_receiving_list = $this->taskTypeProductReceivingRepository->get_product_receiving_list_by_task_id($task_id);

        }else{

            // add project_task_index
            $projectTaskIndex = new ProjectTaskIndex();
            $projectTaskIndex['project_id'] = $id;
            $projectTaskIndex['type'] = 'product_receiving';
            $projectTaskIndex['status'] = 'action_requested';

            $user = auth()->user(); // asset_author_id
            $projectTaskIndex['author_id'] = $user->id;
            $projectTaskIndex->save();
            $task_id = $projectTaskIndex->id;

            // add task_type_qc_request
            $taskTypeProductReceiving = new TaskTypeProductReceiving();
            $taskTypeProductReceiving['id'] = $id;
            $taskTypeProductReceiving['author_id'] = $user->id;
            $taskTypeProductReceiving['type'] = 'product_receiving';
            $taskTypeProductReceiving['created_at'] = Carbon::now();
            $taskTypeProductReceiving['task_id'] = $task_id;

            $taskTypeProductReceiving->save();

            // add new product receiving with action_requested
            // if qc_request not exist
            $this->data['product_receiving_list'] = $product_receiving_list = $this->taskTypeProductReceivingRepository->get_product_receiving_list_by_task_id($task_id);

            $this->correspondence_add_new_task($projectTaskIndex['project_id'], 'Product Receiving', $projectTaskIndex);

            $this->correspondence_new_pr_request($projectTaskIndex['project_id'], 'Product Receiving', $projectTaskIndex);

        }

        // task_detail
        if(sizeof($product_receiving_list)>0){
            foreach ($product_receiving_list as $k => $product_receiving){
                $p_id = $product_receiving->project_id;
                $t_id = $task_id;
                $task_detail = $this->projectTaskIndexRepository->get_task_detail($p_id, $t_id, 'product_receiving');
                $product_receiving_list[$k]->detail = $task_detail;
                $task_files = $this->projectTaskFileAttachmentsRepository->findAllByTaskId($t_id);
                $product_receiving_list[$k]->files = $task_files;
            }
        }

        $this->data['task_status'] = $product_receiving_list[0]->status;

        // Project_notes
        $options = [
            'id' => $task_id,
            'order' => [
                'created_at' => 'desc',
            ]
        ];

        $correspondences = $this->productReceivingNotesRepository->findAll($options);
        $this->data['correspondences'] = $correspondences;

        /////////// Product Receiving Task ////////////////////////////////////////////
        $this->data['qir_statuses'] = [
            'Ongoing','Completed'
        ];
        $this->data['divisions'] = [
            'Nail Division','Lash Division','Appliance & Accessory Division','Cosmetic & Hair Care Division',
            'AST','CPU-JOAH','IMBECOR','PURCHASING','LOGISTIC'
        ];
        $this->data['qir_actions'] = [
            'PU initial Review','Not a defect(Override)','Full Inspection','Full Inspection(GA)','Special Inspection',
            'Rework','Rework(GA)','Rework(LA)','Return/Replace','Wait for CAPA','Return PO#','Inventory Adjustment',
            'Waiting required Action','3rd Inspection Required','System Update'
        ];
        $this->data['cost_center_list'] = [
            '1000_C100', '1000_C200', '1000_C300', '1000_C400', '1000_C700', '1000_C900', '1000_CRE', '1000_E100', '1000_E200',
            '1000_E300', '1000_E400', '1000_E900', '1000_HRGA', '1000_IMBE', '1000_IT', '1000_KCM', '1000_KSAL', '1000_KWH',
            '1000_L100', '1000_L900', '1000_MKT', '1000_N100', '1000_N200', '1000_N300', '1000_N400', '1000_N500', '1000_N600',
            '1000_N900', '1000_OP', '1000_PM', '1000_RND', '1000_SP', '1000_WM', '1000_X100', '1100_C100', '1100_C200', '1100_C300',
            '1100_C400', '1100_C900', '1100_E100', '1100_E200', '1100_E300', '1100_E400', '1100_E900', '1100_HRGA', '1100_IGA',
            '1100_IIAD', '1100_IIMKT', '1100_ISAD', '1100_ISAL', '1100_ISSS1', '1100_ISSS2', '1100_IT', '1100_L100', '1100_L900',
            '1100_N100', '1100_N200', '1100_N300', '1100_N400', '1100_N500', '1100_N600', '1100_N900', '1100_OP', '1100_PM',
            '1100_WM', '1100_X100', '1300_C100', '1300_C200', '1300_C300', '1300_C400', '1300_C900', '1300_E100', '1300_E200',
            '1300_E300', '1300_E900', '1300_L100', '1300_L900', '1300_N100', '1300_N200', '1300_N300', '1300_N400', '1300_N500',
            '1300_N600', '1300_N900', '1300_X100', '4003_K100', '4003_L500', '4003_L500', '4003_C100', '4003_C200', '4003_C300',
            '4003_C400', '4003_C700', '4003_C900', '4003_CRE', '4003_E100', '4003_E200', '4003_E300', '4003_E400', '4003_E900',
            '4003_KCM', '4003_KSAL', '4003_KWH', '4003_L100', '4003_L900', '4003_MKT', '4003_N100', '4003_N200', '4003_N300',
            '4003_N400', '4003_N500', '4003_N600', '4003_N900', '4003_OP', '4003_PM', '4003_RND', '4003_SP', '4003_WM', '4003_X100'
        ];
        $this->data['locations'] = [
            'KDC-NJ','KDC-GA','KDC-LA','KCZ','IMBECOR'
        ];
        $this->data['defect_areas'] = [
            'Product','Package','Label','System'
        ];
        $this->data['defect_types'] = [
            'Blooming', 'Damage(Cosmetic)', 'Function Defect', 'illegible Information', 'Transportation(Damage)',
            'Foreign Substance', 'Mix-up Unit-System', 'Missing BOM', 'Wrong Assembly Defect', 'Dirty', 'UPC Barcode Scan',
            'Out of Specification', 'Wrong Batch Number Coding', 'Wrong Position', 'Missing Information', 'Eyelash Detached',
            'Rough', 'System Error',
        ];
        $this->data['claim_statuses'] = [
            'Issued & Assigned', 'Waived/Canceled', 'Settled', 'Wait Credit', 'Replacement', 'Wait for CAPA',
            'Pending cost update', 'Claim added to main row'
        ];
        $this->data['override_authorized_by_list'] = [
            'COO','CEO','CSO','Lash Division Leader','Nail Division Leader','AA Division Leader',
            'C&H Division Leader','All Related Team Agreement','N/A'
        ];
        $this->data['settlement_type_list'] = [
            'Claim Settled (Full)', 'Claim Merged (Main Row)', 'Claim Waived (Canceled)', 'NA (Override)',
            'Replacement', 'System Updated',
        ];

        return view('admin.product_receiving.form', $this->data);
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

    public function correspondence_new_pr_request($p_id, $task_name, $projectTaskIndex)
    {
        $user = auth()->user();
        $task_name = strtoupper($task_name);
        $change_line = "<p><b style='color: #b91d19;'>$task_name</b> <b>(#$projectTaskIndex->id)</b> has been created by $user->first_name. </p>";
        $qra_request_note = new ProductReceivingNotes();
        $qra_request_note['id'] = $projectTaskIndex->id;
        $qra_request_note['user_id'] = $user->id;
        $qra_request_note['product_receiving_type_id'] = 0;
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

    public function edit_product_receiving(Request $request, $task_id)
    {
        $product_receiving = $this->taskTypeProductReceivingRepository->findById($task_id);

        $param = $request->request->all();

        // Permission_check
//        if(!$this->permission_check($param)){
//            return redirect('admin/project/' . $product_receiving->id . '/edit#'.$task_id)
//                ->with('error', __('This action is no longer permitted. Please contact an Administrator.'));
//        }

        if (isset($param['defect_area'])) {
            $param['defect_area'] = implode(', ', $param['defect_area']);
        } else {
            $param['defect_area'] = '';
        }
        if (isset($param['defect_type'])) {
            $param['defect_type'] = implode(', ', $param['defect_type']);
        } else {
            $param['defect_type'] = '';
        }
        if (isset($param['capa'])) {
            $param['capa'] = 'on';
        } else {
            $param['capa'] = null;
        }

        if($this->taskTypeProductReceivingRepository->update($task_id, $param)){
            $user = auth()->user();
            // insert into campaign note for correspondence
            $this->add_correspondence('product_receiving', $param, $product_receiving, $user);

            if($request->file('p_attachment')){
                foreach ($request->file('p_attachment') as $file) {
                    $project_type_task_attachments = new ProjectTypeTaskAttachments();

//                    $fileName = $file->storeAs('campaigns/'.$product_receiving->id.'/'.$asset_id, $file_name);
                    $fileName = $this->file_exist_check($file, $product_receiving->id, $task_id);

                    $project_type_task_attachments['project_id'] = $product_receiving->id;
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
                    $this->add_file_correspondence_for_task($product_receiving, $user, $fileName, 'product_receiving');
                }
            }
            return redirect('admin/product_receiving/'.$product_receiving->id.'/edit#'.$task_id)
                ->with('success', __('Product Receiving ('.$task_id.') - Update Success'));
        }
        return redirect('admin/product_receiving/'.$product_receiving->id.'/edit#'.$task_id)
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
            $task_type_ = strtoupper($task_type_temp[0]) . " " . strtoupper($task_type_temp[1]);
        }else{
            $task_type_= strtoupper($task_type);
        }

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

            $qra_request_note = new ProductReceivingNotes();
            $qra_request_note['id'] = $origin_param->id;
            $qra_request_note['user_id'] = $user->id;
            $qra_request_note['product_receiving_type_id'] = 0;
            $qra_request_note['task_id'] = $origin_param->task_id;
            $qra_request_note['project_id'] = $origin_param->id;
            $qra_request_note['note'] = $change_line;
            $qra_request_note['created_at'] = Carbon::now();
            $qra_request_note->save();

        }
    }

    public function add_file_correspondence_for_task($product_receiving, $user, $file_type, $task_type)
    {
        // Insert into project note for correspondence (attachment file)
        $task_type_ =  strtoupper(str_replace('_', ' ', $task_type));
        $change_line  = "<p>$user->first_name has added a new attachment ($file_type) to <b style='color: #b91d19;'>$task_type_</b> <b>(#$product_receiving->task_id)</b></p>";

        $qra_request_note = new ProductReceivingNotes();
        $qra_request_note['id'] = $product_receiving->id;
        $qra_request_note['user_id'] = $user->id;
        $qra_request_note['product_receiving_type_id'] = 0;
        $qra_request_note['task_id'] = $product_receiving->task_id;
        $qra_request_note['project_id'] = $product_receiving->id;
        $qra_request_note['note'] = $change_line;
        $qra_request_note['created_at'] = Carbon::now();
        $qra_request_note->save();

    }

    public function get_task_param($task_type, $data)
    {
        if ($task_type == 'product_receiving') {
            $new = array(
                'po' => $data['po'],
                'materials' => $data['materials'],
                'posting_date' => $data['posting_date'],
                'qir_status' => $data['qir_status'],
                'division' => $data['division'],
                'qir_action' => $data['qir_action'],
                'vendor_code' => $data['vendor_code'],
                'vendor_name' => $data['vendor_name'],
                'cost_center' => $data['cost_center'],
                'location' => $data['location'],
                'primary_contact' => $data['primary_contact'],
                'related_team_contact' => $data['related_team_contact'],
                'year' => $data['year'],
                'received_qty' => $data['received_qty'],
                'inspection_qty' => $data['inspection_qty'],
                'defect_qty' => $data['defect_qty'],
                'blocked_qty' => $data['blocked_qty'],
                'blocked_rate' => $data['blocked_rate'],
                'batch' => $data['batch'],
                'item_net_cost' => $data['item_net_cost'],
                'defect_area' => $data['defect_area'],
                'defect_type' => $data['defect_type'],
                'defect_details' => $data['defect_details'],
                'defect_cost' => $data['defect_cost'],
                'full_cost' => $data['full_cost'],
                'rework_cost' => $data['rework_cost'],
                'rsr_id' => $data['rsr_id'],
                'special_inspection_cost' => $data['special_inspection_cost'],
                'processing_date' => $data['processing_date'],
                'aging_days' => $data['aging_days'],
                'capa' => $data['capa'],
                'total_claim' => $data['total_claim'],
                'actual_cm_total' => $data['actual_cm_total'],
                'claim_status' => $data['claim_status'],
                'override_authorized_by' => $data['override_authorized_by'],
                'waived_amount' => $data['waived_amount'],
                'settlement_total' => $data['settlement_total'],
                'settlement_type' => $data['settlement_type'],
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
            $this->pr_status_correspondence($t_id, $project_id, 'In Progress');
            echo '/admin/product_receiving/'.$project_id.'/edit#'.$id;
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
            $this->pr_status_correspondence($t_id, $project_id, 'Action Review');
            echo '/admin/product_receiving/'.$project_id.'/edit#'.$id;
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
            $this->pr_status_correspondence($t_id, $project_id, 'Action Completed');
            echo '/admin/product_receiving/'.$project_id.'/edit#'.$id;
        }else{
            echo 'fail';
        }
    }

    public function pr_status_correspondence($t_id, $p_id, $status)
    {
        // Insert into Project note for correspondence (attachment file)
        $user = auth()->user();

        $change_line  = "<p>$user->first_name updated the status to <b>$status</b> for <b style='color: #b91d19;'>PRODUCT RECEIVING</b><b>(#$t_id)</b></p>";

        $note = new ProductReceivingNotes();
        $note['id'] = $t_id;
        $note['user_id'] = $user->id;
        $note['product_receiving_type_id'] = 0;
        $note['task_id'] = $t_id;
        $note['project_id'] = $p_id;
        $note['note'] = $change_line;
        $note['created_at'] = Carbon::now();
        $note->save();
    }

}
