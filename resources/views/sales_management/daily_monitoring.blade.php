@extends('layouts.app')
@section('content')
<html>

<head>
    <title>Daily Monitoring</title>
    <link rel="stylesheet" href="admin/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="admin/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="admin/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
    <link rel="stylesheet" href="admin/plugins/toastr/toastr.min.css">
    <link rel="icon" href="images/cmu_press_logo.png" type="image/png">
    <script src="admin/plugins/sweetalert2/sweetalert2.min.js"></script>
    <script src="admin/plugins/toastr/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
    <style>
    #MonitoringTable th,
    #MonitoringTable td {
        white-space: nowrap;
    }
    .dataTables_paginate .paginate_button {
        display: none;
    }
    .dataTables_paginate .paginate_button.previous,
    .dataTables_paginate .paginate_button.next {
        display: inline-block;
    }
    </style>
</head>

<body class="hold-transition sidebar-mini" style="font-family: Roboto, sans-serif;">
    <div class="wrapper">
        <div class="container-fluid">
            <br>
            <div class="card card-primary col-md-4">
                <div class="card-body">
                    <div class="form-group">
                        <label for="month">Select Month</label>
                        <div class="input-group">
                            <input type="month" class="form-control" id="ChooseMonth" name="month">
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header" style="background: #E9ECEF;">
                    <h3 class="card-title">Daily Monitoring</h3>
                </div>
                <div class="card-body">
                    <!-- MONITORING TABLE -->
                    <table class="table table-bordered table-striped" id="MonitoringTable" style="font-size: 14px;">
                        <thead class="text-center">
                            <tr>
                                <th>Code</th>
                                <th>Title</th>
                                <th>Batch</th>
                                <th>Unit Price</th>
                                <th>Quantity Available</th>
                                <th>1</th>
                                <th>2</th>
                                <th>3</th>
                                <th>4</th>
                                <th>5</th>
                                <th>6</th>
                                <th>7</th>
                                <th>8</th>
                                <th>9</th>
                                <th>10</th>
                                <th>11</th>
                                <th>12</th>
                                <th>13</th>
                                <th>14</th>
                                <th>15</th>
                                <th>16</th>
                                <th>17</th>
                                <th>18</th>
                                <th>19</th>
                                <th>20</th>
                                <th>21</th>
                                <th>22</th>
                                <th>23</th>
                                <th>24</th>
                                <th>25</th>
                                <th>26</th>
                                <th>27</th>
                                <th>28</th>
                                <th>29</th>
                                <th>30</th>
                                <th>31</th>
                                <th>Unit Sold</th>
                                <th>Unit Deducted</th>
                                <th>Sales</th>
                                <th>Available Stocks</th>
                                <th>Inventory Value</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                        <tfoot>
                        </tfoot>
                    </table>
                    <!-- MONITORING TABLE -->
                </div>
            </div>
        </div>
        <br>
    </div>
    <script>
    function refreshMonitoringTable(selectedMonth) {
        $.ajax({
            url: "{{ route('monitoring.index') }}",
            type: 'GET',
            dataType: 'json',
            data: {
                month: selectedMonth
            },
            success: function(data) {
                var table = $('#MonitoringTable').DataTable();
                var existingRows = table.rows().remove().draw(false);
                data.forEach(function(batch) {
                    var quantityAvailable = batch.quantity_produced - (parseInt(batch
                        .sold_quantity_before) + parseInt(batch.deducted_quantity_before));
                    var unitSold = batch.sold_quantity_within;
                    var unitDeducted = batch.deducted_quantity_within;
                    var sales = batch.price.toFixed(2) * batch.sold_quantity_within;
                    var availableStocks = quantityAvailable - (parseInt(batch
                        .sold_quantity_within) + parseInt(batch.deducted_quantity_within));
                    var inventoryValue = batch.price.toFixed(2) * availableStocks;
                    function monetaryValue(x) {
                        return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                    }
                    var row = [
                        batch.im.code,
                        batch.im.title,
                        batch.name,
                        '<span style="float: right;">' + monetaryValue(batch.price.toFixed(2)) +
                        '</span>',
                        '<span style="float: right;">' + quantityAvailable + '</span>'
                    ];
                    for (var i = 1; i <= 31; i++) {
                        if (batch.daily_sales && batch.daily_sales[i]) {
                            row.push('<span style="float: right;">' + batch.daily_sales[i] +
                                '</span>');
                        } else {
                            row.push('');
                        }
                    }
                    row[36] = '<span style="float: right;">' + unitSold + '</span>';
                    row[37] = '<span style="float: right;">' + unitDeducted + '</span>';
                    row[38] = '<span style="float: right;">' + monetaryValue(sales.toFixed(2)) +
                        '</span>';
                    row[39] = '<span style="float: right;">' + availableStocks + '</span>';
                    row[40] = '<span style="float: right;">' + monetaryValue(inventoryValue.toFixed(
                        2)) + '</span>';
                    table.row.add(row);
                });
                table.draw();
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
            }
        });
    }
    $(document).ready(function() {
        var startDate = new Date();
        startDate.setMonth(startDate.getMonth() - 1);
        startDate.setDate(1);
        var today = new Date();
        today.setDate(1);
        $('#ChooseMonth').val(today.toISOString().slice(0,
            7));
        refreshMonitoringTable(today.toISOString().slice(0, 7));
        $('#MonitoringTable').DataTable({
            "paging": true,
            "lengthChange": false,
            "searching": true,
            "ordering": false,
            "info": true,
            "autoWidth": false,
            "responsive": false,
            "scrollX": true,
            "scrollY": true,
            "scrollCollapse": false,
            "buttons": ["copy", "excel", "pdf"],
            "pageLength": 8
        }).buttons().container().appendTo('#MonitoringTable_wrapper .col-md-6:eq(0)');
        $('#ChooseMonth').on('change', function() {
            var selectedMonth = $(this).val();
            refreshMonitoringTable(selectedMonth);
        });
        var previousWidth = $(window).width();
        $(window).on('resize', function() {
            var currentWidth = $(window).width();
            if (currentWidth !== previousWidth) {
                var selectedMonth = $('#ChooseMonth').val();
                refreshMonitoringTable(selectedMonth);
                previousWidth = currentWidth;
            }
        });
    });
    </script>
</body>

</html>
@endsection