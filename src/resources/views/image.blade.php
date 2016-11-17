<?php $name = isset($name) ? $name : 'image' ?>

<div class="file-uploader file-uploader-image" id="{{ $id or uniqid('uploader_') }}" data-strategy="{{ $strategy or 'default' }}"  data-items='{!! json_encode(isset($$name) ? array_map('asset', $$name) : []) !!}'>
    <div class="file-uploader-items">
        <button class="file-uploader-picker">选择图片</button>
    </div>
</div>