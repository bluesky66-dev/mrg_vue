@component('mail::message')
{{-- Greeting --}}
{{--@if (! empty($greeting))--}}
{{--# {{ $greeting }}--}}
{{--@else--}}
{{--@if ($level == 'error')--}}
    {{--{!! trans('global.email.greeting.error') !!}--}}
{{--@else--}}
    {{--{!! trans('global.email.greeting.normal') !!}--}}
{{--@endif--}}
{{--@endif--}}

{{-- Intro Lines --}}
@foreach ($introLines as $line)
{{ $line }}

@endforeach

{{-- Action Button --}}
@isset($actionText)
<?php
    switch ($level) {
        case 'success':
            $color = 'green';
            break;
        case 'error':
            $color = 'red';
            break;
        default:
            $color = 'blue';
    }
?>
@component('mail::button', ['url' => $actionUrl, 'color' => $color])
{{ $actionText }}
@endcomponent
@endisset

{{-- Outro Lines --}}
@foreach ($outroLines as $line)
{!! $line !!}

@endforeach

{{-- Salutation --}}
@if (! empty($salutation))
{!! $salutation !!}
@else
{!! trans('global.email.salutation') !!}
@endif

{{-- Subcopy --}}
@isset($actionText)
@component('mail::subcopy')
@php
$text = trans('global.email.button_trouble');
$text = str_replace(':actionText', $actionText, $text);
@endphp
{!! $text !!} [{{ $actionUrl }}]({{ $actionUrl }})
@endcomponent
@endisset
@endcomponent
