@extends('layouts.app')

@push('page-title')
<title>View VAT Returns - MTD App</title>
@endpush

@section('content')
<div class="container">
    <!-- View VAT return -->
    <div class="mt-4 bg-gray-100 rounded p-4">
        <p class="title-line mb-2">
            VAT Return Details
        </p>

        <table class="mt-2 table-auto text-gray-700">
            @foreach($return as $key => $value)
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
    </div>
</div>
@endsection