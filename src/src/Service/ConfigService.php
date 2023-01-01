<?php

namespace App\Service;

use App\Entity\ConfigDefinition;
use App\Entity\ConfigValue;
use App\Exception\ConfigDefinitionNotFoundException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

class ConfigService
{
    private $doctrine;
    private $configDefinitionRepository;
    private $configValueRepository;

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
     *
     * @param  string $technicalName
     * @param  array $availableLevels
     * @param  ?Uuid $foreignId
     * @return mixed|null
     */
    public function loadConfig(
        string $technicalName,
        array $availableLevels = null
    ): mixed {
        // Load config definition.
        $configDefinition = $this->configDefinitionRepository->findOneBy([ 
            'technicalName' => $technicalName
        ]);

        if(null === $configDefinition) {
            throw new ConfigDefinitionNotFoundException('No ConfigDefinition with technical name "' . $technicalName . '" found.');
        }

        // TODO: Check if request also asks for user level -> if yes, try to load a value for that user and return it, if one was found.
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
}
