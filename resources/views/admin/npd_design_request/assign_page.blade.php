@extends('layouts.dashboard')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>NPD Designer Assign List</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></div>
            <div class="breadcrumb-item">NPD Designer Assign List</div>
        </div>
    </div>

    <div class="section-body">

        @include('admin.npd_design_request._filter_assign')

        <div class="row" style="margin-top: 15px;">
            <div class="col-12">
                <div class="card">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <tbody>
                                    <tr>
                                        <th>Task Id</th>
                                        <th>Priority</th>
                                        <th>Design Group</th>
                                        <th>Team</th>
                                        <th>Brand</th>
                                        <th>Project Title</th>
                                        <th>Request Type</th>
                                        <th>Created</th>
                                        <th>Action</th>
                                    </tr>

                                    @foreach ($task_list as $task)
                                        <tr>
                                            <td>{{ $task->npd_design_request_type_id }}</td>
                                            <?php
                                                if($task->priority == 'Urgent'){
                                                    $priotity_color = '#FF0000';
                                                }else{
                                                    $priotity_color = '#3d57d9';
                                                }
                                            ?>
                                            <td style="color: {{$priotity_color}};">{{ $task->priority }}</td>
                                            <td>{{ $task->design_group }}</td>
                                            <td>{{ $task->team }}</td>
                                            <td>{{ $task->brand }}</td>
                                            <td>{{ $task->name }}</td>
                                            <td>{{ $task->request_type }}</td>
                                            <td>{{ date('m/d/Y', strtotime($task->created_at)) }}</td>
                                            <td>
                                                <a href="{{ url('admin/npd_design_request/'. $task->project_id .'/edit#'.$task->npd_design_request_type_id)}}" class="btn btn-primary" style="border-radius: 20px; font-size: x-small;">
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
