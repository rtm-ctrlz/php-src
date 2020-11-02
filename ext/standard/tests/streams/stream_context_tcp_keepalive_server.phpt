--TEST--
stream context keepalive server
--SKIPIF--
<?php if (!extension_loaded("sockets")) die("skip: need sockets") ?>
--FILE--
<?php
$serverCode = <<<'CODE'
   $ctxt = stream_context_create([
		"socket" => [
			"tcp_keepalive" => true,
			"tcp_keepalive_idle" => 31,
			"tcp_keepalive_interval" => 21,
			"tcp_keepalive_count" => 11,
		]
	]);

	$server = stream_socket_server(
		"tcp://127.0.0.1:9099", $errno, $errstr, STREAM_SERVER_BIND | STREAM_SERVER_LISTEN, $ctxt);

	$client = stream_socket_accept($server);

	var_dump(socket_get_option(
				socket_import_stream($server), 
					SOL_SOCKET, SO_KEEPALIVE) > 0);
	var_dump(socket_get_option(
				socket_import_stream($server), 
					SOL_TCP, TCP_KEEPIDLE) === 31);
	var_dump(socket_get_option(
				socket_import_stream($server), 
					SOL_TCP, TCP_KEEPINTVL) === 21);
	var_dump(socket_get_option(
				socket_import_stream($server), 
					SOL_TCP, TCP_KEEPCNT) === 11);

	var_dump(socket_get_option(
				socket_import_stream($client), 
					SOL_SOCKET, SO_KEEPALIVE) > 0);
	var_dump(socket_get_option(
				socket_import_stream($client), 
					SOL_TCP, TCP_KEEPIDLE) === 31);
	var_dump(socket_get_option(
				socket_import_stream($client), 
					SOL_TCP, TCP_KEEPINTVL) === 21);
	var_dump(socket_get_option(
				socket_import_stream($client), 
					SOL_TCP, TCP_KEEPCNT) === 11);

	fclose($client);
	fclose($server);
CODE;

$clientCode = <<<'CODE'
    $test = stream_socket_client(
		"tcp://127.0.0.1:9099", $errno, $errstr, 10);

	sleep(1);

	fclose($test);
CODE;

include sprintf(
	"%s/../../../openssl/tests/ServerClientTestCase.inc",
	__DIR__);
ServerClientTestCase::getInstance()->run($serverCode, $clientCode);
?>
--EXPECT--
bool(false)
bool(false)
bool(false)
bool(false)
bool(true)
bool(true)
bool(true)
bool(true)
