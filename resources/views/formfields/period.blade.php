<div class="input-group date">
    <input @if($row->required == 1 || isset($row->details->required)) required @endif type="text" class="form-control
    ext-datepicker" name="{{ $row->field }}" id="{{ $row->field }}"
    value="@if(isset($dataTypeContent->{$row->field})){{ \Carbon\Carbon::parse(old($row->field,
    $dataTypeContent->{$row->field}))->format('d-m-Y') }}@else{{old($row->field)}}@endif">
    <span class="input-group-addon">
        <span class="glyphicon glyphicon-calendar"></span>
    </span>
</div>

@section('javascript')
<script>
    $('#{{ $row->field }}')
    .parent()
    .addClass('input-group datetime')
    .datetimepicker({
        locale: 'id',
        defaultDate: Date.now(),
        format: 'DD-MM-Y',
    });
</script>
@endsection
