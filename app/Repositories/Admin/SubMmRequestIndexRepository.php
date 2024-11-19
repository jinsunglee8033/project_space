<?php

namespace App\Repositories\Admin;

use App\Models\SubLegalRequestIndex;
use App\Models\SubMmRequestIndex;
use App\Models\SubQraRequestIndex;
use App\Repositories\Admin\Interfaces\SubLegalRequestIndexRepositoryInterface;
use App\Repositories\Admin\Interfaces\SubMmRequestIndexRepositoryInterface;
use App\Repositories\Admin\Interfaces\SubQraRequestIndexRepositoryInterface;
use DB;
use Illuminate\Database\Eloquent\Model;


class SubMmRequestIndexRepository implements SubMmRequestIndexRepositoryInterface
{
    public function findAll($options = [])
    {
        $perPage = $options['per_page'] ?? null;
        $orderByFields = $options['order'] ?? [];
        $id = $options['id'] ?? [];

        $subMmRequestIndex = new SubMmRequestIndex();

        if ($id) {
            $subMmRequestIndex = $subMmRequestIndex
                ->where('id', $id);
        }

        if ($orderByFields) {
            foreach ($orderByFields as $field => $sort) {
                $subMmRequestIndex = $subMmRequestIndex->orderBy($field, $sort);
            }
        }

        if ($perPage) {
            return $subMmRequestIndex->paginate($perPage);
        }

        $subMmRequestIndex = $subMmRequestIndex->get();

        return $subMmRequestIndex;
    }

    public function findById($id)
    {
        return SubMmRequestIndex::findOrFail($id);
    }

    public function create($params = [])
    {
        return DB::transaction(function () use ($params) {
            $subMmRequestIndex = SubMmRequestIndex::create($params);
//            $this->syncRolesAndPermissions($params, $campaignBrand);

            return $subMmRequestIndex;
        });
    }

    public function update($id, $params = [])
    {
        $subMmRequestIndex = SubMmRequestIndex::findOrFail($id);

        return DB::transaction(function () use ($params, $subMmRequestIndex) {
            $subMmRequestIndex->update($params);

            return $subMmRequestIndex;
        });
    }

    public function delete($id)
    {
        $subMmRequestIndex  = SubMmRequestIndex::findOrFail($id);

        return $subMmRequestIndex->delete();
    }

}
