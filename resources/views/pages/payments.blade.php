@extends('layouts.app')

@push('page-title')
<title>Payments - MTD App</title>
@endpush

@section('content')
<div class="container">
    <!-- View Payments -->
    <div class="mt-4 bg-gray-100 rounded p-4">
        <p class="title-line mb-2">
            Payments for the selected period
        </p>

        @if(count($payments) > 0)
                @foreach($payments as $payment)
                    <table class="mt-2 table-auto text-gray-700">
                        @foreach($payment as $key => $value)
                                <tr class="border border-gray-500">
                                    <td class="px-4 py-2">
                                        {{ ucfirst($key) }}
                                    </td>
                                    <td class="px-4 py-2">
                                        <strong>{{ $value }}</strong>
                                    </td>
                                </tr>
                        @endforeach
                    </table>
                @endforeach
        @else
            <div class="text-gray-500">No payments found.</div>
        @endif
    </div>
</div>
@endsection