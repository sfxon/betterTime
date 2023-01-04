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

/**
 * Load configuration values from the database
 * by priority and filters.
 */
class ConfigService
{
    private ManagerRegistry $doctrine;
    private ConfigDefinitionRepository $configDefinitionRepository;
    private ConfigValueRepository $configValueRepository;
    
    const levelPriority = [
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
     * Filters an array of ConfigValue entries.
     * 
     * @param array $priorityArray      Must be an indexed array of `ConfigValue`; The index is expected to be an existing value of the self::PriorityLevel array.
     * @param ?array $availableLevels   If this is null, only empty `ConfigValue` entries are filtered out. If it is not null, only those entries of $priorityArray are returned, that have a key, that is set in $availableLevels.
     * @return array
     */
    public function filterConfigValueArray(
        array $priorityArray, 
        ?array $availableLevels): array
    {
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

        return $finalValues;
    }

    /**
     * Prepares a initial priority array, based on the clas constant levelPriority.
     * The values are all null, so other methods can use this to check, if values have been loaded or not.
     * (If they are null, they probably have not been loaded.)
     *
     * @return array
     */
    public static function getPriorityArray(): array
    {
        $retval = [];

        foreach(self::levelPriority as $key => $priority) {
            $retval[$key] = null;
        }

        return $retval;
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
        $configValue = $this->loadUserConfig($technicalName, $availableLevels, $user);

        if($configValue !== null) {
            return $configValue;
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
        $priorityArray = $this->sortConfigValueArray($configValues);

        // Filter out unwanted keys and entries, where no data has been loaded,
        // but only, if filtering is wanted.
        $finalValues = $this->filterConfigValueArray($priorityArray, $availableLevels);

        if (count($finalValues) == 0) {
            return null;
        }

        // Get the entry with the highest priority.
        $configValue = array_pop($finalValues);

        return $configValue->getValue();
    }

    /**
     * Load ConfigDefinition from database.
     *
     * @param  string $technicalName
     * @return ConfigDefinition
     */
    public function loadConfigDefinition(string $technicalName): ConfigDefinition
    {
        $configDefinition = $this->configDefinitionRepository->findOneBy([ 
            'technicalName' => $technicalName
        ]);

        if(null === $configDefinition) {
            throw new ConfigDefinitionNotFoundException('No ConfigDefinition with technical name "' . $technicalName . '" found.');
        }

        return $configDefinition;
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
     * Sorts an array of configValues by there priority.
     * Returns a sorted associative array, with the priority as key.
     *
     * @param  array $configValues
     * @return array
     */
    public function sortConfigValueArray(array $configValues): ?array
    {
        $priorityArray = self::getPriorityArray();

        foreach ($configValues as $configValue) {
            $priority = $configValue->getLevel();

            if (!array_key_exists($priority, $priorityArray)) {
                throw new \RuntimeException('An undefined level has been provided as key.');
            }

            $priorityArray[$priority] = $configValue;
        }

        return $priorityArray;
    }
    
    /**
     * Load user configuration.
     *
     * @param string $technicalName
     * @param ?array $availableLevels
     * @return ?string
     */
    private function loadUserConfig(
        string $technicalName,
        ?array $availableLevels,
        ?User $user): ?string
    {
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

        return null;
    }
}
