<?php

namespace App\Repositories\Admin;

use App\Models\PeRequestNotes;
use App\Repositories\Admin\Interfaces\PeRequestNotesRepositoryInterface;
use DB;

use Illuminate\Database\Eloquent\Model;

class PeRequestNotesRepository implements PeRequestNotesRepositoryInterface
{
    public function findAll($options = [])
    {
        $perPage = $options['per_page'] ?? null;
        $orderByFields = $options['order'] ?? [];
        $id = $options['id'] ?? [];

        $peRequestNotes = new PeRequestNotes();

        if ($id) {
            $peRequestNotes = $peRequestNotes
                ->where('task_id', $id);
        }

        if ($orderByFields) {
            foreach ($orderByFields as $field => $sort) {
                $peRequestNotes = $peRequestNotes->orderBy($field, $sort);
            }
        }

        if ($perPage) {
            return $peRequestNotes->paginate($perPage);
        }

        $peRequestNotes = $peRequestNotes->get();

        return $peRequestNotes;
    }

    public function findById($id)
    {
        return PeRequestNotes::findOrFail($id);
    }

    public function create($params = [])
    {
        return DB::transaction(function () use ($params) {
            $peRequestNotes = PeRequestNotes::create($params);
//            $this->syncRolesAndPermissions($params, $campaignBrand);

            return $peRequestNotes;
        });
    }

    public function update($id, $params = [])
    {
        $peRequestNotes = PeRequestNotes::findOrFail($id);

        return DB::transaction(function () use ($params, $peRequestNotes) {
            $peRequestNotes->update($params);

            return $peRequestNotes;
        });
    }

    public function delete($id)
    {
        $peRequestNotes  = PeRequestNotes::findOrFail($id);

        return $peRequestNotes->delete();
    }
}
