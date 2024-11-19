@extends('layouts.dashboard')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>KPI (Content)</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></div>
            <div class="breadcrumb-item">KPI</div>
        </div>
    </div>

    <div class="section-body">

        @include('admin.asset.kpi_content_filter')
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
