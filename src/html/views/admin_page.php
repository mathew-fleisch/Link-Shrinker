<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Admin</title>
	<link rel="icon" type="image/png" href="/favicon.ico" />
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
	<ul class="nav nav-tabs">
		<li class=""><a href="/" >Home</a></li>
		<li class="active"><a href="/admin">Admin</a></li>
	</ul>
	<div id="container">
		<h1>Admin</h1>
		<div id="errors"></div>
		<div id="info-container"></div>
		<table class="table table-striped table-hover">
			<thead>
				<tr>
					<th>active</th>
					<th>count</th>
					<th>alias</th>
					<th>url</th>
					<th>time</th>
					<th>ip</th>
				</tr>
			</thead>
			<tbody>
				<?php
					foreach($urls as $row) {
						echo '<tr>'
								.'<td>'.$row->active.'</td>'
								.'<td id="admin_'.$row->alias.'">'.$row->visit_count.'</td>'
								.'<td>'
									.'<a href="/a/'.$row->alias.'" target="_blank" '
										.'onClick="'
											.'$(\'#admin_'.$row->alias.'\').html('
												.'parseInt('
													.'parseInt($(\'#admin_'.$row->alias.'\').html())+1'
												.')'
											.');'
										.'"'
									.'>'.$row->alias.'</a></td>'
								.'<td><a href="'.$row->url.'" target="_blank">'.$row->url.'</a></td>'
								.'<td>'.$row->time.'</td>'
								.'<td>'.$row->ip.'</td>'
							.'</tr>';
					}
				?>
			<tbody>
		</table>

		<hr />
		<h2>Phish Tank Log</h2>

		<table class="table table-striped table-hover">
			<thead>
				<tr>
					<th>id</th>
					<th>added</th>
					<th>time</th>
				</tr>
			</thead>
			<tbody>
				<?php
					foreach($phish_logs as $log) {
						echo '<tr>'
								.'<td>'.$log->id.'</td>'
								.'<td>'.$log->added.'</td>'
								.'<td>'.$log->time.'</td>'
							.'</tr>';
					}
				?>
			<tbody>
		</table>
	</div>

    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/clipboard.min.js"></script>
    <script src="js/functions.js"></script>
    <script src="js/script.js"></script>
</body>
</html>