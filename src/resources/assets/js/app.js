import Uploader from './uploader';

setTimeout(function(){
    if (document.querySelectorAll('.file-uploader').length) {
        var images = document.querySelectorAll('.file-uploader');

        Array.prototype.forEach.call(images, function(uploaderItem) {
            var uploader = new Uploader('#' + uploaderItem.id);
            uploader.init();
        });
    }
}, 100);