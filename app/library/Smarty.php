<?php
namespace Phalcon\Mvc\View\Engine;

use Phalcon\Mvc\View\Engine;
use Phalcon\Mvc\View\EngineInterface;

require_once("smarty/Smarty.class.php");

/**
 * Phalcon\Mvc\View\Engine\Smarty
 * Adapter to use Smarty library as templating engine
 */
class Smarty extends Engine implements EngineInterface
{

    /**
     * @var \Smarty
     */
    protected $smarty;

    /**
     * Class constructor.
     *
     * @param \Phalcon\Mvc\ViewInterface $view
     * @param \Phalcon\DiInterface       $di
     */
    public function __construct($view, $di = null) 
    {
        global $config;
        $current_page_arr = explode('/', $_SERVER['PHP_SELF']);
        $base_url = $config->application->baseUri;
        $app_title = "The best song ";
        $app_version = "";
        
        $this->smarty               = new \Smarty();
        $this->smarty->template_dir = $config->application->templatesDir;
        $this->smarty->compile_dir  = $config->application->templatesCompiledDir;
        $this->smarty->config_dir   = SMARTY_DIR . 'configs';
        $this->smarty->cache_dir    = SMARTY_DIR . 'cache';
        $this->smarty->caching      = false;
        $this->smarty->debugging    = true;
        
        $this->smarty->assign('CONFIG_BASEURL', $base_url);
        $this->smarty->assign('CONFIG_IMAGES', $base_url .'/images');
        $this->smarty->assign('CONFIG_CSS', $base_url.'/css');
        $this->smarty->assign('CONFIG_JS', $base_url .'/js');
        $this->smarty->assign('CONFIG_CURRENT_PAGE', $current_page_arr[count($current_page_arr) - 1]);
        $this->smarty->assign('APPLICATION_TITLE', $app_title);
        $this->smarty->assign('APPLICATION_VERSION', $app_version);        

        parent::__construct($view, $di);
    }

    /**
     * {@inheritdoc}
     *
     * @param string  $path
     * @param array   $params
     * @param boolean $mustClean
     */
    public function render($path, $params, $mustClean = null)
    {
        if (!isset($params['content'])) {
            $params['content'] = $this->_view->getContent();
        }
        foreach ($params as $key => $value) {
            $this->smarty->assign($key, $value);
        }
        $this->_view->setContent($this->smarty->fetch($path));
    }

    /**
     * Set Smarty's options
     *
     * @param array $options
     */
    public function setOptions(array $options)
    {
        foreach ($options as $k => $v) {
            $this->smarty->$k = $v;
        }
    }
}
