<?php
$datetime = date('Y-m-d H:i:s');

// Process the cancel action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btnCancel']) && !empty($_POST['chk'])) {
    $selectedIds = $_POST['chk']; // Retrieve selected IDs

    foreach ($selectedIds as $id) {
        $id = intval($id); // Ensure the ID is an integer to prevent SQL injection
        $query = "SELECT status FROM youtuber_income WHERE id = $id";
        $db->sql($query);
        $result = $db->getResult();

        if (!empty($result) && $result[0]['status'] == 1) {
            // Set session variable to show error message
            $_SESSION['error'] = "Income with ID $id is already marked as 'Paid'. Cannot change status to 'Rejected'.";
        } elseif (!empty($result) && $result[0]['status'] != 1) {
            // Set session variable to display reason form
            $_SESSION['updateIds'] = $selectedIds;
        }
    }
    echo '<script>window.location.href = "youtube_income.php";</script>';
    exit; // Ensure that script execution stops after echoing JavaScript
}

// Process the update action (reason and status change)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btnUpdate']) && !empty($_POST['reason']) && !empty($_SESSION['updateIds'])) {
    $reason = $_POST['reason'];
    $updateIds = $_SESSION['updateIds'];

    foreach ($updateIds as $id) {
        $id = intval($id); // Ensure the ID is an integer to prevent SQL injection

        // Update status to 2 (Rejected) and set the reason
        $updateQuery = "UPDATE youtuber_income SET status = 2, reason = '$reason' WHERE id = $id";
        $db->sql($updateQuery);
    }

    // Clear session variable after update
    unset($_SESSION['updateIds']);
    echo '<script>window.location.href = "youtube_income.php";</script>';
    exit; // Ensure that script execution stops after echoing JavaScript
}
?>
<style>
    /* Align elements horizontally in Select All and Cancel row */
/* General Flexbox */
.d-flex {
    display: flex;
    flex-wrap: wrap; /* Ensure wrapping for small screens */
}

/* Center and limit Reason form width */
#reason-form {
    display: flex;
    /* Stack elements vertically */
    margin-right: 0;
    margin-top: -20px;
    text-align: left; /* Align text to the left for better readability */
    width: 100%; /* Full width for smaller screens */
}

/* Label Styling */
label {
    display: inline-block; /* Display labels in a row */
    margin-left: 10px; /* Add left margin for better spacing */
    margin-right: 10px; /* Add right margin for better spacing */
    margin-bottom: 5px;
    font-weight: 700;
       margin-top: 15px;
    text-align: left; /* Align text to the left for readability */
}

/* Buttons */
.btn-success {
    color: #fff;
    background-color: #5cb85c;
    border-color: #4cae4c;
    margin-top: 10px; /* Add spacing on top for mobile */
    width: 15%; /* Full width for smaller devices */
        margin-left: 10px;
    
}

/* Select All and Cancel Buttons */
input[type="checkbox"], 
.btn-danger {
    margin: 5px 0; /* Add margin for better spacing */
}

 .form-control-reason{
        width: 300px;
            display: block;
    
    height: 34px;
    padding: 6px 12px;
    font-size: 14px;
    line-height: 1.42857143;
    color: #555;
    background-color: #fff;
    background-image: none;
    border: 1px solid #ccc;
    border-radius: 4px;
    margin-top: 9px;
    }
/* Responsive Design Adjustments */
@media (max-width: 768px) {
    /* Stack elements vertically for mobile */
    .d-flex {
        flex-direction: column;
        align-items: flex-start; /* Align elements to the start */
    }

    /* Adjust Reason form for smaller screens */
    #reason-form {
        margin-top: 15px;
        width: 100%;
        margin-left: 20px;
    }

    /* Adjust buttons */
    .btn-success{
        margin-left: -370px;
        margin-top: 80px;
    }
    .btn-danger {
        width: 100%; /* Full width for smaller devices */
        text-align: center; /* Center-align text on buttons */
    }

    /* Label adjustments for small screens */
    label {
        margin-left: 0; /* Reset left margin */
        text-align: left; /* Align text for better readability */
    }
}

/* Extra Small Devices (e.g., smartphones) */
@media (max-width: 576px) {
    #reason-form {
        font-size: 14px; /* Reduce font size for better spacing */
    }

    .btn-success,
    .btn-danger {
        font-size: 14px; /* Adjust button text size */
        width: 80px;
    }
    .form-control-reason{
        width: 300px;
            display: block;
    
    height: 34px;
    padding: 6px 12px;
    font-size: 14px;
    line-height: 1.42857143;
    color: #555;
    background-color: #fff;
    background-image: none;
    border: 1px solid #ccc;
    border-radius: 4px;
    }
}
.alert {
    padding: 15px;
    margin-bottom: 20px;
    margin-top: 25px;
    border: 1px solid transparent;
    border-radius: 4px;
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
                                <button type="submit" class="btn btn-danger" name="btnCancel" onclick="showReasonForm()">Cancel</button>
                            </div>
                        </div>
                    <?php if (isset($_SESSION['error'])): ?>
                        <div id="error-message" class="alert alert-danger">
                            <?= $_SESSION['error'] ?>
                        </div>
                        <?php unset($_SESSION['error']); ?>
                    <?php endif; ?>

                    <!-- Reason input and update button, initially hidden -->
                    <?php if (isset($_SESSION['updateIds'])): ?>
                        <div id="reason-form" class="form-group" style="width: 50%;">
                            <label for="reason">Reason</label>
                            <input type="text" name="reason" id="reason" class="form-control-reason" required>
                            <button type="submit" class="btn btn-success" name="btnUpdate">Update</button>
                        </div>
                    <?php endif; ?>
                    </div>
                     <div class="form-group col-md-3">
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
