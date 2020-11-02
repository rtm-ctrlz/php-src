--TEST--
stream context tcp_keepalive fopen
--SKIPIF--
<?php
if (getenv("SKIP_ONLINE_TESTS")) die("skip online test");
if (!extension_loaded("sockets")) die("skip: need sockets");
 ?>
--FILE--
<?php

echo "undefined\n";
$ctxt = stream_context_create([
	"http" => [
		"follow_location" => 0
	]
]);

$stream = fopen("http://www.php.net", "r", false,  $ctxt);

$socket = @socket_import_stream($stream);

var_dump(socket_get_option($socket, SOL_SOCKET, SO_KEEPALIVE) === 0);

fclose($stream);

echo "enable with default paramenters\n";
$ctxt = stream_context_create([
	"socket" => [
		"tcp_keepalive" => true,
	],
	"http" => [
		"follow_location" => 0
	]
]);

$stream = fopen("http://www.php.net", "r", false,  $ctxt);

$socket = @socket_import_stream($stream);

var_dump(socket_get_option($socket, SOL_SOCKET, SO_KEEPALIVE) > 0);

fclose($stream);

echo "enable with custom paramenters\n";
$ctxt = stream_context_create([
	"socket" => [
		"tcp_keepalive" => true,
		"tcp_keepalive_idle" => 10,
		"tcp_keepalive_interval" => 20,
		"tcp_keepalive_count" => 1,
	],
	"http" => [
		"follow_location" => 0
	]
]);

$stream = fopen("http://www.php.net", "r", false,  $ctxt);

$socket = @socket_import_stream($stream);

var_dump(socket_get_option($socket, SOL_SOCKET, SO_KEEPALIVE) > 0);
var_dump(socket_get_option($socket, SOL_TCP, TCP_KEEPIDLE) === 10);
var_dump(socket_get_option($socket, SOL_TCP, TCP_KEEPINTVL) === 20);
var_dump(socket_get_option($socket, SOL_TCP, TCP_KEEPCNT) === 1);

fclose($stream);

?>
--EXPECT--
undefined
bool(true)
enable with default paramenters
bool(true)
enable with custom paramenters
bool(true)
bool(true)
bool(true)
bool(true)
