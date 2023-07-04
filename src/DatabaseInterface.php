<?php
// DatabaseInterface.php
namespace brainwave;

use PDO;

interface DatabaseInterface {
    public function createPDO(): ?PDO;
}
