<?php
if (isLogged() || $_GET['code'] == '') {
    redirect($domain);
} else {
    $code = $_GET['code'];
    $sorgu = mysqli_query($db,"select * from oa_user where reset_password = '{$code}'");
    if(mysqli_num_rows($sorgu) == 0) {
        redirect($domain);
    }
}
?>
<!--Section-->
<section>
    <div class="bannerimg cover-image bg-background3" data-image-src="<?=$domain?>assets/images/banners/banner1.jpg">
        <div class="header-text mb-0">
            <div class="container">
                <div class="text-center text-white ">
                    <h1 class="">Forgot Password</h1>
                    <ol class="breadcrumb text-center">
                        <li class="breadcrumb-item"><a href="<?=$domain?>">Home</a></li>
                        <li class="breadcrumb-item active text-white" aria-current="page">Forgot Password</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</section>
<!--Section-->
<!--Forgot password-->
<section class="sptb">
    <div class="container">
        <div class="row">
            <div class="col-lg-5 col-xl-4 col-md-6 d-block mx-auto">
                <div class="single-page w-100 p-0" >
                    <div class="wrapper wrapper2">
                        <form class="card-body">
                            <h3 class="pb-2">Reset Password</h3>
                            <div class="mail">
                                <input type="password" name="newPassword" autocomplete="new-password">
                                <label>New Password *</label>
                            </div>
                            <div class="mail">
                                <input type="password" name="newPasswordR" autocomplete="new-password">
                                <label>New Password Repeat *</label>
                            </div>
                            <div class="submit">
                                <button class="btn btn-primary btn-block">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!--/Forgot password-->
<script>
    document.addEventListener('DOMContentLoaded',function(){
        $("form").submit(function (e) {
            e.preventDefault();
            var _el = $(this);
            var formData = new FormData($(this)[0]);
            formData.append('code','<?=$_GET['code']?>');
            $.ajax({
                url: domain + "scripts/reset-password.php",
                method: "post",
                dataType: "json",
                contentType: false,
                processData: false,
                data: formData,
                success: function (response) {
                    Swal.fire('Success',response.message,'success');
                    setTimeout(function () {
                        window.location.href = domain;
                    }, 1500)
                },
                error: function (response) {
                    response = response.responseJSON;
                    Swal.fire('Error',response.message,'error');
                }
            });
        });
    });
</script>