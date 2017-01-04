var Uploader = require('./uploader');

setTimeout(function(){
    if (document.querySelectorAll('.file-uploader').length) {
        var images = document.querySelectorAll('.file-uploader');

        images.forEach(function(uploaderItem) {
            var uploader = new Uploader('#' + uploaderItem.id);
            uploader.init();
        });
    }
}, 100);