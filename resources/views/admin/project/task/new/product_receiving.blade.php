<?php $task_type = 'product_receiving'; ?>

    <div class="form-group">
        <label class="form-label"></label>
        <h5 style="text-align: center;">Please Click the "Create" button to initiate a <b style="color: #b91d19;">Product Receiving</b></h5>
    </div>

    <div class="form-group">
        {{--        <input type="submit" name="submit" value="Create Task" style="margin-top:10px;" class="btn btn-primary submit"/>--}}

        <a href="{{ url('admin/product_receiving/'.$project->id.'/edit') }}">
            <button type="button" class="btn btn-primary submit" style="margin-top:10px; padding: .55rem 1.5rem; font-size: medium;">Create</button>
        </a>

    </div>

{{--    <div class="form-group">--}}
{{--        <label>PO# :</label>--}}
{{--        <input type="text" name="{{ $task_type }}_po" class="form-control" value="" >--}}
{{--    </div>--}}

{{--    <div class="form-group">--}}
{{--        <label>Materials# :</label>--}}
{{--        <input type="text" name="{{ $task_type }}_materials" class="form-control" value="" >--}}
{{--    </div>--}}

{{--    <div class="form-group">--}}
{{--        <label>Posting Date :</label>--}}
{{--        <input  type="text" name="{{ $task_type }}_posting_date" id="posting_date" autocomplete="off"--}}
{{--               class="form-control @error($task_type.'_posting_date') is-invalid @enderror @if (!$errors->has($task_type.'_posting_date') && old($task_type.'_posting_date')) is-valid @endif"--}}
{{--               value="{{ old($task_type . '_posting_date', null) }}">--}}
{{--    </div>--}}

{{--    <div class="form-group">--}}
{{--        <label>QIR Status:</label>--}}
{{--        <select class="form-control" name="{{ $task_type }}_qir_status">--}}
{{--            <option value="">Select</option>--}}
{{--            <?php foreach($qir_statuses as $val): ?>--}}
{{--            <option value="{{ $val }}">{{ $val }}</option>--}}
{{--            <?php endforeach ?>--}}
{{--        </select>--}}
{{--    </div>--}}

{{--    <div class="form-group">--}}
{{--        <label>Division:</label>--}}
{{--        <select class="form-control" name="{{ $task_type }}_division">--}}
{{--            <option value="">Select</option>--}}
{{--            <?php foreach($divisions as $val): ?>--}}
{{--            <option value="{{ $val }}">{{ $val }}</option>--}}
{{--            <?php endforeach ?>--}}
{{--        </select>--}}
{{--    </div>--}}

{{--    <div class="form-group">--}}
{{--        <label>QIR Action:</label>--}}
{{--        <select class="form-control" name="{{ $task_type }}_qir_action">--}}
{{--            <option value="">Select</option>--}}
{{--            <?php foreach($qir_actions as $val): ?>--}}
{{--            <option value="{{ $val }}">{{ $val }}</option>--}}
{{--            <?php endforeach ?>--}}
{{--        </select>--}}
{{--    </div>--}}

{{--    <div class="form-group">--}}
{{--        <label>Vendor Code:</label>--}}
{{--        <input type="text" name="{{ $task_type }}_vendor_code" class="form-control" id="vendor_auto">--}}
{{--    </div>--}}

{{--    <div class="form-group">--}}
{{--        <label>Vendor Name:</label>--}}
{{--        <input type="text" name="{{ $task_type }}_vendor_name" class="form-control" id="vendor_name" value="" readonly>--}}
{{--    </div>--}}

{{--    <div class="form-group">--}}
{{--        <label>Cost Center:</label>--}}
{{--        <select class="form-control" name="{{ $task_type }}_cost_center">--}}
{{--            <option value="">Select</option>--}}
{{--            <?php foreach($cost_center_list as $val): ?>--}}
{{--            <option value="{{ $val }}">{{ $val }}</option>--}}
{{--            <?php endforeach ?>--}}
{{--        </select>--}}
{{--    </div>--}}

{{--    <div class="form-group">--}}
{{--        <label>Location:</label>--}}
{{--        <select class="form-control" name="{{ $task_type }}_location">--}}
{{--            <option value="">Select</option>--}}
{{--            <?php foreach($locations as $val): ?>--}}
{{--            <option value="{{ $val }}">{{ $val }}</option>--}}
{{--            <?php endforeach ?>--}}
{{--        </select>--}}
{{--    </div>--}}

{{--    <div class="form-group">--}}
{{--        <label>Primary Contact:</label>--}}
{{--        <input type="text" name="{{ $task_type }}_primary_contact" class="form-control" value="" >--}}
{{--    </div>--}}

{{--    <div class="form-group">--}}
{{--        <label>Related Team Contact:</label>--}}
{{--        <input type="text" name="{{ $task_type }}_related_team_contact" class="form-control" value="" >--}}
{{--    </div>--}}

{{--    <div class="form-group">--}}
{{--        <label>Year:</label>--}}
{{--        <input type="text" name="{{ $task_type }}_year" class="form-control" value="" >--}}
{{--    </div>--}}

{{--    <div class="form-group">--}}
{{--        <label>Received QTY:</label>--}}
{{--        <input type="text" name="{{ $task_type }}_received_qty" class="form-control" value="" >--}}
{{--    </div>--}}

{{--    <div class="form-group">--}}
{{--        <label>Inspection QTY:</label>--}}
{{--        <input type="text" name="{{ $task_type }}_inspection_qty" id="inspection_qty" onkeyup="blocked_rate_cal();" class="form-control" value="" >--}}
{{--    </div>--}}

{{--    <div class="form-group">--}}
{{--        <label>Defect QTY:</label>--}}
{{--        <input type="text" name="{{ $task_type }}_defect_qty" id="defect_qty" onkeyup="blocked_rate_cal();" class="form-control" value="" >--}}
{{--    </div>--}}

{{--    <div class="form-group">--}}
{{--        <label>Blocked Rate: (Defect QTY / Inspection QTY %)</label>--}}
{{--        <input type="text" readonly name="{{ $task_type }}_block_rate" id="blocked_rate" class="form-control" value="">--}}
{{--        <input type="hidden"--}}
{{--    </div>--}}

{{--    <div class="form-group">--}}
{{--        <label>Block QTY:</label>--}}
{{--        <input type="text" name="{{ $task_type }}_block_qty" class="form-control" value="" >--}}
{{--    </div>--}}

{{--    <div class="form-group">--}}
{{--        <label>Item Net Cost ($):</label>--}}
{{--        <input type="text" name="{{ $task_type }}_item_net_cost" id="item_net_cost" onkeyup="defect_cost_cal();" class="form-control" value="" >--}}
{{--    </div>--}}

{{--    <div class="form-group">--}}
{{--        <label>Defect Cost ($): (Defect QTY * Item Next Cost)</label>--}}
{{--        <input type="text" readonly name="{{ $task_type }}_defect_cost" id="defect_cost" class="form-control" value="" >--}}
{{--    </div>--}}

{{--    <div class="form-group">--}}
{{--        <label>Full Cost ($): </label>--}}
{{--        <input type="text" name="{{ $task_type }}_full_cost" id="full_cost" onkeyup="total_claim_cal();" class="form-control" value="" >--}}
{{--    </div>--}}

{{--    <div class="form-group">--}}
{{--        <label>Rework Cost ($): </label>--}}
{{--        <input type="text" name="{{ $task_type }}_rework_cost" id="rework_cost" onkeyup="total_claim_cal();" class="form-control" value="" >--}}
{{--    </div>--}}

{{--    <div class="form-group">--}}
{{--        <label>Special Inspection Cost ($): </label>--}}
{{--        <input type="text" name="{{ $task_type }}_special_inspection_cost" id="special_inspection_cost" onkeyup="total_claim_cal();" class="form-control" value="" >--}}
{{--    </div>--}}

{{--    <div class="form-group">--}}
{{--        <label>Total Claim ($): ( Defect Cost + Full Cost + Rework Cost + Special Inspection Cost ) </label>--}}
{{--        <input type="text" readonly name="{{ $task_type }}_total_claim" id="total_claim" class="form-control" value="" >--}}
{{--    </div>--}}

{{--    <div class="form-group">--}}
{{--        <label>Batch #:</label>--}}
{{--        <input type="text" name="{{ $task_type }}_batch" class="form-control" value="" >--}}
{{--    </div>--}}

{{--    <div class="form-group">--}}
{{--        <label>Defect Area:</label>--}}
{{--        <div class="columns" style="column-count: 3;">--}}
{{--            <?php foreach($defect_areas as $value): ?>--}}
{{--            <div class="col-md">--}}
{{--                <div class="form-check" style="padding-left: 0px;">--}}
{{--                    <input  type="checkbox"--}}
{{--                            name="{{ $task_type }}_defect_areas[]"--}}
{{--                            value="{{ $value }}"--}}
{{--                    >--}}
{{--                    <label class="form-check-label " for="{{ $value }}">--}}
{{--                        {{ $value }}--}}
{{--                    </label>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--            <?php endforeach; ?>--}}
{{--        </div>--}}
{{--    </div>--}}

{{--    <div class="form-group">--}}
{{--        <label>Defect Type:</label>--}}
{{--        <div class="columns" style="column-count: 3;">--}}
{{--            <?php foreach($defect_types as $value): ?>--}}
{{--            <div class="col-md">--}}
{{--                <div class="form-check" style="padding-left: 0px;">--}}
{{--                    <input  type="checkbox"--}}
{{--                            name="{{ $task_type }}_defect_type[]"--}}
{{--                            value="{{ $value }}"--}}
{{--                    >--}}
{{--                    <label class="form-check-label " for="{{ $value }}">--}}
{{--                        {{ $value }}--}}
{{--                    </label>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--            <?php endforeach; ?>--}}
{{--        </div>--}}
{{--    </div>--}}

{{--    <div class="form-group">--}}
{{--        <label>Defect Details:</label>--}}
{{--        {!! Form::textarea($task_type.'_defect_details', null, ['class' => 'form-control summernote']) !!}--}}
{{--    </div>--}}

{{--    <div class="form-group">--}}
{{--        <label>RDS ID: </label>--}}
{{--        <input type="text" name="{{ $task_type }}_rds_id" class="form-control" value="" >--}}
{{--    </div>--}}

{{--    <div class="form-group">--}}
{{--        <label>Processing Date :</label>--}}
{{--        <input  type="text" name="{{ $task_type }}_processing_date" id="processing_date" autocomplete="off"--}}
{{--                class="form-control @error($task_type.'_processing_date') is-invalid @enderror @if (!$errors->has($task_type.'_processing_date') && old($task_type.'_processing_date')) is-valid @endif"--}}
{{--                value="{{ old($task_type . '_processing_date', null) }}">--}}
{{--    </div>--}}

{{--    <div class="form-group">--}}
{{--        <label>Aging Days: </label>--}}
{{--        <input type="text" name="{{ $task_type }}_aging_days" class="form-control" value="" >--}}
{{--    </div>--}}

{{--    <div class="form-group">--}}
{{--        <label>CAPA : </label>--}}
{{--        <input type="checkbox" name="{{ $task_type }}_capa" class="form-control" style="width: 18%; margin: -28px 0px 0px 10px;">--}}
{{--    </div>--}}

{{--    <div class="form-group">--}}
{{--        <label>Actual CM Total: </label>--}}
{{--        <input type="text" name="{{ $task_type }}_actual_cm_total" class="form-control" value="" >--}}
{{--    </div>--}}

{{--    <div class="form-group">--}}
{{--        <label>Claim Status:</label>--}}
{{--        <select class="form-control" name="{{ $task_type }}_claim_status">--}}
{{--            <option value="">Select</option>--}}
{{--            <?php foreach($claim_statuses as $val): ?>--}}
{{--            <option value="{{ $val }}">{{ $val }}</option>--}}
{{--            <?php endforeach ?>--}}
{{--        </select>--}}
{{--    </div>--}}

{{--    <div class="form-group">--}}
{{--        <label>Override Authorized By:</label>--}}
{{--        <select class="form-control" name="{{ $task_type }}_override_authorized_by">--}}
{{--            <option value="">Select</option>--}}
{{--            <?php foreach($override_authorized_by_list as $val): ?>--}}
{{--            <option value="{{ $val }}">{{ $val }}</option>--}}
{{--            <?php endforeach ?>--}}
{{--        </select>--}}
{{--    </div>--}}

{{--    <div class="form-group">--}}
{{--        <label>Waived Amount ($): </label>--}}
{{--        <input type="text" name="{{ $task_type }}_waived_amount" class="form-control" value="" >--}}
{{--    </div>--}}

{{--    <div class="form-group">--}}
{{--        <label>Settlement Total ($): </label>--}}
{{--        <input type="text" name="{{ $task_type }}_settlement_total" class="form-control" value="" >--}}
{{--    </div>--}}

{{--    <div class="form-group">--}}
{{--        <label>Settlement Type:</label>--}}
{{--        <select class="form-control" name="{{ $task_type }}_settlement_type">--}}
{{--            <option value="">Select</option>--}}
{{--            <?php foreach($settlement_type_list as $val): ?>--}}
{{--            <option value="{{ $val }}">{{ $val }}</option>--}}
{{--            <?php endforeach ?>--}}
{{--        </select>--}}
{{--    </div>--}}

{{--    <div class="form-group">--}}
{{--        <label>Defect Photo / Report: <b style="color: #b91d19">(20MB Max)</b></label>--}}
{{--        <input type="file" data-asset="default" name="{{ $task_type }}_p_attachment[]" class="form-control p_attachment last_upload" multiple="multiple"/>--}}
{{--        <a href="javascript:void(0);" onclick="another_upload($(this))" class="another_upload">[ Upload Another ]</a>--}}
{{--    </div>--}}

{{--    <div class="form-group">--}}
{{--        <input type="submit" name="submit" value="Create Task" style="margin-top:10px;" class="btn btn-primary submit"/>--}}
{{--    </div>--}}

</form>

<script type="text/javascript">
    $(function() {
        var today = new Date();
        $('input[name="<?php echo $task_type; ?>_posting_date"]').daterangepicker({
            singleDatePicker: true,
            minDate: today,
            locale: {
                format: 'YYYY-MM-DD'
            },
        });
        $('input[name="<?php echo $task_type; ?>_processing_date"]').daterangepicker({
            singleDatePicker: true,
            minDate: today,
            locale: {
                format: 'YYYY-MM-DD'
            },
        });
        $('input[name="<?php echo $task_type; ?>_posting_date"]').val('');
        $('input[name="<?php echo $task_type; ?>_processing_date"]').val('');
    });
</script>

<script>
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

    function blocked_rate_cal(){
        var inspection_qty = document.getElementById('inspection_qty').value;
        if(inspection_qty == 0){
            alert("You can not input 0 in Inspection QTY");
            return;
        }
        var defect_qty = document.getElementById('defect_qty').value;
        var blocked_rate = defect_qty / inspection_qty * 100;
        var blocked_rate = blocked_rate + '%';
        document.getElementById('blocked_rate').value = blocked_rate;
    }

    function defect_cost_cal(){
        var defect_qty = document.getElementById('defect_qty').value;
        var item_net_cost = document.getElementById('item_net_cost').value;
        var defect_cost = defect_qty * item_net_cost;
        var defect_cost = '$' + defect_cost;
        document.getElementById('defect_cost').value = defect_cost;
    }

    function total_claim_cal(){
        var defect_cost = document.getElementById('defect_cost').value;
        var full_cost = document.getElementById('full_cost').value;
        var rework_cost = document.getElementById('rework_cost').value;
        var special_inspection_cost = document.getElementById('special_inspection_cost').value;
        var total_claim = Number(defect_cost) + Number(full_cost) + Number(rework_cost) + Number(special_inspection_cost);
        document.getElementById('total_claim').value = total_claim;
    }

</script>