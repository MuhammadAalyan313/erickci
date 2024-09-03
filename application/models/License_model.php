<?php

defined('BASEPATH') or exit('No direct script access allowed');

class License_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }
    public function get_license_types()
    {
        $this->db->select('license_type.id,license_type.name');
        $query = $this->db->get('license_type');
        $result = $query->result();
        return $result;
    }
    public function get_all_allowed_licenses()
    {
        $this->db->select('license_restriction.*');
        $query = $this->db->get('license_restriction');
        $result = $query->result();
        return $result;
    }
    public function get_allowed_licenses()
    {
        if ($this->auth_check) {
            if ($this->auth_user->license_type != null) {
                $this->db->select('license_restriction.allowed_id');
                $this->db->where('license_restriction.license_id', $this->auth_user->license_type);
                $query = $this->db->get('license_restriction');

                $result = $query->row();
                return $result;
            } else {
                $this->db->select('license_restriction.allowed_id');
                $this->db->where('license_restriction.license_id', 0);
                $query = $this->db->get('license_restriction');

                $result = $query->row();
                return $result;
            }
        } else {
            $this->db->select('license_restriction.allowed_id');
            $this->db->where('license_restriction.license_id', 0);
            $query = $this->db->get('license_restriction');

            $result = $query->row();
            return $result;
        }
    }
    public function get_auth_user_allowed_licenses()
    {
        $this->db->select('license_restriction.allowed_id');
        if ($this->auth_user->license_type != null) {
            $this->db->where('license_restriction.license_id', $this->auth_user->license_type);
        } else {
            $this->db->where('license_restriction.license_id', 0);
        }
        $query = $this->db->get('license_restriction');

        $result = $query->row();
        return $result->allowed_id;
    }
    public function add_license_types($data)
    {
        $this->db->insert('license_type', $data);
        $insert_id = $this->db->insert_id();
        return $insert_id;
    }
    public function create_or_update_restriction($id, $data)
    {
        $this->db->where('license_id', $id);
        $query = $this->db->get('license_restriction');
        $result = $query->row();
        if (!$result) {
            $this->db->insert('license_restriction', ['license_id' => $id, 'allowed_id' => $data]);
        } else {
            $this->db->where('license_id', $id);
            $this->db->update('license_restriction', ['allowed_id' => $data]);
        }
    }


    public function get_license_restriction_setting()
    {
        // if($this->auth_check){
        //     if($this->auth_user->role_id == 1){
        //         return 1;
        //     }
        // }

        $this->db->select('license_restriction');
        $query = $this->db->get('general_settings');
        $result = $query->row();
        return $result->license_restriction;
    }

    public function enable_restriction_setting()
    {
        $this->db->select('license_restriction');
        $query = $this->db->get('general_settings');
        $result = $query->row();
        return $result->license_restriction;
    }

    public function update_license_restriction_setting($license_restriction)
    {
        $this->db->update('general_settings', $license_restriction);
        redirect($this->agent->referrer());
    }
    public function get_single_license($id)
    {
        $this->db->where('id', $id);
        $query = $this->db->get('license_type');
        $result = $query->row();
        return $result;
    }
    public function update_license_name($data, $id)
    {
        $this->db->where('id', $id);
        $updated_id  = $this->db->update('license_type', ['name' => $data]);
        // $updated_id = $this->db->insert_id();
        return  $updated_id;
    }

    public function delete_license()
    {
        $licenseId = $this->input->post('license_id');
        if ($licenseId) {
            $this->db->where('id', $licenseId);
            $this->db->delete('license_type');
            // Check if any rows were affected by the delete operation
            if ($this->db->affected_rows() > 0) {
                return $licenseId; // Return the ID of the deleted row
            }
        }
        return 0;
    }
    public function delete_allowed_license(){
        $licenseId = $this->input->post('license_id');
        if ($licenseId) {
            $this->db->where('license_id', $licenseId);
            $this->db->delete('license_restriction');
            // Check if any rows were affected by the delete operation
            if ($this->db->affected_rows() > 0) {
                return $licenseId; // Return the ID of the deleted row
            }
        }
        return 0;
    }
}
