<?php

namespace App\Repositories\Admin;

use App\Models\ProjectTypeTaskAttachments;
use App\Models\SubLegalRequestType;
use App\Models\SubPeRequestType;
use App\Models\SubQraRequestType;
use App\Models\TaskTypeDisplayRequest;
use App\Models\TaskTypeLegalRequest;
use App\Repositories\Admin\Interfaces\SubLegalRequestIndexRepositoryInterface;
use App\Repositories\Admin\Interfaces\SubPeRequestIndexRepositoryInterface;
use App\Repositories\Admin\Interfaces\SubQraRequestTypeRepositoryInterface;
use App\Repositories\Admin\Interfaces\TaskTypeLegalRequestRepositoryInterface;
use DB;

use Illuminate\Database\Eloquent\Model;

class SubPeRequestTypeRepository implements SubPeRequestIndexRepositoryInterface
{
    public function findAll($options = [])
    {
        $perPage = $options['per_page'] ?? null;
        $orderByFields = $options['order'] ?? [];
        $id = $options['id'] ?? [];

        $subPeRequestType = new SubPeRequestType();

        if ($id) {
            $subPeRequestType = $subPeRequestType
                ->where('id', $id)->where('task_id', 0);
        }

        $subPeRequestType = $subPeRequestType->get();

        return $subPeRequestType;
    }

    public function findAllByRequestTypeId($request_type_id)
    {
        $subPeRequestType = new SubPeRequestType();
        return $subPeRequestType->where('request_type_id', $request_type_id)->first();
    }

    public function deleteByTaskId($task_id)
    {
        $subPeRequestType = new SubPeRequestType();
        return $subPeRequestType->where('task_id', $task_id)->delete();
    }

    public function findById($id)
    {
        return SubPeRequestType::findOrFail($id);
    }

    public function create($params = [])
    {
        return DB::transaction(function () use ($params) {
            $subPeRequestType = SubPeRequestType::create($params);
            $this->syncRolesAndPermissions($params, $subPeRequestType);

            return $subPeRequestType;
        });
    }

    public function update($id, $params = [])
    {
        $subPeRequestType = SubPeRequestType::findOrFail($id);

        return DB::transaction(function () use ($params, $subPeRequestType) {
            $subPeRequestType->update($params);

            return $subPeRequestType;
        });
    }

    public function delete($id)
    {
        $subPeRequestType  = SubPeRequestType::findOrFail($id);

        return $subPeRequestType->delete();
    }

    public function get_assign_list($cur_user, $request_type, $team)
    {
        if($cur_user != '') {
            $cur_user_filter = $cur_user;
        }else{
            $cur_user_filter = ' ';
        }
        if($request_type != '') {
            $filter = ' and detail.request_type ="' . $request_type . '" ';
        }else{
            $filter = ' ';
        }
        if($team != '') {
            $team_filter = ' and p.team ="' . $team . '" ';
        }else{
            $team_filter = ' ';
        }

        return DB::select(
            '
            select p.id as project_id,
                    p.team as team,
                    p.name as name,
                    p.brand as brand,
                    detail.author_id as author_id,
                    concat(pu.first_name, " ", pu.last_name) as author_name,
                    detail.status as status,
                    detail.request_type as request_type,
                    detail.created_at as sub_task_created_at,
                    rt.due_date as due_date,
                    rt.revision_cnt as revision_cnt,
                    rt.due_date_revision as due_date_revision,
                    rt.pe_request_type_id as pe_request_type_id,
                    rt.assignee as assignee,
                    rt.account as account,
                    rt.task_category as task_category,
                    concat(pe.first_name, " ", pe.last_name) as assignee_name,
                    rt.created_at as created_at
              from sub_pe_request_index detail
              left join project_task_index i on i.id = detail.task_id
              left join project p on p.id = i.project_id    
              left join sub_pe_request_type rt on rt.pe_request_type_id = detail.id
              left join users pu on pu.id = detail.author_id
              left join users pe on pe.id = rt.assignee
            where p.status = "active"
                and rt.assignee is null
            ' . $cur_user_filter . '
              ' . $filter . '
              ' . $team_filter . '
                and detail.status = "action_requested"
             order by detail.created_at desc   

            ');
    }

    public function get_action_requested_list($cur_user, $request_type, $assignee, $team)
    {
        if($cur_user != '') {
            $cur_user_filter = $cur_user;
        }else{
            $cur_user_filter = ' ';
        }
        if($request_type != '') {
            $filter = ' and detail.request_type ="' . $request_type . '" ';
        }else{
            $filter = ' ';
        }
        if($assignee != '') {
            $assignee_filter = ' and rt.assignee ="' . $assignee . '" ';
        }else{
            $assignee_filter = ' ';
        }
        if($team != '') {
            $team_filter = ' and p.team ="' . $team . '" ';
        }else{
            $team_filter = ' ';
        }

        return DB::select(
            '
            select p.id as project_id,
                    p.team as team,
                    p.name as name,
                    p.brand as brand,
                    detail.author_id as author_id,
                    concat(pu.first_name, " ", pu.last_name) as author_name,
                    detail.status as status,
                    detail.request_type as request_type,
                    detail.created_at as sub_task_created_at,
                    rt.due_date as due_date,
                    rt.revision_cnt as revision_cnt,
                    rt.due_date_revision as due_date_revision,
                    rt.pe_request_type_id as pe_request_type_id,
                    rt.assignee as assignee,
                    rt.account as account,
                    rt.task_category as task_category,
                    concat(pe.first_name, " ", pe.last_name) as assignee_name,
                    rt.created_at as created_at
              from sub_pe_request_index detail
              left join project_task_index i on i.id = detail.task_id
              left join project p on p.id = i.project_id    
              left join sub_pe_request_type rt on rt.pe_request_type_id = detail.id
              left join users pu on pu.id = detail.author_id
              left join users pe on pe.id = rt.assignee
            where p.status = "active"
                and rt.assignee is not null
            ' . $cur_user_filter . '
              ' . $filter . '
              ' . $assignee_filter . '
              ' . $team_filter . '
                and detail.status = "action_requested"
             order by detail.created_at desc   

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
            $filter = ' and sp.request_type ="' . $request_type . '" ';
        }else{
            $filter = ' ';
        }

        if($assignee != '') {
            $assignee_filter = ' and rt.assignee ="' . $assignee . '" ';
        }else{
            $assignee_filter = ' ';
        }
        if($team != '') {
            $team_filter = ' and p.team ="' . $team . '" ';
        }else{
            $team_filter = ' ';
        }
        return DB::select(
            '
            select p.id as project_id,
                    p.team as team,
                    p.name as name,
                   p.brand as brand,
                    sp.author_id as author_id,
                    concat(pu.first_name, " ", pu.last_name) as author_name,
                    sp.status as status,
                    sp.request_type as request_type,
                    sp.created_at as sub_task_created_at,
                    rt.due_date as due_date,
                    rt.revision_cnt as revision_cnt,
                    rt.due_date_revision as due_date_revision,
                    rt.pe_request_type_id as pe_request_type_id,
                    rt.assignee as assignee,
                   rt.account as account,
                    rt.task_category as task_category,
                    concat(pe.first_name, " ", pe.last_name) as assignee_name
              from sub_pe_request_index sp
              left join project_task_index i on i.id = sp.task_id
              left join project p on p.id = i.project_id
              left join sub_pe_request_type rt on rt.pe_request_type_id = sp.id
              left join users pu on pu.id = sp.author_id
              left join users pe on pe.id =  rt.assignee
            where p.status = "active"
            ' . $cur_user_filter . '
              ' . $filter . '
              ' . $assignee_filter . '
              ' . $team_filter . '
                and sp.status = "in_progress"
             order by sp.created_at desc   

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
            $filter = ' and sp.request_type ="' . $request_type . '" ';
        }else{
            $filter = ' ';
        }

        if($assignee != '') {
            $assignee_filter = ' and rt.assignee ="' . $assignee . '" ';
        }else{
            $assignee_filter = ' ';
        }
        if($team != '') {
            $team_filter = ' and p.team ="' . $team . '" ';
        }else{
            $team_filter = ' ';
        }
        return DB::select(
            '
            select p.id as project_id,
                    p.team as team,
                    p.name as name,
                   p.brand as brand,
                    sp.author_id as author_id,
                    concat(pu.first_name, " ", pu.last_name) as author_name,
                    sp.status as status,
                    sp.request_type as request_type,
                    sp.created_at as sub_task_created_at,
                    rt.due_date as due_date,
                    rt.revision_cnt as revision_cnt,
                    rt.due_date_revision as due_date_revision,
                    rt.pe_request_type_id as pe_request_type_id,
                    rt.assignee as assignee,
                   rt.account as account,
                    rt.task_category as task_category,
                    concat(pe.first_name, " ", pe.last_name) as assignee_name
              from sub_pe_request_index sp
              left join project_task_index i on i.id = sp.task_id
              left join project p on p.id = i.project_id
              left join sub_pe_request_type rt on rt.pe_request_type_id = sp.id
              left join users pu on pu.id = sp.author_id
              left join users pe on pe.id =  rt.assignee
            where p.status = "active"
            ' . $cur_user_filter . '
              ' . $filter . '
              ' . $assignee_filter . '
              ' . $team_filter . '
                and sp.status = "action_review"
             order by sp.created_at desc   

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
            $filter = ' and sp.request_type ="' . $request_type . '" ';
        }else{
            $filter = ' ';
        }
        if($assignee != '') {
            $assignee_filter = ' and rt.assignee ="' . $assignee . '" ';
        }else{
            $assignee_filter = ' ';
        }
        if($team != '') {
            $team_filter = ' and p.team ="' . $team . '" ';
        }else{
            $team_filter = ' ';
        }
        return DB::select(
            '
            select p.id as project_id,
                    p.team as team,
                    p.name as name,
                   p.brand as brand,
                    sp.author_id as author_id,
                    concat(pu.first_name, " ", pu.last_name) as author_name,
                    sp.status as status,
                    sp.request_type as request_type,
                    sp.created_at as sub_task_created_at,
                    rt.due_date as due_date,
                    rt.revision_cnt as revision_cnt,
                    rt.due_date_revision as due_date_revision,
                    rt.pe_request_type_id as pe_request_type_id,
                    rt.assignee as assignee,
                   rt.account as account,
                    rt.task_category as task_category,
                    concat(pe.first_name, " ", pe.last_name) as assignee_name
              from sub_pe_request_index sp
              left join project_task_index i on i.id = sp.task_id
              left join project p on p.id = i.project_id
              left join sub_pe_request_type rt on rt.pe_request_type_id = sp.id
              left join users pu on pu.id = sp.author_id
              left join users pe on pe.id =  rt.assignee
            where p.status = "active"
            ' . $cur_user_filter . '
              ' . $filter . '
              ' . $assignee_filter . '
              ' . $team_filter . '
                and sp.status = "action_completed"
             order by sp.created_at desc   

            ');
    }

    public function get_sub_pe_request_by_pe_request_type_id($task_id)
    {
        $rs = SubPeRequestType::where('pe_request_type_id', $task_id)->first();
        return $rs;
    }
}
