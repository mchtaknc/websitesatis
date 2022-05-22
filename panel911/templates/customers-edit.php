<?php
if (!empty($request[2]) && is_numeric($request[2])) {
    $sonuc = $db->prepare("select * from customers where customer_id = ?");
    $sonuc->execute([$request[2]]);
    $sonuc = $sonuc->fetch();
    if (empty($sonuc)) {
        redirect($domain_admin . 'customers');
    }
} else {
    redirect($domain_admin);
}
?>
<div class="subheader py-2 py-lg-6 subheader-solid" id="kt_subheader">
    <div class="container-fluid d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
        <!--begin::Info-->
        <div class="d-flex align-items-center flex-wrap mr-1">
            <!--begin::Page Heading-->
            <div class="d-flex align-items-baseline flex-wrap mr-5">
                <!--begin::Page Title-->
                <h5 class="text-dark font-weight-bold my-1 mr-5">Müşteri Düzenle</h5>
                <!--end::Page Title-->
                <!--begin::Breadcrumb-->
                <ul class="breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2 font-size-sm">
                    <li class="breadcrumb-item text-muted">
                        <a href="<?= $domain_admin; ?>" class="text-muted">Anasayfa</a>
                    </li>
                    <li class="breadcrumb-item text-muted">
                        <a href="<?= $domain_admin.'customers'; ?>" class="text-muted">Müşteriler</a>
                    </li>
                    <li class="breadcrumb-item text-muted">
                        <a href="#" class="text-muted">Müşteri Düzenle</a>
                    </li>
                </ul>
                <!--end::Breadcrumb-->
            </div>
            <!--end::Page Heading-->
        </div>
        <!--end::Info-->
    </div>
</div>

<div class="d-flex flex-column-fluid">
    <!--begin::Container-->
    <div class="container-fluid">
        <div class="card card-custom">
            <div class="card-header">
                <div class="card-title">
                    <h3 class="card-label">Müşteri Düzenle</h3>
                </div>
            </div>
            <div class="card-body">
                <form>
                    <div class="form-group">
                        <label for="type">Hesap Türü</label>
                        <select name="type" id="type" class="form-control form-control-sm">
                            <option value="individual" <?php echo ($sonuc->type == 'individual' ? 'selected' : null) ?>>Bireysel</option>
                            <option value="corporate" <?php echo ($sonuc->type == 'corporate' ? 'selected' : null) ?>>Kurumsal</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="firstname">Ad</label>
                        <input type="text" class="form-control form-control-sm" id="firstname" name="firstname" value="<?=$sonuc->firstname?>" required>
                    </div>
                    <div class="form-group">
                        <label for="lastname">Soyad</label>
                        <input type="text" class="form-control form-control-sm" id="lastname" name="lastname" value="<?=$sonuc->lastname?>" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">Telefon</label>
                        <input type="text" class="form-control form-control-sm" id="phone" name="phone" autocomplete="phoen" value="<?=$sonuc->phone?>" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control form-control-sm" id="email" name="email" autocomplete="email" value="<?=$sonuc->email?>" required>
                    </div>
                    <div class="corporateWrap">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="company">Firma Ünvanı</label>
                                    <input type="text" class="form-control form-control-sm" id="company" name="company" autocomplete="company" value="<?=$sonuc->company_name?>">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="tax_id">Vergi No</label>
                                    <input type="text" class="form-control form-control-sm" id="tax_id" name="tax_id" autocomplete="tax_id" value="<?=$sonuc->tax_id?>">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="tax_office">Vergi Dairesi</label>
                                    <input type="text" class="form-control form-control-sm" id="tax_office" name="tax_office" autocomplete="tax_office" value="<?=$sonuc->tax_office?>">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="password">Şifre</label>
                        <input type="password" class="form-control form-control-sm" id="password" name="password"  autocomplete="new-password">
                    </div>
                    <button type="submit" class="btn btn-primary">Kaydet</button>
                </form>
            </div>
        </div>
    </div>
    <!--end::Container-->
</div>
<script>
    $("#type").change(function(){
        if(this.value == 'corporate') {
            $(".corporateWrap input").attr('required','required');
            $(".corporateWrap").show();
        } else {
            $(".corporateWrap").hide();
            $(".corporateWrap input").removeAttr('required');
        }
    });
    $("#type").trigger('change');
    $("form").submit(function () {
        var form = $(this);
        var formData = new FormData($(this)[0]);
        formData.append('action','edit');
        formData.append('customer','<?=$sonuc->customer_id;?>');
        $.ajax({
            url: "<?= $domain_admin; ?>scripts/customer.php",
            dataType: "json",
            method: "post",
            processData: false,
            contentType: false,
            data: formData,
            success: function (response) {
                toastr.success(response);
            },
            error: function (response) {
                toastr.error(response.responseJSON.message);
            }
        });
        return false;
    });
</script>