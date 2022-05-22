<?php isLoggedRedirect() ?>
<!--Breadcrumb-->
<section>
    <div class="bannerimg cover-image bg-background3" data-image-src="<?= $domain ?>assets/images/banners/banner2.jpg">
        <div class="header-text mb-0">
            <div class="container">
                <div class="text-center text-white">
                    <h1 class="">Change Password</h1>
                    <ol class="breadcrumb text-center">
                        <li class="breadcrumb-item"><a href="<?= $domain ?>">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?= $domain ?>my-dashboard">My Dashboard</a></li>
                        <li class="breadcrumb-item active text-white" aria-current="page">Change Password</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</section>
<!--Breadcrumb-->
<!--Section-->
<section class="sptb">
    <div class="container">
        <div class="row">
            <div class="col-xl-3 col-lg-12 col-md-12">
                <?php include "sidebar/dashboard-sidebar.php" ?>
            </div>
            <div class="col-xl-9 col-lg-12 col-md-12">
                <div class="card mb-0">
                    <form action="">
                        <div class="card-header">
                            <h3 class="card-title">Change Password</h3>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label class="form-label">Current Password</label>
                                <input name="currentPassword" type="password" class="form-control"
                                       placeholder="Current Password">
                            </div>
                            <div class="form-group">
                                <label class="form-label">New Password</label>
                                <input name="newPassword" type="password" class="form-control" placeholder="New Password">
                            </div>
                            <div class="form-group">
                                <label class="form-label">New Password Repeat</label>
                                <input name="newPasswordR" type="password" class="form-control"
                                       placeholder="New Password Repeat">
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-success">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
<!--/Section-->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        $("form").submit(function (e) {
            e.preventDefault();
            let form = $(this);
            let formData = new FormData($(form)[0]);
            formData.append('action','change-password');
            $.ajax({
                url: domain + "scripts/user.php",
                method: "post",
                dataType: "json",
                processData: false,
                contentType: false,
                data: formData,
                success: function(response){
                    $(form).trigger('reset');
                    Swal.fire('Success',response.message,'success');
                },
                error: function(response){
                    response = response.responseJSON;
                    Swal.fire('Error',response.message,"error");
                }
            });
        });
    })
</script>