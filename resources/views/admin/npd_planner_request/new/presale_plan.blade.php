<?php $request_type = 'presale_plan'; ?>

<div class="inner_box" style="margin: 30px 15px 0px 20px;">

    <form method="POST" action="{{ route('npd_planner_request.add_presale_plan') }}" enctype="multipart/form-data">
    @csrf
        <input type="hidden" name="{{ $request_type }}_t_id" value="{{ $task_id }}" />
        <input type="hidden" name="{{ $request_type }}_request_type" value="{{ $request_type }}" />
        <input type="hidden" name="{{ $request_type }}_author_id" value="{{ Auth::user()->id }}" />
        <input type="hidden" name="{{ $request_type }}_team" value="{{ $team }}" />

    <h5 style="color: #b91d19; padding: 0 0 25px 0;">Please complete the information for the Presale Plan</h5>

        <div class="form-group">
            <label>Project Code: <b style="color: #b91d19">*</b></label>
            <input type="text" name="{{ $request_type }}_project_code" class="form-control" value="" required>
        </div>

        <div class="form-group">
            <label>Due Date: <b style="color: #b91d19">*</b></label>
            <input type="text" name="{{ $request_type }}_due_date" class="form-control" value="" required>
        </div>

        <div class="form-group">
            <label>Target Door Number: <b style="color: #b91d19">*</b></label>
            <input type="text" name="{{ $request_type }}_target_door_number" class="form-control" value="" required>
        </div>

        <div class="form-group">
            <label>Promotion Items:</label>
            <div class="columns" style="column-count: 3;">
                <?php foreach($promotion_items_list as $val): ?>
                <div class="col-md">
                    <div class="form-check" style="padding-left: 0px;">
                        <input  type="checkbox"
                                name="{{ $request_type }}_promotion_items[]"
                                value="{{ $val }}" id="presale_plan_{{ $val }}"
                        >
                        <label class="form-check-label " for="presale_plan_{{ $val }}">
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

        <div class="form-group">
            <label>Return Plan Description (If selected return):</label>
            <textarea class="form-control" name="{{ $request_type }}_return_plan_description" style="height:120px;"></textarea>
        </div>

        <div class="form-group">
            <label>Purpose:</label>
            <textarea class="form-control" name="{{ $request_type }}_purpose" style="height:120px;"></textarea>
        </div>

        <div class="form-group">
            <label>Promotion Conditions:</label>
            {!! Form::textarea($request_type.'_promotion_conditions', null, ['class' => 'form-control summernote']) !!}
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Presale Start  Date: <b style="color: #b91d19">*</b> <b style="color: #b91d19">(Select from the calendar)</b></label>
                    <input type="text" name="{{ $request_type }}_presale_start_date" class="form-control" required value="">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Presale End  Date: <b style="color: #b91d19">*</b> <b style="color: #b91d19">(Select from the calendar)</b></label>
                    <input type="text" name="{{ $request_type }}_presale_end_date" class="form-control" required value="">
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Promotion Start  Date: <b style="color: #b91d19">*</b> <b style="color: #b91d19">(Select from the calendar)</b></label>
                    <input type="text" name="{{ $request_type }}_promotion_start_date" class="form-control" required value="">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Promotion End  Date: <b style="color: #b91d19">*</b> <b style="color: #b91d19">(Select from the calendar)</b></label>
                    <input type="text" name="{{ $request_type }}_promotion_end_date" class="form-control" required value="">
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label>Presale Initial Shipping Start  Date: <b style="color: #b91d19">*</b> <b style="color: #b91d19">(Select from the calendar)</b></label>
                    <input type="text" name="{{ $request_type }}_presale_initial_shipping_start_date" class="form-control" required value="">
                </div>
            </div>
        </div>


        {{--        <div class="form-group">--}}
{{--            <label>Due Date (Upload)</label>--}}
{{--            <input type="text" name="{{ $request_type }}_due_date_upload" class="form-control" value="" readonly>--}}
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

        $('input[name="<?php echo $request_type; ?>_presale_start_date"]').daterangepicker({
            singleDatePicker: true,
            minDate: today,
            locale: {
                format: 'YYYY-MM-DD',
            },
            isInvalidDate: function(date) {
                return (date.day() == 0 || date.day() == 6);
            },
        });
        $('input[name="<?php echo $request_type; ?>_presale_start_date"]').val('');
        $('input[name="<?php echo $request_type; ?>_presale_end_date"]').daterangepicker({
            singleDatePicker: true,
            minDate: today,
            locale: {
                format: 'YYYY-MM-DD'
            },
            isInvalidDate: function(date) {
                return (date.day() == 0 || date.day() == 6);
            },
        });
        $('input[name="<?php echo $request_type; ?>_presale_end_date"]').val('');
        $('input[name="<?php echo $request_type; ?>_promotion_start_date"]').daterangepicker({
            singleDatePicker: true,
            minDate: today,
            locale: {
                format: 'YYYY-MM-DD'
            },
            isInvalidDate: function(date) {
                return (date.day() == 0 || date.day() == 6);
            },
        });
        $('input[name="<?php echo $request_type; ?>_promotion_start_date"]').val('');
        $('input[name="<?php echo $request_type; ?>_promotion_end_date"]').daterangepicker({
            singleDatePicker: true,
            minDate: today,
            locale: {
                format: 'YYYY-MM-DD'
            },
            isInvalidDate: function(date) {
                return (date.day() == 0 || date.day() == 6);
            },
        });
        $('input[name="<?php echo $request_type; ?>_promotion_end_date"]').val('');
        $('input[name="<?php echo $request_type; ?>_presale_initial_shipping_start_date"]').daterangepicker({
            singleDatePicker: true,
            minDate: today,
            locale: {
                format: 'YYYY-MM-DD'
            },
            isInvalidDate: function(date) {
                return (date.day() == 0 || date.day() == 6);
            },
        });
        $('input[name="<?php echo $request_type; ?>_presale_initial_shipping_start_date"]').val('');

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
