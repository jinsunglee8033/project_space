<?php

namespace App\Repositories\Admin;

use App\Models\ProductCategory;
use App\Models\ProductSegment;
use App\Models\Team;
use App\Repositories\Admin\Interfaces\ProductCategoryRepositoryInterface;
use App\Repositories\Admin\Interfaces\ProductSegmentRepositoryInterface;
use App\Repositories\Admin\Interfaces\TeamRepositoryInterface;
use App\Repositories\Admin\Interfaces\VendorRepositoryInterface;
use DB;

use App\Models\Vendor;
use Illuminate\Database\Eloquent\Model;

class ProductSegmentRepository implements ProductSegmentRepositoryInterface
{
    public function findAll($options = [])
    {
        $perPage = $options['per_page'] ?? null;
        $orderByFields = $options['order'] ?? [];

        $productSegment = new ProductSegment();

        if ($orderByFields) {
            foreach ($orderByFields as $field => $sort) {
                $productSegment = $productSegment->orderBy($field, $sort);
            }
        }
        if (!empty($options['filter']['q'])) {
            $productSegment = $productSegment->Where('name', 'LIKE', "%{$options['filter']['q']}%");
        }
        if (!empty($options['filter']['is_active'])) {
            $productSegment = $productSegment->Where('is_active', 'LIKE', "%{$options['filter']['is_active']}%");
        }
//        if (!empty($options['filter']['status'])) {
//            $productSegment = $productSegment->where('status', $options['filter']['status']);
//        }
//        if (!empty($options['filter']['department'])) {
//            $productSegment = $productSegment->Where('department', $options['filter']['brand']);
//        }
//        if (!empty($options['filter']['category'])) {
//            $productSegment = $productSegment->Where('category', $options['filter']['category']);
//        }
        if ($perPage) {
            return $productSegment->paginate($perPage);
        }

        $productSegment = $productSegment->get();

        return $productSegment;
    }

    public function findById($id)
    {
        return ProductSegment::findOrFail($id);
    }

    public function create($params = [])
    {
        return DB::transaction(function () use ($params) {

            $productSegment = ProductSegment::create($params);

            return $productSegment;
        });
    }

    public function update($id, $params = [])
    {
        $productSegment = ProductSegment::findOrFail($id);

        return DB::transaction(function () use ($params, $productSegment) {
            $productSegment->update($params);

            return $productSegment;
        });
    }

    public function delete($id)
    {
        $productSegment  = ProductSegment::findOrFail($id);

        return $productSegment->delete();
    }

}
