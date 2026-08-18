@extends('layouts.app')

@section('title', 'Log in — FounderOS')

@section('content')
<div class="flex min-h-screen items-center justify-center px-4">
    <div class="w-full max-w-sm rounded-[12px] border border-gray-200 bg-white p-8 shadow-sm">
        <h1 class="mb-6 text-[14px] font-medium text-[#1F2937]">FounderOS</h1>

        @if ($errors->any())
            <div class="mb-4 rounded-[8px] bg-red-50 p-3 text-[12px] text-red-700">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            <div>
                <label for="identifier" class="block text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Username</label>
                <input
                    id="identifier"
                    name="identifier"
                    type="text"
                    value="{{ old('identifier') }}"
                    required
                    autofocus
                    class="mt-1 block w-full rounded-[8px] border border-gray-300 px-3 py-2 text-[12px] focus:border-[#1D9E75] focus:outline-none focus:ring-1 focus:ring-[#1D9E75]"
                >
            </div>

            <div>
                <label for="password" class="block text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Password</label>
                <input
                    id="password"
                    name="password"
                    type="password"
                    required
                    class="mt-1 block w-full rounded-[8px] border border-gray-300 px-3 py-2 text-[12px] focus:border-[#1D9E75] focus:outline-none focus:ring-1 focus:ring-[#1D9E75]"
                >
                <label class="mt-2 flex items-center gap-2 text-[11px] text-gray-600">
                    <input
                        type="checkbox"
                        onclick="document.getElementById('password').type = this.checked ? 'text' : 'password'"
                        class="rounded border-gray-300"
                    >
                    Show password
                </label>
            </div>

            <button
                type="submit"
                class="w-full rounded-[8px] bg-[#1D9E75] px-4 py-2 text-[12px] font-medium text-white hover:bg-[#0F6E56]"
            >
                Log in
            </button>
        </form>

        <div class="my-4 flex items-center gap-3">
            <div class="h-px flex-1 bg-gray-200"></div>
            <span class="text-[10px] text-gray-400">or</span>
            <div class="h-px flex-1 bg-gray-200"></div>
        </div>

        <a
            href="{{ route('google.redirect') }}"
            class="flex w-full items-center justify-center gap-2 rounded-[8px] border border-gray-300 px-4 py-2 text-[12px] font-medium text-gray-700 hover:bg-gray-50"
        >
            Sign in with Google
        </a>
    </div>
</div>
@endsection
