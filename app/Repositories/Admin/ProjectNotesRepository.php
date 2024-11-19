<?php

namespace App\Repositories\Admin;

use DB;

use App\Repositories\Admin\Interfaces\ProjectNotesRepositoryInterface;

use App\Models\ProjectNotes;

class ProjectNotesRepository implements ProjectNotesRepositoryInterface
{
    public function findAll($options = [])
    {
        $perPage = $options['per_page'] ?? null;
        $orderByFields = $options['order'] ?? [];
        $id = $options['id'] ?? [];

        $projectNotes = new ProjectNotes();

        if ($id) {
            $projectNotes = $projectNotes
                ->where('id', $id);
        }

        if ($orderByFields) {
            foreach ($orderByFields as $field => $sort) {
                $projectNotes = $projectNotes->orderBy($field, $sort);
            }
        }

        if ($perPage) {
            return $projectNotes->paginate($perPage);
        }

        $projectNotes = $projectNotes->get();

        return $projectNotes;
    }

    public function findById($id)
    {
        return ProjectNotes::findOrFail($id);
    }

    public function create($params = [])
    {
        return DB::transaction(function () use ($params) {
            $projectNotes = ProjectNotes::create($params);
//            $this->syncRolesAndPermissions($params, $projectBrand);

            return $projectNotes;
        });
    }

    public function update($id, $params = [])
    {
        $projectNotes = ProjectNotes::findOrFail($id);

        return DB::transaction(function () use ($params, $projectNotes) {
            $projectNotes->update($params);

            return $projectNotes;
        });
    }

    public function delete($id)
    {
        $projectNotes  = ProjectNotes::findOrFail($id);

        return $projectNotes->delete();
    }

    public function get_launch_date_history($id)
    {
        return DB::select('select pn.note as note, 
                                pn.created_at as created_at, 
                                pn.user_id as user_id,
                                concat(u.first_name, " ", u.last_name) as author_name
                            from project_notes pn
                            left join users u on u.id = pn.user_id
                            where pn.type = "project"
                            and pn.note like "%Launch Date%"
                            and pn.id =:id order by pn.created_at desc', [
            'id' => $id
        ]);
    }
}
