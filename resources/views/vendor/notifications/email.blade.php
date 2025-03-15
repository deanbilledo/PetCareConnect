@component('mail::message')
{{-- Header with Logo --}}
<div style="text-align: center; margin-bottom: 30px;">
    <img src="{{ url('images/logo.png') }}" alt="{{ config('app.name') }}" style="max-width: 200px; height: auto;">
</div>

{{-- Greeting --}}
@if (! empty($greeting))
# {{ $greeting }}
@else
# Hello!
@endif

{{-- Pet Images Row --}}
<div style="display: flex; justify-content: center; margin: 20px 0;">
    <div style="text-align: center; max-width: 600px;">
        <table cellpadding="0" cellspacing="0" border="0" width="100%">
            <tr>
                <td width="25%" style="padding: 5px;">
                    <img src="{{ url('images/dog1.png') }}" alt="Happy Pet" style="width: 100%; border-radius: 10px;">
                </td>
                <td width="25%" style="padding: 5px;">
                    <img src="{{ url('images/dog2.png') }}" alt="Happy Pet" style="width: 100%; border-radius: 10px;">
                </td>
                <td width="25%" style="padding: 5px;">
                    <img src="{{ url('images/dog3.png') }}" alt="Happy Pet" style="width: 100%; border-radius: 10px;">
                </td>
                <td width="25%" style="padding: 5px;">
                    <img src="{{ url('images/dog4.png') }}" alt="Happy Pet" style="width: 100%; border-radius: 10px;">
                </td>
            </tr>
        </table>
    </div>
</div>

{{-- Intro Lines --}}
@foreach ($introLines as $line)
{{ $line }}

@endforeach

{{-- Action Button --}}
@isset($actionText)
<?php
    $color = match ($level) {
        'success', 'error' => $level,
        default => 'primary',
    };
?>
<div style="text-align: center; margin: 35px 0;">
@component('mail::button', ['url' => $actionUrl, 'color' => $color])
{{ $actionText }}
@endcomponent
</div>
@endisset

{{-- Outro Lines --}}
@foreach ($outroLines as $line)
{{ $line }}

@endforeach

{{-- Salutation --}}
@if (! empty($salutation))
{{ $salutation }}
@else
Best regards,<br>
{{ config('app.name') }} Team
@endif

{{-- Subcopy --}}
@isset($actionText)
@slot('subcopy')
@lang(
    "If you're having trouble clicking the \":actionText\" button, copy and paste the URL below\n".
    'into your web browser:',
    [
        'actionText' => $actionText,
    ]
) <span class="break-all">[{{ $displayableActionUrl }}]({{ $actionUrl }})</span>
@endslot
@endisset

{{-- Footer with Social Links --}}
<div style="text-align: center; margin-top: 30px; color: #718096; font-size: 0.875rem;">
    <p>Connect with us</p>
    <div style="margin-top: 10px;">
        <a href="#" style="text-decoration: none; margin: 0 10px;"><img src="https://cdn-icons-png.flaticon.com/512/733/733547.png" width="24" height="24" alt="Facebook"></a>
        <a href="#" style="text-decoration: none; margin: 0 10px;"><img src="https://cdn-icons-png.flaticon.com/512/3670/3670151.png" width="24" height="24" alt="Instagram"></a>
        <a href="#" style="text-decoration: none; margin: 0 10px;"><img src="https://cdn-icons-png.flaticon.com/512/733/733579.png" width="24" height="24" alt="Twitter"></a>
    </div>
    <p style="margin-top: 15px;">&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
</div>
@endcomponent 