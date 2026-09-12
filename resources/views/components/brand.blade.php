@props(['compact' => false])
<a href="{{ route('home') }}" {{ $attributes->class(['kin-brand']) }} aria-label="K'in Solar Guatemala, inicio">
    <span class="kin-brand-symbol" aria-hidden="true">
        <img src="{{ asset('images/kin-icon-dorado.png') }}" alt="" class="w-full h-full object-contain">
    </span>
    <span><strong>K'in<span class="kin-brand-accent">Solar</span> <small>GT</small></strong>
        @unless($compact)<span class="kin-brand-caption">Energía que transforma.</span>@endunless
    </span>
</a>
