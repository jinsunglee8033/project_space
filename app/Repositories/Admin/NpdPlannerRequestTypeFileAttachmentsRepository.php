<?php

namespace App\Repositories\Admin;

use App\Models\NpdPlannerRequestTypeAttachments;
use App\Models\PeRequestTypeAttachments;
use App\Models\QraRequestTypeAttachments;
use App\Repositories\Admin\Interfaces\NpdDesignRequestTypeFileAttachmentsRepositoryInterface;
use App\Repositories\Admin\Interfaces\NpdPlannerRequestTypeFileAttachmentsRepositoryInterface;
use App\Repositories\Admin\Interfaces\PeRequestTypeFileAttachmentsRepositoryInterface;
use App\Repositories\Admin\Interfaces\ProjectTaskFileAttachmentsRepositoryInterface;
use App\Repositories\Admin\Interfaces\QraRequestTypeFileAttachmentsRepositoryInterface;
use DB;

use App\Models\ProjectTypeTaskAttachments;
use Illuminate\Database\Eloquent\Model;

class NpdPlannerRequestTypeFileAttachmentsRepository implements NpdPlannerRequestTypeFileAttachmentsRepositoryInterface
{
    public function findAll($options = [])
    {
        $perPage = $options['per_page'] ?? null;
        $orderByFields = $options['order'] ?? [];
        $id = $options['id'] ?? [];

        $fileAttachments = new NpdPlannerRequestTypeAttachments();

        if ($id) {
            $fileAttachments = $fileAttachments
                ->where('task_id', $id);
        }

        $fileAttachments = $fileAttachments->get();

        return $fileAttachments;
    }

    public function findAllByRequestTypeId($task_id)
    {
        $fileAttachments = new NpdPlannerRequestTypeAttachments();
        return $fileAttachments->where('npd_planner_request_type_id', $task_id)->orderBy('attachment', 'desc')->get();
    }

    public function findQrCodeById($qr_code_id)
    {
        $fileAttachments = new NpdPlannerRequestTypeAttachments();
        return $fileAttachments->where('type', 'qr_code')->where('id', $qr_code_id)->orderBy('attachment', 'desc')->get();
    }

    public function findById($id)
    {
        return NpdPlannerRequestTypeAttachments::findOrFail($id);
    }

    public function create($params = [])
    {
        return DB::transaction(function () use ($params) {
            $fileAttachments = NpdPlannerRequestTypeAttachments::create($params);
            $this->syncRolesAndPermissions($params, $fileAttachments);

            return $fileAttachments;
        });
    }

    public function update($id, $params = [])
    {
        $fileAttachments = NpdPlannerRequestTypeAttachments::findOrFail($id);

        return DB::transaction(function () use ($params, $fileAttachments) {
            $fileAttachments->update($params);

            return $fileAttachments;
        });
    }

    public function delete($id)
    {
        $fileAttachments  = NpdPlannerRequestTypeAttachments::findOrFail($id);

        return $fileAttachments->delete();
    }
}
