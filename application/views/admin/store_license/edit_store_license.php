<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="row">
    <?php echo form_open('license_controller/edit_store_license_keys_post/'.$license->id); ?>


    <div class="col-sm-12 col-lg-6">
    <?php if ($this->session->flashdata('errors')): ?>
    <div class="form-group">
        <div class="margin-bottom-10">
            <div class="alert alert-danger alert-dismissable">
                <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                <h4>
                    <i class="icon fa fa-times"></i>
                    <?php echo $this->session->flashdata('errors'); ?>
                </h4>
            </div>
        </div>
    </div>
<?php endif; ?>
        <div class="box box-primary">

            <div class="box-header with-border">
                <h3 class="box-title"><?php echo "Add License Type"; ?></h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                <div class="form-group">
                    <div class="row">
                        <div class="col-sm-12 col-xs-12">
                            <label for="license_name"><?php echo "License Type Name"; ?></label>
                        </div>

                        <div class="col-sm-4 col-xs-12 col-option">
                            <input type="text" name="license_name" id="license_name" class="square-purple form-control" value="<?php echo $license->name ?>">

                        </div>
                    </div>
                </div>

            </div>
            <!-- /.box-body -->
            <div class="box-footer">
                <button type="submit" name="submit" value="verification" class="btn btn-primary pull-right"><?php echo "Update License"; ?></button>
            </div>
        </div>
    </div>
    <?php echo form_close(); ?><!-- form end -->