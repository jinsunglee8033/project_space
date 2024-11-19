@extends('layouts.dashboard')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>PROJECT SPACE</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></div>
            <div class="breadcrumb-item active">Project Manager</div>
        </div>
    </div>
    <div class="section-body">

        @include('admin.campaign.flash')
        @include('admin.campaign._filter')

        <div class="row" style="margin-top: 15px;">

            @foreach ($campaigns as $campaign)

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
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
                            <div class="col-md-6" style="border-right:1px solid #eee; padding: 0px 0px 0px 0px;">
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
                                            {{ $campaign->author->first_name }} {{ $campaign->author->last_name }}
                                        </div>
                                        <div>
                                            <b>Status:</b>
                                            {{ ucwords($campaign->status) }}
                                        </div>
                                    </div>
                                    <div style="padding-top: 15px;">
                                        <a href="{{ url('admin/campaign/'. $campaign->id .'/edit') }}">
                                            <button type="button" class="btn-sm design-white-project-btn">Open</button>
                                        </a>
{{--                                        <a href="{{ url('admin/campaign/'. $campaign->id .'/edit')}}" class="btn btn-block btn-light">--}}
{{--                                            Open--}}
{{--                                        </a>--}}
                                    </div>
                                </div>

                            </div>

                            <div class="col-md-6 asset_scroll">
                                <div class="row" style="font-size: 12px;">
                                    <div class="col-sm-6" style="padding: 0px 0px 0px 2px;">
                                        <div style="margin-top:0px;">
                                            <b>Assets:</b>
                                        </div>
                                        <?php $assets = \App\Repositories\Admin\CampaignRepository::get_assets($campaign->id); ?>
                                        <?php if(!empty($assets)){
                                        foreach ($assets as $asset){?>
                                        <div>
                                            <?php
                                                $asset_type = $asset->type;
                                                if($asset_type == 'website_banners') {
                                                    $asset_type = 'web_banners';
                                                }else if($asset_type == 'a_content') {
                                                    $asset_type = 'a+_content';
                                                }else if($asset_type == 'image_request') {
                                                    $asset_type = 'img_request';
                                                }else if($asset_type == 'programmatic_banners') {
                                                    $asset_type = 'pgm_banners';
                                                }else if($asset_type == 'topcategories_copy') {
                                                    $asset_type = 'top_copy';
                                                }
                                            echo ucwords(str_replace('_', ' ', $asset_type))

                                            ?></div>
                                        <?php   }
                                        }
                                        ?>
                                    </div>
                                    <div class="col-sm-6" style="padding: 0px 0px 0px 12px;">
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
            if (confirm("Are you sure to DELETE this project?") == true) {
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
