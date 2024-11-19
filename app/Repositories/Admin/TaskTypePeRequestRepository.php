<?php

namespace App\Repositories\Admin;

use App\Models\ProjectTypeTaskAttachments;
use App\Models\TaskTypePeRequest;
use App\Repositories\Admin\Interfaces\TaskTypePeRequestRepositoryInterface;
use DB;

use Illuminate\Database\Eloquent\Model;

class TaskTypePeRequestRepository implements TaskTypePeRequestRepositoryInterface
{
    public function findAll($options = [])
    {
        $perPage = $options['per_page'] ?? null;
        $orderByFields = $options['order'] ?? [];
        $id = $options['id'] ?? [];

        $taskTypePeRequest = new TaskTypePeRequest();

        if ($id) {
            $taskTypePeRequest = $taskTypePeRequest
                ->where('id', $id)->where('task_id', 0);
        }

        $taskTypePeRequest = $taskTypePeRequest->get();

        return $taskTypePeRequest;
    }

    public function findAllByTaskId($task_id)
    {
        $taskTypePeRequest = new TaskTypePeRequest();
        return $taskTypePeRequest->where('task_id', $task_id)->get();
    }

    public function deleteByTaskId($task_id)
    {
        $taskTypePeRequest = new TaskTypePeRequest();
        return $taskTypePeRequest->where('task_id', $task_id)->delete();
    }

    public function findById($id)
    {
        return taskTypePeRequest::findOrFail($id);
    }

    public function create($params = [])
    {
        return DB::transaction(function () use ($params) {
            $taskTypePeRequest = TaskTypePeRequest::create($params);
            $this->syncRolesAndPermissions($params, $taskTypePeRequest);

            return $taskTypePeRequest;
        });
    }

    public function update($id, $params = [])
    {
        $taskTypePeRequest = TaskTypePeRequest::findOrFail($id);

        return DB::transaction(function () use ($params, $taskTypePeRequest) {
            $taskTypePeRequest->update($params);

            return $taskTypePeRequest;
        });
    }

    public function delete($id)
    {
        $taskTypePeRequest  = TaskTypePeRequest::findOrFail($id);

        return $taskTypePeRequest->delete();
    }

    public function get_project_id_by_task_id($task_id)
    {
        $obj = TaskTypePeRequest::where('task_id', $task_id)->first();
        return $obj->project_id;
    }

}
