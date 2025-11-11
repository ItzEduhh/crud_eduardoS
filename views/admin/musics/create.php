<?php $this->layout('layouts/admin', ['title' => 'Nova Música']) ?>

<?php $this->start('body') ?>
<div class="card shadow-sm" id="formView">
    <?php $this->insert('partials/admin/form/header', ['title' => 'Nova Música']) ?>
    <div class="card-body">
        <form method="post" action="/admin/musics/store" enctype="multipart/form-data" class="">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="name" class="form-label">Nome</label>
                    <input type="text" class="form-control" id="name" name="name" placeholder="Digite o nome"
                           value="<?= $this->e(($old['name'] ?? '')) ?>">
                    <?php if (!empty($errors['name'])): ?>
                        <div class="text-danger"><?= $this->e($errors['name']) ?></div><?php endif; ?>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="text" class="form-label">Texto</label>
                    <input type="text" class="form-control" id="text" name="text"
                           placeholder="Digite o texto" value="<?= $this->e(($old['text'] ?? '')) ?>" required>
                    <?php if (!empty($errors['text'])): ?>
                        <div class="text-danger"><?= $this->e($errors['text']) ?></div><?php endif; ?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="autor_id" class="form-label">Autor</label>
                    <select class="form-select" id="autor_id" name="autor_id" required>
                        <option value="">Selecione um Autor</option>
                        <?php foreach ($autors as $autor): ?>
                        <option value="<?= $autor->id ?>" <?= $this->e(($old['autor_id'] ?? '') == $autor->id ? 'selected' : '') ?>>
                            <?= $this->e($autor->name) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (!empty($errors['autor_id'])): ?>
                        <div class="error"><?= $this->e($errors['autor_id']) ?></div>
                    <?php endif; ?>
                </div>
                <div class="col-md-6 mb-3">
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="producer_id" class="form-label">Produtora</label>
                    <select class="form-select" id="producer_id" name="producer_id" required>
                        <option value="">Selecione uma Produtora</option>
                        <?php foreach ($producers as $producer): ?>
                        <option value="<?= $producer['id'] ?>" <?= $this->e(($old['producer_id'] ?? '') == $producer['id'] ? 'selected' : '') ?>>
                            <?= $this->e($producer['name']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (!empty($errors['producer_id'])): ?>
                        <div class="error"><?= $this->e($errors['producer_id']) ?></div>
                    <?php endif; ?>
                </div>
                <div class="col-md-6 mb-3">
                </div>
            </div>
            <div class="d-flex gap-3">
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-check-lg"></i> Salvar
                </button>
                <button type="reset" class="btn btn-secondary">
                    <i class="bi bi-x-lg"></i> Limpar
                </button>
                <a href="/admin/musics" class="btn align-self-end">
                    <i class="bi bi-x-lg"></i> Cancelar
                </a>
            </div>
            <?= \App\Core\Csrf::input() ?>
        </form>
    </div>
</div>
<?php $this->stop() ?>
