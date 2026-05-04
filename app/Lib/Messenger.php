<?php
namespace PHPMVC\Lib;

class Messenger
{
    public const APP_MESSAGE_SUCCESS       = 1;
    public const APP_MESSAGE_ERROR         = 2;
    public const APP_MESSAGE_WARNING       = 3;
    public const APP_MESSAGE_INFO          = 4;

    private static ?self $_instance = null;
    private array $_messages = [];

    private function __construct(private SessionManager $_session) {}

    private function __clone() {}

    public static function getInstance(SessionManager $session): self
    {
        if (self::$_instance === null) {
            self::$_instance = new self($session);
        }
        return self::$_instance;
    }

    public function add(string $message, int $type = self::APP_MESSAGE_SUCCESS): void
    {
        if (!$this->messagesExists()) {
            $this->_session->messages = [];
        }
        $msgs = $this->_session->messages;
        $msgs[] = [$message, $type];
        $this->_session->messages = $msgs;
    }

    private function messagesExists(): bool
    {
        return isset($this->_session->messages);
    }

    public function getMessages(): array
    {
        if ($this->messagesExists()) {
            $this->_messages = $this->_session->messages;
            unset($this->_session->messages);
            return $this->_messages;
        }
        return [];
    }
}
