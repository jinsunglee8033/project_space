<?php

namespace App\Repositories\Admin;

use App\Models\SubLegalRequestIndex;
use App\Models\SubQraRequestIndex;
use App\Repositories\Admin\Interfaces\SubLegalRequestIndexRepositoryInterface;
use App\Repositories\Admin\Interfaces\SubQraRequestIndexRepositoryInterface;
use DB;
use Illuminate\Database\Eloquent\Model;


class SubLegalRequestIndexRepository implements SubLegalRequestIndexRepositoryInterface
{
    public function findAll($options = [])
    {
        $perPage = $options['per_page'] ?? null;
        $orderByFields = $options['order'] ?? [];
        $id = $options['id'] ?? [];

        $subLegalRequestIndex = new SubLegalRequestIndex();

        if ($id) {
            $subLegalRequestIndex = $subLegalRequestIndex
                ->where('id', $id);
        }

        if ($orderByFields) {
            foreach ($orderByFields as $field => $sort) {
                $subLegalRequestIndex = $subLegalRequestIndex->orderBy($field, $sort);
            }
        }

        if ($perPage) {
            return $subLegalRequestIndex->paginate($perPage);
        }

        $subLegalRequestIndex = $subLegalRequestIndex->get();

        return $subLegalRequestIndex;
    }

    public function findById($id)
    {
        return SubLegalRequestIndex::findOrFail($id);
    }

    public function create($params = [])
    {
        return DB::transaction(function () use ($params) {
            $subLegalRequestIndex = SubLegalRequestIndex::create($params);
//            $this->syncRolesAndPermissions($params, $campaignBrand);

            return $subLegalRequestIndex;
        });
    }

    public function update($id, $params = [])
    {
        $subLegalRequestIndex = SubLegalRequestIndex::findOrFail($id);

        return DB::transaction(function () use ($params, $subLegalRequestIndex) {
            $subLegalRequestIndex->update($params);

            return $subLegalRequestIndex;
        });
    }

    public function delete($id)
    {
        $subLegalRequestIndex  = SubLegalRequestIndex::findOrFail($id);

        return $subLegalRequestIndex->delete();
    }

}
