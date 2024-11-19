<?php

namespace App\Repositories\Admin;

use App\Models\QraRequestNotes;
use App\Repositories\Admin\Interfaces\DevNotesRepositoryInterface;
use App\Repositories\Admin\Interfaces\QraRequestNotesRepositoryInterface;
use App\Repositories\Admin\Interfaces\QraRequestRepositoryInterface;
use DB;


use App\Models\DevNotes;
use Illuminate\Database\Eloquent\Model;

class QraRequestNotesRepository implements QraRequestNotesRepositoryInterface
{
    public function findAll($options = [])
    {
        $perPage = $options['per_page'] ?? null;
        $orderByFields = $options['order'] ?? [];
        $id = $options['id'] ?? [];

        $qraRequestNotes = new QraRequestNotes();

        if ($id) {
            $qraRequestNotes = $qraRequestNotes
                ->where('task_id', $id);
        }

        if ($orderByFields) {
            foreach ($orderByFields as $field => $sort) {
                $qraRequestNotes = $qraRequestNotes->orderBy($field, $sort);
            }
        }

        if ($perPage) {
            return $qraRequestNotes->paginate($perPage);
        }

        $qraRequestNotes = $qraRequestNotes->get();

        return $qraRequestNotes;
    }

    public function findById($id)
    {
        return QraRequestNotes::findOrFail($id);
    }

    public function create($params = [])
    {
        return DB::transaction(function () use ($params) {
            $qraRequestNotes = QraRequestNotes::create($params);
//            $this->syncRolesAndPermissions($params, $campaignBrand);

            return $qraRequestNotes;
        });
    }

    public function update($id, $params = [])
    {
        $qraRequestNotes = QraRequestNotes::findOrFail($id);

        return DB::transaction(function () use ($params, $qraRequestNotes) {
            $qraRequestNotes->update($params);

            return $qraRequestNotes;
        });
    }

    public function delete($id)
    {
        $qraRequestNotes  = QraRequestNotes::findOrFail($id);

        return $qraRequestNotes->delete();
    }
}
