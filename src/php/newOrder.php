<?php
	header('Access-Control-Allow-Origin: *');
	require 'db.php';
	require 'roasters.php';
	require 'pointer.php';

	index($conn, $_POST['id']);

	function index($conn, $id) {
		$roasters = getRoasters($conn);
		$order_count = getRoasterOrderCount($conn, $id);
		$max_assigned_orders = 0;
		$roasters_count = 0;

		foreach ($order_count as $key => $value) {
			if( $value > $max_assigned_orders ) {
				$max_assigned_orders = $value;
				$roasters_count = 1;
			}
			else if( $value === $max_assigned_orders )
				$roasters_count++;
		}

		if( $roasters_count === sizeof($roasters) )
			$max_assigned_orders++;

		$before = true;
		$after = false;
		$selected = '';

		$ptr2 = getSecondPointer($conn);
		$pointing_to = $ptr2['pointing_to'];
		$pid = $ptr2['id'];

		foreach ($order_count as $key => $value) {
			if( $key == $pointing_to )
				$after = true;

			if(!$after) {
				if( $before && $value < $max_assigned_orders )
				{
					$selected = $key;
					$before = false;
				}
			}
			else if( $value < $max_assigned_orders )
			{
				$selected = $key;
				if( $key == $pointing_to )
					updatePointer($conn, $ptr2);
				break;
			}
		}

		$query = "INSERT INTO orders (subscription_id, roaster_id) VALUES ($id, $selected)";
		if( $conn->query($query) ) {
			$cnt = $order_count[$selected] + 1;
			$query = "UPDATE roaster_subscription_orders SET count = $cnt WHERE roaster_id = $selected AND subscription_id = $id";
			if( $conn->query($query) ) {
				$query = "SELECT name FROM roasters WHERE id = $selected";
				$roaster = $conn->query($query);
				$roaster = $roaster->fetch_assoc();
				print $roaster['name'];
			}
			else
				echo $conn->error;
		}
		else
			echo $conn->error;
	}
?>