<?php
if (!empty($request[2]) && is_numeric($request[2])) {
    $sonuc = $db->prepare("select * from tickets where ticket_id = ?");
    $sonuc->execute([$request[2]]);
    $sonuc = $sonuc->fetch();
    if (empty($sonuc)) {
        redirect($domain_admin . 'tickets');
    }
    $customer = $db->query("select * from customers where customer_id = '{$sonuc->customer}'")->fetch();
    $ticket_replies = $db->prepare("select * from ticket_replies where ticket_id = ? order by reply_id asc");
    $ticket_replies->execute([$sonuc->ticket_id]);
    $ticket_replies = $ticket_replies->fetchAll();
} else {
    redirect($domain_admin);
}
?>
<style>
    p {
        margin: 0;
    }
</style>
<div class="subheader py-2 py-lg-6 subheader-solid" id="kt_subheader">
    <div class="container-fluid d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
        <!--begin::Info-->
        <div class="d-flex align-items-center flex-wrap mr-1">
            <!--begin::Page Heading-->
            <div class="d-flex align-items-baseline flex-wrap mr-5">
                <!--begin::Page Title-->
                <h5 class="text-dark font-weight-bold my-1 mr-5">Destek Detayı</h5>
                <!--end::Page Title-->
                <!--begin::Breadcrumb-->
                <ul class="breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2 font-size-sm">
                    <li class="breadcrumb-item text-muted">
                        <a href="<?= $domain_admin; ?>" class="text-muted">Anasayfa</a>
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
        <div class="flex-row-fluid" id="kt_chat_content">
            <div class="card card-custom">
                <!--begin::Header-->
                <div class="card-header align-items-center">
                    <div>
                        <div class="text-dark-75 font-weight-bold font-size-h5">Konu: <?php echo $sonuc->title ?></div>
                    </div>
                    <div class="text-right">
                        <!--begin::Dropdown Menu-->
                        <div class="dropdown dropdown-inline">
                            <button type="button" class="btn btn-clean btn-sm btn-icon btn-icon-md" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							    <span class="svg-icon svg-icon-lg">
							    	<i class="ki ki-bold-more-hor icon-md"></i>
							    </span>
                            </button>
                            <div class="dropdown-menu p-0 m-0 dropdown-menu-right dropdown-menu-md">
                                <!--begin::Navigation-->
                                <ul class="navi navi-hover py-5">
                                    <li class="navi-item">
                                        <a href="javscript:;" class="navi-link statusChange">
										    <span class="navi-icon">
										    	<i class="flaticon2-drop"></i>
										    </span>
                                            <span class="navi-text">Çözüldü olarak güncelle</span>
                                        </a>
                                    </li>
                                </ul>
                                <!--end::Navigation-->
                            </div>
                        </div>
                        <!--end::Dropdown Menu-->
                    </div>
                </div>
                <!--end::Header-->
                <!--begin::Body-->
                <div class="card-body">
                    <!--begin::Scroll-->
                    <div class="scroll scroll-pull" data-mobile-height="350">
                        <!--begin::Messages-->
                        <div class="messages">
                            <!--begin::Message In-->
                            <div class="d-flex flex-column mb-5 align-items-start">
                                <div class="d-flex align-items-center">
                                    <div>
                                        <a href="#" class="text-dark-75 text-hover-primary font-weight-bold"><?php echo $customer->firstname . ' ' . $customer->lastname ?></a>
                                        <span class="text-muted font-size-sm"><?php echo date('d-m-Y H:i:s',strtotime($sonuc->created_at)) ?></span>
                                    </div>
                                </div>
                                <div class="mt-2 rounded p-5 bg-light-success text-dark-50 font-weight-bold font-size-sm text-left max-w-400px"><?php echo nl2br($sonuc->init_msg) ?></div>
                            </div>
                            <div class="message-wrapper"></div>
                        </div>
                        <!--end::Messages-->
                    </div>
                    <!--end::Scroll-->
                </div>
                <!--end::Body-->
                <!--begin::Footer-->
                <div class="card-footer align-items-center">
                    <form>
                    <!--begin::Compose-->
                        <textarea class="form-control border-0 p-0" rows="2" name="message" id="description"></textarea>
                        <button type="submit" class="btn btn-primary btn-sm chat-send py-2 px-6 mt-4">Gönder</button>
                    <!--begin::Compose-->
                    </form>
                </div>
                <!--end::Footer-->
            </div>
        </div>
    </div>
    <!--end::Container-->
</div>
<script>
    CKEDITOR.replace('description');
    function getMessages(ticket) {
        $.ajax({
            url: "<?=$domain_admin?>scripts/tickets.php",
            method: "post",
            dataType: "json",
            data: {
                action: "messages",
                ticket: ticket
            },
            success: function(response) {
                $(".message-wrapper").html(response.html);
            },
            error: function(response) {
                //window.location.href = domain + "tickets";
            }
        });
    }
    getMessages("<?php echo $sonuc->ticket_id ?>")
    $(".statusChange").click(function(e){
        e.preventDefault();
        $.ajax({
            url: "<?=$domain_admin?>scripts/tickets.php",
            method: "post",
            dataType: "json",
            data: {
                action: "status-change",
                status: 1,
                ticket: "<?php echo $sonuc->ticket_id ?>"
            },
            success: function(response) {
                toastr.success(response.message);
            },
            error: function(response) {
                toastr.error(response.responseJSON.message);
            }
        });
    });
    $("form").submit(function () {
        CKEDITOR.instances['description'].updateElement();
        var form = $(this);
        var formData = new FormData($(this)[0]);
        formData.append('action','add-reply');
        formData.append('ticket','<?php echo $sonuc->ticket_id ?>')
        $.ajax({
            url: "<?= $domain_admin; ?>scripts/tickets.php",
            dataType: "json",
            method: "post",
            processData: false,
            contentType: false,
            data: formData,
            success: function (response) {
                getMessages("<?php echo $sonuc->ticket_id ?>");
                CKEDITOR.instances['description'].setData('');
                toastr.success(response);
            },
            error: function (response) {
                toastr.error(response.responseJSON.message);
            }
        });
        return false;
    });
</script>
