<?php

namespace Wave\View;

/**
 * Class Template
 * @package Wave\View
 */
class Template
{
    protected $contents = '';
    protected $data = array();

    private $template = null;
    private $ext = array();

    /**
     * @param $template string Template file
     * @param $path string Template directory
     * @param $extension string Template Extension
     */
    public function __construct($template, $path, $extension)
    {
        $this->template = sprintf("%s/%s.%s", $path, $template, $extension);
    }

    /**
     * Returns the variable for the template or null
     *
     * @param $key string The assigned value to retrieve
     *
     * @return mixed
     */
    public function __get($key)
    {
        $value = null;
        if (array_key_exists($key, $this->data)) {
            $value = $this->data[$key];
        }

        return $value;
    }

    /**
     * Resolves the template 'extensions'
     *
     * @param $method string Name of the extension being called
     * @param $args array arguments passed to the extension
     *
     * @return mixed The result of the extension execution
     *
     * @codeCoverageIgnore
     */
    public function __call($method, $args)
    {
        if (array_key_exists($method, $this->ext)) {
            return call_user_func_array($this->ext[$method], $args);
        }
    }

    /**
     * @param $key The key used in the template
     * @param $value The value for it
     *
     * @return $this
     *
     */
    public function assign($key, $value)
    {
        $this->data[$key] = $value;

        return $this;
    }

    /**
     * @param $data array Assoc array to bulkly define the keys
     *
     * @return $this
     */
    public function assignAll($data)
    {
        $this->data = array_merge($this->data, $data);

        return $this;
    }

    /**
     * @param $name string Name to be used in the template
     * @param $obj callable The callback
     */
    public function addExtension($name, $obj)
    {
        $this->ext[$name] = $obj;
    }

    /**
     * @return string The template output
     */
    public function __toString()
    {
        ob_start();
        include ($this->template);

        return ob_get_clean();
    }
}