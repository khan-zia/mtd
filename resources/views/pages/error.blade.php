@extends('layouts.app')

@push('page-title')
<title>Opps! There was an error - MTD App</title>
@endpush

@section('content')
<div class="container">
    <div class="mt-4 bg-red-100 text-red-600 text-lg rounded p-4">
        {{ $error }}
    </div>
    @if(isset($messages))
        <div class="mt-3 text-red-600">
            @foreach(json_decode($messages, true) as $message)
                <p>
                    - {{ $message }}
                </p>
            @endforeach
        </div>
    @endif
</div>
@endsection