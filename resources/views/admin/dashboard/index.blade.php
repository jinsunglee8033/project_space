@extends('layouts.dashboard')

@section('content')

    <section class="section">
        <div class="section-header">
            <h1>HOME</h1>
        </div>

        @include('admin.campaign.flash')

        <div class="row">

            <?php ?>

            <?php if( auth()->user()->team == 'MDM') { ?>
            <div class="col-lg-4 col-md-4 col-sm-12">
                <a href="{{ url('admin/mm_request/board')}}">
                    <div class="card card-statistic-1">
                        <div class="card-icon shadow-primary bg-primary">
                            <i class="fas fa-th-large"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>MM Board</h4>
                            </div>
                            <div class="card-body">
                                Request Status Board
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <?php } ?>

            <?php if( auth()->user()->team == 'B2B Marketing' || auth()->user()->team == 'SOM') { ?>
            <div class="col-lg-4 col-md-4 col-sm-12">
                <a href="{{ url('admin/npd_planner_board_ivy')}}">
                    <div class="card card-statistic-1">
                        <div class="card-icon shadow-primary bg-primary">
                            <i class="fas fa-th-large"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>IVY NPD Planner Board</h4>
                            </div>
                            <div class="card-body">
                                Request Status Board
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <?php } ?>

            <?php if( auth()->user()->team == 'Red Trade Marketing (A&A)' || auth()->user()->team == 'SOM') { ?>
            <div class="col-lg-4 col-md-4 col-sm-12">
                <a href="{{ url('admin/npd_planner_board_red')}}">
                    <div class="card card-statistic-1">
                        <div class="card-icon shadow-primary bg-primary">
                            <i class="fas fa-th-large"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>RED NPD Planner Board</h4>
                            </div>
                            <div class="card-body">
                                Request Status Board
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <?php } ?>

            <?php if( auth()->user()->team == 'Legal') { ?>
            <div class="col-lg-4 col-md-4 col-sm-12">
                <a href="{{ url('admin/legal_request/board')}}">
                    <div class="card card-statistic-1">
                        <div class="card-icon shadow-primary bg-primary">
                            <i class="fas fa-th-large"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Legal Board</h4>
                            </div>
                            <div class="card-body">
                                Request Status Board
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <?php } ?>

                <?php if( auth()->user()->team == 'Legal RA') { ?>
                <div class="col-lg-4 col-md-4 col-sm-12">
                    <a href="{{ url('admin/ra_request/board')}}">
                        <div class="card card-statistic-1">
                            <div class="card-icon shadow-primary bg-primary">
                                <i class="fas fa-th-large"></i>
                            </div>
                            <div class="card-wrap">
                                <div class="card-header">
                                    <h4>RA Board</h4>
                                </div>
                                <div class="card-body">
                                    Request Status Board
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <?php } ?>

                <?php if( auth()->user()->team == 'Purchasing') { ?>
                <div class="col-lg-4 col-md-4 col-sm-12">
                    <a href="{{ url('admin/npd_po_request')}}">
                        <div class="card card-statistic-1">
                            <div class="card-icon shadow-primary bg-primary">
                                <i class="fas fa-th-large"></i>
                            </div>
                            <div class="card-wrap">
                                <div class="card-header">
                                    <h4>NPD PO Board</h4>
                                </div>
                                <div class="card-body">
                                    Request Status Board
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <?php } ?>

                <?php if( ( auth()->user()->team == 'Brand Design') || (auth()->user()->team == 'Production Design' ) || (auth()->user()->team == 'Industrial Design' ) || (auth()->user()->function == 'Design') ) { ?>
                <div class="col-lg-4 col-md-4 col-sm-12">
                    <a href="{{ url('admin/npd_design_request/board')}}">
                        <div class="card card-statistic-1">
                            <div class="card-icon shadow-primary bg-primary">
                                <i class="fas fa-th-large"></i>
                            </div>
                            <div class="card-wrap">
                                <div class="card-header">
                                    <h4>NPD Design Board</h4>
                                </div>
                                <div class="card-body">
                                    Request Status Board
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <?php } ?>

                <?php if( (auth()->user()->team == 'Display (D&P)') || (auth()->user()->team == 'PE (D&P)')  ) { ?>
                <div class="col-lg-4 col-md-4 col-sm-12">
                    <a href="{{ url('admin/pe_request/board')}}">
                        <div class="card card-statistic-1">
                            <div class="card-icon shadow-primary bg-primary">
                                <i class="fas fa-th-large"></i>
                            </div>
                            <div class="card-wrap">
                                <div class="card-header">
                                    <h4>Display & PE Board</h4>
                                </div>
                                <div class="card-body">
                                    Request Status Board
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <?php } ?>

                <?php if( auth()->user()->team == 'QM QA') { ?>
                <div class="col-lg-4 col-md-4 col-sm-12">
                    <a href="{{ url('admin/qc_request')}}">
                        <div class="card card-statistic-1">
                            <div class="card-icon shadow-primary bg-primary">
                                <i class="fas fa-th-large"></i>
                            </div>
                            <div class="card-wrap">
                                <div class="card-header">
                                    <h4>QA Board</h4>
                                </div>
                                <div class="card-body">
                                    Request Status Board
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            <?php } ?>


                <?php if( (auth()->user()->function == 'Admin') || (auth()->user()->function == 'Management') || (auth()->user()->function == 'Product') ) { ?>

                <?php if( (auth()->user()->function == 'Admin') || (auth()->user()->function == 'Management') ) { ?>
                <div class="col-lg-4 col-md-4 col-sm-12">
                    <a href="{{ url('admin/dashboard/project_status')}}">
                        <div class="card card-statistic-1">
                            <div class="card-icon shadow-primary bg-primary">
                                <i class="fas fa-server"></i>
                            </div>
                            <div class="card-wrap">
                                <div class="card-header">
                                    <h4>PROJECTS</h4>
                                </div>
                                <div class="card-body">
                                    Project Status
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <?php } ?>

                <div class="col-lg-4 col-md-4 col-sm-12">
                    <a href="{{ url('admin/project')}}">
                        <div class="card card-statistic-1">
                            <div class="card-icon shadow-primary bg-primary">
                                <i class="fas fa-calendar"></i>
                            </div>
                            <div class="card-wrap">
                                <div class="card-header">
                                    <h4>PROJECTS</h4>
                                </div>
                                <div class="card-body">
                                    Project NPD
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <?php } ?>


                <?php if( (auth()->user()->function == 'Admin') || (auth()->user()->function == 'Management') || (auth()->user()->function == 'Product' && auth()->user()->role == 'Team Lead') ) { ?>

                <div class="col-lg-4 col-md-4 col-sm-12">
                    <a href="{{ url('admin/project_pre_approve_list')}}">
                        <div class="card card-statistic-1">
                            <div class="card-icon shadow-primary bg-primary">
                                <i class="fas fa-server"></i>
                            </div>
                            <div class="card-wrap">
                                <div class="card-header">
                                    <h4>PROJECTS</h4>
                                </div>
                                <div class="card-body">
                                    NPD Approval List
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <?php } ?>

                <div class="col-lg-4 col-md-4 col-sm-12">
                    <a href="mailto:admin@projectspace.net?subject=[Help Desk] Project Space">
                        <div class="card card-statistic-1">
                            <div class="card-icon shadow-primary bg-primary">
                                <i class="fas fa-paper-plane"></i>
                            </div>
                            <div class="card-wrap">
                                <div class="card-header">
                                    <h4>PROJECT SPACE</h4>
                                </div>
                                <div class="card-body">
                                    Help Desk
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-lg-4 col-md-4 col-sm-12">
                    <a href="https://app.tango.us/app/workflow/Project-Space-User-Manual-2024-fcc35d0dc67044f7a14bf8800d181d24" target="_blank">
                        <div class="card card-statistic-1">
                            <div class="card-icon shadow-primary bg-primary">
                                <i class="fas fa-book"></i>
                            </div>
                            <div class="card-wrap">
                                <div class="card-header">
                                    <h4>PROJECT SPACE</h4>
                                </div>
                                <div class="card-body">
                                    User Manual
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

        </div>

    </section>


    <div class="modal fade" id="myModal-qr" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">QR Code Request</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                            <label style="color: #b91d19; font-size: medium">QR Code Form Link - https://app.smartsheet.com/b/form/e0c97cdbdc7b4ecaaebb269ca32e79e1</label>
                        <div class="row">
                            <div class="col-sm-3">
                                <iframe id="cartoonVideo" width="1080" height="1000" src="https://app.smartsheet.com/b/form/e0c97cdbdc7b4ecaaebb269ca32e79e1" frameborder="0" allowfullscreen></iframe>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="myModal-coupon" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">Coupon Code</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label style="color: #b91d19; font-size: medium">Coupon Code Form Link - https://app.smartsheet.com/b/form/fc883f81ce8e40d5b58c68c636998f37</label>
                        <div class="row">
                            <div class="col-sm-3">
                                <iframe id="cartoonVideo" width="1080" height="1000" src="https://app.smartsheet.com/b/form/fc883f81ce8e40d5b58c68c636998f37" frameborder="0" allowfullscreen></iframe>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="myModal-analytic" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">Performance Marketing Analytic Report</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label style="color: #b91d19; font-size: medium">Analytic Report Request Form Link - https://app.smartsheet.com/b/form/38e9b1c33c914b14904eebc0f133b33c</label>
                        <div class="row">
                            <div class="col-sm-3">
                                <iframe id="cartoonVideo" width="1080" height="1000" src="https://app.smartsheet.com/b/form/38e9b1c33c914b14904eebc0f133b33c" frameborder="0" allowfullscreen></iframe>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>


@endsection
