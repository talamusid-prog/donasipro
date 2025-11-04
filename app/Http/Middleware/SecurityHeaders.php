<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Strict-Transport-Security - Memaksa semua koneksi menggunakan HTTPS selama 1 tahun
        $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');

        // X-Frame-Options - Mencegah clickjacking attacks
        $response->headers->set('X-Frame-Options', 'DENY');

        // X-Content-Type-Options - Mencegah MIME type sniffing
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // X-XSS-Protection - Proteksi terhadap XSS attacks
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // Referrer-Policy - Kontrol informasi referrer untuk privasi
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Permissions-Policy - Membatasi akses ke geolocation, microphone, dan camera
        $response->headers->set(
            'Permissions-Policy',
            'geolocation=(), microphone=(), camera=(), payment=(), usb=(), magnetometer=(), gyroscope=(), accelerometer=()'
        );

        // Content-Security-Policy (optional, bisa ditambahkan jika diperlukan)
        // $response->headers->set('Content-Security-Policy', "default-src 'self'");

        return $response;
    }
}

