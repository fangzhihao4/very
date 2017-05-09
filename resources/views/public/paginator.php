<div id="datatable-checkbox_wrapper" class="dataTables_wrapper no-footer">
    <div class="row">
        <div class="col-sm-5">
            <div class="dataTables_info" id="datatable-fixed-header_info" role="status" aria-live="polite">显示 <?php echo $data->firstItem();?> 到 <?php echo $data->lastItem();?> 共 <?php echo $data->total()?> 条纪录</div>
        </div>
        <div class="col-sm-7">
            <div class="dataTables_paginate paging_simple_numbers" id="datatable-fixed-header_paginate">
                <ul class="pagination">
                    <li class="paginate_button previous <?php if($data->currentPage()==1){echo "disabled";}?>" id="datatable-fixed-header_previous">
                        <a href="<?php echo $data->previousPageUrl();?>" aria-controls="datatable-fixed-header" data-dt-idx="0" tabindex="0">上一页</a>
                    </li>
                    <?php
                        $index = 1;
                        for($page = $data->currentPage(); $page<=$data->lastPage(); $page++){
                            $index++;
                    ?>
                        <li class="paginate_button <?php if($data->currentPage()==$page){echo "active";}?>">
                            <a href="<?php echo $data->url($page);?>" aria-controls="datatable-fixed-header" data-dt-idx="<?php echo $page;?>" tabindex="0"><?php echo $page;?></a>
                        </li>
                        <?php if($index==7){break;}?>
                    <?php }?>
                    <li class="paginate_button next <?php if($data->lastPage()==$data->currentPage()){echo "disabled";}?>"" id="datatable-fixed-header_next">
                        <a href="<?php echo $data->nextPageUrl();?>" aria-controls="datatable-fixed-header" data-dt-idx="<?php echo $data->lastPage();?>" tabindex="0">下一页</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
