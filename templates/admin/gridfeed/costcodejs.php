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
    			property: 'code',
    			label: 'Code',
    			sortable: true
    		},
    		{
    			property:  'cost',
    			label: 'Cost ($)',
    			sortable: true
    		},
    		{
    			property:  'totalspent',
    			label: 'Spent ($)',
    			sortable: true
    		},
    		{
    			property:  'budget',
    			label: 'Budget %',
    			sortable: true
    		},
    		{
    			property:  'manualprogress',
    			label: 'Progress %',
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