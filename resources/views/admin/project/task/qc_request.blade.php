<?php $task_id = $data[0][0]->task_id; $t_type = $data[0][0]->type; $t_author = $data[0][0]->author_id; ?>

<div class="form-group">
    <div class="buttons">
        @foreach ($data[0] as $task)
            <?php
            if($task->type == 'qc_request'){
                if($data[2] == 'action_requested'){
                    $status_color = '#28A745';
                }else if($data[2] == 'in_progress'){
                    $status_color = '#fbd102';
                }else if($data[2] == 'action_review'){
                    $status_color = '#F03C3C';
                }else if($data[2] == 'action_completed'){
                    $status_color = '#7e7e7e';
                }else {
                    $status_color = 'gray';
                }
            }
            ?>
                <button type="button"
                        onclick="window.location='{{ url("admin/qc_request/".$data[0][0]->id."/edit#".$data[0][0]->task_id) }}'"
                        class="btn btn-icon" style="font-family: revert; font-size: 12px; background-color: {{$status_color}}; border-radius:1.25rem;">
                     GO TO REQUEST
                    <span class="badge badge-transparent">{{ ucwords(str_replace('_', ' ', $data[2])) }}</span>
                </button>
        @endforeach
    </div>
</div>