@props([
    'name',
])

<svg
    {{ $attributes->class(['size-5 shrink-0'])->merge([
        'aria-hidden' => 'true',
        'fill' => 'none',
        'viewBox' => '0 0 24 24',
        'stroke' => 'currentColor',
        'stroke-width' => '2',
        'xmlns' => 'http://www.w3.org/2000/svg',
    ]) }}
>
    @switch($name)
        @case('menu')
            <path stroke-linecap="round" d="M4 6h16M4 12h16M4 18h16" />
        @break

        @case('chevron-left')
            <path stroke-linecap="round" stroke-linejoin="round" d="m14 18-6-6 6-6" />
        @break

        @case('chevron-right')
            <path stroke-linecap="round" stroke-linejoin="round" d="m10 6 6 6-6 6" />
        @break

        @case('home')
            <path stroke-linecap="round" stroke-linejoin="round" d="m3 10.5 9-7.5 9 7.5v9a1.5 1.5 0 0 1-1.5 1.5h-15A1.5 1.5 0 0 1 3 19.5v-9Z" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 21v-6h6v6" />
        @break

        @case('users')
            <path stroke-linecap="round" stroke-linejoin="round" d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8ZM22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75" />
        @break

        @case('user')
            <path stroke-linecap="round" stroke-linejoin="round" d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2M12 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z" />
        @break

        @case('settings')
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15.75A3.75 3.75 0 1 0 12 8.25a3.75 3.75 0 0 0 0 7.5Z" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 1 1-4 0v-.09a1.65 1.65 0 0 0-1.08-1.5 1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.6 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 1 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.6h.08A1.65 1.65 0 0 0 10 3.09V3a2 2 0 1 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9c.12.6.65 1 1.26 1H21a2 2 0 1 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1Z" />
        @break

        @case('code')
            <path stroke-linecap="round" stroke-linejoin="round" d="m17.25 6.75 3 3-3 3m-10.5 0-3-3 3-3m7.5-3-4.5 12" />
        @break

        @case('logout')
            <path stroke-linecap="round" stroke-linejoin="round" d="M14 8V5a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h7a2 2 0 0 0 2-2v-3M9 12h12m0 0-3-3m3 3-3 3" />
        @break

        @case('eye')
            <path stroke-linecap="round" stroke-linejoin="round" d="M2.062 12.348a1 1 0 0 1 0-.696C3.541 7.61 7.415 5 12 5c4.584 0 8.459 2.61 9.938 6.652a1 1 0 0 1 0 .696C20.459 16.39 16.584 19 12 19c-4.585 0-8.459-2.61-9.938-6.652Z" />
            <circle cx="12" cy="12" r="3" />
        @break

        @case('eye-off')
            <path stroke-linecap="round" stroke-linejoin="round" d="m2 2 20 20M6.71 6.71C4.664 8.04 3.146 9.912 2.062 11.652a1 1 0 0 0 0 .696C3.541 16.39 7.415 19 12 19c1.495 0 2.91-.278 4.17-.78M10.73 5.073A10.76 10.76 0 0 1 12 5c4.584 0 8.459 2.61 9.938 6.652a1 1 0 0 1 0 .696 11.12 11.12 0 0 1-2.07 3.166M14.12 14.12A3 3 0 0 1 9.88 9.88" />
        @break
    @endswitch
</svg>
