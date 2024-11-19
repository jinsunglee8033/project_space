<?php

namespace App\Repositories\Admin;

use App\Models\NpdDesignRequestTypeAttachments;
use App\Models\PeRequestTypeAttachments;
use App\Models\QraRequestTypeAttachments;
use App\Repositories\Admin\Interfaces\NpdDesignRequestTypeFileAttachmentsRepositoryInterface;
use App\Repositories\Admin\Interfaces\PeRequestTypeFileAttachmentsRepositoryInterface;
use App\Repositories\Admin\Interfaces\ProjectTaskFileAttachmentsRepositoryInterface;
use App\Repositories\Admin\Interfaces\QraRequestTypeFileAttachmentsRepositoryInterface;
use DB;

use App\Models\ProjectTypeTaskAttachments;
use Illuminate\Database\Eloquent\Model;

class NpdDesignRequestTypeFileAttachmentsRepository implements NpdDesignRequestTypeFileAttachmentsRepositoryInterface
{
    public function findAll($options = [])
    {
        $perPage = $options['per_page'] ?? null;
        $orderByFields = $options['order'] ?? [];
        $id = $options['id'] ?? [];

        $fileAttachments = new NpdDesignRequestTypeAttachments();

        if ($id) {
            $fileAttachments = $fileAttachments
                ->where('task_id', $id);
        }

        $fileAttachments = $fileAttachments->get();

        return $fileAttachments;
    }

    public function findAllByRequestTypeId($task_id)
    {
        $fileAttachments = new NpdDesignRequestTypeAttachments();
        return $fileAttachments->where('npd_design_request_type_id', $task_id)->orderBy('attachment', 'desc')->get();
    }

    public function findQrCodeById($qr_code_id)
    {
        $fileAttachments = new NpdDesignRequestTypeAttachments();
        return $fileAttachments->where('type', 'qr_code')->where('id', $qr_code_id)->orderBy('attachment', 'desc')->get();
    }

    public function findById($id)
    {
        return NpdDesignRequestTypeAttachments::findOrFail($id);
    }

    public function create($params = [])
    {
        return DB::transaction(function () use ($params) {
            $fileAttachments = NpdDesignRequestTypeAttachments::create($params);
            $this->syncRolesAndPermissions($params, $fileAttachments);

            return $fileAttachments;
        });
    }

    public function update($id, $params = [])
    {
        $fileAttachments = NpdDesignRequestTypeAttachments::findOrFail($id);

        return DB::transaction(function () use ($params, $fileAttachments) {
            $fileAttachments->update($params);

            return $fileAttachments;
        });
    }

    public function delete($id)
    {
        $fileAttachments  = NpdDesignRequestTypeAttachments::findOrFail($id);

        return $fileAttachments->delete();
    }
}
