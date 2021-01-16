<!DOCTYPE html>
<html>

<?php
include('components/header.php');
include('components/bodytop.php');
?>

<div class="wrapper">
<?php
include('components/nav.php');
include("components/sidebar.php");

if(!isset($_SESSION['user'])){
	header("location: index.php");
}

$profile_info = $db -> select_row("SELECT * FROM `users` WHERE `user_id`=$get_id");

$get_id=0;
if(isset($_GET['user'])){
	$get_id = $db -> escape($_GET['user']);
	if($db -> select("SELECT `user_id` FROM `users` WHERE `user_id`=$get_id") == ""){
		header("location: account.php");
	}else{
		if($db -> select("SELECT `upline` FROM `users` WHERE `user_id`=$get_id") <> $_SESSION['user']['id'] && $current_rank <> "Admin" || $profile_info[0]['is_admin']==1){
			
		}
	}
}else{
	$get_id = $current_uid;
}
$profile_info = $db -> select_row("SELECT * FROM `users` WHERE `user_id`=$get_id");

?>

<div class="content-wrapper">
    <section class="content-header">
        <h1>
            Account
            <small>Update / Edit Profile</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Account</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">


        <div class="row">
            <div class="col-lg-12">
                <div class="box">
                    <div class="box-header">Account Form</div>
                    <div class="box-body">
                        <div class="col-md-6">
                            <form id="profile_form" role="form">

                                <div class="form-group">
                                    <label>Full Name</label>
                                    <input class="form-control" placeholder="Enter Full Name" name="full_name" value="<?php echo htmlspecialchars($profile_info[0]['full_name']);?>">
                                </div>

                                <div class="form-group">
                                    <label>Email</label>
                                    <input class="form-control" type="email" name="email" placeholder="email@example.com" value="<?php echo htmlspecialchars($profile_info[0]['user_email']);?>">
                                </div>
                                <div class="form-group">
                                    <label>Location</label>
                                    <input class="form-control" placeholder="Enter Location" name="location" value="<?php echo htmlspecialchars($profile_info[0]['location']);?>">
                                </div>
                                <div class="form-group">
                                    <label>Mode of Payment (Reseller)</label>
                                    <input class="form-control" placeholder="For Resellers" name="payment" value="<?php echo htmlspecialchars($profile_info[0]['payment']);?>">
                                </div>
                                <div class="form-group">
                                    <label>Contact Info</label>
                                    <input class="form-control" placeholder="Contact Info" name="contact" value="<?php echo htmlspecialchars($profile_info[0]['contact']);?>">
                                </div>
                                <?php if($_SESSION['user']['rank']=="Admin" || $_SESSION['user']['rank']=="Reseller" || $_SESSION['user']['rank']=="Sub Admin"  && $get_id <> $_SESSION['user']['id']){?>
                                    <div class="form-group">
                                        <label>User Type</label>
                                        <select class="form-control" name="type">
                                            <option value="0">Client</option>
                                            <?php
                                            if($_SESSION['user']['rank']=="Admin"){
                                                ?>
                                                <option value="1" <?php echo ($profile_info[0]['is_reseller']==1 ? "selected" : "" )?>>Reseller</option>
                                                <option value="2" <?php echo ($profile_info[0]['is_reseller']==2 ? "selected" : "" )?>>Sub Reseller</option>
                                                <option value="3" <?php echo ($profile_info[0]['is_reseller']==3 ? "selected" : "" )?>>Sub Admin</option>
                                                <option value="4" <?php echo ($profile_info[0]['is_reseller']==9 ? "selected" : "" )?>>Admin</option>
                                                <?php
                                            }elseif($_SESSION['user']['rank']=="Sub Admin"){
                                                ?>
                                                <option value="1" <?php echo ($profile_info[0]['is_reseller']==1 ? "selected" : "" )?>>Reseller</option>
                                                <option value="2" <?php echo ($profile_info[0]['is_reseller']==2 ? "selected" : "" )?>>Sub Reseller</option>
                                                <?php
                                            }elseif($_SESSION['user']['rank']=="Reseller"){
                                                ?>
                                                <option value="2" <?php echo ($profile_info[0]['is_reseller']==2 ? "selected" : "" )?>>Sub Reseller</option>
                                                <?php
                                            }
                                            ?>
                                        </select>
                                    </div>
                                <?php }

                                if($_SESSION['user']['rank']=="Admin" || $_SESSION['user']['rank']=="Reseller" ){?>
                                    <div class="form-group">
                                        <label>Frozen</label>
                                        <select class="form-control" name="frozen">
                                            <option value="0" <?php echo ($profile_info[0]['frozen']==0 ? "selected" : "" )?>>No</option>
                                            <option value="1" <?php echo ($profile_info[0]['frozen']==1 ? "selected" : "" )?>>Yes</option>
                                        </select>
                                    </div>

                                <?php }?>
                                <input type="hidden" name="user_id" value="<?php echo $get_id;?>" >
                                <button type="submit" class="btn btn-primary">Update Profile</button>
                                <br /><br />
                                <div class="alert bg-primary" role="alert" id="error-alert" style="display:none;">
                                    <span class="glyphicon glyphicon-exclamation-sign"></span><span id="alert-message"> Please Login.</span></a>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-6">
                            <form role="form" id="password_form">
                                <input type="hidden" name="user_id" value="<?php echo $get_id;?>" >
                                <div class="form-group">
                                    <label>Old Password</label>
                                    <input type="password" name="current" class="form-control" placeholder="Current Password">
                                </div>
                                <div class="form-group">
                                    <label>New Password</label>
                                    <input id="password" class="form-control" name="password" type="password" pattern="^\S{6,}$" onchange="this.setCustomValidity(this.validity.patternMismatch ? 'Must have at least 6 characters' : ''); if(this.checkValidity()) form.password_two.pattern = this.value;" placeholder="New Password" required>

                                </div>

                                <div class="form-group">
                                    <label>Confirm New Password</label>
                                    <input id="password_two" class="form-control" name="password_two" type="password" pattern="^\S{6,}$" onchange="this.setCustomValidity(this.validity.patternMismatch ? 'Please enter the same Password as above' : '');" placeholder="Verify New Password" required>
                                </div>
                                <button type="submit" class="btn btn-primary">Update Password</button>
                                <br /><br />
                                <div class="alert bg-primary" role="alert" id="error-alert2" style="display:none;">
                                    <span class="glyphicon glyphicon-exclamation-sign"></span><span id="alert-message2"> </span></a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div><!-- /.col-->
        </div><!-- /.row -->


</div>	<!--/.main-->

<?php 
include("components/js.php");
?>
<script>
$("#profile_form").submit(function(event){
	event.preventDefault();
	remove_alert_bg();
	$('#error-alert').addClass("bg-primary");
	$('#error-alert').fadeIn();
	$('#alert-message').text(" Please wait...");
		$.ajax({
			url: "app/account/update_account.php", data: $('#profile_form').serialize(), type: "POST",  dataType: 'json',
			success: function (result) {
						console.log(result.status + " " + result.message);
						if (result.status!=1) { 
							remove_alert_bg();
							$('#error-alert').addClass("alert-danger");
							$('#alert-message').text(result.message);
							setTimeout(function () { $('#error-alert').fadeOut()}, 3000);
						}else{
							remove_alert_bg();
							$('#error-alert').addClass("alert-success");
							$('#alert-message').text(result.message);
							//setTimeout(function () { window.location.assign("index.php");}, 1000);
							setTimeout(function () { $('#error-alert').fadeOut()}, 3000);
						}
					}
		});
		
	console.log('clicked');
});

function remove_alert_bg(){
	$('#error-alert').removeClass("alert-success");
	$('#error-alert').removeClass("alert-primary");
	$('#error-alert').removeClass("alert-danger");
}	

function remove_alert_bg2(){
	$('#error-alert2').removeClass("alert-success");
	$('#error-alert2').removeClass("alert-primary");
	$('#error-alert2').removeClass("alert-danger");
}
function remove_alert_bg3(){
	$('#error-alert3').removeClass("alert-success");
	$('#error-alert3').removeClass("alert-primary");
	$('#error-alert3').removeClass("alert-danger");
}
$("#password_form").submit(function(event){
	event.preventDefault();
	remove_alert_bg2();
	$('#error-alert2').addClass("bg-primary");
	$('#error-alert2').fadeIn();
	$('#alert-message2').text(" Please wait...");
		$.ajax({
			url: "app/account/update_password.php", data: $('#password_form').serialize(), type: "POST",  dataType: 'json',
			success: function (result) {
						console.log(result.status + " " + result.message);
						if (result.status!=1) { 
							remove_alert_bg();
							$('#error-alert2').addClass("alert-danger");
							$('#alert-message2').text(result.message);
							setTimeout(function () { $('#error-alert2').fadeOut()}, 3000);
							$('#password_form')[0].reset();
						}else{
							remove_alert_bg();
							$('#error-alert2').addClass("alert-success");
							$('#alert-message2').text(result.message);
							//setTimeout(function () { window.location.assign("index.php");}, 1000);
							$('#password_form')[0].reset();
							setTimeout(function () { $('#error-alert2').fadeOut()}, 3000);
						}
					}
		});
});

$("#type_form").submit(function(event){
	event.preventDefault();
	remove_alert_bg3();
	$('#error-alert3').addClass("bg-primary");
	$('#error-alert3').fadeIn();
	$('#alert-message3').text(" Please wait...");
		$.ajax({
			url: "app/account/update_password.php", data: $('#type_form').serialize(), type: "POST",  dataType: 'json',
			success: function (result) {
						console.log(result.status + " " + result.message);
						if (result.status!=1) { 
							remove_alert_bg();
							$('#error-alert3').addClass("alert-danger");
							$('#alert-message3').text(result.message);
							setTimeout(function () { $('#error-alert3').fadeOut()}, 3000);
						}else{
							remove_alert_bg();
							$('#error-alert2').addClass("alert-success");
							$('#alert-message2').text(result.message);
							//setTimeout(function () { window.location.assign("index.php");}, 1000);
							setTimeout(function () { $('#error-alert3').fadeOut()}, 3000);
						}
					}
		});
});
    

</script>
<?php
include('components/footer.php');
?>
    <div class="control-sidebar-bg"></div>
</div>
</body>

</html>
