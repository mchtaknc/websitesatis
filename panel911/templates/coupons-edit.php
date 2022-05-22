<?php
if (!empty($request[2]) && is_numeric($request[2])) {
    $sonuc = $db->prepare("select * from coupons where id = ?");
    $sonuc->execute([$request[2]]);
    if ($sonuc->rowCount() == 0) {
        redirect($domain_admin . 'coupons');
    }
    $sonuc = $sonuc->fetch();
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
                <h5 class="text-dark font-weight-bold my-1 mr-5">Kupon Düzenle</h5>
                <!--end::Page Title-->
                <!--begin::Breadcrumb-->
                <ul class="breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2 font-size-sm">
                    <li class="breadcrumb-item text-muted">
                        <a href="<?= $domain_admin; ?>" class="text-muted">Anasayfa</a>
                    </li>
                    <li class="breadcrumb-item text-muted">
                        <a href="<?= $domain_admin . 'coupons'; ?>" class="text-muted">Kuponlar</a>
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
                    <h3 class="card-label">Kupon Düzenle</h3>
                </div>
            </div>
            <div class="card-body">
                <form>
                    <div class="form-group">
                        <label for="code">Kupon Kodu</label>
                        <input type="text" class="form-control form-control-sm" id="code" name="code" value="<?php echo $sonuc->code ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="discount">İndirim Tutar</label>
                        <input type="text" class="form-control form-control-sm" id="discount" name="discount" value="<?php echo $sonuc->value ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="maxUsage">Max Kullanım</label>
                        <input type="text" class="form-control form-control-sm" id="maxUsage" name="maxUsage" value="<?php echo $sonuc->max_usage ?>" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Kaydet</button>
                </form>
            </div>
        </div>
    </div>
    <!--end::Container-->
</div>
<script>
    $("#discount").inputmask('currency',{
        rightAlign: false,
        allowMinus: false,
    });
    $("form").submit(function () {
        var form = $(this);
        var formData = new FormData($(this)[0]);
        formData.append('action', 'edit');
        formData.append('coupon', '<?=$sonuc->id;?>');
        $.ajax({
            url: "<?= $domain_admin; ?>scripts/coupon.php",
            dataType: "json",
            method: "post",
            processData: false,
            contentType: false,
            data: formData,
            success: function (response) {
                toastr.success(response.message);
            },
            error: function (response) {
                toastr.error(response.responseJSON.message);
            }
        });
        return false;
    });
</script>