// Declare the canvas and context variables
let canvas, ctx;

// Variable to keep track of the current part being displayed
export let currentPart = -1;

export function resetCurrentPart() {
    currentPart = -1;
}

// Function to reset the canvas and redraw the initial parts of the image
export function resetCanvas() {
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    resetCurrentPart();
    drawParts();
}

// Get the canvas element and its 2D context
canvas = document.getElementById("canvas");
ctx = canvas.getContext("2d");

canvas.width = 375;
canvas.height = 250;

// Function to draw the opening screen
function drawOpeningScreen() {
    ctx.fillStyle = "rgba(128,0,128, 1)";
    ctx.fillRect(0, 0, canvas.width, canvas.height);
    ctx.fillStyle = "#ffffff";
    ctx.font = "24px Arial";
    ctx.textAlign = "center";
    canvas.style.cursor = 'pointer';
    ctx.fillText("Click to Start", canvas.width / 2, canvas.height / 2);
}

export function newCanvas() {
    ctx.fillStyle = "rgba(255, 255, 255, 1)";
    ctx.fillRect(0, 0, canvas.width, canvas.height);
}

// Function to start the game
function startGame() {
    document.querySelector('.hangman').style.display = 'flex';
    resetCanvas();
    canvas.removeEventListener("click", startGame);
}

// Add a click event listener to the canvas
//canvas.addEventListener("click", startGame);

// Create a new Image object
let imgObj = new Image();

// Set the onload event for the image object
imgObj.onload = function () {
    // Calculate the aspect ratio and canvas height based on the image dimensions
    let w = canvas.width;
    let nw = imgObj.naturalWidth;
    let nh = imgObj.naturalHeight;
    let aspect = nw / nh;
    let h = canvas.width / aspect;
    canvas.height = h;

    // Draw the initial parts of the image
    drawParts();
};

// Set the image source
export function changeImageSource(newImagePath) {
    imgObj.src = newImagePath;
}


// Function to handle the button click event
export const revealPartOfImage = () => {
    // Increment the current part & ensure it doesn't exceed the total # of parts
    currentPart = Math.min(currentPart + 1, 5);
    drawParts()
};

export function fullImage() {
    // Draw the current part on the canvas using the drawImage method
    ctx.drawImage(
        imgObj,                             // the image object to draw from
        0,                                  // the X position on the destination canvas to start drawing to
        0,                                  // the Y position on the destination canvas to start drawing to
        imgObj.naturalWidth,                // the width of the part on the source image
        imgObj.naturalHeight,               // the height of the part on the source image
        0,                                  // the X position on the source image to start drawing from
        0,                                  // the Y position on the source image to start drawing from
        canvas.width,                       // the width of the part on the destination canvas
        canvas.height                       // the height of the part on the destination canvas (in this case, the full height of the canvas)
    );
}

// Function to draw the parts of the image based on the current part
function drawParts() {
    // Set the total number of parts to draw, the gap between parts, and the width of each part
    const numParts = 6;                         // total number of parts
    const gap = 5;                              // gap between parts
    const partWidth = (canvas.width - (numParts - 1) * gap) / numParts;   // width of each part

    // Loop through the parts to be drawn and draw each one on the canvas
    for (let i = 0; i <= currentPart; i++) {    // loop through each part up to the current part
        let sx = (i * imgObj.naturalWidth) / numParts; // calculate the X position of the source image for this part
        let dx = i * (partWidth + gap);         // calculate the X position of the destination canvas for this part

        // Draw the current part on the canvas using the drawImage method
        ctx.drawImage(
            imgObj,                             // the image object to draw from
            sx,                                 // the X position on the source image to start drawing from
            0,                                  // the Y position on the source image to start drawing from
            imgObj.naturalWidth / numParts,     // the width of the part on the source image
            imgObj.naturalHeight,               // the height of the part on the source image
            dx,                                 // the X position on the destination canvas to start drawing to
            0,                                  // the Y position on the destination canvas to start drawing to
            partWidth,                          // the width of the part on the destination canvas
            canvas.height                       // the height of the part on the destination canvas (in this case, the full height of the canvas)
        );
    }
}


