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
    			label: 'Item Code',
    			sortable: false,
    			width:'8%'
    		},
    		{
    			property: 'itemname',
    			label: 'Item Name',
    			sortable: false,
    			width:'8%'
    		},
    		{
    			property: 'item_img',
    			label: 'Item Image',
    			sortable: false,
    			width:'8%'
    		},
    		{
    			property: 'qtyonhand',
    			label: 'Qty On Hand',
    			sortable: false,
    			width:'5%'
    		},
    		{
    			property: 'qtyonpo',
    			label: 'Qty On PO',
    			sortable: false,
    			width:'5%'
    		},
    		{
    			property: 'minstock',
    			label: 'MIN Stock',
    			sortable: false,
    			width:'8%'
    		},
    		{
    			property: 'maxstock',
    			label: 'MAX Stock',
    			sortable: false,
    			width:'8%'
    		},
    		{
    			property: 'reorderqty',
    			label: 'Reorder Qty',
    			sortable: false,
    			width:'8%'
    		},
    		{
    			property: 'daterequested',
    			label: 'Next Delivery',
    			sortable: false,
    			width:'10%'
    		},
    		{
    			property: 'lastaward',
    			label: 'Last Award',
    			sortable: false,
    			width:'5%'
    		},
    		{
    			property: 'valueonhand',
    			label: 'Value On Hand',
    			sortable: false,
    			width:'5%'
    		},
    		{
    			property: 'valuecomitted',
    			label: 'Value Comitted',
    			sortable: false,
    			width:'5%'
    		},
    		{
    			property: 'manage',
    			label: 'Adjust_Quantity On Hand',
    			sortable: false,
    			width:'14%'
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