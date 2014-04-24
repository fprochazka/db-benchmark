<?php

use Model\Mapper;
use Model\Repository\EmployeesRepository;
use LeanMapper\Connection;
use LeanMapper\DefaultEntityFactory;

if (@!include __DIR__ . '/vendor/autoload.php') {
    echo 'Install Lean Mapper using `composer install`';
    exit(1);
}

require_once __DIR__ . '/Model/Mapper.php';
require_once __DIR__ . '/Model/Entity/Employee.php';
require_once __DIR__ . '/Model/Entity/Salary.php';
require_once __DIR__ . '/Model/Entity/Department.php';
require_once __DIR__ . '/Model/Repository/EmployeesRepository.php';

date_default_timezone_set('Europe/Prague');

$limit = 500;

$connection = new Connection(array(
	'username' => 'root',
	'password' => '',
	'database' => 'employees',
	'host' => '127.0.0.1',
));


$time = -microtime(TRUE);
ob_start();

$entityFactory = new DefaultEntityFactory;
$mapper = new Mapper();
$employeesRepository = new EmployeesRepository($connection, $mapper, $entityFactory);

foreach ($employeesRepository->findAll($limit) as $employee) {
	echo "$employee->firstName $employee->lastName ($employee->empNo)\n";
	echo "Salaries:\n";
	foreach ($employee->salaries as $salary) {
		echo $salary->salary, "\n";
	}
	echo "Departments:\n";
	foreach ($employee->departments as $department) {
		echo $department->deptName, "\n";
	}
}

ob_end_clean();

echo 'Time: ', sprintf('%0.3f', $time + microtime(TRUE)), ' s | ',
	'Memory: ', (memory_get_peak_usage() >> 20), ' MB | ',
	'PHP: ', PHP_VERSION;
