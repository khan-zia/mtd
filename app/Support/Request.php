<?php

namespace App\Support;

use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\App;
use Psr\Http\Message\ResponseInterface;

trait Request
{
    /**
     * @var Client
     */
    protected Client $http;

    /**
     * GET request
     * 
     * @param string $queryPath
     * @param array $queryParams
     * @param array $headers
     * @return mixed Response object as array
     */
    private function get(string $queryPath = '', array $queryParams = [], array $headers = [])
    {
        $response = $this->http->get(
            $this->getResource('base_api').$queryPath,
            [
                'query' => $queryParams,
                'headers' => $headers,
            ]
        );

        if ($content = $this->getResponseContent($response)) return $content;

        $this->handleFailedResponse($response);
    }

    /**
     * POST request
     * 
     * @param string $postPath
     * @param array $postData
     * @param array $headers
     * @return mixed Response object as array
     */
    private function post(string $postPath = '', array $postData = [], array $headers = [])
    {
        $response = $this->http->post(
            $this->getResource('base_api').$postPath,
            [
                'headers' => $headers,
                'body' => json_encode($postData),
            ]
        );

        if ($content = $this->getResponseContent($response)) return $content;

        // Check if there was a user friendly error message returned.
        $body = json_decode($response->getBody(), true);
        if(isset($body['message'])) {
            return $body;
        }

        $this->handleFailedResponse($response);
    }

    /**
     * Returns a URL for getting an Auth token.
     * 
     * @return string The Auth query URL
     */
    public function getAuthQueryURL(): string
    {
        $query = [
            'client_id' => $this->getResource('client_id'),
            'response_type' => 'code',
            'redirect_uri' => config('app.redirect_url'),
            'scope' => 'write:vat read:vat',
            'state' => config('app.app_state'),
        ];

        $authQuery = http_build_query($query);

        return $this->getResource('auth_url') . '?' . $authQuery;
    }

    /**
     * Get an Auth token by exchanging Auth code
     * with HMRC
     * 
     * @param string $code The Auth code
     * @return array The Auth tokens
     */
    public function getAuthToken(string $code)
    {
        $response = $this->http->post($this->getResource('token_url'), [
            'form_params' => [
                'grant_type' => 'authorization_code',
                'client_id' => $this->getResource('client_id'),
                'client_secret' => $this->getResource('client_secret'),
                'redirect_uri' => config('app.redirect_url'),
                'code' => $code,
            ]
        ]);
    
        if ($content = $this->getResponseContent($response)) return $content;

        $this->handleFailedResponse($response);
    }

    /**
     * Use a refresh token to get a new user access token.
     * 
     * @param string|null $refreshToken The existing refresh token
     * @return array The Auth token
     */
    public function refreshAuthToken(?string $refreshToken)
    {
        $response = $this->http->post($this->getResource('token_url'), [
            'form_params' => [
                'grant_type' => 'refresh_token',
                'client_id' => $this->getResource('client_id'),
                'client_secret' => $this->getResource('client_secret'),
                'refresh_token' => $refreshToken,
            ]
        ]);

        if ($content = $this->getResponseContent($response)) return $content;

        $this->handleFailedResponse($response);
    }

    /**
     * Determine if an HTTP request was successful.
     * Strictly ensures the status code was 2xx
     * 
     * @param ResponseInterface $response
     * @return mixed Response array or null
     */
    protected function getResponseContent(ResponseInterface $response)
    {
        if ((int) $response->getStatusCode() >= 200  && (int) $response->getStatusCode() < 300) {
            $content = json_decode((string) $response->getBody(), true);

            return $content;
        }

        return null;
    }

    /**
     * Handle an unsuccessful HTTP request
     * 
     * @param ResponseInterface $response
     * @throws Exception
     */
    protected function handleFailedResponse(ResponseInterface $response)
    {

        if ((int) $response->getStatusCode() === 400) {
            throw new Exception("HMRC rejected the request as '{$response->getReasonPhrase()}'. It did not return more details. Perhaps the data you sent for processing is invalid.");
        }

        if ((int) $response->getStatusCode() === 401) {
            throw new Exception(401);
        }

        if ((int) $response->getStatusCode() === 403) {
            throw new Exception("You are not authorized at HMRC to perform this action.");
        }

        if ((int) $response->getStatusCode() === 404) {
            throw new Exception("HMRC has indicated that the requested resource/data can not be found for the specified parameters.");
        }

        if ((int) $response->getStatusCode() >= 500) {
            throw new Exception("There's a technical issue with the HMRC servers. This app could not establish a successful connection.");
        }
    }

    /**
     * Get an app related resource based on the app's
     * env i.e. test or production
     * 
     * @param string $resource The resource ENV identifier.
     * @return string
     */
    protected function getResource(string $resource): string
    {
        /** @var string Resource key */
        $key = App::environment('production') ? $resource : 'test_'.$resource;

        return config('app.'.$key);
    }
}