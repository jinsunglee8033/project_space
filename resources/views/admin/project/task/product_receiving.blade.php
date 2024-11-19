<?php $task_id = $data[0][0]->task_id; $t_type = $data[0][0]->type; $t_author = $data[0][0]->author_id; ?>

    <div class="form-group">
        <button type="button"
                onclick="window.location='{{ url("admin/product_receiving/".$data[0][0]->id."/edit") }}'"
                class="btn btn-icon" style="font-family: revert; font-size: 12px; background-color: #fbd102; border-radius:1.25rem;">
            Go To Detail
            <span class="badge badge-transparent"></span>
        </button>
    </div>
