const elixir = require('laravel-elixir');

elixir.config.assetsPath = 'src/resources/assets';
elixir.config.publicPath = 'src/public';

var vendors = {
    // 'font-awesome': ['node_modules/font-awesome/css', 'node_modules/font-awesome/fonts'],
    // 'nicescroll': ['node_modules/jquery.nicescroll/jquery.nicescroll.min.js'],
    'plupload': ['src/resources/assets/vendors/plupload/']
};

elixir(mix => {
    mix.sass('uploader.scss')
       .webpack('app.js', elixir.config.publicPath + '/js/uploader.js');

    for (vendor in vendors) {
        vendors[vendor].map(function(elem) {
            mix.copy(elem, 'src/public/vendor/' + vendor + '/' + elem.trim('/').split('/').pop());
        });
    }
});
