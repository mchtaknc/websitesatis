<?php
if (!empty($request[2]) && is_numeric($request[2])) {
    $sonuc = $db->prepare("select * from blog_categories where category_id = ?");
    $sonuc->execute([$request[2]]);
    if ($sonuc->rowCount() == 0) {
        redirect($domain_admin . 'blog-categories');
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
                <h5 class="text-dark font-weight-bold my-1 mr-5">Kategori Düzenle</h5>
                <!--end::Page Title-->
                <!--begin::Breadcrumb-->
                <ul class="breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2 font-size-sm">
                    <li class="breadcrumb-item text-muted">
                        <a href="<?= $domain_admin; ?>" class="text-muted">Anasayfa</a>
                    </li>
                    <li class="breadcrumb-item text-muted">
                        <a href="<?= $domain_admin . 'blog-categories'; ?>" class="text-muted">Kategoriler</a>
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
                    <h3 class="card-label">Kategori Düzenle</h3>
                </div>
            </div>
            <div class="card-body">
                <form>
                    <div class="form-group">
                        <label for="title">Kategori Adı</label>
                        <input type="text" class="form-control form-control-sm" id="title" name="title"
                               value="<?= $sonuc->category_name; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="seo_description">Seo Açıklaması</label>
                        <input type="text" class="form-control form-control-sm" id="seo_description" maxlength="160"
                               name="seo_description" value="<?= $sonuc->category_seo_description; ?>" required>
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
        formData.append('action', 'category-edit');
        formData.append('category', '<?=$sonuc->category_id;?>');
        $.ajax({
            url: "<?= $domain_admin; ?>scripts/blog.php",
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