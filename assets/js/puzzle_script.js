// 1. Initialize canvas, context, and audio assets
let canvas = document.getElementById('puzzleCanvas');
let ctx = canvas.getContext('2d');
let snapSound = new Audio('assets/audio/audio-snap-001.ogg');
let celebrateSound = new Audio('assets/audio/audio-celebration-001.wav');

// 2. Setup puzzle image
let image = new Image();
image.src = 'assets/puzzle_images/img-lego-puzzle-001.jpg';

let pieces = [];
const PIECE_COUNT = 4;  // Number of puzzle pieces along one dimension
let draggedPiece = null;
let offsetX, offsetY;  // Offset from the top-left corner of the dragged piece
let isSolved = false;

const MAX_ATTEMPTS = 100;  // Maximum attempts for finding non-overlapping positions for puzzle pieces

// 3. Event listeners for dragging puzzle pieces

// On mouse down, check if any piece is clicked
const handleMouseDown = e => {
    if (isSolved) return;

    let mouseX = e.clientX - canvas.getBoundingClientRect().left;
    let mouseY = e.clientY - canvas.getBoundingClientRect().top;

    for (let piece of pieces) {
        if (mouseX > piece.sx && mouseX < piece.sx + piece.width &&
            mouseY > piece.sy && mouseY < piece.sy + piece.height) {
            draggedPiece = piece;

            offsetX = mouseX - piece.sx;
            offsetY = mouseY - piece.sy;
            break;
        }
    }
};

// On mouse move, move the dragged piece with the cursor
const handleMouseMove = e => {
    if (draggedPiece) {
        draggedPiece.sx = e.clientX - canvas.getBoundingClientRect().left - offsetX;
        draggedPiece.sy = e.clientY - canvas.getBoundingClientRect().top - offsetY;

        redrawCanvas();
    }
};

// On mouse up, release the piece and snap it if close to its correct position
const handleMouseUp = () => {
    if (draggedPiece) {
        const imageX = (canvas.width - image.width) / 2;
        const imageY = (canvas.height - image.height) / 2;
        let targetX = draggedPiece.x + imageX;
        let targetY = draggedPiece.y + imageY;
        let threshold = 20;

        if (Math.abs(draggedPiece.sx - targetX) < threshold && Math.abs(draggedPiece.sy - targetY) < threshold) {
            draggedPiece.sx = targetX;
            draggedPiece.sy = targetY;
            draggedPiece.snapped = true;  // Piece is now in its correct position
            snapSound.play();
        }

        redrawCanvas();
    }
    if (checkForCompletion()) {
        // Remove mouse events after puzzle is solved
        canvas.removeEventListener('mousedown', handleMouseDown);
        canvas.removeEventListener('mousemove', handleMouseMove);
        canvas.removeEventListener('mouseup', handleMouseUp);

        celebrateSound.play();
        alert('Congratulations! Puzzle completed!');
    }
    draggedPiece = null;
};

// Add mouse event listeners
canvas.addEventListener('mousedown', handleMouseDown);
canvas.addEventListener('mousemove', handleMouseMove);
canvas.addEventListener('mouseup', handleMouseUp);

// 4. Puzzle completion check
function checkForCompletion() {
    for (let piece of pieces) {
        if (!piece.snapped) {
            return false;
        }
    }
    isSolved = true;  // Puzzle is completed
    return true;
}

// 5. Redraws the entire canvas
function redrawCanvas() {
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    ctx.fillStyle = "#E0E0E0";
    ctx.fillRect(0, 0, canvas.width, canvas.height);
    const imageX = (canvas.width - image.width) / 2;
    const imageY = (canvas.height - image.height) / 2;
    ctx.fillStyle = "#FFFFFF";
    ctx.fillRect(imageX, imageY, image.width, image.height);

    for (let piece of pieces) {
        ctx.drawImage(image, piece.x, piece.y, piece.width, piece.height, piece.sx, piece.sy, piece.width, piece.height);
    }
}

// 6. Function to check if two pieces overlap
function doesOverlap(piece1, piece2) {
    return piece1.sx < piece2.sx + piece2.width &&
        piece1.sx + piece1.width > piece2.sx &&
        piece1.sy < piece2.sy + piece2.height &&
        piece1.sy + piece1.height > piece2.sy;
}

// 7. Image loading and piece setup
image.onload = function() {
    let pieceWidth = image.width / PIECE_COUNT;
    let pieceHeight = image.height / PIECE_COUNT;

    ctx.clearRect(0, 0, canvas.width, canvas.height);
    ctx.fillStyle = "#E0E0E0";
    ctx.fillRect(0, 0, canvas.width, canvas.height);
    const imageX = (canvas.width - image.width) / 2;
    const imageY = (canvas.height - image.height) / 2;
    ctx.fillStyle = "#FFFFFF";
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
                if (piece.sx > imageX && piece.sx + piece.width < imageX + image.width &&
                    piece.sy > imageY && piece.sy + piece.height < imageY + image.height) {
                    isOverlapping = true;
                }
                for (let placedPiece of pieces) {
                    if (doesOverlap(piece, placedPiece)) {
                        isOverlapping = true;
                        break;
                    }
                }
                attempts++;
                if (attempts > MAX_ATTEMPTS) {
                    console.warn("Could not find a non-overlapping position after maximum attempts.");
                    break;
                }
            } while (isOverlapping);

            pieces.push(piece);
            ctx.drawImage(image, piece.x, piece.y, piece.width, piece.height, piece.sx, piece.sy, pieceWidth, pieceHeight);
        }
    }
};
