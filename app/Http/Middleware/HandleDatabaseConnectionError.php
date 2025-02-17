<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HandleDatabaseConnectionError
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            // Lanjutkan permintaan ke aplikasi
            return $next($request);
        } catch (\Illuminate\Database\QueryException $e) {
            // Periksa apakah error terkait dengan batas koneksi
            if (str_contains($e->getMessage(), 'max_connections_per_hour')) {
                // Redirect ke halaman error
                return redirect()->route('database.error');
            }

            // Jika error lain, lempar kembali
            throw $e;
        }
    }
}
