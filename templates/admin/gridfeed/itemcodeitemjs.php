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
    			property: 'companyname',
    			label: 'Company',
    			sortable: true
    		},
    		{
    			property: 'awardedon',
    			label: 'Date',
    			sortable: true
    		},
    		{
    			property: 'ea',
    			label: 'Price EA',
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
    			property: 'totalprice',
    			label: 'Total Price',
    			sortable: false
    		},
    		{
    			property: 'itemstatus',
    			label: 'Item Status',
    			sortable: true
    		},
    		{
    			property: 'postatus',
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