<?php
defined('BASEPATH') or exit('No direct script access allowed');
require  __DIR__ . '/../../vendor/autoload.php';

use Chat\Chat;
use Chat\WebSocketConnections;
use WebSocket\Client;
class Message_controller extends Home_Core_Controller
{
    public $last_message_id;
    public function __construct()
    {
        parent::__construct();
        //check user
        if (!$this->auth_check) {
            redirect(lang_base_url());
        }
    }

    /**
     * Messages
     */
    public function messages()
    {
        $data['title'] = trans("messages");
        $data['description'] = trans("messages") . " - " . $this->app_name;
        $data['keywords'] = trans("messages") . "," . $this->app_name;
        $data['inbox'] = 'active';
        $data['unread'] = '';
        $data['important'] = '';
        $data['conversation'] = $this->message_model->get_user_latest_conversation($this->auth_user->id);
        $data['user_session'] = get_usession();
        if (!empty($data['conversation'])) {
            $data['unread_conversations'] = $this->message_model->get_unread_conversations($this->auth_user->id);
            $data['read_conversations'] = $this->message_model->get_read_conversations($this->auth_user->id);
            $data['get_conversations'] = $this->message_model->get_read_conversations($this->auth_user->id);
            $data['messages'] = $this->message_model->get_messages($data['conversation']->id);

            $data['conversations'] = '';
            // $this->message_model->set_conversation_messages_as_read($data['conversation']->id);
        }

        $this->load->view('partials/_header', $data);
        $this->load->view('message/messages', $data);
        $this->load->view('partials/_footer');
    }
    public function unread_messages($args)
    {
        $data['title'] = trans("messages");
        $data['description'] = trans("messages") . " - " . $this->app_name;
        $data['keywords'] = trans("messages") . "," . $this->app_name;
        $data['unread'] = 'active';
        $data['important'] = '';
        $data['inbox'] = '';
        $data['conversation'] = $this->message_model->get_user_latest_conversation($this->auth_user->id);
        $data['user_session'] = get_usession();
        if (!empty($data['conversation'])) {
            $data['get_conversations'] = $this->message_model->get_unread_conversations($this->auth_user->id);
            $data['read_conversations'] = $this->message_model->get_read_conversations($this->auth_user->id);
            $data['unread_conversations'] = $this->message_model->get_unread_conversations($this->auth_user->id);
            $data['messages'] = $this->message_model->get_messages($data['conversation']->id);
            // $this->message_model->set_conversation_messages_as_read($data['conversation']->id);

            $data['conversations'] = '';
        }

        $this->load->view('partials/_header', $data);
        $this->load->view('message/messages', $data);
        $this->load->view('partials/_footer');
    }
    public function important_messages()
    {
        $data['title'] = trans("messages");
        $data['description'] = trans("messages") . " - " . $this->app_name;
        $data['keywords'] = trans("messages") . "," . $this->app_name;
        $data['unread'] = '';
        $data['important'] = 'active';
        $data['inbox'] = '';
        $data['conversation'] = $this->message_model->get_user_latest_conversation($this->auth_user->id);
        $data['user_session'] = get_usession();
        if (!empty($data['conversation'])) {
            $data['get_conversations'] = $this->message_model->get_important_conversations($this->auth_user->id);
            $data['read_conversations'] = $this->message_model->get_read_conversations($this->auth_user->id);
            $data['unread_conversations'] = $this->message_model->get_unread_conversations($this->auth_user->id);
            $data['important_conversations'] = '';
            $data['messages'] = $this->message_model->get_messages($data['conversation']->id);
            // $this->message_model->set_conversation_messages_as_read($data['conversation']->id);

            $data['conversations'] = '';
        }

        $this->load->view('partials/_header', $data);
        $this->load->view('message/messages', $data);
        $this->load->view('partials/_footer');
    }

    /**
     * Conversation
     */
    public function conversation($id)
    {

        $data['title'] = trans("messages");
        $data['description'] = trans("messages") . " - " . $this->app_name;
        $data['keywords'] = trans("messages") . "," . $this->app_name;

        $data['conversation'] = $this->message_model->get_user_conversation($id);

        //check message
        if (empty($data['conversation'])) {
            redirect(generate_url("messages"));
        }
        //check message owner
        if ($this->auth_user->id != $data['conversation']->sender_id && $this->auth_user->id != $data['conversation']->receiver_id) {
            redirect(generate_url("messages"));
        }
        $data['get_conversations'] = $this->message_model->get_read_conversations($this->auth_user->id);
        $data['unread_conversations'] = $this->message_model->get_unread_conversations($this->auth_user->id);
        $data['read_conversations'] = $this->message_model->get_read_conversations($this->auth_user->id);
        $data['messages'] = $this->message_model->get_messages($data['conversation']->id);
        $data['user_session'] = get_usession();
        $this->message_model->set_conversation_messages_as_read($data['conversation']->id);
        $data['inbox'] = 'active';
        $data['unread'] = '';
        $data['important'] = '';
        $data['conversations'] = 'conversation';

        $this->load->view('partials/_header', $data);
        $this->load->view('message/messages', $data);
        $this->load->view('partials/_footer');
    }

    /**
     * Send Message
     */
    public function send_message()
    {

        $options = array(
            'cluster' => 'ap1',
            'useTLS' => true
        );
        $pusher = new Pusher\Pusher(
            'b46af13a222b64f2fe66',
            '89d7ddccd49aa03f1698',
            '1560951',
            $options
        );
        $conversation_id = $this->input->post('conversation_id', true);
        if ($this->message_model->add_message($conversation_id)) {
            $conversation = $this->message_model->get_conversation($conversation_id);
            if (!empty($conversation)) {
                //send email
                $sender_id = $this->auth_user->id;
                $receiver_id = $this->input->post('receiver_id', true);
                $message = $this->input->post('message', true);
                $user = get_user($receiver_id);

                if (!empty($user) && $user->send_email_new_message == 1 && !empty($message)) {
                    $email_data = array(
                        'email_type' => 'new_message',
                        'sender_id' => $sender_id,
                        'receiver_id' => $receiver_id,
                        'message_subject' => $conversation->subject,
                        'message_text' => $message
                    );
                    $this->session->set_userdata('mds_send_email_data', json_encode($email_data));
                }
                $messageData['data']['authId'] = $this->auth_user->id;
                $messageData['data']['senderId'] = $sender_id;
                $messageData['data']['senderSlug'] = get_profile_url_by_id($sender_id);
                $messageData['data']['senderImage'] = get_user_avatar_by_id($sender_id);

                $messageData['data']['receiverId'] = $receiver_id;
                $messageData['data']['receiverSlug'] = get_profile_url_by_id($receiver_id);
                $messageData['data']['receiverImage'] = get_user_avatar_by_id($receiver_id);
                $messageData['data']['message'] = $message;
                $pusher->trigger('pusherChat', 'my-event', $messageData);
            }
        }

        return json_encode("sdf");
        // redirect($this->agent->referrer());
    }

    /**
     * Add Conversation
     */

    public function add_conversation()
    {
        $data = array(
            'result' => 0,
            'sender_id' => 0,
            'html_content' => ""
        );
        if ($this->auth_user->id == $this->input->post('receiver_id', true)) {
            $this->session->set_flashdata('error', trans("msg_message_sent_error"));
            $data["result"] = 1;
            $data["html_content"] = $this->load->view('partials/_messages', null, true);
            reset_flash_data();
        } else {
            $conversation_id = $this->message_model->add_conversation();
            if ($conversation_id) {
                if ($this->message_model->add_message($conversation_id)) {
                    $this->session->set_flashdata('success', trans("msg_message_sent"));
                    $data["result"] = 1;
                    $data["sender_id"] = $this->auth_user->id;
                    $data["html_content"] = $this->load->view('partials/_messages', null, true);
                    reset_flash_data();
                } else {
                    $this->session->set_flashdata('error', trans("msg_error"));
                    $data["result"] = 1;
                    $data["html_content"] = $this->load->view('partials/_messages', null, true);
                    reset_flash_data();
                }
            } else {
                $this->session->set_flashdata('error', trans("msg_error"));
                $data["result"] = 1;
                $data["html_content"] = $this->load->view('partials/_messages', null, true);
                reset_flash_data();
            }
        }
        echo json_encode($data);
    }


    /**
     * Delete Conversation
     */
    public function delete_conversation()
    {
        $conversation_id = $this->input->post('conversation_id', true);
        $this->message_model->delete_conversation($conversation_id);
    }

    public function add_fav_conversation()
    {
        $conversation_id = $this->input->post('conversation_id', true);
        $this->message_model->add_fav_conversation($conversation_id);
    }
    public function delete_fav_conversation()
    {
        $conversation_id = $this->input->post('conversation_id', true);
        $this->message_model->delete_fav_conversation($conversation_id);
    }

    /**
     * 
     * Single Thread Message System
     * 
     */

    public function view_conversation()
    {
        $data['title'] = trans("messages");
        $data['description'] = trans("messages") . " - " . $this->app_name;
        $data['keywords'] = trans("messages") . "," . $this->app_name;
        $data['user_id'] = $this->auth_user->id;
        $data['conversation'] = null;
        $data['get_conversations'] = $this->message_model->get_all_conversations_model_auth_user($data['user_id']);
        $data['inbox'] = 'active';
        $data['unread'] = '';
        $data['important'] = '';
        $get_conversation_last_id_obj = $this->get_conversation_last_id();
        if ($get_conversation_last_id_obj) {
            $data['last_conversation_id'] = $this->get_conversation_last_id()->id;
        } else {
            $data['last_conversation_id'] = 0;
        }
        $data['last_message_id'] = 0;
        $this->load->view('partials/_header', $data);
        $this->load->view('message/single/messages', $data);
        $this->load->view('partials/_footer');
        //    echo'<pre>';
        //     var_dump($data['get_conversations'] );
        //     echo "</pre>";
        //     die();
    }


    public function view_conversation_by_con_id($id)
    {
        $data['title'] = trans("messages");
        $data['description'] = trans("messages") . " - " . $this->app_name;
        $data['keywords'] = trans("messages") . "," . $this->app_name;

        $data['inbox'] = 'active';
        $data['unread'] = '';
        $data['important'] = '';
        $data['conversations'] = 'conversation';
        $get_conversation_last_id_obj = $this->get_conversation_last_id();
        if ($get_conversation_last_id_obj) {
            $data['last_conversation_id'] = $this->get_conversation_last_id()->id;
        } else {
            $data['last_conversation_id'] = 0;
        }

        $data['conversation'] = $this->message_model->get_conversation_by_id($id);


        if ($data['conversation'] == null) {
            $this->error_404();
        } else {
            if ($this->auth_user->id == $data['conversation']->sender_id) :
                $data['user_id'] = $data['conversation']->receiver_id;
            else :
                $data['user_id'] = $data['conversation']->sender_id;
            endif;
            if ($data['conversation']->sender_id == $this->auth_user->id) {
                $data['receiver_id'] = $data['conversation']->receiver_id;
            } else {
                $data['receiver_id'] = $data['conversation']->sender_id;
            }
            $data['conversation_exist_btw_users'] = $this->get_conversation_btw_user_id($data['receiver_id'], $this->auth_user->id)->row();
            $data['get_conversations'] = $this->message_model->get_all_conversations_model_auth_user($this->auth_user->id);
            $data['messages_by_conversation_id'] = true;


            $data['messages'] = $this->message_model->get_user_conversation_message_id_model($data['conversation_exist_btw_users']->id);

            $data['last_message_id'] = $this->get_last_conversation_id($data['receiver_id'], $this->auth_user->id);

            $new_conversation = $this->message_model->get_new_messages($data['last_message_id'], $id, $this->auth_user->id);
            // echo'<pre>as';
            //     var_dump($data['conversation']);
            //     echo "</pre>";
            $this->load->view('partials/_header', $data);
            $this->load->view('message/single/messages', $data);
            $this->load->view('partials/_footer');
        }
    }


    public function view_conversation_by_user_id($id)
    {
        $data['title'] = trans("messages");
        $data['description'] = trans("messages") . " - " . $this->app_name;
        $data['keywords'] = trans("messages") . "," . $this->app_name;

        $data['inbox'] = 'active';
        $data['unread'] = '';
        $data['important'] = '';
        $data['conversations'] = 'conversation';
        $data['user_id'] = $id;
        $get_conversation_last_id_obj = $this->get_conversation_last_id();
        if ($get_conversation_last_id_obj) {
            $data['last_conversation_id'] = $this->get_conversation_last_id()->id;
        } else {
            $data['last_conversation_id'] = 0;
        }

        $data['conversation_exist_btw_users'] = $this->get_conversation_btw_user_id($id, $this->auth_user->id)->row();
        $data['get_conversations'] = $this->get_conversation_btw_user_id($id, $this->auth_user->id)->result();

        if ($data['conversation_exist_btw_users']) {

            $data['messages_by_conversation_id'] = true;

            $data['conversation'] = $this->message_model->get_conversation_by_id($data['conversation_exist_btw_users']->id);
            $data['messages'] = $this->message_model->get_user_conversation_message_id_model($data['conversation_exist_btw_users']->id);

            $data['last_message_id'] = $this->get_last_conversation_id($id, $this->auth_user->id);
            $data['new_user_id'] = $id;
            // echo'<pre>';
            // var_dump(  $data['conversation']);
            // echo "</pre>";
        } else {
            // echo'<pre>';
            // var_dump("messages");
            // echo "</pre>";
            $data['new_user_id'] = $id;
            $data['last_message_id'] = $this->get_last_conversation_id($id, $this->auth_user->id);
            $data['messages_by_conversation_id'] = false;

            $data['conversation'] = '';
            $data['receiver_id'] = $id;
        }
        $new_conversation = $this->message_model->get_new_messages($data['last_message_id'], $id, $this->auth_user->id);

        $this->load->view('partials/_header', $data);
        $this->load->view('message/single/messages', $data);
        $this->load->view('partials/_footer');
    }

    public function get_new_conversation()
    {
        $last_id = 1;
        $user_id =   $this->input->post('user_id', true);
        $last_message_id =   $this->input->post('last_message_id', true);
        $sender_id =   $this->input->post('sender_id', true);
        $last_conversation_id_post =   $this->input->post('last_conversation_id', true);
        $sender_user = get_user($sender_id);
        $avatar = get_user_avatar($sender_user);
        $user_profile = generate_profile_url($sender_user->slug);
        $sender_user->avatar = $avatar;
        $sender_user->user_profile_link = $user_profile;
        //$sender_user = array_push($avatar);
        $last_conversation_get = $this->get_conversation_last_id();
        if ($last_conversation_get) {
            $last_conversation_id_get = $last_conversation_get->id;
        } else {
            $last_conversation_id_get = 0;
        }
        if ($last_conversation_id_get != $last_conversation_id_post) {
            $new_user_conversation = get_user($last_conversation_get->sender_id);
            $new_user_conversation->$avatar = get_user_avatar($new_user_conversation);
            $sender_user = $new_user_conversation;
        } else {
            $new_user_conversation = 'null';
        }
        // $last_message_id =$this->get_last_conversation_id( $user_id,$this->auth_user->id);

        $new_conversation = $this->message_model->get_new_messages($last_message_id, $user_id, $this->auth_user->id);
        // // while (empty($new_conversatio)) {
        // //     usleep(1000000); // Sleep for 1 second
        // // }
        // http_response_code(200);
        echo json_encode(["messages" => $new_conversation, "user_id" => $user_id, "last_message_id" => $last_message_id, "auth_id" => $this->auth_user->id, 'new_user_conversation' => $new_user_conversation, "sender_user" => $sender_user, "avatar" => $avatar, "last_conversation_id" => $last_conversation_id_get]);
        return 0;
    }


    public function get_conversation_last_id()
    {
        $last_conversation = $this->message_model->get_last_id_conversation($this->auth_user->id);
        return $last_conversation;
    }

    public function get_conversation_btw_user_id($user_id, $auth_id)
    {
        $conversation = $this->message_model->get_conversation_by_users($user_id, $auth_id);
        return $conversation;
    }

    public function get_conversation_by_id($id)
    {
        $conversation = $this->message_model->check_conversation_exist($id);
        return $conversation;
    }

    public function get_last_conversation_id($user_id, $auth_id)
    {
        $last_conversation_id = $this->message_model->get_last_id($user_id, $auth_id);

        if ($last_conversation_id) {
            $this->last_message_id = $last_conversation_id->id;
        } else {
            $this->last_message_id = 0;
        }
        return  $this->last_message_id;
    }



    public function send_conversation()
    {
        $fileName= null;

        $data = array();
        $message_id =   $this->input->post('conversation_id', true);

        $sender_id =   $this->input->post('senderId', true);

        $receiver_id =   $this->input->post('receiver_id', true);

        $content =   $this->input->post('message', true);

        if (!empty($_FILES['file'])) {
            $fileNameTemp = $this->upload_image();
            $files = [];
            $jsonCode = [];
            for ($i = 0; $i < count($fileNameTemp); $i++) {
                $key = "filepath";
                $value = $fileNameTemp[$i];
                $files[$key] = $value;
                $jsonCode[] = json_encode($files);
            }
            $fileName = json_encode($jsonCode);
            $filePath = http_build_query($_FILES['file']['name']);
        } else {
            $fileName = "";
            $filePath = '';
        }
        $conversation_exist_btw_users = $this->message_model->get_conversation_by_users($sender_id, $receiver_id)->row();
        if ($message_id == 0 && !$conversation_exist_btw_users) {
            $new_conversation = array(
                'sender_id' => $sender_id,
                'receiver_id' => $receiver_id,
            );
            $new_conversation_id = $this->message_model->add_new_conversation_model($new_conversation);

            if ($new_conversation_id) {
                $data['message_id'] = $new_conversation_id;
                $data['sender_id'] = $sender_id;
                $data['receiver_id'] = $receiver_id;
                $data['content'] = $content;
                $data['images'] = $fileName;
                $new_message = $this->message_model->send_conversation_model($data);
            }
            echo json_encode(["content" => $content, "file" => $fileName, "filePath" => $filePath,"status"=>true,
            "message"=>[
                'channel' => 'msg',
                'id'=> $receiver_id,
                'auth_id'=>$sender_id,
                "msg"=>[
                    "auth_id"=>$sender_id,
                    "id"=>$receiver_id,
                    "content"=>$content,
                    'images'=>$fileName,
                    "file"=>$fileName,
                   "sender_details"=>[
                       "avatar"=>$this->auth_user->avatar,
                        "id"=>$this->auth_user->id,
                        "username"=>$this->auth_user->username,
                    ]
                ]
        ]]);
            return 0;
        } elseif ($message_id == 0 && $conversation_exist_btw_users) {
            $data['message_id'] = $conversation_exist_btw_users->id;
            $data['sender_id'] = $sender_id;
            $data['receiver_id'] = $receiver_id;
            $data['content'] = $content;
            $data['images'] = $fileName;
            $new_message = $this->message_model->send_conversation_model($data);
            $deletedMessages = $this->message_model->get_deleted_conversation($conversation_exist_btw_users->id, $sender_id);
            if ($deletedMessages) {
                $this->message_model->update_deleted_conversation_message_send($conversation_exist_btw_users->id, 0);
            }

            echo json_encode(["content" => $content, "file" => $fileName, "filePath" => $filePath,"status"=>true,
            "message"=>[
                'channel' => 'msg',
                'id'=> $receiver_id,
                'auth_id'=>$sender_id,
                "msg"=>[
                    "auth_id"=>$sender_id,
                    "id"=>$receiver_id,
                    "content"=>$content,
                    'images'=>$fileName,
                    "file"=>$fileName,
                    "sender_details"=>[
                       "avatar"=>$this->auth_user->avatar,
                        "id"=>$this->auth_user->id,
                        "username"=>$this->auth_user->username,
                    ]
                ]
            ]]);
            return 0;
        } else {

            $data['message_id'] =  $message_id;
            $data['sender_id'] = $sender_id;
            $data['receiver_id'] = $receiver_id;
            $data['content'] = $content;
            $data['images'] = $fileName;
            $deletedMessages = $this->message_model->get_deleted_conversation($conversation_exist_btw_users->id, $receiver_id);
            if ($deletedMessages) {
                $this->message_model->update_deleted_conversation_message_send($conversation_exist_btw_users->id, 0);
            }
            $new_message = $this->message_model->send_conversation_model($data);
            echo json_encode(["content" => $content, "file" => $fileName, "filePath" => $filePath,"status"=>true,
            "message"=>[
                'channel' => 'msg',
                'id'=> $receiver_id,
                'auth_id'=>$sender_id,
                "msg"=>[
                    "auth_id"=>$sender_id,
                    "id"=>$receiver_id,
                    "content"=>$content,
                    'images'=>$fileName,
                    "file"=>$fileName,
                    "sender_details"=>[
                        "avatar"=>$this->auth_user->avatar,
                        "id"=>$this->auth_user->id,
                        "username"=>$this->auth_user->username,
                    ]
                ]
            ]]);
            return 0;
        }
    }


    public function messages_widget()
    {
        $data['title'] = trans("messages");
        $data['description'] = trans("messages") . " - " . $this->app_name;
        $data['keywords'] = trans("messages") . "," . $this->app_name;

        $this->load->view('partials/_header', $data);
        $this->load->view('message/single/messages_widget', $data);
        $this->load->view('partials/_footer');
    }

    public function error_404()
    {
        get_method();
        header("HTTP/1.0 404 Not Found");
        $data['title'] = "Error 404";
        $data['description'] = "Error 404";
        $data['keywords'] = "error,404";

        $this->load->view('partials/_header', $data);
        $this->load->view('errors/error_404');
        $this->load->view('partials/_footer');
    }

    public function get_all_conversation_auth_id()
    {

        $conversation_all =  $this->message_model->get_all_conversations_model_auth_user($this->auth_user->id);

        echo json_encode(['all_conversations' =>  $conversation_all]);
        return 0;
    }
    public function get_all_important_conversation_auth_id()
    {

        $conversation_all =  $this->message_model->get_all_important_conversations_model_auth_user($this->auth_user->id);

        echo json_encode(['all_conversations' =>  $conversation_all]);
        return 0;
    }
    public function get_conversation_users()
    {
        $get_users;
        $sender_id = $this->input->post('sender_id', true);
        $receiver_id = $this->input->post('receiver_id', true);
        if ($sender_id == $this->auth_user->id) {
            $get_users = get_user($receiver_id);
        } else {
            $get_users = get_user($sender_id);
        }
        // $get_users =  $this->message_model->get_all_conversations_model_auth_user($this->auth_user->id);
        // get_user($sender_id);
        $avatar = get_user_avatar($get_users);
        $get_users->avatar = $avatar;
        $profile_url = generate_profile_url($get_users->slug);
        $get_users->profile_url = $profile_url;

        $is_online = is_user_online($get_users->last_seen);
        $get_users->is_online = $is_online;

        echo json_encode(['user' =>  $get_users]);
        return 0;
    }

    public function get_all_messages_conversation_id()
    {
        $conversation_id = $this->input->post('conversation_id', true);
        $imporatntConversationid = $this->message_model->get_deleted_conversation($conversation_id, $this->auth_user->id);
        $user_id = $this->input->post('user_id', true);
        $get_users = get_user($user_id);
        if ($imporatntConversationid) {
            $get_users->conImp = $imporatntConversationid->is_important;
        } else {
            $get_users->conImp = 0;
        }

        $auth_user = get_user($this->auth_user->id);
        $auth_avatar = get_user_avatar($auth_user);
        $auth_profile_url = generate_profile_url($auth_user->slug);
        $auth_user->avatar = $auth_avatar;
        $auth_user->profile_url = $auth_profile_url;
        // $get_users =  $this->message_model->get_all_conversations_model_auth_user($this->auth_user->id);
        // get_user($sender_id);
        $avatar = get_user_avatar($get_users);
        $get_users->avatar = $avatar;
        $profile_url = generate_profile_url($get_users->slug);
        $get_users->profile_url = $profile_url;
        $messages = $this->message_model->get_user_conversation_message_id_model($conversation_id);
        $message_read = $this->read_messages($conversation_id);
        echo json_encode(['messages' =>  $messages, "auth_id" => $this->auth_user->id, "user" => $get_users, 'auth_user' => $auth_user]);
        return 0;
    }
    public function get_all_messages_user_id()
    {
        //$conversation_id = $this->input->post('conversation_id', true);
        $user_id = $this->input->post('user_id', true);
        $get_users = get_user($user_id);
        $auth_user = get_user($this->auth_user->id);
        $auth_avatar = get_user_avatar($auth_user);
        $auth_profile_url = generate_profile_url($auth_user->slug);
        $auth_user->avatar = $auth_avatar;
        $auth_user->profile_url = $auth_profile_url;
        // $get_users =  $this->message_model->get_all_conversations_model_auth_user($this->auth_user->id);
        // get_user($sender_id);
        $avatar = get_user_avatar($get_users);
        $get_users->avatar = $avatar;
        $profile_url = generate_profile_url($get_users->slug);
        $get_users->profile_url = $profile_url;
        $conversation_exist_btw_users = $this->message_model->get_conversation_by_users($user_id, $this->auth_user->id)->row();


        if ($conversation_exist_btw_users) {
            $messages = $this->message_model->get_user_conversation_message_id_model($conversation_exist_btw_users->id);
            $imporatntConversationid = $this->message_model->get_deleted_conversation($conversation_exist_btw_users->id, $this->auth_user->id);
            if ($imporatntConversationid) {
                $get_users->conImp = $imporatntConversationid->is_important;
            } else {
                $get_users->conImp = 0;
            }
        } else {
            $get_users->conImp = 0;
            echo json_encode(['messages' =>  "no", "conversation_exist_btw_users" => $conversation_exist_btw_users, "auth_id" => $this->auth_user->id, "user" => $get_users, 'auth_user' => $auth_user]);
            return 0;
        }


        echo json_encode(['messages' =>  $messages, "conversation_exist_btw_users" => $conversation_exist_btw_users, "auth_id" => $this->auth_user->id, "user" => $get_users, 'auth_user' => $auth_user]);
        return 0;
    }
    public function get_auth_user()
    {
        $auth_user = get_user($this->auth_user->id);
        $auth_avatar = get_user_avatar($auth_user);
        $auth_profile_url = generate_profile_url($auth_user->slug);
        $auth_user->avatar = $auth_avatar;
        $auth_user->profile_url = $auth_profile_url;
        echo json_encode(['auth_user' => $auth_user]);
        return 0;
    }
    public function get_last_message_by_con_id()
    {
        $conversation_id = $this->input->post('conversation_id', true);
        $messages = $this->message_model->get_last_message_by_con_id($conversation_id);
        echo json_encode(['messages' =>  $messages]);
        return 0;
    }
    public function get_new_message_by_con_id()
    {
        $conversation_id = $this->input->post('conversation_id', true);
        $last_message_id = $this->input->post('last_message_id', true);
        $messages = $this->message_model->get_last_message_by_con_id($conversation_id);
        $sender_user = get_user($messages->sender_id);
        $sender_user_avatar = get_user_avatar($sender_user);
        $sender_user_profile_url = generate_profile_url($sender_user->slug);
        $sender_user->avatar = $sender_user_avatar;
        $sender_user->profile_url = $sender_user_profile_url;

        if ($messages->id == $last_message_id) {
            echo json_encode(['messages' =>  $messages, 'return' =>  0, "sender_user_user" => $sender_user]);
            return 0;
        } else {
            $message_read = $this->read_messages($conversation_id);
            echo json_encode(['messages' =>  $messages, 'return' =>  1, "sender_user" => $sender_user]);
            return 0;
        }
    }
    public function get_all_users()
    {

        $users = $this->message_model->get_all_users($this->auth_user->id);
        $length = count($users);
        for ($i = 0; $i < $length; $i++) {
            $is_online = is_user_online($users[$i]->last_seen);
            $users[$i]->is_online = $is_online;
            $avatars = get_user_avatar($users[$i]);
            $usersProfile = generate_profile_url($users[$i]->slug);
            $users[$i]->avatar = $avatars;
            $users[$i]->profile_url = $usersProfile;
        }

        echo json_encode(['users' =>  $users, 'return' =>  $users[0]]);
        return 0;
    }
    public function sendingMessage()
    {
        echo json_encode(["message" => "message"]);
    }
    public function get_new_conversations()
    {
        $conversation_id = $this->input->post('last_conversation_id', true);
        $conversation_new =  $this->message_model->get_new_conversations($this->auth_user->id, $conversation_id)->result();
        $conversation_last =  $this->message_model->get_new_conversations($this->auth_user->id, $conversation_id)->row();
        if ($conversation_last != null) {
            if ($conversation_last->id != $conversation_id) {
                echo json_encode(["conversation_new" =>  $conversation_new]);
                return 0;
            }
        } else {
            echo json_encode(["conversation_new" => "no"]);
            return 0;
        }
    }
    public function get_last_message_users()
    {
        $sender_id = $this->input->post('sender_id', true);
        $receiver_id = $this->input->post('receiver_id', true);
        $conversation_exist =  $this->message_model->get_conversation_by_users($sender_id, $receiver_id)->row();
        if ($conversation_exist) {
            $messages_last = $this->message_model->get_last_message_by_con_id($conversation_exist->id);

            echo json_encode(["messages" => $conversation_exist, "last_message_id" => $messages_last]);
            return 0;
        } else {
            echo json_encode(["messages" => "no"]);
            return 0;
        }
    }
    public function userSearch()
    {
        $search_text = $this->input->post('search_text', true);
        $userFound =  $this->message_model->userSearch($search_text, $this->auth_user->id)->result();
        if ($userFound) {
            $length = count($userFound);
            for ($i = 0; $i < $length; $i++) {
                $is_online = is_user_online($userFound[$i]->last_seen);
                $userFound[$i]->is_online = $is_online;
                $avatars = get_user_avatar($userFound[$i]);
                $usersProfile = generate_profile_url($userFound[$i]->slug);
                $userFound[$i]->avatar = $avatars;
                $userFound[$i]->profile_url = $usersProfile;
            }
            echo json_encode(['searchedUsers' => $userFound]);
            return 0;
        } else {
            echo json_encode(['searchedUsers' => "no"]);
            return 0;
        }
    }
    public function get_unread_conversation()
    {
        if ($this->auth_user) {
            $unread_con_data = $this->message_model->get_unread_conversation($this->auth_user->id)->result();
            $unread_con_count = $this->message_model->get_unread_conversation($this->auth_user->id)->num_rows();
            echo json_encode(['message_count' => $unread_con_count, "message_data" => $unread_con_data]);
            return 0;
        } else {
            echo json_encode(['message_count' => "no"]);
            return 0;
        }
        echo json_encode(['message_count' => "no"]);
    }
    public function read_messages($id)
    {
        $this->message_model->read_messages($id, $this->auth_user->id);
        return 'read';
    }
    public function post_update_delete_conversations()
    {
        $con_id = $this->input->post('con_id', true);
        $deletedConversationid = $this->message_model->get_deleted_conversation($con_id, $this->auth_user->id);
        if ($deletedConversationid) {
            $this->message_model->update_deleted_conversation($deletedConversationid->id, 1);
            echo json_encode(["return" => 1]);
            return 0;
        } else {
            $this->message_model->post_delete_conversation($con_id, $this->auth_user->id);
            echo json_encode(["return" => 1]);
            return 0;
        }
        echo json_encode(["return" => $deletedConversationid]);
        return 0;
    }
    public function post_update_important_conversations()
    {
        $con_id = $this->input->post('con_id', true);
        $value = $this->input->post('value', true);
        $imporatntConversationid = $this->message_model->get_deleted_conversation($con_id, $this->auth_user->id);
        if ($imporatntConversationid) {
            if ($imporatntConversationid->is_important == 1) {
                $this->message_model->update_important_conversation($imporatntConversationid->id, 0);
                echo json_encode(["return" => 1]);
                return 0;
            } else {
                $this->message_model->update_important_conversation($imporatntConversationid->id, 1);
                echo json_encode(["return" => 2]);
                return 0;
            }
        } else {
            $this->message_model->post_important_conversation($con_id, $this->auth_user->id);
            echo json_encode(["return" => 1]);
            return 0;
        }

        return 0;
    }
    public function markImp_userId()
    {
        $userId = $this->input->post('userId', true);
        $conversation_exist_btw_users = $this->message_model->get_conversation_by_users($userId, $this->auth_user->id)->row();
        $imporatntConversationid = $this->message_model->get_deleted_conversation($conversation_exist_btw_users->id, $this->auth_user->id);
        if ($imporatntConversationid) {
            if ($imporatntConversationid->is_important == 1) {
                $this->message_model->update_important_conversation($imporatntConversationid->id, 0);
                echo json_encode(["return" => 1]);
                return 0;
            } else {
                $this->message_model->update_important_conversation($imporatntConversationid->id, 1);
                echo json_encode(["return" => 2]);
                return 0;
            }
        } else {
            $this->message_model->post_important_conversation($conversation_exist_btw_users->id, $this->auth_user->id);
            echo json_encode(["return" => 1]);
            return 0;
        }
        // echo json_encode(["return"=>$conversation_exist_btw_users]);
        return 0;
    }
    // public function post_update_important_conversations(){
    //     $con_id = $this->input->post('con_id', true);
    //     $value = $this->input->post('value', true);

    //         $this->message_model->post_update_important_conversations($con_id,$value,$this->auth_user->id);
    //         echo json_encode(["return"=>1]);
    //         return 0;
    // }
    public function deleted_conversation()
    {
        $conversation =  $this->message_model->deleted_conversation(1);
        echo "<pre>";
        var_dump($conversation);
        echo "</pre>";
        return 0;
    }
    // public function upload_image(){
    //     $config['upload_path'] = './uploads/messenger/';
    //     $config['allowed_types'] = 'gif|jpg|png|jpeg|pdf';
    //     // $config['max_size'] = 2048;
    //     $config['encrypt_name'] = TRUE;

    //     $this->load->library('upload', $config);
    //     $this->load->library('image_lib', $config);

    //     if (!$this->upload->do_upload('file-input')) {
    //         $error = array('error' => $this->upload->display_errors());
    //         // $this->load->view('upload_form', $error);
    //         return $error;
    //     } 
    //     else {
    //         $data = array('upload_data' => $this->upload->data());
    //         $image_path =  $data['upload_data']['full_path'];
    //         $file_ext = pathinfo($image_path, PATHINFO_EXTENSION);

    //         // Check if the file is an image
    //         $is_image = in_array(strtolower($file_ext), array('gif', 'jpg', 'jpeg', 'png'));
    //         if($is_image){


    //             // Configuration for image manipulation (resizing)
    //             $resize_config['image_library'] = 'gd2'; // Change this based on your server's available library
    //             $resize_config['source_image'] = $image_path;
    //             $resize_config['create_thumb'] = false;
    //             $resize_config['maintain_ratio'] = true;
    //             $resize_config['width'] = 800; // Set the desired width for the resized image (in pixels)
    //             $resize_config['height'] = 600; // Set the desired height for the resized image (in pixels)
    //             $resize_config['dpi'] = 10; // Change this to your desired resolution (in dots per inch)

    //             $this->image_lib->initialize($resize_config);

    //             if (!$this->image_lib->resize()) {
    //                 // Handle the image resizing error
    //                 echo $this->image_lib->display_errors();
    //             } else {
    //                 // Image resized successfully, you can now save the resized image to a designated folder
    //                 $resized_image_path = './uploads/messenger/' .  $data['upload_data']['file_name'];
    //             // Get the file path
    //             // $file_path = 'uploads/messenger/' . $data['upload_data']['file_name'];
    //             return $resized_image_path;
    //             }
    //         }
    //         else{
    //              $file_path = 'uploads/messenger/' . $data['upload_data']['file_name'];
    //             return $file_path;
    //         }
    //     }
    // }
    public function upload_image()
    {
        $config['upload_path'] = './uploads/messenger/';
        $config['allowed_types'] = 'gif|jpg|png|jpeg|pdf';
        // $config['max_size'] = 2048;
        $config['encrypt_name'] = TRUE;

        $this->load->library('upload', $config);
        $this->load->library('image_lib', $config);

        $uploaded_files = array();

        foreach ($_FILES['file']['name'] as $key => $filename) {
            $_FILES['userfile']['name']     = $_FILES['file']['name'][$key];
            $_FILES['userfile']['type']     = $_FILES['file']['type'][$key];
            $_FILES['userfile']['tmp_name'] = $_FILES['file']['tmp_name'][$key];
            $_FILES['userfile']['error']    = $_FILES['file']['error'][$key];
            $_FILES['userfile']['size']     = $_FILES['file']['size'][$key];

            if (!$this->upload->do_upload('userfile')) {
                $error = array('error' => $this->upload->display_errors());
                // Handle the error (you can show the error or log it)
                $uploaded_files[] = $error;
            } else {
                $data = array('upload_data' => $this->upload->data());
                $image_path = $data['upload_data']['full_path'];
                $file_ext = pathinfo($image_path, PATHINFO_EXTENSION);

                // Check if the file is an image
                $is_image = in_array(strtolower($file_ext), array('gif', 'jpg', 'jpeg', 'png'));
                if ($is_image) {
                    // Configuration for image manipulation (resizing)
                    $resize_config['image_library'] = 'gd2'; // Change this based on your server's available library
                    $resize_config['source_image'] = $image_path;
                    $resize_config['create_thumb'] = false;
                    $resize_config['maintain_ratio'] = true;
                    $resize_config['width'] = 800; // Set the desired width for the resized image (in pixels)
                    $resize_config['height'] = 600; // Set the desired height for the resized image (in pixels)
                    $resize_config['dpi'] = 72; // Change this to your desired resolution (in dots per inch)

                    $this->image_lib->initialize($resize_config);

                    if (!$this->image_lib->resize()) {
                        // Handle the image resizing error
                        $error = $this->image_lib->display_errors();
                        // Handle the error (you can show the error or log it)
                        $uploaded_files[] = $error;
                    } else {
                        // Image resized successfully, you can now save the resized image to a designated folder
                        $resized_image_path = './uploads/messenger/' . $data['upload_data']['file_name'];
                        $uploaded_files[] = $resized_image_path;
                    }
                } else {
                    $file_path = 'uploads/messenger/' . $data['upload_data']['file_name'];
                    $uploaded_files[] = $file_path;
                }
            }
        }

        return $fileName = array_unique($uploaded_files);
    }
    public function check_chat(){
        // try {
        //     $serverAddress = 'ws://localhost:5412';
        //     $client = new Client($serverAddress);
        //     $messageData = [
        //         'channel' => 'msg', // or 'private' if sending to a specific user
        //         'msg' => [
        //             "content"=>"Message",
        //             "images"=>'',
        //             'id'=> 5,
        //             'auth_id'=>1,
        //             "file"=>''
        //         ],
        //         'id'=> 5,
        //         'auth_id'=>1,
        //     ];
            
        //     // Convert message data to JSON
        //     $messageJson = json_encode($messageData);
        //     // Send a message to WebSocket server
        //     $client->send($messageJson);

        //     // Close the connection
        //     $client->close();

        //     echo "Message sent to WebSocket server:\n";

        // } catch (Exception $e) {
        //     echo "Error sending message to WebSocket server: " . $e->getMessage() . "\n";
        // }
        
        var_dump(WebSocketConnections::connections());
        // $connections = $conns->getAllConnections();

        // Use $connections as needed in your controller logic
        // foreach ($connections as $conn) {
        //     // Example: Send a message to each connection
        //     $conn->send("asdfasdf");
        // }
        // echo "check-chat-api-running";
    }
    public function check_chat_post(){
        $receiver_id = $this->input->post('receiver_id', true);
        $sender_id = $this->input->post('senderId', true);
        $message = $this->input->post('message', true);
        $messageData = [
                    'channel' => 'msg', // or 'private' if sending to a specific user
                    'msg' => [
                        "content"=>$message,
                        "images"=>'',
                        'id'=>$receiver_id,
                        'auth_id'=> $sender_id,
                        "file"=>''
                    ],
                    'id'=> $receiver_id,
                    'auth_id'=>$sender_id,
                ];
        // try {
        //     $serverAddress = 'ws://localhost:5412';
        //     // if(!isset($_SESSION['client'])){
        //     //     $_SESSION['client']= new Client($serverAddress);
        //     // }
        //     
            
        //     // Convert message data to JSON
        //     $messageJson = json_encode($messageData);
        //     // Send a message to WebSocket server
        //     $this->client->send($messageJson);
           
         


        //     // echo "Message sent to WebSocket server:\n";

        // } catch (Exception $e) {
        //     // echo "Error sending message to WebSocket server: " . $e->getMessage() . "\n";
        // }
        echo json_encode(["return" => 1, "message"=> $messageData ]);
    }
    
}
