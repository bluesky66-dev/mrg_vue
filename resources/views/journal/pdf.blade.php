@extends('layouts.pdf')

@push('style')
    <style>
        .row{
            page-break-inside: avoid;
        }
        @page{
            margin: 1.5cm;
            size: auto;
        }
    </style>
@endpush

@section('content')

    <div>
        @foreach($entries as $entry)
        <div class="row center-items">
            <div class="col-12">
                <div class="q-card">
                    <div class="q-card-main q-card-container">
                        <div class="date">{{$entry->created_at_formatted}}</div>
                        <p class="body">{{$entry->description}}</p>
                        <p class="tags pull-right"><i  aria-hidden="true" class="q-icon material-icons text-primary" style="font-size: 2rem;">label</i>{{$entry->behavior_tags}}
                        </p>
                        <div  style="clear: both;"></div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

@endsection
