@extends('layouts.dashboard')

@section('content')

    <section class="section">
        <div class="section-header">
            <h1>Request Status Board (Dev)</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></div>
                <div class="breadcrumb-item">Dev Request Status Board</div>
            </div>
        </div>

        <div class="section-body">

            @include('admin.dev._filter')
            @include('admin.asset.flash')

            <div class="row flex-nowrap" style="overflow-x: scroll; padding-top: 17px;">

                <div class="col-md-3">
                    <div class="card status_title">
                        <h5 class="status_name">DEV REQUESTED</h5>
                    </div>

                    @foreach ($dev_requested_list as $dev)

                            <div class="card">
                                <a href="{{ url('admin/dev/'. $dev->dev_id .'/edit')}}" style="text-decoration: none;">
                                    <div class="card-body" style="margin: 0px -5px -20px -5px;">
                                        <div class="media" style="padding-bottom: 0px;">
                                            <div class="form-group" style="width: 100%;">

                                                <div style="color: #8b8a8a; font-weight: 600; float:left; font-size: 13px;">
                                                    {{$dev->type}}
                                                </div>
{{--                                                <div style="float: right;">--}}
{{--                                                    <figure class="avatar mr-2 avatar-sm text-white"--}}
{{--                                                            style="background-color: #848484; font-size: 15px; margin-left: -15px; z-index: 1; border: 1px solid white;"--}}
{{--                                                            data-toggle="tooltip" data-placement="top"--}}
{{--                                                            data-original-title="{{ $dev->assign_to }}"--}}
{{--                                                            data-initial="{{ substr($dev->assign_to, 0, 1) }}">--}}
{{--                                                    </figure>--}}
{{--                                                </div>--}}
                                                <div style="float: right;">
                                                    <figure class="avatar mr-2 avatar-sm text-white" style="background-color: #b6b6b6; font-size: 15px;"
                                                            data-toggle="tooltip" data-placement="top"
                                                            data-original-title="{{ $dev->request_by }}"
                                                            data-initial="{{ substr($dev->request_by, 0, 1) }}">
                                                    </figure>
                                                </div>
                                                <div class="media-title" style="clear:both; font-size: large;">
                                                    {{ mb_strimwidth($dev->title, 0,50, '...') }}
                                                </div>
{{--                                                <div class="text-md-left text-muted" style="margin-top: -8px;">--}}
{{--                                                    {{ mb_strimwidth($dev->title, 0,50, '...') }}--}}
{{--                                                </div>--}}
                                                <hr style="border-width: 1px 0px 0px 0px;border-style:solid;border-color: #e0e0e0;
                                                            height:1px;margin-top: 15px;margin-bottom: 10px;width:100%">
                                                <?php if($dev->priority == 'Critical') {
                                                    $bg_type = '#FF0000';
                                                }else if($dev->priority == 'High'){
                                                    $bg_type = '#FFA500';
                                                }else{
                                                    $bg_type = '#008000';
                                                }
                                                ?>
                                                <div class="text-sm-left text-muted" style="float:left; margin-top: -1px;">
                                                    <figure class="avatar sm-2 text-white" style="width: 15px; height: 15px; background-color: {{$bg_type}}; font-size: small;" data-initial=""></figure>
                                                </div>
                                                <div class="text-sm-left text-muted" style="float:left; padding-left: 10px;">
                                                    {{$dev->dev_id}}
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>

                    @endforeach
                </div>

                <div class="col-md-3">
                    <div class="card status_title">
                        <h5 class="status_name">DEV TO DO</h5>
                    </div>

                    @foreach ($dev_to_do_list as $dev)

                        <div class="card">
                            <a href="{{ url('admin/dev/'. $dev->dev_id .'/edit')}}" style="text-decoration: none;">
                                <div class="card-body" style="margin: 0px -5px -20px -5px;">
                                    <div class="media" style="padding-bottom: 0px;">
                                        <div class="form-group" style="width: 100%;">

                                            <div style="color: #8b8a8a; font-weight: 600; float:left; font-size: 13px;">
                                                {{ $dev->type }}
                                            </div>
                                            <div style="float: right;">
                                                <figure class="avatar mr-2 avatar-sm text-white"
                                                        style="background-color: #848484; font-size: 15px; margin-left: -15px; z-index: 1; border: 1px solid white;"
                                                        data-toggle="tooltip" data-placement="top"
                                                        data-original-title="{{ $dev->assign_to }}"
                                                        data-initial="{{ substr($dev->assign_to, 0, 1) }}">
                                                </figure>
                                            </div>
                                            <div style="float: right;">
                                                <figure class="avatar mr-2 avatar-sm text-white" style="background-color: #b6b6b6; font-size: 15px;"
                                                        data-toggle="tooltip" data-placement="top"
                                                        data-original-title="{{ $dev->request_by }}"
                                                        data-initial="{{ substr($dev->request_by, 0, 1) }}">
                                                </figure>
                                            </div>
                                            <div class="media-title" style="clear:both; font-size: large;">
                                                {{ mb_strimwidth($dev->title, 0,50, '...') }}
                                            </div>
                                            {{--                                                <div class="text-md-left text-muted" style="margin-top: -8px;">--}}
                                            {{--                                                    {{ mb_strimwidth($dev->title, 0,50, '...') }}--}}
                                            {{--                                                </div>--}}
                                            <hr style="border-width: 1px 0px 0px 0px;border-style:solid;border-color: #e0e0e0;
                                                            height:1px;margin-top: 15px;margin-bottom: 10px;width:100%">

                                            <?php if($dev->priority == 'Critical') {
                                                $bg_type = '#FF0000';
                                            }else if($dev->priority == 'High'){
                                                $bg_type = '#FFA500';
                                            }else{
                                                $bg_type = '#008000';
                                            }
                                            ?>
                                            <div class="text-sm-left text-muted" style="float:left; margin-top: -1px;">
                                                <figure class="avatar sm-2 text-white" style="width: 15px; height: 15px; background-color: {{$bg_type}}; font-size: small;" data-initial=""></figure>
                                            </div>
                                            <div class="text-sm-left text-muted" style="float:left; padding-left: 10px;">
                                                {{$dev->dev_id}}
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>

                    @endforeach
                </div>

                <div class="col-md-3">
                    <div class="card status_title">
                        <h5 class="status_name">DEV IN PROGRESS</h5>
                    </div>

                    @foreach ($dev_in_progress_list as $dev)

                        <div class="card">
                            <a href="{{ url('admin/dev/'. $dev->dev_id .'/edit')}}" style="text-decoration: none;">
                                <div class="card-body" style="margin: 0px -5px -20px -5px;">
                                    <div class="media" style="padding-bottom: 0px;">
                                        <div class="form-group" style="width: 100%;">

                                            <div style="color: #8b8a8a; font-weight: 600; float:left; font-size: 13px;">
                                                {{$dev->type}}
                                            </div>
                                            <div style="float: right;">
                                                <figure class="avatar mr-2 avatar-sm text-white"
                                                        style="background-color: #848484; font-size: 15px; margin-left: -15px; z-index: 1; border: 1px solid white;"
                                                        data-toggle="tooltip" data-placement="top"
                                                        data-original-title="{{ $dev->assign_to }}"
                                                        data-initial="{{ substr($dev->assign_to, 0, 1) }}">
                                                </figure>
                                            </div>
                                            <div style="float: right;">
                                                <figure class="avatar mr-2 avatar-sm text-white" style="background-color: #b6b6b6; font-size: 15px;"
                                                        data-toggle="tooltip" data-placement="top"
                                                        data-original-title="{{ $dev->request_by }}"
                                                        data-initial="{{ substr($dev->request_by, 0, 1) }}">
                                                </figure>
                                            </div>
                                            <div class="media-title" style="clear:both; font-size: large;">
                                                {{ mb_strimwidth($dev->title, 0,50, '...') }}
                                            </div>
                                            {{--                                                <div class="text-md-left text-muted" style="margin-top: -8px;">--}}
                                            {{--                                                    {{ mb_strimwidth($dev->title, 0,50, '...') }}--}}
                                            {{--                                                </div>--}}
                                            <hr style="border-width: 1px 0px 0px 0px;border-style:solid;border-color: #e0e0e0;
                                                            height:1px;margin-top: 15px;margin-bottom: 10px;width:100%">

                                            <?php if($dev->priority == 'Critical') {
                                                $bg_type = '#FF0000';
                                            }else if($dev->priority == 'High'){
                                                $bg_type = '#FFA500';
                                            }else{
                                                $bg_type = '#008000';
                                            }
                                            ?>
                                            <div class="text-sm-left text-muted" style="float:left; margin-top: -1px;">
                                                <figure class="avatar sm-2 text-white" style="width: 15px; height: 15px; background-color: {{$bg_type}}; font-size: small;" data-initial=""></figure>
                                            </div>
                                            <div class="text-sm-left text-muted" style="float:left; padding-left: 10px;">
                                                {{$dev->dev_id}}
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>

                    @endforeach
                </div>

                <div class="col-md-3">
                    <div class="card status_title">
                        <h5 class="status_name">DEV REVIEW</h5>
                    </div>

                    @foreach ($dev_review_list as $dev)

                        <div class="card">
                            <a href="{{ url('admin/dev/'. $dev->dev_id .'/edit')}}" style="text-decoration: none;">
                                <div class="card-body" style="margin: 0px -5px -20px -5px;">
                                    <div class="media" style="padding-bottom: 0px;">
                                        <div class="form-group" style="width: 100%;">

                                            <div style="color: #8b8a8a; font-weight: 600; float:left; font-size: 13px;">
                                                {{$dev->type}}
                                            </div>
                                            <div style="float: right;">
                                                <figure class="avatar mr-2 avatar-sm text-white"
                                                        style="background-color: #848484; font-size: 15px; margin-left: -15px; z-index: 1; border: 1px solid white;"
                                                        data-toggle="tooltip" data-placement="top"
                                                        data-original-title="{{ $dev->assign_to }}"
                                                        data-initial="{{ substr($dev->assign_to, 0, 1) }}">
                                                </figure>
                                            </div>
                                            <div style="float: right;">
                                                <figure class="avatar mr-2 avatar-sm text-white" style="background-color: #b6b6b6; font-size: 15px;"
                                                        data-toggle="tooltip" data-placement="top"
                                                        data-original-title="{{ $dev->request_by }}"
                                                        data-initial="{{ substr($dev->request_by, 0, 1) }}">
                                                </figure>
                                            </div>
                                            <div class="media-title" style="clear:both; font-size: large;">
                                                {{ mb_strimwidth($dev->title, 0,50, '...') }}
                                            </div>
                                            {{--                                                <div class="text-md-left text-muted" style="margin-top: -8px;">--}}
                                            {{--                                                    {{ mb_strimwidth($dev->title, 0,50, '...') }}--}}
                                            {{--                                                </div>--}}
                                            <hr style="border-width: 1px 0px 0px 0px;border-style:solid;border-color: #e0e0e0;
                                                            height:1px;margin-top: 15px;margin-bottom: 10px;width:100%">

                                            <?php if($dev->priority == 'Critical') {
                                                $bg_type = '#FF0000';
                                            }else if($dev->priority == 'High'){
                                                $bg_type = '#FFA500';
                                            }else{
                                                $bg_type = '#008000';
                                            }
                                            ?>
                                            <div class="text-sm-left text-muted" style="float:left; margin-top: -1px;">
                                                <figure class="avatar sm-2 text-white" style="width: 15px; height: 15px; background-color: {{$bg_type}}; font-size: small;" data-initial=""></figure>
                                            </div>
                                            <div class="text-sm-left text-muted" style="float:left; padding-left: 10px;">
                                                {{$dev->dev_id}}
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>

                    @endforeach
                </div>

                <div class="col-md-3">
                    <div class="card status_title">
                        <h5 class="status_name">DEV COMPLETED</h5>
                    </div>

                    @foreach ($dev_done_list as $dev)

                        <div class="card">
                            <a href="{{ url('admin/dev/'. $dev->dev_id .'/edit')}}" style="text-decoration: none;">
                                <div class="card-body" style="margin: 0px -5px -20px -5px;">
                                    <div class="media" style="padding-bottom: 0px;">
                                        <div class="form-group" style="width: 100%;">

                                            <div style="color: #8b8a8a; font-weight: 600; float:left; font-size: 13px;">
                                                {{$dev->type}}
                                            </div>
                                            <div style="float: right;">
                                                <figure class="avatar mr-2 avatar-sm text-white"
                                                        style="background-color: #848484; font-size: 15px; margin-left: -15px; z-index: 1; border: 1px solid white;"
                                                        data-toggle="tooltip" data-placement="top"
                                                        data-original-title="{{ $dev->assign_to }}"
                                                        data-initial="{{ substr($dev->assign_to, 0, 1) }}">
                                                </figure>
                                            </div>
                                            <div style="float: right;">
                                                <figure class="avatar mr-2 avatar-sm text-white" style="background-color: #b6b6b6; font-size: 15px;"
                                                        data-toggle="tooltip" data-placement="top"
                                                        data-original-title="{{ $dev->request_by }}"
                                                        data-initial="{{ substr($dev->request_by, 0, 1) }}">
                                                </figure>
                                            </div>
                                            <div class="media-title" style="clear:both; font-size: large;">
                                                {{ mb_strimwidth($dev->title, 0,50, '...') }}
                                            </div>
                                            {{--                                                <div class="text-md-left text-muted" style="margin-top: -8px;">--}}
                                            {{--                                                    {{ mb_strimwidth($dev->title, 0,50, '...') }}--}}
                                            {{--                                                </div>--}}
                                            <hr style="border-width: 1px 0px 0px 0px;border-style:solid;border-color: #e0e0e0;
                                                            height:1px;margin-top: 15px;margin-bottom: 10px;width:100%">

                                            <div class="text-sm-left text-muted" style="float:left; margin-top: -1px;">
                                                <figure class="avatar sm-2 text-white" style="width: 15px; height: 15px; background-color: #FF0000; font-size: small;" data-initial=""></figure>
                                            </div>
                                            <div class="text-sm-left text-muted" style="float:left; padding-left: 10px;">
                                                {{$dev->dev_id}}
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
