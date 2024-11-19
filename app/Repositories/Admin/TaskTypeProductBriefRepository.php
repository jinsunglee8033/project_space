<?php

namespace App\Repositories\Admin;

use App\Models\ProjectTypeTaskAttachments;
use App\Models\TaskTypeProductBrief;
use App\Repositories\Admin\Interfaces\TaskTypeProductBriefRepositoryInterface;
use DB;

use Illuminate\Database\Eloquent\Model;

class TaskTypeProductBriefRepository implements TaskTypeProductBriefRepositoryInterface
{
    public function findAll($options = [])
    {
        $perPage = $options['per_page'] ?? null;
        $orderByFields = $options['order'] ?? [];
        $id = $options['id'] ?? [];

        $taskTypeProductBrief = new TaskTypeProductBrief();

        if ($id) {
            $taskTypeProductBrief = $taskTypeProductBrief
                ->where('id', $id)->where('task_id', 0);
        }

        $taskTypeProductBrief = $taskTypeProductBrief->get();

        return $taskTypeProductBrief;
    }

    public function findAllByTaskId($task_id)
    {
        $taskTypeProductBrief = new TaskTypeProductBrief();
        return $taskTypeProductBrief->where('task_id', $task_id)->get();
    }

    public function deleteByTaskId($task_id)
    {
        $taskTypeProductBrief = new TaskTypeProductBrief();
        return $taskTypeProductBrief->where('task_id', $task_id)->delete();
    }

    public function findById($id)
    {
        return taskTypeProductBrief::findOrFail($id);
    }

    public function create($params = [])
    {
        return DB::transaction(function () use ($params) {
            $taskTypeProductBrief = TaskTypeProductBrief::create($params);
            $this->syncRolesAndPermissions($params, $taskTypeProductBrief);

            return $taskTypeProductBrief;
        });
    }

    public function update($id, $params = [])
    {
        $taskTypeProductBrief = TaskTypeProductBrief::findOrFail($id);

        return DB::transaction(function () use ($params, $taskTypeProductBrief) {
            $taskTypeProductBrief->update($params);

            return $taskTypeProductBrief;
        });
    }

    public function delete($id)
    {
        $taskTypeProductBrief  = TaskTypeProductBrief::findOrFail($id);

        return $taskTypeProductBrief->delete();
    }
}
