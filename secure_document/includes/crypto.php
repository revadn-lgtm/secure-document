<?php

// Lokasi konfigurasi dan kunci RSA
putenv('OPENSSL_CONF=C:/xampp/php/extras/ssl/openssl.cnf');

define('PUBLIC_KEY_PATH', 'C:/xampp/secure_document_keys/public.pem');
define('PRIVATE_KEY_PATH', 'C:/xampp/secure_document_keys/private.pem');

// Membaca public key
function getPublicKey()
{
    $key = file_get_contents(PUBLIC_KEY_PATH);

    if ($key === false) {
        throw new Exception('Public key tidak ditemukan.');
    }

    $publicKey = openssl_pkey_get_public($key);

    if ($publicKey === false) {
        throw new Exception('Public key tidak dapat dibaca.');
    }

    return $publicKey;
}

// Membaca private key
function getPrivateKey()
{
    $key = file_get_contents(PRIVATE_KEY_PATH);

    if ($key === false) {
        throw new Exception('Private key tidak ditemukan.');
    }

    $privateKey = openssl_pkey_get_private($key);

    if ($privateKey === false) {
        throw new Exception('Private key tidak dapat dibaca.');
    }

    return $privateKey;
}

// Fungsi enkripsi dokumen
function encryptDocument($plaintext)
{
    $aesKey = random_bytes(32);
    $iv = random_bytes(12);
    $tag = '';

    $ciphertext = openssl_encrypt(
        $plaintext,
        'aes-256-gcm',
        $aesKey,
        OPENSSL_RAW_DATA,
        $iv,
        $tag
    );

    if ($ciphertext === false) {
        throw new Exception('Enkripsi dokumen gagal.');
    }

    // Mengenkripsi kunci AES menggunakan RSA public key
    $publicKey = getPublicKey();

    $encryptedAesKey = '';

    $success = openssl_public_encrypt(
        $aesKey,
        $encryptedAesKey,
        $publicKey,
        OPENSSL_PKCS1_OAEP_PADDING
    );

    if (!$success) {
        throw new Exception('Enkripsi kunci AES gagal.');
    }

    return [
        'ciphertext' => base64_encode($ciphertext),
        'iv' => base64_encode($iv),
        'auth_tag' => base64_encode($tag),
        'encrypted_aes_key' => base64_encode($encryptedAesKey)
    ];
}

// Fungsi dekripsi dokumen
function decryptDocument($document)
{
    // Membuka kembali kunci AES menggunakan RSA private key
    $privateKey = getPrivateKey();

    $encryptedAesKey = base64_decode(
        $document['encrypted_aes_key'],
        true
    );

    if ($encryptedAesKey === false) {
        throw new Exception('Format kunci AES tidak valid.');
    }

    $success = openssl_private_decrypt(
        $encryptedAesKey,
        $aesKey,
        $privateKey,
        OPENSSL_PKCS1_OAEP_PADDING
    );

    if (!$success) {
        throw new Exception('Dekripsi kunci AES gagal.');
    }

    // Mengembalikan isi dokumen seperti semula
    $ciphertext = base64_decode($document['ciphertext'], true);
    $iv = base64_decode($document['iv'], true);
    $tag = base64_decode($document['auth_tag'], true);

    if ($ciphertext === false || $iv === false || $tag === false) {
        throw new Exception('Data dokumen tidak valid.');
    }

    $plaintext = openssl_decrypt(
        $ciphertext,
        'aes-256-gcm',
        $aesKey,
        OPENSSL_RAW_DATA,
        $iv,
        $tag
    );

    if ($plaintext === false) {
        throw new Exception('Dekripsi dokumen gagal. Data mungkin telah diubah.');
    }

    return $plaintext;
}

?>
