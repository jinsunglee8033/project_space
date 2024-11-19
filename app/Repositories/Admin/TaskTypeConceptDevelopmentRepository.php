<?php

namespace App\Repositories\Admin;

use App\Repositories\Admin\Interfaces\TaskTypeConceptDevelopmentRepositoryInterface;
use DB;

use App\Models\TaskTypeConceptDevelopment;
use Illuminate\Database\Eloquent\Model;

class TaskTypeConceptDevelopmentRepository implements TaskTypeConceptDevelopmentRepositoryInterface
{
    public function findAll($options = [])
    {
        $perPage = $options['per_page'] ?? null;
        $orderByFields = $options['order'] ?? [];
        $id = $options['id'] ?? [];

        $taskTypeConceptDevelopment = new TaskTypeConceptDevelopment();

        if ($id) {
            $taskTypeConceptDevelopment = $taskTypeConceptDevelopment
                ->where('id', $id)->where('task_id', 0);
        }

        $taskTypeConceptDevelopment = $taskTypeConceptDevelopment->get();

        return $taskTypeConceptDevelopment;
    }

    public function findAllByTaskId($task_id)
    {
        $taskTypeConceptDevelopment = new TaskTypeConceptDevelopment();
        return $taskTypeConceptDevelopment->where('task_id', $task_id)->get();
    }

    public function deleteByTaskId($task_id)
    {
        $taskTypeConceptDevelopment = new TaskTypeConceptDevelopment();
        return $taskTypeConceptDevelopment->where('task_id', $task_id)->delete();
    }

    public function findById($id)
    {
        return TaskTypeConceptDevelopment::findOrFail($id);
    }

    public function create($params = [])
    {
        return DB::transaction(function () use ($params) {
            $taskTypeConceptDevelopment = TaskTypeConceptDevelopment::create($params);
            $this->syncRolesAndPermissions($params, $taskTypeConceptDevelopment);

            return $taskTypeConceptDevelopment;
        });
    }

    public function update($id, $params = [])
    {
        $taskTypeConceptDevelopment = TaskTypeConceptDevelopment::findOrFail($id);

        return DB::transaction(function () use ($params, $taskTypeConceptDevelopment) {
            $taskTypeConceptDevelopment->update($params);

            return $taskTypeConceptDevelopment;
        });
    }

    public function delete($id)
    {
        $taskTypeConceptDevelopment  = TaskTypeConceptDevelopment::findOrFail($id);

        return $taskTypeConceptDevelopment->delete();
    }
}
