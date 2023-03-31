<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MarkRequest
{
    const RequestIdKey = 'X-Request-Id';
    const WhoKey = 'X-Who';
    const UIDKey = 'X-UID';

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!$uuid = $request->headers->get(self::RequestIdKey)) {
            $request->headers->set(self::RequestIdKey, $uuid = Str::uuid()->toString());
        }

        Log::withContext([
            self::RequestIdKey => $uuid,
            self::WhoKey => $who = gethostname(),
            self::UIDKey => $request->user()->id ?? 0
        ]);

        /** @var Response $response */
        $response = $next($request);
        $response->headers->set(self::RequestIdKey, $uuid);
        $response->headers->set(self::WhoKey, $who);

        return $response;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return void
     */
    public function terminate(Request $request, \Symfony\Component\HttpFoundation\Response $response)
    {
        Log::debug(__METHOD__, ['Terminate' => [
            'path' => $request->path(),
            'query' => $request->getQueryString()
        ]]);
    }
}
