</main>
</div>
</section>
<section class="section" id="qrscanner" style="display: none">
    <div class="container" style="max-width: 800px">
        <div id="qr_loadingMessage">🎥 Unable to access video stream (please make sure you have a webcam enabled)</div>
        <div style="max-width: 100%">
            <canvas id="qr_canvas" hidden style="max-width: 100%"></canvas>
        </div>
        <div id="qr_output" hidden>
            <div id="qr_outputMessage">No QR code detected.</div>
            <div hidden><b>Data:</b> <span id="qr_outputData"></span></div>
        </div>
        <a href="#" id="qr_manual" class="button is-fullwidth">zadat QR kód ručně</a>
    </div>
</section>
<script src="/static/uboot.js"></script>
<script>
whenReady(function () {
    on('navbar-burger', 'click', function () {
        if (hasClass('navbar-burger', 'is-active')) {
            delClass('navbar-burger', 'is-active');
            delClass('navbar-menu', 'is-active');
        } else {
            addClass('navbar-burger', 'is-active');
            addClass('navbar-menu', 'is-active');
        }
    });
});
</script>
<script src="/static/jsQR.js"></script>
<script>
whenReady(function () {
    var video = document.createElement("video");
    var canvasElement = document.getElementById("qr_canvas");
    var canvas = canvasElement.getContext("2d");
    var loadingMessage = document.getElementById("qr_loadingMessage");
    var outputContainer = document.getElementById("qr_output");
    var outputMessage = document.getElementById("qr_outputMessage");
    var outputData = document.getElementById("qr_outputData");
    var localstream;

    function videoOff() {
        //clearInterval(theDrawLoop);
        //ExtensionData.vidStatus = 'off';
        video.pause();
        video.src = "";
        if (localstream) localstream.getTracks()[0].stop();
        //console.log("Vid off");
    }

    function drawLine(begin, end, color) {
        canvas.beginPath();
        canvas.moveTo(begin.x, begin.y);
        canvas.lineTo(end.x, end.y);
        canvas.lineWidth = 4;
        canvas.strokeStyle = color;
        canvas.stroke();
    }

    function tick() {
        loadingMessage.innerText = "⌛ Loading video..."
        if (video.readyState === video.HAVE_ENOUGH_DATA) {
            loadingMessage.hidden = true;
            canvasElement.hidden = false;
            outputContainer.hidden = false;

            canvasElement.height = video.videoHeight;
            canvasElement.width = video.videoWidth;
            canvas.drawImage(video, 0, 0, canvasElement.width, canvasElement.height);
            var imageData = canvas.getImageData(0, 0, canvasElement.width, canvasElement.height);
            var code = jsQR(imageData.data, imageData.width, imageData.height, {
                inversionAttempts: "dontInvert",
            });
            if (code) {
                drawLine(code.location.topLeftCorner, code.location.topRightCorner, "#FF3B58");
                drawLine(code.location.topRightCorner, code.location.bottomRightCorner, "#FF3B58");
                drawLine(code.location.bottomRightCorner, code.location.bottomLeftCorner, "#FF3B58");
                drawLine(code.location.bottomLeftCorner, code.location.topLeftCorner, "#FF3B58");
                outputMessage.hidden = true;
                outputData.parentElement.hidden = false;
                outputData.innerText = code.data;

                gebi('qrcode').value = code.data;
                gebi('app').style.display = 'block';
                gebi('qrscanner').style.display = 'none';

                videoOff();
                return;

            } else {
                outputMessage.hidden = false;
                outputData.parentElement.hidden = true;
            }
        }
        requestAnimationFrame(tick);
    }

    if (gebi('qrcode')) {
        on('qrcode', 'click', function (event) {
            // schovame app a odkryjem QR scanner
            gebi('app').style.display = 'none';
            gebi('qrscanner').style.display = 'block';

            // Use facingMode: environment to attemt to get the front camera on phones
            navigator.mediaDevices.getUserMedia({ video: { facingMode: "environment" } }).then(function(stream) {
                video.srcObject = stream;
                localstream = stream;
                video.setAttribute("playsinline", true); // required to tell iOS safari we don't want fullscreen
                video.play();
                requestAnimationFrame(tick);
            }).catch(function () {
                gebi('app').style.display = 'block';
                gebi('qrscanner').style.display = 'none';
                gebi('qrcode').focus();
                videoOff();
            });

            event.preventDefault();
        });

        on('qr_manual', 'click', function (event) {
            gebi('app').style.display = 'block';
            gebi('qrscanner').style.display = 'none';
            gebi('qrcode').focus();
            videoOff();
            event.preventDefault();
        });
    }



});
</script>
</body>
</html>