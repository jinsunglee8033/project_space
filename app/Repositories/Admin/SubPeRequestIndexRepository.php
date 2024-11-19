<?php

namespace App\Repositories\Admin;

use App\Models\SubPeRequestIndex;
use App\Repositories\Admin\Interfaces\SubPeRequestIndexRepositoryInterface;
use DB;
use Illuminate\Database\Eloquent\Model;


class SubPeRequestIndexRepository implements SubPeRequestIndexRepositoryInterface
{
    public function findAll($options = [])
    {
        $perPage = $options['per_page'] ?? null;
        $orderByFields = $options['order'] ?? [];
        $id = $options['id'] ?? [];

        $peRequestRequestIndex = new SubPeRequestIndex();

        if ($id) {
            $peRequestRequestIndex = $peRequestRequestIndex
                ->where('id', $id);
        }

        if ($orderByFields) {
            foreach ($orderByFields as $field => $sort) {
                $peRequestRequestIndex = $peRequestRequestIndex->orderBy($field, $sort);
            }
        }

        if ($perPage) {
            return $peRequestRequestIndex->paginate($perPage);
        }

        $peRequestRequestIndex = $peRequestRequestIndex->get();

        return $peRequestRequestIndex;
    }

    public function findById($id)
    {
        return SubPeRequestIndex::findOrFail($id);
    }

    public function create($params = [])
    {
        return DB::transaction(function () use ($params) {
            $peRequestRequestIndex = SubPeRequestIndex::create($params);
//            $this->syncRolesAndPermissions($params, $campaignBrand);

            return $peRequestRequestIndex;
        });
    }

    public function update($id, $params = [])
    {
        $peRequestRequestIndex = SubPeRequestIndex::findOrFail($id);

        return DB::transaction(function () use ($params, $peRequestRequestIndex) {
            $peRequestRequestIndex->update($params);

            return $peRequestRequestIndex;
        });
    }

    public function delete($id)
    {
        $peRequestRequestIndex  = SubPeRequestIndex::findOrFail($id);

        return $peRequestRequestIndex->delete();
    }

}
