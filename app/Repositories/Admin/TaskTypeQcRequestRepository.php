<?php

namespace App\Repositories\Admin;

use App\Repositories\Admin\Interfaces\TaskTypeQcRequestRepositoryInterface;
use DB;

use App\Models\TaskTypeQcRequest;
use Illuminate\Database\Eloquent\Model;

class TaskTypeQcRequestRepository implements TaskTypeQcRequestRepositoryInterface
{
    public function findAll($options = [])
    {
        $perPage = $options['per_page'] ?? null;
        $orderByFields = $options['order'] ?? [];
        $id = $options['id'] ?? [];

        $taskTypeQcRequest = new TaskTypeQcRequest();

        if ($id) {
            $taskTypeQcRequest = $taskTypeQcRequest
                ->where('id', $id)->where('task_id', 0);
        }

        $taskTypeQcRequest = $taskTypeQcRequest->get();

        return $taskTypeQcRequest;
    }

    public function findAllByTaskId($task_id)
    {
        $taskTypeQcRequest = new TaskTypeQcRequest();
        return $taskTypeQcRequest->where('task_id', $task_id)->get();
    }

    public function deleteByTaskId($task_id)
    {
        $taskTypeQcRequest = new TaskTypeQcRequest();
        return $taskTypeQcRequest->where('task_id', $task_id)->delete();
    }

    public function findById($id)
    {
        return TaskTypeQcRequest::findOrFail($id);
    }

    public function create($params = [])
    {
        return DB::transaction(function () use ($params) {
            $taskTypeQcRequest = TaskTypeQcRequest::create($params);
            $this->syncRolesAndPermissions($params, $taskTypeQcRequest);

            return $taskTypeQcRequest;
        });
    }

    public function update($id, $params = [])
    {
        $taskTypeQcRequest = TaskTypeQcRequest::findOrFail($id);

        return DB::transaction(function () use ($params, $taskTypeQcRequest) {
            $taskTypeQcRequest->update($params);

            return $taskTypeQcRequest;
        });
    }

    public function delete($id)
    {
        $taskTypeQcRequest  = TaskTypeQcRequest::findOrFail($id);

        return $taskTypeQcRequest->delete();
    }

    public function get_action_requested_list($cur_user, $team, $brand, $material, $vendor_code)
    {
        if($cur_user != '') {
            $cur_user_filter = $cur_user;
        }else{
            $cur_user_filter = ' ';
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

        if($material != '') {
            $material_filter = ' and t.materials like "%' . $material . '%" ';
        }else{
            $material_filter = ' ';
        }

        if($vendor_code != '') {
            $vendor_code_filter = ' and t.vendor_code like "%' . $vendor_code . '%" ';
        }else{
            $vendor_code_filter = ' ';
        }

        return DB::select(
            '
            select t.id as project_id,
                t.task_id as task_id,
                t.work_type as work_type,   
                t.ship_date as ship_date,
                t.qc_date as qc_date,
                t.po as po,
                t.po_usd as po_usd,
                t.materials as materials,
                t.item_type as item_type,
                t.vendor_code as vendor_code,
                t.country as country,
                t.vendor_primary_contact_name as vendor_primary_contact_name,
                t.vendor_primary_contact_email as vendor_primary_contact_email,
                t.vendor_primary_contact_phone as vendor_primary_contact_phone,
                t.facility_address as facility_address,
                t.performed_by as performed_by,
                t.result as result,   
                i.author_id as author_id,
                i.status as status,
                i.created_at as created_at,
                p.team as team,
                p.brand as brand,
                p.name as name,
                concat(u.first_name, " ", u.last_name) as author_name
            from project_task_index i
            left join project p on p.id = i.project_id
            left join task_type_qc_request t on t.task_id = i.id
            left join users u on u.id = t.author_id
            where i.type = "qc_request"
                and p.status = "active"
                ' . $cur_user_filter . '
              ' . $team_filter . '
              ' . $brand_filter . '
              ' . $material_filter . '
              ' . $vendor_code_filter . '
                and	i.status = "action_requested"
                order by t.qc_date asc
            ');
    }

    public function get_in_progress_list($cur_user, $team, $brand, $material, $vendor_code)
    {
        if($cur_user != '') {
            $cur_user_filter = $cur_user;
        }else{
            $cur_user_filter = ' ';
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

        if($material != '') {
            $material_filter = ' and t.materials like "%' . $material . '%" ';
        }else{
            $material_filter = ' ';
        }

        if($vendor_code != '') {
            $vendor_code_filter = ' and t.vendor_code like "%' . $vendor_code . '%" ';
        }else{
            $vendor_code_filter = ' ';
        }

        return DB::select(
            '
            select t.id as project_id,
                t.task_id as task_id,
                t.work_type as work_type,    
                t.ship_date as ship_date,
                t.qc_date as qc_date,
                t.po as po,
                t.po_usd as po_usd,
                t.materials as materials,
                t.item_type as item_type,
                t.vendor_code as vendor_code,
                t.country as country,
                t.vendor_primary_contact_name as vendor_primary_contact_name,
                t.vendor_primary_contact_email as vendor_primary_contact_email,
                t.vendor_primary_contact_phone as vendor_primary_contact_phone,
                t.facility_address as facility_address,
                t.performed_by as performed_by,
                   t.result as result,
                i.author_id as author_id,
                i.status as status,
                i.created_at as created_at,
                p.team as team,
                p.brand as brand,
                p.name as name,
                concat(u.first_name, " ", u.last_name) as author_name
            from project_task_index i
            left join project p on p.id = i.project_id
            left join task_type_qc_request t on t.task_id = i.id
            left join users u on u.id = t.author_id
            where i.type = "qc_request"
                and p.status = "active"
                ' . $cur_user_filter . '
              ' . $team_filter . '
              ' . $brand_filter . '
              ' . $material_filter . '
              ' . $vendor_code_filter . '
                and	i.status = "in_progress"
                order by t.qc_date asc
            ');
    }

    public function get_action_review_list($cur_user, $team, $brand, $material, $vendor_code)
    {
        if($cur_user != '') {
            $cur_user_filter = $cur_user;
        }else{
            $cur_user_filter = ' ';
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

        if($material != '') {
            $material_filter = ' and t.materials like "%' . $material . '%" ';
        }else{
            $material_filter = ' ';
        }

        if($vendor_code != '') {
            $vendor_code_filter = ' and t.vendor_code like "%' . $vendor_code . '%" ';
        }else{
            $vendor_code_filter = ' ';
        }

        return DB::select(
            '
            select t.id as project_id,
                t.task_id as task_id,
                t.work_type as work_type,   
                t.ship_date as ship_date,
                t.qc_date as qc_date,
                t.po as po,
                t.po_usd as po_usd,
                t.materials as materials,
                t.item_type as item_type,
                t.vendor_code as vendor_code,
                t.country as country,
                t.vendor_primary_contact_name as vendor_primary_contact_name,
                t.vendor_primary_contact_email as vendor_primary_contact_email,
                t.vendor_primary_contact_phone as vendor_primary_contact_phone,
                t.facility_address as facility_address,
                t.performed_by as performed_by,
                   t.result as result,
                i.author_id as author_id,
                i.status as status,
                i.created_at as created_at,
                p.team as team,
                p.brand as brand,
                p.name as name,
                concat(u.first_name, " ", u.last_name) as author_name
            from project_task_index i
            left join project p on p.id = i.project_id
            left join task_type_qc_request t on t.task_id = i.id
            left join users u on u.id = t.author_id
            where i.type = "qc_request"
                and p.status = "active"
                ' . $cur_user_filter . '
              ' . $team_filter . '
              ' . $brand_filter . '
              ' . $material_filter . '
              ' . $vendor_code_filter . '
                and	i.status = "action_review"
                order by t.qc_date asc
            ');
    }

    public function get_action_completed_list($cur_user, $team, $brand, $material, $vendor_code)
    {
        if($cur_user != '') {
            $cur_user_filter = $cur_user;
        }else{
            $cur_user_filter = ' ';
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

        if($material != '') {
            $material_filter = ' and t.materials like "%' . $material . '%" ';
        }else{
            $material_filter = ' ';
        }

        if($vendor_code != '') {
            $vendor_code_filter = ' and t.vendor_code like "%' . $vendor_code . '%" ';
        }else{
            $vendor_code_filter = ' ';
        }

        return DB::select(
            '
            select t.id as project_id,
                t.task_id as task_id,
                t.work_type as work_type,   
                t.ship_date as ship_date,
                t.qc_date as qc_date,
                t.po as po,
                t.po_usd as po_usd,
                t.materials as materials,
                t.item_type as item_type,
                t.vendor_code as vendor_code,
                t.country as country,
                t.vendor_primary_contact_name as vendor_primary_contact_name,
                t.vendor_primary_contact_email as vendor_primary_contact_email,
                t.vendor_primary_contact_phone as vendor_primary_contact_phone,
                t.facility_address as facility_address,
                t.performed_by as performed_by,
                   t.result as result,
                i.author_id as author_id,
                i.status as status,
                i.created_at as created_at,
                p.team as team,
                p.brand as brand,
                p.name as name,
                concat(u.first_name, " ", u.last_name) as author_name
            from project_task_index i
            left join project p on p.id = i.project_id
            left join task_type_qc_request t on t.task_id = i.id
            left join users u on u.id = t.author_id
            where i.type = "qc_request"
                and p.status = "active"
                ' . $cur_user_filter . '
              ' . $team_filter . '
              ' . $brand_filter . '
              ' . $material_filter . '
              ' . $vendor_code_filter . '
                and	i.status = "action_completed"
                order by t.qc_date asc
            ');
    }
}
