<?php

namespace App\Repositories\Admin;

use App\Models\NpdDesignRequestNotes;
use App\Models\PeRequestNotes;
use App\Repositories\Admin\Interfaces\NpdDesignRequestNotesRepositoryInterface;
use App\Repositories\Admin\Interfaces\NpdDesignRequestTypeFileAttachmentsRepositoryInterface;
use App\Repositories\Admin\Interfaces\PeRequestNotesRepositoryInterface;
use DB;

use Illuminate\Database\Eloquent\Model;

class NpdDesignRequestNotesRepository implements NpdDesignRequestNotesRepositoryInterface
{
    public function findAll($options = [])
    {
        $perPage = $options['per_page'] ?? null;
        $orderByFields = $options['order'] ?? [];
        $id = $options['id'] ?? [];

        $npdDesignRequestNotes = new NpdDesignRequestNotes();

        if ($id) {
            $npdDesignRequestNotes = $npdDesignRequestNotes
                ->where('task_id', $id);
        }

        if ($orderByFields) {
            foreach ($orderByFields as $field => $sort) {
                $npdDesignRequestNotes = $npdDesignRequestNotes->orderBy($field, $sort);
            }
        }

        if ($perPage) {
            return $npdDesignRequestNotes->paginate($perPage);
        }

        $npdDesignRequestNotes = $npdDesignRequestNotes->get();

        return $npdDesignRequestNotes;
    }

    public function findById($id)
    {
        return NpdDesignRequestNotes::findOrFail($id);
    }

    public function create($params = [])
    {
        return DB::transaction(function () use ($params) {
            $npdDesignRequestNotes = NpdDesignRequestNotes::create($params);
//            $this->syncRolesAndPermissions($params, $campaignBrand);

            return $npdDesignRequestNotes;
        });
    }

    public function update($id, $params = [])
    {
        $npdDesignRequestNotes = NpdDesignRequestNotes::findOrFail($id);

        return DB::transaction(function () use ($params, $npdDesignRequestNotes) {
            $npdDesignRequestNotes->update($params);

            return $npdDesignRequestNotes;
        });
    }

    public function delete($id)
    {
        $npdDesignRequestNotes  = NpdDesignRequestNotes::findOrFail($id);

        return $npdDesignRequestNotes->delete();
    }
}
