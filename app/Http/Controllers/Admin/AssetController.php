<?php

namespace App\Http\Controllers\Admin;

use App\Authorizable;
use App\Http\Controllers\Controller;
use App\Http\Controllers\NotifyController;
use App\Http\Requests\Admin\AssetEmailBlastRequest;
use App\Http\Requests\Admin\AssetLandingPageRequest;
use App\Http\Requests\Admin\AssetMiscRequest;
use App\Http\Requests\Admin\AssetProgrammaticBannersRequest;
use App\Http\Requests\Admin\AssetSocialAdRequest;
use App\Http\Requests\Admin\AssetTopcategoriesCopyRequest;
use App\Http\Requests\Admin\AssetWebsiteBannersRequest;
use App\Http\Requests\Admin\AssetWebsiteChangesRequest;
use App\Http\Requests\Admin\CampaignRequest;
use App\Http\Requests\Admin\UserRequest;
use App\Mail\AssetMessage;
use App\Mail\Todo;
use App\Models\CampaignAssetIndex;
use App\Models\CampaignNotes;

use App\Models\CampaignTypeAssetAttachments;
use App\Models\CampaignTypeEmailBlast;
use App\Models\CampaignTypeLandingPage;
use App\Models\CampaignTypeMisc;
use App\Models\CampaignTypeProgrammaticBanners;
use App\Models\CampaignTypeSocialAd;
use App\Models\CampaignTypeTopcategoriesCopy;
use App\Models\CampaignTypeWebsiteBanners;
use App\Models\CampaignTypeWebsiteChanges;
use App\Repositories\Admin\AssetLeadTimeRepository;
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

use Illuminate\Support\Facades\Log;

use Mail;

class AssetController extends Controller
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
    private $campaignTypeSmsRequestRepository;
    private $campaignTypeTopcategoriesCopyRepository;
    private $campaignTypeProgrammaticBannersRepository;
    private $campaignTypeImageRequestRepository;
    private $campaignTypeRollOverRepository;
    private $campaignTypeStoreFrontRepository;
    private $campaignTypeAContentRepository;
    private $userRepository;
    private $assetNotificationUserRepository;
    private $assetOwnerAssetsRepository;
    private $campaignAssetIndexRepository;
    private $assetLeadTimeRepository;

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
                                CampaignAssetIndexRepository $campaignAssetIndexRepository,
                                UserRepository $userRepository,
                                AssetNotificationUserRepository $assetNotificationUserRepository,
                                AssetOwnerAssetsRepository $assetOwnerAssetsRepository,
                                AssetLeadTimeRepository $assetLeadTimeRepository,
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
        $this->campaignAssetIndexRepository = $campaignAssetIndexRepository;
        $this->userRepository = $userRepository;
        $this->assetNotificationUserRepository =$assetNotificationUserRepository;
        $this->assetOwnerAssetsRepository = $assetOwnerAssetsRepository;
        $this->assetLeadTimeRepository = $assetLeadTimeRepository;
        $this->permissionRepository = $permissionRepository;

    }

    public function index(Request $request)
    {
        $this->data['currentAdminMenu'] = 'asset';
        return view('admin.asset.index', $this->data);
    }

    public function asset_approval(Request $request)
    {
        $this->data['currentAdminMenu'] = 'asset_approval';
        $params = $request->all();
        $str = !empty($params['q']) ? $params['q'] : '';
        $asset_id = !empty($params['asset_id']) ? $params['asset_id'] : '';
        $campaign_id = !empty($params['campaign_id']) ? $params['campaign_id'] : '';

        $this->data['asset_list'] = $this->campaignAssetIndexRepository->get_complete_assets_list($str, $asset_id, $campaign_id);
        $this->data['filter'] = $params;
        $this->data['team'] = 'Creative';

        return view('admin.asset.approval', $this->data);
    }

    public function asset_kpi(Request $request)
    {
        $this->data['currentAdminMenu'] = 'asset_kpi';
        $params = $request->all();
        $str = !empty($params['q']) ? $params['q'] : '';
        $asset_id = !empty($params['asset_id']) ? $params['asset_id'] : '';
        $campaign_id = !empty($params['campaign_id']) ? $params['campaign_id'] : '';
        $search_from = !empty($params['search_from']) ? $params['search_from'] : '';
        $search_to = !empty($params['search_to']) ? $params['search_to'] : '';
        if(isset($_GET['designer'])){
            $designer = $params['designer'];
        }else{
            $designer = '';
        }
        $this->data['designer'] = $designer;
        $this->data['designers'] = $this->userRepository->getCreativeAssignee();
        $this->data['asset_list'] = $this->campaignAssetIndexRepository->get_kpi_assets_list($str, $asset_id, $campaign_id, $designer, $search_from, $search_to);
        $this->data['filter'] = $params;

        return view('admin.asset.kpi', $this->data);
    }

    public function asset_kpi_content(Request $request)
    {
        $this->data['currentAdminMenu'] = 'asset_kpi_content';
        $params = $request->all();
        $str = !empty($params['q']) ? $params['q'] : '';
        $asset_id = !empty($params['asset_id']) ? $params['asset_id'] : '';
        $campaign_id = !empty($params['campaign_id']) ? $params['campaign_id'] : '';
        $search_from = !empty($params['search_from']) ? $params['search_from'] : '';
        $search_to = !empty($params['search_to']) ? $params['search_to'] : '';
        if(isset($_GET['designer'])){
            $designer = $params['designer'];
        }else{
            $designer = '';
        }
        $this->data['designer'] = $designer;
        $this->data['designers'] = $this->userRepository->getContentAssignee();
        $this->data['asset_list'] = $this->campaignAssetIndexRepository->get_kpi_content_assets_list($str, $asset_id, $campaign_id, $designer, $search_from, $search_to);
        $this->data['filter'] = $params;

        return view('admin.asset.kpi_content', $this->data);
    }

    public function asset_kpi_web(Request $request)
    {
        $this->data['currentAdminMenu'] = 'asset_kpi_web';
        $params = $request->all();
        $str = !empty($params['q']) ? $params['q'] : '';
        $asset_id = !empty($params['asset_id']) ? $params['asset_id'] : '';
        $campaign_id = !empty($params['campaign_id']) ? $params['campaign_id'] : '';
        $search_from = !empty($params['search_from']) ? $params['search_from'] : '';
        $search_to = !empty($params['search_to']) ? $params['search_to'] : '';
        if(isset($_GET['designer'])){
            $designer = $params['designer'];
        }else{
            $designer = '';
        }
        $this->data['designer'] = $designer;
        $this->data['designers'] = $this->userRepository->getWebAssignee();
        $this->data['asset_list'] = $this->campaignAssetIndexRepository->get_kpi_web_assets_list($str, $asset_id, $campaign_id, $designer, $search_from, $search_to);
        $this->data['filter'] = $params;

        return view('admin.asset.kpi_web', $this->data);
    }

    public function asset_kpi_copy(Request $request)
    {
        $this->data['currentAdminMenu'] = 'asset_kpi_copy';
        $params = $request->all();
        $str = !empty($params['q']) ? $params['q'] : '';
        $asset_id = !empty($params['asset_id']) ? $params['asset_id'] : '';
        $campaign_id = !empty($params['campaign_id']) ? $params['campaign_id'] : '';
        $search_from = !empty($params['search_from']) ? $params['search_from'] : '';
        $search_to = !empty($params['search_to']) ? $params['search_to'] : '';
        if(isset($_GET['designer'])){
            $designer = $params['designer'];
        }else{
            $designer = '';
        }
        $this->data['designer'] = $designer;
        $this->data['designers'] = $this->userRepository->getCopyWriterAssignee();
        $this->data['asset_list'] = $this->campaignAssetIndexRepository->get_kpi_copy_assets_list($str, $asset_id, $campaign_id, $designer, $search_from, $search_to);
        $this->data['filter'] = $params;

        return view('admin.asset.kpi_copy', $this->data);
    }

    public function asset_approval_copy(Request $request)
    {
        $this->data['currentAdminMenu'] = 'asset_approval_copy';
        $params = $request->all();
        $str = !empty($params['q']) ? $params['q'] : '';
        $asset_id = !empty($params['asset_id']) ? $params['asset_id'] : '';
        $campaign_id = !empty($params['campaign_id']) ? $params['campaign_id'] : '';
        $brand_id = !empty($params['brand_id']) ? $params['brand_id'] : '';

        $this->data['brand_id'] = $brand_id;
        $this->data['asset_list'] = $this->campaignAssetIndexRepository->get_request_assets_list_copy($str, $asset_id, $campaign_id, $brand_id);
        $this->data['filter'] = $params;
        $this->data['team'] = 'Copy';

        $this->data['brands'] = $this->campaignBrandsRepository->findAll()->pluck('campaign_name', 'id');

        return view('admin.asset.approval_copy', $this->data);
    }
    public function asset_approval_content(Request $request)
    {
        $this->data['currentAdminMenu'] = 'asset_approval_content';
        $params = $request->all();
        $str = !empty($params['q']) ? $params['q'] : '';
        $asset_id = !empty($params['asset_id']) ? $params['asset_id'] : '';
        $campaign_id = !empty($params['campaign_id']) ? $params['campaign_id'] : '';

        $this->data['asset_list'] = $this->campaignAssetIndexRepository->get_complete_assets_list_content($str, $asset_id, $campaign_id);
        $this->data['filter'] = $params;
        $this->data['team'] = 'Content';

        return view('admin.asset.approval', $this->data);
    }

    public function asset_approval_web(Request $request)
    {
        $this->data['currentAdminMenu'] = 'asset_approval_web';
        $params = $request->all();
        $str = !empty($params['q']) ? $params['q'] : '';
        $asset_id = !empty($params['asset_id']) ? $params['asset_id'] : '';
        $campaign_id = !empty($params['campaign_id']) ? $params['campaign_id'] : '';

        $this->data['asset_list'] = $this->campaignAssetIndexRepository->get_complete_assets_list_web($str, $asset_id, $campaign_id);
        $this->data['filter'] = $params;
        $this->data['team'] = 'Web Production';

        return view('admin.asset.approval', $this->data);
    }

    public function asset_detail($a_id, $c_id, $a_type)
    {
        $this->data['asset_obj'] = $asset_obj = $this->campaignAssetIndexRepository->findById($a_id);
        if($asset_obj['team_to'] == 'content'){
            $this->data['currentAdminMenu'] = 'asset_approval_content';
        }else if($asset_obj['team_to'] == 'web production'){
            $this->data['currentAdminMenu'] = 'asset_approval_web';
        }else{
            $this->data['currentAdminMenu'] = 'asset_approval';
        }
        $this->data['asset_id'] = $a_id;
        $this->data['a_type'] = $a_type;
        $this->data['c_id'] = $c_id;
        $this->data['asset_detail'] = $asset_detail = $this->campaignRepository->get_asset_detail($a_id, $c_id, $a_type);

        $this->data['lead_time'] = $this->assetLeadTimeRepository->getByAssetType($a_type);
        $author_id = $asset_detail[0]->author_id;

        $user_obj = $this->userRepository->findById($author_id);
        $this->data['asset_creator'] = $user_obj->first_name . ' ' . $user_obj->last_name;
        $this->data['asset_files'] = $this->campaignTypeAssetAttachmentsRepository->findAllByAssetId($a_id);

        $this->data['team_to_list'] = [
            "creative",
            "content",
            "web production"
        ];
        $this->data['copy_writers'] = $this->userRepository->getCopyWriterAssignee();

        $this->data['assignees_designer'] = $this->userRepository->getCreativeAssignee();
//        $this->data['assignees_creative'] = $this->userRepository->getCreativeAssignee();
        $this->data['assignees_content'] = $this->userRepository->getContentAssignee();
        $this->data['assignees_web'] = $this->userRepository->getWebAssignee();

        return view('admin.asset.detail', $this->data);
    }

    public function asset_detail_copy($a_id, $c_id, $a_type, $brand)
    {
        $this->data['asset_obj'] = $this->campaignAssetIndexRepository->findById($a_id);
        $this->data['currentAdminMenu'] = 'asset_approval_copy';
        $this->data['asset_id'] = $a_id;
        $this->data['a_type'] = $a_type;
        $this->data['c_id'] = $c_id;
        $this->data['brand'] = $brand;
        $this->data['asset_detail'] = $asset_detail = $this->campaignRepository->get_asset_detail($a_id, $c_id, $a_type);
        $this->data['lead_time'] = $this->assetLeadTimeRepository->getByAssetType($a_type);
        $author_id = $asset_detail[0]->author_id;

        $user_obj = $this->userRepository->findById($author_id);
        $this->data['asset_creator'] = $user_obj->first_name . ' ' . $user_obj->last_name;
        $this->data['asset_files'] = $this->campaignTypeAssetAttachmentsRepository->findAllByAssetId($a_id);

        $this->data['assignees_copywriter'] = $this->userRepository->getCopyWriterAssignee();

        return view('admin.asset.detail_copy', $this->data);
    }

    public function asset_assign_copy(Request $request)
    {
        $param = $request->all();
        $params['id'] = $param['a_id'];
        $c_id = $param['c_id'];
        $a_type = $param['a_type'];
        $params['campaign_id'] = $param['c_id'];
        $params['type'] = $param['a_type'];
        $params['status'] = 'copy_to_do';
        $params['copy_writer'] = $param['copy_writer'];
        $params['copy_assigned_at'] = Carbon::now();
        $params['updated_at'] = Carbon::now();

        $rs_array = $this->campaignAssetIndexRepository->get_copy_target_date($param['a_id'], $a_type);

        $params['copy_target_at'] = $rs_array[0];
        $params['copy_delay'] = $rs_array[1];

        $this->campaignAssetIndexRepository->update($param['a_id'], $params);

        $this->add_asset_correspondence($c_id, $a_type, $param['a_id'], ' assigned to ' . $params['copy_writer'] . ' for ' , null);

        // TODO notification
        $notify = new NotifyController();
        $notify->copy_to_do($c_id, $param['a_id'], $param['copy_writer']);
//        Log::info('email sent!!');
        ////////////

        $this->data['currentAdminMenu'] = 'asset_approval_copy';
        $asset_type = strtoupper(str_replace('_', ' ', $param['a_type']));
        return redirect('admin/asset_approval_copy')
            ->with('success', __('['.$asset_type.']' . ' Asset ID : '. $param['a_id'] .' assigned to '.$param['copy_writer'].'.'));
    }

    public function asset_team_change(Request $request)
    {
        $param = $request->all();
        $params['id'] = $param['a_id'];
        $c_id = $param['c_id'];
        $a_type = $param['a_type'];
        $params['campaign_id'] = $param['c_id'];
        $params['status'] = 'copy_complete';
        $params['team_to'] = $param['team_to_change'];
        $params['assignee'] = null;
        $params['updated_at'] = Carbon::now();

        $this->campaignAssetIndexRepository->update($param['a_id'], $params);

        $this->add_asset_correspondence($c_id, $a_type, $param['a_id'], ' changed team to ' . $params['team_to'] . ' for ', null);

        // TODO notification
        $notify = new NotifyController();
        $notify->copy_complete($c_id, $param['a_id']);
//        Log::info('email sent!!');
        ////////////
        $this->data['currentAdminMenu'] = 'campaign';

        $asset_type = strtoupper(str_replace('_', ' ', $param['a_type']));
        return redirect('admin/campaign/'.$c_id.'/edit#'.$param['a_id'])
            ->with('success', __('['.$asset_type.']' . ' Asset ID : '. $param['a_id'] .'  Changed Team to '. $params['team_to'] .'.'));
    }

    public function asset_assign(Request $request)
    {

        $param = $request->all();
        $params['id'] = $param['a_id'];
        $c_id = $param['c_id'];
        $a_type = $param['a_type'];
        $params['campaign_id'] = $param['c_id'];
        $params['type'] = $param['a_type'];
        $params['status'] = 'to_do';
        $params['assignee'] = $param['assignee'];
        $params['updated_at'] = Carbon::now();
        $params['assigned_at'] = Carbon::now();

        $rs_array = $this->campaignAssetIndexRepository->get_target_date($param['a_id'], $a_type);

        $params['target_at'] = $rs_array[0];
        $params['delay'] = $rs_array[1];

        $this->campaignAssetIndexRepository->update($param['a_id'], $params);

        $this->add_asset_correspondence($c_id, $a_type, $param['a_id'], ' assigned to ' . $params['assignee'] . ' for ', null);

        // TODO notification
        $notify = new NotifyController();
        $notify->to_do($c_id, $param['a_id'], $param['assignee']);
//        Log::info('email sent!!');
        ////////////

        $asset_obj = $this->campaignAssetIndexRepository->findById($param['a_id']);
        if($asset_obj['team_to'] == 'content'){
            $this->data['currentAdminMenu'] = 'asset_approval_content';
            $addr = 'asset_approval_content';
        }else if($asset_obj['team_to'] == 'web production'){
            $this->data['currentAdminMenu'] = 'asset_approval_web';
            $addr = 'asset_approval_web';
        }else{
            $this->data['currentAdminMenu'] = 'asset_approval';
            $addr = 'asset_approval';
        }

        $this->data['currentAdminMenu'] = 'asset_assign';

        $asset_type = strtoupper(str_replace('_', ' ', $param['a_type']));
        return redirect('admin/'.$addr)
            ->with('success', __('['.$asset_type.']' . ' Asset ID : '. $param['a_id'] .'  has been approved and assigned to '.$param['assignee'].'.'));
    }

    public function asset_assign_change(Request $request)
    {

        $param = $request->all();
        $params['id'] = $param['a_id'];
        $c_id = $param['c_id'];
        $a_type = $param['a_type'];
        $params['campaign_id'] = $param['c_id'];
        $params['type'] = $param['a_type'];
        $params['status'] = 'to_do';
        $params['assignee'] = $param['assignee'];
        $params['updated_at'] = Carbon::now();

        $this->campaignAssetIndexRepository->update($param['a_id'], $params);

        $this->add_asset_correspondence($c_id, $a_type, $param['a_id'], ' assigned to ' . $params['assignee'] . ' for ', null);

        // TODO notification
        $notify = new NotifyController();
        $notify->to_do($c_id, $param['a_id'], $param['assignee']);
//        Log::info('email sent!!');
        ////////////
        $this->data['currentAdminMenu'] = 'asset_assign';

        $asset_type = strtoupper(str_replace('_', ' ', $param['a_type']));
        return redirect('admin/campaign/'.$c_id.'/edit#'.$param['a_id'])
            ->with('success', __('['.$asset_type.']' . ' Asset ID : '. $param['a_id'] .' assigned '.$param['assignee'].'.'));
    }

    public function asset_copy_writer_change(Request $request)
    {

        $param = $request->all();
        $params['id'] = $param['a_id'];
        $c_id = $param['c_id'];
        $a_type = $param['a_type'];
        $params['campaign_id'] = $param['c_id'];
        $params['type'] = $param['a_type'];
        $params['status'] = 'copy_to_do';
        $params['copy_writer'] = $param['copy_writer'];
        $params['updated_at'] = Carbon::now();

        $this->campaignAssetIndexRepository->update($param['a_id'], $params);

        $this->add_asset_correspondence($c_id, $a_type, $param['a_id'], ' assigned to ' . $params['copy_writer'] . ' for ', null);

        // TODO notification
        $notify = new NotifyController();
        $notify->copy_to_do($c_id, $param['a_id'], $param['copy_writer']);
//        Log::info('email sent!!');
        ////////////
        $this->data['currentAdminMenu'] = 'asset_approval_copy';

        $asset_type = strtoupper(str_replace('_', ' ', $param['a_type']));
        return redirect('admin/campaign/'.$c_id.'/edit#'.$param['a_id'])
            ->with('success', __('['.$asset_type.']' . ' Asset ID : '. $param['a_id'] .' assigned to '.$param['copy_writer'].'.'));
    }

    public function asset_decline_copy(Request $request)
    {
        $param = $request->all();
        $params['id'] = $param['a_id'];
        $c_id = $param['c_id'];
        $a_type = $param['a_type'];
        $params['status'] = 'copy_to_do';
        $params['decline_copy'] = $param['decline_copy'];
        $params['updated_at'] = Carbon::now();

        $this->campaignAssetIndexRepository->update($param['a_id'], $params);

        $this->add_asset_correspondence($c_id, $a_type, $param['a_id'], ' declined the ', $params['decline_copy']);

        // TODO notification
        // Send notification to copywriter via email
        // Do action - decline from copy
        $notify = new NotifyController();
        $notify->decline_from_copy($c_id, $param['a_id'], $params);

        $this->data['currentAdminMenu'] = 'asset_assign';

        $asset_type = strtoupper(str_replace('_', ' ', $param['a_type']));
        return redirect('admin/campaign/'.$c_id.'/edit#'.$param['a_id'])
            ->with('success', __('['.$asset_type.']' . ' Asset ID : '. $param['a_id'] .'  has been declined.'));
    }

    public function asset_decline_creative(Request $request)
    {
        $param = $request->all();
        $params['id'] = $param['a_id'];
        $c_id = $param['c_id'];
        $a_type = $param['a_type'];
        $params['status'] = 'copy_review';
        $params['decline_creative'] = $param['decline_creative'];
        $params['updated_at'] = Carbon::now();

        $this->campaignAssetIndexRepository->update($param['a_id'], $params);

        $this->add_asset_correspondence($c_id, $a_type, $param['a_id'], 'declined the ', $params['decline_creative']);

        // TODO notification
        // Send notification to task creator via email
        // Do action - decline from creative
        $notify = new NotifyController();
        $notify->decline_from_creative($c_id, $param['a_id'], $params);

        $this->data['currentAdminMenu'] = 'asset_assign';

        $asset_type = strtoupper(str_replace('_', ' ', $param['a_type']));
        return redirect('admin/campaign/'.$c_id.'/edit#'.$param['a_id'])
            ->with('success', __('['.$asset_type.']' . ' Asset ID : '. $param['a_id'] .'  has been declined.'));
    }

    public function asset_decline_kec(Request $request)
    {
        $param = $request->all();
        $params['id'] = $param['a_id'];
        $params['c_id'] = $param['c_id'];
        $c_id = $param['c_id'];
        $a_type = $param['a_type'];
        $params['status'] = 'to_do';
        $params['decline_kec'] = $param['decline_kec'];
        $params['updated_at'] = Carbon::now();

        $this->campaignAssetIndexRepository->update($param['a_id'], $params);

        $this->add_asset_correspondence($c_id, $a_type, $param['a_id'], 'declined the ', $params['decline_kec']);

        // TODO notification
        // send notification to designer via email
        // Do action - decline from KEC
        $notify = new NotifyController();
        $notify->decline_from_kec($c_id, $param['a_id'], $params);

        $this->data['currentAdminMenu'] = 'asset_assign';

        $asset_type = strtoupper(str_replace('_', ' ', $param['a_type']));
        return redirect('admin/campaign/'.$c_id.'/edit#'.$param['a_id'])
            ->with('success', __('['.$asset_type.']' . ' Asset ID : '. $param['a_id'] .'  has been declined.'));
    }

    public function asset_jira(Request $request)
    {
        $param = $request->all();
        $this->data['currentAdminMenu'] = 'asset_jira';

//        $user = auth()->user();
//        if($user->team == 'Creative' && $user->role == 'graphic designer') {
//            if(isset($_GET['q'])){ // if search with nothing..
//                $str = $param['q'];
//            }else{ // if come first time..
//                $str = $param['q'] = $user->first_name;
//            }
//        }else{
//            $str = !empty($param['q']) ? $param['q'] : '';
//        }

        $this->data['designers'] = $this->userRepository->getCreativeAssignee();

        if(isset($_GET['designer'])){
            $designer = $param['designer'];
        }else{
            $designer = '';
        }

        if(isset($_GET['brand'])) {
            $brand_id = $param['brand'];
        }else{
            $brand_id = !empty($param['brand']) ? $param['brand'] : '';
        }

        if(isset($_GET['asset_id'])) {
            $asset_id = $param['asset_id'];
        }else{
            $asset_id = !empty($param['asset_id']) ? $param['asset_id'] : '';
        }

        $this->data['brand'] = $brand_id;
        $this->data['designer'] = $designer;
        $this->data['asset_id'] = $asset_id;

        $this->data['filter'] = $param;

        $this->data['asset_list_todo'] = $this->campaignAssetIndexRepository->get_asset_jira_to_do_creative($designer, $brand_id, $asset_id, 'creative');
        $this->data['asset_list_progress'] = $this->campaignAssetIndexRepository->get_asset_jira_in_progress_creative($designer, $brand_id, $asset_id, 'creative');
        $this->data['asset_list_done'] = $this->campaignAssetIndexRepository->get_asset_jira_waiting_final_approval_creative($designer, $brand_id, $asset_id, 'creative');
        $this->data['asset_list_finish'] = $this->campaignAssetIndexRepository->get_asset_jira_finish_creative($designer, $brand_id, $asset_id, 'creative');

        $this->data['brands'] = $this->campaignBrandsRepository->findAll()->pluck('campaign_name', 'id');

        return view('admin.asset.jira', $this->data);
    }

    public function asset_jira_kec(Request $request)
    {
        $param = $request->all();
        $this->data['currentAdminMenu'] = 'asset_jira_kec';

        $user = auth()->user();

        if($user->team == 'KDO' && $user->role != 'copywriter') {
            if(isset($_GET['q'])){ // if search with nothing..
                $str = $param['q'];
            }else{ // if come first time..
                $str = $param['q'] = $user->first_name;
            }
        }else{
            $str = !empty($param['q']) ? $param['q'] : '';
        }

        if(isset($_GET['brand'])) {
            $brand_id = $param['brand'];
        }else{
            $brand_id = !empty($param['brand']) ? $param['brand'] : '';
        }

        if(isset($_GET['team'])) {
            $team = $param['team'];
        }else{
            $team = !empty($param['team']) ? $param['team'] : '';
        }

        if(isset($_GET['asset_id'])) {
            $asset_id = $param['asset_id'];
        }else{
            $asset_id = !empty($param['asset_id']) ? $param['asset_id'] : '';
        }

        $this->data['brand_'] = $brand_id;
        $this->data['team_'] = $team;
        $this->data['asset_id'] = $asset_id;

        $this->data['filter'] = $param;
        $this->data['asset_list_copy_request'] = $this->campaignAssetIndexRepository->get_asset_jira_copy_request($str, $brand_id, $asset_id, $team);
        $this->data['asset_list_copy_to_do'] = $this->campaignAssetIndexRepository->get_asset_jira_copy_to_do($str, $brand_id, $asset_id, $team);
        $this->data['asset_list_copy_in_progress'] = $this->campaignAssetIndexRepository->get_asset_jira_copy_in_progress($str, $brand_id, $asset_id, $team);
        $this->data['asset_list_copy_review'] = $this->campaignAssetIndexRepository->get_asset_jira_copy_review($str, $brand_id, $asset_id, $team);

        $this->data['asset_list_copy_complete'] = $this->campaignAssetIndexRepository->get_asset_jira_copy_complete($str, $brand_id, $asset_id, $team);
        $this->data['asset_list_to_do'] = $this->campaignAssetIndexRepository->get_asset_jira_to_do($str, $brand_id, $asset_id, $team);
        $this->data['asset_list_in_progress'] = $this->campaignAssetIndexRepository->get_asset_jira_in_progress($str, $brand_id, $asset_id, $team);
        $this->data['asset_list_waiting_final_approval'] = $this->campaignAssetIndexRepository->get_asset_jira_waiting_final_approval($str, $brand_id, $asset_id, $team);
        $this->data['asset_list_waiting_asset_completed'] = $this->campaignAssetIndexRepository->get_asset_jira_asset_completed($str, $brand_id, $asset_id, $team);

        $this->data['brands'] = $this->campaignBrandsRepository->findAll()->pluck('campaign_name', 'id');
        $this->data['teams'] = [
          'creative',
          'content',
          'web production'
        ];

        return view('admin.asset.jira_kec', $this->data);
    }

    public function asset_jira_copywriter(Request $request)
    {
        $param = $request->all();

        $str = !empty($param['q']) ? $param['q'] : '';

        if(isset($_GET['copy_writer'])){
            $copy_writer = $param['copy_writer'];
        }else{
            $copy_writer = !empty($param['copy_writer']) ? $param['copy_writer'] : '';
        }

        if(isset($_GET['brand'])) {
            $brand_id = $param['brand'];
        }else{
            $brand_id = !empty($param['brand']) ? $param['brand'] : '';
        }

        if(isset($_GET['team'])) {
            $team = $param['team'];
        }else{
            $team = !empty($param['team']) ? $param['team'] : '';
        }

        if(isset($_GET['asset_id'])) {
            $asset_id = $param['asset_id'];
        }else{
            $asset_id = !empty($param['asset_id']) ? $param['asset_id'] : '';
        }

        $this->data['brand_'] = $brand_id;
        $this->data['copy_writer'] = $copy_writer;
        $this->data['asset_id'] = $asset_id;
        $this->data['currentAdminMenu'] = 'asset_jira_copywriter';
        $this->data['filter'] = $param;
        $this->data['asset_list_copy_request'] = $this->campaignAssetIndexRepository->get_asset_jira_copy_request($str, $brand_id, $asset_id, $team, $copy_writer);
        $this->data['asset_list_copy_to_do'] = $this->campaignAssetIndexRepository->get_asset_jira_copy_to_do($str, $brand_id, $asset_id, $team, $copy_writer);
        $this->data['asset_list_copy_in_progress'] = $this->campaignAssetIndexRepository->get_asset_jira_copy_in_progress($str, $brand_id, $asset_id, $team, $copy_writer);
        $this->data['asset_list_copy_review'] = $this->campaignAssetIndexRepository->get_asset_jira_copy_review($str, $brand_id, $asset_id, $team, $copy_writer);

        $this->data['all_copywriters'] = $this->userRepository->getAllCopyWriters();

        $this->data['brands'] = $this->campaignBrandsRepository->findAll()->pluck('campaign_name', 'id');

        return view('admin.asset.jira_copywriter', $this->data);
    }

    public function asset_jira_content(Request $request)
    {
        $param = $request->all();
        $this->data['currentAdminMenu'] = 'asset_jira_content';
        $this->data['content_creators'] = $this->userRepository->getContentAssignee();

        if(isset($_GET['content_creator'])){
            $content_creator = $param['content_creator'];
        }else{
            $content_creator = '';
        }

        if(isset($_GET['brand'])) {
            $brand_id = $param['brand'];
        }else{
            $brand_id = !empty($param['brand']) ? $param['brand'] : '';
        }

        if(isset($_GET['asset_id'])) {
            $asset_id = $param['asset_id'];
        }else{
            $asset_id = !empty($param['asset_id']) ? $param['asset_id'] : '';
        }

        $this->data['brand'] = $brand_id;
        $this->data['content_creator'] = $content_creator;
        $this->data['asset_id'] = $asset_id;

        $this->data['filter'] = $param;

        $this->data['asset_list_todo'] = $this->campaignAssetIndexRepository->get_asset_jira_to_do_creative($content_creator, $brand_id, $asset_id, 'content');
        $this->data['asset_list_progress'] = $this->campaignAssetIndexRepository->get_asset_jira_in_progress_creative($content_creator, $brand_id, $asset_id, 'content');
        $this->data['asset_list_done'] = $this->campaignAssetIndexRepository->get_asset_jira_waiting_final_approval_creative($content_creator, $brand_id, $asset_id, 'content');
        $this->data['asset_list_finish'] = $this->campaignAssetIndexRepository->get_asset_jira_finish_creative($content_creator, $brand_id, $asset_id, 'content');

        $this->data['brands'] = $this->campaignBrandsRepository->findAll()->pluck('campaign_name', 'id');

        return view('admin.asset.jira_content', $this->data);
    }

    public function asset_jira_web(Request $request)
    {
        $param = $request->all();
        $this->data['currentAdminMenu'] = 'asset_jira_web';
        $this->data['web_productions'] = $this->userRepository->getWebAssignee();

        if(isset($_GET['web_production'])){
            $web_production = $param['web_production'];
        }else{
            $web_production = '';
        }

        if(isset($_GET['brand'])) {
            $brand_id = $param['brand'];
        }else{
            $brand_id = !empty($param['brand']) ? $param['brand'] : '';
        }

        if(isset($_GET['asset_id'])) {
            $asset_id = $param['asset_id'];
        }else{
            $asset_id = !empty($param['asset_id']) ? $param['asset_id'] : '';
        }

        $this->data['brand'] = $brand_id;
        $this->data['web_production'] = $web_production;
        $this->data['asset_id'] = $asset_id;

        $this->data['filter'] = $param;

        $this->data['asset_list_todo'] = $this->campaignAssetIndexRepository->get_asset_jira_to_do_creative($web_production, $brand_id, $asset_id, 'web production');
        $this->data['asset_list_progress'] = $this->campaignAssetIndexRepository->get_asset_jira_in_progress_creative($web_production, $brand_id, $asset_id, 'web production');
        $this->data['asset_list_done'] = $this->campaignAssetIndexRepository->get_asset_jira_waiting_final_approval_creative($web_production, $brand_id, $asset_id, 'web production');
        $this->data['asset_list_finish'] = $this->campaignAssetIndexRepository->get_asset_jira_finish_creative($web_production, $brand_id, $asset_id, 'web production');

        $this->data['brands'] = $this->campaignBrandsRepository->findAll()->pluck('campaign_name', 'id');

        return view('admin.asset.jira_web', $this->data);
    }

    public static function get_writers_by_brand($brand)
    {
        $rs = UserRepository::getWritersNameByBrand($brand);
        $list = '';
        foreach ($rs as $writer){
            $list .= $writer->first_name . ' ';
        }
        return $list;
    }

    public static function get_owner_name_by_id($id)
    {
        if($id != 'N/A') {
            $rs = UserRepository::getAssetOwnerNameById($id);
            $rs = $rs[0]->first_name;
        }else{
            return 'N/A';
        }
        return $rs;
    }

    public function copyReview($id)
    {
        $campaignAssetIndex = $this->campaignAssetIndexRepository->findById($id);

        $param['status'] = 'copy_review';
        $param['copy_done_at'] = Carbon::now();
        $param['updated_at'] = Carbon::now();

        $c_id = $campaignAssetIndex->campaign_id;
        $a_id = $campaignAssetIndex->id;

        if($this->campaignAssetIndexRepository->update($id, $param)){

            $this->add_asset_correspondence($c_id, $campaignAssetIndex['type'], $a_id, ' updated the status to Copy Review for ', null);

            // TODO notification
            // send email to asset creator
            // Do action - copy review
            // email asset creator
            $notify = new NotifyController();
            $notify->copy_review($c_id, $a_id);

            echo '/admin/campaign/'.$c_id.'/edit#'.$a_id;
        }else{
            echo 'fail';
        }

    }

    public function copyComplete($id)
    {
        $campaignAssetIndex = $this->campaignAssetIndexRepository->findById($id);

        if($campaignAssetIndex->type == 'topcategories_copy' || $campaignAssetIndex->type == 'youtube_copy'){ // Top Category Copy should not go to Creative. -> go to Done.
            $this->finalApproval($id);
            return;
        }else{
            // Social Ad -> Google Ad (skip-creative)
            if($campaignAssetIndex->skip_creative == 'skip'){
                $this->finalApproval($id);
                return;
            }

            $param['status'] = 'copy_complete';
        }
        $param['updated_at'] = Carbon::now();

        $c_id = $campaignAssetIndex->campaign_id;
        $a_id = $campaignAssetIndex->id;

        if($this->campaignAssetIndexRepository->update($id, $param)){

            $this->add_asset_correspondence($c_id, $campaignAssetIndex['type'], $a_id, ' updated the status to Copy Complete for ', null);

            // TODO notification
            // send email to creative director
            // Do action - copy complete
            $notify = new NotifyController();
            $notify->copy_complete($c_id, $a_id);

            echo '/admin/campaign/'.$c_id.'/edit#'.$a_id;
        }else{
            echo 'fail';
        }

    }

    public function add_asset_correspondence($c_id, $asset_type, $asset_id, $status, $decline)
    {
        // Insert into campaign note for correspondence
        $user = auth()->user();
        if($asset_type == 'a_content'){
            $asset_type = 'a+_content';
        }
        $asset_type_ =  strtoupper(str_replace('_', ' ', $asset_type));
        $change_line  = "<p>$user->first_name $status $asset_type_ (#$asset_id)</p>";

        if(!empty($decline)) {
            $change_line .= "<div class='change_label'><p>Decline Reason:</p></div>"
                . "<div class='change_to'><p>$decline</p></div>";
        }
        $campaign_note = new CampaignNotes();
        $campaign_note['id'] = $c_id;
        $campaign_note['user_id'] = $user->id;
        $campaign_note['asset_id'] = $asset_id;
        $campaign_note['type'] = $asset_type;
        $campaign_note['note'] = $change_line;
        $campaign_note['date_created'] = Carbon::now();
        $campaign_note->save();
    }

    public function copyInProgress($id)
    {
        $campaignAssetIndex = $this->campaignAssetIndexRepository->findById($id);

        $param['status'] = 'copy_in_progress';
        $param['updated_at'] = Carbon::now();

        $c_id = $campaignAssetIndex->campaign_id;
        $a_id = $campaignAssetIndex->id;

        if($this->campaignAssetIndexRepository->update($id, $param)){

            $this->add_asset_correspondence($c_id, $campaignAssetIndex['type'], $a_id, ' updated the status to Copy In Progress for ', null);

            echo '/admin/campaign/'.$c_id.'/edit#'.$a_id;
        }else{
            echo 'fail';
        }

    }

    public function inProgress($id)
    {
        $campaignAssetIndex = $this->campaignAssetIndexRepository->findById($id);

        $param['status'] = 'in_progress';
        $param['updated_at'] = Carbon::now();

        $c_id = $campaignAssetIndex->campaign_id;
        $a_id = $campaignAssetIndex->id;

        if($this->campaignAssetIndexRepository->update($id, $param)){

            $this->add_asset_correspondence($c_id, $campaignAssetIndex['type'], $a_id, ' updated the status to In Progress for ', null);

            echo '/admin/campaign/'.$c_id.'/edit#'.$a_id;
        }else{
            echo 'fail';
        }

    }

    public function done($id)
    {
        $campaignAssetIndex = $this->campaignAssetIndexRepository->findById($id);

        $param['status'] = 'done';
        $param['updated_at'] = Carbon::now();
        if(is_null($campaignAssetIndex->done_at)){ // only add for first time
            $param['done_at'] = Carbon::now();
        }
        $c_id = $campaignAssetIndex->campaign_id;
        $a_id = $campaignAssetIndex->id;

        if($this->campaignAssetIndexRepository->update($id, $param)){

            $this->add_asset_correspondence($c_id, $campaignAssetIndex['type'], $a_id, ' updated the status to Completed for ', null);

            // TODO notification
            // send notification to Asset creator via email
            // Do action - for final approval
            $notify = new NotifyController();
            $notify->final_approval($c_id, $a_id);

            echo '/admin/campaign/'.$c_id.'/edit#'.$a_id;
        }else{
            echo 'fail';
        }
    }

    public function finalApproval($id)
    {
        $campaignAssetIndex = $this->campaignAssetIndexRepository->findById($id);

        $param['status'] = 'final_approval';
        $param['updated_at'] = Carbon::now();

        $c_id = $campaignAssetIndex->campaign_id;
        $a_id = $campaignAssetIndex->id;

        if($this->campaignAssetIndexRepository->update($id, $param)){

            $this->add_asset_correspondence($c_id, $campaignAssetIndex['type'], $a_id, ' updated the status to Final Approval for ', null);

            if($this->check_all_approval($c_id)){
                $param['status'] = 'archived';
                $param['updated_at'] = Carbon::now();
                $this->campaignRepository->update($c_id, $param);
            }

            echo '/admin/campaign/'.$c_id.'/edit#'.$a_id;
        }else{
            echo 'fail';
        }
    }

    public function check_all_approval($c_id)
    {
        $approval_assets = $this->campaignAssetIndexRepository->get_assets_final_approval_by_campaing_id($c_id);

        foreach ($approval_assets as $asset){
            if($asset->status != 'final_approval'){
                return false;
            }
        }

        return true;
    }

    public function asset_notification_user(Request $request)
    {
        $param = $request->all();

        $c_id = $param['c_id'];
        $params['asset_id'] = $param['a_id'];

        if (isset($param['user_id_list'])) {
            $params['user_id_list'] = implode(', ', $param['user_id_list']);
        } else {
            $params['user_id_list'] = '';
        }

        $params['updated_at'] = Carbon::now();

        if($this->assetNotificationUserRepository->getByAssetId($params['asset_id'])->count() !== 0){
            $this->assetNotificationUserRepository->update($params['asset_id'], $params);
        }else{
            $this->assetNotificationUserRepository->create($params);
        }

        $this->data['currentAdminMenu'] = 'campaign';

        return redirect('admin/campaign/'.$c_id.'/edit')
            ->with('success', __('Data has been Updated.'));
    }

    public function asset_owner_change(Request $request)
    {

        $param = $request->all();

        $c_id = $param['c_id'];
        $temp = explode(',', $param['author_id']);
        $params['author_id'] = $temp[0];
        $author_name = $temp[1];
        $params['asset_id'] = $param['a_id'];
        $params['updated_at'] = Carbon::now();

        $this->campaignAssetIndexRepository->update($params['asset_id'], $params);

        $campaign_note = new CampaignNotes();
        $campaign_note['id'] = $param['c_id'];
        $user = auth()->user();
        $campaign_note['user_id'] = $user->id;
        $campaign_note['asset_id'] = $param['a_id'];
        $campaign_note['note'] = 'The Asset (#'. $params['asset_id'] . ') Owner was changed to ' . $author_name;
        $campaign_note['date_created'] = Carbon::now();
        $campaign_note->save();

        $this->data['currentAdminMenu'] = 'campaign';

        return redirect('admin/campaign/'.$c_id.'/edit')
            ->with('success', __('Data has been Updated.'));

    }

    public function asset_owner_change_mapping(Request $request)
    {
        $param = $request->all();

        // {{ $user->id }},kiss_nails,{{$asset['id']}}
        $temp = explode(',', $param['asset_owner_id']);
        $params[$temp[1]] = $temp[0];
        $params['id'] = $temp[2];
        $params['updated_at'] = Carbon::now();
        $this->assetOwnerAssetsRepository->update($params['id'], $params);

        $this->data['currentAdminMenu'] = 'asset_owners';

        return redirect('admin/asset_owners')
            ->with('success', __('Data has been Updated.'));

    }

    public function asset_add_note(Request $request)
    {
        $param = $request->all();
        $user = auth()->user();

        $c_id = $param['c_id'];
        $c_title = $param['c_title'];
        $email_list = $param['email_list'];

        $campaign_note = new CampaignNotes();
        $campaign_note['id'] = $c_id;
        $campaign_note['user_id'] = $user->id;
        $campaign_note['type'] = 'note';
        $campaign_note['note'] = $param['create_note'];
        $campaign_note['date_created'] = Carbon::now();
        $campaign_note->save();

        $new_note = preg_replace("/<p[^>]*?>/", "", $param['create_note']);
        $new_note = str_replace("</p>", "\r\n", $new_note);
        $new_note = html_entity_decode($new_note);

        if($email_list){
            $details = [
                'who' => $user->first_name,
                'c_id' => $c_id,
                'c_title' => $c_title,
                'message' => $new_note,
                'url' => '/admin/campaign/'.$c_id.'/edit',
            ];
            //send to receivers
            $receiver_list = explode(',', $email_list);

            //check admin group//
            if( in_array('admingroup@kissusa.com', $receiver_list)){

                // add all admins to receiver
                $user_obj = new UserRepository();

                $adminGroup_rs = $user_obj->getAdminGroup();
                foreach ($adminGroup_rs as $user) {
                    if ('admingroup@kissusa.com' != $user['email']) {
                        $receiver_list[] = $user['email'];
                    }
                }
            }
            Mail::to($receiver_list)->send(new AssetMessage($details));
        }

        $this->data['currentAdminMenu'] = 'campaign';

        return redirect('admin/campaign/'.$c_id.'/edit')
            ->with('success', __('Data has been Updated.'));
    }

}
