<?php

namespace Fickrr\Support\Visits;

class CountryResolver
{
    public static function resolve(array $headers, string $ip, ?string $database = null): ?string
    {
        $fromHeader = ClientAddress::countryFromHeaders($headers);
        if ($fromHeader !== null) {
            return $fromHeader;
        }
        if ($ip === '' || $database === null || ! is_file($database) || ! class_exists(\GeoIp2\Database\Reader::class)) {
            return null;
        }
        try {
            $reader = new \GeoIp2\Database\Reader($database);
            $code = strtoupper((string) $reader->country($ip)->country->isoCode);
            $reader->close();
            if (strlen($code) === 2 && ctype_alpha($code)) {
                return $code;
            }
        } catch (\Throwable $e) {
            return null;
        }

        return null;
    }
}
