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
    			property: 'itemcode',
    			label: 'Code',
    			sortable: true,
    			width:'15%'
    		},
    		{
    			property: 'itemname',
    			label: 'Item Name',
    			sortable: true,
    			width:'15%'
    		},
    		{
    			property: 'qty',
    			label: 'QTY',
    			sortable: true,
    			width:'5%'
    		},
    		{
    			property: 'specs',
    			label: 'Specs',
    			sortable: true,
    			width:'5%'
    		},
    		{
    			property: 'unit',
    			label: 'Unit',
    			sortable: true,
    			width:'5%'
    		},
    		{
    			property: 'totalpoprice',
    			label: 'Total Purchased Amount',
    			sortable: true,
    			width:'10%'
    		},
    		{
    			property: 'awardedon',
    			label: 'Last Awarded Date',
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