<div>
    <h4 class="text-center">Analisa</h4>
    <div id="analisa"></div>
    <h4 class="text-center">Normalisasi</h4>
    <div id="normalisasi"></div>
    <h4 class="text-center">Perangkingan</h4>
    <div id="rangking"></div>
</div>

@once
@section('head')
<link href="https://unpkg.com/gridjs/dist/theme/mermaid.min.css" rel="stylesheet" />
@stop
@endonce

@once
@section('javascript')
<script type="module">
    import { Grid } from "https://unpkg.com/gridjs?module";

    new Grid({
        columns: @js($this->columns),
        data: @js($this->analisa),
    }).render(document.getElementById("analisa"));

    new Grid({
        columns: @js($this->columns),
        data: @js($this->normalisasi),
    }).render(document.getElementById("normalisasi"));

    new Grid({
        columns: [...@js($this->columns), 'Rangking'],
        data: @js($this->rangking),
    }).render(document.getElementById("rangking"));
</script>
@stop
@endonce
