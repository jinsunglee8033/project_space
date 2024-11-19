<?php

namespace App\Repositories\Admin;

use App\Models\SubQraRequestIndex;
use App\Repositories\Admin\Interfaces\SubQraRequestIndexRepositoryInterface;
use DB;


class SubQraRequestIndexRepository implements SubQraRequestIndexRepositoryInterface
{
    public function findAll($options = [])
    {
        $perPage = $options['per_page'] ?? null;
        $orderByFields = $options['order'] ?? [];
        $id = $options['id'] ?? [];

        $projectTaskIndex = new SubQraRequestIndex();

        if ($id) {
            $projectTaskIndex = $projectTaskIndex
                ->where('id', $id);
        }

        if ($orderByFields) {
            foreach ($orderByFields as $field => $sort) {
                $projectTaskIndex = $projectTaskIndex->orderBy($field, $sort);
            }
        }

        if ($perPage) {
            return $projectTaskIndex->paginate($perPage);
        }

        $projectTaskIndex = $projectTaskIndex->get();

        return $projectTaskIndex;
    }

    public function findById($id)
    {
        return SubQraRequestIndex::findOrFail($id);
    }

    public function create($params = [])
    {
        return DB::transaction(function () use ($params) {
            $projectTaskIndex = SubQraRequestIndex::create($params);
//            $this->syncRolesAndPermissions($params, $campaignBrand);

            return $projectTaskIndex;
        });
    }

    public function update($id, $params = [])
    {
        $projectTaskIndex = SubQraRequestIndex::findOrFail($id);

        return DB::transaction(function () use ($params, $projectTaskIndex) {
            $projectTaskIndex->update($params);

            return $projectTaskIndex;
        });
    }

    public function delete($id)
    {
        $projectTaskIndex  = SubQraRequestIndex::findOrFail($id);

        return $projectTaskIndex->delete();
    }

}
