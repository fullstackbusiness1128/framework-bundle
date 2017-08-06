<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Bundle\FrameworkBundle\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Command\CachePoolClearCommand;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @group functional
 */
class CachePoolClearCommandTest extends WebTestCase
{
    protected function setUp()
    {
        static::bootKernel(array('test_case' => 'CachePoolClear', 'root_config' => 'config.yml'));
    }

    public function testClearPrivatePool()
    {
        $tester = $this->createCommandTester();
        $tester->execute(array('pools' => array('cache.private_pool')), array('decorated' => false));

        $this->assertSame(0, $tester->getStatusCode(), 'cache:pool:clear exits with 0 in case of success');
        $this->assertContains('Clearing cache pool: cache.private_pool', $tester->getDisplay());
        $this->assertContains('[OK] Cache was successfully cleared.', $tester->getDisplay());
    }

    public function testClearPublicPool()
    {
        $tester = $this->createCommandTester();
        $tester->execute(array('pools' => array('cache.public_pool')), array('decorated' => false));

        $this->assertSame(0, $tester->getStatusCode(), 'cache:pool:clear exits with 0 in case of success');
        $this->assertContains('Clearing cache pool: cache.public_pool', $tester->getDisplay());
        $this->assertContains('[OK] Cache was successfully cleared.', $tester->getDisplay());
    }

    public function testClearPoolWithCustomClearer()
    {
        $tester = $this->createCommandTester();
        $tester->execute(array('pools' => array('cache.pool_with_clearer')), array('decorated' => false));

        $this->assertSame(0, $tester->getStatusCode(), 'cache:pool:clear exits with 0 in case of success');
        $this->assertContains('Clearing cache pool: cache.pool_with_clearer', $tester->getDisplay());
        $this->assertContains('[OK] Cache was successfully cleared.', $tester->getDisplay());
    }

    public function testCallClearer()
    {
        $tester = $this->createCommandTester();
        $tester->execute(array('pools' => array('cache.default_clearer')), array('decorated' => false));

        $this->assertSame(0, $tester->getStatusCode(), 'cache:pool:clear exits with 0 in case of success');
        $this->assertContains('Calling cache clearer: cache.default_clearer', $tester->getDisplay());
        $this->assertContains('[OK] Cache was successfully cleared.', $tester->getDisplay());
    }

    /**
     * @expectedException        \Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException
     * @expectedExceptionMessage You have requested a non-existent service "unknown_pool"
     */
    public function testClearUnexistingPool()
    {
        $this->createCommandTester()
            ->execute(array('pools' => array('unknown_pool')), array('decorated' => false));
    }

    /**
     * @group legacy
     * @expectedDeprecation Passing a command name as the first argument of "Symfony\Bundle\FrameworkBundle\Command\CachePoolClearCommand::__construct" is deprecated since version 3.4 and will be removed in 4.0. If the command was registered by convention, make it a service instead.
     */
    public function testLegacyClearCommand()
    {
        $application = new Application(static::$kernel);
        $application->add(new CachePoolClearCommand());

        $tester = new CommandTester($application->find('cache:pool:clear'));

        $tester->execute(array('pools' => array()));

        $this->assertContains('Cache was successfully cleared', $tester->getDisplay());
    }

    private function createCommandTester()
    {
        $container = static::$kernel->getContainer();
        $application = new Application(static::$kernel);
        $application->add(new CachePoolClearCommand($container->get('cache.global_clearer')));

        return new CommandTester($application->find('cache:pool:clear'));
    }
}
