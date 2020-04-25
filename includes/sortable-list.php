<script type="text/javascript">
   function updateOrder()
    {
        
        var options = {
                        method : 'post',	                                
                        parameters : Sortable.serialize('SortableList')                        
                      };
                      

        new Ajax.Request('processor.php?tablename=<?php echo $tablename.'&fieldname='.$fieldname.'&detail='.$sortableDetail; ?>', options);
    }
    <?php
    	if($sortableDetail=="position_initem"){
	?>
	    Sortable.create('SortableList', { onUpdate : updateOrder, constraint: 'horizontal', scroll: window, handles:$$('#SortableList a.Move') });
	<?php	
    	}else{
	?>
	    Sortable.create('SortableList', { onUpdate : updateOrder, handles:$$('#SortableList a.Move') });
	<?php
    	}	    	
    ?>
</script>