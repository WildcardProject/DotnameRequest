<?php
namespace programming_cat\DotnameRequest;

use Closure;

class Middleware
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
        $requestVars = [];
        foreach ($request->all() as $key=>$value) {
            array_set($requestVars, $key, $value);
        }
        return $next($request->replace($requestVars));
    }
}
