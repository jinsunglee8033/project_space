<?php $task_type = 'qc_request'; ?>

<form method="POST" action="{{ route('qc_request.add_qc_request') }}" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="{{ $task_type }}_p_id" value="{{ $project->id }}" />
    <input type="hidden" name="{{ $task_type }}_task_type" value="{{ $task_type }}" />
    <input type="hidden" name="{{ $task_type }}_author_id" value="{{ Auth::user()->id }}" />

{{--    <div class="form-group">--}}
{{--        <label class="form-label"></label>--}}
{{--        <h5 style="text-align: center;">Please Click the "Create" button to initiate a <b style="color: #b91d19;">Onsite QC Request</b></h5>--}}
{{--    </div>--}}

{{--    <div class="form-group">--}}
{{--        <input type="submit" name="submit" value="Create" style="margin-top:10px;" class="btn btn-primary submit"/>--}}
{{--    </div>--}}
    <h5 style="padding: 12px 0 12px 12px; color: black;">Please choose the request type and provide the necessary details.</h5>
    <div class="form-group">
        <label>Request Type: <b style="color: #b91d19">*</b></label>
        <select class="form-control" name="{{ $task_type }}_work_type" required>
            <option value="">Select</option>
            <?php foreach($work_type_list as $val): ?>
            <option value="{{ $val }}">{{ $val }}</option>
            <?php endforeach ?>
        </select>
    </div>

    <div class="form-group">
        <label>Ship Date (ETD): <b style="color: #b91d19">*</b> <b style="color: #b91d19">(Select from the calendar)</b></label>
        <input required type="text" name="{{ $task_type }}_ship_date" id="ship_date" autocomplete="off"
               class="form-control @error($task_type.'_ship_date') is-invalid @enderror @if (!$errors->has($task_type.'_ship_date') && old($task_type.'_ship_date')) is-valid @endif"
               value="{{ old($task_type . '_ship_date', null) }}">
    </div>

    <div class="form-group">
        <label>QC Date (Requested): <b style="color: #b91d19">*</b> <b style="color: #b91d19">(Select from the calendar)</b></label>
        <input required type="text" name="{{ $task_type }}_qc_date" id="qc_date" autocomplete="off"
               class="form-control @error($task_type.'_qc_date') is-invalid @enderror @if (!$errors->has($task_type.'_qc_date') && old($task_type.'_qc_date')) is-valid @endif"
               value="{{ old($task_type . '_qc_date', null) }}">
    </div>

    <div class="form-group">
        <label>PO# <b style="color: #b91d19">*</b> (If PO is not released, fill in 'TBD'):</label>
        <input type="text" name="{{ $task_type }}_po" class="form-control" value="" required>
    </div>

    <div class="form-group">
        <label>PO (USD): <b style="color: #b91d19">*</b></label>
        <input type="text" name="{{ $task_type }}_po_usd" class="form-control" value="" required>
    </div>

    <div class="form-group">
        <label>Materials# <b style="color: #b91d19">*</b> (Item code, SKU):</label>
        <input type="text" name="{{ $task_type }}_materials" class="form-control" value="" required>
    </div>

    <div class="form-group">
        <label>Item Type: <b style="color: #b91d19">*</b> <b style="color: #b91d19">(Auto-Complete)</b></label>
        <input type="text" name="{{ $task_type }}_item_type" id="product_category_auto" class="form-control" value="" required>
    </div>

    <div class="form-group">
        <label>Vendor Code: <b style="color: #b91d19">*</b> <b style="color: #b91d19">(Auto-Complete)</b></label>
        <input type="text" name="{{ $task_type }}_vendor_code" class="form-control" id="vendor_auto" required>
    </div>

    <div class="form-group">
        <label>Vendor Name: </label>
        <input type="text" name="{{ $task_type }}_vendor_name" class="form-control" id="vendor_name" value="" readonly>
    </div>

    <div class="form-group">
        <label>Country: <b style="color: #b91d19">*</b></label>
        <input type="text" name="{{ $task_type }}_country" class="form-control" value="" required>
    </div>

    <div class="form-group">
        <label>Vendor Primary Contact (Name): <b style="color: #b91d19">*</b></label>
        <input type="text" name="{{ $task_type }}_vendor_primary_contact_name" class="form-control" value="" required>
    </div>

    <div class="form-group">
        <label>Vendor Primary Contact (E-mail): <b style="color: #b91d19">*</b></label>
        <input type="text" name="{{ $task_type }}_vendor_primary_contact_email" class="form-control" value="" required>
    </div>

    <div class="form-group">
        <label>Vendor Primary Contact (Phone): <b style="color: #b91d19">*</b></label>
        <input type="text" name="{{ $task_type }}_vendor_primary_contact_phone" class="form-control" value="" required>
    </div>

    <div class="form-group">
        <label>Facility Address: <b style="color: #b91d19">*</b></label>
        <input type="text" name="{{ $task_type }}_facility_address" class="form-control" value="" required>
    </div>

    <div class="form-group">
        <label>Attachments: <b style="color: #b91d19">(Click for icon to download the template)</b>
            <br>
            <img title="COT_Electric Device_R2024"
                 data-file-date="COT_Electric Device_R2024"
                 src="/storage/doc.png"
                 onclick="open_download('/storage/COT_Electric Device_R2024.docx')"
                 class="thumbnail"/>
            COT_Electric Device_R2024
            <img title="COT_General_R2024"
                 data-file-date="COT_Electric Device_R2024"
                 src="/storage/xls.png"
                 onclick="open_download('/storage/COT_General_R2024.xlsx')"
                 class="thumbnail"/>
            COT_General_R2024
        </label>
        <input type="file" data-asset="default" name="{{ $task_type }}_p_attachment[]" class="form-control p_attachment last_upload" multiple="multiple"/>
        <a href="javascript:void(0);" onclick="another_upload($(this))" class="another_upload">[ Upload Another ]</a>
    </div>

    <div class="form-group">
        <input type="submit" name="submit" value="Create" style="margin-top:10px; font-size: medium;" class="btn btn-lg btn-create submit"/>
    </div>

</form>

<script type="text/javascript">
    $(function() {
        var today = new Date();
        $('input[name="<?php echo $task_type; ?>_ship_date"]').daterangepicker({
            singleDatePicker: true,
            minDate: today,
            locale: {
                format: 'YYYY-MM-DD'
            },
        });
        $('input[name="<?php echo $task_type; ?>_qc_date"]').daterangepicker({
            singleDatePicker: true,
            minDate: today,
            locale: {
                format: 'YYYY-MM-DD'
            },
        });
        $('input[name="<?php echo $task_type; ?>_ship_date"]').val('');
        $('input[name="<?php echo $task_type; ?>_qc_date"]').val('');
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
    });
    $(document).ready(function(){
        $("#product_category_auto").autocomplete({
            source: function (request, cb){
                $.ajax({
                    url: "<?php echo url('/admin/project/autocomplete_product_category'); ?>"+"?product_category="+request.term,
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
                                    label: obj.name,
                                    value: obj.name,
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
                    $('#{{$task_type}}_product_category').val(data.name);
                }
            }
        });
    });

</script>
