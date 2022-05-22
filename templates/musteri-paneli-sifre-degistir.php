<div class="account nuhost-filter-list-container">
    <h5>Şifre Değiştir</h5>
    <form class="custom-form mt-4" method="post">
        <div class="form-group">
            <label class="col-label-form-sm">Eski Şifre</label>
            <input type="password" class="form-control form-control-sm " name="current_password">
        </div>
        <div class="form-group">
            <label class="col-label-form-sm">Yeni Şifre</label>
            <input type="password" class="form-control form-control-sm " name="new_password">
        </div>
        <div class="form-group">
            <label class="col-label-form-sm">Yeni Şifre Tekrar</label>
            <input type="password" class="form-control form-control-sm " name="new_password_confirmation">
        </div>
        <button type="submit" class="btn btn-success btn-sm">Kaydet</button>
    </form>
</div>
<script>
    document.addEventListener('DOMContentLoaded',function(){
        $("form").submit(function(e){
            e.preventDefault();
            let form = $(this);
            let formData = new FormData(form[0]);
            formData.append('action','change-password')
            $.ajax({
                url: domain + "scripts/user.php",
                method: "post",
                processData: false,
                contentType: false,
                dataType: "json",
                data: formData,
                success: function(response) {
                    alertify.success(response.message);
                    $(form).trigger('reset');
                },
                error: function(response) {
                    toastr.error(response.responseJSON.message,'Hata');
                }
            });
        });
    })
</script>