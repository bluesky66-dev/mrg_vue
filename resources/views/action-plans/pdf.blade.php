@extends('layouts.pdf')

@section('title', trans('action_plan.share.page_title'))

@push('style')
    <style>
        .q-card,
        .report-scores-chart
        {
            position: relative;
            page-break-inside: avoid;
            top: 10px;
            margin-bottom: 18px !important;
        }
        @page{
            margin: 1.5cm;
            size: auto;
        }
        #momentum-app {
            max-width: 800px;
            margin: 0 auto;
        }
    </style>
@endpush

@section('content')

    <div id="momentum-app"  class="">
        <momentum-action-plans-share-preview></momentum-action-plans-share-preview>
    </div>

@endsection
