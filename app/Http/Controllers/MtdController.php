<?php

namespace App\Http\Controllers;

use App\Services\MtdService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MtdController extends Controller
{
    /**
     * @var MtdService
     */
    protected MtdService $service;

    /**
     * Construct the Controller
     */
    public function __construct()
    {
        $this->service = new MtdService();
    }

    /**
     * Render the message view
     * 
     * @param Request
     * @return Response
     */
    public function message(Request $request)
    {
        return view('pages.message')->with('title', $request->get('title'))->with('message', $request->get('message'));
    }

    /**
     * Render the error view
     * 
     * @param Request
     * @return Response
     */
    public function error(Request $request)
    {
        return view('pages.error')
                ->with('error', $request->get('error', 'There was an error while trying to process your request.'))
                ->with('messages', $request->get('messages', null));
    }

    /**
     * Handle the OAuth redirect response from MTD
     * 
     * @param Request
     * @return Response
     */
    public function handleRedirect(Request $request)
    {
        return $this->service->processOAuthRedirect($request);
    }

    /**
     * Retrieve a list of
     * - obligations
     * - payments
     * - liabilities
     * 
     * @param Request
     * @return Response
     */
    public function getTaxData(Request $request)
    {
        if (!$request->has('action')) {
            return redirect()->route('error', ['error' => 'No action has been specified.']);
        }

        $allowedActions = ['Payments', 'Liabilities', 'Obligations'];

        if (!in_array($request->get('action'), $allowedActions)) {
            return redirect()->route('error', ['error' => 'Invalid action has been specified.']);
        }

        return $this->service->getTaxInfoByPeriod($request->fromDate, $request->toDate, strtolower($request->get('action')));
    }

    /**
     * Get a VAT return details by period key
     * 
     * @param Request $request
     * @return Response
     */
    public function getVATReturn(Request $request)
    {
        if (!$request->has('action')) {
            return redirect()->route('error', ['error' => 'No action has been specified.']);
        }

        $allowedActions = ['View'];

        if (!in_array($request->get('action'), $allowedActions)) {
            return redirect()->route('error', ['error' => 'Invalid action has been specified.']);
        }

        return $this->service->getVATReturnByPeriodKey($request->viewPeriodKey);
    }

    /**
     * Display VAT return info for user's confirmation.
     * 
     * @param Request $request
     * @return Response
     */
    public function confirmReturn(Request $request)
    {
        return view('pages.confirmation')->with('data', [
            'periodKey' => $request->get('key', ''),
            'vatDueSales' => $request->get('vatDueSales', ''),
            'vatDueAcquisitions' => $request->get('vatDueAcquisitions', ''),
            'totalVatDue' => $request->get('totalVatDue', ''),
            'vatReclaimedCurrPeriod' => $request->get('vatReclaimedCurrPeriod', ''),
            'netVatDue' => $request->get('netVatDue', ''),
            'totalValueSalesExVAT' => $request->get('totalValueGoodsSuppliedExVAT', ''),
            'totalValuePurchasesExVAT' => $request->get('totalAcquisitionsExVAT', ''),
            'totalValueGoodsSuppliedExVAT' => $request->get('totalValueSalesExVAT', ''),
            'totalAcquisitionsExVAT' => $request->get('totalValuePurchasesExVAT', ''),
            'finalised' => $request->get('finalised', ''), 
        ]);
    }

    /**
     * Submit a VAT return for a period key
     * 
     * @param Request $request
     * @return Response
     */
    public function submitReturn(Request $request)
    {
        /**
         * Validate the input before processing.
         */
        $validatedData = $request->validate(
            [
                'periodKey' => ['required', 'string', 'max:6'],
                'vatDueSales' => ['required', 'numeric'],
                'vatDueAcquisitions' => ['required', 'numeric'],
                'totalVatDue' => ['required', 'numeric'],
                'vatReclaimedCurrPeriod' => ['required', 'numeric'],
                'netVatDue' => ['required', 'numeric'],
                'totalValueSalesExVAT' => ['required', 'numeric'],
                'totalValuePurchasesExVAT' => ['required', 'numeric'],
                'totalValueGoodsSuppliedExVAT' => ['required', 'numeric'],
                'totalAcquisitionsExVAT' => ['required', 'numeric'],
                'finalised' => ['required', 'string'],
            ],
            [
                // custom validation messages
            ],
            [
                'periodKey' => 'period key',
                'vatDueSales' => 'VAT due sales',
                'vatDueAcquisitions' => 'VAT due acquisitions',
                'totalVatDue' => 'total VAT due',
                'vatReclaimedCurrPeriod' => 'VAT reclaimed for current period',
                'netVatDue' => 'net VAT due',
                'totalValueSalesExVAT' => 'total value of sales before VAT',
                'totalValuePurchasesExVAT' => 'total value of purchase before VAT',
                'totalValueGoodsSuppliedExVAT' => 'total value of goods supplied before VAT',
                'totalAcquisitionsExVAT' => 'total acquisitions before VAT',
                'finalised' => 'finalised',
            ]
        );

        /**
         * If the validation passes, replace the value of
         * 'finalised' with a boolean
         */
        $validatedData['finalised'] = $validatedData['finalised'] == 'true' ? true : false;

        return $this->service->submitVATReturn($validatedData);
    }
}
