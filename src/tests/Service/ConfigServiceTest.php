<?php declare(strict_types=1);

use App\Service\ConfigService;
use App\Exception\ConfigDefinitionNotFoundException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class ConfigServiceTest extends KernelTestCase
{
    private $doctrine;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->doctrine = $kernel->getContainer()
            ->get('doctrine');
    }
        
    /**
     * a) Test that ConfigDefinition is NOT found,
     *    if a technicalName is provided, that is NOT present in the database.
     *
     * @return void
     */
    public function testConfigDefinitionNotFound(): void
    {
        $configService = new ConfigService($this->doctrine);
        $this->expectException(ConfigDefinitionNotFoundException::class);
        $config = $configService->loadConfig('not available key', ['internal']);
    }

    
    // c) if the correct value is returned, when a restriction to specific group is given.
    // d) Restriction to foreign keys.
    // e) All tests before, but with values not found.
    /**
     * a) Test that ConfigDefinition is NOT found,
     *    if a technicalName is provided, that is NOT present in the database.
     *
     * @return void
     */
    public function testConfigService(): void
    {
        // b) Test, if the highest non-team and non-user-level value is loaded correctly.
        $configService = new ConfigService($this->doctrine);
        $config = $configService->loadConfig('Smtp');
        $this->assertEquals(
            "{\"server\":\"smtp.system.example\",\"username\":\"hold\",\"password\":\"supersecure123\",\"sender\":\"hold@example\",\"encryption\":\"tls\",\"port\":\"587\"}",
            $config
        );
        
        // c) Test, if a specific setting is received correctly, e.g. order was correct.
        $configService = new ConfigService($this->doctrine);
        $config = $configService->loadConfig('Smtp', ['initial']);
        $this->assertEquals(
            '{"server":"smtp.internal.example","username":"jake","password":"whoCares123?","sender":"jake@brooklin99.example","encryption":"tls","port":"587"}',
            $config
        );
    }

}