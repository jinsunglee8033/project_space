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
use App\Models\User;
use App\Repositories\Admin\AssetNotificationUserRepository;
use App\Repositories\Admin\AssetOwnerAssetsRepository;
use App\Repositories\Admin\CampaignAssetIndexRepository;
use App\Repositories\Admin\CampaignNotesRepository;
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
use App\Repositories\Admin\Interfaces\CampaignAssetIndexRepositoryInterface;
use App\Repositories\Admin\PermissionRepository;

use App\Repositories\Admin\Interfaces\CampaignBrandsRepositoryInterface;
use App\Repositories\Admin\Interfaces\CampaignNotesRepositoryInterface;
use App\Repositories\Admin\Interfaces\CampaignRepositoryInterface;
use App\Repositories\Admin\Interfaces\PermissionRepositoryInterface;

use App\Repositories\Admin\UserRepository;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CampaignController extends Controller
{

//    use Authorizable;

    private $permissionRepository;
    private $campaignRepository;
    private $campaignBrandsRepository;
    private $campaignNotesRepository;
    private $campaignTypeAssetAttachmentsRepository;
    private $campaignTypeEmailBlastRepository;
    private $campaignTypeSocialAdRepository;
    private $campaignTypeWebsiteBannersRepository;
    private $campaignTypeWebsiteChangesRepository;
    private $campaignTypeLandingPageRepository;
    private $campaignTypeMiscRepository;
    private $campaignTypeSmsRequestRepository;
    private $campaignTypeTopcategoriesCopyRepository;
    private $campaignTypeProgrammaticBannersRepository;
    private $campaignTypeImageRequestRepository;
    private $campaignTypeRollOverRepository;
    private $campaignTypeStoreFrontRepository;
    private $campaignTypeAContentRepository;
    private $campaignTypeYoutubeCopyRepository;
    private $campaignTypeInfoGraphicRepository;
    private $campaignAssetIndexRepository;
    private $assetOwnerAssetsRepository;
    private $assetNotificationUserRepository;
    private $userRepository;

    public function __construct(CampaignRepository $campaignRepository,
                                CampaignBrandsRepository $campaignBrandsRepository,
                                CampaignNotesRepository $campaignNotesRepository,
                                CampaignTypeAssetAttachmentsRepository $campaignTypeAssetAttachmentsRepository,
                                CampaignTypeEmailBlastRepository $campaignTypeEmailBlastRepository,
                                CampaignTypeSocialAdRepository $campaignTypeSocialAdRepository,
                                CampaignTypeWebsiteBannersRepository $campaignTypeWebsiteBannersRepository,
                                CampaignTypeWebsiteChangesRepository $campaignTypeWebsiteChangesRepository,
                                CampaignTypeLandingPageRepository $campaignTypeLandingPageRepository,
                                CampaignTypeMiscRepository $campaignTypeMiscRepository,
                                CampaignTypeSmsRequestRepository $campaignTypeSmsRequestRepository,
                                CampaignTypeTopcategoriesCopyRepository $campaignTypeTopcategoriesCopyRepository,
                                CampaignTypeProgrammaticBannersRepository $campaignTypeProgrammaticBannersRepository,
                                CampaignTypeImageRequestRepository $campaignTypeImageRequestRepository,
                                CampaignTypeRollOverRepository $campaignTypeRollOverRepository,
                                CampaignTypeStoreFrontRepository $campaignTypeStoreFrontRepository,
                                CampaignTypeAContentRepository $campaignTypeAContentRepository,
                                CampaignTypeYoutubeCopyRepository $campaignTypeYoutubeCopyRepository,
                                CampaignTypeInfoGraphicRepository $campaignTypeInfoGraphicRepository,
                                CampaignAssetIndexRepository $campaignAssetIndexRepository,
                                AssetNotificationUserRepository $assetNotificationUserRepository,
                                AssetOwnerAssetsRepository $assetOwnerAssetsRepository,
                                UserRepository $userRepository,
                                PermissionRepository $permissionRepository)
    {
        parent::__construct();

        $this->campaignRepository = $campaignRepository;
        $this->campaignBrandsRepository = $campaignBrandsRepository;
        $this->campaignNotesRepository = $campaignNotesRepository;
        $this->campaignTypeAssetAttachmentsRepository = $campaignTypeAssetAttachmentsRepository;
        $this->campaignTypeEmailBlastRepository = $campaignTypeEmailBlastRepository;
        $this->campaignTypeSocialAdRepository = $campaignTypeSocialAdRepository;
        $this->campaignTypeWebsiteBannersRepository = $campaignTypeWebsiteBannersRepository;
        $this->campaignTypeWebsiteChangesRepository = $campaignTypeWebsiteChangesRepository;
        $this->campaignTypeLandingPageRepository = $campaignTypeLandingPageRepository;
        $this->campaignTypeMiscRepository = $campaignTypeMiscRepository;
        $this->campaignTypeSmsRequestRepository = $campaignTypeSmsRequestRepository;
        $this->campaignTypeTopcategoriesCopyRepository = $campaignTypeTopcategoriesCopyRepository;
        $this->campaignTypeProgrammaticBannersRepository = $campaignTypeProgrammaticBannersRepository;
        $this->campaignTypeImageRequestRepository = $campaignTypeImageRequestRepository;
        $this->campaignTypeRollOverRepository = $campaignTypeRollOverRepository;
        $this->campaignTypeStoreFrontRepository = $campaignTypeStoreFrontRepository;
        $this->campaignTypeAContentRepository = $campaignTypeAContentRepository;
        $this->campaignTypeYoutubeCopyRepository = $campaignTypeYoutubeCopyRepository;
        $this->campaignTypeInfoGraphicRepository = $campaignTypeInfoGraphicRepository;
        $this->campaignAssetIndexRepository = $campaignAssetIndexRepository;
        $this->assetNotificationUserRepository = $assetNotificationUserRepository;
        $this->assetOwnerAssetsRepository = $assetOwnerAssetsRepository;
        $this->userRepository = $userRepository;
        $this->permissionRepository = $permissionRepository;

    }

    public function index(Request $request)
    {
        $params = $request->all();
        $params['status'] = 'active';
        $this->data['currentAdminMenu'] = 'campaign';

        $options = [
            'per_page' => $this->perPage,
            'order' => [
                'date_created' => 'desc',
            ],
            'filter' => $params,
        ];
        $this->data['filter'] = $params;

        $this->data['brands'] = $this->campaignBrandsRepository->findAll()->pluck('campaign_name', 'id');

        $this->data['campaigns'] = $this->campaignRepository->findAll($options);

        $this->data['brand'] = !empty($params['brand']) ? $params['brand'] : '';
        $this->data['id'] = !empty($params['id']) ? $params['id'] : '';

        return view('admin.campaign.index', $this->data);
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
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->data['currentAdminMenu'] = 'campaign';
        $params['bejour'] = 'no';
        $options = [
            'filter' => $params,
        ];
        $this->data['brands'] = $this->campaignBrandsRepository->findAll($options)->pluck('campaign_name', 'id');
        $this->data['promotions'] = [
            'KDO',
            'Creative',
            'Global Marketing',
            'Omni Channel Sales',
            'AST',
        ];

        $this->data['retailers'] = [
            "ASDA",
            "Boots",
            "Bundi",
            "CVS",
            "Dollar General",
            "eBay",
            "Family Dollar",
            "K-Mart",
            "Kaufland",
            "Kroger",
            "London Drug",
            "Muller",
            "RiteAid",
            "Rossmann",
            "Superdrug",
            "Target",
            "TESCO",
            "ULTA",
            "Wal-mart",
            "Walgreens",
            "Website",
        ];

        $this->data['asset_list'] = $this->campaignRepository->getAssetTypeList();
        $this->data['asset_type'] = null;
        $this->data['campaign_brand'] = null;
        $this->data['promotion'] = null;
        $this->data['campaign'] = null;
        $this->data['retailer'] = null;
        $this->data['author_name'] = null;
        $this->data['kiss_users'] = $this->userRepository->getKissUsers();

        return view('admin.campaign.form', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CampaignRequest $request)
    {

        $params = $request->validated();

        $user = auth()->user();

        $params['author_name'] = $user->first_name;
        $params['author_id'] = $user->id;
        $params['author_team'] = $user->team;
        $params['type'] = 'campaign';
        $params['status'] = 'active';
        $params['date_created'] = Carbon::now();

        if (isset($request['asset_type'])) {
            $params['asset_type'] = implode(', ', $request['asset_type']);
        } else {
            $params['asset_type'] = '';
        }

        $campaign = $this->campaignRepository->create($params);

        if ($campaign) {

            if($request->file('c_attachment')){

                foreach ($request->file('c_attachment') as $file) {

                    // file size check
                    if($file->getSize() > 19500000){
                        return redirect('admin/campaign/create')
                            ->with('error', __('You cannot upload files larger than 20 MB. Use google drive link or https://kissftp.kissusa.com:5001/ to upload files. Please add files location link in ticket description/note.'));
                    }

                    $campaign_type_asset_attachments = new CampaignTypeAssetAttachments();

                    // file check if exist.
                    $originalName = $file->getClientOriginalName();
                    $destinationFolder = 'storage/campaigns/'.$campaign->id.'/'.$originalName;

                    // If exist same name file, add numberning for version control
                    if(file_exists($destinationFolder)){
                        if ($pos = strrpos($originalName, '.')) {
                            $new_name = substr($originalName, 0, $pos);
                            $ext = substr($originalName, $pos);
                        }
                        $newpath = 'storage/campaigns/'.$campaign->id.'/'.$originalName;
                        $uniq_no = 1;
                        while (file_exists($newpath)) {
                            $tmp_name = $new_name .'_'. $uniq_no . $ext;
                            $newpath = 'storage/campaigns/'.$campaign->id.'/'.$tmp_name;
                            $uniq_no++;
                        }
                        $file_name = $tmp_name;
                    }else{
                        $file_name = $originalName;
                    }
                    $fileName =$file->storeAs('campaigns/'.$campaign->id, $file_name);

                    $campaign_type_asset_attachments['id'] = $campaign->id;
                    $campaign_type_asset_attachments['asset_id'] = 0;
                    $campaign_type_asset_attachments['type'] = 'attachment_file_' . $file->getMimeType();
                    $campaign_type_asset_attachments['author_id'] = $user->id;
                    $campaign_type_asset_attachments['attachment'] = '/' . $fileName;
                    $campaign_type_asset_attachments['file_ext'] = $file->getExtension();
                    $campaign_type_asset_attachments['file_type'] = $file->getMimeType();
                    $campaign_type_asset_attachments['file_size'] = $file->getSize();
                    $campaign_type_asset_attachments['date_created'] = $file->getSize();
                    $campaign_type_asset_attachments['date_created'] = Carbon::now();

                    $campaign_type_asset_attachments->save();
                }
            }

            // send notification to Frank and Mo when new project
            $notify = new NotifyController();
            $notify->new_project($campaign);

            // send notifications to asset owners (removed 12/20/2023)
//            $asset_note = '';
//            if(isset($params['asset_type'])){
//                $asset_type_array = explode(', ', $params['asset_type']);
//                foreach ($asset_type_array as $asset){
//                    $rs = $this->assetOwnerAssetsRepository->getByAssetName($asset);
//                    if(isset($rs)){
//                        foreach ($rs as $raw){
//                            $brand_name_obj = $this->campaignBrandsRepository->getBrandNameById($params['campaign_brand']);
//                            $brand_name = $brand_name_obj[0]['field_name'];
//                            $user_id = $raw->$brand_name;
//                            $asset_name = $raw->asset_name;
//                            if($user_id != "N/A") {
//                                $asset_owner_user_obj = $this->userRepository->findById($user_id);
//                                if (isset($asset_owner_user_obj)) {
//                                    $notify->new_project_asset_owners($asset_owner_user_obj, $campaign, $asset_name);
//                                    $asset_owner_first_name = $asset_owner_user_obj['first_name'] . " " . $asset_owner_user_obj['last_name'];
//                                    $asset_note .= "<p>$asset_owner_first_name - Please create <b>$asset_name</b></p>";
//                                }
//                            }
//                        }
//                    }
//                }
//            }

            // Correspondence
            $asset_note = '';
            $campaign_note = new CampaignNotes();
            $campaign_note['id'] = $campaign->id;
            $campaign_note['user_id'] = $params['author_id'];
            $campaign_note['asset_id'] = NULL;
            $campaign_note['type'] = 'campaign';
            $campaign_note['note'] = $params['author_name'] . " Created a new Project";
            $campaign_note['note'] .= $asset_note;
            $campaign_note['date_created'] = Carbon::now();

            $campaign_note->save();

            return redirect('admin/campaign/'.$campaign->id.'/edit')
                ->with('success', __('New Project has been created. ID : ' . $campaign->id));
        }

        return redirect('admin/campaign/create')
            ->with('error', __('The campaign could not be saved.'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->data['currentAdminMenu'] = 'campaign';
        $params['bejour'] = 'no';
        $options = [
            'filter' => $params,
        ];
        $this->data['brands'] = $this->campaignBrandsRepository->findAll($options)->pluck('campaign_name', 'id');

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
        $this->data['attach_files'] = $this->campaignTypeAssetAttachmentsRepository->findAll($options);

        // Campaign_item
        $campaign = $this->campaignRepository->findById($id);

        $this->data['campaign'] = $campaign;
        $this->data['campaign_brand'] = $campaign->campaign_brand;
        $this->data['promotions'] = [
            'KDO',
            'Creative',
            'Global Marketing',
            'Omni Channel Sales',
            'AST',
        ];

        $this->data['retailers'] = [
            "ASDA",
            "Boots",
            "Bundi",
            "CVS",
            "Dollar General",
            "eBay",
            "Family Dollar",
            "K-Mart",
            "Kaufland",
            "Kroger",
            "London Drug",
            "Muller",
            "RiteAid",
            "Rossmann",
            "Superdrug",
            "Target",
            "TESCO",
            "ULTA",
            "Wal-mart",
            "Walgreens",
            "Website",
        ];

        $this->data['asset_list'] = $this->campaignRepository->getAssetTypeList();
//        $this->data['asset_type'] = $campaign->asset_type;

        $this->data['promotion'] = $campaign->promotion;
        $this->data['assignee'] = $campaign->assignee;
        $this->data['retailer'] = $campaign->retailer;

        $author_obj = User::find($campaign->author_id);

        if($author_obj){
            $this->data['author_name'] = $author_obj['first_name'] . " " . $author_obj['last_name'];
        }else{
            $this->data['author_name'] = 'N/A';
        }

        $this->data['team_to_list'] = [
            "creative",
            "content",
            "web production"
        ];

        $this->data['assignees_creative'] = $this->userRepository->getCreativeAssignee();
        $this->data['assignees_content'] = $this->userRepository->getContentAssignee();
        $this->data['assignees_web'] = $this->userRepository->getWebAssignee();

        $this->data['copy_writers'] = $this->userRepository->getCopyWriterAssignee();

        $this->data['kiss_users'] = $this->userRepository->getKissUsers();

        // Campaign_assets
        $this->data['assets'] = $assets_list = $this->campaignRepository->getAssetListById($id);

        // Campaign_asset_detail
        if(sizeof($assets_list)>0){
            foreach ($assets_list as $k => $asset){
                $c_id = $asset->c_id;
                $a_id = $asset->a_id;
                $a_type = $asset->a_type;
                $asset_detail = $this->campaignRepository->get_asset_detail($a_id, $c_id, $a_type);
                $assets_list[$k]->detail = $asset_detail;
                $asset_files = $this->campaignTypeAssetAttachmentsRepository->findAllByAssetId($a_id);
                $assets_list[$k]->files = $asset_files;
                $asset_notification_user = $this->assetNotificationUserRepository->getListByAssetId($a_id);
                $assets_list[$k]->asset_notification_user = $asset_notification_user;
            }
        }

        // social_ad, website_banners_fileds
        $this->data['social_ad_fields'] = [
            "FB/IG Video Ad",
            "FB/IG GIF Ad",
            "FB/IG Image Ad",
            "FB/IG Carousel Ad",
            "TT Video Ad",
            "SC Video Ad",
            "Other Ad",
            "FB/IG Video Trfc Ad",
            "FB/IG GIF Trfc Ad",
            "FB/IG Image Trfc Ad",
            "FB/IG Carousel Trfc Ad",
            "TT Video Trfc Ad",
            "SC Video Trfc Ad",
            "Other Trfc Ad",
            "FB/IG Video Conv Ad",
            "FB/IG Stories Conv Ad",
            "FB/IG GIF Conv Ad",
            "FB/IG Image Conv Ad",
            "FB/IG Carousel Conv Ad",
            "FB/IG Catalog Conv Ad",
            "TT Video Conv Ad",
            "SC Video Conv Ad",
            "Other Conv Ad",
            "FB/IG Video Organ Post",
            "FB/IG Stories Organ Post",
            "IG Reels Organ Post",
            "FB/IG GIF Organ Post",
            "FB/IG Image Organ Post",
            "FB/IG Organ Grid",
            "TT Video Organ Post",
            "SC Video Organ Post",
            "Other Organ Post",
            "FB Cover Image",
            "YT Cover Image",
            "TW Cover Image",
            "PIN Cover Image",
            'FB/IG Organic Awareness Post',
            'FB/IG Organic Stories Image Ad',
            'FB/IG Organic Stories Video Ad',
            'Video 16 x 9',
            'Still Image 1 x 1',
            'Still image 4 x 5',
            'Still image 1 x 1.91',
            'Logo'
        ];

        // social_ad, website_banners_fileds
        $this->data['programmatic_banners_fields'] = [
            'Display Ad',
            'Native Ad',
            'CTV'
        ];

        $this->data['banner'] = [
            'Homepage Main Banners',
            'Homepage Portal Banners',
            'Category Ad',
            'Pop up Banners',
            'Retailer Banners'
        ];

        // Campaign_notes
        $options = [
            'id' => $id,
            'order' => [
                'date_created' => 'desc',
            ]
        ];
        $correspondences = $this->campaignNotesRepository->findAll($options);
        $this->data['correspondences'] = $correspondences;

        return view('admin.campaign.form', $this->data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CampaignRequest $request, $id)
    {
        $this->data['currentAdminMenu'] = 'campaign';
        $campaign = $this->campaignRepository->findById($id);
        $user = auth()->user();
//        ddd($campaign);

        // Insert into campaign note for correspondence
        $data = $request->request->all();

//        if (isset($data['asset_type'])) {
//            $data['asset_type'] = implode(', ', $data['asset_type']);
//        } else {
//            $data['asset_type'] = '';
//        }

        $new = array(
            'id'                => $data['id'],
            'name'              => $data['name'],
            'date_from'         => date('Y-m-d', strtotime($data['date_from'])),
//            'date_to'           => date('Y-m-d', strtotime($data['date_to'])),
            'primary_message'   => $data['primary_message'],
            'products_featured' => $data['products_featured'],
            'secondary_message' => $data['secondary_message'],
//            'asset_type'        => $data['asset_type'],
            'campaign_notes'    => $data['campaign_notes'],
        );
//        ddd(htmlspecialchars_decode($data['campaign_notes']));
        $origin = $campaign->toArray();
        foreach ($new as $key => $value) {
            if (array_key_exists($key, $origin)) {
                if (html_entity_decode($new[$key]) != html_entity_decode($origin[$key])) {
                    $changed[$key]['new'] = $new[$key];
                    $changed[$key]['original'] = $origin[$key];
                }
            }
        }
        $change_line  = "<p>$user->first_name made a change to a campaign</p>";
        if(!empty($changed)){
            foreach ($changed as $label => $change) {

                $label = strtoupper(str_replace('_', ' ', $label));
                $from  = trim($change['original']); // Remove strip tags
                $to    = trim($change['new']);      // Remove strip tags

                $change_line .= "<div class='change_label'><p>$label:</p></div>"
                    . "<div class='change_to'><p>$to</p></div>"
                    . "<div class='change_from'><del><p>$from</p></del></div>";
            }
            $campaign_note = new CampaignNotes();
            $campaign_note['id'] = $campaign->id;
            $campaign_note['user_id'] = $user->id;
            $campaign_note['asset_id'] = NULL;
            $campaign_note['type'] = 'campaign';
            $campaign_note['note'] = $change_line;
            $campaign_note['date_created'] = Carbon::now();
            $campaign_note->save();
        }

        if ($this->campaignRepository->update($id, $data)) {
            if($request->file('c_attachment')){
                foreach ($request->file('c_attachment') as $file) {
                    $campaign_type_asset_attachments = new CampaignTypeAssetAttachments();

                    // file size check
                    if($file->getSize() > 20000000){
                        return redirect('admin/campaign/'.$id.'/edit')
                            ->with('error', __('You cannot upload files larger than 20 MB. Use google drive link or https://kissftp.kissusa.com:5001/ to upload files. Please add files location link in ticket description/note.'));
                    }

                    // file check if exist.
                    $originalName = $file->getClientOriginalName();
                    $destinationFolder = 'storage/campaigns/'.$campaign->id.'/'.$originalName;

                    // If exist same name file, add numberning for version control
                    if(file_exists($destinationFolder)){
                        if ($pos = strrpos($originalName, '.')) {
                            $new_name = substr($originalName, 0, $pos);
                            $ext = substr($originalName, $pos);
                        }
                        $newpath = 'storage/campaigns/'.$campaign->id.'/'.$originalName;
                        $uniq_no = 1;
                        while (file_exists($newpath)) {
                            $tmp_name = $new_name .'_'. $uniq_no . $ext;
                            $newpath = 'storage/campaigns/'.$campaign->id.'/'.$tmp_name;
                            $uniq_no++;
                        }
                        $file_name = $tmp_name;
                    }else{
                        $file_name = $originalName;
                    }
                    $fileName =$file->storeAs('campaigns/'.$campaign->id, $file_name);

                    $campaign_type_asset_attachments['id'] = $campaign->id;
                    $campaign_type_asset_attachments['asset_id'] = 0;
                    $campaign_type_asset_attachments['type'] = 'attachment_file_' . $file->getMimeType();
                    $campaign_type_asset_attachments['author_id'] = $user->id;
                    $campaign_type_asset_attachments['attachment'] = '/' . $fileName;
                    $campaign_type_asset_attachments['file_ext'] = pathinfo($fileName, PATHINFO_EXTENSION);
                    $campaign_type_asset_attachments['file_type'] = $file->getMimeType();
                    $campaign_type_asset_attachments['file_size'] = $file->getSize();
                    $campaign_type_asset_attachments['date_created'] = Carbon::now();
                    $campaign_type_asset_attachments->save();

                    // insert file attachment correspondence
                    $this->add_file_correspondence($campaign, $user, $file->getMimeType(), $originalName);
                }
            }
            return redirect('admin/campaign/'.$id.'/edit')
                ->with('success', __('Update Success'));
        }
        return redirect('admin/campaign/'.$id.'/edit')
            ->with('error', __('Update was failed'));
    }

    public function add_file_correspondence($campaign, $user, $file_type, $originalName)
    {
        // Insert into campaign note for correspondence (attachment file)
        $change_line  = "<p>$user->first_name add a file $originalName ($file_type) to project</p>";

        $campaign_note = new CampaignNotes();
        $campaign_note['id'] = $campaign->id;
        $campaign_note['user_id'] = $user->id;
        $campaign_note['asset_id'] = 0;
        $campaign_note['type'] = 'campaign';
        $campaign_note['note'] = $change_line;
        $campaign_note['date_created'] = Carbon::now();
        $campaign_note->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->data['currentAdminMenu'] = 'campaign';
        $campaign = $this->campaignRepository->findById($id);

        $data['status'] = 'deleted';

        if ($this->campaignRepository->update($id, $data)) {
            return redirect('admin/campaign')
                ->with('success', __('Removed the Campaign : ', ['first_name' => $campaign->name]));
        }
        return redirect('admin/campaign')
            ->with('error', __('Fail to delete : ', ['first_name' => $campaign->name]));
    }

    public function fileRemove($id)
    {
        $campaignTypeAssetAttachment = $this->campaignTypeAssetAttachmentsRepository->findById($id);

        $file_type = $campaignTypeAssetAttachment->file_type;
        $campaign_id = $campaignTypeAssetAttachment->id;
        $asset_id = $campaignTypeAssetAttachment->asset_id;

        $user = auth()->user();

        if($campaignTypeAssetAttachment->delete()){

            if($asset_id != 0){

                $assetIndex = $this->campaignAssetIndexRepository->findById($asset_id);
                $asset_type =  strtoupper(str_replace('_', ' ', $assetIndex->type));

                $change_line = "<p>$user->first_name removed a attachment ($file_type) on $asset_type (#$asset_id)</p>";
                $campaign_note['type'] = $assetIndex->type;
            }else {
                $change_line = "<p>$user->first_name removed a attachment ($file_type) on campaign</p>";
                $campaign_note['type'] = 'campaign';
            }

            if ($campaignTypeAssetAttachment->type != 'qr_code') {
                $campaign_note = new CampaignNotes();
                $campaign_note['id'] = $campaign_id;
                $campaign_note['user_id'] = $user->id;
                $campaign_note['asset_id'] = $asset_id;
                $campaign_note['note'] = $change_line;
                $campaign_note['date_created'] = Carbon::now();
                $campaign_note->save();
            }
            echo 'success';
        }else{
            echo 'fail';
        }
    }

    public function assetRemovePermissionCheck($a_id, $c_id, $type)
    {
        $user = auth()->user();

        if( ($user->role == 'admin') || ($user->role == 'creative director') ) return true; // admin okay

//        $c_obj = $this->campaignRepository->findById($c_id);
//        if($user->id != $c_obj->author_id){ // project author check
//            return false;
//        }


        // asset creator check
        if($type == 'email_blast'){
            $rs = $this->campaignTypeEmailBlastRepository->findAllByAssetId($a_id);
            if(!empty($rs[0])) {
                if($user->id != $rs[0]->author_id){
                    return false;
                }
            }
        }else if($type == 'social_ad'){
            $rs = $this->campaignTypeSocialAdRepository->findAllByAssetId($a_id);
            if(!empty($rs[0])) {
                if($user->id != $rs[0]->author_id){
                    return false;
                }
            }
        }else if($type == 'website_banners'){
            $rs = $this->campaignTypeWebsiteBannersRepository->findAllByAssetId($a_id);
            if(!empty($rs[0])) {
                if($user->id != $rs[0]->author_id){
                    return false;
                }
            }
        }else if($type == 'website_changes'){
            $rs = $this->campaignTypeWebsiteChangesRepository->findAllByAssetId($a_id);
            if(!empty($rs[0])) {
                if($user->id != $rs[0]->author_id){
                    return false;
                }
            }
        }else if($type == 'landing_page'){
            $rs = $this->campaignTypeLandingPageRepository->findAllByAssetId($a_id);
            if(!empty($rs[0])) {
                if($user->id != $rs[0]->author_id){
                    return false;
                }
            }
        }else if($type == 'misc'){
            $rs = $this->campaignTypeMiscRepository->findAllByAssetId($a_id);
            if(!empty($rs[0])) {
                if($user->id != $rs[0]->author_id){
                    return false;
                }
            }
        }else if($type == 'sms_request'){
            $rs = $this->campaignTypeSmsRequestRepository->findAllByAssetId($a_id);
            if(!empty($rs[0])) {
                if($user->id != $rs[0]->author_id){
                    return false;
                }
            }
        }else if($type == 'topcategories_copy'){
            $rs = $this->campaignTypeTopcategoriesCopyRepository->findAllByAssetId($a_id);
            if(!empty($rs[0])) {
                if($user->id != $rs[0]->author_id){
                    return false;
                }
            }
        }else if($type == 'programmatic_banners'){
            $rs = $this->campaignTypeProgrammaticBannersRepository->findAllByAssetId($a_id);
            if(!empty($rs[0])) {
                if($user->id != $rs[0]->author_id){
                    return false;
                }
            }
        }else if($type == 'image_request'){
            $rs = $this->campaignTypeImageRequestRepository->findAllByAssetId($a_id);
            if(!empty($rs[0])) {
                if($user->id != $rs[0]->author_id){
                    return false;
                }
            }
        }else if($type == 'roll_over'){
            $rs = $this->campaignTypeRollOverRepository->findAllByAssetId($a_id);
            if(!empty($rs[0])) {
                if($user->id != $rs[0]->author_id){
                    return false;
                }
            }
        }else if($type == 'store_front'){
            $rs = $this->campaignTypeStoreFrontRepository->findAllByAssetId($a_id);
            if(!empty($rs[0])) {
                if($user->id != $rs[0]->author_id){
                    return false;
                }
            }
        }else if($type == 'a_content'){
            $rs = $this->campaignTypeAContentRepository->findAllByAssetId($a_id);
            if(!empty($rs[0])) {
                if($user->id != $rs[0]->author_id){
                    return false;
                }
            }
        }else if($type == 'youtube_copy'){
            $rs = $this->campaignTypeYoutubeCopyRepository->findAllByAssetId($a_id);
            if(!empty($rs[0])) {
                if($user->id != $rs[0]->author_id){
                    return false;
                }
            }
        }else if($type == 'info_graphic'){
            $rs = $this->campaignTypeInfoGraphicRepository->findAllByAssetId($a_id);
            if(!empty($rs[0])) {
                if($user->id != $rs[0]->author_id){
                    return false;
                }
            }
        }
        return true;
    }
    public function assetRemove($a_id, $type)
    {
        $obj = $this->campaignAssetIndexRepository->findById($a_id);
        $c_id = $obj->campaign_id;

        if($this->assetRemovePermissionCheck($a_id, $c_id, $type)){

            // Add correspondence for asset Removed
            $this->add_asset_correspondence($obj->campaign_id, $type, $a_id, 'Removed the Asset ');

            // Delete from campaignAssetIndex table
            $this->campaignAssetIndexRepository->delete($a_id);

            if($type == 'email_blast'){
                if($this->campaignTypeEmailBlastRepository->deleteByAssetId($a_id)){
                    echo '/admin/campaign/'.$c_id.'/edit#'.$a_id;
                }else{
                    echo 'fail';
                }
            }else if($type == 'social_ad'){
                if($this->campaignTypeSocialAdRepository->deleteByAssetId($a_id)){
                    echo '/admin/campaign/'.$c_id.'/edit#'.$a_id;
                }else{
                    echo 'fail';
                }
            }else if($type == 'website_banners'){
                if($this->campaignTypeWebsiteBannersRepository->deleteByAssetId($a_id)){
                    echo '/admin/campaign/'.$c_id.'/edit#'.$a_id;
                }else{
                    echo 'fail';
                }
            }else if($type == 'website_changes'){
                if($this->campaignTypeWebsiteChangesRepository->deleteByAssetId($a_id)){
                    echo '/admin/campaign/'.$c_id.'/edit#'.$a_id;
                }else{
                    echo 'fail';
                }
            }else if($type == 'landing_page'){
                if($this->campaignTypeLandingPageRepository->deleteByAssetId($a_id)){
                    echo '/admin/campaign/'.$c_id.'/edit#'.$a_id;
                }else{
                    echo 'fail';
                }
            }else if($type == 'misc'){
                if($this->campaignTypeMiscRepository->deleteByAssetId($a_id)){
                    echo '/admin/campaign/'.$c_id.'/edit#'.$a_id;
                }else{
                    echo 'fail';
                }
            }else if($type == 'sms_request'){
                if($this->campaignTypeSmsRequestRepository->deleteByAssetId($a_id)){
                    echo '/admin/campaign/'.$c_id.'/edit#'.$a_id;
                }else{
                    echo 'fail';
                }
            }else if($type == 'topcategories_copy'){
                if($this->campaignTypeTopcategoriesCopyRepository->deleteByAssetId($a_id)){
                    echo '/admin/campaign/'.$c_id.'/edit#'.$a_id;
                }else{
                    echo 'fail';
                }
            }else if($type == 'programmatic_banners'){
                if($this->campaignTypeProgrammaticBannersRepository->deleteByAssetId($a_id)){
                    echo '/admin/campaign/'.$c_id.'/edit#'.$a_id;
                }else{
                    echo 'fail';
                }
            }else if($type == 'image_request'){
                if($this->campaignTypeImageRequestRepository->deletebyAssetId($a_id)){
                    echo '/admin/campaign/'.$c_id.'/edit#'.$a_id;
                }else{
                    echo 'fail';
                }
            }else if($type == 'roll_over'){
                if($this->campaignTypeRollOverRepository->deletebyAssetId($a_id)){
                    echo '/admin/campaign/'.$c_id.'/edit#'.$a_id;
                }else{
                    echo 'fail';
                }
            }else if($type == 'store_front'){
                if($this->campaignTypeStoreFrontRepository->deletebyAssetId($a_id)){
                    echo '/admin/campaign/'.$c_id.'/edit#'.$a_id;
                }else{
                    echo 'fail';
                }
            }else if($type == 'a_content'){
                if($this->campaignTypeAContentRepository->deletebyAssetId($a_id)){
                    echo '/admin/campaign/'.$c_id.'/edit#'.$a_id;
                }else{
                    echo 'fail';
                }
            }else if($type == 'youtube_copy'){
                if($this->campaignTypeYoutubeCopyRepository->deletebyAssetId($a_id)){
                    echo '/admin/campaign/'.$c_id.'/edit#'.$a_id;
                }else{
                    echo 'fail';
                }
            }else if($type == 'info_graphic'){
                if($this->campaignTypeInfoGraphicRepository->deletebyAssetId($a_id)){
                    echo '/admin/campaign/'.$c_id.'/edit#'.$a_id;
                }else{
                    echo 'fail';
                }
            }

        }else{
            echo 'fail';
        }

    }


    public function campaignRemove($c_id)
    {

        $user = auth()->user();
        $c_obj = $this->campaignRepository->findById($c_id);
        $a_id = $c_obj->author_id;

        if( ($user->id == $a_id) || ($user->role == 'admin') || ($user->role == 'creative director') ){

//            $this->campaignAssetIndexRepository->deleteByCampaignId($c_id);

            $data['status'] = 'deleted';

            if($this->campaignRepository->update($c_id, $data)){
                // add history... for deleted project!!
                $campaign_note = new CampaignNotes();
                $campaign_note['id'] = $c_id;
                $user = auth()->user();
                $campaign_note['user_id'] = $user->id;
                $campaign_note['type'] = 'campaign';
                $campaign_note['note'] = $user->first_name . " Sent this Project to Deleted";
                $campaign_note['date_created'] = Carbon::now();
                $campaign_note->save();

                echo 'success';
            }else{
                echo 'fail';
            }
        }else{
            echo 'You do not have permission to remove this request';
        }

    }

    public function file_exist_check($file, $project_id, $asset_id)
    {
        $originalName = $file->getClientOriginalName();
        $destinationFolder = 'storage/campaigns/'.$project_id.'/'.$asset_id.'/'.$originalName;

        // If exist same name file, add numberning for version control
        if(file_exists($destinationFolder)){
            if ($pos = strrpos($originalName, '.')) {
                $new_name = substr($originalName, 0, $pos);
                $ext = substr($originalName, $pos);
            }
            $newpath = 'storage/campaigns/'.$project_id.'/'.$asset_id.'/'.$originalName;
            $uniq_no = 1;
            while (file_exists($newpath)) {
                $tmp_name = $new_name .'_v'. $uniq_no . $ext;
                $newpath = 'storage/campaigns/'.$project_id.'/'.$asset_id.'/'.$tmp_name;
                $uniq_no++;
            }
            $file_name = $tmp_name;
        }else{
            $file_name = $originalName;
        }

        $fileName =$file->storeAs('campaigns/'.$project_id.'/'.$asset_id, $file_name);
        return $fileName;
    }

    public function add_email_blast(AssetEmailBlastRequest $request)
    {

        // add Campaign_asset_index
        $campaignAssetIndex = new CampaignAssetIndex();
        $campaignAssetIndex['campaign_id'] = $request['email_blast_c_id'];
        $campaignAssetIndex['type'] = $request['email_blast_asset_type'];
        $campaignAssetIndex['team_to'] = $request['email_blast_team_to'];

        if(isset($request['email_blast_no_copy_necessary']) && $request['email_blast_no_copy_necessary'] =='on'){
            $campaignAssetIndex['status'] = 'copy_complete';
        }else {
            $campaignAssetIndex['status'] = 'copy_requested';
        }
        $user = auth()->user(); // asset_author_id
        $campaignAssetIndex['author_id'] = $user->id;
        $campaignAssetIndex->save();
        $asset_id = $campaignAssetIndex->id;

        // add campaign_type_email_blast
        $campaignTypeEmailBlast = new CampaignTypeEmailBlast();
        $campaignTypeEmailBlast['id'] = $request['email_blast_c_id']; //campaing_id
        $campaignTypeEmailBlast['author_id'] = $request['email_blast_author_id'];
        $campaignTypeEmailBlast['type'] = $request['email_blast_asset_type'];
        $campaignTypeEmailBlast['concept'] = $request['email_blast_concept'];
        $campaignTypeEmailBlast['products_featured'] = $request['email_blast_products_featured'];
        $campaignTypeEmailBlast['main_subject_line'] = $request['email_blast_main_subject_line'];
        $campaignTypeEmailBlast['no_copy_necessary'] = $request['email_blast_no_copy_necessary'];
        $campaignTypeEmailBlast['main_preheader_line'] = $request['email_blast_main_preheader_line'];
        $campaignTypeEmailBlast['alt_subject_line'] = $request['email_blast_alt_subject_line'];
        $campaignTypeEmailBlast['alt_preheader_line'] = $request['email_blast_alt_preheader_line'];
        $campaignTypeEmailBlast['body_copy'] = trim($request['email_blast_body_copy']);
        $campaignTypeEmailBlast['secondary_message'] = $request['email_blast_secondary_message'];
        $campaignTypeEmailBlast['click_through_links'] = $request['email_blast_click_through_links'];
        if (isset($request['email_blast_email_list'])) {
            $request['email_blast_email_list'] = implode(', ', $request['email_blast_email_list']);
        } else {
            $request['email_blast_email_list'] = '';
        }
        $campaignTypeEmailBlast['email_list'] = $request['email_blast_email_list'];
        $campaignTypeEmailBlast['email_blast_date'] = $request['email_blast_email_blast_date'];
        $campaignTypeEmailBlast['video_link'] = $request['email_blast_video_link'];
        $campaignTypeEmailBlast['final_email_proof'] = $request['email_blast_final_email_proof'];
        $campaignTypeEmailBlast['date_created'] = Carbon::now();
        $campaignTypeEmailBlast['asset_id'] = $asset_id;
        $campaignTypeEmailBlast->save();

        // insert note for adding asset
//         $this->add_asset_correspondence($campaignAssetIndex['campaign_id'], $campaignAssetIndex['type'], $asset_id, ucwords(str_replace('_', ' ', $campaignAssetIndex['status'])));

        // new correspondence when adding asset
        $this->new_add_asset_correspondence($campaignAssetIndex['campaign_id'], 'Email Blast', $campaignAssetIndex);

        // add campaign_type_asset_attachments
        if($request->file('email_blast_c_attachment')){
            foreach ($request->file('email_blast_c_attachment') as $file) {
                $campaign_type_asset_attachments = new CampaignTypeAssetAttachments();

//                $fileName = $file->storeAs('campaigns/'.$request['email_blast_c_id'].'/'.$asset_id, $originalName);
                $fileName = $this->file_exist_check($file, $request['email_blast_c_id'], $asset_id);

                $campaign_type_asset_attachments['id'] = $request['email_blast_c_id'];
                $campaign_type_asset_attachments['asset_id'] = $asset_id;
                $campaign_type_asset_attachments['type'] = 'attachment_file_' . $file->getMimeType();
                $campaign_type_asset_attachments['author_id'] = $request['email_blast_author_id'];
                $campaign_type_asset_attachments['attachment'] = '/' . $fileName;
                $campaign_type_asset_attachments['file_ext'] = pathinfo($fileName, PATHINFO_EXTENSION);
                $campaign_type_asset_attachments['file_type'] = $file->getMimeType();
                $campaign_type_asset_attachments['file_size'] = $file->getSize();
                $campaign_type_asset_attachments['date_created'] = Carbon::now();
                $campaign_type_asset_attachments->save();
            }
        }

        // TODO notification
        // Send notification to copywriter(brand check) via email
        // Do action - copy request
        if($campaignAssetIndex['status'] == 'copy_requested') {
            $notify = new NotifyController();
            $notify->copy_request($request['email_blast_c_id'], $asset_id);
        } else if($campaignAssetIndex['status'] == 'copy_complete') {
            $notify = new NotifyController();
            $notify->copy_complete($request['email_blast_c_id'], $asset_id);
        }
        ///////////////////////////////////////////////////////////////

        return redirect('admin/campaign/'.$request['email_blast_c_id'].'/edit#'.$asset_id)
            ->with('success', __('Added the Email Blast Asset : ' . $asset_id));
    }


    public function edit_email_blast(Request $request, $asset_id)
    {
        $email_blast = $this->campaignTypeEmailBlastRepository->findById($asset_id);

        $param = $request->request->all();

        // Permission_check
        if(!$this->permission_check($param)){
            return redirect('admin/campaign/' . $email_blast->id . '/edit#'.$asset_id)
                ->with('error', __('This action is no longer permitted. Please contact an Administrator.'));
        }

        if (isset($param['email_list'])) {
            $param['email_list'] = implode(', ', $param['email_list']);
        } else {
            $param['email_list'] = '';
        }
        if($this->campaignTypeEmailBlastRepository->update($asset_id, $param)){
            $user = auth()->user();
            // insert into campaign note for correspondence
            $this->add_correspondence('email_blast', $param, $email_blast, $user);

            if($request->file('c_attachment')){
                foreach ($request->file('c_attachment') as $file) {
                    $campaign_type_asset_attachments = new CampaignTypeAssetAttachments();

//                    $fileName = $file->storeAs('campaigns/'.$email_blast->id.'/'.$asset_id, $file_name);
                    $fileName = $this->file_exist_check($file, $email_blast->id, $asset_id);

                    $campaign_type_asset_attachments['id'] = $email_blast->id;
                    $campaign_type_asset_attachments['asset_id'] = $asset_id;
                    $campaign_type_asset_attachments['type'] = 'attachment_file_' . $file->getMimeType();
                    $campaign_type_asset_attachments['author_id'] = $user->id;
                    $campaign_type_asset_attachments['attachment'] = '/' . $fileName;
                    $campaign_type_asset_attachments['file_ext'] = pathinfo($fileName, PATHINFO_EXTENSION);
                    $campaign_type_asset_attachments['file_type'] = $file->getMimeType();
                    $campaign_type_asset_attachments['file_size'] = $file->getSize();
                    $campaign_type_asset_attachments['date_created'] = Carbon::now();
                    $campaign_type_asset_attachments->save();

                    // insert file attachment correspondence
                    $this->add_file_correspondence_for_asset($email_blast, $user, $fileName, 'email_blast');
                }
            }

            return redirect('admin/campaign/'.$email_blast->id.'/edit#'.$asset_id)
                ->with('success', __('Email Blast ('.$asset_id.') - Update Success'));
        }
        return redirect('admin/campaign/'.$email_blast->id.'/edit#'.$asset_id)
            ->with('error', __('Update Failed'));
    }

    public function add_social_ad(AssetSocialAdRequest $request){

        $campaignAssetIndex = new CampaignAssetIndex();
        $campaignAssetIndex['campaign_id'] = $request['social_ad_c_id'];
        $campaignAssetIndex['type'] = $request['social_ad_asset_type'];
        $campaignAssetIndex['team_to'] = $request['social_ad_team_to'];

        if(isset($request['skip-creative']) && $request['skip-creative'] =='on'){
            $campaignAssetIndex['skip_creative'] = 'skip';
        }else {
            $campaignAssetIndex['skip_creative'] = '';
        }

        if(isset($request['social_ad_no_copy_necessary']) && $request['social_ad_no_copy_necessary'] =='on'){
            $campaignAssetIndex['status'] = 'copy_complete';
        }else {
            $campaignAssetIndex['status'] = 'copy_requested';
        }
        $user = auth()->user(); // asset_author_id
        $campaignAssetIndex['author_id'] = $user->id;
        $campaignAssetIndex->save();

        $asset_id = $campaignAssetIndex->id;

        $campaignTypeSocialAd = new CampaignTypeSocialAd();
        $campaignTypeSocialAd['id'] = $request['social_ad_c_id']; //campaing_id
        $campaignTypeSocialAd['author_id'] = $request['social_ad_author_id'];
        $campaignTypeSocialAd['type'] = $request['social_ad_asset_type'];
        $campaignTypeSocialAd['date_from'] = $request['social_ad_date_from'];
        $campaignTypeSocialAd['date_to'] = $request['social_ad_date_to'];

        // check if no copy necessary!
        if (isset($request['social_ad_include_formats'])) {
            $request['social_ad_include_formats'] = implode(', ', $request['social_ad_include_formats']);
        } else {
            $request['social_ad_include_formats'] = '';
        }
        $campaignTypeSocialAd['include_formats'] = $request['social_ad_include_formats'];

        $campaignTypeSocialAd['note'] = $request['social_ad_note'];
        $campaignTypeSocialAd['text'] = $request['social_ad_text'];
        $campaignTypeSocialAd['text_2'] = $request['social_ad_text_2'];
        $campaignTypeSocialAd['text_3'] = $request['social_ad_text_3'];
        $campaignTypeSocialAd['headline'] = $request['social_ad_headline'];
        $campaignTypeSocialAd['headline_2'] = $request['social_ad_headline_2'];
        $campaignTypeSocialAd['headline_3'] = $request['social_ad_headline_3'];
        $campaignTypeSocialAd['newsfeed'] = $request['social_ad_newsfeed'];
        $campaignTypeSocialAd['newsfeed_2'] = $request['social_ad_newsfeed_2'];
        $campaignTypeSocialAd['newsfeed_3'] = $request['social_ad_newsfeed_3'];
        $campaignTypeSocialAd['products_featured'] = $request['social_ad_products_featured'];
        $campaignTypeSocialAd['no_copy_necessary'] = $request['social_ad_no_copy_necessary'];
        $campaignTypeSocialAd['copy_inside_graphic'] = $request['social_ad_copy_inside_graphic'];
        $campaignTypeSocialAd['click_through_links'] = $request['social_ad_click_through_links'];
        $campaignTypeSocialAd['google_drive_link'] = $request['social_ad_google_drive_link'];
        $campaignTypeSocialAd['utm_code'] = $request['social_ad_utm_code'];
        $campaignTypeSocialAd['promo_code'] = $request['social_ad_promo_code'];
        $campaignTypeSocialAd['budget_code'] = $request['social_ad_budget_code'];
        $campaignTypeSocialAd['date_created'] = Carbon::now();
        $campaignTypeSocialAd['asset_id'] = $asset_id;
        $campaignTypeSocialAd->save();

        // insert note for adding asset
//        $this->add_asset_correspondence($campaignAssetIndex['campaign_id'], $campaignAssetIndex['type'], $asset_id, ucwords(str_replace('_', ' ', $campaignAssetIndex['status'])));

        // new correspondence when adding asset
        $this->new_add_asset_correspondence($campaignAssetIndex['campaign_id'], $request['category'], $campaignAssetIndex);

        if($request->file('social_ad_c_attachment')){
            foreach ($request->file('social_ad_c_attachment') as $file) {
                $campaign_type_asset_attachments = new CampaignTypeAssetAttachments();
//                $fileName = $file->store('campaigns/'.$request['c_id'].'/'.$asset_id);
                $fileName = $this->file_exist_check($file, $request['social_ad_c_id'], $asset_id);
                $campaign_type_asset_attachments['id'] = $request['social_ad_c_id'];
                $campaign_type_asset_attachments['asset_id'] = $asset_id;
                $campaign_type_asset_attachments['type'] = 'attachment_file_' . $file->getMimeType();
                $campaign_type_asset_attachments['author_id'] = $request['social_ad_author_id'];
                $campaign_type_asset_attachments['attachment'] = '/' . $fileName;
                $campaign_type_asset_attachments['file_ext'] = pathinfo($fileName, PATHINFO_EXTENSION);
                $campaign_type_asset_attachments['file_type'] = $file->getMimeType();
                $campaign_type_asset_attachments['file_size'] = $file->getSize();
                $campaign_type_asset_attachments['date_created'] = Carbon::now();
                $campaign_type_asset_attachments->save();
            }
        }

        // TODO notification
        // Send notification to copywriter(brand check) via email
        // Do action - copy request
        if($campaignAssetIndex['status'] == 'copy_requested'){ // only copy_requested, send notification to copy writers
            $notify = new NotifyController();
            $notify->copy_request($request['social_ad_c_id'], $asset_id);
        } else if($campaignAssetIndex['status'] == 'copy_complete') {
            $notify = new NotifyController();
            $notify->copy_complete($request['social_ad_c_id'], $asset_id);
        }
        ///////////////////////////////////////////////////////////////

        return redirect('admin/campaign/'.$request['social_ad_c_id'].'/edit#'.$asset_id)
            ->with('success', __('Added the Social AD Asset : ' . $asset_id));
    }

    public function edit_social_ad(Request $request, $asset_id){

        $social_ad = $this->campaignTypeSocialAdRepository->findById($asset_id);

        $param = $request->request->all();

        // Permission_check
        if(!$this->permission_check($param)){
            return redirect('admin/campaign/' . $social_ad->id . '/edit#'.$asset_id)
                ->with('error', __('This action is no longer permitted. Please contact an Administrator.'));
        }

        if (isset($param['include_formats'])) {
            $param['include_formats'] = implode(', ', $param['include_formats']);
        } else {
            $param['include_formats'] = '';
        }

        if($this->campaignTypeSocialAdRepository->update($asset_id, $param)){
            $user = auth()->user();

            // insert into campaign note for correspondence
            $this->add_correspondence('social_ad', $param, $social_ad, $user);

            if($request->file('c_attachment')){
                foreach ($request->file('c_attachment') as $file) {

                    $campaign_type_asset_attachments = new CampaignTypeAssetAttachments();

//                    $fileName = $file->store('campaigns/'.$social_ad->id.'/'.$asset_id);
                    $fileName = $this->file_exist_check($file, $social_ad->id, $asset_id);

                    $campaign_type_asset_attachments['id'] = $social_ad->id;
                    $campaign_type_asset_attachments['asset_id'] = $asset_id;
                    $campaign_type_asset_attachments['type'] = 'attachment_file_' . $file->getMimeType();
                    $campaign_type_asset_attachments['author_id'] = $user->id;
                    $campaign_type_asset_attachments['attachment'] = '/' . $fileName;
                    $campaign_type_asset_attachments['file_ext'] = pathinfo($fileName, PATHINFO_EXTENSION);
                    $campaign_type_asset_attachments['file_type'] = $file->getMimeType();
                    $campaign_type_asset_attachments['file_size'] = $file->getSize();
                    $campaign_type_asset_attachments['date_created'] = Carbon::now();
                    $campaign_type_asset_attachments->save();

                    // insert file attachment on asset correspondence
                    $this->add_file_correspondence_for_asset($social_ad, $user, $fileName, 'social_ad');
                }
            }
            return redirect('admin/campaign/'.$social_ad->id.'/edit#'.$asset_id)
                ->with('success', __('Social Ad ('.$asset_id.') - Update Success'));
        }
        return redirect('admin/campaign/'.$social_ad->id.'/edit#'.$asset_id)
            ->with('error', __('Update Failed'));
    }


    public function add_website_banners(AssetWebsiteBannersRequest $request){

//        ddd($request);

        $campaignAssetIndex = new CampaignAssetIndex();
        $campaignAssetIndex['campaign_id'] = $request['website_banners_c_id'];
        $campaignAssetIndex['type'] = $request['website_banners_asset_type'];
        $campaignAssetIndex['team_to'] = $request['website_banners_team_to'];
        if(isset($request['website_banners_no_copy_necessary']) && $request['website_banners_no_copy_necessary'] =='on'){
            $campaignAssetIndex['status'] = 'copy_complete';
        }else {
            $campaignAssetIndex['status'] = 'copy_requested';
        }
        $user = auth()->user(); // asset_author_id
        $campaignAssetIndex['author_id'] = $user->id;
        $campaignAssetIndex->save();

        $asset_id = $campaignAssetIndex->id;

        $campaignTypeWebsiteBanners = new CampaignTypeWebsiteBanners();
        $campaignTypeWebsiteBanners['id'] = $request['website_banners_c_id']; //campaing_id
        $campaignTypeWebsiteBanners['author_id'] = $request['website_banners_author_id'];
        $campaignTypeWebsiteBanners['type'] = $request['website_banners_asset_type'];
        $campaignTypeWebsiteBanners['launch_date'] = $request['website_banners_launch_date'];

        if (isset($request['website_banners_banner'])) {
            $campaignTypeWebsiteBanners['banner'] = implode(', ', $request['website_banners_banner']);
        } else {
            $campaignTypeWebsiteBanners['banner'] = '';
        }
//        $campaignTypeWebsiteBanners['banner'] = $request['website_banners_banner'];

        $campaignTypeWebsiteBanners['details'] = $request['website_banners_details'];
        $campaignTypeWebsiteBanners['no_copy_necessary'] = $request['website_banners_no_copy_necessary'];
        $campaignTypeWebsiteBanners['copy'] = $request['website_banners_copy'];
        $campaignTypeWebsiteBanners['products_featured'] = $request['website_banners_products_featured'];
        $campaignTypeWebsiteBanners['click_through_links'] = $request['website_banners_click_through_links'];
        $campaignTypeWebsiteBanners['date_created'] = Carbon::now();
        $campaignTypeWebsiteBanners['asset_id'] = $asset_id;

        $campaignTypeWebsiteBanners->save();

        // insert note for adding asset
//        $this->add_asset_correspondence($campaignAssetIndex['campaign_id'], $campaignAssetIndex['type'], $asset_id, ucwords(str_replace('_', ' ', $campaignAssetIndex['status'])));

        // new correspondence when adding asset
        if(isset($request['website_banners_banner'])){
            foreach ($request['website_banners_banner'] as $banner){
                $this->new_add_asset_correspondence($campaignAssetIndex['campaign_id'], $banner, $campaignAssetIndex);
            }
        }

        if($request->file('website_banners_c_attachment')){
            foreach ($request->file('website_banners_c_attachment') as $file) {
                $campaign_type_asset_attachments = new CampaignTypeAssetAttachments();
//                $fileName = $file->store('campaigns/'.$request['website_banners_c_id'].'/'.$asset_id);
                $fileName = $this->file_exist_check($file, $request['website_banners_c_id'], $asset_id);
                $campaign_type_asset_attachments['id'] = $request['website_banners_c_id'];
                $campaign_type_asset_attachments['asset_id'] = $asset_id;
                $campaign_type_asset_attachments['type'] = 'attachment_file_' . $file->getMimeType();
                $campaign_type_asset_attachments['author_id'] = $request['website_banners_author_id'];
                $campaign_type_asset_attachments['attachment'] = '/' . $fileName;
                $campaign_type_asset_attachments['file_ext'] = pathinfo($fileName, PATHINFO_EXTENSION);
                $campaign_type_asset_attachments['file_type'] = $file->getMimeType();
                $campaign_type_asset_attachments['file_size'] = $file->getSize();
                $campaign_type_asset_attachments['date_created'] = Carbon::now();
                $campaign_type_asset_attachments->save();
            }
        }

        // TODO notification
        // Send notification to copywriter(brand check) via email
        // Do action - copy request
        if($campaignAssetIndex['status'] == 'copy_requested') { // only copy_requested, send notification to copy writers
            $notify = new NotifyController();
            $notify->copy_request($request['website_banners_c_id'], $asset_id);
        } else if($campaignAssetIndex['status'] == 'copy_complete') {
            $notify = new NotifyController();
            $notify->copy_complete($request['website_banners_c_id'], $asset_id);
        }
        ///////////////////////////////////////////////////////////////

        return redirect('admin/campaign/'.$request['website_banners_c_id'].'/edit#'.$asset_id)
            ->with('success', __('Added the Website Banners Asset : ' . $asset_id));
    }

    public function edit_website_banners(Request $request, $asset_id){

        $website_banners = $this->campaignTypeWebsiteBannersRepository->findById($asset_id);

        $param = $request->request->all();

        // Permission_check
        if(!$this->permission_check($param)){
            return redirect('admin/campaign/' . $website_banners->id . '/edit#'.$asset_id)
                ->with('error', __('This action is no longer permitted. Please contact an Administrator.'));
        }

        if (isset($param['banner'])) {
            $param['banner'] = implode(', ', $param['banner']);
        } else {
            $param['banner'] = '';
        }

        if($this->campaignTypeWebsiteBannersRepository->update($asset_id, $param)){
            $user = auth()->user();
            // insert into campaign note for correspondence
            $this->add_correspondence('website_banners', $param, $website_banners, $user);
            if($request->file('c_attachment')){
                foreach ($request->file('c_attachment') as $file) {
                    $campaign_type_asset_attachments = new CampaignTypeAssetAttachments();

                    //$fileName = $file->store('campaigns/'.$website_banners->id.'/'.$asset_id);
                    $fileName = $this->file_exist_check($file, $website_banners->id, $asset_id);

                    $campaign_type_asset_attachments['id'] = $website_banners->id;
                    $campaign_type_asset_attachments['asset_id'] = $asset_id;
                    $campaign_type_asset_attachments['type'] = 'attachment_file_' . $file->getMimeType();
                    $campaign_type_asset_attachments['author_id'] = $user->id;
                    $campaign_type_asset_attachments['attachment'] = '/' . $fileName;
                    $campaign_type_asset_attachments['file_ext'] = pathinfo($fileName, PATHINFO_EXTENSION);
                    $campaign_type_asset_attachments['file_type'] = $file->getMimeType();
                    $campaign_type_asset_attachments['file_size'] = $file->getSize();
                    $campaign_type_asset_attachments['date_created'] = Carbon::now();
                    $campaign_type_asset_attachments->save();

                    // insert file attachment on asset correspondence
                    $this->add_file_correspondence_for_asset($website_banners, $user, $fileName, 'website_banners');
                }
            }
            return redirect('admin/campaign/'.$website_banners->id.'/edit#'.$asset_id)
                ->with('success', __('Website Banners ('.$asset_id.') - Update Success'));
        }
        return redirect('admin/campaign/'.$website_banners->id.'/edit#'.$asset_id)
            ->with('error', __('Update Failed'));
    }

    public function add_landing_page(AssetLandingPageRequest $request){

//        ddd($request);

        $campaignAssetIndex = new CampaignAssetIndex();
        $campaignAssetIndex['campaign_id'] = $request['landing_page_c_id'];
        $campaignAssetIndex['type'] = $request['landing_page_asset_type'];
        $campaignAssetIndex['team_to'] = $request['landing_page_team_to'];
        if(isset($request['landing_page_no_copy_necessary']) && $request['landing_page_no_copy_necessary'] =='on'){
            $campaignAssetIndex['status'] = 'copy_complete';
        }else {
            $campaignAssetIndex['status'] = 'copy_requested';
        }
        $user = auth()->user(); // asset_author_id
        $campaignAssetIndex['author_id'] = $user->id;
        $campaignAssetIndex->save();

        $asset_id = $campaignAssetIndex->id;

        $campaignTypeLandingPage = new CampaignTypeLandingPage();
        $campaignTypeLandingPage['id'] = $request['landing_page_c_id']; //campaing_id
        $campaignTypeLandingPage['author_id'] = $request['landing_page_author_id'];
        $campaignTypeLandingPage['type'] = $request['landing_page_asset_type'];
        $campaignTypeLandingPage['launch_date'] = $request['landing_page_launch_date'];
        $campaignTypeLandingPage['details'] = $request['landing_page_details'];
        $campaignTypeLandingPage['no_copy_necessary'] = $request['landing_page_no_copy_necessary'];
        $campaignTypeLandingPage['copy'] = $request['landing_page_copy'];
        $campaignTypeLandingPage['products_featured'] = $request['landing_page_products_featured'];
        $campaignTypeLandingPage['landing_url'] = $request['landing_page_landing_url'];
        $campaignTypeLandingPage['date_created'] = Carbon::now();
        $campaignTypeLandingPage['asset_id'] = $asset_id;

        $campaignTypeLandingPage->save();

        // insert note for adding asset
//        $this->add_asset_correspondence($campaignAssetIndex['campaign_id'], $campaignAssetIndex['type'], $asset_id, ucwords(str_replace('_', ' ', $campaignAssetIndex['status'])));

        // new correspondence when adding asset
        $this->new_add_asset_correspondence($campaignAssetIndex['campaign_id'], 'Landing Page', $campaignAssetIndex);

        if($request->file('landing_page_c_attachment')){
            foreach ($request->file('landing_page_c_attachment') as $file) {
                $campaign_type_asset_attachments = new CampaignTypeAssetAttachments();
//                $fileName = $file->store('campaigns/'.$request['landing_page_c_id'].'/'.$asset_id);
                $fileName = $this->file_exist_check($file, $request['landing_page_c_id'], $asset_id);
                $campaign_type_asset_attachments['id'] = $request['landing_page_c_id'];
                $campaign_type_asset_attachments['asset_id'] = $asset_id;
                $campaign_type_asset_attachments['type'] = 'attachment_file_' . $file->getMimeType();
                $campaign_type_asset_attachments['author_id'] = $request['landing_page_author_id'];
                $campaign_type_asset_attachments['attachment'] = '/' . $fileName;
                $campaign_type_asset_attachments['file_ext'] = pathinfo($fileName, PATHINFO_EXTENSION);
                $campaign_type_asset_attachments['file_type'] = $file->getMimeType();
                $campaign_type_asset_attachments['file_size'] = $file->getSize();
                $campaign_type_asset_attachments['date_created'] = Carbon::now();
                $campaign_type_asset_attachments->save();
            }
        }

        // TODO notification
        // Send notification to copywriter(brand check) via email
        // Do action - copy request
        if($campaignAssetIndex['status'] == 'copy_requested') { // only copy_requested, send notification to copy writers
            $notify = new NotifyController();
            $notify->copy_request($request['landing_page_c_id'], $asset_id);
        } else if($campaignAssetIndex['status'] == 'copy_complete') {
            $notify = new NotifyController();
            $notify->copy_complete($request['landing_page_c_id'], $asset_id);
        }
        ///////////////////////////////////////////////////////////////

        return redirect('admin/campaign/'.$request['landing_page_c_id'].'/edit#'.$asset_id)
            ->with('success', __('Added the Landing Page Asset : ' . $asset_id));
    }

    public function edit_landing_page(Request $request, $asset_id){

        $landing_page = $this->campaignTypeLandingPageRepository->findById($asset_id);

        $param = $request->request->all();

        // Permission_check
        if(!$this->permission_check($param)){
            return redirect('admin/campaign/' . $landing_page->id . '/edit#'.$asset_id)
                ->with('error', __('This action is no longer permitted. Please contact an Administrator.'));
        }

        if($this->campaignTypeLandingPageRepository->update($asset_id, $param)){
            $user = auth()->user();
            // insert into campaign note for correspondence
            $this->add_correspondence('landing_page', $param, $landing_page, $user);
            if($request->file('c_attachment')){
                foreach ($request->file('c_attachment') as $file) {
                    $campaign_type_asset_attachments = new CampaignTypeAssetAttachments();

//                    $fileName = $file->store('campaigns/'.$landing_page->id.'/'.$asset_id);
                    $fileName = $this->file_exist_check($file, $landing_page->id, $asset_id);

                    $campaign_type_asset_attachments['id'] = $landing_page->id;
                    $campaign_type_asset_attachments['asset_id'] = $asset_id;
                    $campaign_type_asset_attachments['type'] = 'attachment_file_' . $file->getMimeType();
                    $campaign_type_asset_attachments['author_id'] = $user->id;
                    $campaign_type_asset_attachments['attachment'] = '/' . $fileName;
                    $campaign_type_asset_attachments['file_ext'] = pathinfo($fileName, PATHINFO_EXTENSION);
                    $campaign_type_asset_attachments['file_type'] = $file->getMimeType();
                    $campaign_type_asset_attachments['file_size'] = $file->getSize();
                    $campaign_type_asset_attachments['date_created'] = Carbon::now();
                    $campaign_type_asset_attachments->save();

                    // insert file attachment on asset correspondence
                    $this->add_file_correspondence_for_asset($landing_page, $user, $fileName, 'landing_page');
                }
            }
            return redirect('admin/campaign/'.$landing_page->id.'/edit#'.$asset_id)
                ->with('success', __('Landing Page ('.$asset_id.') - Update Success'));
        }
        return redirect('admin/campaign/'.$landing_page->id.'/edit#'.$asset_id)
            ->with('error', __('Update Failed'));
    }

    public function add_misc(AssetMiscRequest $request){

//        ddd($request);

        $campaignAssetIndex = new CampaignAssetIndex();
        $campaignAssetIndex['campaign_id'] = $request['misc_c_id'];
        $campaignAssetIndex['type'] = $request['misc_asset_type'];
        $campaignAssetIndex['team_to'] = $request['misc_team_to'];
        if(isset($request['misc_no_copy_necessary']) && $request['misc_no_copy_necessary'] =='on'){
            $campaignAssetIndex['status'] = 'copy_complete';
        }else {
            $campaignAssetIndex['status'] = 'copy_requested';
        }
        $user = auth()->user(); // asset_author_id
        $campaignAssetIndex['author_id'] = $user->id;
        $campaignAssetIndex->save();

        $asset_id = $campaignAssetIndex->id;

        $campaignTypeMisc = new CampaignTypeMisc();
        $campaignTypeMisc['title'] = $request['misc_title'];
        $campaignTypeMisc['id'] = $request['misc_c_id']; //campaing_id
        $campaignTypeMisc['author_id'] = $request['misc_author_id'];
        $campaignTypeMisc['type'] = $request['misc_asset_type'];
        $campaignTypeMisc['launch_date'] = $request['misc_launch_date'];
        $campaignTypeMisc['details'] = $request['misc_details'];
        $campaignTypeMisc['products_featured'] = $request['misc_products_featured'];
        $campaignTypeMisc['no_copy_necessary'] = $request['misc_no_copy_necessary'];
        $campaignTypeMisc['copy'] = $request['misc_copy'];
        $campaignTypeMisc['developer_url'] = $request['misc_developer_url'];
        $campaignTypeMisc['date_created'] = Carbon::now();
        $campaignTypeMisc['asset_id'] = $asset_id;

        $campaignTypeMisc->save();

        // insert note for adding asset
//        $this->add_asset_correspondence($campaignAssetIndex['campaign_id'], $campaignAssetIndex['type'], $asset_id, ucwords(str_replace('_', ' ', $campaignAssetIndex['status'])));

        // new correspondence when adding asset
        $this->new_add_asset_correspondence($campaignAssetIndex['campaign_id'], 'Misc', $campaignAssetIndex);

        if($request->file('misc_c_attachment')){
            foreach ($request->file('misc_c_attachment') as $file) {
                $campaign_type_asset_attachments = new CampaignTypeAssetAttachments();
//                $fileName = $file->store('campaigns/'.$request['misc_c_id'].'/'.$asset_id);
                $fileName = $this->file_exist_check($file, $request['misc_c_id'], $asset_id);
                $campaign_type_asset_attachments['id'] = $request['misc_c_id'];
                $campaign_type_asset_attachments['asset_id'] = $asset_id;
                $campaign_type_asset_attachments['type'] = 'attachment_file_' . $file->getMimeType();
                $campaign_type_asset_attachments['author_id'] = $request['misc_author_id'];
                $campaign_type_asset_attachments['attachment'] = '/' . $fileName;
                $campaign_type_asset_attachments['file_ext'] = pathinfo($fileName, PATHINFO_EXTENSION);
                $campaign_type_asset_attachments['file_type'] = $file->getMimeType();
                $campaign_type_asset_attachments['file_size'] = $file->getSize();
                $campaign_type_asset_attachments['date_created'] = Carbon::now();
                $campaign_type_asset_attachments->save();
            }
        }

        // TODO notification
        // Send notification to copywriter(brand check) via email
        // Do action - copy request
        if($campaignAssetIndex['status'] == 'copy_requested') { // only copy_requested, send notification to copy writers
            $notify = new NotifyController();
            $notify->copy_request($request['misc_c_id'], $asset_id);
        } else if($campaignAssetIndex['status'] == 'copy_complete') {
            $notify = new NotifyController();
            $notify->copy_complete($request['misc_c_id'], $asset_id);
        }
        ///////////////////////////////////////////////////////////////

        return redirect('admin/campaign/'.$request['misc_c_id'].'/edit#'.$asset_id)
            ->with('success', __('Added the Misc Asset : ' . $asset_id));
    }

    public function new_add_asset_correspondence($c_id, $asset_name, $campaignAssetIndex)
    {
        // send notification to asset owner
        $campaign_obj = $this->campaignRepository->findById($c_id);
        $brand_id = $campaign_obj->campaign_brand;
        $rs = $this->assetOwnerAssetsRepository->getByAssetName($asset_name);
        $notify = new NotifyController();
        $user = auth()->user();
        $change_line = '';
        if(isset($rs)){
            foreach ($rs as $raw){
                $brand_name_obj = $this->campaignBrandsRepository->getBrandNameById($brand_id);
                $brand_name = $brand_name_obj[0]['field_name'];
                $user_id = $raw->$brand_name;
                $asset_name = $raw->asset_name;
                if($user_id != "N/A") {
                    $asset_owner_user_obj = $this->userRepository->findById($user_id);
                    if (isset($asset_owner_user_obj)) {
                        $notify->new_asset_owners($asset_owner_user_obj, $campaign_obj, $asset_name);
                        $asset_owner_first_name = $asset_owner_user_obj['first_name'] . " " . $asset_owner_user_obj['last_name'];
                        $change_line .= "<p>$asset_owner_first_name, <b>$asset_name</b> has been created by $user->first_name. Please manage accordingly. </p>";
                    }
                }else{
                    $change_line .= "<p><b>$asset_name</b> has been created by $user->first_name. </p>";
                }
            }
        }
        $campaign_note = new CampaignNotes();
        $campaign_note['id'] = $c_id;
        $campaign_note['user_id'] = $user->id;
        $campaign_note['asset_id'] = $campaignAssetIndex->id;
        $campaign_note['type'] = $campaignAssetIndex->type;
        $campaign_note['note'] = $change_line;
        $campaign_note['date_created'] = Carbon::now();
        $campaign_note->save();
    }

    public function edit_misc(Request $request, $asset_id){

        $misc = $this->campaignTypeMiscRepository->findById($asset_id);

        $param = $request->request->all();

        // Permission_check
        if(!$this->permission_check($param)){
            return redirect('admin/campaign/' . $misc->id . '/edit#'.$asset_id)
                ->with('error', __('This action is no longer permitted. Please contact an Administrator.'));
        }

        if($this->campaignTypeMiscRepository->update($asset_id, $param)){
            $user = auth()->user();
            // insert into campaign note for correspondence
            $this->add_correspondence('misc', $param, $misc, $user);
            if($request->file('c_attachment')){
                foreach ($request->file('c_attachment') as $file) {
                    $campaign_type_asset_attachments = new CampaignTypeAssetAttachments();

//                    $fileName = $file->store('campaigns/'.$misc->id.'/'.$asset_id);
                    $fileName = $this->file_exist_check($file, $misc->id, $asset_id);

                    $campaign_type_asset_attachments['id'] = $misc->id;
                    $campaign_type_asset_attachments['asset_id'] = $asset_id;
                    $campaign_type_asset_attachments['type'] = 'attachment_file_' . $file->getMimeType();
                    $campaign_type_asset_attachments['author_id'] = $user->id;
                    $campaign_type_asset_attachments['attachment'] = '/' . $fileName;
                    $campaign_type_asset_attachments['file_ext'] = pathinfo($fileName, PATHINFO_EXTENSION);
                    $campaign_type_asset_attachments['file_type'] = $file->getMimeType();
                    $campaign_type_asset_attachments['file_size'] = $file->getSize();
                    $campaign_type_asset_attachments['date_created'] = Carbon::now();
                    $campaign_type_asset_attachments->save();

                    // insert file attachment on asset correspondence
                    $this->add_file_correspondence_for_asset($misc, $user, $fileName, 'misc');
                }
            }
            return redirect('admin/campaign/'.$misc->id.'/edit#'.$asset_id)
                ->with('success', __('Misc ('.$asset_id.') - Update Success'));
        }
        return redirect('admin/campaign/'.$misc->id.'/edit#'.$asset_id)
            ->with('error', __('Update Failed'));
    }

    public function add_sms_request(AssetSmsRequestRequest $request){

//        ddd($request);

        $campaignAssetIndex = new CampaignAssetIndex();
        $campaignAssetIndex['campaign_id'] = $request['sms_request_c_id'];
        $campaignAssetIndex['type'] = $request['sms_request_asset_type'];
        $campaignAssetIndex['team_to'] = $request['sms_request_team_to'];
        if(isset($request['sms_request_no_copy_necessary']) && $request['sms_request_no_copy_necessary'] =='on'){
            $campaignAssetIndex['status'] = 'copy_complete';
        }else {
            $campaignAssetIndex['status'] = 'copy_requested';
        }
        $user = auth()->user(); // asset_author_id
        $campaignAssetIndex['author_id'] = $user->id;
        $campaignAssetIndex->save();

        $asset_id = $campaignAssetIndex->id;

        $campaignTypeSmsRequest = new CampaignTypeSmsRequest();
        $campaignTypeSmsRequest['title'] = $request['sms_request_title'];
        $campaignTypeSmsRequest['id'] = $request['sms_request_c_id']; //campaing_id
        $campaignTypeSmsRequest['author_id'] = $request['sms_request_author_id'];
        $campaignTypeSmsRequest['type'] = $request['sms_request_asset_type'];
        $campaignTypeSmsRequest['launch_date'] = $request['sms_request_launch_date'];
        $campaignTypeSmsRequest['details'] = $request['sms_request_details'];
        $campaignTypeSmsRequest['products_featured'] = $request['sms_request_products_featured'];
        $campaignTypeSmsRequest['no_copy_necessary'] = $request['sms_request_no_copy_necessary'];
        $campaignTypeSmsRequest['copy'] = $request['sms_request_copy'];
        $campaignTypeSmsRequest['developer_url'] = $request['sms_request_developer_url'];
        $campaignTypeSmsRequest['date_created'] = Carbon::now();
        $campaignTypeSmsRequest['asset_id'] = $asset_id;

        $campaignTypeSmsRequest->save();

        // insert note for adding asset
//        $this->add_asset_correspondence($campaignAssetIndex['campaign_id'], $campaignAssetIndex['type'], $asset_id, ucwords(str_replace('_', ' ', $campaignAssetIndex['status'])));

        // new correspondence when adding asset
        $this->new_add_asset_correspondence($campaignAssetIndex['campaign_id'], 'SMS Request', $campaignAssetIndex);

        if($request->file('sms_request_c_attachment')){
            foreach ($request->file('sms_request_c_attachment') as $file) {
                $campaign_type_asset_attachments = new CampaignTypeAssetAttachments();
//                $fileName = $file->store('campaigns/'.$request['sms_request_c_id'].'/'.$asset_id);
                $fileName = $this->file_exist_check($file, $request['sms_request_c_id'], $asset_id);
                $campaign_type_asset_attachments['id'] = $request['sms_request_c_id'];
                $campaign_type_asset_attachments['asset_id'] = $asset_id;
                $campaign_type_asset_attachments['type'] = 'attachment_file_' . $file->getMimeType();
                $campaign_type_asset_attachments['author_id'] = $request['sms_request_author_id'];
                $campaign_type_asset_attachments['attachment'] = '/' . $fileName;
                $campaign_type_asset_attachments['file_ext'] = pathinfo($fileName, PATHINFO_EXTENSION);
                $campaign_type_asset_attachments['file_type'] = $file->getMimeType();
                $campaign_type_asset_attachments['file_size'] = $file->getSize();
                $campaign_type_asset_attachments['date_created'] = Carbon::now();
                $campaign_type_asset_attachments->save();
            }
        }

        // TODO notification
        // Send notification to copywriter(brand check) via email
        // Do action - copy request
        if($campaignAssetIndex['status'] == 'copy_requested') { // only copy_requested, send notification to copy writers
            $notify = new NotifyController();
            $notify->copy_request($request['sms_request_c_id'], $asset_id);
        } else if($campaignAssetIndex['status'] == 'copy_complete') {
            $notify = new NotifyController();
            $notify->copy_complete($request['sms_request_c_id'], $asset_id);
        }
        ///////////////////////////////////////////////////////////////

        return redirect('admin/campaign/'.$request['sms_request_c_id'].'/edit#'.$asset_id)
            ->with('success', __('Added the SMS Request Asset : ' . $asset_id));
    }

    public function edit_sms_request(Request $request, $asset_id){

        $sms_request = $this->campaignTypeSmsRequestRepository->findById($asset_id);

        $param = $request->request->all();

        // Permission_check
        if(!$this->permission_check($param)){
            return redirect('admin/campaign/' . $sms_request->id . '/edit#'.$asset_id)
                ->with('error', __('This action is no longer permitted. Please contact an Administrator.'));
        }

        if($this->campaignTypeSmsRequestRepository->update($asset_id, $param)){
            $user = auth()->user();
            // insert into campaign note for correspondence
            $this->add_correspondence('sms_request', $param, $sms_request, $user);
            if($request->file('c_attachment')){
                foreach ($request->file('c_attachment') as $file) {
                    $campaign_type_asset_attachments = new CampaignTypeAssetAttachments();

//                    $fileName = $file->store('campaigns/'.$sms_request->id.'/'.$asset_id);
                    $fileName = $this->file_exist_check($file, $sms_request->id, $asset_id);

                    $campaign_type_asset_attachments['id'] = $sms_request->id;
                    $campaign_type_asset_attachments['asset_id'] = $asset_id;
                    $campaign_type_asset_attachments['type'] = 'attachment_file_' . $file->getMimeType();
                    $campaign_type_asset_attachments['author_id'] = $user->id;
                    $campaign_type_asset_attachments['attachment'] = '/' . $fileName;
                    $campaign_type_asset_attachments['file_ext'] = pathinfo($fileName, PATHINFO_EXTENSION);
                    $campaign_type_asset_attachments['file_type'] = $file->getMimeType();
                    $campaign_type_asset_attachments['file_size'] = $file->getSize();
                    $campaign_type_asset_attachments['date_created'] = Carbon::now();
                    $campaign_type_asset_attachments->save();

                    // insert file attachment on asset correspondence
                    $this->add_file_correspondence_for_asset($sms_request, $user, $fileName, 'sms_request');
                }
            }
            return redirect('admin/campaign/'.$sms_request->id.'/edit#'.$asset_id)
                ->with('success', __('SMS Request ('.$asset_id.') - Update Success'));
        }
        return redirect('admin/campaign/'.$sms_request->id.'/edit#'.$asset_id)
            ->with('error', __('Update Failed'));
    }

    public function add_topcategories_copy(AssetTopcategoriesCopyRequest $request){

//        ddd($request);

        $campaignAssetIndex = new CampaignAssetIndex();
        $campaignAssetIndex['campaign_id'] = $request['topcategories_copy_c_id'];
        $campaignAssetIndex['type'] = $request['topcategories_copy_asset_type'];
        $campaignAssetIndex['team_to'] = $request['topcategories_copy_team_to'];
        if(isset($request['topcategories_copy_no_copy_necessary']) && $request['topcategories_copy_no_copy_necessary'] =='on'){
            $campaignAssetIndex['status'] = 'copy_complete';
        }else {
            $campaignAssetIndex['status'] = 'copy_requested';
        }
        $user = auth()->user(); // asset_author_id
        $campaignAssetIndex['author_id'] = $user->id;
        $campaignAssetIndex->save();

        $asset_id = $campaignAssetIndex->id;

        $campaignTypeTopcategoriesCopy = new CampaignTypeTopcategoriesCopy();
        $campaignTypeTopcategoriesCopy['id'] = $request['topcategories_copy_c_id']; //campaing_id
        $campaignTypeTopcategoriesCopy['author_id'] = $request['topcategories_copy_author_id'];
        $campaignTypeTopcategoriesCopy['type'] = $request['topcategories_copy_asset_type'];
        $campaignTypeTopcategoriesCopy['launch_date'] = $request['topcategories_copy_launch_date'];
        $campaignTypeTopcategoriesCopy['products_featured'] = $request['topcategories_copy_products_featured'];
        $campaignTypeTopcategoriesCopy['no_copy_necessary'] = $request['topcategories_copy_no_copy_necessary'];
        $campaignTypeTopcategoriesCopy['copy'] = $request['topcategories_copy_copy'];
        $campaignTypeTopcategoriesCopy['click_through_links'] = $request['topcategories_copy_click_through_links'];
        $campaignTypeTopcategoriesCopy['date_created'] = Carbon::now();
        $campaignTypeTopcategoriesCopy['asset_id'] = $asset_id;

        $campaignTypeTopcategoriesCopy->save();

        // insert note for adding asset
//        $this->add_asset_correspondence($campaignAssetIndex['campaign_id'], $campaignAssetIndex['type'], $asset_id, ucwords(str_replace('_', ' ', $campaignAssetIndex['status'])));

        // new correspondence when adding asset
        $this->new_add_asset_correspondence($campaignAssetIndex['campaign_id'], 'Top Categories Copy', $campaignAssetIndex);

        if($request->file('topcategories_copy_c_attachment')){
            foreach ($request->file('topcategories_copy_c_attachment') as $file) {
                $campaign_type_asset_attachments = new CampaignTypeAssetAttachments();
//                $fileName = $file->store('campaigns/'.$request['topcategories_copy_c_id'].'/'.$asset_id);
                $fileName = $this->file_exist_check($file, $request['topcategories_copy_c_id'], $asset_id);
                $campaign_type_asset_attachments['id'] = $request['topcategories_copy_c_id'];
                $campaign_type_asset_attachments['asset_id'] = $asset_id;
                $campaign_type_asset_attachments['type'] = 'attachment_file_' . $file->getMimeType();
                $campaign_type_asset_attachments['author_id'] = $request['topcategories_copy_author_id'];
                $campaign_type_asset_attachments['attachment'] = '/' . $fileName;
                $campaign_type_asset_attachments['file_ext'] = pathinfo($fileName, PATHINFO_EXTENSION);
                $campaign_type_asset_attachments['file_type'] = $file->getMimeType();
                $campaign_type_asset_attachments['file_size'] = $file->getSize();
                $campaign_type_asset_attachments['date_created'] = Carbon::now();
                $campaign_type_asset_attachments->save();
            }
        }

        // TODO notification
        // Send notification to copywriter(brand check) via email
        // Do action - copy request
        if($campaignAssetIndex['status'] == 'copy_requested') { // only copy_requested, send notification to copy writers
            $notify = new NotifyController();
            $notify->copy_request($request['topcategories_copy_c_id'], $asset_id);
        }
        ///////////////////////////////////////////////////////////////

        return redirect('admin/campaign/'.$request['topcategories_copy_c_id'].'/edit#'.$asset_id)
            ->with('success', __('Added the Top Categories Copy Asset : ' . $asset_id));
    }

    public function edit_topcategories_copy(Request $request, $asset_id){

        $topcategories_copy = $this->campaignTypeTopcategoriesCopyRepository->findById($asset_id);

        $param = $request->request->all();

        // Permission_check
        if(!$this->permission_check($param)){
            return redirect('admin/campaign/' . $topcategories_copy->id . '/edit#'.$asset_id)
                ->with('error', __('This action is no longer permitted. Please contact an Administrator.'));
        }

        if($this->campaignTypeTopcategoriesCopyRepository->update($asset_id, $param)){
            $user = auth()->user();
            // insert into campaign note for correspondence
            $this->add_correspondence('topcategories_copy', $param, $topcategories_copy, $user);
            if($request->file('c_attachment')){
                foreach ($request->file('c_attachment') as $file) {
                    $campaign_type_asset_attachments = new CampaignTypeAssetAttachments();

//                    $fileName = $file->store('campaigns/'.$topcategories_copy->id.'/'.$asset_id);
                    $fileName = $this->file_exist_check($file, $topcategories_copy->id, $asset_id);

                    $campaign_type_asset_attachments['id'] = $topcategories_copy->id;
                    $campaign_type_asset_attachments['asset_id'] = $asset_id;
                    $campaign_type_asset_attachments['type'] = 'attachment_file_' . $file->getMimeType();
                    $campaign_type_asset_attachments['author_id'] = $user->id;
                    $campaign_type_asset_attachments['attachment'] = '/' . $fileName;
                    $campaign_type_asset_attachments['file_ext'] = pathinfo($fileName, PATHINFO_EXTENSION);
                    $campaign_type_asset_attachments['file_type'] = $file->getMimeType();
                    $campaign_type_asset_attachments['file_size'] = $file->getSize();
                    $campaign_type_asset_attachments['date_created'] = Carbon::now();
                    $campaign_type_asset_attachments->save();

                    // insert file attachment on asset correspondence
                    $this->add_file_correspondence_for_asset($topcategories_copy, $user, $fileName, 'topcategories_copy');
                }
            }
            return redirect('admin/campaign/'.$topcategories_copy->id.'/edit#'.$asset_id)
                ->with('success', __('Top Categories Copy ('.$asset_id.') - Update Success'));
        }
        return redirect('admin/campaign/'.$topcategories_copy->id.'/edit#'.$asset_id)
            ->with('error', __('Update Failed'));
    }

    public function add_programmatic_banners(AssetProgrammaticBannersRequest $request){

//        ddd($request);

        $campaignAssetIndex = new CampaignAssetIndex();
        $campaignAssetIndex['campaign_id'] = $request['programmatic_banners_c_id'];
        $campaignAssetIndex['type'] = $request['programmatic_banners_asset_type'];
        $campaignAssetIndex['team_to'] = $request['programmatic_banners_team_to'];
        if(isset($request['programmatic_banners_no_copy_necessary']) && $request['programmatic_banners_no_copy_necessary'] =='on'){
            $campaignAssetIndex['status'] = 'copy_complete';
        }else {
            $campaignAssetIndex['status'] = 'copy_requested';
        }
        $user = auth()->user(); // asset_author_id
        $campaignAssetIndex['author_id'] = $user->id;
        $campaignAssetIndex->save();

        $asset_id = $campaignAssetIndex->id;

        $campaignTypeProgrammaticBanners = new CampaignTypeProgrammaticBanners();
        $campaignTypeProgrammaticBanners['id'] = $request['programmatic_banners_c_id']; //campaing_id
        $campaignTypeProgrammaticBanners['author_id'] = $request['programmatic_banners_author_id'];
        $campaignTypeProgrammaticBanners['type'] = $request['programmatic_banners_asset_type'];
        $campaignTypeProgrammaticBanners['date_from'] = $request['programmatic_banners_date_from'];
        $campaignTypeProgrammaticBanners['date_to'] = $request['programmatic_banners_date_to'];

        if (isset($request['programmatic_banners_include_formats'])) {
            $request['programmatic_banners_include_formats'] = implode(', ', $request['programmatic_banners_include_formats']);
        } else {
            $request['programmatic_banners_include_formats'] = '';
        }
        $campaignTypeProgrammaticBanners['include_formats'] = $request['programmatic_banners_include_formats'];

        $campaignTypeProgrammaticBanners['display_dimension'] = $request['programmatic_banners_display_dimension'];
        $campaignTypeProgrammaticBanners['no_copy_necessary'] = $request['programmatic_banners_no_copy_necessary'];
        $campaignTypeProgrammaticBanners['copy'] = $request['programmatic_banners_copy'];
        $campaignTypeProgrammaticBanners['products_featured'] = $request['programmatic_banners_products_featured'];
        $campaignTypeProgrammaticBanners['promo_code'] = $request['programmatic_banners_promo_code'];
        $campaignTypeProgrammaticBanners['click_through_links'] = $request['programmatic_banners_click_through_links'];
        $campaignTypeProgrammaticBanners['date_created'] = Carbon::now();
        $campaignTypeProgrammaticBanners['asset_id'] = $asset_id;

        $campaignTypeProgrammaticBanners->save();

        // insert note for adding asset
//        $this->add_asset_correspondence($campaignAssetIndex['campaign_id'], $campaignAssetIndex['type'], $asset_id, ucwords(str_replace('_', ' ', $campaignAssetIndex['status'])));

        // new correspondence when adding asset
        $this->new_add_asset_correspondence($campaignAssetIndex['campaign_id'], 'Programmatic Banners', $campaignAssetIndex);

        if($request->file('programmatic_banners_c_attachment')){
            foreach ($request->file('programmatic_banners_c_attachment') as $file) {
                $campaign_type_asset_attachments = new CampaignTypeAssetAttachments();
//                $fileName = $file->store('campaigns/'.$request['programmatic_banners_c_id'].'/'.$asset_id);
                $fileName = $this->file_exist_check($file, $request['programmatic_banners_c_id'], $asset_id);
                $campaign_type_asset_attachments['id'] = $request['programmatic_banners_c_id'];
                $campaign_type_asset_attachments['asset_id'] = $asset_id;
                $campaign_type_asset_attachments['type'] = 'attachment_file_' . $file->getMimeType();
                $campaign_type_asset_attachments['author_id'] = $request['programmatic_banners_author_id'];
                $campaign_type_asset_attachments['attachment'] = '/' . $fileName;
                $campaign_type_asset_attachments['file_ext'] = pathinfo($fileName, PATHINFO_EXTENSION);
                $campaign_type_asset_attachments['file_type'] = $file->getMimeType();
                $campaign_type_asset_attachments['file_size'] = $file->getSize();
                $campaign_type_asset_attachments['date_created'] = Carbon::now();
                $campaign_type_asset_attachments->save();
            }
        }

        // TODO notification
        // Send notification to copywriter(brand check) via email
        // Do action - copy request
        if($campaignAssetIndex['status'] == 'copy_requested') { // only copy_requested, send notification to copy writers
            $notify = new NotifyController();
            $notify->copy_request($request['programmatic_banners_c_id'], $asset_id);
        } else if($campaignAssetIndex['status'] == 'copy_complete') {
            $notify = new NotifyController();
            $notify->copy_complete($request['programmatic_banners_c_id'], $asset_id);
        }
        ///////////////////////////////////////////////////////////////

        return redirect('admin/campaign/'.$request['programmatic_banners_c_id'].'/edit#'.$asset_id)
            ->with('success', __('Added the Programmatic Banners Copy Asset : ' . $asset_id));
    }

    public function edit_programmatic_banners(Request $request, $asset_id){

        $programmatic_banners = $this->campaignTypeProgrammaticBannersRepository->findById($asset_id);

        $param = $request->request->all();

        // Permission_check
        if(!$this->permission_check($param)){
            return redirect('admin/campaign/' . $programmatic_banners->id . '/edit#'.$asset_id)
                ->with('error', __('This action is no longer permitted. Please contact an Administrator.'));
        }

        if (isset($param['include_formats'])) {
            $param['include_formats'] = implode(', ', $param['include_formats']);
        } else {
            $param['include_formats'] = '';
        }

        if($this->campaignTypeProgrammaticBannersRepository->update($asset_id, $param)){
            $user = auth()->user();
            // insert into campaign note for correspondence
            $this->add_correspondence('programmatic_banners', $param, $programmatic_banners, $user);
            if($request->file('c_attachment')){
                $user = auth()->user();
                foreach ($request->file('c_attachment') as $file) {
                    $campaign_type_asset_attachments = new CampaignTypeAssetAttachments();

//                    $fileName = $file->store('campaigns/'.$programmatic_banners->id.'/'.$asset_id);
                    $fileName = $this->file_exist_check($file, $programmatic_banners->id, $asset_id);

                    $campaign_type_asset_attachments['id'] = $programmatic_banners->id;
                    $campaign_type_asset_attachments['asset_id'] = $asset_id;
                    $campaign_type_asset_attachments['type'] = 'attachment_file_' . $file->getMimeType();
                    $campaign_type_asset_attachments['author_id'] = $user->id;
                    $campaign_type_asset_attachments['attachment'] = '/' . $fileName;
                    $campaign_type_asset_attachments['file_ext'] = pathinfo($fileName, PATHINFO_EXTENSION);
                    $campaign_type_asset_attachments['file_type'] = $file->getMimeType();
                    $campaign_type_asset_attachments['file_size'] = $file->getSize();
                    $campaign_type_asset_attachments['date_created'] = Carbon::now();
                    $campaign_type_asset_attachments->save();

                    // insert file attachment on asset correspondence
                    $this->add_file_correspondence_for_asset($programmatic_banners, $user, $fileName, 'programmatic_banners');
                }
            }
            return redirect('admin/campaign/'.$programmatic_banners->id.'/edit#'.$asset_id)
                ->with('success', __('Programmatic Banners ('.$asset_id.') - Update Success'));
        }
        return redirect('admin/campaign/'.$programmatic_banners->id.'/edit#'.$asset_id)
            ->with('error', __('Update Failed'));
    }

    public function add_image_request(AssetImageRequestRequest $request){

        $campaignAssetIndex = new CampaignAssetIndex();
        $campaignAssetIndex['campaign_id'] = $request['image_request_c_id'];
        $campaignAssetIndex['type'] = $request['image_request_asset_type'];
        $campaignAssetIndex['team_to'] = $request['image_request_team_to'];
        $campaignAssetIndex['status'] = 'copy_complete';
        $user = auth()->user(); // asset_author_id
        $campaignAssetIndex['author_id'] = $user->id;
        $campaignAssetIndex->save();

        $asset_id = $campaignAssetIndex->id;

        $campaignTypeImageRequest = new CampaignTypeImageRequest();
        $campaignTypeImageRequest['id'] = $request['image_request_c_id']; //campaing_id
        $campaignTypeImageRequest['author_id'] = $request['image_request_author_id'];
        $campaignTypeImageRequest['type'] = $request['image_request_asset_type'];
        $campaignTypeImageRequest['launch_date'] = $request['image_request_launch_date'];
        $campaignTypeImageRequest['client'] = $request['image_request_client'];
        $campaignTypeImageRequest['description'] = $request['image_request_description'];
        $campaignTypeImageRequest['image_dimensions'] = $request['image_request_image_dimensions'];
        $campaignTypeImageRequest['image_ratio'] = $request['image_request_image_ratio'];
        $campaignTypeImageRequest['image_format'] = $request['image_request_image_format'];
        $campaignTypeImageRequest['max_filesize'] = $request['image_request_max_filesize'];
        $campaignTypeImageRequest['sku'] = $request['image_request_sku'];
        $campaignTypeImageRequest['products_featured'] = $request['image_request_products_featured'];
        $campaignTypeImageRequest['date_created'] = Carbon::now();
        $campaignTypeImageRequest['asset_id'] = $asset_id;

        $campaignTypeImageRequest->save();

        // insert note for adding asset
//        $this->add_asset_correspondence($campaignAssetIndex['campaign_id'], $campaignAssetIndex['type'], $asset_id, 'Copy Complete');

        // new correspondence when adding asset
        $this->new_add_asset_correspondence($campaignAssetIndex['campaign_id'], 'Image Request', $campaignAssetIndex);

        if($request->file('image_request_c_attachment')){
            foreach ($request->file('image_request_c_attachment') as $file) {
                $campaign_type_asset_attachments = new CampaignTypeAssetAttachments();
//                $fileName = $file->store('campaigns/'.$request['programmatic_banners_c_id'].'/'.$asset_id);
                $fileName = $this->file_exist_check($file, $request['image_request_c_id'], $asset_id);
                $campaign_type_asset_attachments['id'] = $request['image_request_c_id'];
                $campaign_type_asset_attachments['asset_id'] = $asset_id;
                $campaign_type_asset_attachments['type'] = 'attachment_file_' . $file->getMimeType();
                $campaign_type_asset_attachments['author_id'] = $request['image_request_author_id'];
                $campaign_type_asset_attachments['attachment'] = '/' . $fileName;
                $campaign_type_asset_attachments['file_ext'] = pathinfo($fileName, PATHINFO_EXTENSION);
                $campaign_type_asset_attachments['file_type'] = $file->getMimeType();
                $campaign_type_asset_attachments['file_size'] = $file->getSize();
                $campaign_type_asset_attachments['date_created'] = Carbon::now();
                $campaign_type_asset_attachments->save();
            }
        }

        $notify = new NotifyController();
        $notify->copy_complete($request['image_request_c_id'], $asset_id);

        return redirect('admin/campaign/'.$request['image_request_c_id'].'/edit#'.$asset_id)
            ->with('success', __('Added the Image Request Asset : ' . $asset_id));
    }

    public function edit_image_request(Request $request, $asset_id){

        $image_request = $this->campaignTypeImageRequestRepository->findById($asset_id);

        $param = $request->request->all();

        // Permission_check
        if(!$this->permission_check($param)){
            return redirect('admin/campaign/' . $image_request->id . '/edit#'.$asset_id)
                ->with('error', __('This action is no longer permitted. Please contact an Administrator.'));
        }

        if($this->campaignTypeImageRequestRepository->update($asset_id, $param)){
            $user = auth()->user();
            // insert into campaign note for correspondence
            $this->add_correspondence('image_request', $param, $image_request, $user);
            if($request->file('c_attachment')){
                $user = auth()->user();
                foreach ($request->file('c_attachment') as $file) {
                    $campaign_type_asset_attachments = new CampaignTypeAssetAttachments();

//                    $fileName = $file->store('campaigns/'.$image_request->id.'/'.$asset_id);
                    $fileName = $this->file_exist_check($file, $image_request->id, $asset_id);

                    $campaign_type_asset_attachments['id'] = $image_request->id;
                    $campaign_type_asset_attachments['asset_id'] = $asset_id;
                    $campaign_type_asset_attachments['type'] = 'attachment_file_' . $file->getMimeType();
                    $campaign_type_asset_attachments['author_id'] = $user->id;
                    $campaign_type_asset_attachments['attachment'] = '/' . $fileName;
                    $campaign_type_asset_attachments['file_ext'] = pathinfo($fileName, PATHINFO_EXTENSION);
                    $campaign_type_asset_attachments['file_type'] = $file->getMimeType();
                    $campaign_type_asset_attachments['file_size'] = $file->getSize();
                    $campaign_type_asset_attachments['date_created'] = Carbon::now();
                    $campaign_type_asset_attachments->save();

                    // insert file attachment on asset correspondence
                    $this->add_file_correspondence_for_asset($image_request, $user, $fileName, 'image_request');
                }
            }
            return redirect('admin/campaign/'.$image_request->id.'/edit#'.$asset_id)
                ->with('success', __('Image Request ('.$asset_id.') - Update Success'));
        }
        return redirect('admin/campaign/'.$image_request->id.'/edit#'.$asset_id)
            ->with('error', __('Update Failed'));
    }

    public function add_roll_over(AssetRollOverRequest $request){

        $campaignAssetIndex = new CampaignAssetIndex();
        $campaignAssetIndex['campaign_id'] = $request['roll_over_c_id'];
        $campaignAssetIndex['type'] = $request['roll_over_asset_type'];
        $campaignAssetIndex['team_to'] = $request['roll_over_team_to'];
        $campaignAssetIndex['status'] = 'copy_complete';
        $user = auth()->user(); // asset_author_id
        $campaignAssetIndex['author_id'] = $user->id;
        $campaignAssetIndex->save();

        $asset_id = $campaignAssetIndex->id;

        $campaignTypeRollOver = new CampaignTypeRollOver();
        $campaignTypeRollOver['id'] = $request['roll_over_c_id']; //campaing_id
        $campaignTypeRollOver['author_id'] = $request['roll_over_author_id'];
        $campaignTypeRollOver['type'] = $request['roll_over_asset_type'];
        $campaignTypeRollOver['launch_date'] = $request['roll_over_launch_date'];
        $campaignTypeRollOver['sku'] = $request['roll_over_sku'];
        $campaignTypeRollOver['products_featured'] = $request['roll_over_products_featured'];
        $campaignTypeRollOver['notes'] = $request['roll_over_notes'];
        $campaignTypeRollOver['date_created'] = Carbon::now();
        $campaignTypeRollOver['asset_id'] = $asset_id;

        $campaignTypeRollOver->save();

        // insert note for adding asset
//        $this->add_asset_correspondence($campaignAssetIndex['campaign_id'], $campaignAssetIndex['type'], $asset_id, 'Copy Complete');

        // new correspondence when adding asset
        $this->new_add_asset_correspondence($campaignAssetIndex['campaign_id'], 'Roll Over', $campaignAssetIndex);

        if($request->file('roll_over_c_attachment')){
            foreach ($request->file('roll_over_c_attachment') as $file) {
                $campaign_type_asset_attachments = new CampaignTypeAssetAttachments();
                $fileName = $this->file_exist_check($file, $request['roll_over_c_id'], $asset_id);
                $campaign_type_asset_attachments['id'] = $request['roll_over_c_id'];
                $campaign_type_asset_attachments['asset_id'] = $asset_id;
                $campaign_type_asset_attachments['type'] = 'attachment_file_' . $file->getMimeType();
                $campaign_type_asset_attachments['author_id'] = $request['roll_over_author_id'];
                $campaign_type_asset_attachments['attachment'] = '/' . $fileName;
                $campaign_type_asset_attachments['file_ext'] = pathinfo($fileName, PATHINFO_EXTENSION);
                $campaign_type_asset_attachments['file_type'] = $file->getMimeType();
                $campaign_type_asset_attachments['file_size'] = $file->getSize();
                $campaign_type_asset_attachments['date_created'] = Carbon::now();
                $campaign_type_asset_attachments->save();
            }
        }

        $notify = new NotifyController();
        $notify->copy_complete($request['roll_over_c_id'], $asset_id);

        return redirect('admin/campaign/'.$request['roll_over_c_id'].'/edit#'.$asset_id)
            ->with('success', __('Added the Roll Over Asset : ' . $asset_id));
    }

    public function edit_roll_over(Request $request, $asset_id){

        $roll_over = $this->campaignTypeRollOverRepository->findById($asset_id);

        $param = $request->request->all();

        // Permission_check
        if(!$this->permission_check($param)){
            return redirect('admin/campaign/' . $roll_over->id . '/edit#'.$asset_id)
                ->with('error', __('This action is no longer permitted. Please contact an Administrator.'));
        }

        if($this->campaignTypeRollOverRepository->update($asset_id, $param)){
            $user = auth()->user();
            // insert into campaign note for correspondence
            $this->add_correspondence('roll_over', $param, $roll_over, $user);
            if($request->file('c_attachment')){
                $user = auth()->user();
                foreach ($request->file('c_attachment') as $file) {
                    $campaign_type_asset_attachments = new CampaignTypeAssetAttachments();

//                    $fileName = $file->store('campaigns/'.$image_request->id.'/'.$asset_id);
                    $fileName = $this->file_exist_check($file, $roll_over->id, $asset_id);

                    $campaign_type_asset_attachments['id'] = $roll_over->id;
                    $campaign_type_asset_attachments['asset_id'] = $asset_id;
                    $campaign_type_asset_attachments['type'] = 'attachment_file_' . $file->getMimeType();
                    $campaign_type_asset_attachments['author_id'] = $user->id;
                    $campaign_type_asset_attachments['attachment'] = '/' . $fileName;
                    $campaign_type_asset_attachments['file_ext'] = pathinfo($fileName, PATHINFO_EXTENSION);
                    $campaign_type_asset_attachments['file_type'] = $file->getMimeType();
                    $campaign_type_asset_attachments['file_size'] = $file->getSize();
                    $campaign_type_asset_attachments['date_created'] = Carbon::now();
                    $campaign_type_asset_attachments->save();

                    // insert file attachment on asset correspondence
                    $this->add_file_correspondence_for_asset($roll_over, $user, $fileName, 'roll_over');
                }
            }
            return redirect('admin/campaign/'.$roll_over->id.'/edit#'.$asset_id)
                ->with('success', __('Roll Over ('.$asset_id.') - Update Success'));
        }
        return redirect('admin/campaign/'.$roll_over->id.'/edit#'.$asset_id)
            ->with('error', __('Update Failed'));
    }

    public function add_store_front(AssetStoreFrontRequest $request){

        $campaignAssetIndex = new CampaignAssetIndex();
        $campaignAssetIndex['campaign_id'] = $request['store_front_c_id'];
        $campaignAssetIndex['type'] = $request['store_front_asset_type'];
        $campaignAssetIndex['team_to'] = $request['store_front_team_to'];
        $campaignAssetIndex['status'] = 'copy_complete';
        $user = auth()->user(); // asset_author_id
        $campaignAssetIndex['author_id'] = $user->id;
        $campaignAssetIndex->save();

        $asset_id = $campaignAssetIndex->id;

        $campaignTypeStoreFront = new CampaignTypeStoreFront();
        $campaignTypeStoreFront['id'] = $request['store_front_c_id']; //campaing_id
        $campaignTypeStoreFront['author_id'] = $request['store_front_author_id'];
        $campaignTypeStoreFront['type'] = $request['store_front_asset_type'];
        $campaignTypeStoreFront['launch_date'] = $request['store_front_launch_date'];
        $campaignTypeStoreFront['client'] = $request['store_front_client'];
        $campaignTypeStoreFront['products_featured'] = $request['store_front_products_featured'];
        $campaignTypeStoreFront['notes'] = $request['store_front_notes'];
        $campaignTypeStoreFront['invision_link'] = $request['store_front_invision_link'];
        $campaignTypeStoreFront['date_created'] = Carbon::now();
        $campaignTypeStoreFront['asset_id'] = $asset_id;

        $campaignTypeStoreFront->save();

        // insert note for adding asset
//        $this->add_asset_correspondence($campaignAssetIndex['campaign_id'], $campaignAssetIndex['type'], $asset_id, 'Copy Complete');

        // new correspondence when adding asset
        $this->new_add_asset_correspondence($campaignAssetIndex['campaign_id'], 'Store Front', $campaignAssetIndex);

        if($request->file('store_front_c_attachment')){
            foreach ($request->file('store_front_c_attachment') as $file) {
                $campaign_type_asset_attachments = new CampaignTypeAssetAttachments();
                $fileName = $this->file_exist_check($file, $request['store_front_c_id'], $asset_id);
                $campaign_type_asset_attachments['id'] = $request['store_front_c_id'];
                $campaign_type_asset_attachments['asset_id'] = $asset_id;
                $campaign_type_asset_attachments['type'] = 'attachment_file_' . $file->getMimeType();
                $campaign_type_asset_attachments['author_id'] = $request['store_front_author_id'];
                $campaign_type_asset_attachments['attachment'] = '/' . $fileName;
                $campaign_type_asset_attachments['file_ext'] = pathinfo($fileName, PATHINFO_EXTENSION);
                $campaign_type_asset_attachments['file_type'] = $file->getMimeType();
                $campaign_type_asset_attachments['file_size'] = $file->getSize();
                $campaign_type_asset_attachments['date_created'] = Carbon::now();
                $campaign_type_asset_attachments->save();
            }
        }

        $notify = new NotifyController();
        $notify->copy_complete($request['store_front_c_id'], $asset_id);

        return redirect('admin/campaign/'.$request['store_front_c_id'].'/edit#'.$asset_id)
            ->with('success', __('Added the Store Front Asset : ' . $asset_id));
    }

    public function edit_store_front(Request $request, $asset_id){

        $store_front = $this->campaignTypeStoreFrontRepository->findById($asset_id);

        $param = $request->request->all();

        // Permission_check
        if(!$this->permission_check($param)){
            return redirect('admin/campaign/' . $store_front->id . '/edit#'.$asset_id)
                ->with('error', __('This action is no longer permitted. Please contact an Administrator.'));
        }

        if($this->campaignTypeStoreFrontRepository->update($asset_id, $param)){
            $user = auth()->user();
            // insert into campaign note for correspondence
            $this->add_correspondence('store_front', $param, $store_front, $user);
            if($request->file('c_attachment')){
                $user = auth()->user();
                foreach ($request->file('c_attachment') as $file) {
                    $campaign_type_asset_attachments = new CampaignTypeAssetAttachments();

//                    $fileName = $file->store('campaigns/'.$image_request->id.'/'.$asset_id);
                    $fileName = $this->file_exist_check($file, $store_front->id, $asset_id);

                    $campaign_type_asset_attachments['id'] = $store_front->id;
                    $campaign_type_asset_attachments['asset_id'] = $asset_id;
                    $campaign_type_asset_attachments['type'] = 'attachment_file_' . $file->getMimeType();
                    $campaign_type_asset_attachments['author_id'] = $user->id;
                    $campaign_type_asset_attachments['attachment'] = '/' . $fileName;
                    $campaign_type_asset_attachments['file_ext'] = pathinfo($fileName, PATHINFO_EXTENSION);
                    $campaign_type_asset_attachments['file_type'] = $file->getMimeType();
                    $campaign_type_asset_attachments['file_size'] = $file->getSize();
                    $campaign_type_asset_attachments['date_created'] = Carbon::now();
                    $campaign_type_asset_attachments->save();

                    // insert file attachment on asset correspondence
                    $this->add_file_correspondence_for_asset($store_front, $user, $fileName, 'store_front');
                }
            }
            return redirect('admin/campaign/'.$store_front->id.'/edit#'.$asset_id)
                ->with('success', __('Store Front ('.$asset_id.') - Update Success'));
        }
        return redirect('admin/campaign/'.$store_front->id.'/edit#'.$asset_id)
            ->with('error', __('Update Failed'));
    }

    public function add_a_content(AssetAContentRequest $request){

        $campaignAssetIndex = new CampaignAssetIndex();
        $campaignAssetIndex['campaign_id'] = $request['a_content_c_id'];
        $campaignAssetIndex['type'] = $request['a_content_asset_type'];
        $campaignAssetIndex['team_to'] = $request['a_content_team_to'];

        if(isset($request['a_content_no_copy_necessary']) && $request['a_content_no_copy_necessary'] =='on'){
            $campaignAssetIndex['status'] = 'copy_complete';
        }else {
            $campaignAssetIndex['status'] = 'copy_requested';
        }
        $user = auth()->user(); // asset_author_id
        $campaignAssetIndex['author_id'] = $user->id;
        $campaignAssetIndex->save();

        $asset_id = $campaignAssetIndex->id;

        $campaignTypeAContent = new CampaignTypeAContent();
        $campaignTypeAContent['id'] = $request['a_content_c_id']; //campaing_id
        $campaignTypeAContent['author_id'] = $request['a_content_author_id'];
        $campaignTypeAContent['type'] = $request['a_content_asset_type'];
        $campaignTypeAContent['launch_date'] = $request['a_content_launch_date'];
        $campaignTypeAContent['product_line'] = $request['a_content_product_line'];
        $campaignTypeAContent['invision_link'] = $request['a_content_invision_link'];
        $campaignTypeAContent['products_featured'] = $request['a_content_products_featured'];
        $campaignTypeAContent['no_copy_necessary'] = $request['a_content_no_copy_necessary'];
        $campaignTypeAContent['note'] = $request['a_content_note'];
        $campaignTypeAContent['date_created'] = Carbon::now();
        $campaignTypeAContent['asset_id'] = $asset_id;

        $campaignTypeAContent->save();

        // insert note for adding asset
//        $this->add_asset_correspondence($campaignAssetIndex['campaign_id'], $campaignAssetIndex['type'], $asset_id, ucwords(str_replace('_', ' ', $campaignAssetIndex['status'])));

        // new correspondence when adding asset
        $this->new_add_asset_correspondence($campaignAssetIndex['campaign_id'], 'A+ Content', $campaignAssetIndex);

        if($request->file('a_content_c_attachment')){
            foreach ($request->file('a_content_c_attachment') as $file) {
                $campaign_type_asset_attachments = new CampaignTypeAssetAttachments();
                $fileName = $this->file_exist_check($file, $request['a_content_c_id'], $asset_id);
                $campaign_type_asset_attachments['id'] = $request['a_content_c_id'];
                $campaign_type_asset_attachments['asset_id'] = $asset_id;
                $campaign_type_asset_attachments['type'] = 'attachment_file_' . $file->getMimeType();
                $campaign_type_asset_attachments['author_id'] = $request['a_content_author_id'];
                $campaign_type_asset_attachments['attachment'] = '/' . $fileName;
                $campaign_type_asset_attachments['file_ext'] = pathinfo($fileName, PATHINFO_EXTENSION);
                $campaign_type_asset_attachments['file_type'] = $file->getMimeType();
                $campaign_type_asset_attachments['file_size'] = $file->getSize();
                $campaign_type_asset_attachments['date_created'] = Carbon::now();
                $campaign_type_asset_attachments->save();
            }
        }

        // TODO notification
        // Send notification to copywriter(brand check) via email
        // Do action - copy request
        if($campaignAssetIndex['status'] == 'copy_requested') { // only copy_requested, send notification to copy writers
            $notify = new NotifyController();
            $notify->copy_request($request['a_content_c_id'], $asset_id);
        } else if($campaignAssetIndex['status'] == 'copy_complete') {
            $notify = new NotifyController();
            $notify->copy_complete($request['a_content_c_id'], $asset_id);
        }
        ///////////////////////////////////////////////////////////////

        return redirect('admin/campaign/'.$request['a_content_c_id'].'/edit#'.$asset_id)
            ->with('success', __('Added the A+ Content Asset : ' . $asset_id));
    }

    public function edit_a_content(Request $request, $asset_id){

        $a_content = $this->campaignTypeAContentRepository->findById($asset_id);

        $param = $request->request->all();

        // Permission_check
        if(!$this->permission_check($param)){
            return redirect('admin/campaign/' . $a_content->id . '/edit#'.$asset_id)
                ->with('error', __('This action is no longer permitted. Please contact an Administrator.'));
        }

        if($this->campaignTypeAContentRepository->update($asset_id, $param)){
            $user = auth()->user();
            // insert into campaign note for correspondence
            $this->add_correspondence('a_content', $param, $a_content, $user);
            if($request->file('c_attachment')){
                $user = auth()->user();
                foreach ($request->file('c_attachment') as $file) {
                    $campaign_type_asset_attachments = new CampaignTypeAssetAttachments();

//                    $fileName = $file->store('campaigns/'.$image_request->id.'/'.$asset_id);
                    $fileName = $this->file_exist_check($file, $a_content->id, $asset_id);

                    $campaign_type_asset_attachments['id'] = $a_content->id;
                    $campaign_type_asset_attachments['asset_id'] = $asset_id;
                    $campaign_type_asset_attachments['type'] = 'attachment_file_' . $file->getMimeType();
                    $campaign_type_asset_attachments['author_id'] = $user->id;
                    $campaign_type_asset_attachments['attachment'] = '/' . $fileName;
                    $campaign_type_asset_attachments['file_ext'] = pathinfo($fileName, PATHINFO_EXTENSION);
                    $campaign_type_asset_attachments['file_type'] = $file->getMimeType();
                    $campaign_type_asset_attachments['file_size'] = $file->getSize();
                    $campaign_type_asset_attachments['date_created'] = Carbon::now();
                    $campaign_type_asset_attachments->save();

                    // insert file attachment on asset correspondence
                    $this->add_file_correspondence_for_asset($a_content, $user, $fileName, 'a_content');
                }
            }
            return redirect('admin/campaign/'.$a_content->id.'/edit#'.$asset_id)
                ->with('success', __('A Content ('.$asset_id.') - Update Success'));
        }
        return redirect('admin/campaign/'.$a_content->id.'/edit#'.$asset_id)
            ->with('error', __('Update Failed'));
    }

    public function add_youtube_copy(AssetAContentRequest $request){

        $campaignAssetIndex = new CampaignAssetIndex();
        $campaignAssetIndex['campaign_id'] = $request['youtube_copy_c_id'];
        $campaignAssetIndex['type'] = $request['youtube_copy_asset_type'];
        $campaignAssetIndex['team_to'] = $request['youtube_copy_team_to'];

        if(isset($request['youtube_copy_no_copy_necessary']) && $request['youtube_copy_no_copy_necessary'] =='on'){
            $campaignAssetIndex['status'] = 'copy_complete';
        }else {
            $campaignAssetIndex['status'] = 'copy_requested';
        }
        $user = auth()->user(); // asset_author_id
        $campaignAssetIndex['author_id'] = $user->id;
        $campaignAssetIndex->save();

        $asset_id = $campaignAssetIndex->id;

        $campaignTypeYoutubeCopy = new CampaignTypeYoutubeCopy();
        $campaignTypeYoutubeCopy['id'] = $request['youtube_copy_c_id']; //campaing_id
        $campaignTypeYoutubeCopy['author_id'] = $request['youtube_copy_author_id'];
        $campaignTypeYoutubeCopy['type'] = $request['youtube_copy_asset_type'];
        $campaignTypeYoutubeCopy['launch_date'] = $request['youtube_copy_launch_date'];
        $campaignTypeYoutubeCopy['information'] = $request['youtube_copy_information'];
        $campaignTypeYoutubeCopy['url_preview'] = $request['youtube_copy_url_preview'];
        $campaignTypeYoutubeCopy['products_featured'] = $request['youtube_copy_products_featured'];
        $campaignTypeYoutubeCopy['no_copy_necessary'] = $request['youtube_copy_no_copy_necessary'];
        $campaignTypeYoutubeCopy['title'] = $request['youtube_copy_title'];
        $campaignTypeYoutubeCopy['description'] = $request['youtube_copy_description'];
        $campaignTypeYoutubeCopy['tags'] = $request['youtube_copy_tags'];
        $campaignTypeYoutubeCopy['date_created'] = Carbon::now();
        $campaignTypeYoutubeCopy['asset_id'] = $asset_id;

        $campaignTypeYoutubeCopy->save();

        // insert note for adding asset
//        $this->add_asset_correspondence($campaignAssetIndex['campaign_id'], $campaignAssetIndex['type'], $asset_id, ucwords(str_replace('_', ' ', $campaignAssetIndex['status'])));

        // new correspondence when adding asset
        $this->new_add_asset_correspondence($campaignAssetIndex['campaign_id'], 'YouTube Copy', $campaignAssetIndex);

        if($request->file('youtube_copy_c_attachment')){
            foreach ($request->file('youtube_copy_c_attachment') as $file) {
                $campaign_type_asset_attachments = new CampaignTypeAssetAttachments();
                $fileName = $this->file_exist_check($file, $request['youtube_copy_c_id'], $asset_id);
                $campaign_type_asset_attachments['id'] = $request['youtube_copy_c_id'];
                $campaign_type_asset_attachments['asset_id'] = $asset_id;
                $campaign_type_asset_attachments['type'] = 'attachment_file_' . $file->getMimeType();
                $campaign_type_asset_attachments['author_id'] = $request['youtube_copy_author_id'];
                $campaign_type_asset_attachments['attachment'] = '/' . $fileName;
                $campaign_type_asset_attachments['file_ext'] = pathinfo($fileName, PATHINFO_EXTENSION);
                $campaign_type_asset_attachments['file_type'] = $file->getMimeType();
                $campaign_type_asset_attachments['file_size'] = $file->getSize();
                $campaign_type_asset_attachments['date_created'] = Carbon::now();
                $campaign_type_asset_attachments->save();
            }
        }

        // TODO notification
        // Send notification to copywriter(brand check) via email
        // Do action - copy request
        if($campaignAssetIndex['status'] == 'copy_requested') { // only copy_requested, send notification to copy writers
            $notify = new NotifyController();
            $notify->copy_request($request['youtube_copy_c_id'], $asset_id);
        }

        return redirect('admin/campaign/'.$request['youtube_copy_c_id'].'/edit#'.$asset_id)
            ->with('success', __('Added the YouTube Copy Asset : ' . $asset_id));
    }

    public function edit_youtube_copy(Request $request, $asset_id){

        $youtube_copy = $this->campaignTypeYoutubeCopyRepository->findById($asset_id);

        $param = $request->request->all();

        // Permission_check
        if(!$this->permission_check($param)){
            return redirect('admin/campaign/' . $youtube_copy->id . '/edit#'.$asset_id)
                ->with('error', __('This action is no longer permitted. Please contact an Administrator.'));
        }

        if($this->campaignTypeYoutubeCopyRepository->update($asset_id, $param)){
            $user = auth()->user();
            // insert into campaign note for correspondence
            $this->add_correspondence('youtube_copy', $param, $youtube_copy, $user);
            if($request->file('c_attachment')){
                $user = auth()->user();
                foreach ($request->file('c_attachment') as $file) {
                    $campaign_type_asset_attachments = new CampaignTypeAssetAttachments();

//                    $fileName = $file->store('campaigns/'.$image_request->id.'/'.$asset_id);
                    $fileName = $this->file_exist_check($file, $youtube_copy->id, $asset_id);

                    $campaign_type_asset_attachments['id'] = $youtube_copy->id;
                    $campaign_type_asset_attachments['asset_id'] = $asset_id;
                    $campaign_type_asset_attachments['type'] = 'attachment_file_' . $file->getMimeType();
                    $campaign_type_asset_attachments['author_id'] = $user->id;
                    $campaign_type_asset_attachments['attachment'] = '/' . $fileName;
                    $campaign_type_asset_attachments['file_ext'] = pathinfo($fileName, PATHINFO_EXTENSION);
                    $campaign_type_asset_attachments['file_type'] = $file->getMimeType();
                    $campaign_type_asset_attachments['file_size'] = $file->getSize();
                    $campaign_type_asset_attachments['date_created'] = Carbon::now();
                    $campaign_type_asset_attachments->save();

                    // insert file attachment on asset correspondence
                    $this->add_file_correspondence_for_asset($youtube_copy, $user, $fileName, 'youtube_copy');
                }
            }
            return redirect('admin/campaign/'.$youtube_copy->id.'/edit#'.$asset_id)
                ->with('success', __('YouTube Copy ('.$asset_id.') - Update Success'));
        }
        return redirect('admin/campaign/'.$youtube_copy->id.'/edit#'.$asset_id)
            ->with('error', __('Update Failed'));
    }

    public function add_info_graphic(AssetInfoGraphicRequest $request){

        $campaignAssetIndex = new CampaignAssetIndex();
        $campaignAssetIndex['campaign_id'] = $request['info_graphic_c_id'];
        $campaignAssetIndex['type'] = $request['info_graphic_asset_type'];
        $campaignAssetIndex['team_to'] = $request['info_graphic_team_to'];

        if(isset($request['info_graphic_no_copy_necessary']) && $request['info_graphic_no_copy_necessary'] =='on'){
            $campaignAssetIndex['status'] = 'copy_complete';
        }else {
            $campaignAssetIndex['status'] = 'copy_requested';
        }
        $user = auth()->user(); // asset_author_id
        $campaignAssetIndex['author_id'] = $user->id;
        $campaignAssetIndex->save();

        $asset_id = $campaignAssetIndex->id;

        $campaignTypeAContent = new CampaignTypeInfoGraphic();
        $campaignTypeAContent['id'] = $request['info_graphic_c_id']; //campaing_id
        $campaignTypeAContent['author_id'] = $request['info_graphic_author_id'];
        $campaignTypeAContent['type'] = $request['info_graphic_asset_type'];
        $campaignTypeAContent['launch_date'] = $request['info_graphic_launch_date'];
        $campaignTypeAContent['product_line'] = $request['info_graphic_product_line'];
        $campaignTypeAContent['invision_link'] = $request['info_graphic_invision_link'];
        $campaignTypeAContent['products_featured'] = $request['info_graphic_products_featured'];
        $campaignTypeAContent['no_copy_necessary'] = $request['info_graphic_no_copy_necessary'];
        $campaignTypeAContent['note'] = $request['info_graphic_note'];
        $campaignTypeAContent['date_created'] = Carbon::now();
        $campaignTypeAContent['asset_id'] = $asset_id;

        $campaignTypeAContent->save();

        // insert note for adding asset
//        $this->add_asset_correspondence($campaignAssetIndex['campaign_id'], $campaignAssetIndex['type'], $asset_id, ucwords(str_replace('_', ' ', $campaignAssetIndex['status'])));

        // new correspondence when adding asset
        $this->new_add_asset_correspondence($campaignAssetIndex['campaign_id'], 'Info Graphic', $campaignAssetIndex);

        if($request->file('info_graphic_c_attachment')){
            foreach ($request->file('info_graphic_c_attachment') as $file) {
                $campaign_type_asset_attachments = new CampaignTypeAssetAttachments();
                $fileName = $this->file_exist_check($file, $request['info_graphic_c_id'], $asset_id);
                $campaign_type_asset_attachments['id'] = $request['info_graphic_c_id'];
                $campaign_type_asset_attachments['asset_id'] = $asset_id;
                $campaign_type_asset_attachments['type'] = 'attachment_file_' . $file->getMimeType();
                $campaign_type_asset_attachments['author_id'] = $request['info_graphic_author_id'];
                $campaign_type_asset_attachments['attachment'] = '/' . $fileName;
                $campaign_type_asset_attachments['file_ext'] = pathinfo($fileName, PATHINFO_EXTENSION);
                $campaign_type_asset_attachments['file_type'] = $file->getMimeType();
                $campaign_type_asset_attachments['file_size'] = $file->getSize();
                $campaign_type_asset_attachments['date_created'] = Carbon::now();
                $campaign_type_asset_attachments->save();
            }
        }

        // TODO notification
        // Send notification to copywriter(brand check) via email
        // Do action - copy request
        if($campaignAssetIndex['status'] == 'copy_requested') { // only copy_requested, send notification to copy writers
            $notify = new NotifyController();
            $notify->copy_request($request['info_graphic_c_id'], $asset_id);
        } else if($campaignAssetIndex['status'] == 'copy_complete') {
            $notify = new NotifyController();
            $notify->copy_complete($request['info_graphic_c_id'], $asset_id);
        }
        ///////////////////////////////////////////////////////////////

        return redirect('admin/campaign/'.$request['info_graphic_c_id'].'/edit#'.$asset_id)
            ->with('success', __('Added the A+ Content Asset : ' . $asset_id));
    }

    public function edit_info_graphic(Request $request, $asset_id){

        $info_graphic = $this->campaignTypeInfoGraphicRepository->findById($asset_id);

        $param = $request->request->all();

        // Permission_check
        if(!$this->permission_check($param)){
            return redirect('admin/campaign/' . $info_graphic->id . '/edit#'.$asset_id)
                ->with('error', __('This action is no longer permitted. Please contact an Administrator.'));
        }

        if($this->campaignTypeInfoGraphicRepository->update($asset_id, $param)){
            $user = auth()->user();
            // insert into campaign note for correspondence
            $this->add_correspondence('info_graphic', $param, $info_graphic, $user);
            if($request->file('c_attachment')){
                $user = auth()->user();
                foreach ($request->file('c_attachment') as $file) {
                    $campaign_type_asset_attachments = new CampaignTypeAssetAttachments();

//                    $fileName = $file->store('campaigns/'.$image_request->id.'/'.$asset_id);
                    $fileName = $this->file_exist_check($file, $info_graphic->id, $asset_id);

                    $campaign_type_asset_attachments['id'] = $info_graphic->id;
                    $campaign_type_asset_attachments['asset_id'] = $asset_id;
                    $campaign_type_asset_attachments['type'] = 'attachment_file_' . $file->getMimeType();
                    $campaign_type_asset_attachments['author_id'] = $user->id;
                    $campaign_type_asset_attachments['attachment'] = '/' . $fileName;
                    $campaign_type_asset_attachments['file_ext'] = pathinfo($fileName, PATHINFO_EXTENSION);
                    $campaign_type_asset_attachments['file_type'] = $file->getMimeType();
                    $campaign_type_asset_attachments['file_size'] = $file->getSize();
                    $campaign_type_asset_attachments['date_created'] = Carbon::now();
                    $campaign_type_asset_attachments->save();

                    // insert file attachment on asset correspondence
                    $this->add_file_correspondence_for_asset($info_graphic, $user, $fileName, 'info_graphic');
                }
            }
            return redirect('admin/campaign/'.$info_graphic->id.'/edit#'.$asset_id)
                ->with('success', __('A Content ('.$asset_id.') - Update Success'));
        }
        return redirect('admin/campaign/'.$info_graphic->id.'/edit#'.$asset_id)
            ->with('error', __('Update Failed'));
    }


    public function add_correspondence($asset_type, $new_param, $origin_param, $user)
    {
        // Insert into campaign note for correspondence

        $new = $this->get_asset_param($asset_type, $new_param);
        $origin = $origin_param->toArray();

        foreach ($new as $key => $value) {
            if (array_key_exists($key, $origin)) {
                if (html_entity_decode($new[$key]) != html_entity_decode($origin[$key])) {
                    $changed[$key]['new'] = $new[$key];
                    $changed[$key]['original'] = $origin[$key];
                }
            }
        }
        $asset_type_ = ucwords(str_replace('_', ' ', $asset_type));
        $change_line  = "<p>$user->first_name made a change to a $asset_type_ (#$origin_param->asset_id)</p>";

        if(!empty($changed)){
            foreach ($changed as $label => $change) {

                $label = ucwords(str_replace('_', ' ', $label));
                $from  = trim($change['original']); // Remove strip tags
                $to    = trim($change['new']);      // Remove strip tags

                $change_line .= "<div class='change_label'><p>$label:</p></div>"
                    . "<div class='change_to'><p>$to</p></div>"
                    . "<div class='change_from'><del><p>$from</p></del></div>";
            }
            $campaign_note = new CampaignNotes();
            $campaign_note['id'] = $origin_param->id;
            $campaign_note['user_id'] = $user->id;
            $campaign_note['asset_id'] = NULL;
            $campaign_note['type'] = $asset_type;
            $campaign_note['note'] = $change_line;
            $campaign_note['date_created'] = Carbon::now();
            $campaign_note->save();
        }
    }

    public function get_asset_param($asset_type, $data)
    {
        if($asset_type == 'email_blast') {
            $new = array(
                'concept' => $data['concept'],
                'products_featured' => $data['products_featured'],
                'main_subject_line' => $data['main_subject_line'],
                'main_preheader_line' => $data['main_preheader_line'],
                'alt_subject_line' => $data['alt_subject_line'],
                'alt_preheader_line' => $data['alt_preheader_line'],
                'body_copy' => $data['body_copy'],
                'click_through_links' => $data['click_through_links'],
                'email_list' => $data['email_list'],
                'email_blast_date' => $data['email_blast_date'],
                'video_link' => $data['video_link']
            );
            return $new;
        }else if($asset_type == 'social_ad'){
            $new = array(
                'date_from' => $data['date_from'],
                'date_to' => $data['date_to'],
                'include_formats' => $data['include_formats'],
                'text' => $data['text'],
                'headline' => $data['headline'],
                'note' => $data['note'],
                'newsfeed' => $data['newsfeed'],
                'products_featured' => $data['products_featured'],
                'copy_inside_graphic' => $data['copy_inside_graphic'],
                'click_through_links' => $data['click_through_links'],
                'google_drive_link' => $data['google_drive_link'],
                'utm_code' => $data['utm_code'],
                'promo_code' => $data['promo_code'],
                'budget_code' => $data['budget_code'],
            );
            return $new;
        }else if($asset_type == 'website_banners'){
            $new = array(
                'launch_date' => $data['launch_date'],
                'banner' => $data['banner'],
                'details' => $data['details'],
                'copy' => $data['copy'],
                'products_featured' => $data['products_featured'],
                'click_through_links' => $data['click_through_links'],
            );
            return $new;
        }else if($asset_type == 'website_changes'){
            $new = array(
                'launch_date' => $data['launch_date'],
                'details' => $data['details'],
                'products_featured' => $data['products_featured'],
                'copy' => $data['copy'],
                'developer_url' => $data['developer_url'],
            );
            return $new;
        }else if($asset_type == 'landing_page'){
            $new = array(
                'launch_date' => $data['launch_date'],
                'details' => $data['details'],
                'copy' => $data['copy'],
                'products_featured' => $data['products_featured'],
                'landing_url' => $data['landing_url'],
            );
            return $new;
        }else if($asset_type == 'misc'){
            $new = array(
                'title' => $data['title'],
                'launch_date' => $data['launch_date'],
                'details' => $data['details'],
                'products_featured' => $data['products_featured'],
                'copy' => $data['copy'],
                'developer_url' => $data['developer_url'],
            );
            return $new;
        }else if($asset_type == 'sms_request'){
            $new = array(
                'title' => $data['title'],
                'launch_date' => $data['launch_date'],
                'details' => $data['details'],
                'products_featured' => $data['products_featured'],
                'copy' => $data['copy'],
                'developer_url' => $data['developer_url'],
            );
            return $new;
        }else if($asset_type == 'topcategories_copy'){
            $new = array(
                'launch_date' => $data['launch_date'],
                'products_featured' => $data['products_featured'],
                'copy' => $data['copy'],
                'click_through_links' => $data['click_through_links'],
            );
            return $new;
        }else if($asset_type == 'programmatic_banners'){
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
        }else if($asset_type == 'image_request'){
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
        }else if($asset_type == 'roll_over'){
            $new = array(
                'launch_date' => $data['launch_date'],
                'sku' => $data['sku'],
                'products_featured' => $data['products_featured'],
                'notes' => $data['notes'],
            );
            return $new;
        }else if($asset_type == 'store_front'){
            $new = array(
                'launch_date' => $data['launch_date'],
                'client' => $data['client'],
                'products_featured' => $data['products_featured'],
                'notes' => $data['notes'],
            );
            return $new;
        }else if($asset_type == 'a_content'){
            $new = array(
                'launch_date' => $data['launch_date'],
                'product_line' => $data['product_line'],
                'invision_link' => $data['invision_link'],
                'products_featured' => $data['products_featured'],
                'note' => $data['note'],
            );
            return $new;
        }else if($asset_type == 'youtube_copy'){
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
        }else if($asset_type == 'info_graphic'){
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

    public function add_asset_correspondence($c_id, $asset_type, $asset_id, $status)
    {
        // Insert into campaign note for correspondence (attachment file)
        $user = auth()->user();
        $asset_type_ =  ucwords(str_replace('_', ' ', $asset_type));
        $change_line  = "<p>$user->first_name $status for $asset_type_ (#$asset_id)</p>";

        $campaign_note = new CampaignNotes();
        $campaign_note['id'] = $c_id;
        $campaign_note['user_id'] = $user->id;
        $campaign_note['asset_id'] = $asset_id;
        $campaign_note['type'] = $asset_type;
        $campaign_note['note'] = $change_line;
        $campaign_note['date_created'] = Carbon::now();
        $campaign_note->save();
    }

    public function add_file_correspondence_for_asset($asset, $user, $file_type, $asset_type)
    {
        // Insert into campaign note for correspondence (attachment file)
        $asset_type_ =  ucwords(str_replace('_', ' ', $asset_type));
        $change_line  = "<p>$user->first_name Has Added a new attachment ($file_type) to <b style='color: #b91d19;'>$asset_type_</b> <b>(#$asset->asset_id)</b></p>";

        $campaign_note = new CampaignNotes();
        $campaign_note['id'] = $asset->id;
        $campaign_note['user_id'] = $user->id;
        $campaign_note['asset_id'] = 0;
        $campaign_note['type'] = $asset_type;
        $campaign_note['note'] = $change_line;
        $campaign_note['date_created'] = Carbon::now();
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

}
