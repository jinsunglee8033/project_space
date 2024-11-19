<?php

namespace App\Repositories\Admin;

use App\Models\LegalRequestNotes;
use App\Models\QraRequestNotes;
use App\Repositories\Admin\Interfaces\DevNotesRepositoryInterface;
use App\Repositories\Admin\Interfaces\LegalRequestRepositoryInterface;
use App\Repositories\Admin\Interfaces\QraRequestNotesRepositoryInterface;
use App\Repositories\Admin\Interfaces\QraRequestRepositoryInterface;
use DB;


use App\Models\DevNotes;
use Illuminate\Database\Eloquent\Model;

class LegalRequestNotesRepository implements LegalRequestRepositoryInterface
{
    public function findAll($options = [])
    {
        $perPage = $options['per_page'] ?? null;
        $orderByFields = $options['order'] ?? [];
        $id = $options['id'] ?? [];

        $legalRequestNotes = new LegalRequestNotes();

        if ($id) {
            $legalRequestNotes = $legalRequestNotes
                ->where('task_id', $id);
        }

        if ($orderByFields) {
            foreach ($orderByFields as $field => $sort) {
                $legalRequestNotes = $legalRequestNotes->orderBy($field, $sort);
            }
        }

        if ($perPage) {
            return $legalRequestNotes->paginate($perPage);
        }

        $legalRequestNotes = $legalRequestNotes->get();

        return $legalRequestNotes;
    }

    public function findById($id)
    {
        return LegalRequestNotes::findOrFail($id);
    }

    public function create($params = [])
    {
        return DB::transaction(function () use ($params) {
            $legalRequestNotes = LegalRequestNotes::create($params);
//            $this->syncRolesAndPermissions($params, $campaignBrand);

            return $legalRequestNotes;
        });
    }

    public function update($id, $params = [])
    {
        $legalRequestNotes = LegalRequestNotes::findOrFail($id);

        return DB::transaction(function () use ($params, $legalRequestNotes) {
            $legalRequestNotes->update($params);

            return $legalRequestNotes;
        });
    }

    public function delete($id)
    {
        $legalRequestNotes  = LegalRequestNotes::findOrFail($id);

        return $legalRequestNotes->delete();
    }
}
