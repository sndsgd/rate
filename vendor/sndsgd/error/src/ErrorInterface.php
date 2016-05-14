<?php

namespace sndsgd;

interface ErrorInterface
{
    public function getMessage(): string;
    public function getCode(): int;
    public function toArray(): array;
}
