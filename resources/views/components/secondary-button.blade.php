<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center justify-center min-h-[40px] px-4 py-2 bg-surface hover:bg-surface-subtle text-ink border border-hairline-strong rounded-[6px] font-semibold text-sm transition-colors disabled:opacity-50']) }}>
    {{ $slot }}
</button>
