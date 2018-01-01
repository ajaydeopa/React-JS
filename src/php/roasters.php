<?php
	function getRoasters($conn) {
		$query = "SELECT * FROM roasters";
		$roasters = $conn->query($query);
		$res = [];

		while($roaster = $roasters->fetch_assoc()) {
			$res[$roaster['id']] = $roaster['name'];
		}

		return $res;
	}

	function getRoasterOrderCount($conn, $id) {
		$query = "SELECT roaster_id, count FROM roaster_subscription_orders WHERE subscription_id = $id";
		$count = $conn->query($query);
		$res = [];

		while($cnt = $count->fetch_assoc()) {
			$res[$cnt['roaster_id']] = $cnt['count'];
		}

		return $res;
	}
?>