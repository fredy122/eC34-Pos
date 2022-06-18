<?php

namespace App\Http\Middleware;

use Closure;

class Autenticado
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
        if (!session('authenticated')) {
            if ($request->ajax())
            {
                return response('Unauthorized.', 401);
            }
            return redirect('/');
        }
        
        // Evitamos el History Back del Navegador
        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
        header('Content-Type: text/html');

        // Next
        return $next($request);
    }
}
