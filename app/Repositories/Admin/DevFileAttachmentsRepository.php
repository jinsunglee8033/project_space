<?php

namespace App\Repositories\Admin;

use App\Repositories\Admin\Interfaces\DevFileAttachmentsRepositoryInterface;
use DB;

use App\Models\DevFileAttachments;
use Illuminate\Database\Eloquent\Model;

class DevFileAttachmentsRepository implements DevFileAttachmentsRepositoryInterface
{
    public function findAll($options = [])
    {
        $perPage = $options['per_page'] ?? null;
        $orderByFields = $options['order'] ?? [];
        $id = $options['id'] ?? [];

        $fileAttachments = new DevFileAttachments();

        if ($id) {
            $fileAttachments = $fileAttachments
                ->where('dev_id', $id);
        }

        $fileAttachments = $fileAttachments->get();

        return $fileAttachments;
    }

    public function findAllByAssetId($asset_id)
    {
        $fileAttachments = new DevFileAttachments();
        return $fileAttachments->where('asset_id', $asset_id)->orderBy('attachment', 'desc')->get();
    }

    public function findAllQrCode()
    {
        $fileAttachments = new DevFileAttachments();
        return $fileAttachments->where('type', 'qr_code')->orderBy('created_at', 'desc')->get();
    }

    public function findQrCodeById($qr_code_id)
    {
        $fileAttachments = new DevFileAttachments();
        return $fileAttachments->where('type', 'qr_code')->where('id', $qr_code_id)->orderBy('attachment', 'desc')->get();
    }

    public function findById($id)
    {
        return DevFileAttachments::findOrFail($id);
    }

    public function create($params = [])
    {
        return DB::transaction(function () use ($params) {
            $fileAttachments = DevFileAttachments::create($params);
            $this->syncRolesAndPermissions($params, $fileAttachments);

            return $fileAttachments;
        });
    }

    public function update($id, $params = [])
    {
        $fileAttachments = DevFileAttachments::findOrFail($id);

        return DB::transaction(function () use ($params, $fileAttachments) {
            $fileAttachments->update($params);

            return $fileAttachments;
        });
    }

    public function delete($id)
    {
        $fileAttachments  = DevFileAttachments::findOrFail($id);

        return $fileAttachments->delete();
    }
}
