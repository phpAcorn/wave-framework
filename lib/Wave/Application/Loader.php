<?php
namespace Wave\Application;

use \Wave\Http;
use \Wave\Pattern\Observer\Subject;
use \Wave\Application;
use \Wave\Route;

/**
 * Loader
 * 
 * @package Wave
 * @author Dimitar Dimitrov
 * @since 1.0.0
 */
class Loader extends Subject
{

    /**
     * Container for the Environment object
     * 
     * @var \Wave\Application\Environment
     */
    protected $environment = null;

    /**
     * Container for the Http\Factory object
     * 
     * @var \Wave\Http\Factory
     */
    protected $http = null;

    /**
     * Container for the Controller
     * 
     * @var \Wave\Application\Controller
     */
    protected $controller = null;

    /**
     * The placeholder for the default configuration
     * 
     * @var array
     */
    protected $defaultConfig = null;

    /**
     * The configuration currently in use
     * 
     * @var array
     */
    protected $config = null;

    /**
     * Constructs the Loader class and injects an array with dependencies
     * for the application.
     * After the instantiation only the containers
     * for Environment and Controller are available.
     *
     * @param array $config
     *            Array with configurations
     */
    public function __construct($config = array())
    {
        $this->defaultConfig = array(
            'mode' => 'devel',
            'debug' => true,
            'handlers' => array(
                'view' => '\Wave\View\Plates\Wrapper',
                'log' => '\Wave\Application\Logger'
            ),
            'environment' => array(
                'request.protocol' => (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.1'),
                'request.port' => (isset($_SERVER['SERVER_PORT']) ? $_SERVER['SERVER_PORT'] : 80),
                'request.uri' => (isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/'),
                'request.method' => (isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'GET')
            ),
            'routes.case_sensitive' => false,
            'template' => array(
                'path' => '../application/templates',
                'extension' => 'phtml',
                'folders' => array(),
                'extensions' => array()
            )

        );
        
        $this->config = array_merge($this->defaultConfig, $config);
        
        $this->controller = new Application\Controller();
        $this->environement = new Application\Environment($this->config['environment']);

        /*
         * Build the HTTP handlers
         */
        $this->state('httpBefore')->notify($this->environement);

        $this->http = new Http\Factory(
            new Http\Request($this->environement),
            new Http\Response($this->environement['request.protocol'])
        );

        $this->state('httpAfter')->notify($this->environement);
    }

    /**
     * @param $key string Variable to get
     *
     * @return mixed the variable value
     */
    public function __get($key)
    {
        if (!isset($this->$key)) {
            return null;
        }

        return $this->$key;
    }

    /**
     * @param string $name The variable name to access
     * @param array $args the key of the value to retrieve, currently only useful for
     *                    when getting config options.
     *
     * @return mixed|null
     */
    public function __call($name, $args = array())
    {
        $result = null;

        if (!empty($args)) {
            if (array_key_exists($args[0], $this->$name)) {
                $r = $this->$name;
                $result = $r[$args[0]];
            } else {
                $result = null;
            }
        } else {
            $result = $this->$name;
        }

        return $result;
    }

    /**
     * This method is more like a placeholder for functionality
     * which is to be added in later versions.
     * This method instantiates the Http\Factory and calls all
     * observers using 'http_before' and 'http_after'
     * methods, respectively before and after the factory is instantiated.
     *
     * @deprecated This method is obsolete since, v1.1.0 and will be removed in 1.5
     * @return \Wave\Application\Loader Current instance for chaining
     */
    public function bootstrap()
    {
        return $this;
    }

    /*****************************
     * Slim specific code follows *
     *****************************/
    
    
    /**
     * This method creates a route for GET requests with a pattern, which
     * when is matched will call the $callback.
     * In addition to the callables, $callback could also be
     * a string containing '\Full\Class\Name:method'. This is
     * as of the time of writing (10/06/2014) the specific
     * controller/action syntax for Slim Framework,see documentation
     * for information.
     *
     * @param string $pattern
     *            Pattern for the route (See Docs)
     * @param mixed $callback
     *            Callback to fire when the pattern is matched
     * @return \Wave\Route
     */
    public function get()
    {
        $args = func_get_args();
        
        return $this->mapRoute($args)->via('GET', 'HEAD');
    }

    /**
     * This method creates the route for POST requests
     *
     * @see \Wave\Application\Loader::get()
     * @param string $pattern
     *            Pattern for the route (See Docs)
     * @param mixed $callback
     *            Callback to fire when the pattern is matched
     * @return \Wave\Route
     */
    public function post()
    {
        $args = func_get_args();
        
        return $this->mapRoute($args)->via('POST');
    }

    /**
     * This method creates the route for PUT requests
     *
     * @see \Wave\Application\Loader::get()
     * @param string $pattern
     *            Pattern for the route (See Docs)
     * @param mixed $callback
     *            Callback to fire when the pattern is matched
     * @return \Wave\Route
     */
    public function put()
    {
        $args = func_get_args();
        
        return $this->mapRoute($args)->via('PUT');
    }

    /**
     * This method creates the route for DELETE requests
     *
     * @see \Wave\Application\Loader::get()
     * @param string $pattern
     *            Pattern for the route (See Docs)
     * @param mixed $callback
     *            Callback to fire when the pattern is matched
     * @return \Wave\Route
     */
    public function delete()
    {
        $args = func_get_args();
        
        return $this->mapRoute($args)->via('DELETE');
    }

    /**
     * This method creates the route for TRACE requests
     *
     * @see \Wave\Application\Loader::get()
     * @param string $pattern
     *            Pattern for the route (See Docs)
     * @param mixed $callback
     *            Callback to fire when the pattern is matched
     * @return \Wave\Route
     */
    public function trace()
    {
        $args = func_get_args();
        
        return $this->mapRoute($args)->via('TRACE');
    }

    /**
     * This method creates the route for CONNECT requests
     *
     * @see \Wave\Application\Loader::get()
     * @param string $pattern
     *            Pattern for the route (See Docs)
     * @param mixed $callback
     *            Callback to fire when the pattern is matched
     * @return \Wave\Route
     */
    public function connect()
    {
        $args = func_get_args();
        
        return $this->mapRoute($args)->via('CONNECT');
    }

    /**
     * This method creates the route for OPTIONS requests
     *
     * @see \Wave\Application\Loader::get()
     * @param string $pattern
     *            Pattern for the route (See Docs)
     * @param mixed $callback
     *            Callback to fire when the pattern is matched
     * @return \Wave\Route
     */
    public function options()
    {
        $args = func_get_args();
        
        return $this->mapRoute($args)->via('OPTIONS');
    }

    /**
     * This method creates the route for OPTIONS requests
     *
     * @see \Wave\Application\Loader::mapRoute()
     * @param string $pattern
     *            Pattern for the route (See Docs)
     * @param mixed $callback
     *            Callback to fire when the pattern is matched
     * @return \Wave\Route
     */
    public function map()
    {
        $args = func_get_args();

        return $this->mapRoute($args);
    }

    /**
     * Creates the route
     *
     * @param array $args
     *            Arguments for the route
     * @return \Wave\Route
     */
    protected function mapRoute($args)
    {
        $pattern = array_shift($args);
        $callable = array_pop($args);
        $route = new Route($pattern, $callable, $this->config['routes.case_sensitive']);
        $this->controller->map($route);
        
        return $route;
    }

    /*********************************
     * Modified Slim Framework Code  *
     *********************************/
    /**
     * This method starts the actual user-land part of the code,
     * it iterates over the registered routes, if none are found it
     * directly return a 404 to the user, except cases where the headers
     * are already sent.
     *
     * Notifys observers using 'map_before' and 'map_after', respectively in the mapping phase.
     * Notifys observers using 'dispatch_before' and 'dispatch_after', respectively in the routing phase.
     * Notifys observers using 'application_after' once the mapping has finished.
     *
     * @throws \Exception
     */
    public function run()
    {
        $this->state('mapBefore')
            ->notify($this->environement);
        
        try {
            $dispatched = false;
            $matchedRoutes = $this->controller
                ->getMatchedRoutes($this->environement['request.method'], $this->environement['request.uri']);
            
            foreach ($matchedRoutes as $route) {
                try {
                    $this->state('dispatchBefore')->notify();
                    
                    $dispatched = $route->dispatch();
                    
                    $this->state('dispatchAfter')->notify();
                    
                    if ($dispatched) {
                        break;
                    }
                } catch (Application\State\Pass $e) {
                    continue;
                } catch (Application\State\Halt $e) {
                    break;
                }
            }
            if (!$dispatched) {
                $this->http->response()
                    ->notFound()
                    ->send();
            }
        } catch (\Exception $e) {
            if ($this->config['debug']) {
                throw $e;
            }
        }
        
        $this->state('mapAfter')->notify($this->environement);
        
        $this->state('applicationAfter')->notify($this->environement);
    }
}
