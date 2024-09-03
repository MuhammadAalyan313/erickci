<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Auth_model extends CI_Model
{
    //input values
    public function input_values()
    {
        $data = array(
            'username' => remove_forbidden_characters($this->input->post('username', true)),
            'email' => $this->input->post('email', true),
            'first_name' => $this->input->post('first_name', true),
            'phone_number' => remove_forbidden_characters_number($this->input->post('phone', true)),
            'password' => $this->input->post('password', true)
        );
        return $data;
    }

    //login
    public function login()
    {
        $this->load->library('bcrypt');

        $data = $this->input_values();
        if (strpos($data['email'], '@') !== false) {
            $user = $this->get_user_by_email($data['email']);
        } else {


            // Regular expression pattern for a username
            $usernamePattern = '/[A-Za-z_\-]/';

            // Regular expression pattern for a phone number
            $phonePattern = '/^[0-9]+$/';
            if (preg_match($usernamePattern, $data['email'])) {

                $user = $this->get_user_by_username($data['email']);
                // Additional actions for username validation

            } else {

                $user = $this->get_user_by_phone($data['email']);
            }
        }


        if (!empty($user)) {
            //check password
            if (!$this->bcrypt->check_password($data['password'], $user->password)) {
                $this->session->set_flashdata('error', "wrong credentials");
                return false;
            }
            if ($user->email_status != 1) {
                $this->session->set_flashdata('error', trans("msg_confirmed_required") . "&nbsp;<a href='javascript:void(0)' class='link-resend-activation-email' onclick=\"send_activation_email('" . $user->id . "','" . $user->token . "');\">" . trans("resend_activation_email") . "</a>");
                return false;
            }
            if ($user->banned == 1) {
                $this->session->set_flashdata('error', trans("msg_ban_error"));
                return false;
            }
            //set user data
            $user_data = array(
                'mds_sess_user_id' => $user->id,
                'mds_sess_user_email' => $user->email,
                'mds_sess_user_ps' => md5($user->password),
                'mds_sess_logged_in' => true,
                'mds_sess_app_key' => $this->config->item('app_key'),
            );
            $this->session->set_userdata($user_data);
            return true;
        } else {
            $this->session->set_flashdata('error', "wrong credentials");
            return false;
        }
    }

    //login direct
    public function login_direct($user)
    {
        //set user data
        $user_data = array(
            'mds_sess_user_id' => $user->id,
            'mds_sess_user_email' => $user->email,
            'mds_sess_user_ps' => md5($user->password),
            'mds_sess_logged_in' => true,
            'mds_sess_app_key' => $this->config->item('app_key'),
        );

        $this->session->set_userdata($user_data);
    }

    //login with facebook
    public function login_with_facebook($fb_user)
    {
        if (!empty($fb_user)) {
            $user = $this->get_user_by_email($fb_user->email);
            //check if user registered
            if (empty($user)) {
                if (empty($fb_user->name)) {
                    $fb_user->name = "user-" . uniqid();
                }
                $username = $this->generate_uniqe_username($fb_user->name);
                $slug = $this->generate_uniqe_slug($username);
                //add user to database
                $data = array(
                    'facebook_id' => $fb_user->id,
                    'email' => $fb_user->email,
                    'email_status' => 1,
                    'token' => generate_token(),
                    'role_id' => 3,
                    'username' => $username,
                    'first_name' => $fb_user->first_name,
                    'last_name' => $fb_user->last_name,
                    'slug' => $slug,
                    'avatar' => "",
                    'user_type' => "facebook",
                    'last_seen' => date('Y-m-d H:i:s'),
                    'created_at' => date('Y-m-d H:i:s')
                );
                //download avatar
                $avatar = "https://graph.facebook.com/" . $fb_user->id . "/picture?type=large";
                if (!empty($avatar)) {
                    $this->load->model('upload_model');
                    $save_to = FCPATH . "uploads/temp/avatar-" . uniqid() . ".jpg";
                    @copy($avatar, $save_to);
                    if (!empty($save_to) && file_exists($save_to)) {
                        $data["avatar"] = $this->upload_model->avatar_upload($save_to);
                    }
                    @unlink($save_to);
                }
                if ($this->general_settings->vendor_verification_system != 1) {
                    $data['role_id'] = 2;
                }
                if (!empty($data['email'])) {
                    $this->db->insert('users', $data);
                    $user = $this->get_user_by_email($fb_user->email);
                }
            }
            if (!empty($user)) {
                if ($user->banned == 1) {
                    $this->session->set_flashdata('error', trans("msg_ban_error"));
                    return false;
                }
                //login
                $this->login_direct($user);
            }
        }
    }

    //login with google
    public function login_with_google($g_user)
    {
        if (!empty($g_user)) {
            $user = $this->get_user_by_email($g_user->email);
            //check if user registered
            if (empty($user)) {
                if (empty($g_user->name)) {
                    $g_user->name = "user-" . uniqid();
                }
                $username = $this->generate_uniqe_username($g_user->name);
                $slug = $this->generate_uniqe_slug($username);
                //add user to database
                $data = array(
                    'google_id' => $g_user->id,
                    'email' => $g_user->email,
                    'email_status' => 1,
                    'token' => generate_unique_id(),
                    'role_id' => 3,
                    'username' => $username,
                    'first_name' => $g_user->first_name,
                    'last_name' => $g_user->last_name,
                    'slug' => $slug,
                    'avatar' => "",
                    'user_type' => "google",
                    'last_seen' => date('Y-m-d H:i:s'),
                    'created_at' => date('Y-m-d H:i:s')
                );
                //download avatar
                if (!empty($g_user->avatar)) {
                    $this->load->model('upload_model');
                    $save_to = FCPATH . "uploads/temp/avatar-" . uniqid() . ".jpg";
                    @copy($g_user->avatar, $save_to);
                    if (!empty($save_to) && file_exists($save_to)) {
                        $data["avatar"] = $this->upload_model->avatar_upload($save_to);
                    }
                    @unlink($save_to);
                }
                if ($this->general_settings->vendor_verification_system != 1) {
                    $data['role_id'] = 2;
                }
                if (!empty($data['email'])) {
                    $this->db->insert('users', $data);
                    $user = $this->get_user_by_email($g_user->email);
                }
            }
            if (!empty($user)) {
                if ($user->banned == 1) {
                    $this->session->set_flashdata('error', trans("msg_ban_error"));
                    return false;
                }
                //login
                $this->login_direct($user);
            }
        }
    }

    //login with vk
    public function login_with_vk($vk_user)
    {
        if (!empty($vk_user)) {
            $user = $this->get_user_by_email($vk_user->email);
            //check if user registered
            if (empty($user)) {
                if (empty($vk_user->name)) {
                    $vk_user->name = "user-" . uniqid();
                }
                $username = $this->generate_uniqe_username($vk_user->name);
                $slug = $this->generate_uniqe_slug($username);
                //add user to database
                $data = array(
                    'vkontakte_id' => $vk_user->id,
                    'email' => $vk_user->email,
                    'email_status' => 1,
                    'token' => generate_unique_id(),
                    'role_id' => 3,
                    'username' => $username,
                    'first_name' => $vk_user->name,
                    'slug' => $slug,
                    'avatar' => "",
                    'user_type' => "vkontakte",
                    'last_seen' => date('Y-m-d H:i:s'),
                    'created_at' => date('Y-m-d H:i:s')
                );
                //download avatar
                if (!empty($vk_user->avatar)) {
                    $this->load->model('upload_model');
                    $save_to = FCPATH . "uploads/temp/avatar-" . uniqid() . ".jpg";
                    @copy($vk_user->avatar, $save_to);
                    if (!empty($save_to) && file_exists($save_to)) {
                        $data["avatar"] = $this->upload_model->avatar_upload($save_to);
                    }
                    @unlink($save_to);
                }
                if ($this->general_settings->vendor_verification_system != 1) {
                    $data['role_id'] = 2;
                }
                if (!empty($data['email'])) {
                    $this->db->insert('users', $data);
                    $user = $this->get_user_by_email($vk_user->email);
                }
            }
            if (!empty($user)) {
                if ($user->banned == 1) {
                    $this->session->set_flashdata('error', trans("msg_ban_error"));
                    return false;
                }
                //login
                $this->login_direct($user);
            }
        }
    }

    //generate uniqe username
    public function generate_uniqe_username($username)
    {
        $new_username = $username;
        if (!empty($this->get_user_by_username($new_username))) {
            $new_username = $username . " 1";
            if (!empty($this->get_user_by_username($new_username))) {
                $new_username = $username . " 2";
                if (!empty($this->get_user_by_username($new_username))) {
                    $new_username = $username . " 3";
                    if (!empty($this->get_user_by_username($new_username))) {
                        $new_username = $username . "-" . uniqid();
                    }
                }
            }
        }
        return $new_username;
    }

    //generate uniqe slug
    public function generate_uniqe_slug($username)
    {
        $slug = str_slug($username);
        if (!empty($this->get_user_by_slug($slug))) {
            $slug = str_slug($username . "-1");
            if (!empty($this->get_user_by_slug($slug))) {
                $slug = str_slug($username . "-2");
                if (!empty($this->get_user_by_slug($slug))) {
                    $slug = str_slug($username . "-3");
                    if (!empty($this->get_user_by_slug($slug))) {
                        $slug = str_slug($username . "-" . uniqid());
                    }
                }
            }
        }
        return $slug;
    }

    //register
    public function register()
    {
        $this->load->library('bcrypt');

        $data = $this->auth_model->input_values();
        $data['username'] = remove_special_characters(str_username($data['username']));
        //secure password
        $data['password'] = $this->bcrypt->hash_password($data['password']);
        $data['role_id'] = 3; // $data['role_id'] = 3mem/2ven/1admin;
        $data['user_type'] = "registered";
        $data["slug"] = $this->generate_uniqe_slug($data["username"]);
        $data['banned'] = 0;
        $data['acc_type'] = 1;
        $data['last_seen'] = date('Y-m-d H:i:s');
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['token'] = generate_token();
        $data['email_status'] = 1;
        $data['cover_image_type'] = $this->settings_model->get_cover_image_type()->cover_image_type;
        if ($this->general_settings->email_verification == 1) {
            $data['email_status'] = 0;
        }
        if ($this->general_settings->vendor_verification_system != 1) {
            // die("hrtr");
            $data['role_id'] = 3;
        }
        if ($this->db->insert('users', $data)) {
            $last_id = $this->db->insert_id();
            if ($this->general_settings->email_verification == 1) {
                $user = $this->get_user($last_id);
                if (!empty($user)) {
                    $this->session->set_flashdata('success', trans("msg_register_success") . " " . trans("msg_send_confirmation_email") . "&nbsp;<a href='javascript:void(0)' class='link-resend-activation-email' onclick=\"send_activation_email_register('" . $user->id . "','" . $user->token . "');\">" . trans("resend_activation_email") . "</a>");
                    $this->send_email_activation_ajax($user->id, $user->token);
                }
            }
            return $last_id;
        } else {
            return false;
        }
    }

    //send email activation
    public function send_email_activation($user_id, $token)
    {
        if (!empty($user_id)) {
            $user = $this->get_user($user_id);
            if (!empty($user)) {
                if (!empty($user->token) && $user->token != $token) {
                    exit();
                }
                //check token
                $data['token'] = $user->token;
                if (empty($data['token'])) {
                    $data['token'] = generate_token();
                    $this->db->where('id', $user->id);
                    $this->db->update('users', $data);
                }
                //send email
                $email_data = array(
                    'template_path' => "email/email_general",
                    'to' => $user->email,
                    'subject' => trans("confirm_your_account"),
                    'email_content' => trans("msg_confirmation_email"),
                    'email_link' => lang_base_url() . "confirm?token=" . $data['token'],
                    'email_button_text' => trans("confirm_your_account")
                );
                $this->load->model("email_model");
                $this->email_model->send_email($email_data);
            }
        }
    }

    //send email activation
    public function send_email_activation_ajax($user_id, $token)
    {
        if (!empty($user_id)) {
            $user = $this->get_user($user_id);
            if (!empty($user)) {
                if (!empty($user->token) && $user->token != $token) {
                    exit();
                }
                //check token
                $data['token'] = $user->token;
                if (empty($data['token'])) {
                    $data['token'] = generate_token();
                    $this->db->where('id', $user->id);
                    $this->db->update('users', $data);
                }

                //send email
                $email_data = array(
                    'email_type' => 'email_general',
                    'to' => $user->email,
                    'subject' => trans("confirm_your_account"),
                    'email_content' => trans("msg_confirmation_email"),
                    'email_link' => lang_base_url() . "confirm?token=" . $data['token'],
                    'email_button_text' => trans("confirm_your_account")
                );
                $this->session->set_userdata('mds_send_email_data', json_encode($email_data));
            }
        }
    }

    //add administrator
    public function add_user()
    {
        $this->load->library('bcrypt');

        $data = $this->auth_model->input_values();
        //secure password
        $data['password'] = $this->bcrypt->hash_password($data['password']);
        $data['user_type'] = "registered";
        $data["slug"] = $this->generate_uniqe_slug($data["username"]);
        $data['role_id'] = $this->input->post('role_id', true);
        $data['banned'] = 0;
        $data['email_status'] = 1;
        $data['token'] = generate_token();
        $data['last_seen'] = date('Y-m-d H:i:s');
        $data['created_at'] = date('Y-m-d H:i:s');

        return $this->db->insert('users', $data);
    }

    //update slug
    public function update_slug($id)
    {
        $id = clean_number($id);
        $user = $this->get_user($id);

        if (empty($user->slug) || $user->slug == "-") {
            $data = array(
                'slug' => "user-" . $user->id,
            );
            $this->db->where('id', $id);
            $this->db->update('users', $data);
        } else {
            if ($this->check_is_slug_unique($user->slug, $id) == true) {
                $data = array(
                    'slug' => $user->slug . "-" . $user->id
                );

                $this->db->where('id', $id);
                $this->db->update('users', $data);
            }
        }
    }

    //logout
    public function logout()
    {
        //unset user data
        $this->session->unset_userdata('mds_sess_user_id');
        $this->session->unset_userdata('mds_sess_user_email');
        $this->session->unset_userdata('mds_sess_user_ps');
        $this->session->unset_userdata('mds_sess_logged_in');
        $this->session->unset_userdata('mds_sess_app_key');
    }

    //reset password
    public function reset_password($id)
    {
        $id = clean_number($id);
        $this->load->library('bcrypt');
        $new_password = $this->input->post('password', true);
        $data = array(
            'password' => $this->bcrypt->hash_password($new_password),
            'token' => generate_token()
        );
        //change password
        $this->db->where('id', $id);
        return $this->db->update('users', $data);
    }

    //delete user
    public function delete_user($id)
    {
        $id = clean_number($id);
        $user = $this->get_user($id);
        if (!empty($user)) {
            //delete products
            $products = $this->db->where('user_id', $user->id)->get('products')->result();
            if (!empty($products)) {
                foreach ($products as $product) {
                    $this->product_admin_model->delete_product_permanently($product->id);
                }
            }
            return $this->db->where('id', $user->id)->delete('users');
        }
        return false;
    }

    //update last seen time
    public function update_last_seen()
    {
        if ($this->auth_check) {
            //update last seen
            $data = array(
                'last_seen' => date("Y-m-d H:i:s"),
            );
            $this->db->where('id', $this->auth_user->id);
            $this->db->update('users', $data);
        }
    }

    //get logged user
    public function get_logged_user()
    {
        if (!empty($this->session->userdata('mds_sess_user_id')) && $this->session->userdata('mds_sess_app_key') == $this->config->item('app_key')) {
            $user = $this->get_user($this->session->userdata('mds_sess_user_id'));
            if (!empty($user)) {
                if ($user->banned == 0) {
                    $sess_pass = $this->session->userdata("mds_sess_user_ps");
                    if (!empty($sess_pass) && md5($user->password) == $sess_pass) {
                        return $user;
                    }
                }
            }
        }
        return false;
    }

    //get user by id
    public function get_user($id)
    {
        return $this->db->select('users.*, (SELECT permissions FROM roles_permissions WHERE roles_permissions.id = users.role_id LIMIT 1) AS permissions,license_type.name As license_name')->join('license_type', 'license_type.id = users.license_type', 'left')->where('users.id', clean_number($id))->get('users')->row();
    }

    //get user by email
    public function get_user_by_email($email)
    {
        return $this->db->select('users.*, (SELECT permissions FROM roles_permissions WHERE roles_permissions.id = users.role_id LIMIT 1) AS permissions,license_type.name As license_name')->join('license_type', 'license_type.id = users.license_type', 'left')->where('users.email', remove_special_characters($email))->get('users')->row();
    }

    //get user by username
    public function get_user_by_username($username)
    {
        return $this->db->select('users.*, (SELECT permissions FROM roles_permissions WHERE roles_permissions.id = users.role_id LIMIT 1) AS permissions,license_type.name As license_name')->join('license_type', 'license_type.id = users.license_type', 'left')->where('users.username', remove_special_characters($username))->get('users')->row();
    }
    //get user by username
    public function get_user_by_phone($username)
    {
        return $this->db->select('users.*, (SELECT permissions FROM roles_permissions WHERE roles_permissions.id = users.role_id LIMIT 1) AS permissions,license_type.name As license_name')->join('license_type', 'license_type.id = users.license_type', 'left')->where('users.phone_number', remove_special_characters($username))->get('users')->row();
    }
    //get user by shop name
    public function get_user_by_shop_name($shop_name)
    {
        return $this->db->select('users.*, (SELECT permissions FROM roles_permissions WHERE roles_permissions.id = users.role_id LIMIT 1) AS permissions,license_type.name As license_name')->join('license_type', 'license_type.id = users.license_type', 'left')->where('users.shop_name', remove_special_characters($shop_name))->get('users')->row();
    }

    //get user by slug
    public function get_user_by_slug($slug)
    {
        return $this->db->select('users.*, (SELECT permissions FROM roles_permissions WHERE roles_permissions.id = users.role_id LIMIT 1) AS permissions,license_type.name As license_name')->join('license_type', 'license_type.id = users.license_type', 'left')->where('users.slug', remove_special_characters($slug))->get('users')->row();
    }

    //get user by token
    public function get_user_by_token($token)
    {
        return $this->db->select('users.*, (SELECT permissions FROM roles_permissions WHERE roles_permissions.id = users.role_id LIMIT 1) AS permissions,license_type.name As license_name')->join('license_type', 'license_type.id = users.license_type', 'left')->where('users.token', remove_special_characters($token))->get('users')->row();
    }

    //get users
    public function get_users()
    {
        return $this->db->get('users')->result();
    }

    //get users count
    public function get_users_count()
    {
        return $this->db->get('users')->num_rows();
    }

    //get paginated vendors
    public function get_paginated_vendors($per_page, $offset)
    {
        $this->filter_vendors();
        return $this->db->order_by('num_products DESC, created_at DESC')->limit(clean_number($per_page), clean_number($offset))->get('users')->result();
    }

    //get users count by role
    public function get_paginated_vendors_count()
    {
        $this->filter_vendors();
        return $this->db->count_all_results('users');
    }

    //filter vendor
    public function filter_vendors()
    {
        $q = input_get('q');
        $this->db->select("users.*, (SELECT COUNT(id) FROM products WHERE users.id = products.user_id AND products.status = 1 AND products.visibility = 1 AND products.is_draft = 0 AND products.is_deleted = 0) AS num_products");
        if ($this->general_settings->vendor_verification_system == 1) {
            $this->db->where('has_active_shop', 1);
        } else {
            $this->db->where('(SELECT COUNT(id) FROM products WHERE users.id = products.user_id AND products.status = 1 AND products.visibility = 1 AND products.is_draft = 0 AND products.is_deleted = 0) > 0');
        }
        $this->db->group_start()->where('banned', 0)->group_end();
        if (!empty($q)) {
            $this->db->group_start();
            $this->db->like("SELECT IF(users.shop_name IS NULL OR users.shop_name = '',users.username,users.shop_name)", clean_str($q));
            $this->db->group_end();
        }
    }

    //get paginated users
    public function get_paginated_filtered_users($role, $per_page, $offset)
    {
        $this->filter_users($role);
        $this->db->select('users.*, acc_type.id AS acc_type_id, acc_type.name AS acc_type,license_type.name AS license_name');
        $this->db->join('license_type', 'license_type.id = users.license_type', 'left');
        $this->db->join('acc_type', 'acc_type.id = users.acc_type');
        $this->db->order_by('created_at', 'DESC')->limit(clean_number($per_page), clean_number($offset));
        return $this->db->get('users')->result();
    }

    public function get_paginated_all_users($per_page, $offset)
    {
        $this->filter_all_users();
        // $this->db->select('users.*, roles_permissions.role_name AS role_name_array, roles_permissions.is_super_admin AS is_super_admin');
        // $this->db->join('roles_permissions', 'roles_permissions.id = users.role_id');
        // $this->db->select('users.*, acc_type.id AS acc_type_id, acc_type.name AS acc_type, license_type.name AS license_name');
        $this->db->join('acc_type', 'acc_type.id = users.acc_type');
        $this->db->join('license_type', 'license_type.id = users.license_type', 'left');
        $this->db->order_by('created_at', 'DESC')->limit(clean_number($per_page), clean_number($offset));
        return $this->db->get('users')->result();
    }

    //get users count by role
    public function get_users_count_by_role($role)
    {
        $this->filter_users($role);
        return $this->db->count_all_results('users');
    }
    public function get_users_all_count()
    {
        // $this->filter_users($role);
        return $this->db->count_all_results('users');
    }

    //users filter
    public function filter_users($role)
    {
        $q = input_get('q');
        if (!empty($q)) {
            $this->db->group_start();
            $this->db->like('username', clean_str($q));
            $this->db->or_like('email', clean_str($q));
            $this->db->group_end();
        }
        $status = input_get('status');
        if (!empty($status)) {
            $banned = $status == 'banned' ? 1 : 0;
            $this->db->where('banned', $banned);
        }
        $email_status = input_get('email_status');
        if (!empty($email_status)) {
            $status = $email_status == 'confirmed' ? 1 : 0;
            $this->db->where('email_status', $status);
        }
        $acc_type = input_get('acc_type');
        if (!empty($acc_type)) {
            $status = $acc_type;
            $this->db->where('acc_type', $status);
        }
        // $acc_type = input_get('acc_type');
        // if (!empty($acc_type)) {
        //     $status = $acc_type;
        //     $this->db->where('acc_type', $status);
        // }
        $this->db->select('users.*, roles_permissions.role_name AS role_name_array, roles_permissions.is_super_admin AS is_super_admin');
        $this->db->join('roles_permissions', 'roles_permissions.id = users.role_id');
        echo "<script>console.log('" . $role . "')</script>";
        if ($role == "admin") {
            $this->db->where('is_admin', 1);
        } elseif ($role == "vendor") {
            $this->db->where('is_vendor', 1);
        } else {
            $this->db->where('is_member', 1);
        }
    }
    //users filter
    public function filter_all_users()
    {
        $q = input_get('q');
        if (!empty($q)) {
            $this->db->group_start();
            $this->db->like('username', clean_str($q));
            $this->db->or_like('email', clean_str($q));
            $this->db->group_end();
        }
        $status = input_get('status');
        if (!empty($status)) {
            $banned = $status == 'banned' ? 1 : 0;
            $this->db->where('banned', $banned);
        }
        $email_status = input_get('email_status');
        if (!empty($email_status)) {
            $status = $email_status == 'confirmed' ? 1 : 0;
            $this->db->where('email_status', $status);
        }
        $acc_type = input_get('acc_type');
        if (!empty($acc_type)) {
            $acc_type_status = $acc_type;
            $this->db->where('acc_type', $acc_type_status);
        }
        $user_role = input_get('user_role');
        if (!empty($user_role)) {
            $user_role_num = $user_role;
            $this->db->where('role_id', $user_role_num);
        }
        $startdate = input_get('startdate');
        $enddate = input_get('enddate');
        if (!empty($startdate) || !empty($enddate)) {
            $start_timestamp = strtotime("$startdate" . " 00:00:00");
            $end_timestamp = strtotime("$enddate" . " 23:59:59");

            $start_datetime = date('Y-m-d H:i:s', $start_timestamp);
            $end_datetime = date('Y-m-d H:i:s', $end_timestamp);
            $this->db->where('created_at BETWEEN "' . $start_datetime . '" AND "' . $end_datetime . '"');
        }
        $this->db->select('users.*, roles_permissions.role_name AS role_name_array, roles_permissions.is_super_admin AS is_super_admin,acc_type.id AS acc_type_id, acc_type.name AS acc_type, license_type.name AS license_name');
        $this->db->join('roles_permissions', 'roles_permissions.id = users.role_id');
        // echo "<script>console.log('".$role."')</script>";
        // if ($role == "admin") {
        //     $this->db->where('is_admin', 1);
        // } elseif ($role == "vendor") {
        //     $this->db->where('is_vendor', 1);
        // } elseif ($role == "member") {
        //     $this->db->where('is_member', 1);
        // } 
        // else {
        //     // $this->db->where('is_admin', 0);
        //     // $this->db->where('is_vendor', 1);
        //     $this->db->where('is_member', 1);
        // }
        // $this->db->where('is_admin', 1);
        // $this->db->where('is_vendor', 1);
        // $this->db->where('is_member', 1);
    }
    //get latest members
    public function get_latest_members($limit)
    {
        $limit = clean_number($limit);
        $this->db->limit($limit);
        $this->db->order_by('users.id', 'DESC');
        $query = $this->db->get('users');
        return $query->result();
    }

    //get last users
    public function get_last_users()
    {
        $this->db->order_by('users.id', 'DESC');
        $this->db->limit(7);
        $query = $this->db->get('users');
        return $query->result();
    }

    //check slug
    public function check_is_slug_unique($slug, $id)
    {
        $id = clean_number($id);
        $this->db->where('users.slug', $slug);
        $this->db->where('users.id !=', $id);
        $query = $this->db->get('users');
        if ($query->num_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    //check if email is unique
    public function is_unique_email($email, $user_id = 0)
    {
        $user_id = clean_number($user_id);
        $user = $this->auth_model->get_user_by_email($email);

        //if id doesnt exists
        if ($user_id == 0) {
            if (empty($user)) {
                return true;
            } else {
                return false;
            }
        }

        if ($user_id != 0) {
            if (!empty($user) && $user->id != $user_id) {
                //email taken
                return false;
            } else {
                return true;
            }
        }
    }

    //check if username is unique
    public function is_unique_username($username, $user_id = 0)
    {
        $user = $this->get_user_by_username($username);

        //if id doesnt exists
        if ($user_id == 0) {
            if (empty($user)) {
                return true;
            } else {
                return false;
            }
        }

        if ($user_id != 0) {
            if (!empty($user) && $user->id != $user_id) {
                //username taken
                return false;
            } else {
                return true;
            }
        }
    }

    //check if shop name is unique
    public function is_unique_shop_name($shop_name, $user_id = 0)
    {
        if (empty($shop_name)) {
            return true;
        }
        $user = $this->get_user_by_shop_name($shop_name);
        //if id doesnt exists
        if ($user_id == 0) {
            if (empty($user)) {
                return true;
            } else {
                return false;
            }
        }

        if ($user_id != 0) {
            if (!empty($user) && $user->id != $user_id) {
                //shop name taken
                return false;
            } else {
                return true;
            }
        }
    }

    //check if shop name is unique
    public function is_unique_license_number($license_number, $user_id = 0)
    {
        $data = $this->db->where('license_number', $license_number)->get('users')->row();
        if ($user_id == 0) {
            if (!empty($data)) {
                //License Number exists
                return false;
            } else {
                return true;
            }
        }
        if($user_id != 0 ){
            if(!empty($data) && $data->id != $user_id){
                //License Number exists
                return false;
            }
            else{
                return true;
            }
        }
    }


    //verify email
    public function verify_email($user)
    {
        if (!empty($user)) {
            $data = array(
                'email_status' => 1,
                'token' => generate_token()
            );
            $this->db->where('id', $user->id);
            return $this->db->update('users', $data);
        }
        return false;
    }

    //ban or remove user ban
    public function ban_remove_ban_user($id)
    {
        $id = clean_number($id);
        $user = $this->get_user($id);

        if (!empty($user)) {
            $data = array();
            if ($user->banned == 0) {
                $data['banned'] = 1;
            }
            if ($user->banned == 1) {
                $data['banned'] = 0;
            }

            $this->db->where('id', $id);
            return $this->db->update('users', $data);
        }

        return false;
    }
}
