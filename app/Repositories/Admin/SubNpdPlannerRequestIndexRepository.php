<?php

namespace App\Repositories\Admin;

use App\Models\ProjectTaskIndex;
use App\Models\SubNpdDesignRequestIndex;
use App\Models\SubNpdPlannerRequestIndex;
use App\Models\SubPeRequestIndex;
use App\Repositories\Admin\Interfaces\SubNpdDesignRequestTypeRepositoryInterface;
use App\Repositories\Admin\Interfaces\SubNpdPlannerRequestIndexRepositoryInterface;
use App\Repositories\Admin\Interfaces\SubPeRequestIndexRepositoryInterface;
use DB;
use Illuminate\Database\Eloquent\Model;


class SubNpdPlannerRequestIndexRepository implements SubNpdPlannerRequestIndexRepositoryInterface
{
    public function findAll($options = [])
    {
        $perPage = $options['per_page'] ?? null;
        $orderByFields = $options['order'] ?? [];
        $id = $options['id'] ?? [];

        $npdPlannerRequestRequestIndex = new SubNpdPlannerRequestIndex();

        if ($id) {
            $npdPlannerRequestRequestIndex = $npdPlannerRequestRequestIndex
                ->where('id', $id);
        }

        if ($orderByFields) {
            foreach ($orderByFields as $field => $sort) {
                $npdPlannerRequestRequestIndex = $npdPlannerRequestRequestIndex->orderBy($field, $sort);
            }
        }

        if ($perPage) {
            return $npdPlannerRequestRequestIndex->paginate($perPage);
        }

        $npdPlannerRequestRequestIndex = $npdPlannerRequestRequestIndex->get();

        return $npdPlannerRequestRequestIndex;
    }

    public function findById($id)
    {
        return SubNpdPlannerRequestIndex::findOrFail($id);
    }

    public function create($params = [])
    {
        return DB::transaction(function () use ($params) {
            $npdPlannerRequestRequestIndex = SubNpdPlannerRequestIndex::create($params);
//            $this->syncRolesAndPermissions($params, $campaignBrand);

            return $npdPlannerRequestRequestIndex;
        });
    }

    public function update($id, $params = [])
    {
        $npdPlannerRequestRequestIndex = SubNpdPlannerRequestIndex::findOrFail($id);

        return DB::transaction(function () use ($params, $npdPlannerRequestRequestIndex) {
            $npdPlannerRequestRequestIndex->update($params);

            return $npdPlannerRequestRequestIndex;
        });
    }

    public function delete($id)
    {
        $npdPlannerRequestRequestIndex  = SubNpdPlannerRequestIndex::findOrFail($id);

        return $npdPlannerRequestRequestIndex->delete();
    }

    public function get_task_id_for_npd_planner($project_id)
    {
        $pti_obj = ProjectTaskIndex::where('project_id', $project_id)
            ->where('type', 'npd_planner_request')->first();
        return $pti_obj->id;
    }

    public function get_request_type_list_by_task_id($task_id)
    {
        return DB::select('
                select  t.npd_planner_request_type_id as npd_planner_request_type_id,
                        t.request_type as request_type,
                        concat(u.first_name, " ", u.last_name) as author_name,
                        i.author_id as author_id,
                        i.decline_reason as decline_reason,
                        p.team as team,
                        i.status as status,
                        t.assignee as assignee,
                        t.npd_planner_request_type_id as npd_planner_request_type_id,
                        t.request_type as request_type,
                        t.request_group as request_group,
                        t.due_date as due_date,
                        t.due_date_revision as due_date_revision,
                        t.revision_cnt as revision_cnt,
                        t.revision_reason as revision_reason,
                        t.uploaded_date as uploaded_date,
                        concat(uu.first_name, " ", uu.last_name) as assignee_name,
                        t.project_code as project_code,
                        t.target_door_number as target_door_number,
                        t.ny_target_receiving_date as ny_target_receiving_date,
                        t.la_target_receiving_date as la_target_receiving_date,
                        t.ny_planned_launch_date as ny_planned_launch_date,
                        t.la_planned_launch_date as la_planned_launch_date,
                        t.nsp as nsp,
                        t.srp as srp,
                        t.sales_channel as sales_channel,
                        t.if_others_sales_channel as if_others_sales_channel,
                        t.expected_reorder as expected_reorder,
                        t.expected_sales as expected_sales,
                        t.benchmark_item as benchmark_item,
                        t.actual_sales as actual_sales,
                        t.display_plan as display_plan,
                        t.if_others_display_plan as if_others_display_plan,
                        t.display_type as display_type,
                        t.penetration_type as penetration_type,
                        t.if_others_penetration_type as if_others_penetration_type,
                        t.tester as tester,
                        t.promotion_items as promotion_items,
                        t.if_others_promotion_items as if_others_promotion_items,
                        t.return_plan as return_plan,
                        t.return_plan_description as return_plan_description,
                        t.purpose as purpose,
                        t.return_plan_description as return_plan_description,
                        t.promotion_conditions as promotion_conditions,
                        t.presale_start_date as presale_start_date,
                        t.presale_end_date as presale_end_date,
                        t.promotion_start_date as promotion_start_date,
                        t.promotion_end_date as promotion_end_date,
                        t.presale_initial_shipping_start_date as presale_initial_shipping_start_date,
                        t.update_type as update_type,
                        t.revised_target_door_number as revised_target_door_number,
                        t.revised_ny_receiving_date as revised_ny_receiving_date,
                        t.revised_la_receiving_date as revised_la_receiving_date,
                        t.revised_ny_launch_date as revised_ny_launch_date,
                        t.revised_la_launch_date as revised_la_launch_date,
                        t.change_request_reason as change_request_reason,
                        t.change_request_detail as change_request_detail,
                        t.due_date_upload as due_date_upload,
                        t.uploaded_date as uploaded_date,
                        t.created_at as created_at
                from sub_npd_planner_request_index i
                left join sub_npd_planner_request_type t on t.npd_planner_request_type_id = i.id
                left join project_task_index pt on pt.id = i.task_id
				left join project p on p.id = pt.project_id
                left join users u on u.id = i.author_id
                left join users uu on uu.id = t.assignee
                where i.task_id =:task_id order by t.id desc', [
            'task_id' => $task_id
        ]);
    }

}
