<form method="GET" action="{{ route('asset.jira_content') }}">
    <div class="form-row" style="background-color: white; margin: -16px 0px 0px 0px; padding: 0px 0px 0px 12px;">
        <hr width="99%" />
        <div class="form-group col-md-2">
            <select class="design-select" name="content_creator" id="content_creator">
                <option value="">Select Content Creator</option>
                @foreach ($content_creators as $value)
                    <option value="{{ $value['first_name'] }}" @if( $value['first_name'] == $content_creator) selected="selected" @endif >
                        {{$value['first_name']}}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group col-md-2">
            <select class="design-select" name="brand">
                <option value="">Select Brand</option>
                @foreach ($brands as $key => $value)
                    <option value="{{ $key }}" @if( $key == $brand) selected="selected" @endif >
                        {{$value}}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group col-md-2">
            <input type="text" name="asset_id" class="design-field" id="asset_id" placeholder="Asset ID" value="{{ !empty($filter['asset_id']) ? $filter['asset_id'] : '' }}">
        </div>

        <div class="form-group col-md-2">
            <button class="design-btn"><i class="fas fa-search"></i> {{ __('general.btn_search_label') }}</button>
        </div>
    </div>
</form>
