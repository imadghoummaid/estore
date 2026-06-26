<?php
namespace PHPMVC\Lib;

use PHPMVC\Lib\Authentication;
use PHPMVC\Lib\Registry;
use PHPMVC\Lib\Template\Template;

class FrontController
{
    use Helper;

    public const NOT_FOUND_ACTION = 'notFoundAction';
    public const NOT_FOUND_CONTROLLER = 'PHPMVC\Controllers\NotFoundController';

    private string $_area = 'front';
    private string $_controller = 'index';
    private string $_action = 'default';
    private array $_params = [];

    public function __construct(
        private Template $_template,
        private Registry $_registry,
        private Authentication $_authentication
    ) {
        $this->_parseUrl();
    }

    private function _parseUrl(): void
    {
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $trimmedPath = trim($path, '/');

        /** @var Router $router */
        $router = $this->_registry->router;

        if ($router) {
            $match = $router->match($trimmedPath);
            if ($match) {
                $this->_area = $match['area'] ?? 'front';
                $this->_controller = $match['controller'] ?? 'index';
                $this->_action = $match['action'] ?? 'default';
                unset($match['area'], $match['controller'], $match['action']);
                $this->_params = $match;
                return;
            }
        }

        $url = explode('/', $trimmedPath, 4);

        if (isset($url[0]) && strtolower($url[0]) === 'admin') {
            $this->_area = 'admin';
            array_shift($url);
        }

        if (isset($url[0]) && $url[0] !== '') {
            $this->_controller = $url[0];
        }
        if (isset($url[1]) && $url[1] !== '') {
            $this->_action = $url[1];
        }
        if (isset($url[2]) && $url[2] !== '') {
            $this->_params = explode('/', $url[2]);
        }
    }

    public function dispatch(): void
    {
        $controllerClassName = 'PHPMVC\Controllers\\' . ucfirst($this->_area) . '\\' . $this->toStudlyCaps($this->_controller) . 'Controller';
        $actionName = $this->toCamelCase($this->_action) . 'Action';

        // Check if the user is authorized to access the application
        if ($this->_area === 'admin') {
            if (!$this->_authentication->isAuthorized()) {
                if ($this->_controller !== 'auth' && $this->_action !== 'login') {
                    $this->redirect('/admin/auth/login');
                }
            } else {
                // deny access to the auth/login
                if ($this->_controller === 'auth' && $this->_action === 'login') {
                    isset($_SERVER['HTTP_REFERER']) ? $this->redirect($_SERVER['HTTP_REFERER']) : $this->redirect('/admin');
                }
                // Check if the user has access to specific url
                if ((bool) CHECK_FOR_PRIVILEGES === true) {
                    if (!$this->_authentication->hasAccess($this->_controller, $this->_action)) {
                        $this->redirect('/admin/accessdenied');
                    }
                }
            }
        }

        if (!class_exists($controllerClassName) || !method_exists($controllerClassName, $actionName)) {
            $controllerClassName = 'PHPMVC\Controllers\\' . ucfirst($this->_area) . '\\NotFoundController';
            $this->_action = $actionName = self::NOT_FOUND_ACTION;

            if (!class_exists($controllerClassName)) {
                $controllerClassName = self::NOT_FOUND_CONTROLLER; // Fallback to original if area-specific not found
            }
        }

        $controller = new $controllerClassName();
        $controller->setArea($this->_area);
        $controller->setController($this->_controller);
        $controller->setAction($this->_action);
        $controller->setParams($this->_params);
        $controller->setTemplate($this->_template);
        $controller->setRegistry($this->_registry);
        $controller->$actionName();
    }

    private function toStudlyCaps(string $string): string
    {
        return str_replace(' ', '', ucwords(str_replace('-', ' ', $string)));
    }

    private function toCamelCase(string $string): string
    {
        return lcfirst($this->toStudlyCaps($string));
    }
}
