<?php
if (is_readable('../model/model.php')) {
	require '../model/model.php';
} else {
	header('location: /errordocs/500.php');
}
$allPages = selectPages();
foreach ($allPages as $page) {
	$array[] = array('id' => $page['pageID'], 
		'active' => $page['pageActive'], 
		'title' => $page['pageTitle'],
		'desc' => $page['pageDesc'],
		'content' => $page['pageContent'],
		'class' => $page['pageClass'],
		'link' => $page['pageLink'],
		'button' => $page['pageButton'],
		'activeChecked' => '');
}
$hint = array();
$show = FALSE;
if (isset($_REQUEST["q"])) {
	$q = $_REQUEST["q"];
} else {
	$q = '';
}
if (isset($_REQUEST["a"])) {
	foreach($array as $page) {
		if ($page['active'] == '1') {
			$page['activeChecked'] = 'checked';
		}
		$hint[] = $page;
		$show = TRUE;
	}
}
if ($q !== '') {
	$q = strtolower($q); 
	$len = strlen($q);
	foreach($array as $page) {
		if (stristr($q, substr($page['title'], 0, $len))) {
			if ($page['active'] == '1') {
				$page['activeChecked'] = 'checked';
			}
			$hint[] = $page;
			$show = TRUE;
		}
	}
}
$table = '<table class="pure-table pure-table-horizontal pageTable">
<thead>
	<tr>
		<th title="Title Column - Required">Title *</th>
		<th title="Description Column - Required">Description *</th>
		<th title="Content Column - Required">Content *</th>
		<th title="Navigation Icon Class Column - Required">NAV Icon *</th>
		<th title="Button Link Column">Button Link</th>
		<th title="Button Text Column">Button Text</th>
		<th title="Page Active Column">Active</th>
		<th title="Save Change Column">Save Changes</th>
	</tr>
</thead>

<tbody>';
	foreach ($hint as $page) {
		$table .= '
		<tr>
			<form class="pure-form pure-form-aligned" action="." method="post">
				<td title="Page Title"><input required="required" id="title" placeholder="' . $page['title'] . '" value="' . $page['title'] . '" name="title" /><span class="form_hint">Proper format "http://someaddress.com"</span></td>
				<td title="Page Description"><input required="required" id="desc" placeholder="' . $page['desc'] . '" value="' . $page['desc'] . '" name="desc" /></td>
				<td title="Page Content"><input required="required" id="content" placeholder="' . $page['content'] . '" value="' . $page['content'] . '" name="content" /></td>
				<td title="Navigation Icon Class"><input required="required" id="class" placeholder="' . $page['class'] . '" value="' . $page['class'] . '" name="class" /></td>
				<td title="Button Link"><input id="link" placeholder="' . $page['link'] . '" value="' . $page['link'] . '" name="link" /></td>
				<td title="Button Text"><input id="button" placeholder="' . $page['button'] . '" value="' . $page['button'] . '" name="button"></td>
				<td title="Active page toggle">
					<input ' . $page['activeChecked'] . ' type="checkbox" name="active" value="1" />
				</td>
				<td title="Save Change">
					<input type="hidden" name="pageID" value="' . $page['id'] .'">
					<button title="Save Settings" type="submit" name="action" value="pageAdmin" class="pure-button outline-inverse"><span class="entryIcon ion-ios7-locked"></span>Save</button>
				</form>
			</td>
		</tr>';
	}
	$table .= '
		<tr>
			<form class="pure-form pure-form-aligned" action="." method="post">
					<td title="Page Title"><input type="text" required="required" id="title" placeholder="Title" name="title" /></td>
					<td title="Page Description"><input type="text" required="required" id="desc" placeholder="Description" name="desc" /></td>
					<td title="Page Content"><input type="text" required="required" id="content" placeholder="Content" name="content" /></td>
					<td title="Navigation Icon Class"><input type="text" required="required" id="class" placeholder="Icon Class" name="class" /></td>
					<td title="Button Link"><input type="text" id="link" placeholder="Button Link" name="link" /></td>
					<td title="Button Text"><input type="text" id="button" placeholder="Button Text" name="button" /></td>
					<td title="Active page toggle">
						<input type="checkbox" name="active" value="1" />
					</td>
					<td title="Save Change">
					<button title="Save Settings" type="submit" name="action" value="newPage" class="pure-button outline-inverse"><span class="entryIcon ion-plus"></span>New</button>
				</form>
			</td>
		</tr>';
	$table .= '</tbody>
</table>';
$table .= '<h3 class="settingsHeading">* Indicates required field</h3>
<h3 class="settingsHeading">Navigation <span class="name">Link</span> and <span class="name">Title</span> are the first word of the <span class="name">Page Title</span>.</h3>
<h3 class="settingsHeading">To add an icon, add the desired class from <a target="_blank" href="http://ionicons.com" title="Ionicons">Ionicons</a>.</h3>';
if (!$show) {
	echo '<h3 class="settingsHeading">No <span class="name">Results</span>.</h3>';
} else {
	echo $table;
}
?>