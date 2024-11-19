<?php

namespace App\Repositories\Admin;

use DB;
use App\Repositories\Admin\Interfaces\AssetLeadTimeRepositoryInterface;
use App\Models\AssetLeadTime;
use Illuminate\Database\Eloquent\Model;

class AssetLeadTimeRepository implements AssetLeadTimeRepositoryInterface
{
    public function findAll($options = [])
    {
        $orderByFields = $options['order'] ?? [];
        $assetLeadTime = new AssetLeadTime();
        if ($orderByFields) {
            foreach ($orderByFields as $field => $sort) {
                $assetLeadTime = $assetLeadTime->orderBy($field, $sort);
            }
        }
        $assetLeadTime = $assetLeadTime->get();

        return $assetLeadTime;
    }

    public function findById($id)
    {
        return AssetLeadTime::findOrFail($id);
    }

    public function create($params = [])
    {
        return DB::transaction(function () use ($params) {
            $assetLeadTime = AssetLeadTime::create($params);
//            $this->syncRolesAndPermissions($params, $campaignBrand);

            return $assetLeadTime;
        });
    }

    public function update($id, $params = [])
    {
        $assetLeadTime = AssetLeadTime::findOrFail($id);

        return DB::transaction(function () use ($params, $assetLeadTime) {
            $assetLeadTime->update($params);

            return $assetLeadTime;
        });
    }

    public function delete($id)
    {
        $role  = AssetLeadTime::findOrFail($id);

        return $role->delete();
    }

    public function getByAssetType($asset_type)
    {
        $assetLeadTime = new AssetLeadTime();
        $obj = $assetLeadTime->where('asset_name', $asset_type)->get();
        return $obj;

    }

}
