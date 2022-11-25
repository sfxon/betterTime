<?php

namespace App\DataTransferObject;

/**
 * PaginationPageDto
 */
class PaginationPageDto
{
    private int $page;
    private bool $isCurrentPage;

    /**
     * __construct
     *
     * @param  int $page
     * @param  bool $isCurrentPage
     */
    public function __construct(int $page, bool $isCurrentPage = false)
    {
        $this->page = $page;
        $this->isCurrentPage = $isCurrentPage;
    }

    /**
     * getPage
     *
     * @return array
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * setPage
     *
     * @param  int $page
     * @return void
     */
    public function setPage(int $page): void
    {
        $this->page = $page;
    }

    /**
     * getIsCurrentPage
     *
     * @return void
     */
    public function getIsCurrentPage(): bool
    {
        return $this->isCurrentPage;
    }

    /**
     * setIsCurrentPage
     *
     * @param  bool $isCurrentPage
     * @return void
     */
    public function setIsCurrentPage(bool $isCurrentPage): void
    {
        $this->isCurrentPage = $isCurrentPage;
    }
}
