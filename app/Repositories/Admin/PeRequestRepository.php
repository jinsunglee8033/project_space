<?php

namespace App\Repositories\Admin;

use App\Models\CampaignNotes;
use App\Models\ProjectTaskIndex;
use App\Repositories\Admin\Interfaces\PeRequestRepositoryInterface;
use DB;

use App\Models\Project;
use Illuminate\Database\Eloquent\Model;

class PeRequestRepository implements PeRequestRepositoryInterface
{
    public function findAll($options = [])
    {
        $perPage = $options['per_page'] ?? null;
        $orderByFields = $options['order'] ?? [];

        $project = new ProjectTaskIndex();

        $project = $project->Select('project_task_index.id as id',
			'project.name as name',
			'project_task_index.project_id as project_id',
            'project.team as team',
            'project.status as status',
            'users.first_name as first_name',
            'users.last_name as last_name',)
            ->LeftJoin('project', function ($join) {
            $join->on('project.id', '=', 'project_task_index.project_id');
        })->LeftJoin('task_type_pe_request', function($join){
            $join->on('task_type_pe_request.id', '=', 'project_task_index.id');
        })->LeftJoin('users', function($join){
            $join->on('users.id', '=', 'project_task_index.author_id');
        })->Where('project_task_index.type', 'pe_request')
            ->Where('project.status', 'active');

//        if ($orderByFields) {
//            foreach ($orderByFields as $field => $sort) {
//                $project = $project->orderBy($field, $sort);
//            }
//        }
        if (!empty($options['filter']['q'])) {
            $project = $project->Where('project.name', 'LIKE', "%{$options['filter']['q']}%");
        }
        if (!empty($options['filter']['cur_user'])) {
            $project = $project->whereIn('project_task_index.author_id', $options['filter']['cur_user']);
        }
//        if (!empty($options['filter']['department'])) {
//            $project = $project->Where('department', $options['filter']['brand']);
//        }

        $project = $project->OrderBy('project_task_index.created_at', 'desc');
        if ($perPage) {
            return $project->paginate($perPage);
        }
        $project = $project->get();

        return $project;
    }

    public function findById($id)
    {
        return Project::findOrFail($id);
    }

    public function create($params = [])
    {
        return DB::transaction(function () use ($params) {

            $campaign = Project::create($params);

            return $campaign;
        });
    }

    public function update($id, $params = [])
    {
        $campaign = Project::findOrFail($id);

        return DB::transaction(function () use ($params, $campaign) {
            $campaign->update($params);

            return $campaign;
        });
    }

    public function delete($id)
    {
        $campaign  = Project::findOrFail($id);

        return $campaign->delete();
    }

    public function get_request_types_by_project_id($project_id)
    {
        return DB::select('
                select *
                from project_task_index pti 
                left join project p on p.id = pti.project_id
                left join task_type_legal_request ttqr on ttqr.id = pti.id
                where pti.type = "pe_request"
                where pti.project_id =:project_id order by p.id desc', [
            'project_id' => $project_id
        ]);
    }

    public function get_task_id_by_project_id($project_id)
    {
        $pti_obj = ProjectTaskIndex::where('project_id', $project_id)->first();
        return $pti_obj->id;
    }

    public function get_task_id_for_pe($project_id)
    {
        $pti_obj = ProjectTaskIndex::where('project_id', $project_id)
            ->where('type', 'pe_request')->first();
        return $pti_obj->id;
    }

    public function get_request_type_list_by_task_id($task_id)
    {
        return DB::select('
                select  t.pe_request_type_id as pe_request_type_id,
                        t.type as request_type,
                        u.first_name as first_name,
                        u.last_name as last_name,
                        t.author_id as author_id,
                        u.team as team,
                        i.status as status,
                        t.pe_request_type_id as pe_request_type_id,
                        t.request_detail as request_detail,
                        t.total_quantity as total_quantity,
                        t.item_number as item_number,
                        t.color_pattern as color_pattern,
                        t.tooling_budget_code as tooling_budget_code,
                        t.due_date as due_date,
                        t.display_ready_date as display_ready_date,
                        t.assignee as assignee,
                        uu.first_name as assignee_first_name,
                        uu.last_name as assignee_last_name,
                        t.revision_cnt_ed as revision_cnt_ed,
                        t.revising_ed as revising_ed,
                        t.design_start_date as design_start_date,
                        t.design_finish_date as design_finish_date,
                        t.sample_start_date as sample_start_date,
                        t.sample_finish_date as sample_finish_date,
                        t.sample_type as sample_type,
                        t.sample_quantity as sample_quantity,
                        t.mold_design_start_date as mold_design_start_date,
                        t.mold_design_finish_date as mold_design_finish_date,
                        t.revision_cnt_md as revision_cnt_md,
                        t.revising_md as revising_md,
                        t.cam_start_date as cam_start_date,
                        t.cam_finish_date as cam_finish_date,
                        t.revision_cnt_cam as revision_cnt_cam,
                        t.revising_cam as revising_cam,
                        t.revision_cnt as revision_cnt,
                        t.due_date_revision as due_date_revision,
                        t.machining_start_date as machining_start_date,
                        t.machining_finish_date as machining_finish_date,
                        t.machining_cost as machining_cost,
                        t.request_category as request_category,
                        t.show_type as show_type,
                        t.show_location as show_type,
                        t.product_category as product_category,
                        t.display_type as display_type,
                        t.display_style as display_style,
                        t.specify_display_style as specify_display_style,
                        t.display as display,
                        t.total_display_qty as total_display_qty,
                        t.display_budget_per_ea as display_budget_per_ea,
                        t.display_budget_code as display_budget_code,
                        t.account as account,
                        t.specify_account as specify_account,
                        t.additional_information as additional_information,
                        t.task_category as task_category,
                        t.kdc_delivery_due_date as kdc_delivery_due_date,
                        t.created_at as created_at
                from sub_pe_request_index i
                left join sub_pe_request_type t on t.pe_request_type_id = i.id
                left join users u on u.id = t.author_id
                left join users uu on uu.id = t.assignee
                where i.task_id =:task_id order by t.id desc', [
            'task_id' => $task_id
        ]);
    }

    static function get_pe_request_type($task_id)
    {
        return DB::select('
                select p.id as id,
                       p.task_id as project_id,
                       p.author_id as author_id,
                       concat(u.first_name, " ", u.last_name) as author_name,
                       p.request_type as type,
                       p.status as status,
                       p.created_at as created_at
                from sub_pe_request_index p 
                left join users u on u.id = p.author_id
                where p.task_id =:task_id order by p.id desc', [
            'task_id' => $task_id
        ]);
    }

}
