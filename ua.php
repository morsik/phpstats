<?php
/*
 * Author: ruudrp <ruudrp@live.nl>
 *         Konrad 'morsik' Mosoń <morsik@darkserver.it>
 * Code found at: http://www.php.net/manual/en/function.get-browser.php#101125
 * modified by morsik
 */

function getBrowser($u_agent) 
{ 
	$bname = 'unknown';
	$platform = 'unknown';
	$version= "";

	//First get the platform?
	if (preg_match('/linux/i', $u_agent)) {
		$platform = 'Linux';
	}
	elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
		$platform = 'Max OS X';
	}
	elseif (preg_match('/windows|win32/i', $u_agent)) {
		$platform = 'Windows';
	}
	
	// Next get the name of the useragent yes seperately and for good reason
	if (preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent)) 
	{ 
		$bname = 'Internet Explorer'; 
		$ub = "MSIE"; 
	} 
	elseif (preg_match('/Firefox/i',$u_agent)) 
	{ 
		$bname = 'Firefox';
		$ub = "Firefox"; 
	} 
	elseif (preg_match('/Chrome/i',$u_agent)) 
	{ 
		$bname = 'Chrome';
		$ub = "Chrome"; 
	} 
	elseif (preg_match('/Safari/i',$u_agent)) 
	{ 
		$bname = 'Safari';
		$ub = "Safari";
	} 
	elseif (preg_match('/Opera/i',$u_agent)) 
	{ 
		$bname = 'Opera'; 
		$ub = "Opera"; 
	} 
	elseif (preg_match('/Netscape/i',$u_agent)) 
	{ 
		$bname = 'Netscape'; 
		$ub = "Netscape"; 
	} 
	elseif (preg_match('/Wget/i',$u_agent))
	{
		$bname = 'Wget';
		$ub = 'wget';
	}
	elseif (preg_match('/^([A-z0-9]+)\/([0-9.]+)/',$u_agent,$m))
	{
		$bname = $m[1];
		$ub = $m[1];
	}
	
	// finally get the correct version number
	$known = array('Version', $ub, 'other');
	$pattern = '#(?<browser>' . join('|', $known) .
	')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
	if (!preg_match_all($pattern, $u_agent, $matches)) {
		// we have no matching number just continue
	}
	
	// see how many we have
	$i = count($matches['browser']);
	if ($i != 1) {
		//we will have two since we are not using 'other' argument yet
		//see if version is before or after the name
		if (strripos($u_agent,"Version") < strripos($u_agent,$ub)){
			$version= $matches['version'][0];
		}
		else {
			$version= $matches['version'][1];
		}
	}
	else {
		$version= $matches['version'][0];
	}
	
	// check if we have a number
	if ($version==null || $version=="") {$version="?";}
	
	return array(
		'userAgent' => $u_agent,
		'name'	  => $bname,
		'version'   => $version,
		'platform'  => $platform,
		'pattern'	=> $pattern
	);
} 
