<?php

namespace App\Repositories\Admin;

use App\Models\ProjectTaskIndex;
use App\Models\ProjectTypeTaskAttachments;
use App\Models\TaskTypeProductReceiving;
use App\Repositories\Admin\Interfaces\TypeProductReceivingRepositoryInterface;
use App\Repositories\Admin\Interfaces\TaskTypeProductReceivingRepositoryInterface;
use DB;

use Illuminate\Database\Eloquent\Model;

class TaskTypeProductReceivingRepository implements TaskTypeProductReceivingRepositoryInterface
{
    public function findAll($options = [])
    {
        $perPage = $options['per_page'] ?? null;
        $orderByFields = $options['order'] ?? [];
        $id = $options['id'] ?? [];

        $taskTypeProductReceiving = new TaskTypeProductReceiving();

        if ($id) {
            $taskTypeProductReceiving = $taskTypeProductReceiving
                ->where('id', $id)->where('task_id', 0);
        }

        $taskTypeProductReceiving = $taskTypeProductReceiving->get();

        return $taskTypeProductReceiving;
    }

    public function findAllByTaskId($task_id)
    {
        $taskTypeProductReceiving = new TaskTypeProductReceiving();
        return $taskTypeProductReceiving->where('task_id', $task_id)->get();
    }

    public function deleteByTaskId($task_id)
    {
        $taskTypeProductReceiving = new TaskTypeProductReceiving();
        return $taskTypeProductReceiving->where('task_id', $task_id)->delete();
    }

    public function findById($id)
    {
        return TaskTypeProductReceiving::findOrFail($id);
    }

    public function create($params = [])
    {
        return DB::transaction(function () use ($params) {
            $taskTypeProductReceiving = TaskTypeProductReceiving::create($params);
            $this->syncRolesAndPermissions($params, $taskTypeProductReceiving);

            return $taskTypeProductReceiving;
        });
    }

    public function update($id, $params = [])
    {
        $taskTypeProductReceiving = TaskTypeProductReceiving::findOrFail($id);

        return DB::transaction(function () use ($params, $taskTypeProductReceiving) {
            $taskTypeProductReceiving->update($params);

            return $taskTypeProductReceiving;
        });
    }

    public function delete($id)
    {
        $taskTypeProductReceiving  = TaskTypeProductReceiving::findOrFail($id);

        return $taskTypeProductReceiving->delete();
    }

    public function get_task_id_for_product_receiving($project_id)
    {
        $pti_obj = ProjectTaskIndex::where('project_id', $project_id)
            ->where('type', 'product_receiving')->first();
        return $pti_obj;
    }

    public function get_product_receiving_list_by_task_id($task_id)
    {
        return DB::select('
            select i.status as status,
                    i.project_id as project_id,
                    i.author_id as author_id,
                    i.type as type,
                    u.first_name as first_name,
                    u.last_name as last_name,
                    t.po as po,
                    t.materials as materials,
                    t.qir_status as qir_status,
                    t.division as division,
                    t.qir_action as qir_action,
                    t.vendor_code as vendor_code,
                    t.vendor_name as vendor_name,
                    t.cost_center as cost_center,
                    t.location as location,
                    t.primary_contact as primary_contact,
                    t.related_team_contact as related_team_contact,
                    t.year as year,
                    t.received_qty as received_qty,
                    t.inspection_qty as inspection_qty,
                    t.defect_qty as defect_qty,
                    t.blocked_qty as blocked_qty,
                    t.blocked_rate as blocked_rate,
                    t.batch as batch,
                    t.item_net_cost as item_net_cost,
                    t.defect_area as defect_area,
                    t.defect_type as defect_type,
                    t.defect_details as defect_details,
                    t.defect_cost as defect_cost,
                    t.full_cost as full_cost,
                    t.rework_cost as rework_cost,
                    t.rsr_id as rsr_id,
                    t.special_inspection_cost as special_inspection_cost,
                    t.processing_date as processing_date,
                    t.aging_days as aging_days,
                    t.capa as capa,
                    t.total_claim as total_claim,
                    t.actual_cm_total as actual_cm_total,
                    t.claim_status as claim_status,
                    t.override_authorized_by as override_authorized_by,
					t.waived_amount as waived_amount,
                    t.settlement_total as settlement_total,
					t.settlement_type as settlement_type,
                    t.task_id as task_id,
                    t.created_at as created_at
            from project_task_index i
            left join task_type_product_receiving t on t.task_id =  i.id
            left join users u on u.id = t.author_id
            where i.id =:task_id order by t.id desc', [
            'task_id' => $task_id
        ]);
    }

    public function get_action_requested_list($team)
    {
        if($team != '') {
            $team_filter = ' and p.team ="' . $team . '" ';
        }else{
            $team_filter = ' ';
        }

        return DB::select(
            'select t.po as po,
                t.materials as materials,
                t.qir_status as qir_status,
                t.division as division,
                t.qir_action as qir_action,
                t.vendor_code as vendor_code,
                t.vendor_name as vendor_name,
                t.cost_center as cost_center,
                t.location as location,
                t.primary_contact as primary_contact,
                t.related_team_contact as related_team_contact,
                t.year as year,
                t.received_qty as received_qty,
                t.inspection_qty as inspection_qty,
                t.defect_qty as defect_qty,
                t.blocked_qty as blocked_qty,
                t.blocked_rate as blocked_rate,
                t.batch as batch,
                t.item_net_cost as item_net_cost,
                t.defect_area as defect_area,
                t.defect_type as defect_type,
                t.defect_details as defect_details,
                t.defect_cost as defect_cost,
                t.full_cost as full_cost,
                t.rework_cost as rework_cost,
                t.rsr_id as rsr_id,
                t.special_inspection_cost as special_inspection_cost,
                t.processing_date as processing_date,
                t.aging_days as aging_days,
                t.capa as capa,
                t.total_claim as total_claim,
                t.actual_cm_total as actual_cm_total,
                t.claim_status as claim_status,
                t.override_authorized_by as override_authorized_by,
                t.waived_amount as waived_amount,
                t.settlement_total as settlement_total,
                t.settlement_type as settlement_type,
                i.author_id as author_id,
                i.status as status,
                i.created_at as created_at,
                i.id as task_id,
                p.id as project_id,
                p.team as team,
                p.brand as brand,
                p.name as name,
                concat(u.first_name, " ", u.last_name) as author_name
            from project_task_index i
            left join project p on p.id = i.project_id
            left join task_type_product_receiving t on t.task_id = i.id
            left join users u on u.id = t.author_id
            where i.type = "product_receiving"
                and p.status = "active"
              ' . $team_filter . '
                and	i.status = "action_requested"
                order by i.created_at asc
            ');
    }

    public function get_in_progress_list($team)
    {
        if($team != '') {
            $team_filter = ' and p.team ="' . $team . '" ';
        }else{
            $team_filter = ' ';
        }

        return DB::select(
            '
            select t.po as po,
                t.materials as materials,
                t.qir_status as qir_status,
                t.division as division,
                t.qir_action as qir_action,
                t.vendor_code as vendor_code,
                t.vendor_name as vendor_name,
                t.cost_center as cost_center,
                t.location as location,
                t.primary_contact as primary_contact,
                t.related_team_contact as related_team_contact,
                t.year as year,
                t.received_qty as received_qty,
                t.inspection_qty as inspection_qty,
                t.defect_qty as defect_qty,
                t.blocked_qty as blocked_qty,
                t.blocked_rate as blocked_rate,
                t.batch as batch,
                t.item_net_cost as item_net_cost,
                t.defect_area as defect_area,
                t.defect_type as defect_type,
                t.defect_details as defect_details,
                t.defect_cost as defect_cost,
                t.full_cost as full_cost,
                t.rework_cost as rework_cost,
                t.rsr_id as rsr_id,
                t.special_inspection_cost as special_inspection_cost,
                t.processing_date as processing_date,
                t.aging_days as aging_days,
                t.capa as capa,
                t.total_claim as total_claim,
                t.actual_cm_total as actual_cm_total,
                t.claim_status as claim_status,
                t.override_authorized_by as override_authorized_by,
                t.waived_amount as waived_amount,
                t.settlement_total as settlement_total,
                t.settlement_type as settlement_type,
                i.author_id as author_id,
                i.status as status,
                i.created_at as created_at,
                i.id as task_id,
                p.id as project_id,
                p.team as team,
                p.brand as brand,
                p.name as name,
                concat(u.first_name, " ", u.last_name) as author_name
            from project_task_index i
            left join project p on p.id = i.project_id
            left join task_type_product_receiving t on t.task_id = i.id
            left join users u on u.id = t.author_id
            where i.type = "product_receiving"
                and p.status = "active"
              ' . $team_filter . '
                and	i.status = "in_progress"
                order by i.created_at asc
            ');
    }

    public function get_action_review_list($team)
    {
        if($team != '') {
            $team_filter = ' and p.team ="' . $team . '" ';
        }else{
            $team_filter = ' ';
        }

        return DB::select(
            '
            select t.po as po,
                t.materials as materials,
                t.qir_status as qir_status,
                t.division as division,
                t.qir_action as qir_action,
                t.vendor_code as vendor_code,
                t.vendor_name as vendor_name,
                t.cost_center as cost_center,
                t.location as location,
                t.primary_contact as primary_contact,
                t.related_team_contact as related_team_contact,
                t.year as year,
                t.received_qty as received_qty,
                t.inspection_qty as inspection_qty,
                t.defect_qty as defect_qty,
                t.blocked_qty as blocked_qty,
                t.blocked_rate as blocked_rate,
                t.batch as batch,
                t.item_net_cost as item_net_cost,
                t.defect_area as defect_area,
                t.defect_type as defect_type,
                t.defect_details as defect_details,
                t.defect_cost as defect_cost,
                t.full_cost as full_cost,
                t.rework_cost as rework_cost,
                t.rsr_id as rsr_id,
                t.special_inspection_cost as special_inspection_cost,
                t.processing_date as processing_date,
                t.aging_days as aging_days,
                t.capa as capa,
                t.total_claim as total_claim,
                t.actual_cm_total as actual_cm_total,
                t.claim_status as claim_status,
                t.override_authorized_by as override_authorized_by,
                t.waived_amount as waived_amount,
                t.settlement_total as settlement_total,
                t.settlement_type as settlement_type,
                i.author_id as author_id,
                i.status as status,
                i.created_at as created_at,
                i.id as task_id,
                p.id as project_id,
                p.team as team,
                p.brand as brand,
                p.name as name,
                concat(u.first_name, " ", u.last_name) as author_name
            from project_task_index i
            left join project p on p.id = i.project_id
            left join task_type_product_receiving t on t.task_id = i.id
            left join users u on u.id = t.author_id
            where i.type = "product_receiving"
                and p.status = "active"
              ' . $team_filter . '
                and	i.status = "action_review"
                order by i.created_at asc
            ');
    }

    public function get_action_completed_list($team)
    {
        if($team != '') {
            $team_filter = ' and p.team ="' . $team . '" ';
        }else{
            $team_filter = ' ';
        }

        return DB::select(
            '
            select t.po as po,
                t.materials as materials,
                t.qir_status as qir_status,
                t.division as division,
                t.qir_action as qir_action,
                t.vendor_code as vendor_code,
                t.vendor_name as vendor_name,
                t.cost_center as cost_center,
                t.location as location,
                t.primary_contact as primary_contact,
                t.related_team_contact as related_team_contact,
                t.year as year,
                t.received_qty as received_qty,
                t.inspection_qty as inspection_qty,
                t.defect_qty as defect_qty,
                t.blocked_qty as blocked_qty,
                t.blocked_rate as blocked_rate,
                t.batch as batch,
                t.item_net_cost as item_net_cost,
                t.defect_area as defect_area,
                t.defect_type as defect_type,
                t.defect_details as defect_details,
                t.defect_cost as defect_cost,
                t.full_cost as full_cost,
                t.rework_cost as rework_cost,
                t.rsr_id as rsr_id,
                t.special_inspection_cost as special_inspection_cost,
                t.processing_date as processing_date,
                t.aging_days as aging_days,
                t.capa as capa,
                t.total_claim as total_claim,
                t.actual_cm_total as actual_cm_total,
                t.claim_status as claim_status,
                t.override_authorized_by as override_authorized_by,
                t.waived_amount as waived_amount,
                t.settlement_total as settlement_total,
                t.settlement_type as settlement_type,
                i.author_id as author_id,
                i.status as status,
                i.created_at as created_at,
                i.id as task_id,
                p.id as project_id,
                p.team as team,
                p.brand as brand,
                p.name as name,
                concat(u.first_name, " ", u.last_name) as author_name
            from project_task_index i
            left join project p on p.id = i.project_id
            left join task_type_product_receiving t on t.task_id = i.id
            left join users u on u.id = t.author_id
            where i.type = "product_receiving"
                and p.status = "active"
              ' . $team_filter . '
                and	i.status = "action_completed"
                order by i.created_at asc
            ');
    }
}
