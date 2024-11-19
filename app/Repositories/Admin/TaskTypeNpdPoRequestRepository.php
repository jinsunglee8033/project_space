<?php

namespace App\Repositories\Admin;

use App\Models\ProjectTaskIndex;
use App\Models\TaskTypeNpdPlannerRequest;
use App\Models\TaskTypeNpdPoRequest;
use App\Repositories\Admin\Interfaces\TaskTypeMmRequestRepositoryInterface;
use App\Repositories\Admin\Interfaces\TaskTypeNpdPoRequestRepositoryInterface;
use DB;

use App\Models\TaskTypeMmRequest;
use Illuminate\Database\Eloquent\Model;

class TaskTypeNpdPoRequestRepository implements TaskTypeNpdPoRequestRepositoryInterface
{
    public function findAll($options = [])
    {
        $perPage = $options['per_page'] ?? null;
        $orderByFields = $options['order'] ?? [];
        $id = $options['id'] ?? [];

        $taskTypeNpdPoRequest = new TaskTypeNpdPoRequest();

        if ($id) {
            $taskTypeNpdPoRequest = $taskTypeNpdPoRequest
                ->where('id', $id)->where('task_id', 0);
        }

        $taskTypeNpdPoRequest = $taskTypeNpdPoRequest->get();

        return $taskTypeNpdPoRequest;
    }

    public function findAllByTaskId($task_id)
    {
        $taskTypeNpdPoRequest = new TaskTypeNpdPoRequest();
        return $taskTypeNpdPoRequest->where('task_id', $task_id)->get();
    }

    public function deleteByTaskId($task_id)
    {
        $taskTypeNpdPoRequest = new TaskTypeNpdPoRequest();
        return $taskTypeNpdPoRequest->where('task_id', $task_id)->delete();
    }

    public function findById($id)
    {
        return TaskTypeNpdPoRequest::findOrFail($id);
    }

    public function findByTaskId($task_id)
    {
        $taskTypeNpdPoRequest = new TaskTypeNpdPoRequest();
        return $taskTypeNpdPoRequest->where('task_id', $task_id)->first();
    }

    public function create($params = [])
    {
        return DB::transaction(function () use ($params) {
            $taskTypeNpdPoRequest = TaskTypeNpdPoRequest::create($params);
            $this->syncRolesAndPermissions($params, $taskTypeNpdPoRequest);

            return $taskTypeNpdPoRequest;
        });
    }

    public function update($id, $params = [])
    {
        $taskTypeNpdPoRequest = TaskTypeNpdPoRequest::findOrFail($id);

        return DB::transaction(function () use ($params, $taskTypeNpdPoRequest) {
            $taskTypeNpdPoRequest->update($params);

            return $taskTypeNpdPoRequest;
        });
    }

    public function delete($id)
    {
        $taskTypeNpdPoRequest  = TaskTypeNpdPoRequest::findOrFail($id);

        return $taskTypeNpdPoRequest->delete();
    }

    public function get_task_id_for_npd_po($project_id)
    {
        $pti_obj = ProjectTaskIndex::where('project_id', $project_id)
            ->where('type', 'npd_po_request')->first();
        return $pti_obj;
    }

    public function get_action_requested_list($cur_user, $buyer, $team, $brand)
    {
        if($cur_user != '') {
            $cur_user_filter = $cur_user;
        }else{
            $cur_user_filter = ' ';
        }
        if($buyer != '') {
            $buyer_filter = ' and t.buyer ="' . $buyer . '" ';
        }else{
            $buyer_filter = ' ';
        }
        if($team != '') {
            $team_filter = ' and p.team ="' . $team . '" ';
        }else{
            $team_filter = ' ';
        }
        if($brand != '') {
            $brand_filter = ' and p.brand ="' . $brand . '" ';
        }else{
            $brand_filter = ' ';
        }
        return DB::select(
            '
            select p.team as team,
                    p.brand as brand,
                    p.name as name,
                    t.id as project_id,
                    i.author_id as author_id,
                    i.status as status,
                    i.created_at as created_at,
                    t.id as project_id,
                    t.task_id as task_id,
                    t.materials as materials,
                    t.priority as priority,
                    t.type as type,
                    t.due_date as due_date,
                    t.due_date_urgent as due_date_urgent,
                    t.due_date_revision as due_date_revision,
                    t.revision_cnt as revision_cnt,   
                    t.vendor_code as vendor_code,
                    t.price_set_up as price_set_up,
                    concat(uu.first_name, " ", uu.last_name) as buyer_name,
                    concat(u.first_name, " ", u.last_name) as author_name
                from project_task_index i
            left join project p on p.id = i.project_id
            left join task_type_npd_po_request t on t.task_id = i.id
            left join users u on u.id = t.author_id
            left join users uu on uu.id = t.buyer
            where i.type = "npd_po_request"
                and p.status = "active"
                ' . $cur_user_filter . '
              ' . $buyer_filter . '
              ' . $team_filter . '
              ' . $brand_filter . '
                and	i.status = "action_requested"
                order by t.due_date asc
            ');
    }

    public function get_in_progress_list($cur_user, $buyer, $team, $brand)
    {
        if($cur_user != '') {
            $cur_user_filter = $cur_user;
        }else{
            $cur_user_filter = ' ';
        }
        if($buyer != '') {
            $buyer_filter = ' and t.buyer ="' . $buyer . '" ';
        }else{
            $buyer_filter = ' ';
        }
        if($team != '') {
            $team_filter = ' and p.team ="' . $team . '" ';
        }else{
            $team_filter = ' ';
        }
        if($brand != '') {
            $brand_filter = ' and p.brand ="' . $brand . '" ';
        }else{
            $brand_filter = ' ';
        }
        return DB::select(
            '
            select p.team as team,
                    p.brand as brand,
                    p.name as name,
                    t.id as project_id,
                    i.author_id as author_id,
                    i.status as status,
                    i.created_at as created_at,
                    t.id as project_id,
                    t.task_id as task_id,
                    t.materials as materials,
                    t.priority as priority,
                    t.type as type,
                    t.due_date as due_date,
                    t.due_date_urgent as due_date_urgent,
                    t.due_date_revision as due_date_revision,
                    t.revision_cnt as revision_cnt,   
                    t.vendor_code as vendor_code,
                   t.price_set_up as price_set_up,
                    concat(uu.first_name, " ", uu.last_name) as buyer_name,
                    concat(u.first_name, " ", u.last_name) as author_name
                from project_task_index i
            left join project p on p.id = i.project_id
            left join task_type_npd_po_request t on t.task_id = i.id
            left join users u on u.id = t.author_id
            left join users uu on uu.id = t.buyer
            where i.type = "npd_po_request"
                and p.status = "active"
                ' . $cur_user_filter . '
              ' . $buyer_filter . '
              ' . $team_filter . '
              ' . $brand_filter . '
                and	i.status = "in_progress"
                order by t.due_date asc
            ');
    }

    public function get_action_review_list($cur_user, $buyer, $team, $brand)
    {
        if($cur_user != '') {
            $cur_user_filter = $cur_user;
        }else{
            $cur_user_filter = ' ';
        }
        if($buyer != '') {
            $buyer_filter = ' and t.buyer ="' . $buyer . '" ';
        }else{
            $buyer_filter = ' ';
        }
        if($team != '') {
            $team_filter = ' and p.team ="' . $team . '" ';
        }else{
            $team_filter = ' ';
        }
        if($brand != '') {
            $brand_filter = ' and p.brand ="' . $brand . '" ';
        }else{
            $brand_filter = ' ';
        }
        return DB::select(
            '
            select p.team as team,
                    p.brand as brand,
                    p.name as name,
                    t.id as project_id,
                    i.author_id as author_id,
                    i.status as status,
                    i.created_at as created_at,
                    t.id as project_id,
                    t.task_id as task_id,
                    t.materials as materials,
                    t.priority as priority,
                    t.type as type,
                    t.due_date as due_date,
                    t.due_date_urgent as due_date_urgent,
                    t.due_date_revision as due_date_revision,
                    t.revision_cnt as revision_cnt,   
                    t.vendor_code as vendor_code,
                   t.price_set_up as price_set_up,
                    concat(uu.first_name, " ", uu.last_name) as buyer_name,
                    concat(u.first_name, " ", u.last_name) as author_name
                from project_task_index i
            left join project p on p.id = i.project_id
            left join task_type_npd_po_request t on t.task_id = i.id
            left join users u on u.id = t.author_id
            left join users uu on uu.id = t.buyer
            where i.type = "npd_po_request"
                and p.status = "active"
                ' . $cur_user_filter . '
              ' . $buyer_filter . '
              ' . $team_filter . '
              ' . $brand_filter . '
                and	i.status = "action_review"
                order by t.due_date asc
            ');
    }

    public function get_action_completed_list($cur_user, $buyer, $team, $brand)
    {
        if($cur_user != '') {
            $cur_user_filter = $cur_user;
        }else{
            $cur_user_filter = ' ';
        }
        if($buyer != '') {
            $buyer_filter = ' and t.buyer ="' . $buyer . '" ';
        }else{
            $buyer_filter = ' ';
        }
        if($team != '') {
            $team_filter = ' and p.team ="' . $team . '" ';
        }else{
            $team_filter = ' ';
        }
        if($brand != '') {
            $brand_filter = ' and p.brand ="' . $brand . '" ';
        }else{
            $brand_filter = ' ';
        }
        return DB::select(
            '
            select p.team as team,
                    p.brand as brand,
                    p.name as name,
                    t.id as project_id,
                    i.author_id as author_id,
                    i.status as status,
                    i.created_at as created_at,
                    t.id as project_id,
                    t.task_id as task_id,
                    t.materials as materials,
                    t.priority as priority,
                    t.type as type,
                    t.due_date as due_date,
                    t.due_date_urgent as due_date_urgent,
                    t.due_date_revision as due_date_revision,
                    t.revision_cnt as revision_cnt,   
                    t.vendor_code as vendor_code,
                   t.price_set_up as price_set_up,
                    t.po as po,
                    concat(uu.first_name, " ", uu.last_name) as buyer_name,
                    concat(u.first_name, " ", u.last_name) as author_name
                from project_task_index i
            left join project p on p.id = i.project_id
            left join task_type_npd_po_request t on t.task_id = i.id
            left join users u on u.id = t.author_id
            left join users uu on uu.id = t.buyer
            where i.type = "npd_po_request"
                and p.status = "active"
                ' . $cur_user_filter . '
              ' . $buyer_filter . '
              ' . $team_filter . '
              ' . $brand_filter . '
                and	i.status = "action_completed"
                order by t.due_date asc
            ');
    }

    public function get_task_list($cur_user, $str, $buyer, $team, $status)
    {
        if($cur_user != '') {
            $cur_user_filter = $cur_user;
        }else{
            $cur_user_filter = ' ';
        }
        if($str != '') {
            $material_filter = ' and t.materials like "%' . $str . '%" ';
        }else{
            $material_filter = ' ';
        }
        if($buyer != '') {
            $buyer_filter = ' and t.buyer ="' . $buyer . '" ';
        }else{
            $buyer_filter = ' ';
        }
        if($team != '') {
            $team_filter = ' and p.team ="' . $team . '" ';
        }else{
            $team_filter = ' ';
        }
        if($status != '') {
            $status_filter = ' and i.status ="' . $status . '" ';
        }else{
            $status_filter = ' ';
        }

        return DB::select(
            '
            select t.id as project_id,
                t.task_id as task_id,
                t.type as request_type,
                t.priority as priority,
                t.due_date as due_date,
                t.due_date_urgent as due_date_urgent,
                t.due_date_revision as due_date_revision,
                t.revision_cnt as revision_cnt,   
                t.request_detail as request_detail,
                t.source_list_completion as source_list_completion,
                t.info_record_completion as info_record_completion,
                t.price_set_up as price_set_up,
                t.forecast_completion as forecast_completion,
                t.materials as materials,
                t.total_sku_count as total_sku_count,
                t.set_up_plant as set_up_plant,   
                t.vendor_code as vendor_code,
                t.vendor_name as vendor_name,
                t.po as po,
                i.author_id as author_id,
                i.status as status,
                i.created_at as created_at,
                p.team as team,
                p.brand as brand,
                p.name as name,
                concat(u.first_name, " ", u.last_name) as author_name,
                concat(uu.first_name, " ", uu.last_name) as assignee_name   
            from project_task_index i
            left join project p on p.id = i.project_id
            left join task_type_npd_po_request t on t.task_id = i.id
            left join users u on u.id = t.author_id
            left join users uu on uu.id = t.buyer
            where i.type = "npd_po_request"
                and p.status = "active"
                and i.status != "action_skip"
              ' . $cur_user_filter . '    
              ' . $material_filter . '
              ' . $buyer_filter . '
              ' . $team_filter . '
              ' . $status_filter . '
                order by t.due_date asc
            ');
    }

    public function get_task_temp_list($cur_user)
    {
        if($cur_user != '') {
            $cur_user_filter = $cur_user;
        }else{
            $cur_user_filter = ' ';
        }
        return DB::select(
            '
            select t.id as project_id,
                t.task_id as task_id,
                t.type as request_type,
                t.priority as priority,
                t.due_date as due_date,
                t.due_date_urgent as due_date_urgent,
                t.due_date_revision as due_date_revision,
                t.revision_cnt as revision_cnt,   
                t.request_detail as request_detail,
                t.source_list_completion as source_list_completion,
                t.info_record_completion as info_record_completion,
                t.price_set_up as price_set_up,
                t.forecast_completion as forecast_completion,
                t.materials as materials,
                t.total_sku_count as total_sku_count,
                t.set_up_plant as set_up_plant,   
                t.vendor_code as vendor_code,
                t.vendor_name as vendor_name,
                t.po as po,
                i.author_id as author_id,
                i.status as status,
                i.created_at as created_at,
                p.team as team,
                p.brand as brand,
                p.name as name,
                concat(u.first_name, " ", u.last_name) as author_name,
                concat(uu.first_name, " ", uu.last_name) as assignee_name   
            from project_task_index i
            left join project p on p.id = i.project_id
            left join task_type_npd_po_request t on t.task_id = i.id
            left join users u on u.id = t.author_id
            left join users uu on uu.id = t.buyer
            where i.type = "npd_po_request"
                and p.status = "active"
              ' . $cur_user_filter . '
                and i.status != "action_skip"
                and t.price_set_up = "Temporary Price (Approved by Division Leader)"  
                and i.status = "action_completed"
                order by t.due_date asc
            ');
    }

    public function get_npd_po_request_list_by_task_id($task_id)
    {
        return DB::select('
            select i.status as status,
                    i.project_id as project_id,
                    i.author_id as author_id,
                    concat(u.first_name, " ", u.last_name) as author_name,
                    concat(uu.first_name, " ", uu.last_name) as buyer_name,
                    i.type as type,
                    t.buyer as buyer,
                    t.request_detail as request_detail,
                    t.priority as priority,
                    t.due_date as due_date,
                    t.due_date_urgent as due_date_urgent,
                    t.due_date_revision as due_date_revision,
                    t.revision_cnt as revision_cnt,
                    t.urgent_reason as urgent_reason,
                    t.source_list_completion as source_list_completion,
                    t.info_record_completion as info_record_completion,
                    t.price_set_up as price_set_up,
                    t.forecast_completion as forecast_completion,
                    t.materials as materials,
                    t.total_sku_count as total_sku_count,
                    t.set_up_plant as set_up_plant,
                    t.vendor_code as vendor_code,
                    t.vendor_name as vendor_name,
                    t.second_vendor_code as second_vendor_code,
                    t.second_vendor_name as second_vendor_name,
                    t.est_ready_date as est_ready_date,
                    t.po as po,
                    t.task_id as task_id,
                    t.created_at as created_at
            from project_task_index i
            left join task_type_npd_po_request t on t.task_id =  i.id
            left join users u on u.id = t.author_id
            left join users uu on uu.id = t.buyer
            where i.id =:task_id order by t.id desc', [
            'task_id' => $task_id
        ]);
    }

    public function get_npd_po_request_by_task_id($task_id)
    {
        $rs = TaskTypeNpdPoRequest::where('task_id', $task_id)->first();
        return $rs;
    }

}
