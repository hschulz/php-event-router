<?php

declare(strict_types=1);

namespace Hschulz\Router\Tests\Integration\Route;

use Hschulz\Config\JSONConfigurationManager;
use Hschulz\Event\EventDispatcher;
use Hschulz\Router\Route\Factory;
use Org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;

final class FactoryTest extends TestCase
{
    /**
     *
     * @var EventDispatcher|null
     */
    protected ?EventDispatcher $dispatcher = null;

    /**
     *
     * @var JSONConfigurationManager|null
     */
    protected ?JSONConfigurationManager $config = null;

    /**
     *
     * @var string
     */
    protected $file = '';

    protected function setUp(): void
    {
        vfsStream::setup('integration');

        $this->file = vfsStream::url('integration/config.json');

        file_put_contents($this->file, '{"Router":{"config":{"strict":true},"plugins":{"routes":{"Segment":"hschulz\\\\Router\\\\Route\\\\SegmentedRoute","Static":"hschulz\\\\Router\\\\Route\\\\StaticRoute"},"controller":{"home":""}},"routes":[{"name":"default","type":"Segment","controller":"home","action":"show","path":"/"},{"name":"home","scheme":"http","domain":"localhost","port":"88","path":"/","type":"Static","controller":"home","action":"show","may_end":true,"methods":["GET","POST","PUT","DELETE","HEAD","OPTIONS"]},{"name":"meep","path":"/derp/herp/merp","type":"Static","controller":"home","action":"show","may_end":true,"scheme":"http","domain":"localhost","port":"88","methods":["POST"]},{"name":"imprint","path":"/imprint","type":"Static","controller":"home","action":"show","may_end":true,"scheme":"http","domain":"localhost","port":"88"}]}}');

        $this->config = new JSONConfigurationManager($this->file, 'integration');

        $this->config['Router']['routes'][1]['segments'][] = [
            'name' => 'test',
            'path' => ':argument',
            'type' => 'Segment',
            'controller' => '',
            'action' => ''
        ];
    }

    protected function tearDown(): void
    {
        $this->config = null;
        $this->file = '';
    }

    public function testFactory()
    {
        $factory = new Factory($this->config);

        $this->assertEquals($this->config, $factory->getConfigurationHandler());
    }
}
