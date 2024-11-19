<?php $request_type = 'project_planner'; ?>


<div class="inner_box" style="margin: 30px 15px 0px 20px;">

    <form method="POST" action="{{ route('npd_planner_request.add_project_planner') }}" enctype="multipart/form-data">
    @csrf
        <input type="hidden" name="{{ $request_type }}_t_id" value="{{ $task_id }}" />
        <input type="hidden" name="{{ $request_type }}_request_type" value="{{ $request_type }}" />
        <input type="hidden" name="{{ $request_type }}_author_id" value="{{ Auth::user()->id }}" />
        <input type="hidden" name="{{ $request_type }}_team" value="{{ $team }}" />

    <h5 style="color: #b91d19; padding: 0 0 25px 0;">Please complete the information for the Project Planner</h5>

        <div class="form-group">
            <label>Project Code: <b style="color: #b91d19">*</b></label>
            <input type="text" name="{{ $request_type }}_project_code" class="form-control" value="" required>
        </div>

        <div class="form-group">
            <label>Due  Date: <b style="color: #b91d19">*</b></label>
            <input type="text" name="{{ $request_type }}_due_date" class="form-control" value="">
        </div>

        <div class="form-group">
            <label>Target Door Number: <b style="color: #b91d19">*</b></label>
            <input type="text" name="{{ $request_type }}_target_door_number" class="form-control" value="" required>
        </div>

{{--        <div class="row">--}}
{{--            <div class="col-md-6">--}}
{{--                <div class="form-group">--}}
{{--                    <label>NY Target Receiving  Date: <b style="color: #b91d19">*</b> <b style="color: #b91d19">(Select from the calendar)</b></label>--}}
{{--                    <input type="text" name="{{ $request_type }}_ny_target_receiving_date" class="form-control" required value="">--}}
{{--                </div>--}}
{{--            </div>--}}
{{--            <div class="col-md-6">--}}
{{--                <div class="form-group">--}}
{{--                    <label>LA Target Receiving  Date: <b style="color: #b91d19">*</b> <b style="color: #b91d19">(Select from the calendar)</b></label>--}}
{{--                    <input type="text" name="{{ $request_type }}_la_target_receiving_date" class="form-control" required value="">--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}

{{--        <div class="row">--}}
{{--            <div class="col-md-6">--}}
{{--                <div class="form-group">--}}
{{--                    <label>NY Planned Launch  Date: <b style="color: #b91d19">*</b> <b style="color: #b91d19">(Select from the calendar)</b></label>--}}
{{--                    <input type="text" name="{{ $request_type }}_ny_planned_launch_date" class="form-control" required value="">--}}
{{--                </div>--}}
{{--            </div>--}}
{{--            <div class="col-md-6">--}}
{{--                <div class="form-group">--}}
{{--                    <label>LA Planned Launch  Date: <b style="color: #b91d19">*</b> <b style="color: #b91d19">(Select from the calendar)</b></label>--}}
{{--                    <input type="text" name="{{ $request_type }}_la_planned_launch_date" class="form-control" required value="">--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}

{{--        <div class="form-group">--}}
{{--            <label>NSP ($):</label>--}}
{{--            <input type="text" name="{{ $request_type }}_nsp" class="form-control" value="" >--}}
{{--        </div>--}}

{{--        <div class="form-group">--}}
{{--            <label>SRP ($):</label>--}}
{{--            <input type="text" name="{{ $request_type }}_srp" class="form-control" value="" >--}}
{{--        </div>--}}

        <div class="form-group">
            <label>Sales Channel:</label>
            <div class="columns" style="column-count: 2;">
                <?php foreach($sales_channel_list as $value): ?>
                <div class="col-md">
                    <div class="form-check" style="padding-left: 0px;">
                        <input  type="checkbox"
                                name="{{ $request_type }}_sales_channel[]"
                                value="{{ $value }}" id="project_planner_{{ $value }}"
                        >
                        <label class="form-check-label" for="project_planner_{{ $value }}">
                            {{ $value }}
                        </label>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="form-group">
            <label>If Retailer, specify:</label>
            <input type="text" name="{{ $request_type }}_if_others_sales_channel" class="form-control" value="">
        </div>

{{--        <div class="form-group">--}}
{{--            <label>Expected Reorder/Unit:</label>--}}
{{--            {!! Form::textarea($request_type.'_expected_reorder', null, ['class' => 'form-control summernote']) !!}--}}
{{--        </div>--}}

{{--        <div class="form-group">--}}
{{--            <label>Expected Sales (12 Months):</label>--}}
{{--            <textarea class="form-control" name="{{ $request_type }}_expected_sales" style="height:120px;"></textarea>--}}
{{--        </div>--}}

{{--        <div class="form-group">--}}
{{--            <label>Benchmark Item:</label>--}}
{{--            {!! Form::textarea($request_type.'_benchmark_item', null, ['class' => 'form-control summernote']) !!}--}}
{{--        </div>--}}

{{--        <div class="form-group">--}}
{{--            <label>Actual Sales (12 Months):</label>--}}
{{--            <textarea class="form-control" name="{{ $request_type }}_actual_sales" style="height:120px;"></textarea>--}}
{{--        </div>--}}

        <div class="form-group">
            <label>Display Plan: <b style="color: #b91d19">*</b></label>
            <select class="form-control" name="{{ $request_type }}_display_plan" required>
                <option value="">Select</option>
                <?php foreach($display_plan_list as $val): ?>
                <option value="{{ $val }}">{{ $val }}</option>
                <?php endforeach ?>
            </select>
        </div>

        <div class="form-group">
            <label>If others, specify:</label>
            <textarea class="form-control" name="{{ $request_type }}_if_others_display_plan" style="height:60px;"></textarea>
        </div>

        <div class="form-group">
            <label>Display Type: <b style="color: #b91d19">*</b></label>
            <select class="form-control" name="{{ $request_type }}_display_type" required>
                <option value="">Select</option>
                <?php foreach($display_type_list as $val): ?>
                <option value="{{ $val }}">{{ $val }}</option>
                <?php endforeach ?>
            </select>
        </div>

        <div class="form-group">
            <label>Penetration Type: <b style="color: #b91d19">*</b></label>
            <select class="form-control" name="{{ $request_type }}_penetration_type" required>
                <option value="">Select</option>
                <?php foreach($penetration_type_list as $val): ?>
                <option value="{{ $val }}">{{ $val }}</option>
                <?php endforeach ?>
            </select>
        </div>

        <div class="form-group">
            <label>If others, specify:</label>
            <textarea class="form-control" name="{{ $request_type }}_if_others_penetration_type" style="height:60px;"></textarea>
        </div>

        <div class="form-group">
            <label>Tester: <b style="color: #b91d19">*</b></label>
            <select class="form-control" name="{{ $request_type }}_tester" required>
                <option value="">Select</option>
                <?php foreach($tester_list as $val): ?>
                <option value="{{ $val }}">{{ $val }}</option>
                <?php endforeach ?>
            </select>
        </div>

        <div class="form-group">
            <label>Promotion Items:</label>
            <div class="columns" style="column-count: 3;">
                <?php foreach($promotion_items_list as $val): ?>
                <div class="col-md">
                    <div class="form-check" style="padding-left: 0px;">
                        <input  type="checkbox"
                                name="{{ $request_type }}_promotion_items[]"
                                value="{{ $val }}" id="project_planner_{{ $val }}"
                        >
                        <label class="form-check-label " for="project_planner_{{ $val }}">
                            {{ $val }}
                        </label>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="form-group">
            <label>If others, specify:</label>
            <textarea class="form-control" name="{{ $request_type }}_if_others_promotion_items" style="height:60px;"></textarea>
        </div>

        <div class="form-group">
            <label>Return Plan: <b style="color: #b91d19">*</b></label>
            <select class="form-control" name="{{ $request_type }}_return_plan" required>
                <option value="">Select</option>
                <?php foreach($return_plan_list as $val): ?>
                <option value="{{ $val }}">{{ $val }}</option>
                <?php endforeach ?>
            </select>
        </div>

{{--        <div class="form-group">--}}
{{--            <label>Return Plan Description (If selected return):</label>--}}
{{--            <textarea class="form-control" name="{{ $request_type }}_return_plan_description" style="height:120px;"></textarea>--}}
{{--        </div>--}}

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
            var count = 6; // past 4pm
        }else{
            var count = 5; // before 4pm
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

    });

</script>
