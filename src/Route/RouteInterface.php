<?php

declare(strict_types=1);

namespace Hschulz\Router\Route;

use Hschulz\Http\Request\Request;
use Hschulz\Network\Port;

/**
 *
 */
interface RouteInterface
{
    /**
     *
     * @param Request $request
     * @return bool
     */
    public function matches(Request $request): bool;

    /**
     *
     * @return string
     */
    public function getPath(): string;

    /**
     *
     * @param string $path
     * @return void
     */
    public function setPath(string $path): void;

    /**
     * @return string
     */
    public function getController(): string;

    /**
     * @param string $controller
     * @return void
     */
    public function setController(string $controller): void;

    /**
     *
     * @return string
     */
    public function getAction(): string;

    /**
     *
     * @param string $action
     * @return void
     */
    public function setAction(string $action): void;

    /**
     *
     * @return string
     */
    public function getDomain(): string;

    /**
     *
     * @param string $domain
     * @return void
     */
    public function setDomain(string $domain): void;

    /**
     *
     * @return string
     */
    public function getScheme(): string;

    /**
     *
     * @param string $scheme
     * @return void
     */
    public function setScheme(string $scheme): void;

    /**
     *
     * @return array
     */
    public function getMethods(): array;

    /**
     *
     * @param array $methods
     * @return void
     */
    public function setMethods(array $methods): void;

    /**
     *
     * @return bool
     */
    public function mayEnd(): bool;

    /**
     *
     * @param bool $mayEnd
     * @return void
     */
    public function ends(bool $mayEnd): void;

    /**
     *
     * @return string
     */
    public function getName(): string;

    /**
     *
     * @param string $name
     * @return void
     */
    public function setName(string $name): void;

    /**
     *
     * @return Port
     */
    public function getPort(): ?Port;

    /**
     *
     * @param Port $port
     * @return void
     */
    public function setPort(Port $port): void;

    /**
     *
     * @return array
     */
    function getParams(): array;

    /**
     *
     * @param array $params
     * @return void
     */
    function setParams(array $params): void;
}
