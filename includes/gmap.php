<script type="text/javascript">
	var options = {method : 'post'};	
	new Ajax.Request('processor.php?tablename=<?php echo $tablename.'&detail=gmap&detail2='.$postcode; ?>, options);
</script>