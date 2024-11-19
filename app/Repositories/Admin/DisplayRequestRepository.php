<?php

namespace App\Repositories\Admin;

use App\Models\ProjectTaskIndex;
use App\Models\TaskTypeDisplayRequest;
use App\Models\TaskTypeMmRequest;
use App\Repositories\Admin\Interfaces\DisplayRequestRepositoryInterface;
use App\Repositories\Admin\Interfaces\MmRequestRepositoryInterface;
use App\Repositories\Admin\Interfaces\ProjectRepositoryInterface;
use DB;

use App\Models\Project;
use Illuminate\Database\Eloquent\Model;

class DisplayRequestRepository implements DisplayRequestRepositoryInterface
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
        })->LeftJoin('task_type_display_request', function($join){
            $join->on('task_type_display_request.id', '=', 'project_task_index.id');
        })->LeftJoin('users', function($join){
            $join->on('users.id', '=', 'project_task_index.author_id');
        })->Where('project_task_index.type', 'display_request')
            ->Where('project.status', 'active');

//        if ($orderByFields) {
//            foreach ($orderByFields as $field => $sort) {
//                $project = $project->orderBy($field, $sort);
//            }
//        }
        if (!empty($options['filter']['q'])) {
            $project = $project->Where('project.name', 'LIKE', "%{$options['filter']['q']}%");
        }
//        if (!empty($options['filter']['status'])) {
//            $project = $project->where('status', $options['filter']['status']);
//        }
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
                left join task_type_mm_request ttqr on ttqr.id = pti.id
                where pti.type = "display_request"
                where pti.project_id =:project_id order by p.id desc', [
            'project_id' => $project_id
        ]);
    }

    public function get_task_id_by_project_id($project_id)
    {
        $pti_obj = ProjectTaskIndex::where('project_id', $project_id)->first();
        return $pti_obj->id;
    }

    public function get_task_id_for_display($project_id)
    {
        $pti_obj = ProjectTaskIndex::where('project_id', $project_id)
            ->where('type', 'display_request')->first();
        return $pti_obj;
    }

    public function get_display_request_list_by_task_id($task_id)
    {
        return DB::select('
            select i.status as status,
                    i.project_id as project_id,
                    i.author_id as author_id,
                    i.type as type,
                    t.request_type as request_type,
                    t.show_type as show_type,
                    t.show_location as show_location,
                    t.product_category as product_category,
                    t.account as account,
                    t.specify_account as specify_account,
                    t.display_style as display_style,
                    t.specify_display_style as sepcify_display_style,
                    t.display_type as specify_account,
                    t.additional_information as specify_account,
                    t.display as specify_account,
                    t.total_display_qty as specify_account,
                    t.display_budget_per_ea as specify_account,
                    t.display_budget_code as specify_account,
                    t.display_ready_date as specify_account,
                    t.assignee as specify_account,
                    t.task_id as task_id,
                    concat(u.first_name, " ", u.last_name) as author_name,
                    concat(uu.first_name, " ", uu.last_name) as assignee_name,
                    t.created_at as created_at
            from project_task_index i
            left join task_type_display_request t on t.task_id =  i.id
            left join users u on u.id = t.author_id
            left join users uu on uu.id = t.assignee
            where i.id =:task_id order by t.id desc', [
            'task_id' => $task_id
        ]);
    }

    public function get_display_request_by_task_id($task_id)
    {
        $rs = TaskTypeDisplayRequest::where('task_id', $task_id)->first();
        return $rs;
    }



}
