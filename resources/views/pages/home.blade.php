@extends('layouts.app')

@push('page-title')
<title>MTD App</title>
@endpush

@section('content')
<div class="container">
    <!-- Get payments, liabilities and obligations -->
    <div class="mt-4 bg-gray-100 rounded p-4">
        <p class="title-line">
            Get payments, liabilities and obligations. Specify a period to retrieve related information.
        </p>
        <form action="{{ route('get-tax-data') }}">
            @include('partials.headers')
            <div id="range" class="mt-4">
                <label for="fromDate" class="mr-2">From</label>
                <input class="input" type="text" placeholder="mm/dd/yyyy" name="fromDate">
                <label for="toDate" class="mx-2">To</label>
                <input class="input" type="text" placeholder="mm/dd/yyyy" name="toDate">
            </div>
            <div class="mt-2 flex">
                <input class="cursor-pointer btn btn-primary mr-2" type="submit" name="action" value="Payments" />
                <input class="cursor-pointer btn btn-primary mr-2" type="submit" name="action" value="Liabilities" />
                <input class="cursor-pointer btn btn-primary" type="submit" name="action" value="Obligations" />
            </div>
        </form>
    </div>

    <!-- View VAT returns -->
    <div class="mt-4 bg-gray-100 rounded p-4">
        <p class="title-line">
            View VAT returns by period.
        </p>
        <form action="{{ route('vat-return') }}">
            @include('partials.headers')
            <div class="mt-4">
                <label for="viewPeriodKey" class="mr-2">Period Key</label>
                <input class="input" type="text" placeholder="Period key" name="viewPeriodKey">
            </div>
            <div class="mt-2 flex">
                <input class="cursor-pointer btn btn-primary mr-2" type="submit" name="action" value="View" />
            </div>
        </form>
    </div>

    @if ($errors->any())
        <div class="my-4 bg-red-100 text-red-600 rounded p-4">
            <h1 class="text-md"><strong>Please fix the following errors with your submission and resubmit.</strong></h1>
            <ul class="mt-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Submit VAT returns -->
    <div class="mt-4 bg-gray-100 rounded p-4">
        <form action="{{ route('submit-return') }}" method="POST">
            @csrf
            @include('partials.headers')
            <table class="table-auto text-gray-700">
                <tr>
                    <td class="py-3 pr-6">
                        <label for="key">Period Key</label>
                    </td>
                    <td>
                        <input class="input w-80" type="text" placeholder="Period key" value="{{ old('periodKey') }}" name="periodKey">
                    </td>
                </tr>

                <tr>
                    <td class="py-3 pr-6">
                        <label for="vatDueSales">VAT Due (Sales)</label>
                    </td>
                    <td>
                        <input class="input w-80" type="text" placeholder="0.0" value="{{ old('vatDueSales') }}" name="vatDueSales">
                    </td>
                </tr>

                <tr>
                    <td class="py-3 pr-6">
                        <label for="vatDueAcquisitions">VAT Due (Acquisitions)</label>
                    </td>
                    <td>
                        <input class="input w-80" type="text" placeholder="0.0" value="{{ old('vatDueAcquisitions') }}" name="vatDueAcquisitions">
                    </td>
                </tr>

                <tr>
                    <td class="py-3 pr-6">
                        <label for="totalVatDue">Total VAT Due</label>
                    </td>
                    <td>
                        <input class="input w-80" type="text" placeholder="0.0" value="{{ old('totalVatDue') }}" name="totalVatDue">
                    </td>
                </tr>

                <tr>
                    <td class="py-3 pr-6">
                        <label for="vatReclaimedCurrPeriod">VAT Reclaimed (Current Period)</label>
                    </td>
                    <td>
                        <input class="input w-80" type="text" placeholder="0.0" value="{{ old('vatReclaimedCurrPeriod') }}" name="vatReclaimedCurrPeriod">
                    </td>
                </tr>

                <tr>
                    <td class="py-3 pr-6">
                        <label for="netVatDue">Net VAT Due</label>
                    </td>
                    <td>
                        <input class="input w-80" type="text" placeholder="0.0" value="{{ old('netVatDue') }}" name="netVatDue">
                    </td>
                </tr>

                <tr>
                    <td class="py-3 pr-6">
                        <label for="totalValueSalesExVAT">Total Value Sales (Ex VAT)</label>
                    </td>
                    <td>
                        <input class="input w-80" type="text" placeholder="0.0" value="{{ old('totalValueSalesExVAT') }}" name="totalValueSalesExVAT">
                    </td>
                </tr>

                <tr>
                    <td class="py-3 pr-6">
                        <label for="totalValuePurchasesExVAT">Total Value Purchases (Ex VAT)</label>
                    </td>
                    <td>
                        <input class="input w-80" type="text" placeholder="0.0" value="{{ old('totalValuePurchasesExVAT') }}" name="totalValuePurchasesExVAT">
                    </td>
                </tr>

                <tr>
                    <td class="py-3 pr-6">
                        <label for="totalValueGoodsSuppliedExVAT">Total Value Goods Supplied (Ex VAT)</label>
                    </td>
                    <td>
                        <input class="input w-80" type="text" placeholder="0.0" value="{{ old('totalValueGoodsSuppliedExVAT') }}" name="totalValueGoodsSuppliedExVAT">
                    </td>
                </tr>

                <tr>
                    <td class="py-3 pr-6">
                        <label for="totalAcquisitionsExVAT">Total Acquisitions (Ex VAT)</label>
                    </td>
                    <td>
                        <input class="input w-80" type="text" placeholder="0.0" value="{{ old('totalAcquisitionsExVAT') }}" name="totalAcquisitionsExVAT">
                    </td>
                </tr>

                <tr>
                    <td class="py-3 pr-6">
                        <label for="finalized">Is Finalized?</label>
                    </td>
                    <td>
                        <input class="input w-80" type="text" placeholder="true/false" value="{{ old('finalised') }}" name="finalised">
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