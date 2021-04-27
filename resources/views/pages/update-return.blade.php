@extends('layouts.app')

@push('page-title')
<title>Submitted VAT Return - MTD App</title>
@endpush

@section('content')
<div class="container">
    <!-- View VAT return -->
    @if(isset($return))
        <div class="mt-4 bg-gray-100 rounded p-4">
            <p class="title-line mb-2">
                VAT return submitted to HMRC with the following details.
            </p>
            <table class="table-auto text-gray-700">
                <tr>
                    <td class="py-3 pr-6">
                        Processing Date:
                    </td>
                    <td>
                        <strong>{{ $return['processingDate'] }}</strong>
                    </td>
                </tr>
                
                <tr>
                    <td class="py-3 pr-6">
                        Form Bundle Number:
                    </td>
                    <td>
                        <strong>{{ $return['formBundleNumber'] }}</strong>
                    </td>
                </tr>

                <tr>
                    <td class="py-3 pr-6">
                        Payment Indicator:
                    </td>
                    <td>
                        @if(isset($return['paymentIndicator']))
                            <strong>{{ $return['paymentIndicator'] }}</strong>
                        @endif
                    </td>
                </tr>

                <tr>
                    <td class="py-3 pr-6">
                        Charge Reference Number:
                    </td>
                    <td>
                        @if(isset($return['chargeRefNumber']))
                            <strong>{{ $return['chargeRefNumber'] }}</strong>
                        @endif
                    </td>
                </tr>
            </table>
        </div>
    @endif
</div>
@endsection