@php
    $user = \Auth::user();
    $client = $user->billing_organization;
    $partner = $user->organization;
@endphp

<div class="footer">
@if($client == $partner)
    <span class="presented-text">{{trans('global.footer.client')}}</span>

    @if($client->logo_path)
        <img class="organization-logo" src="{{$client->logo_path}}">
    @else
        <span class="organization-name">{{$client->name}}</span>
    @endif
@else
    <span class="presented-text">{{trans('global.footer.partner_and_client.prefix')}}</span>
    @if($client->logo_path)
        <img class="organization-logo" src="{{$client->logo_path}}">
    @else
        <span class="organization-name">{{$client->name}}</span>
    @endif
    <span class="presented-text">{{trans('global.footer.partner_and_client.for')}}</span>
    @if($partner->logo_path)
        <img class="organization-logo" src="{{$partner->logo_path}}">
    @else
        <span class="organization-name">{{$partner->name}}</span>
    @endif
@endif
</div>
