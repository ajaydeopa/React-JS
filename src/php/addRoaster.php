<?php
	header('Access-Control-Allow-Origin: *');
	require 'db.php';

	$n = $_POST['name'];

	$query = "INSERT INTO roasters (name) VALUES ('$n')";
	if ($conn->query($query)) {
		$query = "SELECT * from roasters ORDER BY id DESC";
		$roaster = $conn->query($query);
		$curr = $roaster->fetch_assoc();
		$cnt = $roaster->num_rows;

		if($cnt < 3) {
			$id = $curr['id'];
			$query = "INSERT INTO pointers (pointing_to) VALUES ($id)";
			if (!$conn->query($query))
				echo $conn->error;
		}

		setRoasterSubscriptionOrders($conn, $curr);

		print json_encode($curr);
	}
	else
		echo $conn->error;


	function setRoasterSubscriptionOrders($conn, $curr)
	{
		$id = $curr['id'];
		$name = $curr['name'];

		$query = "SELECT id FROM subscriptions";
		$subs = $conn->query($query);

		while($sub = $subs->fetch_assoc()) {
			$sid = $sub['id'];
			$cnt = minOrders($conn, $sid);
			$query = "INSERT INTO roaster_subscription_orders (roaster_id, subscription_id, count) VALUES ($id, $sid, $cnt)";
			if( !$conn->query($query) )
				echo $conn->error;
		}
	}

	function minOrders($conn, $id) {
		$query = "SELECT count FROM roaster_subscription_orders WHERE subscription_id = $id";
		$count = $conn->query($query);

		$min = 1000;

		while($cnt = $count->fetch_assoc()) {
			if( $min > $cnt['count'] )
				$min = $cnt['count'];
		}

		return $min;
	}
?>