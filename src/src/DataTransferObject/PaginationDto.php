<?php

namespace App\DataTransferObject;

use App\DataTransferObject\PaginationPageDto;

/**
 * PaginationDto
 */
class PaginationDto {
    private $pages;
        
    /**
     * __construct
     *
     * @return void
     */
    public function __construct() {
        $this->pages = [];
    }
    
    /**
     * append
     *
     * @param  mixed $page
     * @return void
     */
    public function append(PaginationPageDto $page) {
        $this->pages[] = $page;
    }
    
    /**
     * markCurrentPage
     *
     * @param  mixed $pageNumber
     * @return void
     */
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