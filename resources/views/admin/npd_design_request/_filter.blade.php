<form method="GET" action="{{ route('npd_design_request.index') }}">
    <div class="form-row" style="background-color: white; margin: -16px 0px 0px 0px; padding: 0px 0px 0px 12px;">
        <hr width="99%" />
        <div class="form-group col-md-4">
            <input type="text" name="q" class="design-field" id="q" placeholder="Project Name" value="{{ !empty($filter['q']) ? $filter['q'] : '' }}">
            <input type="hidden" name="status" id="status" value="active">
        </div>
{{--        <div class="form-group col-md-2">--}}
{{--            <select class="design-select" name="brand">--}}
{{--                <option value="">Select Brand</option>--}}
{{--                @foreach ($brands as $key => $value)--}}
{{--                    <option value="{{ $key }}" @if( $key == $brand) selected="selected" @endif >--}}
{{--                        {{$value}}--}}
{{--                    </option>--}}
{{--                @endforeach--}}
{{--            </select>--}}
{{--        </div>--}}
        <div class="form-group col-md-2">
            <button class="design-btn"> Apply </button>
        </div>
    </div>
</form>
