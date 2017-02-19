<?php

/**
 * (c) Benjamin Michalski <benjamin.michalski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Benjamin Michalski <benjamin.michalski@gmail.com>
 */

function vd() {
    call_user_func_array('var_dump', func_get_args());
}

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

    $defaultConnection = $_ENV['arlekin_dbal_driver_pdo_mysql_test_parameters']['dbal']['connections']['pdo_mysql_test'];


    $rootParameters = &$_ENV['arlekin_dbal_driver_pdo_mysql_test_parameters']['dbal']['connections']['root'];

    $dsn = sprintf(
        "mysql:host=%s;port=%s",
        $rootParameters['host'],
        $rootParameters['port'],
        [
            PDO::ATTR_TIMEOUT => 2,
        ]
    );

    $pdo = new PDO(
        $dsn,
        $rootParameters['user'],
        $rootParameters['password'],
        [
            PDO::ATTR_TIMEOUT => 2,
        ]
    );

    $pdo->exec("CREATE DATABASE {$defaultConnection['database']};");

    $pdo->exec("CREATE USER '{$defaultConnection['user']}'@'%' IDENTIFIED BY '{$defaultConnection['password']}';");

    $pdo->exec("GRANT ALL ON `{$defaultConnection['database'] }`.* TO '{$defaultConnection['user']}'@'%';");

    $pdo->exec("FLUSH PRIVILEGES;");
}
