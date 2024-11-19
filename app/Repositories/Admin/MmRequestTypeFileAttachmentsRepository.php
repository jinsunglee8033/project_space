<?php

namespace App\Repositories\Admin;

use App\Models\LegalRequestTypeAttachments;
use App\Models\MmRequestTypeAttachments;
use App\Models\QraRequestTypeAttachments;
use App\Repositories\Admin\Interfaces\LegalRequestTypeFileAttachmentsRepositoryInterface;
use App\Repositories\Admin\Interfaces\MmRequestTypeFileAttachmentsRepositoryInterface;
use App\Repositories\Admin\Interfaces\ProjectTaskFileAttachmentsRepositoryInterface;
use App\Repositories\Admin\Interfaces\QraRequestTypeFileAttachmentsRepositoryInterface;
use DB;

use App\Models\ProjectTypeTaskAttachments;
use Illuminate\Database\Eloquent\Model;

class MmRequestTypeFileAttachmentsRepository implements MmRequestTypeFileAttachmentsRepositoryInterface
{
    public function findAll($options = [])
    {
        $perPage = $options['per_page'] ?? null;
        $orderByFields = $options['order'] ?? [];
        $id = $options['id'] ?? [];

        $fileAttachments = new MmRequestTypeAttachments();

        if ($id) {
            $fileAttachments = $fileAttachments
                ->where('task_id', $id);
        }

        $fileAttachments = $fileAttachments->get();

        return $fileAttachments;
    }

    public function findAllByRequestTypeId($task_id)
    {
        $fileAttachments = new MmRequestTypeAttachments();
        return $fileAttachments->where('mm_request_type_id', $task_id)->orderBy('attachment', 'desc')->get();
    }


    public function findById($id)
    {
        return MmRequestTypeAttachments::findOrFail($id);
    }

    public function create($params = [])
    {
        return DB::transaction(function () use ($params) {
            $fileAttachments = MmRequestTypeAttachments::create($params);
            $this->syncRolesAndPermissions($params, $fileAttachments);

            return $fileAttachments;
        });
    }

    public function update($id, $params = [])
    {
        $fileAttachments = MmRequestTypeAttachments::findOrFail($id);

        return DB::transaction(function () use ($params, $fileAttachments) {
            $fileAttachments->update($params);

            return $fileAttachments;
        });
    }

    public function delete($id)
    {
        $fileAttachments  = MmRequestTypeAttachments::findOrFail($id);

        return $fileAttachments->delete();
    }
}
