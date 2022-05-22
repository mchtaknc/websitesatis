<?php
$kategoriler = $db->query("select * from blog_categories where status = 'published'")->fetchAll();
?>
<div class="subheader py-2 py-lg-6 subheader-solid" id="kt_subheader">
    <div class="container-fluid d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
        <!--begin::Info-->
        <div class="d-flex align-items-center flex-wrap mr-1">
            <!--begin::Page Heading-->
            <div class="d-flex align-items-baseline flex-wrap mr-5">
                <!--begin::Page Title-->
                <h5 class="text-dark font-weight-bold my-1 mr-5">Blog Ekle</h5>
                <!--end::Page Title-->
                <!--begin::Breadcrumb-->
                <ul class="breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2 font-size-sm">
                    <li class="breadcrumb-item text-muted">
                        <a href="<?= $domain_admin; ?>" class="text-muted">Anasayfa</a>
                    </li>
                    <li class="breadcrumb-item text-muted">
                        <a href="<?= $domain_admin.'blog'; ?>" class="text-muted">Blog</a>
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
                    <h3 class="card-label">Blog Ekle</h3>
                </div>
            </div>
            <div class="card-body">
                <form>
                    <div class="form-group">
                        <label for="name">Kategori</label>
                        <select class="form-control form-control-sm" name="category" required>
                            <option value="">Seçiniz...</option>
                            <?php
                            foreach($kategoriler as $kategori) {
                                echo '<option value="'.$kategori->category_id.'">'.$kategori->category_name.'</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="title">Başlık</label>
                        <input type="text" class="form-control form-control-sm" id="title" name="title" required>
                    </div>
                    <div class="form-group">
                        <label for="description">İçerik</label>
                        <textarea name="description" id="description" class="form-control form-control-sm editorarea"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="seo_description">Seo Açıklaması</label>
                        <input type="text" class="form-control form-control-sm" id="seo_description" maxlength="160" name="seo_description" required>
                    </div>
                    <div class="form-group">
                        <div class="preview-area mb-2"></div>
                        <label for="featured_image">Öne Çıkan Resim</label>
                        <input type="file" class="form-control-file form-control-sm" id="featured_image" name="featured_image" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Kaydet</button>
                </form>
            </div>
        </div>
    </div>
    <!--end::Container-->
</div>
<script>
    CKEDITOR.replace('description');
    $(document).on('change','#featured_image',previewImages);
    $("form").submit(function () {
        CKEDITOR.instances['description'].updateElement();
        var form = $(this);
        var formData = new FormData($(this)[0]);
        formData.append('action','add');
        $.ajax({
            url: "<?= $domain_admin; ?>scripts/blog.php",
            dataType: "json",
            method: "post",
            processData: false,
            contentType: false,
            data: formData,
            success: function (response) {
                $(form).trigger('reset');
                toastr.success(response.message);
            },
            error: function (response) {
                toastr.error(response.responseJSON.message);
            }
        });
        return false;
    });
</script>