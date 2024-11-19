<?php

namespace App\Repositories\Admin;

use App\Models\NpdDesignRequestNotes;
use App\Models\NpdPlannerRequestNotes;
use App\Models\PeRequestNotes;
use App\Repositories\Admin\Interfaces\NpdDesignRequestNotesRepositoryInterface;
use App\Repositories\Admin\Interfaces\NpdDesignRequestTypeFileAttachmentsRepositoryInterface;
use App\Repositories\Admin\Interfaces\NpdPlannerRequestNotesRepositoryInterface;
use App\Repositories\Admin\Interfaces\PeRequestNotesRepositoryInterface;
use DB;

use Illuminate\Database\Eloquent\Model;

class NpdPlannerRequestNotesRepository implements NpdPlannerRequestNotesRepositoryInterface
{
    public function findAll($options = [])
    {
        $perPage = $options['per_page'] ?? null;
        $orderByFields = $options['order'] ?? [];
        $id = $options['id'] ?? [];

        $npdPlannerRequestNotes = new NpdPlannerRequestNotes();

        if ($id) {
            $npdPlannerRequestNotes = $npdPlannerRequestNotes
                ->where('task_id', $id);
        }

        if ($orderByFields) {
            foreach ($orderByFields as $field => $sort) {
                $npdPlannerRequestNotes = $npdPlannerRequestNotes->orderBy($field, $sort);
            }
        }

        if ($perPage) {
            return $npdPlannerRequestNotes->paginate($perPage);
        }

        $npdPlannerRequestNotes = $npdPlannerRequestNotes->get();

        return $npdPlannerRequestNotes;
    }

    public function findById($id)
    {
        return NpdPlannerRequestNotes::findOrFail($id);
    }

    public function create($params = [])
    {
        return DB::transaction(function () use ($params) {
            $npdPlannerRequestNotes = NpdPlannerRequestNotes::create($params);
//            $this->syncRolesAndPermissions($params, $campaignBrand);

            return $npdPlannerRequestNotes;
        });
    }

    public function update($id, $params = [])
    {
        $npdPlannerRequestNotes = NpdPlannerRequestNotes::findOrFail($id);

        return DB::transaction(function () use ($params, $npdPlannerRequestNotes) {
            $npdPlannerRequestNotes->update($params);

            return $npdPlannerRequestNotes;
        });
    }

    public function delete($id)
    {
        $npdPlannerRequestNotes  = NpdPlannerRequestNotes::findOrFail($id);

        return $npdPlannerRequestNotes->delete();
    }
}
