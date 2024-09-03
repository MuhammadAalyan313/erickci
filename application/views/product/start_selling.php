<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!-- Wrapper -->
<div id="wrapper">
    <div class="container">
        <div class="row">
            <div id="content" class="col-12">
                <nav class="nav-breadcrumb" aria-label="breadcrumb">
                    <ol class="breadcrumb"></ol>
                </nav>
                <h1 class="page-title page-title-product m-b-15"><?php echo trans("start_selling"); ?></h1>
                <div class="form-add-product">
                    <div class="row justify-content-center">
                        <div class="col-12 col-md-12 col-lg-10">
                            <div class="row">
                                <div class="col-12">
                                    <p class="start-selling-description text-muted"><?php echo trans("start_selling_exp"); ?></p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <!-- include message block -->
                                    <?php $this->load->view('product/_messages'); ?>
                                </div>
                            </div>
                            <?php
                            $result = last_approved_time_check($this->auth_user->id);
                            $lastSubmissionTime = strtotime($result->request_action_time);
                            $currentTime = time();
                            $timeDifference = $currentTime - $lastSubmissionTime;

                            ?>

                            <?php if ($this->auth_check) :
                                if ($this->auth_user->is_active_shop_request == 1) : ?>
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="alert alert-info" role="alert">
                                                <?php echo trans("msg_shop_opening_requests"); ?>
                                            </div>
                                        </div>
                                    </div>

                                <?php elseif ($this->auth_user->is_active_shop_request == 2 && $timeDifference <= 86400) : ?>
                                    <div class="row">
                                        <div class="col-12">


                                            <div class="alert alert-secondary" role="alert">
                                                <?php echo trans("msg_shop_request_declined"); ?>
                                            </div>
                                            <div class="alert alert-warning" role="alert">
                                                <?php echo $rejection_reasons->reasons; ?>
                                            </div>
                                            <div class="alert alert-info" role="alert">
                                                Apply again in 24 hours / <a href="<?= base_url() ?>help-center">Contact Support</a>

                                            </div>
                                        </div>
                                    </div>
                                <?php else : ?>
                                    <div class="row">
                                        <div class="col-12">
                                            <?php echo form_open_multipart('start-selling-post', ['id' => 'form_validate', 'class' => 'validate_terms', 'onkeypress' => "return event.keyCode != 13;"]); ?>
                                            <?php if (!empty($plan)) : ?>
                                                <input type="hidden" name="plan_id" value="<?php echo $plan->id; ?>">
                                            <?php endif; ?>
                                            <div class="form-box m-b-15">
                                                <div class="form-box-head text-center">
                                                    <h4 class="title title-start-selling-box"><?php echo trans('tell_us_about_shop'); ?></h4>
                                                </div>
                                                <div class="form-box-body">

                                                    <div class="form-group">
                                                        <label class="control-label"><?php echo trans("shop_name"); ?></label>
                                                        <input type="text" name="shop_name" class="form-control form-input" value="<?php echo $this->auth_user->username; ?>" placeholder="<?php echo trans("shop_name"); ?>" maxlength="<?php echo $this->username_maxlength; ?>" required>
                                                    </div>

                                                    <div class="form-group">
                                                        <div class="row">
                                                            <div class="col-12 col-sm-4 m-b-15">
                                                                <label class="control-label"><?php echo trans("first_name"); ?></label>
                                                                <input type="text" name="first_name" class="form-control form-input" value="<?php echo html_escape($this->auth_user->first_name); ?>" placeholder="<?php echo trans("first_name"); ?>" required>
                                                            </div>
                                                            <div class="col-12 col-sm-4 m-b-15">
                                                                <label class="control-label"><?php echo trans("last_name"); ?></label>
                                                                <input type="text" name="last_name" class="form-control form-input" value="<?php echo html_escape($this->auth_user->last_name); ?>" placeholder="<?php echo trans("last_name"); ?>" required>
                                                            </div>
                                                            <div class="col-12 col-sm-4 m-b-15">
                                                                <label class="control-label"><?php echo trans("phone_number"); ?></label>
                                                                <input type="text" name="phone_number" class="form-control form-input" value="<?php echo html_escape($this->auth_user->phone_number); ?>" placeholder="<?php echo trans("phone_number"); ?>" required>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="form-group locations-fields">
                                                        <label class="control-label"><?php echo "License Details"; ?></label>
                                                        <div class="row">
                                                            <div class="col-12 col-sm-4 m-b-15">
                                                                <select id="select_countries" name="license_type" class="select2 form-control h-100" required>
                                                                    <option value=""><?php echo "License Type"; ?></option>
                                                                    <?php foreach ($licenses as $license) :
                                                                    ?>
                                                                        <option value="<?php echo $license->id; ?>"><?php echo html_escape($license->name); ?></option>
                                                                    <?php
                                                                    endforeach; ?>
                                                                </select>
                                                            </div>
                                                            <div class="col-12 col-sm-4 m-b-15">
                                                                <input type="text" name="license_number" class="form-control form-input"placeholder="<?php echo "License Number"; ?>" required>
                                                            </div>
                                                        </div>

                                                    </div>

                                                    <div class="form-group locations-fields">
                                                        <label class="control-label"><?php echo trans('location'); ?></label>
                                                        <?php $this->load->view("partials/_location", ['countries' => $this->countries, 'country_id' => $this->auth_user->country_id, 'state_id' => $this->auth_user->state_id, 'city_id' => $this->auth_user->city_id, 'map' => false]); ?>
                                                    </div>
                                                    <div class="form-group">
                                                        <div class="row">
                                                            <div class="col-12 col-sm-8 m-b-15">
                                                                <label class="control-label"><?php echo trans("address"); ?></label>
                                                                <input type="text" name="address" class="form-control form-input" value="<?php echo html_escape($this->auth_user->address); ?>" placeholder="<?php echo trans("address"); ?>" maxlength="490" required>
                                                            </div>
                                                            <div class="col-12 col-sm-4 m-b-15">
                                                                <label class="control-label"><?php echo trans("zip_code"); ?></label>
                                                                <input type="text" name="zip_code" class="form-control form-input" value="<?php echo html_escape($this->auth_user->zip_code); ?>" placeholder="<?php echo trans("zip_code"); ?>" required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <?php if ($this->general_settings->request_documents_vendors == 1) : ?>
                                                        <div class="form-group">
                                                            <label class="control-label">
                                                                <?php echo trans("required_files"); ?>
                                                                <?php if (!empty($this->general_settings->explanation_documents_vendors)) : ?>
                                                                    <span class="text-muted font-weight-normal">(<?= $this->general_settings->explanation_documents_vendors; ?>)</span>
                                                                <?php endif; ?>
                                                            </label>
                                                            <div class="m-b-15">
                                                                <a class='btn btn-md btn-info btn-file-upload'>
                                                                    <?php echo trans('select_file'); ?>
                                                                    <input type="file" name="file[]" size="40" id="input_vendor_files" multiple required>
                                                                </a>
                                                                <div id="container_vendor_files"></div>
                                                            </div>
                                                        </div>
                                                    <?php endif; ?>
                                                    <div class="form-group">
                                                        <label class="control-label"><?php echo trans("shop_description"); ?></label>
                                                        <textarea name="about_me" class="form-control form-textarea" placeholder="<?php echo trans("shop_description"); ?>"><?= $this->auth_user->about_me; ?></textarea>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group m-t-15">
                                                <div class="custom-control custom-checkbox custom-control-validate-input parent-terms-conditions">
                                                    <input type="checkbox" class="custom-control-input" name="terms_conditions" id="terms_conditions" value="1" required>
                                                    <label for="terms_conditions" class="custom-control-label"><?php echo trans("terms_conditions_exp"); ?>&nbsp;
                                                        <?php $page_terms = get_page_by_default_name("terms_conditions", $this->selected_lang->id);
                                                        if (!empty($page_terms)) : ?>
                                                            <a href="<?= generate_url($page_terms->page_default_name); ?>" class="link-terms" target="_blank"><strong><?= html_escape($page_terms->title); ?></strong></a>
                                                        <?php endif; ?>
                                                    </label>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <button type="submit" class="btn btn-lg btn-custom btn-shop-details float-right"><?php echo trans("submit"); ?></button>
                                            </div>

                                            <?php echo form_close(); ?>

                                        </div>
                                    </div>
                            <?php endif;
                            endif; ?>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>