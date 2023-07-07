'use strict';
{
    const IDS_URL = 'get-id.php';
    const WORD_URL = 'get-word-letters.php';
    // set the starting offset and limit values
    let offset = 0;
    const limit = 2;
    const nextBtn = document.querySelector('.hangman__next');
    const word = document.querySelector('.hangman__word');
    const guesses = document.querySelector('.hangman__guesses');
    const remainingGuesses = document.querySelector('.hangman__remaining');
    const scoreDisplay = document.querySelector('.hangman__score');
    const questionDisplay = document.querySelector('.hangman__question');
    const hangmanForm = document.querySelector('#guess');

    scoreDisplay.style.display = 'none';
    nextBtn.style.display = 'none';
    // set global variables;
    let id;
    let next_id;
    let guessedLetters = [];
    let is_it_solved = false;
    //const buttonsEl = document.querySelector(".hangman__buttons");

    let remaining = 6;
    remainingGuesses.textContent = `Remaing Guess: ${remaining}`;
    const canvas = document.getElementById("hangman-canvas");
    let context = canvas.getContext("2d");




    // Draw the remaining guesses (stick figure)
    function drawHangman(remainingGuesses) {
        if (remainingGuesses === 5) {
            // head
            context.beginPath();
            context.arc(250, 125, 25, 0, Math.PI * 2, true);
            context.stroke();
        } else if (remainingGuesses === 4) {
            // body
            context.beginPath();
            context.moveTo(250, 150);
            context.lineTo(250, 250);
            context.stroke();
        } else if (remainingGuesses === 3) {
            // left arm
            context.beginPath();
            context.moveTo(250, 175);
            context.lineTo(200, 225);
            context.stroke();
        } else if (remainingGuesses === 2) {
            // right arm
            context.beginPath();
            context.moveTo(250, 175);
            context.lineTo(300, 225);
            context.stroke();
        } else if (remainingGuesses === 1) {
            // left leg
            context.beginPath();
            context.moveTo(250, 250);
            context.lineTo(200, 300);
            context.stroke();
        } else if (remainingGuesses === 0) {
            // right leg
            context.beginPath();
            context.moveTo(250, 250);
            context.lineTo(300, 300);
            context.stroke();
        }
    }

    // Draw the Gallows
    function drawGallows() {
        context.beginPath();
        context.moveTo(50, 350);
        context.lineTo(150, 350);
        context.lineTo(150, 50);
        context.lineTo(250, 50);
        context.lineTo(250, 100);
        context.stroke();
    }


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
        //console.log('Sending guessedLetters', guessedLetters);
        fetchWord(guessedLetters);
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
            fetchWord(guessedLetters);
        }


        document.getElementById("guess").value = ""; // Clear the input field
    };

    const buttonsEl = document.querySelector(".hangman__buttons");
    const buttons = () => {
// Generate buttons for letters A to Z (ASCII codes 65 to 90)
        for (let i = 65; i <= 90; i++) {
            const button = document.createElement("button");
            button.textContent = String.fromCharCode(i);
            buttonsEl.appendChild(button);
            button.addEventListener("click", handleButtonClick);
        }

// Generate buttons for numbers 0 to 9 (ASCII codes 48 to 57)
/*        for (let i = 48; i <= 57; i++) {
            const button = document.createElement("button");
            button.textContent = String.fromCharCode(i);
            buttonsEl.appendChild(button);
        }*/

// Generate buttons for common symbols
/*        const symbols = ["!", "@", "#", "$", "%", "^", "&", "*", "(", ")", "-", "+", "=", "{", "}", "[", "]", "|", "\\", ":", ";", "\"", "'", "<", ">", ",", ".", "?", "/", "~"];
        for (let i = 0; i < symbols.length; i++) {
            const button = document.createElement("button");
            button.textContent = symbols[i];
            buttonsEl.appendChild(button);
        }*/

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
        context.clearRect(0, 0, canvas.width, canvas.height);
        hangmanForm.disabled = false;
        removeButtons();
        buttons();
        drawGallows();
        drawHangman(remaining);
        questionDisplay.textContent = "";
        guesses.textContent = "";
        nextBtn.style.display = 'none';
        nextBtn.removeEventListener('click', resetForNextQuestion, false);
        is_it_solved = false;
        guessedLetters = []; // Reset Guesses
        offset += 1;
        fetchRows();
    }

// fetch word(s) from Database Table, display on screen, scoring and next question(s)
    const fetchWord = async (guessedLetters) => {
        removeWord();
        const body = {id: id, guessedLetters: guessedLetters, is_it_solved: is_it_solved, remaining: remaining};
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
            //console.log('Parsed Data', data);

            console.log(`Remaing Guesses is ${data.remaining}`)
            drawHangman(data.remaining);
            remainingGuesses.textContent = `Remaing Guess: ${data.remaining}`;
            // If Hangman Word is solved display question and next button
            if (data.is_it_solved === true) { // I want to disable all buttons if this is true
                hangmanForm.disabled = true;
                questionDisplay.textContent = data.question;
                await word.appendChild(document.createRange().createContextualFragment(data.word));
                // Enable next button, display it and add an event listener
                nextBtn.disabled = false;
                nextBtn.style.display = 'block';
                nextBtn.addEventListener('click', resetForNextQuestion, false);
            } else {
                scoreDisplay.style.display = 'block';
                scoreDisplay.textContent = (data.score === null) ? "Your score is 0" : `Your score is ${data.score}`;
                await word.appendChild(document.createRange().createContextualFragment(data.word));
            }
            //console.log(data.word);

        } catch (error) {
            console.error(error);
        }
    }

// Grab the id and next id using Fetch
    const fetchRows = async () => {

        try {
            // fetch the data from the server
            const response = await fetch(IDS_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    offset: offset,
                    limit: limit
                })
            });

            // parse the response as JSON
            const data = await response.json();
            drawGallows();
            id = data.id; // id of first record
            next_id = data.next_id; //id of next record

            //console.log(data, id, next_id);

            // update the offset for the next query
            offset += 1;
            await fetchWord();
        } catch (error) {
            console.error(error);
        }
    };


    buttons(); // Display the buttons in order for player to use
    // call the function to fetch the initial data
    fetchRows();
}