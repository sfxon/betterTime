<?php

namespace App\Service;

use App\Interface\ViewSettingInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class ViewService
{
    private SettingService $settingService;
    private Serializer $serializer;

    public function __construct(SettingService $settingService)
    {
        $this->settingService = $settingService;
        $this->serializer = self::getSerializer();
    }

    public static function getSerializer()
    {
        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        return new Serializer($normalizers, $encoders);
    }

    public static function loadViewFromJson(?string $settingJson, string $className): ViewSettingInterface
    {
        if ($settingJson == null) {
            return new $className();
        }

        // Extract data from json into the viewSetting Object.
        $serializer = self::getSerializer();
        $viewObject = $serializer->deserialize($settingJson, $className, 'json');
        return $viewObject;
    }

    public function saveViewData(ViewSettingInterface $viewSetting, string $textId)
    {
        $settingJson = $this->serializer->serialize($viewSetting, 'json');
        $this->settingService->saveSetting($textId, $settingJson);
    }
}
