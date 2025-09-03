<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForceCors
{
    /**
     * Liste blanche des origines autorisées (sans slash final)
     * -> adapter selon ton environnement (dev / prod)
     */
    protected array $allowedOrigins = [
        'https://yeshouatv.com',
        'http://localhost:5173',
        'https://live-t-vsite.vercel.app',
    ];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $origin = $request->headers->get('origin');

        // Si c'est une requête preflight (OPTIONS), on renvoie rapidement une réponse 204
        if ($request->getMethod() === 'OPTIONS') {
            $response = response('', 204);
            $this->addCorsHeaders($response, $origin, $request);
            return $response;
        }

        // Exécute le pipeline habituel
        $response = $next($request);

        // Assure-toi d'avoir un objet Response
        if (!($response instanceof Response)) {
            $response = response($response);
        }

        // Ajoute ou force les headers CORS
        $this->addCorsHeaders($response, $origin, $request);

        return $response;
    }

    /**
     * Ajoute les en-têtes CORS nécessaires à la réponse.
     */
    protected function addCorsHeaders(Response $response, ?string $origin, Request $request): void
    {
        // Si origin présent et autorisé : autoriser seulement celui-ci (ne pas utiliser '*')
        if ($origin && in_array($origin, $this->allowedOrigins, true)) {
            $response->headers->set('Access-Control-Allow-Origin', $origin);
        }

        // Toujours définir ces headers (valeurs sûres)
        $response->headers->set('Access-Control-Allow-Credentials', 'true');

        // Méthodes acceptées (adapter si tu n'autorises pas tout)
        $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS');

        // Headers autorisés (le navigateur peut demander plus via Access-Control-Request-Headers)
        // On expose communément Content-Type, Authorization, X-Requested-With, Accept, Origin
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, Accept, Origin');

        // Optionnel : durée (en secondes) que le preflight peut être mis en cache
        $response->headers->set('Access-Control-Max-Age', '86400');

        // Aide les caches intermédiaires à conserver la variabilité par Origin
        $response->headers->set('Vary', 'Origin, Access-Control-Request-Method, Access-Control-Request-Headers');
    }
}
