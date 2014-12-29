<script>
$(document).ready(function () {
    /* Datagrid
	================================================== */
    var dataSource = new StaticDataSource({
    	columns: [
    		{
    			property: 'id',
    			label: 'ID',
    			sortable: false
    		},
    		{
    			property: 'ponum',
    			label: 'PO#',
    			sortable: true
    		},    		
    		{
    			property: 'podate',
    			label: 'PO Date',
    			sortable: true
    		},
    		{
    			property: 'status',
    			label: 'Status',
    			sortable: true
    		},
    		{
    			property: 'actions',
    			label: 'Actions',
    			sortable: false
    		}			
    	],
    	data: sampleData.geonames,
    	delay: 250
    });
    
    $('#MyGrid').datagrid({
    	dataSource: dataSource,
    	stretchHeight: true
    });
});
var sampleData = {
	geonames: <?php echo json_encode($items);?>
};
</script>