<?php

class Notification_controller extends Home_Core_Controller{

    public function get_notification(){
      $data['notification'] = $this->notification_model->get_notification();

      
    }
    public function delete_notification(){

      $notification_id = $this->input->post('notification_id', true);
      $notification = $this->notification_model->get_single_notification($notification_id)->row();

     if($notification->notification_type == 4){
      if($notification->deleted_by == NULL){
        $deleted_rows = $this->notification_model->soft_delete_notification($notification_id, $this->auth_user->id);
      }
      else{
        $deleted_rows = $this->notification_model->delete_notification($notification_id);
      }
     }
     else{
      $deleted_rows = $this->notification_model->delete_notification($notification_id);
     }

      
      if($deleted_rows  === 1 ){
      echo json_encode(["status" => true]);
      return 0;
      }
      echo json_encode(["status" => false]);
      return 0;
    }
    public function get_notification_count(){
      $notifications = $this->notification_model->get_notification();
      echo json_encode(["count" =>  $notifications['notification_count'], "notifications"=>$notifications['notifications']]);
    }
    
}