<?php

namespace App\Repositories\Admin;

use App\Repositories\Admin\Interfaces\VendorRepositoryInterface;
use DB;

use App\Models\Vendor;
use Illuminate\Database\Eloquent\Model;

class VendorRepository implements VendorRepositoryInterface
{
    public function findAll($options = [])
    {
        $perPage = $options['per_page'] ?? null;
        $orderByFields = $options['order'] ?? [];

        $vendor = new Vendor();

        if ($orderByFields) {
            foreach ($orderByFields as $field => $sort) {
                $vendor = $vendor->orderBy($field, $sort);
            }
        }
        if (!empty($options['filter']['q'])) {
            $vendor = $vendor->Where('name', 'LIKE', "%{$options['filter']['q']}%");
        }
//        if (!empty($options['filter']['q'])) {
//            $vendor = $vendor->Where('code', 'LIKE', "%{$options['filter']['q']}%");
//        }
//        if (!empty($options['filter']['status'])) {
//            $vendor = $vendor->where('status', $options['filter']['status']);
//        }
//        if (!empty($options['filter']['department'])) {
//            $vendor = $vendor->Where('department', $options['filter']['brand']);
//        }
//        if (!empty($options['filter']['category'])) {
//            $vendor = $vendor->Where('category', $options['filter']['category']);
//        }
        if ($perPage) {
            return $vendor->paginate($perPage);
        }

        $vendor = $vendor->get();

        return $vendor;
    }

    public function findById($id)
    {
        return Vendor::findOrFail($id);
    }

    public function create($params = [])
    {
        return DB::transaction(function () use ($params) {

            $campaign = Vendor::create($params);

            return $campaign;
        });
    }

    public function update($id, $params = [])
    {
        $campaign = Vendor::findOrFail($id);

        return DB::transaction(function () use ($params, $campaign) {
            $campaign->update($params);

            return $campaign;
        });
    }

    public function delete($id)
    {
        $campaign  = Vendor::findOrFail($id);

        return $campaign->delete();
    }

}
