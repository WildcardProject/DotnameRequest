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
        if (strpos($request->header('CONTENT_TYPE'), 'application/x-www-form-urlencoded') !== FALSE || in_array(strtoupper($request->server('REQUEST_METHOD', 'GET')), ["GET"])) {
            $posted = file_get_contents('php://input');
            $got = array_get($_SERVER, 'QUERY_STRING');
            $inputs = array_merge([], $this->_parse($got), $this->_parse($posted));
            return $request->replace($inputs);
        }
        else {
            // multipartの場合はあきらめる
            if (config('app.debug')===true) {
                report(new \Exception("multipartの場合のパラメータ名変換はサポートしていません。"));
            }
            return $request;
        }
    }

    protected function _parse($query) {
        $input = [];
        if (!strlen($query)) return $input;
        foreach (explode('&', $query) as $keyvalue) {
            list($key, $value) = explode('=', $keyvalue);
            $key = $this->unify_array_key($key);
            array_set($input, $key, urldecode($value));
        }
        return $input;
    }

    protected function unify_array_key($key) {
        return str_replace(']', '', str_replace('[', '.', str_replace('][', '.', urldecode($key))));
    }
}
