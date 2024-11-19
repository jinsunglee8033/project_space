<?php

namespace App\Repositories\Admin;

use App\Repositories\Admin\Interfaces\DevNotesRepositoryInterface;
use DB;


use App\Models\DevNotes;
use Illuminate\Database\Eloquent\Model;

class DevNotesRepository implements DevNotesRepositoryInterface
{
    public function findAll($options = [])
    {
        $perPage = $options['per_page'] ?? null;
        $orderByFields = $options['order'] ?? [];
        $id = $options['id'] ?? [];

        $devNotes = new DevNotes();

        if ($id) {
            $devNotes = $devNotes
                ->where('dev_id', $id);
        }

        if ($orderByFields) {
            foreach ($orderByFields as $field => $sort) {
                $devNotes = $devNotes->orderBy($field, $sort);
            }
        }

        if ($perPage) {
            return $devNotes->paginate($perPage);
        }

        $devNotes = $devNotes->get();

        return $devNotes;
    }

    public function findById($id)
    {
        return DevNotes::findOrFail($id);
    }

    public function create($params = [])
    {
        return DB::transaction(function () use ($params) {
            $devNotes = campaignNotes::create($params);
//            $this->syncRolesAndPermissions($params, $campaignBrand);

            return $devNotes;
        });
    }

    public function update($id, $params = [])
    {
        $devNotes = DevNotes::findOrFail($id);

        return DB::transaction(function () use ($params, $devNotes) {
            $devNotes->update($params);

            return $devNotes;
        });
    }

    public function delete($id)
    {
        $devNotes  = DevNotes::findOrFail($id);

        return $devNotes->delete();
    }
}
