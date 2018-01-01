<?php
	header('Access-Control-Allow-Origin: *');
	require 'db.php';
	require 'pointer.php';

	index($conn);

	function index($conn) {
		$query = "SELECT id from subscriptions ORDER BY id DESC LIMIT 1";
		$last = $conn->query($query);
		$last = $last->fetch_assoc()['id'];
		$last++;
		$name = 'S' . $last;

		$query = "INSERT INTO subscriptions	(name) VALUES ('$name')";
		if ($conn->query($query)) {
			
			$ptr1 = getFirstPointer($conn);
			$pointing_to = $ptr1['pointing_to'];
			
			$roasters = getRoasters($conn);
			
			$query = "SELECT * from subscriptions ORDER BY id DESC LIMIT 1";
			$subscription = $conn->query($query);
			$subscription = $subscription->fetch_assoc();
			$sid = $subscription['id'];

			$selected = '';

			while($roaster = $roasters->fetch_assoc()) {
				$id = $roaster['id'];
				$cnt = 0;
				if( $id == $pointing_to )
				{
					$query = "INSERT INTO orders (subscription_id, roaster_id) VALUES ($sid, $id)";
					if (!$conn->query($query))
						echo $conn->error;
					$selected = $roaster['name'];
					$cnt++;
				}
			
				$query = "INSERT INTO roaster_subscription_orders (subscription_id, roaster_id, count) VALUES ($sid, $id, $cnt)";
				if (!$conn->query($query))
					echo $conn->error;
			}

			updatePointer($conn, $ptr1);

			$detail = ['name' => $subscription['name'], 'roasters' => $selected];
			$res = ['id' => $sid, 'detail' => $detail];

			print json_encode($res);
		}
		else
			echo $conn->error;
	}

	function getRoasters($conn) {
		$query = "SELECT * FROM roasters";
		$roasters = $conn->query($query);
		return $roasters;
	}
?>