<?php $task_type = 'qc_request'; ?>

    <div class="form-group">
        <label class="form-label"></label>
        <h5 style="text-align: center;">Please Click the "Create" button to initiate an <b style="color: #b91d19;">QA Request</b></h5>
    </div>

    <div class="form-group">
        <a href="{{ url('admin/qc_request/'.$project->id.'/edit') }}">
            <button type="button" class="btn btn-primary submit" style="margin-top:10px; padding: .55rem 1.5rem; font-size: medium;">Create</button>
        </a>
        <input type="button"
               value="Skip"
               onclick="action_skip($(this))"
               data-project-id="{{ $project->id }}"
               data-task-type="{{$task_type}}"
               style="margin-top:10px; padding: .55rem 1.5rem; font-size: medium;"
               class="btn btn-lg btn-revision submit"/>
    </div>
