<?php
namespace App\Services;

use App\Models\Producer;

class ProducerService {
    public function validate(array $data): array {
        $errors = [];
        $name = trim($data['name'] ?? '');
    
        if ($name === '') $errors['name'] = 'Nome é obrigatório';

        return $errors;
    }

    public function make(array $data): Producer {
        $name = trim($data['name'] ?? '');
        $text = trim($data['text'] ?? '');
        $id = isset($data['id']) ? (int)$data['id'] : null;
        return new Producer($id, $name, $text);
    }
}
