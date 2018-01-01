<?php
	header('Access-Control-Allow-Origin: *');
	require 'db.php';
	require 'roasters.php';

	index($conn);

	function index($conn) {
		$roasters = getRoasters($conn);
		$res = [];

		foreach ($roasters as $key => $value) {
			$id = $key;
			$query = "SELECT * FROM orders WHERE roaster_id = $id";
			$orders = $conn->query($query);
			$res[$value] = $orders->num_rows;
		}

		print json_encode($res);
	}
?>