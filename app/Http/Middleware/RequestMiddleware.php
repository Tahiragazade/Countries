<?php
namespace App\Http\Middleware;

use Closure;

class RequestMiddleware
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
        if ($request->isMethod('get')) {
            $limit = $request->has('limit') ? intval($request->get('limit')) : 10;
            $page = $request->has('page') ? intval($request->get('page')) - 1 : 0;
            $offset = ($page) * $limit;
            $request->request->add(['limit' => $limit, 'offset' => $offset]);
            return $next($request);
        }
    }
}
