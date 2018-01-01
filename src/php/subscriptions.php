<?php
	header('Access-Control-Allow-Origin: *');
	require 'db.php';
	require 'roasters.php';

	index($conn);

	function index($conn) {
		$query = "SELECT * FROM subscriptions";
		$subs = $conn->query($query);
		$res = [];
		$roasters = getRoasters($conn);

		while($sub = $subs->fetch_assoc())
		{
			$id = $sub['id'];
			$detail = ['name' => $sub['name'], 'roasters' => getSubscriptionOrderRoaster($conn, $roasters, $id)];
			$res[$id] = $detail;
		}

		print json_encode($res);
	}

	function getSubscriptionOrderRoaster($conn, $roasters, $id) {
		$res = '';
		$ch = false;
		$query = "SELECT roaster_id FROM orders WHERE subscription_id = $id";
		$orders = $conn->query($query);
		while($order = $orders->fetch_assoc()) {
			if($ch)
				$res = $res . ', ';
			$rid = $order['roaster_id'];
			$res = $res . $roasters[$rid];
			$ch = true;
		}

		return $res;
	}
?>