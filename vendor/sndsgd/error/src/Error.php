<?php

namespace sndsgd;

class Error implements ErrorInterface, \JsonSerializable
{
    /**
     * A public facing message
     * @var string
     */
    protected $message;

    /**
     * A code that indicates a specific error case
     * @var int
     */
    protected $code;

    public function __construct(string $message, int $code = 0)
    {
        $this->message = $message;
        $this->code = $code;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getCode(): int
    {
        return $this->code;
    }

    public function toArray(): array
    {
        return [
            "message" => $this->getMessage(),
            "code" => $this->getCode(),
        ];
    }

    /**
     * @see http://php.net/manual/en/jsonserializable.jsonserialize.php
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
