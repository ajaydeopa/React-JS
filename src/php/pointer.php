<?php
	function updatePointer($conn, $ptr) {
		$pointing_to = $ptr['pointing_to'];
		$id = $ptr['id'];

		$query = "SELECT id FROM roasters WHERE id > $pointing_to LIMIT 1";
		$next = $conn->query($query);

		if( !$next->num_rows ) {
			$query = "SELECT id FROM roasters LIMIT 1";
			$next = $conn->query($query);
		}

		$next = $next->fetch_assoc()['id'];
		$query = "UPDATE pointers SET pointing_to = $next WHERE id = $id";
		if( !$conn->query($query) )
			echo $conn->error;
	}

	function getFirstPointer($conn) {
		$query = "SELECT * FROM pointers LIMIT 1";
		$ptrs = $conn->query($query);
		return $ptrs->fetch_assoc();
	}

	function getSecondPointer($conn) {
		$query = "SELECT * FROM pointers ORDER BY id DESC LIMIT 1";
		$ptrs = $conn->query($query);
		return $ptrs->fetch_assoc();
	}
?>