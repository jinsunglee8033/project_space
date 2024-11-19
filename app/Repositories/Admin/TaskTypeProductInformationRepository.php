<?php

namespace App\Repositories\Admin;

use App\Models\ProjectTypeTaskAttachments;
use App\Models\TaskTypeProductBrief;
use App\Models\TaskTypeProductInformation;
use App\Repositories\Admin\Interfaces\TaskTypeProductBriefRepositoryInterface;
use DB;

use Illuminate\Database\Eloquent\Model;

class TaskTypeProductInformationRepository implements TaskTypeProductBriefRepositoryInterface
{
    public function findAll($options = [])
    {
        $perPage = $options['per_page'] ?? null;
        $orderByFields = $options['order'] ?? [];
        $id = $options['id'] ?? [];

        $taskTypeProductInformation = new TaskTypeProductInformation();

        if ($id) {
            $taskTypeProductInformation = $taskTypeProductInformation
                ->where('id', $id)->where('task_id', 0);
        }

        $taskTypeProductInformation = $taskTypeProductInformation->get();

        return $taskTypeProductInformation;
    }

    public function findAllByTaskId($task_id)
    {
        $taskTypeProductInformation = new TaskTypeProductInformation();
        return $taskTypeProductInformation->where('task_id', $task_id)->get();
    }

    public function deleteByTaskId($task_id)
    {
        $taskTypeProductInformation = new TaskTypeProductInformation();
        return $taskTypeProductInformation->where('task_id', $task_id)->delete();
    }

    public function findById($id)
    {
        return TaskTypeProductInformation::findOrFail($id);
    }

    public function create($params = [])
    {
        return DB::transaction(function () use ($params) {
            $taskTypeProductInformation = TaskTypeProductInformation::create($params);
            $this->syncRolesAndPermissions($params, $taskTypeProductInformation);

            return $taskTypeProductInformation;
        });
    }

    public function update($id, $params = [])
    {
        $taskTypeProductInformation = TaskTypeProductInformation::findOrFail($id);

        return DB::transaction(function () use ($params, $taskTypeProductInformation) {
            $taskTypeProductInformation->update($params);

            return $taskTypeProductInformation;
        });
    }

    public function delete($id)
    {
        $taskTypeProductInformation  = TaskTypeProductInformation::findOrFail($id);

        return $taskTypeProductInformation->delete();
    }
}
