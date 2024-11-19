@extends('layouts.dashboard')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>QR Code Management</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></div>
            <div class="breadcrumb-item">QR Code Management</div>
        </div>
    </div>

    <div class="section-body">

        <h2 class="section-title">Qr Code Management</h2>

{{--        <?php if($team == 'Content'){ ?>--}}
{{--            @include('admin.asset.approval_filter_content')--}}
{{--        <?php }else if($team == 'Web Production'){ ?>--}}
{{--            @include('admin.asset.approval_filter_web')--}}
{{--        <?php } else { ?>--}}
{{--            @include('admin.asset.approval_filter')--}}
{{--        <?php } ?>--}}

        @include('admin.form.flash')

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <tbody>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>QR Code For</th>
                                        <th>Brand</th>
                                        <th>Department</th>
                                        <th>Link To</th>
                                        <th>QR Image Need Date</th>
                                        <th>QR Code Live Date</th>
                                        <th>Expire Date</th>
                                        <th>Additional Information</th>
                                        <th>Request Date</th>
                                        <th>QR Code Image</th>
                                        <th>URL Destination Link</th>
                                        <th>Short URL</th>
                                        <th>Status</th>
                                        <th>QR Created Date</th>
                                    </tr>

                                    @foreach ($qr_code_requests as $qr)
                                        <tr class='clickable-row' data-href="{{ url('admin/edit_qr_code/'.$qr->id) }}">
                                            <td>{{ $qr->id }}</td>
                                            <td>{{ $qr->name }}</td>
                                            <td>{{ $qr->email }}</td>
                                            <td>{{ $qr->qr_code_for }}</td>
                                            <td>{{ $qr->brand }}</td>
                                            <td>{{ $qr->department }}</td>
                                            <td>{{ $qr->link_to }}</td>
                                            <td>{{ date('m/d/Y', strtotime($qr->date_1)) }}</td>
                                            <td>{{ date('m/d/Y', strtotime($qr->date_2)) }}</td>
                                            <td>{{ date('m/d/Y', strtotime($qr->date_3)) }}</td>
                                            <td>{{ $qr->information }}</td>
                                            <td>{{ date('m/d/Y', strtotime($qr->created_at)) }}</td>
                                            <td>
                                                @if(isset($qr->campaignTypeAssetAttachments))
{{--                                                {{ $qr->campaignTypeAssetAttachments->attachment }}--}}
                                                    <img title="test" src="/storage{{ $qr->campaignTypeAssetAttachments->attachment }}" class="thumbnail">
                                                @endif
                                            </td>
                                            <td>{{ $qr->url_destination_link }}</td>
                                            <td>{{ $qr->short_url }}</td>
                                            <td>
                                                @if(isset($qr->campaignTypeAssetAttachments))
                                                    Done
                                                @else
                                                    New
                                                @endif
                                            </td>
                                            <td>
                                                @if(isset($qr->campaignTypeAssetAttachments))
                                                    {{ date('m/d/Y', strtotime($qr->campaignTypeAssetAttachments->created_at)) }}
                                                @endif
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


        <script type="text/javascript">

            jQuery(document).ready(function($) {
                $(".clickable-row").click(function() {
                    window.location = $(this).data("href");
                });
            });


        </script>

@endsection
