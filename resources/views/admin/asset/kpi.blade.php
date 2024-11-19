@extends('layouts.dashboard')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>KPI (Creative)</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></div>
            <div class="breadcrumb-item">KPI</div>
        </div>
    </div>

    <div class="section-body">

        @include('admin.asset.kpi_filter')
        @include('admin.asset.flash')

        <div class="row" style="margin-top: 15px;">
            <div class="col-12">
                <div class="card">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <tbody>
                                    <tr>
                                        <th>Assignee</th>
                                        <th>Project ID</th>
                                        <th>Asset ID</th>
                                        <th>Asset Type</th>
                                        <th>Original Target Date</th>
                                        <th>Delay</th>
                                        <th>Revised Target Date</th>
                                        <th>Finished Date</th>
                                        <th>Difference</th>
                                        <th>Lead Time</th>
                                        <th>%</th>
                                        <th>Points</th>
                                    </tr>

                                    @foreach ($asset_list as $asset)
                                        <tr>
                                            <td>{{ $asset->assignee }}</td>
                                            <td>{{ $asset->campaign_id }}</td>
                                            <td>
                                                <a href="{{ url('admin/campaign/'.$asset->campaign_id.'/edit#'.$asset->asset_id) }}" target="_blank">
                                                    <figure class="avatar mr-2 avatar-md text-white" style="background-color: #767676" data-initial="{{$asset->asset_id}}"></figure>
                                                </a>
                                            </td>
                                            <td>{{ ucwords(str_replace('_', ' ', $asset->asset_type)) }}</td>
                                            <td>{{ $target_at = date('m/d', strtotime($asset->target_at)) }}
                                            </td>
                                            <td>
                                                <?php if($asset->delay != 0) { ?>
                                                {{ $asset->delay }}
                                                <?php } ?>
                                            </td>
                                            <td>{{ $real_target_at = date('m/d', strtotime($asset->target_at. '+'. $asset->delay .' weekdays')) }}</td>
                                            <?php $done_at = date('m/d', strtotime($asset->done_at)); ?>
                                            <?php $real_target_at < $done_at ? $red_text = 'color: red' : $red_text = '' ; ?>
                                            <td style="{{ $red_text }}">{{ $done_at }}</td>
                                            <td style="{{ $red_text }}">
                                                <?php $from = Carbon\Carbon::parse(strtotime($real_target_at));  ?>
                                                <?php $to = Carbon\Carbon::parse(strtotime($done_at)); ?>
                                                {{ $diff = $to->diffInWeekdays($from) }}
                                            </td>

                                            <?php $estimated = 0;
                                            if($asset->asset_type == 'email_blast'){
                                                $estimated = 10;
                                            }elseif($asset->asset_type == 'social_ad'){
                                                $estimated = 10;
                                            }elseif($asset->asset_type == 'website_banners'){
                                                $estimated = 10;
                                            }elseif($asset->asset_type == 'landing_page'){
                                                $estimated = 20;
                                            }elseif($asset->asset_type == 'misc'){
                                                $estimated = 10;
                                            }elseif($asset->asset_type == 'sms_request'){
                                                $estimated = 10;
                                            }elseif($asset->asset_type == 'programmatic_banners'){
                                                $estimated = 10;
                                            }elseif($asset->asset_type == 'image_request'){
                                                $estimated = 10;
                                            }elseif($asset->asset_type == 'rollover'){
                                                $estimated = 10;
                                            }elseif($asset->asset_type == 'store_front'){
                                                $estimated = 20;
                                            }elseif($asset->asset_type == 'a_content'){
                                                $estimated = 20;
                                            }
                                            ?>
                                            <td>
                                                {{ $estimated }}
                                            </td>
                                            <td>
                                                <?php
                                                    $point = 0;
                                                    if($red_text == 'color: red') {   /////// if late
                                                        if($estimated == 10){ $point = 2;  // if 10... ?>
                                                        90%
                                                <?php   }else{ $point = 2; ?>
                                                        90%
                                                <?php
                                                        }
                                                    } else { ////////// if same day or earlier
                                                        if($estimated == 10){
                                                            if($diff == 0){ $point = 3; ?>
                                                        100%
                                                <?php       }elseif($diff == 1){ $point = 4; ?>
                                                        110%
                                                <?php       }else{ $point = 5; ?>
                                                        115%
                                                <?php
                                                            }
                                                        }else{ // if 20....
                                                            if($diff >= 4){ $point = 5; // 4 or 5 or 6 ... ?>
                                                            115%
                                                <?php       }elseif($diff == 3 || $diff == 2){ $point = 4; // 0 or 1 ?>
                                                            110%
                                                <?php
                                                            }else{ $point = 3; ?>
                                                            100%
                                                <?php
                                                            }
                                                        }
                                                    } ?>
                                            </td>
                                            <td>{{ $point }}</td>
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
