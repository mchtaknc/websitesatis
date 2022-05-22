<?php
include_once $path . "templates/headerv3.php";
if(isLogged()) {
    redirect($domain);
}
?>
<section class="form-contact-home-section">
    <!-- start contact us section -->
    <div class="container">
        <!-- start container -->
        <div class="row justify-content-center">
            <!-- start row -->
            <form class="col-md-8 row justify-content-center form-contain-home" style="margin-bottom: 100px;" method="post">
                <h5 style="width: 100%;">Kayıt Ol</h5>
                <div class="message w-100"></div>
                <span class="text-muted w-100 mb-4">* ile belirtilen alanlar doldurulması zorunlu alanlardır.</span>
                <div class="col-md-12">
                    <div class="form-group">
                        <input id="individual" class="radio-custom" name="userType" value="individual" type="radio" checked="">
                        <label for="individual" class="radio-custom-label"><span>Bireysel</span></label>
                        <input id="corporate" class="radio-custom" name="userType" value="corporate" type="radio">
                        <label for="corporate" class="radio-custom-label"><span>Kurumsal</span></label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="field input-field">
                        <input class="form-contain-home-input" type="text" id="firstname" name="firstname" required>
                        <span class="input-group-prepend">Ad *</span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="field input-field">
                        <input class="form-contain-home-input" type="text" id="lastname" name="lastname" required>
                        <span class="input-group-prepend">Soyad *</span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="field input-field">
                        <input class="form-contain-home-input phoneNo" type="text" id="phone" name="phone"required>
                        <span class="input-group-prepend">Telefon Numarası *</span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="field input-field">
                        <input class="form-contain-home-input" type="email" id="email" name="email" autocomplete="newemail" required>
                        <span class="input-group-prepend">E-Posta Adresi *</span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="field input-field">
                        <input class="form-contain-home-input" type="password" id="password" name="password" autocomplete="newepass" required>
                        <span class="input-group-prepend">Şifre *</span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="field input-field">
                        <input class="form-contain-home-input" type="password" id="password_confirmation" name="password_confirmation" required>
                        <span class="input-group-prepend">Şifre (Tekrar) *</span>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="corporate row" style="display: none;">
                        <div class="col-md-12">
                            <div class="field input-field">
                                <input class="form-contain-home-input" type="text" id="companyname" name="companyname">
                                <span class="input-group-prepend">Firma Adı</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="field input-field">
                                <input class="form-contain-home-input" type="text" id="tax_office" name="tax_office">
                                <span class="input-group-prepend">Vergi Dairesi</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="field input-field">
                                <input class="form-contain-home-input" type="text" id="tax_id" name="tax_id">
                                <span class="input-group-prepend">Vergi No</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="btn-holder-contect">
                    <button type="submit">KAYIT OL</button>
                </div>
            </form>

        </div>
    </div>
</section>
<script>
    document.addEventListener('DOMContentLoaded',function(){
        $('.phoneNo').inputmask("(999) 999-9999");
        $(".radio-custom").change(function(){
            if(this.value == 'individual') {
                $(".corporate").hide(); 
            } else {
                $(".corporate").show();
            }
        });

        $("form").submit(function(e){
            e.preventDefault();
            var form = $(this);
            var formData = new FormData($(form)[0]);
            $.ajax({
                url: domain + "scripts/register.php",
                method: "post",
                dataType: "json",
                contentType: false,
                processData: false,
                data: formData,
                success: function(response){
                    $(".message").html($("<div/>",{
                        text: response.message,
                        class: "alert alert-success",
                    }));
                    $(form).trigger('reset');
                    setTimeout(function(){
                        window.location.reload();
                    },1500);
                },
                error: function(response) {
                    response = response.responseJSON;
                    $(".message").html($("<div/>",{
                        text: response.message,
                        class: "alert alert-danger",
                    }));
                }
            });
        });
    })
</script>