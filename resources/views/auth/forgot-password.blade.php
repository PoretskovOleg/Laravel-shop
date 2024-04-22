@extends('layouts.auth')

@section('title', 'Забыли пароль')

@section('content')
    <x-form.auth title="Забыли пароль" action="{{ route('password.forgot') }}" method="POST">
        @csrf

        <x-form.text-input
                name="email"
                type="email"
                placeholder="E-mail"
                required="true"
                :isError="$errors->has('email')"
        />
        @error('email')
        <x-form.error>{{ $message }}</x-form.error>
        @enderror

        <x-form.primary-button type="submit">
            Отправить
        </x-form.primary-button>

        <x-slot:buttons>
            <div class="space-y-3 mt-5">
                <div class="text-xxs md:text-xs">
                    <a href="{{ route('login') }}" class="text-white hover:text-white/70 font-bold">Войти в аккаунт</a>
                </div>
            </div>
        </x-slot:buttons>

    </x-form.auth>
@endsection
