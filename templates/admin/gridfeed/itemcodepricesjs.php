<script>


$(document).ready(function () {
    /* Datagrid
	================================================== */
    var dataSource = new StaticDataSource({
    	columns: [
    		{
    			property: 'companyname',
    			label: 'Company',
    			sortable: true
    		},
    		{
    			property: 'itemname',
    			label: 'Item Name',
    			sortable: true
    		},
    		{
    			property: 'price',
    			label: 'Price',
    			sortable: true
    		},
    		{
    			property: 'substitute',
    			label: 'Substitute',
    			sortable: true
    		},
    		{
    			property: 'quoteon',
    			label: 'Date Quoted',
    			sortable: true
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