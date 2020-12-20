<?php

namespace Nddcoder\PhpOfflineLicense;

class PhpOfflineLicense
{
    protected string $randomString = '0123456789abcdef';
    protected int $randomLength = 16;
    protected array $timestampPosition = [1, 3, 5, 9, 12, 15, 16, 19];
    protected array $checkSumPosition = [0, 5, 8, 12, 18, 24, 26, 30];
    protected array $uuidPosition = [8, 13, 18, 23]; //UUID v4
    protected string $secret = '';

    public function __construct(string $secret)
    {
        $this->secret = $secret;
    }


    public static function create(string $secret): static
    {
        return new static($secret);
    }

    public function withSecret(string $secret): static
    {
        return static::create($secret);
    }

    ////Generate area

    public function generate(?int $expireAt = null): string
    {
        $output        = is_null($expireAt) ? $this->getRandomOddString() : $this->getRandomEvenString();
        $randomStrings = str_split($this->randomString);
        for ($i = 0; $i < $this->randomLength - 1; $i++) {
            $output .= $this->arrayRandom($randomStrings);
        }

        //Ensure this output is unique
        $output = $this->mixWithTimestamp($output, $expireAt);
        //Add checksum
        $output = $this->mixWithChecksum($output);

        return $this->mixLikeUUID($output);
    }

    protected function getRandomOddString(): string
    {
        return $this->randomString[$this->arrayRandom(range(1, strlen($this->randomString) - 1, 2))];
    }

    protected function getRandomEvenString(): string
    {
        return $this->randomString[$this->arrayRandom(range(0, strlen($this->randomString) - 1, 2))];
    }

    protected function arrayRandom($array): mixed
    {
        return $array[array_rand($array)];
    }

    protected function mixWithTimestamp(string $output, ?int $timestamp = null): string
    {
        $timestamp    ??= time();
        $hexTimestamp = dechex($timestamp);
        $mixChars     = substr($hexTimestamp, strlen($hexTimestamp) - count($this->timestampPosition));
        return $this->mix($output, $mixChars, $this->timestampPosition);
    }

    /**
     * @param  string  $str
     * @param  string  $chars
     * @param  array  $positions
     * @return string
     */
    protected function mix(string $str, string $chars, array $positions): string
    {
        $mixLength = count($positions);

        $output = $str;

        for ($i = 0; $i < $mixLength; $i++) {
            $output = substr($output, 0, $positions[$i])
                .$chars[$i].substr($output, $positions[$i]);
        }

        return $output;
    }

    protected function mixWithChecksum(string $str): string
    {
        $checksum = md5($str.$this->secret);
        $mixChars = substr($checksum, strlen($checksum) - count($this->checkSumPosition));

        return $this->mix($str, $mixChars, $this->checkSumPosition);
    }

    protected function mixLikeUUID(string $str): string
    {
        return $this->mix($str, str_repeat('-', count($this->uuidPosition)), $this->uuidPosition);
    }

    ////Validate area

    public function getInfo(string $license): LicenseInfo
    {
        $licenseInfo = new LicenseInfo();

        if (!$this->isValidLength($license)) {
            return $licenseInfo;
        }

        [$checksum, $license] = $this->extractValue($license, $this->checkSumPosition);
        $licenseInfo->validChecksum = $this->isValidChecksum($license, $checksum);
        [$timestampStr, $license] = $this->extractValue($license, $this->timestampPosition);

        if (!$licenseInfo->validChecksum) {
            $licenseInfo->invalidReason = 'Invalid license!';
            return $licenseInfo;
        }

        if (!$this->hasExpireTime($license)) {
            $licenseInfo->valid = true;
            return $licenseInfo;
        }

        $licenseInfo->expireAt = hexdec($timestampStr);

        if ($licenseInfo->expireAt < time()) {
            $licenseInfo->invalidReason = 'License expired!';
            return $licenseInfo;
        }

        $licenseInfo->valid = true;
        return $licenseInfo;
    }

    protected function isValidLength(string $license): bool
    {
        return strlen($license) == $this->randomLength
            + count($this->uuidPosition)
            + count($this->checkSumPosition)
            + count($this->timestampPosition);
    }

    protected function extractValue(string $license, array $positions): array
    {
        $license = str_replace('-', '', $license);
        $value   = '';
        foreach (array_reverse($positions) as $position) {
            $s       = substr($license, $position);
            $value   .= $s[0] ?? '';
            $license = substr($license, 0, $position).substr($s, 1);
        }
        return [strrev($value), $license];
    }

    protected function isValidChecksum(string $license, string $checksum): bool
    {
        return str_ends_with(md5($license.$this->secret), $checksum);
    }

    protected function hasExpireTime(string $license): bool
    {
        return strpos($this->randomString, $license[0]) % 2 == 0;
    }

    public function validate(string $license): bool
    {
        return $this->getInfo($license)->valid;
    }
}
