<?php

namespace Nddcoder\PhpOfflineLicense;

class LicenseInfo
{
    // @codeCoverageIgnoreStart
    public function __construct(
        public bool $valid = false,
        public ?bool $validChecksum = false,
        public ?int $expireAt = null,
        public ?string $invalidReason = null
    ) {
    }
    // @codeCoverageIgnoreEnd
}
