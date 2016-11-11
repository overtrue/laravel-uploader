<?php $name = isset($name) ? $name : 'images' ?>

<div class="file-uploader file-uploader-images" id="{{ $id or uniqid('uploader_') }}" data-max-items="{{ $max or 9999 }}" data-strategy="{{ $strategy or 'default' }}" data-form-name="{{ $name }}" data-items='{!! json_encode(isset($$name) ? array_map('asset', $$name) : []) !!}' data-item-template="<img src='{URL}' />">
    <div class="file-uploader-items">
        <button class="file-uploader-picker">+</button>
    </div>
</div>