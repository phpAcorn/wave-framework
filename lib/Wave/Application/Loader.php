<?php
namespace Wave\Application;

use Wave\Http\Factory;
use Wave\Pattern\Observer\Subject;

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
    public $environement = null;

    /**
     * Container for the Http\Factory object
     * 
     * @var \Wave\Http\Factory
     */
    public $http = null;

    /**
     * Dependency injection container
     * 
     * @var \Di\Container
     */
    public $container = null;

    /**
     * Container for the Controller
     * 
     * @var \Wave\Application\Controller
     */
    protected $controller = null;
    
    /**
     * Container for the EventDispatcher
     * 
     * @var Syfony\Component\EventDispatcher\EventDispatcher
     */
    public $event = null;

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
                'request' => '\Wave\Http\Request',
                'response' => '\Wave\Http\Response',
                'environment' => '\Wave\Application\Environment',
                'http' => '\Wave\Http\Factory',
                'controller' => '\Wave\Application\Controller',
                'di' => '\DI\ContainerBuilder',
                'event' => '\Symfony\Component\EventDispatcher\EventDispatcher'
            ),
            'environment' => array(
                'request.protocol' => (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.1'),
                'request.port' => (isset($_SERVER['SERVER_PORT']) ? $_SERVER['SERVER_PORT'] : 80),
                'request.uri' => (isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/'),
                'request.method' => (isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'GET')
            ),
            'routes.case_sensitive' => false
        );
        
        $this->config = array_merge($this->defaultConfig, $config);
        
        
        $env_handler = $this->config['handlers']['environment'];
        $ctrl_handler = $this->config['handlers']['controller'];
        $di_container = $this->config['handlers']['di'];
        $event_container = $this->config['handlers']['event'];
        
        $this->controller = new $ctrl_handler();
        $this->environement = new $env_handler($this->config['environment']);
        
        if (class_exists($di_container, true)) {
            $this->container = new $di_container();
        }
        
        if (class_exists($event_container, true)) {
            $this->event = new $event_container();
        }
    }

    /**
     * This method is more like a placeholder for functionality
     * which is to be added in later versions.
     * This method instantiates the Http\Factory and calls all
     * observers using 'http_before' and 'http_after'
     * methods, respectively before and after the factory is instantiated.
     *
     * @return \Wave\Application\Loader Current instance for chaining
     */
    public function bootstrap()
    {
        
        
        $this->state('httpBefore')->notify($this->environement);
        
        $this->http = new $this->config['handlers']['http'](
            $this->config['handlers']['request'],
            $this->config['handlers']['response'],
            $this->environement
        );
        
        $this->state('httpAfter')->notify($this->environement);
        
        return $this;
    }

    /*****************************
     * Slim spcific code follows *
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
        $route = new \Wave\Route($pattern, $callable, $this->config['routes.case_sensitive']);
        $this->controller->map($route);
        
        return $route;
    }

    /*********************************
     * Modified Slim Framework Code  *
     *********************************/
    /**
     * This mehod starts the actual user-land part of the code,
     * it iterates over the registered routes, if none are found it
     * directly return a 404 to the user, except cases where the headers
     * are already sent.
     *
     * Notifys observers using 'map_before' and 'map_after', respectively in the mapping phase.
     * Notifys observers using 'dispatch_before' and 'dispatch_after', respectively in the routing phase.
     * Notifys observers using 'application_after' once the mapping has finished.
     *
     * @throws Exception
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
                } catch (\Wave\Application\State\Pass $e) {
                    continue;
                } catch (\Wave\Application\State\Halt $e) {
                    $e = new \Wave\Event();
                    $e->request = $this->http->request();
                    $e->response = $this->http->response();
                    
                    
                    if ($this->event->hasListeners('application.halt')) {
                        // @codeCoverageIgnoreStart
                        $this->event->dispatch('application.halt');
                        // @codeCoverageIgnoreEnd
                    }
                    
                    
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
