<?php $task_type = 'qra_request'; ?>

<form method="POST" action="{{ route('project.add_qra_request') }}" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="{{ $task_type }}_p_id" value="{{ $project->id }}" />
    <input type="hidden" name="{{ $task_type }}_task_type" value="{{ $task_type }}" />
    <input type="hidden" name="{{ $task_type }}_author_id" value="{{ Auth::user()->id }}" />

    <div class="form-group">
        <label class="form-label"></label>
        <h5 style="text-align: center;">Please Click the "Create" button to initiate a QRA Request.</h5>
    </div>

    <div class="form-group">
        <input type="submit" name="submit" value="Create" style="margin-top:10px;" class="btn btn-create submit"/>
    </div>

</form>

