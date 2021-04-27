@extends('layouts.app')

@push('page-title')
<title>River Solutions - MTD App</title>
@endpush

@section('content')
<div class="container">
    <div class="mt-4 bg-green-100 text-green-600 text-lg rounded p-4">
        {{ $title }}
    </div>
    @if(isset($message))
        <div class="mt-2">
            <p> {{ $message }} </p>
        </div>
    @endif
</div>
@endsection