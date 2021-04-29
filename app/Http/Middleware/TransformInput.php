<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class TransformInput
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $transformer)
    {
        $transformedInput = [];

        foreach ($request->request->all() as $input => $value) {
            $transformedInput[$transformer::originalAttribute($input)] = $value;
        }

        $request->replace($transformedInput);

        $response = $next($request);

        return $response;
    }
}
