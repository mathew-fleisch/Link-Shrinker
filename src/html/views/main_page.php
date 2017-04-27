<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Link Shrinker</title>
	<link rel="icon" type="image/png" href="/favicon.ico" />
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
	<ul class="nav nav-tabs">
		<li class="active"><a href="/" >Home</a></li>
		<li class=""><a href="/admin">Admin</a></li>
	</ul>
	<div id="container">
		<h1>Link Shrinker</h1>
		<div id="errors"></div>
		<div id="info-container"></div>
		<div class="form-group" id="form-container">
			<div class="input-group">
				<input type="text" class="form-control" id="long-url" value="http://" placeholder="http://" />
				<span class="input-group-btn">
					<button type="button" class="btn btn-default" id="shrink">shrink</button>
				</span>
			</div>
		</div>
		<br />


		<div class="form-group" id="output-container">
			<div id="lazy-link"></div>
			<div class="input-group">
				<span class="input-group-addon" id="visit-count"></span>
				<input class="form-control" id="short-url" value="" readonly>
				<span class="input-group-btn">
					<button type="button" class="btn btn-default" id="copy-btn" data-clipboard-target="#short-url" onClick="show_info('info', 'Copied to clipboard!', 1);">
					    <img class="invert-svg" width="13" src="assets/clippy.svg" alt="Copy to clipboard">
					</button>
				</span>
			</div>
		</div>


	</div>

    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/clipboard.min.js"></script>
    <script src="js/functions.js"></script>
    <script src="js/script.js"></script>
</body>
</html>