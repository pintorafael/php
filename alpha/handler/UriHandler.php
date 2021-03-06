<?php
/**
 * UriHandler
 *
 * @author Rafael Pinto <santospinto.rafael@gmail.com>
 */
namespace Alpha\Handler;

use Alpha\Exception\ComponentNotFoundException;

/**
 * Class that handles URI components.
 */
class UriHandler
{
    // components should by defined as {(type):(name)}
    const COMPONENT_REGEX = '{(.*?):(.*?)}';
    
    protected $pattern, $uri, $components, $componentsAreInit;
    
    /**
     * Constructs an UriHandler.
     */
    public function __construct()
    {
        $this->components        = array();
        $this->componentsAreInit = false;
    }
    
    /**
     * Sets the URI pattern.
     * 
     * @param string $pattern The pattern.
     * 
     * @return void
     */
    public function setPattern($pattern)
    {
        $this->pattern           = $pattern;
        $this->componentsAreInit = false;
    }
    
    /**
     * Sets the URI.
     * 
     * @param string $uri The URI.
     * 
     * @return void
     */
    public function setUri($uri)
    {
        $this->uri               = $uri;
        $this->componentsAreInit = false;
    }
    
    /**
     * Returns the value of the URI component.
     * 
     * @param string $componentName The name of the component.
     * 
     * @return mixed
     */
    public function getComponent($componentName)
    {
        if(!$this->componentsAreInit) {
            $this->buildComponents();
        }
        
        if(!isset($this->components[$componentName])){
            throw new ComponentNotFoundException($componentName);
        }
        
        return $this->components[$componentName]['value'];
    }
    
    /**
     * Returns the components of the URI.
     * 
     * @return array
     */
    public function getComponents()
    {
        if (!$this->componentsAreInit) {
            $this->buildComponents();
        }

        return $this->components;
    }
        
    /**
     * Sets the value of a component.
     * 
     * @param string $name  The name of the component.
     * @param mixed  $value The value of the component.
     * 
     * @return void
     */
    public function setComponent($name, $value)
    {
        $this->components[$name]['value'] = $value;
        $this->components[$name]['type']  = filter_var($value, FILTER_VALIDATE_INT) !== false ? (int) $value : (string) $value;
    }
    
    /**
     * Sets the components.
     * 
     * @param array $components The array containing the components.
     * 
     * @return void
     */
    public function setComponents(array $components)
    {
        foreach($components as $name => $value){
            $this->setComponent($name, $value);
        }
    }

        
    /**
     * Builds the URI components from the defined URI.
     * 
     * @return void
     */
    public function buildComponents()
    {
        $this->componentsAreInit = true;        
        $this->components        = array();
        $matches                 = array();
        $found                   = preg_match_all(sprintf('#%s#', static::COMPONENT_REGEX), $this->pattern, $matches);
        if($found) {
            $uriComponents   = parse_url($this->uri, PHP_URL_PATH);
            $uriParts        = array_filter(explode('/', $uriComponents));            
            $uriPatternParts = array_filter(explode('/', $this->pattern));            
            for($i=0; $i < $found; $i++){
                $part                              = array_shift($uriParts);
                $patternPart                       = array_shift($uriPatternParts);
                $ignoreValue                       = str_replace($matches[0][$i], '', $patternPart);
                $value                             = str_replace($ignoreValue, '', $part);
                $this->components[$matches[2][$i]] = array(
                                                        'type'  => $matches[1][$i], 
                                                        'raw'   => $value, 
                                                        'value' => $this->makeValue($matches[1][$i], $value)
                                                    ); 
            }
        }
    }
    
    /**
     * Returns the properly casted valued.
     * 
     * @param string $type  The type of the value.
     * @param string $value The value.
     * 
     * @return mixed
     */
    protected function makeValue($type, $value)
    {
        switch ($type) {
            case 'i':
                return (int) $value;
            default :
                return (string) $value;
        }
    }
}
