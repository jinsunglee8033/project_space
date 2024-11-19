<?php

namespace App\Repositories\Admin;

use App\Models\ProjectTypeTaskAttachments;
use App\Models\SubLegalRequestType;
use App\Models\SubNpdPlannerRequestType;
use App\Models\SubPeRequestType;
use App\Models\SubQraRequestType;
use App\Models\TaskTypeDisplayRequest;
use App\Models\TaskTypeLegalRequest;
use App\Repositories\Admin\Interfaces\SubLegalRequestIndexRepositoryInterface;
use App\Repositories\Admin\Interfaces\SubNpdPlannerRequestTypeRepositoryInterface;
use App\Repositories\Admin\Interfaces\SubNpdPlannerRequestIndexRepositoryInterface;
use App\Repositories\Admin\Interfaces\SubPeRequestIndexRepositoryInterface;
use App\Repositories\Admin\Interfaces\SubQraRequestTypeRepositoryInterface;
use App\Repositories\Admin\Interfaces\TaskTypeLegalRequestRepositoryInterface;
use DB;

use Illuminate\Database\Eloquent\Model;

class SubNpdPlannerRequestTypeRepository implements SubNpdPlannerRequestIndexRepositoryInterface
{
    public function findAll($options = [])
    {
        $perPage = $options['per_page'] ?? null;
        $orderByFields = $options['order'] ?? [];
        $id = $options['id'] ?? [];

        $subNpdPlannerRequestType = new SubNpdPlannerRequestType();

        if ($id) {
            $subNpdPlannerRequestType = $subNpdPlannerRequestType
                ->where('id', $id)->where('task_id', 0);
        }

        $subNpdPlannerRequestType = $subNpdPlannerRequestType->get();

        return $subNpdPlannerRequestType;
    }

    public function findAllByRequestTypeId($request_type_id)
    {
        $subNpdPlannerRequestType = new SubNpdPlannerRequestType();
        return $subNpdPlannerRequestType->where('request_type_id', $request_type_id)->first();
    }

    public function deleteByTaskId($task_id)
    {
        $subNpdPlannerRequestType = new SubNpdPlannerRequestType();
        return $subNpdPlannerRequestType->where('task_id', $task_id)->delete();
    }

    public function findById($id)
    {
        return SubNpdPlannerRequestType::findOrFail($id);
    }

    public function create($params = [])
    {
        return DB::transaction(function () use ($params) {
            $subNpdPlannerRequestType = SubNpdPlannerRequestType::create($params);
            $this->syncRolesAndPermissions($params, $subNpdPlannerRequestType);

            return $subNpdPlannerRequestType;
        });
    }

    public function update($id, $params = [])
    {
        $subNpdPlannerRequestType = SubNpdPlannerRequestType::findOrFail($id);

        return DB::transaction(function () use ($params, $subNpdPlannerRequestType) {
            $subNpdPlannerRequestType->update($params);

            return $subNpdPlannerRequestType;
        });
    }

    public function delete($id)
    {
        $subNpdPlannerRequestType  = SubNpdPlannerRequestType::findOrFail($id);

        return $subNpdPlannerRequestType->delete();
    }

    public function get_action_requested_list($design_group, $team, $brand, $assignee)
    {
        if($design_group != '') {
            $filter_design_group = ' and t.design_group ="' . $design_group . '" ';
        }else{
            $filter_design_group = ' ';
        }
        if($team != '') {
            $filter_team = ' and p.team ="' . $team . '" ';
        }else{
            $filter_team = ' ';
        }
        if($brand != '') {
            $filter_brand = ' and p.brand ="' . $brand . '" ';
        }else{
            $filter_brand = ' ';
        }
        if($assignee != '') {
            $filter_assignee = ' and t.assignee ="' . $assignee . '" ';
        }else{
            $filter_assignee = ' ';
        }
        return DB::select(
            'select p.id as project_id,
        p.team as team,
        p.name as name,
        p.brand as brand,           
		t.npd_design_request_type_id as npd_design_request_type_id,
		t.type as request_type,
        t.design_group as design_group,
		t.author_id as author_id,
		i.status as status,
		t.request_type as request_type,
		t.objective as objective,
		t.priority as priority,
		t.due_date as due_date,
		t.due_date_urgent as due_date_urgent,
		t.urgent_reason as urgent_reason,
		t.due_date_revision as due_date_revision,
		t.revision_cnt as revision_cnt,
		t.revision_reason as revision_reason,
		t.scope as scope,
		t.artwork_type as artwork_type,
		t.sales_channel as sales_channel,
		t.material_number as material_number,
        concat(u.first_name, " ", u.last_name) as author_name,
		t.assignee as assignee,
        concat(uu.first_name, " ", uu.last_name) as assignee_name,
		t.multiple_assignees as multiple_assignees,
		uu.first_name as assignee_first_name,
		uu.last_name as assignee_last_name,
		t.created_at as created_at
from sub_npd_design_request_index i
left join sub_npd_design_request_type t on t.npd_design_request_type_id = i.id
left join project_task_index pt on pt.id = i.task_id
left join project p on p.id = pt.project_id
left join users u on u.id = i.author_id
left join users uu on uu.id = t.assignee
where i.status = "action_requested"
and t.assignee is null
' . $filter_design_group . '
' . $filter_team . '
' . $filter_brand . '
' . $filter_assignee . '
order by i.created_at desc   
            ');
    }

    public function get_to_do_list($design_group, $team, $brand, $assignee)
    {
        if($design_group != '') {
            $filter_design_group = ' and t.design_group ="' . $design_group . '" ';
        }else{
            $filter_design_group = ' ';
        }
        if($team != '') {
            $filter_team = ' and p.team ="' . $team . '" ';
        }else{
            $filter_team = ' ';
        }
        if($brand != '') {
            $filter_brand = ' and p.brand ="' . $brand . '" ';
        }else{
            $filter_brand = ' ';
        }
        if($assignee != '') {
            $filter_assignee = ' and t.assignee ="' . $assignee . '" ';
        }else{
            $filter_assignee = ' ';
        }
        return DB::select(
            'select p.id as project_id,
        p.team as team,
        p.name as name,
        p.brand as brand,           
		t.npd_design_request_type_id as npd_design_request_type_id,
		t.author_id as author_id,
		i.status as status,
		t.npd_design_request_type_id as npd_design_request_type_id,
		t.request_type as request_type,
        t.design_group as design_group,
		t.objective as objective,
		t.priority as priority,
		t.due_date as due_date,
		t.due_date_urgent as due_date_urgent,
		t.urgent_reason as urgent_reason,
		t.due_date_revision as due_date_revision,
		t.revision_cnt as revision_cnt,
		t.revision_reason as revision_reason,
		t.scope as scope,
		t.artwork_type as artwork_type,
		t.sales_channel as sales_channel,
		t.material_number as material_number,
        concat(u.first_name, " ", u.last_name) as author_name,
		t.assignee as assignee,
        concat(uu.first_name, " ", uu.last_name) as assignee_name,
		t.created_at as created_at
from sub_npd_design_request_index i
left join sub_npd_design_request_type t on t.npd_design_request_type_id = i.id
left join project_task_index pt on pt.id = i.task_id
left join project p on p.id = pt.project_id
left join users u on u.id = i.author_id
left join users uu on uu.id = t.assignee
where i.status = "action_requested"
and t.assignee is not null
' . $filter_design_group . '
' . $filter_team . '
' . $filter_brand . '
' . $filter_assignee . '
order by i.created_at desc   
            ');
    }

    public function get_in_progress_list($design_group, $team, $brand, $assignee)
    {
        if($design_group != '') {
            $filter_design_group = ' and t.design_group ="' . $design_group . '" ';
        }else{
            $filter_design_group = ' ';
        }
        if($team != '') {
            $filter_team = ' and p.team ="' . $team . '" ';
        }else{
            $filter_team = ' ';
        }
        if($brand != '') {
            $filter_brand = ' and p.brand ="' . $brand . '" ';
        }else{
            $filter_brand = ' ';
        }
        if($assignee != '') {
            $filter_assignee = ' and t.assignee ="' . $assignee . '" ';
        }else{
            $filter_assignee = ' ';
        }
        return DB::select(
            'select p.id as project_id,
        p.team as team,
        p.name as name,
        p.brand as brand,           
		t.npd_design_request_type_id as npd_design_request_type_id,
		t.author_id as author_id,
		i.status as status,
		t.npd_design_request_type_id as npd_design_request_type_id,
		t.request_type as request_type,
        t.design_group as design_group,
		t.objective as objective,
		t.priority as priority,
		t.due_date as due_date,
		t.due_date_urgent as due_date_urgent,
		t.urgent_reason as urgent_reason,
		t.due_date_revision as due_date_revision,
		t.revision_cnt as revision_cnt,
		t.revision_reason as revision_reason,
		t.scope as scope,
		t.artwork_type as artwork_type,
		t.sales_channel as sales_channel,
		t.material_number as material_number,
        concat(u.first_name, " ", u.last_name) as author_name,
		t.assignee as assignee,
        concat(uu.first_name, " ", uu.last_name) as assignee_name,
		t.created_at as created_at
from sub_npd_design_request_index i
left join sub_npd_design_request_type t on t.npd_design_request_type_id = i.id
left join project_task_index pt on pt.id = i.task_id
left join project p on p.id = pt.project_id
left join users u on u.id = i.author_id
left join users uu on uu.id = t.assignee
where i.status = "in_progress"
' . $filter_design_group . '
' . $filter_team . '
' . $filter_brand . '
' . $filter_assignee . '
order by i.created_at desc   
            ');
    }

    public function get_action_review_list($design_group, $team, $brand, $assignee)
    {
        if($design_group != '') {
            $filter_design_group = ' and t.design_group ="' . $design_group . '" ';
        }else{
            $filter_design_group = ' ';
        }
        if($team != '') {
            $filter_team = ' and p.team ="' . $team . '" ';
        }else{
            $filter_team = ' ';
        }
        if($brand != '') {
            $filter_brand = ' and p.brand ="' . $brand . '" ';
        }else{
            $filter_brand = ' ';
        }
        if($assignee != '') {
            $filter_assignee = ' and t.assignee ="' . $assignee . '" ';
        }else{
            $filter_assignee = ' ';
        }
        return DB::select(
            'select p.id as project_id,
        p.team as team,
        p.name as name,
        p.brand as brand,           
		t.npd_design_request_type_id as npd_design_request_type_id,
        t.design_group as design_group,
		t.author_id as author_id,
		i.status as status,
		t.npd_design_request_type_id as npd_design_request_type_id,
		t.request_type as request_type,
		t.objective as objective,
		t.priority as priority,
		t.due_date as due_date,
		t.due_date_urgent as due_date_urgent,
		t.urgent_reason as urgent_reason,
		t.due_date_revision as due_date_revision,
		t.revision_cnt as revision_cnt,
		t.revision_reason as revision_reason,
		t.scope as scope,
		t.artwork_type as artwork_type,
		t.sales_channel as sales_channel,
		t.material_number as material_number,
        concat(u.first_name, " ", u.last_name) as author_name,
		t.assignee as assignee,
        concat(uu.first_name, " ", uu.last_name) as assignee_name,
		t.created_at as created_at
from sub_npd_design_request_index i
left join sub_npd_design_request_type t on t.npd_design_request_type_id = i.id
left join project_task_index pt on pt.id = i.task_id
left join project p on p.id = pt.project_id
left join users u on u.id = i.author_id
left join users uu on uu.id = t.assignee
where i.status in ("action_review", "update_required")
' . $filter_design_group . '
' . $filter_team . '
' . $filter_brand . '
' . $filter_assignee . '
order by i.created_at desc   
            ');
    }

    public function get_action_completed_list($design_group, $team, $brand, $assignee)
    {
        if($design_group != '') {
            $filter_design_group = ' and t.design_group ="' . $design_group . '" ';
        }else{
            $filter_design_group = ' ';
        }
        if($team != '') {
            $filter_team = ' and p.team ="' . $team . '" ';
        }else{
            $filter_team = ' ';
        }
        if($brand != '') {
            $filter_brand = ' and p.brand ="' . $brand . '" ';
        }else{
            $filter_brand = ' ';
        }
        if($assignee != '') {
            $filter_assignee = ' and t.assignee ="' . $assignee . '" ';
        }else{
            $filter_assignee = ' ';
        }
        return DB::select(
            'select p.id as project_id,
        p.team as team,
        p.name as name,
        p.brand as brand,           
		t.npd_design_request_type_id as npd_design_request_type_id,
		t.author_id as author_id,
		i.status as status,
		t.npd_design_request_type_id as npd_design_request_type_id,
		t.request_type as request_type,
        t.design_group as design_group,
		t.objective as objective,
		t.priority as priority,
		t.due_date as due_date,
		t.due_date_urgent as due_date_urgent,
		t.urgent_reason as urgent_reason,
		t.due_date_revision as due_date_revision,
		t.revision_cnt as revision_cnt,
		t.revision_reason as revision_reason,
		t.scope as scope,
		t.artwork_type as artwork_type,
		t.sales_channel as sales_channel,
		t.material_number as material_number,
        concat(u.first_name, " ", u.last_name) as author_name,
		t.assignee as assignee,
        concat(uu.first_name, " ", uu.last_name) as assignee_name,
		t.created_at as created_at
from sub_npd_design_request_index i
left join sub_npd_design_request_type t on t.npd_design_request_type_id = i.id
left join project_task_index pt on pt.id = i.task_id
left join project p on p.id = pt.project_id
left join users u on u.id = i.author_id
left join users uu on uu.id = t.assignee
where i.status = "action_completed"
' . $filter_design_group . '
' . $filter_team . '
' . $filter_brand . '
' . $filter_assignee . '
order by i.created_at desc   
            ');
    }

    public function get_sub_npd_planner_request_by_npd_planner_request_type_id($task_id)
    {
        $rs = SubNpdPlannerRequestType::where('npd_planner_request_type_id', $task_id)->first();
        return $rs;
    }
}
