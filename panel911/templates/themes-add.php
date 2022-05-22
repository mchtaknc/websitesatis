<div class="subheader py-2 py-lg-6 subheader-solid" id="kt_subheader">
    <div class="container-fluid d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
        <!--begin::Info-->
        <div class="d-flex align-items-center flex-wrap mr-1">
            <!--begin::Page Heading-->
            <div class="d-flex align-items-baseline flex-wrap mr-5">
                <!--begin::Page Title-->
                <h5 class="text-dark font-weight-bold my-1 mr-5">Tema Ekle</h5>
                <!--end::Page Title-->
                <!--begin::Breadcrumb-->
                <ul class="breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2 font-size-sm">
                    <li class="breadcrumb-item text-muted">
                        <a href="<?= $domain_admin; ?>" class="text-muted">Anasayfa</a>
                    </li>
                    <li class="breadcrumb-item text-muted">
                        <a href="<?= $domain_admin.'themes'; ?>" class="text-muted"> Temalar</a>
                    </li>
                    <li class="breadcrumb-item text-muted">
                        <a href="#" class="text-muted">Tema Ekle</a>
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
                    <h3 class="card-label">Tema Ekle</h3>
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
                                echo '<option value="'.$item->category_id.'">'.$item->name.'</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="name">Tema Adı</label>
                        <input type="text" class="form-control form-control-sm" id="name" name="name">
                    </div>
                    <div class="form-group">
                        <label for="description">Tema Açıklaması</label>
                        <textarea name="description" id="description" class="form-control form-control-sm editorarea" rows="5"></textarea>
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
                                            <input type="hidden" name="featured_specifications" class="spec-input" value="">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="price">Tema Fiyat</label>
                        <input type="text" class="form-control form-control-sm" id="price" name="price">
                    </div>
                    <div class="form-group">
                        <div class="preview-area mb-2"></div>
                        <label for="image">Gallery Images</label>
                        <input type="file" class="form-control-file form-control-sm pl-0" id="image" name="image[]" multiple>
                        <span class="form-text text-muted">Max file size is 3MB</span>
                    </div>
                    <div class="form-group">
                        <label for="demo">Demo Linki</label>
                        <input type="url" class="form-control form-control-sm" id="demo" name="demo">
                    </div>
                    <button type="submit" class="btn btn-primary mt-3">Kaydet</button>
                </form>
            </div>
        </div>
    </div>
    <!--end::Container-->
</div>
<script>
    $("#price").inputmask('currency',{
        rightAlign: false,
        allowMinus: false,
    });
    CKEDITOR.replace('description');
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
    $(document).on('change','#image',previewImages);
    $("form").submit(function () {
        CKEDITOR.instances['description'].updateElement();
        var form = $(this);
        var formData = new FormData($(this)[0]);
        formData.append('action','add');
        $.ajax({
            url: "<?= $domain_admin; ?>scripts/themes.php",
            dataType: "json",
            method: "post",
            processData: false,
            contentType: false,
            data: formData,
            success: function (response) {
                $(form).trigger('reset');
                $(".theme-specs .check").empty();
                $(".preview-area").empty();
                toastr.success(response);
            },
            error: function (response) {
                toastr.error(response.responseJSON.message);
            }
        });
        return false;
    });
</script>