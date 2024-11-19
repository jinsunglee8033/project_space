<?php

namespace App\Repositories\Admin;

use App\Models\NpdPoRequestNotes;
use App\Repositories\Admin\Interfaces\NpdPoRequestNotesRepositoryInterface;
use DB;

use Illuminate\Database\Eloquent\Model;

class NpdPoRequestNotesRepository implements NpdPoRequestNotesRepositoryInterface
{
    public function findAll($options = [])
    {
        $perPage = $options['per_page'] ?? null;
        $orderByFields = $options['order'] ?? [];
        $id = $options['id'] ?? [];

        $npdPoRequestNotes = new NpdPoRequestNotes();

        if ($id) {
            $npdPoRequestNotes = $npdPoRequestNotes
                ->where('task_id', $id);
        }

        if ($orderByFields) {
            foreach ($orderByFields as $field => $sort) {
                $npdPoRequestNotes = $npdPoRequestNotes->orderBy($field, $sort);
            }
        }

        if ($perPage) {
            return $npdPoRequestNotes->paginate($perPage);
        }

        $npdPoRequestNotes = $npdPoRequestNotes->get();

        return $npdPoRequestNotes;
    }

    public function findById($id)
    {
        return NpdPoRequestNotes::findOrFail($id);
    }

    public function create($params = [])
    {
        return DB::transaction(function () use ($params) {
            $npdPoRequestNotes = NpdPoRequestNotes::create($params);
//            $this->syncRolesAndPermissions($params, $campaignBrand);

            return $npdPoRequestNotes;
        });
    }

    public function update($id, $params = [])
    {
        $npdPoRequestNotes = NpdPoRequestNotes::findOrFail($id);

        return DB::transaction(function () use ($params, $npdPoRequestNotes) {
            $npdPoRequestNotes->update($params);

            return $npdPoRequestNotes;
        });
    }

    public function delete($id)
    {
        $npdPoRequestNotes  = NpdPoRequestNotes::findOrFail($id);

        return $npdPoRequestNotes->delete();
    }
}
