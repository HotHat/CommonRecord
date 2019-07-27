class Route {
    private $middleware = [];
    private $namespace = '';
    private $uri = '';
    private $action;
    private $method = [];
    public function __construct($uri, $action, $middleware = [])
    {
        $this->uri = $uri;
        $this->action = $action;
        $this->middleware = $middleware;
    }
    
    public function addMethod($method) {
        $this->method[] = $method;
    }
    
    public function addPrefix($prefix) {
        
        $this->uri = $prefix . $this->uri;
    }
    
    public function  addMiddleware($middleware) {
        
        $this->middleware = array_merge($middleware, $this->middleware);
    }
    
    public function setNamespace($namespace) {
        $this->namespace = $namespace;
    }
}

class Http {
    /**
     * @var Array
     */
    private $route;
    public function __construct(Route ...$route)
    {
        $this->route = $route;
    }
    
    public function get() {
        return $this->route;
    }
}

class Scope {
    private $middleware = [];
    private $namespace = '';
    private $prefix = '';
    public function __construct($namespace, $middleware, $prefix)
    {
       $this->middleware = $middleware;
       $this->namespace  = $namespace;
       $this->prefix     = $prefix;
    }
    
    public function merge(Http ...$http) {
        $result = merge(...$http);
        
        $routes = $result->get();
        
        foreach ($routes as $route) {
            $route->addMiddleware($this->middleware);
            $route->addPrefix($this->prefix);
            $route->setNamespace($this->namespace);
        }
        
        return new Http(...$routes);
    }
}

function scope($namespace, $middleware, $prefix) {
    return new Scope($namespace, $middleware, $prefix);
}

function route($url, $action) {
    $route = new Route($url, $action);
    return new Http($route);
}

function get(Http $http) : Http {
    
    $route = $http->get();
    
    foreach ($route as $item) {
        $item->addMethod('GET');
    }
    
    return $http;
}

function post(Http $http) : Http {
    $routes = $http->get();
    
    foreach ($routes as $route) {
        $route->addMethod('POST');
    }
    
    return $http;
}



function merge(...$http) {
    $routes = [];
    foreach ($http as $item) {
        $routes = array_merge($routes, $item->get());
    }
    
    return new Http(...$routes);
}

$http1 = route('index', 'action');
$http2 = get(route('edit', 'action'));
$http3 = post(get(route('save', 'action')));

$http = merge($http1, $http2, $http3);
var_dump($http);

$hp = scope('App\\Controller', ['mw1', 'mw2'], 'api/')
    ->merge(
        post(get(route('detail', 'action'))),
        post(get(route('update', 'action'))),
        $http
    );
    

var_dump($hp);


// dispatch http request
