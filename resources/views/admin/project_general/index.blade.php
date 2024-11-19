@extends('layouts.dashboard')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>Interdepartmental Requests</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></div>
            <div class="breadcrumb-item active">Interdepartmental Requests</div>
        </div>
    </div>
    <div class="section-body">

        @include('admin.project_general.flash')
        @include('admin.project_general._filter')

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
                                            <b>Type:</b>
                                            {{ $project->project_type }}
                                        </div>
                                        <div>
                                            <b>Project:</b>
                                            # {{ $project->id }}
                                        </div>
                                        <div>
                                            <b>Creator:</b>
                                            {{ $project->author->first_name }}
                                        </div>
                                        <div>
                                            <b>Status:</b>
                                            {{ ucwords($project->status) }}
                                        </div>
                                    </div>
                                    <div style="padding-top: 15px;">
                                        <a href="{{ url('admin/project/'. $project->id .'/edit_general') }}">
                                            <button type="button" class="btn-sm design-white-project-btn">Open</button>
                                        </a>
{{--                                        <a href="{{ url('admin/campaign/'. $project->id .'/edit')}}" class="btn btn-block btn-light">--}}
{{--                                            Open--}}
{{--                                        </a>--}}
                                    </div>
                                </div>

                            </div>

                            <div class="col-md-8 asset_scroll">
                                <div class="row" style="font-size: 12px;">
                                    <div class="col-sm-12" style="padding: 0px 0px 0px 2px;">

                                        <?php $assets = \App\Repositories\Admin\ProjectTaskIndexRepository::get_tasks($project->id); ?>
                                        <?php if(!empty($assets)){
                                        foreach ($assets as $asset){

                                            if($asset->status == 'action_requested'){
                                                $bg_css = 'bg-primary';
                                                $progress = '50%';
                                            }else if($asset->status == 'in_progress'){
                                                $bg_css = 'bg-primary';
                                                $progress = '70%';
                                            }else if($asset->status == 'action_review'){
                                                $bg_css = 'bg-primary';
                                                $progress = '90%';
                                            }else if($asset->status == 'action_completed'){
                                                $bg_css = 'bg-secondary';
                                                $progress = '100%';
                                            }else{
                                                $bg_css = 'bg-primary';
                                                $progress = '10%';
                                            }


                                        ?>

{{--                                        <div style="padding: 0 0 5px 0;">--}}
{{--                                            <div class="progress-bar {{ $bg_css }}" role="progressbar" data-width="{{$progress}}" style="width: 20%;">--}}
{{--                                                {{ucwords(str_replace('_', ' ', $asset->type))}} ({{$progress}})--}}
{{--                                            </div>--}}
{{--                                        </div>--}}
                                            <div class="progress" style="margin: 0 0 5px 0;">
                                            <div class="progress-bar {{$bg_css}}" role="progressbar"
                                                 data-width="{{$progress}}" aria-valuenow="50" aria-valuemin="0"
                                                 style="width: 20%; font-size: 0.8rem; color: #fff;">
                                                <?php
                                                    $asset_type = $asset->type;
                                                    if($asset_type == 'qc_request'){
                                                        $asset_type = 'QA Request';
                                                    }elseif ($asset_type == 'qra_request'){
                                                        $asset_type = 'QRA Request';
                                                    }elseif ($asset_type == 'mm_request'){
                                                        $asset_type = 'MM Request';
                                                    }
                                                ?>
                                                {{ucwords(str_replace('_', ' ', $asset_type))}}  ({{$progress}})
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
