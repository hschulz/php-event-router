<?php

declare(strict_types=1);

namespace Hschulz\Router\Route;

use Hschulz\Router\Route\RouteInterface;
use Hschulz\DataStructures\Tree\AbstractTreeNode;
use Hschulz\Http\Request\Request;
use Hschulz\Http\Request\Header;
use Hschulz\Network\Port;

use function array_search;
use function stripos;

/**
 * Description of AbstractRoute
 */
abstract class AbstractRoute
    extends AbstractTreeNode
        implements RouteInterface
{
    /**
     * The route name.
     * 
     * @var string
     */
    protected string $name = '';

    /**
     * The requested path.
     * 
     * @var string
     */
    protected string $path = '';

    /**
     * The responsible controller class.
     * 
     * @var string
     */
    protected string $controller = '';

    /**
     * Method of the controller that will be called.
     * 
     * @var string
     */
    protected string $action = '';

    /**
     * Required domain for this route to match.
     * 
     * @var string
     */
    protected string $domain = '';

    /**
     * Required scheme for this route to match.
     * 
     * @var string
     */
    protected string $scheme = '';

    /**
     * Required Port for this route.
     * 
     * @var Port
     */
    protected ?Port $port = null;

    /**
     * Supported request methods for this route.
     * 
     * @var array
     */
    protected array $methods = [];

    /**
     * The route may end here.
     * 
     * @var bool
     */
    protected bool $mayEnd = false;

    /**
     * Route parameters.
     * 
     * @var string
     */
    protected array $params = [];

    /**
     *
     * @param string $path Description
     * @param string $controller
     * @param string $action
     */
    public function __construct(
        string $path,
        string $controller = '',
        string $action = ''
    ) {
        parent::__construct();
        
        $this->path        = $path;
        $this->controller  = $controller;
        $this->action      = $action;
        $this->name        = '';
        $this->domain      = '';
        $this->scheme      = '';
        $this->methods     = [];
        $this->port        = null;
        $this->mayEnd      = false;
        $this->params      = [];
    }

    /**
     *
     */
    abstract protected function parseParam();

    /**
     *
     * @param Request $request
     * @return bool
     */
    public function matches(Request $request): bool
    {
        $isMethodMatch = $this->matchesMethod($request->getRequestMethod());

        if (!$isMethodMatch) {
            return false;
        }

        $isDomainMatch = $this->matchesDomain(
            $request->getHeader()->getHeader(Header::HOST)
        );

        if (!$isDomainMatch) {
            return false;
        }

        $matchesSecurity = $this->matchesSecurity($request->isSecure());

        if (!$matchesSecurity) {
            return false;
        }

        /* If a specific port is required */
        if ($this->port !== null) {
            /* But the request port does not match */
            if ((string) $this->port !== (string) $request->getPort()) {
                return false;
            }
        }

        return true;
    }

    /**
     *
     * @param string $method
     * @return bool
     */
    public function matchesMethod(string $method): bool
    {
        if (!empty($this->methods)) {
            /* If the requested method type is not in the list */
            return array_search($method, $this->methods, true) !== false;
        }

        return true;
    }

    /**
     *
     * @param string $domain
     * @return bool
     */
    public function matchesDomain(string $domain): bool
    {
        /* If a domain is specified for the route */
        if (!empty($this->domain)) {

            /* If the domain is at least partly within the hostname */
            return stripos($domain, $this->domain) !== false;
        }

        return true;
    }

    /**
     *
     * @param bool $isSecure
     * @return bool
     */
    public function matchesSecurity(bool $isSecure): bool
    {
        /* If a secure scheme is required but the request is not secure */
        if ($this->scheme === 'https' && !$isSecure) {
            return false;
        }

        /* If the non secure scheme is required but the request is secure */
        if ($this->scheme === 'http' && $isSecure) {
            return false;
        }

        return true;
    }

    /**
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     *
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     *
     * @return string
     */
    public function getController(): string
    {
        return $this->controller;
    }

    /**
     *
     * @return string
     */
    public function getAction(): string
    {
        return $this->action;
    }

    /**
     *
     * @return string
     */
    public function getDomain(): string
    {
        return $this->domain;
    }

    /**
     *
     * @return string
     */
    public function getScheme(): string
    {
        return $this->scheme;
    }

    /**
     *
     * @return Port|null
     */
    public function getPort(): ?Port
    {
        return $this->port;
    }

    /**
     *
     * @return array
     */
    public function getMethods(): array
    {
        return $this->methods;
    }

    /**
     *
     * @return bool
     */
    public function mayEnd(): bool 
    {
        return $this->mayEnd;
    }

    /**
     *
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     *
     * @param string $name
     * @return void
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     *
     * @param string $path
     * @return void
     */
    public function setPath(string $path): void
    {
        $this->path = $path;
    }

    /**
     *
     * @param string $controller
     * @return void
     */
    public function setController(string $controller): void
    {
        $this->controller = $controller;
    }

    /**
     *
     * @param string $action
     * @return void
     */
    public function setAction(string $action): void
    {
        $this->action = $action;
    }

    /**
     *
     * @param string $domain
     * @return void
     */
    public function setDomain(string $domain): void
    {
        $this->domain = $domain;
    }

    /**
     *
     * @param string $scheme
     * @return void
     */
    public function setScheme(string $scheme): void
    {
        $this->scheme = $scheme;
    }

    /**
     *
     * @param Port $port
     * @return void
     */
    public function setPort(Port $port): void
    {
        $this->port = $port;
    }

    /**
     *
     * @param array $methods
     * @return void
     */
    public function setMethods(array $methods): void
    {
        $this->methods = $methods;
    }

    /**
     *
     * @param bool $mayEnd
     * @return void
     */
    public function ends(bool $mayEnd): void
    {
        $this->mayEnd = $mayEnd;
    }

    /**
     *
     * @param array $params
     * @return void
     */
    public function setParams(array $params): void
    {
        $this->params = $params;
    }
}
