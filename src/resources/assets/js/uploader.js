
export default class Uploader {
    constructor(container, options) {
        if (typeof window.uploader_options === 'undefined') {
            return console.error('Base uploader config "window.uploader_options" not found.');
        }
        options = options || {
            selectors: {}
        }

        this.selectors = Object.assign({
            picker: 'file-uploader-picker',
            items: 'file-uploader-items',
            item: 'file-uploader-item',
            delete_btn: 'file-uploader-delete',
            error: 'file-uploader-error',
            progress_bar: 'file-uploader-progress-bar',
        }, options.selectors);

        this.container = document.querySelector(container);

        if (this.container.dataset.filters) {
            options.filters = JSON.parse(this.container.dataset.filters);
        }

        this.maxItems = this.container.dataset.maxItems || 999;
        this.multiple = options.multi_selection = this.container.hasAttribute('multiple');
        this.itemsContainer = this.container.querySelector('.' + this.selectors.items);
        this.picker = this.createPicker();
        this.formName = (options.form_name || this.container.dataset.formName || 'images') + ((this.multiple) ? '[]' : '');
        this.itemTemplate = options.item_template || this.container.dataset.itemTemplate || '<img src="{URL}" />';
        this.assetBase = options.assetBase || this.container.dataset.assetBase || window.location.origin;
        this.pluploadUploader = this.createPluploadUploader(options);
    }

    init() {
        this.createPresentItems();
        this.pluploadUploader.init();
    }

    createPresentItems() {
        var that = this;
        let items = JSON.parse(this.container.dataset.items || '[]');

        items.forEach(function(url) {
            that.appendItemToContainer(that.createFileItem({
                id: "o_" + Math.random().toString(36).substring(7)
            }, that.renderItemContent({
                url: url
            })));
        });
    }

    createFileItem(file, content) {
        let item = document.createElement('div');
        item.id = file.id;
        item.setAttribute('class', this.selectors.item);
        item.insertAdjacentHTML('beforeEnd', '<a class="' + this.selectors.delete_btn + '" title="删除">&times;</a>');
        item.insertAdjacentHTML('beforeEnd', '<div class="' + this.selectors.progress_bar + '"><span></span></div>');

        if (content && content.toString().length) {
            item.insertAdjacentHTML('beforeEnd', content.toString());
        }

        return this.attachItemEventListeners(item);
    }

    renderItemContent(data, withForm = true) {
        var itemHtml = this.itemTemplate;
        Object.keys(data).forEach(function(key) {
            itemHtml = itemHtml.replace(new RegExp('\{' + key.toUpperCase() + '\}', 'g'), data[key]);
        });

        var relativeUrl = data.relative_url || data.url.replace(this.assetBase.replace(/\/$/, '') + '/', '');

        if (withForm) {
            itemHtml += '<input type="hidden" name="' + this.formName + '" value="' + relativeUrl + '" />';
        }

        return itemHtml;
    }

    appendItemToContainer(item) {
        this.checkReachMaxItemsLimit();

         if (!this.multiple) {
            var items = this.itemsContainer.querySelectorAll('.'+this.selectors.item);
            Array.prototype.forEach.call(items, function(existedItem){
                existedItem.remove();
            });
        }

        let position = this.itemsContainer.querySelector('.' + this.selectors.picker) || null;

        this.itemsContainer.insertBefore(item, position);

        return this.checkReachMaxItemsLimit();
    }

    removeItemFromContainer(item) {
        this.itemsContainer.removeChild(item);

        return this.checkReachMaxItemsLimit();
    }

    getFileItem(file) {
        return this.itemsContainer.querySelector('#' + file.id);
    }

    checkReachMaxItemsLimit() {
        let reached = this.getItemsCount() >= this.maxItems;

        this.picker.style.display = reached ? 'none' : 'inline-block';

        return reached;
    }

    showItemProgress(file) {
        let progressBar = this.getFileItem(file).querySelector('.' + this.selectors.progress_bar);
        progressBar.style.display = 'block';
        progressBar.querySelector('span').style.width = file.percent + '%';
    }

    showItemError(err) {
        if (!this.getFileItem(err.file)) {
            this.appendItemToContainer(this.createFileItem(err.file));
        }
        let item = this.getFileItem(err.file);

        item.querySelector('.' + this.selectors.progress_bar).display = 'none';

        let error = document.createElement('div');
        error.setAttribute('class', this.selectors.error);
        error.innerHTML = "#" + err.code + ": " + err.message;

        item.appendChild(error);
    }

    togglePicker() {
        if (this.picker.style.display != 'none') {
            this.picker.style.display = 'block';
        } else {
            this.picker.style.display = 'none';
        }
    }

    getItemsCount() {
        return this.itemsContainer.querySelectorAll('.' + this.selectors.item).length;
    }

    attachItemEventListeners(item) {
        let that = this;


        item.querySelector('.' + this.selectors.delete_btn).addEventListener('click', function() {
            item.remove();
            that.checkReachMaxItemsLimit();
        });

        return item;
    }

    createPicker() {
        let picker = this.container.querySelector('.' + this.selectors.picker);
        let pickerBtn = document.createElement('div');

        picker.style.position = 'relative';

        pickerBtn.style.position = 'absolute';
        pickerBtn.style.top = pickerBtn.style.bottom = pickerBtn.style.left = pickerBtn.style.right = 0;
        picker.appendChild(pickerBtn);

        return pickerBtn;
    }

    createPluploadUploader(userOptions) {
        let options = Object.assign(window.uploader_options, {
            form_name: this.formName,
            browse_button: this.picker,
            filters: {
                max_file_size: this.container.dataset.maxFileSize || "2mb",
                mime_types: [{
                    title: this.container.dataset.title || "Image files",
                    extensions: this.container.dataset.extensions || "jpg,jpeg,gif,png,bmp"
                }, ]
            },
            drop_element: this.picker,
            multipart_params: {
                strategy: this.container.dataset.strategy || 'default'
            },
            init: this.getPluploadUploaderListeners(),
            container: this.container.querySelector('.' + this.selectors.picker)
        }, userOptions || {});

        return new plupload.Uploader(options);
    }

    getPluploadUploaderListeners() {
        let that = this;

        return {
            FilesAdded: function(up, files) {
                that.checkReachMaxItemsLimit();

                files = files.slice(0, that.maxItems - that.getItemsCount());

                files.forEach(function(file) {
                    that.appendItemToContainer(that.createFileItem(file));
                });

                up.start();
            },

            FileUploaded: function(up, file, result) {
                let response = JSON.parse(result.response);
                var itemHtml = that.renderItemContent(response);

                that.getFileItem(file).insertAdjacentHTML('beforeEnd', itemHtml);
                that.getFileItem(file).removeChild(that.getFileItem(file).querySelector('.' + that.selectors.progress_bar));
            },

            UploadProgress: function(up, file) {
                that.showItemProgress(file);
            },

            Error: function(up, err) {
                that.showItemError(err);
            }
        }
    }
}
window.Uploader = Uploader;