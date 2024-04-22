@extends('layouts.auth')

@section('title', 'Подтверждение e-mail')

@section('content')
    <x-form.auth title="Необходимо подтвердить e-mail" action="{{ route('verification.send') }}" method="POST">
        @csrf

        <x-form.primary-button type="submit">
            Отправить письмо еще раз
        </x-form.primary-button>

    </x-form.auth>
@endsection
