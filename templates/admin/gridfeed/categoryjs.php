<script>
/*
var StaticDataSource = function (a)
{
	this._formatter = a.formatter;
	this._columns = a.columns;
	this._delay = a.delay || 0;
	this._data = a.data
};
StaticDataSource.prototype = {
	columns: function ()
	{
		return this._columns
	},
	data: function (b, c)
	{
		var a = this;
		setTimeout(function ()
		{
			var i = $.extend(true, [], a._data);
			var f = i.length;
			if (b.sortProperty)
			{
				i = a.sortBy(i, b.sortProperty);
				if (b.sortDirection === "desc")
				{
					i.reverse()
				}
			}
			var j = b.pageIndex * b.pageSize;
			var h = j + b.pageSize;
			var e = (h > f) ? f : h;
			var d = Math.ceil(f / b.pageSize);
			var g = b.pageIndex + 1;
			var k = j + 1;
			i = i.slice(j, h);
			if (a._formatter)
			{
				a._formatter(i)
			}
			c(
			{
				data: i,
				start: k,
				end: e,
				count: f,
				pages: d,
				page: g
			})
		}, this._delay)
	},
	filter: function (e, a, d)
	{
		results = [];
		if (e == null)
		{
			return results
		}
		for (var f = e.length, b = 0; b < f; b++)
		{
			if (d(e[b]) === true)
			{
				results[results.length] = e[b]
			}
		}
		return results
	},
	sortBy: function (b, a)
	{
		return b.sort(function (d, c)
		{
			if (d[a] < c[a])
			{
				return -1
			}
			if (d[a] > c[a])
			{
				return 1
			}
			return 0
		})
	}
};
*/
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