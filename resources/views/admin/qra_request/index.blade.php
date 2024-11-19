@extends('layouts.dashboard')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>RA Request</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></div>
            <div class="breadcrumb-item active">RA Request</div>
        </div>
    </div>
    <div class="section-body">

        @include('admin.qra_request.flash')
        @include('admin.qra_request._filter')

        <div class="row" style="margin-top: 15px;">

            @foreach ($projects as $project)

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h4>{{ $project->name }}
                                <span class="float-right">
                                <a  href="javascript:void(0);"
                                    class="close"
                                    data-id=""
                                    data-project-id="{{ $project->id }}"
                                    onclick="delete_project($(this));">
                                <i class="fa fa-times"></i>
                                </a>
                            </span>
                            </h4>

                        </div>
                        <div class="card-body" style="display: flex;">
                            <div class="col-md-4" style="border-right:1px solid #eee; padding: 0px 0px 0px 0px;">
                                <div class="form-group">
                                    <div class="input-group info" style="display: block; ">
                                        <div>
                                            <b>Team:</b>
                                            {{ $project->team }}
                                        </div>
                                        <div>
                                            <b>Project:</b>
                                            #{{ $project->project_id }}
                                        </div>
                                        <div>
                                            <b>Task ID:</b>
                                            #{{ $project->id }}
                                        </div>
                                        <div>
                                            <b>Creator:</b>
                                            {{ $project->first_name }} {{ $project->last_name }}
                                        </div>
                                    </div>
                                    <div style="padding-top: 15px;">
                                        <a href="{{ url('admin/qra_request/'. $project->project_id .'/edit') }}">
                                            <button type="button" class="btn-sm design-white-project-btn">Open</button>
                                        </a>
                                    </div>
                                </div>

                            </div>

                            <div class="col-md-8 asset_scroll">
                                <div class="row" style="font-size: 12px;">
                                    <div class="col-sm-12" style="padding: 0px 0px 0px 2px;">

                                        <?php $assets = \App\Repositories\Admin\QraRequestRepository::get_qra_request_type($project->id); ?>
                                        <?php if(!empty($assets)){
                                        foreach ($assets as $asset){

                                        if($asset->status == 'action_requested'){
                                            $bg_css = 'bg-qra_request';
                                            $progress = '50%';
                                        }else if($asset->status == 'in_progress'){
                                            $bg_css = 'bg-qra_request';
                                            $progress = '70%';
                                        }else if($asset->status == 'action_review'){
                                            $bg_css = 'bg-qra_request';
                                            $progress = '90%';
                                        }else if($asset->status == 'action_completed'){
                                            $bg_css = 'bg-secondary';
                                            $progress = '100%';
                                        }else{
                                            $bg_css = 'bg-qra_request';
                                            $progress = '10%';
                                        }
                                        ?>

                                        <div class="progress" style="margin: 0 0 5px 0;">
                                            <div class="progress-bar {{$bg_css}}" role="progressbar"
                                                 data-width="{{$progress}}" aria-valuenow="50" aria-valuemin="0"
                                                 style="width: 20%; font-size: 0.8rem; color: #fff;">
                                                {{ucwords(str_replace('_', ' ', $asset->type))}}  ({{$progress}})
                                            </div>
                                        </div>

                                        <?php   }
                                        }
                                        ?>
                                    </div>
                                    {{--                                    <div class="col-sm-6" style="padding: 0px 0px 0px 12px;">--}}
                                    {{--                                        <div style="margin-top:0px;">--}}
                                    {{--                                            <b>Due:</b>--}}
                                    {{--                                        </div>--}}
                                    {{--                                        <?php $assets = \App\Repositories\Admin\CampaignRepository::get_assets($project->id); ?>--}}
                                    {{--                                        <?php if(!empty($assets)){--}}
                                    {{--                                        foreach ($assets as $asset){?>--}}
                                    {{--                                        <div><?php echo date('m/d/Y', strtotime($asset->due))  ?></div>--}}
                                    {{--                                        <?php   }--}}
                                    {{--                                        }--}}
                                    {{--                                        ?>--}}
                                    {{--                                    </div>--}}
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        {{ $projects->appends(['q' => !empty($filter['q']) ? $filter['q'] : ''])->links() }}
    </div>
</section>

    <script>
        function delete_project(el) {
            if (confirm("Are you sure to DELETE this project?") == true) {
                let p_id = $(el).attr('data-project-id');
                $.ajax({
                    url: "<?php echo url('/admin/project/projectRemove'); ?>"+"/"+p_id,
                    type: "GET",
                    datatype: "json",
                    success: function(response) {
                        if(response == 'success'){
                            $(el).parent().parent().parent().parent().parent().fadeOut( "slow", function() {
                                $(el).parent().parent().parent().parent().parent().remove();
                            });
                        }else{
                            alert(response);
                        }
                    },
                })
            }
        }
    </script>

@endsection
