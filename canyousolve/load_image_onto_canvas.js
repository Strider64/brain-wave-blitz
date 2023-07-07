
    let theCanvas, ctx;

    theCanvas = document.getElementById('canvas');
    ctx = theCanvas.getContext('2d');

    theCanvas.width = 375;
    theCanvas.height = 250;

    let imageObj = new Image();

    imageObj.onload = function () {
        let w = theCanvas.width;
        let nw = imageObj.naturalWidth;
        let nh = imageObj.naturalHeight;
        let aspect = nw / nh;
        let h = theCanvas.width / aspect;
        theCanvas.height = h;
        imageToBeLoaded();
    }
    export function imageToBeLoaded() {
        // Draw the current part on the canvas using the drawImage method
        ctx.drawImage(
            imageObj,                             // the image object to draw from
            0,                                  // the X position on the destination canvas to start drawing to
            0,                                  // the Y position on the destination canvas to start drawing to
            imageObj.naturalWidth,                // the width of the part on the source image
            imageObj.naturalHeight,               // the height of the part on the source image
            0,                                  // the X position on the source image to start drawing from
            0,                                  // the Y position on the source image to start drawing from
            theCanvas.width,                       // the width of the part on the destination canvas
            theCanvas.height                       // the height of the part on the destination canvas (in this case, the full height of the canvas)
        );
    }

    // Set the image source
    export function changeImage(newImagePath) {
        imageObj.src = newImagePath;
        console.log(imageObj.src);
    }