@extends('layouts.admin')
@section('title', 'Mensaje de contacto')
@section('content')
<div class="max-w-2xl space-y-5">
    <div class="bg-white border border-stone-100 shadow-sm p-6 space-y-4">
        <div class="flex justify-between items-start border-b border-stone-100 pb-4">
            <div>
                <p class="font-medium text-stone-700 text-lg">{{ $message->name }}</p>
                <p class="text-sm text-stone-400">{{ $message->email }} @if($message->phone) · {{ $message->phone }} @endif</p>
            </div>
            <p class="text-xs text-stone-400">{{ $message->created_at->format('d/m/Y H:i') }}</p>
        </div>
        @if($message->subject)
        <div>
            <p class="text-xs text-stone-400 uppercase tracking-wider mb-1">Asunto</p>
            <p class="text-stone-700 font-medium">{{ $message->subject }}</p>
        </div>
        @endif
        <div>
            <p class="text-xs text-stone-400 uppercase tracking-wider mb-1">Mensaje</p>
            <p class="text-stone-600 whitespace-pre-wrap">{{ $message->message }}</p>
        </div>
    </div>
    <div class="flex gap-3">
        <a href="mailto:{{ $message->email }}?subject=Re: {{ $message->subject }}" class="btn-copper">Responder por email</a>
        <a href="{{ route('admin.contacts.index') }}" class="btn-stone">Volver</a>
    </div>
</div>
@endsection
