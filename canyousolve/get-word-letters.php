<?php
require_once '../assets/config/config.php';
require_once "../vendor/autoload.php";

use brainwave\ErrorHandler;
use brainwave\Database;



$errorHandler = new ErrorHandler();

// Register the exception handler method
set_exception_handler([$errorHandler, 'handleException']);

$database = new Database();
$pdo = $database->createPDO();
$data = json_decode(file_get_contents('php://input'), true);
if (!$data) {
    errorOutput('Invalid input data', 400);
    exit();
}

$stmt = $pdo->prepare('SELECT id, score FROM `score` WHERE id =:id');
$stmt->execute(['id' => 1]);
$current_score = $stmt->fetch();

// Fetch the word from the database
$stmt = $pdo->prepare('SELECT question, answer, canvas_images, points, (SELECT MAX(id) FROM canyousolve) AS max_id FROM canyousolve WHERE id=:id LIMIT 1');

$stmt->execute(['id' => (int)$data['id']]);
$result = $stmt->fetch(PDO::FETCH_ASSOC);


$max_id = $result['max_id'];
$is_end_of_table = ((int)$data['id'] === (int)$max_id);

// Question
$question = $result['question'];

$displayQuestion = "";

// Store the words/answer in $word
$word = strtoupper($result['answer']);

// Take out the spaces in the words
$wordWithoutSpaces = strtoupper(string: str_replace(' ', '', $word));

// convert the word without spaces to a temporary answer array
$temp_answer = str_split($wordWithoutSpaces);
$wordLetters = str_split($word); // word letters array for display

$guessedLetters = $data['guessedLetters'] ?? []; // grab the guessed letters array

function remainGuesses($guessedLetters, $temp_answer, $remaining): int
{
    $badGuesses= [];
    foreach ($guessedLetters as $value) {
        if (!in_array($value, $temp_answer)){
            $badGuesses[] = $value;
        }
    }
    return $remaining  - count($badGuesses);
}

$remaining = remainGuesses($guessedLetters, $temp_answer, $data['remaining']);

if (isset($result['is_it_solved'])) {
    $is_it_solved = $result['is_it_solved'];
} else {
    // If the user doesn't solve the question then next Question
    if ($remaining === 0 ) {
        $is_it_solved = true;
    } else {
        $is_it_solved = false;
    }
}



// Define a function to render the word with underscores for unguessed letters to display
function renderWord($wordLetters, $guessedLetters): string
{
    $word = "";
    foreach ($wordLetters as $letter) {
        if ($letter === " ") {
            $word .= "<span>&nbsp;</span>";
        } else {
            if (in_array(strtoupper($letter), array_map('strtoupper', $guessedLetters))) {
                $word .= "<span>" . strtoupper($letter) . "</span>";
            } else {
                $word .= "<span>_</span>";
            }
        }
    }

    return $word;
}

try {
    // check the answer to the guessed letters to see if ALL the letters have been guessed
    $unsolvedLetters = array_diff($temp_answer, $guessedLetters);
    // call the renderWord function to set up the canyouseeme display
    $renderedWord = renderWord($wordLetters, $guessedLetters);

    // Check if the last guessed letter is in the word, but only if there are any guessed letters
    if (!empty($guessedLetters)) {
        /* The end() function in PHP moves the internal pointer of the array to its
         * last element and returns the value of that element. */
        $lastGuessedLetter = end($guessedLetters);
        if (in_array($lastGuessedLetter, $temp_answer)) {
            // The last guessed letter is correct, add points to the score
            $current_score['score'] += 15;
        } else {
            // The last guessed letter is incorrect, deduct points from the score
            $current_score['score'] -= 15;
        }
    }

    function update_score($current_score, $pdo): void
    {
        $sql = "UPDATE score SET score=:score WHERE id=:id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['score' => $current_score, 'id' => 1]);
    }



    // if $unsolvedLetters is empty we know the answer has been solved
    if (empty($unsolvedLetters)) {
        $is_it_solved = true;
    }

    update_score($current_score['score'], $pdo);

    // Send back to the JavaScript using Fetch and in JSON format
    output(['word' => $renderedWord, 'question' => $displayQuestion, 'is_it_solved' => $is_it_solved, 'score' => $current_score['score'], 'remaining' => $remaining, 'is_end_of_table' => $is_end_of_table]);
} catch (Exception $e) {
    errorOutput($e->getMessage()); // Error Message
}


function errorOutput($output, $code = 500): void
{
    http_response_code($code);
    echo json_encode(['error' => $output]);
}

function output($data): void
{
    http_response_code(200);
    echo json_encode($data);
}
