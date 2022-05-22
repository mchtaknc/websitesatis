<div class="account nuhost-filter-list-container">
    <h5>Bilgilerim</h5>
    <form class="custom-form mt-4" action="" method="post">
        <div class="form-group">
            <label class="d-block">Hesap Türü</label>
            <div class="form-check form-check-inline">
                <input class="form-check-input accountType" type="radio" name="accountType" id="inlineRadio1" value="individual" <?php echo ($user->type == 'individual' ? 'checked' : null)?>>
                <label class="form-check-label" for="inlineRadio1">Bireysel</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input accountType" type="radio" name="accountType" id="inlineRadio2" value="corporate" <?php echo ($user->type == 'corporate' ? 'checked' : null)?>>
                <label class="form-check-label" for="inlineRadio2">Kurumsal</label>
            </div>
        </div>
        <div class="form-row">
            <div class="col-md-6">
                <div class="form-group">
                    <label class="col-label-form-sm">Ad</label>
                    <input type="text" class="form-control form-control-sm" name="firstname" value="<?php echo $user->firstname ?>" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label class="col-label-form-sm">Soyad</label>
                    <input type="text" class="form-control form-control-sm" name="lastname" value="<?php echo $user->lastname ?>" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label class="col-label-form-sm">E-Posta</label>
                    <input type="email" class="form-control form-control-sm" name="email" value="<?php echo $user->email ?>" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label class="col-label-form-sm">Telefon Numarası</label>
                    <input type="text" class="form-control form-control-sm phoneNo" name="phonenumber" value="<?php echo $user->phone ?>" required>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label class="col-label-form-sm">Adres</label>
            <input type="text" class="form-control form-control-sm" name="address" value="<?php echo $user->address ?>" required>
        </div>
        <div class="corporate" <?php echo ($user->type == 'individual' ? 'style="display: none;"' : null) ?>>
            <div class="form-row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label class="col-label-form-sm">Firma Adı</label>
                        <input type="text" class="form-control form-control-sm" name="companyname" value="<?php echo $user->company_name ?>">
                    </div>
                </div>
            </div>
            <div class="form-row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-label-form-sm">Vergi Dairesi</label>
                        <input type="text" class="form-control form-control-sm" name="tax_office" value="<?php echo $user->tax_office ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-label-form-sm">Vergi No</label>
                        <input type="text" class="form-control form-control-sm" name="tax_id" value="<?php echo $user->tax_id ?>">
                    </div>
                </div>
            </div>
        </div>
        <button type="submit" class="btn btn-success btn-sm">Kaydet</button>
    </form>
</div>
<script>
    document.addEventListener('DOMContentLoaded',function(){
        $('.phoneNo').inputmask("(999) 999-9999");
        $(".accountType").trigger('change');
        $(".accountType").change(function(){
            if(this.value == 'individual') {
                $(".corporate").hide();
            } else {
                $(".corporate").show();
            }
        });
        $("form").submit(function(e){
            e.preventDefault();
            let form = $(this);
            let formData = new FormData(form[0]);
            formData.append('action','update-information');
            $.ajax({
                url: domain + "scripts/user.php",
                method: "post",
                dataType: "json",
                processData: false,
                contentType: false,
                data: formData,
                success: function(response) {
                    alertify.success(response.message);
                },
                error: function(response) {
                    alertify.error(response.responseJSON.message);
                }
            });
        });
    })
</script>