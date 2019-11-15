@extends('layouts.observer')

@section('title', trans('pulse_survey.observer.thankyou.page_title'))

@section('content')
    <div class="pulse-survey-container">
        <div class="text-region">
            <div class="text-center">
                <img src="{{ asset('images/MRG-logo.png') }}" alt="logo"/>
            </div>
            <div class="bg-faded hr"></div>
            <h5 class="text-center text-faded">{{trans('pulse_survey.observer.thankyou.page_title')}}</h5>
            <div class="body-text">
                {{trans('pulse_survey.observer.thankyou.message')}}
            </div>
        </div>
    </div>
@endsection

