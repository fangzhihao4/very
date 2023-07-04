function fileUploader(options, callback){
    var uploader = WebUploader.create({
        // 选完文件后，是否自动上传。
        auto: options.auto !== undefined ? options.auto : true,

        // swf文件路径
        swf: './Uploader.swf',

        // 文件接收服务端。
        server: options.server ? options.server : "/upload/image",

        // 选择文件的按钮
        pick: {
            id: options.pick && options.pick.id ? options.pick.id : '.filePicker',
            label: options.pick && options.pick.label ? options.pick.label : '选择图片',
            // 是否开起同时选择多个文件
            multiple: options.pick && options.pick.multiple ? options.pick.multiple : true,
        },

        // 允许选择文件的类型。
        accept: {
            title: options.accept && options.accept.title ? options.accept.title : 'Images',
            extensions: options.accept && options.accept.extensions ? options.accept.extensions : 'gif,jpg,jpeg,bmp,png',
            mimeTypes: options.accept && options.accept.mimeTypes ? options.accept.mimeTypes : 'image/*'
        },

        // 以下参数含义参考 https://fex-team.github.io/webuploader/doc/
        dnd: options.dnd ? options.dnd : undefined,
        disableGlobalDnd: options.disableGlobalDnd ? options.disableGlobalDnd : false,
        paste: options.paste ? options.paste : undefined,
        compress: options.compress ? options.compress : false,
        chunked: options.chunked ? options.chunked : false,
        fileNumLimit: options.fileNumLimit ? options.fileNumLimit : 50,
        fileSizeLimit: options.fileSizeLimit ? options.fileSizeLimit : 20 * 1024 * 1024,
        fileSingleSizeLimit: options.fileSingleSizeLimit ? options.fileSingleSizeLimit : 2 * 1024 * 1024,
        duplicate: options.duplicate ? options.duplicate : true,
        formData: {
            save_path: options.formData && options.formData.save_path ? options.formData.save_path : '',
            img_size: options.formData && options.formData.img_size ? options.formData.img_size : '',
            img_width: options.formData && options.formData.img_width ? options.formData.img_width : '',
            img_height: options.formData && options.formData.img_height ? options.formData.img_height : '',
            img_wh_rate: options.formData && options.formData.img_wh_rate ? options.formData.img_wh_rate : ''
        }
    });

    // 当有文件添加进来的时候
    uploader.on( 'fileQueued', function( file ) {
        if( typeof(eval(callback.fileQueued)) == "function" ) {
            callback.fileQueued(file);
        }
    });

    // 文件上传过程中创建进度条实时显示。
    uploader.on( 'uploadProgress', function( file, percentage ) {
        if( typeof(eval(callback.uploadProgress)) == "function" ) {
            callback.uploadProgress(file);
        }
    });

    // 文件上传成功，给item添加成功class, 用样式标记上传成功。
    uploader.on( 'uploadSuccess', function( file, resp) {
        if( typeof(eval(callback.uploadSuccess)) == "function" ) {
            callback.uploadSuccess(file);
        }
    });

    // 文件上传失败，显示上传出错。
    uploader.on( 'uploadError', function( file ) {
        if( typeof(eval(callback.uploadError)) == "function" ) {
            callback.uploadError(file);
        }
    });

    // 完成上传完了，成功或者失败，先删除进度条。
    uploader.on( 'uploadComplete', function( file ) {
        if( typeof(eval(callback.uploadComplete)) == "function" ) {
            callback.uploadComplete(file);
        }
    });

    return uploader;
}