<?php

namespace App\Repositories\Admin;

use App\Models\ProjectTaskIndex;
use App\Models\ProjectTypeTaskAttachments;
use App\Models\TaskTypeNpdDesignRequest;
use App\Models\TaskTypePeRequest;
use App\Repositories\Admin\Interfaces\TaskTypeNpdDesignRequestRepositoryInterface;
use App\Repositories\Admin\Interfaces\TaskTypePeRequestRepositoryInterface;
use DB;

use Illuminate\Database\Eloquent\Model;

class TaskTypeNpdDesignRequestRepository implements TaskTypeNpdDesignRequestRepositoryInterface
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
            })->LeftJoin('task_type_npd_design_request', function($join){
                $join->on('task_type_npd_design_request.id', '=', 'project_task_index.id');
            })->LeftJoin('users', function($join){
                $join->on('users.id', '=', 'project_task_index.author_id');
            })->Where('project_task_index.type', 'npd_design_request')
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

    public function findAllByTaskId($task_id)
    {
        $taskTypeNpdDesignRequest = new TaskTypeNpdDesignRequest();
        return $taskTypeNpdDesignRequest->where('task_id', $task_id)->get();
    }

    public function deleteByTaskId($task_id)
    {
        $taskTypeNpdDesignRequest = new TaskTypeNpdDesignRequest();
        return $taskTypeNpdDesignRequest->where('task_id', $task_id)->delete();
    }

    public function findById($id)
    {
        return TaskTypeNpdDesignRequest::findOrFail($id);
    }

    public function create($params = [])
    {
        return DB::transaction(function () use ($params) {
            $taskTypeNpdDesignRequest = TaskTypeNpdDesignRequest::create($params);
            $this->syncRolesAndPermissions($params, $taskTypeNpdDesignRequest);

            return $taskTypeNpdDesignRequest;
        });
    }

    public function update($id, $params = [])
    {
        $taskTypeNpdDesignRequest = TaskTypeNpdDesignRequest::findOrFail($id);

        return DB::transaction(function () use ($params, $taskTypeNpdDesignRequest) {
            $taskTypeNpdDesignRequest->update($params);

            return $taskTypeNpdDesignRequest;
        });
    }

    public function delete($id)
    {
        $taskTypeNpdDesignRequest  = TaskTypeNpdDesignRequest::findOrFail($id);

        return $taskTypeNpdDesignRequest->delete();
    }

    public function get_project_id_by_task_id($task_id)
    {
        $obj = TaskTypeNpdDesignRequest::where('task_id', $task_id)->first();
        return $obj->project_id;
    }

    static function get_npd_design_request_type($task_id)
    {
        return DB::select('
                select p.id as id,
                       p.task_id as project_id,
                       p.author_id as author_id,
                       concat(u.first_name, " ", u.last_name) as author_name,
                       p.request_type as type,
                       p.status as status,
                       p.created_at as created_at
                from sub_npd_design_request_index p 
                left join users u on u.id = p.author_id
                where p.task_id =:task_id order by p.id desc', [
            'task_id' => $task_id
        ]);
    }

}
