<?php

namespace App\Http\Controllers\Admin;

use App\Authorizable;
use App\Http\Controllers\Controller;
use App\Http\Controllers\NotifyController;
use App\Http\Requests\Admin\AssetAContentRequest;
use App\Http\Requests\Admin\AssetEmailBlastRequest;
use App\Http\Requests\Admin\AssetImageRequestRequest;
use App\Http\Requests\Admin\AssetInfoGraphicRequest;
use App\Http\Requests\Admin\AssetLandingPageRequest;
use App\Http\Requests\Admin\AssetMiscRequest;
use App\Http\Requests\Admin\AssetProgrammaticBannersRequest;
use App\Http\Requests\Admin\AssetRollOverRequest;
use App\Http\Requests\Admin\AssetSmsRequestRequest;
use App\Http\Requests\Admin\AssetSocialAdRequest;
use App\Http\Requests\Admin\AssetStoreFrontRequest;
use App\Http\Requests\Admin\AssetTopcategoriesCopyRequest;
use App\Http\Requests\Admin\AssetWebsiteBannersRequest;
use App\Http\Requests\Admin\AssetWebsiteChangesRequest;
use App\Http\Requests\Admin\CampaignRequest;
use App\Http\Requests\Admin\ProjectRequest;
use App\Http\Requests\Admin\TaskConceptDevelopmentRequest;
use App\Http\Requests\Admin\TaskLegalRequestRequest;
use App\Http\Requests\Admin\UserRequest;
use App\Mail\MyDemoMail;
use App\Mail\NewProject;
use App\Mail\NoteProject;
use App\Mail\SendMail;
use App\Mail\TaskStatusNotification;
use App\Models\AssetOwnerAssets;
use App\Models\Brand;
use App\Models\CampaignAssetIndex;
use App\Models\CampaignNotes;
use App\Mail\AssetMessage;

use App\Models\CampaignTypeAContent;
use App\Models\CampaignTypeAssetAttachments;
use App\Models\CampaignTypeEmailBlast;
use App\Models\CampaignTypeImageRequest;
use App\Models\CampaignTypeInfoGraphic;
use App\Models\CampaignTypeLandingPage;
use App\Models\CampaignTypeMisc;
use App\Models\CampaignTypeProgrammaticBanners;
use App\Models\CampaignTypeRollOver;
use App\Models\CampaignTypeSmsRequest;
use App\Models\CampaignTypeSocialAd;
use App\Models\CampaignTypeStoreFront;
use App\Models\CampaignTypeTopcategoriesCopy;
use App\Models\CampaignTypeWebsiteBanners;
use App\Models\CampaignTypeWebsiteChanges;
use App\Models\CampaignTypeYoutubeCopy;
use App\Models\LegalRequestNotes;
use App\Models\MmRequestNotes;
use App\Models\NpdDesignRequestNotes;
use App\Models\NpdPlannerRequestNotes;
use App\Models\PeRequestNotes;
use App\Models\ProductCategory;
use App\Models\ProductSegment;
use App\Models\ProjectNotes;
use App\Models\ProjectTaskIndex;
use App\Models\ProjectTypeTaskAttachments;
use App\Models\QcRequestNotes;
use App\Models\QraRequestNotes;
use App\Models\RaRequestNotes;
use App\Models\TaskTypeConceptDevelopment;
use App\Models\TaskTypeLegalRequest;
use App\Models\TaskTypeMmRequest;
use App\Models\TaskTypeNpdDesignRequest;
use App\Models\TaskTypeNpdPlannerRequest;
use App\Models\TaskTypePeRequest;
use App\Models\TaskTypeProductBrief;
use App\Models\TaskTypeProductInformation;
use App\Models\TaskTypeProductReceiving;
use App\Models\TaskTypeQcRequest;
use App\Models\TaskTypeQraRequest;
use App\Models\TaskTypeRaRequest;
use App\Models\User;

use App\Models\Vendor;
use App\Repositories\Admin\AssetNotificationUserRepository;
use App\Repositories\Admin\AssetOwnerAssetsRepository;
use App\Repositories\Admin\BrandRepository;
use App\Repositories\Admin\CampaignAssetIndexRepository;
use App\Repositories\Admin\CampaignNotesRepository;
use App\Repositories\Admin\PlantRepository;
use App\Repositories\Admin\ProjectNotesRepository;
use App\Repositories\Admin\CampaignRepository;
use App\Repositories\Admin\CampaignBrandsRepository;
use App\Repositories\Admin\CampaignTypeAContentRepository;
use App\Repositories\Admin\CampaignTypeAssetAttachmentsRepository;
use App\Repositories\Admin\CampaignTypeEmailBlastRepository;
use App\Repositories\Admin\CampaignTypeImageRequestRepository;
use App\Repositories\Admin\CampaignTypeInfoGraphicRepository;
use App\Repositories\Admin\CampaignTypeLandingPageRepository;
use App\Repositories\Admin\CampaignTypeMiscRepository;
use App\Repositories\Admin\CampaignTypeProgrammaticBannersRepository;
use App\Repositories\Admin\CampaignTypeRollOverRepository;
use App\Repositories\Admin\CampaignTypeSmsRequestRepository;
use App\Repositories\Admin\CampaignTypeSocialAdRepository;
use App\Repositories\Admin\CampaignTypeStoreFrontRepository;
use App\Repositories\Admin\CampaignTypeTopcategoriesCopyRepository;
use App\Repositories\Admin\CampaignTypeWebsiteBannersRepository;
use App\Repositories\Admin\CampaignTypeWebsiteChangesRepository;
use App\Repositories\Admin\CampaignTypeYoutubeCopyRepository;

use App\Repositories\Admin\RaRequestRepository;
use App\Repositories\Admin\SubQraRequestIndexRepository;
use App\Repositories\Admin\TaskTypeConceptDevelopmentRepository;

use App\Repositories\Admin\ProjectRepository;
use App\Repositories\Admin\ProjectTaskIndexRepository;
use App\Repositories\Admin\ProjectTaskFileAttachmentsRepository;
use App\Repositories\Admin\TaskTypeDisplayRequestRepository;
use App\Repositories\Admin\TaskTypeLegalRequestRepository;
use App\Repositories\Admin\TaskTypeMmRequestRepository;
use App\Repositories\Admin\TaskTypeNpdPoRequestRepository;
use App\Repositories\Admin\TaskTypePeRequestRepository;
use App\Repositories\Admin\TaskTypeProductBriefRepository;
use App\Repositories\Admin\TaskTypeProductInformationRepository;
use App\Repositories\Admin\TaskTypeProductReceivingRepository;
use App\Repositories\Admin\TaskTypeQcRequestRepository;
use App\Repositories\Admin\TeamRepository;
use App\Repositories\Admin\UserRepository;
use App\Repositories\Admin\VendorRepository;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use Illuminate\Support\Facades\Mail;

class ProjectController extends Controller
{

    private $assetOwnerAssetsRepository;
    private $assetNotificationUserRepository;
    Private $projectRepository;
    Private $projectTaskIndexRepository;
    Private $subQraRequestIndexRepository;
    Private $taskTypeConceptDevelopmentRepository;
    Private $taskTypeLegalRequestRepository;
    Private $taskTypeProductBriefRepository;
    Private $taskTypeProductInformationRepository;
    Private $taskTypeMmRequestRepository;
    Private $taskTypeNpdPoRequestRepository;
    Private $taskTypeQcRequestRepository;
    Private $taskTypeProductReceivingRepository;
    Private $taskTypeDisplayRequestRepository;
    Private $taskTypePeRequestRepository;
    Private $raRequestRepository;
    private $projectTaskFileAttachmentsRepository;
    Private $projectNotesRepository;
    private $userRepository;
    private $vendorRepository;
    private $teamRepository;
    private $plantRepository;
    private $brandRepository;

    public function __construct(AssetNotificationUserRepository $assetNotificationUserRepository,
                                AssetOwnerAssetsRepository $assetOwnerAssetsRepository,
                                ProjectRepository $projectRepository,
                                ProjectTaskIndexRepository $projectTaskIndexRepository,
                                SubQraRequestIndexRepository $subQraRequestIndexRepository,
                                TaskTypeConceptDevelopmentRepository $taskTypeConceptDevelopmentRepository,
                                TaskTypeLegalRequestRepository $taskTypeLegalRequestRepository,
                                TaskTypeProductBriefRepository $taskTypeProductBriefRepository,
                                TaskTypeProductInformationRepository $taskTypeProductInformationRepository,
                                TaskTypeMmRequestRepository $taskTypeMmRequestRepository,
                                TaskTypeNpdPoRequestRepository $taskTypeNpdPoRequestRepository,
                                TaskTypeQcRequestRepository $taskTypeQcRequestRepository,
                                TaskTypeProductReceivingRepository $taskTypeProductReceivingRepository,
                                TaskTypeDisplayRequestRepository $taskTypeDisplayRequestRepository,
                                TaskTypePeRequestRepository $taskTypePeRequestRepository,
                                RaRequestRepository $raRequestRepository,
                                ProjectTaskFileAttachmentsRepository $projectTaskFileAttachmentsRepository,
                                ProjectNotesRepository $projectNotesRepository,
                                UserRepository $userRepository,
                                VendorRepository $vendorRepository,
                                TeamRepository $teamRepository,
                                PlantRepository $plantRepository,
                                BrandRepository $brandRepository
    )
    {
        parent::__construct();

        $this->assetNotificationUserRepository = $assetNotificationUserRepository;
        $this->assetOwnerAssetsRepository = $assetOwnerAssetsRepository;
        $this->projectRepository = $projectRepository;
        $this->projectTaskIndexRepository = $projectTaskIndexRepository;
        $this->subQraRequestIndexRepository = $subQraRequestIndexRepository;
        $this->taskTypeConceptDevelopmentRepository = $taskTypeConceptDevelopmentRepository;
        $this->taskTypeLegalRequestRepository = $taskTypeLegalRequestRepository;
        $this->taskTypeProductBriefRepository = $taskTypeProductBriefRepository;
        $this->taskTypeProductInformationRepository = $taskTypeProductInformationRepository;
        $this->taskTypeMmRequestRepository = $taskTypeMmRequestRepository;
        $this->taskTypeNpdPoRequestRepository = $taskTypeNpdPoRequestRepository;
        $this->taskTypeQcRequestRepository = $taskTypeQcRequestRepository;
        $this->taskTypeProductReceivingRepository = $taskTypeProductReceivingRepository;
        $this->taskTypeDisplayRequestRepository = $taskTypeDisplayRequestRepository;
        $this->taskTypePeRequestRepository = $taskTypePeRequestRepository;
        $this->raRequestRepository = $raRequestRepository;
        $this->projectTaskFileAttachmentsRepository = $projectTaskFileAttachmentsRepository;
        $this->projectNotesRepository = $projectNotesRepository;
        $this->userRepository = $userRepository;
        $this->vendorRepository = $vendorRepository;
        $this->teamRepository = $teamRepository;
        $this->plantRepository = $plantRepository;
        $this->brandRepository = $brandRepository;

    }

    public function index(Request $request)
    {

        $user_team = auth()->user()->team;
//        ddd($user_team);
        $params['team'] = $user_team;

        $params = $request->all();
        $params['status'] = array();
        $params['status'] = ["active", "pending", "review"];

        $params['category'] = ['NPD'];
        $this->data['currentAdminMenu'] = 'project';

        $this->data['team'] = !empty($params['team']) ? $params['team'] : '';
        $this->data['id'] = !empty($params['id']) ? $params['id'] : '';

        $user = auth()->user();
        if($user->role == 'Project Manager'){ // Division Unit
            $cur_user = $user->id;
            $params['cur_user'] = $cur_user;
        }else if($user->role == 'Team Lead' && $user->function == 'Product'){ // Team Lead
            $cur_team = $user->team;
            $params['cur_team'] = $cur_team;
        }else if($user->function == 'Management'){ // Unit Manager (Pre set)
            $manage_team = $user->team;
            $team_aa_group = ['Kiss A&A (Red)',
                'Red Appliance (A&A)',
                'Red Accessory & Jewelry (A&A)',
                'Red Fashion & Hair Cap (A&A)',
                'Red Brush & Implement (A&A)',
                'Red Trade Marketing (A&A)',
            ];
            $team_ch_group = ['Kiss Hair Care (C&H)',
                'Ivy Cosmetic (C&H)',
                'Ivy Hair Care (C&H)'
            ];
            $team_ld_group = [
                'Kiss Lash (LD)',
                'Ivy Lash (LD)'
            ];
            $team_nd_group = ['Kiss Nail (ND)'];

            if(in_array($manage_team, $team_aa_group)){
                $params['cur_team_group'] = $team_aa_group;
            }else if(in_array($manage_team, $team_ch_group)){
                $params['cur_team_group'] = $team_ch_group;
            }else if(in_array($manage_team, $team_ld_group)){
                $params['cur_team_group'] = $team_ld_group;
            }else if(in_array($manage_team, $team_nd_group)){
                $params['cur_team_group'] = $team_nd_group;
            }

        }
        $options = [
            'per_page' => $this->perPage,
            'order' => [
                'id' => 'desc',
            ],
            'filter' => $params,
        ];
        $this->data['filter'] = $params;
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
        $this->data['projects'] = $this->projectRepository->findAll($options);

        return view('admin.project.index', $this->data);
    }

    public function project_pre_approve_list(Request $request)
    {
        $this->data['currentAdminMenu'] = 'project_pre_approve_list';

        $params = $request->all();
        $params['status'] = ['pending', 'review'];
        $params['category'] = ['NPD'];


        $user = auth()->user();
        if($user->role == 'Team Lead' && $user->function == 'Product'){ // Team Lead
            $cur_team = $user->team;
            $params['cur_team'] = $cur_team;
        }else if($user->function == 'Management'){ // Unit Manager (Pre set)
            $manage_team = $user->team;
            $team_aa_group = ['Kiss A&A (Red)',
                'Red Appliance (A&A)',
                'Red Accessory & Jewelry (A&A)',
                'Red Fashion & Hair Cap (A&A)',
                'Red Brush & Implement (A&A)',
                'Red Trade Marketing (A&A)',
            ];
            $team_ch_group = ['Kiss Hair Care (C&H)',
                'Ivy Cosmetic (C&H)',
                'Ivy Hair Care (C&H)'
            ];
            $team_ld_group = [
                'Kiss Lash (LD)',
                'Ivy Lash (LD)'
            ];
            $team_nd_group = ['Kiss Nail (ND)'];

            if(in_array($manage_team, $team_aa_group)){
                $params['cur_team_group'] = $team_aa_group;
            }else if(in_array($manage_team, $team_ch_group)){
                $params['cur_team_group'] = $team_ch_group;
            }else if(in_array($manage_team, $team_ld_group)){
                $params['cur_team_group'] = $team_ld_group;
            }else if(in_array($manage_team, $team_nd_group)){
                $params['cur_team_group'] = $team_nd_group;
            }

        }
        $options = [
            'per_page' => $this->perPage,
            'order' => [
                'created_at' => 'desc',
            ],
            'filter' => $params,
        ];

        $this->data['projects'] = $this->projectRepository->findAll($options);

        return view('admin.project.pre_approve_list', $this->data);
    }

    public function approveProject($project_id)
    {
        $this->projectRepository->findById($project_id);
        $param['status'] = 'active';
        $param['updated_at'] = Carbon::now();

        if($this->projectRepository->update($project_id, $param)){
            // Correspondence
            $project_note = new ProjectNotes();
            $project_note['id'] = $project_id;
            $user = auth()->user();
            $project_note['user_id'] = $user->id;
            $project_note['type'] = 'project';
            $project_note['note'] = "The <b style='color: #000000;'>NPD PROJECT (#$project_id)</b> has been <b>Approved</b> by " . $user->first_name;
            $project_note['created_at'] = Carbon::now();
            $project_note->save();

            // Approve Project Notification
            $this->send_notification_approve_project($project_id);
            echo '/admin/project_pre_approve_list';
        }else{
            echo 'fail';
        }
    }

    public function send_notification_approve_project($project_id)
    {
        // From : Team Lead & Management (Approve)
        // Receiver : Project Creator
        $project_obj = $this->projectRepository->findById($project_id);

        // Task Creator
        $creator_author_name = $project_obj->author->first_name . ' ' . $project_obj->author->last_name;

        // Approve
        $user = auth()->user();
        $approve_name = $user->first_name . ' ' . $user->last_name;

        $details = [
            'template'          => 'emails.project.approve_project',
            'mail_subject'      => 'Action Requested : Project Approved',
            'receiver'          => "Hello " . $creator_author_name . ", ",
            'message'           => 'You got a new request from ' . $approve_name . ".",
            'title'             => "Action Requested : Project Approved",
            'project_id'        => $project_obj->id,
            'project_title'     => $project_obj->name,
            'url'               => '/admin/project/'.$project_obj->id.'/edit',
        ];
        $creator_email = $project_obj->author->email;

        /// Send to receivers
        Mail::to($creator_email)->send(new TaskStatusNotification($details));
    }

    public function resubmitProject($project_id)
    {
        $this->projectRepository->findById($project_id);
        $param['status'] = 'pending';
        $param['updated_at'] = Carbon::now();

        if($this->projectRepository->update($project_id, $param)){
            // Correspondence
            $project_note = new ProjectNotes();
            $project_note['id'] = $project_id;
            $user = auth()->user();
            $project_note['user_id'] = $user->id;
            $project_note['type'] = 'project';
            $project_note['note'] = "The <b style='color: #000000;'>NPD PROJECT (#$project_id)</b> has been <b>Resubmitted</b> by " . $user->first_name;
            $project_note['created_at'] = Carbon::now();
            $project_note->save();

            // Send Notification for Resubmit
            $this->send_notification_resubmit_project($project_id);
            echo '/admin/project';
        }else{
            echo 'fail';
        }
    }

    public function send_notification_resubmit_project($project_id)
    {
        // From : Project Creator
        // Receiver : Team Lead & Management (Approve)

        $project_obj = $this->projectRepository->findById($project_id);

        // Task Creator
        $creator_author_name = $project_obj->author->first_name . ' ' . $project_obj->author->last_name;

        $details = [
            'template'          => 'emails.project.new_project',
            'mail_subject'      => 'Action Requested : Project Resubmitted',
            'receiver'          => "Hello Team Lead,",
            'message'           => 'You got a new request from ' . $creator_author_name . ".",
            'title'             => "Action Requested : Project Resubmitted",
            'project_id'        => $project_obj->id,
            'project_title'     => $project_obj->name,
            'url'               => '/admin/project/'.$project_obj->id.'/edit',
        ];

        $division_team = $project_obj->team;
        $group_rs = $this->userRepository->get_product_team_lead_emails_by_team($division_team);

        foreach ($group_rs as $team_user) {
            $receiver_list[] = $team_user['email'];
        }

        // Add Managers manually
        if ($division_team == 'Kiss Lash (LD)'){
            $receiver_list[] = 'kichula@kissusa.com';
        }elseif ($division_team == 'Kiss Hair Care (C&H)'){
            $receiver_list[] = 'IvanC@kissusa.com';
        }elseif ($division_team == 'Kiss A&A (Red)'){
            $receiver_list[] = 'younghunk@kissusa.com';
        }elseif ($division_team == 'Ivy Lash (LD)'){
            $receiver_list[] = 'kichula@kissusa.com';
        }elseif ($division_team == 'Ivy Cosmetic (C&H)'){
            $receiver_list[] = 'IvanC@kissusa.com';
        }elseif ($division_team == 'Ivy Hair Care (C&H)'){
            $receiver_list[] = 'IvanC@kissusa.com';
        }elseif ($division_team == 'Red Appliance (A&A)'){
            $receiver_list[] = 'younghunk@kissusa.com';
        }elseif ($division_team == 'Red Accessory & Jewelry (A&A)'){
            $receiver_list[] = 'younghunk@kissusa.com';
        }elseif ($division_team == 'Red Fashion & Hair Cap (A&A)'){
            $receiver_list[] = 'younghunk@kissusa.com';
        }elseif ($division_team == 'Red Brush & Implement (A&A)'){
            $receiver_list[] = 'younghunk@kissusa.com';
        }elseif ($division_team == 'Kiss Nail (ND)') {
//            $receiver_list[] = 'James@kissusa.com';
        }else{
            //test for developer
            $receiver_list[] = 'jilee2@kissusa.com';
            $receiver_list[] = 'admin@projectspace.net';
        }

        /// Send to receivers
        Mail::to($receiver_list)
            ->send(new TaskStatusNotification($details));
    }

    public function index_general(Request $request)
    {
        $params = $request->all();
        $params['status'] = ['active'];
        $params['category'] = ['General'];
        $this->data['currentAdminMenu'] = 'project_general';

        $params['status'] = ["active", "pending", "review"];

        $options = [
            'per_page' => $this->perPage,
            'order' => [
                'id' => 'desc',
            ],
            'filter' => $params,
        ];
        $this->data['filter'] = $params;
//        $this->data['brands'] = $this->campaignBrandsRepository->findAll()->pluck('campaign_name', 'id');
//        $this->data['campaigns'] = $this->campaignRepository->findAll($options);

        $this->data['projects'] = $this->projectRepository->findAll($options);

//        $this->data['brand'] = !empty($params['brand']) ? $params['brand'] : '';
        $this->data['id'] = !empty($params['id']) ? $params['id'] : '';

        return view('admin.project_general.index', $this->data);
    }

    public function index_promotion(Request $request)
    {
        $this->data['currentAdminMenu'] = 'project_promotion';
        $params = $request->all();
        $params['status'] = ['active'];

        $options = [
            'per_page' => $this->perPage,
            'order' => [
                'id' => 'desc',
            ],
            'filter' => $params,
        ];
        $this->data['filter'] = $params;
//        $this->data['brands'] = $this->campaignBrandsRepository->findAll()->pluck('campaign_name', 'id');
//        $this->data['campaigns'] = $this->campaignRepository->findAll($options);

        $this->data['projects'] = null;

//        $this->data['brand'] = !empty($params['brand']) ? $params['brand'] : '';
        $this->data['id'] = !empty($params['id']) ? $params['id'] : '';

        return view('admin.project_promotion.index', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->data['currentAdminMenu'] = 'project';
        $team_options = [
            'order' => [
                'id' => 'asc',
            ],
            'filter' => [
                'is_active' => 'yes'
            ],
        ];
        $this->data['teams'] =$this->teamRepository->findAll($team_options);
        $options = [
            'filter' =>
                [
                    'is_active' => 'yes'
                ]
        ];
        $this->data['brands'] = $this->brandRepository->findAll($options);
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
        $this->data['priorities'] = [
            'Urgent','Normal','Low'
        ];
        $this->data['mm_request_types'] = [
            'New','Update','Dimensions/Logistics','Price'
        ];
        $this->data['team'] = auth()->user()->team;
        $this->data['brand'] = null;
        $this->data['project_type'] = null;
        $this->data['project_year'] = null;
        $this->data['international_sales_plan'] = null;
        $this->data['author_name'] = null;

        $this->data['kiss_users'] = $this->userRepository->getKissUsers();

        return view('admin.project.form', $this->data);
    }

    public function create_general()
    {
        $this->data['currentAdminMenu'] = 'project_general';
        $team_options = [
            'order' => [
                'id' => 'asc',
            ],
            'filter' => [
                'is_active' => 'yes'
            ],
        ];
        $this->data['teams'] =$this->teamRepository->findAll($team_options);
        $options = [
            'filter' =>
                [
                    'is_active' => 'yes'
                ]
        ];
        $this->data['brands'] = $this->brandRepository->findAll($options);

        $this->data['project_types'] = [
            'New',
            'Soft Change',
            'Hard Change',
            'SKU Extension',
        ];
        $this->data['project_year_list'] = [
            '2024','2025','2026',
            '2027', '2028', '2029',
            '2030', '2031', '2032',
        ];
        $this->data['sales_plan_list'] = [
            'YES','NO',
        ];
        $this->data['priorities'] = [
            'Urgent','Normal','Low'
        ];
        $this->data['team'] = auth()->user()->team;
        $this->data['brand'] = null;
        $this->data['project_type'] = null;
        $this->data['author_name'] = null;

        $this->data['kiss_users'] = $this->userRepository->getKissUsers();

        return view('admin.project_general.form', $this->data);
    }

    public function store(ProjectRequest $request)
    {
        $params = $request->validated();

        $user = auth()->user();
        $params['category'] = 'NPD';
        $params['author_name'] = $user->first_name;
        $params['author_id'] = $user->id;
        $params['author_dept'] = $user->team;
        $params['status'] = 'pending';
        $params['created_at'] = Carbon::now();

        $project = $this->projectRepository->create($params);
        if ($project) {
            // Correspondence
            $project_note = new ProjectNotes();
            $project_note['id'] = $project->id;
            $project_note['user_id'] = $params['author_id'];
            $project_note['task_id'] = NULL;
            $project_note['type'] = 'project';
            $project_note['note'] = "A new <b style='color: #000000;'>NPD PROJECT (#$project->id)</b> has been <b>created</b> by " . $params['author_name'];
            $project_note['created_at'] = Carbon::now();
            $project_note->save();

            // Send Notification
            $this->send_notification_new_project($project);

            return redirect('admin/project/'.$project->id.'/edit')
                ->with('success', __('Your project has been successfully submitted for approval. ID : ' . $project->id));
        }
        return redirect('admin/project/create')
            ->with('error', __('The Project could not be saved.'));
    }

    public function send_notification_new_project($project)
    {
        // From : Division
        // Receiver : Team Lead & Management
        $project_obj = $project;

        // Task Creator
        $creator_author_name = $project_obj->author->first_name . ' ' . $project_obj->author->last_name;

        $details = [
            'template'          => 'emails.project.new_project',
            'mail_subject'      => 'Action Requested : New Project',
            'receiver'          => "Hello Team Lead,",
            'message'           => 'You got a new request from ' . $creator_author_name . ".",
            'title'             => "Action Requested : New Project",
            'project_id'        => $project_obj->id,
            'project_title'     => $project_obj->name,
            'url'               => '/admin/project/'.$project_obj->id.'/edit',
        ];

        $division_team = $project_obj->team;
        $group_rs = $this->userRepository->get_product_team_lead_emails_by_team($division_team);

        foreach ($group_rs as $team_user) {
            $receiver_list[] = $team_user['email'];
        }

        // Add Managers manually
        if ($division_team == 'Kiss Lash (LD)'){
            $receiver_list[] = 'kichula@kissusa.com';
        }elseif ($division_team == 'Kiss Hair Care (C&H)'){
            $receiver_list[] = 'IvanC@kissusa.com';
        }elseif ($division_team == 'Kiss A&A (Red)'){
            $receiver_list[] = 'younghunk@kissusa.com';
        }elseif ($division_team == 'Ivy Lash (LD)'){
            $receiver_list[] = 'kichula@kissusa.com';
        }elseif ($division_team == 'Ivy Cosmetic (C&H)'){
            $receiver_list[] = 'IvanC@kissusa.com';
        }elseif ($division_team == 'Ivy Hair Care (C&H)'){
            $receiver_list[] = 'IvanC@kissusa.com';
        }elseif ($division_team == 'Red Appliance (A&A)'){
            $receiver_list[] = 'younghunk@kissusa.com';
        }elseif ($division_team == 'Red Accessory & Jewelry (A&A)'){
            $receiver_list[] = 'younghunk@kissusa.com';
        }elseif ($division_team == 'Red Fashion & Hair Cap (A&A)'){
            $receiver_list[] = 'younghunk@kissusa.com';
        }elseif ($division_team == 'Red Brush & Implement (A&A)'){
            $receiver_list[] = 'younghunk@kissusa.com';
        }elseif ($division_team == 'Kiss Nail (ND)') {
//            $receiver_list[] = 'James@kissusa.com';
        }else{
            //test for developer
            $receiver_list[] = 'jilee2@kissusa.com';
        }

        /// Send to receivers
        Mail::to($receiver_list)
            ->send(new TaskStatusNotification($details));
    }


    public function store_general(Request $request)
    {
        $params = $request->request->all();

        $user = auth()->user();
        $params['category'] = 'General';
        $params['author_name'] = $user->first_name;
        $params['author_id'] = $user->id;
        $params['author_dept'] = $user->team;
        $params['status'] = 'active';
        $params['created_at'] = Carbon::now();

        $project = $this->projectRepository->create($params);
        if ($project) {
            // Correspondence
            $project_note = new ProjectNotes();
            $project_note['id'] = $project->id;
            $project_note['user_id'] = $params['author_id'];
            $project_note['task_id'] = NULL;
            $project_note['type'] = 'project_general';
            $project_note['note'] = "A new <b style='color: #000000;'>Internal Request (#$project->id)</b> has been created by " . $params['author_name'];
            $project_note['created_at'] = Carbon::now();
            $project_note->save();

            return redirect('admin/project/'.$project->id.'/edit_general')
                ->with('success', __('New Project has been created. ID : ' . $project->id));
        }
        return redirect('admin/project/create_general')
            ->with('error', __('The Project could not be saved.'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if(basename(url()->previous()) == 'project_pre_approve_list'){
            $this->data['currentAdminMenu'] = 'project_pre_approve_list';
        }else{
            $this->data['currentAdminMenu'] = 'project';
        }

        $this->data['users'] = $this->userRepository->findAll([
            'order' => [
                'first_name' => 'asc',
            ]
        ]);

        // Campaign_type_asset_attachments
        $options = [
            'id' => $id,
            'order' => [
                'date_created' => 'desc',
            ]
        ];

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
        $options = [
            'filter' =>
                [
                    'is_active' => 'yes'
                ]
        ];
        $this->data['brands'] = $this->brandRepository->findAll($options);
        $this->data['project_types'] = [
            'New', 'Soft Change', 'Hard Change', 'SKU Extension'
        ];
        $this->data['project_year_list'] = [
            '2024','2025','2026',
            '2027', '2028', '2029',
            '2030', '2031', '2032',
        ];
        $this->data['sales_plan_list'] = [
            'YES','NO',
        ];
        $this->data['priorities'] = [
            'Urgent','Normal','Low'
        ];
        $this->data['mm_request_types'] = [
            'New','Update','Dimensions/Logistics','Price'
        ];
        $this->data['team'] = $project->team;
        $this->data['brand'] = $project->brand;
        $this->data['project_type'] = $project->project_type;
        $this->data['project_year'] = $project->project_year;

        $author_obj = User::find($project->author_id);

        if($author_obj){
            $this->data['author_name'] = $author_obj['first_name'] . " " . $author_obj['last_name'];
        }else{
            $this->data['author_name'] = 'N/A';
        }

        $this->data['kiss_users'] = $this->userRepository->getKissUsers();

        // Tasks
        $this->data['tasks'] = $task_list = $this->projectTaskIndexRepository->get_task_list_by_project_id($id);

        // task_detail
        if(sizeof($task_list)>0){
            foreach ($task_list as $k => $task){
                $p_id = $task->project_id;
                $t_id = $task->id;
                $t_type = $task->type;
                $task_detail = $this->projectTaskIndexRepository->get_task_detail($p_id, $t_id, $t_type);
                $task_list[$k]->detail = $task_detail;
                $design_detail = $this->projectTaskIndexRepository->get_design_detail($t_id);
                $task_list[$k]->design_detail = $design_detail;
                $legal_detail = $this->projectTaskIndexRepository->get_legal_detail($t_id);
                $task_list[$k]->legal_detail = $legal_detail;
                $mm_detail = $this->projectTaskIndexRepository->get_mm_detail($t_id);
                $task_list[$k]->mm_detail = $mm_detail;
                $pe_detail = $this->projectTaskIndexRepository->get_pe_detail($t_id);
                $task_list[$k]->pe_detail = $pe_detail;
                $ra_detail = $this->projectTaskIndexRepository->get_ra_detail($t_id);
                $task_list[$k]->ra_detail = $ra_detail;
                $planner_detail = $this->projectTaskIndexRepository->get_planner_detail($t_id);
                $task_list[$k]->planner_detail = $planner_detail;
                $task_files = $this->projectTaskFileAttachmentsRepository->findAllByTaskId($t_id);
                $task_list[$k]->files = $task_files;
            }
        }

        // All Task List
        $this->data['all_task_list'] = $all_task_list = [
//            'concept_development'   => 'Concept Development',
            'mm_request'            => 'MM Request',
            'npd_planner_request'   => 'NPD Planner Request',
            'legal_request'         => 'Legal Request',
            'ra_request'            => 'RA Request',
            'npd_po_request'        => 'NPD PO Request',
            'npd_design_request'    => 'NPD Design Request',
            'pe_request'            => 'DISPLAY & PE Request',
            'qc_request'            => 'QA Request',
            'product_information'   => 'Product Information',
        ];

        // In Active Task List Make
        $inactive_task_list = array();
        $res = $this->projectTaskIndexRepository->active_task_list($id);

        if(count($res) > 0){
            $active_task_list = array();
            foreach ($res as $val){
                $active_task_list[] = $val->type;
            }
            $this->data['active_task_list'] = $active_task_list;
            // Inactive task list
            foreach ($all_task_list as $key =>$val){
                if(!in_array($key, $active_task_list)){
                    $inactive_task_list[$key] = $val;
                }
            }
            $this->data['inactive_task_list'] =$inactive_task_list;

        }else{
            $this->data['inactive_task_list'] =$all_task_list;
        }

        // Project_notes
        $options = [
            'id' => $id,
            'order' => [
                'created_at' => 'desc',
            ]
        ];
        $correspondences = $this->projectNotesRepository->findAll($options);
        $this->data['correspondences'] = $correspondences;

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

        /////////// Product Information Task ////////////////////////////////////////////
        $this->data['sustainability_list'] = [
            'Biodegradable', 'Carbon Offsetting', 'Eco-Friendly Materials', 'Energy Efficiency', 'Recyclable',
            'Reduced Packaging', 'Renewable Material', 'N/A', 'Others'
        ];
        $this->data['weight_unit_list'] = [
            'Gram (g)', 'Milligram (mg)', 'Pound (lb)', 'Ounce (Oz)', 'Milliliter (ml)', 'N/A'
        ];

        /////////// Product Brief Task ////////////////////////////////////////////
        $this->data['categories'] = [
            'Face','Eye','Lip','Acc & Tool','Moisturizer','Cleanser','Mask','Treatments','Sun','Body','Self-Tanning'
        ];
        $this->data['franchises'] = [
            'Glass','Calm','Enrich','N/A'
        ];
        $this->data['finishes'] = [
            'Sheer','Glow','Satin','Semi-matte','Matte','Natural','Radiant','Shimmer','Metalic'
        ];
        $this->data['product_formats'] = [
            'Aerosol','Assorted','Balm','Brush','Bubble','Buffer','Butter','Capsule','Cartridge','Clay','Cloth','Cotton','Crayon','Cream','Cushion',
            'Cushion tip','Drop','Dual ended','Elixir','Emulsion','Fluid','Foam','Gel','Gloss','Jelly','Kit','Liquid','Loofah','Loose powder',
            'Lotion', 'Marker', 'Mask', 'Milk', 'Mist', 'Mitt', 'Mousse', 'Mud', 'Oil', 'Ointment', 'Pad', 'Paper', 'Paste', 'Patch', 'Peel',
            'Peel off', 'Pen', 'Pencil', 'Polish', 'Pomade', 'Powder', 'Pressed Powder', 'Puff', 'Putty', 'Reusable', 'Roll on', 'Rubber', 'Scrub',
            'Self-sharpening pencil', 'Serum', 'Sheet', 'Soap', 'Souffle', 'Spatula', 'Sponge', 'Spray', 'Stamp', 'Stick', 'Sticker', 'Sugar',
            'Swab', 'Tattoo', 'Tissue', 'Toner', 'Towel', 'Wand', 'Wash', 'Wash off', 'Water', 'Wax', 'Whipped', 'Wipe', 'Wooden pencil'
        ];
        $this->data['textures'] = [
          'Normal', 'Oily', 'Dry', 'Combination', 'Sensitive', 'All'
        ];
        $this->data['coverages'] = [
            'Sheer Coverage', 'Medium Coverage', 'Full Coverage'
        ];
        $this->data['highlightss'] = [
            'JOAH CLEAN','ULTA CLEAN','TARGET CLEAN','VEGAN','CRUELTY FREE','FORMALDEHYDE FREE','PHTHALATE FREE','SODIUM LAURETH SULFATE FREE',
            'PARABEN FREE','COAL TAR DYES FREE','DEA RELATED INGREDIENTS FREE','TRICLOSAN FREE','GLUTEN FREE','NUT FREE','ALUMINUM FREE',
            'BHA FREE','BHT FREE','SOY FREE','MINERAL OIL FREE','OIL FREE','PEG FREE','SILOXANE FREE','SILICONE FREE','FRAGRANCE FREE','TALC FREE'
        ];
        /////////////////////////////////////////////////////////////////////////

        /////////// RA Request Task ////////////////////////////////////////////
        $this->data['request_types'] = [
            'Formulation Prescreen', 'Artwork Contents', 'Artwork Review', 'Registration WERCS', 'Registration SmarterX',
            'Registration California', 'Registration CNF', 'Registration CPNP', 'Registration SCPN', 'Registration IIO',
            'Registration MoCRA', 'Document Support'
        ];
        $this->data['request_type'] = null;

        $this->data['target_regions'] = [
            'U.S.', 'Canada', 'EU', 'UK'
        ];
        /////////////////////////////////////////////////////////////////////////

        /////////// Onsite QC Request Task ////////////////////////////////////////////
        $this->data['performed_bys'] = [
            'SGS', 'QM QA', 'Sourcing'
        ];
        /////////////////////////////////////////////////////////////////////////

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

//        $vendor_list = $this->vendorRepository->findAll();
//        $this->data['vendor_list'] = response()->json($vendor_list);

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
        /////////////////////////////////////////////////////////////////////////



        return view('admin.project.form', $this->data);
    }

    public function autocomplete_vendor()
    {
        if(isset($_GET['code'])) {
            $code = $_GET['code'];
            $vendor = new Vendor();
            $vendor = $vendor->Where('code', 'LIKE', "%$code%")
                ->Where('is_active', '=', 'yes');
            return $vendor->get();
        }else{
            return [];
        }
    }

    public function autocomplete_brand()
    {
        if(isset($_GET['brand'])) {
            $name = $_GET['brand'];
            $brand = new Brand();
            $brand = $brand->Where('name', 'LIKE', "%$name%")
                ->Where('is_active', '=', 'yes');;
            return $brand->get();
        }else{
            return [];
        }
    }

    public function autocomplete_product_category()
    {
        if(isset($_GET['product_category'])) {
            $name = $_GET['product_category'];
            $brand = new ProductCategory();
            $brand = $brand->Where('name', 'LIKE', "%$name%")
                ->Where('is_active', '=', 'yes');;
            return $brand->get();
        }else{
            return [];
        }
    }

    public function autocomplete_product_segment()
    {
        if(isset($_GET['product_segment'])) {
            $name = $_GET['product_segment'];
            $brand = new ProductSegment();
            $brand = $brand->Where('name', 'LIKE', "%$name%")
                ->Where('is_active', '=', 'yes');;
            return $brand->get();
        }else{
            return [];
        }
    }

    public function edit_general($id)
    {
        $this->data['currentAdminMenu'] = 'project_general';

        $this->data['users'] = $this->userRepository->findAll([
            'order' => [
                'first_name' => 'asc',
            ]
        ]);

        // Campaign_type_asset_attachments
        $options = [
            'id' => $id,
            'order' => [
                'date_created' => 'desc',
            ]
        ];

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
            'New', 'Soft Change', 'Hard Change', 'SKU Extension'
        ];
        $this->data['project_year_list'] = [
            '2024','2025','2026',
            '2027', '2028', '2029',
            '2030', '2031', '2032',
        ];
        $this->data['sales_plan_list'] = [
            'YES','NO',
        ];
        $this->data['priorities'] = [
            'Urgent','Normal','Low'
        ];
        $this->data['mm_request_types'] = [
            'New','Update','Dimensions/Logistics','Price'
        ];
        $this->data['mm_request_set_up_plants'] = [
            '1000_KISS','1010_Kiss Canada','1100_IVY','1110_IVY LA',
            '1300_AST','1310_IVY E-commerce','1320_KISS E-commerce',
            '1410_Red Beauty LA','1700_Kiss MX','4027_KGH','4021_KGH UK',
            '4023_IIO UK','4028_BSH UK','G100_KISS GA','G110_IVY GA',
            'G130_AST GA','G140_RED GA','G190_Vivace GA'
        ];
        $this->data['team'] = $project->team;
        $this->data['brand'] = $project->brand;
        $this->data['project_type'] = $project->project_type;
        $this->data['project_year'] = $project->project_year;

        $author_obj = User::find($project->author_id);

        if($author_obj){
            $this->data['author_name'] = $author_obj['first_name'] . " " . $author_obj['last_name'];
        }else{
            $this->data['author_name'] = 'N/A';
        }

        $this->data['kiss_users'] = $this->userRepository->getKissUsers();

        // Tasks
        $this->data['tasks'] = $task_list = $this->projectTaskIndexRepository->get_task_list_by_project_id($id);

        // task_detail
        if(sizeof($task_list)>0){
            foreach ($task_list as $k => $task){
                $p_id = $task->project_id;
                $t_id = $task->id;
                $t_type = $task->type;
                $task_detail = $this->projectTaskIndexRepository->get_task_detail($p_id, $t_id, $t_type);
                $task_list[$k]->detail = $task_detail;
                $qra_detail = $this->projectTaskIndexRepository->get_qra_detail($t_id);
                $task_list[$k]->qra_detail = $qra_detail;
                $legal_detail = $this->projectTaskIndexRepository->get_legal_detail($t_id);
                $task_list[$k]->legal_detail = $legal_detail;
                $mm_detail = $this->projectTaskIndexRepository->get_mm_detail($t_id);
                $task_list[$k]->mm_detail = $mm_detail;
                $task_files = $this->projectTaskFileAttachmentsRepository->findAllByTaskId($t_id);
                $task_list[$k]->files = $task_files;
            }
        }

        // Project_notes
        $options = [
            'id' => $id,
            'order' => [
                'created_at' => 'desc',
            ]
        ];
        $correspondences = $this->projectNotesRepository->findAll($options);
        $this->data['correspondences'] = $correspondences;

        /////////// Product Brief Task ////////////////////////////////////////////
        $this->data['categories'] = [
            'Face','Eye','Lip','Acc & Tool','Moisturizer','Cleanser','Mask','Treatments','Sun','Body','Self-Tanning'
        ];
        $this->data['franchises'] = [
            'Glass','Calm','Enrich','N/A'
        ];
        $this->data['finishes'] = [
            'Sheer','Glow','Satin','Semi-matte','Matte','Natural','Radiant','Shimmer','Metalic'
        ];
        $this->data['product_formats'] = [
            'Aerosol','Assorted','Balm','Brush','Bubble','Buffer','Butter','Capsule','Cartridge','Clay','Cloth','Cotton','Crayon','Cream','Cushion',
            'Cushion tip','Drop','Dual ended','Elixir','Emulsion','Fluid','Foam','Gel','Gloss','Jelly','Kit','Liquid','Loofah','Loose powder',
            'Lotion', 'Marker', 'Mask', 'Milk', 'Mist', 'Mitt', 'Mousse', 'Mud', 'Oil', 'Ointment', 'Pad', 'Paper', 'Paste', 'Patch', 'Peel',
            'Peel off', 'Pen', 'Pencil', 'Polish', 'Pomade', 'Powder', 'Pressed Powder', 'Puff', 'Putty', 'Reusable', 'Roll on', 'Rubber', 'Scrub',
            'Self-sharpening pencil', 'Serum', 'Sheet', 'Soap', 'Souffle', 'Spatula', 'Sponge', 'Spray', 'Stamp', 'Stick', 'Sticker', 'Sugar',
            'Swab', 'Tattoo', 'Tissue', 'Toner', 'Towel', 'Wand', 'Wash', 'Wash off', 'Water', 'Wax', 'Whipped', 'Wipe', 'Wooden pencil'
        ];
        $this->data['textures'] = [
            'Normal', 'Oily', 'Dry', 'Combination', 'Sensitive', 'All'
        ];
        $this->data['coverages'] = [
            'Sheer Coverage', 'Medium Coverage', 'Full Coverage'
        ];
        $this->data['highlightss'] = [
            'JOAH CLEAN','ULTA CLEAN','TARGET CLEAN','VEGAN','CRUELTY FREE','FORMALDEHYDE FREE','PHTHALATE FREE','SODIUM LAURETH SULFATE FREE',
            'PARABEN FREE','COAL TAR DYES FREE','DEA RELATED INGREDIENTS FREE','TRICLOSAN FREE','GLUTEN FREE','NUT FREE','ALUMINUM FREE',
            'BHA FREE','BHT FREE','SOY FREE','MINERAL OIL FREE','OIL FREE','PEG FREE','SILOXANE FREE','SILICONE FREE','FRAGRANCE FREE','TALC FREE'
        ];
        /////////////////////////////////////////////////////////////////////////

        /////////// RA Request Task ////////////////////////////////////////////
        $this->data['request_types'] = [
            'Formulation Prescreen', 'Artwork Contents', 'Artwork Review', 'Registration WERCS', 'Registration SmarterX',
            'Registration California', 'Registration CNF', 'Registration CPNP', 'Registration SCPN', 'Registration IIO',
            'Registration MoCRA', 'Document Support'
        ];
        $this->data['request_type'] = null;

        $this->data['target_regions'] = [
            'U.S.', 'Canada', 'EU', 'UK'
        ];
        /////////////////////////////////////////////////////////////////////////

        /////////// Onsite QC Request Task ////////////////////////////////////////////
        $this->data['performed_bys'] = [
            'SGS', 'QM QA', 'Sourcing'
        ];
        /////////////////////////////////////////////////////////////////////////

        return view('admin.project_general.form', $this->data);
    }


    public function archives(Request $request)
    {
        $params = $request->all();
        $this->data['currentAdminMenu'] = 'archives';

        $options = [
            'per_page' => $this->perPage,
            'order' => [
                'date_created' => 'desc',
            ],
            'filter' => $params,
        ];
        $this->data['filter'] = $params;
        $this->data['campaigns'] = $this->campaignRepository->findAll($options);

        return view('admin.campaign.archives', $this->data);
    }

    public function sendArchive($project_id)
    {
        $this->campaignRepository->findById($project_id);
        $param['status'] = 'archived';
        $param['updated_at'] = Carbon::now();

        if($this->campaignRepository->update($project_id, $param)){
            // Correspondence
            $campaign_note = new CampaignNotes();
            $campaign_note['id'] = $project_id;
            $user = auth()->user();
            $campaign_note['user_id'] = $user->id;
            $campaign_note['type'] = 'campaign';
            $campaign_note['note'] = $user->first_name . " Sent this Project to Archive";
            $campaign_note['date_created'] = Carbon::now();
            $campaign_note->save();
            echo '/admin/campaign';
        }else{
            echo 'fail';
        }
    }

    public function sendActive($project_id)
    {
        $this->campaignRepository->findById($project_id);
        $param['status'] = 'active';
        $param['updated_at'] = Carbon::now();

        if($this->campaignRepository->update($project_id, $param)){
            // Correspondence
            $campaign_note = new CampaignNotes();
            $campaign_note['id'] = $project_id;
            $user = auth()->user();
            $campaign_note['user_id'] = $user->id;
            $campaign_note['type'] = 'campaign';
            $campaign_note['note'] = $user->first_name . " Sent this Project to Active Back";
            $campaign_note['date_created'] = Carbon::now();
            $campaign_note->save();
            echo '/admin/deleted';
        }else{
            echo 'fail';
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ProjectRequest $request, $id)
    {
        $this->data['currentAdminMenu'] = 'project';
        $project = $this->projectRepository->findById($id);
        $user = auth()->user();

        // Insert into campaign note for correspondence
        $data = $request->request->all();
//        ddd($data);

        $new = array(
            'id'                => $data['id'],
            'team'              => $data['team'],
            'brand'             => $data['brand'],
            'name'              => $data['name'],
            'description'       => $data['description'],
            'project_type'      => $data['project_type'],
            'project_year'      => $data['project_year'],
            'launch_date'       => $data['launch_date'],
            'international_sales_plan' => $data['international_sales_plan'],
            'sale_available_date' => $data['sale_available_date'],
        );
//        ddd(htmlspecialchars_decode($data['campaign_notes']));
        $origin = $project->toArray();
        foreach ($new as $key => $value) {
            if (array_key_exists($key, $origin)) {
                if (html_entity_decode($new[$key]) != html_entity_decode($origin[$key])) {
                    $changed[$key]['new'] = $new[$key];
                    $changed[$key]['original'] = $origin[$key];
                }
            }
        }
        $change_line  = "<p>$user->first_name made a change to a <b style='color: #000000;'>PROJECT (#$project->id)</b></p>";
        if(!empty($changed)){
            foreach ($changed as $label => $change) {

                $label = ucwords(str_replace('_', ' ', $label));
                $from  = trim($change['original']); // Remove strip tags
                $to    = trim($change['new']);      // Remove strip tags

                $change_line .= "<div class='change_label'><p>$label:</p></div>"
                    . "<div class='change_to'><p>$to</p></div>"
                    . "<div class='change_from'><del><p>$from</p></del></div>";
            }
            $project_note = new ProjectNotes();
            $project_note['id'] = $project->id;
            $project_note['user_id'] = $user->id;
            $project_note['task_id'] = NULL;
            $project_note['type'] = 'project';
            $project_note['note'] = $change_line;
            $project_note['created_at'] = Carbon::now();
            $project_note->save();
        }

        if ($this->projectRepository->update($id, $data)) {

            return redirect('admin/project/'.$id.'/edit')
                ->with('success', __('Update Success'));
        }
        return redirect('admin/project/'.$id.'/edit')
            ->with('error', __('Update was failed'));
    }

    public function update_general(Request $request, $id)
    {
        $this->data['currentAdminMenu'] = 'project_general';
        $project = $this->projectRepository->findById($id);
        $user = auth()->user();

        // Insert into campaign note for correspondence
        $data = $request->request->all();
//        ddd($data);

        $new = array(
            'id'                => $data['id'],
            'team'              => $data['team'],
            'name'              => $data['name'],
            'description'       => $data['description'],
            'due_date'          => $data['due_date'],
        );
//        ddd(htmlspecialchars_decode($data['campaign_notes']));
        $origin = $project->toArray();
        foreach ($new as $key => $value) {
            if (array_key_exists($key, $origin)) {
                if (html_entity_decode($new[$key]) != html_entity_decode($origin[$key])) {
                    $changed[$key]['new'] = $new[$key];
                    $changed[$key]['original'] = $origin[$key];
                }
            }
        }
        $change_line  = "<p>$user->first_name made a change to a project</p>";
        if(!empty($changed)){
            foreach ($changed as $label => $change) {

                $label = ucwords(str_replace('_', ' ', $label));
                $from  = trim($change['original']); // Remove strip tags
                $to    = trim($change['new']);      // Remove strip tags

                $change_line .= "<div class='change_label'><p>$label:</p></div>"
                    . "<div class='change_to'><p>$to</p></div>"
                    . "<div class='change_from'><del><p>$from</p></del></div>";
            }
            $project_note = new ProjectNotes();
            $project_note['id'] = $project->id;
            $project_note['user_id'] = $user->id;
            $project_note['task_id'] = NULL;
            $project_note['type'] = 'project';
            $project_note['note'] = $change_line;
            $project_note['created_at'] = Carbon::now();
            $project_note->save();
        }

        if ($this->projectRepository->update($id, $data)) {

            return redirect('admin/project/'.$id.'/edit_general')
                ->with('success', __('Update Success'));
        }
        return redirect('admin/project/'.$id.'/edit_general')
            ->with('error', __('Update was failed'));
    }

    public function fileRemove($id)
    {
        $projectTypeTaskAttachment = $this->projectTaskFileAttachmentsRepository->findById($id);

        $file_name = $projectTypeTaskAttachment->attachment;
        $project_id = $projectTypeTaskAttachment->project_id;
        $task_id = $projectTypeTaskAttachment->task_id;

        $user = auth()->user();

        if($projectTypeTaskAttachment->delete()){

            $taskIndex = $this->projectTaskIndexRepository->findById($task_id);
            $task_type =  ucwords(str_replace('_', ' ', $taskIndex->type));

            $change_line = "<p>$user->first_name has removed a attachment ($file_name) on <b style='color: #b91d19'>$task_type</b> <b>(#$task_id)</b></p>";
            $project_note['type'] = $taskIndex->type;

            $project_note = new ProjectNotes();
            $project_note['id'] = $project_id;
            $project_note['user_id'] = $user->id;
            $project_note['task_id'] = $task_id;
            $project_note['note'] = $change_line;
            $project_note['created_at'] = Carbon::now();
            $project_note->save();

            echo 'success';
        }else{
            echo 'fail';
        }
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

    public function add_concept_development(TaskConceptDevelopmentRequest $request)
    {

        // add Campaign_asset_index
        $projectTaskIndex = new ProjectTaskIndex();
        $projectTaskIndex['project_id'] = $request['concept_development_p_id'];
        $projectTaskIndex['type'] = $request['concept_development_task_type'];
        $projectTaskIndex['status'] = 'in_progress';
        $projectTaskIndex['due_date'] = $request['concept_development_due_date'];

        $user = auth()->user(); // asset_author_id
        $projectTaskIndex['author_id'] = $user->id;
        $projectTaskIndex->save();
        $task_id = $projectTaskIndex->id;

        // add campaign_type_concept_development
        $taskTypeConceptDevelopment = new TaskTypeConceptDevelopment();
        $taskTypeConceptDevelopment['id'] = $request['concept_development_p_id']; //project_id
        $taskTypeConceptDevelopment['author_id'] = $request['concept_development_author_id'];
        $taskTypeConceptDevelopment['type'] = $request['concept_development_task_type'];
        $taskTypeConceptDevelopment['benchmark'] = $request['concept_development_benchmark'];
        $taskTypeConceptDevelopment['due_date'] = $request['concept_development_due_date'];
        $taskTypeConceptDevelopment['created_at'] = Carbon::now();
        $taskTypeConceptDevelopment['task_id'] = $task_id;
        $taskTypeConceptDevelopment->save();

        // new correspondence when adding asset
        $this->correspondence_add_new_task($projectTaskIndex['project_id'], 'CONCEPT DEVELOPMENT', $projectTaskIndex);

        // add campaign_type_asset_attachments
        if($request->file('concept_development_p_attachment')){
            foreach ($request->file('concept_development_p_attachment') as $file) {
                $project_type_task_attachments = new ProjectTypeTaskAttachments();

//                $fileName = $file->storeAs('campaigns/'.$request['concept_development_c_id'].'/'.$asset_id, $originalName);
                $fileName = $this->file_exist_check($file, $request['concept_development_p_id'], $task_id);

                $project_type_task_attachments['project_id'] = $request['concept_development_p_id'];
                $project_type_task_attachments['task_id'] = $task_id;
                $project_type_task_attachments['type'] = 'attachment_file_' . $file->getMimeType();
                $project_type_task_attachments['author_id'] = $request['concept_development_author_id'];
                $project_type_task_attachments['attachment'] = '/' . $fileName;
                $project_type_task_attachments['file_ext'] = pathinfo($fileName, PATHINFO_EXTENSION);
                $project_type_task_attachments['file_type'] = $file->getMimeType();
                $project_type_task_attachments['file_size'] = $file->getSize();
                $project_type_task_attachments['created_at'] = Carbon::now();
                $project_type_task_attachments->save();
            }
        }

        // TODO notification
        // Send notification to copywriter(brand check) via email
        // Do action - copy request
//        if($projectTaskIndex['status'] == 'copy_requested') {
//            $notify = new NotifyController();
//            $notify->copy_request($request['concept_development_c_id'], $task_id);
//        } else if($projectTaskIndex['status'] == 'copy_complete') {
//            $notify = new NotifyController();
//            $notify->copy_complete($request['concept_development_c_id'], $task_id);
//        }
        ///////////////////////////////////////////////////////////////

        return redirect('admin/project/'.$request['concept_development_p_id'].'/edit#'.$task_id)
            ->with('success', __('Added the Concept Development Task : ' . $task_id));
    }

    public function edit_concept_development(Request $request, $task_id)
    {
        $concept_development = $this->taskTypeConceptDevelopmentRepository->findById($task_id);

        $param = $request->request->all();
        // Permission_check
//        if(!$this->permission_check($param)){
//            return redirect('admin/project/' . $concept_development->id . '/edit#'.$task_id)
//                ->with('error', __('This action is no longer permitted. Please contact an Administrator.'));
//        }

        if($this->taskTypeConceptDevelopmentRepository->update($task_id, $param)){
            $user = auth()->user();
            // insert into campaign note for correspondence
            $this->add_correspondence('concept_development', $param, $concept_development, $user);

            if($request->file('p_attachment')){
                foreach ($request->file('p_attachment') as $file) {
                    $project_type_task_attachments = new ProjectTypeTaskAttachments();

//                    $fileName = $file->storeAs('campaigns/'.$concept_development->id.'/'.$asset_id, $file_name);
                    $fileName = $this->file_exist_check($file, $concept_development->id, $task_id);

                    $project_type_task_attachments['project_id'] = $concept_development->id;
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
                    $this->add_file_correspondence_for_task($concept_development, $user, $fileName, 'concept_development');
                }
            }

            return redirect('admin/project/'.$concept_development->id.'/edit#'.$task_id)
                ->with('success', __('Concept Development ('.$task_id.') - Update Success'));
        }

        return redirect('admin/project/'.$concept_development->id.'/edit#'.$task_id)
            ->with('error', __('Update Failed'));
    }

    public function add_mm_request(Request $request)
    {
        $param = $request->request->all();

        // add project_task_index
        $projectTaskIndex = new ProjectTaskIndex();
        $projectTaskIndex['project_id'] = $param['mm_request_p_id'];
        $projectTaskIndex['type'] = $param['mm_request_task_type'];
        $projectTaskIndex['status'] = 'action_requested';

        $user = auth()->user(); // asset_author_id
        $projectTaskIndex['author_id'] = $user->id;
        $projectTaskIndex->save();
        $task_id = $projectTaskIndex->id;

        // add task_type_mm_request
        $taskTypeMmRequest = new TaskTypeMmRequest();
        $taskTypeMmRequest['id'] = $param['mm_request_p_id']; //project_id
        $taskTypeMmRequest['author_id'] = $param['mm_request_author_id'];
        $taskTypeMmRequest['type'] = $param['mm_request_task_type'];
        $taskTypeMmRequest['created_at'] = Carbon::now();
        $taskTypeMmRequest['task_id'] = $task_id;
        $taskTypeMmRequest->save();

        // new correspondence when adding asset
        $this->correspondence_add_new_task($projectTaskIndex['project_id'], 'MM Request', $projectTaskIndex);

        // Correspondence for Legal Request Type
        $this->correspondence_new_mm_reqeust($projectTaskIndex['project_id'], 'MM Request', $projectTaskIndex);

        // TODO notification
        // Send notification to copywriter(brand check) via email
        // Do action - copy request
//        if($projectTaskIndex['status'] == 'copy_requested') {
//            $notify = new NotifyController();
//            $notify->copy_request($request['mm_request_c_id'], $task_id);
//        } else if($projectTaskIndex['status'] == 'copy_complete') {
//            $notify = new NotifyController();
//            $notify->copy_complete($request['mm_request_c_id'], $task_id);
//        }
        ///////////////////////////////////////////////////////////////

        $previous = basename(url()->previous());
        if($previous == 'edit_general'){
            return redirect('admin/project/'.$request['mm_request_p_id'].'/edit_general#'.$task_id)
                ->with('success', __('Added the MM Request : ' . $task_id));
        }else{
            return redirect('admin/mm_request/'.$request['mm_request_p_id'].'/edit#asset_selector')
                ->with('success', __('Added the MM Request : ' . $task_id));
//            return redirect('admin/project/'.$request['mm_request_p_id'].'/edit#'.$task_id)
//                ->with('success', __('Added the MM Request Task : ' . $task_id));
        }

    }

    public function correspondence_new_mm_reqeust($p_id, $task_name, $projectTaskIndex)
    {

        $user = auth()->user();
        $task_name = strtoupper($task_name);
        $change_line = "<p><b style='color: #b91d19;'>$task_name</b> <b>(#$projectTaskIndex->id)</b> has been created by $user->first_name. </p>";

        $legal_request_note = new MmRequestNotes();
        $legal_request_note['id'] = $projectTaskIndex->id;
        $legal_request_note['user_id'] = $user->id;
        $legal_request_note['mm_request_type_id'] = 0;
        $legal_request_note['task_id'] = $projectTaskIndex->id;
        $legal_request_note['project_id'] = $p_id;
        $legal_request_note['note'] = $change_line;
        $legal_request_note['created_at'] = Carbon::now();
        $legal_request_note->save();
    }

    public function edit_mm_request(Request $request, $task_id)
    {
        $mm_request = $this->taskTypeMmRequestRepository->findById($task_id);

        $param = $request->request->all();

        if (isset($param['set_up_plant'])) {
            $param['set_up_plant'] = implode(', ', $param['set_up_plant']);
        } else {
            $param['set_up_plant'] = '';
        }

        // Permission_check
//        if(!$this->permission_check($param)){
//            return redirect('admin/project/' . $mm_request->id . '/edit#'.$task_id)
//                ->with('error', __('This action is no longer permitted. Please contact an Administrator.'));
//        }

        if($this->taskTypeMmRequestRepository->update($task_id, $param)){
            $user = auth()->user();
            // insert into campaign note for correspondence
            $this->add_correspondence('mm_request', $param, $mm_request, $user);

            if($request->file('p_attachment')){
                foreach ($request->file('p_attachment') as $file) {
                    $project_type_task_attachments = new ProjectTypeTaskAttachments();

//                    $fileName = $file->storeAs('campaigns/'.$mm_request->id.'/'.$asset_id, $file_name);
                    $fileName = $this->file_exist_check($file, $mm_request->id, $task_id);

                    $project_type_task_attachments['project_id'] = $mm_request->id;
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
                    $this->add_file_correspondence_for_task($mm_request, $user, $fileName, 'mm_request');
                }
            }

            return redirect('admin/project/'.$mm_request->id.'/edit#'.$task_id)
                ->with('success', __('MM Request ('.$task_id.') - Update Success'));
        }

        return redirect('admin/project/'.$mm_request->id.'/edit#'.$task_id)
            ->with('error', __('Update Failed'));
    }

    public function add_legal_request(TaskLegalRequestRequest $request)
    {

        $param = $request->request->all();

        // add project_task_index
        $projectTaskIndex = new ProjectTaskIndex();
        $projectTaskIndex['project_id'] = $param['legal_request_p_id'];
        $projectTaskIndex['type'] = $param['legal_request_task_type'];
        $projectTaskIndex['status'] = 'action_requested';


        $user = auth()->user(); // asset_author_id
        $projectTaskIndex['author_id'] = $user->id;
        $projectTaskIndex->save();
        $task_id = $projectTaskIndex->id;

        // add task_type_legal_request
        $taskTypeLegalRequest = new TaskTypeLegalRequest();
        $taskTypeLegalRequest['id'] = $param['legal_request_p_id']; //project_id
        $taskTypeLegalRequest['author_id'] = $param['legal_request_author_id'];
        $taskTypeLegalRequest['type'] = $param['legal_request_task_type'];
        $taskTypeLegalRequest['created_at'] = Carbon::now();
        $taskTypeLegalRequest['task_id'] = $task_id;
        $taskTypeLegalRequest->save();

        // new correspondence when adding asset
        $this->correspondence_add_new_task($projectTaskIndex['project_id'], 'Legal Request', $projectTaskIndex);

        // Correspondence for Legal Request Type
        $this->correspondence_new_legal_reqeust($projectTaskIndex['project_id'], 'Legal Request', $projectTaskIndex);

        // TODO notification
        // Send notification to copywriter(brand check) via email
        // Do action - copy request
//        if($projectTaskIndex['status'] == 'copy_requested') {
//            $notify = new NotifyController();
//            $notify->copy_request($request['legal_request_c_id'], $task_id);
//        } else if($projectTaskIndex['status'] == 'copy_complete') {
//            $notify = new NotifyController();
//            $notify->copy_complete($request['legal_request_c_id'], $task_id);
//        }
        ///////////////////////////////////////////////////////////////

        $previous = basename(url()->previous());
        if($previous == 'edit_general'){
            return redirect('admin/project/'.$request['legal_request_p_id'].'/edit_general#'.$task_id)
                ->with('success', __('Added the Legal Request : ' . $task_id));
        }else{
            return redirect('admin/legal_request/'.$request['legal_request_p_id'].'/edit#asset_selector')
                ->with('success', __('Added the Legal Request : ' . $task_id));
//            return redirect('admin/project/'.$request['legal_request_p_id'].'/edit#'.$task_id)
//                ->with('success', __('Added the Legal Request Task : ' . $task_id));
        }

    }

    public function edit_legal_request(Request $request, $task_id)
    {
        $legal_request = $this->taskTypeLegalRequestRepository->findById($task_id);

        $param = $request->request->all();

        // Permission_check
//        if(!$this->permission_check($param)){
//            return redirect('admin/project/' . $concept_development->id . '/edit#'.$task_id)
//                ->with('error', __('This action is no longer permitted. Please contact an Administrator.'));
//        }

        if($this->taskTypeLegalRequestRepository->update($task_id, $param)){
            $user = auth()->user();
            // insert into campaign note for correspondence
            $this->add_correspondence('legal_request', $param, $legal_request, $user);

            if($request->file('p_attachment')){
                foreach ($request->file('p_attachment') as $file) {
                    $project_type_task_attachments = new ProjectTypeTaskAttachments();

//                    $fileName = $file->storeAs('campaigns/'.$concept_development->id.'/'.$asset_id, $file_name);
                    $fileName = $this->file_exist_check($file, $legal_request->id, $task_id);

                    $project_type_task_attachments['project_id'] = $legal_request->id;
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
                    $this->add_file_correspondence_for_task($legal_request, $user, $fileName, 'legal_request');
                }
            }

            return redirect('admin/project/'.$legal_request->id.'/edit#'.$task_id)
                ->with('success', __('Legal Request ('.$task_id.') - Update Success'));
        }

        return redirect('admin/project/'.$legal_request->id.'/edit#'.$task_id)
            ->with('error', __('Update Failed'));
    }

    public function correspondence_new_legal_reqeust($p_id, $task_name, $projectTaskIndex)
    {

        $user = auth()->user();
        $task_name = strtoupper($task_name);
        $change_line = "<p><b style='color: #b91d19;'>$task_name</b> <b>(#$projectTaskIndex->id)</b> has been created by $user->first_name. </p>";

        $legal_request_note = new LegalRequestNotes();
        $legal_request_note['id'] = $projectTaskIndex->id;
        $legal_request_note['user_id'] = $user->id;
        $legal_request_note['legal_request_type_id'] = 0;
        $legal_request_note['task_id'] = $projectTaskIndex->id;
        $legal_request_note['project_id'] = $p_id;
        $legal_request_note['note'] = $change_line;
        $legal_request_note['created_at'] = Carbon::now();
        $legal_request_note->save();
    }

    public function add_product_information(Request $request)
    {
        $param = $request->request->all();
        // add Project_Task_index
        $projectTaskIndex = new ProjectTaskIndex();
        $projectTaskIndex['project_id'] = $param['product_information_p_id'];
        $projectTaskIndex['type'] = $param['product_information_task_type'];
        $projectTaskIndex['status'] = 'in_progress';

        $user = auth()->user(); // asset_author_id
        $projectTaskIndex['author_id'] = $user->id;
        $projectTaskIndex->save();
        $task_id = $projectTaskIndex->id;

        // add campaign_type_product_information
        $taskTypeProductInformation = new TaskTypeProductInformation();
        $taskTypeProductInformation['id'] = $param['product_information_p_id']; //project_id
        $taskTypeProductInformation['author_id'] = $param['product_information_author_id'];
        $taskTypeProductInformation['type'] = $param['product_information_task_type'];

        $taskTypeProductInformation['product_name'] = $param['product_information_product_name'];
        $taskTypeProductInformation['product_line'] = $param['product_information_product_line'];
        $taskTypeProductInformation['total_sku_count'] = $param['product_information_total_sku_count'];
        $taskTypeProductInformation['category'] = $param['product_information_category'];
        $taskTypeProductInformation['segment'] = $param['product_information_segment'];
        $taskTypeProductInformation['product_dimension'] = $param['product_information_product_dimension'];
        $taskTypeProductInformation['claim_weight'] = $param['product_information_claim_weight'];
        $taskTypeProductInformation['weight_unit'] = $param['product_information_weight_unit'];
        $taskTypeProductInformation['components'] = $param['product_information_components'];
        $taskTypeProductInformation['what_it_is'] = $param['product_information_what_it_is'];
        $taskTypeProductInformation['features'] = $param['product_information_features'];
        $taskTypeProductInformation['marketing_claim'] = $param['product_information_marketing_claim'];
        $taskTypeProductInformation['applications'] = $param['product_information_applications'];

        if (isset($param['product_information_sustainability'])) {
            $taskTypeProductInformation['sustainability'] = implode(', ', $param['product_information_sustainability']);
        } else {
            $taskTypeProductInformation['sustainability'] = '';
        }

        $taskTypeProductInformation['if_others'] = $param['product_information_if_others'];

        $taskTypeProductInformation['distribution'] = $param['product_information_distribution'];
        if (isset($param['product_information_distribution'])) {
            $taskTypeProductInformation['distribution'] = implode(', ', $param['product_information_distribution']);
        } else {
            $taskTypeProductInformation['distribution'] = '';
        }

        $taskTypeProductInformation['if_others_distribution'] = $param['product_information_if_others_distribution'];

        $taskTypeProductInformation['created_at'] = Carbon::now();
        $taskTypeProductInformation['task_id'] = $task_id;
        $taskTypeProductInformation->save();

        // new correspondence when adding asset
        $this->correspondence_add_new_task($projectTaskIndex['project_id'], 'Product Information', $projectTaskIndex);

        // add campaign_type_asset_attachments
        if($request->file('product_information_p_attachment')){
            foreach ($request->file('product_information_p_attachment') as $file) {
                $project_type_task_attachments = new ProjectTypeTaskAttachments();

//                $fileName = $file->storeAs('campaigns/'.$param['product_information_c_id'].'/'.$asset_id, $originalName);
                $fileName = $this->file_exist_check($file, $param['product_information_p_id'], $task_id);

                $project_type_task_attachments['project_id'] = $param['product_information_p_id'];
                $project_type_task_attachments['task_id'] = $task_id;
                $project_type_task_attachments['type'] = 'attachment_file_' . $file->getMimeType();
                $project_type_task_attachments['author_id'] = $param['product_information_author_id'];
                $project_type_task_attachments['attachment'] = '/' . $fileName;
                $project_type_task_attachments['file_ext'] = pathinfo($fileName, PATHINFO_EXTENSION);
                $project_type_task_attachments['file_type'] = $file->getMimeType();
                $project_type_task_attachments['file_size'] = $file->getSize();
                $project_type_task_attachments['created_at'] = Carbon::now();
                $project_type_task_attachments->save();
            }
        }

        // TODO notification
        // Send notification to copywriter(brand check) via email
        // Do action - copy request
//        if($projectTaskIndex['status'] == 'copy_requested') {
//            $notify = new NotifyController();
//            $notify->copy_request($param['product_information_c_id'], $task_id);
//        } else if($projectTaskIndex['status'] == 'copy_complete') {
//            $notify = new NotifyController();
//            $notify->copy_complete($param['product_information_c_id'], $task_id);
//        }
        ///////////////////////////////////////////////////////////////

        return redirect('admin/project/'.$param['product_information_p_id'].'/edit#'.$task_id)
            ->with('success', __('Added the Product Information Task : ' . $task_id));
    }

    public function edit_product_information(Request $request, $task_id)
    {
        $product_information = $this->taskTypeProductInformationRepository->findById($task_id);

        $param = $request->request->all();

        // Permission_check
//        if(!$this->permission_check($param)){
//            return redirect('admin/project/' . $product_information->id . '/edit#'.$task_id)
//                ->with('error', __('This action is no longer permitted. Please contact an Administrator.'));
//        }

        if (isset($param['sustainability'])) {
            $param['sustainability'] = implode(', ', $param['sustainability']);
        } else {
            $param['sustainability'] = '';
        }

        if (isset($param['distribution'])) {
            $param['distribution'] = implode(', ', $param['distribution']);
        } else {
            $param['distribution'] = '';
        }

        if($this->taskTypeProductInformationRepository->update($task_id, $param)){
            $user = auth()->user();
            // insert into project note for correspondence
            $this->add_correspondence('product_information', $param, $product_information, $user);

            if($request->file('p_attachment')){
                foreach ($request->file('p_attachment') as $file) {
                    $project_type_task_attachments = new ProjectTypeTaskAttachments();

//                    $fileName = $file->storeAs('campaigns/'.$product_information->id.'/'.$asset_id, $file_name);
                    $fileName = $this->file_exist_check($file, $product_information->id, $task_id);

                    $project_type_task_attachments['project_id'] = $product_information->id;
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
                    $this->add_file_correspondence_for_task($product_information, $user, $fileName, 'product_information');
                }
            }

            return redirect('admin/project/'.$product_information->id.'/edit#'.$task_id)
                ->with('success', __('Product Information ('.$task_id.') - Update Success'));
        }

        return redirect('admin/project/'.$product_information->id.'/edit#'.$task_id)
            ->with('error', __('Update Failed'));
    }

    public function add_product_brief(Request $request)
    {
        $param = $request->request->all();
        // add Campaign_asset_index
        $projectTaskIndex = new ProjectTaskIndex();
        $projectTaskIndex['project_id'] = $param['product_brief_p_id'];
        $projectTaskIndex['type'] = $param['product_brief_task_type'];
        $projectTaskIndex['status'] = 'in_progress';

        $user = auth()->user(); // asset_author_id
        $projectTaskIndex['author_id'] = $user->id;
        $projectTaskIndex->save();
        $task_id = $projectTaskIndex->id;

        // add campaign_type_product_brief
        $taskTypeProductBrief = new TaskTypeProductBrief();
        $taskTypeProductBrief['id'] = $param['product_brief_p_id']; //project_id
        $taskTypeProductBrief['author_id'] = $param['product_brief_author_id'];
        $taskTypeProductBrief['type'] = $param['product_brief_task_type'];

        $taskTypeProductBrief['product_name'] = $param['product_brief_product_name'];
        $taskTypeProductBrief['material_number'] = $param['product_brief_material_number'];
        $taskTypeProductBrief['total_sku_count'] = $param['product_brief_total_sku_count'];
        $taskTypeProductBrief['target_receiving_date'] = $param['product_brief_target_receiving_date'];
        $taskTypeProductBrief['door'] = $param['product_brief_door'];
        $taskTypeProductBrief['nsp'] = $param['product_brief_nsp'];
        $taskTypeProductBrief['srp'] = $param['product_brief_srp'];
        $taskTypeProductBrief['category'] = $param['product_brief_category'];
        $taskTypeProductBrief['sub_category'] = $param['product_brief_sub_category'];
        $taskTypeProductBrief['franchise'] = $param['product_brief_franchise'];
        $taskTypeProductBrief['shade_names'] = $param['product_brief_shade_names'];
        $taskTypeProductBrief['claim_weight'] = $param['product_brief_claim_weight'];
        $taskTypeProductBrief['testing_claims'] = $param['product_brief_testing_claims'];
        $taskTypeProductBrief['concept'] = $param['product_brief_concept'];
        $taskTypeProductBrief['key'] = $param['product_brief_key'];

        if (isset($param['product_brief_product_format'])) {
            $taskTypeProductBrief['product_format'] = implode(', ', $param['product_brief_product_format']);
        } else {
            $taskTypeProductBrief['product_format'] = '';
        }
        if (isset($param['product_brief_texture'])) {
            $taskTypeProductBrief['texture'] = implode(', ', $param['product_brief_texture']);
        } else {
            $taskTypeProductBrief['texture'] = '';
        }
        if (isset($param['product_brief_finish'])) {
            $taskTypeProductBrief['finish'] = implode(', ', $param['product_brief_finish']);
        } else {
            $taskTypeProductBrief['finish'] = '';
        }
        $taskTypeProductBrief['coverage'] = $param['product_brief_coverage'];
        $taskTypeProductBrief['must_ban'] = $param['product_brief_must_ban'];
        if (isset($param['product_brief_highlights'])) {
            $taskTypeProductBrief['highlights'] = implode(', ', $param['product_brief_highlights']);
        } else {
            $taskTypeProductBrief['highlights'] = '';
        }
        $taskTypeProductBrief['created_at'] = Carbon::now();
        $taskTypeProductBrief['task_id'] = $task_id;
        $taskTypeProductBrief->save();

        // new correspondence when adding asset
        $this->correspondence_add_new_task($projectTaskIndex['project_id'], 'Product Brief', $projectTaskIndex);

        // add campaign_type_asset_attachments
        if($request->file('product_brief_p_attachment')){
            foreach ($request->file('product_brief_p_attachment') as $file) {
                $project_type_task_attachments = new ProjectTypeTaskAttachments();

//                $fileName = $file->storeAs('campaigns/'.$request['product_brief_c_id'].'/'.$asset_id, $originalName);
                $fileName = $this->file_exist_check($file, $param['product_brief_p_id'], $task_id);

                $project_type_task_attachments['project_id'] = $param['product_brief_p_id'];
                $project_type_task_attachments['task_id'] = $task_id;
                $project_type_task_attachments['type'] = 'attachment_file_' . $file->getMimeType();
                $project_type_task_attachments['author_id'] = $param['product_brief_author_id'];
                $project_type_task_attachments['attachment'] = '/' . $fileName;
                $project_type_task_attachments['file_ext'] = pathinfo($fileName, PATHINFO_EXTENSION);
                $project_type_task_attachments['file_type'] = $file->getMimeType();
                $project_type_task_attachments['file_size'] = $file->getSize();
                $project_type_task_attachments['created_at'] = Carbon::now();
                $project_type_task_attachments->save();
            }
        }

        // TODO notification
        // Send notification to copywriter(brand check) via email
        // Do action - copy request
//        if($projectTaskIndex['status'] == 'copy_requested') {
//            $notify = new NotifyController();
//            $notify->copy_request($param['product_brief_c_id'], $task_id);
//        } else if($projectTaskIndex['status'] == 'copy_complete') {
//            $notify = new NotifyController();
//            $notify->copy_complete($param['product_brief_c_id'], $task_id);
//        }
        ///////////////////////////////////////////////////////////////

        return redirect('admin/project/'.$param['product_brief_p_id'].'/edit#'.$task_id)
            ->with('success', __('Added the Product Brief Task : ' . $task_id));
    }

    public function edit_product_brief(Request $request, $task_id)
    {
        $product_brief = $this->taskTypeProductBriefRepository->findById($task_id);

        $param = $request->request->all();

        // Permission_check
//        if(!$this->permission_check($param)){
//            return redirect('admin/project/' . $product_brief->id . '/edit#'.$task_id)
//                ->with('error', __('This action is no longer permitted. Please contact an Administrator.'));
//        }

        if (isset($param['product_format'])) {
            $param['product_format'] = implode(', ', $param['product_format']);
        } else {
            $param['product_format'] = '';
        }
        if (isset($param['texture'])) {
            $param['texture'] = implode(', ', $param['texture']);
        } else {
            $param['texture'] = '';
        }
        if (isset($param['finish'])) {
            $param['finish'] = implode(', ', $param['finish']);
        } else {
            $param['finish'] = '';
        }
        if (isset($param['highlights'])) {
            $param['highlights'] = implode(', ', $param['highlights']);
        } else {
            $param['highlights'] = '';
        }

        if($this->taskTypeProductBriefRepository->update($task_id, $param)){
            $user = auth()->user();
            // insert into project note for correspondence
            $this->add_correspondence('product_brief', $param, $product_brief, $user);

            if($request->file('p_attachment')){
                foreach ($request->file('p_attachment') as $file) {
                    $project_type_task_attachments = new ProjectTypeTaskAttachments();

//                    $fileName = $file->storeAs('campaigns/'.$product_brief->id.'/'.$asset_id, $file_name);
                    $fileName = $this->file_exist_check($file, $product_brief->id, $task_id);

                    $project_type_task_attachments['project_id'] = $product_brief->id;
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
                    $this->add_file_correspondence_for_task($product_brief, $user, $fileName, 'product_brief');
                }
            }

            return redirect('admin/project/'.$product_brief->id.'/edit#'.$task_id)
                ->with('success', __('Product Brief ('.$task_id.') - Update Success'));
        }

        return redirect('admin/project/'.$product_brief->id.'/edit#'.$task_id)
            ->with('error', __('Update Failed'));
    }

    public function add_qra_request(Request $request)
    {
        $param = $request->request->all();

        // add project_task_index
        $projectTaskIndex = new ProjectTaskIndex();
        $projectTaskIndex['project_id'] = $param['qra_request_p_id'];
        $projectTaskIndex['type'] = $param['qra_request_task_type'];
        $projectTaskIndex['status'] = 'action_requested';

        $user = auth()->user(); // asset_author_id
        $projectTaskIndex['author_id'] = $user->id;
        $projectTaskIndex->save();
        $task_id = $projectTaskIndex->id;

        // add campaign_type_legal_request
        $taskTypeLegalRequest = new TaskTypeQraRequest();
        $taskTypeLegalRequest['id'] = $param['qra_request_p_id']; //project_id
        $taskTypeLegalRequest['author_id'] = $param['qra_request_author_id'];
        $taskTypeLegalRequest['type'] = $param['qra_request_task_type'];
        $taskTypeLegalRequest['created_at'] = Carbon::now();
        $taskTypeLegalRequest['task_id'] = $task_id;
        $taskTypeLegalRequest->save();

        // new correspondence when adding Task
        $this->correspondence_add_new_task($projectTaskIndex['project_id'], 'RA Request', $projectTaskIndex);

        // Correspondence for QRA Request Type
        $this->correspondence_new_qra_reqeust($projectTaskIndex['project_id'], 'RA Request', $projectTaskIndex);

        // TODO notification
        // Send notification to copywriter(brand check) via email
        // Do action - copy request
//        if($projectTaskIndex['status'] == 'copy_requested') {
//            $notify = new NotifyController();
//            $notify->copy_request($request['legal_request_c_id'], $task_id);
//        } else if($projectTaskIndex['status'] == 'copy_complete') {
//            $notify = new NotifyController();
//            $notify->copy_complete($request['legal_request_c_id'], $task_id);
//        }
        ///////////////////////////////////////////////////////////////

        //// here
        $previous = basename(url()->previous());
        if($previous == 'edit_general'){
            return redirect('admin/project/'.$request['qra_request_p_id'].'/edit_general#'.$task_id)
                ->with('success', __('Added the RA Request Task : ' . $task_id));
        }else{
            return redirect('admin/project/'.$request['qra_request_p_id'].'/edit#'.$task_id)
                ->with('success', __('Added the RA Request Task : ' . $task_id));
        }

    }

    public function add_ra_request(Request $request)
    {
        $param = $request->request->all();

        $projectTaskIndex = new ProjectTaskIndex();
        $projectTaskIndex['project_id'] = $param['ra_request_p_id'];
        $projectTaskIndex['type'] = $param['ra_request_task_type'];
        $projectTaskIndex['status'] = 'action_requested';

        $user = auth()->user();
        $projectTaskIndex['author_id'] = $user->id;
        $projectTaskIndex->save();
        $task_id = $projectTaskIndex->id;

        $taskTypeRaRequest =  new TaskTypeRaRequest();
        $taskTypeRaRequest['id'] = $param['ra_request_p_id']; //project_id
        $taskTypeRaRequest['author_id'] = $param['ra_request_author_id'];
        $taskTypeRaRequest['type'] = $param['ra_request_task_type'];
        $taskTypeRaRequest['created_at'] = Carbon::now();
        $taskTypeRaRequest['task_id'] = $task_id;
        $taskTypeRaRequest->save();

        // new correspondence when adding Task
        $this->correspondence_add_new_task($projectTaskIndex['project_id'], 'RA Request', $projectTaskIndex);

        // Correspondence for RA Request Type
        $this->correspondence_new_ra_reqeust($projectTaskIndex['project_id'], 'RA Request', $projectTaskIndex);

        // TODO notification
        // Send notification to copywriter(brand check) via email
        // Do action - copy request
//        if($projectTaskIndex['status'] == 'copy_requested') {
//            $notify = new NotifyController();
//            $notify->copy_request($request['legal_request_c_id'], $task_id);
//        } else if($projectTaskIndex['status'] == 'copy_complete') {
//            $notify = new NotifyController();
//            $notify->copy_complete($request['legal_request_c_id'], $task_id);
//        }
        ///////////////////////////////////////////////////////////////

        //// here
        $previous = basename(url()->previous());
        if($previous == 'edit_general'){
            return redirect('admin/project/'.$request['ra_request_p_id'].'/edit_general#'.$task_id)
                ->with('success', __('Added the RA Request : ' . $task_id));
        }else{
            return redirect('admin/ra_request/'.$request['ra_request_p_id'].'/edit#asset_selector')
                ->with('success', __('Added the RA Request : ' . $task_id));
//            return redirect('admin/project/'.$request['ra_request_p_id'].'/edit#'.$task_id)
//                ->with('success', __('Added the RA Request Task : ' . $task_id));
        }

    }

    public function correspondence_new_qra_reqeust($p_id, $task_name, $projectTaskIndex)
    {
        $user = auth()->user();

        $change_line = "<p><b style='color: #b91d19;'>$task_name</b> <b>(#$projectTaskIndex->id)</b> has been created by $user->first_name. </p>";

        $qra_request_note = new QraRequestNotes();
        $qra_request_note['id'] = $projectTaskIndex->id;
        $qra_request_note['user_id'] = $user->id;
        $qra_request_note['qra_request_type_id'] = 0;
        $qra_request_note['task_id'] = $projectTaskIndex->id;
        $qra_request_note['project_id'] = $p_id;
        $qra_request_note['note'] = $change_line;
        $qra_request_note['created_at'] = Carbon::now();
        $qra_request_note->save();
    }

    public function correspondence_new_ra_reqeust($p_id, $task_name, $projectTaskIndex)
    {
        $user = auth()->user();

        $change_line = "<p><b style='color: #b91d19;'>$task_name</b> <b>(#$projectTaskIndex->id)</b> has been created by $user->first_name. </p>";

        $qra_request_note = new RaRequestNotes();
        $qra_request_note['id'] = $projectTaskIndex->id;
        $qra_request_note['user_id'] = $user->id;
        $qra_request_note['ra_request_type_id'] = 0;
        $qra_request_note['task_id'] = $projectTaskIndex->id;
        $qra_request_note['project_id'] = $p_id;
        $qra_request_note['note'] = $change_line;
        $qra_request_note['created_at'] = Carbon::now();
        $qra_request_note->save();
    }

    public function add_npd_po_request(Request $request)
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

        // add task_type_mm_request
        $taskTypeMmRequest = new TaskTypeMmRequest();
        $taskTypeMmRequest['id'] = $param['mm_request_p_id']; //project_id
        $taskTypeMmRequest['author_id'] = $param['mm_request_author_id'];
        $taskTypeMmRequest['type'] = $param['mm_request_task_type'];

        $taskTypeMmRequest['materials'] = $param['mm_request_materials'];
        $taskTypeMmRequest['priority'] = $param['mm_request_priority'];
        $taskTypeMmRequest['due_date'] = $param['mm_request_due_date'];
        $taskTypeMmRequest['request_type'] = $param['mm_request_request_type'];

        if (isset($param['mm_request_set_up_plant'])) {
            $taskTypeMmRequest['set_up_plant'] = implode(', ', $param['mm_request_set_up_plant']);
        } else {
            $taskTypeMmRequest['set_up_plant'] = '';
        }
        $taskTypeMmRequest['remark'] = $param['mm_request_remark'];

        $taskTypeMmRequest['created_at'] = Carbon::now();
        $taskTypeMmRequest['task_id'] = $task_id;
        $taskTypeMmRequest->save();

        // new correspondence when adding asset
        $this->correspondence_add_new_task($projectTaskIndex['project_id'], 'MM Request', $projectTaskIndex);

        // add campaign_type_asset_attachments
        if($request->file('mm_request_p_attachment')){
            foreach ($request->file('mm_request_p_attachment') as $file) {
                $project_type_task_attachments = new ProjectTypeTaskAttachments();

//                $fileName = $file->storeAs('campaigns/'.$request['mm_request_c_id'].'/'.$asset_id, $originalName);
                $fileName = $this->file_exist_check($file, $param['mm_request_p_id'], $task_id);

                $project_type_task_attachments['project_id'] = $param['mm_request_p_id'];
                $project_type_task_attachments['task_id'] = $task_id;
                $project_type_task_attachments['type'] = 'attachment_file_' . $file->getMimeType();
                $project_type_task_attachments['author_id'] = $param['mm_request_author_id'];
                $project_type_task_attachments['attachment'] = '/' . $fileName;
                $project_type_task_attachments['file_ext'] = pathinfo($fileName, PATHINFO_EXTENSION);
                $project_type_task_attachments['file_type'] = $file->getMimeType();
                $project_type_task_attachments['file_size'] = $file->getSize();
                $project_type_task_attachments['created_at'] = Carbon::now();
                $project_type_task_attachments->save();
            }
        }

        // TODO notification
        // Send notification to copywriter(brand check) via email
        // Do action - copy request
//        if($projectTaskIndex['status'] == 'copy_requested') {
//            $notify = new NotifyController();
//            $notify->copy_request($request['mm_request_c_id'], $task_id);
//        } else if($projectTaskIndex['status'] == 'copy_complete') {
//            $notify = new NotifyController();
//            $notify->copy_complete($request['mm_request_c_id'], $task_id);
//        }
        ///////////////////////////////////////////////////////////////

        return redirect('admin/project/'.$param['mm_request_p_id'].'/edit#'.$task_id)
            ->with('success', __('Added the MM Request Task : ' . $task_id));
    }

    public function edit_npd_po_request(Request $request, $task_id)
    {
        $mm_request = $this->taskTypeNpdPoRequestRepository->findById($task_id);

        $param = $request->request->all();

        if (isset($param['set_up_plant'])) {
            $param['set_up_plant'] = implode(', ', $param['set_up_plant']);
        } else {
            $param['set_up_plant'] = '';
        }

        // Permission_check
//        if(!$this->permission_check($param)){
//            return redirect('admin/project/' . $mm_request->id . '/edit#'.$task_id)
//                ->with('error', __('This action is no longer permitted. Please contact an Administrator.'));
//        }

        if($this->taskTypeNpdPoRequestRepository->update($task_id, $param)){
            $user = auth()->user();
            // insert into campaign note for correspondence
            $this->add_correspondence('mm_request', $param, $mm_request, $user);

            if($request->file('p_attachment')){
                foreach ($request->file('p_attachment') as $file) {
                    $project_type_task_attachments = new ProjectTypeTaskAttachments();

//                    $fileName = $file->storeAs('campaigns/'.$mm_request->id.'/'.$asset_id, $file_name);
                    $fileName = $this->file_exist_check($file, $mm_request->id, $task_id);

                    $project_type_task_attachments['project_id'] = $mm_request->id;
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
                    $this->add_file_correspondence_for_task($mm_request, $user, $fileName, 'mm_request');
                }
            }

            return redirect('admin/project/'.$mm_request->id.'/edit#'.$task_id)
                ->with('success', __('NPD PO Request ('.$task_id.') - Update Success'));
        }

        return redirect('admin/project/'.$mm_request->id.'/edit#'.$task_id)
            ->with('error', __('Update Failed'));
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

        $taskTypeQcRequest['ship_date'] = $param['qc_request_ship_date'];
        $taskTypeQcRequest['qc_date'] = $param['qc_request_qc_date'];
        $taskTypeQcRequest['po'] = $param['qc_request_po'];
        $taskTypeQcRequest['po_usd'] = $param['qc_request_po_usd'];
        $taskTypeQcRequest['materials'] = $param['qc_request_materials'];
        $taskTypeQcRequest['item_type'] = $param['qc_request_item_type'];
        $taskTypeQcRequest['vendor_code'] = $param['qc_request_vendor_code'];
        $taskTypeQcRequest['country'] = $param['qc_request_country'];
        $taskTypeQcRequest['vendor_primary_contact_name'] = $param['qc_request_vendor_primary_contact_name'];
        $taskTypeQcRequest['vendor_primary_contact_email'] = $param['qc_request_vendor_primary_contact_email'];
        $taskTypeQcRequest['vendor_primary_contact_phone'] = $param['qc_request_vendor_primary_contact_phone'];
        $taskTypeQcRequest['facility_address'] = $param['qc_request_facility_address'];
        $taskTypeQcRequest['performed_by'] = $param['qc_request_performed_by'];

        $taskTypeQcRequest->save();

        // new correspondence when adding asset
        $this->correspondence_add_new_task($projectTaskIndex['project_id'], 'QA Request', $projectTaskIndex);

        // Correspondence for Onsite QC Request Type
        $this->correspondence_new_qc_request($projectTaskIndex['project_id'], 'QA Request', $projectTaskIndex);

        //// here
        $previous = basename(url()->previous());
        if($previous == 'edit_general'){
            return redirect('admin/project/'.$request['qc_request_p_id'].'/edit_general#'.$task_id)
                ->with('success', __('Added the QA Request Task : ' . $task_id));
        }else{
            return redirect('admin/project/'.$request['qc_request_p_id'].'/edit#'.$task_id)
                ->with('success', __('Added the QA Request Task : ' . $task_id));
        }


//        // add campaign_type_asset_attachments
//        if($request->file('qc_request_p_attachment')){
//            foreach ($request->file('qc_request_p_attachment') as $file) {
//                $project_type_task_attachments = new ProjectTypeTaskAttachments();
//
////                $fileName = $file->storeAs('campaigns/'.$request['qc_request_c_id'].'/'.$asset_id, $originalName);
//                $fileName = $this->file_exist_check($file, $param['qc_request_p_id'], $task_id);
//
//                $project_type_task_attachments['project_id'] = $param['qc_request_p_id'];
//                $project_type_task_attachments['task_id'] = $task_id;
//                $project_type_task_attachments['type'] = 'attachment_file_' . $file->getMimeType();
//                $project_type_task_attachments['author_id'] = $param['qc_request_author_id'];
//                $project_type_task_attachments['attachment'] = '/' . $fileName;
//                $project_type_task_attachments['file_ext'] = pathinfo($fileName, PATHINFO_EXTENSION);
//                $project_type_task_attachments['file_type'] = $file->getMimeType();
//                $project_type_task_attachments['file_size'] = $file->getSize();
//                $project_type_task_attachments['created_at'] = Carbon::now();
//                $project_type_task_attachments->save();
//            }
//        }

        ///////////////////////////////////////////////////////////////
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

            return redirect('admin/project/'.$qc_request->id.'/edit#'.$task_id)
                ->with('success', __('QA Request ('.$task_id.') - Update Success'));
        }

        return redirect('admin/project/'.$qc_request->id.'/edit#'.$task_id)
            ->with('error', __('Update Failed'));
    }

    public function correspondence_new_qc_request($p_id, $task_name, $projectTaskIndex)
    {
        $user = auth()->user();

        $change_line = "<p><b style='color: #b91d19;'>$task_name</b> <b>(#$projectTaskIndex->id)</b> has been created by $user->first_name. </p>";

        $qc_request_note = new QcRequestNotes();
        $qc_request_note['id'] = $projectTaskIndex->id;
        $qc_request_note['user_id'] = $user->id;
        $qc_request_note['qc_request_type_id'] = 0;
        $qc_request_note['task_id'] = $projectTaskIndex->id;
        $qc_request_note['project_id'] = $p_id;
        $qc_request_note['note'] = $change_line;
        $qc_request_note['created_at'] = Carbon::now();
        $qc_request_note->save();
    }


    public function add_product_receiving(Request $request)
    {

        $param = $request->request->all();

        // add project_task_index
        $projectTaskIndex = new ProjectTaskIndex();
        $projectTaskIndex['project_id'] = $param['product_receiving_p_id'];
        $projectTaskIndex['type'] = $param['product_receiving_task_type'];
        $projectTaskIndex['status'] = 'action_requested';

        $user = auth()->user(); // asset_author_id
        $projectTaskIndex['author_id'] = $user->id;
        $projectTaskIndex->save();
        $task_id = $projectTaskIndex->id;

        // add task_type_product_receiving
        $taskTypeProductReceiving = new TaskTypeProductReceiving();
        $taskTypeProductReceiving['id'] = $param['product_receiving_p_id']; //project_id
        $taskTypeProductReceiving['author_id'] = $param['product_receiving_author_id'];
        $taskTypeProductReceiving['type'] = $param['product_receiving_task_type'];

        $taskTypeProductReceiving['po'] = $param['product_receiving_po'];
        $taskTypeProductReceiving['materials'] = $param['product_receiving_materials'];
        $taskTypeProductReceiving['qir_status'] = $param['product_receiving_qir_status'];
        $taskTypeProductReceiving['division'] = $param['product_receiving_division'];
        $taskTypeProductReceiving['qir_action'] = $param['product_receiving_qir_action'];
        $taskTypeProductReceiving['vendor_code'] = $param['product_receiving_vendor_code'];
        $taskTypeProductReceiving['vendor_name'] = $param['product_receiving_vendor_name'];
        $taskTypeProductReceiving['cost_center'] = $param['product_receiving_cost_center'];
        $taskTypeProductReceiving['location'] = $param['product_receiving_location'];
        $taskTypeProductReceiving['primary_contact'] = $param['product_receiving_primary_contact'];
        $taskTypeProductReceiving['related_team_contact'] = $param['product_receiving_related_team_contact'];
        $taskTypeProductReceiving['year'] = $param['product_receiving_year'];
        $taskTypeProductReceiving['received_qty'] = $param['product_receiving_received_qty'];
        $taskTypeProductReceiving['inspection_qty'] = $param['product_receiving_inspection_qty'];
        $taskTypeProductReceiving['defect_qty'] = $param['product_receiving_defect_qty'];
        $taskTypeProductReceiving['blocked_qty'] = $param['product_receiving_blocked_qty'];
        $taskTypeProductReceiving['blocked_rate'] = $param['product_receiving_blocked_rate'];
        $taskTypeProductReceiving['batch'] = $param['product_receiving_batch'];
        $taskTypeProductReceiving['item_net_cost'] = $param['product_receiving_item_net_cost'];
        if (isset($param['product_receiving_defect_area'])) {
            $taskTypeProductReceiving['defect_area'] = implode(', ', $param['product_receiving_defect_area']);
        } else {
            $taskTypeProductReceiving['defect_area'] = '';
        }
        if (isset($param['product_receiving_defect_type'])) {
            $taskTypeProductReceiving['defect_type'] = implode(', ', $param['product_receiving_defect_type']);
        } else {
            $taskTypeProductReceiving['defect_type'] = '';
        }
        $taskTypeProductReceiving['defect_details'] = $param['product_receiving_defect_details'];
        $taskTypeProductReceiving['defect_cost'] = $param['product_receiving_defect_cost'];
        $taskTypeProductReceiving['full_cost'] = $param['product_receiving_full_cost'];
        $taskTypeProductReceiving['rework_cost'] = $param['product_receiving_rework_cost'];
        $taskTypeProductReceiving['rsr_id'] = $param['product_receiving_rsr_id'];
        $taskTypeProductReceiving['special_inspection_cost'] = $param['product_receiving_special_inspection_cost'];
        $taskTypeProductReceiving['processing_date'] = $param['product_receiving_processing_date'];
        $taskTypeProductReceiving['aging_days'] = $param['product_receiving_aging_days'];
        $taskTypeProductReceiving['capa'] = $param['product_receiving_capa'];
        $taskTypeProductReceiving['total_claim'] = $param['product_receiving_total_claim'];
        $taskTypeProductReceiving['actual_cm_total'] = $param['product_receiving_actual_cm_total'];
        $taskTypeProductReceiving['claim_status'] = $param['product_receiving_claim_status'];
        $taskTypeProductReceiving['override_authorized_by'] = $param['product_receiving_override_authorized_by'];
        $taskTypeProductReceiving['waived_amount'] = $param['product_receiving_waived_amount'];
        $taskTypeProductReceiving['settlement_total'] = $param['product_receiving_settlement_total'];
        $taskTypeProductReceiving['settlement_type'] = $param['product_receiving_settlement_type'];
        $taskTypeProductReceiving['task_id'] = $param['product_receiving_task_id'];

        $taskTypeProductReceiving['created_at'] = Carbon::now();
        $taskTypeProductReceiving['task_id'] = $task_id;
        $taskTypeProductReceiving->save();

        // new correspondence when adding asset
        $this->correspondence_add_new_task($projectTaskIndex['project_id'], 'Product Receiving', $projectTaskIndex);

        // add campaign_type_asset_attachments
        if($request->file('product_receiving_p_attachment')){
            foreach ($request->file('product_receiving_p_attachment') as $file) {
                $project_type_task_attachments = new ProjectTypeTaskAttachments();

//                $fileName = $file->storeAs('campaigns/'.$request['product_receiving_c_id'].'/'.$asset_id, $originalName);
                $fileName = $this->file_exist_check($file, $param['product_receiving_p_id'], $task_id);

                $project_type_task_attachments['project_id'] = $param['product_receiving_p_id'];
                $project_type_task_attachments['task_id'] = $task_id;
                $project_type_task_attachments['type'] = 'attachment_file_' . $file->getMimeType();
                $project_type_task_attachments['author_id'] = $param['product_receiving_author_id'];
                $project_type_task_attachments['attachment'] = '/' . $fileName;
                $project_type_task_attachments['file_ext'] = pathinfo($fileName, PATHINFO_EXTENSION);
                $project_type_task_attachments['file_type'] = $file->getMimeType();
                $project_type_task_attachments['file_size'] = $file->getSize();
                $project_type_task_attachments['created_at'] = Carbon::now();
                $project_type_task_attachments->save();
            }
        }
        // TODO notification
        // Send notification to copywriter(brand check) via email
        // Do action - copy request
//        if($projectTaskIndex['status'] == 'copy_requested') {
//            $notify = new NotifyController();
//            $notify->copy_request($request['product_receiving_c_id'], $task_id);
//        } else if($projectTaskIndex['status'] == 'copy_complete') {
//            $notify = new NotifyController();
//            $notify->copy_complete($request['product_receiving_c_id'], $task_id);
//        }
        ///////////////////////////////////////////////////////////////
        return redirect('admin/project/'.$param['product_receiving_p_id'].'/edit#'.$task_id)
            ->with('success', __('Added the Product Receiving Task : ' . $task_id));
    }

    public function edit_product_receiving(Request $request, $task_id)
    {
        $product_receiving = $this->taskTypeQcRequestRepository->findById($task_id);
        $param = $request->request->all();

        // Permission_check
//        if(!$this->permission_check($param)){
//            return redirect('admin/project/' . $product_receiving->id . '/edit#'.$task_id)
//                ->with('error', __('This action is no longer permitted. Please contact an Administrator.'));
//        }

        if($this->taskTypeQcRequestRepository->update($task_id, $param)){
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
            return redirect('admin/project/'.$product_receiving->id.'/edit#'.$task_id)
                ->with('success', __('Product Receiving ('.$task_id.') - Update Success'));
        }
        return redirect('admin/project/'.$product_receiving->id.'/edit#'.$task_id)
            ->with('error', __('Update Failed'));
    }

    public function add_pe_request(Request $request)
    {

        $param = $request->request->all();

        // add project_task_index
        $projectTaskIndex = new ProjectTaskIndex();
        $projectTaskIndex['project_id'] = $param['pe_request_p_id'];
        $projectTaskIndex['type'] = $param['pe_request_task_type'];
        $projectTaskIndex['status'] = 'action_requested';


        $user = auth()->user(); // asset_author_id
        $projectTaskIndex['author_id'] = $user->id;
        $projectTaskIndex->save();
        $task_id = $projectTaskIndex->id;

        // add task_type_pe_request
        $taskTypeLegalRequest = new TaskTypePeRequest();
        $taskTypeLegalRequest['id'] = $param['pe_request_p_id']; //project_id
        $taskTypeLegalRequest['author_id'] = $param['pe_request_author_id'];
        $taskTypeLegalRequest['type'] = $param['pe_request_task_type'];
        $taskTypeLegalRequest['created_at'] = Carbon::now();
        $taskTypeLegalRequest['task_id'] = $task_id;
        $taskTypeLegalRequest->save();

        // new correspondence when adding asset
        $this->correspondence_add_new_task($projectTaskIndex['project_id'], 'DISPLAY & PE Request', $projectTaskIndex);

        // Correspondence for PE Request Type
        $this->correspondence_new_pe_reqeust($projectTaskIndex['project_id'], 'DISPLAY & PE Request', $projectTaskIndex);

        // TODO notificationㅗ
        // Send notification to copywriter(brand check) via email
        // Do action - copy request
//        if($projectTaskIndex['status'] == 'copy_requested') {
//            $notify = new NotifyController();
//            $notify->copy_request($request['legal_request_c_id'], $task_id);
//        } else if($projectTaskIndex['status'] == 'copy_complete') {
//            $notify = new NotifyController();
//            $notify->copy_complete($request['legal_request_c_id'], $task_id);
//        }
        ///////////////////////////////////////////////////////////////

        $previous = basename(url()->previous());
        if($previous == 'edit_general'){
            return redirect('admin/project/'.$request['pe_request_p_id'].'/edit_general#'.$task_id)
                ->with('success', __('Added the DISPLAY & PE Request : ' . $task_id));
        }else{
            return redirect('admin/pe_request/'.$request['pe_request_p_id'].'/edit#asset_selector')
                ->with('success', __('Added the DISPLAY & PE Request : ' . $task_id));
//            return redirect('admin/project/'.$request['pe_request_p_id'].'/edit#'.$task_id)
//                ->with('success', __('Added the PE Request Task : ' . $task_id));
        }

    }

    public function add_npd_design_request(Request $request)
    {

        $param = $request->request->all();

        // add project_task_index
        $projectTaskIndex = new ProjectTaskIndex();
        $projectTaskIndex['project_id'] = $param['npd_design_request_p_id'];
        $projectTaskIndex['type'] = $param['npd_design_request_task_type'];
        $projectTaskIndex['status'] = 'action_requested';


        $user = auth()->user(); // asset_author_id
        $projectTaskIndex['author_id'] = $user->id;
        $projectTaskIndex->save();
        $task_id = $projectTaskIndex->id;

        // add task_type_pe_request
        $taskTypeLegalRequest = new TaskTypeNpdDesignRequest();
        $taskTypeLegalRequest['id'] = $param['npd_design_request_p_id']; //project_id
        $taskTypeLegalRequest['author_id'] = $param['npd_design_request_author_id'];
        $taskTypeLegalRequest['type'] = $param['npd_design_request_task_type'];
        $taskTypeLegalRequest['created_at'] = Carbon::now();
        $taskTypeLegalRequest['task_id'] = $task_id;
        $taskTypeLegalRequest->save();

        // new correspondence when adding asset
        $this->correspondence_add_new_task($projectTaskIndex['project_id'], 'NPD Design Request', $projectTaskIndex);

        // Correspondence for NPD Design Request Type
        $this->correspondence_new_npd_design_reqeust($projectTaskIndex['project_id'], 'NPD Design Request', $projectTaskIndex);

        // TODO notification
        // Send notification to copywriter(brand check) via email
        // Do action - copy request
//        if($projectTaskIndex['status'] == 'copy_requested') {
//            $notify = new NotifyController();
//            $notify->copy_request($request['legal_request_c_id'], $task_id);
//        } else if($projectTaskIndex['status'] == 'copy_complete') {
//            $notify = new NotifyController();
//            $notify->copy_complete($request['legal_request_c_id'], $task_id);
//        }
        ///////////////////////////////////////////////////////////////

        $previous = basename(url()->previous());
        if($previous == 'edit_general'){
            return redirect('admin/project/'.$request['npd_design_request_p_id'].'/edit_general#'.$task_id)
                ->with('success', __('Added the NPD Design Request : ' . $task_id));
        }else{
            return redirect('admin/npd_design_request/'.$request['npd_design_request_p_id'].'/edit#asset_selector')
                ->with('success', __('Added the NPD DESIGN Request : ' . $task_id));
//            return redirect('admin/project/'.$request['npd_design_request_p_id'].'/edit#'.$task_id)
//                ->with('success', __('Added the NPD Design Request Task : ' . $task_id));
        }

    }

    public function add_npd_planner_request(Request $request)
    {

        $param = $request->request->all();

        // add project_task_index
        $projectTaskIndex = new ProjectTaskIndex();
        $projectTaskIndex['project_id'] = $param['npd_planner_request_p_id'];
        $projectTaskIndex['type'] = $param['npd_planner_request_task_type'];
        $projectTaskIndex['status'] = 'action_requested';


        $user = auth()->user(); // asset_author_id
        $projectTaskIndex['author_id'] = $user->id;
        $projectTaskIndex->save();
        $task_id = $projectTaskIndex->id;

        // add task_type_pe_request
        $taskTypeLegalRequest = new TaskTypeNpdPlannerRequest();
        $taskTypeLegalRequest['id'] = $param['npd_planner_request_p_id']; //project_id
        $taskTypeLegalRequest['author_id'] = $param['npd_planner_request_author_id'];
        $taskTypeLegalRequest['type'] = $param['npd_planner_request_task_type'];
        $taskTypeLegalRequest['created_at'] = Carbon::now();
        $taskTypeLegalRequest['task_id'] = $task_id;
        $taskTypeLegalRequest->save();

        // new correspondence when adding asset
        $this->correspondence_add_new_task($projectTaskIndex['project_id'], 'NPD Planner Request', $projectTaskIndex);

        // Correspondence for NPD Design Request Type
        $this->correspondence_new_npd_planner_reqeust($projectTaskIndex['project_id'], 'NPD Planner Request', $projectTaskIndex);

        // TODO notification
        // Send notification to copywriter(brand check) via email
        // Do action - copy request
//        if($projectTaskIndex['status'] == 'copy_requested') {
//            $notify = new NotifyController();
//            $notify->copy_request($request['legal_request_c_id'], $task_id);
//        } else if($projectTaskIndex['status'] == 'copy_complete') {
//            $notify = new NotifyController();
//            $notify->copy_complete($request['legal_request_c_id'], $task_id);
//        }
        ///////////////////////////////////////////////////////////////

        $previous = basename(url()->previous());
        if($previous == 'edit_general'){
            return redirect('admin/project/'.$request['npd_planner_request_p_id'].'/edit_general#'.$task_id)
                ->with('success', __('Added the NPD Planner Request Task : ' . $task_id));
        }else{
            return redirect('admin/npd_planner_request/'.$request['npd_planner_request_p_id'].'/edit#asset_selector')
                ->with('success', __('Added the NPD Planner Request : ' . $task_id));
//            return redirect('admin/project/'.$request['npd_planner_request_p_id'].'/edit#'.$task_id)
//                ->with('success', __('Added the NPD Planner Request : ' . $task_id));
        }

    }

    public function correspondence_new_pe_reqeust($p_id, $task_name, $projectTaskIndex)
    {

        $user = auth()->user();
        $task_name = strtoupper($task_name);
        $change_line = "<p><b style='color: #b91d19;'>DISPLAY & PE REQUEST</b> <b>(#$projectTaskIndex->id)</b> has been created by $user->first_name. </p>";

        $pe_request_note = new PeRequestNotes();
        $pe_request_note['id'] = $projectTaskIndex->id;
        $pe_request_note['user_id'] = $user->id;
        $pe_request_note['pe_request_type_id'] = 0;
        $pe_request_note['task_id'] = $projectTaskIndex->id;
        $pe_request_note['project_id'] = $p_id;
        $pe_request_note['note'] = $change_line;
        $pe_request_note['created_at'] = Carbon::now();
        $pe_request_note->save();
    }

    public function correspondence_new_npd_design_reqeust($p_id, $task_name, $projectTaskIndex)
    {

        $user = auth()->user();
        $task_name = strtoupper($task_name);
        $change_line = "<p><b style='color: #b91d19;'>$task_name</b> <b>(#$projectTaskIndex->id)</b> has been created by $user->first_name. </p>";

        $pe_request_note = new NpdDesignRequestNotes();
        $pe_request_note['id'] = $projectTaskIndex->id;
        $pe_request_note['user_id'] = $user->id;
        $pe_request_note['npd_design_request_type_id'] = 0;
        $pe_request_note['task_id'] = $projectTaskIndex->id;
        $pe_request_note['project_id'] = $p_id;
        $pe_request_note['note'] = $change_line;
        $pe_request_note['created_at'] = Carbon::now();
        $pe_request_note->save();
    }

    public function correspondence_new_npd_planner_reqeust($p_id, $task_name, $projectTaskIndex)
    {

        $user = auth()->user();
        $task_name = strtoupper($task_name);
        $change_line = "<p><b style='color: #b91d19;'>$task_name</b> <b>(#$projectTaskIndex->id)</b> has been created by $user->first_name. </p>";

        $request_note = new NpdPlannerRequestNotes();
        $request_note['id'] = $projectTaskIndex->id;
        $request_note['user_id'] = $user->id;
        $request_note['npd_planner_request_type_id'] = 0;
        $request_note['task_id'] = $projectTaskIndex->id;
        $request_note['project_id'] = $p_id;
        $request_note['note'] = $change_line;
        $request_note['created_at'] = Carbon::now();
        $request_note->save();
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


    public function add_file_correspondence_for_task($concept_development, $user, $file_type, $task_type)
    {
        // Insert into project note for correspondence (attachment file)
        $task_type_ =  strtoupper(str_replace('_', ' ', $task_type));
        $change_line  = "<p>$user->first_name has added a new attachment ($file_type) to <b style='color: #b91d19;'>$task_type_</b> <b>(#$concept_development->task_id)</b></p>";

        $project_note = new ProjectNotes();
        $project_note['id'] = $concept_development->id;
        $project_note['user_id'] = $user->id;
        $project_note['task_id'] = 0;
        $project_note['type'] = $task_type;
        $project_note['note'] = $change_line;
        $project_note['created_at'] = Carbon::now();
        $project_note->save();
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

                $label = ucwords(str_replace('_', ' ', $label));
                $from  = trim($change['original']); // Remove strip tags
                $to    = trim($change['new']);      // Remove strip tags

                $change_line .= "<div class='change_label'><p>$label:</p></div>"
                    . "<div class='change_to'><p>$to</p></div>"
                    . "<div class='change_from'><del><p>$from</p></del></div>";
            }
            $campaign_note = new ProjectNotes();
            $campaign_note['id'] = $origin_param->id;
            $campaign_note['user_id'] = $user->id;
            $campaign_note['task_id'] = NULL;
            $campaign_note['type'] = $task_type;
            $campaign_note['note'] = $change_line;
            $campaign_note['created_at'] = Carbon::now();
            $campaign_note->save();
        }
    }


    public function get_task_param($task_type, $data)
    {
        if($task_type == 'concept_development') {
            $new = array(
                'benchmark' => $data['benchmark'],
                'due_date' => $data['due_date']
            );
            return $new;
        }else if($task_type == 'legal_request'){
            $new = array(
                'request' => $data['request'],
                'priority' => $data['priority'],
            );
            return $new;
        }else if($task_type == 'product_brief'){
            $new = array(
                'product_name' => $data['product_name'],
                'material_number' => $data['material_number'],
                'total_sku_count' => $data['total_sku_count'],
                'target_receiving_date' => $data['target_receiving_date'],
                'door' => $data['door'],
                'nsp' => $data['nsp'],
                'srp' => $data['srp'],
                'category' => $data['category'],
                'sub_category' => $data['sub_category'],
                'franchise' => $data['franchise'],
                'shade_names' => $data['shade_names'],
                'claim_weight' => $data['claim_weight'],
                'testing_claims' => $data['testing_claims'],
                'concept' => $data['concept'],
                'key' => $data['key'],
                'product_format' => $data['product_format'],
                'texture' => $data['texture'],
                'finish' => $data['finish'],
                'coverage' => $data['coverage'],
                'must_ban' => $data['must_ban'],
                'highlights' => $data['highlights'],
            );
            return $new;
        }else if($task_type == 'product_information'){
            $new = array(
                'product_name' => $data['product_name'],
                'product_line' => $data['product_line'],
                'total_sku_count' => $data['total_sku_count'],
                'category' => $data['category'],
                'segment' => $data['segment'],
                'product_dimension' => $data['product_dimension'],
                'claim_weight' => $data['claim_weight'],
                'weight_unit' => $data['weight_unit'],
                'components' => $data['components'],
                'what_it_is' => $data['what_it_is'],
                'features' => $data['features'],
                'marketing_claim' => $data['marketing_claim'],
                'applications' => $data['applications'],
                'sustainability' => $data['sustainability'],
                'if_others' => $data['if_others'],
                'distribution' => $data['distribution'],
                'if_others_distribution' => $data['if_others_distribution'],
            );
            return $new;
        }else if($task_type == 'qc_request'){
            $new = array(
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
            );
            return $new;
        }else if($task_type == 'misc'){
            $new = array(
                'title' => $data['title'],
                'launch_date' => $data['launch_date'],
                'details' => $data['details'],
                'products_featured' => $data['products_featured'],
                'copy' => $data['copy'],
                'developer_url' => $data['developer_url'],
            );
            return $new;
        }else if($task_type == 'sms_request'){
            $new = array(
                'title' => $data['title'],
                'launch_date' => $data['launch_date'],
                'details' => $data['details'],
                'products_featured' => $data['products_featured'],
                'copy' => $data['copy'],
                'developer_url' => $data['developer_url'],
            );
            return $new;
        }else if($task_type == 'topcategories_copy'){
            $new = array(
                'launch_date' => $data['launch_date'],
                'products_featured' => $data['products_featured'],
                'copy' => $data['copy'],
                'click_through_links' => $data['click_through_links'],
            );
            return $new;
        }else if($task_type == 'programmatic_banners'){
            $new = array(
                'date_from' => $data['date_from'],
                'date_to' => $data['date_to'],
                'include_formats' => $data['include_formats'],
                'display_dimension' => $data['display_dimension'],
                'products_featured' => $data['products_featured'],
                'click_through_links' => $data['click_through_links'],
                'promo_code' => $data['promo_code'],
            );
            return $new;
        }else if($task_type == 'image_request'){
            $new = array(
                'launch_date' => $data['launch_date'],
                'client' => $data['client'],
                'description' => $data['description'],
                'image_dimensions' => $data['image_dimensions'],
                'image_ratio' => $data['image_ratio'],
                'image_format' => $data['image_format'],
                'max_filesize' => $data['max_filesize'],
                'sku' => $data['sku'],
                'products_featured' => $data['products_featured'],
            );
            return $new;
        }else if($task_type == 'roll_over'){
            $new = array(
                'launch_date' => $data['launch_date'],
                'sku' => $data['sku'],
                'products_featured' => $data['products_featured'],
                'notes' => $data['notes'],
            );
            return $new;
        }else if($task_type == 'store_front'){
            $new = array(
                'launch_date' => $data['launch_date'],
                'client' => $data['client'],
                'products_featured' => $data['products_featured'],
                'notes' => $data['notes'],
            );
            return $new;
        }else if($task_type == 'a_content'){
            $new = array(
                'launch_date' => $data['launch_date'],
                'product_line' => $data['product_line'],
                'invision_link' => $data['invision_link'],
                'products_featured' => $data['products_featured'],
                'note' => $data['note'],
            );
            return $new;
        }else if($task_type == 'youtube_copy'){
            $new = array(
                'launch_date' => $data['launch_date'],
                'information' => $data['information'],
                'url_preview' => $data['url_preview'],
                'products_featured' => $data['products_featured'],
                'title' => $data['title'],
                'description' => $data['description'],
                'tags' => $data['tags'],
            );
            return $new;
        }else if($task_type == 'info_graphic'){
            $new = array(
                'launch_date' => $data['launch_date'],
                'product_line' => $data['product_line'],
                'invision_link' => $data['invision_link'],
                'products_featured' => $data['products_featured'],
                'note' => $data['note'],
            );
            return $new;
        }

    }

    public function add_task_correspondence($p_id, $task_type, $task_id, $status)
    {
        // Insert into Project note for correspondence (attachment file)
        $user = auth()->user();
        $task_type_ =  strtoupper(str_replace('_', ' ', $task_type));
        $change_line  = "<p>$user->first_name $status for <b style='color: #b91d19'>$task_type_</b> <b>(#$task_id)</b></p>";

        $campaign_note = new ProjectNotes();
        $campaign_note['id'] = $p_id;
        $campaign_note['user_id'] = $user->id;
        $campaign_note['task_id'] = $task_id;
        $campaign_note['type'] = $task_type;
        $campaign_note['note'] = $change_line;
        $campaign_note['created_at'] = Carbon::now();
        $campaign_note->save();
    }



    public function permission_check($param){

        if($param['status'] != 'copy_requested'){
            $user = auth()->user();
            $user_role = $user->role;

            if($param['status'] == 'in_progress'){
                if($user_role != 'graphic designer'
                    && $user_role != 'creative director'
                    && $user_role != 'content creator'
                    && $user_role != 'content manager'
                    && $user_role != 'web production'
                    && $user_role != 'web production manager'
                    && $user_role != 'admin'){
                    return false;
                }
            } else if ($param['status'] == 'copy_in_progress') {
                if ($user_role != 'copywriter'
                    && $user_role != 'copywriter manager'
                    && $user_role != 'admin') {
                    return false;
                }
            } else {
                if ($user_role != 'admin') {
                    return false;
                }
            }

        }
        return true;
    }

    public function taskRemovePermissionCheck($t_id)
    {
        $user = auth()->user();
        if($user->role == 'Admin') return true; // admin okay
        // task creator check
        $task_author_id = $this->projectTaskIndexRepository->get_author_id_by_task_id($t_id);
        if($task_author_id != $user->id){
            return false;
        }
        return true;
    }

    public function taskRemove($t_id, $type)
    {
        $obj = $this->projectTaskIndexRepository->findById($t_id);

        $p_id = $obj->project_id;

        if($this->taskRemovePermissionCheck($t_id)){

            // Add correspondence for asset Removed
            $this->add_task_correspondence($p_id, $type, $t_id, 'Removed the Task ');

            // Delete from projectTaskIndex table
            $this->projectTaskIndexRepository->delete($t_id);

            echo '/admin/project/'.$p_id.'/edit#'.$t_id;

//            if($type == 'concept_development'){
//                if($this->taskTypeConceptDevelopmentRepository->deleteByTaskId($t_id)){
//                    echo '/admin/project/'.$p_id.'/edit#'.$t_id;
//                }else{
//                    echo 'fail';
//                }
//            }else if($type == 'product_information'){
//                if($this->taskTypeProductInformationRepository->deleteByTaskId($t_id)){
//                    echo '/admin/project/'.$p_id.'/edit#'.$t_id;
//                }else{
//                    echo 'fail';
//                }
//            }else if($type == 'mm_request'){
//                if($this->taskTypeMmRequestRepository->deleteByTaskId($t_id)){
//                    echo '/admin/project/'.$p_id.'/edit#'.$t_id;
//                }else{
//                    echo 'fail';
//                }
//            }else if($type == 'legal_request'){
//                if($this->taskTypeLegalRequestRepository->deleteByTaskId($t_id)){
//                    echo '/admin/project/'.$p_id.'/edit#'.$t_id;
//                }else{
//                    echo 'fail';
//                }
//            }else if($type == 'ra_request'){
//                if($this->raRequestRepository->deleteByTaskId($t_id)){
//                    echo '/admin/project/'.$p_id.'/edit#'.$t_id;
//                }else{
//                    echo 'fail';
//                }
//            }else if($type == 'npd_po_request'){
//                if($this->taskTypeNpdPoRequestRepository->deleteByTaskId($t_id)){
//                    echo '/admin/project/'.$p_id.'/edit#'.$t_id;
//                }else{
//                    echo 'fail';
//                }
//            }else if($type == 'display_request'){
//                if($this->taskTypeDisplayRequestRepository->deleteByTaskId($t_id)){
//                    echo '/admin/project/'.$p_id.'/edit#'.$t_id;
//                }else{
//                    echo 'fail';
//                }
//            }else if($type == 'pe_request'){
//                if($this->taskTypePeRequestRepository->deleteByTaskId($t_id)){
//                    echo '/admin/project/'.$p_id.'/edit#'.$t_id;
//                }else{
//                    echo 'fail';
//                }
//            }else if($type == 'qc_request') {
//                if ($this->taskTypeQcRequestRepository->deleteByTaskId($t_id)) {
//                    echo '/admin/project/' . $p_id . '/edit#' . $t_id;
//                } else {
//                    echo 'fail';
//                }
//            }
        }else{
            echo 'fail';
        }

    }

    public function skippedTaskRemove($t_id, $type)
    {
        $obj = $this->projectTaskIndexRepository->findById($t_id);

        $p_id = $obj->project_id;

        if($this->taskRemovePermissionCheck($t_id)){

            // Add correspondence for asset Removed
            $this->add_task_correspondence($p_id, $type, $t_id, 'Removed the Skipped Task ');

            // Delete from projectTaskIndex table
            $this->projectTaskIndexRepository->delete($t_id);

            echo '/admin/project/'.$p_id.'/edit#'.$t_id;

        }else{
            echo 'fail';
        }

    }



    public function projectRemove($c_id)
    {
        $user = auth()->user();
        $c_obj = $this->projectRepository->findById($c_id);
        $a_id = $c_obj->author_id;

        if( ($user->id == $a_id) || ($user->role == 'Admin') ){

//            $this->campaignAssetIndexRepository->deleteByCampaignId($c_id);
            $data['status'] = 'deleted';

            if($this->projectRepository->update($c_id, $data)){
                // add history... for deleted project!!
                $project_note = new ProjectNotes();
                $project_note['id'] = $c_id;
                $user = auth()->user();
                $project_note['user_id'] = $user->id;
                $project_note['type'] = 'project';
                $project_note['note'] = $user->first_name . " Sent this Project to Deleted";
                $project_note['created_at'] = Carbon::now();
                $project_note->save();

                // TODO delete subQraRequestIndex by task_id..
//                $task_id = $c_obj->id;
//                $this->subQraRequestIndexRepository->delete_qra_request_by_task_id($task_id);

                echo 'success';
            }else{
                echo 'fail';
            }
        }else{
            echo 'You do not have permission to remove this request';
        }

    }

    public function project_add_note(Request $request)
    {
        $param = $request->all();
        $user = auth()->user();

        $p_id = $param['p_id'];
        $p_title = $param['p_title'];
        $email_list = $param['email_list'];

        $project_note = new ProjectNotes();
        $project_note['id'] = $p_id;
        $project_note['user_id'] = $user->id;
        $project_note['type'] = 'note';
        $project_note['note'] = $param['create_note'];
        $project_note->save();

        if($email_list) {
            $details = [
                'mail_subject' => 'New Message',
                'template' => 'emails.task.message',
                'receiver' => 'You got a new message from ' . $user->first_name . ' ', $user->last_name . ',',
                'title' => $p_title,
                'message' => $param['create_note'],
                'url' => '/admin/project/' . $p_id . '/edit',
            ];
            $receiver_list = explode(',', $email_list);
            Mail::to($receiver_list)->send(new TaskStatusNotification($details));
        }

        $this->data['currentAdminMenu'] = 'project';

        return redirect('admin/project/'.$p_id.'/edit')
            ->with('success', __('Data has been Updated.'));
    }

    public function revision_reason(Request $request)
    {
        $param = $request->all();
        $project_id = $param['project_id'];
        $revision_reason = $param['revision_reason'];
        $revision_reason_note = $param['revision_reason_note'];

        $params['status'] = 'review';
        $params['updated_at'] = Carbon::now();
        $params['revision_reason'] = $revision_reason;
        $params['revision_reason_note'] = $revision_reason_note;
        if($this->projectRepository->update($project_id, $params)){

            $user = auth()->user();
            $change_line  = "<p>$user->first_name updated the status to <b>Revision</b> for <b style='color: #b91d19;'>Project</b><b>(#$project_id)</b>
                            <br> <b style='color: black;'>Revision Reason : $revision_reason </b>
                            <br> <b style='color: black;'>$revision_reason_note </b>
                            </p>";
            $note = new ProjectNotes();
            $note['id'] = $project_id;
            $note['user_id'] = $user->id;
            $note['task_id'] = null;
            $note['type'] = 'project';
            $note['note'] = $change_line;
            $note['created_at'] = Carbon::now();
            $note->save();

            // Send Notification for revision
            $this->send_notification_revision_project($project_id);

            return redirect('admin/project/'.$project_id.'/edit')
                ->with('success', __('Data has been Updated.'));
        }

        return redirect('admin/project/'.$project_id.'/edit')
            ->with('error', __('Data updates Failed'));
    }

    public function send_notification_revision_project($project_id)
    {
        // From : Team Lead & Management (Approve)
        // Receiver : Project Creator
        $project_obj = $this->projectRepository->findById($project_id);

        // Task Creator
        $creator_author_name = $project_obj->author->first_name . ' ' . $project_obj->author->last_name;

        // Revision
        $user = auth()->user();
        $revision_name = $user->first_name . ' ' . $user->last_name;

        $details = [
            'template'          => 'emails.project.revision_project',
            'mail_subject'      => 'Action Requested : Project Revision',
            'receiver'          => "Hello " . $creator_author_name . ", ",
            'message'           => 'You got a new request from ' . $revision_name . ".",
            'title'             => "Action Requested : Project Revision",
            'project_id'        => $project_obj->id,
            'project_title'     => $project_obj->name,
            'reason'            => $project_obj['revision_reason'],
            'note'              => $project_obj['revision_reason_note'],
            'url'               => '/admin/project/'.$project_obj->id.'/edit',
        ];
        $creator_email = $project_obj->author->email;

        /// Send to receivers
        Mail::to($creator_email)->send(new TaskStatusNotification($details));
    }

}
