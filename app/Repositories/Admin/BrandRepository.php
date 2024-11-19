<?php

namespace App\Repositories\Admin;

use App\Models\Brand;
use App\Repositories\Admin\Interfaces\BrandRepositoryInterface;
use DB;

use Illuminate\Database\Eloquent\Model;

class BrandRepository implements BrandRepositoryInterface
{
    public function findAll($options = [])
    {
        $perPage = $options['per_page'] ?? null;
        $orderByFields = $options['order'] ?? [];

        $brand = new Brand();

        if ($orderByFields) {
            foreach ($orderByFields as $field => $sort) {
                $brand = $brand->orderBy($field, $sort);
            }
        }
        if (!empty($options['filter']['q'])) {
            $brand = $brand->Where('name', 'LIKE', "%{$options['filter']['q']}%");
        }
        if (!empty($options['filter']['is_active'])) {
            $brand = $brand->where('is_active', $options['filter']['is_active']);
        }
//        if (!empty($options['filter']['department'])) {
//            $brand = $brand->Where('department', $options['filter']['brand']);
//        }
//        if (!empty($options['filter']['category'])) {
//            $brand = $brand->Where('category', $options['filter']['category']);
//        }
        if ($perPage) {
            return $brand->paginate($perPage);
        }

        $brand = $brand->get();

        return $brand;
    }

    public function findById($id)
    {
        return Brand::findOrFail($id);
    }

    public function create($params = [])
    {
        return DB::transaction(function () use ($params) {

            $brand = Brand::create($params);

            return $brand;
        });
    }

    public function update($id, $params = [])
    {
        $brand = Brand::findOrFail($id);

        return DB::transaction(function () use ($params, $brand) {
            $brand->update($params);

            return $brand;
        });
    }

    public function delete($id)
    {
        $brand  = Brand::findOrFail($id);

        return $brand->delete();
    }

}
