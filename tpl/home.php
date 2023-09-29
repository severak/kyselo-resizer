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

        <progress class="progress" value="0" max="100" id="progress" style="display: none">uploading...</progress>

        <button class="button is-primary is-fullwidth" id="uploadButton">Upload</button>
    </div>
</form>

<script src="/static/uploader.min.js"></script>
<script>
    var uploader = new Uploader({
        target: '/upload-chunked',
        singleFile: true,
        testChunks: false
    });

    if (!uploader.support) {
        // uploader is not supported, uploading by HTML
        const fileInput = document.querySelector('input[type=file]');
        fileInput.onchange = () => {
            if (fileInput.files.length > 0) {
                const fileName = document.querySelector('.file-name');
                fileName.textContent = fileInput.files[0].name;
            }
        }
    } else {
        // uploader supported using that
        uploader.assignBrowse(document.querySelector('input[type=file]'));

        uploader.on('fileAdded', function (file, event) {
            setTimeout(function (){
                if (!uploader.isUploading()) {
                    document.getElementById('uploadButton').style.display = 'none';
                    document.getElementById('progress').style.display = 'block';
                    uploader.upload();
                }
            }, 1000);
        });

        uploader.on('fileSuccess', function (rootFile, file, message) {
            var message = JSON.parse(message);
            if (message.status && message.status==='done') {
                document.location.href = '/upload/' + message.hash;
            }
        });

        uploader.on('fileProgress', function (rootFile, file, chunk){
            // console.log('progress ' + (uploader.progress() * 100)  + '%');
            document.getElementById('progress').value = uploader.progress() * 100;
        });
    }
</script>

<?= render('_footer'); ?>
