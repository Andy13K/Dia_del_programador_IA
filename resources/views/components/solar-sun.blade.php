@props(['id' => 'kin-login-sun'])

<div class="kin-solar">
    <div class="kin-solar-art" aria-hidden="true">
        <div class="kin-solar-halo"></div>
        <div class="kin-solar-rays"></div>
        <div class="kin-solar-rays kin-solar-rays-soft"></div>
        <div class="kin-solar-orbit"><span></span></div>
        <div class="kin-solar-orbit kin-solar-orbit-inner"><span></span></div>
        <svg class="kin-solar-star" viewBox="0 0 480 480" fill="none" xmlns="http://www.w3.org/2000/svg" focusable="false">
            <defs>
                <radialGradient id="{{ $id }}-surface" cx=".36" cy=".3" r=".75">
                    <stop stop-color="#fff5bf" />
                    <stop offset=".3" stop-color="#ffda69" />
                    <stop offset=".65" stop-color="#ffac24" />
                    <stop offset=".88" stop-color="#f8790c" />
                    <stop offset="1" stop-color="#d94a06" />
                </radialGradient>
                <radialGradient id="{{ $id }}-limb">
                    <stop offset=".78" stop-color="#ffb52e" stop-opacity="0" />
                    <stop offset=".95" stop-color="#ff6f0c" stop-opacity=".15" />
                    <stop offset="1" stop-color="#ffe3a0" stop-opacity=".85" />
                </radialGradient>
                <linearGradient id="{{ $id }}-flare" x1="140" y1="130" x2="350" y2="350" gradientUnits="userSpaceOnUse">
                    <stop stop-color="#ffb52e" stop-opacity=".05" />
                    <stop offset=".45" stop-color="#ffda78" />
                    <stop offset="1" stop-color="#f8790c" stop-opacity=".1" />
                </linearGradient>
                <filter id="{{ $id }}-plasma" x="0" y="0" width="100%" height="100%">
                    <feTurbulence type="fractalNoise" baseFrequency=".11" numOctaves="3" seed="8" />
                    <feColorMatrix values="1.6 0 0 0 .25  .7 0 0 0 .1  .15 0 0 0 0  0 0 0 1 0" />
                </filter>
                <filter id="{{ $id }}-glow" x="-50%" y="-50%" width="200%" height="200%">
                    <feGaussianBlur stdDeviation="3" />
                </filter>
                <clipPath id="{{ $id }}-disc"><circle cx="240" cy="240" r="96" /></clipPath>
            </defs>
            <circle cx="240" cy="240" r="222" stroke="#ffe3a0" stroke-opacity=".1" stroke-dasharray="1 13" />
            <g class="kin-solar-flares" stroke="url(#{{ $id }}-flare)" stroke-linecap="round">
                <g stroke-width="7" filter="url(#{{ $id }}-glow)">
                    <path d="M308 174C369 108 387 215 332 219M170 305C115 371 102 265 146 255M200 151C180 101 248 104 267 148" />
                </g>
                <g stroke-width="1.3">
                    <path d="M308 174C369 108 387 215 332 219M313 184C353 128 367 201 334 207" />
                    <path d="M170 305C115 371 102 265 146 255M162 292C125 330 123 281 147 272" />
                    <path d="M200 151C180 101 248 104 267 148M209 147C196 120 239 114 254 145" />
                </g>
            </g>
            <circle cx="240" cy="240" r="98" fill="#ffb52e" opacity=".75" filter="url(#{{ $id }}-glow)" />
            <circle cx="240" cy="240" r="96" fill="url(#{{ $id }}-surface)" />
            <g clip-path="url(#{{ $id }}-disc)">
                <g class="kin-solar-texture">
                    <rect x="134" y="134" width="212" height="212" filter="url(#{{ $id }}-plasma)" opacity=".5" />
                </g>
                <g stroke="#fff2ae" stroke-opacity=".25" stroke-width=".8">
                    <path d="M167 210C182 173 199 225 218 197S263 181 280 206M202 286C225 255 253 307 283 269M268 157C284 169 303 182 309 207" />
                </g>
            </g>
            <circle cx="240" cy="240" r="96" fill="url(#{{ $id }}-limb)" />
        </svg>
        <span class="kin-solar-spark kin-solar-spark-one"></span>
        <span class="kin-solar-spark kin-solar-spark-two"></span>
        <span class="kin-solar-spark kin-solar-spark-three"></span>
    </div>
    <label class="kin-solar-control">
        <input class="kin-solar-pause" type="checkbox" aria-label="Pausar animación del sol">
        <svg class="kin-solar-pause-icon" width="12" height="12" viewBox="0 0 12 12" fill="currentColor" aria-hidden="true"><path d="M3 2h2v8H3zm4 0h2v8H7z" /></svg>
        <svg class="kin-solar-play-icon" width="12" height="12" viewBox="0 0 12 12" fill="currentColor" aria-hidden="true"><path d="m3 1 8 5-8 5z" /></svg>
        <span class="kin-solar-pause-text">Pausar</span><span class="kin-solar-play-text">Reanudar</span>
    </label>
</div>
