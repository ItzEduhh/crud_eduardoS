<?php
namespace App\Services;

use App\Models\Music;

class MusicService {
    public function validate(array $data): array {
        $errors = [];
        $name = trim($data['name'] ?? '');
        $autor_id = $data['autor_id'] ?? '';
    
        if ($name === '') $errors['name'] = 'Nome é obrigatório';
        if ($autor_id === '') $errors['autor_id'] = 'Autor é obrigatório';

        return $errors;
    }

    public function make(array $data): Music {
        $name = trim($data['name'] ?? '');
        $text = trim($data['text'] ?? '');
        $autor_id = (int)($data['autor_id'] ?? 0);
        $id = isset($data['id']) ? (int)$data['id'] : null;
        return new Music($id, $name, $text, $autor_id);
    }
}
