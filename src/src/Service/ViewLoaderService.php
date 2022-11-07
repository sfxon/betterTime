<?php

namespace App\Service;

use App\Interface\ViewSettingInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class ViewLoaderService
{
    public static function loadViewFromJson(string $settingJson, string $className): ViewSettingInterface
    {
        // Extract data from json into the viewSetting Object.
        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);

        $viewObject = $serializer->deserialize($settingJson, $className, 'json');
        return $viewObject;
    }
}