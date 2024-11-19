@extends('layouts.dashboard')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>Project Deleted List</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></div>
            <div class="breadcrumb-item active">Project Archives</div>
        </div>
    </div>
    <div class="section-body">

        @include('admin.campaign.flash')
        @include('admin.campaign.archives_filter')

        <div class="row" style="margin-top: 15px;">

            @foreach ($campaigns as $campaign)

                <div class="col-md-4">
                    <div class="card" style="border-radius: 30px;background-color: #e2e2e2" >
                        <div class="card-header" >
                            <h4>{{ $campaign->name }}
                                <span class="float-right">
                                <a  href="javascript:void(0);"
                                    class="close"
                                    data-id=""
                                    data-campaign-id="{{ $campaign->id }}"
                                    onclick="delete_campaign($(this));">
                                <i class="fa fa-times"></i>
                                </a>
                            </span>
                            </h4>

                        </div>
                        <div class="card-body" style="display: flex;">
                            <div class="col-md-6" style="border-right:1px solid #eee">
                                <div class="form-group">
                                    <div class="input-group info" style="display: block; ">
                                        <div>
                                            <b>Brand:</b>
                                            {{ $campaign->brands->campaign_name }}
                                        </div>
                                        <div>
                                            <b>Project:</b>
                                            # {{ $campaign->id }}
                                        </div>
                                        <div>
                                            <b>Created By:</b>
                                            <?php if(isset($campaign->author)) { ?>
                                            {{ $campaign->author->first_name }} {{ $campaign->author->last_name }}
                                            <?php } ?>
                                        </div>
                                        <div>
                                            <b>Status:</b>
                                            {{ ucwords($campaign->status) }}
                                        </div>
                                    </div>
                                    <div style="padding-top: 15px;">
                                        <a href="{{ url('admin/deleted/'. $campaign->id .'/edit') }}">
                                            <button type="button" class="btn-sm design-white-project-btn">Open</button>
                                        </a>
{{--                                        <a href="{{ url('admin/archives/'. $campaign->id .'/edit')}}" class="btn btn-block btn-light">--}}
{{--                                            Open--}}
{{--                                        </a>--}}
                                    </div>
                                </div>

                            </div>

                            <div class="col-md-6 asset_scroll">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div style="margin-top:0px;">
                                            <b>Assets:</b>
                                        </div>
                                        <?php $assets = \App\Repositories\Admin\CampaignRepository::get_assets($campaign->id); ?>
                                        <?php if(!empty($assets)){
                                        foreach ($assets as $asset){?>
                                        <div><?php echo ucwords(str_replace('_', ' ', $asset->type)) ?></div>
                                        <?php   }
                                        }
                                        ?>
                                    </div>
                                    <div class="col-sm-6">
                                        <div style="margin-top:0px;">
                                            <b>Due:</b>
                                        </div>
                                        <?php $assets = \App\Repositories\Admin\CampaignRepository::get_assets($campaign->id); ?>
                                        <?php if(!empty($assets)){
                                        foreach ($assets as $asset){?>
                                        <div><?php echo date('m/d/Y', strtotime($asset->due))  ?></div>
                                        <?php   }
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        {{ $campaigns->appends(['q' => !empty($filter['q']) ? $filter['q'] : ''])->links() }}
    </div>
</section>

    <script>
        function delete_campaign(el) {
            if (confirm("Are you sure to DELETE?") == true) {
                let c_id = $(el).attr('data-campaign-id');
                $.ajax({
                    url: "<?php echo url('/admin/campaign/campaignRemove'); ?>"+"/"+c_id,
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
