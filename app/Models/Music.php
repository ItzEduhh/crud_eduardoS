<?php

namespace App\Models;

class Music
{
    public ?int $id;
    public string $name;
    public string $text;
    public int $autor_id;

    public function __construct(?int $id, string $name, string $text, int $autor_id,)
    {
        $this->id = $id;
        $this->name = $name;
        $this->text = $text;
        $this->autor_id = $autor_id;
    }
}
