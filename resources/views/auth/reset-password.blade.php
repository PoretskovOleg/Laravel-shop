@extends('layouts.auth')

@section('title', 'Восстановление пароля')

@section('content')
    <x-form.auth title="Восстановление пароля" action="{{ route('password.update') }}" method="POST">
        @csrf

        <input type="hidden" name="token" value="{{ $token }}">

        <x-form.text-input
                name="email"
                type="email"
                placeholder="E-mail"
                required="true"
                value="{{ request('email') }}"
                :isError="$errors->has('email')"
        />
        @error('email')
        <x-form.error>{{ $message }}</x-form.error>
        @enderror

        <x-form.text-input
                name="password"
                type="password"
                placeholder="Пароль"
                required="true"
                :isError="$errors->has('password')"
        />
        @error('password')
        <x-form.error>{{ $message }}</x-form.error>
        @enderror

        <x-form.text-input
                name="password_confirmation"
                type="password"
                placeholder="Повторите пароль"
                required="true"
                :isError="$errors->has('password_confirmation')"
        />
        @error('password_confirmation')
        <x-form.error>{{ $message }}</x-form.error>
        @enderror

        <x-form.primary-button type="submit">
            Обновить пароль
        </x-form.primary-button>

    </x-form.auth>
@endsection
