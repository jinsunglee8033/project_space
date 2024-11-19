<?php

namespace App\Repositories\Admin;

use App\Models\ProjectTypeTaskAttachments;
use App\Models\TaskTypeLegalRequest;
use App\Repositories\Admin\Interfaces\TaskTypeLegalRequestRepositoryInterface;
use DB;

use Illuminate\Database\Eloquent\Model;

class TaskTypeLegalRequestRepository implements TaskTypeLegalRequestRepositoryInterface
{
    public function findAll($options = [])
    {
        $perPage = $options['per_page'] ?? null;
        $orderByFields = $options['order'] ?? [];
        $id = $options['id'] ?? [];

        $taskTypeLegalRequest = new TaskTypeLegalRequest();

        if ($id) {
            $taskTypeLegalRequest = $taskTypeLegalRequest
                ->where('id', $id)->where('task_id', 0);
        }

        $taskTypeLegalRequest = $taskTypeLegalRequest->get();

        return $taskTypeLegalRequest;
    }

    public function findAllByTaskId($task_id)
    {
        $taskTypeLegalRequest = new TaskTypeLegalRequest();
        return $taskTypeLegalRequest->where('task_id', $task_id)->get();
    }

    public function deleteByTaskId($task_id)
    {
        $taskTypeLegalRequest = new TaskTypeLegalRequest();
        return $taskTypeLegalRequest->where('task_id', $task_id)->delete();
    }

    public function findById($id)
    {
        return taskTypeLegalRequest::findOrFail($id);
    }

    public function create($params = [])
    {
        return DB::transaction(function () use ($params) {
            $taskTypeLegalRequest = TaskTypeLegalRequest::create($params);
            $this->syncRolesAndPermissions($params, $taskTypeLegalRequest);

            return $taskTypeLegalRequest;
        });
    }

    public function update($id, $params = [])
    {
        $taskTypeLegalRequest = TaskTypeLegalRequest::findOrFail($id);

        return DB::transaction(function () use ($params, $taskTypeLegalRequest) {
            $taskTypeLegalRequest->update($params);

            return $taskTypeLegalRequest;
        });
    }

    public function delete($id)
    {
        $taskTypeLegalRequest  = TaskTypeLegalRequest::findOrFail($id);

        return $taskTypeLegalRequest->delete();
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
            select t.legal_request_type_id as legal_request_type_id,
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
                t.priority as priority,
                t.due_date as due_date,
                t.due_date_urgent as due_date_urgent,
                t.urgent_reason as urgent_reason,
                t.due_date_revision as due_date_revision,
                t.revision_cnt as revision_cnt,
                t.registration as registration   
              from sub_legal_request_index i
            left join sub_legal_request_type t on t.legal_request_type_id = i.id
            left join project_task_index pti on pti.id = i.task_id
            left join project p on p.id = pti.project_id
            left join users u on u.id = t.author_id
            left join users uu on uu.id = t.assignee
            where p.status = "active"
                and t.type in ("trademark", "patent")
              '. $cur_user_filter .'  
              ' . $assignee_filter . '
              ' . $team_filter . '
              ' . $status_filter . '
                order by t.due_date desc
            ');
    }

}
