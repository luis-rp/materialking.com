<script>

$(document).ready(function () {
    /* Datagrid
	================================================== */
    var dataSource = new StaticDataSource({
    	columns: [
    		{
    			property: 'id',
    			label: 'ID',
    			sortable: false,
    			width:'2%'
    		},
    		{
    			property: 'ponum',
    			label: 'PO#',
    			sortable: true,
    			width:'10%'
    		},
    		{
    			property: 'itemcode',
    			label: 'Code',
    			sortable: true,
    			width:'10%'
    		},
    		{
    			property: 'item_img',
    			label: 'Item Image',
    			sortable: true,
    			width:'10%'
    		},
    		{
    			property: 'itemname',
    			label: 'Item Name',
    			sortable: true,
    			width:'10%'
    		},
    		{
    			property: 'unit',
    			label: 'Unit',
    			sortable: true,
    			width:'4%'
    		},
    		{
    			property: 'quantity',
    			label: 'Quantity',
    			sortable: true,
    			width:'4%'
    		},
    		{
    			property: 'newreceived',
    			label: 'Qty Received',
    			sortable: true,
    			width:'4%'
    		},
    		{
    			property: 'ea',
    			label: 'Price EA',
    			sortable: true,
    			width:'4%'
    		},
    		{
    			property: 'totalprice',
    			label: 'Total Price',
    			sortable: true,
    			width:'5%'
    		},
    		{
    			property: 'daterequested',
    			label: 'Date Requested',
    			sortable: false,
    			width:'10%'
    		},
    		{
    			property: 'itemstatus',
    			label: 'Item Status',
    			sortable: true,
    			width:'10%'
    		},
    		{
    			property: 'status',
    			label: 'P.O. Status',
    			sortable: true,
    			width:'10%'
    		},
    		{
    			property: 'actions',
    			label: 'Actions',
    			sortable: false,
    			width:'10%'
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