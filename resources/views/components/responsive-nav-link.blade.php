@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full border-l-4 border-cyan-300 bg-cyan-300/10 py-2 ps-3 pe-4 text-start text-base font-medium text-cyan-100 focus:border-cyan-200 focus:bg-cyan-300/20 focus:text-cyan-100 focus:outline-none transition duration-150 ease-in-out'
            : 'block w-full border-l-4 border-transparent py-2 ps-3 pe-4 text-start text-base font-medium text-slate-300 hover:border-white/30 hover:bg-white/5 hover:text-slate-100 focus:border-white/30 focus:bg-white/5 focus:text-slate-100 focus:outline-none transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
