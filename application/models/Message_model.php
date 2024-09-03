<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Message_model extends CI_Model
{
    //add conversation
    public function add_conversation()
    {
        $data = array(
            'sender_id' => $this->auth_user->id,
            'receiver_id' => $this->input->post('receiver_id', true),
            'subject' => $this->input->post('subject', true),
            'product_id' => $this->input->post('product_id', true),
            'created_at' => date("Y-m-d H:i:s")
        );
        if (empty($data['product_id'])) {
            $data['product_id'] = 0;
        }
        if ($this->db->insert('conversations', $data)) {
            return $this->db->insert_id();
        }
        return false;
    }

    //add message
    public function add_message($conversation_id)
    {
        $conversation_id = clean_number($conversation_id);
        $data = array(
            'conversation_id' => $conversation_id,
            'sender_id' => $this->auth_user->id,
            'receiver_id' => $this->input->post('receiver_id', true),
            'message' => $this->input->post('message', true),
            'is_read' => 0,
            'deleted_user_id' => 0,
            'created_at' => date("Y-m-d H:i:s")
        );
        if (!empty($data['message'])) {
            return $this->db->insert('conversation_messages', $data);
        }
        return false;
    }

    //get unread conversations
    public function get_unread_conversations($user_id)
    {
        $user_id = clean_number($user_id);
        $query_unread_conversations = $this->get_user_unread_conversation_ids_query($user_id);
        $this->db->where("conversations.id IN ($query_unread_conversations)", NULL, FALSE);
        $this->db->order_by('conversations.created_at', 'DESC');
        $this->db->distinct();
        $query = $this->db->get('conversations');
        return $query->result();
    }

    //get read_conversations
    public function get_read_conversations($user_id)
    {
        $user_id = clean_number($user_id);
        $query_unread_conversations = $this->get_user_unread_conversation_ids_query($user_id);
        $query_conversations = $this->get_user_conversation_ids_query($user_id);
        $this->db->where("conversations.id IN ($query_conversations)", NULL, FALSE);
        // $this->db->where("conversations.id NOT IN ($query_unread_conversations)", NULL, FALSE);
        $this->db->order_by('conversations.created_at', 'DESC');
        $this->db->distinct();
        $query = $this->db->get('conversations');
        return $query->result();
    }

    public function get_important_conversations($user_id)
    {
        $user_id = clean_number($user_id);
        $query_unread_conversations = $this->get_user_unread_conversation_ids_query($user_id);
        $query_conversations = $this->get_user_conversation_ids_query($user_id);
        $this->db->where("conversations.id IN ($query_conversations)", NULL, FALSE);
        // $this->db->where("conversations.id NOT IN ($query_unread_conversations)", NULL, FALSE);
        $this->db->where("conversations.important", 1);
        $this->db->order_by('conversations.created_at', 'DESC');
        $this->db->distinct();
        $query = $this->db->get('conversations');
        return $query->result();
    }

    //get user latest conversation
    public function get_user_latest_conversation($user_id)
    {
        $user_id = clean_number($user_id);
        $this->db->join('conversation_messages', 'conversation_messages.conversation_id = conversations.id');
        $this->db->select('conversations.*, conversation_messages.is_read as is_read');
        $this->db->where('deleted_user_id != ', $this->auth_user->id);
        $this->db->group_start();
        $this->db->where('conversations.sender_id', $user_id);
        $this->db->or_where('conversations.receiver_id', $user_id);
        $this->db->group_end();
        $this->db->order_by('conversations.created_at', 'DESC');
        $query = $this->db->get('conversations');
        return $query->row();
    }

    //get user conversation
    public function get_user_conversation($id)
    {
        $this->db->where('conversation_id', clean_number($id));
        $this->db->where('deleted_user_id != ', $this->auth_user->id);
        $query = $this->db->get('conversation_messages');
        if ($query->num_rows() > 0) {
            $this->db->where('id', $id);
            $query = $this->db->get('conversations');
            return $query->row();
        }
        return false;
    }

    //get conversation
    public function get_conversation($id)
    {
        $id = clean_number($id);
        $this->db->where('id', $id);
        $query = $this->db->get('conversations');
        return $query->row();
    }

    //get messages
    public function get_messages($conversation_id)
    {
        $conversation_id = clean_number($conversation_id);
        $this->db->where('conversation_id', $conversation_id);
        $query = $this->db->get('conversation_messages');
        return $query->result();
    }

    //get unread conversation count
    public function get_unread_conversations_count($receiver_id)
    {
        $this->db->join('conversation_messages', 'conversation_messages.conversation_id = conversations.id');
        $this->db->select('conversations.*, conversation_messages.is_read as is_read');
        $this->db->where('conversation_messages.receiver_id', clean_number($receiver_id));
        $this->db->where('conversation_messages.is_read', 0);
        $this->db->where('conversation_messages.deleted_user_id !=', clean_number($receiver_id));
        $this->db->distinct();
        $query = $this->db->get('conversations');
        return $query->num_rows();
    }

    //set conversation messages as read
    public function set_conversation_messages_as_read($conversation_id)
    {
        $conversation_id = clean_number($conversation_id);
        $messages = $this->get_unread_messages($conversation_id);
        if (!empty($messages)) {
            foreach ($messages as $message) {
                if ($message->receiver_id == $this->auth_user->id) {
                    $data = array(
                        'is_read' => 1
                    );
                    $this->db->where('id', $message->id);
                    $this->db->update('conversation_messages', $data);
                }
            }
        }
    }

    //get unread messages
    public function get_unread_messages($conversation_id)
    {
        $conversation_id = clean_number($conversation_id);
        $this->db->where('conversation_id', $conversation_id);
        $this->db->where('receiver_id', $this->auth_user->id);
        $this->db->where('is_read', 0);
        $this->db->order_by('id', 'DESC');
        $query = $this->db->get('conversation_messages');
        return $query->result();
    }

    //get conversation unread messages count
    public function get_conversation_unread_messages_count($conversation_id)
    {
        $conversation_id = clean_number($conversation_id);
        $this->db->where('conversation_id', $conversation_id);
        $this->db->where('receiver_id', $this->auth_user->id);
        $this->db->where('is_read', 0);
        $query = $this->db->get('conversation_messages');
        return $query->num_rows();
    }

    //get user unread conversation ids
    public function get_user_unread_conversation_ids_query($user_id)
    {
        $user_id = clean_number($user_id);
        $this->db->select('conversation_id');
        $this->db->where('receiver_id', $user_id);
        $this->db->where('deleted_user_id !=', $user_id);
        $this->db->where('is_read', 0);
        $this->db->distinct();
        $this->db->from('conversation_messages');
        $query = $this->db->get_compiled_select();
        $this->db->reset_query();
        return $query;
    }

    //get user conversation ids
    public function get_user_conversation_ids_query($user_id)
    {
        $user_id = clean_number($user_id);
        $this->db->select('conversation_id');
        $this->db->group_start();
        $this->db->where('sender_id', $user_id);
        $this->db->or_where('receiver_id', $user_id);
        $this->db->group_end();
        $this->db->where('deleted_user_id !=', $user_id);
        $this->db->distinct();
        $this->db->from('conversation_messages');
        $query = $this->db->get_compiled_select();
        $this->db->reset_query();
        return $query;
    }

    //delete conversation
    public function delete_conversation($id)
    {
        $id = clean_number($id);
        $conversation = $this->get_conversation($id);
        if (!empty($conversation)) {
            $messages = $this->get_messages($conversation->id);

            if (!empty($messages)) {
                foreach ($messages as $message) {
                    if ($message->sender_id == $this->auth_user->id || $message->receiver_id == $this->auth_user->id) {
                        if ($message->deleted_user_id == 0) {
                            $data = array(
                                'deleted_user_id' => $this->auth_user->id
                            );
                            $this->db->where('id', $message->id);
                            $this->db->update('conversation_messages', $data);
                        } else {
                            $this->db->where('id', $message->id);
                            $this->db->delete('conversation_messages');
                        }
                    }
                }
            }

            //delete conversation if does not have messages
            $messages = $this->get_messages($conversation->id);
            if (empty($messages)) {
                $this->db->where('id', $conversation->id);
                $this->db->delete('conversations');
            }
        }
    }
    public function  add_fav_conversation($id)
    {
        $data = array(
            'important' => 1
        );
        $this->db->where('conversations.id', $id);
        $this->db->update('conversations', $data);
    }
    public function  delete_fav_conversation($id)
    {
        $data = array(
            'important' => 0
        );
        $this->db->where('conversations.id', $id);
        $this->db->update('conversations', $data);
    }
    public function get_all_conversations_model_auth_user($auth_id)
    {
        $sql = "SELECT m.* From messages_conver m LEFT JOIN messages_deleted d ON m.id = d.message_id AND d.user_id = $auth_id WHERE (d.message_id IS NOT NULL AND d.is_deleted = 0) OR d.message_id IS NULL AND (m.receiver_id = $auth_id OR m.sender_id = $auth_id)";
        $query = $this->db->query($sql);
        return $query->result();
    }
    public function get_all_important_conversations_model_auth_user($auth_id)
    {
        $sql = "SELECT m.* From messages_conver m LEFT JOIN messages_deleted d ON m.id = d.message_id AND d.user_id = $auth_id WHERE (d.message_id IS NOT NULL AND d.is_deleted = 0) AND (m.receiver_id = $auth_id OR m.sender_id = $auth_id) AND d.is_important = 1";
        $query = $this->db->query($sql);
        return $query->result();
    }
    public function check_conversation_exist($con_id)
    {
        $this->db->where('id', $con_id);
        $query = $this->db->get('messages_conver');
        return $query->row();
    }
    public function get_last_id_conversation($auth_id)
    {
        $this->db->where('receiver_id', $auth_id);
        $this->db->order_by('id', 'DESC')->limit(1);
        $query = $this->db->get('messages_conver');
        return $query->row();
    }
    public function get_conversation_by_users($user_id, $auth_id)
    {
        $this->db->group_start();
        $this->db->where('sender_id', $user_id);
        $this->db->where('receiver_id', $auth_id);
        $this->db->group_end();
        $this->db->or_group_start();
        $this->db->where('receiver_id', $user_id);
        $this->db->where('sender_id', $auth_id);
        $this->db->group_end();
        $query = $this->db->get('messages_conver');
        return $query;
    }
    public function get_conversation_by_id($con_id)
    {
        $this->db->where('id', $con_id);
        $query = $this->db->get('messages_conver');
        return $query->row();
    }
    public function get_user_conversation_message_id_model($conversation_id)
    {
        $this->db->where('message_id', $conversation_id);
        $query = $this->db->get('messages_data');
        return $query->result();
    }
    public function get_last_id($user_id, $auth_id)
    {
        $this->db->group_start();
        $this->db->where('sender_id', $user_id);
        $this->db->where('receiver_id', $auth_id);
        $this->db->group_end();
        $this->db->or_group_start();
        $this->db->where('receiver_id', $user_id);
        $this->db->where('sender_id', $auth_id);
        $this->db->group_end();
        $this->db->order_by('id', 'DESC')->limit(1);
        $query = $this->db->get('messages_data');
        return $query->row();
    }
    public function get_new_messages($last_message_id, $user_id, $auth_id)
    {
        $sql = "SELECT * FROM `messages_data` WHERE ((`sender_id` = $user_id AND `receiver_id` = $auth_id) OR (`sender_id` = $auth_id AND `receiver_id` = $user_id)) AND `id` > $last_message_id ";
        $query = $this->db->query($sql);
        $result = $query->result();
        return $result;
    }
    public function add_new_conversation_model($data)
    {
        $query = $this->db->insert('messages_conver', $data);
        $insert_id = $this->db->insert_id();
        return $insert_id;
    }
    public function send_conversation_model($data)
    {
        $query = $this->db->insert('messages_data', $data);
        $insert_id = $this->db->insert_id();
        return $insert_id;
    }
    public function get_last_message_by_con_id($con_id)
    {
        $this->db->where('message_id', $con_id);
        $this->db->order_by('id', 'DESC')->limit(1);
        $query = $this->db->get('messages_data');
        return $query->row();
    }
    public function get_new_message_by_con_id($con_id)
    {
        $this->db->where('message_id', $con_id);
        $this->db->order_by('id', 'DESC')->limit(1);
        $query = $this->db->get('messages_data');
        return $query->row();
    }
    public function get_all_users($auth_id)
    {
        $is_not_restricted =  $this->license_model->get_license_restriction_setting();
        $licenseRisData = $this->license_model->get_allowed_licenses();
        $licenses_allowed = json_decode($licenseRisData->allowed_id);

        if ($is_not_restricted) {
            $this->db->where_in('license_type',  $licenses_allowed);
        }
        $this->db->where('id !=', $auth_id);
        $this->db->limit(10);
        $query = $this->db->get('users');
        return $query->result();
    }
    public function get_new_conversations($auth_id, $last_id)
    {
        $this->db->where('receiver_id', $auth_id);
        $this->db->where('id >', $last_id);
        //$this->db->order_by('id', 'DESC');
        $query = $this->db->get('messages_conver');
        return $query;
    }


    public function userSearch($inputText, $auth_id)
    {
        $is_not_restricted =  $this->license_model->get_license_restriction_setting();
        $licenseRisData = $this->license_model->get_allowed_licenses();
        $licenses_allowed = json_decode($licenseRisData->allowed_id);

        $this->db->group_start();
        $this->db->like('username', $inputText, 'both');
        $this->db->or_like('first_name', $inputText, 'both');
        $this->db->or_like('shop_name', $inputText, 'both');
        $this->db->group_end();
        
        if ($is_not_restricted) {
            $this->db->where_in('license_type',  $licenses_allowed);
        }
        $this->db->where('id !=', $auth_id);
        $query = $this->db->get('users');
        return $query;
    }

    public function get_unread_conversation($auth_id)
    {
        $sql = "SELECT *
    FROM messages_data
    WHERE receiver_id = $auth_id AND is_read = 0
    GROUP BY `message_id`
    ORDER BY MAX(id) DESC";
        // $this->db->select('id');

        // $this->db->distinct();
        $query = $this->db->query($sql);
        return $query;
    }
    public function read_messages($con_id, $auth_id)
    {
        $this->db->set('is_read', 1); // Set the column value to 1 (assuming 1 represents "read")
        $this->db->where('message_id', $con_id);
        $this->db->where('receiver_id', $auth_id); // Specify the condition for the update
        $this->db->update('messages_data'); // Update the table 'messages'
    }
    public function deleted_conversation($id)
    {
        $sql = "SELECT m.* From messages_conver m LEFT JOIN messages_deleted d ON m.id = d.message_id AND d.user_id = $id WHERE (d.message_id IS NOT NULL AND d.is_deleted = 0) OR d.message_id IS NULL AND (m.receiver_id = $id OR m.sender_id = $id)";
        $query = $this->db->query($sql);
        return $query->result();
    }
    public function get_deleted_conversation($con_id, $user_id)
    {
        $sql = "SELECT `id`,`is_important` from messages_deleted where message_id = $con_id AND user_id = $user_id";
        $query = $this->db->query($sql);
        return $query->row();
    }
    public function post_delete_conversation($con_id, $auth_id)
    {
        $data = array(
            "message_id" => $con_id,
            "user_id" => $auth_id,
            "is_deleted" => 1
        );
        return $this->db->insert('messages_deleted', $data);
    }
    public function update_deleted_conversation($id, $value)
    {
        $this->db->set('is_deleted', $value); // Set the column value to 1 (assuming 1 represents "read")
        $this->db->where('id', $id);
        // Specify the condition for the update
        $this->db->update('messages_deleted');
    }
    public function update_deleted_conversation_message_send($messages_id, $value)
    {
        $this->db->set('is_deleted', $value); // Set the column value to 1 (assuming 1 represents "read")
        $this->db->where('message_id', $messages_id);
        // Specify the condition for the update
        $this->db->update('messages_deleted');
    }
    public function update_important_conversation($messages_id, $value)
    {
        $this->db->set('is_important', $value); // Set the column value to 1 (assuming 1 represents "read")
        $this->db->where('id', $messages_id);
        // Specify the condition for the update
        $this->db->update('messages_deleted');
    }
    public function post_important_conversation($con_id, $auth_id)
    {
        $data = array(
            "message_id" => $con_id,
            "user_id" => $auth_id,
            "is_important" => 1
        );
        return $this->db->insert('messages_deleted', $data);
    }
}
