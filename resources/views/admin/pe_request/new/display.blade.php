<?php $request_type = 'display'; ?>

<form method="POST" action="{{ route('pe_request.add_display') }}" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="{{ $request_type }}_t_id" value="{{ $task_id }}" />
    <input type="hidden" name="{{ $request_type }}_request_type" value="{{ $request_type }}" />
    <input type="hidden" name="{{ $request_type }}_author_id" value="{{ Auth::user()->id }}" />

    <div class="rendering">
        <p style="color: #b91d19; text-align: center; font-weight: bold;">Display</p>

        <div class="form-group">
            <label>Request Category: <b style="color: #b91d19">*</b> </label>
            <select class="form-control" name="{{ $request_type }}_request_category" onchange="request_cagetory($(this))" required>
                <option value="">Select</option>
                <?php foreach($request_category_list as $val): ?>
                <option value="{{ $val }}">{{ $val }}</option>
                <?php endforeach ?>
            </select>
        </div>

        <div class="form-group show-type" hidden>
            <label>Show Type:</label>
            <select class="form-control" name="{{ $request_type }}_show_type">
                <option value="">Select</option>
                <?php foreach($show_type_list as $val): ?>
                <option value="{{ $val }}">{{ $val }}</option>
                <?php endforeach ?>
            </select>
        </div>

        <div class="form-group show-type" hidden>
            <label>Show Location (City, Country):</label>
            <input type="text" name="{{ $request_type }}_show_location" class="form-control" value="">
        </div>

        <div class="form-group">
            <label>Product Category(s):</label>
            <div class="columns" style="column-count: 2;">
                <?php foreach($product_category_list as $value): ?>
                <div class="col-md">
                    <div class="form-check" style="padding-left: 0px;">
                        <input  type="checkbox"
                                name="{{ $request_type }}_product_category[]"
                                value="{{ $value }}" id="display_{{$value}}"
                        >
                        <label class="form-check-label" for="display_{{ $value }}">
                            {{ $value }}
                        </label>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="form-group">
            <label>Due Date: <b style="color: #b91d19">*</b> <b style="color: #b91d19">(Select from the calendar)</b></label>
            <input type="text" name="{{ $request_type }}_due_date" id="{{ $request_type }}_due_date" class="form-control" value="" required>
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
            <label>Display Style: <b style="color: #b91d19">*</b></label>
            <select class="form-control" name="{{ $request_type }}_display_style" onchange="others_display_style($(this))" required>
                <option value="">Select</option>
                <?php foreach($display_style_list as $val): ?>
                <option value="{{ $val }}">{{ $val }}</option>
                <?php endforeach ?>
            </select>
        </div>

        <div class="form-group specify_display_style" hidden>
            <label>If Retailer, Specify:</label>
            <input type="text" name="{{ $request_type }}_specify_display_style" class="form-control" value="">
        </div>

        <div class="form-group">
            <label>Display #: (Please ensure that Display# is set up correctly with "Item# + S".)</label>
            <input type="text" name="{{ $request_type }}_display" class="form-control" value="" >
        </div>

        <div class="form-group">
            <label>Total Display QTY #: <b style="color: #b91d19">*</b></label>
            <input type="text" name="{{ $request_type }}_total_display_qty" class="form-control" value="" required>
        </div>

        <div class="form-group">
            <label>Display Budget Per EA #: <b style="color: #b91d19">*</b></label>
            <input type="text" name="{{ $request_type }}_display_budget_per_ea" class="form-control" value="" required>
        </div>

        <div class="form-group">
            <label>Display Budget Code:</label>
            <input type="text" name="{{ $request_type }}_display_budget_code" class="form-control" value="" >
        </div>

        <div class="form-group">
            <label>Display Ready Date: <b style="color: #b91d19">*</b> <b style="color: #b91d19">(Select from the calendar)</b></label>
            <input type="text" name="{{ $request_type }}_display_ready_date" id="{{ $request_type }}_display_ready_date" class="form-control" value="" required>
        </div>

        <div class="form-group">
            <label>Sales Channel:</label>
            <div class="columns" style="column-count: 2;">
                <?php foreach($account_list as $value): ?>
                <div class="col-md">
                    <div class="form-check" style="padding-left: 0px;">
                        <input  type="checkbox"
                                name="{{ $request_type }}_account[]"
                                value="{{ $value }}" id="display_{{$value}}"
                        >
                        <label class="form-check-label " for="display_{{ $value }}">
                            {{ $value }}
                        </label>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="form-group specify_account">
            <label>If Others, Specify:</label>
            <input type="text" name="{{ $request_type }}_specify_account" class="form-control" value="">
        </div>

        <div class="form-group">
            <label>Additional Information:</label>
            {!! Form::textarea($request_type.'_additional_information', null, ['class' => 'form-control summernote']) !!}
        </div>


        <div class="form-group">
            <label>Attachment: <b style="color: #b91d19">(20MB Max)</b></label>
            <input type="file" data-asset="default" name="{{ $request_type }}_attachment[]" class="form-control p_attachment last_upload" multiple="multiple"/>
            <a href="javascript:void(0);" onclick="another_upload($(this))" class="another_upload">[ Upload Another ]</a>
        </div>
    </div>

    <div class="form-group">
        <input type="submit" name="submit" value="Create" style="margin-top:10px; padding: .55rem 1.5rem; font-size: medium;" class="btn btn-create submit"/>
    </div>

</form>

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
        $('input[id="<?php echo $request_type; ?>_due_date"]').daterangepicker({
            singleDatePicker: true,
            autoUpdateInput: false,
            minDate: today,
            locale: {
                format: 'YYYY-MM-DD'
            },
        }).on("apply.daterangepicker", function (e, picker) {
            picker.element.val(picker.startDate.format(picker.locale.format));
        });
        $('input[id="<?php echo $request_type; ?>_due_date"]').val('');

        $('input[id="<?php echo $request_type; ?>_display_ready_date"]').daterangepicker({
            singleDatePicker: true,
            autoUpdateInput: false,
            minDate: today,
            locale: {
                format: 'YYYY-MM-DD'
            },
        }).on("apply.daterangepicker", function (e, picker) {
            picker.element.val(picker.startDate.format(picker.locale.format));
        });
        $('input[id="<?php echo $request_type; ?>_display_ready_date"]').val('');
    });

    function request_cagetory(e){
        let val = $(e).val();
        if(val == 'Show Display') {
            $('.show-type').removeAttr('hidden');
        }else{
            $(".show-type").attr("hidden",true);
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


