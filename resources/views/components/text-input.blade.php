@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'w-full px-3 py-2 rounded-[6px] border border-hairline-strong bg-surface text-ink text-sm transition-colors placeholder:text-ink-muted']) }}>
