<?php
declare(strict_types=1);

namespace App\Security;

use App\Support\Environment;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Explicit boundary for reverse-proxy trust. Forwarding headers such as
 * X-Forwarded-Proto and X-Forwarded-For are ignored unless REMOTE_ADDR
 * matches an explicitly configured trusted proxy allowlist.
 */
final readonly class TrustedProxy
{
    /**
     * @param list<string> $trustedIps Exact proxy IP addresses allowed to supply forwarding headers.
     */
    public function __construct(private array $trustedIps)
    {
    }

    /**
     * Build proxy trust policy from TRUSTED_PROXY_IPS environment variable.
     */
    public static function fromEnvironment(): self
    {
        $raw = Environment::get('TRUSTED_PROXY_IPS');
        if (!is_string($raw) || trim($raw) === '') {
            return new self([]);
        }
        $ips = [];
        foreach (explode(',', $raw) as $candidate) {
            $ip = trim($candidate);
            if ($ip !== '' && filter_var($ip, FILTER_VALIDATE_IP) !== false) {
                $ips[] = $ip;
            }
        }

        return new self(array_values(array_unique($ips)));
    }

    /**
     * Check whether the direct TCP peer is an explicitly trusted proxy.
     */
    public function isTrustedPeer(?string $remoteAddr): bool
    {
        if (!is_string($remoteAddr) || filter_var($remoteAddr, FILTER_VALIDATE_IP) === false) {
            return false;
        }
        $packedPeer = inet_pton($remoteAddr);
        if ($packedPeer === false) {
            return false;
        }
        foreach ($this->trustedIps as $trusted) {
            $packedTrusted = inet_pton($trusted);
            if ($packedTrusted !== false && $packedPeer === $packedTrusted) {
                return true;
            }
        }

        return false;
    }

    /**
     * Resolve client IP address without trusting spoofed forwarding headers from untrusted peers.
     */
    public function clientIp(ServerRequestInterface $request): string
    {
        $server = $request->getServerParams();
        $remoteAddr = is_string($server['REMOTE_ADDR'] ?? null) ? $server['REMOTE_ADDR'] : null;
        $peer = is_string($remoteAddr) && filter_var($remoteAddr, FILTER_VALIDATE_IP) !== false
            ? $remoteAddr
            : 'unknown';

        if (!$this->isTrustedPeer($remoteAddr)) {
            return $peer;
        }

        $forwardedFor = trim($request->getHeaderLine('X-Forwarded-For'));
        if ($forwardedFor === '') {
            return $peer;
        }

        // Walk from right to left and return the first valid IP that is not itself a trusted proxy.
        $parts = array_reverse(explode(',', $forwardedFor));
        foreach ($parts as $part) {
            $candidate = trim($part);
            if (filter_var($candidate, FILTER_VALIDATE_IP) === false) {
                return $peer;
            }
            if (!$this->isTrustedPeer($candidate)) {
                return $candidate;
            }
        }

        return $peer;
    }

    /**
     * Resolve effective protocol ('http' or 'https'), trusting X-Forwarded-Proto only from trusted peers.
     */
    public function effectiveScheme(ServerRequestInterface $request): string
    {
        $uriScheme = strtolower($request->getUri()->getScheme());
        $server = $request->getServerParams();
        $remoteAddr = is_string($server['REMOTE_ADDR'] ?? null) ? $server['REMOTE_ADDR'] : null;

        if ($this->isTrustedPeer($remoteAddr)) {
            $protoHeader = trim($request->getHeaderLine('X-Forwarded-Proto'));
            if ($protoHeader !== '') {
                $tokens = array_values(array_filter(
                    array_map(static fn(string $part): string => strtolower(trim($part)), explode(',', $protoHeader)),
                    static fn(string $token): bool => $token !== '',
                ));
                if ($tokens !== []) {
                    // Use the rightmost entry added by the nearest trusted edge proxy.
                    $edgeProto = $tokens[count($tokens) - 1];
                    if ($edgeProto === 'http' || $edgeProto === 'https') {
                        return $edgeProto;
                    }
                }
            }
        }

        return $uriScheme === 'https' ? 'https' : 'http';
    }
}
