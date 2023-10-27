'use strict';
import {updateImageSource, revealPartOfImage, resetCanvas, fullImage} from "./split_picture.js";
import { changeImage } from "./load_image_onto_canvas.js";
const QUESTION_URL = `get-question.php`;
const WORD_URL = 'get-word-letters.php';
// set the starting offset and limit values
const category = document.getElementById('category');
const nextBtn = document.querySelector('.hangman__next');
const word = document.querySelector('.hangman__word');
const guesses = document.querySelector('.hangman__guesses');
const remainingGuesses = document.querySelector('.hangman__remaining');
const scoreDisplay = document.querySelector('.hangman__score');
const questionDisplay = document.querySelector('.hangman__question');
const hangmanForm = document.querySelector('#guess');
const startButton = document.querySelector('#myButton');
document.getElementById("myButton");
//button.addEventListener('click', onButtonClick, false);
scoreDisplay.style.display = 'none';
nextBtn.style.display = 'none';
let image_index = 0;
let imageObj = null;
if (sessionStorage.getItem('isRefresh') === null) {
    sessionStorage.setItem('isRefresh', 'true');
} else {
    sessionStorage.removeItem('isRefresh');
    sessionStorage.setItem('resetScore', 'true');
}

let index = 0;
let id = 0;
// set global variables;
let guessedLetters = [];
let is_it_solved = false;
//const buttonsEl = document.querySelector(".hangman__buttons");
let noReveal = true;
let check = 6;
let remaining = 6;
let selected = category.value;
console.log('category', selected);

category.addEventListener('change', () => {
    selected = category.value;
})
const startingScreen = () => {
    document.querySelector('.hangman').style.display = 'flex';
    startButton.style.display = "none";
    remainingGuesses.textContent = `Remaining Guess: ${remaining}`;
    fetchFirstId(); // Start the Game
    buttons(); // Display the buttons in order for player to use
}
changeImage("../assets/canvas_images/img-game-start.jpg");

// Create a new Image object
let imgObj = new Image();

// Declare imageArray at the top of the script
let imageArray = [];

let baseUrl;

if (window.location.hostname === "localhost" || window.location.hostname === "127.0.0.1") {
    // If we're on the local machine, use the local path
    baseUrl = window.location.protocol + "//" + window.location.host + "/brainwaveblitz";
} else {
    // Otherwise, use the production path
    baseUrl = window.location.protocol + "//" + window.location.host;
}

startButton.addEventListener('click', startingScreen, false);

// Define a function to handle button clicks
const handleButtonClick = (event) => {
    const button = event.target;
    const letter = button.textContent.toUpperCase();
    if (!button.classList.contains("guessedButton")) {
        guessedLetters.push(letter); // Push the letter into the guessedLetters array
        guesses.textContent = `Guessed Letters : ${guessedLetters}`;
        button.classList.add("guessedButton"); // Highlight and Disable Character
        // console.log('Guessed Letter', guessedLetters);
    }
    fetchWord();
    button.removeEventListener("click", handleButtonClick);
};

// Define a function to handle input
const handleInput = (event) => {
    event.preventDefault(); // Prevent the form from submitting
    const letter = document.getElementById("guess").value.toUpperCase();
    // Get a reference to the input element
    const input = document.querySelector("input[type=text]");

    // Focus on the input element to bring up the virtual keyboard
    input.focus();

    if (letter.length !== 1 || !letter.match(/[a-z]/i)) {
        console.log("Please enter a single letter.");
        return;
    }

    if (!guessedLetters.includes(letter)) {
        guessedLetters.push(letter);
        guesses.textContent = `Guessed Letters : ${guessedLetters}`;
        fetchWord();
    }


    document.getElementById("guess").value = ""; // Clear the input field
};

const buttonsEl = document.querySelector(".hangman__buttons");

const buttons = () => {
    /* Generate empty buttons for offsets */
    for (let i = 0; i < 2; i++) {
        const button = document.createElement("button");
        button.className = "empty-button";
        buttonsEl.appendChild(button);
    }

    /* Generate buttons for letters A to Z (ASCII codes 65 to 90) */
    for (let i = 65; i <= 90; i++) {
        const button = document.createElement("button");
        button.textContent = String.fromCharCode(i);
        button.className = "letter-" + String.fromCharCode(i).toLowerCase();
        buttonsEl.appendChild(button);
        button.addEventListener("click", handleButtonClick);
    }

    /* Generate another empty button */
    const button = document.createElement("button");
    button.className = "empty-button";
    buttonsEl.appendChild(button);
};



// Add an event listener to the input element to listen for input events
document.getElementById("guess").addEventListener("input", handleInput);

// Remove Current Word, so that it can be redrawn on screen correctly
const removeWord = () => {
    let element = document.querySelector('.hangman__word');
    while (element.firstChild) {
        element.removeChild(element.firstChild);
    }
}

let removeButtons = () => {
    let element = document.querySelector('.hangman__buttons');
    while (element.firstChild) {
        element.removeChild(element.firstChild);
    }
}

const resetForNextQuestion = () => {
    buttonsEl.style.display = "block";
    noReveal = true; // Set Back to True
    check = 6; // Reset Check of Guess Back to Original State
    resetCanvas(); // Reset Canvas (Image to Blank)
    hangmanForm.disabled = false;
    removeButtons();
    buttons();
    questionDisplay.textContent = "";
    guesses.textContent = "";
    nextBtn.style.display = 'none';
    nextBtn.removeEventListener('click', resetForNextQuestion, false);
    is_it_solved = false;
    guessedLetters = []; // Reset Guesses
    index += 1;
    fetchNextId(id);
}

// fetch current question
const fetchQuestion = async () => {
    const body = {id: id};
    try {
        const response = await fetch(QUESTION_URL, {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify(body)
        });
        if (!response.ok) {
            throw new Error('Something went wrong!');
        }
        const data = await response.json();
        document.querySelector('.hangman__question').textContent = data.question;
    } catch (error) {
        console.error(error);
    }
};

// fetch word(s) from Database Table, display on screen, scoring and next question(s)
const fetchWord = async () => {
    removeWord();
    const body = {
        id: id,
        guessedLetters: guessedLetters,
        is_it_solved: is_it_solved,
        remaining: remaining,
    };
    try {
        const response = await fetch(WORD_URL, {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify(body)
        });
        if (!response.ok) {
            throw new Error('Something went wrong!');
        }

        const data = await response.json();
        scoreDisplay.style.display = 'block';
        scoreDisplay.textContent = `Your score is ${data.score}`;

        // Show Part of Picture if Player misses a guess
        if ( (data.remaining - check) !== 0 ) {
            revealPartOfImage(); // Call function that shows part of image
            check = data.remaining; // set check to data.remaining that is coming from fetch
            noReveal= false; // Set Global Variable to false as player has guessed incorrectly
        }
        // Check if puzzle is solved plus if there are no missed guesses
        if (data.is_it_solved && noReveal) {
            fullImage(); // Display full image
        }

        remainingGuesses.textContent = `Remaining Guess: ${data.remaining}`;

        // If Hangman Word is solved display question and next button
        if (data.is_it_solved === true) { // I want to disable all buttons if this is true
            buttonsEl.style.display = "none";
            hangmanForm.disabled = true;
            //questionDisplay.textContent = data.question;
            await word.appendChild(document.createRange().createContextualFragment(data.word));
            // Enable next button, display it and add an event listener
            nextBtn.disabled = false;
            nextBtn.style.display = 'block';
            nextBtn.addEventListener('click', resetForNextQuestion, false);
        } else {

            await word.appendChild(document.createRange().createContextualFragment(data.word));
        }

    } catch (error) {
        console.error(error);
    }
}

// Function to fetch and load image
const fetchAndLoadImage = async (imagePath) => {
    try {
        let response = await fetch(imagePath);
        let blob = await response.blob();
        let img = new Image();
        img.src = URL.createObjectURL(blob);
        return img;
    } catch (error) {
        console.error(error);
    }
};

// Fetch the Next ID
const fetchNextId = async (currentId) => {
    try {
        // fetch the data from the server
        const response = await fetch('get-next-id.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                current_id: currentId,
                selected: selected
            })
        });

        // parse the response as JSON
        const data = await response.json();
        if (!data.end_of_table) {
            id = data.next_id;
            let imagePath = baseUrl + data.image;

            imageObj = await fetchAndLoadImage(imagePath);
            await updateImageSource(imageObj);
            await fetchQuestion();
            await fetchWord();
        } else {
            // End of Game
            let element = document.querySelector('.hangman');
            while (element.firstChild) {
                element.removeChild(element.firstChild);
            }
            console.log('End of Game');
            await changeImage("../assets/canvas_images/img-game-over.jpg");
        }

    } catch (error) {
        console.error(error);
    }
};

// Fetch the First ID
const fetchFirstId = async () => {
    try {
        // fetch the data from the server
        const response = await fetch('get-first-id.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({resetScore: false, selected: selected})
        });

        // parse the response as JSON
        const data = await response.json();

        id = data.first_id;
        let imagePath = baseUrl + data.image;
        imageObj = await fetchAndLoadImage(imagePath);
        await updateImageSource(imageObj);
        await fetchWord(); // fetch the answers
        await fetchQuestion();


    } catch (error) {
        console.error(error);
    }
};

