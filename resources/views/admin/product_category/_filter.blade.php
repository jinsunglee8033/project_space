<form method="GET" action="{{ route('product_category.index') }}">
    <div class="form-row" style="background-color: white; margin: -16px 0px 0px 0px; padding: 0px 0px 0px 12px;">
        <hr width="99%" />
        <div class="form-group col-md-2">
        <input type="text" name="q" class="design-field" id="q" placeholder="Type Name.." value="{{ !empty($filter['q']) ? $filter['q'] : old('q') }}">
        </div>
        <div class="form-group col-md-2">
{{--            <select class="design-select" name="team">--}}
{{--                <option value="">Select Team</option>--}}

{{--            </select>--}}
        </div>
        <div class="form-group col-md-2">
{{--            <select class="design-select" name="function">--}}
{{--                <option value="">Select Function</option>--}}

{{--            </select>--}}
        </div>
        <div class="form-group col-md-2">

        </div>
        <div class="form-group col-md-2">
            <button class="btn btn-project-apply">Apply</button>
        </div>
        <div class="form-group col-md-2">
            <a href="{{ url('admin/product_category/create') }}">
                <button type="button" class="btn btn-project-create"><i class="fas fa-plus"></i> Create</button>
            </a>
        </div>
    </div>
</form>
