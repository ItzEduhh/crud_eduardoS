<?php

namespace App\Models;

class Music
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
