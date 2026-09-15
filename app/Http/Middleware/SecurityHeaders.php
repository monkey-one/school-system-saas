<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

// Adds baseline browser security headers to every web and API response:
// no MIME sniffing, no framing by other sites, a conservative referrer
// policy, limited device permissions and HSTS on HTTPS.
class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        $headers = $response->headers;

        $headers->set('X-Content-Type-Options', 'nosniff');
        $headers->set('X-Frame-Options', 'SAMEORIGIN');
        $headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        // Camera is used by the QR scanner and geolocation by teacher check-in.
        $headers->set('Permissions-Policy', 'camera=(self), geolocation=(self), microphone=(), payment=(self)');

        if ($request->isSecure()) {
            $headers->set('Strict-Transport-Security', 'max-age=31536000');
        }

        $headers->remove('X-Powered-By');

        return $response;
    }
}
