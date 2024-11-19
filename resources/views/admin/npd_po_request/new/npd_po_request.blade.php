<?php $task_type = 'npd_po_request'; ?>

<div class="inner_box" style="margin: 30px 15px 0px 20px;">

    <form method="POST" action="{{ route('npd_po_request.add_npd_po_request') }}" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="{{ $task_type }}_p_id" value="{{ $project->id }}" />
    <input type="hidden" name="{{ $task_type }}_task_type" value="{{ $task_type }}" />
    <input type="hidden" name="{{ $task_type }}_author_id" value="{{ Auth::user()->id }}" />

    <h5 style="color: #b91d19; padding: 0 0 25px 0;">Please complete the information for the NPD PO Request.</h5>

        <div class="form-group">
            <label>Request Detail:</label>
            <textarea class="form-control" name="{{ $task_type }}_request_detail" style="height:100px;"></textarea>
        </div>

        <div class="form-group">
            <label>Priority:</label>
            <select class="form-control" name="{{ $task_type }}_priority" onchange="urgent_check($(this))">
                <?php foreach($priorities as $val): ?>
                <option value="{{ $val }}">{{ $val }}</option>
                <?php endforeach ?>
            </select>
        </div>

        <div class="form-group urgent_due_date" hidden>
            <label>Due Date (Urgent): <b style="color: #b91d19">(Select from the calendar)</b></label>
            <input type="text" name="{{ $task_type }}_due_date_urgent" id="due_date"
                   class="form-control"
                   value="">
        </div>

        <div class="form-group urgent_due_date" hidden>
            <label>Urgent Reason:</label>
            <textarea class="form-control" name="{{ $task_type }}_urgent_reason" style="height:100px;"></textarea>
        </div>

        <div class="form-group">
            <label>Due Date: <b style="color: #b91d19">*</b></label>
            <input type="text" name="{{ $task_type }}_due_date" class="form-control" value="" required>
        </div>

        <div class="form-group">
            <label>Source List Completion: <b style="color: #b91d19">*</b></label>
            <select class="form-control" name="{{ $task_type }}_source_list_completion" required>
                <option value="">Select</option>
                <?php foreach($yes_or_no_list as $val): ?>
                <option value="{{ $val }}">{{ $val }}</option>
                <?php endforeach ?>
            </select>
        </div>

        <div class="form-group">
            <label>Info Record Completion: <b style="color: #b91d19">*</b></label>
            <select class="form-control" name="{{ $task_type }}_info_record_completion" required>
                <option value="">Select</option>
                <?php foreach($yes_or_no_list as $val): ?>
                <option value="{{ $val }}">{{ $val }}</option>
                <?php endforeach ?>
            </select>
        </div>

        <div class="form-group">
            <label>Price Set Up: <b style="color: #b91d19">*</b></label>
            <select class="form-control" name="{{ $task_type }}_price_set_up" required>
                <option value="">Select</option>
                <?php foreach($price_set_up_list as $val): ?>
                <option value="{{ $val }}">{{ $val }}</option>
                <?php endforeach ?>
            </select>
        </div>

        <div class="form-group">
            <label>Forecast Completion: <b style="color: #b91d19">*</b></label>
            <select class="form-control" name="{{ $task_type }}_forecast_completion" required>
                <option value="">Select</option>
                <?php foreach($yes_or_no_list as $val): ?>
                <option value="{{ $val }}">{{ $val }}</option>
                <?php endforeach ?>
            </select>
        </div>

        <div class="form-group">
            <label>Materials: <b style="color: #b91d19">*</b></label>
            <textarea class="form-control" name="{{ $task_type }}_materials" style="height:200px;" required></textarea>
        </div>

        <div class="form-group">
            <label>Total SKU Count: <b style="color: #b91d19">*</b></label>
            <input type="text" name="{{ $task_type }}_total_sku_count" class="form-control" value="" required>
        </div>

        <div class="form-group">
            <label>Set-up Plant(s):</label>
            <div class="columns" style="column-count: 3;">
                <?php foreach($set_up_plants_list as $value): ?>
                <div class="col-md">
                    <div class="form-check" style="padding-left: 0px;">
                        <input  type="checkbox"
                                name="{{ $task_type }}_set_up_plant[]"
                                value="{{ $value->name }}" id="new_{{$value->name}}"
                        >
                        <label class="form-check-label " for="new_{{ $value->name }}">
                            {{ $value->name }}
                        </label>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Vendor Code: <b style="color: #b91d19">*</b> <b style="color: #b91d19">(Auto-Complete)</b></label>
                    <input type="text" name="{{ $task_type }}_vendor_code" id="vendor_auto" class="form-control" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Vendor Name: <b style="color: #b91d19">*</b> </label>
                    <input type="text" name="{{ $task_type }}_vendor_name" id="vendor_name" class="form-control" value="" readonly>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Second Vendor Code: <b style="color: #b91d19">(Auto-Complete)</b></label>
                    <input type="text" name="{{ $task_type }}_second_vendor_code" id="second_vendor_auto" class="form-control">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Second Vendor Name:</label>
                    <input type="text" name="{{ $task_type }}_second_vendor_name" id="second_vendor_name" class="form-control" value="" readonly>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label>Est. Ready Date: <b style="color: #b91d19">* (Select from the calendar)</b></label>
            <input type="text" name="{{ $task_type }}_est_ready_date" id="est_ready_date" autocomplete="off"
                   class="form-control @error($task_type.'_est_ready_date') is-invalid @enderror @if (!$errors->has($task_type.'_est_ready_date') && old($task_type.'_est_ready_date')) is-valid @endif"
                   value="{{ old($task_type . '_est_ready_date', null) }}" required>
        </div>

        <div class="form-group">
            <label>Buyer: <b style="color: #b91d19">*</b></label>
            <select class="form-control" name="{{ $task_type }}_buyer" required>
                <option value="">Select</option>
                <?php foreach($po_buyer_list as $val): ?>
                <option value="{{ $val->id }}">{{ $val->first_name }} {{ $val->last_name }}</option>
                <?php endforeach ?>
            </select>
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
        $('input[name="<?php echo $task_type; ?>_due_date"]').val(today.toISOString().split('T')[0]);

        var today_urgent = new Date();
        $('input[name="<?php echo $task_type; ?>_due_date_urgent"]').daterangepicker({
            singleDatePicker: true,
            minDate: today_urgent,
            locale: {
                format: 'YYYY-MM-DD'
            },
        });

        var today_est_ready_date = new Date();
        $('input[name="<?php echo $task_type; ?>_est_ready_date"]').daterangepicker({
            singleDatePicker: true,
            minDate: today_est_ready_date,
            locale: {
                format: 'YYYY-MM-DD'
            },
        });
        $('input[name="<?php echo $task_type; ?>_est_ready_date"]').val('');
    });

    $(document).ready(function(){
        $("#vendor_auto").autocomplete({
            source: function (request, cb){
                $.ajax({
                    url: "<?php echo url('/admin/project/autocomplete_vendor'); ?>"+"?code="+request.term,
                    method: 'GET',
                    dataType: 'json',
                    success: function(res){
                        var result;
                        result = [
                            {
                                label : 'There is no matching record found for '+request.term,
                                value : ''
                            }
                        ];
                        // console.log(res);
                        if(res.length) {
                            result = $.map(res, function(obj){
                                return {
                                    label: obj.code,
                                    value: obj.code,
                                    data : obj
                                };
                            });
                        }
                        cb(result);
                    }
                });
            },
            select:function(e,selectedData) {
                console.log(selectedData);
                if(selectedData && selectedData.item && selectedData.item.data){
                    var data = selectedData.item.data;
                    $('#vendor_name').val(data.name);
                }
            }
        });

        $("#second_vendor_auto").autocomplete({
            source: function (request, cb){
                $.ajax({
                    url: "<?php echo url('/admin/project/autocomplete_vendor'); ?>"+"?code="+request.term,
                    method: 'GET',
                    dataType: 'json',
                    success: function(res){
                        var result;
                        result = [
                            {
                                label : 'There is no matching record found for '+request.term,
                                value : ''
                            }
                        ];
                        // console.log(res);
                        if(res.length) {
                            result = $.map(res, function(obj){
                                return {
                                    label: obj.code,
                                    value: obj.code,
                                    data : obj
                                };
                            });
                        }
                        cb(result);
                    }
                });
            },
            select:function(e,selectedData) {
                console.log(selectedData);
                if(selectedData && selectedData.item && selectedData.item.data){
                    var data = selectedData.item.data;
                    $('#second_vendor_name').val(data.name);
                }
            }
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

</script>
