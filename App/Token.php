<?php

namespace App;

use Exception;

/**
 * Token class
 */
class Token
{
    /**
     * The token value
     * @var string $token
     */
    private $token;

    /**
     * Class constructor. Create a new random token composed of 16 bytes = 128 bits = 32 hex characters
     *
     * @param string (optional) $tokenValue
     *
     * @throws Exception
     */
    public function __construct($tokenValue = null)
    {
        if ($tokenValue) {
            $this->token = $tokenValue;
        } else {
            $this->token = bin2hex(random_bytes(16));
        }
    }

    /**
     * Get the token value
     *
     * @return string The value
     */
    public function getValue()
    {
        return $this->token;
    }

    /**
     * Get the hashed token value (sha256 = 64 chars)
     *
     * @return string The hashed value
     */
    public function getHash()
    {
        return hash_hmac('sha256', $this->token, Config::secretKey());
    }
}
