<?php

namespace App\Repositories\Admin;

use App\Models\ProjectTaskIndex;
use App\Models\SubNpdDesignRequestIndex;
use App\Models\SubPeRequestIndex;
use App\Repositories\Admin\Interfaces\SubNpdDesignRequestTypeRepositoryInterface;
use App\Repositories\Admin\Interfaces\SubPeRequestIndexRepositoryInterface;
use DB;
use Illuminate\Database\Eloquent\Model;


class SubNpdDesignRequestIndexRepository implements SubNpdDesignRequestTypeRepositoryInterface
{
    public function findAll($options = [])
    {
        $perPage = $options['per_page'] ?? null;
        $orderByFields = $options['order'] ?? [];
        $id = $options['id'] ?? [];

        $npdDesignRequestRequestIndex = new SubNpdDesignRequestIndex();

        if ($id) {
            $npdDesignRequestRequestIndex = $npdDesignRequestRequestIndex
                ->where('id', $id);
        }

        if ($orderByFields) {
            foreach ($orderByFields as $field => $sort) {
                $npdDesignRequestRequestIndex = $npdDesignRequestRequestIndex->orderBy($field, $sort);
            }
        }

        if ($perPage) {
            return $npdDesignRequestRequestIndex->paginate($perPage);
        }

        $npdDesignRequestRequestIndex = $npdDesignRequestRequestIndex->get();

        return $npdDesignRequestRequestIndex;
    }

    public function findById($id)
    {
        return SubNpdDesignRequestIndex::findOrFail($id);
    }

    public function create($params = [])
    {
        return DB::transaction(function () use ($params) {
            $npdDesignRequestRequestIndex = SubNpdDesignRequestIndex::create($params);
//            $this->syncRolesAndPermissions($params, $campaignBrand);

            return $npdDesignRequestRequestIndex;
        });
    }

    public function update($id, $params = [])
    {
        $npdDesignRequestRequestIndex = SubNpdDesignRequestIndex::findOrFail($id);

        return DB::transaction(function () use ($params, $npdDesignRequestRequestIndex) {
            $npdDesignRequestRequestIndex->update($params);

            return $npdDesignRequestRequestIndex;
        });
    }

    public function delete($id)
    {
        $npdDesignRequestRequestIndex  = SubNpdDesignRequestIndex::findOrFail($id);

        return $npdDesignRequestRequestIndex->delete();
    }

    public function get_task_id_for_npd_design($project_id)
    {
        $pti_obj = ProjectTaskIndex::where('project_id', $project_id)
            ->where('type', 'npd_design_request')->first();
        return $pti_obj->id;
    }

    public function get_request_type_list_by_task_id($task_id)
    {
        return DB::select('
                select  t.npd_design_request_type_id as npd_design_request_type_id,
                        t.type as request_type,
                        concat(u.first_name, " ", u.last_name) as author_name,
                        i.author_id as author_id,
                        p.team as team,
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
                        t.if_others_sales_channel as if_others_sales_channel,
                        t.target_audience as target_audience,
                        t.head_copy as head_copy,
                        t.reference as reference,
                        t.material_number as material_number,
                        t.component_number as component_number,
                        t.assignee as assignee,
                        t.multiple_assignees as multiple_assignees,
                        concat(uu.first_name, " ", uu.last_name) as assignee_name,
                        t.created_at as created_at
                from sub_npd_design_request_index i
                left join sub_npd_design_request_type t on t.npd_design_request_type_id = i.id
                left join project_task_index pt on pt.id = i.task_id
				left join project p on p.id = pt.project_id
                left join users u on u.id = i.author_id
                left join users uu on uu.id = t.assignee
                where i.task_id =:task_id order by t.id desc', [
            'task_id' => $task_id
        ]);
    }

}
