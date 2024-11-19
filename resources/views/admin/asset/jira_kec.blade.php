@extends('layouts.dashboard')

@section('content')

    <section class="section">
        <div class="section-header">
            <h1>Status Board (Digital Ops)</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></div>
                <div class="breadcrumb-item">Status Board (Digital Ops)</div>
            </div>
        </div>
        <div class="section-body">

            @include('admin.asset.jira_kec_filter')
            @include('admin.asset.flash')

            <div class="row flex-nowrap" style="overflow-x: scroll; padding-top: 17px;">

                <div class="col-md-3" >
                    <div class="card status_title">
                        <h5 class="status_name">COPY REQUESTED</h5>
                    </div>
                        @foreach ($asset_list_copy_request as $asset)

                        <?php

                        $time_to_spare = ($asset->time_to_spare == 'N/A') ? 0 : $asset->time_to_spare;
                        $kdo = ($asset->kdo == 'N/A') ? 0 : $asset->kdo;
                        $development = ($asset->development == 'N/A') ? 0 : $asset->development;
                        $final_review = ($asset->final_review == 'N/A') ? 0 : $asset->final_review;
                        $creative_work = ($asset->creative_work == 'N/A') ? 0 : $asset->creative_work;
                        $creator_assign = ($asset->creator_assign == 'N/A') ? 0 : $asset->creator_assign;
                        $copy_review = ($asset->copy_review == 'N/A') ? 0 : $asset->copy_review;
                        $copy = ($asset->copy == 'N/A') ? 0 : $asset->copy;
                        $copywriter_assign = ($asset->copywriter_assign == 'N/A') ? 0 : $asset->copywriter_assign;

                        $step_8 = $time_to_spare + $kdo;
                        $step_7 = $step_8 + $development;
                        $step_6 = $step_7 + $final_review;
                        $step_5 = $step_6 + $creative_work;
                        $step_4 = $step_5 + $creator_assign;
                        $step_3 = $step_4 + $copy_review;
                        $step_2 = $step_3 + $copy;
                        $step_1 = $step_2 + $copywriter_assign;

                        $start_css = 'color:#98a6ad;';
                        $start_late_css = "color:#a50018;";

                        $start_date = date('m/d/Y', strtotime($asset->due . ' -' . $step_1 . ' weekday'));
                        if(strtotime($start_date) <= strtotime(date('m/d/Y')) ){
                            $start_css = $start_late_css;
                        }

//                            if($asset->asset_type == 'email_blast'){
//                                $start_date = date('m/d/Y', strtotime($asset->due . ' -' . $step_1 . ' weekday'));
//                                if(strtotime($start_date) <= strtotime(date('m/d/Y')) ){
//                                    $start_css = $start_late_css;
//                                }
//                            }else if($asset->asset_type == 'social_ad'){
//                                $start_date = date('m/d/Y', strtotime($asset->due . ' -28 weekday'));
//                                if(strtotime($start_date) <= strtotime(date('m/d/Y')) ){
//                                    $start_css = $start_late_css;
//                                }
//                            }else if($asset->asset_type == 'website_banners'){
//                                $start_date = date('m/d/Y', strtotime($asset->due . ' -29 weekday'));
//                                if(strtotime($start_date) <= strtotime(date('m/d/Y')) ){
//                                    $start_css = $start_late_css;
//                                }
//                            }else if($asset->asset_type == 'landing_page'){
//                                $start_date = date('m/d/Y', strtotime($asset->due . ' -49 weekday'));
//                                if(strtotime($start_date) <= strtotime(date('m/d/Y')) ){
//                                    $start_css = $start_late_css;
//                                }
//                            }else if($asset->asset_type == 'misc'){
//                                $start_date = date('m/d/Y', strtotime($asset->due . ' -27 weekday'));
//                                if(strtotime($start_date) <= strtotime(date('m/d/Y')) ){
//                                    $start_css = $start_late_css;
//                                }
//                            }else if($asset->asset_type == 'sms_request'){
//                                $start_date = date('m/d/Y', strtotime($asset->due . ' -27 weekday'));
//                                if(strtotime($start_date) <= strtotime(date('m/d/Y')) ){
//                                    $start_css = $start_late_css;
//                                }
//                            }else if($asset->asset_type == 'topcategories_copy'){
//                                $start_date =  date('m/d/Y', strtotime($asset->due . ' -7 weekday'));
//                                if(strtotime($start_date) <= strtotime(date('m/d/Y')) ){
//                                    $start_css = $start_late_css;
//                                }
//                            }else if ($asset->asset_type == 'programmatic_banners'){
//                                $start_date = date('m/d/Y', strtotime($asset->due . ' -28 weekday'));
//                                if(strtotime($start_date) <= strtotime(date('m/d/Y')) ){
//                                    $start_css = $start_late_css;
//                                }
//                            }else if($asset->asset_type == 'a_content'){
//                                $start_date = date('m/d/Y', strtotime($asset->due . ' -41 weekday'));
//                                if(strtotime($start_date) <= strtotime(date('m/d/Y')) ){
//                                    $start_css = $start_late_css;
//                                }
//                            }else if ($asset->asset_type == 'youtube_copy'){
//                                $start_date = date('m/d/Y', strtotime($asset->due . ' -14 weekday'));
//                                if(strtotime($start_date) <= strtotime(date('m/d/Y')) ){
//                                    $start_css = $start_late_css;
//                                }
//                            }else{
//                                $start_date = 'N/A';
//                            }
                        ?>
                            <div class="card">
                                <a href="{{ url('admin/campaign/'. $asset->campaign_id .'/edit#'.$asset->asset_id)}}" style="text-decoration: none;">
                                    <div class="card-body" style="padding-left: 10px; padding-right: 10px; margin-bottom: -25px;">
                                        <div class="media" style="padding-bottom: 0px;">
                                            <div class="form-group" style="width: 100%; padding: 0 8 0 8;" >

                                                <div style="color: #8b8a8a; font-weight: 600; float:left; font-size: 13px;">
                                                    {{$asset->campaign_name}}
                                                </div>
                                                <div style="float: right;">
                                                    <figure class="avatar mr-2 avatar-sm text-white" style="background-color: #b6b6b6; font-size: 15px;"
                                                            data-toggle="tooltip" data-placement="top"
                                                            data-original-title="{{ $asset->author_name }}"
                                                            data-initial="{{ substr($asset->author_name, 0, 1) }}">
                                                    </figure>
                                                </div>
                                                <div class="media-title" style="clear:both; font-size: large;">
                                                    {{ ucwords(str_replace('_', ' ', $asset->asset_type)) }}
                                                </div>
                                                <div class="text-md-left text-muted" style="margin-top: -8px;">
                                                    {{ mb_strimwidth($asset->name, 0,50, '...') }}
                                                </div>
                                                <hr style="border-width: 1px 0px 0px 0px;border-style:solid;border-color: #e0e0e0;
                                                            height:1px;margin-top: 15px;margin-bottom: 10px;width:100%">
                                                <?php if($asset->team_to == 'content') {
                                                    $bg_type = '#9f76c2';
                                                }else if($asset->team_to == 'web production'){
                                                    $bg_type = '#017cc2';
                                                }else{
                                                    $bg_type = '#03b06d';
                                                }
                                                ?>
                                                <div class="text-sm-left text-muted" style="float:left; padding-top: 3px;">
                                                    <figure class="avatar sm-2 text-white" style="width: 15px; height: 15px; background-color: {{$bg_type}}; font-size: small;" data-initial=""></figure>
                                                </div>
                                                <div class="text-sm-left text-muted" style="float:left; padding-left: 10px;">
                                                    {{$asset->asset_id}}
                                                </div>
                                                <div style="float: right; {{ $start_css }}" >
                                                    {{ $start_date }}
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                </div>

                <div class="col-md-3" >
                    <div class="card status_title">
                        <h5 class="status_name">COPY TO DO</h5>
                    </div>

                    @foreach ($asset_list_copy_to_do as $asset)

                        <?php

                        $time_to_spare = ($asset->time_to_spare == 'N/A') ? 0 : $asset->time_to_spare;
                        $kdo = ($asset->kdo == 'N/A') ? 0 : $asset->kdo;
                        $development = ($asset->development == 'N/A') ? 0 : $asset->development;
                        $final_review = ($asset->final_review == 'N/A') ? 0 : $asset->final_review;
                        $creative_work = ($asset->creative_work == 'N/A') ? 0 : $asset->creative_work;
                        $creator_assign = ($asset->creator_assign == 'N/A') ? 0 : $asset->creator_assign;
                        $copy_review = ($asset->copy_review == 'N/A') ? 0 : $asset->copy_review;
                        $copy = ($asset->copy == 'N/A') ? 0 : $asset->copy;
                        $copywriter_assign = ($asset->copywriter_assign == 'N/A') ? 0 : $asset->copywriter_assign;

                        $step_8 = $time_to_spare + $kdo;
                        $step_7 = $step_8 + $development;
                        $step_6 = $step_7 + $final_review;
                        $step_5 = $step_6 + $creative_work;
                        $step_4 = $step_5 + $creator_assign;
                        $step_3 = $step_4 + $copy_review;
                        $step_2 = $step_3 + $copy;
                        $step_1 = $step_2 + $copywriter_assign;

                        $start_css = 'color:#98a6ad;';
                        $start_late_css = "color:#a50018;";

                        $start_date = date('m/d/Y', strtotime($asset->due . ' -' . $step_2 . ' weekday'));
                        if(strtotime($start_date) <= strtotime(date('m/d/Y')) ){
                            $start_css = $start_late_css;
                        }

//                        if($asset->asset_type == 'email_blast'){
//                            $start_date = date('m/d/Y', strtotime($asset->due . ' -26 weekday'));
//                            if(strtotime($start_date) <= strtotime(date('m/d/Y')) ){
//                                $start_css = $start_late_css;
//                            }
//                        }else if($asset->asset_type == 'social_ad'){
//                            $start_date = date('m/d/Y', strtotime($asset->due . ' -26 weekday'));
//                            if(strtotime($start_date) <= strtotime(date('m/d/Y')) ){
//                                $start_css = $start_late_css;
//                            }
//                        }else if($asset->asset_type == 'website_banners'){
//                            $start_date = date('m/d/Y', strtotime($asset->due . ' -27 weekday'));
//                            if(strtotime($start_date) <= strtotime(date('m/d/Y')) ){
//                                $start_css = $start_late_css;
//                            }
//                        }else if($asset->asset_type == 'landing_page'){
//                            $start_date = date('m/d/Y', strtotime($asset->due . ' -47 weekday'));
//                            if(strtotime($start_date) <= strtotime(date('m/d/Y')) ){
//                                $start_css = $start_late_css;
//                            }
//                        }else if($asset->asset_type == 'misc'){
//                            $start_date = date('m/d/Y', strtotime($asset->due . ' -25 weekday'));
//                            if(strtotime($start_date) <= strtotime(date('m/d/Y')) ){
//                                $start_css = $start_late_css;
//                            }
//                        }else if($asset->asset_type == 'sms_request'){
//                            $start_date = date('m/d/Y', strtotime($asset->due . ' -25 weekday'));
//                            if(strtotime($start_date) <= strtotime(date('m/d/Y')) ){
//                                $start_css = $start_late_css;
//                            }
//                        }else if($asset->asset_type == 'topcategories_copy'){
//                            $start_date =  date('m/d/Y', strtotime($asset->due . ' -5 weekday'));
//                            if(strtotime($start_date) <= strtotime(date('m/d/Y')) ){
//                                $start_css = $start_late_css;
//                            }
//                        }else if ($asset->asset_type == 'programmatic_banners'){
//                            $start_date = date('m/d/Y', strtotime($asset->due . ' -26 weekday'));
//                            if(strtotime($start_date) <= strtotime(date('m/d/Y')) ){
//                                $start_css = $start_late_css;
//                            }
//                        }else if($asset->asset_type == 'a_content'){
//                            $start_date = date('m/d/Y', strtotime($asset->due . ' -39 weekday'));
//                            if(strtotime($start_date) <= strtotime(date('m/d/Y')) ){
//                                $start_css = $start_late_css;
//                            }
//                        }else if($asset->asset_type == 'youtube_copy'){
//                            $start_date = date('m/d/Y', strtotime($asset->due . ' -12 weekday'));
//                            if(strtotime($start_date) <= strtotime(date('m/d/Y')) ){
//                                $start_css = $start_late_css;
//                            }
//                        }else{
//                            $start_date = 'N/A';
//                        }
                        ?>
                        <div class="card">
                            <a href="{{ url('admin/campaign/'. $asset->campaign_id .'/edit#'.$asset->asset_id)}}" style="text-decoration: none;">
                                <div class="card-body" style="padding-left: 10px; padding-right: 10px; margin-bottom: -25px;">
                                    <div class="media" style="padding-bottom: 0px;">
                                        <div class="form-group" style="width: 100%; padding: 0 8 0 8;">

                                            <div style="color: #8b8a8a; font-weight: 600; float:left; font-size: 13px;">
                                                {{$asset->campaign_name}}
                                            </div>
                                            <div style="float: right;">
                                                <figure class="avatar mr-2 avatar-sm text-white"
                                                        style="background-color: #848484; font-size: 15px; margin-left: -15px; z-index: 1; border: 1px solid white;"
                                                        data-toggle="tooltip" data-placement="top"
                                                        data-original-title="{{ $asset->copy_writer }}"
                                                        data-initial="{{ substr($asset->copy_writer, 0, 1) }}">
                                                </figure>
                                            </div>
                                            <div style="float: right;">
                                                <figure class="avatar mr-2 avatar-sm text-white" style="background-color: #b6b6b6; font-size: 15px;"
                                                        data-toggle="tooltip" data-placement="top"
                                                        data-original-title="{{ $asset->author_name }}"
                                                        data-initial="{{ substr($asset->author_name, 0, 1) }}">
                                                </figure>
                                            </div>
                                            <div class="media-title" style="clear:both; font-size: large;">
                                                {{ ucwords(str_replace('_', ' ', $asset->asset_type)) }}
                                            </div>
                                            <div class="text-md-left text-muted" style="margin-top: -8px;">
                                                {{ mb_strimwidth($asset->name, 0,50, '...') }}
                                            </div>
                                            <hr style="border-width: 1px 0px 0px 0px;border-style:solid;border-color: #e0e0e0;
                                                            height:1px;margin-top: 15px;margin-bottom: 10px;width:100%">
                                            <?php if($asset->team_to == 'content') {
                                                $bg_type = '#9f76c2';
                                            }else if($asset->team_to == 'web production'){
                                                $bg_type = '#017cc2';
                                            }else{
                                                $bg_type = '#03b06d';
                                            }
                                            ?>
                                            <div class="text-sm-left text-muted" style="float:left; padding-top: 3px;">
                                                <figure class="avatar sm-2 text-white" style="width: 15px; height: 15px; background-color: {{$bg_type}}; font-size: small;" data-initial=""></figure>
                                            </div>
                                            <div class="text-sm-left text-muted" style="float:left; padding-left: 10px;">
                                                {{$asset->asset_id}}
                                            </div>
                                            <div style="float: right; {{ $start_css }}" >
                                                {{ $start_date }}
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>

                <div class="col-md-3" >
                    <div class="card status_title">
                        <h5 class="status_name">COPY IN PROGRESS</h5>
                    </div>
                    @foreach ($asset_list_copy_in_progress as $asset)

                        <div class="card">
                            <a href="{{ url('admin/campaign/'. $asset->campaign_id .'/edit#'.$asset->asset_id)}}" style="text-decoration: none;">
                                <div class="card-body" style="padding-left: 10px; padding-right: 10px; margin-bottom: -25px;">
                                    <div class="media" style="padding-bottom: 0px;">
                                        <div class="form-group" style="width: 100%; padding: 0 8 0 8;">

                                            <div style="color: #8b8a8a; font-weight: 600; float:left; font-size: 13px;">
                                                {{$asset->campaign_name}}
                                            </div>
                                            <div style="float: right;">
                                                <figure class="avatar mr-2 avatar-sm text-white"
                                                        style="background-color: #848484; font-size: 15px; margin-left: -15px; z-index: 1; border: 1px solid white;"
                                                        data-toggle="tooltip" data-placement="top"
                                                        data-original-title="{{ $asset->copy_writer }}"
                                                        data-initial="{{ substr($asset->copy_writer, 0, 1) }}">
                                                </figure>
                                            </div>
                                            <div style="float: right;">
                                                <figure class="avatar mr-2 avatar-sm text-white" style="background-color: #b6b6b6; font-size: 15px;"
                                                        data-toggle="tooltip" data-placement="top"
                                                        data-original-title="{{ $asset->author_name }}"
                                                        data-initial="{{ substr($asset->author_name, 0, 1) }}">
                                                </figure>
                                            </div>
                                            <div class="media-title" style="clear:both; font-size: large;">
                                                {{ ucwords(str_replace('_', ' ', $asset->asset_type)) }}
                                            </div>
                                            <div class="text-md-left text-muted" style="margin-top: -8px;">
                                                {{ mb_strimwidth($asset->name, 0,50, '...') }}
                                            </div>
                                            <hr style="border-width: 1px 0px 0px 0px;border-style:solid;border-color: #e0e0e0;
                                                            height:1px;margin-top: 15px;margin-bottom: 10px;width:100%">
                                            <?php if($asset->team_to == 'content') {
                                                $bg_type = '#9f76c2';
                                            }else if($asset->team_to == 'web production'){
                                                $bg_type = '#017cc2';
                                            }else{
                                                $bg_type = '#03b06d';
                                            }
                                            ?>
                                            <div class="text-sm-left text-muted" style="float:left; padding-top: 3px;">
                                                <figure class="avatar sm-2 text-white" style="width: 15px; height: 15px; background-color: {{$bg_type}}; font-size: small;" data-initial=""></figure>
                                            </div>
                                            <div class="text-sm-left text-muted" style="float:left; padding-left: 10px;">
                                                {{$asset->asset_id}}
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>

                <div class="col-md-3" >
                    <div class="card status_title">
                        <h5 class="status_name">COPY REVIEW</h5>
                    </div>
                    @foreach ($asset_list_copy_review as $asset)

                        <?php

                        $time_to_spare = ($asset->time_to_spare == 'N/A') ? 0 : $asset->time_to_spare;
                        $kdo = ($asset->kdo == 'N/A') ? 0 : $asset->kdo;
                        $development = ($asset->development == 'N/A') ? 0 : $asset->development;
                        $final_review = ($asset->final_review == 'N/A') ? 0 : $asset->final_review;
                        $creative_work = ($asset->creative_work == 'N/A') ? 0 : $asset->creative_work;
                        $creator_assign = ($asset->creator_assign == 'N/A') ? 0 : $asset->creator_assign;
                        $copy_review = ($asset->copy_review == 'N/A') ? 0 : $asset->copy_review;
                        $copy = ($asset->copy == 'N/A') ? 0 : $asset->copy;
                        $copywriter_assign = ($asset->copywriter_assign == 'N/A') ? 0 : $asset->copywriter_assign;

                        $step_8 = $time_to_spare + $kdo;
                        $step_7 = $step_8 + $development;
                        $step_6 = $step_7 + $final_review;
                        $step_5 = $step_6 + $creative_work;
                        $step_4 = $step_5 + $creator_assign;
                        $step_3 = $step_4 + $copy_review;
                        $step_2 = $step_3 + $copy;
                        $step_1 = $step_2 + $copywriter_assign;

                        $start_css = 'color:#98a6ad;';
                        $start_late_css = "color:#a50018;";

                        $start_date = date('m/d/Y', strtotime($asset->due . ' -' . $step_3 . ' weekday'));
                        if(strtotime($start_date) <= strtotime(date('m/d/Y')) ){
                            $start_css = $start_late_css;
                        }

//                        $start_css = 'color:#98a6ad;';
//                        $start_late_css = "color:#a50018;";
//                        if($asset->asset_type == 'email_blast'){
//                            $start_date = date('m/d/Y', strtotime($asset->due . ' -24 weekday'));
//                            if(strtotime($start_date) <= strtotime(date('m/d/Y')) ){
//                                $start_css = $start_late_css;
//                            }
//                        }else if($asset->asset_type == 'social_ad'){
//                            $start_date = date('m/d/Y', strtotime($asset->due . ' -24 weekday'));
//                            if(strtotime($start_date) <= strtotime(date('m/d/Y')) ){
//                                $start_css = $start_late_css;
//                            }
//                        }else if($asset->asset_type == 'website_banners'){
//                            $start_date = date('m/d/Y', strtotime($asset->due . ' -25 weekday'));
//                            if(strtotime($start_date) <= strtotime(date('m/d/Y')) ){
//                                $start_css = $start_late_css;
//                            }
//                        }else if($asset->asset_type == 'landing_page'){
//                            $start_date = date('m/d/Y', strtotime($asset->due . ' -43 weekday'));
//                            if(strtotime($start_date) <= strtotime(date('m/d/Y')) ){
//                                $start_css = $start_late_css;
//                            }
//                        }else if($asset->asset_type == 'misc'){
//                            $start_date = date('m/d/Y', strtotime($asset->due . ' -23 weekday'));
//                            if(strtotime($start_date) <= strtotime(date('m/d/Y')) ){
//                                $start_css = $start_late_css;
//                            }
//                        }else if($asset->asset_type == 'sms_request'){
//                            $start_date = date('m/d/Y', strtotime($asset->due . ' -23 weekday'));
//                            if(strtotime($start_date) <= strtotime(date('m/d/Y')) ){
//                                $start_css = $start_late_css;
//                            }
//                        }else if($asset->asset_type == 'topcategories_copy'){
//                            $start_date =  date('m/d/Y', strtotime($asset->due . ' -3 weekday'));
//                            if(strtotime($start_date) <= strtotime(date('m/d/Y')) ){
//                                $start_css = $start_late_css;
//                            }
//                        }else if($asset->asset_type == 'programmatic_banners'){
//                            $start_date = date('m/d/Y', strtotime($asset->due . ' -24 weekday'));
//                            if(strtotime($start_date) <= strtotime(date('m/d/Y')) ){
//                                $start_css = $start_late_css;
//                            }
//                        }else if($asset->asset_type == 'a_content'){
//                            $start_date = date('m/d/Y', strtotime($asset->due . ' -37 weekday'));
//                            if(strtotime($start_date) <= strtotime(date('m/d/Y')) ){
//                                $start_css = $start_late_css;
//                            }
//                        }else if($asset->asset_type == 'youtube_copy'){
//                            $start_date = date('m/d/Y', strtotime($asset->due . ' -10 weekday'));
//                            if(strtotime($start_date) <= strtotime(date('m/d/Y')) ){
//                                $start_css = $start_late_css;
//                            }
//                        }else{
//                            $start_date = 'N/A';
//                        }
                        ?>

                        <div class="card">
                            <a href="{{ url('admin/campaign/'. $asset->campaign_id .'/edit#'.$asset->asset_id)}}" style="text-decoration: none;">
                                <div class="card-body" style="padding-left: 10px; padding-right: 10px; margin-bottom: -25px;">
                                    <div class="media" style="padding-bottom: 0px;">
                                        <div class="form-group" style="width: 100%; padding: 0 8 0 8;">

                                            <div style="color: #8b8a8a; font-weight: 600; float:left; font-size: 13px;">
                                                {{$asset->campaign_name}}
                                            </div>
                                            <div style="float: right;">
                                                <figure class="avatar mr-2 avatar-sm text-white"
                                                        style="background-color: #848484; font-size: 15px; margin-left: -15px; z-index: 1; border: 1px solid white;"
                                                        data-toggle="tooltip" data-placement="top"
                                                        data-original-title="{{ $asset->copy_writer }}"
                                                        data-initial="{{ substr($asset->copy_writer, 0, 1) }}">
                                                </figure>
                                            </div>
                                            <div style="float: right;">
                                                <figure class="avatar mr-2 avatar-sm text-white" style="background-color: #b6b6b6; font-size: 15px;"
                                                        data-toggle="tooltip" data-placement="top"
                                                        data-original-title="{{ $asset->author_name }}"
                                                        data-initial="{{ substr($asset->author_name, 0, 1) }}">
                                                </figure>
                                            </div>
                                            <div class="media-title" style="clear:both; font-size: large;">
                                                {{ ucwords(str_replace('_', ' ', $asset->asset_type)) }}
                                            </div>
                                            <div class="text-md-left text-muted" style="margin-top: -8px;">
                                                {{ mb_strimwidth($asset->name, 0,50, '...') }}
                                            </div>
                                            <hr style="border-width: 1px 0px 0px 0px;border-style:solid;border-color: #e0e0e0;
                                                            height:1px;margin-top: 15px;margin-bottom: 10px;width:100%">
                                            <?php if($asset->team_to == 'content') {
                                                $bg_type = '#9f76c2';
                                            }else if($asset->team_to == 'web production'){
                                                $bg_type = '#017cc2';
                                            }else{
                                                $bg_type = '#03b06d';
                                            }
                                            ?>
                                            <div class="text-sm-left text-muted" style="float:left; padding-top: 3px;">
                                                <figure class="avatar sm-2 text-white" style="width: 15px; height: 15px; background-color: {{$bg_type}}; font-size: small;" data-initial=""></figure>
                                            </div>
                                            <div class="text-sm-left text-muted" style="float:left; padding-left: 10px;">
                                                {{$asset->asset_id}}
                                            </div>
                                            <div style="float: right; {{ $start_css }}" >
                                                {{ $start_date }}
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>

                <div class="col-md-3" >
                    <div class="card status_title">
                        <h5 class="status_name">CREATOR ASSIGN</h5>
                    </div>
                    @foreach ($asset_list_copy_complete as $asset)
                        <?php

                        $time_to_spare = ($asset->time_to_spare == 'N/A') ? 0 : $asset->time_to_spare;
                        $kdo = ($asset->kdo == 'N/A') ? 0 : $asset->kdo;
                        $development = ($asset->development == 'N/A') ? 0 : $asset->development;
                        $final_review = ($asset->final_review == 'N/A') ? 0 : $asset->final_review;
                        $creative_work = ($asset->creative_work == 'N/A') ? 0 : $asset->creative_work;
                        $creator_assign = ($asset->creator_assign == 'N/A') ? 0 : $asset->creator_assign;
                        $copy_review = ($asset->copy_review == 'N/A') ? 0 : $asset->copy_review;
                        $copy = ($asset->copy == 'N/A') ? 0 : $asset->copy;
                        $copywriter_assign = ($asset->copywriter_assign == 'N/A') ? 0 : $asset->copywriter_assign;

                        $step_8 = $time_to_spare + $kdo;
                        $step_7 = $step_8 + $development;
                        $step_6 = $step_7 + $final_review;
                        $step_5 = $step_6 + $creative_work;
                        $step_4 = $step_5 + $creator_assign;
                        $step_3 = $step_4 + $copy_review;
                        $step_2 = $step_3 + $copy;
                        $step_1 = $step_2 + $copywriter_assign;

                        $start_css = 'color:#98a6ad;';
                        $start_late_css = "color:#a50018;";

                        $start_date = date('m/d/Y', strtotime($asset->due . ' -' . $step_4 . ' weekday'));
                        if(strtotime($start_date) <= strtotime(date('m/d/Y')) ){
                            $start_css = $start_late_css;
                        }

//                        $start_css = 'color:#98a6ad;';
//                        $start_late_css = "color:#a50018;";
//                        if($asset->asset_type == 'email_blast'){
//                            $start_date = date('m/d/Y', strtotime($asset->due . ' -22 weekday'));
//                            if(strtotime($start_date) <= strtotime(date('m/d/Y')) ){
//                                $start_css = $start_late_css;
//                            }
//                        }else if($asset->asset_type == 'social_ad'){
//                            $start_date = date('m/d/Y', strtotime($asset->due . ' -22 weekday'));
//                            if(strtotime($start_date) <= strtotime(date('m/d/Y')) ){
//                                $start_css = $start_late_css;
//                            }
//                        }else if($asset->asset_type == 'website_banners'){
//                            $start_date = date('m/d/Y', strtotime($asset->due . ' -23 weekday'));
//                            if(strtotime($start_date) <= strtotime(date('m/d/Y')) ){
//                                $start_css = $start_late_css;
//                            }
//                        }else if($asset->asset_type == 'landing_page'){
//                            $start_date = date('m/d/Y', strtotime($asset->due . ' -40 weekday'));
//                            if(strtotime($start_date) <= strtotime(date('m/d/Y')) ){
//                                $start_css = $start_late_css;
//                            }
//                        }else if($asset->asset_type == 'misc'){
//                            $start_date = date('m/d/Y', strtotime($asset->due . ' -21 weekday'));
//                            if(strtotime($start_date) <= strtotime(date('m/d/Y')) ){
//                                $start_css = $start_late_css;
//                            }
//                        }else if($asset->asset_type == 'sms_request'){
//                            $start_date = date('m/d/Y', strtotime($asset->due . ' -21 weekday'));
//                            if(strtotime($start_date) <= strtotime(date('m/d/Y')) ){
//                                $start_css = $start_late_css;
//                            }
//                        }else if($asset->asset_type == 'programmatic_banners'){
//                            $start_date = date('m/d/Y', strtotime($asset->due . ' -22 weekday'));
//                            if(strtotime($start_date) <= strtotime(date('m/d/Y')) ){
//                                $start_css = $start_late_css;
//                            }
//                        }else if($asset->asset_type == 'image_request'){
//                            $start_date = date('m/d/Y', strtotime($asset->due . ' -14 weekday'));
//                            if(strtotime($start_date) <= strtotime(date('m/d/Y')) ){
//                                $start_css = $start_late_css;
//                            }
//                        }else if($asset->asset_type == 'roll_over'){
//                            $start_date = date('m/d/Y', strtotime($asset->due . ' -15 weekday'));
//                            if(strtotime($start_date) <= strtotime(date('m/d/Y')) ){
//                                $start_css = $start_late_css;
//                            }
//                        }else if($asset->asset_type == 'store_front'){
//                            $start_date = date('m/d/Y', strtotime($asset->due . ' -35 weekday'));
//                            if(strtotime($start_date) <= strtotime(date('m/d/Y')) ){
//                                $start_css = $start_late_css;
//                            }
//                        }else if($asset->asset_type == 'a_content'){
//                            $start_date = date('m/d/Y', strtotime($asset->due . ' -35 weekday'));
//                            if(strtotime($start_date) <= strtotime(date('m/d/Y')) ){
//                                $start_css = $start_late_css;
//                            }
//                        }else if($asset->asset_type == 'youtube_copy'){
//                            $start_date = date('m/d/Y', strtotime($asset->due . ' -8 weekday'));
//                            if(strtotime($start_date) <= strtotime(date('m/d/Y')) ){
//                                $start_css = $start_late_css;
//                            }
//                        }else{
//                            $start_date = 'N/A';
//                        }
                        ?>
                        <div class="card">
                            <a href="{{ url('admin/campaign/'. $asset->campaign_id .'/edit#'.$asset->asset_id)}}" style="text-decoration: none;">
                                <div class="card-body" style="padding-left: 10px; padding-right: 10px; margin-bottom: -25px;">
                                    <div class="media" style="padding-bottom: 0px;">
                                        <div class="form-group" style="width: 100%; padding: 0 8 0 8;">

                                            <div style="color: #8b8a8a; font-weight: 600; float:left; font-size: 13px;">
                                                {{$asset->campaign_name}}
                                            </div>
                                            <div style="float: right;">
                                                <figure class="avatar mr-2 avatar-sm text-white" style="background-color: #b6b6b6; font-size: 15px;"
                                                        data-toggle="tooltip" data-placement="top"
                                                        data-original-title="{{ $asset->author_name }}"
                                                        data-initial="{{ substr($asset->author_name, 0, 1) }}">
                                                </figure>
                                            </div>
                                            <div class="media-title" style="clear:both; font-size: large;">
                                                {{ ucwords(str_replace('_', ' ', $asset->asset_type)) }}
                                            </div>
                                            <div class="text-md-left text-muted" style="margin-top: -8px;">
                                                {{ mb_strimwidth($asset->name, 0,50, '...') }}
                                            </div>
                                            <hr style="border-width: 1px 0px 0px 0px;border-style:solid;border-color: #e0e0e0;
                                                            height:1px;margin-top: 15px;margin-bottom: 10px;width:100%">
                                            <?php if($asset->team_to == 'content') {
                                                $bg_type = '#9f76c2';
                                            }else if($asset->team_to == 'web production'){
                                                $bg_type = '#017cc2';
                                            }else{
                                                $bg_type = '#03b06d';
                                            }
                                            ?>
                                            <div class="text-sm-left text-muted" style="float:left; padding-top: 3px;">
                                                <figure class="avatar sm-2 text-white" style="width: 15px; height: 15px; background-color: {{$bg_type}}; font-size: small;" data-initial=""></figure>
                                            </div>
                                            <div class="text-sm-left text-muted" style="float:left; padding-left: 10px;">
                                                {{$asset->asset_id}}
                                            </div>
                                            <div style="float: right; {{ $start_css }}" >
                                                {{ $start_date }}
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>

                <div class="col-md-3" >
                    <div class="card status_title">
                        <h5 class="status_name">To DO</h5>
                    </div>
                    @foreach ($asset_list_to_do as $asset)
                        <?php

                        $time_to_spare = ($asset->time_to_spare == 'N/A') ? 0 : $asset->time_to_spare;
                        $kdo = ($asset->kdo == 'N/A') ? 0 : $asset->kdo;
                        $development = ($asset->development == 'N/A') ? 0 : $asset->development;
                        $final_review = ($asset->final_review == 'N/A') ? 0 : $asset->final_review;
                        $creative_work = ($asset->creative_work == 'N/A') ? 0 : $asset->creative_work;
                        $creator_assign = ($asset->creator_assign == 'N/A') ? 0 : $asset->creator_assign;
                        $copy_review = ($asset->copy_review == 'N/A') ? 0 : $asset->copy_review;
                        $copy = ($asset->copy == 'N/A') ? 0 : $asset->copy;
                        $copywriter_assign = ($asset->copywriter_assign == 'N/A') ? 0 : $asset->copywriter_assign;

                        $step_8 = $time_to_spare + $kdo;
                        $step_7 = $step_8 + $development;
                        $step_6 = $step_7 + $final_review;
                        $step_5 = $step_6 + $creative_work;
                        $step_4 = $step_5 + $creator_assign;
                        $step_3 = $step_4 + $copy_review;
                        $step_2 = $step_3 + $copy;
                        $step_1 = $step_2 + $copywriter_assign;

                        $start_css = 'color:#98a6ad;';
                        $start_late_css = "color:#a50018;";

                        $start_date = date('m/d/Y', strtotime($asset->due . ' -' . $step_5 . ' weekday'));
                        if(strtotime($start_date) <= strtotime(date('m/d/Y')) ){
                            $start_css = $start_late_css;
                        }

//                        $start_css = 'color:#98a6ad;';
//                        $start_late_css = "color:#a50018;";
//                        if($asset->asset_type == 'email_blast'){
//                            $start_date = date('m/d/Y', strtotime($asset->due . ' -20 weekday'));
//                            if(strtotime($start_date) <= strtotime(date('m/d/Y')) ){
//                                $start_css = $start_late_css;
//                            }
//                        }else if($asset->asset_type == 'social_ad'){
//                            $start_date = date('m/d/Y', strtotime($asset->due . ' -20 weekday'));
//                            if(strtotime($start_date) <= strtotime(date('m/d/Y')) ){
//                                $start_css = $start_late_css;
//                            }
//                        }else if($asset->asset_type == 'website_banners'){
//                            $start_date = date('m/d/Y', strtotime($asset->due . ' -21 weekday'));
//                            if(strtotime($start_date) <= strtotime(date('m/d/Y')) ){
//                                $start_css = $start_late_css;
//                            }
//                        }else if($asset->asset_type == 'landing_page'){
//                            $start_date = date('m/d/Y', strtotime($asset->due . ' -38 weekday'));
//                            if(strtotime($start_date) <= strtotime(date('m/d/Y')) ){
//                                $start_css = $start_late_css;
//                            }
//                        }else if($asset->asset_type == 'misc'){
//                            $start_date = date('m/d/Y', strtotime($asset->due . ' -19 weekday'));
//                            if(strtotime($start_date) <= strtotime(date('m/d/Y')) ){
//                                $start_css = $start_late_css;
//                            }
//                        }else if($asset->asset_type == 'sms_request'){
//                            $start_date = date('m/d/Y', strtotime($asset->due . ' -19 weekday'));
//                            if(strtotime($start_date) <= strtotime(date('m/d/Y')) ){
//                                $start_css = $start_late_css;
//                            }
//                        }else if($asset->asset_type == 'programmatic_banners'){
//                            $start_date = date('m/d/Y', strtotime($asset->due . ' -20 weekday'));
//                            if(strtotime($start_date) <= strtotime(date('m/d/Y')) ){
//                                $start_css = $start_late_css;
//                            }
//                        }else if($asset->asset_type == 'image_request'){
//                            $start_date = date('m/d/Y', strtotime($asset->due . ' -12 weekday'));
//                            if(strtotime($start_date) <= strtotime(date('m/d/Y')) ){
//                                $start_css = $start_late_css;
//                            }
//                        }else if($asset->asset_type == 'roll_over'){
//                            $start_date = date('m/d/Y', strtotime($asset->due . ' -13 weekday'));
//                            if(strtotime($start_date) <= strtotime(date('m/d/Y')) ){
//                                $start_css = $start_late_css;
//                            }
//                        }else if($asset->asset_type == 'store_front'){
//                            $start_date = date('m/d/Y', strtotime($asset->due . ' -33 weekday'));
//                            if(strtotime($start_date) <= strtotime(date('m/d/Y')) ){
//                                $start_css = $start_late_css;
//                            }
//                        }else if($asset->asset_type == 'a_content'){
//                            $start_date = date('m/d/Y', strtotime($asset->due . ' -33 weekday'));
//                            if(strtotime($start_date) <= strtotime(date('m/d/Y')) ){
//                                $start_css = $start_late_css;
//                            }
//                        }else{
//                            $start_date = 'N/A';
//                        }
                        ?>

                        <div class="card">
                            <a href="{{ url('admin/campaign/'. $asset->campaign_id .'/edit#'.$asset->asset_id)}}" style="text-decoration: none;">
                                <div class="card-body" style="padding-left: 10px; padding-right: 10px; margin-bottom: -25px;">
                                    <div class="media" style="padding-bottom: 0px;">
                                        <div class="form-group" style="width: 100%; padding: 0 8 0 8;">

                                            <div style="color: #8b8a8a; font-weight: 600; float:left; font-size: 13px;">
                                                {{$asset->campaign_name}}
                                            </div>
                                            <div style="float: right;">
                                                <figure class="avatar mr-2 avatar-sm text-white"
                                                        style="background-color: #848484; font-size: 15px; margin-left: -15px; z-index: 1; border: 1px solid white;"
                                                        data-toggle="tooltip" data-placement="top"
                                                        data-original-title="{{ $asset->assignee }}"
                                                        data-initial="{{ substr($asset->assignee, 0, 1) }}">
                                                </figure>
                                            </div>
                                            <div style="float: right;">
                                                <figure class="avatar mr-2 avatar-sm text-white" style="background-color: #b6b6b6; font-size: 15px;"
                                                        data-toggle="tooltip" data-placement="top"
                                                        data-original-title="{{ $asset->author_name }}"
                                                        data-initial="{{ substr($asset->author_name, 0, 1) }}">
                                                </figure>
                                            </div>
                                            <div class="media-title" style="clear:both; font-size: large;">
                                                {{ ucwords(str_replace('_', ' ', $asset->asset_type)) }}
                                            </div>
                                            <div class="text-md-left text-muted" style="margin-top: -8px;">
                                                {{ mb_strimwidth($asset->name, 0,50, '...') }}
                                            </div>
                                            <hr style="border-width: 1px 0px 0px 0px;border-style:solid;border-color: #e0e0e0;
                                                            height:1px;margin-top: 15px;margin-bottom: 10px;width:100%">
                                            <?php if($asset->team_to == 'content') {
                                                $bg_type = '#9f76c2';
                                            }else if($asset->team_to == 'web production'){
                                                $bg_type = '#017cc2';
                                            }else{
                                                $bg_type = '#03b06d';
                                            }
                                            ?>
                                            <div class="text-sm-left text-muted" style="float:left; padding-top: 3px;">
                                                <figure class="avatar sm-2 text-white" style="width: 15px; height: 15px; background-color: {{$bg_type}}; font-size: small;" data-initial=""></figure>
                                            </div>
                                            <div class="text-sm-left text-muted" style="float:left; padding-left: 10px;">
                                                {{$asset->asset_id}}
                                            </div>
                                            <div style="float: right; {{ $start_css }}" >
                                                {{ $start_date }}
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>

                <div class="col-md-3" >
                    <div class="card status_title">
                        <h5 class="status_name">IN PROGRESS</h5>
                    </div>
                    @foreach ($asset_list_in_progress as $asset)

                        <div class="card">
                            <a href="{{ url('admin/campaign/'. $asset->campaign_id .'/edit#'.$asset->asset_id)}}" style="text-decoration: none;">
                                <div class="card-body" style="padding-left: 10px; padding-right: 10px; margin-bottom: -25px;">
                                    <div class="media" style="padding-bottom: 0px;">
                                        <div class="form-group" style="width: 100%; padding: 0 8 0 8;">

                                            <div style="color: #8b8a8a; font-weight: 600; float:left; font-size: 13px;">
                                                {{$asset->campaign_name}}
                                            </div>
                                            <div style="float: right;">
                                                <figure class="avatar mr-2 avatar-sm text-white"
                                                        style="background-color: #848484; font-size: 15px; margin-left: -15px; z-index: 1; border: 1px solid white;"
                                                        data-toggle="tooltip" data-placement="top"
                                                        data-original-title="{{ $asset->assignee }}"
                                                        data-initial="{{ substr($asset->assignee, 0, 1) }}">
                                                </figure>
                                            </div>
                                            <div style="float: right;">
                                                <figure class="avatar mr-2 avatar-sm text-white" style="background-color: #b6b6b6; font-size: 15px;"
                                                        data-toggle="tooltip" data-placement="top"
                                                        data-original-title="{{ $asset->author_name }}"
                                                        data-initial="{{ substr($asset->author_name, 0, 1) }}">
                                                </figure>
                                            </div>
                                            <div class="media-title" style="clear:both; font-size: large;">
                                                {{ ucwords(str_replace('_', ' ', $asset->asset_type)) }}
                                            </div>
                                            <div class="text-md-left text-muted" style="margin-top: -8px;">
                                                {{ mb_strimwidth($asset->name, 0,50, '...') }}
                                            </div>
                                            <hr style="border-width: 1px 0px 0px 0px;border-style:solid;border-color: #e0e0e0;
                                                            height:1px;margin-top: 15px;margin-bottom: 10px;width:100%">
                                            <?php if($asset->team_to == 'content') {
                                                $bg_type = '#9f76c2';
                                            }else if($asset->team_to == 'web production'){
                                                $bg_type = '#017cc2';
                                            }else{
                                                $bg_type = '#03b06d';
                                            }
                                            ?>
                                            <div class="text-sm-left text-muted" style="float:left; padding-top: 3px;">
                                                <figure class="avatar sm-2 text-white" style="width: 15px; height: 15px; background-color: {{$bg_type}}; font-size: small;" data-initial=""></figure>
                                            </div>
                                            <div class="text-sm-left text-muted" style="float:left; padding-left: 10px;">
                                                {{$asset->asset_id}}
                                            </div>
{{--                                            <div style="float: right; {{ $start_css }}" >--}}
{{--                                                {{ $start_date }}--}}
{{--                                            </div>--}}

                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>

                <div class="col-md-3" >
                    <div class="card status_title">
                        <h5 class="status_name">CREATION REVIEW</h5>
                    </div>
                    @foreach ($asset_list_waiting_final_approval as $asset)
                        <?php

                        $time_to_spare = ($asset->time_to_spare == 'N/A') ? 0 : $asset->time_to_spare;
                        $kdo = ($asset->kdo == 'N/A') ? 0 : $asset->kdo;
                        $development = ($asset->development == 'N/A') ? 0 : $asset->development;
                        $final_review = ($asset->final_review == 'N/A') ? 0 : $asset->final_review;
                        $creative_work = ($asset->creative_work == 'N/A') ? 0 : $asset->creative_work;
                        $creator_assign = ($asset->creator_assign == 'N/A') ? 0 : $asset->creator_assign;
                        $copy_review = ($asset->copy_review == 'N/A') ? 0 : $asset->copy_review;
                        $copy = ($asset->copy == 'N/A') ? 0 : $asset->copy;
                        $copywriter_assign = ($asset->copywriter_assign == 'N/A') ? 0 : $asset->copywriter_assign;

                        $step_8 = $time_to_spare + $kdo;
                        $step_7 = $step_8 + $development;
                        $step_6 = $step_7 + $final_review;
                        $step_5 = $step_6 + $creative_work;
                        $step_4 = $step_5 + $creator_assign;
                        $step_3 = $step_4 + $copy_review;
                        $step_2 = $step_3 + $copy;
                        $step_1 = $step_2 + $copywriter_assign;

                        $start_css = 'color:#98a6ad;';
                        $start_late_css = "color:#a50018;";

                        $start_date = date('m/d/Y', strtotime($asset->due . ' -' . $step_6 . ' weekday'));
                        if(strtotime($start_date) <= strtotime(date('m/d/Y')) ){
                            $start_css = $start_late_css;
                        }

//                        $start_css = 'color:#98a6ad;';
//                        $start_late_css = "color:#a50018;";
//                        if($asset->asset_type == 'email_blast'){
//                            $start_date = date('m/d/Y', strtotime($asset->due . ' -10 weekday'));
//                            if(strtotime($start_date) <= strtotime(date('m/d/Y')) ){
//                                $start_css = $start_late_css;
//                            }
//                        }else if($asset->asset_type == 'social_ad'){
//                            $start_date = date('m/d/Y', strtotime($asset->due . ' -10 weekday'));
//                            if(strtotime($start_date) <= strtotime(date('m/d/Y')) ){
//                                $start_css = $start_late_css;
//                            }
//                        }else if($asset->asset_type == 'website_banners'){
//                            $start_date = date('m/d/Y', strtotime($asset->due . ' -11 weekday'));
//                            if(strtotime($start_date) <= strtotime(date('m/d/Y')) ){
//                                $start_css = $start_late_css;
//                            }
//                        }else if($asset->asset_type == 'landing_page'){
//                            $start_date = date('m/d/Y', strtotime($asset->due . ' -18 weekday'));
//                            if(strtotime($start_date) <= strtotime(date('m/d/Y')) ){
//                                $start_css = $start_late_css;
//                            }
//                        }else if($asset->asset_type == 'misc'){
//                            $start_date = date('m/d/Y', strtotime($asset->due . ' -9 weekday'));
//                            if(strtotime($start_date) <= strtotime(date('m/d/Y')) ){
//                                $start_css = $start_late_css;
//                            }
//                        }else if($asset->asset_type == 'sms_request'){
//                            $start_date = date('m/d/Y', strtotime($asset->due . ' -9 weekday'));
//                            if(strtotime($start_date) <= strtotime(date('m/d/Y')) ){
//                                $start_css = $start_late_css;
//                            }
//                        }else if($asset->asset_type == 'programmatic_banners'){
//                            $start_date = date('m/d/Y', strtotime($asset->due . ' -10 weekday'));
//                            if(strtotime($start_date) <= strtotime(date('m/d/Y')) ){
//                                $start_css = $start_late_css;
//                            }
//                        }else if($asset->asset_type == 'image_request'){
//                            $start_date = date('m/d/Y', strtotime($asset->due . ' -2 weekday'));
//                            if(strtotime($start_date) <= strtotime(date('m/d/Y')) ){
//                                $start_css = $start_late_css;
//                            }
//                        }else if($asset->asset_type == 'roll_over'){
//                            $start_date = date('m/d/Y', strtotime($asset->due . ' -3 weekday'));
//                            if(strtotime($start_date) <= strtotime(date('m/d/Y')) ){
//                                $start_css = $start_late_css;
//                            }
//                        }else if($asset->asset_type == 'store_front'){
//                            $start_date = date('m/d/Y', strtotime($asset->due . ' -13 weekday'));
//                            if(strtotime($start_date) <= strtotime(date('m/d/Y')) ){
//                                $start_css = $start_late_css;
//                            }
//                        }else if($asset->asset_type == 'a_content'){
//                            $start_date = date('m/d/Y', strtotime($asset->due . ' -13 weekday'));
//                            if(strtotime($start_date) <= strtotime(date('m/d/Y')) ){
//                                $start_css = $start_late_css;
//                            }
//                        }else{
//                            $start_date = 'N/A';
//                        }
                        ?>
                        <div class="card">
                            <a href="{{ url('admin/campaign/'. $asset->campaign_id .'/edit#'.$asset->asset_id)}}" style="text-decoration: none;">
                                <div class="card-body" style="padding-left: 10px; padding-right: 10px; margin-bottom: -25px;">
                                    <div class="media" style="padding-bottom: 0px;">
                                        <div class="form-group" style="width: 100%; padding: 0 8 0 8;">

                                            <div style="color: #8b8a8a; font-weight: 600; float:left; font-size: 13px;">
                                                {{$asset->campaign_name}}
                                            </div>
                                            <div style="float: right;">
                                                <figure class="avatar mr-2 avatar-sm text-white"
                                                        style="background-color: #848484; font-size: 15px; margin-left: -15px; z-index: 1; border: 1px solid white;"
                                                        data-toggle="tooltip" data-placement="top"
                                                        data-original-title="{{ $asset->assignee }}"
                                                        data-initial="{{ substr($asset->assignee, 0, 1) }}">
                                                </figure>
                                            </div>
                                            <div style="float: right;">
                                                <figure class="avatar mr-2 avatar-sm text-white" style="background-color: #b6b6b6; font-size: 15px;"
                                                        data-toggle="tooltip" data-placement="top"
                                                        data-original-title="{{ $asset->author_name }}"
                                                        data-initial="{{ substr($asset->author_name, 0, 1) }}">
                                                </figure>
                                            </div>
                                            <div class="media-title" style="clear:both; font-size: large;">
                                                {{ ucwords(str_replace('_', ' ', $asset->asset_type)) }}
                                            </div>
                                            <div class="text-md-left text-muted" style="margin-top: -8px;">
                                                {{ mb_strimwidth($asset->name, 0,50, '...') }}
                                            </div>
                                            <hr style="border-width: 1px 0px 0px 0px;border-style:solid;border-color: #e0e0e0;
                                                            height:1px;margin-top: 15px;margin-bottom: 10px;width:100%">
                                            <?php if($asset->team_to == 'content') {
                                                $bg_type = '#9f76c2';
                                            }else if($asset->team_to == 'web production'){
                                                $bg_type = '#017cc2';
                                            }else{
                                                $bg_type = '#03b06d';
                                            }
                                            ?>
                                            <div class="text-sm-left text-muted" style="float:left; padding-top: 3px;">
                                                <figure class="avatar sm-2 text-white" style="width: 15px; height: 15px; background-color: {{$bg_type}}; font-size: small;" data-initial=""></figure>
                                            </div>
                                            <div class="text-sm-left text-muted" style="float:left; padding-left: 10px;">
                                                {{$asset->asset_id}}
                                            </div>
                                            <div style="float: right; {{ $start_css }}" >
                                                {{ $start_date }}
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>

                <div class="col-md-3" >
                    <div class="card status_title">
                        <h5 class="status_name">ASSET COMPLETED</h5>
                    </div>
                @foreach ($asset_list_waiting_asset_completed as $asset)
                    <div class="card">
                        <a href="{{ url('admin/campaign/'. $asset->campaign_id .'/edit#'.$asset->asset_id)}}" style="text-decoration: none;">
                            <div class="card-body" style="padding-left: 10px; padding-right: 10px; margin-bottom: -25px;">
                                <div class="media" style="padding-bottom: 0px;">

                                    <div class="form-group" style="width: 100%; padding: 0 8 0 8;">

                                        <div style="color: #8b8a8a; font-weight: 600; float:left; font-size: 13px;">
                                            {{$asset->campaign_name}}
                                        </div>
                                        <div style="float: right;">
                                            <figure class="avatar mr-2 avatar-sm text-white"
                                                    style="background-color: #848484; font-size: 15px; margin-left: -15px; z-index: 1; border: 1px solid white;"
                                                    data-toggle="tooltip" data-placement="top"
                                                    data-original-title="{{ $asset->assignee }}"
                                                    data-initial="{{ substr($asset->assignee, 0, 1) }}">
                                            </figure>
                                        </div>
                                        <div style="float: right;">
                                            <figure class="avatar mr-2 avatar-sm text-white" style="background-color: #b6b6b6; font-size: 15px;"
                                                    data-toggle="tooltip" data-placement="top"
                                                    data-original-title="{{ $asset->author_name }}"
                                                    data-initial="{{ substr($asset->author_name, 0, 1) }}">
                                            </figure>
                                        </div>
                                        <div class="media-title" style="clear:both; font-size: large;">
                                            {{ ucwords(str_replace('_', ' ', $asset->asset_type)) }}
                                        </div>
                                        <div class="text-md-left text-muted" style="margin-top: -8px;">
                                            {{ mb_strimwidth($asset->name, 0,50, '...') }}
                                        </div>
                                        <hr style="border-width: 1px 0px 0px 0px;border-style:solid;border-color: #e0e0e0;
                                                            height:1px;margin-top: 15px;margin-bottom: 10px;width:100%">
                                        <?php if($asset->team_to == 'content') {
                                            $bg_type = '#9f76c2';
                                        }else if($asset->team_to == 'web production'){
                                            $bg_type = '#017cc2';
                                        }else{
                                            $bg_type = '#03b06d';
                                        }
                                        ?>
                                        <div class="text-sm-left text-muted" style="float:left; padding-top: 3px;">
                                            <figure class="avatar sm-2 text-white" style="width: 15px; height: 15px; background-color: {{$bg_type}}; font-size: small;" data-initial=""></figure>
                                        </div>
                                        <div class="text-sm-left text-muted" style="float:left; padding-left: 10px;">
                                            {{$asset->asset_id}}
                                        </div>
                                        <div style="float: right; color: #1a1a1a; word-spacing: 5px;" >
                                            {{ date('m/d/Y g:ia', strtotime($asset->updated_at)) }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
                </div>

            </div>

        </div>



@endsection

<script type="text/javascript">
    // 420 by 1 col
    function moveScrollLeft(){
        var _scrollX = $('.flex-nowrap').scrollLeft();
        $('.flex-nowrap').animate({
            scrollLeft:_scrollX + 1630}, 500);
    }

    function moveScrollRight(){
        var _scrollX = $('.flex-nowrap').scrollLeft();
        $('.flex-nowrap').animate({
            scrollLeft:_scrollX - 1630}, 500);
    }
</script>

<style type="text/css">

    .left {
        display: inline-block;
        width: 4em;
        height: 4em;
        border-color: #E9E9E9;
        border-radius: 50%;
        margin-right: 1.0em;
        background-color: #EFEFEF;
        border: 1px solid #ebebeb;
    }

    .left:after {
        content: '';
        display: inline-block;
        margin-top: 1.5em;
        margin-left: 1.5em;
        width: 1.0em;
        height: 1.0em;
        border-top: 0.3em solid #848484;
        border-right: 0.3em solid #848484;
        -moz-transform: rotate(-135deg);
        -webkit-transform: rotate(-135deg);
        transform: rotate(-135deg);
    }

    .left:hover {
        background-color: #fdfdfd;
        border-color: #848484;
        border: 1px solid #ebebeb;
    }

    .left:hover:after {
        border-top: 0.3em solid #2f2f2f;
        border-right: 0.3em solid #2f2f2f;
        -moz-transform: rotate(-135deg);
        -webkit-transform: rotate(-135deg);
        transform: rotate(-135deg);
    }

    .right {
        display: inline-block;
        width: 4em;
        height: 4em;
        border-color: #E9E9E9;
        border-radius: 50%;
        margin-left: -0.5em;
        background-color: #EFEFEF;
        border: 1px solid #ebebeb;
    }

    .right:after {
        content: '';
        display: inline-block;
        margin-top: 1.5em;
        margin-left: 1.3em;
        width: 1.0em;
        height: 1.0em;
        border-top: 0.3em solid #848484;
        border-right: 0.3em solid #848484;
        -moz-transform: rotate(45deg);
        -webkit-transform: rotate(45deg);
        transform: rotate(45deg);
    }

    .right:hover {
        background-color: #fdfdfd;
        border-color: #848484;
        border: 1px solid #ebebeb;
    }

    .right:hover:after {
        border-top: 0.3em solid #2f2f2f;
        border-right: 0.3em solid #2f2f2f;
        -moz-transform: rotate(45deg);
        -webkit-transform: rotate(45deg);
        transform: rotate(45deg);
    }

    .follow {
        right: 2%;
        z-index: 1;
    }

</style>
