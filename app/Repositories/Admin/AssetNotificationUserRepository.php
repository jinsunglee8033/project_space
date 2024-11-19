<?php

namespace App\Repositories\Admin;

use DB;

use App\Repositories\Admin\Interfaces\AssetNotificationUserRepositoryInterface;

use App\Models\AssetNotificationUser;
use Illuminate\Database\Eloquent\Model;

class AssetNotificationUserRepository implements AssetNotificationUserRepositoryInterface
{
    public function findAll($options = [])
    {
        $assetNotificationUser = new AssetNotificationUser();

        $assetNotificationUser = $assetNotificationUser->get();

        return $assetNotificationUser;
    }

    public function findById($id)
    {
        return AssetNotificationUser::findOrFail($id);
    }

    public function create($params = [])
    {
        return DB::transaction(function () use ($params) {
            $assetNotificationUser = AssetNotificationUser::create($params);

            return $assetNotificationUser;
        });
    }

    public function update($id, $params = [])
    {
        $assetNotificationUser = AssetNotificationUser::findOrFail($id);

        return DB::transaction(function () use ($params, $assetNotificationUser) {
            $assetNotificationUser->update($params);

            return $assetNotificationUser;
        });
    }

    public function delete($id)
    {
        $role  = AssetNotificationUser::findOrFail($id);

        return $role->delete();
    }

    public function getListByAssetId($a_id)
    {
        $assetNotificationUser = new AssetNotificationUser();
        $assetNotificationUser = $assetNotificationUser->Where('asset_id', '=', "$a_id");
        return $assetNotificationUser->get();
    }

    public function getByAssetId($a_id)
    {
        $assetNotificationUser = new AssetNotificationUser();
        $assetNotificationUser = $assetNotificationUser->Where('asset_id', '=', "$a_id");
        return $assetNotificationUser->get();
    }

    public function getCopyRequestStatus()
    {
        return DB::select(
            'select c_id as campaign_id,
                    a_id as asset_id,
                    a_type as asset_type,
                    due,
                    ci.name as project_name,
                    cai.status,
                    cb.campaign_name as brand_name,
                    cb.id as brand_id,
                    alt.copywriter_assign,
                    alt.copy,
                    alt.copy_review,
                    alt.creator_assign,
                    alt.creative_work,
                    alt.final_review,
                    alt.development,
                    alt.kdo,
                    alt.time_to_spare,
                    alt.total
            from
                    (select id as c_id, asset_id as a_id, type as a_type, email_blast_date as due from campaign_type_email_blast
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_landing_page
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_misc
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_sms_request
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, date_from as due from campaign_type_social_ad
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, date_from as due from campaign_type_programmatic_banners
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_website_banners
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_topcategories_copy
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_a_content
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_youtube_copy
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_info_graphic) b
            left join campaign_asset_index cai on cai.id = a_id
            left join campaign_item ci on ci.id = c_id
            left join campaign_brands cb on cb.id = ci.campaign_brand
            left join asset_lead_time alt on alt.asset_name = a_type
            where cai.status = "copy_requested"
            and ci.name is not null
            order by due asc');
    }

    public function getCopyToDoStatus()
    {
        return DB::select(
            'select c_id as campaign_id,
                    a_id as asset_id,
                    a_type as asset_type,
                    due,
                    ci.name as project_name,
                    cai.status,
                    cai.copy_writer,
                    cb.campaign_name as brand_name,
                    cb.id as brand_id,
                    alt.copywriter_assign,
                    alt.copy,
                    alt.copy_review,
                    alt.creator_assign,
                    alt.creative_work,
                    alt.final_review,
                    alt.development,
                    alt.kdo,
                    alt.time_to_spare,
                    alt.total
            from
                    (select id as c_id, asset_id as a_id, type as a_type, email_blast_date as due from campaign_type_email_blast
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_landing_page
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_misc
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_sms_request
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, date_from as due from campaign_type_social_ad
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, date_from as due from campaign_type_programmatic_banners
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_website_banners
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_topcategories_copy
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_a_content
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_youtube_copy
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_info_graphic) b
            left join campaign_asset_index cai on cai.id = a_id
            left join campaign_item ci on ci.id = c_id
            left join campaign_brands cb on cb.id = ci.campaign_brand
            left join asset_lead_time alt on alt.asset_name = a_type
            where cai.status = "copy_to_do"
            and ci.name is not null
            order by due asc');
    }

    public function getCopyReviewStatus()
    {
        return DB::select(
            'select c_id as campaign_id,
                    author_team as author_team,
                    a_id as asset_id,
                    a_type as asset_type,
                    due,
                    ci.name as project_name,
                    u.id as asset_author_id,
                    u.first_name as asset_author_name,
                    u.email as asset_author_email,
                    cai.status,
                    cb.campaign_name as brand_name,
                    cb.id as brand_id,
                    alt.copywriter_assign,
                    alt.copy,
                    alt.copy_review,
                    alt.creator_assign,
                    alt.creative_work,
                    alt.final_review,
                    alt.development,
                    alt.kdo,
                    alt.time_to_spare,
                    alt.total
            from
                    (select id as c_id, asset_id as a_id, type as a_type, email_blast_date as due from campaign_type_email_blast
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_landing_page
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_misc
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_sms_request
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, date_from as due from campaign_type_social_ad
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, date_from as due from campaign_type_programmatic_banners
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_website_banners
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_topcategories_copy
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_a_content
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_youtube_copy
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_info_graphic) b
            left join campaign_asset_index cai on cai.id = a_id
            left join campaign_item ci on ci.id = c_id
            left join users u on u.id = cai.author_id
            left join campaign_brands cb on cb.id = ci.campaign_brand
            left join asset_lead_time alt on alt.asset_name = a_type
            where cai.status = "copy_review"
            and ci.name is not null
            order by due asc');
    }

    public function getCopyCompleteStatus()
    {
        return DB::select(
            'select c_id as campaign_id,
                    a_id as asset_id,
                    a_type as asset_type,
                    due,
                    ci.name as project_name,
                    cai.status,
                    cai.team_to,
                    cb.campaign_name as brand_name,
                    cb.id as brand_id,
                    alt.copywriter_assign,
                    alt.copy,
                    alt.copy_review,
                    alt.creator_assign,
                    alt.creative_work,
                    alt.final_review,
                    alt.development,
                    alt.kdo,
                    alt.time_to_spare,
                    alt.total
            from
                    (select id as c_id, asset_id as a_id, type as a_type, email_blast_date as due from  campaign_type_email_blast
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_landing_page
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_misc
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_sms_request
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, date_from as due from campaign_type_social_ad
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, date_from as due from campaign_type_programmatic_banners
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_website_banners
                    union all
					select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_image_request
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_roll_over
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_store_front
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_a_content
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_youtube_copy
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_info_graphic) b
            left join campaign_asset_index cai on cai.id = a_id
            left join campaign_item ci on ci.id = c_id
            left join campaign_brands cb on cb.id = ci.campaign_brand
            left join asset_lead_time alt on alt.asset_name = a_type
            where cai.status = "copy_complete"
            and ci.name is not null
            order by due asc');
    }

    public function getToDoStatus()
    {
        return DB::select(
            'select  c_id as campaign_id,
                    a_id as asset_id,
                    a_type as asset_type,
                    due,
                    ci.name as project_name,
                    u.first_name as author_name,
                    cai.status,
                    cai.assignee,
                    cai.team_to,
                    cb.campaign_name as brand_name,
                    cb.id as brand_id,
                    alt.copywriter_assign,
                    alt.copy,
                    alt.copy_review,
                    alt.creator_assign,
                    alt.creative_work,
                    alt.final_review,
                    alt.development,
                    alt.kdo,
                    alt.time_to_spare,
                    alt.total
            from
                    (select id as c_id, asset_id as a_id, type as a_type, email_blast_date as due from  campaign_type_email_blast
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_landing_page
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_misc
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_sms_request
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, date_from as due from campaign_type_social_ad
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, date_from as due from campaign_type_programmatic_banners
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_website_banners
                    union all
					select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_image_request
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_roll_over
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_store_front
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_a_content
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_youtube_copy
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_info_graphic) b
            left join campaign_asset_index cai on cai.id = a_id
            left join campaign_item ci on ci.id = c_id
            left join users u on u.id = ci.author_id
            left join campaign_brands cb on cb.id = ci.campaign_brand
            left join asset_lead_time alt on alt.asset_name = a_type
            where cai.status = "to_do"
            and ci.name is not null
            order by due asc');
    }

    public function getDoneStatus()
    {
        return DB::select(
            'select c_id as campaign_id,
                    author_team as author_team,
                    a_id as asset_id,
                    a_type as asset_type,
                    due,
                    ci.name as project_name,
                    u.id as asset_author_id,
                    u.first_name as asset_author_name,
                    u.email as asset_author_email,
                    cai.status,
                    cai.assignee,
                    cb.campaign_name as brand_name,
                    cb.id as brand_id,
                    alt.copywriter_assign,
                    alt.copy,
                    alt.copy_review,
                    alt.creator_assign,
                    alt.creative_work,
                    alt.final_review,
                    alt.development,
                    alt.kdo,
                    alt.time_to_spare,
                    alt.total
            from
                    (select id as c_id, asset_id as a_id, type as a_type, email_blast_date as due from  campaign_type_email_blast
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_landing_page
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_misc
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_sms_request
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, date_from as due from campaign_type_social_ad
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, date_from as due from campaign_type_programmatic_banners
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_website_banners
                    union all
					select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_image_request
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_roll_over
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_store_front
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_a_content
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_youtube_copy
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_info_graphic) b
            left join campaign_asset_index cai on cai.id = a_id
            left join campaign_item ci on ci.id = c_id
            left join users u on u.id = cai.author_id
            left join campaign_brands cb on cb.id = ci.campaign_brand
            left join asset_lead_time alt on alt.asset_name = a_type
            where cai.status = "done"
            and ci.name is not null
            order by due asc');
    }

}
