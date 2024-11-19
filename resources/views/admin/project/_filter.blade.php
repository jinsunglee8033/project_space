<form method="GET" action="{{ route('project.index') }}">
    <div class="form-row" style="background-color: white; margin: -16px 0px 0px 0px; padding: 0px 0px 0px 12px;">
        <hr width="99%" />
{{--        <div class="form-group col-md-1" style="max-width: 12.666%; margin: -10px 0 0 5px;"  >--}}
{{--            <div class="text-small font-600-bold" style="color: #c6c6c6;"><i class="fas fa-circle"></i> Not Started</div>--}}
{{--            <div class="text-small font-600-bold" style="color: #b91d19;"><i class="fas fa-circle"></i> In Progress</div>--}}
{{--            <div class="text-small font-600-bold" style="color: #404040FF;"><i class="fas fa-circle"></i> Completed</div>--}}
{{--        </div>--}}
        <div class="form-group col-md-4">
            <input type="text" name="q" class="design-field" id="q" placeholder="Project Name" value="{{ !empty($filter['q']) ? $filter['q'] : '' }}">
            <input type="hidden" name="status" id="status" value="active">
        </div>
        <div class="form-group col-md-2">
            <select class="design-select" name="team">
                <option value="">Select Team</option>
                @foreach ($team_list as $value)
                    <option value="{{ $value->name }}" @if( $value->name == $team) selected="selected" @endif >
                        {{ $value->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="form-group col-md-2">
            <button class="btn btn-project-apply design-btn"> Apply </button>
        </div>
        <div class="form-group col-md-2">
            <a href="{{ url('admin/project/create') }}">
                <button type="button" class="btn btn-project-create"><i class="fas fa-plus"></i> Create</button>
            </a>
        </div>
    </div>
</form>
