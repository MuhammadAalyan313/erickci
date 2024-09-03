<?php
defined('BASEPATH') or exit('No direct script access allowed');

class License_controller extends Admin_Core_Controller
{
    public function __construct()
    {
        parent::__construct();
    }
    public function store_license_settings()
    {

        $da = [];
        foreach ($this->license_model->get_all_allowed_licenses() as $li) {
            $da[$li->license_id] =   $li->allowed_id;
        }
        // var_dump($da );
        // die;
        check_permission('general_settings');
        $data['title'] = "License Setting";
        $data["active_tab"] = "license_settings";
        $data["licenses"] = $this->license_model->get_license_types();
        $data["license_restriction"] = $this->license_model->enable_restriction_setting();
        $data['general_settings'] = $this->settings_model->get_general_settings();
        $data['da'] = $da;

        $this->load->view('admin/includes/_header', $data);
        $this->load->view('admin/store_license/store_license_setting', $data);
        $this->load->view('admin/includes/_footer');
    }



    public function all_store_license_keys()
    {
        check_permission('general_settings');
        $data['title'] = "Store Licenses";
        $data["active_tab"] = "license_settings";
        $data["licenses"] = $this->license_model->get_license_types();
        $data['general_settings'] = $this->settings_model->get_general_settings();

        $this->load->view('admin/includes/_header', $data);
        $this->load->view('admin/store_license/store_licenses', $data);
        $this->load->view('admin/includes/_footer');
    }

    public function add_store_license_keys()
    {
        check_permission('general_settings');
        $data['title'] = "Add Store Licenses";
        $data["active_tab"] = "license_settings";

        $data['general_settings'] = $this->settings_model->get_general_settings();

        $this->load->view('admin/includes/_header', $data);
        $this->load->view('admin/store_license/add_store_license', $data);
        $this->load->view('admin/includes/_footer');
    }

    public function add_store_license_keys_post()
    {

        $this->form_validation->set_rules('license_name', "License Type Name", 'required|max_length[500]|regex_match[/[A-Za-z0-9\s\-_]$/]');
        if ($this->form_validation->run() === false) {
            $this->session->set_flashdata('errors', validation_errors());
            redirect($this->agent->referrer());
        } else {
            $license_name = ["name" => $this->input->post("license_name")];
            $license_added = $this->license_model->add_license_types($license_name);
            if ($license_added) {
                $this->license_model->create_or_update_restriction($license_added,json_encode([$license_added]));
                redirect("/admin/store-licenses");
            } else {
                $this->session->set_flashdata('errors', trans("msg_error"));
                redirect($this->agent->referrer());
            }
        }
    }

    public function edit_store_license_keys($id)
    {
        $data['title'] = "Edit Store Licenses";
        $data["active_tab"] = "license_settings";
        $data['license'] = $this->license_model->get_single_license($id);
        $this->load->view('admin/includes/_header', $data);
        $this->load->view('admin/store_license/edit_store_license', $data);
        $this->load->view('admin/includes/_footer');
    }

    public function edit_store_license_keys_post($id)
    {
        $this->form_validation->set_rules('license_name', "License Type Name", 'required|max_length[500]|regex_match[/[A-Za-z0-9\s\-_]$/]');
        if ($this->form_validation->run() === false) {
            $this->session->set_flashdata('errors', validation_errors());
            redirect($this->agent->referrer());
        } else {
            $lic = $this->input->post("license_name");
            $update_license = $this->license_model->update_license_name($lic, $id);
            if ($update_license) {
                redirect("/admin/store-licenses");
            } else {
                $this->session->set_flashdata('errors', trans("msg_error"));
                redirect($this->agent->referrer());
            }
        }
    }

    public function restriction_enables()
    {
        $license_restriction = ["license_restriction" => $this->input->post("restriction_setting")];
        $this->license_model->update_license_restriction_setting($license_restriction);
        var_dump($license_restriction);
    }

    public function allowed_licenses_post()
    {
        $licenses = $this->license_model->get_license_types();
        foreach ($licenses as $license) {
            $lic = $this->input->post("license" . $license->id);
            if ($lic != null) {
                array_push($lic, ...[$license->id]);
            } else {
                $lic = [$license->id];
            }
            $data = json_encode($lic);
            $this->license_model->create_or_update_restriction($license->id, $data);
            var_dump(json_encode($lic) . "<br>");
        }
        $data = $this->input->post("license0");

        array_push($data, ...[0]);
        $visitorData = json_encode($data);
        $this->license_model->create_or_update_restriction(0, $visitorData);
        redirect($this->agent->referrer());
        var_dump($visitorData);

        die("This is post allowed_licenses_post");
    }

    public function delete_license()
    {
        $deleted_license = $this->license_model->delete_license();
        $this->license_model->delete_allowed_license();
        if ($deleted_license) {
            echo  json_encode(["deletedId" => $deleted_license, 'status' => true]);
            return redirect($this->agent->referrer());
        }
       
        return redirect($this->agent->referrer());
    }
}
