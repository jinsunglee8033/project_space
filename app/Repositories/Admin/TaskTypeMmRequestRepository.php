<?php

namespace App\Repositories\Admin;

use App\Repositories\Admin\Interfaces\TaskTypeMmRequestRepositoryInterface;
use DB;

use App\Models\TaskTypeMmRequest;
use Illuminate\Database\Eloquent\Model;

class TaskTypeMmRequestRepository implements TaskTypeMmRequestRepositoryInterface
{
    public function findAll($options = [])
    {
        $perPage = $options['per_page'] ?? null;
        $orderByFields = $options['order'] ?? [];
        $id = $options['id'] ?? [];

        $taskTypeMmRequest = new TaskTypeMmRequest();

        if ($id) {
            $taskTypeMmRequest = $taskTypeMmRequest
                ->where('id', $id)->where('task_id', 0);
        }

        $taskTypeMmRequest = $taskTypeMmRequest->get();

        return $taskTypeMmRequest;
    }

    public function findAllByTaskId($task_id)
    {
        $taskTypeMmRequest = new TaskTypeMmRequest();
        return $taskTypeMmRequest->where('task_id', $task_id)->get();
    }

    public function deleteByTaskId($task_id)
    {
        $taskTypeMmRequest = new TaskTypeMmRequest();
        return $taskTypeMmRequest->where('task_id', $task_id)->delete();
    }

    public function findById($id)
    {
        return TaskTypeMmRequest::findOrFail($id);
    }

    public function findByTaskId($task_id)
    {
        $taskTypeMmRequest = new TaskTypeMmRequest();
        return $taskTypeMmRequest->where('task_id', $task_id)->first();
    }

    public function create($params = [])
    {
        return DB::transaction(function () use ($params) {
            $taskTypeMmRequest = TaskTypeMmRequest::create($params);
            $this->syncRolesAndPermissions($params, $taskTypeMmRequest);

            return $taskTypeMmRequest;
        });
    }

    public function update($id, $params = [])
    {
        $taskTypeMmRequest = TaskTypeMmRequest::findOrFail($id);

        return DB::transaction(function () use ($params, $taskTypeMmRequest) {
            $taskTypeMmRequest->update($params);

            return $taskTypeMmRequest;
        });
    }

    public function delete($id)
    {
        $taskTypeMmRequest  = TaskTypeMmRequest::findOrFail($id);

        return $taskTypeMmRequest->delete();
    }

    public function get_action_requested_list($cur_user, $assignee, $team, $brand)
    {
        if($cur_user != '') {
            $cur_user_filter = $cur_user;
        }else{
            $cur_user_filter = ' ';
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
            select t.mm_request_type_id as mm_request_type_id,
                   i.author_id as author_i,
                p.name as name,
                p.team as team,
                p.brand as brand,
                p.status as project_status,
                i.status as task_status,
                pti.project_id as project_id,
                pti.id as task_id,
                pti.status as sub_task_status,
                t.type as request_type,
                concat(u.first_name, " ", u.last_name) as author_name,
                concat(uu.first_name, " ", uu.last_name) as assignee_name,
                t.materials as materials,
                t.priority as priority,
                t.due_date as due_date,
                t.due_date_urgent as due_date_urgent,
                t.urgent_reason as urgent_reason,
                t.due_date_revision as due_date_revision,
                t.revision_cnt as revision_cnt,
                t.set_up_plant as set_up_plant,
                t.remark as remark
              from sub_mm_request_index i
            left join sub_mm_request_type t on t.mm_request_type_id = i.id
            left join project_task_index pti on pti.id = i.task_id
            left join project p on p.id = pti.project_id
            left join users u on u.id = t.author_id
            left join users uu on uu.id = t.assignee
            where i. status = "action_requested"
            and p.status = "active"
              ' . $cur_user_filter . '
              ' . $assignee_filter . '
              ' . $team_filter . '
              ' . $brand_filter . '
                order by t.due_date asc
            ');
    }

    public function get_in_progress_list($cur_user, $assignee, $team, $brand)
    {
        if($cur_user != '') {
            $cur_user_filter = $cur_user;
        }else{
            $cur_user_filter = ' ';
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
            select t.mm_request_type_id as mm_request_type_id,
                p.name as name,
                p.team as team,
                p.brand as brand,
                p.status as project_status,
                i.status as task_status,
                pti.project_id as project_id,
                pti.id as task_id,
                pti.status as sub_task_status,
                t.type as request_type,
                concat(u.first_name, " ", u.last_name) as author_name,
                concat(uu.first_name, " ", uu.last_name) as assignee_name,
                t.materials as materials,
                t.priority as priority,
                t.due_date as due_date,
                t.due_date_urgent as due_date_urgent,
                t.urgent_reason as urgent_reason,
                t.due_date_revision as due_date_revision,
                t.revision_cnt as revision_cnt,
                t.set_up_plant as set_up_plant,
                t.remark as remark
              from sub_mm_request_index i
            left join sub_mm_request_type t on t.mm_request_type_id = i.id
            left join project_task_index pti on pti.id = i.task_id
            left join project p on p.id = pti.project_id
            left join users u on u.id = t.author_id
            left join users uu on uu.id = t.assignee
            where i. status = "in_progress"
            and p.status = "active"
            ' . $cur_user_filter . '
              ' . $assignee_filter . '
              ' . $team_filter . '
              ' . $brand_filter . '
                order by t.due_date asc
            ');
    }

    public function get_action_review_list($cur_user, $assignee, $team, $brand)
    {
        if($cur_user != '') {
            $cur_user_filter = $cur_user;
        }else{
            $cur_user_filter = ' ';
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
            select t.mm_request_type_id as mm_request_type_id,
                p.name as name,
                p.team as team,
                p.brand as brand,
                p.status as project_status,
                i.status as task_status,
                pti.project_id as project_id,
                pti.id as task_id,
                pti.status as sub_task_status,
                t.type as request_type,
                concat(u.first_name, " ", u.last_name) as author_name,
                concat(uu.first_name, " ", uu.last_name) as assignee_name,
                t.materials as materials,
                t.priority as priority,
                t.due_date as due_date,
                t.due_date_urgent as due_date_urgent,
                t.urgent_reason as urgent_reason,
                t.due_date_revision as due_date_revision,
                t.revision_cnt as revision_cnt,
                t.set_up_plant as set_up_plant,
                t.remark as remark
              from sub_mm_request_index i
            left join sub_mm_request_type t on t.mm_request_type_id = i.id
            left join project_task_index pti on pti.id = i.task_id
            left join project p on p.id = pti.project_id
            left join users u on u.id = t.author_id
            left join users uu on uu.id = t.assignee
            where i. status = "action_review"
            and p.status = "active"
            ' . $cur_user_filter . '
              ' . $assignee_filter . '
              ' . $team_filter . '
              ' . $brand_filter . '
                order by t.due_date asc
            ');
    }

    public function get_action_completed_list($cur_user, $assignee, $team, $brand)
    {
        if($cur_user != '') {
            $cur_user_filter = $cur_user;
        }else{
            $cur_user_filter = ' ';
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
            select t.mm_request_type_id as mm_request_type_id,
                p.name as name,
                p.team as team,
                p.brand as brand,
                p.status as project_status,
                i.status as task_status,
                pti.project_id as project_id,
                pti.id as task_id,
                pti.status as sub_task_status,
                t.type as request_type,
                concat(u.first_name, " ", u.last_name) as author_name,
                concat(uu.first_name, " ", uu.last_name) as assignee_name,
                t.materials as materials,
                t.priority as priority,
                t.due_date as due_date,
                t.due_date_urgent as due_date_urgent,
                t.urgent_reason as urgent_reason,
                t.due_date_revision as due_date_revision,
                t.revision_cnt as revision_cnt,
                t.set_up_plant as set_up_plant,
                t.remark as remark
              from sub_mm_request_index i
            left join sub_mm_request_type t on t.mm_request_type_id = i.id
            left join project_task_index pti on pti.id = i.task_id
            left join project p on p.id = pti.project_id
            left join users u on u.id = t.author_id
            left join users uu on uu.id = t.assignee
            where i. status = "action_completed"
            and p.status = "active"
            ' . $cur_user_filter . '
              ' . $assignee_filter . '
              ' . $team_filter . '
              ' . $brand_filter . '
                order by t.due_date asc
            ');
    }

    public function get_task_list($cur_user, $assignee, $team, $status)
    {
        if($cur_user != '') {
            $cur_user_filter = $cur_user;
        }else{
            $cur_user_filter = ' ';
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
        if($status != '') {
            $status_filter = ' and i.status ="' . $status . '" ';
        }else{
            $status_filter = ' ';
        }

        return DB::select(
            '
            select t.mm_request_type_id as mm_request_type_id,
                p.name as name,
                p.team as team,
                p.brand as brand,
                p.status as project_status,
                i.status as status,
                pti.project_id as project_id,
                pti.id as task_id,
                pti.status as sub_task_status,
                t.type as request_type,
                concat(u.first_name, " ", u.last_name) as author_name,
                concat(uu.first_name, " ", uu.last_name) as assignee_name,
                t.materials as materials,
                t.priority as priority,
                t.due_date as due_date,
                t.due_date_urgent as due_date_urgent,
                t.urgent_reason as urgent_reason,
                t.due_date_revision as due_date_revision,
                t.revision_cnt as revision_cnt,
                t.set_up_plant as set_up_plant,
                t.remark as remark
              from sub_mm_request_index i
            left join sub_mm_request_type t on t.mm_request_type_id = i.id
            left join project_task_index pti on pti.id = i.task_id
            left join project p on p.id = pti.project_id
            left join users u on u.id = t.author_id
            left join users uu on uu.id = t.assignee
            where p.status = "active"
            ' . $cur_user_filter . '
              ' . $assignee_filter . '
              ' . $team_filter . '
              ' . $status_filter . '
                order by t.due_date desc
            ');
    }

}
