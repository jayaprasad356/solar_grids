
  <section class="content-header">
        <h1>Coupon Code 
            <small><a href="home.php"><i class="fa fa-home"></i> Home</a></small>
        </h1>
        <ol class="breadcrumb">
            <a class="btn btn-block btn-default" href="add_coupon_code.php"><i class="fa fa-plus-square"></i> Add New Coupon Code</a>
        </ol>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
            
                    <!-- Table -->
                    <div class="box-body table-responsive">
                       <table id='users_table' class="table table-hover" data-toggle="table" data-url="api-firebase/get-bootstrap-table-data.php?table=coupon_code" data-page-list="[5, 10, 20, 50, 100, 200]" data-show-refresh="true" data-show-columns="true" data-side-pagination="server" data-pagination="true" data-search="true" data-trim-on-search="false" data-filter-control="true" data-query-params="queryParams" data-sort-name="id" data-sort-order="desc" data-show-export="true" data-export-types='["txt","csv"]' data-export-options='{
                            "fileName": "users-list-<?= date('d-m-Y') ?>",
                            "ignoreColumn": ["operate"] 
                        }'>
                            <thead>
                                <tr>
                                    <th data-field="id" data-sortable="true">ID</th>
                                    <th data-field="plan_name" data-sortable="true">Plan Name</th>
                                    <th data-field="amount" data-sortable="true">Amount</th>
                                    <th data-field="coupon_code" data-sortable="true">Coupon Code</th>
                                    <th data-field="operate" data-events="actionEvents">Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- JavaScript -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-table/1.20.2/bootstrap-table.min.js"></script>
    <script>
        // Refresh table on filter change
        $('#name').on('change', function () {
            $('#users_table').bootstrapTable('refresh');
        });

        $('#joined_date').on('change', function () {
            $('#users_table').bootstrapTable('refresh');
        });

        function queryParams(p) {
            return {
                name: $('#name').val(),
                joined_date: $('#joined_date').val(),
                limit: p.limit,
                sort: p.sort,
                order: p.order,
                offset: p.offset,
                search: p.search
            };
        }
    </script>
    


