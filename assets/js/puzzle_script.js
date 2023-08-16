let canvas = document.getElementById('puzzleCanvas');
let ctx = canvas.getContext('2d');

let snapSound = new Audio('assets/audio/audio-snap-001.ogg');
let celebrateSound = new Audio('assets/audio/audio-celebration-001.wav');


let image = new Image();
image.src = 'assets/puzzle_images/img-lego-puzzle-001.jpg';  // Update this path to your image

let pieces = [];
const PIECE_COUNT = 4;  // example for a 2x2 puzzle

let draggedPiece = null;
let offsetX, offsetY;  // To manage the position of the mouse relative to the top-left of the piece
let isSolved = false;

const MAX_ATTEMPTS = 100; // or some other suitable number

function handleMouseDown(e) {
    if (isSolved) return;  // If the puzzle is solved, exit early

    let mouseX = e.clientX - canvas.getBoundingClientRect().left;
    let mouseY = e.clientY - canvas.getBoundingClientRect().top;

    // Find if a piece is under the cursor
    for (let piece of pieces) {
        if (mouseX > piece.sx && mouseX < piece.sx + piece.width &&
            mouseY > piece.sy && mouseY < piece.sy + piece.height) {
            draggedPiece = piece;

            // Calculate the offset
            offsetX = mouseX - piece.sx;
            offsetY = mouseY - piece.sy;
            break;
        }
    }
}

function handleMouseMove(e) {
    if (draggedPiece) {
        // Update the piece's drawn position
        draggedPiece.sx = e.clientX - canvas.getBoundingClientRect().left - offsetX;
        draggedPiece.sy = e.clientY - canvas.getBoundingClientRect().top - offsetY;

        // Redraw the entire canvas (backgrounds + pieces)
        redrawCanvas();
    }
}

function handleMouseUp(e) {
    if (draggedPiece) {
        const imageX = (canvas.width - image.width) / 2;
        const imageY = (canvas.height - image.height) / 2;

        let targetX = draggedPiece.x + imageX;
        let targetY = draggedPiece.y + imageY;
        let threshold = 20;  // Snap into place if within 20 pixels

        if (Math.abs(draggedPiece.sx - targetX) < threshold && Math.abs(draggedPiece.sy - targetY) < threshold) {
            draggedPiece.sx = targetX;
            draggedPiece.sy = targetY;
            draggedPiece.snapped = true;  // Mark as correctly placed

            snapSound.play();  // Play the snap sound
        }

        redrawCanvas();
    }
    if (checkForCompletion()) {
        canvas.removeEventListener('mousedown', handleMouseDown);
        canvas.removeEventListener('mousemove', handleMouseMove);
        canvas.removeEventListener('mouseup', handleMouseUp);
        celebrateSound.play();
        alert('Congratulations! Puzzle completed!');
    }

    draggedPiece = null;  // Reset the dragged piece
}

canvas.addEventListener('mousedown', handleMouseDown);
canvas.addEventListener('mousemove', handleMouseMove);
canvas.addEventListener('mouseup', handleMouseUp);


function checkForCompletion() {
    for (let piece of pieces) {
        if (!piece.snapped) {
            return false;  // Not all pieces are correctly placed
        }
    }
    isSolved = true;  // Set the flag here
    return true;
}

function redrawCanvas() {
    // 1. Clear the canvas
    ctx.clearRect(0, 0, canvas.width, canvas.height);

    // 2. Redraw the entire canvas background
    ctx.fillStyle = "#E0E0E0";  // Light gray for demonstration
    ctx.fillRect(0, 0, canvas.width, canvas.height);

    // 2.1 Redraw the background color for the image portion (assuming it's centrally aligned)
    const imageX = (canvas.width - image.width) / 2;
    const imageY = (canvas.height - image.height) / 2;
    ctx.fillStyle = "#FFFFFF";  // White for demonstration
    ctx.fillRect(imageX, imageY, image.width, image.height);

    // 3. Redraw each piece
    for (let piece of pieces) {
        ctx.drawImage(image, piece.x, piece.y, piece.width, piece.height, piece.sx, piece.sy, piece.width, piece.height);
    }
}

let spacing = 30;  // Adjust this value based on your preference

function doesOverlap(piece1, piece2) {
    return piece1.sx < piece2.sx + piece2.width + spacing &&
        piece1.sx + piece1.width + spacing > piece2.sx &&
        piece1.sy < piece2.sy + piece2.height + spacing &&
        piece1.sy + piece1.height + spacing > piece2.sy;
}

image.onload = function() {
    let pieceWidth = image.width / PIECE_COUNT;
    let pieceHeight = image.height / PIECE_COUNT;

    // 1. Clear the entire canvas
    ctx.clearRect(0, 0, canvas.width, canvas.height);

    // 2. Set the background color for the entire canvas
    ctx.fillStyle = "#E0E0E0";  // Light gray for demonstration
    ctx.fillRect(0, 0, canvas.width, canvas.height);

    // 2.1 Set the background color for the image portion (assuming it's centrally aligned)
    const imageX = (canvas.width - image.width) / 2;
    const imageY = (canvas.height - image.height) / 2;
    ctx.fillStyle = "#FFFFFF";  // White for demonstration
    ctx.fillRect(imageX, imageY, image.width, image.height);

    for (let x = 0; x < PIECE_COUNT; x++) {
        for (let y = 0; y < PIECE_COUNT; y++) {
            let piece;
            let isOverlapping;

            let attempts = 0;
            do {
                isOverlapping = false;

                piece = {
                    x: x * pieceWidth,
                    y: y * pieceHeight,
                    sx: Math.random() * (canvas.width - pieceWidth),
                    sy: Math.random() * (canvas.height - pieceHeight),
                    width: pieceWidth,
                    height: pieceHeight
                };

                // Check if the piece is within the white space (intended image area)
                if (piece.sx > imageX && piece.sx + piece.width < imageX + image.width &&
                    piece.sy > imageY && piece.sy + piece.height < imageY + image.height) {
                    isOverlapping = true;  // Mark as overlapping so a new position is calculated
                }

                for (let placedPiece of pieces) {
                    if (doesOverlap(piece, placedPiece)) {
                        isOverlapping = true;
                        break;
                    }
                }

                attempts++;

                if (attempts > MAX_ATTEMPTS) {
                    console.warn("Could not find a non-overlapping position outside the intended image area after maximum attempts.");
                    break;
                }
            } while (isOverlapping);


            pieces.push(piece);
            ctx.drawImage(image, piece.x, piece.y, piece.width, piece.height, piece.sx, piece.sy, pieceWidth, pieceHeight);
        }
    }
};




