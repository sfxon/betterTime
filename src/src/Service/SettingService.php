<?php

namespace App\Service;

use App\Entity\Setting;
use Doctrine\Persistence\ManagerRegistry;

class SettingService {
    private $doctrine;

    public function __construct(ManagerRegistry $doctrine) {
        $this->doctrine = $doctrine;
    }

    /**
     * Get setting by text id.
     * Example usage: $settingJson = $settingService->getSettingByTextId('view.project.setting');
     * 
     * @param string $textId
     * @return Setting|null
     */
    public function getSettingByTextId(string $textId, bool $required = false): ?string
    {
        $textId = basename($textId); // Secure path
        $value = null;
        $repository = $this->doctrine->getRepository(Setting::class);
        $setting = $repository->findOneBy(
            [ 'textId' => $textId ]
        );

        if(null === $setting) {
            $value = $this->getDefaultSettingFromFile($textId, true);
        } else {
            // get value
            $value = $setting->getValue();
        }

        if($required && null === $value) {
            throw new \Exception('Could not find setting with textId ' . htmlspecialchars($textId));
        }

        return $value;
    }

    /**
     * Get default setting from file.
     * 
     * @param string $textId
     * @param bool $required
     * @return string|null
     */
    public function getDefaultSettingFromFile(string $textId, bool $createDefaultSettingIfNonExists = false): ?string
    {
        
        $filename = './../config/dlh/' . $textId . '.json';
        $data = @file_get_contents($filename);

        if(false === $data) {
            if($createDefaultSettingIfNonExists) {
                $this->createDefaultSettingIfItNotExists($filename);
                return $this->getDefaultSettingFromFile($textId, false);
            }

            return null;
        }

        return $data;
    }

    /**
     * Create default setting if it not exists.
     * 
     * @param string $filename
     * @return void
     */
    public function createDefaultSettingIfItNotExists(string $filename): void
    {
        if(file_exists($filename)) {
            return;
        }

        $defaultFilename = $filename . '.default';

        if(file_exists($defaultFilename)) {
            copy($defaultFilename, $filename);
        }
    }

    public function saveSetting(string $textId, string $jsonData) {
        $entityManager = $this->doctrine->getManager();
        $repository = $entityManager->getRepository(Setting::class);

        // Try to load entry.
        $textId = basename($textId); // Secure path
        $setting = $repository->findOneBy(
            [ 'textId' => $textId ]
        );

        // Create entry, if it does not exist.
        if(null === $setting) {
            $setting = new Setting();
            $setting->setTextId($textId);
        }

        $setting->setValue($jsonData);
        $entityManager->persist($setting);
        $entityManager->flush();
    }
}