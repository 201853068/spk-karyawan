<div>
    <select name="periode" class="form-control" wire:model="selected">
        @foreach ($this->options as $option)
            <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
        @endforeach
    </select>
</div>
