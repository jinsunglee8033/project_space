<?php

namespace App\Repositories\Admin;

use App\Repositories\Admin\Interfaces\ProjectTaskFileAttachmentsRepositoryInterface;
use DB;

use App\Models\ProjectTypeTaskAttachments;
use Illuminate\Database\Eloquent\Model;

class ProjectTaskFileAttachmentsRepository implements ProjectTaskFileAttachmentsRepositoryInterface
{
    public function findAll($options = [])
    {
        $perPage = $options['per_page'] ?? null;
        $orderByFields = $options['order'] ?? [];
        $id = $options['id'] ?? [];

        $fileAttachments = new ProjectTypeTaskAttachments();

        if ($id) {
            $fileAttachments = $fileAttachments
                ->where('task_id', $id);
        }

        $fileAttachments = $fileAttachments->get();

        return $fileAttachments;
    }

    public function findAllByTaskId($task_id)
    {
        $fileAttachments = new ProjectTypeTaskAttachments();
        return $fileAttachments->where('task_id', $task_id)->orderBy('attachment', 'desc')->get();
    }

    public function findAllQrCode()
    {
        $fileAttachments = new ProjectTypeTaskAttachments();
        return $fileAttachments->where('type', 'qr_code')->orderBy('created_at', 'desc')->get();
    }

    public function findQrCodeById($qr_code_id)
    {
        $fileAttachments = new ProjectTypeTaskAttachments();
        return $fileAttachments->where('type', 'qr_code')->where('id', $qr_code_id)->orderBy('attachment', 'desc')->get();
    }

    public function findById($id)
    {
        return ProjectTypeTaskAttachments::findOrFail($id);
    }

    public function create($params = [])
    {
        return DB::transaction(function () use ($params) {
            $fileAttachments = ProjectTypeTaskAttachments::create($params);
            $this->syncRolesAndPermissions($params, $fileAttachments);

            return $fileAttachments;
        });
    }

    public function update($id, $params = [])
    {
        $fileAttachments = ProjectTypeTaskAttachments::findOrFail($id);

        return DB::transaction(function () use ($params, $fileAttachments) {
            $fileAttachments->update($params);

            return $fileAttachments;
        });
    }

    public function delete($id)
    {
        $fileAttachments  = ProjectTypeTaskAttachments::findOrFail($id);

        return $fileAttachments->delete();
    }
}
