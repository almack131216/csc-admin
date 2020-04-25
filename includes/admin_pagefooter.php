<?php
	/*
	$time = microtime();
	$time = explode(" ", $time);
	$time = $time[1] + $time[0];
	$finish = $time;
	$totaltime = ($finish - $start);
	printf ("This page took %f seconds to load.", $totaltime);
	*/
	if($_SERVER['HTTP_HOST']=="localhost" || $_GET['debug']) echo '<p style="position:absolute;top:0px;left:0px;background:#f30;color:#fff;">'.$echo.'</p>';
?>
</div><!--(END OF): CONTENT -->
</div><!--(END OF): WRAP ALL -->


</body>
</html>

<?php
	if(!$testing) if($_SESSION['FirstName']) mysql_close();
	// Flush buffered output to the web browser
	ob_end_flush();
?>