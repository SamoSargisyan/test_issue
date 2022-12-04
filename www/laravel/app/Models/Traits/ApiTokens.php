<?php

namespace App\Models\Traits;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Laravel\Passport\HasApiTokens;

trait ApiTokens
{
    use HasApiTokens;

    protected $accessToken;

    public function createTokenWithRefresh(string $password)
    {
        $oauth_client = DB::table('oauth_clients')
            ->where('personal_access_client', 0)
            ->first();

        if (!$oauth_client) {
            throw new \Exception('oauth_client not found');
        }

        $data = [
            'grant_type' => 'password',
            'client_id' => $oauth_client->id,
            'client_secret' => $oauth_client->secret,
            'username' => "{$this->id}",
            'password' => $password,
        ];

        $request = Request::create('/oauth/token', 'POST', $data);

        $response = app()
            ->handle($request)
            ->getContent();

        $token = json_decode($response);

        if (isset($token->error)) {
            throw new AuthorizationException();
        }

        return $token;
    }

    public function withAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;

        return $this;
    }

    public function validateForPassportPasswordGrant($data)
    {
        return true;
    }

    public function findForPassport($username)
    {
        return $this->where('id', $username)
            ->first();
    }

    public function updateRefreshToken($refreshToken)
    {
        $oauth_client = DB::table('oauth_clients')
            ->where('personal_access_client', 0)
            ->first();

        if (!$oauth_client) {
            throw new \Exception('oauth_client not found');
        }

        $data = [
            'grant_type' => 'refresh_token',
            'refresh_token' => $refreshToken,
            'client_id' => $oauth_client->id,
            'client_secret' => $oauth_client->secret,
        ];

        $request = Request::create('/oauth/token', 'POST', $data);
        $response = app()->handle($request)
            ->getContent();

        $token = json_decode($response);

        if (isset($token->error)) {
            throw new AuthorizationException;
        }

        return $token;
    }

}
