import Uploader from './uploader';

setTimeout(function(){
    if (document.querySelectorAll('.file-uploader').length) {
        var images = document.querySelectorAll('.file-uploader');

<<<<<<< HEAD
        images.forEach(function(uploaderItem) {
=======
        Array.prototype.forEach.call(images, function(uploaderItem) {
>>>>>>> 7ccb309e696247b601b2a3e3f1e191543dfb8cdf
            var uploader = new Uploader('#' + uploaderItem.id);
            uploader.init();
        });
    }
}, 100);