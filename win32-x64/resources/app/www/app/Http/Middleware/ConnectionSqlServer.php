<?php

namespace App\Http\Middleware;

use Closure;

class ConnectionSqlServer
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
        \Config::set('database.default', 'sqlsrv');
        \Config::set('database.connections.sqlsrv.host', session('connection')->host);
        \Config::set('database.connections.sqlsrv.port', '1433');
        \Config::set('database.connections.sqlsrv.username', session('connection')->username);
        \Config::set('database.connections.sqlsrv.password', session('connection')->password);
        \Config::set('database.connections.sqlsrv.database', 'eC34');
        
        return $next($request);
    }
}
