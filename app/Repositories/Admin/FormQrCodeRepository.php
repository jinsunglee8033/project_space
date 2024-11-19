<?php

namespace App\Repositories\Admin;

use App\Models\CampaignTypeAssetAttachments;
use App\Repositories\Admin\Interfaces\FormQrCodeRepositoryInterface;
use DB;
use App\Models\FormQrCode;
use Illuminate\Database\Eloquent\Model;

class FormQrCodeRepository implements FormQrCodeRepositoryInterface
{
    public function findAll($options = [])
    {
        $orderByFields = $options['order'] ?? [];
        $formQrCode = new FormQrCode();
        if ($orderByFields) {
            foreach ($orderByFields as $field => $sort) {
                $formQrCode = $formQrCode->orderBy($field, $sort);
            }
        }
        $formQrCode = $formQrCode->with('campaignTypeAssetAttachments', 'formQrCode')->get();

        return $formQrCode;
    }

    public function findById($id)
    {
        return FormQrCode::findOrFail($id);
    }

    public function create($params = [])
    {
        return DB::transaction(function () use ($params) {
            $formQrCode = FormQrCode::create($params);
//            $this->syncRolesAndPermissions($params, $campaignBrand);

            return $formQrCode;
        });
    }

    public function update($id, $params = [])
    {
        $formQrCode = FormQrCode::findOrFail($id);

        return DB::transaction(function () use ($params, $formQrCode) {
            $formQrCode->update($params);

            return $formQrCode;
        });
    }

    public function delete($id)
    {
        $role  = FormQrCode::findOrFail($id);

        return $role->delete();
    }

    public function getByAssetName($asset_name)
    {
        $formQrCode = new FormQrCode();
        $obj = $formQrCode->where('asset_name', $asset_name)->get();
        return $obj;

    }

}
