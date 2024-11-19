<?php $task_type = 'display_request'; ?>

<div class="inner_box" style="margin: 30px 15px 0px 20px;">

    <form method="POST" action="{{ route('display_request.add_display_request') }}" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="{{ $task_type }}_p_id" value="{{ $project->id }}" />
    <input type="hidden" name="{{ $task_type }}_task_type" value="{{ $task_type }}" />
    <input type="hidden" name="{{ $task_type }}_author_id" value="{{ Auth::user()->id }}" />

    <h5 style="color: #b91d19; padding: 0 0 25px 0;">Please provide the necessary information to complete the Display Request.</h5>

        <div class="form-group">
            <label>Request Type:</label>
            <select class="form-control" name="{{ $task_type }}_request_type" onchange="request_type($(this))" required>
                <option value="">Select</option>
                <?php foreach($request_type_list as $val): ?>
                <option value="{{ $val }}">{{ $val }}</option>
                <?php endforeach ?>
            </select>
        </div>

        <div class="form-group show-type" hidden>
            <label>Show Type:</label>
            <select class="form-control" name="{{ $task_type }}_show_type">
                <option value="">Select</option>
                <?php foreach($show_type_list as $val): ?>
                <option value="{{ $val }}">{{ $val }}</option>
                <?php endforeach ?>
            </select>
        </div>
        <div class="form-group show-type" hidden>
            <label>Show Location (City, Country):</label>
            <input type="text" name="{{ $task_type }}_show_location" class="form-control" value="">
        </div>

        <div class="form-group">
            <label>Product Category(s):</label>
            <div class="columns" style="column-count: 2;">
                <?php foreach($product_category_list as $value): ?>
                <div class="col-md">
                    <div class="form-check" style="padding-left: 0px;">
                        <input  type="checkbox"
                                name="{{ $task_type }}_product_category[]"
                                value="{{ $value }}"
                        >
                        <label class="form-check-label " for="{{ $value }}">
                            {{ $value }}
                        </label>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

{{--        <div class="form-group">--}}
{{--            <label>Priority:</label>--}}
{{--            <select class="form-control" name="{{ $task_type }}_priority" onchange="urgent_check($(this))">--}}
{{--                --}}{{--            <option value="">Select</option>--}}
{{--                <?php foreach($priorities as $val): ?>--}}
{{--                <option value="{{ $val }}">{{ $val }}</option>--}}
{{--                <?php endforeach ?>--}}
{{--            </select>--}}
{{--        </div>--}}

{{--        <div class="form-group urgent_due_date" hidden>--}}
{{--            <label>Due Date (Urgent):</label>--}}
{{--            <input required type="text" name="{{ $task_type }}_due_date_urgent" id="due_date"--}}
{{--                   class="form-control @error($task_type.'_due_date_urgent') is-invalid @enderror @if (!$errors->has($task_type.'_due_date_urgent') && old($task_type.'_due_date_urgent')) is-valid @endif"--}}
{{--                   value="{{ old($task_type . '_due_date_urgent', null) }}">--}}
{{--        </div>--}}

{{--        <div class="form-group urgent_due_date" hidden>--}}
{{--            <label>Urgent Reason:</label>--}}
{{--            <textarea class="form-control" name="{{ $task_type }}_urgent_reason" style="height:100px;"></textarea>--}}
{{--        </div>--}}

        <div class="form-group">
            <label>Display Type:</label>
            <select class="form-control" name="{{ $task_type }}_display_type" required>
                <option value="">Select</option>
                <?php foreach($display_type_list as $val): ?>
                <option value="{{ $val }}">{{ $val }}</option>
                <?php endforeach ?>
            </select>
        </div>

        <div class="form-group">
            <label>Display Style:</label>
            <select class="form-control" name="{{ $task_type }}_display_style" onchange="others_display_style($(this))" required>
                <option value="">Select</option>
                <?php foreach($display_style_list as $val): ?>
                <option value="{{ $val }}">{{ $val }}</option>
                <?php endforeach ?>
            </select>
        </div>

        <div class="form-group specify_display_style" hidden>
            <label>If Others, Specify:</label>
            <input type="text" name="{{ $task_type }}_specify_display_style" class="form-control" value="">
        </div>

        <div class="form-group">
            <label>Display #: (Please ensure that Display# is set up correctly with "Item# + S".)</label>
            <input type="text" name="{{ $task_type }}_display" class="form-control" value="" required>
        </div>

        <div class="form-group">
            <label>Total Display QTY #:</label>
            <input type="text" name="{{ $task_type }}_total_display_qty" class="form-control" value="" required>
        </div>

        <div class="form-group">
            <label>Display Budget Per EA #:</label>
            <input type="text" name="{{ $task_type }}_display_budget_per_ea" class="form-control" value="" required>
        </div>

        <div class="form-group">
            <label>Display Budget Code:</label>
            <input type="text" name="{{ $task_type }}_display_budget_code" class="form-control" value="" required>
        </div>

        <div class="form-group">
            <label>Display Ready Date:</label>
            <input type="text" name="{{ $task_type }}_due_date" id="{{ $task_type }}_due_date" class="form-control" value="" required>
        </div>

{{--        <div class="form-group">--}}
{{--            <label>Sales Channel:</label>--}}
{{--            <select class="form-control" name="{{ $task_type }}_account" onchange="others_account($(this))" required>--}}
{{--                <option value="">Select</option>--}}
{{--                <?php foreach($account_list as $val): ?>--}}
{{--                <option value="{{ $val }}">{{ $val }}</option>--}}
{{--                <?php endforeach ?>--}}
{{--            </select>--}}
{{--        </div>--}}

        <div class="form-group">
            <label>Sales Channel:</label>
            <div class="columns" style="column-count: 2;">
                <?php foreach($account_list as $value): ?>
                <div class="col-md">
                    <div class="form-check" style="padding-left: 0px;">
                        <input  type="checkbox"
                                name="{{ $task_type }}_account[]"
                                value="{{ $value }}"
                        >
                        <label class="form-check-label " for="{{ $value }}">
                            {{ $value }}
                        </label>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="form-group specify_account" hidden>
            <label>If Others, Specify:</label>
            <input type="text" name="{{ $task_type }}_specify_account" class="form-control" value="">
        </div>

        <div class="form-group">
            <label>Additional Information:</label>
            {!! Form::textarea($task_type.'_additional_information', null, ['class' => 'form-control summernote']) !!}
        </div>

        <div class="form-group">
            <label>Attachments: <b style="color: #b91d19">(20MB Max)</b></label>
            <input type="file" data-asset="default" name="{{ $task_type }}_p_attachment[]" class="form-control p_attachment last_upload" multiple="multiple"/>
            <a href="javascript:void(0);" onclick="another_upload($(this))" class="another_upload">[ Upload Another ]</a>
        </div>

        <div class="form-group">
            <input type="submit" name="submit" value="Create" style="margin-top:10px; font-size: medium;" class="btn btn-lg btn-create submit"/>
        </div>

    </form>

</div>

<script type="text/javascript">
    $(function() {
        var today = new Date();
        if(today.getHours() >= 16){
            var count = 1; // past 4pm
        }else{
            var count = 0; // before 4pm
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
        $('input[id="<?php echo $task_type; ?>_due_date"]').daterangepicker({
            singleDatePicker: true,
            autoUpdateInput: false,
            minDate: today,
            locale: {
                format: 'YYYY-MM-DD'
            },
        }).on("apply.daterangepicker", function (e, picker) {
            picker.element.val(picker.startDate.format(picker.locale.format));
        });
    });



    function urgent_check(e){
        let priority = $(e).val();

        if(priority == 'Urgent') {
            $('.urgent_due_date').removeAttr('hidden');
        }else{
            $(".urgent_due_date").attr("hidden",true);
        }
    }

    function request_type(e){
        let val = $(e).val();
        if(val == 'Show Display') {
            $('.show-type').removeAttr('hidden');
        }else{
            $(".show-type").attr("hidden",true);
        }
    }

    function others_account(e){
        let val = $(e).val();
        if(val == 'Others') {
            $('.specify_account').removeAttr('hidden');
        }else{
            $(".specify_account").attr("hidden",true);
        }
    }

    function others_display_style(e){
        let val = $(e).val();
        if(val == 'Others') {
            $('.specify_display_style').removeAttr('hidden');
        }else{
            $(".specify_display_style").attr("hidden",true);
        }
    }

</script>
