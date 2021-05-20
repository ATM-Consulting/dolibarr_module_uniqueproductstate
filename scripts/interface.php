<?php

if (!defined('NOTOKENRENEWAL')) define('NOTOKENRENEWAL', 1); // Disables token renewal
if (!defined('NOREQUIREMENU'))  define('NOREQUIREMENU', '1');
if (!defined('NOREQUIREHTML'))  define('NOREQUIREHTML', '1');
if (!defined('NOREQUIREAJAX'))  define('NOREQUIREAJAX', '1');
if (!defined('NOREQUIRESOC'))   define('NOREQUIRESOC', '1');
if (!defined('NOCSRFCHECK'))    define('NOCSRFCHECK', '1');
if (empty($_GET['keysearch']) && !defined('NOREQUIREHTML')) define('NOREQUIREHTML', '1');

$dir = '';
$cpt = 0;
while (!is_file($dir.'main.inc.php') && $cpt < 4)
{
	$dir.='../';
	$cpt++;
}
require $dir.'main.inc.php';

dol_include_once('/uniqueproductstate/class/uniqueproductstateline.class.php');

$put = GETPOST('put', 'none');
$get = GETPOST('get', 'none');

$Tlineid = GETPOST('Tlineid', 'array');
$value = GETPOST('value', 'int');

$response = array(
	'msg' => "nothing happen",
	'error' => ""
);

switch ($get)
{

	default:
		break;

}

switch ($put)
{
	case 'changeLineState':
		$response = changeLineState($value, $Tlineid);
		break;

	default:
		break;

}

print json_encode($response);


function changeLineState($value, $Tlineid)
{
	global $db, $user;
	$errors = $ok = array();

	$response = array(
		'msg'=> ''
		,'ok' => $ok
		,'error' => $errors
	);

	if (empty($Tlineid))
	{
		$response['error'] = 'no line to update';
		return $response;
	}

	foreach ($Tlineid as $lineid)
	{
		$line = new UniqueProductStateline($db);
		$res = $line->fetch($lineid);
		if ($res > 0)
		{
			$line->fk_noticed_state = $value;
			$resupd = $line->update($user);
			if ($resupd > 0) $ok[$lineid] = "line $lineid updated";
			else $errors[$lineid] = "line $lineid : error during update";
		}
		else $errors[$lineid] = "can't get line $lineid";
	}

	$response['ok'] = $ok;
	$response['error'] = $errors;
	if (!empty($ok)) $response['msg'].= count($ok)." lines updated <br>";

	if (!empty($errors)) $response['msg'].= count($errors)." lines in error <br>";

	return $response;
}
