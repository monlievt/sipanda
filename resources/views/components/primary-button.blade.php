<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center min-h-[40px] px-4 py-2 bg-brand hover:bg-brand-hover active:bg-emerald-900 text-white font-semibold text-sm rounded-[6px] border border-transparent transition-colors disabled:opacity-50']) }}>
    {{ $slot }}
</button>
