<?php

namespace App\Repositories\Admin;

use App\Models\QraRequestTypeAttachments;
use App\Models\RaRequestTypeAttachments;
use App\Repositories\Admin\Interfaces\ProjectTaskFileAttachmentsRepositoryInterface;
use App\Repositories\Admin\Interfaces\QraRequestTypeFileAttachmentsRepositoryInterface;
use App\Repositories\Admin\Interfaces\RaRequestTypeFileAttachmentsRepositoryInterface;
use DB;

use App\Models\ProjectTypeTaskAttachments;
use Illuminate\Database\Eloquent\Model;

class RaRequestTypeFileAttachmentsRepository implements RaRequestTypeFileAttachmentsRepositoryInterface
{
    public function findAll($options = [])
    {
        $perPage = $options['per_page'] ?? null;
        $orderByFields = $options['order'] ?? [];
        $id = $options['id'] ?? [];

        $fileAttachments = new RaRequestTypeAttachments();

        if ($id) {
            $fileAttachments = $fileAttachments
                ->where('task_id', $id);
        }

        $fileAttachments = $fileAttachments->get();

        return $fileAttachments;
    }

    public function findAllByRequestTypeId($task_id)
    {
        $fileAttachments = new RaRequestTypeAttachments();
        return $fileAttachments->where('ra_request_type_id', $task_id)->orderBy('attachment', 'desc')->get();
    }

    public function findById($id)
    {
        return RaRequestTypeAttachments::findOrFail($id);
    }

    public function create($params = [])
    {
        return DB::transaction(function () use ($params) {
            $fileAttachments = RaRequestTypeAttachments::create($params);
            $this->syncRolesAndPermissions($params, $fileAttachments);

            return $fileAttachments;
        });
    }

    public function update($id, $params = [])
    {
        $fileAttachments = RaRequestTypeAttachments::findOrFail($id);

        return DB::transaction(function () use ($params, $fileAttachments) {
            $fileAttachments->update($params);

            return $fileAttachments;
        });
    }

    public function delete($id)
    {
        $fileAttachments  = RaRequestTypeAttachments::findOrFail($id);

        return $fileAttachments->delete();
    }
}
