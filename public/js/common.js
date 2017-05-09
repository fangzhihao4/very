/**
 * Created by Wanna on 17/4/11.
 */

function addressLinkage(p_id, id){
    $("#"+id).html('<option value="">-- 请选择 --</option>');
    $("#"+id).parent().next().find("select").html('<option value="">-- 请选择 --</option>');

    if( p_id === "" ){
        return false;
    }
    $.ajax({
        url:'/common/address',
        data:{'p_id':p_id},
        method:'post',
        dataType:'json',
        success:function(ret){
            var html = '';
            for(var i in ret.data){
                html += "<option value='"+ret.data[i].id+"'>"+ret.data[i].name+"</option>";
            }
            $('#'+id).append(html);
        }
    });
}