<?php /** @noinspection ALL */

namespace brainwave;

use PDO;
use Exception;
use JetBrains\PhpStorm\Pure;
use DateTime;
use DateTimeZone;

class ImageContentManager implements ImageContentManagerInterface
{
    protected string $table = "gallery"; // Table Name:
    protected $db_columns = ['id', 'category', 'user_id', 'thumb_path', 'image_path', 'Model', 'ExposureTime', 'Aperture', 'ISO', 'FocalLength', 'author', 'heading', 'content', 'data_updated', 'date_added'];
    public $id;
    public $user_id;
    public $page;
    public $category;
    public $thumb_path;
    public $image_path;
    public $Model;
    public $ExposureTime;
    public $Aperture;
    public $ISO;
    public $FocalLength;
    public $author;
    public $heading;
    public $content;
    public $date_updated;
    public $date_added;


    protected PDO $pdo;

    public function __construct(PDO $pdo, array $args = [])
    {
        $this->pdo = $pdo;

        // Caution: allows private/protected properties to be set
        foreach ($args as $k => $v) {
            if (property_exists($this, $k)) {
                $v = $this->filterwords($v);
                $this->$k = $v;
                $this->params[$k] = $v;
                $this->objects[] = $v;
            }
        }
    } // End of construct method:

    /*
     * Create a short description of content and place a link button that I call 'more' at the end of the
     * shorten content.
     */
    #[Pure] public function intro($content = "", $count = 100): string
    {
        return substr($content, 0, $count) . "...";
    }

    public function setImagePath($image_path)
    {
        $this->image_path = $image_path;

    }

    // Total Record/Pages in gallery database table
    public function totalCount()
    {
        $sql = "SELECT count(id) FROM gallery";
        $stmt = $this->pdo->prepare($sql);

        $stmt->execute();
        return $stmt->fetchColumn();
    }

    // Filter Dirty Words Method
    protected function filterwords($text)
    {
        $filterWords = array('fuck', 'shit', 'ass', 'asshole', 'motherfucker');
        $filterCount = sizeof($filterWords);
        for ($i = 0; $i < $filterCount; $i++) {
            $text = preg_replace_callback('/\b' . $filterWords[$i] . '\b/i', function ($matches) {
                return str_repeat('*', strlen($matches[0]));
            }, $text);
        }
        return $text;
    }

    #[Pure] public function total_pages($total_count, $per_page): float|bool
    {
        return ceil($total_count / $per_page);
    }

    public function offset($per_page, $current_page): float|int
    {
        return $per_page * ($current_page - 1);
    }


    // Total Record/Pages in category in gallery database table
    public function countAllPage($category = 'wildlife')
    {
        $sql = "SELECT count(id) FROM gallery WHERE category=:category";
        $stmt = $this->pdo->prepare($sql);

        $stmt->execute(['category' => $category]);
        return $stmt->fetchColumn();

    }
    // Fetch Headings
    public function headings() {
         return $this->pdo->query('SELECT id, heading FROM gallery')->fetchAlL(PDO::FETCH_ASSOC);
    }

    // Display Record(s) by Pagination
    public function page($perPage, $offset, $page = "index", $category = "wildlife"): array
    {
        $sql = 'SELECT * FROM gallery WHERE page =:page AND category =:category ORDER BY id DESC, date_added DESC LIMIT :perPage OFFSET :blogOffset';
        $stmt = $this->pdo->prepare($sql); // Prepare the query:
        $stmt->execute(['page' => $page, 'perPage' => $perPage, 'category' => $category, 'blogOffset' => $offset]); // Execute the query with the supplied data:
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

// Create new Record
    public function create(): bool
    {
        /* Initialize an array */
        $attribute_pairs = [];

        /*
         * Set up the query using prepared statements with named placeholders.
         */
        $sql = 'INSERT INTO gallery (' . implode(", ", array_keys($this->params)) . ')';
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

    public function fetch_by_heading() {
        $sql = "SELECT heading FROM gallery WHERE category= :category LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['category' => $this->category]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // Grab Record by id
    public function fetch_by_id()
    {
        $sql = "SELECT * FROM gallery WHERE id=:id LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $this->id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Delete Record by id
    public function delete(): bool
    {
        $sql = 'DELETE FROM gallery WHERE id=:id';
        return $this->pdo->prepare($sql)->execute([':id' => $this->id]);
    }

} // End of class:

