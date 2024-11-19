<?php $task_id = $data[0][0]->task_id; $t_type = $data[0][0]->type; $t_author = $data[0][0]->author_id; ?>

    <div class="form-group">
        <?php if(count($data[4]) == 0){ ?>
        <button type="button"
                onclick="window.location='{{ url("admin/legal_request/".$data[0][0]->id."/edit") }}'"
                class="btn btn-icon" style="font-family: revert; font-size: 12px; background-color: #28A745; border-radius:1.25rem;">
            Add Request Type
            <span class="badge badge-transparent"></span>
        </button>
        <?php } ?>
        <div class="buttons">
            @foreach ($data[4] as $legal)
                <?php
                if($legal->status == 'action_requested'){
                    $status_color = '#fbd102';
                }else if($legal->status == 'in_progress'){
                    $status_color = '#e95300';
                }else if($legal->status == 'action_review'){
                    $status_color = '#a50018';
                }else if($legal->status == 'action_completed'){
                    $status_color = '#7e7e7e';
                }else {
                    $status_color = 'white';
                }
                ?>
                <button type="button"
                        onclick="window.location='{{ url("admin/legal_request/".$data[0][0]->id."/edit") }}'"
                        class="btn btn-icon" style="font-family: revert; font-size: 12px; background-color: {{$status_color}}; border-radius:1.25rem;">
                    {{ strtoupper(str_replace('_', ' ', $legal->request_type)) }}
                    <span class="badge badge-transparent">{{ ucwords(str_replace('_', ' ', $legal->status)) }}</span>
                </button>
            @endforeach
        </div>
    </div>


