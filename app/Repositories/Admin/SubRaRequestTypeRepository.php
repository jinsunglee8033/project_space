<?php

namespace App\Repositories\Admin;

use App\Models\ProjectTypeTaskAttachments;
use App\Models\SubQraRequestType;
use App\Models\SubRaRequestType;
use App\Models\TaskTypeLegalRequest;
use App\Repositories\Admin\Interfaces\SubQraRequestTypeRepositoryInterface;
use App\Repositories\Admin\Interfaces\SubRaRequestTypeRepositoryInterface;
use App\Repositories\Admin\Interfaces\TaskTypeLegalRequestRepositoryInterface;
use DB;

use Illuminate\Database\Eloquent\Model;

class SubRaRequestTypeRepository implements SubRaRequestTypeRepositoryInterface
{
    public function findAll($options = [])
    {
        $perPage = $options['per_page'] ?? null;
        $orderByFields = $options['order'] ?? [];
        $id = $options['id'] ?? [];

        $subRaRequestType = new SubRaRequestType();

        if ($id) {
            $subRaRequestType = $subRaRequestType
                ->where('id', $id)->where('task_id', 0);
        }

        $subRaRequestType = $subRaRequestType->get();

        return $subRaRequestType;
    }

    public function findAllByRequestTypeId($request_type_id)
    {
        $subRaRequestType = new SubRaRequestType();
        return $subRaRequestType->where('request_type_id', $request_type_id)->first();
    }

    public function deleteByTaskId($task_id)
    {
        $subRaRequestType = new SubRaRequestType();
        return $subRaRequestType->where('task_id', $task_id)->delete();
    }

    public function findById($id)
    {
        return SubRaRequestType::findOrFail($id);
    }

    public function create($params = [])
    {
        return DB::transaction(function () use ($params) {
            $subRaRequestType = SubRaRequestType::create($params);
            $this->syncRolesAndPermissions($params, $subRaRequestType);

            return $subRaRequestType;
        });
    }

    public function update($id, $params = [])
    {
        $subRaRequestType = SubRaRequestType::findOrFail($id);

        return DB::transaction(function () use ($params, $subRaRequestType) {
            $subRaRequestType->update($params);

            return $subRaRequestType;
        });
    }

    public function delete($id)
    {
        $subRaRequestType  = SubRaRequestType::findOrFail($id);

        return $subRaRequestType->delete();
    }

    public function get_action_requested_list($cur_user, $request_type, $assignee, $team)
    {
        if($cur_user != '') {
            $cur_user_filter = $cur_user;
        }else{
            $cur_user_filter = ' ';
        }
        if($request_type != '') {
            $filter_rt = ' and t.type ="' . $request_type . '" ';
        }else{
            $filter_rt = ' ';
        }
        if($assignee != '') {
            $filter_a = ' and t.assignee ="' . $assignee . '" ';
        }else{
            $filter_a = ' ';
        }
        if($team != '') {
            $filter_t = ' and p.team ="' . $team . '" ';
        }else{
            $filter_t = ' ';
        }

        return DB::select(
            '
            select t.ra_request_type_id as ra_request_type_id,
                    t.type as request_type,
                    i.status as status,
                    p.name as name,
                    p.brand as brand,
                    p.team as team,
                    pti.project_id as project_id,
                    pti.id as task_id,
                    t.author_id as author_id,
                    concat(u.first_name, " ", u.last_name) as author_name,
                    concat(uu.first_name, " ", uu.last_name) as assignee_name,
                    t.due_date as due_date,
                    t.due_date_revision as due_date_revision,
                    t.revision_cnt as revision_cnt,
                    t.registration_due_date as registration_due_date,
                    t.assignee as assignee
            from sub_ra_request_index i
            left join sub_ra_request_type t on t.ra_request_type_id = i.id
            left join project_task_index pti on pti.id = i.task_id
            left join project p on p.id = pti.project_id
            left join users u on u.id = t.author_id
            left join users uu on uu.id = t.assignee
            where i.status = "action_requested"
            and p.status = "active" 
                ' . $cur_user_filter . '
                ' . $filter_rt . '
                ' . $filter_a . '
                ' . $filter_t . '
                order by t.id desc   
            ');
    }

    public function get_in_progress_list($cur_user, $request_type, $assignee, $team)
    {
        if($cur_user != '') {
            $cur_user_filter = $cur_user;
        }else{
            $cur_user_filter = ' ';
        }
        if($request_type != '') {
            $filter_rt = ' and t.type ="' . $request_type . '" ';
        }else{
            $filter_rt = ' ';
        }
        if($assignee != '') {
            $filter_a = ' and t.assignee ="' . $assignee . '" ';
        }else{
            $filter_a = ' ';
        }
        if($team != '') {
            $filter_t = ' and p.team ="' . $team . '" ';
        }else{
            $filter_t = ' ';
        }
        return DB::select(
            '
            select t.ra_request_type_id as ra_request_type_id,
                    t.type as request_type,
                    i.status as status,
                    p.name as name,
                    p.brand as brand,
                    p.team as team,
                    pti.project_id as project_id,
                    pti.id as task_id,
                    t.author_id as author_id,
                    concat(u.first_name, " ", u.last_name) as author_name,
                    concat(uu.first_name, " ", uu.last_name) as assignee_name,
                    t.due_date as due_date,
                    t.due_date_revision as due_date_revision,
                    t.revision_cnt as revision_cnt,
                    t.registration_due_date as registration_due_date,
                    t.assignee as assignee
            from sub_ra_request_index i
            left join sub_ra_request_type t on t.ra_request_type_id = i.id
            left join project_task_index pti on pti.id = i.task_id
            left join project p on p.id = pti.project_id
            left join users u on u.id = t.author_id
            left join users uu on uu.id = t.assignee
            where i.status = "in_progress"
            and p.status = "active" 
                ' . $cur_user_filter . '
                ' . $filter_rt . '
                ' . $filter_a . '
                ' . $filter_t . '
                order by t.id desc   
            ');
    }

    public function get_action_review_list($cur_user, $request_type, $assignee, $team)
    {
        if($cur_user != '') {
            $cur_user_filter = $cur_user;
        }else{
            $cur_user_filter = ' ';
        }
        if($request_type != '') {
            $filter_rt = ' and t.type ="' . $request_type . '" ';
        }else{
            $filter_rt = ' ';
        }
        if($assignee != '') {
            $filter_a = ' and t.assignee ="' . $assignee . '" ';
        }else{
            $filter_a = ' ';
        }
        if($team != '') {
            $filter_t = ' and p.team ="' . $team . '" ';
        }else{
            $filter_t = ' ';
        }
        return DB::select(
            '
            select t.ra_request_type_id as ra_request_type_id,
                    t.type as request_type,
                    i.status as status,
                    p.name as name,
                    p.brand as brand,
                    p.team as team,
                    pti.project_id as project_id,
                    pti.id as task_id,
                    t.author_id as author_id,
                    concat(u.first_name, " ", u.last_name) as author_name,
                    concat(uu.first_name, " ", uu.last_name) as assignee_name,
                    t.due_date as due_date,
                    t.due_date_revision as due_date_revision,
                    t.revision_cnt as revision_cnt,
                    t.registration_due_date as registration_due_date,
                    t.assignee as assignee
            from sub_ra_request_index i
            left join sub_ra_request_type t on t.ra_request_type_id = i.id
            left join project_task_index pti on pti.id = i.task_id
            left join project p on p.id = pti.project_id
            left join users u on u.id = t.author_id
            left join users uu on uu.id = t.assignee
            where i.status = "action_review"
            and p.status = "active" 
                ' . $cur_user_filter . '
                ' . $filter_rt . '
                ' . $filter_a . '
                ' . $filter_t . '
                order by t.id desc   
            ');
    }

    public function get_action_completed_list($cur_user, $request_type, $assignee, $team)
    {
        if($cur_user != '') {
            $cur_user_filter = $cur_user;
        }else{
            $cur_user_filter = ' ';
        }
        if($request_type != '') {
            $filter_rt = ' and t.type ="' . $request_type . '" ';
        }else{
            $filter_rt = ' ';
        }
        if($assignee != '') {
            $filter_a = ' and t.assignee ="' . $assignee . '" ';
        }else{
            $filter_a = ' ';
        }
        if($team != '') {
            $filter_t = ' and p.team ="' . $team . '" ';
        }else{
            $filter_t = ' ';
        }
        return DB::select(
            '
            select t.ra_request_type_id as ra_request_type_id,
                    t.type as request_type,
                    i.status as status,
                    p.name as name,
                    p.brand as brand,
                    p.team as team,
                    pti.project_id as project_id,
                    pti.id as task_id,
                    t.author_id as author_id,
                    concat(u.first_name, " ", u.last_name) as author_name,
                    concat(uu.first_name, " ", uu.last_name) as assignee_name,
                    t.due_date as due_date,
                    t.due_date_revision as due_date_revision,
                    t.revision_cnt as revision_cnt,
                    t.registration_due_date as registration_due_date,
                    t.registration_number as registration_number,
                    t.assignee as assignee
            from sub_ra_request_index i
            left join sub_ra_request_type t on t.ra_request_type_id = i.id
            left join project_task_index pti on pti.id = i.task_id
            left join project p on p.id = pti.project_id
            left join users u on u.id = t.author_id
            left join users uu on uu.id = t.assignee
            where i.status = "action_completed"
            and p.status = "active" 
                ' . $cur_user_filter . '
                ' . $filter_rt . '
                ' . $filter_a . '
                ' . $filter_t . '
                order by t.id desc   
            ');
    }

    public function get_sub_ra_request_by_ra_request_type_id($task_id)
    {
        $rs = SubRaRequestType::where('ra_request_type_id', $task_id)->first();
        return $rs;
    }

}
