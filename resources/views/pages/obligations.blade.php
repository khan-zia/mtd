@extends('layouts.app')

@push('page-title')
<title>Obligations - MTD App</title>
@endpush

@section('content')
<div class="container">
    <!-- View obligations -->
    <div class="mt-4 bg-gray-100 rounded p-4">
        <p class="title-line mb-2">
            Obligations for the selected period
        </p>

        <h3 class="text-md mt-4">Open obligations</h3>
        @if(count($open) > 0)
            <table class="mt-2 table-auto text-gray-700">
                @foreach($open as $obligation)
                    @foreach($obligation as $key => $value)
                        <tr class="border border-gray-500">
                            <td class="px-4 py-2">
                                {{ ucfirst($key) }}
                            </td>
                            <td class="px-4 py-2">
                                <strong>{{ $value }}</strong>
                            </td>
                        </tr>
                    @endforeach
                @endforeach
            </table>
        @else
            <div class="text-gray-500">No open obligations.</div>
        @endif

        <h3 class="text-md mt-6">Fulfilled obligations</h3>
        @if(count($fulfilled) > 0)
            <table class="mt-2 table-auto text-gray-700">
                @foreach($fulfilled as $obligation)
                    @foreach($obligation as $key => $value)
                        <tr class="border border-gray-500">
                            <td class="px-4 py-2">
                                {{ $key }}
                            </td>
                            <td class="px-4 py-2">
                                <strong>{{ $value }}</strong>
                            </td>
                        </tr>
                    @endforeach
                @endforeach
            </table>
        @else
            <div class="text-gray-500">No fulfilled obligations.</div>
        @endif
    </div>
</div>
@endsection