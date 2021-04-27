<?php

namespace App\Services;

use App\Support\HmrcAuth;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class MtdService
{
    use HmrcAuth;

    /**
     * Initialize the service.
     * 
     * @return void
     */
    public function __construct()
    {
        $this->http = new Client([
            'http_errors' => false
        ]);
    }

    /**
     * Process and attempt to retrieve
     * - Payments
     * - Obligations
     * - Liabilities
     * 
     * @param string $fromDate
     * @param string $toDate
     * @param string $action
     * @return Response
     */
    public function getTaxInfoByPeriod(?string $fromDate, ?string $toDate, string $action)
    {
        // Check headers
        $hmrcHeaders = $this->getHeaders();
        if (!$hmrcHeaders) {
            return redirect()->route('error', ['error' => 'Fraud detection headers required by HMRC are not set.']);
        }

        // If either of the dates is not specified
        if(empty($fromDate) || empty($toDate)) {
            return redirect()->route('error', ['error' => 'Please specify a period.']);
        }

        $path = 'organisations/vat/'. $this->getResource('vrn') .'/'.$action;

        try {
            $response = $this->get(
                $path,
                [
                    'from' => Carbon::createFromFormat('m/d/Y', $fromDate)->format('Y-m-d'), //'2017-01-25',
                    'to' => Carbon::createFromFormat('m/d/Y', $toDate)->format('Y-m-d'), //'2018-01-25',
                ],
                array_merge(
                    [
                        'Accept' => 'application/vnd.hmrc.1.0+json',
                        'Authorization' => 'Bearer '.$this->getUserToken(),
                        // 'Gov-Test-Scenario' => 'MULTIPLE_PAYMENTS_2018_19',
                        // 'Gov-Test-Scenario' => 'MULTIPLE_LIABILITIES_2018_19',
                    ],
                    $hmrcHeaders
                )
            );

            // Return view based on the action/requested data
            switch($action) {
                case 'payments':        
                    return view('pages.payments')->with('payments', $response['payments']);
                break;
                case 'liabilities':
                    return view('pages.liabilities')->with('liabilities', $response['liabilities']);
                break;
                case 'obligations':
                    $open = [];
                    $fulfilled = [];
        
                    foreach ($response['obligations'] as $obligation) {
                        if ($obligation['status'] == 'O') {
                            $open[] = $obligation;
                        } else {
                            $fulfilled[] = $obligation;
                        }
                    }
        
                    return view('pages.obligations')->with('open', $open)->with('fulfilled', $fulfilled);
                break;
            }
        } catch(Exception $e) {
            if ((int) $e->getMessage() === 401) {
                Log::debug("401 received...");
                /**
                 * Because a 401 is returned, the user token is either expired or
                 * invalid. First attempt to refresh the user token if a valid
                 * refresh token exist.
                 */
                return $this->refreshUserToken();
            } else {
                Log::debug('error with getting tax data by period ...');
                // otherwise, display the exception message
                return redirect()->route('error', ['error' => $e->getMessage()]);
            }
        }
    }

    /**
     * Process and attempt to retrieve details of a VAT return
     * by a specified period key.
     * 
     * @param string $periodKey
     * @return Response
     */
    public function getVATReturnByPeriodKey(?string $periodKey)
    {
        // Check headers
        $hmrcHeaders = $this->getHeaders();
        if (!$hmrcHeaders) {
            return redirect()->route('error', ['error' => 'Fraud detection headers required by HMRC are not set.']);
        }

        if (empty($periodKey)) {
            return redirect()->route('error', ['error' => 'Please specify a period key.']);
        }

        $path = 'organisations/vat/'. $this->getResource('vrn') .'/returns/'.$periodKey;

        try {
            $response = $this->get(
                $path,
                [],
                array_merge(
                    [
                        'Accept'     => 'application/vnd.hmrc.1.0+json',
                        'Authorization' => 'Bearer '.$this->getUserToken(),
                        // 'Gov-Test-Scenario' => '',
                    ],
                    $hmrcHeaders
                )
            );

            return view('pages.vat-return')->with('return', $response);
        } catch(Exception $e) {
            if ((int) $e->getMessage() === 401) {
                Log::debug("401 received...");
                /**
                 * Because a 401 is returned, the user token is either expired or
                 * invalid. First attempt to refresh the user token if a valid
                 * refresh token exist.
                 */
                return $this->refreshUserToken();
            } else {
                Log::debug('error with details of VAT return...');
                // otherwise, display the exception message
                return redirect()->route('error', ['error' => $e->getMessage()]);
            }
        }
    }

    /**
     * Attempt to submit a VAT return to the HMRC
     * 
     * @param array $data
     * @return Response
     */
    public function submitVATReturn(array $data)
    {
        // Check headers
        $hmrcHeaders = $this->getHeaders();
        if (!$hmrcHeaders) {
            return redirect()->route('error', ['error' => 'Fraud detection headers required by HMRC are not set.']);
        }

        $path = 'organisations/vat/'. $this->getResource('vrn') .'/returns';

        try {
            $response = $this->post(
                $path,
                $data,
                array_merge(
                    [
                        'Accept'     => 'application/vnd.hmrc.1.0+json',
                        'Content-Type' => 'application/json',
                        'Authorization' => 'Bearer '.$this->getUserToken(),
                        // 'Gov-Test-Scenario' => 'INSOLVENT_TRADER',
                    ],
                    $hmrcHeaders
                )
            );

            if (isset($response['message'])) {
                $messages = [];

                if(isset($response['errors'])) {
                    foreach($response['errors'] as $error) {
                        $messages[] = $error['message'];
                    }
                }

                return redirect()->route('error', 
                    [
                        'error' => $response['message'],
                        'messages' => json_encode($messages)
                    ]
                );
            }

            /**
             * Parse and format the processing date
             */
            $date = Carbon::parse($response['processingDate']);
            $response['processingDate'] = $date->format('d F Y g:i a');

            return view('pages.update-return')->with('return', $response);
        } catch(Exception $e) {
            if ((int) $e->getMessage() === 401) {
                Log::debug("401 received...");
                /**
                 * Because a 401 is returned, the user token is either expired or
                 * invalid. First attempt to refresh the user token if a valid
                 * refresh token exist.
                 */
                return $this->refreshUserToken();
            } else {
                Log::debug('error with submitting of VAT return...');
                // otherwise, display the exception message
                return redirect()->route('error', ['error' => $e->getMessage()]);
            }
        }
    }

    /**
     * Collect HMRC fraud detection headers
     * 
     * @return array|null array of headers or null
     */
    private function getHeaders()
    {
        if (!request()->has('headers')) {
            return null;
        }

        $submitted = request('headers');
        $headers = [];

        foreach ($submitted as $header => $value) {
            if ($value === null) {
                $headers[$header] = '';
                continue;
            }
            
            $headers[$header] = $value;
        }

        return $headers;
    }
}