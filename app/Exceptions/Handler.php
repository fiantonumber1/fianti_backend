<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Database\QueryException;
use Symfony\Component\HttpFoundation\Response;

class Handler extends ExceptionHandler
{
    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function render($request, \Throwable $exception): Response
    {
        // Periksa jika error adalah QueryException dengan pesan max_connections_per_hour
        if ($exception instanceof QueryException && str_contains($exception->getMessage(), 'max_connections_per_hour')) {
            return response()->view('errors.database', [], 503);
        }

        // Untuk error lain, gunakan default
        return parent::render($request, $exception);
    }
}
