<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CampaignRequest;
use App\Models\CampaignNotes;

use App\Models\CampaignTypeAssetAttachments;
use App\Repositories\Admin\CampaignAssetIndexRepository;
use App\Repositories\Admin\CampaignNotesRepository;
use App\Repositories\Admin\CampaignRepository;
use App\Repositories\Admin\CampaignBrandsRepository;
use App\Repositories\Admin\CampaignTypeAssetAttachmentsRepository;
use App\Repositories\Admin\CampaignTypeEmailBlastRepository;
use App\Repositories\Admin\CampaignTypeLandingPageRepository;
use App\Repositories\Admin\CampaignTypeMiscRepository;
use App\Repositories\Admin\CampaignTypeProgrammaticBannersRepository;
use App\Repositories\Admin\CampaignTypeSocialAdRepository;
use App\Repositories\Admin\CampaignTypeTopcategoriesCopyRepository;
use App\Repositories\Admin\CampaignTypeWebsiteBannersRepository;
use App\Repositories\Admin\CampaignTypeWebsiteChangesRepository;
use App\Repositories\Admin\PermissionRepository;

use App\Repositories\Admin\UserRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DeletedController extends Controller
{
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
    private $campaignTypeTopcategoriesCopyRepository;
    private $campaignTypeProgrammaticBannersRepository;
    private $campaignAssetIndexRepository;
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
                                CampaignTypeTopcategoriesCopyRepository $campaignTypeTopcategoriesCopyRepository,
                                CampaignTypeProgrammaticBannersRepository $campaignTypeProgrammaticBannersRepository,
                                CampaignAssetIndexRepository $campaignAssetIndexRepository,
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
        $this->campaignTypeTopcategoriesCopyRepository = $campaignTypeTopcategoriesCopyRepository;
        $this->campaignTypeProgrammaticBannersRepository = $campaignTypeProgrammaticBannersRepository;
        $this->campaignAssetIndexRepository = $campaignAssetIndexRepository;
        $this->userRepository = $userRepository;
        $this->permissionRepository = $permissionRepository;

    }

    public function index(Request $request)
    {
        $params = $request->all();
        $params['status'] = 'deleted';
        $this->data['currentAdminMenu'] = 'deleted';

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

        return view('admin.campaign.deleted', $this->data);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->data['currentAdminMenu'] = 'deleted';
        $this->data['brands'] = $this->campaignBrandsRepository->findAll()->pluck('campaign_name', 'id');

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
        $this->data['asset_type'] = $campaign->asset_type;

        $this->data['promotion'] = $campaign->promotion;
        $this->data['assignee'] = $campaign->assignee;
        $this->data['retailer'] = $campaign->retailer;
        $this->data['author_name'] = $campaign->author_name;

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
            }
        }

        // social_ad, website_banners_fileds
        $this->data['social_ad_fields'] = [
            'FB/IG Carousel Ad',
            'FB/IG GIF Ad',
            'FB/IG Image Ad',
            'FB/IG Organic Awareness Post',
            'FB/IG Organic Stories Image Ad',
            'FB/IG Organic Stories Video Ad',
            'FB/IG Video Ad'
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

        $this->data['team_to_list'] = [
            "creative",
            "content",
            "web production"
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
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->data['currentAdminMenu'] = 'campaign';
        $campaign = $this->campaignRepository->findById($id);
        if($this->campaignRepository->delete($id)){
            return redirect('admin/campaign')
                ->with('success', __('Removed the Campaign : ', ['first_name' => $campaign->name]));
        }
        return redirect('admin/campaign')
            ->with('error', __('Fail to delete : ', ['first_name' => $campaign->name]));
    }


}
