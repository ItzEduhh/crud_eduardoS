<?php $this->layout('layouts/admin', ['title' => 'Detalhe da Música']) ?>

<?php $this->start('body') ?>
<div class="container mt-4">
    <div class="card shadow-sm">
        <div class="card-header bg-light">
            <h5 class="mb-0">Detalhes da Música</h5>
        </div>
        <div class="card-body">
            <form>
                <div class="mb-3">
                    <label class="form-label"><strong>ID:</strong></label>
                    <input type="text" class="form-control" value="<?= $this->e($music['id']) ?>" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label"><strong>Nome:</strong></label>
                    <input type="text" class="form-control" value="<?= $this->e($music['name']) ?>" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label"><strong>Texto:</strong></label>
                    <input type="text" class="form-control"
                           value="<?= $this->e($music['text']) ?>" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label"><strong>Autor:</strong></label>
                    <input type="text" class="form-control"
                        value="<?= $this->e($music['autor_name'] ?? '—') ?>" readonly>
                </div>

                <div class="mb-3">
                    <label class="form-label"><strong>Produtora:</strong></label>
                    <input type="text" class="form-control"
                        value="<?= $this->e($music['producer_name'] ?? '—') ?>" readonly>
                </div>
                <div class="text-end">
                    <a href="javascript:history.back()" class="btn btn-secondary">Voltar</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php $this->stop() ?>
