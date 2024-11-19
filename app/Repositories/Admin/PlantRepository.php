<?php

namespace App\Repositories\Admin;

use App\Models\Plant;
use App\Models\Team;
use App\Repositories\Admin\Interfaces\PlantRepositoryInterface;
use App\Repositories\Admin\Interfaces\TeamRepositoryInterface;
use App\Repositories\Admin\Interfaces\VendorRepositoryInterface;
use DB;

use App\Models\Vendor;
use Illuminate\Database\Eloquent\Model;

class PlantRepository implements PlantRepositoryInterface
{
    public function findAll($options = [])
    {
        $perPage = $options['per_page'] ?? null;
        $orderByFields = $options['order'] ?? [];

        $plant = new Plant();

        if ($orderByFields) {
            foreach ($orderByFields as $field => $sort) {
                $plant = $plant->orderBy($field, $sort);
            }
        }
        if (!empty($options['filter']['q'])) {
            $plant = $plant->Where('name', 'LIKE', "%{$options['filter']['q']}%");
        }
        if (!empty($options['filter']['is_active'])) {
            $plant = $plant->Where('is_active', 'LIKE', "%{$options['filter']['is_active']}%");
        }
//        if (!empty($options['filter']['status'])) {
//            $plant = $plant->where('status', $options['filter']['status']);
//        }
//        if (!empty($options['filter']['department'])) {
//            $plant = $plant->Where('department', $options['filter']['brand']);
//        }
//        if (!empty($options['filter']['category'])) {
//            $plant = $plant->Where('category', $options['filter']['category']);
//        }
        if ($perPage) {
            return $plant->paginate($perPage);
        }

        $plant = $plant->get();

        return $plant;
    }

    public function findById($id)
    {
        return Plant::findOrFail($id);
    }

    public function create($params = [])
    {
        return DB::transaction(function () use ($params) {

            $plant = Plant::create($params);

            return $plant;
        });
    }

    public function update($id, $params = [])
    {
        $plant = Plant::findOrFail($id);

        return DB::transaction(function () use ($params, $plant) {
            $plant->update($params);

            return $plant;
        });
    }

    public function delete($id)
    {
        $plant  = Plant::findOrFail($id);

        return $plant->delete();
    }

    public function get_set_up_plants()
    {
        $plant = new Plant();
        $plant = $plant
            ->Where('is_active', '=', 'yes')
            ->orderBy('id', 'asc');
        return $plant->get();
    }

}
