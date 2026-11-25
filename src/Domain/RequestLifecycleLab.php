<?php
declare(strict_types=1);

namespace App\Domain;

/**
 * Deterministic domain model and fixtures for the PHP Request Lifecycle Lab.
 * Represents real CakePHP 5 SSR request execution stages with exact code references.
 */
final class RequestLifecycleLab
{
    /**
     * @return list<array{
     *     id: string,
     *     title: array{cs: string, en: string},
     *     description: array{cs: string, en: string},
     *     method: string,
     *     url: string,
     *     status: int,
     *     status_text: string,
     *     headers: array<string, string>,
     *     code_ref: string,
     *     pipeline: list<array{
     *         stage: string,
     *         title: array{cs: string, en: string},
     *         detail: array{cs: string, en: string},
     *         component: string,
     *         outcome: string
     *     }>
     * }>
     */
    public static function scenarios(): array
    {
        return [
            [
                'id' => 'public-project-get',
                'title' => [
                    'cs' => 'GET /cs/projects/harfusha-lab (Běžný veřejný SSR požadavek)',
                    'en' => 'GET /en/projects/harfusha-lab (Standard public SSR request)',
                ],
                'description' => [
                    'cs' => 'Průchod standardního veřejného požadavku od reverzní proxy přes bezpečnostní hlavičky, směrování, kontroler a doménový katalog až po server-side rendering a RFC 9110 ETag.',
                    'en' => 'Standard public request trace from reverse proxy through security headers, routing, controller, domain catalog to server-side rendering and RFC 9110 ETag caching.',
                ],
                'method' => 'GET',
                'url' => '/cs/projects/harfusha-lab',
                'status' => 200,
                'status_text' => 'OK',
                'headers' => [
                    'Content-Type' => 'text/html; charset=UTF-8',
                    'Cache-Control' => 'public, max-age=3600, stale-while-revalidate=86400',
                    'ETag' => 'W/"5.2.0-harfusha-lab"',
                    'X-Content-Type-Options' => 'nosniff',
                    'Content-Security-Policy' => "default-src 'self'; script-src 'self' ...",
                ],
                'code_ref' => 'src/Controller/PagesController.php#L65-L100',
                'pipeline' => [
                    [
                        'stage' => '01-edge',
                        'title' => [
                            'cs' => 'Ochrana reverzní proxy a kanonického hostu',
                            'en' => 'Reverse proxy defense & canonical host guard',
                        ],
                        'detail' => [
                            'cs' => 'HostGuardMiddleware ověřuje klientský Host a port, vynucuje HTTPS přes TrustedProxy CIDR allowlist a blokuje spoofované X-Forwarded-* hlavičky.',
                            'en' => 'HostGuardMiddleware verifies client Host/port, enforces HTTPS through TrustedProxy CIDR allowlist, and rejects spoofed X-Forwarded-* headers.',
                        ],
                        'component' => 'App\\Middleware\\HostGuardMiddleware',
                        'outcome' => '200 Continue (trusted origin confirmed)',
                    ],
                    [
                        'stage' => '02-security',
                        'title' => [
                            'cs' => 'Bezpečnostní hlavičky a izolace CSRF',
                            'en' => 'Security headers & CSRF boundary',
                        ],
                        'detail' => [
                            'cs' => 'SecurityHeadersMiddleware aplikuje striktní CSP, HSTS a Permissions-Policy. CsrfProtectionMiddleware je izolován pouze pro /admin* cesty (žádné zbytečné Set-Cookie na veřejném webu).',
                            'en' => 'SecurityHeadersMiddleware enforces strict CSP, HSTS, and Permissions-Policy. CsrfProtectionMiddleware is scoped strictly to /admin* routes (no unwanted Set-Cookie on public web).',
                        ],
                        'component' => 'App\\Application::middleware',
                        'outcome' => 'Headers applied (CSP, HSTS, nosniff)',
                    ],
                    [
                        'stage' => '03-routing',
                        'title' => [
                            'cs' => 'CakePHP striktní směrování (DashedRoute)',
                            'en' => 'CakePHP strict routing (DashedRoute)',
                        ],
                        'detail' => [
                            'cs' => 'Router páruje vzor /{lang}/projects/{slug} s regexy lang: cs|en a slug: [a-z0-9-]+. Deleguje na PagesController::project.',
                            'en' => 'Router matches /{lang}/projects/{slug} with regex patterns lang: cs|en and slug: [a-z0-9-]+, delegating to PagesController::project.',
                        ],
                        'component' => 'config/routes.php',
                        'outcome' => 'Matched route: PagesController::project',
                    ],
                    [
                        'stage' => '04-controller',
                        'title' => [
                            'cs' => 'Kontroler a typově bezpečné parametry',
                            'en' => 'Controller & typed parameter extraction',
                        ],
                        'detail' => [
                            'cs' => 'PagesController přijímá injektovaný ContentRepository a EvidenceRegistry přes CakePHP 5 Dependency Injection container.',
                            'en' => 'PagesController receives injected ContentRepository and EvidenceRegistry via CakePHP 5 Dependency Injection container.',
                        ],
                        'component' => 'App\\Controller\\PagesController::project',
                        'outcome' => 'Parameters bound: lang=cs, slug=harfusha-lab',
                    ],
                    [
                        'stage' => '05-persistence',
                        'title' => [
                            'cs' => 'Izolované čtení a single-transaction snapshot',
                            'en' => 'Isolated read & single-transaction snapshot',
                        ],
                        'detail' => [
                            'cs' => 'ContentRepository načítá data z REPEATABLE READ transakce nebo recenzované paměťové mezipaměti se zárukou nulových N+1 dotazů.',
                            'en' => 'ContentRepository loads data from REPEATABLE READ snapshot or reviewed in-memory cache guaranteeing zero N+1 queries.',
                        ],
                        'component' => 'App\\Service\\ContentRepository',
                        'outcome' => 'Entity found: Project(slug="harfusha-lab")',
                    ],
                    [
                        'stage' => '06-domain',
                        'title' => [
                            'cs' => 'Doménový registr ověřitelných důkazů',
                            'en' => 'Domain registry of verifiable evidence',
                        ],
                        'detail' => [
                            'cs' => 'EvidenceRegistry vyhledává ověřená technická tvrzení v evidence.json. Vrací typovaná DTO EngineeringClaim a EngineeringEvidence s přesnými SHA revizemi.',
                            'en' => 'EvidenceRegistry queries verified technical claims in evidence.json, returning typed EngineeringClaim and EngineeringEvidence DTOs with exact SHA revisions.',
                        ],
                        'component' => 'App\\Domain\\EvidenceRegistry',
                        'outcome' => 'Evidence bound: 1 verified claim loaded',
                    ],
                    [
                        'stage' => '07-view',
                        'title' => [
                            'cs' => 'Server-side rendering v CakePHP View',
                            'en' => 'CakePHP View server-side rendering',
                        ],
                        'detail' => [
                            'cs' => 'View vykresluje templates/Pages/project.php a obaluje layoutem default.php. Všechny dynamické hodnoty jsou zabezpečeny přes Html::escape.',
                            'en' => 'View renders templates/Pages/project.php wrapped with default.php layout. All dynamic values sanitized via Html::escape.',
                        ],
                        'component' => 'templates/Pages/project.php',
                        'outcome' => 'SSR HTML generated (0 client hydration lag)',
                    ],
                    [
                        'stage' => '08-caching',
                        'title' => [
                            'cs' => 'Podmíněná odpověď a validace ETag (RFC 9110)',
                            'en' => 'Conditional response & ETag validation (RFC 9110)',
                        ],
                        'detail' => [
                            'cs' => 'HttpConditionalResponder vyhodnocuje hlavičku If-None-Match. Pokud klient disponuje aktuálním ETagem, server okamžitě emituje 304 Not Modified s prázdným tělem.',
                            'en' => 'HttpConditionalResponder evaluates If-None-Match header. When client holds matching ETag, server immediately emits 304 Not Modified with empty body.',
                        ],
                        'component' => 'App\\Support\\HttpConditionalResponder',
                        'outcome' => 'Response 200 OK with weak ETag',
                    ],
                ],
            ],
            [
                'id' => 'invalid-lang-slug-404',
                'title' => [
                    'cs' => 'GET /de/projects/unknown (Neplatný jazyk / neznámý slug)',
                    'en' => 'GET /de/projects/unknown (Invalid language / unknown slug)',
                ],
                'description' => [
                    'cs' => 'Ukázka zachycení neplatného požadavku na úrovni směrování i kontroleru s bezpečným zobrazením lokalizované chybové stránky 404.',
                    'en' => 'Handling of invalid routing and unknown slugs with secure localized 404 error page delivery.',
                ],
                'method' => 'GET',
                'url' => '/de/projects/unknown',
                'status' => 404,
                'status_text' => 'Not Found',
                'headers' => [
                    'Content-Type' => 'text/html; charset=UTF-8',
                    'Cache-Control' => 'no-cache, private',
                    'X-Content-Type-Options' => 'nosniff',
                ],
                'code_ref' => 'src/Controller/PagesController.php#L80-L92',
                'pipeline' => [
                    [
                        'stage' => '01-edge',
                        'title' => [
                            'cs' => 'HostGuardMiddleware kontrola',
                            'en' => 'HostGuardMiddleware inspection',
                        ],
                        'detail' => [
                            'cs' => 'HostGuardMiddleware propouští požadavek, host i schéma jsou v pořádku.',
                            'en' => 'HostGuardMiddleware validates host and protocol, allowing the request to proceed.',
                        ],
                        'component' => 'App\\Middleware\\HostGuardMiddleware',
                        'outcome' => '200 Continue to routing',
                    ],
                    [
                        'stage' => '02-routing',
                        'title' => [
                            'cs' => 'Kontrola jazykového prefixu',
                            'en' => 'Language prefix regex match',
                        ],
                        'detail' => [
                            'cs' => 'Prefix "de" neodpovídá regexu "cs|en". Požadavek přepadá na standardní zachycení neplatné cesty nebo fallback na výchozí jazyk s kontrolou existence.',
                            'en' => 'Prefix "de" fails the "cs|en" regex constraint. Request falls through to unrouted error handling.',
                        ],
                        'component' => 'config/routes.php',
                        'outcome' => 'Routing failure / NotFoundException',
                    ],
                    [
                        'stage' => '03-domain',
                        'title' => [
                            'cs' => 'Ověření existence entity v katalogu',
                            'en' => 'Domain catalog presence check',
                        ],
                        'detail' => [
                            'cs' => 'ProjectCatalog::find("unknown") vrací null. Kontroler okamžitě vyvolává NotFoundException se zprávou chránící interní stav.',
                            'en' => 'ProjectCatalog::find("unknown") returns null. Controller throws NotFoundException with privacy-safe message.',
                        ],
                        'component' => 'App\\Domain\\ProjectCatalog',
                        'outcome' => 'throw new NotFoundException("Projekt nebyl nalezen")',
                    ],
                    [
                        'stage' => '04-error',
                        'title' => [
                            'cs' => 'ErrorHandlerMiddleware a lokalizovaná šablona',
                            'en' => 'ErrorHandlerMiddleware & localized template',
                        ],
                        'detail' => [
                            'cs' => 'ErrorHandlerMiddleware zachycuje výjimku, potlačuje PHP stack trace a vykresluje přístupnou chybovou stránku 404.',
                            'en' => 'ErrorHandlerMiddleware catches exception, suppresses PHP stack trace, and renders accessible 404 error page.',
                        ],
                        'component' => 'templates/Error/error400.php',
                        'outcome' => 'Response 404 Not Found (zero leaks)',
                    ],
                ],
            ],
            [
                'id' => 'admin-csrf-violation-403',
                'title' => [
                    'cs' => 'POST /admin/login (Syntetický neplatný CSRF token)',
                    'en' => 'POST /admin/login (Synthetic invalid CSRF token)',
                ],
                'description' => [
                    'cs' => 'Bezpečnostní brána CSRF pro administraci. Požadavek s chybějícím nebo podvrženým tokenem je zastaven v middleware vrstvě dříve, než se dotkne kontroleru nebo databáze.',
                    'en' => 'Admin CSRF security barrier. Requests with missing or forged tokens are stopped in the middleware layer before touching controller or database.',
                ],
                'method' => 'POST',
                'url' => '/admin/login',
                'status' => 403,
                'status_text' => 'Forbidden',
                'headers' => [
                    'Content-Type' => 'text/html; charset=UTF-8',
                    'Cache-Control' => 'no-store, private',
                    'X-Content-Type-Options' => 'nosniff',
                ],
                'code_ref' => 'src/Application.php#L125-L140',
                'pipeline' => [
                    [
                        'stage' => '01-edge',
                        'title' => [
                            'cs' => 'HostGuard a BodyParser',
                            'en' => 'HostGuard & BodyParser',
                        ],
                        'detail' => [
                            'cs' => 'Ověření kanonického původu a rozparsování application/x-www-form-urlencoded těla formuláře do CakePHP ServerRequest.',
                            'en' => 'Canonical origin verification and parsing of application/x-www-form-urlencoded body into CakePHP ServerRequest.',
                        ],
                        'component' => 'Cake\\Http\\Middleware\\BodyParserMiddleware',
                        'outcome' => 'Body parsed: username="admin"',
                    ],
                    [
                        'stage' => '02-csrf',
                        'title' => [
                            'cs' => 'Ověření CsrfProtectionMiddleware',
                            'en' => 'CsrfProtectionMiddleware validation',
                        ],
                        'detail' => [
                            'cs' => 'Middleware porovnává _csrfToken odeslaný ve formuláři s kryptografickým tokenem z šifrovaného cookie. Hodnoty nesouhlasí.',
                            'en' => 'Middleware compares _csrfToken submitted in form against the cryptographic token in encrypted cookie. Verification fails.',
                        ],
                        'component' => 'Cake\\Http\\Middleware\\CsrfProtectionMiddleware',
                        'outcome' => 'throw new InvalidCsrfTokenException',
                    ],
                    [
                        'stage' => '03-guard',
                        'title' => [
                            'cs' => 'Okamžité zastavení a auditní záznam',
                            'en' => 'Immediate execution halt & audit event',
                        ],
                        'detail' => [
                            'cs' => 'Běh je ukončen dříve, než kontroler inicializuje ověřování hesla nebo LoginLimiter. Žádná zátěž databáze ani únik interních údajů.',
                            'en' => 'Execution terminated prior to password check or LoginLimiter invocation. Zero database load or internal information leakage.',
                        ],
                        'component' => 'App\\Security\\LoginLimiter',
                        'outcome' => 'Execution aborted before AdminController',
                    ],
                    [
                        'stage' => '04-response',
                        'title' => [
                            'cs' => 'Emise 403 Forbidden',
                            'en' => '403 Forbidden response emission',
                        ],
                        'detail' => [
                            'cs' => 'Klient obdrží odpověď 403 Forbidden s hlavičkou Cache-Control: no-store.',
                            'en' => 'Client receives 403 Forbidden response with Cache-Control: no-store header.',
                        ],
                        'component' => 'Cake\\Error\\Renderer\\WebExceptionRenderer',
                        'outcome' => 'Response 403 Forbidden',
                    ],
                ],
            ],
            [
                'id' => 'database-fallback-resilience',
                'title' => [
                    'cs' => 'GET /cs/open-source (Výpadek dynamické databáze)',
                    'en' => 'GET /en/open-source (Dynamic database outage resilience)',
                ],
                'description' => [
                    'cs' => 'Demonstrace graceful degradation: když je MariaDB dočasně nedostupná, ContentRepository zachytí výjimku a bezpečně servíruje recenzovaný snapshot s jasným indikátorem zálohy.',
                    'en' => 'Demonstration of graceful degradation: when MariaDB is temporarily unreachable, ContentRepository catches the query failure and securely serves reviewed fallback snapshot with an explicit indicator.',
                ],
                'method' => 'GET',
                'url' => '/cs/open-source',
                'status' => 200,
                'status_text' => 'OK (Static Fallback)',
                'headers' => [
                    'Content-Type' => 'text/html; charset=UTF-8',
                    'Cache-Control' => 'public, max-age=300',
                    'X-Data-Provenance' => 'static-fallback',
                ],
                'code_ref' => 'src/Service/ContentRepository.php#L85-L115',
                'pipeline' => [
                    [
                        'stage' => '01-routing',
                        'title' => [
                            'cs' => 'Směrování na PagesController::openSource',
                            'en' => 'Routing to PagesController::openSource',
                        ],
                        'detail' => [
                            'cs' => 'Router předává požadavek do PagesController::openSource.',
                            'en' => 'Router dispatches request to PagesController::openSource.',
                        ],
                        'component' => 'config/routes.php',
                        'outcome' => 'Route matched: PagesController::openSource',
                    ],
                    [
                        'stage' => '02-persistence',
                        'title' => [
                            'cs' => 'Pokus o dynamický SQL dotaz selhal',
                            'en' => 'Dynamic SQL query attempt failed',
                        ],
                        'detail' => [
                            'cs' => 'Connection::execute() vyvolává QueryException (SQL server timeout / connection refused).',
                            'en' => 'Connection::execute() triggers QueryException (SQL server timeout / connection refused).',
                        ],
                        'component' => 'Cake\\Database\\Connection',
                        'outcome' => 'Exception: Cake\\Database\\Exception\\QueryException',
                    ],
                    [
                        'stage' => '03-resilience',
                        'title' => [
                            'cs' => 'Odchycení výjimky v ContentRepository',
                            'en' => 'Exception trapped in ContentRepository',
                        ],
                        'detail' => [
                            'cs' => 'ContentRepository zachytí výjimku, zaloguje pouze get_class($error) (žádné SQL dotazy ani citlivá data do logů) a přejde na záložní větev.',
                            'en' => 'ContentRepository catches exception, logs solely get_class($error) (zero SQL or credential leaks) and activates fallback.',
                        ],
                        'component' => 'App\\Service\\ContentRepository::githubSnapshot',
                        'outcome' => 'Fallback activated, error class logged',
                    ],
                    [
                        'stage' => '04-fallback',
                        'title' => [
                            'cs' => 'Aktivace recenzovaného snapshotu GithubProvenance',
                            'en' => 'GithubProvenance reviewed snapshot activation',
                        ],
                        'detail' => [
                            'cs' => 'Načtení atomického záložního souboru data/github_provenance.json s příznakem is_stale: true a snapshot_date.',
                            'en' => 'Loads atomic backup file data/github_provenance.json with is_stale: true flag and snapshot_date.',
                        ],
                        'component' => 'App\\Domain\\GithubProvenance',
                        'outcome' => 'Fallback snapshot loaded (33 merged PRs)',
                    ],
                    [
                        'stage' => '05-view',
                        'title' => [
                            'cs' => 'Vykreslení stránky s transparentním indikátorem',
                            'en' => 'Page rendered with transparent fallback notice',
                        ],
                        'detail' => [
                            'cs' => 'Šablona zobrazuje komponentu github_provenance_notice s informací o použití recenzovaného snapshotu. Web zůstává 100% funkční a čitelný.',
                            'en' => 'Template renders github_provenance_notice informing the visitor about reviewed snapshot usage. Site remains 100% available and readable.',
                        ],
                        'component' => 'templates/Pages/open_source.php',
                        'outcome' => 'Response 200 OK with fallback badge',
                    ],
                ],
            ],
        ];
    }
}
