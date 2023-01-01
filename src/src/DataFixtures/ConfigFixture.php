<?php

namespace App\DataFixtures;

use App\Entity\ConfigDefinition;
use App\Entity\ConfigValue;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Uid\Uuid;

class ConfigFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Add configDefinition
        $configDefinition = new ConfigDefinition();
        $configDefinition->setTechnicalName('Smtp');
        $configDefinition->setLevels(['initial', 'system', 'admin', 'team', 'user']);
        $configDefinition->setDescription('The Smtp Configuration should contain all data, that is required to handle an smtp connection. The data is to be stored in json format. Possible fields are: server, username, password, sender, encryption, port');
        $manager->persist($configDefinition);
        $manager->flush();

        $configDefinitionId = $configDefinition->getId();

        // Add configValues for previously created configDefinition.
        // Add initial config.
        $configValue = new ConfigValue();
        $configValue->setConfigDefinitionId($configDefinitionId);
        $configValue->setLevel('initial');
        $configValue->setForeignId(null);
        $configValue->setValue("{\"server\":\"smtp.internal.example\",\"username\":\"jake\",\"password\":\"whoCares123?\",\"sender\":\"jake@brooklin99.example\",\"encryption\":\"tls\",\"port\":\"587\"}");
        $manager->persist($configValue);
        $manager->flush();

        // Add system config.
        $configValue = new ConfigValue();
        $configValue->setConfigDefinitionId($configDefinitionId);
        $configValue->setLevel('system');
        $configValue->setForeignId(null);
        $configValue->setValue("{\"server\":\"smtp.system.example\",\"username\":\"hold\",\"password\":\"supersecure123\",\"sender\":\"hold@example\",\"encryption\":\"tls\",\"port\":\"587\"}");
        $manager->persist($configValue);
        $manager->flush();

        // Add 2 entries for the user level.
        $userBoyle = new User();
        $userBoyle->setEmail('boyle@b99.example');
        $userBoyle->setPassword('brokenPassword');
        $manager->persist($userBoyle);
        $manager->flush();

        $userIdBoyle = $userBoyle->getId();
        
        $configValue = new ConfigValue();
        $configValue->setConfigDefinitionId($configDefinitionId);
        $configValue->setLevel('user');
        $configValue->setForeignId($userIdBoyle);
        $configValue->setValue("{\"server\":\"smtp.user1.example\",\"username\":\"boyle\",\"password\":\"supersecure123\",\"sender\":\"hold@example\",\"encryption\":\"tls\",\"port\":\"587\"}");
        $manager->persist($configValue);
        $manager->flush();

        $userDiaz = new User();
        $userDiaz->setEmail('diaz@b99.example');
        $userDiaz->setPassword('defect');
        $manager->persist($userDiaz);
        $manager->flush();

        $userIdDiaz = $userDiaz->getId();

        $configValue = new ConfigValue();
        $configValue->setConfigDefinitionId($configDefinitionId);
        $configValue->setLevel('user');
        $configValue->setForeignId($userIdDiaz);
        $configValue->setValue("{\"server\":\"smtp.user2.example\",\"username\":\"diaz\",\"password\":\"supersecure123\",\"sender\":\"hold@example\",\"encryption\":\"tls\",\"port\":\"587\"}");
        $manager->persist($configValue);
        $manager->flush();

        // Add 1 team entry
        $configValue = new ConfigValue();
        $configValue->setConfigDefinitionId($configDefinitionId);
        $configValue->setLevel('team');
        $configValue->setForeignId(Uuid::fromBase32("6SWYGR8QAV27NACAHMK5RG0RPG"));
        $configValue->setValue("{\"server\":\"smtp.team1.example\",\"username\":\"99\",\"password\":\"supersecure123\",\"sender\":\"hold@example\",\"encryption\":\"tls\",\"port\":\"587\"}");
        $manager->persist($configValue);
        $manager->flush();
    }
}
