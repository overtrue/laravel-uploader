<?php $name = isset($name) ? $name : 'avatar' ?>

<div class="file-uploader file-uploader-avatar" id="{{ $id or uniqid('uploader_') }}" data-strategy="{{ $strategy or 'default' }}"  data-items='{!! isset($avatar) ? "[\"{$avatar}\"]" : '[]' !!}' data-form-name="{{ $name }}">
    <div class="file-uploader-items">
        <button class="file-uploader-picker">选择图片</button>
    </div>
</div>