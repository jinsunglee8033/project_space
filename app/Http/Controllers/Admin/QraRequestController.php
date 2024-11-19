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
use App\Mail\SendMail;
use App\Models\AssetOwnerAssets;
use App\Models\CampaignAssetIndex;
use App\Models\CampaignNotes;

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
use App\Models\ProjectNotes;
use App\Models\ProjectTaskIndex;
use App\Models\ProjectTypeTaskAttachments;
use App\Models\QraRequestNotes;
use App\Models\QraRequestTypeAttachments;
use App\Models\SubQraRequestIndex;
use App\Models\SubQraRequestType;
use App\Models\TaskTypeConceptDevelopment;
use App\Models\TaskTypeLegalRequest;
use App\Models\TaskTypeProductBrief;
use App\Models\User;

use App\Repositories\Admin\AssetNotificationUserRepository;
use App\Repositories\Admin\AssetOwnerAssetsRepository;
use App\Repositories\Admin\CampaignAssetIndexRepository;
use App\Repositories\Admin\CampaignNotesRepository;
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

use App\Repositories\Admin\QraRequestNotesRepository;
use App\Repositories\Admin\QraRequestRepository;
use App\Repositories\Admin\QraRequestTypeFileAttachmentsRepository;
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

class QraRequestController extends Controller
{
    Private $projectRepository;
    Private $qraRequestRepository;
    Private $subQraRequestTypeRepository;
    Private $subQraRequestIndexRepository;
    Private $projectTaskIndexRepository;
    Private $taskTypeConceptDevelopmentRepository;
    Private $taskTypeLegalRequestRepository;
    Private $taskTypeProductBriefRepository;
    private $projectTaskFileAttachmentsRepository;
    private $qraRequestTypeFileAttachmentsRepository;
    Private $projectNotesRepository;
    Private $qraRequestNotesRepository;
    Private $teamRepository;
    private $userRepository;

    public function __construct(ProjectRepository $projectRepository,
                                QraRequestRepository $qraRequestRepository,
                                SubQraRequestTypeRepository $subQraRequestTypeRepository,
                                SubQraRequestIndexRepository $subQraRequestIndexRepository,
                                ProjectTaskIndexRepository $projectTaskIndexRepository,
                                TaskTypeConceptDevelopmentRepository $taskTypeConceptDevelopmentRepository,
                                TaskTypeLegalRequestRepository $taskTypeLegalRequestRepository,
                                TaskTypeProductBriefRepository $taskTypeProductBriefRepository,
                                ProjectTaskFileAttachmentsRepository $projectTaskFileAttachmentsRepository,
                                QraRequestTypeFileAttachmentsRepository $qraRequestTypeFileAttachmentsRepository,
                                ProjectNotesRepository $projectNotesRepository,
                                QraRequestNotesRepository $qraRequestNotesRepository,
                                TeamRepository $teamRepository,
                                UserRepository $userRepository)
    {
        parent::__construct();

        $this->projectRepository = $projectRepository;
        $this->qraRequestRepository = $qraRequestRepository;
        $this->subQraRequestTypeRepository = $subQraRequestTypeRepository;
        $this->subQraRequestIndexRepository = $subQraRequestIndexRepository;
        $this->projectTaskIndexRepository = $projectTaskIndexRepository;
        $this->taskTypeConceptDevelopmentRepository = $taskTypeConceptDevelopmentRepository;
        $this->taskTypeLegalRequestRepository = $taskTypeLegalRequestRepository;
        $this->taskTypeProductBriefRepository = $taskTypeProductBriefRepository;
        $this->projectTaskFileAttachmentsRepository = $projectTaskFileAttachmentsRepository;
        $this->qraRequestTypeFileAttachmentsRepository = $qraRequestTypeFileAttachmentsRepository;
        $this->projectNotesRepository = $projectNotesRepository;
        $this->qraRequestNotesRepository = $qraRequestNotesRepository;
        $this->teamRepository = $teamRepository;
        $this->userRepository = $userRepository;
    }

    public function index(Request $request)
    {
        $params = $request->all();
        $params['status'] = 'active';
        $this->data['currentAdminMenu'] = 'qra_request';
        $options = [
            'per_page' => $this->perPage,
            'order' => [
                'id' => 'asc',
            ],
            'filter' => $params,
        ];
        $this->data['filter'] = $params;
        $this->data['projects'] = $this->qraRequestRepository->findAll($options);
        $this->data['id'] = !empty($params['id']) ? $params['id'] : '';

        return view('admin.qra_request.index', $this->data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->data['currentAdminMenu'] = 'qra_request';
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
        $task_id = $this->qraRequestRepository->get_task_id_for_qra($id);
        $this->data['task_id']= $task_id;

        // Request Type list
        $this->data['request_type_list'] = $request_type_list = $this->qraRequestRepository->get_request_type_list_by_task_id($task_id);

        // task_detail
        if(sizeof($request_type_list)>0){
            foreach ($request_type_list as $k => $request_type){
                $qra_request_type_id = $request_type->qra_request_type_id;
                $task_files = $this->qraRequestTypeFileAttachmentsRepository->findAllByRequestTypeId($qra_request_type_id);
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
//        $correspondences = $this->projectNotesRepository->findAll($options);
        $correspondences = $this->qraRequestNotesRepository->findAll($options);
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

        /////////// QRA Request Task ////////////////////////////////////////////
        $this->data['request_types'] = [
            'Formulation Prescreen', 'Artwork Contents', 'Artwork Review', 'Registration WERCS', 'Registration SmarterX',
            'Registration California', 'Registration CNF', 'Registration CPNP', 'Registration SCPN', 'Registration IIO',
            'Registration MoCRA', 'Document Support'
        ];
        $this->data['versions'] = [
            '2023 NPD','2024 NPD','2025 NPD','2026 NPD','2027 NPD','2028 NPD','2029 NPD','2030 NPD','Existing/Revamp'
        ];
        $this->data['request_type'] = null;
        $this->data['target_regions'] = [
            'U.S.', 'Canada', 'EU', 'UK'
        ];
        /////////////////////////////////////////////////////////////////////////

        return view('admin.qra_request.form', $this->data);
    }


    public function add_formulation_prescreen(Request $request){

        $user = auth()->user();

        $sub_qra_request_index = new SubQraRequestIndex();
        $sub_qra_request_index['task_id'] = $request['formulation_prescreen_t_id'];
        $sub_qra_request_index['request_type'] = $request['formulation_prescreen_request_type'];
        $sub_qra_request_index['author_id'] = $user->id;
        $sub_qra_request_index['status'] = 'action_requested';
        $sub_qra_request_index->save();

        $qra_request_type_id = $sub_qra_request_index->id;

        $subQraRequestType = new SubQraRequestType();
        $subQraRequestType['id'] = $request['formulation_prescreen_t_id'];
        $subQraRequestType['author_id'] = $user->id;
        $subQraRequestType['type'] = 'formulation_prescreen';
        $subQraRequestType['qra_request_type_id'] = $qra_request_type_id;
        $subQraRequestType['version'] = $request['formulation_prescreen_version'];
        $subQraRequestType['material_number'] = $request['formulation_prescreen_material_number'];
        $subQraRequestType['vendor_code'] = $request['formulation_prescreen_vendor_code'];
        $subQraRequestType['vendor_name'] = $request['formulation_prescreen_vendor_name'];
        $subQraRequestType['target_region'] = $request['formulation_prescreen_target_region'];
        if (isset($request['formulation_prescreen_target_region'])) {
            $subQraRequestType['target_region'] = implode(',', $request['formulation_prescreen_target_region']);
        } else {
            $subQraRequestType['target_region'] = '';
        }
        $subQraRequestType->save();

        $this->correspondence_add_qra_reqeust_type($qra_request_type_id, 'Formulation Prescreen', $sub_qra_request_index);

        // add campaign_type_asset_attachments
        if($request->file('formulation_prescreen_attachment')){
            foreach ($request->file('formulation_prescreen_attachment') as $file) {
                $attachments = new QraRequestTypeAttachments();

//                $fileName = $file->storeAs('campaigns/'.$request['legal_request_c_id'].'/'.$asset_id, $originalName);
                $fileName = $this->file_exist_check_qra($file, $request['formulation_prescreen_t_id'], $qra_request_type_id);

                $attachments['task_id'] = $request['formulation_prescreen_t_id'];
                $attachments['type'] = 'attachment_file_' . $file->getMimeType();
                $attachments['author_id'] = $user->id;
                $attachments['attachment'] = '/' . $fileName;
                $attachments['qra_request_type_id'] = $qra_request_type_id;
                $attachments['file_ext'] = pathinfo($fileName, PATHINFO_EXTENSION);
                $attachments['file_type'] = $file->getMimeType();
                $attachments['file_size'] = $file->getSize();
                $attachments['created_at'] = Carbon::now();
                $attachments->save();

                $this->add_file_correspondence_for_qra($qra_request_type_id, $subQraRequestType->id, $user, $fileName, 'formulation_prescreen');
            }
        }

        $project_id = $this->projectTaskIndexRepository->get_project_id_by_task_id($request['formulation_prescreen_t_id']);

        return redirect('admin/qra_request/'.$project_id.'/edit#'.$qra_request_type_id)
            ->with('success', __('Added the Formulation Prescreen Type : ' . $qra_request_type_id));
    }

    public function edit_formulation_prescreen(Request $request, $qra_request_type_id)
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

        $project_id = $this->projectTaskIndexRepository->get_project_id_by_task_id($param['formulation_prescreen_t_id']);
        $subQraRequestType = $this->subQraRequestTypeRepository->findById($qra_request_type_id);

        if($this->subQraRequestTypeRepository->update($qra_request_type_id, $param)){

            $user = auth()->user();
            // insert into campaign note for correspondence
            $this->correspondence_update_qra_request_type('formulation_prescreen', $param, $subQraRequestType, $user);

            if($request->file('attachment')){
                foreach ($request->file('attachment') as $file) {
                    $attachments = new QraRequestTypeAttachments();

                    $fileName = $this->file_exist_check_qra($file, $subQraRequestType->id, $qra_request_type_id);

                    $attachments['task_id'] = $subQraRequestType->id; // task_id
                    $attachments['type'] = 'attachment_file_' . $file->getMimeType();
                    $attachments['author_id'] = $user->id;
                    $attachments['attachment'] = '/' . $fileName;
                    $attachments['qra_request_type_id'] = $qra_request_type_id;
                    $attachments['file_ext'] = pathinfo($fileName, PATHINFO_EXTENSION);
                    $attachments['file_type'] = $file->getMimeType();
                    $attachments['file_size'] = $file->getSize();
                    $attachments['created_at'] = Carbon::now();
                    $attachments->save();

                    // insert file attachment correspondence
                    $this->add_file_correspondence_for_qra($qra_request_type_id, $subQraRequestType->id, $user, $fileName, 'formulation_prescreen');
                }
            }

            return redirect('admin/qra_request/'.$project_id.'/edit#'.$qra_request_type_id)
                ->with('success', __('Formulation Prescreen ('.$qra_request_type_id.') - Update Success'));
        }

        return redirect('admin/qra_request/'.$project_id.'/edit#'.$qra_request_type_id)
            ->with('error', __('Update Failed'));
    }

    public function add_artwork_contents(Request $request){

        $user = auth()->user();

        $sub_qra_request_index = new SubQraRequestIndex();
        $sub_qra_request_index['task_id'] = $request['artwork_contents_t_id'];
        $sub_qra_request_index['request_type'] = $request['artwork_contents_request_type'];
        $sub_qra_request_index['author_id'] = $user->id;
        $sub_qra_request_index['status'] = 'action_requested';
        $sub_qra_request_index->save();

        $qra_request_type_id = $sub_qra_request_index->id;

        $subQraRequestType = new SubQraRequestType();
        $subQraRequestType['id'] = $request['artwork_contents_t_id'];
        $subQraRequestType['author_id'] = $user->id;
        $subQraRequestType['type'] = 'artwork_contents';
        $subQraRequestType['qra_request_type_id'] = $qra_request_type_id;
        $subQraRequestType['version'] = $request['artwork_contents_version'];
        $subQraRequestType['material_number'] = $request['artwork_contents_material_number'];
        $subQraRequestType['vendor_code'] = $request['artwork_contents_vendor_code'];
        $subQraRequestType['vendor_name'] = $request['artwork_contents_vendor_name'];
        $subQraRequestType['target_region'] = $request['artwork_contents_target_region'];
        if (isset($request['artwork_contents_target_region'])) {
            $subQraRequestType['target_region'] = implode(',', $request['artwork_contents_target_region']);
        } else {
            $subQraRequestType['target_region'] = '';
        }
        $subQraRequestType->save();

        $this->correspondence_add_qra_reqeust_type($qra_request_type_id, 'Artwork Contents', $sub_qra_request_index);

        // add qra_request_type_attachments
        if($request->file('artwork_contents_attachment')){
            foreach ($request->file('artwork_contents_attachment') as $file) {
                $attachments = new QraRequestTypeAttachments();

//                $fileName = $file->storeAs('campaigns/'.$request['legal_request_c_id'].'/'.$asset_id, $originalName);
                $fileName = $this->file_exist_check_qra($file, $request['artwork_contents_t_id'], $qra_request_type_id);

                $attachments['task_id'] = $subQraRequestType->id; // task_id
                $attachments['type'] = 'attachment_file_' . $file->getMimeType();
                $attachments['author_id'] = $user->id;
                $attachments['attachment'] = '/' . $fileName;
                $attachments['qra_request_type_id'] = $qra_request_type_id;
                $attachments['file_ext'] = pathinfo($fileName, PATHINFO_EXTENSION);
                $attachments['file_type'] = $file->getMimeType();
                $attachments['file_size'] = $file->getSize();
                $attachments['created_at'] = Carbon::now();
                $attachments->save();

                // insert file attachment correspondence
                $this->add_file_correspondence_for_qra($qra_request_type_id, $subQraRequestType->id, $user, $fileName, 'artwork_contents');
            }
        }

        $project_id = $this->projectTaskIndexRepository->get_project_id_by_task_id($request['artwork_contents_t_id']);

        return redirect('admin/qra_request/'.$project_id.'/edit#'.$qra_request_type_id)
            ->with('success', __('Added the Artwork Contents Type : ' . $qra_request_type_id));
    }

    public function edit_artwork_contents(Request $request, $qra_request_type_id)
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

        $project_id = $this->projectTaskIndexRepository->get_project_id_by_task_id($param['artwork_contents_t_id']);
        $subQraRequestType = $this->subQraRequestTypeRepository->findById($qra_request_type_id);

        if($this->subQraRequestTypeRepository->update($qra_request_type_id, $param)){

            $user = auth()->user();
            // insert into QRA Request note for correspondence
            $this->correspondence_update_qra_request_type('artwork_contents', $param, $subQraRequestType, $user);

            if($request->file('attachment')){
                foreach ($request->file('attachment') as $file) {
                    $attachments = new QraRequestTypeAttachments();

                    $fileName = $this->file_exist_check_qra($file, $subQraRequestType->id, $qra_request_type_id);

                    $attachments['task_id'] = $subQraRequestType->id; // task_id
                    $attachments['type'] = 'attachment_file_' . $file->getMimeType();
                    $attachments['author_id'] = $user->id;
                    $attachments['attachment'] = '/' . $fileName;
                    $attachments['qra_request_type_id'] = $qra_request_type_id;
                    $attachments['file_ext'] = pathinfo($fileName, PATHINFO_EXTENSION);
                    $attachments['file_type'] = $file->getMimeType();
                    $attachments['file_size'] = $file->getSize();
                    $attachments['created_at'] = Carbon::now();
                    $attachments->save();

                    // insert file attachment correspondence
                    $this->add_file_correspondence_for_qra($qra_request_type_id, $subQraRequestType->id, $user, $fileName, 'artwork_contents');
                }
            }

            return redirect('admin/qra_request/'.$project_id.'/edit#'.$qra_request_type_id)
                ->with('success', __('Artwork Contents ('.$qra_request_type_id.') - Update Success'));
        }

        return redirect('admin/qra_request/'.$project_id.'/edit#'.$qra_request_type_id)
            ->with('error', __('Update Failed'));
    }

    public function add_artwork_review(Request $request){

        $user = auth()->user();

        $sub_qra_request_index = new SubQraRequestIndex();
        $sub_qra_request_index['task_id'] = $request['artwork_review_t_id'];
        $sub_qra_request_index['request_type'] = $request['artwork_review_request_type'];
        $sub_qra_request_index['author_id'] = $user->id;
        $sub_qra_request_index['status'] = 'action_requested';
        $sub_qra_request_index->save();

        $qra_request_type_id = $sub_qra_request_index->id;

        $subQraRequestType = new SubQraRequestType();
        $subQraRequestType['id'] = $request['artwork_review_t_id'];
        $subQraRequestType['author_id'] = $user->id;
        $subQraRequestType['type'] = 'artwork_review';
        $subQraRequestType['qra_request_type_id'] = $qra_request_type_id;
        $subQraRequestType['version'] = $request['artwork_review_version'];
        $subQraRequestType['material_number'] = $request['artwork_review_material_number'];
        $subQraRequestType['vendor_code'] = $request['artwork_review_vendor_code'];
        $subQraRequestType['vendor_name'] = $request['artwork_review_vendor_name'];
        $subQraRequestType['target_region'] = $request['artwork_review_target_region'];
        if (isset($request['artwork_review_target_region'])) {
            $subQraRequestType['target_region'] = implode(',', $request['artwork_review_target_region']);
        } else {
            $subQraRequestType['target_region'] = '';
        }
        $subQraRequestType->save();

        $this->correspondence_add_qra_reqeust_type($qra_request_type_id, 'Artwork Review', $sub_qra_request_index);

        // add qra_request_type_attachments
        if($request->file('artwork_review_attachment')){
            foreach ($request->file('artwork_review_attachment') as $file) {
                $attachments = new QraRequestTypeAttachments();

//                $fileName = $file->storeAs('campaigns/'.$request['legal_request_c_id'].'/'.$asset_id, $originalName);
                $fileName = $this->file_exist_check_qra($file, $request['artwork_review_t_id'], $qra_request_type_id);

                $attachments['task_id'] = $subQraRequestType->id; // task_id
                $attachments['type'] = 'attachment_file_' . $file->getMimeType();
                $attachments['author_id'] = $user->id;
                $attachments['attachment'] = '/' . $fileName;
                $attachments['qra_request_type_id'] = $qra_request_type_id;
                $attachments['file_ext'] = pathinfo($fileName, PATHINFO_EXTENSION);
                $attachments['file_type'] = $file->getMimeType();
                $attachments['file_size'] = $file->getSize();
                $attachments['created_at'] = Carbon::now();
                $attachments->save();

                // insert file attachment correspondence
                $this->add_file_correspondence_for_qra($qra_request_type_id, $subQraRequestType->id, $user, $fileName, 'artwork_review');
            }
        }

        $project_id = $this->projectTaskIndexRepository->get_project_id_by_task_id($request['artwork_review_t_id']);

        return redirect('admin/qra_request/'.$project_id.'/edit#'.$qra_request_type_id)
            ->with('success', __('Added the Artwork Review Type : ' . $qra_request_type_id));
    }

    public function edit_artwork_review(Request $request, $qra_request_type_id)
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

        $project_id = $this->projectTaskIndexRepository->get_project_id_by_task_id($param['artwork_review_t_id']);
        $subQraRequestType = $this->subQraRequestTypeRepository->findById($qra_request_type_id);

        if($this->subQraRequestTypeRepository->update($qra_request_type_id, $param)){

            $user = auth()->user();
            // insert into campaign note for correspondence
            $this->correspondence_update_qra_request_type('artwork_review', $param, $subQraRequestType, $user);

            if($request->file('attachment')){
                foreach ($request->file('attachment') as $file) {
                    $attachments = new QraRequestTypeAttachments();

                    $fileName = $this->file_exist_check_qra($file, $subQraRequestType->id, $qra_request_type_id);

                    $attachments['task_id'] = $subQraRequestType->id; // task_id
                    $attachments['type'] = 'attachment_file_' . $file->getMimeType();
                    $attachments['author_id'] = $user->id;
                    $attachments['attachment'] = '/' . $fileName;
                    $attachments['qra_request_type_id'] = $qra_request_type_id;
                    $attachments['file_ext'] = pathinfo($fileName, PATHINFO_EXTENSION);
                    $attachments['file_type'] = $file->getMimeType();
                    $attachments['file_size'] = $file->getSize();
                    $attachments['created_at'] = Carbon::now();
                    $attachments->save();

                    // insert file attachment correspondence
                    $this->add_file_correspondence_for_qra($qra_request_type_id, $subQraRequestType->id, $user, $fileName, 'artwork_review');
                }
            }

            return redirect('admin/qra_request/'.$project_id.'/edit#'.$qra_request_type_id)
                ->with('success', __('Artwork Review ('.$qra_request_type_id.') - Update Success'));
        }

        return redirect('admin/qra_request/'.$project_id.'/edit#'.$qra_request_type_id)
            ->with('error', __('Update Failed'));
    }

    public function add_registration_wercs(Request $request){

        $user = auth()->user();

        $sub_qra_request_index = new SubQraRequestIndex();
        $sub_qra_request_index['task_id'] = $request['registration_wercs_t_id'];
        $sub_qra_request_index['request_type'] = $request['registration_wercs_request_type'];
        $sub_qra_request_index['author_id'] = $user->id;
        $sub_qra_request_index['status'] = 'action_requested';
        $sub_qra_request_index->save();

        $qra_request_type_id = $sub_qra_request_index->id;

        $subQraRequestType = new SubQraRequestType();
        $subQraRequestType['id'] = $request['registration_wercs_t_id'];
        $subQraRequestType['author_id'] = $user->id;
        $subQraRequestType['type'] = 'registration_wercs';
        $subQraRequestType['qra_request_type_id'] = $qra_request_type_id;
        $subQraRequestType['version'] = $request['registration_wercs_version'];
        $subQraRequestType['material_number'] = $request['registration_wercs_material_number'];
        $subQraRequestType['vendor_code'] = $request['registration_wercs_vendor_code'];
        $subQraRequestType['vendor_name'] = $request['registration_wercs_vendor_name'];

        $subQraRequestType->save();

        $this->correspondence_add_qra_reqeust_type($qra_request_type_id, 'Registration WERCS', $sub_qra_request_index);

        // add qra_request_type_attachments
        if($request->file('registration_wercs_attachment')){
            foreach ($request->file('registration_wercs_attachment') as $file) {
                $attachments = new QraRequestTypeAttachments();

//                $fileName = $file->storeAs('campaigns/'.$request['legal_request_c_id'].'/'.$asset_id, $originalName);
                $fileName = $this->file_exist_check_qra($file, $request['registration_wercs_t_id'], $qra_request_type_id);

                $attachments['task_id'] = $subQraRequestType->id; // task_id
                $attachments['type'] = 'attachment_file_' . $file->getMimeType();
                $attachments['author_id'] = $user->id;
                $attachments['attachment'] = '/' . $fileName;
                $attachments['qra_request_type_id'] = $qra_request_type_id;
                $attachments['file_ext'] = pathinfo($fileName, PATHINFO_EXTENSION);
                $attachments['file_type'] = $file->getMimeType();
                $attachments['file_size'] = $file->getSize();
                $attachments['created_at'] = Carbon::now();
                $attachments->save();

                // insert file attachment correspondence
                $this->add_file_correspondence_for_qra($qra_request_type_id, $subQraRequestType->id, $user, $fileName, 'registration_wercs');
            }
        }

        $project_id = $this->projectTaskIndexRepository->get_project_id_by_task_id($request['registration_wercs_t_id']);

        return redirect('admin/qra_request/'.$project_id.'/edit#'.$qra_request_type_id)
            ->with('success', __('Added the Registration WERCS Type : ' . $qra_request_type_id));
    }

    public function edit_registration_wercs(Request $request, $qra_request_type_id)
    {
        $param = $request->all();

        // Permission_check
//        if(!$this->permission_check($param)){
//            return redirect('admin/project/' . $concept_development->id . '/edit#'.$task_id)
//                ->with('error', __('This action is no longer permitted. Please contact an Administrator.'));
//        }

        $project_id = $this->projectTaskIndexRepository->get_project_id_by_task_id($param['registration_wercs_t_id']);
        $subQraRequestType = $this->subQraRequestTypeRepository->findById($qra_request_type_id);

        if($this->subQraRequestTypeRepository->update($qra_request_type_id, $param)){

            $user = auth()->user();
            // insert into campaign note for correspondence
            $this->correspondence_update_qra_request_type('registration_wercs', $param, $subQraRequestType, $user);

            if($request->file('attachment')){
                foreach ($request->file('attachment') as $file) {
                    $attachments = new QraRequestTypeAttachments();

                    $fileName = $this->file_exist_check_qra($file, $subQraRequestType->id, $qra_request_type_id);

                    $attachments['task_id'] = $subQraRequestType->id; // task_id
                    $attachments['type'] = 'attachment_file_' . $file->getMimeType();
                    $attachments['author_id'] = $user->id;
                    $attachments['attachment'] = '/' . $fileName;
                    $attachments['qra_request_type_id'] = $qra_request_type_id;
                    $attachments['file_ext'] = pathinfo($fileName, PATHINFO_EXTENSION);
                    $attachments['file_type'] = $file->getMimeType();
                    $attachments['file_size'] = $file->getSize();
                    $attachments['created_at'] = Carbon::now();
                    $attachments->save();

                    // insert file attachment correspondence
                    $this->add_file_correspondence_for_qra($qra_request_type_id, $subQraRequestType->id, $user, $fileName, 'registration_wercs');
                }
            }

            return redirect('admin/qra_request/'.$project_id.'/edit#'.$qra_request_type_id)
                ->with('success', __('Registration WERCS ('.$qra_request_type_id.') - Update Success'));
        }

        return redirect('admin/qra_request/'.$project_id.'/edit#'.$qra_request_type_id)
            ->with('error', __('Update Failed'));
    }

    public function add_registration_smarterx(Request $request){

        $user = auth()->user();

        $sub_qra_request_index = new SubQraRequestIndex();
        $sub_qra_request_index['task_id'] = $request['registration_smarterx_t_id'];
        $sub_qra_request_index['request_type'] = $request['registration_smarterx_request_type'];
        $sub_qra_request_index['author_id'] = $user->id;
        $sub_qra_request_index['status'] = 'action_requested';
        $sub_qra_request_index->save();

        $qra_request_type_id = $sub_qra_request_index->id;

        $subQraRequestType = new SubQraRequestType();
        $subQraRequestType['id'] = $request['registration_smarterx_t_id'];
        $subQraRequestType['author_id'] = $user->id;
        $subQraRequestType['type'] = 'registration_smarterx';
        $subQraRequestType['qra_request_type_id'] = $qra_request_type_id;
        $subQraRequestType['version'] = $request['registration_smarterx_version'];
        $subQraRequestType['material_number'] = $request['registration_smarterx_material_number'];
        $subQraRequestType['vendor_code'] = $request['registration_smarterx_vendor_code'];
        $subQraRequestType['vendor_name'] = $request['registration_smarterx_vendor_name'];

        $subQraRequestType->save();

        $this->correspondence_add_qra_reqeust_type($qra_request_type_id, 'Registration SmarterX', $sub_qra_request_index);

        // add qra_request_type_attachments
        if($request->file('registration_smarterx_attachment')){
            foreach ($request->file('registration_smarterx_attachment') as $file) {
                $attachments = new QraRequestTypeAttachments();

//                $fileName = $file->storeAs('campaigns/'.$request['legal_request_c_id'].'/'.$asset_id, $originalName);
                $fileName = $this->file_exist_check_qra($file, $request['registration_smarterx_t_id'], $qra_request_type_id);

                $attachments['task_id'] = $subQraRequestType->id; // task_id
                $attachments['type'] = 'attachment_file_' . $file->getMimeType();
                $attachments['author_id'] = $user->id;
                $attachments['attachment'] = '/' . $fileName;
                $attachments['qra_request_type_id'] = $qra_request_type_id;
                $attachments['file_ext'] = pathinfo($fileName, PATHINFO_EXTENSION);
                $attachments['file_type'] = $file->getMimeType();
                $attachments['file_size'] = $file->getSize();
                $attachments['created_at'] = Carbon::now();
                $attachments->save();

                // insert file attachment correspondence
                $this->add_file_correspondence_for_qra($qra_request_type_id, $subQraRequestType->id, $user, $fileName, 'registration_smarterx');
            }
        }

        $project_id = $this->projectTaskIndexRepository->get_project_id_by_task_id($request['registration_smarterx_t_id']);

        return redirect('admin/qra_request/'.$project_id.'/edit#'.$qra_request_type_id)
            ->with('success', __('Added the Registration SmarterX Type : ' . $qra_request_type_id));
    }

    public function edit_registration_smarterx(Request $request, $qra_request_type_id)
    {
        $param = $request->all();

        // Permission_check
//        if(!$this->permission_check($param)){
//            return redirect('admin/project/' . $concept_development->id . '/edit#'.$task_id)
//                ->with('error', __('This action is no longer permitted. Please contact an Administrator.'));
//        }

        $project_id = $this->projectTaskIndexRepository->get_project_id_by_task_id($param['registration_smarterx_t_id']);
        $subQraRequestType = $this->subQraRequestTypeRepository->findById($qra_request_type_id);

        if($this->subQraRequestTypeRepository->update($qra_request_type_id, $param)){

            $user = auth()->user();
            // insert into campaign note for correspondence
            $this->correspondence_update_qra_request_type('registration_smarterx', $param, $subQraRequestType, $user);

            if($request->file('attachment')){
                foreach ($request->file('attachment') as $file) {
                    $attachments = new QraRequestTypeAttachments();

                    $fileName = $this->file_exist_check_qra($file, $subQraRequestType->id, $qra_request_type_id);

                    $attachments['task_id'] = $subQraRequestType->id; // task_id
                    $attachments['type'] = 'attachment_file_' . $file->getMimeType();
                    $attachments['author_id'] = $user->id;
                    $attachments['attachment'] = '/' . $fileName;
                    $attachments['qra_request_type_id'] = $qra_request_type_id;
                    $attachments['file_ext'] = pathinfo($fileName, PATHINFO_EXTENSION);
                    $attachments['file_type'] = $file->getMimeType();
                    $attachments['file_size'] = $file->getSize();
                    $attachments['created_at'] = Carbon::now();
                    $attachments->save();

                    // insert file attachment correspondence
                    $this->add_file_correspondence_for_qra($qra_request_type_id, $subQraRequestType->id, $user, $fileName, 'registration_smarterx');
                }
            }

            return redirect('admin/qra_request/'.$project_id.'/edit#'.$qra_request_type_id)
                ->with('success', __('Registration SmarterX ('.$qra_request_type_id.') - Update Success'));
        }

        return redirect('admin/qra_request/'.$project_id.'/edit#'.$qra_request_type_id)
            ->with('error', __('Update Failed'));
    }

    public function add_registration_california(Request $request){

        $user = auth()->user();

        $sub_qra_request_index = new SubQraRequestIndex();
        $sub_qra_request_index['task_id'] = $request['registration_california_t_id'];
        $sub_qra_request_index['request_type'] = $request['registration_california_request_type'];
        $sub_qra_request_index['author_id'] = $user->id;
        $sub_qra_request_index['status'] = 'action_requested';
        $sub_qra_request_index->save();

        $qra_request_type_id = $sub_qra_request_index->id;

        $subQraRequestType = new SubQraRequestType();
        $subQraRequestType['id'] = $request['registration_california_t_id'];
        $subQraRequestType['author_id'] = $user->id;
        $subQraRequestType['type'] = 'registration_california';
        $subQraRequestType['qra_request_type_id'] = $qra_request_type_id;
        $subQraRequestType['version'] = $request['registration_california_version'];
        $subQraRequestType['material_number'] = $request['registration_california_material_number'];
        $subQraRequestType['vendor_code'] = $request['registration_california_vendor_code'];
        $subQraRequestType['vendor_name'] = $request['registration_california_vendor_name'];

        $subQraRequestType->save();

        $this->correspondence_add_qra_reqeust_type($qra_request_type_id, 'Registration California', $sub_qra_request_index);

        // add qra_request_type_attachments
        if($request->file('registration_california_attachment')){
            foreach ($request->file('registration_california_attachment') as $file) {
                $attachments = new QraRequestTypeAttachments();

//                $fileName = $file->storeAs('campaigns/'.$request['legal_request_c_id'].'/'.$asset_id, $originalName);
                $fileName = $this->file_exist_check_qra($file, $request['registration_california_t_id'], $qra_request_type_id);

                $attachments['task_id'] = $subQraRequestType->id; // task_id
                $attachments['type'] = 'attachment_file_' . $file->getMimeType();
                $attachments['author_id'] = $user->id;
                $attachments['attachment'] = '/' . $fileName;
                $attachments['qra_request_type_id'] = $qra_request_type_id;
                $attachments['file_ext'] = pathinfo($fileName, PATHINFO_EXTENSION);
                $attachments['file_type'] = $file->getMimeType();
                $attachments['file_size'] = $file->getSize();
                $attachments['created_at'] = Carbon::now();
                $attachments->save();

                // insert file attachment correspondence
                $this->add_file_correspondence_for_qra($qra_request_type_id, $subQraRequestType->id, $user, $fileName, 'registration_california');
            }
        }

        $project_id = $this->projectTaskIndexRepository->get_project_id_by_task_id($request['registration_california_t_id']);

        return redirect('admin/qra_request/'.$project_id.'/edit#'.$qra_request_type_id)
            ->with('success', __('Added the Registration California Type : ' . $qra_request_type_id));
    }

    public function edit_registration_california(Request $request, $qra_request_type_id)
    {
        $param = $request->all();

        // Permission_check
//        if(!$this->permission_check($param)){
//            return redirect('admin/project/' . $concept_development->id . '/edit#'.$task_id)
//                ->with('error', __('This action is no longer permitted. Please contact an Administrator.'));
//        }

        $project_id = $this->projectTaskIndexRepository->get_project_id_by_task_id($param['registration_california_t_id']);
        $subQraRequestType = $this->subQraRequestTypeRepository->findById($qra_request_type_id);

        if($this->subQraRequestTypeRepository->update($qra_request_type_id, $param)){

            $user = auth()->user();
            // insert into campaign note for correspondence
            $this->correspondence_update_qra_request_type('registration_california', $param, $subQraRequestType, $user);

            if($request->file('attachment')){
                foreach ($request->file('attachment') as $file) {
                    $attachments = new QraRequestTypeAttachments();

                    $fileName = $this->file_exist_check_qra($file, $subQraRequestType->id, $qra_request_type_id);

                    $attachments['task_id'] = $subQraRequestType->id; // task_id
                    $attachments['type'] = 'attachment_file_' . $file->getMimeType();
                    $attachments['author_id'] = $user->id;
                    $attachments['attachment'] = '/' . $fileName;
                    $attachments['qra_request_type_id'] = $qra_request_type_id;
                    $attachments['file_ext'] = pathinfo($fileName, PATHINFO_EXTENSION);
                    $attachments['file_type'] = $file->getMimeType();
                    $attachments['file_size'] = $file->getSize();
                    $attachments['created_at'] = Carbon::now();
                    $attachments->save();

                    // insert file attachment correspondence
                    $this->add_file_correspondence_for_qra($qra_request_type_id, $subQraRequestType->id, $user, $fileName, 'registration_california');
                }
            }

            return redirect('admin/qra_request/'.$project_id.'/edit#'.$qra_request_type_id)
                ->with('success', __('Registration California ('.$qra_request_type_id.') - Update Success'));
        }

        return redirect('admin/qra_request/'.$project_id.'/edit#'.$qra_request_type_id)
            ->with('error', __('Update Failed'));
    }

    public function add_registration_cnf(Request $request){

        $user = auth()->user();

        $sub_qra_request_index = new SubQraRequestIndex();
        $sub_qra_request_index['task_id'] = $request['registration_cnf_t_id'];
        $sub_qra_request_index['request_type'] = $request['registration_cnf_request_type'];
        $sub_qra_request_index['author_id'] = $user->id;
        $sub_qra_request_index['status'] = 'action_requested';
        $sub_qra_request_index->save();

        $qra_request_type_id = $sub_qra_request_index->id;

        $subQraRequestType = new SubQraRequestType();
        $subQraRequestType['id'] = $request['registration_cnf_t_id'];
        $subQraRequestType['author_id'] = $user->id;
        $subQraRequestType['type'] = 'registration_cnf';
        $subQraRequestType['qra_request_type_id'] = $qra_request_type_id;
        $subQraRequestType['version'] = $request['registration_cnf_version'];
        $subQraRequestType['material_number'] = $request['registration_cnf_material_number'];
        $subQraRequestType['vendor_code'] = $request['registration_cnf_vendor_code'];
        $subQraRequestType['vendor_name'] = $request['registration_cnf_vendor_name'];

        $subQraRequestType->save();

        $this->correspondence_add_qra_reqeust_type($qra_request_type_id, 'Registration CNF', $sub_qra_request_index);

        // add qra_request_type_attachments
        if($request->file('registration_cnf_attachment')){
            foreach ($request->file('registration_cnf_attachment') as $file) {
                $attachments = new QraRequestTypeAttachments();

//                $fileName = $file->storeAs('campaigns/'.$request['legal_request_c_id'].'/'.$asset_id, $originalName);
                $fileName = $this->file_exist_check_qra($file, $request['registration_cnf_t_id'], $qra_request_type_id);

                $attachments['task_id'] = $subQraRequestType->id; // task_id
                $attachments['type'] = 'attachment_file_' . $file->getMimeType();
                $attachments['author_id'] = $user->id;
                $attachments['attachment'] = '/' . $fileName;
                $attachments['qra_request_type_id'] = $qra_request_type_id;
                $attachments['file_ext'] = pathinfo($fileName, PATHINFO_EXTENSION);
                $attachments['file_type'] = $file->getMimeType();
                $attachments['file_size'] = $file->getSize();
                $attachments['created_at'] = Carbon::now();
                $attachments->save();

                // insert file attachment correspondence
                $this->add_file_correspondence_for_qra($qra_request_type_id, $subQraRequestType->id, $user, $fileName, 'registration_cnf');
            }
        }

        $project_id = $this->projectTaskIndexRepository->get_project_id_by_task_id($request['registration_cnf_t_id']);

        return redirect('admin/qra_request/'.$project_id.'/edit#'.$qra_request_type_id)
            ->with('success', __('Added the Registration CNF Type : ' . $qra_request_type_id));
    }

    public function edit_registration_cnf(Request $request, $qra_request_type_id)
    {
        $param = $request->all();

        // Permission_check
//        if(!$this->permission_check($param)){
//            return redirect('admin/project/' . $concept_development->id . '/edit#'.$task_id)
//                ->with('error', __('This action is no longer permitted. Please contact an Administrator.'));
//        }

        $project_id = $this->projectTaskIndexRepository->get_project_id_by_task_id($param['registration_cnf_t_id']);
        $subQraRequestType = $this->subQraRequestTypeRepository->findById($qra_request_type_id);

        if($this->subQraRequestTypeRepository->update($qra_request_type_id, $param)){

            $user = auth()->user();
            // insert into campaign note for correspondence
            $this->correspondence_update_qra_request_type('registration_cnf', $param, $subQraRequestType, $user);

            if($request->file('attachment')){
                foreach ($request->file('attachment') as $file) {
                    $attachments = new QraRequestTypeAttachments();

                    $fileName = $this->file_exist_check_qra($file, $subQraRequestType->id, $qra_request_type_id);

                    $attachments['task_id'] = $subQraRequestType->id; // task_id
                    $attachments['type'] = 'attachment_file_' . $file->getMimeType();
                    $attachments['author_id'] = $user->id;
                    $attachments['attachment'] = '/' . $fileName;
                    $attachments['qra_request_type_id'] = $qra_request_type_id;
                    $attachments['file_ext'] = pathinfo($fileName, PATHINFO_EXTENSION);
                    $attachments['file_type'] = $file->getMimeType();
                    $attachments['file_size'] = $file->getSize();
                    $attachments['created_at'] = Carbon::now();
                    $attachments->save();

                    // insert file attachment correspondence
                    $this->add_file_correspondence_for_qra($qra_request_type_id, $subQraRequestType->id, $user, $fileName, 'registration_cnf');
                }
            }

            return redirect('admin/qra_request/'.$project_id.'/edit#'.$qra_request_type_id)
                ->with('success', __('Registration CNF ('.$qra_request_type_id.') - Update Success'));
        }

        return redirect('admin/qra_request/'.$project_id.'/edit#'.$qra_request_type_id)
            ->with('error', __('Update Failed'));
    }

    public function add_registration_cpnp(Request $request){

        $user = auth()->user();

        $sub_qra_request_index = new SubQraRequestIndex();
        $sub_qra_request_index['task_id'] = $request['registration_cpnp_t_id'];
        $sub_qra_request_index['request_type'] = $request['registration_cpnp_request_type'];
        $sub_qra_request_index['author_id'] = $user->id;
        $sub_qra_request_index['status'] = 'action_requested';
        $sub_qra_request_index->save();

        $qra_request_type_id = $sub_qra_request_index->id;

        $subQraRequestType = new SubQraRequestType();
        $subQraRequestType['id'] = $request['registration_cpnp_t_id'];
        $subQraRequestType['author_id'] = $user->id;
        $subQraRequestType['type'] = 'registration_cpnp';
        $subQraRequestType['qra_request_type_id'] = $qra_request_type_id;
        $subQraRequestType['version'] = $request['registration_cpnp_version'];
        $subQraRequestType['material_number'] = $request['registration_cpnp_material_number'];
        $subQraRequestType['vendor_code'] = $request['registration_cpnp_vendor_code'];
        $subQraRequestType['vendor_name'] = $request['registration_cpnp_vendor_name'];

        $subQraRequestType->save();

        $this->correspondence_add_qra_reqeust_type($qra_request_type_id, 'Registration CPNP', $sub_qra_request_index);

        // add qra_request_type_attachments
        if($request->file('registration_cpnp_attachment')){
            foreach ($request->file('registration_cpnp_attachment') as $file) {
                $attachments = new QraRequestTypeAttachments();

//                $fileName = $file->storeAs('campaigns/'.$request['legal_request_c_id'].'/'.$asset_id, $originalName);
                $fileName = $this->file_exist_check_qra($file, $request['registration_cpnp_t_id'], $qra_request_type_id);

                $attachments['task_id'] = $subQraRequestType->id; // task_id
                $attachments['type'] = 'attachment_file_' . $file->getMimeType();
                $attachments['author_id'] = $user->id;
                $attachments['attachment'] = '/' . $fileName;
                $attachments['qra_request_type_id'] = $qra_request_type_id;
                $attachments['file_ext'] = pathinfo($fileName, PATHINFO_EXTENSION);
                $attachments['file_type'] = $file->getMimeType();
                $attachments['file_size'] = $file->getSize();
                $attachments['created_at'] = Carbon::now();
                $attachments->save();

                // insert file attachment correspondence
                $this->add_file_correspondence_for_qra($qra_request_type_id, $subQraRequestType->id, $user, $fileName, 'registration_cpnp');
            }
        }

        $project_id = $this->projectTaskIndexRepository->get_project_id_by_task_id($request['registration_cpnp_t_id']);

        return redirect('admin/qra_request/'.$project_id.'/edit#'.$qra_request_type_id)
            ->with('success', __('Added the Registration CPNP Type : ' . $qra_request_type_id));
    }

    public function edit_registration_cpnp(Request $request, $qra_request_type_id)
    {
        $param = $request->all();

        // Permission_check
//        if(!$this->permission_check($param)){
//            return redirect('admin/project/' . $concept_development->id . '/edit#'.$task_id)
//                ->with('error', __('This action is no longer permitted. Please contact an Administrator.'));
//        }

        $project_id = $this->projectTaskIndexRepository->get_project_id_by_task_id($param['registration_cpnp_t_id']);
        $subQraRequestType = $this->subQraRequestTypeRepository->findById($qra_request_type_id);

        if($this->subQraRequestTypeRepository->update($qra_request_type_id, $param)){

            $user = auth()->user();
            // insert into campaign note for correspondence
            $this->correspondence_update_qra_request_type('registration_cpnp', $param, $subQraRequestType, $user);

            if($request->file('attachment')){
                foreach ($request->file('attachment') as $file) {
                    $attachments = new QraRequestTypeAttachments();

                    $fileName = $this->file_exist_check_qra($file, $subQraRequestType->id, $qra_request_type_id);

                    $attachments['task_id'] = $subQraRequestType->id; // task_id
                    $attachments['type'] = 'attachment_file_' . $file->getMimeType();
                    $attachments['author_id'] = $user->id;
                    $attachments['attachment'] = '/' . $fileName;
                    $attachments['qra_request_type_id'] = $qra_request_type_id;
                    $attachments['file_ext'] = pathinfo($fileName, PATHINFO_EXTENSION);
                    $attachments['file_type'] = $file->getMimeType();
                    $attachments['file_size'] = $file->getSize();
                    $attachments['created_at'] = Carbon::now();
                    $attachments->save();

                    // insert file attachment correspondence
                    $this->add_file_correspondence_for_qra($qra_request_type_id, $subQraRequestType->id, $user, $fileName, 'registration_cpnp');
                }
            }

            return redirect('admin/qra_request/'.$project_id.'/edit#'.$qra_request_type_id)
                ->with('success', __('Registration CPNP ('.$qra_request_type_id.') - Update Success'));
        }

        return redirect('admin/qra_request/'.$project_id.'/edit#'.$qra_request_type_id)
            ->with('error', __('Update Failed'));
    }

    public function add_registration_scpn(Request $request){

        $user = auth()->user();

        $sub_qra_request_index = new SubQraRequestIndex();
        $sub_qra_request_index['task_id'] = $request['registration_scpn_t_id'];
        $sub_qra_request_index['request_type'] = $request['registration_scpn_request_type'];
        $sub_qra_request_index['author_id'] = $user->id;
        $sub_qra_request_index['status'] = 'action_requested';
        $sub_qra_request_index->save();

        $qra_request_type_id = $sub_qra_request_index->id;

        $subQraRequestType = new SubQraRequestType();
        $subQraRequestType['id'] = $request['registration_scpn_t_id'];
        $subQraRequestType['author_id'] = $user->id;
        $subQraRequestType['type'] = 'registration_scpn';
        $subQraRequestType['qra_request_type_id'] = $qra_request_type_id;
        $subQraRequestType['version'] = $request['registration_scpn_version'];
        $subQraRequestType['material_number'] = $request['registration_scpn_material_number'];
        $subQraRequestType['vendor_code'] = $request['registration_scpn_vendor_code'];
        $subQraRequestType['vendor_name'] = $request['registration_scpn_vendor_name'];

        $subQraRequestType->save();

        $this->correspondence_add_qra_reqeust_type($qra_request_type_id, 'Registration SCPN', $sub_qra_request_index);

        // add qra_request_type_attachments
        if($request->file('registration_scpn_attachment')){
            foreach ($request->file('registration_scpn_attachment') as $file) {
                $attachments = new QraRequestTypeAttachments();

//                $fileName = $file->storeAs('campaigns/'.$request['legal_request_c_id'].'/'.$asset_id, $originalName);
                $fileName = $this->file_exist_check_qra($file, $request['registration_scpn_t_id'], $qra_request_type_id);

                $attachments['task_id'] = $subQraRequestType->id; // task_id
                $attachments['type'] = 'attachment_file_' . $file->getMimeType();
                $attachments['author_id'] = $user->id;
                $attachments['attachment'] = '/' . $fileName;
                $attachments['qra_request_type_id'] = $qra_request_type_id;
                $attachments['file_ext'] = pathinfo($fileName, PATHINFO_EXTENSION);
                $attachments['file_type'] = $file->getMimeType();
                $attachments['file_size'] = $file->getSize();
                $attachments['created_at'] = Carbon::now();
                $attachments->save();

                // insert file attachment correspondence
                $this->add_file_correspondence_for_qra($qra_request_type_id, $subQraRequestType->id, $user, $fileName, 'artwork_review');
            }
        }

        $project_id = $this->projectTaskIndexRepository->get_project_id_by_task_id($request['registration_scpn_t_id']);

        return redirect('admin/qra_request/'.$project_id.'/edit#'.$qra_request_type_id)
            ->with('success', __('Added the Registration SCPN Type : ' . $qra_request_type_id));
    }

    public function edit_registration_scpn(Request $request, $qra_request_type_id)
    {
        $param = $request->all();

        // Permission_check
//        if(!$this->permission_check($param)){
//            return redirect('admin/project/' . $concept_development->id . '/edit#'.$task_id)
//                ->with('error', __('This action is no longer permitted. Please contact an Administrator.'));
//        }

        $project_id = $this->projectTaskIndexRepository->get_project_id_by_task_id($param['registration_scpn_t_id']);
        $subQraRequestType = $this->subQraRequestTypeRepository->findById($qra_request_type_id);

        if($this->subQraRequestTypeRepository->update($qra_request_type_id, $param)){

            $user = auth()->user();
            // insert into campaign note for correspondence
            $this->correspondence_update_qra_request_type('registration_scpn', $param, $subQraRequestType, $user);

            if($request->file('attachment')){
                foreach ($request->file('attachment') as $file) {
                    $attachments = new QraRequestTypeAttachments();

                    $fileName = $this->file_exist_check_qra($file, $subQraRequestType->id, $qra_request_type_id);

                    $attachments['task_id'] = $subQraRequestType->id; // task_id
                    $attachments['type'] = 'attachment_file_' . $file->getMimeType();
                    $attachments['author_id'] = $user->id;
                    $attachments['attachment'] = '/' . $fileName;
                    $attachments['qra_request_type_id'] = $qra_request_type_id;
                    $attachments['file_ext'] = pathinfo($fileName, PATHINFO_EXTENSION);
                    $attachments['file_type'] = $file->getMimeType();
                    $attachments['file_size'] = $file->getSize();
                    $attachments['created_at'] = Carbon::now();
                    $attachments->save();

                    // insert file attachment correspondence
                    $this->add_file_correspondence_for_qra($qra_request_type_id, $subQraRequestType->id, $user, $fileName, 'registration_scpn');
                }
            }

            return redirect('admin/qra_request/'.$project_id.'/edit#'.$qra_request_type_id)
                ->with('success', __('Registration SCPN ('.$qra_request_type_id.') - Update Success'));
        }

        return redirect('admin/qra_request/'.$project_id.'/edit#'.$qra_request_type_id)
            ->with('error', __('Update Failed'));
    }

    public function add_registration_iio(Request $request){

        $user = auth()->user();

        $sub_qra_request_index = new SubQraRequestIndex();
        $sub_qra_request_index['task_id'] = $request['registration_iio_t_id'];
        $sub_qra_request_index['request_type'] = $request['registration_iio_request_type'];
        $sub_qra_request_index['author_id'] = $user->id;
        $sub_qra_request_index['status'] = 'action_requested';
        $sub_qra_request_index->save();

        $qra_request_type_id = $sub_qra_request_index->id;

        $subQraRequestType = new SubQraRequestType();
        $subQraRequestType['id'] = $request['registration_iio_t_id'];
        $subQraRequestType['author_id'] = $user->id;
        $subQraRequestType['type'] = 'registration_iio';
        $subQraRequestType['qra_request_type_id'] = $qra_request_type_id;

        $subQraRequestType->save();

        $this->correspondence_add_qra_reqeust_type($qra_request_type_id, 'Registration IIO', $sub_qra_request_index);

        // add qra_request_type_attachments
        if($request->file('registration_iio_attachment')){
            foreach ($request->file('registration_iio_attachment') as $file) {
                $attachments = new QraRequestTypeAttachments();

//                $fileName = $file->storeAs('campaigns/'.$request['legal_request_c_id'].'/'.$asset_id, $originalName);
                $fileName = $this->file_exist_check_qra($file, $request['registration_iio_t_id'], $qra_request_type_id);

                $attachments['task_id'] = $subQraRequestType->id; // task_id
                $attachments['type'] = 'attachment_file_' . $file->getMimeType();
                $attachments['author_id'] = $user->id;
                $attachments['attachment'] = '/' . $fileName;
                $attachments['qra_request_type_id'] = $qra_request_type_id;
                $attachments['file_ext'] = pathinfo($fileName, PATHINFO_EXTENSION);
                $attachments['file_type'] = $file->getMimeType();
                $attachments['file_size'] = $file->getSize();
                $attachments['created_at'] = Carbon::now();
                $attachments->save();

                // insert file attachment correspondence
                $this->add_file_correspondence_for_qra($qra_request_type_id, $subQraRequestType->id, $user, $fileName, 'registration_iio');
            }
        }

        $project_id = $this->projectTaskIndexRepository->get_project_id_by_task_id($request['registration_iio_t_id']);

        return redirect('admin/qra_request/'.$project_id.'/edit#'.$qra_request_type_id)
            ->with('success', __('Added the Registration IIO Type : ' . $qra_request_type_id));
    }

    public function edit_registration_iio(Request $request, $qra_request_type_id)
    {
        $param = $request->all();

        // Permission_check
//        if(!$this->permission_check($param)){
//            return redirect('admin/project/' . $concept_development->id . '/edit#'.$task_id)
//                ->with('error', __('This action is no longer permitted. Please contact an Administrator.'));
//        }

        $project_id = $this->projectTaskIndexRepository->get_project_id_by_task_id($param['registration_iio_t_id']);
        $subQraRequestType = $this->subQraRequestTypeRepository->findById($qra_request_type_id);

        if($this->subQraRequestTypeRepository->update($qra_request_type_id, $param)){

            $user = auth()->user();
            // insert into campaign note for correspondence
            $this->correspondence_update_qra_request_type('registration_iio', $param, $subQraRequestType, $user);

            if($request->file('attachment')){
                foreach ($request->file('attachment') as $file) {
                    $attachments = new QraRequestTypeAttachments();

                    $fileName = $this->file_exist_check_qra($file, $subQraRequestType->id, $qra_request_type_id);

                    $attachments['task_id'] = $subQraRequestType->id; // task_id
                    $attachments['type'] = 'attachment_file_' . $file->getMimeType();
                    $attachments['author_id'] = $user->id;
                    $attachments['attachment'] = '/' . $fileName;
                    $attachments['qra_request_type_id'] = $qra_request_type_id;
                    $attachments['file_ext'] = pathinfo($fileName, PATHINFO_EXTENSION);
                    $attachments['file_type'] = $file->getMimeType();
                    $attachments['file_size'] = $file->getSize();
                    $attachments['created_at'] = Carbon::now();
                    $attachments->save();

                    // insert file attachment correspondence
                    $this->add_file_correspondence_for_qra($qra_request_type_id, $subQraRequestType->id, $user, $fileName, 'registration_iio');
                }
            }

            return redirect('admin/qra_request/'.$project_id.'/edit#'.$qra_request_type_id)
                ->with('success', __('Registration IIO ('.$qra_request_type_id.') - Update Success'));
        }

        return redirect('admin/qra_request/'.$project_id.'/edit#'.$qra_request_type_id)
            ->with('error', __('Update Failed'));
    }

    public function add_registration_mocra(Request $request){

        $user = auth()->user();

        $sub_qra_request_index = new SubQraRequestIndex();
        $sub_qra_request_index['task_id'] = $request['registration_mocra_t_id'];
        $sub_qra_request_index['request_type'] = $request['registration_mocqra_request_type'];
        $sub_qra_request_index['author_id'] = $user->id;
        $sub_qra_request_index['status'] = 'action_requested';
        $sub_qra_request_index->save();

        $qra_request_type_id = $sub_qra_request_index->id;

        $subQraRequestType = new SubQraRequestType();
        $subQraRequestType['id'] = $request['registration_mocra_t_id'];
        $subQraRequestType['author_id'] = $user->id;
        $subQraRequestType['type'] = 'registration_mocra';
        $subQraRequestType['qra_request_type_id'] = $qra_request_type_id;

        $subQraRequestType->save();

        $this->correspondence_add_qra_reqeust_type($qra_request_type_id, 'Registration MoCRA', $sub_qra_request_index);

        // add qra_request_type_attachments
        if($request->file('registration_mocra_attachment')){
            foreach ($request->file('registration_mocra_attachment') as $file) {
                $attachments = new QraRequestTypeAttachments();

//                $fileName = $file->storeAs('campaigns/'.$request['legal_request_c_id'].'/'.$asset_id, $originalName);
                $fileName = $this->file_exist_check_qra($file, $request['registration_mocra_t_id'], $qra_request_type_id);

                $attachments['task_id'] = $subQraRequestType->id; // task_id
                $attachments['type'] = 'attachment_file_' . $file->getMimeType();
                $attachments['author_id'] = $user->id;
                $attachments['attachment'] = '/' . $fileName;
                $attachments['qra_request_type_id'] = $qra_request_type_id;
                $attachments['file_ext'] = pathinfo($fileName, PATHINFO_EXTENSION);
                $attachments['file_type'] = $file->getMimeType();
                $attachments['file_size'] = $file->getSize();
                $attachments['created_at'] = Carbon::now();
                $attachments->save();

                // insert file attachment correspondence
                $this->add_file_correspondence_for_qra($qra_request_type_id, $subQraRequestType->id, $user, $fileName, 'registration_mocra');
            }
        }

        $project_id = $this->projectTaskIndexRepository->get_project_id_by_task_id($request['registration_mocra_t_id']);

        return redirect('admin/qra_request/'.$project_id.'/edit#'.$qra_request_type_id)
            ->with('success', __('Added the Registration MoCRA Type : ' . $qra_request_type_id));
    }

    public function edit_registration_mocra(Request $request, $qra_request_type_id)
    {
        $param = $request->all();

        // Permission_check
//        if(!$this->permission_check($param)){
//            return redirect('admin/project/' . $concept_development->id . '/edit#'.$task_id)
//                ->with('error', __('This action is no longer permitted. Please contact an Administrator.'));
//        }

        $project_id = $this->projectTaskIndexRepository->get_project_id_by_task_id($param['registration_mocra_t_id']);
        $subQraRequestType = $this->subQraRequestTypeRepository->findById($qra_request_type_id);

        if($this->subQraRequestTypeRepository->update($qra_request_type_id, $param)){

            $user = auth()->user();
            // insert into campaign note for correspondence
            $this->correspondence_update_qra_request_type('registration_mocra', $param, $subQraRequestType, $user);

            if($request->file('attachment')){
                foreach ($request->file('attachment') as $file) {
                    $attachments = new QraRequestTypeAttachments();

                    $fileName = $this->file_exist_check_qra($file, $subQraRequestType->id, $qra_request_type_id);

                    $attachments['task_id'] = $subQraRequestType->id; // task_id
                    $attachments['type'] = 'attachment_file_' . $file->getMimeType();
                    $attachments['author_id'] = $user->id;
                    $attachments['attachment'] = '/' . $fileName;
                    $attachments['qra_request_type_id'] = $qra_request_type_id;
                    $attachments['file_ext'] = pathinfo($fileName, PATHINFO_EXTENSION);
                    $attachments['file_type'] = $file->getMimeType();
                    $attachments['file_size'] = $file->getSize();
                    $attachments['created_at'] = Carbon::now();
                    $attachments->save();

                    // insert file attachment correspondence
                    $this->add_file_correspondence_for_qra($qra_request_type_id, $subQraRequestType->id, $user, $fileName, 'registration_mocra');
                }
            }

            return redirect('admin/qra_request/'.$project_id.'/edit#'.$qra_request_type_id)
                ->with('success', __('Registration MoCRA ('.$qra_request_type_id.') - Update Success'));
        }

        return redirect('admin/qra_request/'.$project_id.'/edit#'.$qra_request_type_id)
            ->with('error', __('Update Failed'));
    }

    public function add_document_support(Request $request){

        $user = auth()->user();

        $sub_qra_request_index = new SubQraRequestIndex();
        $sub_qra_request_index['task_id'] = $request['document_support_t_id'];
        $sub_qra_request_index['request_type'] = $request['document_support_request_type'];
        $sub_qra_request_index['author_id'] = $user->id;
        $sub_qra_request_index['status'] = 'action_requested';
        $sub_qra_request_index->save();

        $qra_request_type_id = $sub_qra_request_index->id;

        $subQraRequestType = new SubQraRequestType();
        $subQraRequestType['id'] = $request['document_support_t_id'];
        $subQraRequestType['author_id'] = $user->id;
        $subQraRequestType['type'] = 'document_support';
        $subQraRequestType['qra_request_type_id'] = $qra_request_type_id;

        $subQraRequestType->save();

        $this->correspondence_add_qra_reqeust_type($qra_request_type_id, 'Document Support', $sub_qra_request_index);

        // add qra_request_type_attachments
        if($request->file('document_support_attachment')){
            foreach ($request->file('document_support_attachment') as $file) {
                $attachments = new QraRequestTypeAttachments();

//                $fileName = $file->storeAs('campaigns/'.$request['legal_request_c_id'].'/'.$asset_id, $originalName);
                $fileName = $this->file_exist_check_qra($file, $request['document_support_t_id'], $qra_request_type_id);

                $attachments['task_id'] = $subQraRequestType->id; // task_id
                $attachments['type'] = 'attachment_file_' . $file->getMimeType();
                $attachments['author_id'] = $user->id;
                $attachments['attachment'] = '/' . $fileName;
                $attachments['qra_request_type_id'] = $qra_request_type_id;
                $attachments['file_ext'] = pathinfo($fileName, PATHINFO_EXTENSION);
                $attachments['file_type'] = $file->getMimeType();
                $attachments['file_size'] = $file->getSize();
                $attachments['created_at'] = Carbon::now();
                $attachments->save();

                // insert file attachment correspondence
                $this->add_file_correspondence_for_qra($qra_request_type_id, $subQraRequestType->id, $user, $fileName, 'document_support');
            }
        }

        $project_id = $this->projectTaskIndexRepository->get_project_id_by_task_id($request['document_support_t_id']);

        return redirect('admin/qra_request/'.$project_id.'/edit#'.$qra_request_type_id)
            ->with('success', __('Added the Document Support Type : ' . $qra_request_type_id));
    }

    public function edit_document_support(Request $request, $qra_request_type_id)
    {
        $param = $request->all();

        // Permission_check
//        if(!$this->permission_check($param)){
//            return redirect('admin/project/' . $concept_development->id . '/edit#'.$task_id)
//                ->with('error', __('This action is no longer permitted. Please contact an Administrator.'));
//        }

        $project_id = $this->projectTaskIndexRepository->get_project_id_by_task_id($param['document_support_t_id']);
        $subQraRequestType = $this->subQraRequestTypeRepository->findById($qra_request_type_id);

        if($this->subQraRequestTypeRepository->update($qra_request_type_id, $param)){

            $user = auth()->user();
            // insert into campaign note for correspondence
            $this->correspondence_update_qra_request_type('document_support', $param, $subQraRequestType, $user);

            if($request->file('attachment')){
                foreach ($request->file('attachment') as $file) {
                    $attachments = new QraRequestTypeAttachments();

                    $fileName = $this->file_exist_check_qra($file, $subQraRequestType->id, $qra_request_type_id);

                    $attachments['task_id'] = $subQraRequestType->id; // task_id
                    $attachments['type'] = 'attachment_file_' . $file->getMimeType();
                    $attachments['author_id'] = $user->id;
                    $attachments['attachment'] = '/' . $fileName;
                    $attachments['qra_request_type_id'] = $qra_request_type_id;
                    $attachments['file_ext'] = pathinfo($fileName, PATHINFO_EXTENSION);
                    $attachments['file_type'] = $file->getMimeType();
                    $attachments['file_size'] = $file->getSize();
                    $attachments['created_at'] = Carbon::now();
                    $attachments->save();

                    // insert file attachment correspondence
                    $this->add_file_correspondence_for_qra($qra_request_type_id, $subQraRequestType->id, $user, $fileName, 'document_support');
                }
            }

            return redirect('admin/qra_request/'.$project_id.'/edit#'.$qra_request_type_id)
                ->with('success', __('Document Support ('.$qra_request_type_id.') - Update Success'));
        }

        return redirect('admin/qra_request/'.$project_id.'/edit#'.$qra_request_type_id)
            ->with('error', __('Update Failed'));
    }

    public function file_exist_check_qra($file, $task_id, $qra_request_type_id)
    {
        $originalName = $file->getClientOriginalName();
        $destinationFolder = 'storage/qra_request/'.$task_id.'/'.$qra_request_type_id.'/'.$originalName;

        // If exist same name file, add numberning for version control
        if(file_exists($destinationFolder)){
            if ($pos = strrpos($originalName, '.')) {
                $new_name = substr($originalName, 0, $pos);
                $ext = substr($originalName, $pos);
            }
            $newpath = 'storage/qra_request/'.$task_id.'/'.$qra_request_type_id.'/'.$originalName;
            $uniq_no = 1;
            while (file_exists($newpath)) {
                $tmp_name = $new_name .'_v'. $uniq_no . $ext;
                $newpath = 'storage/qra_request/'.$task_id.'/'.$qra_request_type_id.'/'.$tmp_name;
                $uniq_no++;
            }
            $file_name = $tmp_name;
        }else{
            $file_name = $originalName;
        }

        $fileName =$file->storeAs('qra_request/'.$task_id.'/'.$qra_request_type_id, $file_name);
        return $fileName;
    }

    public function correspondence_add_qra_reqeust_type($qra_request_type_id, $type_name, $sub_qra_request_index)
    {
        $user = auth()->user();
        $type_name = strtoupper($type_name);
        $change_line = "<p>$user->first_name requested <b style='color: #b91d19'>$type_name</b> <b>(#$qra_request_type_id)</b></p>";

        $qra_request_note = new QraRequestNotes();
        $qra_request_note['id'] = $sub_qra_request_index->task_id;
        $qra_request_note['user_id'] = $user->id;
        $qra_request_note['qra_request_type_id'] = $qra_request_type_id;
        $qra_request_note['task_id'] = $sub_qra_request_index->task_id;
        $qra_request_note['project_id'] = 0;
        $qra_request_note['note'] = $change_line;
        $qra_request_note['created_at'] = Carbon::now();
        $qra_request_note->save();
    }

    public function add_file_correspondence_for_qra($qra_request_type_id, $task_id, $user, $file_type, $request_type)
    {
        // Insert into project note for correspondence (attachment file)
        $request_type_ =  strtoupper(str_replace('_', ' ', $request_type));

        $change_line  = "<p>$user->first_name has added a new attachment ($file_type) to <b style='color: #b91d19'>$request_type_</b> <b>(#$qra_request_type_id)</b></p>";

        $qra_request_note = new QraRequestNotes();
        $qra_request_note['id'] = $task_id;
        $qra_request_note['user_id'] = $user->id;
        $qra_request_note['qra_request_type_id'] = $qra_request_type_id;
        $qra_request_note['task_id'] = $task_id;
        $qra_request_note['note'] = $change_line;
        $qra_request_note['created_at'] = Carbon::now();
        $qra_request_note->save();

    }

    public function correspondence_update_qra_request_type($task_type, $new_param, $origin_param, $user)
    {
        // Insert into qra_reqeust_note for correspondence
        $new = $this->get_request_type_param($task_type, $new_param);
        $origin = $origin_param->toArray();
        foreach ($new as $key => $value) {
            if (array_key_exists($key, $origin)) {
                if (html_entity_decode($new[$key]) != html_entity_decode($origin[$key])) {
                    $changed[$key]['new'] = $new[$key];
                    $changed[$key]['original'] = $origin[$key];
                }
            }
        }
        $task_type_ = strtoupper(str_replace('_', ' ', $task_type));
        $change_line  = "<p>$user->first_name made a change to a <b style='color: #b91d19'>$task_type_</b> <b>(#$origin_param->qra_request_type_id)</b></p>";

        if(!empty($changed)){
            foreach ($changed as $label => $change) {

                $label = strtoupper(str_replace('_', ' ', $label));
                $from  = trim($change['original']); // Remove strip tags
                $to    = trim($change['new']);      // Remove strip tags

                $change_line .= "<div class='change_label'><p>$label:</p></div>"
                    . "<div class='change_to'><p>$to</p></div>"
                    . "<div class='change_from'><del><p>$from</p></del></div>";
            }
            $qra_request_note = new QraRequestNotes();
            $qra_request_note['id'] = $origin_param->id; // task_id
            $qra_request_note['user_id'] = $user->id;
            $qra_request_note['qra_request_type_id'] = $origin_param->qra_request_type_id;
            $qra_request_note['task_id'] = $origin_param->id; // task_id
            $qra_request_note['project_id'] = 0;
            $qra_request_note['note'] = $change_line;
            $qra_request_note['created_at'] = Carbon::now();
            $qra_request_note->save();
        }
    }

    public function get_request_type_param($task_type, $data)
    {
        if($task_type == 'formulation_prescreen') {
            $new = array(
                'version' => $data['version'],
                'material_number' => $data['material_number'],
                'vendor_name' => $data['vendor_name'],
                'target_region' => $data['target_region']
            );
            return $new;
        }else if($task_type == 'artwork_contents'){
            $new = array(
                'version' => $data['version'],
                'material_number' => $data['material_number'],
                'vendor_name' => $data['vendor_name'],
                'target_region' => $data['target_region']
            );
            return $new;
        }else if($task_type == 'artwork_review'){
            $new = array(
                'version' => $data['version'],
                'material_number' => $data['material_number'],
                'vendor_name' => $data['vendor_name'],
                'target_region' => $data['target_region']
            );
            return $new;
        }else if($task_type == 'registration_wercs'){
            $new = array(
                'version' => $data['version'],
                'material_number' => $data['material_number'],
                'vendor_name' => $data['vendor_name'],
                'registration' => $data['registration']
            );
            return $new;
        }else if($task_type == 'registration_smarterx'){
            $new = array(
//                'version' => $data['version'],
//                'material_number' => $data['material_number'],
//                'vendor_name' => $data['vendor_name'],
//                'target_region' => $data['target_region']
                'registration' => $data['registration']
            );
            return $new;
        }else if($task_type == 'registration_california'){
            $new = array(
                'version' => $data['version'],
                'material_number' => $data['material_number'],
                'vendor_name' => $data['vendor_name'],
                'registration' => $data['registration']
            );
            return $new;
        }else if($task_type == 'registration_cnf'){
            $new = array(
                'version' => $data['version'],
                'material_number' => $data['material_number'],
                'vendor_name' => $data['vendor_name'],
                'registration' => $data['registration']
            );
            return $new;
        }else if($task_type == 'registration_cpnp'){
            $new = array(
                'version' => $data['version'],
                'material_number' => $data['material_number'],
                'vendor_name' => $data['vendor_name'],
                'registration' => $data['registration']
            );
            return $new;
        }else if($task_type == 'registration_scpn'){
            $new = array(
                'version' => $data['version'],
                'material_number' => $data['material_number'],
                'vendor_name' => $data['vendor_name'],
                'registration' => $data['registration']
            );
            return $new;
        }else if($task_type == 'registration_iio'){
            $new = array(
//                'version' => $data['version'],
//                'material_number' => $data['material_number'],
//                'vendor_name' => $data['vendor_name'],
//                'target_region' => $data['target_region']
                'registration' => $data['registration']
            );
            return $new;
        }else if($task_type == 'registration_mocra'){
            $new = array(
//                'version' => $data['version'],
//                'material_number' => $data['material_number'],
//                'vendor_name' => $data['vendor_name'],
//                'target_region' => $data['target_region']
                'registration' => $data['registration']
            );
            return $new;
        }else if($task_type == 'document_support'){
            $new = array(
//                'version' => $data['version'],
//                'material_number' => $data['material_number'],
//                'vendor_name' => $data['vendor_name'],
//                'target_region' => $data['target_region']
            );
            return $new;
        }

    }

    public function actionInProgress($id)
    {
        $sbu_qra_request_index = $this->subQraRequestIndexRepository->findById($id);
        $param['status'] = 'in_progress';
        $param['updated_at'] = Carbon::now();
        $t_id = $sbu_qra_request_index->task_id;
        $project_id = $this->projectTaskIndexRepository->get_project_id_by_task_id($t_id);
        if($this->subQraRequestIndexRepository->update($id, $param)){
            $this->qra_status_correspondence($t_id, $project_id, $sbu_qra_request_index->request_type, $sbu_qra_request_index->id, 'In Progress');
            echo '/admin/qra_request/'.$project_id.'/edit#'.$id;
        }else{
            echo 'fail';
        }
    }

    public function actionReview($id)
    {
        $sbu_qra_request_index = $this->subQraRequestIndexRepository->findById($id);
        $param['status'] = 'action_review';
        $param['updated_at'] = Carbon::now();
        $t_id = $sbu_qra_request_index->task_id;
        $project_id = $this->projectTaskIndexRepository->get_project_id_by_task_id($t_id);
        if($this->subQraRequestIndexRepository->update($id, $param)){
            $this->qra_status_correspondence($t_id, $project_id, $sbu_qra_request_index->request_type, $sbu_qra_request_index->id, 'Action Review');
            echo '/admin/qra_request/'.$project_id.'/edit#'.$id;
        }else{
            echo 'fail';
        }
    }

    public function actionComplete($id)
    {
        $sbu_qra_request_index = $this->subQraRequestIndexRepository->findById($id);
        $param['status'] = 'action_completed';
        $param['updated_at'] = Carbon::now();
        $t_id = $sbu_qra_request_index->task_id;
        $project_id = $this->projectTaskIndexRepository->get_project_id_by_task_id($t_id);
        if($this->subQraRequestIndexRepository->update($id, $param)){
            $this->qra_status_correspondence($t_id, $project_id, $sbu_qra_request_index->request_type, $sbu_qra_request_index->id, 'Action Completed');
            echo '/admin/qra_request/'.$project_id.'/edit#'.$id;
        }else{
            echo 'fail';
        }
    }

    public function fileRemove($id)
    {
        $attachment_obj = $this->qraRequestTypeFileAttachmentsRepository->findById($id);
        $file_name = $attachment_obj->attachment;
        $task_id = $attachment_obj->task_id;
        $qra_request_type_id = $attachment_obj->qra_request_type_id;
        $user = auth()->user();
        if($attachment_obj->delete()){
            $requestTypeIndex = $this->subQraRequestIndexRepository->findById($qra_request_type_id);
            $request_type =  ucwords(str_replace('_', ' ', $requestTypeIndex->request_type));
            $change_line = "<p>$user->first_name removed a attachment ($file_name) on <b style='color: #b91d19'>$request_type</b> <b>(#$qra_request_type_id)</b></p>";

            $qra_request_note = new QraRequestNotes();
            $qra_request_note['id'] = $task_id; // task_id
            $qra_request_note['user_id'] = $user->id;
            $qra_request_note['qra_request_type_id'] = $qra_request_type_id;
            $qra_request_note['task_id'] = $task_id; // task_id
            $qra_request_note['note'] = $change_line;
            $qra_request_note['created_at'] = Carbon::now();
            $qra_request_note->save();

            echo 'success';
        }else{
            echo 'fail';
        }
    }

    public function requestTypeRemovePermissionCheck($request_type_id){

        $user = auth()->user();
        if($user->role == 'Admin') return true; // admin okay

        $obj = $this->subQraRequestIndexRepository->findById($request_type_id);
        if($obj->author_id != $user->id){
            return false;
        }
        return true;
    }

    public function requestTypeRemove($request_type_id, $type)
    {
        $obj = $this->subQraRequestIndexRepository->findById($request_type_id);
        $t_id = $obj->task_id;
        $p_id = $this->projectTaskIndexRepository->get_project_id_by_task_id($t_id);
        if($this->requestTypeRemovePermissionCheck($request_type_id)){
            // Delete from sub_qra_request_index, sub_qra_request_type tables
            $this->subQraRequestIndexRepository->delete($request_type_id);
            $this->subQraRequestTypeRepository->delete($request_type_id);

            // Add correspondence for asset Removed
            $this->qra_correspondence($t_id, $p_id, $type, $request_type_id, 'Removed the Task ');

            echo '/admin/qra_request/'.$p_id.'/edit';
        }else{
            echo 'fail';
        }
    }

    public function qra_status_correspondence($t_id, $p_id, $task_type, $qra_request_type_id, $status)
    {
        // Insert into Project note for correspondence (attachment file)
        $user = auth()->user();
        $task_type_ =  strtoupper(str_replace('_', ' ', $task_type));
        $change_line  = "<p>$user->first_name updated the status to <b>$status</b> for <b style='color: #b91d19;'>$task_type_ </b><b>(#$qra_request_type_id)</b></p>";

        $note = new QraRequestNotes();
        $note['id'] = $t_id;
        $note['user_id'] = $user->id;
        $note['qra_request_type_id'] = $qra_request_type_id;
        $note['task_id'] = $t_id;
        $note['project_id'] = $p_id;
        $note['note'] = $change_line;
        $note['created_at'] = Carbon::now();
        $note->save();
    }

    public function qra_correspondence($t_id, $p_id, $task_type, $qra_request_type_id, $status)
    {
        // Insert into Project note for correspondence (attachment file)
        $user = auth()->user();
        $task_type_ =  strtoupper(str_replace('_', ' ', $task_type));
        $change_line  = "<p>$user->first_name $status for <b style='color: #b91d19;'>$task_type_ </b><b>(#$qra_request_type_id)</b></p>";

        $note = new QraRequestNotes();
        $note['id'] = $t_id;
        $note['user_id'] = $user->id;
        $note['qra_request_type_id'] = $qra_request_type_id;
        $note['task_id'] = $t_id;
        $note['project_id'] = $p_id;
        $note['note'] = $change_line;
        $note['created_at'] = Carbon::now();
        $note->save();
    }

}
