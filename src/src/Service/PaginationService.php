<?php

namespace App\Service;

use App\DataTransferObject\PaginationDto;
use App\DataTransferObject\PaginationPageDto;

class PaginationService {
    private int $pageCur = 0;
    private int $pagesTotal = 0;
    private int $pagesDisplayMax = 0;
    private PaginationDto $pagination;

    public function __construct(
        int $pageCur,
        int $pagesTotal,
        int $pagesDisplayMax
    ) {
        $this->pageCur = $pageCur;
        $this->pagesTotal = $pagesTotal;
        $this->pagesDisplayMax = $pagesDisplayMax;
        $this->pagination = new PaginationDto();
        $this->calculate();
    }

    public function calculate() {
        if($this->pagesTotal < $this->pagesDisplayMax) {
            $this->buildLeftSide(1, $this->pagesTotal);
        }
    }

    private function buildLeftSide(int $from, $count) {
        if($from > $count) {
            throw new \Exception('Parameter from cannot be bigger than parameter count.');
        }

        for($i = $from; $i <= $count; $i++) {
            $page = new PaginationPageDto($i);
            $this->pagination->append($page); 
        }
    }
}