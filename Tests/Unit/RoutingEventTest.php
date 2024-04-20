<?php

declare(strict_types=1);

namespace Hschulz\Router\Tests\Unit;

use Hschulz\Http\Request\Request;
use Hschulz\Http\Response\Response;
use Hschulz\Router\Route\StaticRoute;
use Hschulz\Router\RoutingEvent;
use PHPUnit\Framework\TestCase;

/**
 *
 */
class RoutingEventTest extends TestCase
{
    /**
     *
     * @var RoutingEvent|null
     */
    protected ?RoutingEvent $event = null;

    protected function setUp(): void
    {
        $this->event = new RoutingEvent('', [], new Request());
    }

    protected function tearDown(): void
    {
        $this->event = null;
    }

    public function testIsCreatedWithRequest() {
        $this->assertNotNull($this->event->getRequest());
        $this->assertEmpty($this->event->getName());
        $this->assertEmpty($this->event->getParams());
        $this->assertEmpty($this->event->getResponse());
        $this->assertEmpty($this->event->getRoute());
    }

    public function testCanSetRequest() {

        $this->event->setRequest(new Request());

        $this->assertNotNull($this->event->getRequest());
        $this->assertEmpty($this->event->getName());
        $this->assertEmpty($this->event->getParams());
        $this->assertEmpty($this->event->getResponse());
        $this->assertEmpty($this->event->getRoute());
    }

    public function testCanSetName() {

        $this->event->setName('Unit-Test-Event');

        $this->assertNotNull($this->event->getRequest());
        $this->assertEquals('Unit-Test-Event', $this->event->getName());
        $this->assertEmpty($this->event->getParams());
        $this->assertEmpty($this->event->getResponse());
        $this->assertEmpty($this->event->getRoute());
    }

    public function testCanSetParams() {

        $params = ['Test' => 'test', 1 => 0.1];

        $this->event->setParams($params);

        $this->assertNotNull($this->event->getRequest());
        $this->assertEmpty($this->event->getName());
        $this->assertEquals($params, $this->event->getParams());
        $this->assertEmpty($this->event->getResponse());
        $this->assertEmpty($this->event->getRoute());
    }

    public function testCanSetResponse() {

        $response = new Response();

        $this->event->setResponse($response);

        $this->assertNotNull($this->event->getRequest());
        $this->assertEmpty($this->event->getName());
        $this->assertEmpty($this->event->getParams());
        $this->assertEquals((string) $response, (string) $this->event->getResponse());
        $this->assertEmpty($this->event->getRoute());
    }

    public function testCanSetRoute() {

        $route = new StaticRoute('/');

        $this->event->setRoute($route);

        $this->assertNotNull($this->event->getRequest());
        $this->assertEmpty($this->event->getName());
        $this->assertEmpty($this->event->getParams());
        $this->assertEmpty($this->event->getResponse());
        $this->assertNotNull($this->event->getRoute());
    }
}
