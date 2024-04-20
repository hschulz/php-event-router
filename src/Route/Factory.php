<?php

declare(strict_types=1);

namespace Hschulz\Router\Route;

use Hschulz\Config\Configurable;
use Hschulz\Config\ConfigurationManager;
use Hschulz\Network\Port;
use Hschulz\Router\Route\RouteInterface;

/**
 *
 */
class Factory implements Configurable
{
    /**
     *
     * @var array
     */
    protected $routes = [];

    /**
     *
     * @var ConfigurationManager
     */
    protected $config = null;

    /**
     *
     * @var array
     */
    protected $routePlugins = [];

    /**
     *
     * @var array
     */
    protected $controllerPlugins = [];

    /**
     *
     * @param ConfigurationManager $config
     */
    public function __construct(ConfigurationManager $config) {
        $this->routePlugins = [];
        $this->controllerPlugins = [];
        $this->routes = [];
        $this->setConfiguationHandler($config);
    }

    /**
     *
     * @return ConfigurationManager
     */
    public function getConfigurationHandler(): ConfigurationManager {
        return $this->config;
    }

    /**
     *
     * @param ConfigurationManager $config
     * @return void
     */
    public function setConfiguationHandler(ConfigurationManager $config): void {
        $this->config = $config;

        $this->loadRoutePlugins();
        $this->loadControllerPlugins();
    }

    /**
     * @return void
     */
    protected function loadRoutePlugins() {

        if (!empty($this->config['Router']['plugins']['routes'])) {
            $routePlugins = $this->config['Router']['plugins']['routes'];

            foreach ($routePlugins as $name => $FQCN) {
                $this->routePlugins[$name] = str_replace('\\\\', '\\', $FQCN);
            }
        }
    }

    /**
     * @return void
     */
    protected function loadControllerPlugins() {

        if (!empty($this->config['Router']['plugins']['controller'])) {
            $controllerPlugins = $this->config['Router']['plugins']['controller'];

            foreach ($controllerPlugins as $name => $FQCN) {
                $this->controllerPlugins[$name] = $FQCN;
            }
        }
    }

    /**
     *
     * @param array $data
     * @param RouteInterface|null $parent
     * @return void
     */
    public function makeRoute(array $data, ?RouteInterface $parent = null): void {

        $route = null;

        $mayEnd = !isset($data['may_end']) ? true : (bool) $data['may_end'];

        $routeClass = $this->routePlugins[$data['type']] ?? $data['type'] ?? '';
        $controllerClass = $this->controllerPlugins[$data['controller']] ?? $data['controller'] ?? '';

        /* if the route may not end here there is no need for a controller or action */
        if (class_exists($routeClass) || !$mayEnd) {

            $path = '';

            if (!empty($parent)) {
                $path = $parent->getPath();
            }

            $path .= $data['path'];

            // check if action exists in controller class?

            $action = '';

            if (!empty($data['action'])) {
                $action = $data['action'];
            }

            $route = new $routeClass($path, $controllerClass, $action);
            $route->setName($data['name']);
            $route->ends($mayEnd);

            $this->setRouteParamsFromConfig($route, $data);

            if (!empty($parent)) {
                $this->setRouteParamsFromParent($route, $parent);
            }

            if ($mayEnd) {
                $this->routes[] = $route;
            }

            if (!empty($data['segments'])) {
                foreach ($data['segments'] as $child) {
                    $this->makeRoute($child, $route);
                }
            }
        }
    }

    /**
     *
     * @param RouteInterface $route
     * @param array $segment
     * @return void
     */
    protected function setRouteParamsFromConfig(RouteInterface $route, array $segment): void {

        if (!empty($segment['methods'])) {
            $route->setMethods($segment['methods']);
        }

        if (!empty($segment['scheme'])) {
            $route->setScheme($segment['scheme']);
        }

        if (!empty($segment['domain'])) {
            $route->setDomain($segment['domain']);
        }

        if (!empty($segment['port'])) {
            $route->setPort(new Port((int) $segment['port']));
        }
    }

    /**
     *
     * @param RouteInterface $route
     * @param RouteInterface $parent
     * @return void
     */
    protected function setRouteParamsFromParent(RouteInterface $route, RouteInterface $parent): void {

        if (!empty($parent->getMethods())) {
            $route->setMethods($parent->getMethods());
        }

        if (!empty($parent->getScheme())) {
            $route->setScheme($parent->getScheme());
        }

        if (!empty($parent->getDomain())) {
            $route->setDomain($parent->getDomain());
        }

        if (!empty($parent->getPort())) {
            $route->setPort($parent->getPort());
        }
    }

    /**
     *
     * @return array<RouteInterface>
     */
    public function getRoutes(): array {
        return $this->routes;
    }
}
