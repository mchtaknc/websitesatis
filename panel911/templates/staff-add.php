<div class="subheader py-2 py-lg-6 subheader-solid" id="kt_subheader">
    <div class="container-fluid d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
        <!--begin::Info-->
        <div class="d-flex align-items-center flex-wrap mr-1">
            <!--begin::Page Heading-->
            <div class="d-flex align-items-baseline flex-wrap mr-5">
                <!--begin::Page Title-->
                <h5 class="text-dark font-weight-bold my-1 mr-5">Yetkili Ekle</h5>
                <!--end::Page Title-->
                <!--begin::Breadcrumb-->
                <ul class="breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2 font-size-sm">
                    <li class="breadcrumb-item text-muted">
                        <a href="<?= $domain_admin; ?>" class="text-muted">Anasayfa</a>
                    </li>
                    <li class="breadcrumb-item text-muted">
                        <a href="<?= $domain_admin.'staff'; ?>" class="text-muted">Yetkili Listesi</a>
                    </li>
                    <li class="breadcrumb-item text-muted">
                        <a href="#" class="text-muted">Yetkili Ekle</a>
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
                    <h3 class="card-label">Yetkili Ekle</h3>
                </div>
            </div>
            <div class="card-body">
                <form>
                    <div class="form-group">
                        <label for="name">İsim</label>
                        <input type="text" class="form-control form-control-sm" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control form-control-sm" id="email" name="email" autocomplete="email" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Şifre</label>
                        <input type="password" class="form-control form-control-sm" id="password" name="password"  autocomplete="new-password" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Kaydet</button>
                </form>
            </div>
        </div>
    </div>
    <!--end::Container-->
</div>
<script>
    $("form").submit(function () {
        var form = $(this);
        var formData = new FormData($(this)[0]);
        formData.append('action','add');
        $.ajax({
            url: "<?= $domain_admin; ?>scripts/staff.php",
            dataType: "json",
            method: "post",
            processData: false,
            contentType: false,
            data: formData,
            success: function (response) {
                $(form).trigger('reset');
                toastr.success(response);
            },
            error: function (response) {
                toastr.error(response.responseJSON.message);
            }
        });
        return false;
    });
</script>