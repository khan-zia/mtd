@extends('layouts.app')

@push('page-title')
<title>Confirm VAT return details - MTD App</title>
@endpush

@section('content')
<div class="container">
    @if ($errors->any())
        <div class="my-4 bg-red-100 text-red-600 rounded p-4">
            <h1 class="text-md"><strong>The external resource sent the VAT return details with the following errors. You must fix
            these errors before it can be submitted to HMRC.</strong></h1>
            <ul class="mt-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Submit VAT return -->
    <div class="mt-4 bg-gray-100 rounded p-4">
        <p class="title-line mb-2">
        Please Verify the following VAT return details before submitting.
        </p>
        <form action="{{ route('submit-return') }}" method="POST">
            @csrf
            @include('partials.headers')
            <table class="table-auto text-gray-700">
                <tr>
                    <td class="py-3 pr-6">
                        <label for="key">Period Key</label>
                    </td>
                    <td>
                        <input class="input w-80" type="text" placeholder="Period key" value="{{ $data['periodKey'] }}" name="periodKey">
                    </td>
                </tr>

                <tr>
                    <td class="py-3 pr-6">
                        <label for="vatDueSales">VAT Due (Sales)</label>
                    </td>
                    <td>
                        <input class="input w-80" type="text" placeholder="0.0" value="{{ $data['vatDueSales'] }}" name="vatDueSales">
                    </td>
                </tr>

                <tr>
                    <td class="py-3 pr-6">
                        <label for="vatDueAcquisitions">VAT Due (Acquisitions)</label>
                    </td>
                    <td>
                        <input class="input w-80" type="text" placeholder="0.0" value="{{ $data['vatDueAcquisitions'] }}" name="vatDueAcquisitions">
                    </td>
                </tr>

                <tr>
                    <td class="py-3 pr-6">
                        <label for="totalVatDue">Total VAT Due</label>
                    </td>
                    <td>
                        <input class="input w-80" type="text" placeholder="0.0" value="{{ $data['totalVatDue'] }}" name="totalVatDue">
                    </td>
                </tr>

                <tr>
                    <td class="py-3 pr-6">
                        <label for="vatReclaimedCurrPeriod">VAT Reclaimed (Current Period)</label>
                    </td>
                    <td>
                        <input class="input w-80" type="text" placeholder="0.0" value="{{ $data['vatReclaimedCurrPeriod'] }}" name="vatReclaimedCurrPeriod">
                    </td>
                </tr>

                <tr>
                    <td class="py-3 pr-6">
                        <label for="netVatDue">Net VAT Due</label>
                    </td>
                    <td>
                        <input class="input w-80" type="text" placeholder="0.0" value="{{ $data['netVatDue'] }}" name="netVatDue">
                    </td>
                </tr>

                <tr>
                    <td class="py-3 pr-6">
                        <label for="totalValueSalesExVAT">Total Value Sales (Ex VAT)</label>
                    </td>
                    <td>
                        <input class="input w-80" type="text" placeholder="0.0" value="{{ $data['totalValueSalesExVAT'] }}" name="totalValueSalesExVAT">
                    </td>
                </tr>

                <tr>
                    <td class="py-3 pr-6">
                        <label for="totalValuePurchasesExVAT">Total Value Purchases (Ex VAT)</label>
                    </td>
                    <td>
                        <input class="input w-80" type="text" placeholder="0.0" value="{{ $data['totalValuePurchasesExVAT'] }}" name="totalValuePurchasesExVAT">
                    </td>
                </tr>

                <tr>
                    <td class="py-3 pr-6">
                        <label for="totalValueGoodsSuppliedExVAT">Total Value Goods Supplied (Ex VAT)</label>
                    </td>
                    <td>
                        <input class="input w-80" type="text" placeholder="0.0" value="{{ $data['totalValueGoodsSuppliedExVAT'] }}" name="totalValueGoodsSuppliedExVAT">
                    </td>
                </tr>

                <tr>
                    <td class="py-3 pr-6">
                        <label for="totalAcquisitionsExVAT">Total Acquisitions (Ex VAT)</label>
                    </td>
                    <td>
                        <input class="input w-80" type="text" placeholder="0.0" value="{{ $data['totalAcquisitionsExVAT'] }}" name="totalAcquisitionsExVAT">
                    </td>
                </tr>

                <tr>
                    <td class="py-3 pr-6">
                        <label for="finalized">Is Finalized?</label>
                    </td>
                    <td>
                        <input class="input w-80" type="text" placeholder="true/false" value="{{ $data['finalised'] }}" name="finalised">
                    </td>
                </tr>
            </table>
            <div class="mt-2 flex">
                <input class="cursor-pointer btn btn-default mr-2" type="reset" value="Reset" />
                <input class="cursor-pointer btn btn-primary" type="submit" value="Submit">
            </div>
        </form>
    </div>
</div>
@endsection