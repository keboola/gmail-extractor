<?php

namespace Keboola\GmailExtractor;

class Base64Url
{
    /**
     * Decodes base64url encoded data
     * @see Section 5. in https://www.ietf.org/rfc/rfc4648.txt
     * @param $data
     * @return string|bool
     */
    public static function decode($data)
    {
        return base64_decode(str_replace(['-', '_'], ['+', '/'], $data));
    }
}
