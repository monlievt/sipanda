<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center min-h-[40px] px-4 py-2 bg-red-700 hover:bg-red-800 active:bg-red-900 text-white border border-transparent rounded-[6px] font-semibold text-sm transition-colors disabled:opacity-50']) }}>
    {{ $slot }}
</button>
