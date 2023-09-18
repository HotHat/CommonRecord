<?php
require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;
use Workerman\Timer;
use Workerman\Worker;
use Workerman\Connection\TcpConnection;
use Dotenv\Dotenv;
use Illuminate\Database\Capsule\Manager as DB;


ini_set('display_errors', 'on');
error_reporting(E_ALL);

// 加载.env配制文件
Dotenv::createUnsafeImmutable('./')->load();


date_default_timezone_set('Asia/Shanghai');

Worker::$onMasterReload = function (){
    if (function_exists('opcache_get_status')) {
        if ($status = opcache_get_status()) {
            if (isset($status['scripts']) && $scripts = $status['scripts']) {
                foreach (array_keys($scripts) as $file) {
                    opcache_invalidate($file, true);
                }
            }
        }
    }
};

$storagePath = worker_storage_path();
$config      = [
    'listen'               => 'http://0.0.0.0:' . ($_ENV['WORKERMAN_PORT'] ?? 9702),
    'transport'            => 'tcp',
    'context'              => [],
    'name'                 => 'lis-worker',
    'count'                => 1,
    'user'                 => ($_ENV['WORKERMAN_USER'] ?? 'www-data'),
    'group'                => ($_ENV['WORKERMAN_GROUP'] ?? 'www-data'),
    'pid_file'             => $storagePath . '/webman.pid',
    'stdout_file'          => $storagePath . '/logs/stdout.log',
    'log_file'             => $storagePath . '/logs/workerman.log',
    'max_request'          => 1000000,
    'max_package_size'     => 10*1024*1024
];

Worker::$pidFile                      = $config['pid_file'];
Worker::$stdoutFile                   = $config['stdout_file'];
Worker::$logFile                      = $config['log_file'];
TcpConnection::$defaultMaxPackageSize = $config['max_package_size'] ?? 10*1024*1024;

$worker      = new Worker($config['listen'], $config['context']);
$propertyMap = [
    'name',
    'count',
    'user',
    'group',
    'reusePort',
    'transport',
];
foreach ($propertyMap as $property) {
    if (isset($config[$property])) {
        $worker->$property = $config[$property];
    }
}

$worker->onWorkerStart = function ($worker) {
    $app = new \App\Lis\IvdImp();
    $worker->protocol = 'App\Lis\IvdProtocol';
    $app->onWorkerStart($worker);

    $worker->onConnect = [$app, 'onConnect'];

    $worker->onMessage = [$app, 'onMessage'];

    $worker->onClose = [$app, 'onClose'];

    $worker->onWorkerStop = [$app, 'onWorkerStop'];
};

// 初始化数据库链接
$capsule = new Capsule;
$configs = [
    // 默认数据库
    'default' => 'mysql',

    // 各种数据库配置
    'connections' => [
        'mysql' => [
            'driver'      => 'mysql',
            'host'        => $_ENV['DB_HOST'],
            'port'        => $_ENV['DB_PORT'],
            'database'    => $_ENV['DB_DATABASE'],
            'username'    => $_ENV['DB_USERNAME'],
            'password'    => $_ENV['DB_PASSWORD'],
            'unix_socket' => '',
            'charset'     => 'utf8',
            'collation'   => 'utf8_unicode_ci',
            'prefix'      => '',
            'strict'      => true,
            'engine'      => null,
        ],
    ],
];

$connections = $configs['connections'];

if (isset($configs['default'])) {
    $default_config = $connections[$configs['default']];
    $capsule->addConnection($default_config);
}

foreach ($connections as $name => $config) {
    $capsule->addConnection($config, $name);
}

$capsule->setAsGlobal();

$capsule->bootEloquent();

Timer::add(55, function () use ($connections) {
    foreach ($connections as $key => $item) {
        if ($item['driver'] == 'mysql') {
            DB::connection($key)->select('select 1');
        }
    }
});

Worker::runAll();
