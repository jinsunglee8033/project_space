@extends('layouts.dashboard')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>IVY Request (Change Request Only)</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></div>
            <div class="breadcrumb-item">IVY Request (List)</div>
        </div>
    </div>

    <div class="section-body">

        @include('admin.npd_planner_request._filter_list_ivy')

        <div class="row" style="margin-top: 15px;">
            <div class="col-12">
                <div class="card">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <tbody>
                                    <tr>
                                        <th>Task Id</th>
                                        <th>Project Title</th>
                                        <th>Request Type</th>
                                        <th>Assignee</th>
                                        <th>Team</th>
                                        <th>Brand</th>
                                        <th>Status</th>
                                        <th>Created</th>
                                        <th>Action</th>
                                    </tr>

                                    @foreach ($task_list as $task)
                                        <tr>
                                            <td>{{ $task->task_id }}</td>
                                            <td>{{ $task->name}}</td>
                                            <td>{{ strtoupper(str_replace('_', ' ', $task->request_type)) }}</td>
                                            <td>{{ $task->assignee_name}}</td>
                                            <td>{{ $task->team }}</td>
                                            <td>{{ $task->brand }}</td>
                                            <td>{{ strtoupper(str_replace('_', ' ', $task->status)) }}</td>
                                            <td>{{ date('m/d/Y', strtotime($task->created_at)) }}</td>
                                            <td>
                                                <a href="{{ url('admin/npd_planner_request/'. $task->project_id .'/edit#'.$task->npd_planner_request_type_id)}}" class="btn btn-primary" style="border-radius: 20px; font-size: x-small;">
                                                    Detail
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>



@endsection
