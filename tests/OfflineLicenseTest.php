<?php

namespace Nddcoder\PhpOfflineLicense\Tests;

class OfflineLicenseTest extends TestCase
{
    /** @test */
    public function it_should_generate_license()
    {
        $license = $this->offlineLicense->generate();
        $this->assertEquals(36, strlen($license));

        $licenseInfo = $this->offlineLicense->getInfo($license);

        $this->assertTrue($licenseInfo->valid);
        $this->assertTrue($licenseInfo->validChecksum);
        $this->assertNull($licenseInfo->expireAt);

        $this->assertTrue($this->offlineLicense->validate($license));
    }

    /** @test */
    public function it_should_generate_license_with_expire_time()
    {
        $expireTime = time() + 10;
        $license    = $this->offlineLicense->generate($expireTime);

        $licenseInfo = $this->offlineLicense->getInfo($license);

        $this->assertEquals($expireTime, $licenseInfo->expireAt);

        $this->assertTrue($this->offlineLicense->validate($license));
    }

    /** @test */
    public function it_should_generate_expired_license_with_time_in_past()
    {
        $expireTime = time() - 10;
        $license    = $this->offlineLicense->generate($expireTime);

        $licenseInfo = $this->offlineLicense->getInfo($license);

        $this->assertEquals($expireTime, $licenseInfo->expireAt);
        $this->assertEquals('License expired!', $licenseInfo->invalidReason);

        $this->assertFalse($this->offlineLicense->validate($license));
    }

    /** @test */
    public function it_should_is_valid_license_with_other_secret()
    {
        $license = $this->offlineLicense->generate();
        $this->assertTrue($this->offlineLicense->validate($license));

        $this->assertFalse($this->offlineLicense->withSecret($this->secret.'123')->validate($license));
    }

    /** @test */
    public function it_should_return_invalid_license_info_when_invalid_length()
    {
        $license    = $this->offlineLicense->generate();

        $licenseInfo = $this->offlineLicense->getInfo($license . '123');

        $this->assertFalse($licenseInfo->valid);
        $this->assertFalse($licenseInfo->validChecksum);
    }

    /** @test */
    public function it_should_return_invalid_license_info_when_invalid_checksum()
    {
        $license    = $this->offlineLicense->withSecret($this->secret . '123')->generate();

        $licenseInfo = $this->offlineLicense->getInfo($license);

        $this->assertFalse($licenseInfo->valid);
        $this->assertFalse($licenseInfo->validChecksum);
        $this->assertEquals('Invalid license!', $licenseInfo->invalidReason );
    }
}
