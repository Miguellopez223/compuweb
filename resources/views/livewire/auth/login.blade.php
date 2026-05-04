<div class="w-full max-w-md">
    {{-- Logo / Brand --}}
    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-indigo-600 rounded-2xl mb-4 shadow-lg">
            <svg class="w-9 h-9 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3H5a2 2 0 00-2 2v4m6-6h10a2 2 0 012 2v4M9 3v18m0 0h10a2 2 0 002-2V9M9 21H5a2 2 0 01-2-2V9m0 0h18"/>
            </svg>
        </div>
        <h1 class="text-2xl font-bold text-white">CompuWeb System</h1>
        <p class="text-slate-400 text-sm mt-1">Gestión de Inventario y Ventas</p>
    </div>

    {{-- Card --}}
    <div class="bg-white rounded-2xl shadow-2xl p-8">
        <h2 class="text-lg font-semibold text-slate-800 mb-6">Iniciar Sesión</h2>

        <form wire:submit="login" class="space-y-5">
            {{-- Email --}}
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Correo electrónico</label>
                <input wire:model="email" type="email" autocomplete="email"
                       placeholder="correo@ejemplo.com"
                       class="w-full px-4 py-2.5 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition
                              {{ $errors->has('email') ? 'border-red-400 bg-red-50' : 'border-slate-300' }}">
                @error('email')
                    <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Password --}}
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Contraseña</label>
                <input wire:model="password" type="password" autocomplete="current-password"
                       placeholder="••••••••"
                       class="w-full px-4 py-2.5 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition
                              {{ $errors->has('password') ? 'border-red-400 bg-red-50' : 'border-slate-300' }}">
                @error('password')
                    <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Remember --}}
            <div class="flex items-center gap-2">
                <input wire:model="remember" id="remember" type="checkbox"
                       class="w-4 h-4 text-indigo-600 border-slate-300 rounded">
                <label for="remember" class="text-sm text-slate-600">Mantener sesión iniciada</label>
            </div>

            {{-- Submit --}}
            <button type="submit"
                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2.5 rounded-lg transition text-sm flex items-center justify-center gap-2">
                <span wire:loading.remove wire:target="login">Ingresar al sistema</span>
                <span wire:loading wire:target="login" class="flex items-center gap-2">
                    <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                    Verificando...
                </span>
            </button>
        </form>
    </div>
</div>
