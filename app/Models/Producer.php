<?php

namespace App\Models;

class Producer
{
    public ?int $id;
    public string $name;
    public string $text;

    public function __construct(?int $id, string $name, string $text)
    {
        $this->id = $id;
        $this->name = $name;
        $this->text = $text;
    }
}
