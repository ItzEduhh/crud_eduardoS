<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateMusicsTable extends AbstractMigration
{
    public function change(): void
    {
        $this->table('musics')
            ->addColumn('name', 'string', ['limit' => 50])
            ->addColumn('text', 'string', ['limit' => 200])
            ->addColumn('autor_id', 'integer', ['signed' => false])
            ->addColumn('producer_id', 'integer', ['signed' => false])
            ->addForeignKey('autor_id', 'autors', 'id', ['delete' => 'NO ACTION', 'update' => 'NO ACTION'])            
            ->addForeignKey('producer_id', 'producers', 'id', ['delete' => 'NO ACTION', 'update' => 'NO ACTION'])
            ->create();
    }
}
