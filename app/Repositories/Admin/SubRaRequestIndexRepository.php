<?php

namespace App\Repositories\Admin;

use App\Models\SubQraRequestIndex;
use App\Models\SubRaRequestIndex;
use App\Repositories\Admin\Interfaces\SubQraRequestIndexRepositoryInterface;
use App\Repositories\Admin\Interfaces\SubRaRequestIndexRepositoryInterface;
use DB;
use Illuminate\Database\Eloquent\Model;


class SubRaRequestIndexRepository implements SubRaRequestIndexRepositoryInterface
{
    public function findAll($options = [])
    {
        $perPage = $options['per_page'] ?? null;
        $orderByFields = $options['order'] ?? [];
        $id = $options['id'] ?? [];

        $projectTaskIndex = new SubRaRequestIndex();

        if ($id) {
            $projectTaskIndex = $projectTaskIndex
                ->where('id', $id);
        }

        if ($orderByFields) {
            foreach ($orderByFields as $field => $sort) {
                $projectTaskIndex = $projectTaskIndex->orderBy($field, $sort);
            }
        }

        if ($perPage) {
            return $projectTaskIndex->paginate($perPage);
        }

        $projectTaskIndex = $projectTaskIndex->get();

        return $projectTaskIndex;
    }

    public function findById($id)
    {
        return SubRaRequestIndex::findOrFail($id);
    }

    public function create($params = [])
    {
        return DB::transaction(function () use ($params) {
            $projectTaskIndex = SubRaRequestIndex::create($params);
//            $this->syncRolesAndPermissions($params, $campaignBrand);

            return $projectTaskIndex;
        });
    }

    public function update($id, $params = [])
    {
        $projectTaskIndex = SubRaRequestIndex::findOrFail($id);

        return DB::transaction(function () use ($params, $projectTaskIndex) {
            $projectTaskIndex->update($params);

            return $projectTaskIndex;
        });
    }

    public function delete($id)
    {
        $projectTaskIndex  = SubRaRequestIndex::findOrFail($id);

        return $projectTaskIndex->delete();
    }

    public function get_task_list_request($cur_user, $assignee, $team, $status)
    {
        if($cur_user != '') {
            $cur_user_filter = $cur_user;
        }else{
            $cur_user_filter = ' ';
        }
        if($assignee != '') {
            $assignee_filter = ' and t.assignee ="' . $assignee . '" ';
        }else{
            $assignee_filter = ' ';
        }
        if($team != '') {
            $team_filter = ' and p.team ="' . $team . '" ';
        }else{
            $team_filter = ' ';
        }
        if($status != '') {
            $status_filter = ' and i.status ="' . $status . '" ';
        }else{
            $status_filter = ' ';
        }

        return DB::select(
            '
            select t.ra_request_type_id as ra_request_type_id,
                p.name as name,
                p.team as team,
                p.brand as brand,
                p.status as project_status,
                i.status as status,
                pti.project_id as project_id,
                pti.id as task_id,
                pti.status as sub_task_status,
                t.type as request_type,
                t.registration_number as registration,   
                concat(u.first_name, " ", u.last_name) as author_name,
                concat(uu.first_name, " ", uu.last_name) as assignee_name,
                t.due_date as due_date,
                t.due_date_revision as due_date_revision,
                t.revision_cnt as revision_cnt
              from sub_ra_request_index i
            left join sub_ra_request_type t on t.ra_request_type_id = i.id
            left join project_task_index pti on pti.id = i.task_id
            left join project p on p.id = pti.project_id
            left join users u on u.id = t.author_id
            left join users uu on uu.id = t.assignee
            where p.status = "active"
              '. $cur_user_filter .'
              ' . $assignee_filter . '
              ' . $team_filter . '
              ' . $status_filter . '
                order by t.due_date desc
            ');
    }

    public function get_task_list_registration($cur_user, $assignee, $team, $status)
    {
        if($cur_user != '') {
            $cur_user_filter = $cur_user;
        }else{
            $cur_user_filter = ' ';
        }
        if($assignee != '') {
            $assignee_filter = ' and t.assignee ="' . $assignee . '" ';
        }else{
            $assignee_filter = ' ';
        }
        if($team != '') {
            $team_filter = ' and p.team ="' . $team . '" ';
        }else{
            $team_filter = ' ';
        }
        if($status != '') {
            $status_filter = ' and i.status ="' . $status . '" ';
        }else{
            $status_filter = ' ';
        }

        return DB::select(
            '
            select t.ra_request_type_id as ra_request_type_id,
                p.name as name,
                p.team as team,
                p.brand as brand,
                p.status as project_status,
                i.status as status,
                pti.project_id as project_id,
                pti.id as task_id,
                pti.status as sub_task_status,
                t.type as request_type,
                t.registration_number as registration,   
                concat(u.first_name, " ", u.last_name) as author_name,
                concat(uu.first_name, " ", uu.last_name) as assignee_name,
                t.due_date as due_date,
                t.due_date_revision as due_date_revision,
                t.revision_cnt as revision_cnt
              from sub_ra_request_index i
            left join sub_ra_request_type t on t.ra_request_type_id = i.id
            left join project_task_index pti on pti.id = i.task_id
            left join project p on p.id = pti.project_id
            left join users u on u.id = t.author_id
            left join users uu on uu.id = t.assignee
            where p.status = "active"
                and t.type in ("us_launch", "eu_launch", "canada_launch", "uk_launch")
              '. $cur_user_filter .'
              ' . $assignee_filter . '
              ' . $team_filter . '
              ' . $status_filter . '
                order by t.due_date desc
            ');
    }

}
