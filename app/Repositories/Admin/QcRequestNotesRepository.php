<?php

namespace App\Repositories\Admin;

use App\Models\LegalRequestNotes;
use App\Models\QcRequestNotes;
use App\Repositories\Admin\Interfaces\QcRequestNotesRepositoryInterface;
use DB;

use Illuminate\Database\Eloquent\Model;

class QcRequestNotesRepository implements QcRequestNotesRepositoryInterface
{
    public function findAll($options = [])
    {
        $perPage = $options['per_page'] ?? null;
        $orderByFields = $options['order'] ?? [];
        $id = $options['id'] ?? [];

        $qcRequestNotes = new QcRequestNotes();

        if ($id) {
            $qcRequestNotes = $qcRequestNotes
                ->where('task_id', $id);
        }

        if ($orderByFields) {
            foreach ($orderByFields as $field => $sort) {
                $qcRequestNotes = $qcRequestNotes->orderBy($field, $sort);
            }
        }

        if ($perPage) {
            return $qcRequestNotes->paginate($perPage);
        }

        $qcRequestNotes = $qcRequestNotes->get();

        return $qcRequestNotes;
    }

    public function findById($id)
    {
        return QcRequestNotes::findOrFail($id);
    }

    public function create($params = [])
    {
        return DB::transaction(function () use ($params) {
            $qcRequestNotes = QcRequestNotes::create($params);
//            $this->syncRolesAndPermissions($params, $campaignBrand);

            return $qcRequestNotes;
        });
    }

    public function update($id, $params = [])
    {
        $qcRequestNotes = QcRequestNotes::findOrFail($id);

        return DB::transaction(function () use ($params, $qcRequestNotes) {
            $qcRequestNotes->update($params);

            return $qcRequestNotes;
        });
    }

    public function delete($id)
    {
        $qcRequestNotes  = QcRequestNotes::findOrFail($id);

        return $qcRequestNotes->delete();
    }
}
