<form method="GET" action="{{ route('users.index') }}">
    <div class="form-row" style="background-color: white; margin: -16px 0px 0px 0px; padding: 0px 0px 0px 12px;">
        <hr width="99%" />
        <div class="form-group col-md-2">
        <input type="text" name="q" class="design-field" id="q" placeholder="Type name or email.." value="{{ !empty($filter['q']) ? $filter['q'] : old('q') }}">
        </div>
        <div class="form-group col-md-2">
            <select class="design-select" name="team">
                <option value="">Select Teams</option>
                @foreach ($teams as $value)
                    <option value="{{ $value->name }}" @if( $value->name == $team) selected="selected" @endif >
                        {{$value->name}}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="form-group col-md-2">
            <select class="design-select" name="function">
                <option value="">Select Function</option>
                @foreach ($functions as $value)
                    <option value="{{ $value }}" @if( $value == $function) selected="selected" @endif >
                        {{$value}}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="form-group col-md-2">
            <select class="design-select" name="role">
                <option value="">Select Role</option>
                @foreach ($roles_ as $value)
                    <option value="{{ $value }}" @if( $value == $role_) selected="selected" @endif >
                        {{$value}}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="form-group col-md-2">
            <button class="btn btn-project-apply">Apply</button>
        </div>
        <div class="form-group col-md-2">
            <a href="{{ url('admin/users/create') }}">
                <button type="button" class="btn btn-project-create"><i class="fas fa-plus"></i> Create</button>
            </a>
        </div>
    </div>
</form>
