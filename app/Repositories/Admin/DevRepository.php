<?php

namespace App\Repositories\Admin;

use App\Repositories\Admin\Interfaces\DevRepositoryInterface;
use DB;

use App\Models\Dev;
use Illuminate\Database\Eloquent\Model;

class DevRepository implements DevRepositoryInterface
{
    public function findAll($options = [])
    {
        $dev = new Dev();

        $dev = $dev->orderBy('seq', 'asc')->get();

        return $dev;
    }

    public function findById($id)
    {
        return Dev::findOrFail($id);
    }

    public function create($params = [])
    {
        return DB::transaction(function () use ($params) {
            $dev = Dev::create($params);
            return $dev;
        });
    }

    public function update($id, $params = [])
    {
        $dev = Dev::findOrFail($id);

        return DB::transaction(function () use ($params, $dev) {
            $dev->update($params);

            return $dev;
        });
    }

    public function delete($id)
    {
        $role  = Dev::findOrFail($id);

        return $role->delete();
    }

    public function get_jira_dev_requested($priority, $developer)
    {
        if($priority != '') {
            $priority_filter = ' and d.priority ="' . $priority . '" ';
        }else{
            $priority_filter = ' ';
        }

        if($developer != '') {
            $developer_filter = ' and d.assign_to =' . $developer . ' ';
        }else{
            $developer_filter = ' ';
        }

        return DB::select(
            'select  d.id as dev_id,
                        d.title as title,
                        d.type as type,
                        d.domain as domain,
                        d.description as description,
                        u.first_name as request_by,
                        v.first_name as assign_to,
                        d.priority as priority,
                        d.status as status,
                        d.created_at as created_at,
                        d.updated_at as updated_at
                from dev d
                left join users u on u.id = d.request_by
                left join users v on v.id = d.assign_to
                where d.status in ("dev_requested")
                  ' . $priority_filter . '
                  ' . $developer_filter . '
                order by d.created_at asc');
    }

    public function get_jira_dev_to_do($priority, $developer)
    {
        if($priority != '') {
            $priority_filter = ' and d.priority ="' . $priority . '" ';
        }else{
            $priority_filter = ' ';
        }

        if($developer != '') {
            $developer_filter = ' and d.assign_to =' . $developer . ' ';
        }else{
            $developer_filter = ' ';
        }

        return DB::select(
            'select  d.id as dev_id,
                        d.title as title,
                        d.type as type,
                        d.domain as domain,
                        d.description as descriptoin,
                        u.first_name as request_by,
                        v.first_name as assign_to,
                        d.priority as priority,
                        d.status as status,
                        d.created_at as created_at,
                        d.updated_at as updated_at
                from dev d
                left join users u on u.id = d.request_by
                left join users v on v.id = d.assign_to
                where d.status in ("dev_to_do")
                  ' . $priority_filter . '
                  ' . $developer_filter . '
                order by d.created_at asc');
    }

    public function get_jira_dev_in_progress($priority, $developer)
    {
        if($priority != '') {
            $priority_filter = ' and d.priority ="' . $priority . '" ';
        }else{
            $priority_filter = ' ';
        }

        if($developer != '') {
            $developer_filter = ' and d.assign_to =' . $developer . ' ';
        }else{
            $developer_filter = ' ';
        }

        return DB::select(
            'select  d.id as dev_id,
                        d.title as title,
                        d.type as type,
                        d.domain as domain,
                        d.description as descriptoin,
                        u.first_name as request_by,
                        v.first_name as assign_to,
                        d.priority as priority,
                        d.status as status,
                        d.created_at as created_at,
                        d.updated_at as updated_at
                from dev d
                left join users u on u.id = d.request_by
                left join users v on v.id = d.assign_to
                where d.status in ("dev_in_progress")
                  ' . $priority_filter . '
                  ' . $developer_filter . '
                order by d.created_at asc');
    }

    public function get_jira_dev_review($priority, $developer)
    {
        if($priority != '') {
            $priority_filter = ' and d.priority ="' . $priority . '" ';
        }else{
            $priority_filter = ' ';
        }

        if($developer != '') {
            $developer_filter = ' and d.assign_to =' . $developer . ' ';
        }else{
            $developer_filter = ' ';
        }

        return DB::select(
            'select  d.id as dev_id,
                        d.title as title,
                        d.type as type,
                        d.domain as domain,
                        d.description as descriptoin,
                        u.first_name as request_by,
                        v.first_name as assign_to,
                        d.priority as priority,
                        d.status as status,
                        d.created_at as created_at,
                        d.updated_at as updated_at
                from dev d
                left join users u on u.id = d.request_by
                left join users v on v.id = d.assign_to
                where d.status in ("dev_review")
                  ' . $priority_filter . '
                  ' . $developer_filter . '
                order by d.created_at asc');
    }

    public function get_jira_dev_done($priority, $developer)
    {
        if($priority != '') {
            $priority_filter = ' and d.priority ="' . $priority . '" ';
        }else{
            $priority_filter = ' ';
        }

        if($developer != '') {
            $developer_filter = ' and d.assign_to =' . $developer . ' ';
        }else{
            $developer_filter = ' ';
        }

        return DB::select(
            'select  d.id as dev_id,
                        d.title as title,
                        d.type as type,
                        d.domain as domain,
                        d.description as descriptoin,
                        u.first_name as request_by,
                        v.first_name as assign_to,
                        d.priority as priority,
                        d.status as status,
                        d.created_at as created_at,
                        d.updated_at as updated_at
                from dev d
                left join users u on u.id = d.request_by
                left join users v on v.id = d.assign_to
                where d.status in ("dev_done")
                  ' . $priority_filter . '
                  ' . $developer_filter . '
                and d.updated_at >= DATE_ADD(CURDATE(), INTERVAL -21 DAY)
                order by d.created_at asc');
    }

    public function get_dev_approval_list()
    {
        return DB::select(
            'select d.id as dev_id,
                    d.title as title,
                    d.type as type,
                    d.domain as domain,
                    u.first_name as requested_by,
                    k.first_name as assign_to,
                    d.priority as priority,
                    d.created_at as created_at
                from dev d
                left join users u on u.id = d.request_by
                left join users k on k.id = d.assign_to
                where d.status = "dev_requested"
                order by d.created_at desc');
    }

    public function get_dev_archives_list()
    {
        return DB::select(
            'select d.id as dev_id,
                    d.title as title,
                    d.type as type,
                    d.domain as domain,
                    u.first_name as requested_by,
                    k.first_name as assign_to,
                    d.priority as priority,
                    d.created_at as created_at
                from dev d
                left join users u on u.id = d.request_by
                left join users k on k.id = d.assign_to
                where d.status = "dev_done"
                order by d.created_at desc');
    }

}
