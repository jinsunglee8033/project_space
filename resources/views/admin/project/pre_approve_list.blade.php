@extends('layouts.dashboard')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>NPD Approval List</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></div>
            <div class="breadcrumb-item">NPD Approval List</div>
        </div>
    </div>

    <div class="section-body">

{{--        @include('admin.mm_request._filter_list')--}}

        <div class="row" style="margin-top: 15px;">
            <div class="col-12">
                <div class="card">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <tbody>
                                    <tr>
                                        <th>Id</th>
                                        <th>Status</th>
                                        <th>Project Name</th>
                                        <th>Team</th>
                                        <th>Brand</th>
                                        <th>Project Type</th>
                                        <th>Person In Charge</th>
                                        <th>Target Receiving Date</th>
                                        <th>Launch Date</th>
                                        <th>Created Date</th>
                                        <th>Action</th>
                                    </tr>

                                    @foreach ($projects as $project)
                                        <tr>
                                            <td>{{ $project->id }}</td>
                                            <?php
                                            if($project->status == 'pending'){
                                                $status_color = '#3d57d9';
                                            }else{
                                                $status_color = '#FF0000';
                                            }
                                            ?>
                                            <td style="color: {{$status_color}};">{{ strtoupper($project->status) }}</td>

                                            <td>{{ mb_strimwidth($project->name, 0,80, '...') }}</td>
                                            <td>{{ $project->team }}</td>
                                            <td>{{ $project->brand }}</td>
                                            <td>{{ $project->project_type }}</td>
                                            <td>{{ $project->author->first_name}} {{ $project->author->last_name }}</td>
                                            <td>{{ date('m/d/Y', strtotime($project->target_date)) }}</td>
                                            <td>{{ date('m/d/Y', strtotime($project->launch_date)) }}</td>
                                            <td>{{ date('m/d/Y', strtotime($project->created_at)) }}</td>
                                            <td>
                                                <a href="{{ url('admin/project/'. $project->id . '/edit')}}" class="btn btn-primary" style="border-radius: 20px; font-size: x-small;">
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
