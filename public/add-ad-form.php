<?php
include_once('includes/functions.php');
$function = new functions;
include_once('includes/custom-functions.php');
$fn = new custom_functions;

$sql = "SELECT id, name FROM categories ORDER BY id ASC";
$db->sql($sql);
$res = $db->getResult();
date_default_timezone_set('Asia/Kolkata');
?>
<?php
 $ID = $db->escapeString($_GET['id']);
if (isset($_POST['btnAdd'])) {
        $ads = $db->escapeString(($_POST['ads']));
        $error = array();
       
        if (empty($ads)) {
            $error['ads'] = " <span class='label label-danger'>Required!</span>";
        }
       
            if (!empty($ads)) 
            {
                $datetime = date('Y-m-d H:i:s');
                $type = 'ad_bonus';
                $per_code_cost = 0.125;
                $amount = $ads * $per_code_cost;

                $sql = "INSERT INTO transactions (`user_id`,`ads`,`amount`,`datetime`,`type`)VALUES('$ID','$ads','$amount','$datetime','$type')";
                $db->sql($sql);
                $res = $db->getResult();
            
                $sql = "UPDATE `users` SET  `today_ads` = today_ads + $ads,`total_ads` = total_ads + $ads,`earn` = earn + $amount,`balance` = balance + $amount WHERE `id` = $ID";
                $db->sql($sql);
                 $result = $db->getResult();
                 if (!empty($result)) {
                     $result = 0;
                 } else {
                     $result = 1;
                 }
     
                 if ($result == 1) {
                     $error['add_ads'] = "<section class='content-header'>
                                                     <span class='label label-success'>ads Added Successfully</span> </section>";
                 }
                 }

        }
?>
<section class="content-header">
    <h1>Add Ads <small><a href='users.php'> <i class='fa fa-angle-double-left'></i>&nbsp;&nbsp;&nbsp;Back to Users</a></small></h1>
    <?php echo isset($error['add_ads']) ? $error['add_ads'] : ''; ?>
    <ol class="breadcrumb">
        <li><a href="home.php"><i class="fa fa-home"></i> Home</a></li>
    </ol>
</section>
<section class="content">
    <div class="row">
        <div class="col-md-6">
           
            <!-- general form elements -->
            <div class="box box-primary">
                <div class="box-header with-border">

                </div>
                <!-- /.box-header -->
                <!-- form start -->
                <form name="add_ads_form" method="post" enctype="multipart/form-data">
                    <div class="box-body">
                        <div class="row">
                            <div class="form-group">
                                <div class='col-md-8'>
                                    <label for="exampleInputEmail1">Ads</label> <i class="text-danger asterik">*</i><?php echo isset($error['ads']) ? $error['ads'] : ''; ?>
                                    <input type="number" class="form-control" name="ads" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.box-body -->

                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary" name="btnAdd">Add</button>
                    </div>

                </form>

            </div><!-- /.box -->
        </div>
    </div>
</section>

<div class="separator"> </div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.17.0/jquery.validate.min.js"></script>

<?php $db->disconnect(); ?>