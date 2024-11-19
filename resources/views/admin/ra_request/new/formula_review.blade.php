<?php $request_type = 'formula_review'; ?>

<form method="POST" action="{{ route('ra_request.add_formula_review') }}" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="{{ $request_type }}_t_id" value="{{ $task_id }}" />
    <input type="hidden" name="{{ $request_type }}_request_type" value="{{ $request_type }}" />
    <input type="hidden" name="{{ $request_type }}_author_id" value="{{ Auth::user()->id }}" />

    <div class="formula_review">
        <p style="color: #b91d19; text-align: center; font-weight: bold;">FORMULA REVIEW</p>
        <div class="form-group">
            <a href="https://drive.google.com/drive/folders/1kCsyOnjxxQ0Z1wu7rbBRDyfe9gpilTMQ?usp=drive_link" target="_blank" class="badge badge-danger">View Submission Guideline</a>
        </div>

        <div class="form-group">
            <label>Requested Due Date: <b style="color: #b91d19">*</b></label>
            <input type="text" name="{{ $request_type }}_due_date" required autocomplete="off"
                   class="form-control @error($request_type.'_due_date') is-invalid @enderror @if (!$errors->has($request_type.'_due_date') && old($request_type.'_due_date')) is-valid @endif"
                   value="{{ old($request_type . '_due_date', null) }}">
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Vendor Code: <b style="color: #b91d19">*</b> <b style="color: #b91d19">(Auto-Complete)</b></label>
                    <input type="text" name="{{ $request_type }}_vendor_code" class="form-control" id="{{ $request_type }}_vendor_auto" value="" required>
                </div>
            </div>
{{--            <div class="col-md-6">--}}
{{--                <div class="form-group">--}}
{{--                    <label>Vendor Name: <b style="color: #b91d19">*</b></label>--}}
{{--                    <input type="text" name="{{ $request_type }}_vendor_name" class="form-control" id="{{ $request_type }}_vendor_name" value="" readonly>--}}
{{--                </div>--}}
{{--            </div>--}}
        </div>

        <div class="form-group">
            <label>Product Type: <b style="color: #b91d19">*</b></label>
            <select class="form-control" name="{{ $request_type }}_product_type" required>
                <option>Select</option>
                <?php foreach($product_type_list as $val): ?>
                <option value="{{ $val }}">{{ $val }}</option>
                <?php endforeach ?>
            </select>
        </div>

        <div class="form-group">
            <label>Product Form: <b style="color: #b91d19">*</b></label>
            <select class="form-control" name="{{ $request_type }}_product_form" onchange="others_product_form($(this))" required>
                <option>Select</option>
                <?php foreach($product_form_list as $val): ?>
                <option value="{{ $val }}">{{ $val }}</option>
                <?php endforeach ?>
            </select>
        </div>

        <div class="form-group if_other_product_form" hidden>
            <label>If Others, please specify:</label>
            <input type="text"
                   name="{{ $request_type }}_if_other_product_form"
                   id="{{ $request_type }}_if_other_product_form"
                   class="form-control" value="">
        </div>

        <div class="form-group">
            <label>Area of Application:</label>
            <div class="columns" style="column-count: 4;">
                <?php foreach($area_of_application_list as $value): ?>
                <div class="col-md">
                    <div class="form-check" style="padding-left: 0px;">
                        <input  type="checkbox"
                                name="{{ $request_type }}_area_of_application[]"
                                value="{{ $value }}" id="formula_{{$value}}"
                        >
                        <label class="form-check-label " for="formula_{{ $value }}">
                            {{ $value }}
                        </label>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="form-group if_other_area_of_application" >
            <label>If Others, please specify:</label>
            <input type="text"
                   name="{{ $request_type }}_if_other_area_of_application"
                   id="{{ $request_type }}_if_other_area_of_application"
                   class="form-control" value="">
        </div>

        <div class="form-group">
            <label>Fragrance, Flavor, or Essential Oils? : </label>
            <input type="checkbox" name="{{ $request_type }}_fragrance" class="form-control" style="margin: -28px 0px 0px 0px;" onclick="show_msg(this)">
        </div>


        <div class="form-group">
            <label>Attachment: <b style="color: #b91d19">(The file must be in PDF or .xlsx format / 20MB Max)</b></label>
            <input type="file" data-asset="default" name="{{ $request_type }}_attachment[]" class="form-control p_attachment last_upload" multiple="multiple"/>
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
        $('input[name="<?php echo $request_type; ?>_due_date"]').val(today.toISOString().split('T')[0]);
    });

    $(document).ready(function(){
        $("#{{ $request_type }}_vendor_auto").autocomplete({
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
                    $('#{{ $request_type }}_vendor_name').val(data.name);
                }
            }
        });
    });

    function others_product_form(e){
        let val = $(e).val();

        if(val == 'Others') {
            $('.if_other_product_form').removeAttr('hidden');
        }else{
            $('#{{$request_type}}_if_other_product_form').val('');
            $(".if_other_product_form").attr("hidden",true);
        }
    }

    function others_area_of_application(e){
        let val = $(e).val();
        if(val == 'Others') {
            $('.if_other_area_of_application').removeAttr('hidden');
        }else{
            $('#{{$request_type}}_if_other_area_of_application').val('');
            $(".if_other_area_of_application").attr("hidden",true);
        }
    }

    function show_msg(aCheckBox) {
        if (aCheckBox.checked) {
            alert("Please upload the corresponding IFRA Certificate, Allergen Declaration, and EU SDS using the 'Attachment' field below.");
        } else {
            alert("Please upload the corresponding IFRA Certificate, Allergen Declaration, and EU SDS using the 'Attachment' field below.");
        }
    }

</script>