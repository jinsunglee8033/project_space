<?php $request_type = 'change_request'; ?>


<div class="inner_box" style="margin: 30px 15px 0px 20px;">

    <form method="POST" action="{{ route('npd_planner_request.add_change_request') }}" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="{{ $request_type }}_t_id" value="{{ $task_id }}" />
    <input type="hidden" name="{{ $request_type }}_request_type" value="{{ $request_type }}" />
    <input type="hidden" name="{{ $request_type }}_author_id" value="{{ Auth::user()->id }}" />
        <input type="hidden" name="{{ $request_type }}_team" value="{{ $team }}" />

    <h5 style="color: #b91d19; padding: 0 0 25px 0;">Please complete the information for the Change Request</h5>

        <div class="form-group">
            <label>Due Date: <b style="color: #b91d19">*</b></label>
            <input type="text" name="{{ $request_type }}_due_date" class="form-control" value="" required>
        </div>

        <div class="form-group">
            <label>Target Door Number:</label>
            <input type="text" name="{{ $request_type }}_target_door_number" class="form-control" value="" >
        </div>

{{--        <div class="row">--}}
{{--            <div class="col-md-6">--}}
{{--                <div class="form-group">--}}
{{--                    <label>NY Launch Date: <b style="color: #b91d19">(Select from the calendar)</b></label>--}}
{{--                    <input type="text" name="{{ $request_type }}_ny_target_receiving_date" class="form-control"  value="">--}}
{{--                </div>--}}
{{--            </div>--}}
{{--            <div class="col-md-6">--}}
{{--                <div class="form-group">--}}
{{--                    <label>LA Launch Date: <b style="color: #b91d19">(Select from the calendar)</b></label>--}}
{{--                    <input type="text" name="{{ $request_type }}_la_target_receiving_date" class="form-control"  value="">--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>NY Planned Launch  Date: <b style="color: #b91d19">(Select from the calendar)</b></label>
                    <input type="text" name="{{ $request_type }}_ny_planned_launch_date" class="form-control"  value="">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>LA Planned Launch  Date: <b style="color: #b91d19">(Select from the calendar)</b></label>
                    <input type="text" name="{{ $request_type }}_la_planned_launch_date" class="form-control"  value="">
                </div>
            </div>
        </div>

        <div class="form-group">
            <label>Update Type: <b style="color: #b91d19">*</b></label>
            <select class="form-control" name="{{ $request_type }}_update_type" required>
                <option value="">Select</option>
                <?php foreach($update_type_list as $val): ?>
                <option value="{{ $val }}">{{ $val }}</option>
                <?php endforeach ?>
            </select>
        </div>

        <div class="form-group">
            <label>Revised Target Door Number:</label>
            <input type="text" name="{{ $request_type }}_revised_target_door_number" class="form-control" value="" >
        </div>

{{--        <div class="row">--}}
{{--            <div class="col-md-6">--}}
{{--                <div class="form-group">--}}
{{--                    <label>Revised NY Receiving  Date: <b style="color: #b91d19">(Select from the calendar)</b></label>--}}
{{--                    <input type="text" name="{{ $request_type }}_revised_ny_receiving_date" class="form-control"  value="">--}}
{{--                </div>--}}
{{--            </div>--}}
{{--            <div class="col-md-6">--}}
{{--                <div class="form-group">--}}
{{--                    <label>Revised LA Receiving  Date: <b style="color: #b91d19">(Select from the calendar)</b></label>--}}
{{--                    <input type="text" name="{{ $request_type }}_revised_la_receiving_date" class="form-control"  value="">--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Revised NY Launch  Date: <b style="color: #b91d19">(Select from the calendar)</b></label>
                    <input type="text" name="{{ $request_type }}_revised_ny_launch_date" class="form-control"  value="">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Revised LA Launch  Date: <b style="color: #b91d19">(Select from the calendar)</b></label>
                    <input type="text" name="{{ $request_type }}_revised_la_launch_date" class="form-control"  value="">
                </div>
            </div>
        </div>

        <div class="form-group">
            <label>Change Request Reason: <b style="color: #b91d19">*</b></label>
            <select class="form-control" name="{{ $request_type }}_change_request_reason" required>
                <option value="">Select</option>
                <?php foreach($change_request_reason_list as $val): ?>
                <option value="{{ $val }}">{{ $val }}</option>
                <?php endforeach ?>
            </select>
        </div>

        <div class="form-group">
            <label>Change Request Detail:</label>
            {!! Form::textarea($request_type.'_change_request_detail', null, ['class' => 'form-control summernote']) !!}
        </div>

        <div class="form-group">
            <label>Attachments: <b style="color: #b91d19">(20MB Max) Attach P&L Here</b></label>
            <input type="file" data-asset="default" name="{{ $request_type }}_attachment[]" class="form-control attachment last_upload" multiple="multiple"/>
            <a href="javascript:void(0);" onclick="another_upload($(this))" class="another_upload">[ Upload Another ]</a>
        </div>

        <div class="form-group">
            <input type="submit" name="submit" value="Create" style="margin-top:10px; padding: .55rem 1.5rem; font-size: medium;" class="btn btn-create submit"/>
        </div>


    </form>

</div>
<script type="text/javascript">
    $(function() {
        var today = new Date();
        if(today.getHours() >= 16){
            var count = 3; // past 4pm
        }else{
            var count = 2; // before 4pm
        }
        for (let i = 1; i <= count; i++) {
            today.setDate(today.getDate() + 1);
            if (today.getDay() === 6) {
                today.setDate(today.getDate() + 2);
            }
            else if (today.getDay() === 0) {
                today.setDate(today.getDate() + 1);
            }
        }
        $('input[name="<?php echo $request_type; ?>_due_date"]').val(today.toISOString().split('T')[0]);

        $('input[name="<?php echo $request_type; ?>_ny_target_receiving_date"]').daterangepicker({
            singleDatePicker: true,
            minDate: today,
            locale: {
                format: 'YYYY-MM-DD'
            },
            isInvalidDate: function(date) {
                return (date.day() == 0 || date.day() == 6);
            },
        });
        $('input[name="<?php echo $request_type; ?>_ny_target_receiving_date"]').val('');

        $('input[name="<?php echo $request_type; ?>_la_target_receiving_date"]').daterangepicker({
            singleDatePicker: true,
            minDate: today,
            locale: {
                format: 'YYYY-MM-DD'
            },
            isInvalidDate: function(date) {
                return (date.day() == 0 || date.day() == 6);
            },
        });
        $('input[name="<?php echo $request_type; ?>_la_target_receiving_date"]').val('');

        $('input[name="<?php echo $request_type; ?>_ny_planned_launch_date"]').daterangepicker({
            singleDatePicker: true,
            minDate: today,
            locale: {
                format: 'YYYY-MM-DD'
            },
            isInvalidDate: function(date) {
                return (date.day() == 0 || date.day() == 6);
            },
        });
        $('input[name="<?php echo $request_type; ?>_ny_planned_launch_date"]').val('');

        $('input[name="<?php echo $request_type; ?>_la_planned_launch_date"]').daterangepicker({
            singleDatePicker: true,
            minDate: today,
            locale: {
                format: 'YYYY-MM-DD'
            },
            isInvalidDate: function(date) {
                return (date.day() == 0 || date.day() == 6);
            },
        });
        $('input[name="<?php echo $request_type; ?>_la_planned_launch_date"]').val('');

        $('input[name="<?php echo $request_type; ?>_revised_ny_receiving_date"]').daterangepicker({
            singleDatePicker: true,
            minDate: today,
            locale: {
                format: 'YYYY-MM-DD'
            },
            isInvalidDate: function(date) {
                return (date.day() == 0 || date.day() == 6);
            },
        });
        $('input[name="<?php echo $request_type; ?>_revised_ny_receiving_date"]').val('');

        $('input[name="<?php echo $request_type; ?>_revised_la_receiving_date"]').daterangepicker({
            singleDatePicker: true,
            minDate: today,
            locale: {
                format: 'YYYY-MM-DD'
            },
            isInvalidDate: function(date) {
                return (date.day() == 0 || date.day() == 6);
            },
        });
        $('input[name="<?php echo $request_type; ?>_revised_la_receiving_date"]').val('');

        $('input[name="<?php echo $request_type; ?>_revised_ny_launch_date"]').daterangepicker({
            singleDatePicker: true,
            minDate: today,
            locale: {
                format: 'YYYY-MM-DD'
            },
            isInvalidDate: function(date) {
                return (date.day() == 0 || date.day() == 6);
            },
        });
        $('input[name="<?php echo $request_type; ?>_revised_ny_launch_date"]').val('');

        $('input[name="<?php echo $request_type; ?>_revised_la_launch_date"]').daterangepicker({
            singleDatePicker: true,
            minDate: today,
            locale: {
                format: 'YYYY-MM-DD'
            },
            isInvalidDate: function(date) {
                return (date.day() == 0 || date.day() == 6);
            },
        });
        $('input[name="<?php echo $request_type; ?>_revised_la_launch_date"]').val('');

        var today = new Date();
        if(today.getHours() >= 16){
            var count = 2; // past 4pm
        }else{
            var count = 1; // before 4pm
        }
        for (let i = 1; i <= count; i++) {
            today.setDate(today.getDate() + 1);
            if (today.getDay() === 6) {
                today.setDate(today.getDate() + 2);
            }
            else if (today.getDay() === 0) {
                today.setDate(today.getDate() + 1);
            }
        }
        $('input[name="<?php echo $request_type; ?>_due_date_upload"]').val(today.toISOString().split('T')[0]);

    });

</script>
