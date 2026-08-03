@props(['form', 'theme' => 'light'])
@if(\App\Services\HCaptchaVerifier::isEnabledFor($form))
<div>
    <div class="h-captcha" data-sitekey="{{ \App\Services\HCaptchaVerifier::siteKey() }}" data-theme="{{ $theme }}"></div>
    @error('h-captcha-response')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
</div>
@endif
