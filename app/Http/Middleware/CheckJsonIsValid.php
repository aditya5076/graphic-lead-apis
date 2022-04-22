<?php

namespace App\Http\Middleware;

use Closure;

class CheckJsonIsValid
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Attempt to decode payload
        // $validJson = $request->json()->all();
        // if (empty($validJson)) {
        //     return \response()->json(['error' => 'The request is not a valid JSON.'], 422);
        // }

        // Attempt to decode payload
        json_decode($request->getContent());

        if (json_last_error() != JSON_ERROR_NONE) {
            // There was an error
            return \response()->json(['detail' => ['msg' => 'The request is not a valid JSON.']], 400);
        }
        return $next($request);
    }
}
