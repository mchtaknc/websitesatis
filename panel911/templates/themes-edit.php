<?php
if (!empty($request[2]) && is_numeric($request[2])) {
    $sonuc = $db->prepare("select * from themes where theme_id = ?");
    $sonuc->execute([$request[2]]);
    $sonuc = $sonuc->fetch();
    if (empty($sonuc)) {
        redirect($domain_admin . 'themes');
    }
    $images = $db->prepare("select * from theme_images where theme_id = ? order by image_order asc");
    $images->execute([$sonuc->theme_id]);
    $images = $images->fetchAll();
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
                <h5 class="text-dark font-weight-bold my-1 mr-5">Tema Düzenle</h5>
                <!--end::Page Title-->
                <!--begin::Breadcrumb-->
                <ul class="breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2 font-size-sm">
                    <li class="breadcrumb-item text-muted">
                        <a href="<?= $domain_admin; ?>" class="text-muted">Anasayfa</a>
                    </li>
                    <li class="breadcrumb-item text-muted">
                        <a href="<?= $domain_admin.'themes'; ?>" class="text-muted">Temalar</a>
                    </li>
                    <li class="breadcrumb-item text-muted">
                        <a href="#" class="text-muted">Tema Düzenle</a>
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
                    <h3 class="card-label">Tema Düzenle</h3>
                </div>
            </div>
            <div class="card-body">
                <form>
                    <div class="form-group">
                        <label for="category">Kategori</label>
                        <select name="category" id="category" class="form-control form-control-sm">
                            <option value="" selected>Kategori Seçin</option>
                            <?php
                            $cats = $db->query("select * from theme_categories")->fetchAll();
                            foreach($cats as $item) {
                                echo '<option value="'.$item->category_id.'" '.($item->category_id == $sonuc->category_id ? 'selected' : null).'>'.$item->name.'</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="name">Tema Adı</label>
                        <input type="text" class="form-control form-control-sm" id="name" name="name" value="<?php echo $sonuc->name ?>">
                    </div>
                    <div class="form-group">
                        <label for="description">Tema Açıklaması</label>
                        <textarea name="description" id="description" class="form-control form-control-sm editorarea" rows="5"><?php echo unclear($sonuc->description) ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="description">Tema Öne Çıkan Özellikleri</label>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="theme-specs">
                                    <div class="input-group input-group-sm">
                                        <input type="text" class="form-control form-control-sm theme-spec ">
                                        <div class="input-group-append">
                                            <span class="input-group-text btn btn-success add"><i class="fa fa-plus"></i></span>
                                        </div>
                                        <div class="check">
                                            <?php
                                                $specs = json_decode(unclear($sonuc->featured_specifications),1);
                                                foreach($specs as $spec) {
                                                    echo '<span>'.$spec['value'].'<i class="fa fa-trash-alt float-right remove-spec"></i></span>';
                                                }
                                            ?>
                                            <input type="hidden" name="featured_specifications" class="spec-input" value="<?php echo $sonuc->featured_specifications ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="price">Tema Fiyat</label>
                        <input type="text" class="form-control form-control-sm" id="price" name="price" value="<?php echo $sonuc->price ?>">
                    </div>
                    <div class="form-group">
                        <div class="preview-area mb-2"></div>
                        <label for="image">Gallery Images</label>
                        <input type="file" class="form-control-file form-control-sm pl-0" id="image" name="image[]" multiple>
                        <span class="form-text text-muted">Max file size is 3MB</span>
                    </div>
                    <div class="form-group">
                        <label for="demo">Demo Linki</label>
                        <input type="url" class="form-control form-control-sm" id="demo" name="demo" value="<?php echo $sonuc->demo_link ?>">
                    </div>
                    <button type="submit" class="btn btn-primary mt-3">Kaydet</button>
                </form>
            </div>
        </div>
    </div>
    <!--end::Container-->
</div>
<script type="text/javascript">
    CKEDITOR.replace('description');
    $("#price").inputmask('currency',{
        rightAlign: false,
        allowMinus: false,
    });
    $(document).on('change','#image',previewImages);
    var array = [];
    if ($(".spec-input").val().length > 3) {
        array = JSON.parse($(".spec-input").val());
    }
    $('.theme-specs .add').click(function(){
        if($('.theme-spec').val().length > 3) {
            var value = $('.theme-spec').val();
            var span = document.createElement('span');
            span.innerHTML = value+' <i class="fa fa-trash-alt float-right remove-spec"></i>'
            array.push({'value':value});
            $('.check').append(span);
            $('.theme-spec').val('');
        }
    });
    $(document).on('click','.remove-spec',function(){
        var value = $(this).parent().text();
        var index = array.map(function (element) { return element.value; }).indexOf(value.trim());
        if (index > -1) {
            array.splice(index, 1);
        }
        $(this).parent().remove();
    });
    $("form").submit(function(){
        if(array.length > 0) {
            $(".spec-input").val(JSON.stringify(array));
        }
    });
    loadImages(<?=$sonuc->theme_id;?>);
    $(document).on('click','.remove-listing-image',function(e){
        e.preventDefault();
        var _this = $(this);
        var theme = $(this).data('theme');
        var image = $(this).data('image');
        $.ajax({
            url: "<?=$domain_admin;?>scripts/themes.php",
            dataType: "json",
            type: "post",
            data: {
                type: "sale",
                action: "remove-theme-image",
                theme: theme,
                image: image
            },
            success: function(response) {
                $(_this).parent().remove();
                loadImages(<?=$sonuc->theme_id;?>);
                toastr.success(response.message);
            },
            error: function(response) {
                toastr.error(response.responseJSON.message);
            }
        });
    });
    $(document).on('click','.main-listing-image',function(e){
        e.preventDefault();
        var _this = $(this);
        var theme = $(this).data('theme');
        var image = $(this).data('image');
        $.ajax({
            url: "<?=$domain_admin;?>scripts/themes.php",
            dataType: "json",
            type: "post",
            data: {
                action: "main-theme-image",
                theme: theme,
                image: image
            },
            success: function(response) {
                $(_this).remove();
                loadImages(<?=$sonuc->theme_id;?>);
                toastr.success(response.message);
            },
            error: function(response) {
                toastr.error(response.responseJSON.message);
            }
        });
    });
    $(".preview-area").sortable({
        update: function() {
            var data = $(this).sortable('toArray');
            orderUpdate('theme_images',data);
        }
    });
    $(".preview-area").disableSelection();
    $("form").submit(function () {
        CKEDITOR.instances['description'].updateElement();
        var form = $(this);
        var formData = new FormData($(form)[0]);
        formData.append('action','edit');
        formData.append('theme',"<?=$sonuc->theme_id;?>");
        $.ajax({
            url: "<?= $domain_admin; ?>scripts/themes.php",
            dataType: "json",
            method: "post",
            processData: false,
            contentType: false,
            data: formData,
            success: function (response) {
                toastr.success(response.message);
                loadImages(<?=$sonuc->theme_id;?>);
            },
            error: function (response) {
                toastr.error(response.responseJSON.message);
            }
        });
        return false;
    });
</script>