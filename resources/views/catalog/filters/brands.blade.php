<div>
    <h5 class="mb-4 text-sm 2xl:text-md font-bold">{{ $filter->title() }}</h5>

    @foreach($filter->values() as $brandId => $brandTitle)
        <div class="form-checkbox">
            <input name="{{ $filter->name($brandId) }}"
                   type="checkbox"
                   id="{{ $filter->id($brandId) }}"
                   @checked($filter->requestValue($brandId))
                   value="{{ $brandId }}"
            >

            <label for="{{ $filter->id($brandId) }}" class="form-checkbox-label">
                {{ $brandTitle }}
            </label>
        </div>
    @endforeach

</div>
