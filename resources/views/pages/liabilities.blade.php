@extends('layouts.app')

@push('page-title')
<title>Liabilities - MTD App</title>
@endpush

@section('content')
<div class="container">
    <!-- View Liabilities -->
    <div class="mt-4 bg-gray-100 rounded p-4">
        <p class="title-line mb-2">
            Liabilities for the selected period
        </p>

        @if(count($liabilities) > 0)
                @foreach($liabilities as $liability)
                    <table class="mt-2 table-auto text-gray-700">
                        @foreach($liability as $key => $value)
                            <tr class="border border-gray-500">
                                <td class="px-4 py-2">
                                    {{ ucfirst($key) }}
                                </td>
                                <td class="px-4 py-2">
                                    @if(is_array($value))
                                        @foreach($value as $key => $val)
                                            {{ ucfirst($key) }}: <strong>{{ $val }}</strong>
                                            <br />
                                        @endforeach
                                    @else
                                        <strong>{{ $value }}</strong>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </table>
                @endforeach
        @else
            <div class="text-gray-500">No liabilities found.</div>
        @endif
    </div>
</div>
@endsection