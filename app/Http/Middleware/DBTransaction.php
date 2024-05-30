<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\DB;

class DBTransaction
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Begin a database transaction
        DB::beginTransaction();

        try {
            // Handle the incoming request
            $response = $next($request);
        } catch (\Exception $e) {
            // Roll back the transaction if an exception occurs and rethrow the exception
            DB::rollBack();
            throw $e;
        }

        // Check the HTTP status code of the response
        if ($response->getStatusCode() > 399) {
            // Roll back the transaction if response status code indicates an error
            DB::rollBack();
        } else {
            // Commit the transaction if the response status code indicates success
            DB::commit();
        }

        // Return the response
        return $response;
    }
}
