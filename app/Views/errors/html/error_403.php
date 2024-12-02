<?= $this->extend('templates/menu'); ?>

<?= $this->section('title'); ?>
<h3>
</h3>
<?= $this->endSection(); ?>

<?= $this->section('content') ?>
<section class="content">
    <div class="error-page">
        <h2 class="headline text-danger">403</h2>

        <div class="error-content">
            <h3><i class="fas fa-exclamation-triangle text-danger"></i> Oops! Directory Access is Forbidden.</h3>

            <p>
                You are not allowed to access this page.
                You may <br /> <a href="<?= base_url('/') ?>">return to dashboard</a> or try using the search form.
            </p>

            <?= form_open('', ['id' => 'search', 'method' => 'get']) ?>
            <?= csrf_field() ?>
            <div class="input-group">
                <input type="text" id="search-input" name="search" class="form-control" placeholder="Search">
                <div class="input-group-append">
                    <button type="submit" class="btn btn-danger"><i class="fas fa-search"></i></button>
                </div>
            </div>
            <?= form_close(); ?>
        </div>
    </div>
</section>
<script>
    $(document).ready(function() {
        $('#search').submit(function(e) {
            e.preventDefault();

            var searchValue = $('#search-input').val();

            var url = "<?= base_url() ?>/" + searchValue

            window.location.href = url;
        });
    });
</script>
<?= $this->endSection(); ?>