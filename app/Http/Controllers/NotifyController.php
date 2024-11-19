<?php

namespace App\Http\Controllers;

use App\Mail\AssetOwner;
use App\Mail\AssignToDo;
use App\Mail\CopyAssignToDo;
use App\Mail\CopyComplete;
use App\Mail\CopyRequest;
use App\Mail\CopyReview;
use App\Mail\DeclineCopy;
use App\Mail\DeclineCreative;
use App\Mail\DeclineKec;
use App\Mail\DevAssignToDo;
use App\Mail\DevRequest;
use App\Mail\DevReview;
use App\Mail\FinalApproval;
use App\Mail\NewProject;
use App\Mail\SendMail;
use App\Mail\Todo;
use App\Models\CampaignBrands;
use App\Models\User;
use App\Repositories\Admin\AssetNotificationUserRepository;
use App\Repositories\Admin\CampaignAssetIndexRepository;
use App\Repositories\Admin\CampaignBrandsRepository;
use App\Repositories\Admin\CampaignRepository;
use App\Repositories\Admin\DevRepository;
use App\Repositories\Admin\UserRepository;
use Illuminate\Http\Request;
use App\Mail\MyDemoMail;
use App\Mail\ReminderDueAfter;
use App\Mail\ReminderDueBefore;
use App\Mail\ReminderDueToday;
use Mail;
use DB;

class NotifyController extends Controller
{

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    public function copy_request($c_id, $a_id)
    {
        $asset_index_obj = new CampaignAssetIndexRepository();
        $asset_index_rs = $asset_index_obj->findById($a_id);

        $asset_type = $asset_index_rs['type'];
        $asset_status = $asset_index_rs['status'];

        $campaign_obj = new CampaignRepository();
        $campaign_rs = $campaign_obj->findById($c_id);

        $brand_id = $campaign_rs['campaign_brand'];
        $brand_obj = new CampaignBrandsRepository();
        $brand_rs = $brand_obj->findById($brand_id);

        $user_obj = new UserRepository();
        $user_rs = $user_obj->getCopyWriterManager(); // get copy writer manager

        if($user_rs) {
            foreach ($user_rs as $user){
                $details = [
                    'who'           => $user['first_name'],
                    'c_id'          => $c_id,
                    'a_id'          => $a_id,
                    'task_name'     => $campaign_rs['name'],
                    'asset_type'    => $asset_type,
                    'asset_status'  => $asset_status,
//                    'url'           => '/admin/asset/'.$a_id.'/'.$c_id.'/'.$asset_type.'/detail_copy',
                    'url'           => '/admin/asset/'.$a_id.'/'.$c_id.'/'.$asset_type. '/' . $brand_rs['campaign_name'] . '/detail_copy',
                ];

                Mail::to($user['email'])->send(new CopyRequest($details));
            }
        }

    }

    public function copy_review($c_id, $a_id)
    {
        $asset_index_obj = new CampaignAssetIndexRepository();
        $asset_index_rs = $asset_index_obj->findById($a_id);

        $asset_type = $asset_index_rs['type'];
        $asset_status = $asset_index_rs['status'];
        $asset_author_id = $asset_index_rs['author_id'];

        $campaign_obj = new CampaignRepository();
        $campaign_rs = $campaign_obj->findById($c_id);

        $author_id = $campaign_rs['author_id'];

        $user_obj = new UserRepository();
        $user_rs = $user_obj->findById($author_id);

        // Send notification to asset creator
        if($asset_author_id) {
            if ($author_id != $asset_author_id) {
                $asset_author_rs = $user_obj->findById($asset_author_id);
                $who = $asset_author_rs['first_name'];
                $email_to = $asset_author_rs['email'];
            } else {
                $who = $user_rs['first_name'];
                $email_to = $user_rs['email'];
            }

            $details = [
                'who' => $who,
                'c_id' => $c_id,
                'a_id' => $a_id,
                'task_name' => $campaign_rs['name'],
                'asset_type' => $asset_type,
                'asset_status' => $asset_status,
                'url' => '/admin/campaign/' . $c_id . '/edit#' . $a_id,
            ];

            // If MKT, Group email by brand
            if ($campaign_rs['author_team'] == 'Global Marketing') {
                $cc_list = array();
                $brand_id = $campaign_rs['campaign_brand'];
                $brand_obj = new CampaignBrandsRepository();
                $brand_rs = $brand_obj->findById($brand_id);
                $brand_name = $brand_rs['campaign_name'];
                $mktGroup_rs = $user_obj->getMKTGroupByBrand($brand_name);
                foreach ($mktGroup_rs as $user) {
                    if ($asset_author_id != $user['id']) {
                        $cc_list[] = $user['email'];
                    }
                }
                Mail::to($email_to)
                    ->cc($cc_list)
                    ->send(new CopyReview($details));
            } else {
                Mail::to($email_to)
                    ->send(new CopyReview($details));
            }
        }

        // Email to asset_notification_user
        $anu_obj = new AssetNotificationUserRepository();
        $anu_rs = $anu_obj->getByAssetId($a_id);
        if(isset($anu_rs[0])){
            if($anu_rs[0]->user_id_list != "" ) {
                $reciver_list = explode(', ', $anu_rs[0]->user_id_list);
            }else{
                $reciver_list = '';
            }
        }else{
            $reciver_list = '';
        }
        if($reciver_list != ''){
            foreach ($reciver_list as $reciver_id){
                $reciver_rs = $user_obj->findById($reciver_id);
                $details = [
                    'who' => $reciver_rs['first_name'],
                    'c_id' => $c_id,
                    'a_id' => $a_id,
                    'task_name' => $campaign_rs['name'],
                    'asset_type' => $asset_type,
                    'asset_status' => $asset_status,
                    'url' => '/admin/campaign/' . $c_id . '/edit#' . $a_id,
                ];
                // Eamil to asset_notification_user
                Mail::to($reciver_rs['email'])->send(new CopyReview($details));
            }
        }
    }

    public function copy_complete($c_id, $a_id)
    {
        $asset_index_obj = new CampaignAssetIndexRepository();
        $asset_index_rs = $asset_index_obj->findById($a_id);

        $asset_type = $asset_index_rs['type'];
        $asset_status = $asset_index_rs['status'];

        $campaign_obj = new CampaignRepository();
        $campaign_rs = $campaign_obj->findById($c_id);

        $campaign_brand = $campaign_rs['campaign_brand'];
        $user_obj = new UserRepository();

        if($asset_index_rs['team_to'] == 'content'){
            $user_rs = $user_obj->getContentManager();
            if($user_rs){
                foreach ($user_rs as $user) {
                    $details = [
                        'who' => $user['first_name'],
                        'c_id' => $c_id,
                        'a_id' => $a_id,
                        'task_name' => $campaign_rs['name'],
                        'asset_type' => $asset_type,
                        'asset_status' => $asset_status,
                        'url' => '/admin/campaign/' . $c_id . '/edit#' . $a_id,
                    ];
                    Mail::to($user['email'])->send(new CopyComplete($details));
                }
            }
        }elseif ($asset_index_rs['team_to'] == 'web production'){
            $user_rs = $user_obj->getWebProductionManager();
            if($user_rs){
                foreach ($user_rs as $user) {
                    $details = [
                        'who' => $user['first_name'],
                        'c_id' => $c_id,
                        'a_id' => $a_id,
                        'task_name' => $campaign_rs['name'],
                        'asset_type' => $asset_type,
                        'asset_status' => $asset_status,
                        'url' => '/admin/campaign/' . $c_id . '/edit#' . $a_id,
                    ];
                    Mail::to($user['email'])->send(new CopyComplete($details));
                }
            }
        }else{
            if ($campaign_brand == 5) { // if Joah -> Joah Director
                $user_rs = $user_obj->getJoahDirector();
                if ($user_rs) {
                    foreach ($user_rs as $user) {
                        $details = [
                            'who' => $user['first_name'],
                            'c_id' => $c_id,
                            'a_id' => $a_id,
                            'task_name' => $campaign_rs['name'],
                            'asset_type' => $asset_type,
                            'asset_status' => $asset_status,
                            'url' => '/admin/campaign/' . $c_id . '/edit#' . $a_id,
                        ];
                        Mail::to($user['email'])->send(new CopyComplete($details));
                    }
                }
            } else { // others -> Creative Director
                $user_rs = $user_obj->getCreativeDirector();
                if ($user_rs) {
                    foreach ($user_rs as $user) {
                        $details = [
                            'who' => $user['first_name'],
                            'c_id' => $c_id,
                            'a_id' => $a_id,
                            'task_name' => $campaign_rs['name'],
                            'asset_type' => $asset_type,
                            'asset_status' => $asset_status,
                            'url' => '/admin/campaign/' . $c_id . '/edit#' . $a_id,
                        ];
                        Mail::to($user['email'])->send(new CopyComplete($details));
                    }
                }
            }
        }
    }

    public function to_do($c_id, $a_id, $assignee)
    {
        $asset_index_obj = new CampaignAssetIndexRepository();
        $asset_index_rs = $asset_index_obj->findById($a_id);

        $asset_type = $asset_index_rs['type'];
        $asset_status = $asset_index_rs['status'];

        $campaign_obj = new CampaignRepository();
        $campaign_rs = $campaign_obj->findById($c_id);

        $user_obj = new UserRepository();
        $names = $user_obj->getEmailByDesignerName($assignee);

        foreach ($names as $name){
            $details = [
                'who'           => $name['first_name'],
                'c_id'          => $c_id,
                'a_id'          => $a_id,
                'task_name'     => $campaign_rs['name'],
                'asset_type'    => $asset_type,
                'asset_status'  => $asset_status,
                'url'           => '/admin/campaign/'.$c_id.'/edit#'.$a_id,
            ];
//            Mail::to($name['email'])->send(new Todo($details));
            Mail::to($name['email'])->send(new AssignToDo($details));
        }
    }

    public function copy_to_do($c_id, $a_id, $assignee)
    {
        $asset_index_obj = new CampaignAssetIndexRepository();
        $asset_index_rs = $asset_index_obj->findById($a_id);

        $asset_type = $asset_index_rs['type'];
        $asset_status = $asset_index_rs['status'];

        $campaign_obj = new CampaignRepository();
        $campaign_rs = $campaign_obj->findById($c_id);

        $user_obj = new UserRepository();
        $names = $user_obj->getEmailByCopyWriterName($assignee);

        foreach ($names as $name){
            $details = [
                'who'           => $name['first_name'],
                'c_id'          => $c_id,
                'a_id'          => $a_id,
                'task_name'     => $campaign_rs['name'],
                'asset_type'    => $asset_type,
                'asset_status'  => $asset_status,
                'url'           => '/admin/campaign/'.$c_id.'/edit#'.$a_id,
            ];
//            Mail::to($name['email'])->send(new Todo($details));
            Mail::to($name['email'])->send(new CopyAssignToDo($details));
        }
    }

    public function dev_request($dev)
    {
        $user_obj = new UserRepository();
        $requestor = $user_obj->findById($dev->request_by);

        $rs = $user_obj->getDeveloperManager();
        if($rs) {
            $cc_list = array();
            foreach ($rs as $dev_manager){
                $cc_list[] = $dev_manager['email'];
            }
            $details = [
                'who'           => $requestor['first_name'],
                'd_id'          => $dev->id,
                'task_name'     => $dev->title,
                'task_type'     => $dev->priority,
                'task_status'   => $dev->status,
                'url'           => '/admin/dev/'.$dev->id.'/edit',
            ];
            Mail::to($requestor['email'])
                ->cc($cc_list)
                ->send(new DevRequest($details));

        }
    }

    public function dev_to_do($d_id, $assign_to)
    {
        $dev_repo = new DevRepository();
        $dev_array = $dev_repo->findById($d_id);

        $user_repo = new UserRepository();
        $user_array = $user_repo->findById($assign_to);

        $details = [
            'who'           => $user_array['first_name'],
            'task_type'     => $dev_array['type'],
            'd_id'          => $d_id,
            'title'         => $dev_array['title'],
            'task_status'   => $dev_array['status'],
            'url'           => '/admin/dev/'.$d_id.'/edit',
        ];
        Mail::to($user_array['email'])->send(new DevAssignToDo($details));
    }

    public function dev_review($d_id, $request_by)
    {
        $dev_repo = new DevRepository();
        $dev_array = $dev_repo->findById($d_id);

        $dev_repo = new UserRepository();
        $user_array = $dev_repo->findById($request_by);

        $details = [
            'who'           => $user_array['first_name'],
            'task_type'     => $dev_array['type'],
            'd_id'          => $d_id,
            'title'         => $dev_array['title'],
            'task_status'   => $dev_array['status'],
            'url'           => '/admin/dev/'.$d_id.'/edit',
        ];
        Mail::to($user_array['email'])->send(new DevReview($details));
    }

    public function final_approval($c_id, $a_id)
    {
        $asset_index_obj = new CampaignAssetIndexRepository();
        $asset_index_rs = $asset_index_obj->findById($a_id);

        $asset_type = $asset_index_rs['type'];
        $asset_status = $asset_index_rs['status'];
        $asset_author_id = $asset_index_rs['author_id'];

        $campaign_obj = new CampaignRepository();
        $campaign_rs = $campaign_obj->findById($c_id);

        $author_id = $campaign_rs['author_id'];

        $user_obj = new UserRepository();
        $user_rs = $user_obj->findById($author_id);

        // Send notification to asset creator
        if($asset_author_id) {
            if ($author_id != $asset_author_id) {
                $asset_author_rs = $user_obj->findById($asset_author_id);
                $who = $asset_author_rs['first_name'];
                $email_to = $asset_author_rs['email'];
            } else {
                $who = $user_rs['first_name'];
                $email_to = $user_rs['email'];
            }

            $details = [
                'who' => $who,
                'c_id' => $c_id,
                'a_id' => $a_id,
                'task_name' => $campaign_rs['name'],
                'asset_type' => $asset_type,
                'asset_status' => $asset_status,
                'url' => '/admin/campaign/' . $c_id . '/edit#' . $a_id,
            ];

            if ($campaign_rs['author_team'] == 'Global Marketing') {
                // If MKT, Group email by brand
                $cc_list = array();
                $brand_id = $campaign_rs['campaign_brand'];
                $brand_obj = new CampaignBrandsRepository();
                $brand_rs = $brand_obj->findById($brand_id);
                $brand_name = $brand_rs['campaign_name'];
                $mktGroup_rs = $user_obj->getMKTGroupByBrand($brand_name);
                foreach ($mktGroup_rs as $user) {
                    if ($asset_author_id != $user['id']) {
                        $cc_list[] = $user['email'];
                    }
                }
                Mail::to($email_to)
                    ->cc($cc_list)
                    ->send(new FinalApproval($details));
            } else {
                Mail::to($email_to)
                    ->send(new FinalApproval($details));
            }
        }

        // Email to asset_notification_user
        $anu_obj = new AssetNotificationUserRepository();
        $anu_rs = $anu_obj->getByAssetId($a_id);
        if(isset($anu_rs[0])){
            if($anu_rs[0]->user_id_list != "" ) {
                $reciver_list = explode(', ', $anu_rs[0]->user_id_list);
            }else{
                $reciver_list = '';
            }
        }else{
            $reciver_list = '';
        }
        if($reciver_list != ''){
            foreach ($reciver_list as $reciver_id){
                $reciver_rs = $user_obj->findById($reciver_id);
                $details = [
                    'who' => $reciver_rs['first_name'],
                    'c_id' => $c_id,
                    'a_id' => $a_id,
                    'task_name' => $campaign_rs['name'],
                    'asset_type' => $asset_type,
                    'asset_status' => $asset_status,
                    'url' => '/admin/campaign/' . $c_id . '/edit#' . $a_id,
                ];
                // Eamil to asset_notification_user
                Mail::to($reciver_rs['email'])->send(new FinalApproval($details));
            }
        }

    }

    public function decline_from_copy($c_id, $a_id, $params)
    {
        $asset_index_obj = new CampaignAssetIndexRepository();
        $asset_index_rs = $asset_index_obj->findById($a_id);

        $asset_type = $asset_index_rs['type'];
        $asset_status = $asset_index_rs['status'];

        $campaign_obj = new CampaignRepository();
        $campaign_rs = $campaign_obj->findById($c_id);

        $brand_id = $campaign_rs['campaign_brand'];

        $brand_obj = new CampaignBrandsRepository();
        $brand_rs = $brand_obj->findById($brand_id);

        $user_obj = new UserRepository();

        // Send email to copy writers
        $copywriter_rs = $user_obj->getWriterByBrandName($brand_rs['campaign_name']);
        if($copywriter_rs) {
            foreach ($copywriter_rs as $copywriter){
                $details = [
                    'who'           => $copywriter['first_name'],
                    'c_id'          => $c_id,
                    'a_id'          => $a_id,
                    'task_name'     => $campaign_rs['name'],
                    'asset_type'    => $asset_type,
                    'asset_status'  => $asset_status,
                    'url'           => '/admin/campaign/'.$c_id.'/edit#'.$a_id,
                ];
                Mail::to($copywriter['email'])->send(new DeclineCopy($details));
            }
        }
    }

    public function decline_from_kec($c_id, $a_id, $params)
    {
        $asset_index_obj = new CampaignAssetIndexRepository();
        $asset_index_rs = $asset_index_obj->findById($a_id);

        $asset_type = $asset_index_rs['type'];
        $asset_status = $asset_index_rs['status'];
        $asset_assignee = $asset_index_rs['assignee'];

        $user_obj = new UserRepository();
        $names = $user_obj->getEmailByDesignerName($asset_assignee);

        $campaign_obj = new CampaignRepository();
        $campaign_rs = $campaign_obj->findById($c_id);

        foreach ($names as $name){
            $details = [
                'who'           => $name['first_name'],
                'c_id'          => $c_id,
                'a_id'          => $a_id,
                'task_name'     => $campaign_rs['name'],
                'asset_type'    => $asset_type,
                'asset_status'  => $asset_status,
                'url'           => '/admin/campaign/'.$c_id.'/edit#'.$a_id,
            ];
            Mail::to($name['email'])->send(new DeclineKec($details));
        }

    }

    public function decline_from_creative($c_id, $a_id, $params)
    {
        $asset_index_obj = new CampaignAssetIndexRepository();
        $asset_index_rs = $asset_index_obj->findById($a_id);

        $asset_type = $asset_index_rs['type'];
        $asset_status = $asset_index_rs['status'];
        $asset_author_id = $asset_index_rs['author_id'];

        $campaign_obj = new CampaignRepository();
        $campaign_rs = $campaign_obj->findById($c_id);

        $author_id = $campaign_rs['author_id'];

        $user_obj = new UserRepository();
        $user_rs = $user_obj->findById($author_id); // task creator

        // Send notification to asset creator
        if($asset_author_id) {
            if ($author_id != $asset_author_id) {
                $asset_author_rs = $user_obj->findById($asset_author_id);
                $who = $asset_author_rs['first_name'];
                $email_to = $asset_author_rs['email'];
            } else {
                $who = $user_rs['first_name'];
                $email_to = $user_rs['email'];
            }

            $details = [
                'who' => $who,
                'c_id' => $c_id,
                'a_id' => $a_id,
                'task_name' => $campaign_rs['name'],
                'asset_type' => $asset_type,
                'asset_status' => $asset_status,
                'url' => '/admin/campaign/' . $c_id . '/edit#' . $a_id,
            ];

            if ($campaign_rs['author_team'] == 'Global Marketing') {
                $cc_list = array();
                $brand_id = $campaign_rs['campaign_brand'];
                $brand_obj = new CampaignBrandsRepository();
                $brand_rs = $brand_obj->findById($brand_id);
                $brand_name = $brand_rs['campaign_name'];
                $mktGroup_rs = $user_obj->getMKTGroupByBrand($brand_name);
                foreach ($mktGroup_rs as $user) {
                    if ($asset_author_id != $user['id']) {
                        $cc_list[] = $user['email'];
                    }
                }
                Mail::to($email_to)
                    ->cc($cc_list)
                    ->send(new DeclineCreative($details));
            } else {
                Mail::to($email_to)
                    ->send(new DeclineCreative($details));
            }
        }

    }

    public function new_project($campaign)
    {
        $user_obj = new UserRepository();
        $project_creator = $user_obj->findById($campaign->author_id); // task creator

        $details = [
            'creator' => $project_creator['first_name'],
            'c_id' => $campaign->id,
            'team' => $project_creator['team'],
            'task_name' => $campaign->name,
            'url' => '/admin/campaign/' . $campaign->id . '/edit',
        ];

        $cc_list = array();
        $cc_list[] = 'frank.russo@kissusa.com';
//        $cc_list[] = 'jilee2@kissusa.com';
        Mail::to('motuhin@kissusa.com')
            ->cc($cc_list)
            ->send(new NewProject($details));
    }

    public function new_asset($asset)
    {

    }

    public function new_asset_owners($asset_owner_user_obj, $campaign, $asset_name)
    {
        $user_obj = new UserRepository();
        $project_creator = $user_obj->findById($campaign->author_id); // task creator

        $details = [
            'who'       => $asset_owner_user_obj['first_name'],
            'creator'   => $project_creator['first_name'],
            'c_id'      => $campaign->id,
            'task_name' => $campaign->name,
            'asset_type'=> ucwords(str_replace('_', ' ', $asset_name)),
            'url'       => '/admin/campaign/' . $campaign->id . '/edit',

        ];
//        $cc_list[] = 'jilee2@kissusa.com';
        Mail::to($asset_owner_user_obj['email'])
            ->send(new AssetOwner($details));
    }

    public static function reminder_email()
    {
//        $user_obj = new UserRepository();
//        $jin =$user_obj->findById(97);
//        $details = [
//            'due' => '2022-11-11',
//            'who' => 'test',
//            'c_id' => 1580,
//            'a_id' => 5039,
//            'task_name' => 'Holiday KISS Nails & Lashes - KISS Mass Market',
//            'asset_type' => 'Website Banners',
//            'asset_status' => 'Creative Review',
//            'url' => '/admin/campaign/1580/edit#5039',
//        ];

//        $cc_list = array();
//
//        $cc_list[] = 'jinsunglee.8033@gmail.com';
//
//        Mail::to('jilee2@kissusa.com')
//            ->send(new ReminderDueAfter($details));
//        return 'done';
//        $details = [
//            'creator' => 'Trang',
//            'c_id' => 1121,
//            'team' => 'Global Marketing',
//            'task_name' => 'blravbrla',
//            'url' => '/admin/campaign/' . 1121 . '/edit'
//        ];
//
//        // This is for template preview!!!
//        $send_email = new NewProject($details);
//        return $send_email;
//
//        ddd("here");

        //
        // Only Sending on WEEKDAYS!
        //
        if(date('N') <= 5) {

            $obj = new AssetNotificationUserRepository();
            $user_obj = new UserRepository();

            $today = date('Y-m-d');
            $day_after_tomorrow = date('Y-m-d', strtotime($today . '2 day'));

            // [Step 1] copy_request for copy writer manager!!!
            $result_copy_request = $obj->getCopyRequestStatus();
            foreach ($result_copy_request as $item) {

                $asset_type = $item->asset_type;
                $copywriter_start_due = date('Y-m-d');

                $time_to_spare = ($item->time_to_spare == 'N/A') ? 0 : $item->time_to_spare;
                $kdo = ($item->kdo == 'N/A') ? 0 : $item->kdo;
                $development = ($item->development == 'N/A') ? 0 : $item->development;
                $final_review = ($item->final_review == 'N/A') ? 0 : $item->final_review;
                $creative_work = ($item->creative_work == 'N/A') ? 0 : $item->creative_work;
                $creator_assign = ($item->creator_assign == 'N/A') ? 0 : $item->creator_assign;
                $copy_review = ($item->copy_review == 'N/A') ? 0 : $item->copy_review;
                $copy = ($item->copy == 'N/A') ? 0 : $item->copy;
                $copywriter_assign = ($item->copywriter_assign == 'N/A') ? 0 : $item->copywriter_assign;

                $step_8 = $time_to_spare + $kdo;        // e-commerce start
                $step_7 = $step_8 + $development;       // development start
                $step_6 = $step_7 + $final_review;      // creative review start
                $step_5 = $step_6 + $creative_work;     // creative work start
                $step_4 = $step_5 + $creator_assign;    // creator assign start
                $step_3 = $step_4 + $copy_review;       // copy review start
                $step_2 = $step_3 + $copy;              // copy start
                $step_1 = $step_2 + $copywriter_assign; // copywriter assign start

                $copywriter_start_due = date('m/d/Y', strtotime($item->due . ' -' . $step_1 . ' weekday'));


//                if ($asset_type == 'email_blast') {
//                    $copywriter_start_due = date('Y-m-d', strtotime($item->due . '-28 weekday'));
//                } else if ($asset_type == 'social_ad') {
//                    $copywriter_start_due = date('Y-m-d', strtotime($item->due . '-28 weekday'));
//                } else if ($asset_type == 'website_banners') {
//                    $copywriter_start_due = date('Y-m-d', strtotime($item->due . '-29 weekday'));
//                } else if ($asset_type == 'landing_page') {
//                    $copywriter_start_due = date('Y-m-d', strtotime($item->due . '-49 weekday'));
//                } else if ($asset_type == 'misc') {
//                    $copywriter_start_due = date('Y-m-d', strtotime($item->due . '-27 weekday'));
//                } else if ($asset_type == 'sms_request') {
//                    $copywriter_start_due = date('Y-m-d', strtotime($item->due . '-27 weekday'));
//                } else if ($asset_type == 'topcategories_copy') {
//                    $copywriter_start_due = date('Y-m-d', strtotime($item->due . '-7 weekday'));
//                } else if ($asset_type == 'programmatic_banners') {
//                    $copywriter_start_due = date('Y-m-d', strtotime($item->due . '-28 weekday'));
//                } else if ($asset_type == 'a_content') {
//                    $copywriter_start_due = date('Y-m-d', strtotime($item->due . '-41 weekday'));
//                } else if ($asset_type == 'youtube_copy') {
//                    $copywriter_start_due = date('Y-m-d', strtotime($item->due . '-14 weekday'));
//                }

                if ($copywriter_start_due == $today) {
                    // sending 'today is due' email => send to copy writer Manager

                    $copy_writer_managers = $user_obj->getCopyWriterManager(); // get copywriter manager
                    foreach ($copy_writer_managers as $person) {
                        $details = [
                            'due' => $copywriter_start_due,
                            'who' => $person['first_name'],
                            'c_id' => $item->campaign_id,
                            'a_id' => $item->asset_id,
                            'task_name' => $item->project_name,
                            'asset_type' => ucwords(str_replace('_', ' ', $item->asset_type)),
                            'asset_status' => 'Copy Requested',
                            'url' => '/admin/asset/' . $item->asset_id . '/' . $item->campaign_id . '/' . $item->asset_type . '/' . $item->brand_name . '/detail_copy',
                        ];
                        // Email to asset creator!
                        $cc_list = array();
                        $cc_list[] = 'frank.russo@kissusa.com';
                        $cc_list[] = 'motuhin@kissusa.com';
                        //$cc_list[] = 'jilee2@kissusa.com';
                        Mail::to($person['email'])
//                        ->cc($cc_list)
                            ->send(new ReminderDueToday($details));
//                    Mail::to('jilee2@kissusa.com')->send(new ReminderDueToday($details));
                    }

                } else if ($copywriter_start_due == $day_after_tomorrow) {
                    // sending 'tomorrow is due' email => send to copy writer manager

                    $copy_writer_managers = $user_obj->getCopyWriterManager(); // get copywriter manager
                    foreach ($copy_writer_managers as $person) {
                        $details = [
                            'due' => $copywriter_start_due,
                            'who' => $person['first_name'],
                            'c_id' => $item->campaign_id,
                            'a_id' => $item->asset_id,
                            'task_name' => $item->project_name,
                            'asset_type' => ucwords(str_replace('_', ' ', $item->asset_type)),
                            'asset_status' => 'Copy Requested',
                            'url' => '/admin/asset/' . $item->asset_id . '/' . $item->campaign_id . '/' . $item->asset_type . '/' . $item->brand_name . '/detail_copy',
                        ];
                        // Email to asset creator!
                        $cc_list = array();
                        $cc_list[] = 'frank.russo@kissusa.com';
                        $cc_list[] = 'motuhin@kissusa.com';
                        //$cc_list[] = 'jilee2@kissusa.com';
                        Mail::to($person['email'])
//                        ->cc($cc_list)
                            ->send(new ReminderDueBefore($details));
//                    Mail::to('jilee2@kissusa.com')->send(new ReminderDueBefore($details));
                    }
                } else if (strtotime($copywriter_start_due) < strtotime($today)) {
                    // sending 'past due date' email => send to copy writer manager and directors
                    $copy_writer_managers = $user_obj->getCopyWriterManager();
                    foreach ($copy_writer_managers as $person) {
                        $details = [
                            'due' => $copywriter_start_due,
                            'who' => $person['first_name'],
                            'c_id' => $item->campaign_id,
                            'a_id' => $item->asset_id,
                            'task_name' => $item->project_name,
                            'asset_type' => ucwords(str_replace('_', ' ', $item->asset_type)),
                            'asset_status' => 'Copy Requested',
                            'url' => '/admin/asset/' . $item->asset_id . '/' . $item->campaign_id . '/' . $item->asset_type . '/' . $item->brand_name . '/detail_copy',
                        ];
                        // Email to copy writer! and director Frank and Mo
                        $cc_list = array();
                        $cc_list[] = 'frank.russo@kissusa.com';
                        $cc_list[] = 'motuhin@kissusa.com';
                        //$cc_list[] = 'jilee2@kissusa.com';
                        Mail::to($person['email'])
                            ->cc($cc_list)
                            ->send(new ReminderDueAfter($details));
//                    Mail::to('jilee2@kissusa.com')
//                        ->cc('jinsunglee.8033@gmail.com', 'jinsunglee.8033@gmail.com')
//                        ->send(new ReminderDueAfter($details));

                    }
                }
            }

            // [Step 2] for copy writer
            $result_copy_to_do = $obj->getCopyToDoStatus();
            foreach ($result_copy_to_do as $item) {

                $asset_type = $item->asset_type;
                $copy_to_do_start_due = date('Y-m-d');

                $time_to_spare = ($item->time_to_spare == 'N/A') ? 0 : $item->time_to_spare;
                $kdo = ($item->kdo == 'N/A') ? 0 : $item->kdo;
                $development = ($item->development == 'N/A') ? 0 : $item->development;
                $final_review = ($item->final_review == 'N/A') ? 0 : $item->final_review;
                $creative_work = ($item->creative_work == 'N/A') ? 0 : $item->creative_work;
                $creator_assign = ($item->creator_assign == 'N/A') ? 0 : $item->creator_assign;
                $copy_review = ($item->copy_review == 'N/A') ? 0 : $item->copy_review;
                $copy = ($item->copy == 'N/A') ? 0 : $item->copy;
                $copywriter_assign = ($item->copywriter_assign == 'N/A') ? 0 : $item->copywriter_assign;

                $step_8 = $time_to_spare + $kdo;        // e-commerce start
                $step_7 = $step_8 + $development;       // development start
                $step_6 = $step_7 + $final_review;      // creative review start
                $step_5 = $step_6 + $creative_work;     // creative work start
                $step_4 = $step_5 + $creator_assign;    // creator assign start
                $step_3 = $step_4 + $copy_review;       // copy review start
                $step_2 = $step_3 + $copy;              // copy start
                $step_1 = $step_2 + $copywriter_assign; // copywriter assign start

                $copy_to_do_start_due = date('m/d/Y', strtotime($item->due . ' -' . $step_2 . ' weekday'));

//                if ($asset_type == 'email_blast') {
//                    $copy_to_do_start_due = date('Y-m-d', strtotime($item->due . '-26 weekday'));
//                } else if ($asset_type == 'social_ad') {
//                    $copy_to_do_start_due = date('Y-m-d', strtotime($item->due . '-26 weekday'));
//                } else if ($asset_type == 'website_banners') {
//                    $copy_to_do_start_due = date('Y-m-d', strtotime($item->due . '-27 weekday'));
//                } else if ($asset_type == 'landing_page') {
//                    $copy_to_do_start_due = date('Y-m-d', strtotime($item->due . '-47 weekday'));
//                } else if ($asset_type == 'misc') {
//                    $copy_to_do_start_due = date('Y-m-d', strtotime($item->due . '-25 weekday'));
//                } else if ($asset_type == 'sms_request') {
//                    $copy_to_do_start_due = date('Y-m-d', strtotime($item->due . '-25 weekday'));
//                } else if ($asset_type == 'topcategories_copy') {
//                    $copy_to_do_start_due = date('Y-m-d', strtotime($item->due . '-5 weekday'));
//                } else if ($asset_type == 'programmatic_banners') {
//                    $copy_to_do_start_due = date('Y-m-d', strtotime($item->due . '-26 weekday'));
//                } else if ($asset_type == 'a_content') {
//                    $copy_to_do_start_due = date('Y-m-d', strtotime($item->due . '-39 weekday'));
//                } else if ($asset_type == 'youtube_copy') {
//                    $copy_to_do_start_due = date('Y-m-d', strtotime($item->due . '-12 weekday'));
//                }

                if ($copy_to_do_start_due == $today) {
                    // sending 'today is due' email => send to copy writer

                    $copy_writer = $user_obj->getCopywriterByFirstName($item->copy_writer); // get copywriter
                    foreach ($copy_writer as $person) {
                        $details = [
                            'due' => $copy_to_do_start_due,
                            'who' => $person['first_name'],
                            'c_id' => $item->campaign_id,
                            'a_id' => $item->asset_id,
                            'task_name' => $item->project_name,
                            'asset_type' => ucwords(str_replace('_', ' ', $item->asset_type)),
                            'asset_status' => 'Copy To Do',
                            'url' => '/admin/campaign/' . $item->campaign_id . '/edit#' . $item->asset_id,
                        ];
                        // Email to Assigned Copywriter
                        $cc_list = array();
                        $cc_list[] = 'frank.russo@kissusa.com';
                        $cc_list[] = 'motuhin@kissusa.com';
                        //$cc_list[] = 'jilee2@kissusa.com';
                        Mail::to($person['email'])
//                        ->cc($cc_list)
                            ->send(new ReminderDueToday($details));
//                    Mail::to('jilee2@kissusa.com')->send(new ReminderDueToday($details));
                    }

                } else if ($copy_to_do_start_due == $day_after_tomorrow) {
                    // sending 'tomorrow is due' email => send to copy writer manager

                    $copy_writer = $user_obj->getCopywriterByFirstName($item->copy_writer); // get copywriter manager
                    foreach ($copy_writer as $person) {
                        $details = [
                            'due' => $copy_to_do_start_due,
                            'who' => $person['first_name'],
                            'c_id' => $item->campaign_id,
                            'a_id' => $item->asset_id,
                            'task_name' => $item->project_name,
                            'asset_type' => ucwords(str_replace('_', ' ', $item->asset_type)),
                            'asset_status' => 'Copy To Do',
                            'url' => '/admin/campaign/' . $item->campaign_id . '/edit#' . $item->asset_id,
                        ];
                        // Email to asset creator!
                        $cc_list = array();
                        $cc_list[] = 'frank.russo@kissusa.com';
                        $cc_list[] = 'motuhin@kissusa.com';
                        //$cc_list[] = 'jilee2@kissusa.com';
                        Mail::to($person['email'])
//                        ->cc($cc_list)
                            ->send(new ReminderDueBefore($details));
//                    Mail::to('jilee2@kissusa.com')->send(new ReminderDueBefore($details));
                    }
                } else if (strtotime($copy_to_do_start_due) < strtotime($today)) {
                    // sending 'past due date' email => send to copy writer manager and directors
                    $copy_writer = $user_obj->getCopywriterByFirstName($item->copy_writer);
                    foreach ($copy_writer as $person) {
                        $details = [
                            'due' => $copy_to_do_start_due,
                            'who' => $person['first_name'],
                            'c_id' => $item->campaign_id,
                            'a_id' => $item->asset_id,
                            'task_name' => $item->project_name,
                            'asset_type' => ucwords(str_replace('_', ' ', $item->asset_type)),
                            'asset_status' => 'Copy To Do',
                            'url' => '/admin/campaign/' . $item->campaign_id . '/edit#' . $item->asset_id,
                        ];
                        // Email to copy writer! and director Frank and Mo
                        $cc_list = array();
                        $cc_list[] = 'frank.russo@kissusa.com';
                        $cc_list[] = 'motuhin@kissusa.com';
                        //$cc_list[] = 'jilee2@kissusa.com';
                        Mail::to($person['email'])
                            ->cc($cc_list)
                            ->send(new ReminderDueAfter($details));
//                    Mail::to('jilee2@kissusa.com')
//                        ->cc('jinsunglee.8033@gmail.com', 'jinsunglee.8033@gmail.com')
//                        ->send(new ReminderDueAfter($details));

                    }
                }

            }

            // [Step 3] copy_review for Asset Creator!!! [Step 3]
            $result_copy_review = $obj->getCopyReviewStatus();
            foreach ($result_copy_review as $item) {

                $asset_type = $item->asset_type;
                $copyreview_start_due = date('Y-m-d');

                $time_to_spare = ($item->time_to_spare == 'N/A') ? 0 : $item->time_to_spare;
                $kdo = ($item->kdo == 'N/A') ? 0 : $item->kdo;
                $development = ($item->development == 'N/A') ? 0 : $item->development;
                $final_review = ($item->final_review == 'N/A') ? 0 : $item->final_review;
                $creative_work = ($item->creative_work == 'N/A') ? 0 : $item->creative_work;
                $creator_assign = ($item->creator_assign == 'N/A') ? 0 : $item->creator_assign;
                $copy_review = ($item->copy_review == 'N/A') ? 0 : $item->copy_review;
                $copy = ($item->copy == 'N/A') ? 0 : $item->copy;
                $copywriter_assign = ($item->copywriter_assign == 'N/A') ? 0 : $item->copywriter_assign;

                $step_8 = $time_to_spare + $kdo;        // e-commerce start
                $step_7 = $step_8 + $development;       // development start
                $step_6 = $step_7 + $final_review;      // creative review start
                $step_5 = $step_6 + $creative_work;     // creative work start
                $step_4 = $step_5 + $creator_assign;    // creator assign start
                $step_3 = $step_4 + $copy_review;       // copy review start
                $step_2 = $step_3 + $copy;              // copy start
                $step_1 = $step_2 + $copywriter_assign; // copywriter assign start

                $copyreview_start_due = date('m/d/Y', strtotime($item->due . ' -' . $step_3 . ' weekday'));

//                if ($asset_type == 'email_blast') {
//                    $copyreview_start_due = date('Y-m-d', strtotime($item->due . '-24 weekday'));
//                } else if ($asset_type == 'social_ad') {
//                    $copyreview_start_due = date('Y-m-d', strtotime($item->due . '-24 weekday'));
//                } else if ($asset_type == 'website_banners') {
//                    $copyreview_start_due = date('Y-m-d', strtotime($item->due . '-25 weekday'));
//                } else if ($asset_type == 'landing_page') {
//                    $copyreview_start_due = date('Y-m-d', strtotime($item->due . '-43 weekday'));
//                } else if ($asset_type == 'misc') {
//                    $copyreview_start_due = date('Y-m-d', strtotime($item->due . '-23 weekday'));
//                } else if ($asset_type == 'sms_request') {
//                    $copyreview_start_due = date('Y-m-d', strtotime($item->due . '-23 weekday'));
//                } else if ($asset_type == 'topcategories_copy') {
//                    $copyreview_start_due = date('Y-m-d', strtotime($item->due . '-3 weekday'));
//                } else if ($asset_type == 'programmatic_banners') {
//                    $copyreview_start_due = date('Y-m-d', strtotime($item->due . '-24 weekday'));
//                } else if ($asset_type == 'a_content') {
//                    $copyreview_start_due = date('Y-m-d', strtotime($item->due . '-37 weekday'));
//                } else if ($asset_type == 'youtube_copy') {
//                    $copyreview_start_due = date('Y-m-d', strtotime($item->due . '-10 weekday'));
//                }

                if ($copyreview_start_due == $today) {
                    // sending 'today is due' email => to asset creator
                    if (isset($item->asset_author_id)) {
                        $details = [
                            'due' => $copyreview_start_due,
                            'who' => $item->asset_author_name,
                            'c_id' => $item->campaign_id,
                            'a_id' => $item->asset_id,
                            'task_name' => $item->project_name,
                            'asset_type' => ucwords(str_replace('_', ' ', $item->asset_type)),
                            'asset_status' => 'Copy Review',
                            'url' => '/admin/campaign/' . $item->campaign_id . '/edit#' . $item->asset_id,
                        ];
                        // Eamil to asset creator!
                        $cc_list = array();
                        $cc_list[] = 'frank.russo@kissusa.com';
                        $cc_list[] = 'motuhin@kissusa.com';
                        //$cc_list[] = 'jilee2@kissusa.com';
                        Mail::to($item->asset_author_email)
//                        ->cc($cc_list)
                            ->send(new ReminderDueToday($details));
//                    Mail::to('jilee2@kissusa.com')->send(new ReminderDueToday($details));
                    }
                } else if ($copyreview_start_due == $day_after_tomorrow) {
                    // sending 'tomorrow is due' email => to asset creator (okay)
                    if (isset($item->asset_author_id)) {
                        $details = [
                            'due' => $copyreview_start_due, // tomorrow date!
                            'who' => $item->asset_author_name,
                            'c_id' => $item->campaign_id,
                            'a_id' => $item->asset_id,
                            'task_name' => $item->project_name,
                            'asset_type' => ucwords(str_replace('_', ' ', $item->asset_type)),
                            'asset_status' => 'Copy Review',
                            'url' => '/admin/campaign/' . $item->campaign_id . '/edit#' . $item->asset_id,
                        ];
                        // Eamil to asset creator
                        $cc_list = array();
                        $cc_list[] = 'frank.russo@kissusa.com';
                        $cc_list[] = 'motuhin@kissusa.com';
                        //$cc_list[] = 'jilee2@kissusa.com';
                        Mail::to($item->asset_author_email)
//                        ->cc($cc_list)
                            ->send(new ReminderDueBefore($details));
//                    Mail::to('jilee2@kissusa.com')->send(new ReminderDueBefore($details));
                    }
                } else if (strtotime($copyreview_start_due) < strtotime($today)) {
                    // sending 'over due' email => to asset creator and directors (okay)
                    if (isset($item->asset_author_id)) {
                        $details = [
                            'due' => $copyreview_start_due,
                            'who' => $item->asset_author_name,
                            'c_id' => $item->campaign_id,
                            'a_id' => $item->asset_id,
                            'task_name' => $item->project_name,
                            'asset_type' => ucwords(str_replace('_', ' ', $item->asset_type)),
                            'asset_status' => 'Copy Review',
                            'url' => '/admin/campaign/' . $item->campaign_id . '/edit#' . $item->asset_id,
                        ];

                        $cc_list = array();
                        $cc_list[] = 'frank.russo@kissusa.com';
                        $cc_list[] = 'motuhin@kissusa.com';

                        // If MKT, Group reminder emails by brand
                        if ($item->author_team == 'Global Marketing') {
                            $brand_name = $item->brand_name;
                            $mktGroup_rs = $user_obj->getMKTGroupByBrand($brand_name);
                            foreach ($mktGroup_rs as $user) {
                                if ($item->asset_author_id != $user['id']) {
                                    $cc_list[] = $user['email'];
                                }
                            }
                        }

                        //Send email to director
                        Mail::to($item->asset_author_email)
                            ->cc($cc_list)
                            ->send(new ReminderDueAfter($details));

                    }
                }
            }

            // [Step 4] copy_complete for Creative Director!!! (Creator Assign start)
            $result_copy_complete = $obj->getCopyCompleteStatus();
            foreach ($result_copy_complete as $item) {

                $asset_type = $item->asset_type;
                $team_to = $item->team_to;
                $creative_assign_start_due = date('Y-m-d');

                $time_to_spare = ($item->time_to_spare == 'N/A') ? 0 : $item->time_to_spare;
                $kdo = ($item->kdo == 'N/A') ? 0 : $item->kdo;
                $development = ($item->development == 'N/A') ? 0 : $item->development;
                $final_review = ($item->final_review == 'N/A') ? 0 : $item->final_review;
                $creative_work = ($item->creative_work == 'N/A') ? 0 : $item->creative_work;
                $creator_assign = ($item->creator_assign == 'N/A') ? 0 : $item->creator_assign;
                $copy_review = ($item->copy_review == 'N/A') ? 0 : $item->copy_review;
                $copy = ($item->copy == 'N/A') ? 0 : $item->copy;
                $copywriter_assign = ($item->copywriter_assign == 'N/A') ? 0 : $item->copywriter_assign;

                $step_8 = $time_to_spare + $kdo;        // e-commerce start
                $step_7 = $step_8 + $development;       // development start
                $step_6 = $step_7 + $final_review;      // creative review start
                $step_5 = $step_6 + $creative_work;     // creative work start
                $step_4 = $step_5 + $creator_assign;    // creator assign start
                $step_3 = $step_4 + $copy_review;       // copy review start
                $step_2 = $step_3 + $copy;              // copy start
                $step_1 = $step_2 + $copywriter_assign; // copywriter assign start

                $creative_assign_start_due = date('m/d/Y', strtotime($item->due . ' -' . $step_4 . ' weekday'));

//                if ($asset_type == 'email_blast') {
//                    $creative_assign_start_due = date('Y-m-d', strtotime($item->due . '-22 weekday'));
//                } else if ($asset_type == 'social_ad') {
//                    $creative_assign_start_due = date('Y-m-d', strtotime($item->due . '-22 weekday'));
//                } else if ($asset_type == 'website_banners') {
//                    $creative_assign_start_due = date('Y-m-d', strtotime($item->due . '-23 weekday'));
//                } else if ($asset_type == 'landing_page') {
//                    $creative_assign_start_due = date('Y-m-d', strtotime($item->due . '-40 weekday'));
//                } else if ($asset_type == 'misc') {
//                    $creative_assign_start_due = date('Y-m-d', strtotime($item->due . '-21 weekday'));
//                } else if ($asset_type == 'sms_request') {
//                    $creative_assign_start_due = date('Y-m-d', strtotime($item->due . '-21 weekday'));
//                } else if ($asset_type == 'programmatic_banners') {
//                    $creative_assign_start_due = date('Y-m-d', strtotime($item->due . '-22 weekday'));
//                } else if ($asset_type == 'image_request') {
//                    $creative_assign_start_due = date('Y-m-d', strtotime($item->due . '-14 weekday'));
//                } else if ($asset_type == 'roll_over') {
//                    $creative_assign_start_due = date('Y-m-d', strtotime($item->due . '-15 weekday'));
//                } else if ($asset_type == 'store_front') {
//                    $creative_assign_start_due = date('Y-m-d', strtotime($item->due . '-35 weekday'));
//                } else if ($asset_type == 'a_content') {
//                    $creative_assign_start_due = date('Y-m-d', strtotime($item->due . '-35 weekday'));
//                } else if ($asset_type == 'youtube_copy') {
//                    $creative_assign_start_due = date('Y-m-d', strtotime($item->due . '-8 weekday'));
//                }

                if ($creative_assign_start_due == $today) {
                    // sending 'today is due' email => Hong, Geunho
                    if($team_to == 'creative') {

                        if ($item->brand_id == 5) { // If Joah.. => Geunho
                            $joah_team_leaders = $user_obj->getJoahDirector();
                            if (isset($joah_team_leaders[0])) {
                                foreach ($joah_team_leaders as $joah_team_leader) {
                                    $details = [
                                        'due' => $creative_assign_start_due,
                                        'who' => $joah_team_leader['first_name'],
                                        'c_id' => $item->campaign_id,
                                        'a_id' => $item->asset_id,
                                        'task_name' => $item->project_name,
                                        'asset_type' => ucwords(str_replace('_', ' ', $item->asset_type)),
                                        'asset_status' => 'Creator Assign',
                                        'url' => '/admin/campaign/' . $item->campaign_id . '/edit#' . $item->asset_id,
                                    ];
                                    $cc_list = array();
                                    $cc_list[] = 'frank.russo@kissusa.com';
                                    $cc_list[] = 'motuhin@kissusa.com';
                                    //$cc_list[] = 'jilee2@kissusa.com';
                                    Mail::to($joah_team_leader['email'])
                                        ->send(new ReminderDueToday($details));
                                }
                            }
                        } else { // If NOT Joah.. => Hong Jung
                            $creative_leaders = $user_obj->getCreativeDirector();
                            if (isset($creative_leaders[0])) {
                                foreach ($creative_leaders as $creative_leader) {
                                    $details = [
                                        'due' => $creative_assign_start_due,
                                        'who' => $creative_leader['first_name'],
                                        'c_id' => $item->campaign_id,
                                        'a_id' => $item->asset_id,
                                        'task_name' => $item->project_name,
                                        'asset_type' => ucwords(str_replace('_', ' ', $item->asset_type)),
                                        'asset_status' => 'Creator Assign',
                                        'url' => '/admin/campaign/' . $item->campaign_id . '/edit#' . $item->asset_id,
                                    ];
                                    $cc_list = array();
                                    $cc_list[] = 'frank.russo@kissusa.com';
                                    $cc_list[] = 'motuhin@kissusa.com';
                                    //$cc_list[] = 'jilee2@kissusa.com';
                                    Mail::to($creative_leader['email'])
                                        ->send(new ReminderDueToday($details));
                                }
                            }
                        }
                    } else if ($team_to == 'content') {
                        $content_leaders = $user_obj->getContentManager();
                        if (isset($content_leaders[0])) {
                            foreach ($content_leaders as $content_leader) {
                                $details = [
                                    'due' => $creative_assign_start_due,
                                    'who' => $content_leader['first_name'],
                                    'c_id' => $item->campaign_id,
                                    'a_id' => $item->asset_id,
                                    'task_name' => $item->project_name,
                                    'asset_type' => ucwords(str_replace('_', ' ', $item->asset_type)),
                                    'asset_status' => 'Creator Assign',
                                    'url' => '/admin/campaign/' . $item->campaign_id . '/edit#' . $item->asset_id,
                                ];
                                $cc_list = array();
                                $cc_list[] = 'frank.russo@kissusa.com';
                                $cc_list[] = 'motuhin@kissusa.com';
                                //$cc_list[] = 'jilee2@kissusa.com';
                                Mail::to($content_leader['email'])
                                    ->send(new ReminderDueToday($details));
                            }
                        }
                    } else if ($team_to == 'web production') {
                        $web_leaders = $user_obj->getWebProductionManager();
                        if (isset($web_leaders[0])) {
                            foreach ($web_leaders as $web_leader) {
                                $details = [
                                    'due' => $creative_assign_start_due,
                                    'who' => $web_leader['first_name'],
                                    'c_id' => $item->campaign_id,
                                    'a_id' => $item->asset_id,
                                    'task_name' => $item->project_name,
                                    'asset_type' => ucwords(str_replace('_', ' ', $item->asset_type)),
                                    'asset_status' => 'Creator Assign',
                                    'url' => '/admin/campaign/' . $item->campaign_id . '/edit#' . $item->asset_id,
                                ];
                                $cc_list = array();
                                $cc_list[] = 'frank.russo@kissusa.com';
                                $cc_list[] = 'motuhin@kissusa.com';
                                //$cc_list[] = 'jilee2@kissusa.com';
                                Mail::to($web_leader['email'])
                                    ->send(new ReminderDueToday($details));
                            }
                        }
                    }
                } else if ($creative_assign_start_due == $day_after_tomorrow) {
                    if($team_to == 'creative') {
                        // sending 'tomorrow is due' email => send to hong, geunho
                        if ($item->brand_id == 5) { // If Joah.. => Geunho
                            $joah_team_leaders = $user_obj->getJoahDirector();
                            if (isset($joah_team_leaders[0])) {
                                foreach ($joah_team_leaders as $joah_team_leader) {
                                    $details = [
                                        'due' => $creative_assign_start_due,
                                        'who' => $joah_team_leader['first_name'],
                                        'c_id' => $item->campaign_id,
                                        'a_id' => $item->asset_id,
                                        'task_name' => $item->project_name,
                                        'asset_type' => ucwords(str_replace('_', ' ', $item->asset_type)),
                                        'asset_status' => 'Creator Assign',
                                        'url' => '/admin/campaign/' . $item->campaign_id . '/edit#' . $item->asset_id,
                                    ];
                                    $cc_list = array();
                                    $cc_list[] = 'frank.russo@kissusa.com';
                                    $cc_list[] = 'motuhin@kissusa.com';
                                    //$cc_list[] = 'jilee2@kissusa.com';
                                    Mail::to($joah_team_leader['email'])
                                        ->send(new ReminderDueBefore($details));
                                }
                            }
                        } else { // If NOT Joah.. => Hong Jung
                            $creative_leaders = $user_obj->getCreativeDirector();
                            if (isset($creative_leaders[0])) {
                                foreach ($creative_leaders as $creative_leader) {
                                    $details = [
                                        'due' => $creative_assign_start_due,
                                        'who' => $creative_leader['first_name'],
                                        'c_id' => $item->campaign_id,
                                        'a_id' => $item->asset_id,
                                        'task_name' => $item->project_name,
                                        'asset_type' => ucwords(str_replace('_', ' ', $item->asset_type)),
                                        'asset_status' => 'Creator Assign',
                                        'url' => '/admin/campaign/' . $item->campaign_id . '/edit#' . $item->asset_id,
                                    ];
                                    $cc_list = array();
                                    $cc_list[] = 'frank.russo@kissusa.com';
                                    $cc_list[] = 'motuhin@kissusa.com';
                                    //$cc_list[] = 'jilee2@kissusa.com';
                                    Mail::to($creative_leader['email'])
                                        ->send(new ReminderDueBefore($details));
                                }
                            }
                        }
                    } else if ($team_to == 'content') {
                        $content_leaders = $user_obj->getContentManager();
                        if (isset($content_leaders[0])) {
                            foreach ($content_leaders as $content_leader) {
                                $details = [
                                    'due' => $creative_assign_start_due,
                                    'who' => $content_leader['first_name'],
                                    'c_id' => $item->campaign_id,
                                    'a_id' => $item->asset_id,
                                    'task_name' => $item->project_name,
                                    'asset_type' => ucwords(str_replace('_', ' ', $item->asset_type)),
                                    'asset_status' => 'Creator Assign',
                                    'url' => '/admin/campaign/' . $item->campaign_id . '/edit#' . $item->asset_id,
                                ];
                                $cc_list = array();
                                $cc_list[] = 'frank.russo@kissusa.com';
                                $cc_list[] = 'motuhin@kissusa.com';
                                //$cc_list[] = 'jilee2@kissusa.com';
                                Mail::to($content_leader['email'])
                                    ->send(new ReminderDueBefore($details));
                            }
                        }
                    } else if ($team_to == 'web production') {
                        $web_leaders = $user_obj->getWebProductionManager();
                        if (isset($web_leaders[0])) {
                            foreach ($web_leaders as $web_leader) {
                                $details = [
                                    'due' => $creative_assign_start_due,
                                    'who' => $web_leader['first_name'],
                                    'c_id' => $item->campaign_id,
                                    'a_id' => $item->asset_id,
                                    'task_name' => $item->project_name,
                                    'asset_type' => ucwords(str_replace('_', ' ', $item->asset_type)),
                                    'asset_status' => 'Creator Assign',
                                    'url' => '/admin/campaign/' . $item->campaign_id . '/edit#' . $item->asset_id,
                                ];
                                $cc_list = array();
                                $cc_list[] = 'frank.russo@kissusa.com';
                                $cc_list[] = 'motuhin@kissusa.com';
                                //$cc_list[] = 'jilee2@kissusa.com';
                                Mail::to($web_leader['email'])
                                    ->send(new ReminderDueBefore($details));
                            }
                        }
                    }

                } else if (strtotime($creative_assign_start_due) < strtotime($today)) {

                    if($team_to == 'creative') {
                        // sending 'past due date' if late, email to => hong, geunho and Flori, Haejin (their directors)
                        if ($item->brand_id == 5) { // If Joah.. => Geunho
                            $joah_team_leaders = $user_obj->getJoahDirector();
                            if (isset($joah_team_leaders[0])) {
                                foreach ($joah_team_leaders as $joah_team_leader) {
                                    $details = [
                                        'due' => $creative_assign_start_due,
                                        'who' => $joah_team_leader['first_name'],
                                        'c_id' => $item->campaign_id,
                                        'a_id' => $item->asset_id,
                                        'task_name' => $item->project_name,
                                        'asset_type' => ucwords(str_replace('_', ' ', $item->asset_type)),
                                        'asset_status' => 'Creator Assign',
                                        'url' => '/admin/campaign/' . $item->campaign_id . '/edit#' . $item->asset_id,
                                    ];
                                    // Send to director Haejin
                                    $cc_list = array();
                                    $cc_list[] = 'haejin.chang@kissusa.com';
                                    $cc_list[] = 'frank.russo@kissusa.com';
                                    $cc_list[] = 'motuhin@kissusa.com';
                                    //$cc_list[] = 'jilee2@kissusa.com';
                                    Mail::to($joah_team_leader['email'])
                                        ->cc($cc_list)
                                        ->send(new ReminderDueAfter($details));
                                }
                            }
                        } else { // If NOT Joah.. => Hong Jung
                            $creative_leaders = $user_obj->getCreativeDirector();
                            if (isset($creative_leaders[0])) {
                                foreach ($creative_leaders as $creative_leader) {
                                    $details = [
                                        'due' => $creative_assign_start_due,
                                        'who' => $creative_leader['first_name'],
                                        'c_id' => $item->campaign_id,
                                        'a_id' => $item->asset_id,
                                        'task_name' => $item->project_name,
                                        'asset_type' => ucwords(str_replace('_', ' ', $item->asset_type)),
                                        'asset_status' => 'Creator Assign',
                                        'url' => '/admin/campaign/' . $item->campaign_id . '/edit#' . $item->asset_id,
                                    ];
                                    // Send to director Flori
                                    $cc_list = array();
                                    $cc_list[] = 'flori.ohm@kissusa.com';
                                    $cc_list[] = 'frank.russo@kissusa.com';
                                    $cc_list[] = 'motuhin@kissusa.com';
                                    //$cc_list[] = 'jilee2@kissusa.com';
                                    Mail::to($creative_leader['email'])
                                        ->cc($cc_list)
                                        ->send(new ReminderDueAfter($details));
                                }
                            }
                        }
                    } else if ($team_to == 'content') {
                        $content_leaders = $user_obj->getContentManager();
                        if (isset($content_leaders[0])) {
                            foreach ($content_leaders as $content_leader) {
                                $details = [
                                    'due' => $creative_assign_start_due,
                                    'who' => $content_leader['first_name'],
                                    'c_id' => $item->campaign_id,
                                    'a_id' => $item->asset_id,
                                    'task_name' => $item->project_name,
                                    'asset_type' => ucwords(str_replace('_', ' ', $item->asset_type)),
                                    'asset_status' => 'Creator Assign',
                                    'url' => '/admin/campaign/' . $item->campaign_id . '/edit#' . $item->asset_id,
                                ];
                                // Send to director Flori
                                $cc_list = array();
                                $cc_list[] = 'frank.russo@kissusa.com';
                                $cc_list[] = 'motuhin@kissusa.com';
                                //$cc_list[] = 'jilee2@kissusa.com';
                                Mail::to($content_leader['email'])
                                    ->cc($cc_list)
                                    ->send(new ReminderDueAfter($details));
                            }
                        }
                    } else if ($team_to == 'web production') {
                        $web_leaders = $user_obj->getWebProductionManager();
                        if (isset($web_leaders[0])) {
                            foreach ($web_leaders as $web_leader) {
                                $details = [
                                    'due' => $creative_assign_start_due,
                                    'who' => $web_leader['first_name'],
                                    'c_id' => $item->campaign_id,
                                    'a_id' => $item->asset_id,
                                    'task_name' => $item->project_name,
                                    'asset_type' => ucwords(str_replace('_', ' ', $item->asset_type)),
                                    'asset_status' => 'Creator Assign',
                                    'url' => '/admin/campaign/' . $item->campaign_id . '/edit#' . $item->asset_id,
                                ];
                                // Send to director Flori
                                $cc_list = array();
                                $cc_list[] = 'frank.russo@kissusa.com';
                                $cc_list[] = 'motuhin@kissusa.com';
                                Mail::to($web_leader['email'])
                                    ->cc($cc_list)
                                    ->send(new ReminderDueAfter($details));
                            }
                        }
                    }

                }

            }

            // [Step 5] to_do for designer!!! (Creative Work start)
            $result_to_do = $obj->getToDoStatus();
            foreach ($result_to_do as $item) {

                $asset_type = $item->asset_type;
                $creative_work_start_due = date('Y-m-d');

                $time_to_spare = ($item->time_to_spare == 'N/A') ? 0 : $item->time_to_spare;
                $kdo = ($item->kdo == 'N/A') ? 0 : $item->kdo;
                $development = ($item->development == 'N/A') ? 0 : $item->development;
                $final_review = ($item->final_review == 'N/A') ? 0 : $item->final_review;
                $creative_work = ($item->creative_work == 'N/A') ? 0 : $item->creative_work;
                $creator_assign = ($item->creator_assign == 'N/A') ? 0 : $item->creator_assign;
                $copy_review = ($item->copy_review == 'N/A') ? 0 : $item->copy_review;
                $copy = ($item->copy == 'N/A') ? 0 : $item->copy;
                $copywriter_assign = ($item->copywriter_assign == 'N/A') ? 0 : $item->copywriter_assign;

                $step_8 = $time_to_spare + $kdo;        // e-commerce start
                $step_7 = $step_8 + $development;       // development start
                $step_6 = $step_7 + $final_review;      // creative review start
                $step_5 = $step_6 + $creative_work;     // creative work start
                $step_4 = $step_5 + $creator_assign;    // creator assign start
                $step_3 = $step_4 + $copy_review;       // copy review start
                $step_2 = $step_3 + $copy;              // copy start
                $step_1 = $step_2 + $copywriter_assign; // copywriter assign start

                $creative_work_start_due = date('m/d/Y', strtotime($item->due . ' -' . $step_5 . ' weekday'));

//                if ($asset_type == 'email_blast') {
//                    $creative_work_start_due = date('Y-m-d', strtotime($item->due . '-20 weekday'));
//                } else if ($asset_type == 'social_ad') {
//                    $creative_work_start_due = date('Y-m-d', strtotime($item->due . '-20 weekday'));
//                } else if ($asset_type == 'website_banners') {
//                    $creative_work_start_due = date('Y-m-d', strtotime($item->due . '-21 weekday'));
//                } else if ($asset_type == 'landing_page') {
//                    $creative_work_start_due = date('Y-m-d', strtotime($item->due . '-38 weekday'));
//                } else if ($asset_type == 'misc') {
//                    $creative_work_start_due = date('Y-m-d', strtotime($item->due . '-19 weekday'));
//                } else if ($asset_type == 'sms_request') {
//                    $creative_work_start_due = date('Y-m-d', strtotime($item->due . '-19 weekday'));
//                } else if ($asset_type == 'programmatic_banners') {
//                    $creative_work_start_due = date('Y-m-d', strtotime($item->due . '-20 weekday'));
//                } else if ($asset_type == 'image_request') {
//                    $creative_work_start_due = date('Y-m-d', strtotime($item->due . '-12 weekday'));
//                } else if ($asset_type == 'roll_over') {
//                    $creative_work_start_due = date('Y-m-d', strtotime($item->due . '-13 weekday'));
//                } else if ($asset_type == 'store_front') {
//                    $creative_work_start_due = date('Y-m-d', strtotime($item->due . '-33 weekday'));
//                } else if ($asset_type == 'a_content') {
//                    $creative_work_start_due = date('Y-m-d', strtotime($item->due . '-33 weekday'));
//                }

                $assignee_first_name = $item->assignee;

                if ($item->team_to == 'content') {
                    $assignee_obj = $user_obj->getContentByFirstName($assignee_first_name);
                    $team_manager = 'miyi@kissusa.com';
                } else if ($item->team_to == 'web production') {
                    $assignee_obj = $user_obj->getWebByFirstName($assignee_first_name);
                    $team_manager = 'watalhami@kissusa.com';
                } else {
                    $assignee_obj = $user_obj->getDesignerByFirstName($assignee_first_name);
                    $team_manager = 'jolee2@kissusa.com';
                }

                if(isset($assignee_obj[0])) {

                    $creator_email = $assignee_obj[0]->email;

                    if ($creative_work_start_due == $today) {
                        // sending 'today is due' email => send to designer
                        if ($creator_email) {
                            $details = [
                                'due' => $creative_work_start_due,
                                'who' => $item->assignee,
                                'c_id' => $item->campaign_id,
                                'a_id' => $item->asset_id,
                                'task_name' => $item->project_name,
                                'asset_type' => ucwords(str_replace('_', ' ', $item->asset_type)),
                                'asset_status' => 'Creative Work',
                                'url' => '/admin/campaign/' . $item->campaign_id . '/edit#' . $item->asset_id,
                            ];

                            $cc_list = array();
                            $cc_list[] = 'frank.russo@kissusa.com';
                            $cc_list[] = 'motuhin@kissusa.com';
                            //$cc_list[] = 'jilee2@kissusa.com';
                            Mail::to($creator_email)
                                ->send(new ReminderDueToday($details));
                        }
                    } else if ($creative_work_start_due == $day_after_tomorrow) {
                        // sending 'tomorrow is due' email => send to designer
                        if ($creator_email) {
                            $details = [
                                'due' => $creative_work_start_due,
                                'who' => $item->assignee,
                                'c_id' => $item->campaign_id,
                                'a_id' => $item->asset_id,
                                'task_name' => $item->project_name,
                                'asset_type' => ucwords(str_replace('_', ' ', $item->asset_type)),
                                'asset_status' => 'Creative Work',
                                'url' => '/admin/campaign/' . $item->campaign_id . '/edit#' . $item->asset_id,
                            ];
                            $cc_list = array();
                            $cc_list[] = 'frank.russo@kissusa.com';
                            $cc_list[] = 'motuhin@kissusa.com';
                            //$cc_list[] = 'jilee2@kissusa.com';
                            Mail::to($creator_email)
                                ->send(new ReminderDueBefore($details));
                        }
                    } else if (strtotime($creative_work_start_due) < strtotime($today)) {
                        // sending 'past due date' if late, email to => Jongjeong, Geunho
                        if ($creator_email) {
                            $details = [
                                'due' => $creative_work_start_due,
                                'who' => $item->assignee,
                                'c_id' => $item->campaign_id,
                                'a_id' => $item->asset_id,
                                'task_name' => $item->project_name,
                                'asset_type' => ucwords(str_replace('_', ' ', $item->asset_type)),
                                'asset_status' => 'Creative Work',
                                'url' => '/admin/campaign/' . $item->campaign_id . '/edit#' . $item->asset_id,
                            ];
                            // Send to leader .. Hong, Geunho-joah
                            if ($item->brand_id == 5) { // if joah, Geunho
                                $cc_list = array();
                                $cc_list[] = 'geunho.kang@kissusa.com';
                                $cc_list[] = 'frank.russo@kissusa.com';
                                $cc_list[] = 'motuhin@kissusa.com';
                                //$cc_list[] = 'jilee2@kissusa.com';
                                Mail::to($creator_email)
                                    ->cc($cc_list)
                                    ->send(new ReminderDueAfter($details));

                            } else { // others, Hong
                                $cc_list = array();
                                $cc_list[] = $team_manager;
                                $cc_list[] = 'frank.russo@kissusa.com';
                                $cc_list[] = 'motuhin@kissusa.com';
                                //$cc_list[] = 'jilee2@kissusa.com';
                                Mail::to($creator_email)
                                    ->cc($cc_list)
                                    ->send(new ReminderDueAfter($details));
                            }
                        }
                    }
                }
            }

            // [Step 6] waiting_approval (creative review) for asset creator!!! (Final Review start)
            $result_done = $obj->getDoneStatus();
            foreach ($result_done as $item) {

                $asset_type = $item->asset_type;
                $final_review_start_due = date('Y-m-d');

                $time_to_spare = ($item->time_to_spare == 'N/A') ? 0 : $item->time_to_spare;
                $kdo = ($item->kdo == 'N/A') ? 0 : $item->kdo;
                $development = ($item->development == 'N/A') ? 0 : $item->development;
                $final_review = ($item->final_review == 'N/A') ? 0 : $item->final_review;
                $creative_work = ($item->creative_work == 'N/A') ? 0 : $item->creative_work;
                $creator_assign = ($item->creator_assign == 'N/A') ? 0 : $item->creator_assign;
                $copy_review = ($item->copy_review == 'N/A') ? 0 : $item->copy_review;
                $copy = ($item->copy == 'N/A') ? 0 : $item->copy;
                $copywriter_assign = ($item->copywriter_assign == 'N/A') ? 0 : $item->copywriter_assign;

                $step_8 = $time_to_spare + $kdo;        // e-commerce start
                $step_7 = $step_8 + $development;       // development start
                $step_6 = $step_7 + $final_review;      // creative review start
                $step_5 = $step_6 + $creative_work;     // creative work start
                $step_4 = $step_5 + $creator_assign;    // creator assign start
                $step_3 = $step_4 + $copy_review;       // copy review start
                $step_2 = $step_3 + $copy;              // copy start
                $step_1 = $step_2 + $copywriter_assign; // copywriter assign start

                $final_review_start_due = date('m/d/Y', strtotime($item->due . ' -' . $step_6 . ' weekday'));

//                if ($asset_type == 'email_blast') {
//                    $final_review_start_due = date('Y-m-d', strtotime($item->due . '-10 weekday'));
//                } else if ($asset_type == 'social_ad') {
//                    $final_review_start_due = date('Y-m-d', strtotime($item->due . '-10 weekday'));
//                } else if ($asset_type == 'website_banners') {
//                    $final_review_start_due = date('Y-m-d', strtotime($item->due . '-11 weekday'));
//                } else if ($asset_type == 'landing_page') {
//                    $final_review_start_due = date('Y-m-d', strtotime($item->due . '-18 weekday'));
//                } else if ($asset_type == 'misc') {
//                    $final_review_start_due = date('Y-m-d', strtotime($item->due . '-9 weekday'));
//                } else if ($asset_type == 'sms_request') {
//                    $final_review_start_due = date('Y-m-d', strtotime($item->due . '-9 weekday'));
//                } else if ($asset_type == 'programmatic_banners') {
//                    $final_review_start_due = date('Y-m-d', strtotime($item->due . '-10 weekday'));
//                } else if ($asset_type == 'image_request') {
//                    $final_review_start_due = date('Y-m-d', strtotime($item->due . '-2 weekday'));
//                } else if ($asset_type == 'roll_over') {
//                    $final_review_start_due = date('Y-m-d', strtotime($item->due . '-3 weekday'));
//                } else if ($asset_type == 'store_front') {
//                    $final_review_start_due = date('Y-m-d', strtotime($item->due . '-13 weekday'));
//                } else if ($asset_type == 'a_content') {
//                    $final_review_start_due = date('Y-m-d', strtotime($item->due . '-13 weekday'));
//                }

                if ($final_review_start_due == $today) {
                    // sending 'today is due' email => asset creator
                    if (isset($item->asset_author_id)) {
                        $details = [
                            'due' => $final_review_start_due,
                            'who' => $item->asset_author_name,
                            'c_id' => $item->campaign_id,
                            'a_id' => $item->asset_id,
                            'task_name' => $item->project_name,
                            'asset_type' => ucwords(str_replace('_', ' ', $item->asset_type)),
                            'asset_status' => 'Final Review',
                            'url' => '/admin/campaign/' . $item->campaign_id . '/edit#' . $item->asset_id,
                        ];
                        // Eamil to asset creator!
                        $cc_list = array();
                        $cc_list[] = 'frank.russo@kissusa.com';
                        $cc_list[] = 'motuhin@kissusa.com';
                        //$cc_list[] = 'jilee2@kissusa.com';
                        Mail::to($item->asset_author_email)
//                        ->cc($cc_list)
                            ->send(new ReminderDueToday($details));
//                    Mail::to('jilee2@kissusa.com')->send(new ReminderDueToday($details));
                    }


                } else if ($final_review_start_due == $day_after_tomorrow) {
                    // sending 'tomorrow is due' email => send to asset creator
                    if (isset($item->asset_author_id)) {
                        $details = [
                            'due' => $final_review_start_due, // tomorrow date!
                            'who' => $item->asset_author_name,
                            'c_id' => $item->campaign_id,
                            'a_id' => $item->asset_id,
                            'task_name' => $item->project_name,
                            'asset_type' => ucwords(str_replace('_', ' ', $item->asset_type)),
                            'asset_status' => 'Final Review',
                            'url' => '/admin/campaign/' . $item->campaign_id . '/edit#' . $item->asset_id,
                        ];
                        // Eamil to asset creator
                        $cc_list = array();
                        $cc_list[] = 'frank.russo@kissusa.com';
                        $cc_list[] = 'motuhin@kissusa.com';
                        //$cc_list[] = 'jilee2@kissusa.com';
                        Mail::to($item->asset_author_email)
//                        ->cc($cc_list)
                            ->send(new ReminderDueBefore($details));
//                    Mail::to('jilee2@kissusa.com')->send(new ReminderDueBefore($details));
                    }

                } else if (strtotime($final_review_start_due) < strtotime($today)) {
                    // sending 'past due date' if late, email to => asset creator and directors. same as copy_review
                    if (isset($item->asset_author_id)) {
                        $details = [
                            'due' => $final_review_start_due,
                            'who' => $item->asset_author_name,
                            'c_id' => $item->campaign_id,
                            'a_id' => $item->asset_id,
                            'task_name' => $item->project_name,
                            'asset_type' => ucwords(str_replace('_', ' ', $item->asset_type)),
                            'asset_status' => 'Final Review',
                            'url' => '/admin/campaign/' . $item->campaign_id . '/edit#' . $item->asset_id,
                        ];

                        $cc_list = array();
                        $cc_list[] = 'frank.russo@kissusa.com';
                        $cc_list[] = 'motuhin@kissusa.com';
                        //$cc_list[] = 'jilee2@kissusa.com';

                        // If MKT, Group reminder emails by brand
                        if ($item->author_team == 'Global Marketing') {
                            $brand_name = $item->brand_name;
                            $mktGroup_rs = $user_obj->getMKTGroupByBrand($brand_name);
                            foreach ($mktGroup_rs as $user) {
                                if ($item->asset_author_id != $user['id']) {
                                    $cc_list[] = $user['email'];
                                }
                            }
                        }

                        // Send email to director
                        Mail::to($item->asset_author_email)
                            ->cc($cc_list)
                            ->send(new ReminderDueAfter($details));

//                        Mail::to('jilee2@kissusa.com')
//                            ->cc('jinsunglee.8033@gmail.com', '33.jinsunglee@gmail.com')
//                            ->send(new ReminderDueAfter($details));

                    }
                }
            }
        }
    }


    public function clean_up_projects(){

        $project_obj = new CampaignRepository();
        $project_obj->clean_up_more_than_two_weeks_projects();

    }

    public function test(){

        ddd("test");
//        $a_id = 5401;
//        $c_id = 1704;
//
//        $asset_index_obj = new CampaignAssetIndexRepository();
//        $asset_index_rs = $asset_index_obj->findById($a_id);
//
//        $asset_type = $asset_index_rs['type'];
//        $asset_status = $asset_index_rs['status'];
//        $asset_author_id = $asset_index_rs['author_id'];
//
//        $campaign_obj = new CampaignRepository();
//        $campaign_rs = $campaign_obj->findById($c_id);
//
//        $author_id = $campaign_rs['author_id'];
//
//        $user_obj = new UserRepository();
//
//        if($asset_author_id) {
//            $asset_author_rs = $user_obj->findById($asset_author_id);
//            $details = [
//                'who' => $asset_author_rs['first_name'],
//                'c_id' => $c_id,
//                'a_id' => $a_id,
//                'task_name' => $campaign_rs['name'],
//                'asset_type' => $asset_type,
//                'asset_status' => $asset_status,
//                'url' => '/admin/campaign/' . $c_id . '/edit#' . $a_id,
//            ];
//            $cc_list = array();
//            if($campaign_rs['author_team'] == 'Global Marketing'){
//                $brand_id = $campaign_rs['campaign_brand'];
//                $brand_obj = new CampaignBrandsRepository();
//                $brand_rs = $brand_obj->findById($brand_id);
//                $brand_name = $brand_rs['campaign_name'];
//                $mktGroup_rs = $user_obj->getMKTGroupByBrand($brand_name);
//                foreach ($mktGroup_rs as $user) {
//                    if($asset_author_id != $user['id']) {
//                        $cc_list[] = $user['email'];
//                    }
//                }
//                Mail::to($asset_author_rs['email'])
//                    ->cc($cc_list)
//                    ->send(new CopyReview($details));
//            }else{
//                // Eamil to asset creator..
//                Mail::to($asset_author_rs['email'])->send(new CopyReview($details));
//            }
//        }


    }

}
