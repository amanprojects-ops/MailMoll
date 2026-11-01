<?php

namespace App\Services;

use RuntimeException;

class CryptoService
{
    private string $key;
    private string $cipher = 'aes-256-gcm';

    public function __construct(?string $key = null)
    {
        $appKey = $key ?? config('app.key');
        if (empty($appKey)) {
            throw new RuntimeException("Encryption key APP_KEY is not configured.");
        }
        // Derive exact 32-byte binary key
        $this->key = hash('sha256', $appKey, true);
    }

    public function encrypt(string $plainText): string
    {
        $ivLength = openssl_cipher_iv_length($this->cipher);
        $iv = openssl_random_pseudo_bytes($ivLength);
        $tag = '';

        $ciphertext = openssl_encrypt(
            $plainText,
            $this->cipher,
            $this->key,
            OPENSSL_RAW_DATA,
            $iv,
            $tag,
            '',
            16
        );

        if ($ciphertext === false) {
            throw new RuntimeException("Failed to encrypt data using OpenSSL.");
        }

        // Package: 12-byte IV + 16-byte Tag + Ciphertext
        return base64_encode($iv . $tag . $ciphertext);
    }

    public function decrypt(string $payload): ?string
    {
        $data = base64_decode($payload, true);
        if ($data === false) {
            return null;
        }

        $ivLength = openssl_cipher_iv_length($this->cipher);
        $tagLength = 16;

        if (strlen($data) < ($ivLength + $tagLength)) {
            return null;
        }

        $iv = substr($data, 0, $ivLength);
        $tag = substr($data, $ivLength, $tagLength);
        $ciphertext = substr($data, $ivLength + $tagLength);

        $plainText = openssl_decrypt(
            $ciphertext,
            $this->cipher,
            $this->key,
            OPENSSL_RAW_DATA,
            $iv,
            $tag
        );

        return $plainText !== false ? $plainText : null;
    }
}
