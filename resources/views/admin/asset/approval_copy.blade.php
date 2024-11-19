@extends('layouts.dashboard')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>Copy List</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></div>
            <div class="breadcrumb-item">Copy List</div>
        </div>
    </div>

    <div class="section-body">

        @include('admin.asset.approval_filter_copy')
        @include('admin.asset.flash')

        <div class="row" style="margin-top: 15px;">
            <div class="col-12">
                <div class="card">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <tbody>
                                    <tr>
                                        <th>Due Date</th>
                                        <th>Team</th>
                                        <th>Asset Type</th>
                                        <th>Brand</th>
                                        <th>Project ID</th>
                                        <th>Asset ID</th>
                                        <th>Project Name</th>
                                        <th>Action</th>
                                    </tr>

                                    @foreach ($asset_list as $asset)
                                        <tr>
                                            <td>{{ date('m/d/Y', strtotime($asset->due)) }}</td>
                                            <td>{{ $asset->team_to }}</td>
                                            <td>{{ ucwords(str_replace('_', ' ', $asset->asset_type)) }}</td>
                                            <td>{{$asset->brand}}</td>
                                            <td>
                                                {{$asset->campaign_id}}
                                            </td>
                                            <td>
                                                <a href="{{ url('admin/campaign/'.$asset->campaign_id.'/edit#'.$asset->asset_id) }}">
                                                    <figure class="avatar mr-2 avatar-md text-white" style="background-color: #767676" data-initial="{{$asset->asset_id}}"></figure>
                                                </a>
                                            </td>
                                            <td>{{ $asset->name }}</td>
                                            <td>
                                                <a href="{{ url('admin/asset/'. $asset->asset_id .'/'. $asset->campaign_id . '/' . $asset->asset_type . '/' . $asset->brand . '/detail_copy')}}" class="btn btn-primary" style="border-radius: 20px; font-size: x-small;">
                                                    Asset Detail
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
