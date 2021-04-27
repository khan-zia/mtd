<?php

namespace App\Support;

use App\Models\Token;
use Exception;
use Illuminate\Http\Request as HTTPRequest;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

trait HmrcAuth
{
    use Request;

    /**
     * Handle and process OAuth redirect
     * 
     * @param HTTPRequest $request
     * @return mixed
     */
    public function processOAuthRedirect(HTTPRequest $request)
    {
        if ($request->has('error')) {
            return redirect()->route('error', ['error' => $request->error_description]);
        }

        if (!$request->state) {
            return redirect()->route('error', ['error' => 'The response from HMRC servers could not be processed because it does not contain the application state.']);
        }

        if ($request->state !== config('app.app_state')) {
            return redirect()->route('error', ['error' => 'The response from HMRC servers returned an invalid application state.']);
        }

        try {
            $auth = $this->getAuthToken($request->code);

            $this->setTokensOnDB($auth);

            Log::debug('Oauth handled. Auth and refresh tokens set');

            return redirect()->route('info', ['title' => 'An access token for HMRC has been added to your application.']);
        } catch(Exception $e) {
            Log::debug('Error handling oauth');
            return redirect()->route('error', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Get an existing user token from the
     * database
     * 
     * @return string|null
     */
    private function getUserToken()
    {
        $token = Token::find(config('app.token_model_id', '1'));

        if ($token) {
            return $token->auth_token;
        }

        return null;
    }

    /**
     * Get an existing user refresh token from the
     * database
     * 
     * @return string|null
     */
    private function getUserRefreshToken()
    {
        $token = Token::find(config('app.token_model_id', '1'));

        if ($token) {
            return $token->refresh_token;
        }

        return null;
    }

    /**
     * Use an existing refresh token to refresh the user token.
     * 
     * @return mixed
     */
    private function refreshUserToken()
    {
        Log::debug('Attempting to refresh token');

        try {
            $auth = $this->refreshAuthToken($this->getUserRefreshToken());

            $this->setTokensOnDB($auth);

            Log::debug('token refreshed');

            return redirect()->route('info', ['title' => 'Your existing access token was expired and it has been renewed. Please perform your desired action again.']);
        } catch(Exception $e) {
            if ($e->getMessage() == 'Bad Request') {
                Log::debug('token could not be refreshed. attempting to get consent again.');
                /**
                 * Because a 400 is returned on a refresh token request, perhaps the
                 * user consent is needed again to generate a token.
                 */
                $url = $this->getAuthQueryURL();
                return Redirect::away($url);
            } else {
                Log::debug('Error while trying to refresh token');
                // otherwise, display the exception message
                return redirect()->route('error', ['error' => $e->getMessage()]);
            }
        }
    }

    /**
     * Set new auth and/or refresh tokens in the
     * database
     * 
     * @param array $auth
     * @return void
     */
    private function setTokensOnDB(array $auth): void
    {
        Token::updateOrInsert(
            ['id' => config('app.token_model_id', '1')],
            [
                'auth_token' => $auth['access_token'],
                'refresh_token' => $auth['refresh_token'],
            ]
        );
    }
}