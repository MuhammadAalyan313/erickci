<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="row">
    <?php echo form_open('license_controller/restriction_enables'); ?>

    <div class="col-sm-12 col-lg-6">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><?php echo "License Restriction"; ?></h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                <div class="form-group">
                    <div class="row">
                        <div class="col-sm-12 col-xs-12">
                            <label><?php echo "Enable License Restriction"; ?></label>
                        </div>
                        <div class="col-sm-4 col-xs-12 col-option">
                            <input type="radio" name="restriction_setting" value="1" id="restriction_setting_allowed" class="square-purple" <?php echo ($license_restriction == '1') ? 'checked' : ''; ?>>
                            <label for="restriction_setting_allowed" class="option-label"><?php echo trans('enable'); ?></label>
                        </div>
                        <div class="col-sm-4 col-xs-12 col-option">
                            <input type="radio" name="restriction_setting" value="0" id="restriction_setting_not" class="square-purple" <?php echo ($license_restriction == '0') ? 'checked' : ''; ?>>
                            <label for="restriction_setting_not" class="option-label"><?php echo trans('disable'); ?></label>
                        </div>
                    </div>
                </div>

            </div>
            <!-- /.box-body -->
            <div class="box-footer">
                <button type="submit" name="submit" value="verification" class="btn btn-primary pull-right"><?php echo trans('save_changes'); ?></button>
            </div>
        </div>
    </div>
    <?php echo form_close(); ?><!-- form end -->

    <?php echo form_open('license_controller/allowed_licenses_post'); ?>
    <div class="col-sm-12 col-lg-6">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><?php echo "Allowed Licenses"; ?></h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                <div class="form-group">
                    <div class="row">
                        <div class="col-sm-12 col-xs-12">
                            <label><?php echo  "User/Visitor" ?></label>

                        </div>
                        <?php
                        $array = false;
                        $innerArray = false;
                        $outerArray = false;

                        foreach ($licenses as $subLicense) :
                            if ($da[0] == "null") {
                                $array = false;
                            } else {
                                $array = in_array($subLicense->id, json_decode($da[0]));
                            }
                        ?>

                            <div class="col-sm-4 col-xs-12 col-option">
                                <input type="checkbox" name="license<?php echo 0 ?>[]" value="<?php echo $subLicense->id ?>" id="<?php echo "license-$subLicense->id-" . $subLicense->id ?>" class="square-purple" <?php echo ($array) ? 'checked' : ''; ?>>
                                <label for="<?php echo "license-$subLicense->id-" . $subLicense->id ?>" class="option-label"><?php echo $subLicense->name; ?></label>
                            </div>
                            <?php
                            ?>

                        <?php endforeach; ?>


                        <?php foreach ($licenses as $license) :

                        ?>

                            <div class="col-sm-12 col-xs-12">
                                <label><?php echo  $license->name; ?></label>

                            </div>
                            <?php foreach ($licenses as $subLicense) :

                                if ($da[$license->id] == "null" || $da[$license->id] == null) {
                                    $innerArray = false;
                                } else {
                                    $innerArray = in_array($subLicense->id, json_decode($da[$license->id]));
                                    // $array = in_array($subLicense->id, json_decode($da[0]));
                                }
                                // $innerArray = in_array($subLicense->id, json_decode($da[$license->id]));
                            ?>

                                <?php if ($subLicense->id === $license->id) {
                                    continue;
                                } else {
                                ?>
                                    <div class="col-sm-4 col-xs-12 col-option">
                                        <input type="checkbox" name="license<?php echo $license->id ?>[]" value="<?php echo $subLicense->id ?>" id="<?php echo "license-$license->id-" . $subLicense->id ?>" class="square-purple" <?php echo ($innerArray) ? 'checked' : ''; ?>>
                                        <label for="<?php echo "license-$license->id-" . $subLicense->id ?>" class="option-label"><?php echo $subLicense->name; ?></label>
                                    </div>
                                <?php
                                } ?>

                            <?php endforeach;
                            if (!isset($da[$license->id])) {
                                $outerArray = false;
                            } else {
                                $outerArray = in_array(0, json_decode($da[$license->id]));
                            }

                            ?>
                            <div class="col-sm-4 col-xs-12 col-option">
                                <input type="checkbox" name="license<?php echo $license->id ?>[]" value="0" id="<?php echo "license-$license->id-0" ?>" class="square-purple" <?php echo ($outerArray) ? 'checked' : ''; ?>>
                                <label for="<?php echo "license-$license->id-0" ?>" class="option-label"><?php echo "Visiting User"; ?></label>
                            </div>
                        <?php endforeach; ?>

                    </div>
                </div>

            </div>
            <!-- /.box-body -->
            <div class="box-footer">
                <button type="submit" name="submit" value="verification" class="btn btn-primary pull-right"><?php echo trans('save_changes'); ?></button>
            </div>
        </div>
    </div>
    <?php echo form_close(); ?><!-- form end -->
</div>