<?php
$datetime = date('Y-m-d H:i:s');

// Process the cancel action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btnCancel']) && !empty($_POST['chk']) && !empty($_POST['reason'])) {
    $selectedIds = $_POST['chk']; // Retrieve selected IDs
    $reason = $_POST['reason']; // Retrieve the reason for cancellation

    foreach ($selectedIds as $id) {
        $id = intval($id); // Ensure the ID is an integer to prevent SQL injection
        $query = "SELECT status FROM youtuber_income WHERE id = $id";
        $db->sql($query);
        $result = $db->getResult();

        if (!empty($result) && $result[0]['status'] == 1) {
            // Set session variable to show error message
            // $_SESSION['error'] = "Income with ID $id is already marked as 'Paid'. Cannot change status to 'Rejected'.";
            echo '<script>alert("Income with ID ' . $id . ' is already marked as \'Paid\'. Cannot change status to \'Rejected\'.");</script>';
        } elseif (!empty($result) && $result[0]['status'] != 1) {
            // Update status to 2 (Rejected) and set the reason
            $updateQuery = "UPDATE youtuber_income SET status = 2, reason = '$reason' WHERE id = $id";
            $db->sql($updateQuery);
        }
    }

    echo '<script>window.location.href = "youtube_income.php";</script>';
    exit; // Ensure that script execution stops after redirecting
}
?>

<style>
    .form-group {
        display: flex;
        /* direction: row; */
        align-items: center;
        gap: 10px;
        margin-bottom: 20px;
        
    }

    .form-control-reason {
        
        width: 300px;
        padding: 5px;
        border-radius: 4px;
        border: 1px solid #ddd;
        /* flex-direction: column; */
        
    }

    .btn-danger {
       padding: 6px 15px;
    font: size 10px;
    margin-top: 24px;
    margin-left: 94px;
}
    

    .table-responsive {
        margin-top: 20px;   
    }

    .table th, .table td {
        text-align: center;
        vertical-align: middle;
    }

    #error-message {
        margin-top: 10px;
        padding: 10px;
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
        border-radius: 4px;
    }
    .fixed-table-pagination .pagination-info {
    line-height: 8px;
    margin-right: 7px;
    margin-left: 20px;
}
.form-control{
    width: 300px;
    margin-left: 20px;
    }
.box-title{
    margin-left: 20px;
}/* Mobile responsiveness */
@media (max-width: 768px) {
    .form-group {
        flex-direction: column; /* Stack form elements vertically */
        align-items: flex-start; /* Align items to the left */
        gap: 15px;
    }

    .form-control-reason {
        width: 100%; /* Full width on smaller screens */
    }

    .btn-danger {
        margin-left: 0; /* Align button with other elements */
        margin-top: 10px; /* Add spacing on top */
        width: 100%; /* Full width for better touch accessibility */
    }

    .form-control {
        width: 100%; /* Full width for inputs */
        margin-left: 0; /* Remove extra margin on smaller screens */
    }

    .box-title {
        margin-left: 14px; /* Align title to the left */
    }

    .table-responsive {
        overflow-x: auto; /* Ensure horizontal scrolling */
    }

    .fixed-table-pagination .pagination-info {
        margin-left: 0;
        margin-right: 0;
        text-align: center; /* Center pagination info on small screens */
    }
}

@media (max-width: 480px) {
    .form-group {
        gap: 10px; /* Reduce gap for smaller screens */
    }

    .btn-danger {
        font-size: 12px; /* Slightly larger font size for readability */
    }

    .table th, .table td {
        font-size: 12px; /* Adjust table font size for smaller screens */
    }
}
</style>

<section class="content-header">
    <h1>Youtube Income /<small><a href="home.php"><i class="fa fa-home"></i> Home</a></small></h1>
    <ol class="breadcrumb">
        <a class="btn btn-block btn-default" href="add_youtube_income.php"><i class="fa fa-plus-square"></i> Add New Youtube Income</a>
    </ol>
</section>

<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <form method="POST" action="">
                <div  class="box-body table-responsive">
                   <div class="row">
                         <div class="form-group">
                                <div class="col-md-2">
                                    <input type="checkbox" onchange="checkAll(this)" name="selectAll"> Select All
                                </div>
                                <div class="col-md-2">
                                    <label for="reason">Reason</label>
                                    <input type="text" name="reason" id="reason" class="form-control-reason" placeholder="Enter reason" required>
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-danger" name="btnCancel">Cancel</button>
                                </div>
                            </div>
                     <div>
                                                <h4 class="box-title">Filter by Status </h4>
                                                <select id='status' name="status" class='form-control'>
                                                <option value="0">Wait For Approval</option>
                                                        <option value="1">Paid</option>
                                                        <option value="2">Cancelled</option>
                                                </select>
                                        </div>

                        <table id='users_table' class="table table-hover" data-toggle="table" data-url="api-firebase/get-bootstrap-table-data.php?table=youtube_income" data-page-list="[5, 10, 20, 50, 100, 200]" data-show-refresh="true" data-show-columns="true" data-side-pagination="server" data-pagination="true" data-search="true" data-trim-on-search="false" data-filter-control="true" data-query-params="queryParams" data-sort-name="id" data-sort-order="desc" data-show-export="true" data-export-types='["txt","csv"]' data-export-options='{
                            "fileName": "users-list-<?= date('d-m-Y') ?>",
                            "ignoreColumn": ["operate"] 
                        }'>
                            <thead>
                                <tr>
                                    <th data-field="column"> All</th>
                                    <th data-field="operate" data-events="actionEvents">Action</th>
                                    <th data-field="id" data-sortable="true">ID</th>
                                    <th data-field="user_name" data-sortable="true">User Name</th>
                                    <th data-field="user_mobile" data-sortable="true">User Mobile</th>
                                    <th data-field="link" data-sortable="true">Video Link</th>
                                    <th data-field="amount" data-sortable="true">Amount</th>
                                    <th data-field="datetime" data-sortable="true">Datetime</th>
                                    <th data-field="status" data-sortable="true">Status</th>
                                    <th data-field="reason" data-sortable="true">Reason</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </form>
            </div>
            <div class="separator"></div>
        </div>
    </div>
</section>

<script>
    // Toggle the visibility of the reason form
    function showReasonForm() {
        document.getElementById('reason-form').style.display = 'block';
    }

    // Hide error message after 1 second
    window.onload = function() {
        var errorMessage = document.getElementById('error-message');
        if (errorMessage) {
            setTimeout(function() {
                errorMessage.style.display = 'none';
            }, 1000);
        }
    }

    function checkAll(ele) {
        var checkboxes = document.getElementsByTagName('input');
        if (ele.checked) {
            for (var i = 0; i < checkboxes.length; i++) {
                if (checkboxes[i].type == 'checkbox') {
                    checkboxes[i].checked = true;
                }
            }
        } else {
            for (var i = 0; i < checkboxes.length; i++) {
                if (checkboxes[i].type == 'checkbox') {
                    checkboxes[i].checked = false;
                }
            }
        }
    }

    // Automatically refresh table when filters change
    $('#seller_id').on('change', function() {
        $('#products_table').bootstrapTable('refresh');
    });
    $('#community').on('change', function() {
        $('#users_table').bootstrapTable('refresh');
    });
    $('#status').on('change', function() {
        $('#users_table').bootstrapTable('refresh');
    });
    $('#trail_completed').on('change', function() {
        $('#users_table').bootstrapTable('refresh');
    });
    $('#date').on('change', function() {
        $('#users_table').bootstrapTable('refresh');
    });
    $('#referred_by').on('change', function() {
        $('#users_table').bootstrapTable('refresh');
    });
    $('#plan').on('change', function() {
        $('#users_table').bootstrapTable('refresh');
    });
    $('#status').on('change', function() {
        $('#users_table').bootstrapTable('refresh');
    });

    function queryParams(p) {
        return {
            "date": $('#date').val(),
            "seller_id": $('#seller_id').val(),
            "community": $('#community').val(),
            "status": $('#status').val(),
            "trail_completed": $('#trail_completed').val(),
            "referred_by": $('#referred_by').val(),
            "plan": $('#plan').val(),
            "status": $('#status').val(),
            limit: p.limit,
            sort: p.sort,
            order: p.order,
            offset: p.offset,
            search: p.search
        };
    }
</script>
