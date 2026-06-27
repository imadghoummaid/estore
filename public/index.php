<?php
namespace PHPMVC;

use PHPMVC\Lib\Authentication;
use PHPMVC\Lib\Messenger;
use PHPMVC\Lib\Registry;
use PHPMVC\Lib\FrontController;
use PHPMVC\Lib\Language;
use PHPMVC\Lib\SessionManager;
use PHPMVC\Lib\Template\Template;
use PHPMVC\Lib\Router;

if(!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}

// Area Detection for Session and Template Loading
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$url_parts = explode('/', trim($path, '/'), 2);
$area = (isset($url_parts[0]) && strtolower($url_parts[0]) === 'admin') ? 'admin' : 'front';

if(!defined('CURRENT_AREA')) {
    define('CURRENT_AREA', $area);
}

require_once '..' . DS . 'app' . DS . 'config' . DS . 'config.php';
require_once '..' . DS . 'vendor' . DS . 'autoload.php';

$session = new SessionManager();
$session->start();

if(!isset($session->lang)) {
    $session->lang = APP_DEFAULT_LANGUAGE;
}

$template_parts = require_once '..' . DS . 'app' . DS . 'config' . DS . 'templateconfig' . CURRENT_AREA . '.php';

$template = new Template($template_parts);

$language = new Language();

$messenger = Messenger::getInstance($session);

$authentication = Authentication::getInstance($session);

$registry = Registry::getInstance();
$registry->session = $session;
$registry->language = $language;
$registry->messenger = $messenger;

// Simple Router Setup
$router = new Router();
$router->add('', ['area' => 'front', 'controller' => 'index', 'action' => 'default']);
$router->add('admin', ['area' => 'admin', 'controller' => 'index', 'action' => 'default']);
$router->add('admin/{controller}/{action}', ['area' => 'admin']);
$router->add('admin/{controller}/{action}/{id:\d+}', ['area' => 'admin']);
$router->add('{controller}/{action}', ['area' => 'front']);
$router->add('{controller}/{action}/{id:\d+}', ['area' => 'front']);

$registry->router = $router;

$frontController = new FrontController($template, $registry, $authentication);
$frontController->dispatch();
