<div class="tickets nuhost-filter-list-container">
    <div class="tickets-head">
        <h5>Yeni Talep Oluştur</h5>
    </div>
    <form class="custom-form mt-4" method="post">
        <div class="form-group">
            <label class="col-label-form-sm">Konu</label>
            <input type="text" class="form-control form-control-sm" name="subject" value="">
        </div>
        <?php
        /*<div class="form-group">
            <?php
            $orders = $db->prepare("select * from orders where customer_id = ? and status = ?");
            $orders->execute([$_SESSION['front']['customer_id'],'success']);
            $orders = $orders->fetchAll();
            ?>
            <label class="col-label-form-sm">İlişkili Hizmet</label>
            <select class="form-control form-control-sm" name="service">
                <option value="0">Yok</option>
                <?php foreach($orders as $item) { ?>
                    <option value="<?php echo $item->order_id ?>"><?php echo $item->order_no ?></option>
                <?php } ?>
            </select>
        </div>*/
        ?>
        <div class="form-group">
            <label class="col-label-form-sm">Mesaj</label>
            <textarea cols="5" rows="10" class="form-control form-control-sm" name="message"></textarea>
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
           formData.append('action','ticket-add');
           $.ajax({
               url: domain + "scripts/user.php",
               method: "post",
               dataType: "json",
               processData: false,
               contentType: false,
               data: formData,
               success: function(response) {
                   alertify.success(response.message);
                   $(form).trigger('reset');
                   window.location.href = domain + "musteri-paneli/talep/" + response.ticket
               },
               error: function(response) {
                   alertify.error(response.responseJSON.message);
               }
           });
       });
    });
</script>