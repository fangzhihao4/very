
<div class="clearfix"></div>

<div class="row">
    <div class="col-md-<?php echo isset($width) ? $width : '12' ?> col-sm-12 col-xs-12">
        <div class="x_panel">
            <div class="x_title">
                <h2>可上传多个文件</h2>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
                <p>可拖拽多个文件到下面区域,也可选择单个文件</p>
                <form action="<?php echo $action;?>" class="dropzone" id="myDropzone">
                    <?php echo csrf_field();?>
                </form>
                <br />
                <br />
                <br />
                <br />
            </div>
        </div>
    </div>
</div>
<div class="modal fade docs-cropped" id="getCroppedCanvasModal" aria-hidden="true" aria-labelledby="getCroppedCanvasTitle" role="dialog" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="getCroppedCanvasTitle">提示框</h4>
            </div>
            <div class="modal-body" style="text-align: center;line-height: 100px;">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
            </div>
        </div>
    </div>
</div>

<script src="/js/dropzone.js"></script>
<script>
    Dropzone.prototype._finished = function(files, responseText, e) {
        if(responseText.retcode!='200'){
            $('#getCroppedCanvasModal').modal().find('.modal-body').html(responseText.msg);
            return;
        }
        var file, _i, _len;
        for (_i = 0, _len = files.length; _i < _len; _i++) {
            file = files[_i];
            file.status = Dropzone.SUCCESS;
            this.emit("success", file, responseText, e);
            this.emit("complete", file);
            $('#getCroppedCanvasModal').modal().find('.modal-body').html(responseText.msg);
        }
        if (this.options.uploadMultiple) {
            this.emit("successmultiple", files, responseText, e);
            this.emit("completemultiple", files);
        }
        if (this.options.autoProcessQueue) {
            return this.processQueue();
        }

    };

</script>
