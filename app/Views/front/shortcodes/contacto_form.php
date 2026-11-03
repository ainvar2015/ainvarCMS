<div class="card shadow-sm my-4">
    <div class="card-body">
        <h4 class="mb-3"><i class="bi bi-envelope"></i> <?= esc($titulo) ?></h4>
        
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
        <?php endif; ?>

        <form action="<?= base_url('form/submit') ?>" method="post">
            <?= csrf_field() ?>
            <input type="hidden" name="page_id" value="0">
            <input type="hidden" name="page_slug" value="contacto">

            <div class="row g-2">
                <div class="col-md-6">
                    <input type="text" name="template_fields[nombre]" class="form-control" placeholder="Nombre" required>
                </div>
                <div class="col-md-6">
                    <input type="email" name="template_fields[email]" class="form-control" placeholder="Email" required>
                </div>
                <div class="col-12">
                    <textarea name="template_fields[mensaje]" class="form-control" rows="4" placeholder="Mensaje..." required></textarea>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-send"></i> Enviar</button>
                </div>
            </div>
        </form>
    </div>
</div>