<?php

namespace App\Service;

use App\Entity\Setting;
use Doctrine\Persistence\ManagerRegistry;

class SettingService {
    private $doctrine;

    public function __construct(ManagerRegistry $doctrine) {
        $this->doctrine = $doctrine;
    }

    // Example usage: $settingJson = $settingService->getSettingByTextId('view.project.setting');
    public function getSettingByTextId(string $textId, bool $required = false) {
        $value = null;
        $repository = $this->doctrine->getRepository(Setting::class);
        $setting = $repository->findOneBy(
            [ 'textId' => $textId ]
        );

        if(null === $setting) {
            $value = $this->getDefaultSettingFromFile($textId);
        } else {
            // get value
            $value = $setting->getValue();
        }

        if($required && null === $value) {
            throw new \Exception('Could not find setting with textId ' . htmlspecialchars($textId));
        }

        return $value;
    }

    public function getDefaultSettingFromFile($textId) {
        $data = @file_get_contents('./../config/dlh/' . basename($textId) . '.json');

        if(false === $data) {
            return null;
        }

        return $data;
    }
}