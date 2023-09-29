<?= render('_header'); ?>

<form action="/upload/" method="post" enctype="multipart/form-data">
    <div class="buttons">

        <div class="field">
            <div class="file has-name is-fullwidth">
                <label class="file-label">
                    <input class="file-input" type="file" name="upload">
                    <span class="file-cta">
      <span class="file-icon">
        <i class="fas fa-upload"></i>
      </span>
      <span class="file-label">
        Choose a file…
      </span>
    </span>
                    <span class="file-name">nothing selected...</span>
                </label>
            </div>
        </div>

        <button class="button is-primary is-fullwidth">Upload</button>
    </div>
</form>

<script>
    const fileInput = document.querySelector('input[type=file]');
    fileInput.onchange = () => {
        if (fileInput.files.length > 0) {
            const fileName = document.querySelector('.file-name');
            fileName.textContent = fileInput.files[0].name;
        }
    }
</script>

<?= render('_footer'); ?>
