<?php

namespace Nddcoder\PhpOfflineLicense\Tests;

use Nddcoder\PhpOfflineLicense\PhpOfflineLicense;

class TestCase extends \PHPUnit\Framework\TestCase
{
    protected PhpOfflineLicense $offlineLicense;

    protected string $secret = 'secret';

    protected function setUp(): void
    {
        $this->offlineLicense = PhpOfflineLicense::create($this->secret);
    }


}
