<?php $task_type = 'product_information'; ?>

<form method="POST" action="{{ route('project.add_product_information') }}" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="{{ $task_type }}_p_id" value="{{ $project->id }}" />
    <input type="hidden" name="{{ $task_type }}_task_type" value="{{ $task_type }}" />
    <input type="hidden" name="{{ $task_type }}_author_id" value="{{ Auth::user()->id }}" />

    <div class="form-group">
        <label>Product Name:</label>
        <input type="text" name="{{ $task_type }}_product_name" class="form-control" value="" required>
    </div>

    <div class="form-group">
        <label>Product Line:</label>
        <input type="text" name="{{ $task_type }}_product_line" class="form-control" value="" required>
    </div>

    <div class="form-group">
        <label>Total SKU Count:</label>
        <input type="text" name="{{ $task_type }}_total_sku_count" class="form-control" value="" required>
    </div>

    <div class="form-group">
        <label>Category: <b style="color: #b91d19">(Auto-Complete)</b></label>
        <input type="text" name="{{ $task_type }}_category" id="product_category_auto" class="form-control" value="" required>
    </div>

    <div class="form-group">
        <label>Segment: <b style="color: #b91d19">(Auto-Complete)</b></label>
        <input type="text" name="{{ $task_type }}_segment" id="product_segment_auto" class="form-control" value="" required>
    </div>

    <div class="form-group">
        <label data-toggle="modal"
               data-target="#Modal_dimension_guidance">Product Dimension (Package): X in (L) × X in (W) × X in (H) <b style="color: #b91d19">(Click for checking guidance)</b></label>
        <input type="text" name="{{ $task_type }}_product_dimension" class="form-control" value="" required>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label>Claim Weight:</label>
                <input type="text" name="{{ $task_type }}_claim_weight" class="form-control" value="" required>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label>Weight Unit:</label>
                <select class="form-control" name="{{ $task_type }}_weight_unit" required>
                    <option value="">Select</option>
                    <?php foreach($weight_unit_list as $val): ?>
                    <option value="{{ $val }}">{{ $val }}</option>
                    <?php endforeach ?>
                </select>
            </div>
        </div>
    </div>

    <div class="form-group">
        <label>Components / Ingredients:</label>
        <textarea class="form-control" name="{{ $task_type }}_components" style="height:100px;" required></textarea>
    </div>

    <div class="form-group">
        <label>What it is:</label>
        <textarea class="form-control" name="{{ $task_type }}_what_it_is" style="height:100px;" required></textarea>
    </div>

    <div class="form-group">
        <label>Features & Benefits:</label>
        {!! Form::textarea($task_type.'_features', null, ['class' => 'form-control summernote']) !!}
    </div>

    <div class="form-group">
        <label>Marketing Claim:</label>
        {!! Form::textarea($task_type.'_marketing_claim', null, ['class' => 'form-control summernote']) !!}
    </div>

    <div class="form-group">
        <label>Applications / How to Use:</label>
        {!! Form::textarea($task_type.'_applications', null, ['class' => 'form-control summernote']) !!}
    </div>

    <div class="form-group">
        <label>Sustainability: </label>
        <div class="columns" style="column-count: 3;">
        <?php foreach($sustainability_list as $value): ?>
        <div class="col-md">
            <div class="form-check" style="padding-left: 0px;">
                <input  type="checkbox"
                        name="{{ $task_type }}_sustainability[]"
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

    <div class="form-group">
        <label>If others, specify:</label>
        <textarea class="form-control" name="{{ $task_type }}_if_others" style="height:100px;"></textarea>
    </div>

{{--    <div class="form-group">--}}
{{--        <label>Distribution:</label>--}}
{{--        <textarea class="form-control" name="{{ $task_type }}_distribution" style="height:100px;" required></textarea>--}}
{{--    </div>--}}

    <div class="form-group">
        <label>Sales Channel:</label>
        <div class="columns" style="column-count: 2;">
            <?php foreach($account_list as $value): ?>
            <div class="col-md">
                <div class="form-check" style="padding-left: 0px;">
                    <input  type="checkbox"
                            name="{{ $task_type }}_distribution[]"
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

    <div class="form-group">
        <label>If others, specify (Sales Channel):</label>
        <textarea class="form-control" name="{{ $task_type }}_if_others_distribution" style="height:100px;"></textarea>
    </div>

    <div class="form-group">
        <label>Attachments: <b style="color: #b91d19">(20MB Max)</b></label>
        <input type="file" data-asset="default" name="{{ $task_type }}_p_attachment[]" class="form-control p_attachment last_upload" multiple="multiple"/>
        <a href="javascript:void(0);" onclick="another_upload($(this))" class="another_upload">[ Upload Another ]</a>
    </div>

    <div class="form-group">
        <input type="submit" name="submit" value="Create" style="margin-top:10px; padding: .55rem 1.5rem; font-size: medium;" class="btn btn-create submit"/>
    </div>

</form>

<div class="modal fade"
     id="Modal_dimension_guidance"
     tabindex="-1"
     data-backdrop="false"
     role="dialog"
     aria-labelledby="exampleModalLabel"
     aria-hidden="true">
    <div class="modal-dialog"
         role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button"
                        class="close"
                        data-dismiss="modal"
                        aria-label="Close">
                                    <span aria-hidden="true">
                                      ×
                                  </span>
                </button>
            </div>
            <!--Modal body with image-->
            <div class="modal-body">
                <img class="img-fluid" src="<?php echo '/storage/dimension_guidance.png'; ?>">
            </div>
            <div class="modal-footer">
                <button type="button"
                        class="btn btn-danger"
                        data-dismiss="modal">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">

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

    $(document).ready(function(){
        $("#product_segment_auto").autocomplete({
            source: function (request, cb){
                $.ajax({
                    url: "<?php echo url('/admin/project/autocomplete_product_segment'); ?>"+"?product_segment="+request.term,
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
                    $('#{{$task_type}}_product_segment').val(data.name);
                }
            }
        });
    });

</script>
