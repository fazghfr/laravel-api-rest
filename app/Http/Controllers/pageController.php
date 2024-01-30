<?php

namespace App\Http\Controllers;


use App\Models\User;
use Illuminate\Http\Request as RequestHTTP;
use Illuminate\Support\Facades\Request as RequestFacade;
use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\PersonalAccessToken;


class pageController extends Controller
{
    const web_prefix = "http://localhost:8000/api/";

    private function get_authenticated_user()
    {
        $token = session('auth_token');
        if(!$token){
            return null;
        }

        // Create a request with the specified headers
        $apiReq = RequestFacade::create(self::web_prefix . 'user', 'GET');
        $apiReq->headers->set('Authorization', 'Bearer '.$token);

        $responseData = json_decode(app()->handle($apiReq)->getContent());
        $userData = $responseData->user;

        $authenticated_user = [
            'name' => $userData->name,
            'email' => $userData->email,
            'email_verified_at' => $userData->email_verified_at,
            'created_at' => $userData->created_at,
            'updated_at' => $userData->updated_at,
        ];

        return $authenticated_user;
    }

    public function login_page()
    {
        return view('login');
    }

    public function login(RequestHTTP $request)
    {

        $apiReq = RequestFacade::create(self::web_prefix . 'login', 'POST', [
            'email' => $request->all()['email'],
            'password' => $request->all()['password']
        ]);

        $apiResponse = Route::dispatch($apiReq);
        $dataResponse = json_decode($apiResponse->getContent());


        if($apiResponse->getStatusCode() != 200){
            // TODO : send error message

            return redirect('/login');
        }
        session(['auth_token' => $dataResponse->token]);

        return redirect('/home');
    }

    public function log_out()
    {
        // call api logout and handle using app()->handle
        $apiReq = RequestFacade::create(self::web_prefix . 'logout', 'POST');
        $apiReq->headers->set('Authorization', 'Bearer '.session('auth_token'));

        $apiResponse = app()->handle($apiReq);

        if($apiResponse->getStatusCode() != 200){
            // TODO : send error message
            dd('masuk');
            return redirect('/home');
        }

        session()->forget('auth_token');

        return redirect('/home');
    }

    public function home_page()
    {
        $homeData = $this->get_authenticated_user();
;
        return view('home', compact('homeData'));
    }
}
