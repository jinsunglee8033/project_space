<?php $request_type = 'us_launch'; ?>

<form method="POST" action="{{ route('ra_request.add_us_launch') }}" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="{{ $request_type }}_t_id" value="{{ $task_id }}" />
    <input type="hidden" name="{{ $request_type }}_request_type" value="{{ $request_type }}" />
    <input type="hidden" name="{{ $request_type }}_author_id" value="{{ Auth::user()->id }}" />

    <div class="us_launch">
        <p style="color: #b91d19; text-align: center; font-weight: bold;">US LAUNCH (WERCS, Smarter X, California, MoCRA)</p>
        <div class="form-group">
            <a href="https://drive.google.com/drive/folders/1kCsyOnjxxQ0Z1wu7rbBRDyfe9gpilTMQ?usp=drive_link" target="_blank" class="badge badge-danger">View Submission Guideline</a>
        </div>

        <div class="form-group">
            <label>Requested Due Date: <b style="color: #b91d19">*</b> <b style="color: #b91d19">(The request will take 5 business days to complete.)</b></label>
            <input type="text" name="{{ $request_type }}_due_date" required autocomplete="off"
                   class="form-control @error($request_type.'_due_date') is-invalid @enderror @if (!$errors->has($request_type.'_due_date') && old($request_type.'_due_date')) is-valid @endif"
                   value="{{ old($request_type . '_due_date', null) }}">
        </div>

        <div class="form-group">
            <label>Market: <b style="color: #b91d19">*</b> (WERCS or SmarterX registration will be sent to all retailers, unless specifically identified by Product Division)</label>
            <select class="form-control" name="{{ $request_type }}_market" required>
                <option value="">Select</option>
                <?php foreach($market_list as $val): ?>
                <option value="{{ $val }}">{{ $val }}</option>
                <?php endforeach ?>
            </select>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Primary Vendor Code: <b style="color: #b91d19">*</b> <b style="color: #b91d19">(Auto-Complete)</b></label>
                    <input type="text" name="{{ $request_type }}_bulk_vendor_code" class="form-control" id="{{ $request_type }}_bulk_vendor_auto" value="" required>
                </div>
            </div>
{{--            <div class="col-md-6">--}}
{{--                <div class="form-group">--}}
{{--                    <label>Primary Vendor Name: <b style="color: #b91d19">*</b> </label>--}}
{{--                    <input type="text" name="{{ $request_type }}_bulk_vendor_name" class="form-control" id="{{ $request_type }}_bulk_vendor_name" value="" readonly>--}}
{{--                </div>--}}
{{--            </div>--}}
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Filling Vendor Code: <b style="color: #b91d19">(Auto-Complete)</b></label>
                    <input type="text" name="{{ $request_type }}_filling_vendor_code" class="form-control" id="{{ $request_type }}_filling_vendor_auto" value="">
                </div>
            </div>
{{--            <div class="col-md-6">--}}
{{--                <div class="form-group">--}}
{{--                    <label>Filling Vendor Name:</label>--}}
{{--                    <input type="text" name="{{ $request_type }}_filling_vendor_name" class="form-control" id="{{ $request_type }}_filling_vendor_name" value="" readonly>--}}
{{--                </div>--}}
{{--            </div>--}}
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Packaging Vendor Code: <b style="color: #b91d19">(Auto-Complete)</b></label>
                    <input type="text" name="{{ $request_type }}_packaging_vendor_code" class="form-control" id="{{ $request_type }}_packaging_vendor_auto" value="">
                </div>
            </div>
{{--            <div class="col-md-6">--}}
{{--                <div class="form-group">--}}
{{--                    <label>Packaging Vendor Name:</label>--}}
{{--                    <input type="text" name="{{ $request_type }}_packaging_vendor_name" class="form-control" id="{{ $request_type }}_packaging_vendor_name" value="" readonly>--}}
{{--                </div>--}}
{{--            </div>--}}
        </div>

        <div class="form-group">
            <label><b style="color: #b91d19;">*</b> Upload Artwork: <b style="color: #b91d19">(20MB Max)</b></label>
            <input type="file" data-asset="default" name="{{ $request_type }}_attachment[]" class="form-control p_attachment last_upload" multiple="multiple" required/>
            <a href="javascript:void(0);" onclick="another_upload($(this))" class="another_upload">[ Upload Another ]</a>
        </div>
    </div>

    <div class="form-group">
        <input type="submit" name="submit" value="Create" style="margin-top:10px; padding: .55rem 1.5rem; font-size: medium;" class="btn btn-lg btn-create submit"/>
    </div>

</form>

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

        $('input[name="<?php echo $request_type; ?>_due_date"]').daterangepicker({
            singleDatePicker: true,
            minDate: today,
            locale: {
                format: 'YYYY-MM-DD'
            },
            isInvalidDate: function(date) {
                return (date.day() == 0 || date.day() == 6);
            },
        });
        $('input[name="<?php echo $request_type; ?>_due_date"]').val('');
    });

    $(document).ready(function(){
        $("#{{ $request_type }}_bulk_vendor_auto").autocomplete({
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
                    $('#{{ $request_type }}_bulk_vendor_name').val(data.name);
                }
            }
        });

        $("#{{ $request_type }}_filling_vendor_auto").autocomplete({
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
                    $('#{{ $request_type }}_filling_vendor_name').val(data.name);
                }
            }
        });

        $("#{{ $request_type }}_packaging_vendor_auto").autocomplete({
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
                    $('#{{ $request_type }}_packaging_vendor_name').val(data.name);
                }
            }
        });

    });


</script>

