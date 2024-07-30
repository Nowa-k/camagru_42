let camera_button = document.querySelector("#start-camera");
let video = document.querySelector("#video");
let click_button = document.querySelector("#click-photo");
let canvas = document.querySelector("#canvas");
let createButton = document.getElementById("create");
let canvasFilled = false;
let overlaySelected = false;
let image_data_url;

function checkConditions() {
    if (canvasFilled && overlaySelected) {
        createButton.disabled = false;
        createButton.classList.remove('disabled');
    } else {
        createButton.disabled = true;
        createButton.classList.add('disabled');
    }
}

camera_button.addEventListener('click', async function() {
   	let stream = await navigator.mediaDevices.getUserMedia({ video: true, audio: false });
	video.srcObject = stream;
	video.addEventListener('loadedmetadata', function() {
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
    });
    click_button.classList.remove('disabled');
    canvas.style.display = 'none';
   	video.style.display = 'block';

});

click_button.addEventListener('click', async function() {
    canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);

    canvas.style.display = 'block';
    video.style.display = 'none';

    let tracks = video.srcObject.getTracks();
    tracks.forEach(track => track.stop());
    image_data_url = canvas.toDataURL('image/jpeg');
    canvasFilled = true;
    checkConditions();
});

const overlayPositions = {
    "overlay/ange.png": { x: "mid", y: "mid" },
    "overlay/bob.png": { x: "mid", y: "top" },
    "overlay/cat.png": { x: "left", y: "mid" },
    "overlay/demon.png": { x: "mid", y: "mid" },
    "overlay/france.png": { x: "mid", y: "bottom" },
    "overlay/lunette.png": { x: "mid", y: "top" },
    "overlay/ovnis.png": { x: "mid", y: "top" }
};

function getOverlayPosition(overlayPath, canvasWidth, canvasHeight, overlayWidth, overlayHeight) {
    const position = overlayPositions[overlayPath];
    let posX, posY;

    switch(position.x) {
        case "mid":
            posX = Math.ceil(canvasWidth / 2 - overlayWidth / 2);
            break;
        case "left":
            posX = 0;
            break;
    }

    switch(position.y) {
        case "mid":
            posY = Math.ceil(canvasHeight / 2 - overlayHeight / 2);
            break;
        case "top":
            posY = 0;
            break;
        case "bottom":
            posY = Math.ceil((canvasHeight / 4) * 3 - overlayHeight / 3);
            break;
    }

    return { x: posX, y: posY };
}

document.querySelectorAll('input[name="overlayImage"]').forEach((input) => {
    input.addEventListener('change', () => {
        if (document.getElementById('userImage').files.length > 0) {
            document.getElementById('submitButton').disabled = false;
            document.getElementById('submitButton').classList.remove('disabled');
        }

        let overlayPath = input.value;
        let overlayImage = new Image();
        overlayImage.src = overlayPath;

        overlayImage.onload = () => {
            let context = canvas.getContext('2d');
            let position = getOverlayPosition(overlayPath, canvas.width, canvas.height, overlayImage.width, overlayImage.height);

            let tempImage = new Image();
            tempImage.src = image_data_url;
            tempImage.onload = () => {
                context.drawImage(tempImage, 0, 0, canvas.width, canvas.height);
                context.drawImage(overlayImage, position.x, position.y, overlayImage.width, overlayImage.height);
                overlaySelected = true;
                checkConditions();
            };
        };
    });
});


document.getElementById('userImage').addEventListener('change', () => {
    if (document.querySelector('input[name="overlayImage"]:checked')) {
        document.getElementById('submitButton').disabled = false;

        document.getElementById('submitButton').classList.remove('disabled');
    }
});


createButton.addEventListener("click", function() {
    if (image_data_url) {
        var checkboxes = [
            document.getElementById("overlay1"),
            document.getElementById("overlay2"),
            document.getElementById("overlay3"),
            document.getElementById("overlay4"),
            document.getElementById("overlay5"),
            document.getElementById("overlay6"),
            document.getElementById("overlay7")
        ];
    
        var isAnyChecked = checkboxes.some(function(checkbox) {
            return checkbox.checked;
        });
        
        if (isAnyChecked) {
            let overlay = document.querySelector('input[name="overlayImage"]:checked').value; 
            let formData = new FormData();
            formData.append('canvasData', image_data_url);
            formData.append('overlay', overlay);

            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'index.php?controller=feed&action=create', true);
            xhr.onload = function() {
                window.location.href = 'index.php?controller=feed&action=index';
                console.log(xhr.responseText);
            };
            xhr.send(formData);
        }
    }
});
