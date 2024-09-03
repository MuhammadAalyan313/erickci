<?php

class Notification_model extends CI_Model{
    public function set_notification($data){
        $this->load->library('sockets');
        $this->db->insert('notifications', $data);
        $notification_id = $this->db->insert_id();
        if($notification_id){
            $this->sockets->create_connection();
            return true;
        }
        else{
            return false;
        }
    }
    
    public function reviews_notification($data){

       
        $product = $this->get_product($data['product_id'])->row();

        $data['user_id'] = $product->user_id;
        $data['auth_id'] = $this->auth_user->id;
        $data['notification_type'] = '3';
        // var_dump($data);
        // die;
       
        $this->set_notification($data);

    }
    
    public function get_notification(){
        $notifications = $this->notification_query_build()->result();
        $notification_count = $this->notification_query_build()->num_rows();
       foreach ($notifications as $notification) {
        // if($notification->notification_type == 2 || $notification->notification_type== 3)
        $notification->user = $this->get_users($notification->auth_id)->row();
        $notification->product = $this->get_product($notification->product_id)->row();
       }
    //    echo '<pre>';
    //    var_dump($notifications);
    //    echo '</pre>';
        // return $notifications;
       return ["notifications"=>$notifications, "notification_count"=>$notification_count];
    }
    public function notification_query_build(){
        // $this->db->select('*');
        // $this->db->where('user_id',$this->auth_user->id);
        // $this->db->or_group_start();
        // $this->db->where('commit_parent', $this->auth_user->id);
        // $this->db->group_end();
        // $this->db->group_start();
        // $this->db->where('deleted_by !=', $this->auth_user->id);
        // $this->db->where('deleted_by IS NULL');
        // $this->db->group_end();
        // $this->db->order_by('id',"DESC");
        // return $this->db->get('notifications')->result();
        return $this->db->query('SELECT * from notifications where (user_id = ? OR commit_parent = ?) AND (deleted_by != ? OR deleted_by is null) ORDER BY created_at DESC', [$this->auth_user->id, $this->auth_user->id, $this->auth_user->id]);
    }
    public function get_product($id){
        return $this->db->query('SELECT products.slug,products.user_id, product_details.title FROM products JOIN product_details ON products.id = product_details.product_id WHERE products.id = ?',$id);
        // $this->db->where('id', $id);
       
}
public function get_users($id){
    $this->db->select('*');
    $this->db->where('id', $id);
    return $this->db->get('users');
}
public function delete_notification($notification_id){
    $this->db->where('id', $notification_id);
    $this->db->delete('notifications');
    if ($this->db->affected_rows() > 0) {
        return 1;
    } else {
        return 0;
    }
}
public function soft_delete_notification($notification_id, $user_id){
    $data = array(
        'deleted_by'=> $user_id,
        // Add more columns and their new values here
    );
    $this->db->where('id', $notification_id);
    $this->db->update('notifications', $data);
    if ($this->db->affected_rows() > 0) {
        return 1;
    } else {
        return 0;
    }
}
public function get_single_notification($id){
    $this->db->select('*');
    $this->db->where('id', $id);
    return $this->db->get('notifications');
}
}