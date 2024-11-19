<?php

namespace App\Repositories\Admin;

use DB;
use App\Repositories\Admin\Interfaces\AssetOwnerAssetsRepositoryInterface;
use App\Models\AssetOwnerAssets;
use Illuminate\Database\Eloquent\Model;

class AssetOwnerAssetsRepository implements AssetOwnerAssetsRepositoryInterface
{
    public function findAll($options = [])
    {
        $orderByFields = $options['order'] ?? [];
        $assetOwnerAssets = new AssetOwnerAssets();
        if ($orderByFields) {
            foreach ($orderByFields as $field => $sort) {
                $assetOwnerAssets = $assetOwnerAssets->orderBy($field, $sort);
            }
        }
        $assetOwnerAssets = $assetOwnerAssets->get();

        return $assetOwnerAssets;
    }

    public function findById($id)
    {
        return AssetOwnerAssets::findOrFail($id);
    }

    public function create($params = [])
    {
        return DB::transaction(function () use ($params) {
            $assetOwnerAssets = AssetOwnerAssets::create($params);
//            $this->syncRolesAndPermissions($params, $campaignBrand);

            return $assetOwnerAssets;
        });
    }

    public function update($id, $params = [])
    {
        $assetOwnerAssets = AssetOwnerAssets::findOrFail($id);

        return DB::transaction(function () use ($params, $assetOwnerAssets) {
            $assetOwnerAssets->update($params);

            return $assetOwnerAssets;
        });
    }

    public function delete($id)
    {
        $role  = AssetOwnerAssets::findOrFail($id);

        return $role->delete();
    }

    public function getByAssetName($asset_name)
    {
        $assetOwnerAssets = new AssetOwnerAssets();
        $obj = $assetOwnerAssets->where('asset_name', $asset_name)->get();
        return $obj;

    }

}
