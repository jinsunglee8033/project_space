@extends('layouts.dashboard')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>NPD PO Request List View</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></div>
            <div class="breadcrumb-item">NPD PO Request List View</div>
        </div>
    </div>

    <div class="section-body">

        @include('admin.npd_po_request._filter_list')

        <div class="row" style="margin-top: 15px;">
            <div class="col-12">
                <div class="card">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <tbody>
                                    <tr>
                                        <th>Task Id</th>
                                        <th>Requester</th>
                                        <th>Buyer</th>
                                        <th>Team</th>
                                        <th>Priority</th>
                                        <th>Plant</th>
                                        <th>Materials</th>
                                        <th>Price Status</th>
                                        <th>Due Date</th>
                                        <th>Status</th>
                                        <th>PO#</th>
                                        <th>Action</th>
                                    </tr>

                                    @foreach ($task_list as $task)
                                        <tr>
                                            <td>{{ $task->task_id }}</td>
                                            <td>{{ $task->author_name }}</td>
                                            <td>{{ $task->assignee_name}}</td>
                                            <td>{{ $task->team }}</td>
                                            <td>{{ $task->priority }}</td>
                                            <td style="width: 250px;">{!! str_replace(",", "<br/> ", $task->set_up_plant) !!}</td>
                                            <td>{{ mb_strimwidth($task->materials, 0,80, '...') }}</td>
                                            <td>{{ $task->price_set_up }}</td>
                                            <td>{{ $task->due_date }}</td>
                                            <td>{{ strtoupper(str_replace('_', ' ', $task->status)) }}</td>
                                            <td>{{ $task->po }}</td>
                                            <td>
                                                <a href="{{ url('admin/npd_po_request/'. $task->project_id .'/edit#'.$task->task_id)}}" class="btn btn-primary" style="border-radius: 20px; font-size: x-small;">
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
