<?php

namespace App\Service;

use App\DataTransferObject\PaginationDto;
use App\DataTransferObject\PaginationPageDto;

class PaginationService
{
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

    public function calculate()
    {
        if ($this->pagesTotal < $this->pagesDisplayMax) {
            $this->buildLeftSide(1, $this->pagesTotal);
        } else {
            if ($this->isLeftSide()) {
                $this->buildLeftSide(1, $this->pagesDisplayMax);
            } elseif ($this->isRightSide()) {
                $this->buildRightSide();
            } else {
                $this->buildMiddle();
            }
        }

        $this->pagination->markCurrentPage($this->pageCur);
    }

    private function build($from, $to)
    {
        for ($i = $from; $i <= $to; $i++) {
            $page = new PaginationPageDto($i);
            $this->pagination->append($page);
        }
    }

    private function buildLeftSide(int $from, $count): void
    {
        $this->build($from, $count);
    }

    private function buildMiddle(): void
    {
        $offset = $this->pageCur - floor($this->pagesDisplayMax / 2);
        $this->build($offset, ($offset + $this->pagesDisplayMax - 1));
    }

    private function buildRightSide()
    {
        $offset = $this->pagesTotal - $this->pagesDisplayMax + 1;
        $this->build($offset, $this->pagesTotal);
    }

    private function isLeftSide(): bool
    {
        $halfOfPages = ceil($this->pagesDisplayMax / 2);

        if ($this->pageCur <= $halfOfPages) {
            return true;
        }

        return false;
    }

    private function isRightSide(): bool
    {
        $halfOfPages = ceil($this->pagesDisplayMax / 2);

        if ($this->pageCur > ($this->pagesTotal - $halfOfPages)) {
            return true;
        }

        return false;
    }

    public function getPagination()
    {
        return $this->pagination;
    }
}
