<?php
/*
 * Author: Konrad 'morsik' Mosoń <morsik@darkserver.it>
 */

include('./config.php');
include('./ua.php');

$f = fopen($logfile, 'r');
if (!$f) die("error while opening log");

$sites    = array();
$browsers = array();
$oses     = array();

$count = 0;

while (!feof($f))
{
	$l = fgets($f, 1024);
	$match = array();
	
	preg_match('#^([0-9.]+) (.*) "GET '.$urifile.' HTTP/1.1" 200 ([0-9\-]+) "(.+)" "(.*)"$#', $l, $match);

	if ($match)
	{
		$count++;

		// BEGIN // count reflinks
		$found = false;
		if (preg_match('#^-$#', $match[4]))
		{
			$sites['direct']++;
		}
		else
		{
			$url = parse_url($match[4]);

			switch ($url['host'])
			{
				case 'www.google.com':
				case 'www.google.pl':
					$url['host'] = 'google.com';
					break;
				case 'm.facebook.com':
				case 'www.facebook.com':
					$url['host'] = 'facebook.com';
					break;
			}
			$sites[$url['host']]++;
		}
		//  END  // count reflinks

		// BEGIN // collect ua data
		$ua = getBrowser($match[5]);

		$oses[$ua['platform']]++;
		$browsers[$ua['name']]++;
		//  END  // collect ua data
	}
}

// sort things
arsort($sites);
arsort($browsers);
arsort($oses);

function drawProgress($x, $color)
{
	global $count;
	echo '<div class="progress-overlay b'.$color.'"><div class="progress bl'.$color.'" style="width:'.round($x/$count*100, 2).'%"></div></div></td><td class="a_r">'.round($x/$count*100, 1).'%';
}

?>
<html>
<head>
	<title>rootnode-dump.sql stats</title>
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
	<style>
* {
	font-size: 12px;
	font-family: monospace;
	margin: 0;
	padding: 0;
	line-height: 16px;
}
body {
	background: #222;
	color: #ccc;
	padding: 32px;
}
a:link,
a:visited {
	color: #080;
}
a:hover {
	color: #0f0;
}
.section {
	margin: 16px 0;
}
td {
	padding: 0px 4px;
}
.a_r {
	text-align: right;
}
.progress-overlay {
	width: 128px;
	height: 6px;
}
.progress {
	height: 100%;
}
.c2 { color: #0f0; }
.c3 { color: #ff0; }
.c6 { color: #0ff; }
.c7 { color: #fff; }
.b2 { background: #080; }
.b3 { background: #880; }
.b6 { background: #088; }
.bl2 { background: #0f0; }
.bl3 { background: #ff0; }
.bl6 { background: #0ff; }
	</style>
</head>
<body>
	stats for <a href="<?php echo $uri ?>"><?php echo $uri ?></a>
	<div><span class="c7">all requests:</span> <?php echo $count ?></div>

	<div class="section">
		reflinks:
		<table>
		<?php foreach ($sites as $s => $c) { ?>
			<tr><td class="c3"><?php echo $s ?></td><td><?php echo $c ?></td><td><?php drawProgress($c, 3) ?></td></tr>
		<?php } ?>
		</table>
	</div>
	
	<div class="section">
		oses:
		<table>
		<?php foreach ($oses as $os => $c) { ?>
			<tr><td class="c6"><?php echo $os ?></td><td><?php echo $c ?></td><td><?php drawProgress($c, 6) ?></td></tr>
		<?php } ?>
		</table>
	</div>

	<div class="section">
		browsers:
		<table>
		<?php foreach ($browsers as $b => $c) { ?>
			<tr><td class="c2"><?php echo $b ?></td><td><?php echo $c ?></td><td><?php drawProgress($c, 2) ?></td></tr>
		<?php } ?>
		</table>
	</div>
</body>
</html>
