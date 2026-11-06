<?php
include('include/db.php');
header('Content-Type: application/json; charset=utf-8');

$query = array_key_exists('q', $_GET) && is_string($_GET['q']) ? trim($_GET['q']) : '';
if($query === '') {
	echo json_encode(array());
	exit;
}

$sql = 'SELECT `tag` FROM '.db('tags').' WHERE LOCATE(?, `tag`) > 0 ORDER BY `tag` LIMIT 20';
$stmt = $mysqli->prepare($sql);
if(!$stmt) {
	http_response_code(500);
	error_log('Tag suggestions query preparation failed: '.$mysqli->error);
	echo json_encode(array('error' => 'Unable to retrieve tag suggestions'));
	exit;
}
if(!$stmt->bind_param('s', $query) || !$stmt->execute() || !$stmt->bind_result($tag)) {
	http_response_code(500);
	error_log('Tag suggestions query failed: '.$stmt->error);
	echo json_encode(array('error' => 'Unable to retrieve tag suggestions'));
	exit;
}

$suggestions = array();
while($stmt->fetch()) {
	$suggestions[] = $tag;
}
$stmt->close();

echo json_encode($suggestions);
?>
