
<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php $this->load->view("profile/_cover_image"); ?>
<div id="wrapper">
    <div class="container">
        <?php if (empty($user->cover_image)): ?>
            <div class="row">
                <div class="col-12">
                    <nav class="nav-breadcrumb" aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo lang_base_url(); ?>"><?php echo trans("home"); ?></a></li>
                            <li class="breadcrumb-item active" aria-current="page"><?php echo trans("following"); ?></li>
                        </ol>
                    </nav>
                </div>
            </div>
        <?php endif; ?>
        <div class="row">
            <div class="col-12">
                <div class="profile-page-top">
                    <!-- load profile details -->
                    <?php $this->load->view("profile/_profile_user_info"); ?>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <?php $this->load->view("profile/_profile_tabs"); ?>
            </div>
            <div class="col-12">
                <div class="profile-tab-content">
                    <div class="row row-follower">
                        <?php if (!empty(get_location($user))): ?>
                           
                                <div class="col-12 col-sm-12" style="padding-left:25px">
                                    <div class="row">
                                        <div class="col-3" style="background-color:rgba(0,0,0,.02);font-weight:600; padding-top:15px;padding-bottom:15px;text-align:center"><?php echo trans("shop_location"); ?></div>
                                        <div  class="col-9" style=" padding:15px"><span id="span_shop_location_address"><?=get_location($user);?></span></div>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-12" style="padding-left:25px; margin-top:15px">
                                    <div class="product-location-map" >
                                        <iframe id="iframe_shop_location_address" frameborder="0" scrolling="no" marginheight="0" marginwidth="0"></iframe>
                                    </div>
                                </div>
                                
                            <?php
                        else:?>
                            <div class="col-12">
                                <p class="text-center text-muted"><?php echo trans("no_records_found"); ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="row-custom">
                        <!--Include banner-->
                        <?php $this->load->view("partials/_ad_spaces", ["ad_space" => "profile", "class" => "m-t-30"]); ?>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- include send message modal -->
<?php $this->load->view("partials/_modal_send_message", ["subject" => null]); ?>
<?php $this->load->view("partials/_success_message"); ?>
<?php $this->load->view("partials/_fail_message"); ?>


<script>
    window.addEventListener('load', ()=>{
        load_product_shop_location_map();
    })
   
</script>