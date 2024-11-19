<?php

namespace App\Repositories\Admin;

use App\Models\ProjectTaskIndex;
use App\Models\TaskTypeMmRequest;
use App\Repositories\Admin\Interfaces\MmRequestRepositoryInterface;
use App\Repositories\Admin\Interfaces\ProjectRepositoryInterface;
use DB;

use App\Models\Project;
use Illuminate\Database\Eloquent\Model;

class MmRequestRepository implements MmRequestRepositoryInterface
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
        })->LeftJoin('task_type_mm_request', function($join){
            $join->on('task_type_mm_request.id', '=', 'project_task_index.id');
        })->LeftJoin('users', function($join){
            $join->on('users.id', '=', 'project_task_index.author_id');
        })->Where('project_task_index.type', 'mm_request')
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
                left join task_type_mm_request ttqr on ttqr.id = pti.id
                where pti.type = "mm_request"
                where pti.project_id =:project_id order by p.id desc', [
            'project_id' => $project_id
        ]);
    }

    public function get_task_id_by_project_id($project_id)
    {
        $pti_obj = ProjectTaskIndex::where('project_id', $project_id)->first();
        return $pti_obj->id;
    }

    public function get_task_id_for_mm($project_id)
    {
        $pti_obj = ProjectTaskIndex::where('project_id', $project_id)
            ->where('type', 'mm_request')->first();
        return $pti_obj->id;
    }

    public function get_mm_request_list_by_task_id($task_id)
    {
        return DB::select('
            select 
                i.task_id as task_id,
                i.author_id as author_id,  
                i.request_type as sub_task_type, 
                t.mm_request_type_id as mm_request_type_id,
                t.materials as materials,
                t.priority as priority,
                t.due_date as due_date,
                t.due_date_urgent as due_date_urgent,
                t.due_date_revision as due_date_revision,
                t.assignee as assignee,
                t.revision_cnt as revision_cnt,
                t.urgent_reason as urgent_reason,
                t.urgent_detail as urgent_detail,   
                t.set_up_plant as set_up_plant,
                t.remark as remark,
                t.created_at as created_at,
                u.team as team,
                concat(u.first_name, " ", u.last_name) as author_name,
                concat(uu.first_name, " ", uu.last_name) as assignee_name,
                i.status as status
            from sub_mm_request_index i
            left join sub_mm_request_type t on t.mm_request_type_id = i.id
            left join users u on u.id = t.author_id
            left join users uu on uu.id = t.assignee
            where i.task_id =:task_id order by t.id desc', [
            'task_id' => $task_id
        ]);
    }

    public function get_mm_request_by_task_id($task_id)
    {
        $rs = TaskTypeMmRequest::where('task_id', $task_id)->first();
        return $rs;
    }

    static function get_mm_request_type($task_id)
    {
        return DB::select('
                select p.id as id,
                       p.task_id as project_id,
                       p.author_id as author_id,
                       concat(u.first_name, " ", u.last_name) as author_name,
                       p.request_type as type,
                       p.status as status,
                       p.created_at as created_at
                from sub_mm_request_index p 
                left join users u on u.id = p.author_id
                where p.task_id =:task_id order by p.id desc', [
            'task_id' => $task_id
        ]);
    }

}
