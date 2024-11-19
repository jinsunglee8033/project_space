<?php

namespace App\Repositories\Admin;

use App\Models\CampaignNotes;
use App\Repositories\Admin\Interfaces\ProjectRepositoryInterface;
use DB;

use App\Models\Project;
use Illuminate\Database\Eloquent\Model;

class ProjectRepository implements ProjectRepositoryInterface
{
    public function findAll($options = [])
    {
        $perPage = $options['per_page'] ?? null;
        $orderByFields = $options['order'] ?? [];

        $project = new Project();

        if ($orderByFields) {
            foreach ($orderByFields as $field => $sort) {
                $project = $project->orderBy($field, $sort);
            }
        }
        if (!empty($options['filter']['q'])) {
            $project = $project->Where('name', 'LIKE', "%{$options['filter']['q']}%");
        }
        if (!empty($options['filter']['id'])) {
            $project = $project->Where('id', $options['filter']['id']);
        }
        if (!empty($options['filter']['category'])) {
            $project = $project->whereIn('category', $options['filter']['category']);
        }
        if (!empty($options['filter']['status'])) {
            $project = $project->whereIn('status', $options['filter']['status']);
        }
        if (!empty($options['filter']['team'])) {
            $project = $project->Where('team', $options['filter']['team']);
        }
        if (!empty($options['filter']['cur_team'])) {
            $project = $project->Where('team', $options['filter']['cur_team']);
        }
        if (!empty($options['filter']['cur_team_group'])) {
            $project = $project->WhereIn('team', $options['filter']['cur_team_group']);
        }
        if (!empty($options['filter']['cur_user'])) {
            $project = $project->Where('author_id', $options['filter']['cur_user']);
        }
        if ($perPage) {
            return $project->paginate($perPage);
        }

        $project = $project->get();

        return $project;
    }

    public function findById($id)
    {
        return Project::findOrFail($id);
    }

    public function create($params = [])
    {
        return DB::transaction(function () use ($params) {

            $campaign = Project::create($params);

            return $campaign;
        });
    }

    public function update($id, $params = [])
    {
        $campaign = Project::findOrFail($id);

        return DB::transaction(function () use ($params, $campaign) {
            $campaign->update($params);

            return $campaign;
        });
    }

    public function delete($id)
    {
        $campaign  = Project::findOrFail($id);

        return $campaign->delete();
    }

}
