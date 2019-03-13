<?php if( !empty($data) ){?>
<div id="datatable-checkbox_wrapper" class="dataTables_wrapper no-footer">
    <div class="row">
        <div class="col-sm-5">
            <div class="dataTables_info" id="datatable-fixed-header_info" role="status" aria-live="polite">显示 <?php echo $data->firstItem();?> 到 <?php echo $data->lastItem();?> 共 <?php echo $data->total()?> 条纪录</div>
        </div>
        <div class="col-sm-7">
            <div class="dataTables_paginate paging_simple_numbers" id="datatable-fixed-header_paginate">
                <ul class="pagination">
                    <?php if( $data->currentPage() > 1 ){?>
                        <li class="paginate_button">
                            <a href="<?php echo $data->url(1);?>" aria-controls="datatable-fixed-header" data-dt-idx="0" tabindex="0">首页</a>
                        </li>
                    <?php }?>
                    <li class="paginate_button previous <?php if($data->currentPage()==1){echo "disabled";}?>" id="datatable-fixed-header_previous">
                        <a href="<?php echo $data->previousPageUrl();?>" aria-controls="datatable-fixed-header" data-dt-idx="0" tabindex="0">上一页</a>
                    </li>

                    <?php
                    $page_inator_currentPage = $data->currentPage();
                    $page_inator_lastPage = $data->lastPage();
                    $limit = 5;
                    $end_page_index  =  (ceil($page_inator_currentPage/$limit))*$limit;
                    if($end_page_index > $page_inator_lastPage){
                        $end_page_index = $page_inator_lastPage;
                    }

                    $start_page_index = $end_page_index - $limit + 1;
                    if($start_page_index < 1){
                        $start_page_index = 1;
                    }
                    ?>

                    <?php for($page = $start_page_index ;$page<=$end_page_index; $page++){?>
                        <li class="paginate_button <?php if($page_inator_currentPage == $page){echo "active";}?>">
                            <a href="<?php echo $data->url($page);?>" aria-controls="datatable-fixed-header" data-dt-idx="<?php echo $page;?>" tabindex="0"><?php echo $page;?></a>
                        </li>
                    <?php }?>

                    <li class="paginate_button next <?php if($data->lastPage()==$data->currentPage()){echo "disabled";}?>"" id="datatable-fixed-header_next">
                    <a href="<?php echo $data->nextPageUrl();?>" aria-controls="datatable-fixed-header" data-dt-idx="<?php echo $data->lastPage();?>" tabindex="0">下一页</a>
                    </li>
                    <li class="paginate_button">
                        <a href="<?php echo $data->url($data->lastPage());?>" aria-controls="datatable-fixed-header" data-dt-idx="0" tabindex="0">尾页</a>
                    </li>

                    <li class="paginate_button previous" id="datatable-fixed-header_previous">
                        <a aria-controls="datatable-fixed-header" data-dt-idx="0" tabindex="0" style="height: 32px;width: 150px;">
                            <span style="display: inline-block;height: 20px;position: relative;top: -2px;">跳至页码</span>
                            <input onkeyup="this.value=this.value.replace(/\D/g,'');" id="goto_page" aria-controls="datatable-fixed-header" data-dt-idx="0" tabindex="0" style="width: 50px;height: 20px;position: relative;top: -1px;">
                            <i class="fa fa-arrow-right" onclick="gotoPage();" style="cursor: pointer;position: relative;top: -2px;"></i>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
    var query_string = '<?php parse_str($_SERVER['QUERY_STRING'], $query); unset($query['page']); $query['page']=''; echo '?'.http_build_query($query);?>';

    function gotoPage(){
        var page = $('#goto_page').val();
        if(page>0){
            var url = window.location.href.split('?')[0];

            window.location.href = url+query_string+page;
            return;
        }
        alert('请输入正确页码');
    }
</script>

<?php }?>