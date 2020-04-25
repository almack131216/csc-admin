<?php

	if(empty($max_results)) $max_results = 50;
	if($cust_orderby=="position" || $cust_orderby=="position_incat") $cust_asc_or_desc = "asc";$sortableDetail = "position_incat";
	
	if(!$PreOrder) $WHERE .= " ORDER BY $cust_orderby $cust_asc_or_desc"; /// Your sql statement
	
	if(!isset($_GET['pg'])){ $pg = 1; } else { $pg = $_GET['pg']; }	
	$from = (($pg * $max_results) - $max_results);

	// FEEDBACK (FOR DEBUGGING)
	/*
	echo '<br>cust_status: '.$cust_status;
	echo '<br>cust_category: '.$cust_category;
	echo '<br>cust_orderby: '.$cust_orderby;
	echo '<br>cust_asc_or_desc: '.$cust_asc_or_desc;
	echo '<br>cust_maxperpage: '.$cust_maxperpage.' ('.$max_results.')';
	*/
	
	/// Count total
	
	if(!$PreTotalsQuery){
		$TotalsQuery = "SELECT COUNT(*) as Num FROM $FULLtablename $WHERE ";
	}else{
		$TotalsQuery = $PreTotalsQuery;
	}
	$echo .= $TotalsQuery.'<br>';
	$totals = mysql_result(mysql_query($TotalsQuery),0);
	$total_pgs = ceil($totals / $max_results);
	
	/// Page limiter & result builder
	if(!$PreQuery){
		$sql = "SELECT * FROM $FULLtablename";
	}else{
		$sql = $PreQuery;
	}
	$sql .= " $WHERE LIMIT $from, $max_results";
	//echo '<br>(FB):paginator > sql:'.$sql;
	$result = mysql_query($sql); $num_sql = mysql_num_rows ($result);

	
	// Build paginator
	$paginator = '';
	if($totals>$max_results){//only show if more than one page is needed
		$paginator .= '<div class="panel_oneline">';
		//$paginator .= '<br>(FB):'.$sql.'<br>';
		$paginator .= '<p>Results: '.$totals.'<br>';
		$paginator .= 'Viewing page '.$pg.' of '.$total_pgs.'</p>';
		$paginator .= '<ul class="paginator">';			
		if($total_pgs>10 && $pg!=1)	$paginator .= '<li><a href="'.$paginatorHref.'&amp;pg=1">First</a></li>';
		
		if($pg > 1){
			$prev = ($pg - 1); // Previous Link
			$paginator .= '<li><a href="'.$paginatorHref.'&amp;pg='.$prev.'" class="BackBut"><span>Previous page</span>&nbsp;</a></li>';
		}
		
		for($i = 1;($i <= $total_pgs && $i<10); $i++){ /// Numbers			
			if(($pg) == $i) {
				if($total_pgs>1) $paginator .= '<li><strong>'.$i.'</strong></li>'; //don't show selected page if it's there are no others
			} else {
				$paginator .= '<li><a href="'.$paginatorHref.'&amp;pg='.$i.'">'.$i.'</a></li>';
			}
		}
		if($pg < $total_pgs){
			$next = ($pg + 1); // Next Link
			$paginator .= '<li><a href="'.$paginatorHref.'&amp;pg='.$next.'" class="NextBut"><span>Next page</span>&nbsp;</a></li>';
		}
		if($total_pgs>10 && $pg!=$total_pgs) $paginator .= '<li><a href="'.$paginatorHref.'&amp;pg='.$total_pgs.'">Last</a></li>';
		$paginator .= '</ul>';
		$paginator .= '</div>';
	}
	
	/// Display results
	if ($num_sql>0) {
		if(!$field_id) $field_id = 'id';//id field name to go through
		$i=0;
		while ($i < $num_sql) {	
			$id = mysql_result($result,$i,$field_id);
			if($id){
				$itemArray_query = "SELECT * FROM $FULLtablename WHERE $field_id=$id LIMIT 1";
				$itemArray_result = mysql_query($itemArray_query);
				if($itemArray_result && mysql_num_rows($itemArray_result)==1){
					$row = mysql_fetch_array($itemArray_result);
					$itemArray[] = $row;					
					$i++;							
				}
			}
		}
	}
	
?>