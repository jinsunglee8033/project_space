<form method="GET" action="{{ route('dev.dev_jira') }}">
    <div class="form-row" style="background-color: white; margin: -16px 0px 0px 0px; padding: 0px 0px 0px 12px;">
        <hr width="99%" />
        <div class="form-group col-md-1" style="max-width: 12.666%; margin-top: -10px;" >
            <div class="text-small font-600-bold" style="color: #FF0000;"><i class="fas fa-circle"></i> Critical</div>
            <div class="text-small font-600-bold" style="color: #FFA500;"><i class="fas fa-circle"></i> High</div>
            <div class="text-small font-600-bold" style="color: #008000;"><i class="fas fa-circle"></i> Normal</div>
        </div>
        <div class="form-group col-md-2">
            <select class="design-select" name="priority">
                <option value="">Select Priority</option>
                @foreach ($priorities as $value)
                    <option value="{{ $value }}" @if( $value == $priority) selected="selected" @endif >
                        {{$value}}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="form-group col-md-2">
            <select class="design-select" name="developer">
                <option value="">Select Developer</option>
                @foreach ($developers as $assignee)
                    <option value="{{ $assignee['id'] }}" @if( $assignee['id'] == $developer) selected="selected" @endif >
                        {{$assignee['first_name']}}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="form-group col-md-2">
            <button class="btn btn-project-apply">Apply</button>
        </div>
        <div class="form-group col-md-2">
            <a href="{{ url('admin/dev/create') }}">
                <button type="button" class="btn btn-project-create"><i class="fas fa-plus"></i> Create</button>
            </a>
        </div>
        <div class="form-group col-md-1">
        </div>
        <div class="form-group col-md-2">
            <div class="follow" style="float:right; margin-top: -5px; padding-right: 15px;">
                <i class="left" onclick="moveScrollRight()" ></i>
                <i class="right" onclick="moveScrollLeft()" ></i>
            </div>
        </div>
    </div>
</form>
