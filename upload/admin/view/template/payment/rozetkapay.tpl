<?php echo $header; ?>
<div id="content">
    <div class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
        <?php } ?>
    </div>
    <?php if ($error_warning) { ?>
    <div class="warning"><?php echo $error_warning; ?></div>
    <?php } ?>
    <div class="box">
        <div class="heading">
            <h1><img src="view/image/payment.png" alt="" /> <?php echo $heading_title; ?></h1>
            <div class="buttons"><a onclick="$('#form').submit();" class="button"><?php echo $button_save; ?></a>
                <a href="<?php echo $cancel; ?>" class="button"><?php echo $button_cancel; ?></a>
            </div>
        </div>
        <div class="content">
            <div id="tabs" class="htabs">
                <a href="#tab-general" style="display: inline;" class="selected"><i class="fa fa-cogs text-primary"></i> <?php echo $tab_general; ?></a>
                <a href="#tab-order_status" style="display: inline;"><?=$text_tab_order_status;?></a>
                <a href="#tab-test" style="display: inline;"><i class="fa fa-bug" aria-hidden="true"><?php echo $text_tab_test; ?></a>
            </div>

            <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
                <div id="tab-general" style="display: block;">

                    <table class="form">
                        <tbody>
                            <tr>
                                <td><span class="required">*</span> <?=$entry_login;?>: </td>
                                <td>
                                    <input type="text" name="rozetkapay_login" size="100" value="<?=$rozetkapay_login;?>">
                                    <?php if (isset($error_login)) { ?>
                                    <span class="error"><?php echo $error_login; ?></span>
                                    <?php } ?>
                                </td>
                            </tr>

                            <tr>
                                <td><span class="required">*</span> <?=$entry_password;?>: </td>
                                <td>
                                    <input type="text" name="rozetkapay_password" size="100" value="<?=$rozetkapay_password;?>">
                                    <?php if (isset($error_password)) { ?>
                                    <span class="error"><?php echo $error_password; ?></span>
                                    <?php } ?>
                                </td>
                            </tr>


                            <tr>
                                <td><?=$entry_qrcode_status;?>: </td>
                                <td>
                                    <select name="rozetkapay_qrcode_status" class="form-control">
                                        <?php if ($rozetkapay_qrcode_status) { ?>
                                        <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                                        <option value="0"><?php echo $text_disabled; ?></option>
                                        <?php } else { ?>
                                        <option value="1"><?php echo $text_enabled; ?></option>
                                        <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                                        <?php } ?>
                                    </select>
                                </td>
                            </tr>

                            <tr>
                                <td><?=$entry_geo_zone;?>: </td>
                                <td>
                                    <select name="rozetkapay_geo_zone_id" id="input-geo-zone" class="form-control">
                                        <option value="0"><?php echo $text_all_zones; ?></option>
                                        <?php foreach ($geo_zones as $geo_zone) { ?>
                                        <?php if ($geo_zone['geo_zone_id'] == $rozetkapay_geo_zone_id) { ?>
                                        <option value="<?php echo $geo_zone['geo_zone_id']; ?>" selected="selected"><?php echo $geo_zone['name']; ?></option>
                                        <?php } else { ?>
                                        <option value="<?php echo $geo_zone['geo_zone_id']; ?>"><?php echo $geo_zone['name']; ?></option>
                                        <?php } ?>
                                        <?php } ?>
                                    </select>
                                </td>
                            </tr>

                            <tr>
                                <td><?=$entry_status;?>: </td>
                                <td>
                                    <select name="rozetkapay_status" class="form-control">
                                        <?php if ($rozetkapay_status) { ?>
                                        <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                                        <option value="0"><?php echo $text_disabled; ?></option>
                                        <?php } else { ?>
                                        <option value="1"><?php echo $text_enabled; ?></option>
                                        <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                                        <?php } ?>
                                    </select>
                                </td>
                            </tr>

                            <tr>
                                <td><span class="required">*</span> <?=$entry_sort_order;?>: </td>
                                <td>
                                    <input type="text" name="rozetkapay_sort_order" value="<?=$rozetkapay_sort_order;?>">
                                </td>
                            </tr>

                        </tbody>
                    </table>

                </div>
                <div id="tab-order_status" style="display: block;">
                    <table class="form">
                        <tbody>

                            <tr>
                                <td><?=$text_order_status_init;?>: </td>
                                <td>
                                    <select name="rozetkapay_order_status_init" class="form-control">
                                        <option value="0" <?php if ($rozetkapay_order_status_init == "0") { ?>
                                            selected="selected"
                                            <?php } ?>>---</option>
                                        <?php foreach ($order_statuses as $order_status) { ?>
                                        <?php if ($order_status['order_status_id'] == $rozetkapay_order_status_init) { ?>
                                        <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"
                                                ><?php echo $order_status['name']; ?></option>
                                        <?php } else { ?>
                                        <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                                        <?php } ?>
                                        <?php } ?>
                                    </select>
                                </td>
                            </tr>

                            <tr>
                                <td><?=$text_order_status_pending;?>: </td>
                                <td>
                                    <select name="rozetkapay_order_status_pending" class="form-control">
                                        <option value="0" <?php if ($rozetkapay_order_status_pending == "0") { ?>
                                            selected="selected"
                                            <?php } ?>>---</option>
                                        <?php foreach ($order_statuses as $order_status) { ?>
                                        <?php if ($order_status['order_status_id'] == $rozetkapay_order_status_pending) { ?>
                                        <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"
                                                ><?php echo $order_status['name']; ?></option>
                                        <?php } else { ?>
                                        <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                                        <?php } ?>
                                        <?php } ?>
                                    </select>
                                </td>
                            </tr>

                            <tr>
                                <td><span class="required">*</span><?=$text_order_status_success;?>: </td>
                                <td>
                                    <select name="rozetkapay_order_status_success" class="form-control">
                                        <?php foreach ($order_statuses as $order_status) { ?>
                                        <?php if ($order_status['order_status_id'] == $rozetkapay_order_status_success) { ?>
                                        <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"
                                                ><?php echo $order_status['name']; ?></option>
                                        <?php } else { ?>
                                        <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                                        <?php } ?>
                                        <?php } ?>
                                    </select>
                                </td>
                            </tr>

                            <tr>
                                <td><span class="required">*</span><?=$text_order_status_failure;?>: </td>
                                <td>
                                    <select name="rozetkapay_order_status_failure" class="form-control">
                                        <?php foreach ($order_statuses as $order_status) { ?>
                                        <?php if ($order_status['order_status_id'] == $rozetkapay_order_status_failure) { ?>
                                        <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"
                                                ><?php echo $order_status['name']; ?></option>
                                        <?php } else { ?>
                                        <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                                        <?php } ?>
                                        <?php } ?>
                                    </select>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div id="tab-test" style="display: block;">

                    <table class="form">
                        <tbody>
                            <tr>
                                <td><?=$entry_status;?>: </td>
                                <td>
                                    <select name="rozetkapay_test_status" class="form-control">
                                        <?php if ($rozetkapay_test_status) { ?>
                                        <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                                        <option value="0"><?php echo $text_disabled; ?></option>
                                        <?php } else { ?>
                                        <option value="1"><?php echo $text_enabled; ?></option>
                                        <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                                        <?php } ?>
                                    </select>
                                </td>
                            </tr>

                            <tr>
                                <td><?=$entry_log_status;?>: </td>
                                <td>
                                    <select name="rozetkapay_log_status" class="form-control">
                                        <?php if ($rozetkapay_log_status) { ?>
                                        <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                                        <option value="0"><?php echo $text_disabled; ?></option>
                                        <?php } else { ?>
                                        <option value="1"><?php echo $text_enabled; ?></option>
                                        <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                                        <?php } ?>
                                    </select>
                                </td>
                            </tr>

                            <tr>
                                <td><?=$text_test_cards;?>: </td>
                                <td>
                                    <div class="well well-sm">
                                        Для тестів можна використовувати ці карти:<br>
                                        card=4242424242424242  exp=any cvv=any  3ds=Yes result=success<br>
                                        card=5454545454545454  exp=any cvv=any  3ds=Yes result=success<br>
                                        card=4111111111111111  exp=any cvv=any  3ds=No result=success<br>
                                        card=4200000000000000  exp=any cvv=any  3ds=Yes result=rejected<br>
                                        card=5105105105105100  exp=any cvv=any  3ds=Yes result=rejected<br>
                                        card=4444333322221111  exp=any cvv=any  3ds=No result=rejected<br>
                                        card=5100000020002000  exp=any cvv=any  3ds=No result=rejected<br>
                                        card=4000000000000044  exp=any cvv=any  3ds=No result=insufficient-funds<br>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    
                    <div class="box">
                        <div class="heading">
                            <h1><img src="view/image/log.png" alt="" /> <?=$text_test_log_title;?></h1>
                            <div class="buttons"><a href="<?php echo $href_log_download; ?>" class="button"><?php echo $button_log_download; ?></a></div>
                            <div class="buttons"><a href="<?php echo $href_log_clear; ?>" class="button"><?php echo $button_log_clear; ?></a></div>
                        </div>
                        <div class="content">
                            <textarea wrap="off" style="width: 98%; height: 300px; padding: 5px; border: 1px solid #CCCCCC; background: #FFFFFF; overflow: scroll;"><?php echo $log; ?></textarea>
                        </div>
                    </div>                    

                </div>
        </div>
        </form>
    </div>
</div>
</div>
<script type="text/javascript"><!--
$('#tabs a').tabs();
    $('#languages a').tabs();
//--></script> 
<?php echo $footer; ?> 

