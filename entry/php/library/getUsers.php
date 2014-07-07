<?php
if (is_readable('../model/model.php')) {
	require '../model/model.php';
} else {
	header('location: /errordocs/500.php');
}
$allUsers = selectNames();
foreach ($allUsers as $user) {
	$array[] = array('email' => $user['userEmail'], 
		'firstName' => $user['userFirstName'], 
		'lastName' => $user['userLastName'],
		'userID' => $user['userID'],
		'admin' => $user['userAdmin'],
		'active' => $user['userActive'],
		'adminChecked' => '', 'activeChecked' => '');
}
$hint = array();
$show = FALSE;
if (isset($_REQUEST["q"])) {
	$q = $_REQUEST["q"];
} else {
	$q = '';
}
if (isset($_REQUEST["a"])) {
	foreach($array as $user) {
		if ($user['admin'] == '1') {
			$user['adminChecked'] = 'checked';
		}
		if ($user['active'] == '1') {
			$user['activeChecked'] = 'checked';
		}
		$hint[] = $user;
		$show = TRUE;
	}
}
if ($q !== '') {
	$q = strtolower($q); 
	$len = strlen($q);
	foreach($array as $user) {
		if (stristr($q, substr($user['firstName'], 0, $len)) ||
			stristr($q, substr($user['lastName'], 0, $len)) ||
			stristr($q, substr($user['email'], 0, $len))) {
			if ($user['admin'] == '1') {
				$user['adminChecked'] = 'checked';
			}
			if ($user['active'] == '1') {
				$user['activeChecked'] = 'checked';
			}
			$hint[] = $user;
			$show = TRUE;
		}
	}
}
$table = '<table class="pure-table pure-table-horizontal userTable">
<thead>
	<tr>
		<th title="First Name Column">First</th>
		<th title="Last Name Column">Last</th>
		<th title="User Email Address Column">Email</th>
		<th title="User Identification Column">User ID</th>
		<th title="User Administration Privileges Column">Admin</th>
		<th title="Current user toggle Column">Active</th>
		<th title="Save Change Column">Save Changes</th>
	</tr>
</thead>

<tbody>';
	foreach ($hint as $user) {
		$table .= '
		<tr>
			<form class="pure-form pure-form-aligned" action="." method="post">
					<td title="First Name"><input required="required" type="text" id="firstName" placeholder="' . $user['firstName'] . '" value="' . $user['firstName'] . '" name="firstName"></td>
					<td title="Last Name"><input required="required" type="text" id="lastName" placeholder="' . $user['lastName'] . '" value="' . $user['lastName'] . '" name="lastName"></td>
					<td title="User Email"><input required="required" type="text" id="email" placeholder="' . $user['email'] . '" value="' . $user['email'] . '" name="email"></td>
					<td title="User Identification">' . $user['userID'] . '<input type="hidden" name="userID" value="' . $user['userID'] .'"></td>
					<td title="User Administration Privileges">
						<input ' . $user['adminChecked'] . ' type="checkbox" name="admin" value="1">
					</td>
					<td title="Current user toggle">
						<input ' . $user['activeChecked'] . ' type="checkbox" name="active" value="1">
					</td>
					<td title="Save Change">
					<button title="Save Settings" type="submit" name="action" value="userAdmin" class="pure-button outline-inverse"><span class="entryIcon ion-ios7-locked"></span>Save</button>
				</form>
			</td>
		</tr>';
	}
	$table .= '</tbody>
</table>';
if (!$show) {
	echo '<h3 class="settingsHeading">No <span class="name">Results</span>.</h3>';
} else {
	echo $table;
}
?>