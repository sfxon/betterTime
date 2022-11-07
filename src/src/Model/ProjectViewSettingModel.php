<?php

namespace App\Model;

use App\Interface\ViewSettingInterface;

class ProjectViewSettingModel implements ViewSettingInterface
{
    private string $sortBy;
    private string $sortOrder;

    public function setSortBy(string $sortBy): void
    {
        $this->sortBy = $sortBy;
    }
    
    public function getSortBy(): string
    {
        return $this->sortBy;
    }

    public function setSortOrder(string $sortOrder): void
    {
        $this->sortOrder = $sortOrder;
    }

    public function getSortOrder(): string
    {
        return $this->sortOrder;
    }
}