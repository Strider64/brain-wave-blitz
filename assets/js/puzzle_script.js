/*
 * Jigsaw Puzzle 2.0 βeta
 * Created by John Pepp
 * on August 16, 2023
 * Updated by John Pepp
 * on September 23, 2023
 */

// 1. Initialize canvas, context, and audio assets
let canvas = document.getElementById('puzzleCanvas');
let ctx = canvas.getContext('2d');

// Sound Effects
let snapSound = new Audio('assets/audio/audio-snap-001.ogg');
let celebrateSound = new Audio('assets/audio/audio-celebration-001.wav'); // Change Path to the Correct One

let image; // Make image global
let pieces = []; // Array for the pieces of the puzzle
let draggedPiece = null;
let offsetX, offsetY;  // Offset from the top-left corner of the dragged piece
let isSolved = false; // Puzzle set to false which means it isn't solved
let puzzleContainer = document.querySelector('.puzzleImage');
puzzleContainer.style.display = 'none';
let puzzleImage = document.getElementById('puzzleImage');
let imageDescription = document.querySelector('.imageDescription');
let currentTitle = ''; // declare it outside to have it globally accessible
let selectedCategory = '';  // A variable to hold the selected category globally.
let titles_in_selected_category = []; // Global Variable
const PIECE_COUNT = 4;  // Number of puzzle pieces along one dimension
// Hide the alert
let alertOverlay = document.querySelector('.custom-alert-overlay');
let alertBox = document.querySelector('.custom-alert');
alertOverlay.style.display = "none";
alertBox.style.display = "none";
const populateTitles = () => {
    const selectedCategory = document.getElementById('category').value;

    // Clear the session of shown images when the category is changed
    fetch('clear_session.php')
        .then(() => {
            const selectElement = document.getElementById('title');
            fetch(`fetch_titles.php?category=${selectedCategory}`)
                .then(response => response.json())
                .then(titles => {
                    titles_in_selected_category = titles; //
                    //console.log('titles:', titles_in_selected_category, 'category', selectedCategory);
                    selectElement.innerHTML = '';
                    titles.forEach(title => {
                        const optionElement = document.createElement('option');
                        optionElement.value = title;
                        optionElement.textContent = title;
                        selectElement.appendChild(optionElement);
                    });
                    // If there are titles, load the first puzzle of the new category.
                    if(titles.length > 0) {
                        alertOverlay.style.display = "none";  // Hide the alert
                        alertBox.style.display = "none";      // Hide the alert
                        loadNextPuzzle(titles[0], selectedCategory);
                    }
                })
                .catch(error => console.error('Error fetching the titles:', error));
        })
        .catch(error => console.error('Error clearing the session:', error));
};



document.addEventListener('DOMContentLoaded', () => {
    // Populate titles when the page loads
    populateTitles();

    document.getElementById('title').addEventListener('change', (e) => {
        const selectedTitle = e.target.value;
        const selectedCategory = document.getElementById('category').value; // Get selected category here as well
        alertOverlay.style.display = "none";
        alertBox.style.display = "none";
        // Clear the session of shown images when the title is changed
        fetch('clear_session.php')
            .then(() => {
                // Load next puzzle after session is cleared
                loadNextPuzzle(selectedTitle, selectedCategory);
            })
            .catch(error => console.error('Error clearing the session:', error));
    });


    // Also populate titles when the selected category changes
    document.getElementById('category').addEventListener('change', populateTitles);
});


const loadNextPuzzle = (title = '') => {
    let url = 'fetch_image.php';

    if (selectedCategory) url += `?category=${selectedCategory}`;
    if (title) url += (selectedCategory ? '&' : '?') + `title=${title}`;

    fetch(url)
        .then(response => response.json())
        .then(data => {
            currentTitle = data.title || '';
            //console.log(data);
            // Extract the image path and description from the JSON response
            const image_path = data.image_path;
            const description = data.description;

            if (data.image_path === 'NO_MORE_IMAGES') {
                ctx.clearRect(0, 0, canvas.width, canvas.height);

                // Set font, size, and color
                ctx.font = '30px Arial';
                ctx.fillStyle = 'black';

                // Align the text to be centered
                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';
                ctx.fillText("Please Select an Image!", canvas.width / 2, canvas.height / 2);
            }


            image = new Image();
            image.src = image_path;
            //console.log('image.src', image.src);
            imageDescription.textContent = description;
            puzzleContainer.style.display = 'block';
            // Set the src for the img element to display the image on the page
            puzzleImage.src = image_path;
            // Ensure the image is loaded before proceeding
            image.onload = function () {
                // Reset pieces and isSolved flag
                pieces = [];
                isSolved = false;

                let pieceWidth = image.width / PIECE_COUNT;
                let pieceHeight = image.height / PIECE_COUNT;

                const {imageX, imageY} = setupCanvasBackground(image);

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
                                height: pieceHeight,
                                snapped: false
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

                // Add mouse event listeners
                canvas.addEventListener('mousedown', handleMouseDown);
                canvas.addEventListener('mousemove', handleMouseMove);
                canvas.addEventListener('mouseup', handleMouseUp);
            };

            image.onerror = function () {
                console.error("Error loading the image.");
            };
        })

        .catch(error => {
            console.error("Error fetching the image path:", error);
        });
};



//loadNextPuzzle('Ruby-throated Hummingbird', 'wildlife');


const MAX_ATTEMPTS = 100;  // Maximum attempts for finding non-overlapping positions for puzzle pieces

const isMouseOverPiece = (mouseX, mouseY) => {
    for (let piece of pieces) {
        if (mouseX > piece.sx && mouseX < piece.sx + piece.width &&
            mouseY > piece.sy && mouseY < piece.sy + piece.height) {
            return true;
        }
    }
    return false;
};




// 3. Event listeners for dragging puzzle pieces
// On mouse down, check if any piece is clicked
const handleMouseDown = e => {
    if (isSolved) return;

    let mouseX = e.clientX - canvas.getBoundingClientRect().left;
    let mouseY = e.clientY - canvas.getBoundingClientRect().top;

    for (let piece of pieces) {
        if (!piece.snapped && mouseX > piece.sx && mouseX < piece.sx + piece.width &&
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
    let mouseX = e.clientX - canvas.getBoundingClientRect().left;
    let mouseY = e.clientY - canvas.getBoundingClientRect().top;

    if (!draggedPiece) {
        if (isMouseOverPiece(mouseX, mouseY)) {
            canvas.style.cursor = 'pointer';  // Change cursor to hand
        } else {
            canvas.style.cursor = 'default';  // Change cursor back to arrow
        }
    }

    if (draggedPiece) {
        draggedPiece.sx = mouseX - offsetX;
        draggedPiece.sy = mouseY - offsetY;

        redrawCanvas();
    }
};

function showAlert(message) {

    let alertText = document.getElementById('alertText');
    alertBox.style.position = 'absolute'; // Ensure that the position is set to absolute
    document.getElementById('customAlertContent').addEventListener('click', closeAlert);
    alertText.textContent = message;
    alertOverlay.style.display = "flex";
    alertBox.style.display = "block";
}

function closeAlert() {

    // Remove the solved puzzle title from the titles_in_selected_category array
    titles_in_selected_category = titles_in_selected_category.filter(title => title !== currentTitle);

    // Redraw the title select element with the remaining titles
    const selectElement = document.getElementById('title');
    selectElement.innerHTML = ''; // clear existing options
    titles_in_selected_category.forEach(title => {
        const optionElement = document.createElement('option');
        optionElement.value = title;
        optionElement.textContent = title;
        selectElement.appendChild(optionElement);
    });

    // Load the next puzzle if there are remaining titles, else handle the case where there are no more titles
    if(titles_in_selected_category.length > 0) {
        loadNextPuzzle(titles_in_selected_category[0], selectedCategory);
    } else {
        // Handle the case where there are no more titles in the selected category, e.g., display a message
    }

    // Hide the alert
    alertOverlay.style.display = "none";
    alertBox.style.display = "none";
}


// On mouse up, release the piece and snap it if close to its correct position
const handleMouseUp = e => {
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
        draggedPiece = null;
    }

    if (checkForCompletion()) {
        // Remove mouse events after puzzle is solved
        canvas.removeEventListener('mousedown', handleMouseDown);
        canvas.removeEventListener('mousemove', handleMouseMove);
        canvas.removeEventListener('mouseup', handleMouseUp);
        canvas.style.cursor = 'default';  // Change cursor back to arrow
        celebrateSound.play();
        showAlert('Congratulations! Puzzle completed! Click to Continue');

    } else {
        // Check for cursor change
        let mouseX = e.clientX - canvas.getBoundingClientRect().left;
        let mouseY = e.clientY - canvas.getBoundingClientRect().top;

        if (isMouseOverPiece(mouseX, mouseY)) {
            canvas.style.cursor = 'pointer';  // Change cursor to hand
        } else {
            canvas.style.cursor = 'default';  // Change cursor back to arrow
        }
    }
};

// Puzzle completion check
function checkForCompletion() {
    for (let piece of pieces) {
        if (!piece.snapped) {
            return false;
        }
    }
    isSolved = true;  // Puzzle is completed
    return true;
}

function setupCanvasBackground() {
    // Clear the entire canvas
    ctx.clearRect(0, 0, canvas.width, canvas.height);

    // Set the background color for the entire canvas
    ctx.fillStyle = "#E0E0E0";
    ctx.fillRect(0, 0, canvas.width, canvas.height);

    // Calculate image coordinates and set the background color for the image portion
    const imageX = (canvas.width - image.width) / 2;
    const imageY = (canvas.height - image.height) / 2;
    ctx.fillStyle = "#FFFFFF";
    ctx.fillRect(imageX, imageY, image.width, image.height);

    return {imageX, imageY};  // Return these values since they're used in both places.
}


// 5. Redraws the entire canvas
function redrawCanvas() {
    const {imageX, imageY} = setupCanvasBackground();

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