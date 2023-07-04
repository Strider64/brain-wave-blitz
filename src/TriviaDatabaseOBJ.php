<?php

namespace brainwave;
use PDO;

class TriviaDatabaseOBJ
{

    public $id;
    public $user_id;
    public $hidden;
    public $question;
    public $ans1;
    public $ans2;
    public $ans3;
    public $ans4;
    public $correct;
    public $category;
    public $date_added;
    static protected string $table = "brainwaveblitz";
    protected $params;
    protected $objects;

    protected PDO $pdo;

    public function __construct(PDO $pdo, $args = [])
    {
        $this->pdo = $pdo;

        foreach ($args as $k => $v) {
            if (property_exists($this, $k)) {
                $this->$k = $v;
                $this->params[$k] = $v;
                $this->objects[] = $v;
            }
        }
    } // End of construct method:


    public function fetchQuestions($category = 'lego'): bool|array
    {

        $sql = "SELECT id, user_id, hidden, question, ans1, ans2, ans3, ans4, category FROM brainwaveblitz WHERE category=:category";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['category' => $category]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function fetchAllQuestions() {
        $sql = 'SELECT * FROM brainwaveblitz ORDER BY date_added DESC';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /*
 * Fetch correct answer:
 */
    public function fetch_correct_answer($id):array
    {

        $sql = "SELECT id, correct FROM brainwaveblitz WHERE id=:id";
        $stmt = $this->pdo->prepare($sql); // Database::pdo() is the PDO Connection

        $stmt->execute([ 'id' => $id ]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create(): bool
    {
        /* Initialize an array */
        $attribute_pairs = [];

        /*
         * Set up the query using prepared statements with named placeholders.
         */
        $sql = 'INSERT INTO ' . static::$table . ' (' . implode(", ", array_keys($this->params)) . ')';
        $sql .= ' VALUES ( :' . implode(', :', array_keys($this->params)) . ')';

        /*
         * Prepare the Database Table:
         */
        $stmt = $this->pdo->prepare($sql); // PHP Version 8.x Database::pdo()

        /*
         * Bind the corresponding values in order to
         * insert them into the table when the script
         * is executed.
         */
        foreach ($this->params as $key => $value) {
            if ($key === 'id') {
                continue; // Don't include the id
            }
            $stmt->bindValue(':' . $key, $value); // Bind values to the named placeholders
        }

        // Execute the statement and return true if successful, otherwise false
        return $stmt->execute();
    }


    public function update(): bool
    {
        /* Initialize an array */
        $attribute_pairs = [];

        /* Create the prepared statement string */
        foreach ($this->params as $key => $value)
        {
            if($key === 'id') { continue; } // Don't include the id:
            $attribute_pairs[] = "$key=:$key"; // Assign it to an array:
        }

        /*
         * The sql implodes the prepared statement array in the proper format
         * and updates the correct record by id.
         */
        $sql  = 'UPDATE ' . static::$table . ' SET ';
        $sql .= implode(", ", $attribute_pairs) . ' WHERE id =:id';

        /* Normally in two lines, but you can daisy-chain pdo method calls */
        $this->pdo->prepare($sql)->execute($this->params);

        return true;

    }

    public function save_scores($data) {
        $query = 'INSERT INTO hs_table( player, score, played, correct, totalQuestions, day_of_year ) VALUES ( :player, :score, NOW(), :correct, :totalQuestions, :day_of_year )';
        $stmt = $this->pdo->prepare($query);
        $result = $stmt->execute(['player' => $data['player'], 'score' => $data['score'], 'correct' => $data['correct'], 'totalQuestions' => $data['totalQuestions'], 'day_of_year' => $data['day_of_year']]);

        // If score was saved successfully, fetch updated high scores for today
        if ($result) {
            $today = date("Y-m-d");
            $scores = $this->fetch_top_5_scores_for_date($today);
            return ['result' => $result, 'scores' => $scores];
        }

        return ['result' => $result];
    }



    public function fetch_top_5_scores_for_date(string $date): array
    {
        $sql = "SELECT * FROM hs_table WHERE DATE(played) = :date ORDER BY score DESC LIMIT 5";

        // Prepare and execute a query
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['date' => $date]);

        // fetch all rows
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function fetch_last_played_top_5_scores(): array
    {
        $sql = "SELECT * FROM hs_table WHERE DATE(played) = (SELECT MAX(DATE(played)) FROM hs_table) ORDER BY score DESC LIMIT 5";

        // Prepare and execute a query
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        // fetch all rows
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function fetch_today_top_5_scores(): array
    {
        // Get the current date
        $today = date("Y-m-d");

        // Fetch today's scores
        $scores = $this->fetch_top_5_scores_for_date($today);

        // If there are no scores for today, fetch yesterday's scores
        if (empty($scores)) {
            $yesterday = date("Y-m-d", strtotime("-1 day"));
            $scores = $this->fetch_top_5_scores_for_date($yesterday);
        }

        // If there are no scores for yesterday, fetch scores for the last day when there were players
        if (empty($scores)) {
            $scores = $this->fetch_last_played_top_5_scores();
        }

        return $scores;
    }




}