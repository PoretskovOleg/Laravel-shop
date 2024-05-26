<option @selected($sort->selected())
        value="{{ $sort->value() }}"
        class="text-dark"
>
    {{ $sort->title() }}
</option>
