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
    			property: 'title',
    			label: 'Name',
    			sortable: true
    		},
    		{
    			property: 'primaryemail',
    			label: 'Email',
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