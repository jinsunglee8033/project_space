<form method="GET" action="{{ route('asset.kpi_web') }}">
    <div class="form-row" style="background-color: white; margin: -16px 0px 0px 0px; padding: 0px 0px 0px 12px;">
        <hr width="99%" />
        <div class="form-group col-md-2">
            <select class="design-select" name="designer" id="designer">
                <option value="">Select Designer</option>
                @foreach ($designers as $value)
                    <option value="{{ $value['first_name'] }}" @if( $value['first_name'] == $designer) selected="selected" @endif >
                        {{$value['first_name']}}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group col-md-2">
            <input type="text" name="campaign_id" class="design-field" id="q" placeholder="Project ID" value="{{ !empty($filter['campaign_id']) ? $filter['campaign_id'] : '' }}">
        </div>
        <div class="form-group col-md-2">
            <input type="text" name="asset_id" class="design-field" id="q" placeholder="Asset ID" value="{{ !empty($filter['asset_id']) ? $filter['asset_id'] : '' }}">
        </div>

        <div class="form-group col-md-2">
            <input type="text" name="search_from" id="search_from" placeholder="From"
                   class="form-control design-field datepicker"
                   value="{{ old('search_from', !empty($filter['search_from']) ? $filter['search_from'] : null) }}">
        </div>

        <div class="form-group col-md-2">
            <input type="text" name="search_to" id="search_to" placeholder="To"
                   class="form-control design-field datepicker"
                   value="{{ old('search_to', !empty($filter['search_to']) ? $filter['search_to'] : null) }}">
        </div>

        <div class="form-group col-md-2">
            <button class="design-btn">Apply</button>
        </div>
    </div>
</form>
