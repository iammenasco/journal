<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="Nobody cares about this crap.">
	<title>I am Menasco. | Entries </title>
	<link rel="stylesheet" href="css/pure-main.css">
	<link rel="stylesheet" href="css/entry.css">
	<link rel="stylesheet" href="css/side-menu.css">
	<link rel="stylesheet" href="css/custom.css">
</head>
<body>
	<div id="layout" class="content">
	<?php echo $nav; ?>
	<?php 
	switch ($view) {
		case 'signIn':
			echo $signIn;
			break;
		case 'list':
			echo $body;
			break;
		case 'signUp':
			echo $signUp;
			break;
		default:
			echo $home;
			break;
	}
	?>
	</div>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
	<script src="js/ui.js"></script>
	<script src="js/js.js"></script>
</body>
</html>