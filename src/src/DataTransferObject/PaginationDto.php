<?php

namespace App\DataTransferObject;

use App\DataTransferObject\PaginationPageDto;

class PaginationDto {
    private $pages;

    public function __construct() {
        $this->pages = [];
    }

    public function append($page) {
        $this->pages[] = $page;
    }
}