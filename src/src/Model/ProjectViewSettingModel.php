<?php

namespace App\Model;

use App\Interface\ViewSettingInterface;

class ProjectViewSettingModel implements ViewSettingInterface
{
    private string $sortBy;
    private string $sortOrder;
    public const AVAILABLE_SORT_FIELDS = ['name'];
    public const AVAILABLE_SORT_ORDERS = ['ASC', 'DESC'];

    public function __construct()
    {
        $this->sortBy = 'name';
        $this->sortOrder = 'ASC';
    }

    public function setSortBy(string $sortBy): void
    {
        if (in_array($sortBy, self::AVAILABLE_SORT_FIELDS)) {
            $this->sortBy = $sortBy;
        }
    }

    public function getSortBy(): string
    {
        return $this->sortBy;
    }

    public function setSortOrder(string $sortOrder): void
    {
        if (in_array($sortOrder, self::AVAILABLE_SORT_ORDERS)) {
            $this->sortOrder = $sortOrder;
        }
    }

    public function getSortOrder(): string
    {
        return $this->sortOrder;
    }
}
