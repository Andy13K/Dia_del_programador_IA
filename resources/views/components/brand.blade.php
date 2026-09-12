@props(['compact' => false, 'badge' => true])
<a href="{{ route('home') }}" {{ $attributes->class(['kin-brand']) }} aria-label="K'in Solar Guatemala, inicio">
    <span class="kin-brand-symbol" aria-hidden="true">
        <img src="{{ asset('images/kin-icon-dorado.png') }}" alt="" class="w-full h-full object-contain">
    </span>
    <span><strong>K'in <span class="kin-brand-accent">Solar</span>
        @if($badge)<small>GT <svg class="kin-flag-gt" viewBox="0 0 24 16" aria-hidden="true"><rect width="24" height="16" fill="#fff"/><rect width="8" height="16" fill="#4997D0"/><rect x="16" width="8" height="16" fill="#4997D0"/></svg></small>@endif
        </strong>
        @unless($compact)<span class="kin-brand-caption">Energía que transforma.</span>@endunless
    </span>
</a>
