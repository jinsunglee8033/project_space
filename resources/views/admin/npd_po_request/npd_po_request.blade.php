<?php $task_id = $data[0][0]->task_id; $t_type = $data[0][0]->type; $t_author = $data[0][0]->author_id; ?>

<?php if (!empty($task_status) && $task_status == 'action_requested') { ?>
<?php if(auth()->user()->role == 'Admin' || auth()->user()->team == 'Purchasing') { ?>
<div class="form-group">
<input type="button"
       name="action start"
       value="Start"
       onclick="action_start($(this))"
       data-task-id="<?php echo $task_id; ?>"
       style="margin-top:-5px; font-size: medium;"
       class="btn btn-lg btn-success submit"/>
<label style="font-size: medium; font-weight:100; color: #b91d19; padding: 22px 0 30px 20px;"><b>* Click to indicate the start of work.</b></label>
</div>
<?php } ?>
<?php }?>

    <form method="POST" action="{{ route('npd_po_request.edit_npd_po_request', $task_id) }}" enctype="multipart/form-data">
        @csrf

        <div class="form-group">
            <label>Request Detail:</label>
            <textarea class="form-control" style="height:100px;"
                      id="{{ $task_id }}_request_detail"
                      name="request_detail">{{ $data[0][0]->request_detail }}</textarea>
        </div>

        <div class="form-group">
            <label>Priority:</label>
            <select id="{{ $task_id }}_priority" class="form-control"
                    name="priority" required>
                <option value="">Select</option>
                @foreach ($priorities as $value)
                    <option value="{{ $value }}" {{ $value == $data[0][0]->priority ? 'selected' : '' }}>
                        {{ $value }}
                    </option>
                @endforeach
            </select>
        </div>

        <?php if($data[0][0]->due_date_urgent) { ?>
        <div class="form-group due_date_urgent">
            <label>Due Date (Urgent): <b style="color: #b91d19">(Select from the calendar)</b></label>
            <input type="text" name="due_date_urgent" id="{{$task_id}}_due_date_urgent" placeholder="Due Date Urgent"
                   class="form-control @error('due_date_urgent') is-invalid @enderror @if (!$errors->has('due_date_urgent') && old('due_date_urgent')) is-valid @endif"
                   value="{{ old('due_date_urgent', !empty($data[0]) ? $data[0][0]->due_date_urgent : null) }}">
        </div>

        <div class="form-group">
            <label>Urgent Reason:</label>
            <textarea class="form-control" id="urgent_reason" name="urgent_reason" style="height: 100px;">{{ $data[0][0]->urgent_reason }}</textarea>
        </div>

        <?php } ?>

        <div class="form-group">
            <label>Due Date:</label>
            <input type="text" name="due_date" class="form-control" value="{{ $data[0][0]->due_date }}" required>
        </div>

        <div class="form-group">
            <label>Source List Completion:</label>
            <select id="{{ $task_id }}_source_list_completion" class="form-control"
                    name="source_list_completion" required>
                <option value="">Select</option>
                @foreach ($yes_or_no_list as $value)
                    <option value="{{ $value }}" {{ $value == $data[0][0]->source_list_completion ? 'selected' : '' }}>
                        {{ $value }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label>Info Record Completion:</label>
            <select id="{{ $task_id }}_info_record_completion" class="form-control"
                    name="info_record_completion" required>
                <option value="">Select</option>
                @foreach ($yes_or_no_list as $value)
                    <option value="{{ $value }}" {{ $value == $data[0][0]->info_record_completion ? 'selected' : '' }}>
                        {{ $value }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label>Price Set Up:</label>
            <select id="{{ $task_id }}_price_set_up" class="form-control"
                    name="price_set_up" required>
                <option value="">Select</option>
                @foreach ($price_set_up_list as $value)
                    <option value="{{ $value }}" {{ $value == $data[0][0]->price_set_up ? 'selected' : '' }}>
                        {{ $value }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label>Forecast Completion:</label>
            <select id="{{ $task_id }}_forecast_completion" class="form-control"
                    name="forecast_completion" required>
                <option value="">Select</option>
                @foreach ($yes_or_no_list as $value)
                    <option value="{{ $value }}" {{ $value == $data[0][0]->forecast_completion ? 'selected' : '' }}>
                        {{ $value }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label>Materials:</label>
            <textarea class="form-control" id="materials" name="materials" style="height: 200px;" required>{{ $data[0][0]->materials }}</textarea>
        </div>

        <div class="form-group">
            <label>Total SKU Count:</label>
            <input type="text" name="total_sku_count" class="form-control" value="{{ $data[0][0]->total_sku_count }}" required>
        </div>

        <div class="form-group">
            <label>Set-up Plant(s):</label>
            <div class="columns" style="column-count: 3;">
                <?php if ($data[0][0]->set_up_plant != null) { ?>
                @foreach($set_up_plants_list as $value)
                    <?php $checkbox_fields = explode(',', $data[0][0]->set_up_plant); ?>
                    <div class="col-md">
                        <div class="form-check" style="padding-left: 0px;">
                            <input  <?php if (in_array($value->name, $checkbox_fields)) echo "checked" ?>
                                    type="checkbox"
                                    name="set_up_plant[]"
                                    value="{{ $value->name }}" id="{{$task_id}}_{{$value->name}}"
                            >
                            <label class="form-check-label " for="{{$task_id}}_{{ $value->name }}">
                                {{ $value->name }}
                            </label>
                        </div>
                    </div>
                @endforeach
                <?php }else{ ?>
                @foreach($set_up_plants_list as $value)
                    <?php $checkbox_fields = explode(',', $data[0][0]->set_up_plant); ?>
                    <div class="col-md">
                        <div class="form-check" style="padding-left: 0px;">
                            <input  type="checkbox"
                                    name="set_up_plant[]"
                                    value="{{ $value->name }}" id="{{$task_id}}_{{$value->name}}"
                            >
                            <label class="form-check-label " for="{{$task_id}}_{{ $value->name }}">
                                {{ $value->name }}
                            </label>
                        </div>
                    </div>
                @endforeach
                <?php } ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Vendor Code: <b style="color: #b91d19">(Auto-Complete)</b></label>
                    <input type="text" name="vendor_code" id="{{$task_id}}_vendor_code" class="form-control" value="{{ $data[0][0]->vendor_code }}" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Vendor Name:</label>
                    <input type="text" name="vendor_name" id="{{$task_id}}_vendor_name" class="form-control" value="{{ $data[0][0]->vendor_name }}" readonly>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Second Vendor Code: <b style="color: #b91d19">(Auto-Complete)</b></label>
                    <input type="text" name="second_vendor_code" id="{{$task_id}}_second_vendor_code" class="form-control" value="{{ $data[0][0]->second_vendor_code }}">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Second Vendor Name:</label>
                    <input type="text" name="second_vendor_name" id="{{$task_id}}_second_vendor_name" class="form-control" value="{{ $data[0][0]->second_vendor_name }}" readonly>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label>Est. Ready Date: <b style="color: #b91d19">(Select from the calendar)</b></label>
            <input type="text" name="est_ready_date" id="{{$task_id}}_est_ready_date" class="form-control" value="{{ $data[0][0]->est_ready_date }}" required>
        </div>

        <div class="form-group">
            <label>Buyer:</label>
            <select id="{{ $task_id }}_buyer" class="form-control"
                    name="buyer" required>
                <option value="">Select</option>
                @foreach ($po_buyer_list as $value)
                    <option value="{{ $value->id }}" {{ $value->id == $data[0][0]->buyer ? 'selected' : '' }}>
                        {{ $value->first_name }} {{ $value->last_name }}
                    </option>
                @endforeach
            </select>
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
            <label>Attachments: <b style="color: #b91d19">(20MB Max)</b></label>
            <input type="file" data-asset="default" name="p_attachment[]" class="form-control p_attachment last_upload" multiple="multiple"/>
            <a href="javascript:void(0);" onclick="another_upload($(this))" class="another_upload">[ Upload Another ]</a>
        </div>

        <div style="background-color: #e9e9e9; padding: 10px 10px 10px 10px; margin: 0 0 10px 0;">
            <div class="section-title mt-1">For Purchasing Team Only</div>
            <div class="form-group">
                <label>PO#:</label>
                <input type="text" name="po" class="form-control" value="{{ $data[0][0]->po }}">
            </div>

            <br>
            <div class="form-group">
                <a href="https://app.smartsheet.com/b/home?lx=Gn2rFzgChvcGF0Ob7c_nMg" target="_blank" class="badge badge-danger">Inbound Air Shipment Data</a>
            </div>

            <div class="form-group">
                <a href="https://app.smartsheet.com/sheets/wVPFHc7p4F356wcFRMf8g8RWv3jGRhXjCjpgCC61?view=grid" target="_blank" class="badge badge-danger">Inbound Shipment Detail</a>
            </div>

        </div>

        <div class="form-group">
            <?php if (!empty($task_status) && $task_status == 'in_progress') { ?>
            <?php if(auth()->user()->role == 'Admin' || auth()->user()->team == 'Purchasing') { ?>

            <input type="submit" name="submit" value="Save" style="font-size: medium" class="btn btn-lg btn-primary submit"/>
                <label style="font-size: medium; font-weight:100; font-family: sans-serif; color: #b91d19; padding: 0 0 0 20px;"><b>* All changes must be saved before clicking any action buttons.</b></label>
            <br>
            <input type="button"
                   value="Revision"
                   data-toggle="modal"
                   data-target="#revision_reason_{{$task_id}}"
{{--                   onclick="action_review($(this))"--}}
{{--                   data-task-id="<?php echo $task_id; ?>"--}}
                   style="margin-top:10px; font-size: medium;"
                   class="btn btn-lg btn-revision submit"/>

{{--           Complete button  --}}
            <input type="button"
                   value="Complete"
                   onclick="action_complete($(this))"
                   data-task-id="<?php echo $task_id; ?>"
                   style="margin-top:10px; font-size: medium;"
                   class="btn btn-lg btn-complete submit"/>

            <?php } ?>
            <?php }?>

            <?php if (!empty($task_status) && $task_status == 'action_review') { ?>
            <?php if(auth()->user()->role == 'Admin' || auth()->user()->id == $t_author ) { ?>
                <input type="submit" name="submit" value="Save" style="font-size: medium" class="btn btn-lg btn-primary submit"/>
                <label style="font-size: medium; font-weight:100; font-family: sans-serif; color: #b91d19; padding: 0 0 0 20px;">* All changes must be saved before clicking the 'Resubmit' button.</label>
                <br>

                <input type="button"
                   value="Resubmit"
                   onclick="action_resubmit($(this))"
                   data-task-id="<?php echo $task_id; ?>"
                   style="margin-top:10px; font-size: medium;"
                   class="btn btn-lg btn-resubmit submit"/>
            <?php } ?>
            <?php }?>

                <?php if (!empty($task_status) && $task_status == 'action_completed' && $data[0][0]->price_set_up == "Temporary Price (Approved by Division Leader)" ) { ?>
                <?php if(auth()->user()->role == 'Admin'
                || auth()->user()->id == $t_author) { ?>
                <div class="form-group">
                    <input type="button"
                           name="final price"
                           value="Final Price"
                           onclick="final_price($(this))"
                           data-task-id="<?php echo $task_id; ?>"
                           style="margin-top:-5px; font-size: medium;"
                           class="btn btn-lg btn-dark submit"/>
                    <label style="font-size: medium; color: #b91d19; padding: 22px 0 30px 20px;"><b>* Please click "Final Price" once the final price has been updated in the system. *</b></label>
                </div>
                <?php } ?>
                <?php }?>

        </div>
    </form>

<?php if (count($data[1])>0): ?>

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

<script type="text/javascript">

    $(document).ready(function(){
        $("#{{ $task_id }}_vendor_code").autocomplete({
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
                    $('#{{ $task_id }}_vendor_name').val(data.name);
                }
            }
        });

        $("#{{ $task_id }}_second_vendor_code").autocomplete({
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
                    $('#{{ $task_id }}_second_vendor_name').val(data.name);
                }
            }
        });

    });

    $(function() {

        var today_urgent = "<?php echo $data[0][0]->due_date_urgent; ?>"
        $('input[name="<?php echo $task_id; ?>_due_date_urgent"]').daterangepicker({
            singleDatePicker: true,
            minDate: today_urgent,
            locale: {
                format: 'YYYY-MM-DD'
            },
        });

        var today_est_ready_date = new Date();
        $('input[id="<?php echo $task_id; ?>_est_ready_date"]').daterangepicker({
            singleDatePicker: true,
            minDate: today_est_ready_date,
            locale: {
                format: 'YYYY-MM-DD'
            },
        });

    });
</script>

<div class="modal fade"
     id="revision_reason_{{$task_id}}"
     tabindex="-1"
     data-backdrop="false"
     role="dialog"
     aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form method="POST" action="{{ route('npd_po_request.revision_reason') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="request_type_id" value="{{$task_id}}">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">Revision Reason</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <h6><b>Select the appropriate reason and provide any additional details in the note before saving.</b></h6>
                        <div class="columns" style="column-count: 1;">
                            @foreach($revision_reason_list as $val)
                                <div class="col-md">
                                    <div class="form-check">
                                        <input  type="radio" name="revision_reason" required
                                                id="revision_{{$task_id}}_{{$val}}" value="{{$val}}"
                                        >
                                        <label class="form-check-label" for="revision_{{$task_id}}_{{ $val }}">
                                            {{ $val}}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <label>Note:</label>
                        <textarea class="form-control" id="revision_reason_note" name="revision_reason_note" style="height: 100px;"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-dark" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-revision">Save Revision Reason</button>
                </div>
            </form>

        </div>
    </div>
</div>
