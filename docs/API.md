# Public API v1 — read-only project catalog

The API exists to make the portfolio inspectable, reusable and auditable, not to expose admin or home-lab internals. Responses use typed, manually reviewed DTOs (`PublicProjectView`) and never serialize CakePHP ORM entities directly.

## Endpoints

| Method | URL | Result |
|---|---|---|
| GET | `/api/v1/projects` | All 13 curated project case studies |
| GET | `/api/v1/projects/{slug}` | One study, or HTTP 404 |
| GET | `/api/v1/projects?lang=en` | English prose, default is Czech |
| GET | `/api/v1/projects?lang=cs` | Czech prose |

Invalid languages return 400. Mutation methods are not registered. There is no CORS wildcard; same-origin scripts can consume it. API data does not require an access token.

## Contract

`GET /api/v1/projects?lang=en`

```json
{
  "version": "1",
  "language": "en",
  "count": 13,
  "projects": [
    {
      "slug": "local-ai-hub",
      "name": "Local AI Hub",
      "category": "AI & Developer Tools",
      "status": "active",
      "featured": true,
      "summary": "...",
      "problem": "...",
      "solution": "...",
      "stack": ["..."],
      "features": ["..."],
      "year": "2026",
      "repository": "https://github.com/Harfusha/local-ai-hub",
      "demo": null
    }
  ]
}
```

The fields above show **shape only**, not a guaranteed current project status or a full sample record. The `repository` field is `null` for non-public projects. An external `demo` URL is sent only when the allowlisted URL validator accepts it. Internal hostnames, IP addresses, authentication, unpublished notes, service endpoints and server telemetry are never serialized.

### Cache semantics

Responses include `ETag` (SHA-256 of the actual serialized body) and `Cache-Control: public, max-age=120, must-revalidate`. Clients may send `If-None-Match`; an exact match returns 304 with no body. Because the ETag hashes the complete output, a changed editorial override changes the ETag. The API sets no cross-origin sharing policy. The global CSP and security headers apply here too.

### Test coverage and deployment

- `tests/Unit/PublicProjectViewTest.php`: typed redaction and bilingual DTO behavior.
- `tests/TestCase/Controller/ApiControllerTest.php`: real CakePHP routing and HTTP behavior (**not executable in the isolated authoring environment**).
- `scripts/http-smoke.py`: expected HTTP JSON and 304 behavior (**requires a running CakePHP server**).
- `tests/standalone.php`: serializer checks without CakePHP dependencies.

Public API output is intentionally limited; private project descriptions still require editorial approval, as for the visible HTML pages.
