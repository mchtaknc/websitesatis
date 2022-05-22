<?php
require_once(__DIR__ . "/../../_com/functions.php");
if (!isLogged('admin')) {
    redirect($domain_admin . 'login');
}
$error['type'] = 0;
$error['message'] = "Parametre Hatalı!";
if (!staffCheck(session('back.staff_id'))) {
    $error['type']++;
}
if (isset($_POST['action']) && $_POST['action'] == 'list') {
    $array['data'] = array();
    if ($error['type'] == 0) {
        $tickets = $db->query("select * from tickets order by ticket_id desc")->fetchAll();
        $array['data'] = array();
        foreach($tickets as $item) {
            $data = array();
            $customer = $db->prepare("select * from customers where customer_id = ?");
            $customer->execute([$item->customer]);
            $customer = $customer->fetch();
            $status = "Açık";
            $classLabel = "label-success";
            if($item->resolved == 1) {
                $status = "Çözüldü";
                $classLabel = "label-warning";
            }
            $html = '<a href="' . $domain_admin . 'tickets/view/' . $item->ticket_id . '" class="btn btn-sm btn-clean btn-icon" title="Görüntüle"><span class="svg-icon svg-icon-md"><i class="la la-eye"></i></span></a>';
            $data[] = $item->uniqid;
            $data[] = $item->title;
            $data[] = $customer->firstname.' '.$customer->lastname;
            $data[] = '<span class="label label-lg font-weight-bold ' . $classLabel . ' label-inline">' . $status . '</span>';
            $data[] = date('d-m-Y',strtotime($item->created_at));
            $data[] = $html;
            $array['data'][] = $data;
        }
        http_response_code(200);
        echo json_encode($array);
    } else {
        http_response_code(400);
        echo json_encode($array);
    }
}
if(isset($_POST['action']) && $_POST['action'] == 'messages') {
    $ticket_id = isset($_POST['ticket']) ? $_POST['ticket'] : 0;
    $ticket = $db->prepare("select * from tickets where ticket_id = ?");
    $ticket->execute([$ticket_id]);
    if($ticket->rowCount() == 0) {
        $error['type']++;
    }
    if($error['type'] == 0) {
        $html = "";
        $replies = $db->query("select * from ticket_replies where ticket_id = '{$ticket_id}'")->fetchAll();
        foreach($replies as $reply) {
            if($reply->user_type == 'customer') {
                $customer = $db->query("select * from customers where customer_id = '{$reply->user}'")->fetch();
                $html .= '<div class="d-flex flex-column mb-5 align-items-start">
                    <div class="d-flex align-items-center">
                        <div>
                            <a href="#" class="text-dark-75 text-hover-primary font-weight-bold">'.$customer->firstname . ' ' . $customer->lastname.'</a>
                            <span class="text-muted font-size-sm">'.date('d-m-Y H:i:s',strtotime($reply->created_at)).'</span>
                        </div>
                    </div>
                    <div class="rounded p-5 bg-light-success text-dark-50 font-weight-bold font-size-sm text-left max-w-400px">'.nl2br($reply->message).'</div>
                </div>';
            } else {
                $staffDetail = $db->query("select * from staff where staff_id = '{$reply->user}'")->fetch();
                $html .= '<div class="d-flex flex-column mb-5 align-items-end">
                    <div class="d-flex align-items-center">
                        <div>
                            <span class="text-muted font-size-sm">'.date('d-m-Y H:i:s',strtotime($reply->created_at)).'</span>
                            <a href="#" class="text-dark-75 text-hover-primary font-weight-bold">'.$staffDetail->name.'</a>
                        </div>
                    </div>
                    <div class="rounded p-5 bg-light-primary text-dark-50 font-weight-bold font-size-sm text-right max-w-400px">'.unclear(nl2br($reply->message)).'</div>
                </div>';
            }
        }
        http_response_code(200);
        echo json_encode(['html' => $html]);
    } else {
        http_response_code(400);
        echo json_encode($error);
    }
}
if (isset($_POST['action']) && $_POST['action'] === 'add-reply') {
    $required = array(
        'message' => 'Mesaj',
    );
    $ticket_id = isset($_POST['ticket']) ? $_POST['ticket'] : 0;
    $ticket = $db->prepare("select * from tickets where ticket_id = ?");
    $ticket->execute([$ticket_id]);
    if ($ticket->rowCount() == 0) {
        $error['type']++;
    }
    requireCheck($required);
    if ($error['type'] == 0) {
        $query = $db->prepare("insert into ticket_replies (user,user_type,message,ticket_id) values (?,?,?,?)")->execute([
            $_SESSION['back']['staff_id'],
            'staff',
            $_POST['message'],
            $ticket_id
        ]);
        if ($query) {
            http_response_code(200);
            echo json_encode('Cevabınız gönderildi.');
        } else {
            http_response_code(400);
            echo json_encode(['message' => 'Bir şeyler ters gitti.']);
        }
    } else {
        http_response_code(400);
        echo json_encode($error);
    }
}
if (isset($_POST['action']) && $_POST['action'] === 'status-change') {
    $ticket_id = isset($_POST['ticket']) ? $_POST['ticket'] : 0;
    $ticket = $db->prepare("select * from tickets where ticket_id = ?");
    $ticket->execute([$ticket_id]);
    if ($ticket->rowCount() == 0) {
        $error['type']++;
    }
    if ($error['type'] == 0) {
        $query = $db->query("update tickets set resolved = '{$_POST['status']}' where ticket_id = '{$ticket_id}'");
        if ($query) {
            http_response_code(200);
            echo json_encode(['message' => 'Talep çözüldü olarak güncellendi.']);
        } else {
            http_response_code(400);
            echo json_encode(['message' => 'Bir şeyler ters gitti.']);
        }
    } else {
        http_response_code(404);
        echo json_encode($error);
    }
}