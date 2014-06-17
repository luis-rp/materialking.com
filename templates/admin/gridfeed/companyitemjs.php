<script>


$(document).ready(function () {
    /* Datagrid
	================================================== */
    var dataSource = new StaticDataSource({
    	columns: [
    		{
    			property: 'ponum',
    			label: 'PO#',
    			sortable: true
    		},
    		{
    			property: 'totalamount',
    			label: 'Total amount',
    			sortable: true
    		},
    		{
    			property: 'awardedon',
    			label: 'Awarded Date',
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