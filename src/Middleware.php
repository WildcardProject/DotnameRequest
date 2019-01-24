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
        return $next($this->_reset_request($request));
    }

    protected function _reset_request($request)
    {
        $posted = file_get_contents('php://input');
        $got = array_get($_SERVER, 'QUERY_STRING');

        $inputs = array_merge([], $this->_parse($got), $this->_parse($posted));
        return $request->replace($inputs);
    }

    protected function _parse($query) {
        $input = [];
        if (!strlen($query)) return $input;
        foreach (explode('&', $query) as $keyvalue) {
            list($key, $value) = explode('=', $keyvalue);
            array_set($input, $key, rawurldecode($value));
        }
        return $input;
    }
}
