<?php
namespace PHPMVC;

use PHPMVC\lib\Authentication;
use PHPMVC\lib\Messenger;
use PHPMVC\Lib\Registry;
use PHPMVC\LIB\FrontController;
use PHPMVC\LIB\Language;
use PHPMVC\LIB\SessionManager;
use PHPMVC\LIB\Template\Template;

if(!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}

require_once '..' . DS . 'app' . DS . 'config' . DS . 'config.php';
require_once APP_PATH . DS . 'lib' . DS . 'autoload.php';

$url = explode('/', trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/'), 3);
$area = 'front';
if(isset($url[0]) && $url[0] == 'admin') {
    $area = 'admin';
}

$session = new SessionManager($area);
$session->start();

if(!isset($session->lang)) {
    $session->lang = APP_DEFAULT_LANGUAGE;
}

$template_parts = require_once '..' . DS . 'app' . DS . 'config' . DS . 'templateconfig' . $area . '.php';

$template = new Template($template_parts);

$language = new Language();

$messenger = Messenger::getInstance($session);

$authentication = Authentication::getInstance($session);

$registry = Registry::getInstance();
$registry->session = $session;
$registry->language = $language;
$registry->messenger = $messenger;

$frontController = new FrontController($template, $registry, $authentication);
$frontController->dispatch();
