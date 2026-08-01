@if (session('new_password'))
    <div class="mb-6 rounded-2xl border border-[#c7d9c0] bg-[#eef6ea] px-4 py-3 text-sm text-[#3f6a2c]">
        <p class="mb-2">{{ session('success') }} New password (shown once — copy it now):</p>
        <div class="flex items-center gap-2">
            <input
                id="new-password-field"
                type="password"
                readonly
                value="{{ session('new_password') }}"
                class="w-full max-w-xs rounded-xl border border-[#c7d9c0] bg-white px-3 py-2 font-mono text-sm text-[#1b1b18] outline-none"
            />
            <button
                type="button"
                onclick="const f = document.getElementById('new-password-field'); f.type = f.type === 'password' ? 'text' : 'password'; this.textContent = f.type === 'password' ? 'Show' : 'Hide';"
                class="rounded-full border border-[#c7d9c0] px-3 py-1.5 text-xs font-medium text-[#3f6a2c] hover:bg-white"
            >Show</button>
        </div>
    </div>
@elseif (session('success'))
    <div class="mb-6 rounded-2xl border border-[#c7d9c0] bg-[#eef6ea] px-4 py-3 text-sm text-[#3f6a2c]">{{ session('success') }}</div>
@endif
