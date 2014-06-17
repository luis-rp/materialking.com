<script>

    function update_invoice_status(invoice_number) {
        var invoice_status_value = $('#invoice_' + invoice_number + " option:selected").val();
        var url = "<?php echo base_url("admin/quote/update_invoice_status");?>";
        $.ajax({
            type: "POST",
            url: url,
            data: {id:invoice_number, status: invoice_status_value}
        }).done(function(data) {
            $('#message_div').html(data);
        });
    }
    
    function update_invoice_payment_status(invoice_number) 
    {
        var invoice_payment_status_value = $('#invoice_payment_' + invoice_number + " option:selected").val();
        var invoice_payment_type_value = $('#invoice_paymenttype_' + invoice_number + " option:selected").val();
        var refnum_value = $('#refnum_' + invoice_number + "").val();
        var url = "<?php echo base_url("admin/quote/update_invoice_payment_status");?>";
        //alert(invoice_payment_status_value);
        $.ajax({
            type: "POST",
            url: url,
            data: {invoicenum:invoice_number, paymentstatus: invoice_payment_status_value, paymenttype: invoice_payment_type_value, refnum: refnum_value}
        }).done(function(data) {
            $('#message_div').html(data);
        });
    }


    $(document).ready(function() {
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
                    property: 'invoicenum',
                    label: 'Invoice#',
                    sortable: true
                },
                {
                    property: 'status_selectbox',
                    label: 'Status',
                    sortable: false
                },
                {
                    property: 'payment_status_selectbox',
                    label: 'Payment',
                    sortable: false
                },
                {
                    property: 'receiveddate',
                    label: 'Received on',
                    sortable: true
                },
                {
                    property: 'totalprice',
                    label: 'Total Cost',
                    sortable: true
                },
                {
                    property: 'actions',
                    label: 'Details',
                    sortable: false
                }
            ],
            data: sampleData.geonames,
            delay: 250
        });


        $('#MyGrid').datagrid({
            dataSource: dataSource,
            stretchHeight: true,
            onClickRow: function(rowIndex) {
                alert('1');
                if (lastIndex != rowIndex) {
                    $('#MyGrid').datagrid('endEdit', lastIndex);
                    $('#MyGrid').datagrid('beginEdit', rowIndex);
                    setEditing(rowIndex);
                }
                lastIndex = rowIndex;
            }
        });
    });



    var sampleData = {
        geonames: <?php echo json_encode($items); ?>
    };

    function setEditing(rowIndex) {
        var editors = $('#tt').datagrid('getEditors', rowIndex);
        var priceEditor = editors[0];
        var amountEditor = editors[1];
        var costEditor = editors[2];
        priceEditor.target.bind('change', function() {
            calculate();
        });
        amountEditor.target.bind('change', function() {
            calculate();
        });
        function calculate() {
            var cost = priceEditor.target.val() * amountEditor.target.val();
            $(costEditor.target).numberbox('setValue', cost);
        }
    }
</script>