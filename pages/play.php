<?php
/* 
 *	Made by Samerton
 *  http://worldscapemc.co.uk
 *
 *  License: MIT
 */

?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Samerton">
    <link rel="icon" href="/assets/favicon.ico">

    <title><?php echo $sitename; ?> &bull; Play</title>
	
	<?php require('inc/templates/header.php'); ?>
	
	<!-- Custom style -->
	<style>
	html {
		overflow-y: scroll;
	}
	</style>
	
  </head>
  <body>
    <?php require('inc/templates/navbar.php'); ?>
	<div class="container">
	<?php
	// Get the default server IP
	$default_server = $queries->getWhere("mc_servers", array("is_default", "=", "1"));
	if(empty($default_server)){
		echo 'No default server defined.';
		die();
	}
	$default_server = htmlspecialchars($default_server[0]->ip);
	/*
	 *  Resolve real IP address (to support SRV records)
	 */
	require('inc/integration/status/SRVResolver.php');
	$parts = explode(':', $default_server);
	if(count($parts) == 1){
		$domain = $parts[0];
		$query_ip = SRVResolver($domain);
		$parts = explode(':', $query_ip);
		$default_ip = $parts[0];
		$default_port = $parts[1];
	} else if(count($parts) == 2){
		$domain = $parts[0];
		$default_ip = $parts[0];
		$default_port = $parts[1];
		$port = $parts[1];
	} else {
		echo 'Invalid IP';
		die();
	}

	// IP to display
	if(!isset($port)){
		$address = $domain;
	} else {
		$address = $domain . ':' . $port;
	}
	?>
	  <div class="alert alert-info"><center>Connect to the server with the IP <strong><?php echo htmlspecialchars($domain); ?></strong></center></div>
	  <?php require('inc/integration/status/global.php'); ?>
	  <div class="row">
		<div class="col-md-3">
		  <div class="well">
			<table class="table">
			  <tr class="<?php if(!empty($Info)){ ?>success<?php } else { ?>danger<?php } ?>">
				<td><b>Status:</b></td>
				<td><?php if(!empty($Info)){ ?>Online<?php } else { ?>Offline<?php } ?></td>
			  </tr>
			  <tr>
				<td><b>Players:</b></td>
				<td><?php echo $Info['players']['online'] . ' / ' . $Info['players']['max'];?></td>
			  </tr>
			  <tr>
				<td><b>Queried in:</b></td>
				<td><?php echo $Timer; ?>s</td>
			  </tr>
			</table>
		  </div>
		</div>
		<div class="col-md-9">
		  <div class="well">
			<h3>Players online</h3>
			<?php 
			$servers = $queries->getWhere("mc_servers", array("display", "=", "1"));
			require('inc/integration/status/server.php');
			$serverStatus = new ServerStatus();
			foreach($servers as $server){
				$parts = explode(':', $server->ip);
				if(count($parts) == 1){
					$domain = $parts[0];
					$query_ip = SRVResolver($domain);
					$parts = explode(':', $query_ip);
					$server_ip = $parts[0];
					$server_port = $parts[1];
				} else if(count($parts) == 2){
					$server_ip = $parts[0];
					$server_port = $parts[1];
				} else {
					echo 'Invalid IP';
					die();
				}
			?>
			<h4><?php echo htmlspecialchars($server->name); ?></h4>
			<?php 
				$serverStatus->serverPlay($server_ip, $server_port, $server->name);
			}
			?>
			</div>
		  </div>
		</div>
		
	  <hr>
	  
	  <?php require('inc/templates/footer.php'); ?>
	</div>
	<?php require('inc/templates/scripts.php'); ?>
	<script>
	$(document).ready(function(){
		$("[rel=tooltip]").tooltip({ placement: 'top'});
	});
	</script>
  </body>
</html>