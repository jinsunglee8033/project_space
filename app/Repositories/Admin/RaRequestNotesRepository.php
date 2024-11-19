<?php

namespace App\Repositories\Admin;

use App\Models\QraRequestNotes;
use App\Models\RaRequestNotes;
use App\Repositories\Admin\Interfaces\DevNotesRepositoryInterface;
use App\Repositories\Admin\Interfaces\QraRequestNotesRepositoryInterface;
use App\Repositories\Admin\Interfaces\QraRequestRepositoryInterface;
use App\Repositories\Admin\Interfaces\RaRequestNotesRepositoryInterface;
use DB;


use App\Models\DevNotes;
use Illuminate\Database\Eloquent\Model;

class RaRequestNotesRepository implements RaRequestNotesRepositoryInterface
{
    public function findAll($options = [])
    {
        $perPage = $options['per_page'] ?? null;
        $orderByFields = $options['order'] ?? [];
        $id = $options['id'] ?? [];

        $raRequestNotes = new RaRequestNotes();

        if ($id) {
            $raRequestNotes = $raRequestNotes
                ->where('task_id', $id);
        }

        if ($orderByFields) {
            foreach ($orderByFields as $field => $sort) {
                $raRequestNotes = $raRequestNotes->orderBy($field, $sort);
            }
        }

        if ($perPage) {
            return $raRequestNotes->paginate($perPage);
        }

        $raRequestNotes = $raRequestNotes->get();

        return $raRequestNotes;
    }

    public function findById($id)
    {
        return RaRequestNotes::findOrFail($id);
    }

    public function create($params = [])
    {
        return DB::transaction(function () use ($params) {
            $raRequestNotes = RaRequestNotes::create($params);
//            $this->syncRolesAndPermissions($params, $campaignBrand);

            return $raRequestNotes;
        });
    }

    public function update($id, $params = [])
    {
        $raRequestNotes = RaRequestNotes::findOrFail($id);

        return DB::transaction(function () use ($params, $raRequestNotes) {
            $raRequestNotes->update($params);

            return $raRequestNotes;
        });
    }

    public function delete($id)
    {
        $raRequestNotes  = RaRequestNotes::findOrFail($id);

        return $raRequestNotes->delete();
    }
}
