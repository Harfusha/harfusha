<?php
declare(strict_types=1);

namespace App\Domain;

use InvalidArgumentException;

/** Public-only GitHub repository snapshot; rejects incomplete upstream data. */
final readonly class PublicRepository
{
    /**
     * Initialize the immutable domain model with validated inputs.
     */
    public function __construct(
        public string $name,
        public string $description,
        public string $url,
        public string $pushedAt,
    ) {
    }

    /**
     * @param list<array<string,mixed>> $records
     * @return list<self>
     */
    public static function fromGithub(array $records, string $account): array
    {
        if (preg_match('/^[A-Za-z0-9-]{1,39}$/D', $account) !== 1) {
            throw new InvalidArgumentException('Invalid GitHub account');
        }
        $result = [];
        foreach ($records as $record) {
            $private = $record['private'] ?? null;
            $fork = $record['fork'] ?? null;
            if (!is_bool($private) || !is_bool($fork)) {
                throw new InvalidArgumentException('Repository visibility metadata missing');
            }
            // This endpoint is deliberately unauthenticated; private content is
            // never imported, even if an upstream response unexpectedly includes it.
            if ($private) {
                throw new InvalidArgumentException('Private repository in public API response');
            }
            $fullName = $record['full_name'] ?? null;
            $url = $record['html_url'] ?? null;
            if (
                !is_string($fullName) || !is_string($url) ||
                preg_match('~^([A-Za-z0-9-]{1,39})/([A-Za-z0-9_.-]{1,100})$~D', $fullName, $match) !== 1 ||
                strcasecmp($match[1], $account) !== 0 ||
                $url !== 'https://github.com/' . $fullName
            ) {
                throw new InvalidArgumentException('Untrusted repository identity');
            }
            if ($fork) {
                continue;
            }
            $pushed = $record['pushed_at'] ?? null;
            if (!is_string($pushed) || preg_match('/^20\d\d-\d\d-\d\dT\d\d:\d\d:\d\dZ$/D', $pushed) !== 1) {
                throw new InvalidArgumentException('Repository timestamp missing');
            }
            $when = GithubTimestamp::normalize($pushed);
            $description = $record['description'] ?? null;
            if ($description !== null && !is_string($description)) {
                throw new InvalidArgumentException('Invalid repository description');
            }
            $description ??= '';
            if (preg_match('//u', $description) !== 1) {
                throw new InvalidArgumentException('Invalid UTF-8 description');
            }
            $key = strtolower($fullName);
            if (isset($result[$key])) {
                throw new InvalidArgumentException('Duplicate public repository');
            }
            $result[$key] = new self(
                $fullName,
                mb_substr($description, 0, 350, 'UTF-8'),
                $url,
                $when,
            );
        }
        if ($result === []) {
            throw new InvalidArgumentException('Empty public repository listing; previous cache preserved');
        }

        return array_values($result);
    }
}
