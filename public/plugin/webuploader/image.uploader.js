function displayTrash(obj) {
    $(obj).children('.trash').css("display","block");
}

function hideTrash(obj) {
    $(obj).children('.trash').css("display","none");
}

function trash(obj) {
    $(obj).remove();
}

$(function () {
    // $list为容器jQuery实例
    $('.filePicker').on('click', function () {
        $list = $(this).siblings('.fileList');
    });

    var uploader = WebUploader.create({

        // 选完文件后，是否自动上传。
        auto: true,

        // swf文件路径
        swf: './Uploader.swf',

        // 文件接收服务端。
        server: "/upload/image",

        // 选择文件的按钮
        pick: {
            label: '选择图片',
            id: '.filePicker',
            // 是否开起同时选择多个文件
            multiple: true,
        },

        // 只允许选择图片文件。
        accept: {
            title: 'Images',
            extensions: 'gif,jpg,jpeg,bmp,png',
            mimeTypes: 'image/*'
        },

        // 不压缩图片
        compress: false,
        // 最多上传50张图片
        fileNumLimit: 50,
        // 总大小最大20M
        fileSizeLimit: 20 * 1024 * 1024,
        // 单图片最大2M
        fileSingleSizeLimit: 5 * 1024 * 1024,
        // 允许上传重复的图片
        duplicate: true,
        // 自定义图片保存路径
        formData: {
            save_path: '',
            img_size: '',
            img_width: '',
            img_height: '',
            img_wh_rate: ''
        }
    });

    // 当有文件添加进来的时候
    uploader.on( 'fileQueued', function( file ) {
        // 自定义的图片保存路径
        uploader.options.formData.save_path = $list.data('save_path');
        // 图片大小
        uploader.options.formData.img_size = $list.data('img_size');
        // 图片宽
        uploader.options.formData.img_width = $list.data('img_width');
        // 图片高
        uploader.options.formData.img_height = $list.data('img_height');
        // 图片宽高比
        uploader.options.formData.img_wh_rate = $list.data('img_wh_rate');
    });

    // 文件上传过程中创建进度条实时显示。
    // uploader.on( 'uploadProgress', function( file, percentage ) {
    //     var $li = $( '#'+file.id ),
    //         $percent = $li.find('.progress span');
    //
    //     // 避免重复创建
    //     if ( !$percent.length ) {
    //         $percent = $('<p class="progress"><span></span></p>')
    //             .appendTo( $li )
    //             .find('span');
    //     }
    //
    //     $percent.css( 'width', percentage * 100 + '%' );
    // });

    // 文件上传成功，给item添加成功class, 用样式标记上传成功。
    uploader.on( 'uploadSuccess', function( file, resp) {
        if (resp.retcode === 200) {
            var name = $list.data('name');
            var multiple = $list.data('multiple');
            if (multiple) {
                name = name + '[]';
            }
            if (!multiple) {
                $list.html('');
            }

            var $li = $(
                '<div style="margin-top: 10px;" onmouseover="displayTrash(this)" onmouseleave="hideTrash(this)" onclick="trash(this)" id="' + file.id + '" class="col-md-3 col-sm-3 col-xs-12">' +
                '<div class="trash" style="display:none;position:absolute;top: 0;color: white;background:black;cursor: pointer;z-index: 1000;">' +
                '<i class="fa fa-trash-o" style="font-size: 20px;"></i>' +
                '</div>' +
                '<img style="width: 100%;height: auto;">' +
                '</div>'
                ),
                $img = $li.find('img');

            var url_host = resp.data.url_host;
            var url_path = resp.data.url_path;
            $list.append( $li );
            $img.attr( 'src', url_host + url_path );

            var html = "<input type='hidden' name='"+name+"' value='"+url_path+"'>";
            $("#"+file.id).append(html);
        } else {
            $( '#'+file.id ).remove();
            alert(resp.msg);
        }
    });

    // 文件上传失败，显示上传出错。
    uploader.on( 'uploadError', function( file ) {
        var $li = $( '#'+file.id ),
            $error = $li.find('div.error');

        // 避免重复创建
        if ( !$error.length ) {
            $error = $('<div class="error col-md-12 col-sm-12 col-xs-12" style="position:absolute;width: '+thumbnailWidth+'px;color: white;font-size: 17px;background: red;text-align: center;top: 0;"></div>').appendTo( $li );
        }

        $error.text('上传失败');
    });

    // 完成上传完了，成功或者失败，先删除进度条。
    // uploader.on( 'uploadComplete', function( file ) {
    //     $( '#'+file.id ).find('.progress').remove();
    // });
});