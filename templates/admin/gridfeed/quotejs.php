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
    			property: 'potype',
    			label: 'Purchase Type',
    			sortable: true
    		},
    		{
    			property: 'pricerank',
    			label: 'Price Rank',
    			width: 100,
    			sortable: false
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
    		},
			{
    			property: 'recived',
    			label: 'Received',
    			sortable: false
    		},
			{
    			property: 'sent',
    			label: 'Sent',
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