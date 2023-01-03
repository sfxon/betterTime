<?php

namespace App\Service;

use App\Entity\ConfigDefinition;
use App\Entity\ConfigValue;
use App\Entity\User;
use App\Exception\ConfigDefinitionNotFoundException;
use App\Repository\ConfigDefinitionRepository;
use App\Repository\ConfigValueRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

class ConfigService
{
    private ManagerRegistry $doctrine;
    private ConfigDefinitionRepository $configDefinitionRepository;
    private ConfigValueRepository $configValueRepository;
    private User $user;

    private $levelPriority = [
        'initial' => 10,
        'system' => 20,
        'backend' => 30,
        'frontend' => 40,
        'team' => 50,
        'user' => 60
    ];
    
    /**
     * __construct
     *
     * @param  ManagerRegistry $doctrine
     */
    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
        $this->configDefinitionRepository = $this->doctrine->getRepository(ConfigDefinition::class);
        $this->configValueRepository = $this->doctrine->getRepository(ConfigValue::class);
    }
        
    /**
     * Load configuration value.
     * The function tries to load a given configuration
     * over certain config-levels. It begins with the highest
     * and goes one step down everytime it doesn't find
     * a configuration for a certain level.
     *
     * @param  string $technicalName
     * @param  array $availableLevels
     * @return mixed|null
     */
    public function loadConfig(
        string $technicalName,
        array $availableLevels = null,
        User $user = null
    ): mixed {
        $configDefinition = $this->loadConfigDefinition($technicalName);

        // Load user configuration if wanted and available.
        if (
            null !== $availableLevels &&
            in_array('user', $availableLevels) &&
            $user !== null
        ) {
            if(null === $user) {
                throw new \RuntimeException('User not found');
            }

            $configValue = $this->loadUserConfigValue($technicalName, $user->getId());

            if($configValue !== null) {
                return $configValue->getValue();
            }
        }

        // TODO: Implement same check for teams as for user level, as soon as teams are implemented. (There is no implementation yet).

        // Load config values for config definition.
        $configValues = $this->configValueRepository->findBy([ 
            'configDefinitionId' => $configDefinition->getId(),
            'foreignId' => null
        ]);

        if($configValues === null) {
            return null;
        }

        // Sort keys by priority
        $priorityArray = $this->getPriorityArray();

        foreach($configValues as $configValue) {
            $priority = $configValue->getLevel();

            if(!array_key_exists($priority, $priorityArray)) {
                throw new \RuntimeException('An undefined level has been loaded from the configuration database');
            }

            $priorityArray[$priority] = $configValue;
        }

        // Sort out unwanted keys and entries, where no data has been loaded,
        // but only, if filtering is wanted.
        $finalValues = [];

        if (null === $availableLevels) {
            foreach ($priorityArray as $key => $configValue) {
                if (
                    $configValue !== null
                ) {
                    $finalValues[] = $configValue;
                }
            }
        } else {
            foreach ($priorityArray as $key => $configValue) {
                if (
                    in_array($key, $availableLevels) &&
                    $configValue !== null
                ) {
                    $finalValues[] = $configValue;
                }
            }
        }

        if (count($finalValues) == 0) {
            return null;
        }

        // Get the entry with the highest priority.
        $configValue = array_pop($finalValues);

        return $configValue->getValue();
    }
    
    /**
     * Load ConfigValue on a user level.
     * If the parameter configDefinition is not provided,
     * the method tries to load the ConfigDefinition
     * from the database, too.
     *
     * @param  string $technicalName
     * @param  Uuid $foreignId
     * @return ?ConfigValue
     */
    public function loadUserConfigValue(
        string $technicalName, 
        Uuid $userId,
        ConfigDefinition $configDefinition = null
    ): ?ConfigValue
    {
        if($configDefinition === null) {
            $configDefinition = $this->loadConfigDefinition($technicalName);
        }

        $configValue = $this->configValueRepository->findOneBy([ 
            'configDefinitionId' => $configDefinition->getId(),
            'level' => 'user',
            'foreignId' => $userId
        ]);

        return $configValue;
    }
    
    /**
     * getPriorityArray
     *
     * @return array
     */
    private function getPriorityArray(): array
    {
        $retval = [];

        foreach($this->levelPriority as $key => $priority) {
            $retval[$key] = null;
        }

        return $retval;
    }
    
    /**
     * Load ConfigDefinition from database.
     *
     * @param  string $technicalName
     * @return ConfigDefinition
     */
    private function loadConfigDefinition(string $technicalName): ConfigDefinition
    {
        $configDefinition = $this->configDefinitionRepository->findOneBy([ 
            'technicalName' => $technicalName
        ]);

        if(null === $configDefinition) {
            throw new ConfigDefinitionNotFoundException('No ConfigDefinition with technical name "' . $technicalName . '" found.');
        }

        return $configDefinition;
    }
}
