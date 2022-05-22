<?php isLoggedRedirect('admin'); ?>
<div class="subheader py-2 py-lg-6 subheader-solid" id="kt_subheader">
    <div class="container-fluid d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
        <!--begin::Info-->
        <div class="d-flex align-items-center flex-wrap mr-1">
            <!--begin::Page Heading-->
            <div class="d-flex align-items-baseline flex-wrap mr-5">
                <!--begin::Page Title-->
                <h5 class="text-dark font-weight-bold my-1 mr-5">Profil</h5>
                <!--end::Page Title-->
                <!--begin::Breadcrumb-->
                <ul class="breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2 font-size-sm">
                    <li class="breadcrumb-item text-muted">
                        <a href="<?= $domain_admin; ?>" class="text-muted">Anasayfa</a>
                    </li>
                    <li class="breadcrumb-item text-muted">
                        <a href="#" class="text-muted">Profil</a>
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
                    <h3 class="card-label">Profil</h3>
                </div>
            </div>
            <div class="card card-custom">
                <div class="card-header">

                    <div class="card-toolbar">
                        <ul class="nav nav-light-success nav-bold nav-pills">
                            <li class="nav-item">
                                <a class="nav-link active" data-toggle="tab" href="#info">
                                    <span class="nav-text">Profil Bilgileri</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#info-pass">
                                    <span class="nav-text">Şifre Değiştir</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="card-body">
                    <div class="tab-content">
                        <div class="tab-pane fade active show" id="info" role="tabpanel" aria-labelledby="info">
                            <form>
                                <div class="form-group">
                                    <label for="name">İsim</label>
                                    <input type="text" class="form-control form-control-sm" id="name" name="name" value="<?=$staff->name?>" required="">
                                </div>
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" class="form-control form-control-sm" id="email" name="email" value="<?=$staff->email?>" required="">
                                </div>
                                <input type="hidden" name="action" value="update-information">
                                <button type="submit" class="btn btn-primary">Kaydet</button>
                            </form>
                        </div>
                        <div class="tab-pane fade" id="info-pass" role="tabpanel" aria-labelledby="info-pass">
                            <form method="post">
                                <div class="form-group">
                                    <label for="current_pass">Geçerli Şifre</label>
                                    <input type="password" class="form-control form-control-sm" id="current_pass" name="current_pass" required="">
                                </div>
                                <div class="form-group">
                                    <label for="new_pass">Yeni Şifre</label>
                                    <input type="password" class="form-control form-control-sm" id="new_pass" name="new_pass" required="">
                                </div>
                                <div class="form-group">
                                    <label for="new_pass_r">Yeni Şifre (Tekrar)</label>
                                    <input type="password" class="form-control form-control-sm" id="new_pass_r" name="new_pass_r" required="">
                                </div>
                                <input type="hidden" name="action" value="change-password">
                                <button type="submit" class="btn btn-primary">Kaydet</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end::Container-->
</div>
<script>
    $("form").submit(function () {
        var form = $(this);
        var formData = new FormData($(form)[0]);
        $.ajax({
            url: "<?= $domain_admin; ?>scripts/ajax.php",
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