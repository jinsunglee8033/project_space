<?php $request_type = 'registration_cpnp'; ?>

<form method="POST" action="{{ route('qra_request.add_registration_cpnp') }}" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="{{ $request_type }}_t_id" value="{{ $task_id }}" />
    <input type="hidden" name="{{ $request_type }}_request_type" value="{{ $request_type }}" />
    <input type="hidden" name="{{ $request_type }}_author_id" value="{{ Auth::user()->id }}" />

    <div class="registration_cpnp">
        <p style="color: #b91d19; text-align: center; font-weight: bold;">REGISTRATION CPNP</p>
        <div class="form-group">
            <a href="https://drive.google.com/drive/folders/1kCsyOnjxxQ0Z1wu7rbBRDyfe9gpilTMQ?usp=drive_link" target="_blank" class="badge badge-danger">View Submission Guideline</a>
        </div>
        <div class="form-group">
            <label>Version: </label>
            <select class="form-control" name="{{ $request_type }}_version">
                <option value="">Select</option>
                <?php foreach($versions as $val): ?>
                <option value="{{ $val }}">{{ $val }}</option>
                <?php endforeach ?>
            </select>
        </div>

        <div class="form-group">
            <label>Material Number:</label>
            <input type="text" name="{{ $request_type }}_material_number" class="form-control" value="">
        </div>

        <div class="form-group">
            <label>Vendor Code:</label>
            <input type="text" name="{{ $request_type }}_vendor_code" class="form-control" id="{{ $request_type }}_vendor_auto" value="">
        </div>

        <div class="form-group">
            <label>Vendor Name:</label>
            <input type="text" name="{{ $request_type }}_vendor_name" class="form-control" id="{{ $request_type }}_vendor_name" value="" readonly>
        </div>

        <div class="form-group">
            <label>Attachment: <b style="color: #b91d19">(20MB Max)</b></label>
            <input type="file" data-asset="default" name="{{ $request_type }}_attachment[]" class="form-control p_attachment last_upload" multiple="multiple"/>
            <a href="javascript:void(0);" onclick="another_upload($(this))" class="another_upload">[ Upload Another ]</a>
        </div>
    </div>

    <div class="form-group">
        <input type="submit" name="submit" value="Create" style="margin-top:10px; padding: .55rem 1.5rem; font-size: medium;" class="btn btn-lg btn-create submit"/>
    </div>

</form>

<script type="text/javascript">

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

</script>