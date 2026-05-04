<?php
namespace PHPMVC\Lib;

class SessionManager extends \SessionHandler
{
    private string $sessionName = SESSION_NAME;
    private int $sessionMaxLifetime = SESSION_LIFE_TIME;
    private bool $sessionSSL = false;
    private bool $sessionHTTPOnly = true;
    private string $sessionPath = '/';
    private ?string $sessionDomain = null;
    private string $sessionSavePath = SESSION_SAVE_PATH;

    private string $sessionCipherAlgorithm = 'AES-256-CBC';
    private string $sessionCipherKey = SESSION_CIPHER_KEY;

    public function __construct()
    {
        $this->sessionDomain = $_SERVER['HTTP_HOST'] ?? null;

        ini_set('session.use_cookies', 1);
        ini_set('session.use_only_cookies', 1);
        ini_set('session.use_trans_sid', 0);
        ini_set('session.save_handler', 'files');

        session_name($this->sessionName);
        session_save_path($this->sessionSavePath);

        session_set_cookie_params([
            'lifetime' => $this->sessionMaxLifetime,
            'path' => $this->sessionPath,
            'domain' => $this->sessionDomain,
            'secure' => $this->sessionSSL,
            'httponly' => $this->sessionHTTPOnly,
            'samesite' => 'Lax'
        ]);

        session_set_save_handler($this, true);
    }

    public function read($id): string
    {
        $data = parent::read($id);
        if (!$data) {
            return '';
        } else {
            $ivLength = openssl_cipher_iv_length($this->sessionCipherAlgorithm);
            $iv = substr($data, 0, $ivLength);
            $cipherText = substr($data, $ivLength);
            return openssl_decrypt($cipherText, $this->sessionCipherAlgorithm, $this->sessionCipherKey, 0, $iv) ?: '';
        }
    }

    public function write($id, $data): bool
    {
        $ivLength = openssl_cipher_iv_length($this->sessionCipherAlgorithm);
        $iv = openssl_random_pseudo_bytes($ivLength);
        $cipherText = openssl_encrypt($data, $this->sessionCipherAlgorithm, $this->sessionCipherKey, 0, $iv);
        return parent::write($id, $iv . $cipherText);
    }

    public function __get(string $key): mixed
    {
        return $_SESSION[$key] ?? null;
    }

    public function __set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    public function __isset(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    public function __unset(string $key): void
    {
        unset($_SESSION[$key]);
    }

    public function start(): void
    {
        if (session_id() === '') {
            if (session_start()) {
                $this->setSessionStartTime();
                $this->checkSessionValidity();
            }
        }
    }

    private function setSessionStartTime(): void
    {
        if (!isset($this->sessionStartTime)) {
            $this->sessionStartTime = time();
        }
    }

    private function checkSessionValidity(): void
    {
        if ((time() - $this->sessionStartTime) > ($this->sessionMaxLifetime)) {
            // $this->renewSession();
        }
    }

    private function renewSession(): void
    {
        $this->sessionStartTime = time();
        session_regenerate_id(true);
    }

    public function kill(): void
    {
        session_unset();
        setcookie(
            $this->sessionName,
            '',
            time() - 1000,
            $this->sessionPath,
            $this->sessionDomain,
            $this->sessionSSL,
            $this->sessionHTTPOnly
        );
        session_destroy();
    }
}
