<?php

namespace App\Repositories\Admin;

use App\Models\DisplayRequestNotes;
use App\Repositories\Admin\Interfaces\DisplayRequestNotesRepositoryInterface;
use DB;

use Illuminate\Database\Eloquent\Model;

class DisplayRequestNotesRepository implements DisplayRequestNotesRepositoryInterface
{
    public function findAll($options = [])
    {
        $perPage = $options['per_page'] ?? null;
        $orderByFields = $options['order'] ?? [];
        $id = $options['id'] ?? [];

        $displayRequestNotes = new DisplayRequestNotes();

        if ($id) {
            $displayRequestNotes = $displayRequestNotes
                ->where('task_id', $id);
        }

        if ($orderByFields) {
            foreach ($orderByFields as $field => $sort) {
                $displayRequestNotes = $displayRequestNotes->orderBy($field, $sort);
            }
        }

        if ($perPage) {
            return $displayRequestNotes->paginate($perPage);
        }

        $displayRequestNotes = $displayRequestNotes->get();

        return $displayRequestNotes;
    }

    public function findById($id)
    {
        return DisplayRequestNotes::findOrFail($id);
    }

    public function create($params = [])
    {
        return DB::transaction(function () use ($params) {
            $displayRequestNotes = DisplayRequestNotes::create($params);
//            $this->syncRolesAndPermissions($params, $campaignBrand);

            return $displayRequestNotes;
        });
    }

    public function update($id, $params = [])
    {
        $displayRequestNotes = DisplayRequestNotes::findOrFail($id);

        return DB::transaction(function () use ($params, $displayRequestNotes) {
            $displayRequestNotes->update($params);

            return $displayRequestNotes;
        });
    }

    public function delete($id)
    {
        $displayRequestNotes  = DisplayRequestNotes::findOrFail($id);

        return $displayRequestNotes->delete();
    }
}
