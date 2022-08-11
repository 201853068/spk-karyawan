@extends('voyager::master')

@section('page_header')
<div class="container-fluid">
    <h1 class="page-title">
        <i class="voyager-documentation"></i> Laporan
    </h1>
</div>
@stop

@section('content')
<div class="page-content browse container-fluid vext-browse">
    @include('voyager::alerts')
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-bordered">
                <div class="panel-body">
                    <livewire:laporan-penilaian />
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('head')
@livewireStyles
@stop

@section('javascript')
@livewireScripts
@stop
