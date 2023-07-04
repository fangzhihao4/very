/**
 * Created by wupei on 18/10/18.
 */
var offset_x = '33%';
var offset_y = '39%';
var dialogs_path = '/Presales/dialogs/editor';
var layer = null;
//打开模态框
function openText(type){
    layer.close(layer.index);

    if(type == 1){
        addText();
    }

    var active = $('.active.text').find('.child');
    var color = active.attr('color');
    var fontsize = active.attr('fontsize');
    var text = saveTextFormat(active.attr('content'));
    var textalign = active.attr('textalign');

    var ifram_url = dialogs_path+'?page=text';
    layer.open({
        type: 2,
        title: '文本',
        maxmin: true,
        anim: 3,
        shadeClose: true, //点击遮罩关闭层
        shade: 0,//取消遮罩层
//            move: false,//禁止拖拽
        btn:['确认','取消'],
        offset: [offset_x, offset_y],
        area : ['500px' , '400px'],
        content: ifram_url,
        success: function(layero, index){//层弹出后回调
            setTimeout(function () {
                var body = layer.getChildFrame('body', index);
                body.find('input[name=color]').val(color);
                body.find('#text').val(text);
                body.find('input[name=fontsize]').prop("checked",false);
                body.find('input[name=fontsize][value='+fontsize+']').prop("checked",true);
                body.find('input[name=textalign]').prop("checked",false);
                body.find('input[name=textalign][value='+textalign+']').prop("checked",true);
                var iframeWin = window[layero.find('iframe')[0]['name']]; //得到iframe页的窗口对象，执行iframe页的方法：iframeWin.method();
                iframeWin.setColorPicker(color);

                layer.iframeAuto(index);
            },200);
        },
        yes: function(index, layero){
            var iframeWin = window[layero.find('iframe')[0]['name']]; //得到iframe页的窗口对象，执行iframe页的方法：iframeWin.method();
            if(iframeWin.validatorResult()){
                var body = layer.getChildFrame('body', index);
                var text_content = body.find('#text').val();//获取ifram的textarea元素值
                var color = body.find('input[name=color]').val();
                var fontsize = body.find('input[name=fontsize]:checked').val();
                var textalign = body.find('input[name=textalign]:checked').val();
                setText(text_content,color,fontsize,textalign);
            }
            layer.close(index);
        }
    });

    //如果你想关闭最新弹出的层，直接获取layer.index即可
//        layer.close(layer.index); //它获取的始终是最新弹出的某个层，值是由layer内部动态递增计算的
}
function openImg(type){
    layer.close(layer.index);

    if(type == 1){
        addImg();
    }
    var active = $('.active.img').find('img');
    var img_url = active.attr('src');

    var ifram_url = dialogs_path+'?page=img';
    layer.open({
        type: 2,
        title: '图片',
        maxmin: true,
        anim: 3,
        shadeClose: true, //点击遮罩关闭层
        shade: 0,//取消遮罩层
//            move: false,//禁止拖拽
        btn:['确认','取消'],
        offset: [offset_x, offset_y],
        area : ['500px' , '400px'],
        content: ifram_url,
        success: function(layero, index){//层弹出后回调
            setTimeout(function () {
                var body = layer.getChildFrame('body', index);
                body.find('#imgview').attr({"src":img_url});
                layer.iframeAuto(index);
            },200);
        },
        yes: function(index, layero){
            var body = layer.getChildFrame('body', index);
            var img_url = body.find('#imgview').attr('src');
            setImg(img_url);
            layer.close(index);
        },
        cancel: function(index, layero){
//                if(confirm('确定要关闭么')){ //只有当点击confirm框的确定时，该层才会关闭
//                    layer.close(index)
//                }
//                return false;
        },
        end: function(){//层销毁后回调（右上角关闭或确认或取消）

        }
    });

    //如果你想关闭最新弹出的层，直接获取layer.index即可
//        layer.close(layer.index); //它获取的始终是最新弹出的某个层，值是由layer内部动态递增计算的
}
function openMultiImg(type){
    layer.close(layer.index);

    if(type == 1){
        addMultiImg();
    }
    var img_list = $('.active.multi_img').find('.img_list img');

    var ifram_url = dialogs_path+'?page=multi_img';
    layer.open({
        type: 2,
        title: '多图片',
        maxmin: true,
        anim: 3,
        shadeClose: true, //点击遮罩关闭层
        shade: 0,//取消遮罩层
//            move: false,//禁止拖拽
        btn:['确认','取消'],
        offset: [offset_x, offset_y],
        area : ['600px' , '400px'],
        content: ifram_url,
        success: function(layero, index){//层弹出后回调
            setTimeout(function () {
                var body = layer.getChildFrame('body', index);
                var obj = body.find('#multi_img');
                obj.html('');//清空原有数据
                img_list.each(function(index,item){
                    obj.append('<div index="'+index+'" class="img_block multi-img-mpmall-model"><div class="close" onclick="del_img(this);">✖️</div><img src="'+ $(item).attr('src') +'" class="layui-upload-img"></div>')
                });
                layer.iframeAuto(index);
                var iframeWin = window[layero.find('iframe')[0]['name']]; //得到iframe页的窗口对象，执行iframe页的方法：iframeWin.method();
                iframeWin.dragSortMultiImg();
            },200);
        },
        yes: function(index, layero){
            var body = layer.getChildFrame('body', index);
            var img_list = body.find('#multi_img .img_block');
            setMultiImg(img_list);
            layer.close(index);
        }
    });

    //如果你想关闭最新弹出的层，直接获取layer.index即可
//        layer.close(layer.index); //它获取的始终是最新弹出的某个层，值是由layer内部动态递增计算的
}
function openVideo(type){
    layer.close(layer.index);

    if(type == 1){
        addVideo();
    }

    var active = $('.active.video').find('video source');
    var video_url = active.attr('src');

    var ifram_url = dialogs_path+'?page=video';
    layer.open({
        type: 2,
        title: '视频',
        maxmin: true,
        anim: 3,
        shadeClose: true, //点击遮罩关闭层
        shade: 0,//取消遮罩层
//            move: false,//禁止拖拽
        btn:['确认','取消'],
        offset: [offset_x, offset_y],
        area : ['500px' , '400px'],
        content: ifram_url,
        success: function(layero, index){//层弹出后回调
            setTimeout(function () {
                var body = layer.getChildFrame('body', index);
                body.find('#video-box video source').attr({"src":video_url});
                body.find('#video-box video').load();
            },200);

        },
        yes: function(index, layero){
            var body = layer.getChildFrame('body', index);
            var video_url = body.find('#video-box video source').attr('src');
            setVideo(video_url);
            layer.close(index);
        }
    });

    //如果你想关闭最新弹出的层，直接获取layer.index即可
//        layer.close(layer.index); //它获取的始终是最新弹出的某个层，值是由layer内部动态递增计算的 为了提交
}

function openUeditor(type){
    layer.close(layer.index);

    if(type == 1){
        addUeditor();
    }

    var active = $('.active.ueditor').find('ueditor');
    var content = active.html();

    var ifram_url = dialogs_path+'?page=wangeditor';
    layer.open({
        type: 2,
        title: '富文本',
        maxmin: true,
        anim: 3,
        shadeClose: true, //点击遮罩关闭层
        shade: 0,//取消遮罩层
//            move: false,//禁止拖拽
        btn:['确认','取消'],
        offset: [offset_x, offset_y],
        area : ['700px' , '500px'],
        content: ifram_url,
        success: function(layero, index){//层弹出后回调
            setTimeout(function(){
                var iframeWin = window[layero.find('iframe')[0]['name']]; //得到iframe页的窗口对象，执行iframe页的方法：iframeWin.method();
                iframeWin.editor.txt.html(content)
            },200);
        },
        yes: function(index, layero){
            var iframeWin = window[layero.find('iframe')[0]['name']]; //得到iframe页的窗口对象，执行iframe页的方法：iframeWin.method();
            var content =  iframeWin.editor.txt.html();
            setUeditor(content);
            layer.close(index);
        }
    });

    //如果你想关闭最新弹出的层，直接获取layer.index即可
//        layer.close(layer.index); //它获取的始终是最新弹出的某个层，值是由layer内部动态递增计算的
}


//设置模态框数据
function setText(text_content,color,fontsize,textalign){
    var obj = $('.active.text .child');
    switch(fontsize){
        case "small":
            font_size = '10px';
            break;
        case "middle":
            font_size = '12px';
            break;
        case "big":
            font_size = '18px';
            break;
        default:
            font_size = '';
    }
    obj.html(getFormatCode(text_content)).css({"color":color,"font-size":font_size,"text-align":textalign}).attr({'color':color,'fontsize':fontsize,'textalign':textalign,'content':text_content});
}

function setUeditor(content){
    var obj = $('.active.ueditor ueditor');
    obj.html(content);
}

function setImg(img_url){
    var obj = $('.active.img img');
    obj.attr({"src":img_url});
}
function setMultiImg(img_list) {
    var obj = $('.active.multi_img multi_img');
    //清空原有数据
    obj.find('.broadcast').remove();
    obj.find('.img_list').before('<div class="broadcast"><ul></ul></div>');
    obj.find('.img_list').html('');
    img_list.each(function(index,item){
        obj.find('.broadcast ul').append('<li><img src="'+$(item).find('img').attr('src')+'"/></li>');
        obj.find('.img_list').append('<img src="'+$(item).find('img').attr('src')+'"/>');
    });
    initTerseBanner('.broadcast');
}

function setVideo(video_url){
    var video = $('.active.video video');
    video.find('source').attr({"src":video_url});
    video.load();
}

//新增模块
function addText(){
    var html =
        '       <div index="0" class="mpmall-model text" type="text">' +
        '           <div class="model-close"><div style="display: none;">×</div></div>' +
        '           <div class="child" color="" fontsize="middle" textalign="left" onclick="setActive(this);openText(2);" content=""></div>' +
        '       </div>';
    $('.mobile_content .scrol').append(html);
    var model = $('.mpmall-model');
    for(var i=0; i<model.length;i++){
        model.eq(i).attr('index',i);
    }

    model.removeClass('active');
    model.eq(i-1).addClass('active');
    init();
}

function addImg(){
    var html =
        '       <div index="0" class="mpmall-model img" type="img">' +
        '           <div class="model-close"><div style="display: none;">×</div></div>' +
        '           <img color="#000000" class="ifram-img child layui-upload-img" onclick="setActive(this);openImg(2);"/>' +
        '       </div>';
    $('.mobile_content .scrol').append(html);
    var model = $('.mpmall-model');
    for(var i=0; i<model.length;i++){
        model.eq(i).attr('index',i);
    }

    model.removeClass('active');
    model.eq(i-1).addClass('active');
    init();
}
function addMultiImg(){
    var html =
        '       <div index="0" class="mpmall-model multi_img" type="multi_img">' +
        '           <div class="model-close"><div style="display: none;">×</div></div>' +
        '           <multi_img class="child" onclick="setActive(this);openMultiImg(2);">' +
        '               <div class="broadcast" style="">' +
        '                   <ul>' +
        '                   </ul>' +
        '               </div>'+
        '               <div class="img_list">' +
        '               </div>'+
        '           </multi_img>'+

        '       </div>';
    $('.mobile_content .scrol').append(html);
    var model = $('.mpmall-model');
    for(var i=0; i<model.length;i++){
        model.eq(i).attr('index',i);
    }

    model.removeClass('active');
    model.eq(i-1).addClass('active');
    init();
}

function addVideo() {
    var html =
        '       <div index="0" class="mpmall-model active video" type="video">'+
        '           <div class="model-close"><div style="display: none;">×</div></div>' +
        '           <video width="320" height="240" controls onclick="setActive(this);openVideo(2);" class="child">'+
        '               <source type="video/mp4">'+
        '           </video>'+
        '       </div>';
    $('.mobile_content .scrol').append(html);
    var model = $('.mpmall-model');
    for(var i=0; i<model.length;i++){
        model.eq(i).attr('index',i);
    }

    model.removeClass('active');
    model.eq(i-1).addClass('active');
    init();
}

function addUeditor(){
    var html =
        '       <div index="0" class="mpmall-model active ueditor" type="ueditor">' +
        '           <div class="model-close"><div style="display: none;">×</div></div>' +
        '            <ueditor onclick="setActive(this);openUeditor(2);" class="child"></ueditor>' +
        '       </div>';
    $('.mobile_content .scrol').append(html);
    var model = $('.mpmall-model');
    for(var i=0; i<model.length;i++){
        model.eq(i).attr('index',i);
    }

    model.removeClass('active');
    model.eq(i-1).addClass('active');
    init();
}

//获取详情配置数据
function getContentInfo(){
    var content_list = $('.mpmall-model');
    var content = [];
    content_list.each(function(index,item){
        content[index] = {};
        var item_child = null;
        var item_type = $(item).attr('type');

        switch (item_type){
            case "text":
                item_child = $(item).find('.child');
                if(item_child.attr('content') && item_child.attr('content').length>0){
                    content[index] = {
                        type:item_type,
                        id:(new Date().getTime()).toString()+ Math.round(Math.random() * 1000),
                        fontsize:item_child.attr('fontsize'),
                        textalign:item_child.attr('textalign'),
                        color:item_child.attr('color'),
                        text_content:saveTextFormat(item_child.attr('content'),'save')
                    };
                }
                break;
            case "img":
                item_child = $(item).find('img');
                if(item_child.attr('src') && item_child.attr('src').length>0){
                    content[index] = {
                        type:item_type,
                        id:(new Date().getTime()).toString()+ Math.round(Math.random() * 1000),
                        img_url:item_child.attr('src'),
                    };
                }

                break;
            case "video":
                item_child = $(item).find('video source');
                if(item_child.attr('src') && item_child.attr('src').length>0){
                    content[index] = {
                        type:item_type,
                        id:(new Date().getTime()).toString()+ Math.round(Math.random() * 1000),
                        video_url:item_child.attr('src'),
                        video_img_url:item_child.attr('src')+'?x-oss-process=video/snapshot,t_1,f_png'
                    };
                }
                break;
            case "ueditor":
                item_child = $(item).find('ueditor');
                if(item_child.html() && item_child.html().length>0){
                    content[index] = {
                        type:item_type,
                        id:(new Date().getTime()).toString()+ Math.round(Math.random() * 1000),
                        ueditor_content:item_child.html(),
                    };
                }
                break;
            case "multi_img":
                item_child = $(item).find('.img_list img');
                var img_list = [];
                item_child.each(function(index,img){
                    if($(img).attr('src') && $(img).attr('src').length>0){
                        img_list.push($(img).attr('src'));
                    }
                });

                if(img_list.length > 0){
                    content[index] = {
                        type:item_type,
                        id:(new Date().getTime()).toString()+ Math.round(Math.random() * 1000000),
                        img_url_list:img_list,
                    };
                }
                break;
        }
    });

    return content;
}

/**
 * 数据渲染初始化
 * @param content json结构
 */
function setContentInfo(content){
    var html = '';
    console.log(content);
    $.each(content,function(index,item){
        switch(item.type){
            case "text":
                if(item.text_content && item.text_content != 'null' && item.text_content != 'undefined'){
                    html += '<div index="'+index+'" class="mpmall-model text" type="text">' +
                        '       <div class="model-close"><div>×</div></div>' +
                        '       <div class="child '+item.fontsize+'" style="color:'+item.color+';text-align:'+item.textalign+';" color="'+item.color+'" fontsize="'+item.fontsize+'" textalign="'+item.textalign+'" onclick="setActive(this);openText(2);" content="'+saveTextFormat(item.text_content)+'">'+getFormatCode(saveTextFormat(item.text_content))+'</div>' +
                        '   </div>';
                }
                break;
            case "img":
                if(item.img_url && item.img_url != 'null' && item.img_url != 'undefined'){
                    html += '<div index="'+index+'" class="mpmall-model img" type="img">' +
                        '        <div class="model-close"><div>×</div></div>' +
                        '        <img onclick="setActive(this);openImg(2);" src="'+item.img_url+'"  class="ifram-img child layui-upload-img">' +
                        '   </div>';
                }
                break;
            case "video":
                if(item.video_url && item.video_url != 'null' && item.video_url != 'undefined'){
                    html += '<div index="'+index+'" class="mpmall-model video" type="video">' +
                        '       <div class="model-close"><div>×</div></div>' +
                        '       <video controls onclick="setActive(this);openVideo(2);" class="child">' +
                        '            <source src="'+item.video_url+'" type="video/mp4">' +
                        '       </video>' +
                        '   </div>';
                }
                break;
            case "ueditor":
                if(item.ueditor_content && item.ueditor_content != 'null' && item.ueditor_content != 'undefined'){
                    html += '<div index="'+index+'" class="mpmall-model ueditor" type="ueditor">' +
                        '        <div class="model-close"><div>×</div></div>' +
                        '        <ueditor onclick="setActive(this);openUeditor(2);" class="child">'+item.ueditor_content+'</ueditor>' +
                        '   </div>';
                }
                break;
            case "multi_img":
                var img_list = item.img_url_list;
                var oli = '';
                var oimg = '';
                $.each(img_list,function(index,img){
                    if(img){
                        oli += '<li><img src="'+img+'"/></li>';
                        oimg += '<img src="'+img+'"/>';
                    }
                });

                if(oimg && oli){
                    html += '<div index="'+index+'" class="mpmall-model multi_img" type="multi_img">' +
                        '        <div class="model-close"><div>×</div></div>' +
                        '        <multi_img class="child" onclick="setActive(this);openMultiImg(2);">' +
                        '           <div class="broadcast">' +
                        '               <ul>'+oli+'</ul>' +
                        '           </div>' +
                        '           <div  class="img_list">'+oimg+'</div>' +
                        '        </multi_img>' +
                        '   </div>';
                }
                break;
        }
    });
    $('.mpmall .mobile_content .scrol').html(html);
    //初始化轮播
    initTerseBanner('.broadcast');
}

function setActive(obj){
    $('.mpmall-model').removeClass('active');
    $(obj).closest('.mpmall-model').addClass('active');
}

//拖动排序
function dragSort(){
    var $dom = $("#sort");
    var olds=null;//旧的顺序
    $dom.sortable({
        cursor: "move",
        items: ".mpmall-model", //只是tr可以拖动
        axis:"y",//只允许垂直方向拖动，
        containmentType:"parent",
        opacity: 0.6, //拖动时，透明度为0.6
        revert: false, //释放时，增加动画
        start:function(event, ui){//开始排序时调用
            olds = $(this).sortable("toArray");
        },
        update: function(event, ui) { //更新排序完成之后
            var model = $('.mpmall-model');
            for(var i=0; i<model.length;i++){
                model.eq(i).attr('index',i);
            }
        }
    });

    $dom.disableSelection();
}

function getFormatCode(strValue){
    if(strValue && strValue.length>0){
        return strValue.replace(/\r\n/g, '<br/>').replace(/\n/g, '<br/>').replace(/\s/g, '&nbsp;');
    }
    return '';
}

function saveTextFormat(strValue,type){
    return strValue;
    if(strValue && strValue.length>0){
        if(type == 'save'){
            return strValue.replace(/\r\n/g, '<br1/>').replace(/\n/g, '<br2/>').replace(/\s/g, '\t').replace(/<br1\/>/g, '\r\n').replace(/<br2\/>/g, '\n');
        }else{
            return strValue.replace(/\t/g, ' ');
        }

    }
    return '';
}

function initTerseBanner(obj){
    if(typeof $(obj).terseBanner != 'undefined'){
        $(obj).terseBanner({
            arrow: false,
            btn: false,
            auto: 1000,
        });
    }
}

function init(){
    $('.mpmall-model').on('mouseout',function(){
        $(this).find('.model-close').find('div').hide();
    }).on('mouseover',function(){
        $('.model-close div').hide();
        $(this).find('.model-close').find('div').show();
    });

    $('#plus').on('mouseout',function(){
        $(this).find('#btn-group').hide();
        $(this).find('font').show();
    }).on('mouseover',function(){
        $(this).find('font').hide();
        $(this).find('#btn-group').show();
    });

    $('.model-close div').on('click',function(){
        $(this).closest('.mpmall-model').remove();
        var model = $('.mpmall-model');
        for(var i=0; i<model.length;i++){
            model.eq(i).attr('index',i);
        }
    });

    dragSort();
    layui.use('layer', function(){
        layer = layui.layer;
    });
    initTerseBanner('.broadcast');
}
$(function(){
    init();
});