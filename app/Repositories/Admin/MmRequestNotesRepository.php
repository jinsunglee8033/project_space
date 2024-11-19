<?php

namespace App\Repositories\Admin;

use App\Models\MmRequestNotes;
use App\Models\QcRequestNotes;
use App\Repositories\Admin\Interfaces\MmRequestNotesRepositoryInterface;
use DB;

use Illuminate\Database\Eloquent\Model;

class MmRequestNotesRepository implements MmRequestNotesRepositoryInterface
{
    public function findAll($options = [])
    {
        $perPage = $options['per_page'] ?? null;
        $orderByFields = $options['order'] ?? [];
        $id = $options['id'] ?? [];

        $mmRequestNotes = new MmRequestNotes();

        if ($id) {
            $mmRequestNotes = $mmRequestNotes
                ->where('task_id', $id);
        }

        if ($orderByFields) {
            foreach ($orderByFields as $field => $sort) {
                $mmRequestNotes = $mmRequestNotes->orderBy($field, $sort);
            }
        }

        if ($perPage) {
            return $mmRequestNotes->paginate($perPage);
        }

        $mmRequestNotes = $mmRequestNotes->get();

        return $mmRequestNotes;
    }

    public function findById($id)
    {
        return MmRequestNotes::findOrFail($id);
    }

    public function create($params = [])
    {
        return DB::transaction(function () use ($params) {
            $mmRequestNotes = MmRequestNotes::create($params);
//            $this->syncRolesAndPermissions($params, $campaignBrand);

            return $mmRequestNotes;
        });
    }

    public function update($id, $params = [])
    {
        $mmRequestNotes = MmRequestNotes::findOrFail($id);

        return DB::transaction(function () use ($params, $mmRequestNotes) {
            $mmRequestNotes->update($params);

            return $mmRequestNotes;
        });
    }

    public function delete($id)
    {
        $mmRequestNotes  = MmRequestNotes::findOrFail($id);

        return $mmRequestNotes->delete();
    }
}
