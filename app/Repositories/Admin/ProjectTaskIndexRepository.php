<?php

namespace App\Repositories\Admin;

use App\Repositories\Admin\Interfaces\ProjectTaskIndexRepositoryInterface;
use Carbon\Carbon;
use DB;


use App\Models\ProjectTaskIndex;
use Illuminate\Database\Eloquent\Model;

class ProjectTaskIndexRepository implements ProjectTaskIndexRepositoryInterface
{
    public function findAll($options = [])
    {
        $perPage = $options['per_page'] ?? null;
        $orderByFields = $options['order'] ?? [];
        $id = $options['id'] ?? [];

        $projectTaskIndex = new ProjectTaskIndex();

        if ($id) {
            $projectTaskIndex = $projectTaskIndex
                ->where('id', $id);
        }

        if ($orderByFields) {
            foreach ($orderByFields as $field => $sort) {
                $projectTaskIndex = $projectTaskIndex->orderBy($field, $sort);
            }
        }

        if ($perPage) {
            return $projectTaskIndex->paginate($perPage);
        }

        $projectTaskIndex = $projectTaskIndex->get();

        return $projectTaskIndex;
    }

    public function findById($id)
    {
        return projectTaskIndex::findOrFail($id);
    }

    public function create($params = [])
    {
        return DB::transaction(function () use ($params) {
            $projectTaskIndex = projectTaskIndex::create($params);
//            $this->syncRolesAndPermissions($params, $campaignBrand);

            return $projectTaskIndex;
        });
    }

    public function update($id, $params = [])
    {
        $projectTaskIndex = projectTaskIndex::findOrFail($id);

        return DB::transaction(function () use ($params, $projectTaskIndex) {
            $projectTaskIndex->update($params);

            return $projectTaskIndex;
        });
    }

    public function delete($id)
    {
        $projectTaskIndex  = projectTaskIndex::findOrFail($id);

        return $projectTaskIndex->delete();
    }

    public function get_author_id_by_task_id($task_id)
    {
        $obj = ProjectTaskIndex::where('id', $task_id)->first();
        return $obj->author_id;
    }

    public function get_project_id_by_task_id($task_id)
    {
        $obj = ProjectTaskIndex::where('id', $task_id)->first();
        return $obj->project_id;
    }

    public function get_task_list_by_project_id($project_id)
    {
        return DB::select('
                select p.id as id,
                       p.project_id as project_id,
                       p.author_id as author_id,
                       concat(u.first_name, " ", u.last_name) as author_name,
                       p.type as type,
                       p.status as status,
                       p.created_at as created_at
                from project_task_index p 
                left join users u on u.id = p.author_id
                where p.project_id =:project_id order by p.id desc', [
            'project_id' => $project_id
        ]);
    }

    static function get_tasks($project_id)
    {
        return DB::select('
                select p.id as id,
                       p.project_id as project_id,
                       p.author_id as author_id,
                       concat(u.first_name, " ", u.last_name) as author_name,
                       p.type as type,
                       p.status as status,
                       p.due_date as due_date,
                       p.created_at as created_at
                from project_task_index p 
                left join users u on u.id = p.author_id
                where p.project_id =:project_id order by p.id desc', [
            'project_id' => $project_id
        ]);
    }

    static function get_tasks_npd($project_id)
    {

        $all_task_list = [
//            'concept_development' => 'Concept Development',
            'mm_request' => 'MM Request',
            'npd_planner_request' => 'NPD Planner Request',
            'legal_request' => 'Legal Request',
            'ra_request' => 'RA Request',
            'npd_po_request' => 'NPD PO Request',
            'npd_design_request' => 'NPD NPD Design',
            'pe_request' => 'PE Request',
            'qc_request' => 'QA Request',
            'product_information' => 'Product Information',
        ];

        $rs = DB::select('
                select p.type as type,
                       p.status as status
                from project_task_index p 
                left join users u on u.id = p.author_id
                where p.project_id =:project_id order by p.id desc', [
            'project_id' => $project_id
        ]);

        $rs_array = array();
        foreach ($rs as $row){
            $rs_array[$row->type] = $row->status;
        }

        $return_array = array();
        foreach ($all_task_list as $key => $val){
            if(isset($rs_array[$key])) {
                $return_array[$key] = $rs_array[$key];
            }else{
                $return_array[$key] = 'TBD';
            }
        }

        return $return_array;

    }

    public function active_task_list($p_id)
    {
//        $rs = DB::select('
//                select p.type as type,
//                       p.status as status
//                from project_task_index p
//                left join users u on u.id = p.author_id
//                where p.project_id =:project_id order by p.id desc', [
//            'project_id' => $project_id
//        ]);

        $projectTaskIndex =  new projectTaskIndex();
        $rs = $projectTaskIndex->where('project_id', $p_id)->get();
        return $rs;
    }

    public function get_task_detail($p_id, $t_id, $t_type)
    {
        $table = 'task_type_' . $t_type;
        return DB::select('select * from '
            . $table .
            ' where id =:param_1
            and task_id =:param_2', [
            'param_1' => $p_id,
            'param_2' => $t_id
        ]);
    }

    public function get_ra_detail($t_id)
    {
        return DB::select('select *
                            from sub_ra_request_index
                            where task_id =:task_id', [
            'task_id' => $t_id
        ]);
    }
    public function get_planner_detail($t_id)
    {
        return DB::select('select *
                            from sub_npd_planner_request_index
                            where task_id =:task_id', [
            'task_id' => $t_id
        ]);
    }
    public function get_design_detail($t_id)
    {
        return DB::select('select *
                            from sub_npd_design_request_index
                            where task_id =:task_id', [
                        'task_id' => $t_id
        ]);
    }

    public function get_legal_detail($t_id)
    {
        return DB::select('select *
                            from sub_legal_request_index
                            where task_id =:task_id', [
            'task_id' => $t_id
        ]);
    }

    public function get_mm_detail($t_id)
    {
        return DB::select('select *
                            from sub_mm_request_index
                            where task_id =:task_id', [
            'task_id' => $t_id
        ]);
    }

    public function get_pe_detail($t_id)
    {
        return DB::select('select *
                            from sub_pe_request_index
                            where task_id =:task_id', [
            'task_id' => $t_id
        ]);
    }

    public function deleteByCampaignId($c_id)
    {
        $projectTaskIndex =  new projectTaskIndex();
        $obj = $projectTaskIndex->where('campaign_id', $c_id)->delete();
        return $obj;
    }

    public function get_assets_final_approval_by_campaing_id($c_id)
    {
        $projectTaskIndex =  new projectTaskIndex();
        $obj = $projectTaskIndex->where('campaign_id', $c_id)->get();
        return $obj;
    }

    public function get_complete_assets_list($str, $asset_id, $campaign_id)
    {
        $filter_1 = !empty($str) ? ' and name like "%'.$str.'%" ' : '';
        $filter_2 = !empty($asset_id) ? ' and a_id ='.$asset_id : '';
        $filter_3 = !empty($campaign_id) ? ' and c_id ='.$campaign_id : '';

        return DB::select(
            'select  c_id as campaign_id,
                    a_id as asset_id,
                    a_type as asset_type,
                    cb.campaign_name as brand,
                    due,
                    ci.name as name,
                    cai.status
            from
                    (select id as c_id, asset_id as a_id, type as a_type, email_blast_date as due from  campaign_type_email_blast
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_landing_page
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_misc
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_sms_request
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, date_from as due from campaign_type_search_ad
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, date_from as due from campaign_type_social_ad
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_video_production
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_website_banners
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_website_changes
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_topcategories_copy
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_image_request
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_roll_over
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_store_front
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, date_from as due from campaign_type_programmatic_banners
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_a_content
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_youtube_copy
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_info_graphic) b
            left join campaign_asset_index cai on cai.id = a_id
            left join campaign_item ci on ci.id = c_id
            left join campaign_brands cb on cb.id = ci.campaign_brand
            where cai.status = "copy_complete"
            and ci.status = "active"
            and cai.team_to = "creative"
            ' . $filter_1 . $filter_2 . $filter_3 . '
            order by due asc');
    }

    public function get_kpi_assets_list($str, $asset_id, $campaign_id, $designer, $from, $to)
    {
        $filter_1 = !empty($str) ? ' and name like "%'.$str.'%" ' : '';
        $filter_2 = !empty($asset_id) ? ' and a_id ='.$asset_id : '';
        $filter_3 = !empty($campaign_id) ? ' and c_id ='.$campaign_id : '';
        $filter_4 = !empty($designer) ? ' and cai.assignee = "'.$designer.'" ' : '';
        $filter_5 = !empty($from) ? ' and cai.done_at >= "'.$from.' 00:00:00 " ' : '';
        $filter_6 = !empty($to) ? ' and cai.done_at <= "'.$to.' 23:59:59 " ' : '';

        return DB::select(
            'select  c_id as campaign_id,
                    a_id as asset_id,
                    a_type as asset_type,
                    due,
                    ci.name as name,
                    cai.assignee,
                    cai.assigned_at,
                    cai.target_at,
                    cai.delay,
                    cai.start_at,
                    cai.done_at,
                    cai.status
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
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_website_banners
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_image_request
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_roll_over
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_store_front
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, date_from as due from campaign_type_programmatic_banners
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_a_content
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_info_graphic) b
            left join campaign_asset_index cai on cai.id = a_id
            left join campaign_item ci on ci.id = c_id
            where cai.status in ("final_approval")
            and cai.team_to = "creative"
            and cai.target_at is not null
            and cai.assignee is not null
            ' . $filter_1 . $filter_2 . $filter_3 . $filter_4 . $filter_5 . $filter_6 . '
            order by done_at asc');
    }

    public function get_kpi_content_assets_list($str, $asset_id, $campaign_id, $designer, $from, $to)
    {
        $filter_1 = !empty($str) ? ' and name like "%'.$str.'%" ' : '';
        $filter_2 = !empty($asset_id) ? ' and a_id ='.$asset_id : '';
        $filter_3 = !empty($campaign_id) ? ' and c_id ='.$campaign_id : '';
        $filter_4 = !empty($designer) ? ' and cai.assignee = "'.$designer.'" ' : '';
        $filter_5 = !empty($from) ? ' and cai.done_at >= "'.$from.' 00:00:00 " ' : '';
        $filter_6 = !empty($to) ? ' and cai.done_at <= "'.$to.' 23:59:59 " ' : '';

        return DB::select(
            'select  c_id as campaign_id,
                    a_id as asset_id,
                    a_type as asset_type,
                    due,
                    ci.name as name,
                    cai.assignee,
                    cai.assigned_at,
                    cai.target_at,
                    cai.delay,
                    cai.start_at,
                    cai.done_at,
                    cai.status
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
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_website_banners
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_image_request
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_roll_over
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_store_front
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, date_from as due from campaign_type_programmatic_banners
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_a_content
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_info_graphic) b
            left join campaign_asset_index cai on cai.id = a_id
            left join campaign_item ci on ci.id = c_id
            where cai.status in ("final_approval")
            and cai.team_to = "content"
            and cai.target_at is not null
            and cai.assignee is not null
            ' . $filter_1 . $filter_2 . $filter_3 . $filter_4 . $filter_5 . $filter_6 . '
            order by done_at asc');
    }

    public function get_kpi_web_assets_list($str, $asset_id, $campaign_id, $designer, $from, $to)
    {
        $filter_1 = !empty($str) ? ' and name like "%'.$str.'%" ' : '';
        $filter_2 = !empty($asset_id) ? ' and a_id ='.$asset_id : '';
        $filter_3 = !empty($campaign_id) ? ' and c_id ='.$campaign_id : '';
        $filter_4 = !empty($designer) ? ' and cai.assignee = "'.$designer.'" ' : '';
        $filter_5 = !empty($from) ? ' and cai.done_at >= "'.$from.' 00:00:00 " ' : '';
        $filter_6 = !empty($to) ? ' and cai.done_at <= "'.$to.' 23:59:59 " ' : '';

        return DB::select(
            'select  c_id as campaign_id,
                    a_id as asset_id,
                    a_type as asset_type,
                    due,
                    ci.name as name,
                    cai.assignee,
                    cai.assigned_at,
                    cai.target_at,
                    cai.delay,
                    cai.start_at,
                    cai.done_at,
                    cai.status
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
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_website_banners
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_image_request
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_roll_over
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_store_front
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, date_from as due from campaign_type_programmatic_banners
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_a_content
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_info_graphic) b
            left join campaign_asset_index cai on cai.id = a_id
            left join campaign_item ci on ci.id = c_id
            where cai.status in ("final_approval")
            and cai.team_to = "web production"
            and cai.target_at is not null
            and cai.assignee is not null
            ' . $filter_1 . $filter_2 . $filter_3 . $filter_4 . $filter_5 . $filter_6 . '
            order by done_at asc');
    }

    public function get_kpi_copy_assets_list($str, $asset_id, $campaign_id, $designer, $from, $to)
    {
        $filter_1 = !empty($str) ? ' and name like "%'.$str.'%" ' : '';
        $filter_2 = !empty($asset_id) ? ' and a_id ='.$asset_id : '';
        $filter_3 = !empty($campaign_id) ? ' and c_id ='.$campaign_id : '';
        $filter_4 = !empty($designer) ? ' and cai.copy_writer = "'.$designer.'" ' : '';
        $filter_5 = !empty($from) ? ' and cai.copy_done_at >= "'.$from.' 00:00:00 " ' : '';
        $filter_6 = !empty($to) ? ' and cai.copy_done_at <= "'.$to.' 23:59:59 " ' : '';

        return DB::select(
            'select  c_id as campaign_id,
                    a_id as asset_id,
                    a_type as asset_type,
                    due,
                    ci.name as name,
                    cai.copy_writer,
                    cai.copy_assigned_at,
                    cai.copy_delay,
                    cai.copy_target_at,
                    cai.copy_done_at,
                    cai.status
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
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_website_banners
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_image_request
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_roll_over
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_store_front
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, date_from as due from campaign_type_programmatic_banners
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_a_content
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_info_graphic) b
            left join campaign_asset_index cai on cai.id = a_id
            left join campaign_item ci on ci.id = c_id
            where cai.status in ("final_approval")
            and cai.copy_target_at is not null
            and cai.copy_writer is not null
            ' . $filter_1 . $filter_2 . $filter_3 . $filter_4 . $filter_5 . $filter_6 . '
            order by done_at asc');
    }

    public function get_request_assets_list_copy($str, $asset_id, $campaign_id, $brand_id)
    {
        $filter_1 = !empty($str) ? ' and name like "%'.$str.'%" ' : '';
        $filter_2 = !empty($asset_id) ? ' and a_id ='.$asset_id : '';
        $filter_3 = !empty($campaign_id) ? ' and c_id ='.$campaign_id : '';
        $filter_4 = !empty($brand_id) ? ' and ci.campaign_brand ='.$brand_id : '';

        return DB::select(
            'select  c_id as campaign_id,
                    a_id as asset_id,
                    a_type as asset_type,
                    cb.campaign_name as brand,
                    ci.campaign_brand as brand_id,
                    due,
                    ci.name as name,
                    cai.team_to,
                    cai.status
            from
                    (select id as c_id, asset_id as a_id, type as a_type, email_blast_date as due from  campaign_type_email_blast
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_landing_page
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_misc
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_sms_request
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, date_from as due from campaign_type_search_ad
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, date_from as due from campaign_type_social_ad
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_video_production
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_website_banners
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_website_changes
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_topcategories_copy
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_image_request
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_roll_over
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_store_front
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, date_from as due from campaign_type_programmatic_banners
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_a_content
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_youtube_copy
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_info_graphic) b
            left join campaign_asset_index cai on cai.id = a_id
            left join campaign_item ci on ci.id = c_id
            left join campaign_brands cb on cb.id = ci.campaign_brand
            where cai.status = "copy_requested"
            and ci.status = "active"
            ' . $filter_1 . $filter_2 . $filter_3 . $filter_4 . '
            order by due asc');
    }

    public function get_complete_assets_list_content($str, $asset_id, $campaign_id)
    {
        $filter_1 = !empty($str) ? ' and name like "%'.$str.'%" ' : '';
        $filter_2 = !empty($asset_id) ? ' and a_id ='.$asset_id : '';
        $filter_3 = !empty($campaign_id) ? ' and c_id ='.$campaign_id : '';

        return DB::select(
            'select  c_id as campaign_id,
                    a_id as asset_id,
                    a_type as asset_type,
                    cb.campaign_name as brand,
                    due,
                    ci.name as name,
                    cai.status
            from
                    (select id as c_id, asset_id as a_id, type as a_type, email_blast_date as due from  campaign_type_email_blast
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_landing_page
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_misc
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_sms_request
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, date_from as due from campaign_type_search_ad
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, date_from as due from campaign_type_social_ad
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_video_production
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_website_banners
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_website_changes
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_topcategories_copy
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_image_request
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_roll_over
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_store_front
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, date_from as due from campaign_type_programmatic_banners
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_a_content
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_youtube_copy
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_info_graphic) b
            left join campaign_asset_index cai on cai.id = a_id
            left join campaign_item ci on ci.id = c_id
            left join campaign_brands cb on cb.id = ci.campaign_brand
            where cai.status = "copy_complete"
            and ci.status = "active"
            and cai.team_to = "content"
            ' . $filter_1 . $filter_2 . $filter_3 . '
            order by due asc');
    }

    public function get_complete_assets_list_web($str, $asset_id, $campaign_id)
    {
        $filter_1 = !empty($str) ? ' and name like "%'.$str.'%" ' : '';
        $filter_2 = !empty($asset_id) ? ' and a_id ='.$asset_id : '';
        $filter_3 = !empty($campaign_id) ? ' and c_id ='.$campaign_id : '';

        return DB::select(
            'select  c_id as campaign_id,
                    a_id as asset_id,
                    a_type as asset_type,
                    cb.campaign_name as brand,
                    due,
                    ci.name as name,
                    cai.status
            from
                    (select id as c_id, asset_id as a_id, type as a_type, email_blast_date as due from  campaign_type_email_blast
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_landing_page
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_misc
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_sms_request
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, date_from as due from campaign_type_search_ad
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, date_from as due from campaign_type_social_ad
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_video_production
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_website_banners
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_website_changes
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_topcategories_copy
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_image_request
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_roll_over
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_store_front
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, date_from as due from campaign_type_programmatic_banners
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_a_content
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_youtube_copy
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_info_graphic) b
            left join campaign_asset_index cai on cai.id = a_id
            left join campaign_item ci on ci.id = c_id
            left join campaign_brands cb on cb.id = ci.campaign_brand
            where cai.status = "copy_complete"
            and ci.status = "active"
            and cai.team_to = "web production"
            ' . $filter_1 . $filter_2 . $filter_3 . '
            order by due asc');
    }

    public function get_asset_jira_todo($str)
    {
        return DB::select(
            'select  c_id as campaign_id,
                    a_id as asset_id,
                    a_type as asset_type,
                    due,
                    ci.name as name,
                    cai.status,
                    cai.assignee,
                    cb.campaign_name
            from
                    (select id as c_id, asset_id as a_id, type as a_type, email_blast_date as due from  campaign_type_email_blast
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_landing_page
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_misc
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, date_from as due from campaign_type_search_ad
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, date_from as due from campaign_type_social_ad
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_video_production
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_website_banners
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_website_changes
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_topcategories_copy
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_image_request
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_roll_over
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_store_front
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_a_content
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_info_graphic) b
            left join campaign_asset_index cai on cai.id = a_id
            left join campaign_item ci on ci.id = c_id
            left join campaign_brands cb on cb.id = ci.campaign_brand
            where cai.status = "to_do"
            and cai.assignee like "%'.$str.'%"
            order by due asc');
    }

    public function get_asset_jira_progress($str)
    {
        return DB::select(
            'select  c_id as campaign_id,
                    a_id as asset_id,
                    a_type as asset_type,
                    due,
                    ci.name as name,
                    cai.status,
                    cai.assignee,
                    cb.campaign_name
            from
                    (select id as c_id, asset_id as a_id, type as a_type, email_blast_date as due from  campaign_type_email_blast
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_landing_page
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_misc
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, date_from as due from campaign_type_search_ad
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, date_from as due from campaign_type_social_ad
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_video_production
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_website_banners
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_website_changes
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_topcategories_copy
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_image_request
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_roll_over
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_store_front
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_a_content
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_info_graphic) b
            left join campaign_asset_index cai on cai.id = a_id
            left join campaign_item ci on ci.id = c_id
            left join campaign_brands cb on cb.id = ci.campaign_brand
            where cai.status = "in_progress"
            and cai.assignee like "%'.$str.'%"
            order by due asc');
    }

    public function get_asset_jira_done($str)
    {
        return DB::select(
            'select  c_id as campaign_id,
                    a_id as asset_id,
                    a_type as asset_type,
                    due,
                    ci.name as name,
                    cai.status,
                    cai.assignee,
                    cb.campaign_name
            from
                    (select id as c_id, asset_id as a_id, type as a_type, email_blast_date as due from  campaign_type_email_blast
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_landing_page
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_misc
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, date_from as due from campaign_type_search_ad
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, date_from as due from campaign_type_social_ad
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_video_production
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_website_banners
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_website_changes
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_topcategories_copy
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_image_request
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_roll_over
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_store_front
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_a_content
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_info_graphic) b
            left join campaign_asset_index cai on cai.id = a_id
            left join campaign_item ci on ci.id = c_id
            left join campaign_brands cb on cb.id = ci.campaign_brand
            where cai.status = "done"
            and cai.assignee like "%'.$str.'%"
            order by due asc');
    }

    public function get_asset_jira_finish_creative($str, $brand_id, $asset_id, $team_to = null)
    {

        if($brand_id != '') {
            $brand_filter = ' and ci.campaign_brand =' . $brand_id . ' ';
        }else{
            $brand_filter = ' ';
        }

        if($asset_id != '') {
            $asset_id_filter = ' and cai.id =' . $asset_id . ' ';
        }else{
            $asset_id_filter = ' ';
        }

        if($team_to != null) {
            $team_to_filter = ' and cai.team_to ="' . $team_to . '" ';
        }else{
            $team_to_filter = ' ';
        }

        return DB::select(
            'select  c_id as campaign_id,
                    a_id as asset_id,
                    a_type as asset_type,
                    due,
                    ci.name as name,
                    concat(u.first_name, " ", u.last_name) as author_name,
                    cai.status,
                    cai.assignee,
                    cb.campaign_name,
                    cai.updated_at
            from
                    (select id as c_id, asset_id as a_id, type as a_type, email_blast_date as due from  campaign_type_email_blast
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_landing_page
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_misc
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_sms_request
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, date_from as due from campaign_type_search_ad
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, date_from as due from campaign_type_social_ad
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_video_production
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_website_banners
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_website_changes
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_topcategories_copy
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_image_request
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_roll_over
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_store_front
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, date_from as due from campaign_type_programmatic_banners
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
            where cai.status = "final_approval"
              ' . $brand_filter . '
              ' . $asset_id_filter . '
              ' . $team_to_filter . '
            and cai.assignee like "%'.$str.'%"
            and cai.updated_at >= DATE_ADD(CURDATE(), INTERVAL -7 DAY)
            order by updated_at asc');
    }

    public function get_asset_jira_copy_request($str, $brand_id, $asset_id, $team, $copy_writer = null)
    {

        if($copy_writer != '') {
            $copy_writer_filter = ' and cai.copy_writer ="' . $copy_writer . '" ';
        }else{
            $copy_writer_filter = ' ';
        }

        if($brand_id != '') {
            $brand_filter = ' and ci.campaign_brand =' . $brand_id . ' ';
        }else{
            $brand_filter = ' ';
        }

        if($team != '') {
            $team_filter = ' and team_to ="' . $team . '" ';
        }else{
            $team_filter = ' ';
        }

        if($asset_id != '') {
            $asset_id_filter = ' and cai.id =' . $asset_id . ' ';
        }else{
            $asset_id_filter = ' ';
        }

        return DB::select(
            'select c_id as campaign_id,
                    a_id as asset_id,
                    a_type as asset_type,
                    due,
                    date_created,
                    ci.name as name,
                    concat(u.first_name, " ", u.last_name) as author_name,
                    cai.status,
                    cai.assignee,
                    cai.copy_writer,
                    cb.campaign_name,
                    cai.team_to,
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
                    select id as c_id, asset_id as a_id, type as a_type, date_from as due from campaign_type_search_ad
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, date_from as due from campaign_type_social_ad
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_video_production
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_website_banners
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_website_changes
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_topcategories_copy
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_image_request
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_roll_over
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_store_front
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, date_from as due from campaign_type_programmatic_banners
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
            left join asset_lead_time alt on alt.asset_name = cai.type
            where cai.status = "copy_requested"
                and ci.status = "active"
                and ci.name is not null
                  ' . $brand_filter . '
                  ' . $team_filter . '
                  ' . $asset_id_filter . '
                  ' . $copy_writer_filter . '
                and date_created > "2022-01-01 00:00:00"
                and u.first_name like "%'.$str.'%"
                order by due asc');
    }

    public function get_asset_jira_copy_to_do($str, $brand_id, $asset_id, $team, $copy_writer = null)
    {

        if($copy_writer != '') {
            $copy_writer_filter = ' and cai.copy_writer ="' . $copy_writer . '" ';
        }else{
            $copy_writer_filter = ' ';
        }

        if($brand_id != '') {
            $brand_filter = ' and ci.campaign_brand =' . $brand_id . ' ';
        }else{
            $brand_filter = ' ';
        }

        if($team != '') {
            $team_filter = ' and team_to ="' . $team . '" ';
        }else{
            $team_filter = ' ';
        }

        if($asset_id != '') {
            $asset_id_filter = ' and cai.id =' . $asset_id . ' ';
        }else{
            $asset_id_filter = ' ';
        }

        return DB::select(
            'select c_id as campaign_id,
                    a_id as asset_id,
                    a_type as asset_type,
                    due,
                    date_created,
                    ci.name as name,
                    concat(u.first_name, " ", u.last_name) as author_name,
                    cai.status,
                    cai.assignee,
                    cai.copy_writer,
                    cb.campaign_name,
                    cai.team_to,
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
                    select id as c_id, asset_id as a_id, type as a_type, date_from as due from campaign_type_search_ad
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, date_from as due from campaign_type_social_ad
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_video_production
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_website_banners
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_website_changes
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_topcategories_copy
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_image_request
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_roll_over
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_store_front
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, date_from as due from campaign_type_programmatic_banners
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
            left join asset_lead_time alt on alt.asset_name = cai.type
            where cai.status = "copy_to_do"
                and ci.status = "active"
                and ci.name is not null
                ' . $brand_filter . '
                ' . $team_filter . '
                ' . $asset_id_filter . '
                ' . $copy_writer_filter . '
                and u.first_name like "%'.$str.'%"
                order by due asc');
    }

    public function get_asset_jira_copy_in_progress($str, $brand_id, $asset_id, $team, $copy_writer = null)
    {

        if($copy_writer != '') {
            $copy_writer_filter = ' and cai.copy_writer ="' . $copy_writer . '" ';
        }else{
            $copy_writer_filter = ' ';
        }

        if($brand_id != '') {
            $brand_filter = ' and ci.campaign_brand =' . $brand_id . ' ';
        }else{
            $brand_filter = ' ';
        }

        if($team != '') {
            $team_filter = ' and team_to ="' . $team . '" ';
        }else{
            $team_filter = ' ';
        }

        if($asset_id != '') {
            $asset_id_filter = ' and cai.id =' . $asset_id . ' ';
        }else{
            $asset_id_filter = ' ';
        }

        return DB::select(
            'select c_id as campaign_id,
                    a_id as asset_id,
                    a_type as asset_type,
                    due,
                    date_created,
                    ci.name as name,
                    concat(u.first_name, " ", u.last_name) as author_name,
                    cai.status,
                    cai.assignee,
                    cai.copy_writer,
                    cb.campaign_name,
                    cai.team_to,
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
                    select id as c_id, asset_id as a_id, type as a_type, date_from as due from campaign_type_search_ad
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, date_from as due from campaign_type_social_ad
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_video_production
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_website_banners
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_website_changes
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_topcategories_copy
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_image_request
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_roll_over
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_store_front
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, date_from as due from campaign_type_programmatic_banners
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
            left join asset_lead_time alt on alt.asset_name = cai.type
            where cai.status = "copy_in_progress"
                and ci.status = "active"
                and ci.name is not null
                  ' . $brand_filter . '
                  ' . $team_filter . '
                  ' . $asset_id_filter . '
                  ' . $copy_writer_filter . '
                and u.first_name like "%'.$str.'%"
                order by due asc');
    }

    public function get_asset_jira_copy_request_copywriter($brand_id)
    {
        if($brand_id != '') {
            $brand_filter = ' and ci.campaign_brand =' . $brand_id . ' ';
        }else{
            $brand_filter = ' ';
        }

        return DB::select(
            'select  c_id as campaign_id,
                    a_id as asset_id,
                    a_type as asset_type,
                    due,
                    date_created,
                    ci.name as name,
                    concat(u.first_name, " ", u.last_name) as author_name,
                    cai.status,
                    cai.assignee,
                    cb.campaign_name,
                    cb.id as brand_id
            from
                    (select id as c_id, asset_id as a_id, type as a_type, email_blast_date as due from  campaign_type_email_blast
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_landing_page
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_misc
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, date_from as due from campaign_type_social_ad
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_video_production
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_website_banners
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_topcategories_copy
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, date_from as due from campaign_type_programmatic_banners
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
            where cai.status = "copy_requested"
              ' . $brand_filter . '
            and ci.name is not null
            order by due asc');
    }

    public function get_asset_jira_copy_review($str, $brand_id, $asset_id, $team, $copy_writer = null)
    {
        if($copy_writer != '') {
            $copy_writer_filter = ' and cai.copy_writer ="' . $copy_writer . '" ';
        }else{
            $copy_writer_filter = ' ';
        }

        if($brand_id != '') {
            $brand_filter = ' and ci.campaign_brand =' . $brand_id . ' ';
        }else{
            $brand_filter = ' ';
        }

        if($team != '') {
            $team_filter = ' and team_to ="' . $team . '" ';
        }else{
            $team_filter = ' ';
        }

        if($asset_id != '') {
            $asset_id_filter = ' and cai.id =' . $asset_id . ' ';
        }else{
            $asset_id_filter = ' ';
        }

        return DB::select(
            'select  c_id as campaign_id,
                    a_id as asset_id,
                    a_type as asset_type,
                    due,
                    date_created,
                    ci.name as name,
                    concat(u.first_name, " ", u.last_name) as author_name,
                    cai.status,
                    cai.assignee,
                    cai.copy_writer,
                    cb.campaign_name,
                    cai.team_to,
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
                    select id as c_id, asset_id as a_id, type as a_type, date_from as due from campaign_type_search_ad
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, date_from as due from campaign_type_social_ad
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_video_production
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_website_banners
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_website_changes
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_topcategories_copy
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_image_request
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_roll_over
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_store_front
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, date_from as due from campaign_type_programmatic_banners
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
            left join asset_lead_time alt on alt.asset_name = cai.type
            where cai.status = "copy_review"
                and ci.status = "active"
                and ci.name is not null
                ' . $brand_filter . '
                ' . $team_filter . '
                ' . $asset_id_filter . '
                ' . $copy_writer_filter . '
                and date_created > "2021-01-01 00:00:00"
                and u.first_name like "%'.$str.'%"
                order by due asc');
    }

    public function get_asset_jira_copy_complete($str, $brand_id, $asset_id, $team)
    {
        if($brand_id != '') {
            $brand_filter = ' and ci.campaign_brand =' . $brand_id . ' ';
        }else{
            $brand_filter = ' ';
        }

        if($team != '') {
            $team_filter = ' and team_to ="' . $team . '" ';
        }else{
            $team_filter = ' ';
        }

        if($asset_id != '') {
            $asset_id_filter = ' and cai.id =' . $asset_id . ' ';
        }else{
            $asset_id_filter = ' ';
        }

        return DB::select(
            'select  c_id as campaign_id,
                    a_id as asset_id,
                    a_type as asset_type,
                    due,
                    date_created,
                    ci.name as name,
                    concat(u.first_name, " ", u.last_name) as author_name,
                    cai.status,
                    cai.assignee,
                    cb.campaign_name,
                    cai.team_to,
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
                    select id as c_id, asset_id as a_id, type as a_type, date_from as due from campaign_type_social_ad
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_website_banners
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_landing_page
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_misc
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_sms_request
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, date_from as due from campaign_type_programmatic_banners
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
            left join asset_lead_time alt on alt.asset_name = cai.type
            where cai.status = "copy_complete"
                and ci.status = "active"
                and ci.name is not null
                ' . $brand_filter . '
                ' . $team_filter . '
                ' . $asset_id_filter . '
                and date_created > "2022-03-01 00:00:00"
                and u.first_name like "%'.$str.'%"
                order by due asc');
    }

    public function get_asset_jira_to_do($str, $brand_id, $asset_id, $team)
    {
        if($brand_id != '') {
            $brand_filter = ' and ci.campaign_brand =' . $brand_id . ' ';
        }else{
            $brand_filter = ' ';
        }

        if($team != '') {
            $team_filter = ' and team_to ="' . $team . '" ';
        }else{
            $team_filter = ' ';
        }

        if($asset_id != '') {
            $asset_id_filter = ' and cai.id =' . $asset_id . ' ';
        }else{
            $asset_id_filter = ' ';
        }

        return DB::select(
            'select  c_id as campaign_id,
                    a_id as asset_id,
                    a_type as asset_type,
                    due,
                    date_created,
                    ci.name as name,
                    concat(u.first_name, " ", u.last_name) as author_name,
                    cai.status,
                    cai.assignee,
                    cb.campaign_name,
                    cai.team_to,
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
                    select id as c_id, asset_id as a_id, type as a_type, date_from as due from campaign_type_social_ad
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_website_banners
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_landing_page
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_misc
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_sms_request
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, date_from as due from campaign_type_programmatic_banners
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
            left join asset_lead_time alt on alt.asset_name = cai.type
            where cai.status in ("to_do")
                and ci.status = "active"
                and ci.name is not null
                ' . $brand_filter . '
                ' . $team_filter . '
                ' . $asset_id_filter . '
                and u.first_name like "%'.$str.'%"
                order by due asc');
    }

    public function get_asset_jira_to_do_creative($str, $brand_id, $asset_id, $team_to = null)
    {
        if($brand_id != '') {
            $brand_filter = ' and ci.campaign_brand =' . $brand_id . ' ';
        }else{
            $brand_filter = ' ';
        }

        if($asset_id != '') {
            $asset_id_filter = ' and cai.id =' . $asset_id . ' ';
        }else{
            $asset_id_filter = ' ';
        }

        if($team_to != null) {
            $team_to_filter = ' and cai.team_to ="' . $team_to . '" ';
        }else{
            $team_to_filter = ' ';
        }

        return DB::select(
            'select  c_id as campaign_id,
                    a_id as asset_id,
                    a_type as asset_type,
                    due,
                    date_created,
                    ci.name as name,
                    concat(u.first_name, " ", u.last_name) as author_name,
                    cai.status,
                    cai.assignee,
                    cb.campaign_name,
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
                    select id as c_id, asset_id as a_id, type as a_type, date_from as due from campaign_type_search_ad
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, date_from as due from campaign_type_social_ad
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_video_production
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_website_banners
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_website_changes
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_topcategories_copy
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_image_request
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_roll_over
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_store_front
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, date_from as due from campaign_type_programmatic_banners
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
            left join asset_lead_time alt on alt.asset_name = cai.type
            where cai.status in ("to_do")
            and ci.name is not null
              ' . $brand_filter . '
              ' . $asset_id_filter . '
              ' . $team_to_filter . '
            and cai.assignee like "%'.$str.'%"
            order by due asc');
    }

    public function get_asset_jira_in_progress($str, $brand_id, $asset_id, $team)
    {
        if($brand_id != '') {
            $brand_filter = ' and ci.campaign_brand =' . $brand_id . ' ';
        }else{
            $brand_filter = ' ';
        }

        if($team != '') {
            $team_filter = ' and team_to ="' . $team . '" ';
        }else{
            $team_filter = ' ';
        }

        if($asset_id != '') {
            $asset_id_filter = ' and cai.id =' . $asset_id . ' ';
        }else{
            $asset_id_filter = ' ';
        }

        return DB::select(
            'select  c_id as campaign_id,
                    a_id as asset_id,
                    a_type as asset_type,
                    due,
                    date_created,
                    ci.name as name,
                    concat(u.first_name, " ", u.last_name) as author_name,
                    cai.status,
                    cai.assignee,
                    cb.campaign_name,
                    cai.team_to,
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
                    select id as c_id, asset_id as a_id, type as a_type, date_from as due from campaign_type_social_ad
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_website_banners
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_landing_page
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_misc
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_sms_request
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, date_from as due from campaign_type_programmatic_banners
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
            left join asset_lead_time alt on alt.asset_name = cai.type
            where cai.status in ("in_progress")
                and ci.status = "active"
                and ci.name is not null
                  ' . $brand_filter . '
                  ' . $team_filter . '
                  ' . $asset_id_filter . '
                and u.first_name like "%'.$str.'%"
                order by due asc');
    }

    public function get_asset_jira_in_progress_creative($str, $brand_id, $asset_id, $team_to = null)
    {
        if($brand_id != '') {
            $brand_filter = ' and ci.campaign_brand =' . $brand_id . ' ';
        }else{
            $brand_filter = ' ';
        }

        if($asset_id != '') {
            $asset_id_filter = ' and cai.id =' . $asset_id . ' ';
        }else{
            $asset_id_filter = ' ';
        }

        if($team_to != null) {
            $team_to_filter = ' and cai.team_to ="' . $team_to . '" ';
        }else{
            $team_to_filter = ' ';
        }

        return DB::select(
            'select  c_id as campaign_id,
                    a_id as asset_id,
                    a_type as asset_type,
                    due,
                    date_created,
                    ci.name as name,
                    concat(u.first_name, " ", u.last_name) as author_name,
                    cai.status,
                    cai.assignee,
                    cb.campaign_name,
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
                    select id as c_id, asset_id as a_id, type as a_type, date_from as due from campaign_type_search_ad
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, date_from as due from campaign_type_social_ad
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_video_production
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_website_banners
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_website_changes
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_topcategories_copy
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_image_request
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_roll_over
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_store_front
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, date_from as due from campaign_type_programmatic_banners
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
            left join asset_lead_time alt on alt.asset_name = cai.type
            where cai.status in ("in_progress")
            and ci.name is not null
              ' . $brand_filter . '
              ' . $asset_id_filter . '
              ' . $team_to_filter . '
            and cai.assignee like "%'.$str.'%"
            order by due asc');
    }

    public function get_asset_jira_waiting_final_approval($str, $brand_id, $asset_id, $team)
    {
        if($brand_id != '') {
            $brand_filter = ' and ci.campaign_brand =' . $brand_id . ' ';
        }else{
            $brand_filter = ' ';
        }

        if($team != '') {
            $team_filter = ' and team_to ="' . $team . '" ';
        }else{
            $team_filter = ' ';
        }

        if($asset_id != '') {
            $asset_id_filter = ' and cai.id =' . $asset_id . ' ';
        }else{
            $asset_id_filter = ' ';
        }

        return DB::select(
            'select  c_id as campaign_id,
                    a_id as asset_id,
                    a_type as asset_type,
                    due,
                    date_created,
                    ci.name as name,
                    concat(u.first_name, " ", u.last_name) as author_name,
                    cai.status,
                    cai.assignee,
                    cb.campaign_name,
                    cai.team_to,
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
                    select id as c_id, asset_id as a_id, type as a_type, date_from as due from campaign_type_social_ad
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_website_banners
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_landing_page
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_misc
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_sms_request
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_topcategories_copy
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, date_from as due from campaign_type_programmatic_banners
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
            left join asset_lead_time alt on alt.asset_name = cai.type
            where cai.status = "done"
                and ci.status = "active"
                and ci.name is not null
                ' . $brand_filter . '
                ' . $team_filter . '
                ' . $asset_id_filter . '
                and u.first_name like "%'.$str.'%"
                order by due asc');
    }

    public function get_asset_jira_waiting_final_approval_creative($str, $brand_id, $asset_id, $team_to = null)
    {
        if($brand_id != '') {
            $brand_filter = ' and ci.campaign_brand =' . $brand_id . ' ';
        }else{
            $brand_filter = ' ';
        }

        if($asset_id != '') {
            $asset_id_filter = ' and cai.id =' . $asset_id . ' ';
        }else{
            $asset_id_filter = ' ';
        }

        if($team_to != null) {
            $team_to_filter = ' and cai.team_to ="' . $team_to . '" ';
        }else{
            $team_to_filter = ' ';
        }

        return DB::select(
            'select  c_id as campaign_id,
                    a_id as asset_id,
                    a_type as asset_type,
                    due,
                    date_created,
                    ci.name as name,
                    concat(u.first_name, " ", u.last_name) as author_name,
                    cai.status,
                    cai.assignee,
                    cb.campaign_name,
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
                    select id as c_id, asset_id as a_id, type as a_type, date_from as due from campaign_type_search_ad
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, date_from as due from campaign_type_social_ad
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_video_production
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_website_banners
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_website_changes
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_topcategories_copy
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_image_request
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_roll_over
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_store_front
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, date_from as due from campaign_type_programmatic_banners
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
            left join asset_lead_time alt on alt.asset_name = cai.type
            where cai.status = "done"
            and ci.name is not null
              ' . $brand_filter . '
              ' . $asset_id_filter . '
              ' . $team_to_filter . '
            and cai.assignee like "%'.$str.'%"
            order by due asc');
    }

    public function get_asset_jira_asset_completed($str, $brand_id, $asset_id, $team)
    {
        if($brand_id != '') {
            $brand_filter = ' and ci.campaign_brand =' . $brand_id . ' ';
        }else{
            $brand_filter = ' ';
        }

        if($team != '') {
            $team_filter = ' and team_to ="' . $team . '" ';
        }else{
            $team_filter = ' ';
        }

        if($asset_id != '') {
            $asset_id_filter = ' and cai.id =' . $asset_id . ' ';
        }else{
            $asset_id_filter = ' ';
        }

        return DB::select(
            'select c_id as campaign_id,
                    a_id as asset_id,
                    a_type as asset_type,
                    due,
                    ci.name as name,
                    cai.status,
                    cai.assignee,
                    cai.author_id,
                    concat(u.first_name, " ", u.last_name) as author_name,
                    cb.campaign_name,
                    cai.updated_at,
                    cai.team_to,
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
                    select id as c_id, asset_id as a_id, type as a_type, date_from as due from campaign_type_search_ad
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, date_from as due from campaign_type_social_ad
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_video_production
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_website_banners
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_website_changes
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_topcategories_copy
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
            left join asset_lead_time alt on alt.asset_name = cai.type
            where cai.status = "final_approval"
                and ci.status = "active"
                    ' . $brand_filter . '
                    ' . $team_filter . '
                    ' . $asset_id_filter . '
                and u.first_name like "%'.$str.'%"
                and cai.updated_at >= DATE_ADD(CURDATE(), INTERVAL -14 DAY)
                order by updated_at asc');
    }

    public function get_target_date($a_id, $asset_type)
    {
        $res = DB::select(
            'select c_id as campaign_id,
                    a_id as asset_id,
                    a_type as asset_type,
                    due,
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
                    select id as c_id, asset_id as a_id, type as a_type, date_from as due from campaign_type_social_ad
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_website_banners
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_landing_page
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_misc
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_sms_request
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, date_from as due from campaign_type_programmatic_banners
                    union all
					select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_image_request
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_roll_over
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_store_front
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_a_content
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_info_graphic) b
            left join campaign_asset_index cai on cai.id = a_id
            left join campaign_item ci on ci.id = c_id
            left join asset_lead_time alt on alt.asset_name = cai.type
            where cai.id ='.$a_id);

        $due = $res[0]->due;

        $time_to_spare = ($res[0]->time_to_spare == 'N/A') ? 0 : $res[0]->time_to_spare;
        $kdo = ($res[0]->kdo == 'N/A') ? 0 : $res[0]->kdo;
        $development = ($res[0]->development == 'N/A') ? 0 : $res[0]->development;
        $final_review = ($res[0]->final_review == 'N/A') ? 0 : $res[0]->final_review;
        $creative_work = ($res[0]->creative_work == 'N/A') ? 0 : $res[0]->creative_work;
        $creator_assign = ($res[0]->creator_assign == 'N/A') ? 0 : $res[0]->creator_assign;
        $copy_review = ($res[0]->copy_review == 'N/A') ? 0 : $res[0]->copy_review;
        $copy = ($res[0]->copy == 'N/A') ? 0 : $res[0]->copy;
        $copywriter_assign = ($res[0]->copywriter_assign == 'N/A') ? 0 : $res[0]->copywriter_assign;

        $step_8 = $time_to_spare + $kdo;        // e-commerce start
        $step_7 = $step_8 + $development;       // development start
        $step_6 = $step_7 + $final_review;      // creative review start
        $step_5 = $step_6 + $creative_work;     // creative work start
        $step_4 = $step_5 + $creator_assign;    // creator assign start
        $step_3 = $step_4 + $copy_review;       // copy review start
        $step_2 = $step_3 + $copy;              // copy start
        $step_1 = $step_2 + $copywriter_assign; // copywriter assign start

        // Get Target_At
        $target_at = date('Y-m-d 19:00:00', strtotime($due . ' -' . $step_6 . ' weekday'));

//        if ($asset_type == 'email_blast') {
//            $target_at = date('Y-m-d 19:00:00', strtotime($due . ' -' . $step_6 . ' weekday'));
//        } else if ($asset_type == 'social_ad') {
//            $target_at = date('Y-m-d 19:00:00', strtotime($due . '-10 weekday'));
//        } else if ($asset_type == 'website_banners') {
//            $target_at = date('Y-m-d 19:00:00', strtotime($due . '-11 weekday'));
//        } else if ($asset_type == 'landing_page') {
//            $target_at = date('Y-m-d 19:00:00', strtotime($due . '-18 weekday'));
//        } else if ($asset_type == 'misc') {
//            $target_at = date('Y-m-d 19:00:00', strtotime($due . '-9 weekday'));
//        } else if ($asset_type == 'sms_request') {
//            $target_at = date('Y-m-d 19:00:00', strtotime($due . '-9 weekday'));
//        } else if ($asset_type == 'programmatic_banners') {
//            $target_at = date('Y-m-d 19:00:00', strtotime($due . '-10 weekday'));
//        } else if ($asset_type == 'image_request') {
//            $target_at = date('Y-m-d 19:00:00', strtotime($due . '-2 weekday'));
//        } else if ($asset_type == 'roll_over') {
//            $target_at = date('Y-m-d 19:00:00', strtotime($due . '-3 weekday'));
//        } else if ($asset_type == 'store_front') {
//            $target_at = date('Y-m-d 19:00:00', strtotime($due . '-13 weekday'));
//        } else if ($asset_type == 'a_content') {
//            $target_at = date('Y-m-d 19:00:00', strtotime($due . '-13 weekday'));
//        }

        // Check for Assign is late or not.. // To Do
        $today = date('Y-m-d');
        $assign_due = date('Y-m-d', strtotime($due . ' -' . $step_5 . ' weekday'));

//        if ($asset_type == 'email_blast') {
//            $assign_due = date('Y-m-d', strtotime($due . ' -' . $step_5 . ' weekday'));
//        } else if ($asset_type == 'social_ad') {
//            $assign_due = date('Y-m-d ', strtotime($due . '-20 weekday'));
//        } else if ($asset_type == 'website_banners') {
//            $assign_due = date('Y-m-d', strtotime($due . '-21 weekday'));
//        } else if ($asset_type == 'landing_page') {
//            $assign_due = date('Y-m-d', strtotime($due . '-38 weekday'));
//        } else if ($asset_type == 'misc') {
//            $assign_due = date('Y-m-d', strtotime($due . '-19 weekday'));
//        } else if ($asset_type == 'sms_request') {
//            $assign_due = date('Y-m-d', strtotime($due . '-19 weekday'));
//        } else if ($asset_type == 'programmatic_banners') {
//            $assign_due = date('Y-m-d', strtotime($due . '-20 weekday'));
//        } else if ($asset_type == 'image_request') {
//            $assign_due = date('Y-m-d', strtotime($due . '-12 weekday'));
//        } else if ($asset_type == 'roll_over') {
//            $assign_due = date('Y-m-d', strtotime($due . '-13 weekday'));
//        } else if ($asset_type == 'store_front') {
//            $assign_due = date('Y-m-d', strtotime($due . '-33 weekday'));
//        } else if ($asset_type == 'a_content') {
//            $assign_due = date('Y-m-d', strtotime($due . '-33 weekday'));
//        }

        $delay = 0;

        if($today >= $assign_due){
            $from = Carbon::parse($today);
            $to = Carbon::parse($assign_due);
            $delay = $to->diffInWeekdays($from);
        }

        return [$target_at, $delay];
    }

    public function get_copy_target_date($a_id, $asset_type)
    {
        $res = DB::select(
            'select c_id as campaign_id,
                    a_id as asset_id,
                    a_type as asset_type,
                    due,
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
                    select id as c_id, asset_id as a_id, type as a_type, date_from as due from campaign_type_social_ad
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_website_banners
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_landing_page
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_misc
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_sms_request
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_topcategories_copy
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, date_from as due from campaign_type_programmatic_banners
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_a_content
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_youtube_copy
                    union all
                    select id as c_id, asset_id as a_id, type as a_type, launch_date as due from campaign_type_info_graphic) b
            left join campaign_asset_index cai on cai.id = a_id
            left join campaign_item ci on ci.id = c_id
            left join asset_lead_time alt on alt.asset_name = cai.type
            where cai.id ='.$a_id);

        $due = $res[0]->due;

        $time_to_spare = ($res[0]->time_to_spare == 'N/A') ? 0 : $res[0]->time_to_spare;
        $kdo = ($res[0]->kdo == 'N/A') ? 0 : $res[0]->kdo;
        $development = ($res[0]->development == 'N/A') ? 0 : $res[0]->development;
        $final_review = ($res[0]->final_review == 'N/A') ? 0 : $res[0]->final_review;
        $creative_work = ($res[0]->creative_work == 'N/A') ? 0 : $res[0]->creative_work;
        $creator_assign = ($res[0]->creator_assign == 'N/A') ? 0 : $res[0]->creator_assign;
        $copy_review = ($res[0]->copy_review == 'N/A') ? 0 : $res[0]->copy_review;
        $copy = ($res[0]->copy == 'N/A') ? 0 : $res[0]->copy;
        $copywriter_assign = ($res[0]->copywriter_assign == 'N/A') ? 0 : $res[0]->copywriter_assign;

        $step_8 = $time_to_spare + $kdo;        // e-commerce start
        $step_7 = $step_8 + $development;       // development start
        $step_6 = $step_7 + $final_review;      // creative review start
        $step_5 = $step_6 + $creative_work;     // creative work start
        $step_4 = $step_5 + $creator_assign;    // creator assign start
        $step_3 = $step_4 + $copy_review;       // copy review start
        $step_2 = $step_3 + $copy;              // copy start
        $step_1 = $step_2 + $copywriter_assign; // copywriter assign start

        $target_at = date('Y-m-d 19:00:00', strtotime($due . ' -' . $step_3 . ' weekday'));

        // Get Target_At
//        if ($asset_type == 'email_blast') {
//            $target_at = date('Y-m-d 19:00:00', strtotime($due . '-24 weekday'));
//        } else if ($asset_type == 'social_ad') {
//            $target_at = date('Y-m-d 19:00:00', strtotime($due . '-24 weekday'));
//        } else if ($asset_type == 'website_banners') {
//            $target_at = date('Y-m-d 19:00:00', strtotime($due . '-25 weekday'));
//        } else if ($asset_type == 'landing_page') {
//            $target_at = date('Y-m-d 19:00:00', strtotime($due . '-43 weekday'));
//        } else if ($asset_type == 'misc') {
//            $target_at = date('Y-m-d 19:00:00', strtotime($due . '-23 weekday'));
//        } else if ($asset_type == 'sms_request') {
//            $target_at = date('Y-m-d 19:00:00', strtotime($due . '-23 weekday'));
//        } else if ($asset_type == 'topcategories_copy') {
//            $target_at = date('Y-m-d 19:00:00', strtotime($due . '-3 weekday'));
//        } else if ($asset_type == 'programmatic_banners') {
//            $target_at = date('Y-m-d 19:00:00', strtotime($due . '-24 weekday'));
//        } else if ($asset_type == 'a_content') {
//            $target_at = date('Y-m-d 19:00:00', strtotime($due . '-37 weekday'));
//        } else if ($asset_type == 'youtube_copy') {
//            $target_at = date('Y-m-d 19:00:00', strtotime($due . '-10 weekday'));
//        }

        // Check for Copy Assign is late or not.. // Copy To Do
        $today = date('Y-m-d');
        $assign_due = date('Y-m-d', strtotime($due . ' -' . $step_2 . ' weekday'));

//        if ($asset_type == 'email_blast') {
//            $assign_due = date('Y-m-d', strtotime($due . '-26 weekday'));
//        } else if ($asset_type == 'social_ad') {
//            $assign_due = date('Y-m-d ', strtotime($due . '-26 weekday'));
//        } else if ($asset_type == 'website_banners') {
//            $assign_due = date('Y-m-d', strtotime($due . '-27 weekday'));
//        } else if ($asset_type == 'landing_page') {
//            $assign_due = date('Y-m-d', strtotime($due . '-47 weekday'));
//        } else if ($asset_type == 'misc') {
//            $assign_due = date('Y-m-d', strtotime($due . '-25 weekday'));
//        } else if ($asset_type == 'sms_request') {
//            $assign_due = date('Y-m-d', strtotime($due . '-25 weekday'));
//        } else if ($asset_type == 'topcategories_copy') {
//            $assign_due = date('Y-m-d', strtotime($due . '-5 weekday'));
//        } else if ($asset_type == 'programmatic_banners') {
//            $assign_due = date('Y-m-d', strtotime($due . '-26 weekday'));
//        } else if ($asset_type == 'a_content') {
//            $assign_due = date('Y-m-d', strtotime($due . '-39 weekday'));
//        } else if ($asset_type == 'youtube_copy') {
//            $assign_due = date('Y-m-d', strtotime($due . '-12 weekday'));
//        }

        $delay = 0;

        if($today >= $assign_due){
            $from = Carbon::parse($today);
            $to = Carbon::parse($assign_due);
            $delay = $to->diffInWeekdays($from);
        }

        return [$target_at, $delay];
    }
}
