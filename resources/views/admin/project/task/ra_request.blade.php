<?php $task_id = $data[0][0]->task_id; $t_type = $data[0][0]->type; $t_author = $data[0][0]->author_id; ?>

    <div class="form-group">
        <div class="buttons">
            <button type="button"
                    onclick="window.location='{{ url("admin/ra_request/".$data[0][0]->id."/edit#asset_selector") }}'"
                    class="btn btn-icon" style="font-family: revert; font-size: 12px; background-color: #323232; color: white; border-radius:1.25rem;">
                 ADD REQUEST
                <span class="badge badge-transparent"></span>
            </button>
            @foreach ($data[3] as $ra)
                <?php
                if($ra->status == 'action_requested'){
                    $status_color = '#28A745';
                }else if($ra->status == 'in_progress'){
                    $status_color = '#fbd102';
                }else if($ra->status == 'action_review'){
                    $status_color = '#F03C3C';
                }else if($ra->status == 'action_completed'){
                    $status_color = '#7e7e7e';
                }else {
                    $status_color = 'gray';
                }
                ?>
                <button type="button"
                            onclick="window.location='{{ url("admin/ra_request/".$data[0][0]->id."/edit#".$ra->id) }}'"
                            class="btn btn-icon" style="font-family: revert; font-size: 12px; background-color: {{$status_color}}; border-radius:1.25rem;">
                    {{ strtoupper(str_replace('_', ' ', $ra->request_type)) }}
                    <span class="badge badge-transparent">{{ ucwords(str_replace('_', ' ', $ra->status)) }}</span>
                </button>
            @endforeach
        </div>
    </div>
