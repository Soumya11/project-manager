<?php

namespace SIOFramework\Common\Controller;

use Slim\Slim;
use SIOFramework\Common\Factory\DatabaseFactoryInterface;
use SIOFramework\Common\Factory\StandardFactory;


abstract class DefaultController implements ControllerInterface
{
    /**
     * @var Slim $app
     */
    protected $app;

    /**
     * @var array $data
     */
    protected $data;

    /**
     * @var \Twig_Environment $twig
     */
    protected $twig;

    /*
     * @var Language
     */
    protected $language;

    /**
     * @var DatabaseFactoryInterface 
     */
    protected $databaseFactory;
    
    /**
     * @var array of WidgetInterface
     */
    protected $widgets;


    /**
     * DefaultController constructor.
     * @param Slim $app
     */
    public function __construct(Slim $app)
    {
        $this->app = $app;
        $this->data = array_merge(array(),$app->request->params());

        $this->widgets = array();
        $this->twig = $this->app->container->get('twig');
        
        $this->databaseFactory = $app->container->get('databaseFactory');
    }

    /**
     * Renders a view.
     * E.g.: $view = "module/view.twig"
     *
     * If $args is NULL, it will use $this->data
     * as the view data.
     *
     * @param string $view
     * @param null|array $args
     */
    public function render($view, $args=NULL, $returnString=FALSE)
    {
        $this->processWidgets();
        $args = ($args == NULL ? $this->data : $args);

        $resp = $this->twig->loadTemplate($view);

        if($returnString)
        	return $resp->render($args);
        else
        	echo $resp->render($args);
    }
    
    

    /**
     * Adds a widget to controller.
     *
     * @param WidgetInterface $widget
     */
    public function addWidget(WidgetInterface $widget)
    {
        $this->widgets[] = $widget;
    }

    /**
     * Gets a list of widgets.
     *
     * @return array
     */
    protected function getWidgets()
    {
        $widgets = array();
        foreach($this->widgets as $w)
        {
            /**
             * @var $w WidgetInterface
             */
            $name = $w->getPartial();

            $widgets[$name] = $w;
        }

        return $widgets;
    }

    /**
     * Process the widgets.
     * Creates $this->data['Widgets'].
     */
    protected function processWidgets()
    {
        $this->data['Widgets'] = array();

        foreach($this->getWidgets() as $key=>$val)
        {
            $this->data['Widgets'][$key] = $val->renderWidget();
        }
    }
    
    
}