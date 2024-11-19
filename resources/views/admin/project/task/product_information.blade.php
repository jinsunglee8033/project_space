<?php $task_id = $data[0][0]->task_id; $t_type = $data[0][0]->type; $t_author = $data[0][0]->author_id; ?>


<form method="POST" action="{{ route('project.edit_product_information', $task_id) }}" enctype="multipart/form-data">
    @csrf

    <div class="form-group">
        <label>Product Name:</label>
        <input type="text" name="product_name" class="form-control" required value="<?php echo $data[0][0]->product_name; ?>">
    </div>

    <div class="form-group">
        <label>Product Line:</label>
        <input type="text" name="product_line" class="form-control" required value="<?php echo $data[0][0]->product_line; ?>">
    </div>

    <div class="form-group">
        <label>Total SKU Count:</label>
        <input type="text" name="total_sku_count" class="form-control" required value="<?php echo $data[0][0]->total_sku_count; ?>">
    </div>

    <div class="form-group">
        <label>Category: <b style="color: #b91d19">(Auto-Complete)</b></label>
        <input type="text" name="category" id="product_category_auto" class="form-control" required value="<?php echo $data[0][0]->category; ?>">
    </div>

    <div class="form-group">
        <label>Segment: <b style="color: #b91d19">(Auto-Complete)</b></label>
        <input type="text" name="segment" id="product_segment_auto" class="form-control" required value="<?php echo $data[0][0]->segment; ?>">
    </div>

    <div class="form-group">
        <label data-toggle="modal"
               data-target="#Modal_dimension_guidance">Product Dimension (Package): X in (L) × X in (W) × X in (H) <b style="color: #b91d19">(Click for checking guidance)</b></label>
        <input type="text" name="product_dimension" class="form-control" required value="<?php echo $data[0][0]->product_dimension; ?>">
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label>Claim Weight:</label>
                <input type="text" name="claim_weight" class="form-control" required value="<?php echo $data[0][0]->claim_weight; ?>">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label>Weight Unit:</label>
                <select id="weight_unit" class="form-control"
                        name="weight_unit" required>
                    <option value="">Select</option>
                    @foreach ($weight_unit_list as $value)
                        <option value="{{ $value }}" {{ $value == $data[0][0]->weight_unit ? 'selected' : '' }}>
                            {{ $value }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <div class="form-group">
        <label>Components / Ingredients:</label>
        <textarea class="form-control" name="components" required style="height:100px;"><?php echo $data[0][0]->components; ?></textarea>
    </div>

    <div class="form-group">
        <label>What it is:</label>
        <textarea class="form-control" name="what_it_is" required style="height:100px;"><?php echo $data[0][0]->what_it_is; ?></textarea>
    </div>

    <div class="form-group">
        <label>Features & Benefits:</label>
        {!! Form::textarea('features', !empty($data[0][0]) ? $data[0][0]->features : null, ['class' => 'form-control summernote']) !!}
    </div>

    <div class="form-group">
        <label>Marketing Claim:</label>
        {!! Form::textarea('marketing_claim', !empty($data[0][0]) ? $data[0][0]->marketing_claim : null, ['class' => 'form-control summernote']) !!}
    </div>

    <div class="form-group">
        <label>Applications / How to Use:</label>
        {!! Form::textarea('applications', !empty($data[0][0]) ? $data[0][0]->applications : null, ['class' => 'form-control summernote']) !!}
    </div>

    <div class="form-group">
        <label>Sustainability:</label>
        <div class="columns" style="column-count: 4;">
            <?php if ($data[0][0]->sustainability != null) { ?>
            @foreach($sustainability_list as $value)
                <?php $checkbox_fields = explode(', ', $data[0][0]->sustainability); ?>
                <div class="col-md">
                    <div class="form-check" style="padding-left: 0px;">
                        <input  <?php if (in_array($value, $checkbox_fields)) echo "checked" ?>
                                type="checkbox"
                                name="sustainability[]"
                                value="{{ $value }}"
                        >
                        <label class="form-check-label " for="{{ $value }}">
                            {{ $value }}
                        </label>
                    </div>
                </div>
            @endforeach
            <?php }else{ ?>
            @foreach($sustainability_list as $value)
                <?php $checkbox_fields = explode(', ', $data[0][0]->sustainability); ?>
                <div class="col-md">
                    <div class="form-check" style="padding-left: 0px;">
                        <input  type="checkbox"
                                name="sustainability[]"
                                value="{{ $value }}"
                        >
                        <label class="form-check-label " for="{{ $value }}">
                            {{ $value }}
                        </label>
                    </div>
                </div>
            @endforeach
            <?php } ?>
        </div>
    </div>

    <div class="form-group">
        <label>If others, specify:</label>
        <textarea class="form-control" name="if_others" style="height:100px;"><?php echo $data[0][0]->if_others; ?></textarea>
    </div>

{{--    <div class="form-group">--}}
{{--        <label>Distribution:</label>--}}
{{--        <textarea class="form-control" name="distribution" required style="height:100px;"><?php echo $data[0][0]->distribution; ?></textarea>--}}
{{--    </div>--}}

    <div class="form-group">
        <label>Sales Channel:</label>
        <div class="columns" style="column-count: 2;">
            <?php if ($data[0][0]->distribution != null) { ?>
            @foreach($account_list as $value)
                <?php $checkbox_fields = explode(', ', $data[0][0]->distribution); ?>
                <div class="col-md">
                    <div class="form-check" style="padding-left: 0px;">
                        <input  <?php if (in_array($value, $checkbox_fields)) echo "checked" ?>
                                type="checkbox"
                                name="distribution[]"
                                value="{{ $value }}"
                        >
                        <label class="form-check-label " for="{{ $value }}">
                            {{ $value }}
                        </label>
                    </div>
                </div>
            @endforeach
            <?php }else{ ?>
            @foreach($account_list as $value)
                <?php $checkbox_fields = explode(', ', $data[0][0]->distribution); ?>
                <div class="col-md">
                    <div class="form-check" style="padding-left: 0px;">
                        <input  type="checkbox"
                                name="distribution[]"
                                value="{{ $value }}"
                        >
                        <label class="form-check-label " for="{{ $value }}">
                            {{ $value }}
                        </label>
                    </div>
                </div>
            @endforeach
            <?php } ?>
        </div>
    </div>

    <div class="form-group">
        <label>If others, specify (Sales Channel):</label>
        <textarea class="form-control" name="if_others_distribution" style="height:100px;"><?php echo $data[0][0]->if_others_distribution; ?></textarea>
    </div>


<?php if (count($data[1]) > 0): ?>
    <div class="form-group">
        <label>Attachments: </label>
        <br/>
        <?php foreach ($data[1] as $attachment): ?>
        <?php
        $file_ext = $attachment['file_ext'];
        if(strpos($file_ext, ".") !== false){
            $file_ext = substr($file_ext, 1);
        }
        $not_image = ['pdf','doc','docx','pptx','ppt','mp4','xls','xlsx','csv'];
        $file_icon = '/storage/'.$file_ext.'.png';
        $attachment_link = '/storage' . $attachment['attachment'];
        $open_link = 'open_download';
        ?>
        <div class="attachment_wrapper">
            <?php $name = explode('/', $attachment['attachment']); ?>
            <?php $name = $name[count($name)-1]; ?>
            <?php $date = date('m/d/Y g:ia', strtotime($attachment['date_created'])); ?>
            <div class="attachement">{{ $name }}</div>
            <a onclick="remove_file($(this))"
               class="delete attachement close"
               title="Delete"
               data-file-name="<?php echo $name; ?>"
               data-attachment-id="<?php echo $attachment['attachment_id']; ?>">
                <i class="fa fa-times"></i>
            </a>
            <img title="<?php echo $name . ' (' . date('m/d/Y g:ia', strtotime($date)) . ')'; ?>"
                 data-file-date="<?php echo $date; ?>"
                 <?php
                 if (!in_array($file_ext, $not_image)) {
                 $file_icon = $attachment_link;
                 $open_link = 'open_image';
                 ?>
                 data-toggle="modal"
                 data-target="#exampleModal_<?php echo $attachment['attachment_id']; ?>"
                 <?php
                 }
                 ?>
                 onclick="<?php echo $open_link; ?>('<?php echo $attachment_link; ?>')"
                 src="<?php echo $file_icon; ?>"
                 class="thumbnail"/>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <div class="form-group">
        <label>Upload Visual References: <b style="color: #b91d19">(20MB Max)</b></label>
        <input type="file" data-asset="default" name="p_attachment[]" class="form-control p_attachment last_upload" multiple="multiple"/>
        <a href="javascript:void(0);" onclick="another_upload($(this))" class="another_upload">[ Upload Another ]</a>
    </div>

    <div class="form-group">

        <?php if (!empty($data[2]) && ( $data[2] != 'action_completed' ) ) { ?>
        <input type="submit" name="submit" value="Save" style="margin-top:10px; font-size: large" class="btn btn-lg btn-primary submit"/>
            <label style="font-size: medium; font-weight: 100; color: #b91d19; padding: 0 0 0 20px;">* Save all changes before clicking action buttons.</label>
        <input type="hidden" name="status" value="{{ $data[2] }}"/>
        <?php }?>

        <?php if (!empty($data[2]) && $data[2] == 'in_progress') { ?>
        <?php if(auth()->user()->role == 'Admin'
        || auth()->user()->id == $t_author) { ?>
            </br>
        <input type="button"
               value="Complete"
               onclick="action_complete($(this))"
               data-task-id="<?php echo $task_id; ?>"
               style="margin-top:10px; font-size: medium;"
               class="btn btn-lg btn-complete submit"/>
        <?php } ?>
        <?php }?>

    </div>
</form>



<?php if (!empty($data[1])): ?>
<?php foreach ($data[1] as $attachment): ?>
<div class="modal fade"
     id="exampleModal_<?php echo $attachment['attachment_id']; ?>"
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
            <?php $name = explode('/', $attachment['attachment']); ?>
            <?php $name = $name[count($name)-1]; ?>
            <div class="modal-title text-lg-center" style="font-size: 18px; color: #1a1a1a; float: right;">{{ $name }} </div>
            <div class="modal-title text-sm-center">{{ $attachment['date_created'] }} </div>
            <div class="modal-body">
                <img class="img-fluid" src="<?php echo '/storage' . $attachment['attachment']; ?>">
            </div>
            <div class="modal-footer">
                <button type="button"
                        class="btn btn-primary"
                        data-dismiss="modal"
                        onclick="open_download('<?php echo '/storage' . $attachment['attachment']; ?>')"
                >
                    Download
                </button>
                <button type="button"
                        class="btn btn-danger"
                        data-dismiss="modal">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>
<?php endforeach; ?>
<?php endif; ?>

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
                    $('#product_category').val(data.name);
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
                    $('#product_segment').val(data.name);
                }
            }
        });
    });

</script>
