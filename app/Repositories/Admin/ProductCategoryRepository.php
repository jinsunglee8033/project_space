<?php

namespace App\Repositories\Admin;

use App\Models\ProductCategory;
use App\Repositories\Admin\Interfaces\ProductCategoryRepositoryInterface;
use App\Repositories\Admin\Interfaces\TeamRepositoryInterface;
use App\Repositories\Admin\Interfaces\VendorRepositoryInterface;
use DB;

use App\Models\Vendor;
use Illuminate\Database\Eloquent\Model;

class ProductCategoryRepository implements ProductCategoryRepositoryInterface
{
    public function findAll($options = [])
    {
        $perPage = $options['per_page'] ?? null;
        $orderByFields = $options['order'] ?? [];

        $productCategory = new ProductCategory();

        if ($orderByFields) {
            foreach ($orderByFields as $field => $sort) {
                $productCategory = $productCategory->orderBy($field, $sort);
            }
        }
        if (!empty($options['filter']['q'])) {
            $productCategory = $productCategory->Where('name', 'LIKE', "%{$options['filter']['q']}%");
        }
        if (!empty($options['filter']['is_active'])) {
            $productCategory = $productCategory->Where('is_active', 'LIKE', "%{$options['filter']['is_active']}%");
        }
//        if (!empty($options['filter']['status'])) {
//            $productCategory = $productCategory->where('status', $options['filter']['status']);
//        }
//        if (!empty($options['filter']['department'])) {
//            $productCategory = $productCategory->Where('department', $options['filter']['brand']);
//        }
//        if (!empty($options['filter']['category'])) {
//            $productCategory = $productCategory->Where('category', $options['filter']['category']);
//        }
        if ($perPage) {
            return $productCategory->paginate($perPage);
        }

        $productCategory = $productCategory->get();

        return $productCategory;
    }

    public function findById($id)
    {
        return ProductCategory::findOrFail($id);
    }

    public function create($params = [])
    {
        return DB::transaction(function () use ($params) {

            $productCategory = ProductCategory::create($params);

            return $productCategory;
        });
    }

    public function update($id, $params = [])
    {
        $productCategory = ProductCategory::findOrFail($id);

        return DB::transaction(function () use ($params, $productCategory) {
            $productCategory->update($params);

            return $productCategory;
        });
    }

    public function delete($id)
    {
        $productCategory  = ProductCategory::findOrFail($id);

        return $productCategory->delete();
    }

}
