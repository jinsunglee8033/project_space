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
use App\Models\ProjectNotes;
use App\Models\ProjectTaskIndex;
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

use App\Repositories\Admin\ProjectTaskIndexRepository;
use App\Repositories\Admin\UserRepository;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use Illuminate\Support\Facades\Log;

use Mail;

class TaskController extends Controller
{

    private $userRepository;
    private $assetNotificationUserRepository;
    private $assetOwnerAssetsRepository;
    private $campaignAssetIndexRepository;
    private $projectTaskIndexRepository;
    private $assetLeadTimeRepository;

    public function __construct(
                                ProjectTaskIndexRepository $projectTaskIndexRepository,
                                UserRepository $userRepository,
                                AssetNotificationUserRepository $assetNotificationUserRepository,
                                AssetOwnerAssetsRepository $assetOwnerAssetsRepository,
                                AssetLeadTimeRepository $assetLeadTimeRepository,
                                PermissionRepository $permissionRepository)
    {
        parent::__construct();

        $this->projectTaskIndexRepository = $projectTaskIndexRepository;
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


    public function add_task_correspondence($p_id, $task_type, $task_id, $status, $decline)
    {
        // Insert into project note for new task add
        $user = auth()->user();

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

        $change_line  = "<p>$user->first_name updated the status to <b>$status</b> for <b style='color: #b91d19;'>$task_type_ </b><b>(#$task_id)</b></p>";

        if(!empty($decline)) {
            $change_line .= "<div class='change_label'><p>Decline Reason:</p></div>"
                . "<div class='change_to'><p>$decline</p></div>";
        }

        $campaign_note = new ProjectNotes();
        $campaign_note['id'] = $p_id;
        $campaign_note['user_id'] = $user->id;
        $campaign_note['task_id'] = $task_id;
        $campaign_note['type'] = $task_type;
        $campaign_note['note'] = $change_line;
        $campaign_note['created_at'] = Carbon::now();
        $campaign_note->save();
    }

    public function actionInProgress($id)
    {
        $projectTaskIndex = $this->projectTaskIndexRepository->findById($id);
        $param['status'] = 'in_progress';
        $param['updated_at'] = Carbon::now();
        $p_id = $projectTaskIndex->project_id;
        $t_id = $projectTaskIndex->id;
        if($this->projectTaskIndexRepository->update($id, $param)){
            $this->add_task_correspondence($p_id, $projectTaskIndex['type'], $t_id, ' In Progress ', null);
            echo '/admin/project/'.$p_id.'/edit#'.$t_id;
        }else{
            echo 'fail';
        }
    }

    public function actionReview($id)
    {
        $projectTaskIndex = $this->projectTaskIndexRepository->findById($id);
        $param['status'] = 'action_review';
        $param['updated_at'] = Carbon::now();
        $p_id = $projectTaskIndex->project_id;
        $t_id = $projectTaskIndex->id;
        if($this->projectTaskIndexRepository->update($id, $param)){
            $this->add_task_correspondence($p_id, $projectTaskIndex['type'], $t_id, ' Action Review ', null);
            // TODO notification
            // send email to asset creator
            // Do action - copy review
            // email asset creator
//            $notify = new NotifyController();
//            $notify->copy_review($c_id, $a_id);
            echo '/admin/project/'.$p_id.'/edit#'.$t_id;
        }else{
            echo 'fail';
        }
    }

    public function actionComplete($id)
    {
        $projectTaskIndex = $this->projectTaskIndexRepository->findById($id);
        $param['status'] = 'action_completed';
        $param['updated_at'] = Carbon::now();
        $p_id = $projectTaskIndex->project_id;
        $t_id = $projectTaskIndex->id;
        if($this->projectTaskIndexRepository->update($id, $param)){
            $this->add_task_correspondence($p_id, $projectTaskIndex['type'], $t_id, ' Action Completed ', null);
            // TODO notification
            // send notification to Asset creator via email
            // Do action - for final approval
//            $notify = new NotifyController();
//            $notify->final_approval($c_id, $a_id);
            echo '/admin/project/'.$p_id.'/edit#'.$t_id;
        }else{
            echo 'fail';
        }
    }

    public function actionSkip($id, $type)
    {
        $projectTaskIndex = new ProjectTaskIndex();
        $projectTaskIndex['project_id'] = $id;
        $projectTaskIndex['type'] = $type;
        $projectTaskIndex['status'] = 'action_skip';
        $user = auth()->user();
        $projectTaskIndex['author_id'] = $user->id;
        if($projectTaskIndex->save()){
            $task_name = strtoupper($type);
            $change_line = "<p><b style='color: #b91d19;'>$task_name</b> <b>(#$projectTaskIndex->id)</b> has been <b>skipped</b> by $user->first_name. </p>";

            $project_note = new ProjectNotes();
            $project_note['id'] = $id;
            $project_note['user_id'] = $user->id;
            $project_note['task_id'] = $projectTaskIndex->id;
            $project_note['type'] = $projectTaskIndex->type;
            $project_note['note'] = $change_line;
            $project_note['created_at'] = Carbon::now();
            $project_note->save();

            echo '/admin/project/'.$id.'/edit';
        }else{
            echo 'fail';
        }
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
