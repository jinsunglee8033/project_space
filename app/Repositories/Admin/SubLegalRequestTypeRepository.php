<?php

namespace App\Repositories\Admin;

use App\Models\ProjectTypeTaskAttachments;
use App\Models\SubLegalRequestType;
use App\Models\SubQraRequestType;
use App\Models\TaskTypeLegalRequest;
use App\Repositories\Admin\Interfaces\SubLegalRequestIndexRepositoryInterface;
use App\Repositories\Admin\Interfaces\SubQraRequestTypeRepositoryInterface;
use App\Repositories\Admin\Interfaces\TaskTypeLegalRequestRepositoryInterface;
use DB;

use Illuminate\Database\Eloquent\Model;

class SubLegalRequestTypeRepository implements SubLegalRequestIndexRepositoryInterface
{
    public function findAll($options = [])
    {
        $perPage = $options['per_page'] ?? null;
        $orderByFields = $options['order'] ?? [];
        $id = $options['id'] ?? [];

        $subLegalRequestType = new SubLegalRequestType();

        if ($id) {
            $subLegalRequestType = $subLegalRequestType
                ->where('id', $id)->where('task_id', 0);
        }

        $subLegalRequestType = $subLegalRequestType->get();

        return $subLegalRequestType;
    }

    public function findAllByRequestTypeId($request_type_id)
    {
        $subLegalRequestType = new SubLegalRequestType();
        return $subLegalRequestType->where('request_type_id', $request_type_id)->first();
    }

    public function deleteByTaskId($task_id)
    {
        $subLegalRequestType = new SubLegalRequestType();
        return $subLegalRequestType->where('task_id', $task_id)->delete();
    }

    public function findById($id)
    {
        return SubLegalRequestType::findOrFail($id);
    }

    public function create($params = [])
    {
        return DB::transaction(function () use ($params) {
            $subLegalRequestType = SubLegalRequestType::create($params);
            $this->syncRolesAndPermissions($params, $subLegalRequestType);

            return $subLegalRequestType;
        });
    }

    public function update($id, $params = [])
    {
        $subLegalRequestType = SubLegalRequestType::findOrFail($id);

        return DB::transaction(function () use ($params, $subLegalRequestType) {
            $subLegalRequestType->update($params);

            return $subLegalRequestType;
        });
    }

    public function delete($id)
    {
        $subLegalRequestType  = SubLegalRequestType::findOrFail($id);

        return $subLegalRequestType->delete();
    }

    public function get_action_requested_list($cur_user, $request_type, $assignee)
    {
        if($cur_user != '') {
            $cur_user_filter = $cur_user;
        }else{
            $cur_user_filter = ' ';
        }

        if($request_type != '') {
            $filter_rt = ' and i.request_type ="' . $request_type . '" ';
        }else{
            $filter_rt = ' ';
        }

        if($assignee != '') {
            $filter_a = ' and t.assignee ="' . $assignee . '" ';
        }else{
            $filter_a = ' ';
        }

        return DB::select(
            '
            select  t.legal_request_type_id as legal_request_type_id,
                        p.name as name,
                        p.brand as brand,
                      pti.project_id as project_id,
                      pti.id as task_id,
                        t.type as request_type,
                        u.first_name as first_name,
                        u.last_name as last_name,
                        t.author_id as author_id,
                        p.team as team,
                        i.status as status,
                        t.legal_request_type_id as legal_request_type_id,
                        t.request_description as request_description,
                        t.description_of_goods as description_of_goods,
                        t.request_category as request_category,
                        t.trademark_owner as trademark_owner,
                        t.priority as priority,
                        t.vendor_code as vendor_code,
                        t.vendor_name as vendor_name,
                        t.vendor_location as vendor_location,
                        t.due_date as due_date,
                        t.due_date_urgent as due_date_urgent,
                        t.urgent_reason as urgent_reason,
                        t.due_date_revision as due_date_revision,
                        t.revision_cnt as revision_cnt,
                        t.target_region as target_region,
                        t.if_selected_others as if_selected_others,
                        t.created_at as created_at,
                        concat(u.first_name, " ", u.last_name) as author_name,
                        concat(uu.first_name, " ", uu.last_name) as assignee_name
                from sub_legal_request_index i
                left join sub_legal_request_type t on t.legal_request_type_id = i.id
                left join project_task_index pti on pti.id = i.task_id
                left join project p on p.id = pti.project_id    
                left join users u on u.id = t.author_id
                left join users uu on uu.id = t.assignee
                where i.status = "action_requested" 
                and p.status = "active"
                ' . $cur_user_filter . '
                ' . $filter_rt . '
                ' . $filter_a . '
                order by t.id desc   
            ');
    }

    public function get_in_progress_list($cur_user, $request_type, $assignee)
    {
        if($cur_user != '') {
            $cur_user_filter = $cur_user;
        }else{
            $cur_user_filter = ' ';
        }

        if($request_type != '') {
            $filter_rt = ' and i.request_type ="' . $request_type . '" ';
        }else{
            $filter_rt = ' ';
        }

        if($assignee != '') {
            $filter_a = ' and t.assignee ="' . $assignee . '" ';
        }else{
            $filter_a = ' ';
        }

        return DB::select(
            '
            select  t.legal_request_type_id as legal_request_type_id,
                        p.name as name,
                        p.brand as brand,
                      pti.project_id as project_id,
                      pti.id as task_id,
                        t.type as request_type,
                        u.first_name as first_name,
                        u.last_name as last_name,
                        t.author_id as author_id,
                        p.team as team,
                        i.status as status,
                        t.legal_request_type_id as legal_request_type_id,
                        t.request_description as request_description,
                        t.description_of_goods as description_of_goods,
                        t.request_category as request_category,
                        t.trademark_owner as trademark_owner,
                        t.priority as priority,
                        t.vendor_code as vendor_code,
                        t.vendor_name as vendor_name,
                        t.vendor_location as vendor_location,
                        t.due_date as due_date,
                        t.due_date_urgent as due_date_urgent,
                        t.urgent_reason as urgent_reason,
                        t.due_date_revision as due_date_revision,
                        t.revision_cnt as revision_cnt,
                        t.target_region as target_region,
                        t.if_selected_others as if_selected_others,
                        t.created_at as created_at,
                        concat(u.first_name, " ", u.last_name) as author_name,
                        concat(uu.first_name, " ", uu.last_name) as assignee_name
                from sub_legal_request_index i
                left join sub_legal_request_type t on t.legal_request_type_id = i.id
                left join project_task_index pti on pti.id = i.task_id
                left join project p on p.id = pti.project_id    
                left join users u on u.id = t.author_id
                left join users uu on uu.id = t.assignee
                where i.status = "in_progress" 
                and p.status = "active"
                ' . $cur_user_filter . '
                ' . $filter_rt . '
                ' . $filter_a . '
                order by t.id desc   
            ');
    }

    public function get_action_review_list($cur_user, $request_type, $assignee)
    {
        if($cur_user != '') {
            $cur_user_filter = $cur_user;
        }else{
            $cur_user_filter = ' ';
        }

        if($request_type != '') {
            $filter_rt = ' and i.request_type ="' . $request_type . '" ';
        }else{
            $filter_rt = ' ';
        }

        if($assignee != '') {
            $filter_a = ' and t.assignee ="' . $assignee . '" ';
        }else{
            $filter_a = ' ';
        }

        return DB::select(
            '
            select  t.legal_request_type_id as legal_request_type_id,
                        p.name as name,
                        p.brand as brand,
                      pti.project_id as project_id,
                      pti.id as task_id,
                        t.type as request_type,
                        u.first_name as first_name,
                        u.last_name as last_name,
                        t.author_id as author_id,
                        p.team as team,
                        i.status as status,
                        t.legal_request_type_id as legal_request_type_id,
                        t.request_description as request_description,
                        t.description_of_goods as description_of_goods,
                        t.request_category as request_category,
                        t.trademark_owner as trademark_owner,
                        t.priority as priority,
                        t.vendor_code as vendor_code,
                        t.vendor_name as vendor_name,
                        t.vendor_location as vendor_location,
                        t.due_date as due_date,
                        t.due_date_urgent as due_date_urgent,
                        t.urgent_reason as urgent_reason,
                        t.due_date_revision as due_date_revision,
                        t.revision_cnt as revision_cnt,
                        t.target_region as target_region,
                        t.if_selected_others as if_selected_others,
                        t.created_at as created_at,
                        concat(u.first_name, " ", u.last_name) as author_name,
                        concat(uu.first_name, " ", uu.last_name) as assignee_name
                from sub_legal_request_index i
                left join sub_legal_request_type t on t.legal_request_type_id = i.id
                left join project_task_index pti on pti.id = i.task_id
                left join project p on p.id = pti.project_id    
                left join users u on u.id = t.author_id
                left join users uu on uu.id = t.assignee
                where i.status = "action_review" 
                and p.status = "active"
                ' . $cur_user_filter . '
                ' . $filter_rt . '
                ' . $filter_a . '
                order by t.id desc   
            ');
    }

    public function get_action_completed_list($cur_user, $request_type, $assignee)
    {
        if($cur_user != '') {
            $cur_user_filter = $cur_user;
        }else{
            $cur_user_filter = ' ';
        }

        if($request_type != '') {
            $filter_rt = ' and i.request_type ="' . $request_type . '" ';
        }else{
            $filter_rt = ' ';
        }

        if($assignee != '') {
            $filter_a = ' and t.assignee ="' . $assignee . '" ';
        }else{
            $filter_a = ' ';
        }

        return DB::select(
            '
            select  t.legal_request_type_id as legal_request_type_id,
                        p.name as name,
                        p.brand as brand,
                      pti.project_id as project_id,
                      pti.id as task_id,
                        t.type as request_type,
                        u.first_name as first_name,
                        u.last_name as last_name,
                        t.author_id as author_id,
                        p.team as team,
                        i.status as status,
                        t.legal_request_type_id as legal_request_type_id,
                        t.request_description as request_description,
                        t.description_of_goods as description_of_goods,
                        t.request_category as request_category,
                        t.trademark_owner as trademark_owner,
                        t.priority as priority,
                        t.vendor_code as vendor_code,
                        t.vendor_name as vendor_name,
                        t.vendor_location as vendor_location,
                        t.due_date as due_date,
                        t.due_date_urgent as due_date_urgent,
                        t.urgent_reason as urgent_reason,
                        t.due_date_revision as due_date_revision,
                        t.revision_cnt as revision_cnt,
                        t.target_region as target_region,
                        t.if_selected_others as if_selected_others,
                        t.created_at as created_at,
                        concat(u.first_name, " ", u.last_name) as author_name,
                        concat(uu.first_name, " ", uu.last_name) as assignee_name
                from sub_legal_request_index i
                left join sub_legal_request_type t on t.legal_request_type_id = i.id
                left join project_task_index pti on pti.id = i.task_id
                left join project p on p.id = pti.project_id    
                left join users u on u.id = t.author_id
                left join users uu on uu.id = t.assignee
                where i.status = "action_completed" 
                and p.status = "active"
                ' . $cur_user_filter . '
                ' . $filter_rt . '
                ' . $filter_a . '
                order by t.id desc   
            ');
    }

    public function get_sub_legal_request_by_legal_request_type_id($task_id)
    {
        $rs = SubLegalRequestType::where('legal_request_type_id', $task_id)->first();
        return $rs;
    }

}
