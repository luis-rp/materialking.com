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
    			property: 'qty',
    			label: 'QTY',
    			sortable: true
    		},
    		{
    			property: 'specs',
    			label: 'Specs',
    			sortable: true
    		},
    		{
    			property: 'unit',
    			label: 'Unit',
    			sortable: true
    		},
    		{
    			property: 'totalpoprice',
    			label: 'Total Purchased Amount',
    			sortable: true
    		},
    		{
    			property: 'awardedon',
    			label: 'Last Awarded Date',
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