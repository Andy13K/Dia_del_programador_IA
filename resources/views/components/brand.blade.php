@props(['compact' => false])
<a href="{{ route('home') }}" {{ $attributes->class(['kin-brand']) }} aria-label="K'in Solar Guatemala, inicio">
    <span class="kin-brand-symbol" aria-hidden="true">
        <svg viewBox="0 0 32 32" fill="none"><circle cx="16" cy="16" r="6" stroke="currentColor" stroke-width="2"/><path d="M16 2v5m0 18v5M2 16h5m18 0h5M6.1 6.1l3.5 3.5m12.8 12.8 3.5 3.5M6.1 25.9l3.5-3.5M22.4 9.6l3.5-3.5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
    </span>
    <span><strong>K'in<span class="kin-brand-accent">Solar</span> <small>GT</small></strong>
        @unless($compact)<span class="kin-brand-caption">Energía que transforma.</span>@endunless
    </span>
</a>
