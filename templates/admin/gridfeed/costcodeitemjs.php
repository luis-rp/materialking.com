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
    			property: 'itemcode',
    			label: 'Code',
    			sortable: true
    		},
    		{
    			property: 'itemname',
    			label: 'Item Name',
    			sortable: true
    		},
    		{
    			property: 'unit',
    			label: 'Unit',
    			sortable: true
    		},
    		{
    			property: 'quantity',
    			label: 'Quantity',
    			sortable: true
    		},
    		{
    			property: 'newreceived',
    			label: 'Qty Received',
    			sortable: true
    		},
    		{
    			property: 'ea',
    			label: 'Price EA',
    			sortable: true
    		},
    		{
    			property: 'totalprice',
    			label: 'Total Price',
    			sortable: true
    		},
    		{
    			property: 'daterequested',
    			label: 'Date Requested',
    			sortable: false
    		},
    		{
    			property: 'status',
    			label: 'P.O. Status',
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