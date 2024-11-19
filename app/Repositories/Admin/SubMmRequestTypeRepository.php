<?php

namespace App\Repositories\Admin;

use App\Models\ProjectTypeTaskAttachments;
use App\Models\SubLegalRequestType;
use App\Models\SubMmRequestType;
use App\Models\SubQraRequestType;
use App\Models\TaskTypeLegalRequest;
use App\Repositories\Admin\Interfaces\SubLegalRequestIndexRepositoryInterface;
use App\Repositories\Admin\Interfaces\SubMmRequestIndexRepositoryInterface;
use App\Repositories\Admin\Interfaces\SubMmRequestTypeRepositoryInterface;
use App\Repositories\Admin\Interfaces\SubQraRequestTypeRepositoryInterface;
use App\Repositories\Admin\Interfaces\TaskTypeLegalRequestRepositoryInterface;
use DB;

use Illuminate\Database\Eloquent\Model;

class SubMmRequestTypeRepository implements SubMmRequestTypeRepositoryInterface
{
    public function findAll($options = [])
    {
        $perPage = $options['per_page'] ?? null;
        $orderByFields = $options['order'] ?? [];
        $id = $options['id'] ?? [];

        $subMmRequestType = new SubMmRequestType();

        if ($id) {
            $subMmRequestType = $subMmRequestType
                ->where('id', $id)->where('task_id', 0);
        }

        $subMmRequestType = $subMmRequestType->get();

        return $subMmRequestType;
    }

    public function findAllByRequestTypeId($request_type_id)
    {
        $subMmRequestType = new SubMmRequestType();
        return $subMmRequestType->where('request_type_id', $request_type_id)->first();
    }

    public function deleteByTaskId($task_id)
    {
        $subMmRequestType = new SubMmRequestType();
        return $subMmRequestType->where('task_id', $task_id)->delete();
    }

    public function findById($id)
    {
        return SubMmRequestType::findOrFail($id);
    }

    public function create($params = [])
    {
        return DB::transaction(function () use ($params) {
            $subMmRequestType = SubMmRequestType::create($params);
            $this->syncRolesAndPermissions($params, $subMmRequestType);

            return $subMmRequestType;
        });
    }

    public function update($id, $params = [])
    {
        $subMmRequestType = SubMmRequestType::findOrFail($id);

        return DB::transaction(function () use ($params, $subMmRequestType) {
            $subMmRequestType->update($params);

            return $subMmRequestType;
        });
    }

    public function delete($id)
    {
        $subMmRequestType  = SubMmRequestType::findOrFail($id);

        return $subMmRequestType->delete();
    }

    public function get_action_requested_list($request_type, $assignee)
    {
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
                        concat(u.first_name, " ", u.last_name) as author_name,
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
                ' . $filter_rt . '
                ' . $filter_a . '
                order by t.id desc   
            ');
    }

    public function get_in_progress_list($request_type, $assignee)
    {
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
                        concat(u.first_name, " ", u.last_name) as author_name,
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
                ' . $filter_rt . '
                ' . $filter_a . '
                order by t.id desc   
            ');
    }

    public function get_action_review_list($request_type, $assignee)
    {
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
                        concat(u.first_name, " ", u.last_name) as author_name,
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
                ' . $filter_rt . '
                ' . $filter_a . '
                order by t.id desc   
            ');
    }

    public function get_action_completed_list($request_type, $assignee)
    {
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
                        concat(u.first_name, " ", u.last_name) as author_name,
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
                ' . $filter_rt . '
                ' . $filter_a . '
                order by t.id desc   
            ');
    }

    public function get_sub_mm_request_by_mm_request_type_id($task_id)
    {
        $rs = SubMmRequestType::where('mm_request_type_id', $task_id)->first();
        return $rs;
    }

}
