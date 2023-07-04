/**
 * 上传图片js
 * @param upload_id 上传图片时绑定的按钮的ID值
 * @param upload_img_path   上传图片的控制器路径
 * @param obj_value         上传图片后需要保存在哪个input对象的value中
 * @param obj_show          上传图片后需要显示的在哪个img对象的src中
 */
var file_upload_loading;
function qqUpload(upload_id, upload_img_path, obj_value, obj_show) {
    var upload_main_img = new qq.FileUploaderBasic({
        allowedExtensions: ['jpg','gif','png','jpeg','JPG','GIF','PNG','JPEG','mp4'],
        button: document.getElementById(upload_id),
        multiple: false,
        action: upload_img_path,
        inputName: 'img',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        forceMultipart: true, //用$_FILES
        messages: {
            typeError: '不允许上传的文件类型！',
            sizeError: '文件大小不能超过0.5M！',
            minSizeError: '文件大小不能小于0！',
            emptyError: '请文件为空，请重新选择！',
            noFilesError: '没有选择要上传的文件！',
            onLeave: '正在上传文件，离开此页将取消上传！'
        },
        onSubmit: function(id, fileName){
            file_upload_loading = layer.load(1, {
                shade: [0.5,'#fff'] //0.1透明度的白色背景
            });
        },
        onComplete: function(id, fileName, result){
            layer.close(file_upload_loading);
            layer.msg(result.msg, {time:1000});

            obj_value.val(result.data.img_path);
            obj_show.attr("src",result.data.img_path);
        },
        onError: function(id, fileName, reason) {
            layer.close(file_upload_loading);
            layer.msg("上传错误,请重试");
        },
    });
}

function zoneMoreUpload(drop_main_zone, upload_img_path) {
    var drop_zone_id = new Dropzone(drop_main_zone, {
        url: upload_img_path,
        method: "post",
        paramName:"img",
        maxFiles: 1,
        maxFilesize: 0.5,
        autoProcessQueue:true,
        addRemoveLinks:true,
        dictDefaultMessage:"",
        acceptedFiles: ".jpg,.jpeg,.png",
        dictRemoveFile: "删除",
        dictMaxFilesExceeded:"",
        init: function() {
            this.on("success", function(file, res) {
                file.path = res.data.img_path;
                $(drop_main_zone+" .dz-preview:last-child").append("<input type='hidden' name='img_list[]' value='"+res.data.img_path+"'>");
                if( res.retcode != 200 ){
                    layer.msg(res.msg, {time:1000});
                    this.removeFile(file);
                }
            });
            this.on("error", function (file, message) {
                layer.msg(message, {icon:2, time:1000});
                this.removeFile(file);
            });
            this.on("removedfile", function(file) {

            });
        }
    });
}

var file_sort_loading;
function dragSort(class_name, sort_path){
    //拖动排序
    var $dom = $(class_name);
    $dom.sortable({
        cursor: "move",
        containmentType:"parent",
        opacity: 0.6, //拖动时，透明度为0.6
        start:function(event, ui){//开始排序时调用

        },
        update: function(event, ui) { //更新排序完成之后
            file_sort_loading = layer.load(1, {
                shade: [0.5,'#fff'] //0.1透明度的白色背景
            });
            var news = $(this).sortable("toArray");//新的顺序
            var $this = $(this);
            $.ajax({
                url: sort_path,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                dataType:'json',
                traditional:true,
                data: {news:JSON.stringify(news)},
                success: function(data) {
                    layer.close(file_sort_loading);
                    if(data.retcode == 200){
                        console.log(data);
                        layer.msg('更新成功', {icon:1, time:1000});
                    }else{
                        $this.sortable("cancel");
                        layer.msg(data.msg);
                    }
                }
            });
        }
    });
    $dom.disableSelection();
}