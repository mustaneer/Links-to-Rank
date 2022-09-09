jQuery(function ($) {
    $("#file").change(function (e) {
        var img = e.target.files[0];

        if (!iEdit.open(img, true, function (res) {
            $("#result").attr("src", res);
        })) {
            alert("Whoops! That is not an image!");
        }

    });

    $('a.btn.yes.btn-primary').click(function(){
        var img_src = $(".awesome-cropper > img").attr("src");
        $(".employ_image").attr("src",img_src);
    });

});

function get_downloaded_image() {
    var $ = jQuery;
    var caption = "Download"; //Downlaod File Name

    html2canvas($("#main_form_preview"), {
        dpi: 100000,
        onrendered: function (canvas) {

            $("#blank").attr('href', canvas.toDataURL("image/png"));
            $("#blank").attr('download', caption + '.png');
            $("#blank")[0].click();

        }
    });
}

function getBase64Image(img) {
    var $ = jQuery;
    var canvas = document.createElement("canvas");
    canvas.width = img.width;
    canvas.height = img.height;
    var ctx = canvas.getContext("2d");
    ctx.drawImage(img, 0, 0);
    var dataURL = canvas.toDataURL("image/png");
    return dataURL.replace(/^data:image\/(png|jpg);base64,/, "");
}