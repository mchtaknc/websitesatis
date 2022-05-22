<?php
debug(1);
if(!isLogged()) {
    redirect($domain);
}
if($request[1] == 'talep' && !isset($request[2])) {
    redirect($domain.'musteri-paneli/destek');
}
$ticket = $db->prepare("select * from tickets where uniqid = ? and customer = ?");
$ticket->execute([$request[2],$_SESSION['front']['customer_id']]);
$ticket = $ticket->fetch();
?>
<div class="tickets nuhost-filter-list-container">
    <div class="tickets-head" style="border-bottom: 1px solid #ddd">
        <h5>Talebi Görüntüle</h5>
    </div>
    <h4 class="pt-3">#<?php echo $ticket->uniqid." - ".$ticket->title ?></h4>
    <button class="btn btn-flat btn-domain-check replybtn"><i class="fa fa-pencil-alt"></i> Cevap Yaz</button>
    <form class="custom-form replyForm mt-4" method="post" style="display: none">
        <div class="form-row">
            <div class="col-md-12">
                <div class="form-group">
                    <label class="col-label-form-sm">Mesaj</label>
                    <textarea cols="5" rows="10" class="form-control form-control-sm" name="message"></textarea>
                </div>
            </div>
        </div>
        <button type="submit" class="btn btn-success btn-sm">Kaydet</button>
    </form>
</div>
<div class="ticket-messages"></div>
<script>
    document.addEventListener('DOMContentLoaded',function(){
        let ticketMessages = function(ticket) {
            $.ajax({
                url: domain + "scripts/user.php",
                method: "post",
                data: {
                    action: "ticket-messages",
                    ticket: ticket
                },
                success: function(response) {
                    $(".ticket-messages").html(response);
                }
            });
        }
        $(".replyForm").submit(function(e){
            e.preventDefault();
            let form = $(this);
            let formData = new FormData(form[0]);
            formData.append('action','ticket-reply-add');
            formData.append('ticket','<?php echo $ticket->ticket_id ?>');
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
                    $(".replybtn").trigger('click');
                    ticketMessages(response.ticket)
                },
                error: function(response) {
                    alertify.error(response.responseJSON.message);
                }
            });
        });
        $(function(){
            ticketMessages('<?php echo $ticket->ticket_id ?>');
            $(".replybtn").click(function(e){
                e.preventDefault();
                $(".replyForm").slideToggle();
            });
        });
    })
</script>