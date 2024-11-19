<?php

namespace App\Repositories\Admin;

use App\Models\ProjectTypeTaskAttachments;
use App\Models\SubQraRequestType;
use App\Models\TaskTypeLegalRequest;
use App\Repositories\Admin\Interfaces\SubQraRequestTypeRepositoryInterface;
use App\Repositories\Admin\Interfaces\TaskTypeLegalRequestRepositoryInterface;
use DB;

use Illuminate\Database\Eloquent\Model;

class SubQraRequestTypeRepository implements SubQraRequestTypeRepositoryInterface
{
    public function findAll($options = [])
    {
        $perPage = $options['per_page'] ?? null;
        $orderByFields = $options['order'] ?? [];
        $id = $options['id'] ?? [];

        $subQraRequestType = new SubQraRequestType();

        if ($id) {
            $subQraRequestType = $subQraRequestType
                ->where('id', $id)->where('task_id', 0);
        }

        $subQraRequestType = $subQraRequestType->get();

        return $subQraRequestType;
    }

    public function findAllByRequestTypeId($request_type_id)
    {
        $subQraRequestType = new SubQraRequestType();
        return $subQraRequestType->where('request_type_id', $request_type_id)->first();
    }

    public function deleteByTaskId($task_id)
    {
        $subQraRequestType = new SubQraRequestType();
        return $subQraRequestType->where('task_id', $task_id)->delete();
    }

    public function findById($id)
    {
        return SubQraRequestType::findOrFail($id);
    }

    public function create($params = [])
    {
        return DB::transaction(function () use ($params) {
            $subQraRequestType = SubQraRequestType::create($params);
            $this->syncRolesAndPermissions($params, $subQraRequestType);

            return $subQraRequestType;
        });
    }

    public function update($id, $params = [])
    {
        $subQraRequestType = SubQraRequestType::findOrFail($id);

        return DB::transaction(function () use ($params, $subQraRequestType) {
            $subQraRequestType->update($params);

            return $subQraRequestType;
        });
    }

    public function delete($id)
    {
        $subQraRequestType  = SubQraRequestType::findOrFail($id);

        return $subQraRequestType->delete();
    }
}
