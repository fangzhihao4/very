<?php if( !empty($data) ){?>
    <div id="datatable-checkbox_wrapper" class="dataTables_wrapper no-footer">
        <div class="row">
            <div class="col-sm-5">
                <div class="dataTables_info" id="datatable-fixed-header_info" role="status" aria-live="polite">显示 <?php echo $data->firstItem();?> 到 <?php echo $data->lastItem();?> 共 <?php echo $data->total()?> 条纪录</div>
            </div>
            <div class="col-sm-7">
                <div class="dataTables_paginate paging_simple_numbers" id="datatable-fixed-header_paginate">
                    <ul class="pagination">
                        <li class="paginate_button">
                            <a href="<?php echo $data->url(1);?>" aria-controls="datatable-fixed-header" data-dt-idx="0" tabindex="0">首页</a>
                        </li>
                        <li class="paginate_button previous <?php if($data->currentPage()==1){echo "disabled";}?>" id="datatable-fixed-header_previous">
                            <a href="<?php echo $data->previousPageUrl();?>" aria-controls="datatable-fixed-header" data-dt-idx="0" tabindex="0">上一页</a>
                        </li>

                        <?php if( $data->currentPage() <= 5 ){?>
                            <?php for( $page = 1; $page<=($data->lastPage()>5?5:$data->lastPage()); $page++ ){?>
                                <li class="paginate_button <?php if($data->currentPage()==$page){echo "active";}?>">
                                    <a href="<?php echo $data->url($page);?>" aria-controls="datatable-fixed-header" data-dt-idx="<?php echo $page;?>" tabindex="0"><?php echo $page;?></a>
                                </li>
                            <?php }?>
                        <?php }else{?>
                            <?php if( $data->lastPage() - $data->currentPage() < 5 ){?>
                                <?php for( $page = ($data->lastPage()-4<1?1:$data->lastPage()-4); $page<=$data->lastPage(); $page++ ){?>
                                    <li class="paginate_button <?php if($data->currentPage()==$page){echo "active";}?>">
                                        <a href="<?php echo $data->url($page);?>" aria-controls="datatable-fixed-header" data-dt-idx="<?php echo $page;?>" tabindex="0"><?php echo $page;?></a>
                                    </li>
                                <?php }?>
                            <?php }else{?>
                                <?php for( $page = ($data->currentPage()-2<1?1:$data->currentPage()-2); $page<($data->currentPage()+3>$data->lastPage()?$data->lastPage():$data->currentPage()+3); $page++ ){?>
                                    <li class="paginate_button <?php if($data->currentPage()==$page){echo "active";}?>">
                                        <a href="<?php echo $data->url($page);?>" aria-controls="datatable-fixed-header" data-dt-idx="<?php echo $page;?>" tabindex="0"><?php echo $page;?></a>
                                    </li>
                                <?php }?>
                            <?php }?>
                        <?php }?>

                        <li class="paginate_button next <?php if($data->lastPage()==$data->currentPage()){echo "disabled";}?>"" id="datatable-fixed-header_next">
                        <a href="<?php echo $data->nextPageUrl();?>" aria-controls="datatable-fixed-header" data-dt-idx="<?php echo $data->lastPage();?>" tabindex="0">下一页</a>
                        </li>
                        <li class="paginate_button">
                            <a href="<?php echo $data->url($data->lastPage());?>" aria-controls="datatable-fixed-header" data-dt-idx="0" tabindex="0">尾页</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
<?php }?>