<?php
/**
 * ViewConnector
 *
 * @author Rafael Pinto <santospinto.rafael@gmail.com>
 */
namespace Connectors;

use Alpha\Connector\ViewConnectorInterface;
use Connectors\View\ViewTagAbstract;
use Connectors\View\PropertiesTag;
use Connectors\View\ForeachTag;
use Connectors\View\IncludeTag;
use Connectors\View\UsesTag;
use Connectors\View\StringTag;
use Connectors\View\ConfigTag;
use Connectors\View\ConditionalPropertiesTag;

/**
 * Base class for Views.
 */
class ViewConnector implements ViewConnectorInterface
{
    protected $tags;
    
    /**
     * Constructs a ViewConnector.
     */
    public function __construct()
    {
        $this->tags = array();
        $this->registerTag(new UsesTag());
        $this->registerTag(new IncludeTag());
        $this->registerTag(new ForeachTag());
        $this->registerTag(new PropertiesTag());
        $this->registerTag(new ConditionalPropertiesTag());
        $this->registerTag(new ConfigTag());
        $this->registerTag(new StringTag());
    }

    /**
     * Registers a tag in the View.
     * 
     * @param \Alpha\Web\View\ViewTagAbstract $tag The tag to be registered.
     * 
     * @return void
     */
    public function registerTag(ViewTagAbstract $tag)
    {
        $this->tags[] = $tag;
    }
        
    /**
     * Renders the content of the view with the data.
     * 
     * @param string $content The original content.
     * @param array  $data    The data to bind.
     * 
     * @return string
     */
    public function render($content, array $data)
    {
        foreach ($this->tags as $tag){            
            $content = $tag->render($content, $data);
        }
        return $content;
    }
    
    /**
     * Sets the configuration.
     * 
     * @param array $configuration The array containing the connector configuration.
     * 
     * @return void
     */
    public function setup(array $configuration)
    {
        // void
    }         
}
