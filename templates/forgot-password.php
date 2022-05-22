<?php
if(isLogged()) {
    redirect($domain);
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
                        <form id="forgotpsd" class="card-body">
                            <h3 class="pb-2">Forgot password</h3>
                            <div class="mail">
                                <input type="email" name="email" autocomplete="new-email">
                                <label>E-Mail Address</label>
                            </div>
                            <div class="submit">
                                <button class="btn btn-primary btn-block">Send</button>
                            </div>
                            <div class="text-center text-dark mb-0">
                                Forget it, <a href="<?=$domain?>login">send me back</a> to the sign in.
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!--/Forgot password-->