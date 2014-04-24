<?php


if (@!include __DIR__ . '/vendor/autoload.php') {
    echo 'Install Nette using `composer install`';
    exit(1);
}

$useCache = TRUE;

date_default_timezone_set('Europe/Prague');

$connection = new Nette\Database\Connection(
	'mysql:host=127.0.0.1;dbname=employees;',
	'root',
	''
);

$cacheStorage = new Nette\Caching\Storages\FileStorage(__DIR__ . '/temp');
$connection->setCacheStorage($useCache ? $cacheStorage : NULL);
$connection->setDatabaseReflection(new Nette\Database\Reflection\DiscoveredReflection($useCache ? $cacheStorage : NULL));
$dao = $connection;


$time = -microtime(TRUE);
ob_start();

foreach ($dao->table('employees')->limit(500) as $employe) {
	echo "$employe->first_name $employe->last_name ($employe->emp_no)\n";
	echo "Salaries:\n";
	foreach ($employe->related('salaries') as $salary) {
		echo $salary->salary, "\n";
	}
	echo "Departments:\n";
	foreach ($employe->related('dept_emp') as $department) {
		echo $department->dept->dept_name, "\n";
	}
}

ob_end_clean();

echo 'Time: ', sprintf('%0.3f', $time + microtime(TRUE)), ' s | ',
	'Memory: ', (memory_get_peak_usage() >> 20), ' MB | ',
	'PHP: ', PHP_VERSION, ' | ',
	'Nette: ', Nette\Framework::VERSION;
