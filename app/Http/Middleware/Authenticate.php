<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
  /*  protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            return route('login');
        }
    }*/

    protected function redirectTo($request)
    {
        // if (! $request->expectsJson()) {
        //     return route('login');
        // }
        if ($request->is("api/*")) {
            $code = 401;
            $response = [
                'code'    => $code,
                'message' => 'Unauthorize User',
                'data'    => ['message' => __('User is not authorize!') ]
            ];
            return abort(response()->json($response, $code));
        }
        else{
            return route('login');
        }
    }













}
