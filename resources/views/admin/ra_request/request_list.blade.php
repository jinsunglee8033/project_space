@extends('layouts.dashboard')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>RA Registration List View</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></div>
            <div class="breadcrumb-item">RA Registration List View</div>
        </div>
    </div>

    <div class="section-body">

        @include('admin.ra_request._filter_request_list')

        <div class="row" style="margin-top: 15px;">
            <div class="col-12">
                <div class="card">
                    <div class="card-body p-0">
                        <div class="table-responsive table-container">
                            <table class="table table-striped">
                                <tbody>
                                    <tr>
                                        <th>Task Id</th>
                                        <th>Request Type</th>
{{--                                        <th>Priority</th>--}}
                                        <th>Status</th>
                                        <th>Assignee</th>
{{--                                        <th>Setup Plant</th>--}}
{{--                                        <th>Materials</th>--}}
                                        <th>Registration #</th>
                                        <th>Due Date</th>
                                        <th>PIC</th>
                                        <th>Team</th>
                                        <th>Action</th>
                                    </tr>

                                    @foreach ($task_list as $task)
                                        <tr>
                                            <td>{{ $task->ra_request_type_id }}</td>
                                            <td>{{ strtoupper($task->request_type) }}</td>
{{--                                            <td style="color: {{$status_color}};">--}}
{{--                                                {{ $task->priority }}--}}
{{--                                            </td>--}}
                                            <td>{{ strtoupper(str_replace('_', ' ', $task->status)) }}</td>
                                            <td>{{ $task->assignee_name}}</td>
{{--                                            <td style="width: 250px;">{!! str_replace(",", "<br/> ", $task->set_up_plant) !!}</td>--}}
{{--                                            <td>{{ mb_strimwidth($task->materials, 0,80, '...') }}</td>--}}
                                            <td>{{ $task->registration}}</td>
                                            <?php if($task->due_date_revision != null) {?>
                                            <td style="color: #4b0cb3;">{{ date('m/d/Y', strtotime($task->due_date_revision)) }}</td>
                                            <?php }else{?>
                                            <td>{{ date('m/d/Y', strtotime($task->due_date)) }}</td>
                                            <?php } ?>
                                            <td>{{ $task->author_name }}</td>
                                            <td>{{ $task->team }}</td>
                                            <td>
                                                <a href="{{ url('admin/ra_request/'. $task->project_id .'/edit#'.$task->ra_request_type_id)}}" class="btn btn-primary" style="border-radius: 20px; font-size: x-small;">
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
