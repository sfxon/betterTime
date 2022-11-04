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

    public function markCurrentPage($pageNumber) {
        for($i = 0; $i < count($this->pages); $i++) {
            if($this->pages[$i]->getPage() == $pageNumber) {
                $this->pages[$i]->setIsCurrentPage(true);
                return;
            }
        }
    }

    public function getPages() {
        return $this->pages;
    }
}