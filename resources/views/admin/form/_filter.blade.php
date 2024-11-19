<form method="GET" action="{{ route('users.index') }}">
    <div class="form-row">
        <div class="form-group col-md-4">
        <input type="text" name="q" class="form-control" id="q" placeholder="Type name or email.." value="{{ !empty($filter['q']) ? $filter['q'] : old('q') }}">
        </div>
        <div class="form-group col-md-2">
            <select class="form-control" name="team">
                <option value="">Select Team</option>
                @foreach ($teams_ as $value)
                    <option value="{{ $value }}" @if( $value == $team_) selected="selected" @endif >
                        {{$value}}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="form-group col-md-2">
            <select class="form-control" name="role">
                <option value="">Select Role</option>
                @foreach ($roles_ as $key => $value)
                    <option value="{{ $value }}" @if( strtolower($key) == $role_) selected="selected" @endif >
                        {{$key}}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="form-group col-md-2">
            <button class="btn btn-block btn-primary btn-filter"><i class="fas fa-search"></i> {{ __('general.btn_search_label') }}</button>
        </div>
        <div class="form-group col-md-2">
            <a href="{{ url('admin/users/create') }}" class="btn btn-block btn-icon icon-left btn-success btn-filter"><i class="fas fa-plus-circle"></i> @lang('general.btn_create_label')</a>
        </div>
    </div>
</form>
