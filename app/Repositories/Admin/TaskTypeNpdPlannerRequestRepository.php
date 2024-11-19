<?php

namespace App\Repositories\Admin;

use App\Models\ProjectTaskIndex;
use App\Models\TaskTypeNpdPlannerRequest;
use App\Models\TaskTypeNpdPoRequest;
use App\Models\Team;
use App\Repositories\Admin\Interfaces\TaskTypeMmRequestRepositoryInterface;
use App\Repositories\Admin\Interfaces\TaskTypeNpdPlannerRequestRepositoryInterface;
use App\Repositories\Admin\Interfaces\TaskTypeNpdPoRequestRepositoryInterface;
use DB;

use App\Models\TaskTypeMmRequest;
use Illuminate\Database\Eloquent\Model;

class TaskTypeNpdPlannerRequestRepository implements TaskTypeNpdPlannerRequestRepositoryInterface
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
            'users.last_name as last_name')
            ->LeftJoin('project', function ($join) {
                $join->on('project.id', '=', 'project_task_index.project_id')
                    ->WhereIn('project.team',
                        array('Red Appliance (A&A)',
                            'Red Accessory & Jewelry (A&A)',
                            'Red Fashion & Hair Cap (A&A)',
                            'Red Brush & Implement (A&A)',
                            'Red Trade Marketing (A&A)'));
            })->LeftJoin('task_type_npd_planner_request', function($join){
                $join->on('task_type_npd_planner_request.id', '=', 'project_task_index.id');
            })->LeftJoin('users', function($join){
                $join->on('users.id', '=', 'project_task_index.author_id');
            })->Where('project_task_index.type', 'npd_planner_request')
            ->Where('project.status', 'active')
            ->Where('project_task_index.status', '!=', 'action_skip');

        if (!empty($options['filter']['q'])) {
            $project = $project->Where('project.name', 'LIKE', "%{$options['filter']['q']}%");
        }
        if (!empty($options['filter']['status'])) {
            $project = $project->where('status', $options['filter']['status']);
        }
        if (!empty($options['filter']['cur_user'])) {
            $project = $project->whereIn('project_task_index.author_id', $options['filter']['cur_user']);
        }

        $project = $project->OrderBy('project_task_index.created_at', 'desc');
        if ($perPage) {
            return $project->paginate($perPage);
        }
        $project = $project->get();

        return $project;
    }

    public function findAll_ivy($options = [])
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
                $join->on('project.id', '=', 'project_task_index.project_id')
                ->WhereIn('project.team',
                        array('Ivy Nail (ND)',
                            'Ivy Lash (LD)',
                            'Ivy Cosmetic (C&H)',
                            'Ivy Hair Care (C&H)'));
            })->LeftJoin('task_type_npd_planner_request', function($join){
                $join->on('task_type_npd_planner_request.id', '=', 'project_task_index.id');
            })->LeftJoin('users', function($join){
                $join->on('users.id', '=', 'project_task_index.author_id');
            })->Where('project_task_index.type', 'npd_planner_request')
            ->Where('project_task_index.status', '!=', 'action_skip')
            ->Where('project.status', 'active');

//        if ($orderByFields) {
//            foreach ($orderByFields as $field => $sort) {
//                $project = $project->orderBy($field, $sort);
//            }
//        }
        if (!empty($options['filter']['q'])) {
            $project = $project->Where('project.name', 'LIKE', "%{$options['filter']['q']}%");
        }
        if (!empty($options['filter']['cur_user'])) {
            $project = $project->whereIn('project_task_index.author_id', $options['filter']['cur_user']);
        }
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

    public function findAllByTaskId($task_id)
    {
        $taskTypeNpdPlannerRequest = new TaskTypeNpdPlannerRequest();
        return $taskTypeNpdPlannerRequest->where('task_id', $task_id)->get();
    }

    public function deleteByTaskId($task_id)
    {
        $taskTypeNpdPlannerRequest = new TaskTypeNpdPlannerRequest();
        return $taskTypeNpdPlannerRequest->where('task_id', $task_id)->delete();
    }

    public function findById($id)
    {
        return TaskTypeNpdPlannerRequest::findOrFail($id);
    }

    public function findByTaskId($task_id)
    {
        $taskTypeNpdPlannerRequest = new TaskTypeNpdPlannerRequest();
        return $taskTypeNpdPlannerRequest->where('task_id', $task_id)->first();
    }

    public function create($params = [])
    {
        return DB::transaction(function () use ($params) {
            $taskTypeNpdPlannerRequest = TaskTypeNpdPlannerRequest::create($params);
            $this->syncRolesAndPermissions($params, $taskTypeNpdPlannerRequest);

            return $taskTypeNpdPlannerRequest;
        });
    }

    public function update($id, $params = [])
    {
        $taskTypeNpdPlannerRequest = TaskTypeNpdPlannerRequest::findOrFail($id);

        return DB::transaction(function () use ($params, $taskTypeNpdPlannerRequest) {
            $taskTypeNpdPlannerRequest->update($params);

            return $taskTypeNpdPlannerRequest;
        });
    }

    public function delete($id)
    {
        $taskTypeNpdPlannerRequest  = TaskTypeNpdPlannerRequest::findOrFail($id);

        return $taskTypeNpdPlannerRequest->delete();
    }

    public function get_task_id_for_npd_planner($project_id)
    {
        $pti_obj = ProjectTaskIndex::where('project_id', $project_id)
            ->where('type', 'npd_planner_request')->first();
        return $pti_obj;
    }

    public function get_action_requested_list($cur_user, $request_group, $request_type, $team, $brand)
    {
        if($cur_user != '') {
            $cur_user_filter = $cur_user;
        }else{
            $cur_user_filter = ' ';
        }
        if($request_group == "Red Trade Marketing (A&A)"){
            $team_group = '("Red Appliance (A&A)", "Red Accessory & Jewelry (A&A)", "Red Fashion & Hair Cap (A&A)", "Red Brush & Implement (A&A)", "Red Trade Marketing (A&A)")';
        }else if($request_group == "B2B Marketing"){
            $team_group = '("Ivy Nail (ND)", "Ivy Lash (LD)", "Ivy Cosmetic (C&H)", "Ivy Hair Care (C&H)", "Kiss Nail (ND)" )';
        }
        if($request_type != '') {
            $request_type_filter = ' and t.request_type ="' . $request_type . '" ';
        }else{
            $request_type_filter = ' ';
        }
        if($team != '') {
            $team_filter = ' and p.team ="' . $team . '" ';
        }else{
            $team_filter = ' ';
        }
        if($brand != '') {
            $brand_filter = ' and p.brand ="' . $brand . '" ';
        }else{
            $brand_filter = ' ';
        }
        return DB::select(
            'select  t.npd_planner_request_type_id as npd_planner_request_type_id,
                    t.type as task_type,
                    t.request_type as request_type,
                    t.created_at as created_at,
                    t.due_date as due_date,
                    t.due_date_revision as due_date_revision,
                    t.revision_cnt as revision_cnt,
                    p.name as name,
                    p.brand as brand,
                    pti.project_id as project_id,
                    pti.id as task_id,
                    u.first_name as first_name,
                    u.last_name as last_name,
                    t.author_id as author_id,
                    p.team as team,
                    i.status as status,
                    i.decline_reason as decline_reason,
                    concat(u.first_name, " ", u.last_name) as author_name,
                    concat(uu.first_name, " ", uu.last_name) as assignee_name
                from sub_npd_planner_request_index i
                left join sub_npd_planner_request_type t on t.npd_planner_request_type_id = i.id
                left join project_task_index pti on pti.id = i.task_id
                left join project p on p.id = pti.project_id    
                left join users u on u.id = t.author_id
                left join users uu on uu.id = t.assignee
                where i.status = "action_requested" 
                and p.status != "action_skip"
                and p.status = "active"
                and p.team in '. $team_group .' 
                      ' . $cur_user_filter . '
                      ' . $request_type_filter . '
                      ' . $team_filter . '
                      ' . $brand_filter . '
                order by t.due_date asc
            ');
    }

    public function get_in_progress_list($cur_user, $request_group, $request_type, $team, $brand)
    {
        if($cur_user != '') {
            $cur_user_filter = $cur_user;
        }else{
            $cur_user_filter = ' ';
        }
        if($request_group == "Red Trade Marketing (A&A)"){
            $team_group = '("Red Appliance (A&A)", "Red Accessory & Jewelry (A&A)", "Red Fashion & Hair Cap (A&A)", "Red Brush & Implement (A&A)", "Red Trade Marketing (A&A)")';
        }else if($request_group == "B2B Marketing"){
            $team_group = '("Ivy Nail (ND)", "Ivy Lash (LD)", "Ivy Cosmetic (C&H)", "Ivy Hair Care (C&H)", "Kiss Nail (ND)")';
        }
        if($request_type != '') {
            $request_type_filter = ' and t.request_type ="' . $request_type . '" ';
        }else{
            $request_type_filter = ' ';
        }
        if($team != '') {
            $team_filter = ' and p.team ="' . $team . '" ';
        }else{
            $team_filter = ' ';
        }
        if($brand != '') {
            $brand_filter = ' and p.brand ="' . $brand . '" ';
        }else{
            $brand_filter = ' ';
        }
        return DB::select(
            'select  t.npd_planner_request_type_id as npd_planner_request_type_id,
                    t.type as task_type,
                    t.request_type as request_type,
                    t.created_at as created_at,
                    t.due_date as due_date,
                    t.due_date_revision as due_date_revision,
                    t.revision_cnt as revision_cnt,
                    p.name as name,
                    p.brand as brand,
                    pti.project_id as project_id,
                    pti.id as task_id,
                    u.first_name as first_name,
                    u.last_name as last_name,
                    t.author_id as author_id,
                    p.team as team,
                    i.status as status,
                    i.decline_reason as decline_reason,
                    concat(u.first_name, " ", u.last_name) as author_name,
                    concat(uu.first_name, " ", uu.last_name) as assignee_name
                from sub_npd_planner_request_index i
                left join sub_npd_planner_request_type t on t.npd_planner_request_type_id = i.id
                left join project_task_index pti on pti.id = i.task_id
                left join project p on p.id = pti.project_id    
                left join users u on u.id = t.author_id
                left join users uu on uu.id = t.assignee
                where i.status = "in_progress" 
                and p.status != "action_skip"
                and p.status = "active"
                and p.team in '. $team_group .'   
                    ' . $cur_user_filter . '
                    ' . $request_type_filter . '
                    ' . $team_filter . '
                    ' . $brand_filter . '
                order by t.due_date asc
            ');
    }

    public function get_action_review_list($cur_user, $request_group, $request_type, $team, $brand)
    {
        if($cur_user != '') {
            $cur_user_filter = $cur_user;
        }else{
            $cur_user_filter = ' ';
        }
        if($request_group == "Red Trade Marketing (A&A)"){
            $team_group = '("Red Appliance (A&A)", "Red Accessory & Jewelry (A&A)", "Red Fashion & Hair Cap (A&A)", "Red Brush & Implement (A&A)", "Red Trade Marketing (A&A)")';
        }else if($request_group == "B2B Marketing"){
            $team_group = '("Ivy Nail (ND)", "Ivy Lash (LD)", "Ivy Cosmetic (C&H)", "Ivy Hair Care (C&H)", "Kiss Nail (ND)")';
        }
        if($request_type != '') {
            $request_type_filter = ' and t.request_type ="' . $request_type . '" ';
        }else{
            $request_type_filter = ' ';
        }
        if($team != '') {
            $team_filter = ' and p.team ="' . $team . '" ';
        }else{
            $team_filter = ' ';
        }
        if($brand != '') {
            $brand_filter = ' and p.brand ="' . $brand . '" ';
        }else{
            $brand_filter = ' ';
        }
        return DB::select(
            'select  t.npd_planner_request_type_id as npd_planner_request_type_id,
                    t.type as task_type,
                    t.request_type as request_type,
                    t.created_at as created_at,
                    t.due_date as due_date,
                    t.due_date_revision as due_date_revision,
                    t.revision_cnt as revision_cnt,
                    p.name as name,
                    p.brand as brand,
                    pti.project_id as project_id,
                    pti.id as task_id,
                    u.first_name as first_name,
                    u.last_name as last_name,
                    t.author_id as author_id,
                    p.team as team,
                    i.status as status,
                    i.decline_reason as decline_reason,                        
                    concat(u.first_name, " ", u.last_name) as author_name,
                    concat(uu.first_name, " ", uu.last_name) as assignee_name
                from sub_npd_planner_request_index i
                left join sub_npd_planner_request_type t on t.npd_planner_request_type_id = i.id
                left join project_task_index pti on pti.id = i.task_id
                left join project p on p.id = pti.project_id    
                left join users u on u.id = t.author_id
                left join users uu on uu.id = t.assignee
                where i.status in ("action_review", "update_required")
                and p.status != "action_skip"
                and p.status = "active"
                and p.team in '. $team_group .'  
                      ' . $cur_user_filter . '
                      ' . $request_type_filter . '
                      ' . $team_filter . '
                      ' . $brand_filter . '
                order by t.due_date asc
            ');
    }

    public function get_action_completed_list($cur_user, $request_group, $request_type, $team, $brand)
    {
        if($cur_user != '') {
            $cur_user_filter = $cur_user;
        }else{
            $cur_user_filter = ' ';
        }
        if($request_group == "Red Trade Marketing (A&A)"){
            $team_group = '("Red Appliance (A&A)", "Red Accessory & Jewelry (A&A)", "Red Fashion & Hair Cap (A&A)", "Red Brush & Implement (A&A)", "Red Trade Marketing (A&A)")';
        }else if($request_group == "B2B Marketing"){
            $team_group = '("Ivy Nail (ND)", "Ivy Lash (LD)", "Ivy Cosmetic (C&H)", "Ivy Hair Care (C&H)", "Kiss Nail (ND)")';
        }
        if($request_type != '') {
            $request_type_filter = ' and t.request_type ="' . $request_type . '" ';
        }else{
            $request_type_filter = ' ';
        }
        if($team != '') {
            $team_filter = ' and p.team ="' . $team . '" ';
        }else{
            $team_filter = ' ';
        }
        if($brand != '') {
            $brand_filter = ' and p.brand ="' . $brand . '" ';
        }else{
            $brand_filter = ' ';
        }
        return DB::select(
            'select  t.npd_planner_request_type_id as npd_planner_request_type_id,
                    t.type as task_type,
                    t.request_type as request_type,
                    t.created_at as created_at,
                    t.due_date as due_date,
                    t.due_date_revision as due_date_revision,
                    t.revision_cnt as revision_cnt,
                    t.due_date_upload as due_date_upload,
                    t.uploaded_date as uploaded_date,
                    t.uploaded_user as uploaded_user,
                    p.name as name,
                    p.brand as brand,
                    pti.project_id as project_id,
                    pti.id as task_id,
                    u.first_name as first_name,
                    u.last_name as last_name,
                    t.author_id as author_id,
                    p.team as team,
                    i.status as status,
                    i.decline_reason as decline_reason,
                    concat(u.first_name, " ", u.last_name) as author_name,
                    concat(uu.first_name, " ", uu.last_name) as assignee_name,
                    concat(uuu.first_name, " ", uuu.last_name) as uploaded_by
                from sub_npd_planner_request_index i
                left join sub_npd_planner_request_type t on t.npd_planner_request_type_id = i.id
                left join project_task_index pti on pti.id = i.task_id
                left join project p on p.id = pti.project_id    
                left join users u on u.id = t.author_id
                left join users uu on uu.id = t.assignee
                left join users uuu on uuu.id = t.uploaded_user
                where i.status = "action_completed" 
                and p.status != "action_skip"
                and p.status = "active"
                and p.team in  '. $team_group .'   
                      '. $cur_user_filter .'
                    ' .  $request_type_filter . '
                    ' . $team_filter . '
                    ' . $brand_filter . '
                order by t.due_date asc
            ');
    }

    public function get_task_list($cur_user, $team, $status)
    {

        if($cur_user != '') {
            $cur_user_filter = $cur_user;
        }else{
            $cur_user_filter = ' ';
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

        $team_group = '("Ivy Nail (ND)", "Ivy Lash (LD)", "Ivy Cosmetic (C&H)", "Ivy Hair Care (C&H)", "Kiss Nail (ND)" )';

        return DB::select(
            '
            select t.npd_planner_request_type_id as npd_planner_request_type_id,
                p.name as name,
                p.team as team,
                p.brand as brand,
                p.status as project_status,
                i.status as status,
                pti.project_id as project_id,
                pti.id as task_id,
                pti.status as sub_task_status,
                t.request_type as request_type,
                concat(u.first_name, " ", u.last_name) as author_name,
                concat(uu.first_name, " ", uu.last_name) as assignee_name,
                t.due_date as due_date,
                t.due_date_revision as due_date_revision,
                t.revision_cnt as revision_cnt,
                t.created_at as created_at   
              from sub_npd_planner_request_index i
            left join sub_npd_planner_request_type t on t.npd_planner_request_type_id = i.id
            left join project_task_index pti on pti.id = i.task_id
            left join project p on p.id = pti.project_id
            left join users u on u.id = t.author_id
            left join users uu on uu.id = t.assignee
            where p.status = "active"
              and t.request_type = "change_request"
              and p.team in '. $team_group .' 
              '. $cur_user_filter .'
              ' . $team_filter . '
              ' . $status_filter . '
                order by t.due_date asc
            ');
    }

    public function get_npd_planner_request_list_by_task_id($task_id)
    {
        return DB::select('
            select i.status as status,
                    i.project_id as project_id,
                    i.author_id as author_id,
                    concat(u.first_name, " ", u.last_name) as author_name,
                    i.type as type,
                    t.assignee as assignee,
                    t.request_group as request_group,
                    t.project_code as project_code,
                    t.due_date_review as due_date_review,
                    t.due_date_upload as due_date_upload,
                    t.target_door_number as target_door_number,
                    t.sales_channel as sales_channel,
                    t.if_others_sales_channel as if_others_sales_channel,
                    t.expected_reorder_max as expected_reorder_max,
                    t.expected_reorder_low as expected_reorder_low,
                    t.expected_reorder_avg as expected_reorder_avg,
                    t.expected_sales as expected_sales,
                    t.benchmark_item as benchmark_item,
                    t.display_plan as display_plan,
                    t.if_others_display_plan as if_others_display_plan,
                    t.display_type as display_type,
                    t.penetration_type as penetration_type,
                    t.if_others_penetration_type as if_others_penetration_type,
                    t.tester as tester,
                    t.promotion_items as promotion_items,
                    t.if_others_promotion_items as if_others_promotion_items,
                    t.return_plan as return_plan,
                    t.due_date_upload as due_date_upload,
                    t.due_date_revision as due_date_revision,
                    t.revision_cnt as revision_cnt,   
                    concat(u.first_name, " ", u.last_name) as author_name,
                    concat(uu.first_name, " ", uu.last_name) as assignee_name,
                    t.task_id as task_id,
                    t.created_at as created_at
            from project_task_index i
            left join task_type_npd_planner_request t on t.task_id =  i.id
            left join users u on u.id = t.author_id
            left join users uu on uu.id = t.assignee
            where i.id =:task_id order by t.id desc', [
            'task_id' => $task_id
        ]);
    }

    public function get_npd_planner_request_by_task_id($task_id)
    {
        $rs = TaskTypeNpdPlannerRequest::where('task_id', $task_id)->first();
        return $rs;
    }


    static function get_npd_planner_request_type($task_id)
    {
        return DB::select('
                select p.id as id,
                       p.task_id as project_id,
                       p.author_id as author_id,
                       concat(u.first_name, " ", u.last_name) as author_name,
                       p.request_type as type,
                       p.status as status,
                       p.created_at as created_at
                from sub_npd_planner_request_index p 
                left join users u on u.id = p.author_id
                where p.task_id =:task_id order by p.id desc', [
            'task_id' => $task_id
        ]);
    }
}
