<?php

namespace App\DataTransferObject;

class PaginationPageDto {
    private int $page;
    private bool $isCurrentPage;

    public function __construct(int $page, bool $isCurrentPage = false) {
        $this->page = $page;
        $this->isCurrentPage = $isCurrentPage;
    }

    public function getPage() {
        return $this->page;
    }

    public function setPage(int $page) {
        $this->page = $page;
    }

    public function getIsCurrentPage() {
        return $this->isCurrentPage;
    }

    public function setIsCurrentPage(bool $isCurrentPage) {
        $this->isCurrentPage = $isCurrentPage;
    }
}