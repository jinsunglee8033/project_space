<?php

namespace App\Repositories\Admin;

use App\Models\CampaignNotes;
use App\Models\ProjectTaskIndex;
use App\Repositories\Admin\Interfaces\LegalRequestRepositoryInterface;
use App\Repositories\Admin\Interfaces\ProjectRepositoryInterface;
use App\Repositories\Admin\Interfaces\QcRequestNotesRepositoryInterface;
use DB;

use App\Models\Project;
use Illuminate\Database\Eloquent\Model;

class QcRequestRepository implements QcRequestNotesRepositoryInterface
{
    public function findAll($options = [])
    {
        $perPage = $options['per_page'] ?? null;
        $orderByFields = $options['order'] ?? [];

        $project = new ProjectTaskIndex();

        $project = $project->Select('project_task_index.id as id',
			'project.name as name',
			'project_task_index.project_id as project_id',
            'project.team as team',
            'project.status as status',
            'users.first_name as first_name',
            'users.last_name as last_name',)
            ->LeftJoin('project', function ($join) {
            $join->on('project.id', '=', 'project_task_index.project_id');
        })->LeftJoin('$task_type_qc_request', function($join){
            $join->on('$task_type_qc_request.id', '=', 'project_task_index.id');
        })->LeftJoin('users', function($join){
            $join->on('users.id', '=', 'project_task_index.author_id');
        })->Where('project_task_index.type', 'qc_request')
            ->Where('project.status', 'active');

//        if ($orderByFields) {
//            foreach ($orderByFields as $field => $sort) {
//                $project = $project->orderBy($field, $sort);
//            }
//        }
        if (!empty($options['filter']['q'])) {
            $project = $project->Where('project.name', 'LIKE', "%{$options['filter']['q']}%");
        }
//        if (!empty($options['filter']['status'])) {
//            $project = $project->where('status', $options['filter']['status']);
//        }
//        if (!empty($options['filter']['department'])) {
//            $project = $project->Where('department', $options['filter']['brand']);
//        }

        $project = $project->OrderBy('project_task_index.created_at', 'desc');
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

    public function get_request_types_by_project_id($project_id)
    {
        return DB::select('
                select *
                from project_task_index pti 
                left join project p on p.id = pti.project_id
                left join $task_type_qc_request ttqr on ttqr.id = pti.id
                where pti.type = "qc_request"
                where pti.project_id =:project_id order by p.id desc', [
            'project_id' => $project_id
        ]);
    }

    public function get_task_id_by_project_id($project_id)
    {
        $pti_obj = ProjectTaskIndex::where('project_id', $project_id)->first();
        return $pti_obj->id;
    }

    public function get_task_id_for_qc($project_id)
    {
        $pti_obj = ProjectTaskIndex::where('project_id', $project_id)
            ->where('type', 'qc_request')->first();
        return $pti_obj;
    }

    public function get_qc_request_list_by_task_id($task_id)
    {
        return DB::select('
            select i.status as status,
                    i.project_id as project_id,
                    i.author_id as author_id,
                    i.type as type,
                    t.work_type as work_type,
                    t.ship_date as ship_date,
                    t.qc_date,
                    t.po as po,
                    t.po_usd as pd_usd,
                    t.materials as materials,
                    t.item_type as item_type,
                    t.vendor_code as vendor_code,
                    t.country as country,
                    t.vendor_primary_contact_name as vendor_primary_contact_name,
                    t.vendor_primary_contact_email as vendor_primary_contact_email,
                    t.vendor_primary_contact_phone as vendor_primary_contact_phone,
                    t.facility_address as facility_address,
                    t.performed_by as performed_by,
                    concat(u.first_name, " ", u.last_name) as author_name,
                    t.created_at as created_at
            from project_task_index i
            left join task_type_qc_request t on t.task_id =  i.id
            left join users u on u.id = t.author_id
            where i.id =:task_id order by t.id desc', [
            'task_id' => $task_id
        ]);
    }

    static function get_qc_request_type($task_id)
    {
        return DB::select('
                select p.id as id,
                       p.task_id as project_id,
                       p.author_id as author_id,
                       concat(u.first_name, " ", u.last_name) as author_name,
                       p.request_type as type,
                       p.status as status,
                       p.created_at as created_at
                from sub_qc_request_index p 
                left join users u on u.id = p.author_id
                where p.task_id =:task_id order by p.id desc', [
            'task_id' => $task_id
        ]);
    }

}
