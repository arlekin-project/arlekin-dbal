<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Benjamin Michalski <benjamin.michalski@gmail.com>
 */

if (!isset($GLOBALS['arlekin_bootstrap_loaded'])
    || !$GLOBALS['arlekin_bootstrap_loaded']) {
    $currentDirectory = __DIR__;

    $GLOBALS['arlekin_bootstrap_loaded'] = true;

    error_reporting(E_ALL | E_STRICT);

    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');

    $autoloadFilePhp = sprintf(
        '%s/../vendor/autoload.php',
        $currentDirectory
    );

    require $autoloadFilePhp;
    
    $testParametersFilePathName = __DIR__ . '/testParameters.php';

    if (file_exists($testParametersFilePathName)) {
        require $testParametersFilePathName;
    } else {
        require __DIR__ . '/testParameters.dist.php';
    }
    
    $_ENV['arlekin_dbal_driver_pdo_mysql_test_parameters']['dbal']['connections']['root'] = $_ENV['arlekin_dbal_driver_pdo_mysql_test_parameters']['dbal']['connections'][0];
    
    $rootParameters = &$_ENV['arlekin_dbal_driver_pdo_mysql_test_parameters']['dbal']['connections']['root'];

    $rootParameters['user'] = 'root';
    $rootParameters['password'] = null;
    $rootParameters['database'] = null;
    $rootParameters['name'] = 'root';

    $dsn = sprintf(
        "mysql:host=%s;port=%s",
        $rootParameters['host'],
        $rootParameters['port']
    );

    $pdo = new PDO(
        $dsn,
        $rootParameters['user'],
        $rootParameters['password']
    );

    $pdo->exec("CREATE USER 'arlekin'@'%' IDENTIFIED BY 'arlekin';");
    $pdo->exec("GRANT ALL ON `arlekin`.* TO 'arlekin'@'%';");
    $pdo->exec("FLUSH PRIVILEGES;");
}
