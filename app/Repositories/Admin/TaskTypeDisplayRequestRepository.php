<?php

namespace App\Repositories\Admin;

use App\Models\TaskTypeDisplayRequest;
use App\Repositories\Admin\Interfaces\TaskTypeDisplayRequestRepositoryInterface;
use DB;

use Illuminate\Database\Eloquent\Model;

class TaskTypeDisplayRequestRepository implements TaskTypeDisplayRequestRepositoryInterface
{
    public function findAll($options = [])
    {
        $perPage = $options['per_page'] ?? null;
        $orderByFields = $options['order'] ?? [];
        $id = $options['id'] ?? [];

        $taskTypeDisplayRequest = new TaskTypeDisplayRequest();

        if ($id) {
            $taskTypeDisplayRequest = $taskTypeDisplayRequest
                ->where('id', $id)->where('task_id', 0);
        }

        $taskTypeDisplayRequest = $taskTypeDisplayRequest->get();

        return $taskTypeDisplayRequest;
    }

    public function findAllByTaskId($task_id)
    {
        $taskTypeDisplayRequest = new TaskTypeDisplayRequest();
        return $taskTypeDisplayRequest->where('task_id', $task_id)->get();
    }

    public function deleteByTaskId($task_id)
    {
        $taskTypeDisplayRequest = new TaskTypeDisplayRequest();
        return $taskTypeDisplayRequest->where('task_id', $task_id)->delete();
    }

    public function findById($id)
    {
        return TaskTypeDisplayRequest::findOrFail($id);
    }

    public function findByTaskId($task_id)
    {
        $taskTypeDisplayRequest = new TaskTypeDisplayRequest();
        return $taskTypeDisplayRequest->where('task_id', $task_id)->first();
    }

    public function create($params = [])
    {
        return DB::transaction(function () use ($params) {
            $taskTypeDisplayRequest = TaskTypeDisplayRequest::create($params);
            $this->syncRolesAndPermissions($params, $taskTypeDisplayRequest);

            return $taskTypeDisplayRequest;
        });
    }

    public function update($id, $params = [])
    {
        $taskTypeDisplayRequest = TaskTypeDisplayRequest::findOrFail($id);

        return DB::transaction(function () use ($params, $taskTypeDisplayRequest) {
            $taskTypeDisplayRequest->update($params);

            return $taskTypeDisplayRequest;
        });
    }

    public function delete($id)
    {
        $taskTypeDisplayRequest  = TaskTypeDisplayRequest::findOrFail($id);

        return $taskTypeDisplayRequest->delete();
    }

    public function get_action_requested_list($assignee, $team, $brand, $request_type)
    {

        if($assignee != '') {
            $assignee_filter = ' and t.assignee ="' . $assignee . '" ';
        }else{
            $assignee_filter = ' ';
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
        if($request_type != '') {
            $request_type_filter = ' and t.request_type ="' . $request_type . '" ';
        }else{
            $request_type_filter = ' ';
        }
        return DB::select(
            '
            select t.id as project_id,
                t.request_type as request_type,
                    t.show_type as show_type,
                    t.show_location as show_location,
                    t.product_category as product_category,
                    t.account as account,
                    t.due_date as due_date,
                    t.task_category as task_category,
                    t.due_date_revision as due_date_revision,
                    t.revision_cnt as revision_cnt,
                    t.specify_account as specify_account,
                    t.display_style as display_style,
                    t.specify_display_style as sepcify_display_style,
                    t.display_type as specify_account,
                    t.additional_information as specify_account,
                    t.display as display,
                    t.total_display_qty as total_display_qty,
                    t.display_budget_per_ea as display_budget_per_ea,
                    t.display_budget_code as display_budget_code,
                    t.display_ready_date as display_ready_date,
                    t.assignee as assignee,
                    t.task_id as task_id,
                    t.created_at as created_at,
                   p.team as team,
                p.brand as brand,
                p.name as name,
                concat(uu.first_name, " ", uu.last_name) as assignee_name,
                concat(u.first_name, " ", u.last_name) as author_name
            from project_task_index pt
            left join project p on p.id = pt.project_id
            left join task_type_display_request t on t.task_id = pt.id
            left join users u on u.id = t.author_id
            left join users uu on uu.id = t.assignee
            where pt.type = "display_request"
                and p.status = "active"
              ' . $assignee_filter . '
              ' . $team_filter . '
              ' . $brand_filter . '
              ' . $request_type_filter . '
                and	pt.status = "action_requested"
                order by t.created_at asc
            ');
    }

    public function get_in_progress_list($assignee, $team, $brand, $request_type)
    {
        if($assignee != '') {
            $assignee_filter = ' and t.assignee ="' . $assignee . '" ';
        }else{
            $assignee_filter = ' ';
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
        if($request_type != '') {
            $request_type_filter = ' and t.request_type ="' . $request_type . '" ';
        }else{
            $request_type_filter = ' ';
        }
        return DB::select(
            '
            select t.id as project_id,
                t.request_type as request_type,
                    t.show_type as show_type,
                    t.show_location as show_location,
                    t.product_category as product_category,
                    t.account as account,
                    t.due_date as due_date,
                    t.task_category as task_category,
                    t.due_date_revision as due_date_revision,
                    t.revision_cnt as revision_cnt,
                    t.specify_account as specify_account,
                    t.display_style as display_style,
                    t.specify_display_style as sepcify_display_style,
                    t.display_type as display_type,
                    t.additional_information as additional_information,
                    t.display as display,
                    t.total_display_qty as total_display_qty,
                    t.display_budget_per_ea as display_budget_per_ea,
                    t.display_budget_code as display_budget_code,
                    t.display_ready_date as display_ready_date,
                    t.assignee as assignee,
                    t.task_id as task_id,
                    t.created_at as created_at,
                   p.team as team,
                p.brand as brand,
                p.name as name,
                concat(uu.first_name, " ", uu.last_name) as assignee_name,
                concat(u.first_name, " ", u.last_name) as author_name
            from project_task_index pt
            left join project p on p.id = pt.project_id
            left join task_type_display_request t on t.task_id = pt.id
            left join users u on u.id = t.author_id
            left join users uu on uu.id = t.assignee
            where pt.type = "display_request"
                and p.status = "active"
              ' . $assignee_filter . '
              ' . $team_filter . '
              ' . $brand_filter . '
              ' . $request_type_filter . '
                and	pt.status = "in_progress"
                order by t.created_at asc
            ');
    }

    public function get_action_review_list($assignee, $team, $brand, $request_type)
    {
        if($assignee != '') {
            $assignee_filter = ' and t.assignee ="' . $assignee . '" ';
        }else{
            $assignee_filter = ' ';
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
        if($request_type != '') {
            $request_type_filter = ' and t.request_type ="' . $request_type . '" ';
        }else{
            $request_type_filter = ' ';
        }
        return DB::select(
            '
            select t.id as project_id,
                t.request_type as request_type,
                    t.show_type as show_type,
                    t.show_location as show_location,
                    t.product_category as product_category,
                    t.account as account,
                    t.due_date as due_date,
                    t.task_category as task_category,
                    t.due_date_revision as due_date_revision,
                    t.revision_cnt as revision_cnt,
                    t.specify_account as specify_account,
                    t.display_style as display_style,
                    t.specify_display_style as sepcify_display_style,
                    t.display_type as display_type,
                    t.additional_information as additional_information,
                    t.display as display,
                    t.total_display_qty as total_display_qty,
                    t.display_budget_per_ea as display_budget_per_ea,
                    t.display_budget_code as display_budget_code,
                    t.display_ready_date as display_ready_date,
                    t.assignee as assignee,
                    t.task_id as task_id,
                    t.created_at as created_at,
                   p.team as team,
                p.brand as brand,
                p.name as name,
                concat(uu.first_name, " ", uu.last_name) as assignee_name,
                concat(u.first_name, " ", u.last_name) as author_name
            from project_task_index pt
            left join project p on p.id = pt.project_id
            left join task_type_display_request t on t.task_id = pt.id
            left join users u on u.id = t.author_id
            left join users uu on uu.id = t.assignee
            where pt.type = "display_request"
                and p.status = "active"
              ' . $assignee_filter . '
              ' . $team_filter . '
              ' . $brand_filter . '
              ' . $request_type_filter . '
                and	pt.status = "action_review"
                order by t.created_at asc
            ');
    }

    public function get_action_completed_list($assignee, $team, $brand, $request_type)
    {
        if($assignee != '') {
            $assignee_filter = ' and t.assignee ="' . $assignee . '" ';
        }else{
            $assignee_filter = ' ';
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
        if($request_type != '') {
            $request_type_filter = ' and t.request_type ="' . $request_type . '" ';
        }else{
            $request_type_filter = ' ';
        }
        return DB::select(
            '
            select t.id as project_id,
                t.request_type as request_type,
                    t.show_type as show_type,
                    t.show_location as show_location,
                    t.product_category as product_category,
                    t.account as account,
                    t.due_date as due_date,
                    t.task_category as task_category,
                    t.due_date_revision as due_date_revision,
                    t.revision_cnt as revision_cnt,
                    t.specify_account as specify_account,
                    t.display_style as display_style,
                    t.specify_display_style as sepcify_display_style,
                    t.display_type as display_type,
                    t.additional_information as additional_information,
                    t.display as display,
                    t.total_display_qty as total_display_qty,
                    t.display_budget_per_ea as display_budget_per_ea,
                    t.display_budget_code as display_budget_code,
                    t.display_ready_date as display_ready_date,
                    t.assignee as assignee,
                    t.task_id as task_id,
                    t.created_at as created_at,
                   p.team as team,
                p.brand as brand,
                p.name as name,
                concat(uu.first_name, " ", uu.last_name) as assignee_name,    
                concat(u.first_name, " ", u.last_name) as author_name
            from project_task_index pt
            left join project p on p.id = pt.project_id
            left join task_type_display_request t on t.task_id = pt.id
            left join users u on u.id = t.author_id
            left join users uu on uu.id = t.assignee
            where pt.type = "display_request"
                and p.status = "active"
              ' . $assignee_filter . '
              ' . $team_filter . '
              ' . $brand_filter . '
              ' . $request_type_filter . '
                and	pt.status = "action_completed"
                order by t.created_at asc
            ');
    }

    public function get_task_list($str, $assignee, $team, $brand)
    {

        if($str != '') {
            $material_filter = ' and t.materials like "%' . $str . '%" ';
        }else{
            $material_filter = ' ';
        }
        if($assignee != '') {
            $assignee_filter = ' and t.assignee ="' . $assignee . '" ';
        }else{
            $assignee_filter = ' ';
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
            select t.id as project_id,
                t.task_id as task_id,
                t.materials as materials,
                t.priority as priority,
                t.request_type as request_type,
                t.due_date as due_date,
                t.due_date_urgent as due_date_urgent,
                t.due_date_revision as due_date_revision,
                t.revision_cnt as revision_cnt,      
                pt.author_id as author_id,
                pt.status as status,
                pt.created_at as created_at,
                p.team as team,
                p.brand as brand,
                p.name as name,
                concat(u.first_name, " ", u.last_name) as author_name,
                concat(uu.first_name, " ", uu.last_name) as assignee_name   
            from project_task_index pt
            left join project p on p.id = pt.project_id
            left join task_type_display_request t on t.task_id = pt.id
            left join users u on u.id = t.author_id
            left join users uu on uu.id = t.assignee
            where pt.type = "display_request"
                and p.status = "active"
              ' . $material_filter . '
              ' . $assignee_filter . '
              ' . $team_filter . '
              ' . $brand_filter . '
                order by t.due_date asc
            ');
    }

}
