<?php

namespace App\Repositories\Admin;

use App\Models\Team;
use App\Repositories\Admin\Interfaces\TeamRepositoryInterface;
use App\Repositories\Admin\Interfaces\VendorRepositoryInterface;
use DB;

use App\Models\Vendor;
use Illuminate\Database\Eloquent\Model;

class TeamRepository implements TeamRepositoryInterface
{
    public function findAll($options = [])
    {
        $perPage = $options['per_page'] ?? null;
        $orderByFields = $options['order'] ?? [];

        $team = new Team();

        if ($orderByFields) {
            foreach ($orderByFields as $field => $sort) {
                $team = $team->orderBy($field, $sort);
            }
        }
        if (!empty($options['filter']['q'])) {
            $team = $team->Where('name', 'LIKE', "%{$options['filter']['q']}%");
        }
        if (!empty($options['filter']['is_active'])) {
            $team = $team->Where('is_active', 'LIKE', "%{$options['filter']['is_active']}%");
        }
        if (!empty($options['filter']['npd'])) {
            $team = $team->Where('npd', '=', "{$options['filter']['npd']}");
        }
//        if (!empty($options['filter']['status'])) {
//            $team = $team->where('status', $options['filter']['status']);
//        }
//        if (!empty($options['filter']['department'])) {
//            $team = $team->Where('department', $options['filter']['brand']);
//        }
//        if (!empty($options['filter']['category'])) {
//            $team = $team->Where('category', $options['filter']['category']);
//        }
        if ($perPage) {
            return $team->paginate($perPage);
        }

        $team = $team->get();

        return $team;
    }

    public function findById($id)
    {
        return Team::findOrFail($id);
    }

    public function create($params = [])
    {
        return DB::transaction(function () use ($params) {

            $team = Team::create($params);

            return $team;
        });
    }

    public function update($id, $params = [])
    {
        $team = Team::findOrFail($id);

        return DB::transaction(function () use ($params, $team) {
            $team->update($params);

            return $team;
        });
    }

    public function delete($id)
    {
        $team  = Team::findOrFail($id);

        return $team->delete();
    }

}
