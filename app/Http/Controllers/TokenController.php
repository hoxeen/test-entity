<?php

namespace App\Http\Controllers;

use App\Models\AmoApiClient;
use App\Models\AmoToken;
use Illuminate\Database\Eloquent\Model;
use League\OAuth2\Client\Grant\RefreshToken;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Token\AccessTokenInterface;

class TokenController extends Controller
{
    public function createRefreshByCode($code) {
        if(count(AmoToken::all())==0) {
            $access=AmoApiClient::get()->getOAuthClient()->getAccessTokenByCode($code);
            $token = new AmoToken();
            $token->access_token = $access->getToken();
            $token->refresh_token = $access->getRefreshToken();
            $token->expires = $access->getExpires();
            $token->save();
            dd($access);
        } else {
            return 'previously created';
        }
//        $tokenResult=AmoToken::all();

    }

    public function changeRefreshByCode($code) {
        $access=AmoApiClient::get()->getOAuthClient()->getAccessTokenByCode($code);
        AmoToken::query()->update(['access_token' => $access->getToken(),
                                   'refresh_token' => $access->getRefreshToken(),
                                   'expires' => $access->getExpires()]);
//        $tokenResult=AmoToken::all();
        dd($access);
    }

    /**
     * @throws \League\OAuth2\Client\Provider\Exception\IdentityProviderException
     */
    public function refresh() {
        $tokenResult=AmoToken::all();
        if(count($tokenResult)!=0) {
            $access = AmoApiClient::get()->getOAuthClient()->getOAuthProvider()->getAccessToken(new RefreshToken(), [
                'refresh_token' => $tokenResult[0]['refresh_token'],
            ]);
            AmoToken::query()->update(['access_token' => $access->getToken(),
                                       'refresh_token' => $access->getRefreshToken(),
                                       'expires' => $access->getExpires()]);
            dd($access);
        } else {
            return 'does not exist';
        }
    }
}
