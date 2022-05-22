function previewImages() {
    let story = false;
    if(this.classList.contains('story')) {
        story = true;
    }
    var previewArea = $(document).find(".preview-area");
    if (!$('.edit-gallery-form').length) {
        $(previewArea).empty();
    }
    var fileList = this.files;
    var anyWindow = window.URL || window.webkitURL;
    for (var i = 0; i < fileList.length; i++) {
        //get a blob to play with
        var objectUrl = anyWindow.createObjectURL(fileList[i]);
        // for the next line to work, you need something class="preview-area" in your html
        if(fileList[i].type != 'video/mp4') {
            if(story) {
                $(previewArea).append('<img src="' + objectUrl + '" style="width: 300px; height: 530px" />');
            } else {
                $(previewArea).append('<img src="' + objectUrl + '" width="200px" />');
            }
        } else {
            if(story) {
                $(previewArea).append('<video controls="1" src="' + objectUrl + '" width="300px" height="530px" />');
            } else {
                $(previewArea).append('<video controls="1" src="' + objectUrl + '" width="300px" />');
            }
        }
        // get rid of the blob
        window.URL.revokeObjectURL(fileList[i]);
    }
}
function loadImages(themeID) {
    $.ajax({
        url: domain + "scripts/themes.php",
        method: "post",
        data: {
            action: "theme-images",
            theme: themeID
        },
        success: function(data) {
            $(".preview-area").html(data);
        }
    });
}
function orderUpdate(orderType,data) {
    $.post({
        url: domain + "scripts/ajax.php",
        cache: false,
        dataType: "html",
        data: {
            action: "order-update",
            orderType: orderType,
            orders: data,
        }
    });
}