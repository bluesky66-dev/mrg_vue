@extends('layouts.pdf')

@section('title', trans('pulse_survey.share.page_title'))

@push('style')
    <style>
        .q-card,
        .report-scores-chart
        {
            page-break-inside: avoid;
        }
        .open-answers{
            page-break-before: always;
        }
        @page{
            margin: 1.5cm;
            size: auto;
        }
        .hover-over-card-top-right .q-btn{
           display: none !important;
        }
        #momentum-app {
            max-width: 800px;
            margin: 0 auto;
        }
    </style>
@endpush

@section('content')

    <div id="momentum-app"  class="">
        <momentum-action-plans-results />
    </div>

@endsection
