@extends('layouts.observer')

@section('title', trans('pulse_survey.observer.feedback.page_title'))

@section('content')
    <div class="pulse-survey-container">
        <style>
        select::-ms-expand { display: none; }
        </style>
        <div class="text-region">
            <form action="{{route('pulse-survey-result.culture', ['share_code' => $survey->share_code])}}" method="POST">
                <div class="text-sm text-faded">{{trans('global.language_preference')}}</div>
                {{csrf_field()}}
                <div class="mdl-selectfield">
                    <select class="browser-default" name="culture_id">
                        @foreach ($cultures as $culture)
                            <option @if($survey->observer->culture_id == $culture->id) selected="selected" @endif value="{{ $culture->id}}">{{ $culture->name_key_translated }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit"
                        class="q-btn row inline flex-center q-focusable q-hoverable relative-position q-btn-rectangle q-btn-standard bg-primary text-white">
                    <div class="desktop-only q-focus-helper">
                    </div>
                    <span class="q-btn-inner row col flex-center">
                        {{trans('global.cta.set')}}
                    </span>
                </button>
            </form>
        </div>
        <form method="post" action="{{route('pulse-survey-result.save', ['share_code' => $survey->share_code])}}">
            {{csrf_field()}}
            <div class="text-region">
                <div class="text-center">
                    <img src="{{ asset('images/MRG-logo.png') }}" alt="logo"/>
                </div>
                <div class="bg-faded hr"></div>
                <h5 class="text-center text-faded">{{trans('pulse_survey.title')}}</h5>
                <div class="body-text">
                    <div>{{trans('pulse_survey.email.feedback.intro', [
                    'full_name' => $survey->pulse_survey->user->full_name,
                    'due_date' => $survey->pulse_survey->formatted_dates['due_at']['localized']
                    ])}}</div>
                </div>
                <div class="body-text">
                    <div>{{trans('pulse_survey.email.feedback.colleague_label', ['first_name' => $survey->pulse_survey->user->first_name])}}</div>
                    <p style="line-height: 16px;">{{$survey->pulse_survey->message}}</p>
                </div>
            </div>
            <div class="q-card">
                <div class="q-card-main q-card-container">
                    <div>
                        {{$survey->rating_feedback_question_key_translated}}
                    </div>
                    <br/>
                    <div class="pulse-survey-radio-labels-numbers">
                        <div data-label="one">1</div>
                        <div data-label="two">2</div>
                        <div data-label="three">3</div>
                        <div data-label="four">4</div>
                        <div data-label="five">5</div>
                        <div data-label="six">6</div>
                        <div data-label="sevent">7</div>
                    </div>
                    <div class="pulse-survey-radio-buttons">
                        <div class="radio bar">
                            <input type="radio" class="score" name="score" value="1">
                        </div>
                        <div class="radio bar">
                            <input type="radio" class="score" name="score" value="2">
                        </div>
                        <div class="radio bar">
                            <input type="radio" class="score" name="score" value="3">
                        </div>
                        <div class="radio bar">
                            <input type="radio" class="score" name="score" value="4">
                        </div>
                        <div class="radio bar">
                            <input type="radio" class="score" name="score" value="5">
                        </div>
                        <div class="radio bar">
                            <input type="radio" class="score" name="score" value="6">
                        </div>
                        <div class="radio bar end">
                            <input type="radio" class="score" name="score" value="7">
                        </div>
                        <div class="radio">
                            <input type="radio" name="score" value="-1">
                        </div>
                    </div>
                    <div class="pulse-survey-radio-labels">
                        <div data-label="rarely">{{trans('pulse_survey.preview.recent.rarely')}}</div>
                        <div data-label="sometimes">{{trans('pulse_survey.preview.recent.sometimes')}}</div>
                        <div data-label="regularly">{{trans('pulse_survey.preview.recent.regularly')}}</div>
                        <div data-label="often">{{trans('pulse_survey.preview.recent.often')}}</div>
                        <div data-label="very-often">{{trans('pulse_survey.preview.recent.very_often')}}</div>
                        <div data-label="na">{{trans('pulse_survey.preview.recent.na')}}</div>
                    </div>
                    <div class="body-text text-negative">{{ $errors->first('score') }}</div>
                </div>
            </div>
            <div class="q-card">
                <div class="q-card-main q-card-container">
                    <div>
                        {{$survey->additional_feedback_question_key_translated}}
                        <div class="q-field row no-wrap items-start q-field-no-label">
                            <div class="row col">
                                <div class="q-field-content col-xs-12 col-sm">
                                    <div class="q-if row no-wrap items-center relative-position q-input text-primary">
                                        <div class="q-if-inner col row no-wrap items-center relative-position">
                                            <textarea name="additional_comments" maxlength="520" rows="5"
                                                      class="col"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-center text-region">
                <button type="submit" id="survey-main-submit"
                        class="q-btn row inline flex-center q-focusable q-hoverable relative-position q-btn-rectangle q-btn-big bg-primary text-white">
                    <div class="desktop-only q-focus-helper">
                    </div>
                    <span class="q-btn-inner row col flex-center">
                        {{trans('pulse_survey.email.feedback.button')}}
                    </span>
                </button>
            </div>
        </form>
    </div>
    <script>
    (function(){
        var clickCount = 0;
        var el = document.getElementById("survey-main-submit");
        el.addEventListener("click", function(event){
            if(clickCount == 1){
                console.log("Unable to submit, button already clicked!");
                event.preventDefault();
            } else{
                console.log("Submitting!");
                clickCount = 1;
            }
        })
    })();
    </script>
@endsection
