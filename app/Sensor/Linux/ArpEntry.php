<?php

declare(strict_types=1);

namespace App\Sensor\Linux;

/**
 * Represents a single line from an arpwatch file.
 */
final class ArpEntry
{
    public function __construct(
        public readonly string $mac,
        public readonly string $ip,
        public readonly int $timestamp,
        public readonly string $interface,
        public readonly ?string $vendor = null
    ) {
    }

    /** Convenience: timestamp as a readable DateTimeImmutable. */
    public function seenAt(): \DateTimeImmutable
    {
        return (new \DateTimeImmutable())->setTimestamp($this->timestamp);
    }

    public function toArray(): array
    {
        return [
            'mac'        => $this->mac,
            'ip'         => $this->ip,
            'timestamp'  => $this->timestamp,
            'interface'  => $this->interface,
            'vendor'     => $this->vendor,
            'seen_at'    => $this->seenAt()->format(DATE_ATOM),
        ];
    }
}
