<?php
// CMSInterface.php
namespace brainwave;


interface CMSInterface {
    public function intro($content = "", $count = 100): string;
    public function setImagePath($image_path);
    public function countAllPage($category = 'home');
    public function page($perPage, $offset, $page = "index", $category = "home"): array;
}

