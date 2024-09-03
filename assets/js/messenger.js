let auth_user_details;
let last_con_id = null;
let conversationArray;
let messageTimeout;
let messageLastTimeout;
let conversationTimeout;
let messageClear;
let activeChat = null;
let allFiles = new Array();

setCookie('chatIsOpen', "NotOpen", 2)
var conn = new WebSocket('ws://erickci.staging-server.online:8080');

conn.onopen = function (e) {
    if (e.readyState === WebSocket.open) {
        console.log("Connection established!");
    }
    else {
        console.log('Connection Failed failed to open');
    }
};

conn.onmessage = function (e) {
    console.log(JSON.parse(e.data));
    let message = JSON.parse(e.data);
    append_send_message(message)
    console.log(activeChat)
    console.log(message)
    if (message.notify == true) {
        get_notification()
        return;
    }
    if(message.auth_id == activeChat){

        append_receive_message(message)
    }
    if(message.auth_id != activeChat || activeChat == null){
        messageNotification();
    }
    let chatIsOpen = getCookie('chatIsOpen');
    if(activeChat == null && chatIsOpen != "NotOpen"){
        const Data = new FormData();
        Data.append(mds_config.csfr_token_name, $.cookie(mds_config.csfr_cookie_name));
        Data.append('last_conversation_id', last_con_id);
        newConversation('POST', mds_config.base_url + "get-new-conversations", last_con_id, Data)
    }
    // if(e.data.auth_id == auth_user_details.auth_user.id){
    //     console.log("received")
    //     appendReceivedMessage(JSON.parse(e.data).msg,auth_user_details.auth_user)
    // }
    // if(e.data.id == auth_user_details.auth_user.id){
    //     console.log("send")
    //     appendSentMessage(JSON.parse(e.data),auth_user_details.auth_user)
    // }

};
function attachUserIdToSocket() {
    conn.send(JSON.stringify({
        auth_id: auth_user_details.auth_user.id,
        channel: 'authAttachment',
        msg: '',
    }));
}
setTimeout(() => {
    attachUserIdToSocket();
}, 1000);


function get_notification() {
    const xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function () {
        if (xhr.readyState === XMLHttpRequest.DONE) {

            if (xhr.status === 200) {
                const response = JSON.parse(xhr.responseText);
                if(document.getElementById("message-notify")){
                    document.getElementById("message-notify").innerHTML = response.count
                }
                else{
                    let span = document.createElement('span');
                    span.classList.add('message-notification');
                    span.id='message-notify'
                    span.style.top = '8px';
                    span.style.left='30px';
                    span.innerHTML = response.count;
                   if(response.count != 0){
                    document.getElementById('noti-link').appendChild(span);
                   }

                }
                
                if(document.getElementById("span-message-count")){
                    document.getElementById("span-message-count").innerHTML = response.count
                }
                else{
                    let span = document.createElement('span');
                    span.classList.add('span-message-count');
                    span.id='span-message-count'
                    span.innerHTML = response.count;
                   if(response.count != 0){
                    document.getElementById('alert-btn').appendChild(span);
                   }

                }
                console.log(response)
                document.getElementById('notification-list').innerHTML ="";
                create_notification_list(response)


            }
        }
    }
    xhr.open("GET", mds_config.base_url + "get-notification-counts");
    xhr.send();
}


get_notification()

function create_notification_list(data) {
    let name = "";
    data.notifications.forEach(element => {
        if(element.user.role_id ==2 || element.user.role_id ==1){
                name = element.user.shop_name;
        }
        else{
             name = element.user.first_name;
        }
        if(element.notification_type == 3){
            create_notification_item(name + " reviewed your product <br>", element)
        }
        else if(element.notification_type == 2){
            create_notification_item(name +  " post a comment on your product <br>", element)
        }
        else if(element.notification_type == 4){
            create_notification_item(name + " replied on your comment<br>", element)
        }
        else{
            create_notification_item(name+" replied to a comment on your product<br>", element)
        }
    
    });
}
function create_notification_item(txt, data){
    let list = document.createElement('li');
    list.classList.add('notification-items');
    list.id = "notification-"+data.id;
    list.innerHTML = '' +
        '<a href="asdfasdf"class="notification-item-link">'+
        '<div class="notification-items-avatar-div">' +
        '<span class="notification-item-avatar-span">' +
        '<img src="'+mds_config.base_url+'/'+data.user.avatar+'" alt="" class="notification-item-avatar-img">' +
        '</span>' +
        '</div>' +
        '<div class="notification-text-div">' +
          txt+
        '<span>' +
        '<span class="notification-created-span">' +
        data.created_at +
        '</span>' +
        '</span>' +
        '</div>' +
        '</a>' +
        '<span class="notification-icon-span"><i class="fa fa-close" onclick="deleteNotification(event, '+data.id+')">' +
        '</i></span>';
        document.getElementById('notification-list').appendChild(list)
}

function ajax(method, url, data = []) {

    const xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function () {
        if (xhr.readyState === XMLHttpRequest.DONE) {

            if (xhr.status === 200) {


                const response = JSON.parse(xhr.responseText);
                conversationArray = response;

                // consolefun(response)
                const formData = new FormData();
                formData.append(mds_config.csfr_token_name, $.cookie(mds_config.csfr_cookie_name));
                response.all_conversations.forEach(all_conversations => {
                    // consolefun(all_conversations.id)
                    formData.append("sender_id", all_conversations.sender_id);
                    formData.append("receiver_id", all_conversations.receiver_id);
                    POST(mds_config.base_url + "get-users", formData, displayConversation, all_conversations.id, all_conversations.important)
                    last_con_id = all_conversations.id;
                });

                
            }
        }
    }
    xhr.open(method, url);
    xhr.send(data);
}


function getImportantconversation(method, url, data = []) {

    const xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function () {
        if (xhr.readyState === XMLHttpRequest.DONE) {

            if (xhr.status === 200) {


                const response = JSON.parse(xhr.responseText);
                conversationArray = response;

                // consolefun(response)
                const formData = new FormData();
                formData.append(mds_config.csfr_token_name, $.cookie(mds_config.csfr_cookie_name));
                response.all_conversations.forEach(all_conversations => {
                    // consolefun(all_conversations.id)
                    formData.append("sender_id", all_conversations.sender_id);
                    formData.append("receiver_id", all_conversations.receiver_id);
                    POST(mds_config.base_url + "get-users", formData, displayImportantConversation, all_conversations.id, all_conversations.important)
                    last_con_id = all_conversations.id;
                });

            }
        }
    }
    xhr.open(method, url);
    xhr.send(data);
    // setTimeout(() => {
    //     ajax('GET',mds_config.base_url+"get_conversations") 
    // }, 5000);
}


function getAllUsers() {
    const xhrs = new XMLHttpRequest();
    xhrs.onreadystatechange = function () {
        if (xhrs.readyState === XMLHttpRequest.DONE) {

            if (xhrs.status === 200) {
                const responses = JSON.parse(xhrs.responseText);

                responses.users.forEach(users => {

                    // formData.append("sender_id",all_conversations.sender_id);
                    // formData.append("receiver_id",all_conversations.receiver_id);
                    displayUsers(users)

                });
                createUsersSearch()
            }
        }
    }
    xhrs.open("GET", mds_config.base_url + 'get-all-users');
    xhrs.send();
}


function newConversation(method, url, last_conversation_id, data) {
    listElement = document.getElementById('conversation-list');
    if (!listElement) {
        return;
    }
    const xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function () {
        if (xhr.readyState === XMLHttpRequest.DONE) {

            if (xhr.status === 200) {
                // consolefun(xhr.responseText)
                const response = JSON.parse(xhr.responseText);

                const formData = new FormData();
                formData.append(mds_config.csfr_token_name, $.cookie(mds_config.csfr_cookie_name));
                consolefun(response)
                if (response.conversation_new != "no") {
                    response.conversation_new.forEach(conversation_new => {

                        formData.append("sender_id", conversation_new.sender_id);
                        formData.append("receiver_id", conversation_new.receiver_id);
                        POST(mds_config.base_url + "get-users", formData, displayConversation, conversation_new.id,null,'')
                        last_con_id = conversation_new.id;
                    });
                }
                else {
                    last_con_id = last_conversation_id;
                }

            }
        }
    }

    //consolefun(last_con_id + "new Message id")

    xhr.open(method, url);
    xhr.send(data);
    // conversationTimeout = setTimeout(() => {
    //     let FData = new FormData();
    //     FData.append(mds_config.csfr_token_name, $.cookie(mds_config.csfr_cookie_name));
    //     FData.append('last_conversation_id', last_con_id);
    //     // newConversation('POST', mds_config.base_url + "get-new-conversations", last_con_id, FData)
    // }, 5000);
}


function POST(urls, datas, callBack, conversation_id = null, is_important = null,display = 'displayNone') {
    const xhrs = new XMLHttpRequest();
    xhrs.onreadystatechange = function () {
        if (xhrs.readyState === XMLHttpRequest.DONE) {

            if (xhrs.status === 200) {
                const responses = JSON.parse(xhrs.responseText);
                console.log(responses)
                callBack(responses, conversation_id, is_important,display)
            }
        }
    }
    xhrs.open("POST", urls);
    xhrs.send(datas);
}

function GET(urls) {
    const xhrs = new XMLHttpRequest();
    xhrs.onreadystatechange = function () {
        if (xhrs.readyState === XMLHttpRequest.DONE) {

            if (xhrs.status === 200) {
                const responses = JSON.parse(xhrs.responseText);
                consolefun(responses)
                auth_user_details = responses;
            }
        }
    }
    xhrs.open("GET", urls);
    xhrs.send();
}

function consolefun(response) {
    console.log(response)
}
GET(mds_config.base_url + "auth-user")

//for testing 


function assignNull() {
    return new Promise((resolve) => {
        setTimeout(() => {
            last_messageId = null;
            resolve();
        }, 2000);
    });
}

function createConversationListBox() {
    allFiles = []
    activeChat =null;
    console.log(messageTimeout)
    console.log(messageLastTimeout)
    if (messageTimeout != null || messageTimeout != undefined) {
        clearTimeout(messageTimeout)
        console.log("time out Cleared messageTimeout")
    }
    if (messageLastTimeout != null || messageLastTimeout != undefined) {
        clearTimeout(messageLastTimeout)
        console.log("time out Cleared messageLastTimeout")
    }
    // await assignNull();
    last_messageId = null;
    console.log(last_messageId)
    chatMessage = document.getElementById('user-chatBox');
    chatTextarea = document.getElementById('message-text-area-box');
    if (chatMessage) {
        chatMessage.remove();
        chatTextarea.remove();
    }
    parentDiv = document.getElementById('messenger-box');
    listMainBox = document.createElement('div');
    listMainBox.id = "allconversation";
    listMainBox.className = "all-conversation";
    listMainBox.innerHTML = '<div class="messenger-main-menu">' +
        '<div id="conversation-box" class="conversation-box active" onclick="displayConversationList()"><i class="icon-mail" ></i></div>' +
        ' <div class="user-box" id="user-box"  onclick="displayUserList()"><i class="fa fa-user"></i></div>' +
        ' <div class="important-box" id="important-box"  onclick="displayImportantList()"><i class="fa fa-star"></i></div>' +
        '</div>' +
        '<div class="main-messenger-box">' +
        ' <div class="messenger-heading-box">' +
        '<div class="messenger-heading-text" id="messenger-heading-text">Conversation</div>' +
        '</div>' +
        '<div class="conversation-list box-animation" id ="conversation-list">' +

        '</div>' +
        '<div class="conversation-list displayNone box-animation" id ="user-list">' +

        '</div>' +
        '<div class="conversation-list displayNone box-animation" id ="important-list">' +

        '</div>';

    parentDiv.appendChild(listMainBox)



    setTimeout(() => {
        get_all_conversation();
    }, 10);
    setTimeout(() => {
        getAllUsers()
    }, 20);
    setTimeout(() => {
        get_all_important_conversation()
    }, 30);
}

function get_all_conversation() {
    ajax('GET', mds_config.base_url + "get_conversations")
}
function get_all_important_conversation() {
    getImportantconversation('GET', mds_config.base_url + "get_imp-conversations")
}



function get_user_conversation_by_con_id(con_id, user_id) {
    if (conversationTimeout != null || conversationTimeout != undefined) {
        clearTimeout(conversationTimeout);
        console.log("time out cleared conversationTimeout")
    }
    const formData = new FormData();
    formData.append(mds_config.csfr_token_name, $.cookie(mds_config.csfr_cookie_name));
    formData.append('conversation_id', con_id)
    formData.append('user_id', user_id)
    POST(mds_config.base_url + "conversation-message", formData, createContentInChatBox)

    getLastMessage(con_id)

    setTimeout(() => {
        scrollToBottom();
    }, 500);

}



function get_user_conversation_by_user_id(sender_id, user_id) {
    if (conversationTimeout != null || conversationTimeout != undefined) {
        clearTimeout(conversationTimeout);
        console.log("time out cleared conversationTimeout")
    }
    const formData = new FormData();
    formData.append(mds_config.csfr_token_name, $.cookie(mds_config.csfr_cookie_name));
    formData.append('sender_id', auth_user_details.auth_user.id)
    formData.append('user_id', user_id)

    POST(mds_config.base_url + "conversation-message-user", formData, createContentInChatBox)
    //createMessageTextarea(user_id,con_id)
    getLastMessageByUsersId(sender_id, user_id)
    setTimeout(() => {
        scrollToBottom();
    }, 500);


}


/**
 * 
 * events functions 
 * 
**/

//sent message event function
function sentMessages() {


    message_element = document.getElementById('message-text-area');
    message_content = message_element.value;


    const formData = new FormData();
    receiver_id = document.getElementById('receiver_id').value
    sender_id = document.getElementById('sender_id').value
    conversation_id = document.getElementById('con_id').value
    message = document.getElementById('message-text-area').value
    let inputFile = document.getElementById('file-input');
    // const files = inputFile.files;
    const files = allFiles
    for (let i = 0; i < files.length; i++) {
        console.log(files[i].name)
        formData.append('file[]', files[i]);
    }
    // formData.append('file-input',file);
    console.log(files)
    if (message.trim() == '' && files.length == 0) {
        document.getElementById("message-text-area").style.borderColor = "rgba(220,53,69,0.40)"
        console.log("message_not send")
    }
    else {
        // appendSentMessage(message_content,sender_details.auth_user);

        formData.append(mds_config.csfr_token_name, $.cookie(mds_config.csfr_cookie_name));
        formData.append('receiver_id', receiver_id);
        formData.append('senderId', sender_id);
        formData.append('conversation_id', conversation_id);
        formData.append('message', message);

        SentMessageAjax(mds_config.base_url + "send-new-message-post", formData, consolefun)
        //   SentMessageAjax(mds_config.base_url + "check-chat-post", formData, consolefun)
        document.getElementById("message-text-area").value = '';
        document.getElementById("file-input").value = '';
        allFiles = []
        document.getElementById('file-list-div').style.display = 'none'
        const allImagesDiv = document.getElementById("file-list-div");
        while (allImagesDiv.firstChild) {
            allImagesDiv.removeChild(allImagesDiv.firstChild);
        }
    }

}
function SentMessageAjax(urls, datas) {
    sender_details = auth_user_details;
    const xhrs = new XMLHttpRequest();
    xhrs.onreadystatechange = function () {
        if (xhrs.readyState === XMLHttpRequest.DONE) {

            if (xhrs.status === 200) {
                const responses = JSON.parse(xhrs.responseText);
                console.log(responses)
                if (responses.status) {
                    conn.send(JSON.stringify(responses.message));
                }
                // appendSentMessage(responses, sender_details.auth_user);
            }
        }
    }
    xhrs.open("POST", urls);
    xhrs.send(datas);
}
//event listener for send message



/**
 * 
 * content display functions
 * 
 * */

// list of all conversation
function displayConversation(response, con_id, is_important,display = 'displayNone') {

    let Conversation = response.user;
    if (Conversation.role_id == 1 || Conversation.role_id == 2) {
        if (Conversation.shop_name == '') {
            username = Conversation.username
        }
        else {
            username = Conversation.shop_name
        }
    }
    else {
        username = Conversation.first_name
    }
    if (Conversation.role_id == 1 || Conversation.role_id == 2) {
        if (Conversation.acc_type == 2) {
            iconHtml = '<i class="icon-verified icon-verified-member" style="color:orange;float:unset"></i>'
        }
        else {
            iconHtml = '<i class="icon-verified icon-verified-member" style="color:#09b1ba;float:unset"></i>'
        }
    }
    else {
        iconHtml = '';
    }
    if (Conversation.is_online) {
        statusHtml = '<i class="icon-circle" style ="color:#49e346"></i>'
    }
    else {
        statusHtml = '<i class="icon-circle" style ="color:#ababab"></i>'
    }

    HTMLContent = '<div class="user-logo-box"><img src="' + Conversation.avatar + '" alt ="' + Conversation.profile_url + '"></div>' +
        '<div class="user-name-box">' + username + iconHtml + '</div>' +
        '<span class="new-noti-name '+display+'" id="m-noti-' + con_id + '"></span>' +
        '<div class="user-status-box">' + statusHtml + '</div>';

    var conversation_div = document.createElement('div');
    conversation_div.className = "user-conversation-box";
    conversation_div.id = "Conversationid-" + con_id;
    conversation_div.setAttribute("data-imp", is_important)
    conversation_div.oncontextmenu = function (e) {
        child = e.target;
        const { clientX: mouseX, clientY: mouseY } = e;
        contextMenu.style.top = `${mouseY}px`;
        contextMenu.style.left = `${mouseX}px`;
        contextMenu.classList.remove('displayNone')
        // document.getElementById("id").innerHTML = e.currentTarget.id;
        document.getElementById("deleteCon").setAttribute("data-id", con_id)
        document.getElementById("markAsImportant").setAttribute("data-id", con_id)

        document.getElementById("markAsImportant").innerHTML = "Mark / unmark Important"
        document.getElementById("markAsImportant").setAttribute("data-imp", 1)

        // alert("hello");
        e.preventDefault();
    }
    conversation_div.onclick = function () {
        setCookie('chatIsOpen', true, 2)
        document.getElementById('allconversation').remove()
        document.getElementById('message-search-area-box').remove()
        chat_div = document.createElement('div');
        chat_div.className = "user-chatBox box-animation";
        chat_div.id = 'user-chatBox';
        document.getElementById('messenger-box').appendChild(chat_div)

        setTimeout(() => {

            if (con_id == 0) {

                get_user_conversation_by_user_id(auth_user_details.auth_user.id, Conversation.id)
            }
            else {
                get_user_conversation_by_con_id(con_id, Conversation.id)
            }

            createMessageTextarea(Conversation.id, con_id)
        }, 300);

    }
    conversation_div.innerHTML = HTMLContent
    parentElement = document.getElementById('conversation-list')
    if (parentElement) {
        parentElement.appendChild(conversation_div)
    }
}

function displayImportantConversation(response, con_id, is_important) {

    let Conversation = response.user;
    if (Conversation.role_id == 1 || Conversation.role_id == 2) {
        if (Conversation.shop_name == '') {
            username = Conversation.username
        }
        else {
            username = Conversation.shop_name
        }
    }
    else {
        username = Conversation.first_name
    }
    if (Conversation.role_id == 1 || Conversation.role_id == 2) {
        if (Conversation.acc_type == 2) {
            iconHtml = '<i class="icon-verified icon-verified-member" style="color:orange;float:unset"></i>'
        }
        else {
            iconHtml = '<i class="icon-verified icon-verified-member" style="color:#09b1ba;float:unset"></i>'
        }
    }
    else {
        iconHtml = '';
    }
    if (Conversation.is_online) {
        statusHtml = '<i class="icon-circle" style ="color:#49e346"></i>'
    }
    else {
        statusHtml = '<i class="icon-circle" style ="color:#ababab"></i>'
    }

    HTMLContent = '<div class="user-logo-box"><img src="' + Conversation.avatar + '" alt ="' + Conversation.profile_url + '"></div>' +
        '<div class="user-name-box">' + username + iconHtml + '</div>' +
        '<span class="new-noti-name displayNone" id="i-c-noti-' + con_id + '"></span>' +
        '<div class="user-status-box">' + statusHtml + '</div>';

    var conversation_div = document.createElement('div');
    conversation_div.className = "user-conversation-box";
    conversation_div.id = "ImpConversationid-" + con_id;
    conversation_div.setAttribute("data-imp", is_important)
    conversation_div.oncontextmenu = function (e) {
        child = e.target;
        const { clientX: mouseX, clientY: mouseY } = e;
        contextMenu.style.top = `${mouseY}px`;
        contextMenu.style.left = `${mouseX}px`;
        contextMenu.classList.remove('displayNone')
        // document.getElementById("id").innerHTML = e.currentTarget.id;
        document.getElementById("deleteCon").setAttribute("data-id", con_id)
        document.getElementById("markAsImportant").setAttribute("data-id", con_id)

        document.getElementById("markAsImportant").innerHTML = "Umark Important"
        document.getElementById("markAsImportant").setAttribute("data-imp", 0)


        // alert("hello");
        e.preventDefault();
    }
    conversation_div.onclick = function () {
        document.getElementById('allconversation').remove()
        document.getElementById('message-search-area-box').remove()
        chat_div = document.createElement('div');
        chat_div.className = "user-chatBox box-animation";
        chat_div.id = 'user-chatBox';
        document.getElementById('messenger-box').appendChild(chat_div)

        setTimeout(() => {

            if (con_id == 0) {

                get_user_conversation_by_user_id(auth_user_details.auth_user.id, Conversation.id)
            }
            else {
                get_user_conversation_by_con_id(con_id, Conversation.id)
            }

            createMessageTextarea(Conversation.id, con_id)
        }, 300);

    }
    conversation_div.innerHTML = HTMLContent
    parentElement = document.getElementById('important-list')
    if (parentElement) {
        parentElement.appendChild(conversation_div)
    }
}


function displayUsers(Conversation, con_id) {
    // consolefun(response)
    // let Conversation = response.users;
    if (Conversation.role_id == 1 || Conversation.role_id == 2) {
        if (Conversation.shop_name == '') {
            username = Conversation.username
        }
        else {
            username = Conversation.shop_name
        }
    }
    else {
        username = Conversation.first_name
    }
    if (Conversation.role_id == 1 || Conversation.role_id == 2) {
        if (Conversation.acc_type == 2) {
            iconHtml = '<i class="icon-verified icon-verified-member" style="color:orange;float:unset"></i>'
        }
        else {
            iconHtml = '<i class="icon-verified icon-verified-member" style="color:#09b1ba;float:unset"></i>'
        }
    }
    else {
        iconHtml = '';
    }
    if (Conversation.is_online) {
        statusHtml = '<i class="icon-circle" style ="color:#49e346"></i>'
    }
    else {
        statusHtml = '<i class="icon-circle" style ="color:#ababab"></i>'
    }

    HTMLContent = '<div class="user-logo-box"><img src="' + Conversation.avatar + '" alt ="' + Conversation.profile_url + '"></div>' +
        '<div class="user-name-box">' + username + iconHtml + '</div>' +
        '<div class="user-status-box">' + statusHtml + '</div>';

    var conversation_div = document.createElement('div');
    conversation_div.className = "user-conversation-box";
    conversation_div.onclick = function () {
        document.getElementById('allconversation').remove()
        document.getElementById('message-search-area-box').remove()
        chat_div = document.createElement('div');
        chat_div.className = "user-chatBox box-animation";
        chat_div.id = 'user-chatBox';
        document.getElementById('messenger-box').appendChild(chat_div)
        setTimeout(() => {

            if (!con_id) {

                get_user_conversation_by_user_id(auth_user_details.auth_user.id, Conversation.id)
            }
            else {
                get_user_conversation_by_con_id(con_id, Conversation.id)
            }

            createMessageTextarea(Conversation.id, con_id)
        }, 300);

    }
    conversation_div.innerHTML = HTMLContent
    parentElement = document.getElementById('user-list')
    if (parentElement) {
        parentElement.appendChild(conversation_div)

    }
}


//create the mesages content
function displayUserDetails(userDetails) {

    if (userDetails.role_id == 1 || userDetails.role_id == 2) {
        if (userDetails.shop_name == '') {
            username = userDetails.username
        }
        else {
            username = userDetails.shop_name
        }
    }
    else {
        username = userDetails.first_name
    }
    if (userDetails.role_id == 1 || userDetails.role_id == 2) {
        if (userDetails.acc_type == 2) {
            iconHtml = '<i class="icon-verified icon-verified-member" style="color:orange;float:unset"></i>'
        }
        else {
            iconHtml = '<i class="icon-verified icon-verified-member" style="color:#09b1ba;float:unset"></i>'
        }
    }
    else {
        iconHtml = '';
    }
    if (userDetails.is_online) {
        statusHtml = '<i class="icon-circle" style ="color:#49e346"></i>'
    }
    else {
        statusHtml = '<i class="icon-circle" style ="color:#ababab"></i>'
    }
    if (userDetails.conImp == 0) {
        displayedImpStar = "imp-star-display"
        displayedNonImpStar = ""
    }
    else {
        displayedImpStar = ""
        displayedNonImpStar = "imp-star-display"
    }
    htmlContent = '<div class="user-detail-div"><i class="back-arrow fa fa-arrow-left" aria-hidden="true" onclick="createConversationListBox()"></i>' +
        '<a href="' + userDetails.profile_url + '" style="display:flex"><div class="chat-user-image"><img src="' + userDetails.avatar + '" alt ="' + userDetails.profile_url + '"></div>' +
        '<div class="chat-user-name">' + username + iconHtml + '</div></a></div>' + '<div class="imp-div"><i onclick="conversationMarkImp(' + userDetails.id + ')" id="non-imp-star" class="imp-star ' + displayedNonImpStar + ' far fa-star" aria-hidden="true"></i><i onclick="conversationMarkImp(' + userDetails.id + ')" id="imp-star" class="imp-star ' + displayedImpStar + ' fa fa-star"></i></div>';

    var user_details_div = document.createElement('div');
    user_details_div.className = 'chat-user-details ';
    user_details_div.innerHTML = htmlContent;

    parentElement = document.getElementById('user-chatBox')

    if (parentElement) {
        parentElement.appendChild(user_details_div)
    }

}


function displayMessages(response) {
    htmlContent = '';
    var user_messages_div = document.createElement('div');
    user_messages_div.className = 'chat-user-messages';
    user_messages_div.id = 'chat-user-messages';
    consolefun(response)
    // GET(mds_config.base_url+'read-messages');
    if (response.messages != "no") {
        response.messages.forEach(messages => {
            if (messages.sender_id == response.auth_id) {
                htmlContent = htmlContent + displaySentMessages(messages, response.auth_user.avatar, response.auth_user.profile_url)
            }
            else {
                htmlContent = htmlContent + displayReceivedMessages(messages, response.user.avatar, response.user.profile_url)
            }
        });
    }
    user_messages_div.innerHTML = htmlContent;
    if (parentElement) {
        parentElement.appendChild(user_messages_div)
    }

}
function displayReceivedMessages(messages, userAvatar, userSlug) {
    let convertedMessage;
    let attachment = '';
    if (messages.images != '') {
        fileArray = JSON.parse(messages.images)
        fileArray.forEach(pathJson => {
            path = JSON.parse(pathJson)
            const fileExtension = path.filepath.split(".").pop().toLowerCase();
            if (fileExtension === "pdf") {



                attachment += '<div class="chat-message-receive box-animation">' +
                    '<div class="chat-message-image"><div class="image-tag"><a href ="' + userSlug + '" style="display:block"><img src="' + userAvatar + '" alt ="' + userSlug + '"></a></div></div>' +
                    '<div class="chat-message-content"><div class="file-div"><a href="' + mds_config.base_url + path.filepath + '" target="_blank">File Attachment</a></div></div>' +
                    '</div>';
            }
            else {
                imageUrl = `${path.filepath}`
                attachment += '<div class="chat-message-receive box-animation">' +
                    '<div class="chat-message-image"><div class="image-tag"><a href ="' + userSlug + '" style="display:block"><img src="' + userAvatar + '" alt ="' + userSlug + '"></a></div></div>' +
                    '<div class="chat-message-content"><div class="message-image-div"><img onclick="imageModal(this.src)" src="' + mds_config.base_url + imageUrl + '"></div></div>' +
                    '</div>';
            }
            // console.log(path)
        });
    }
    if (messages.content != '') {
        convertedMessage = linkify(messages.content)
    }

    if (messages.images != '' && messages.content != '') {
        content = attachment + '<div class="chat-message-receive box-animation">' +
            '<div class="chat-message-image"><div class="image-tag"><a href ="' + userSlug + '" style="display:block"><img src="' + userAvatar + '" alt ="' + userSlug + '"></a></div></div>' + '<div class="chat-message-content"><p class="message-text">' + convertedMessage + '</p></div>' + '</div>';
    }
    else if (messages.images != '' && messages.content == '') {
        content = attachment
    }
    else {
        content = '<div class="chat-message-receive box-animation">' +
            '<div class="chat-message-image"><div class="image-tag"><a href ="' + userSlug + '" style="display:block"><img src="' + userAvatar + '" alt ="' + userSlug + '"></a></div></div>' +
            '<div class="chat-message-content"><p class="message-text">' + convertedMessage + '</p></div>' + '</div>';
    }
    htmlContent = content
    // htmlContent = '<div class="chat-message-receive box-animation">'+
    //         '<div class="chat-message-image"><div class="image-tag"><a href ="'+userSlug+'" style="display:block"><img src="'+userAvatar+'" alt ="'+userSlug+'"></a></div></div>'+
    //         content+
    //          '</div>';
    return htmlContent;
}
function displaySentMessages(messages, userAvatar, userSlug) {
    let attachment = '';
    // console.log
    // (messages)
    let convertedMessage
    if (messages.images != '') {
        fileArray = JSON.parse(messages.images)
        fileArray.forEach(pathJson => {
            path = JSON.parse(pathJson)
            const fileExtension = path.filepath.split(".").pop().toLowerCase();
            if (fileExtension === "pdf") {
                attachment += '<div class="chat-message-sent box-animation">' +
                    '<div class="chat-message-content"><div class="file-div"><a href="' + mds_config.base_url + path.filepath + '" target="_blank">File Attachment</a></div></div>' +
                    '<div class="chat-message-image"><div class="image-tag"><a href ="' + userSlug + '" style="display:block"><img src="' + userAvatar + '" alt ="' + userSlug + '"></a></div></div>' +
                    '</div>';
            }
            else {
                imageUrl = `${path.filepath}`
                attachment += '<div class="chat-message-sent box-animation">' +
                    '<div class="chat-message-content"><div class="message-image-div"><img onclick="imageModal(this.src)" src="' + mds_config.base_url + imageUrl + '"></div></div>' +
                    '<div class="chat-message-image"><div class="image-tag"><a href ="' + userSlug + '" style="display:block"><img src="' + userAvatar + '" alt ="' + userSlug + '"></a></div></div>' +
                    '</div>';
            }
            // console.log(path)
        });
    }
    if (messages.content != '') {
        convertedMessage = linkify(messages.content)
    }
    if (messages.images != '' && messages.content != '') {
        content = attachment + '<div class="chat-message-sent box-animation">' +
            '<div class="chat-message-content"><p class="message-text">' + convertedMessage + '</p></div>' +
            '<div class="chat-message-image"><div class="image-tag"><a href ="' + userSlug + '" style="display:block"><img src="' + userAvatar + '" alt ="' + userSlug + '"></a></div></div>' +
            '</div>';
    }
    else if (messages.images != '' && messages.content == '') {
        content = attachment
    }
    else {
        content = '<div class="chat-message-sent box-animation">' +
            '<div class="chat-message-content"><p class="message-text">' + convertedMessage + '</p></div>' +
            '<div class="chat-message-image"><div class="image-tag"><a href ="' + userSlug + '" style="display:block"><img src="' + userAvatar + '" alt ="' + userSlug + '"></a></div></div>' +
            '</div>';
    }
    htmlContent = content
    // htmlContent = '<div class="chat-message-sent box-animation">'+
    // content+
    // '<div class="chat-message-image"><div class="image-tag"><a href ="'+userSlug+'" style="display:block"><img src="'+userAvatar+'" alt ="'+userSlug+'"></a></div></div>'+
    //  '</div>';
    return htmlContent;
}
function show_files() {
    files = document.getElementById('file-input').files;


    // files.forEach(file =>{
    //     allFiles.push(file)
    // })
    let fileIndex = allFiles.length;
    if (allFiles.length !== 6) {
        for (let index = 0; index < files.length; index++) {
            if (!allFiles.some(existingFile => existingFile.name === files[index].name)) {
                if (index === 6) {
                    errorMessageElement = document.getElementById("file-error");
                    // fileListElement = document.getElementById("file-list-div");
                    errorMessageElement.classList.remove("removeMessage")
                    document.getElementById('error-message-file').innerHTML = "6 files allowed to send at once"
                    messageClear = setTimeout(() => {
                        errorMessageElement.classList.add("removeMessage")
                        if (allFiles.length < 1) {
                            document.getElementById('file-list-div').style.display = 'none'
                        }
                    }, 2000);
                    break
                }
                var parts = files[index].name.split('.');
                if (parts.length > 1) {
                    var fileType = parts.slice(parts.length - 1).join('.');
                    if (fileType == "png" || fileType == "jpg" || fileType == "jpeg" || fileType == "pdf" || fileType == "gif") {
                        files[index].fileId = fileIndex
                        var file_box = document.createElement('div')
                        file_box.className = "file-box";
                        file_box.id = "file-box-" + fileIndex
                        file_box.setAttribute('data-fileId', fileIndex)
                        file_box.innerHTML = '<p class="close-btn">File<i class="fa fa-close" onclick="removeFile(' + fileIndex + ')" style="background-color:#dedcdc!important"></i></p><p class="file-name-p" title="' + files[index].name + '">' + files[index].name + '</p>'
                        allFiles.push(files[index]);
                        sentbtn = document.getElementById('file-list-div').appendChild(file_box);
                        fileIndex++
                    }
                    else {

                        errorMessageElement = document.getElementById("file-error");
                        // fileListElement = document.getElementById("file-list-div");
                        errorMessageElement.classList.remove("removeMessage")
                        document.getElementById('error-message-file').innerHTML = "Error! '" + fileType + "' file type is not allowed to send"
                        messageClear = setTimeout(() => {
                            errorMessageElement.classList.add("removeMessage")
                            if (allFiles.length < 1) {
                                document.getElementById('file-list-div').style.display = 'none'
                            }
                        }, 2000);
                    }
                }


            }
            else {
                errorMessageElement = document.getElementById("file-error");
                // fileListElement = document.getElementById("file-list-div");
                errorMessageElement.classList.remove("removeMessage")
                document.getElementById('error-message-file').innerHTML = "file already exists"
                messageClear = setTimeout(() => {
                    errorMessageElement.classList.add("removeMessage")
                    if (allFiles.length < 1) {
                        document.getElementById('file-list-div').style.display = 'none'
                    }
                }, 2000);
            }
        }
    }
    else {
        errorMessageElement = document.getElementById("file-error");
        // fileListElement = document.getElementById("file-list-div");
        errorMessageElement.classList.remove("removeMessage")
        messageClear = setTimeout(() => {
            errorMessageElement.classList.add("removeMessage")
            if (allFiles.length < 1) {
                document.getElementById('file-list-div').style.display = 'none'
            }
        }, 2000);
    }
    console.log(files[0].name)
    console.log(allFiles)
}
function removeFile(fileId) {
    document.getElementById('file-box-' + fileId).remove()
    const index = allFiles.findIndex((file) => file.fileId === fileId);
    allFiles.splice(index, 1);
    if (allFiles.length === 0) {
        document.getElementById('file-list-div').style.display = 'none'
        document.getElementById('file-input').value = ''
    }
    console.log(allFiles)
}
function createMessageTextarea(user_id, con_id = 0) {
    activeChat = user_id;
    var textarea_box = document.createElement('div');
    textarea_box.className = "message-text-area-box box-animation";
    textarea_box.id = "message-text-area-box";
    htmlContent = '<div class="file-list-div" id="file-list-div"></div>' +
        '<div class="file-error removeMessage" id="file-error"><div id="error-message-file">Error! file type not allowed</div></div>' +
        '<textarea class="message-text-area" id="message-text-area" placeholder = "Write Message"></textarea>' +
        '<input id="receiver_id" value="' + user_id + '" hidden>' +
        '<input id="sender_id" value="' + auth_user_details.auth_user.id + '" hidden>' +
        '<input id="con_id" value="' + con_id + '" hidden>' +
        '<label for="file-input" class="file-input-label">' +
        '<i class="fa-solid fa-paperclip" style="color:#fff"></i>' +

        '</label>' +
        '<input type="file" id="file-input" hidden multiple> ' +
        '<div class="send-button-box"><button class="send-messages" id="send-messages" style="background-color: #F15B29;"><i class="fa fa-paper-plane" style="background-color: transparent!important;" aria-hidden="true"></i></button></div>';
    textarea_box.innerHTML = htmlContent
    parent = document.getElementById('messenger-box');
    if (parent) {
        parent.appendChild(textarea_box)
        sentbtn = document.getElementById('send-messages');
        document.getElementById('message-text-area').addEventListener('keyup', function (event) {
            if (event.key === "Enter" && !event.shiftKey && !event.ctrlKey && !event.metaKey) {
                sentMessages()
            }
        });
        if (sentbtn) {
            sentbtn.addEventListener('click', sentMessages)
        }

    }
    document.getElementById('file-input').addEventListener('change', () => {
        show_files();
        document.getElementById('file-list-div').style.display = "flex"
        console.log("asdfasdfasdfasdfasdfadsf")
    })

}
function createUsersSearch() {

    var textarea_box = document.createElement('div');
    textarea_box.className = "message-text-area-box box-animation displayNone";
    textarea_box.id = "message-search-area-box";
    htmlContent = '<input class="message-text-area" id="message-search-area" placeholder = "Search users">' +
        '<div class="send-button-box" ><button style="background-color: #F15B29;" class="send-messages" id="search-users"><i class="fa fa-search" aria-hidden="true" style="background-color: transparent!important;"></i></button></div>';
    textarea_box.innerHTML = htmlContent
    parent = document.getElementById('messenger-box');
    if (parent) {
        parent.appendChild(textarea_box)
        UserSearch();
        document.getElementById('search-users').addEventListener('click', function () {
            searchValue = document.getElementById('message-search-area').value;
            searchValue = searchValue.trim();
            if (searchValue != '') {
                document.getElementById('user-list').innerHTML = '';
                const formData = new FormData();
                formData.append(mds_config.csfr_token_name, $.cookie(mds_config.csfr_cookie_name));
                formData.append('search_text', searchValue);
                searchPOST(formData)
            }
        })




    }

}
function createContentInChatBox(response) {
    console.log("message box")
    displayUserDetails(response.user)
    displayMessages(response)

    console.log("testing testing testing")
    console.log(response)

}
//end create the mesages content


//append send message function
function appendSentMessage(message_content, sender_detail) {
    let attachment = '';
    // console.log(message_content.file)
    // console.log(JSON.parse(message_content.file))

    // console.log(JSON.parse(JSON.parse(message_content.file)[0]))
    let convertedMessage;
    // sent_message_element = document.createElement('div');
    // sent_message_element.className= 'chat-message-sent box-animation';
    if (message_content.file != '') {
        fileArray = JSON.parse(message_content.file)
        fileArray.forEach(pathJson => {
            sent_message_element = document.createElement('div');
            sent_message_element.className = 'chat-message-sent box-animation';
            path = JSON.parse(pathJson)
            const fileExtension = path.filepath.split(".").pop().toLowerCase();
            if (fileExtension === "pdf") {
                attachment = '<div class="chat-message-content"><div class="file-div"><a href="' + mds_config.base_url + path.filepath + '" target="_blank">File Attachment</a></div></div>'
            }
            else {
                imageUrl = `${path.filepath}`
                attachment = '<div class="chat-message-content"><div class="message-image-div"><img onclick="imageModal(this.src)" src="' + mds_config.base_url + imageUrl + '"></div></div>'
            }
            console.log(path)
            htmlContent = attachment +
                '<div class="chat-message-image"><a href="' + sender_detail.profile_url + '"><div class="image-tag"><img src="' + sender_detail.avatar + '" alt ="' + sender_detail.profile_url + '"></a></div></div>';

            sent_message_element.innerHTML = htmlContent;
            parent_element = document.getElementById('chat-user-messages');
            if (parent_element) {
                parent_element.appendChild(sent_message_element);
                // consolefun(parent_element.appendChild(sent_message_element))
                scrollToBottom();
            }
        });

    }
    if (message_content.content != '') {
        sent_message_element = document.createElement('div');
        sent_message_element.className = 'chat-message-sent box-animation';
        convertedMessage = linkify(message_content.content)
        content = '<div class="chat-message-content"><p class="message-text">' + convertedMessage + '</p></div>'
        htmlContent = content +
            '<div class="chat-message-image"><a href="' + sender_detail.profile_url + '"><div class="image-tag"><img src="' + sender_detail.avatar + '" alt ="' + sender_detail.profile_url + '"></a></div></div>';
        sent_message_element.innerHTML = htmlContent;
        parent_element = document.getElementById('chat-user-messages');
        if (parent_element) {
            parent_element.appendChild(sent_message_element);
            // consolefun(parent_element.appendChild(sent_message_element))
            scrollToBottom();
        }
    }
    // if(message_content.file != '' && message_content.content != ''){
    //     content = attachment+'<div class="chat-message-content"><p class="message-text">'+convertedMessage+'</p></div>'
    // }
    // else if(message_content.file != '' && message_content.content == ''){
    //     content = attachment
    // }
    // else{
    //     content = '<div class="chat-message-content"><p class="message-text">'+convertedMessage+'</p></div>'
    // }
    // htmlContent =  content +
    // '<div class="chat-message-image"><a href="'+sender_detail.profile_url+'"><div class="image-tag"><img src="'+sender_detail.avatar+'" alt ="'+sender_detail.profile_url+'"></a></div></div>';

    // sent_message_element.innerHTML = htmlContent;
    // parent_element = document.getElementById('chat-user-messages');
    // if(parent_element){
    //     parent_element.appendChild(sent_message_element);
    //     // consolefun(parent_element.appendChild(sent_message_element))
    //     scrollToBottom();
    // }
}

function appendReceivedMessage(message_content, sender_detail) {
    let convertedMessage = ''
    let attachment = '';
    // console.log(message_content)
    // receive_message_element = document.createElement('div');
    // receive_message_element.className= 'chat-message-receive box-animation';
    if (message_content.images != '') {
        fileArray = JSON.parse(message_content.images)

        fileArray.forEach(pathJson => {
            console.log(pathJson)
            path = JSON.parse(pathJson)

            receive_message_element = document.createElement('div');
            receive_message_element.className = 'chat-message-receive box-animation';
            const fileExtension = path.filepath.split(".").pop().toLowerCase();
            if (fileExtension === "pdf") {
                attachment = '<div class="chat-message-content"><div class="file-div"><a href="' + mds_config.base_url + path.filepath + '" target="_blank">File Attachment</a></div></div>'
            }
            else {
                imageUrl = `${message_content.images}`
                attachment = '<div class="chat-message-content"><div class="message-image-div"><img onclick="imageModal(this.src)" src="' + mds_config.base_url + path.filepath + '"></div></div>'
            }
            htmlContent = '<div class="chat-message-image"><div class="image-tag"><a href="' + sender_detail.profile_url + '"><img src="' + sender_detail.avatar + '" alt ="' + sender_detail.profile_url + '"></a></div></div>' +
                attachment;


            receive_message_element.innerHTML = htmlContent;
            parent_element = document.getElementById('chat-user-messages');
            if (parent_element) {
                parent_element.appendChild(receive_message_element);
                // consolefun(parent_element.appendChild(receive_message_element))
                scrollToBottom();
            }
        });
    }

    if (message_content.content != '') {
        receive_message_element = document.createElement('div');
        receive_message_element.className = 'chat-message-receive box-animation';
        convertedMessage = linkify(message_content.content)
        content = '<div class="chat-message-content"><p class="message-text">' + convertedMessage + '</p></div>'
        htmlContent = '<div class="chat-message-image"><div class="image-tag"><a href="' + sender_detail.profile_url + '"><img src="' + sender_detail.avatar + '" alt ="' + sender_detail.profile_url + '"></a></div></div>' +
            content;


        receive_message_element.innerHTML = htmlContent;
        parent_element = document.getElementById('chat-user-messages');
        if (parent_element) {
            parent_element.appendChild(receive_message_element);
            // consolefun(parent_element.appendChild(receive_message_element))
            scrollToBottom();
        }
    }

    // if(message_content.images != '' && message_content.content != ''){

    //     content = '<div class="chat-message-content">'+attachment+'<p class="message-text">'+convertedMessage+'</p></div>'
    // }
    // else if(message_content.images != '' && message_content.content == ''){
    //     content = '<div class="chat-message-content">'+attachment+'</div>'
    // }
    // else{
    //     content = '<div class="chat-message-content"><p class="message-text">'+convertedMessage+'</p></div>'
    // }


    // htmlContent =  '<div class="chat-message-image"><div class="image-tag"><a href="'+sender_detail.profile_url+'"><img src="'+sender_detail.avatar+'" alt ="'+sender_detail.profile_url+'"></a></div></div>'+
    // content;


    // receive_message_element.innerHTML = htmlContent;
    // parent_element = document.getElementById('chat-user-messages');
    // if(parent_element){
    //     parent_element.appendChild(receive_message_element);
    //     // consolefun(parent_element.appendChild(receive_message_element))
    //     scrollToBottom();
    // }
}

function getLastMessage(conversation_id) {
    const xhrs = new XMLHttpRequest();
    xhrs.onreadystatechange = function () {
        if (xhrs.readyState === XMLHttpRequest.DONE) {

            if (xhrs.status === 200) {
                const responses = JSON.parse(xhrs.responseText);
                // consolefun(responses)
                getNewMessage(responses.messages.id, conversation_id)
                // callBack(responses)
            }
        }
    }

    const formData = new FormData();
    formData.append(mds_config.csfr_token_name, $.cookie(mds_config.csfr_cookie_name));
    formData.append('conversation_id', conversation_id)
    xhrs.open("POST", mds_config.base_url + "get-last-message");
    xhrs.send(formData);
}



function getLastMessageByUsersId(sender_id, receiver_id) {
    messageElement = document.getElementById('user-chatBox');
    if (!messageElement) {
        return;
    }
    const xhrs = new XMLHttpRequest();
    xhrs.onreadystatechange = function () {
        if (xhrs.readyState === XMLHttpRequest.DONE) {

            if (xhrs.status === 200) {

                const responses = JSON.parse(xhrs.responseText);


                if (responses.messages == "no") {
                    messageLastTimeout = setTimeout(() => {
                        getLastMessageByUsersId(sender_id, receiver_id)
                    }, 5000);
                    console.log("timeout start")
                }
                else {
                    getNewMessage(responses.last_message_id.id, responses.messages.id)
                    return;
                }

                //
                // callBack(responses)
            }
        }
    }
    const formData = new FormData();
    formData.append(mds_config.csfr_token_name, $.cookie(mds_config.csfr_cookie_name));
    consolefun(sender_id + " last meesage user " + receiver_id)
    formData.append('sender_id', sender_id)
    formData.append('receiver_id', receiver_id)
    xhrs.open("POST", mds_config.base_url + "get-last-message-users");
    xhrs.send(formData);

}







last_messageId = null;
// function getNewMessage(message_id, conversation_id) {
//     if (messageLastTimeout != null || messageLastTimeout != undefined) {
//         clearTimeout(messageLastTimeout)
//         console.log("time out cleared")
//     }
//     console.log("time out running")
//     messageElement = document.getElementById('user-chatBox');
//     if (!messageElement) {
//         return;
//     }
//     const xhrs = new XMLHttpRequest();
//     xhrs.onreadystatechange = function () {
//         if (xhrs.readyState === XMLHttpRequest.DONE) {

//             if (xhrs.status === 200) {
//                 const NewMessage = JSON.parse(xhrs.responseText);

//                 consolefun(NewMessage)
//                 // callBack(responses)
//                 if (NewMessage.return) {
//                     last_messageId = NewMessage.messages.id;
//                     if (auth_user_details.auth_user.id != NewMessage.messages.sender_id) {
//                         appendReceivedMessage(NewMessage.messages, NewMessage.sender_user)
//                         consolefun(auth_user_details.auth_user.id)
//                     }


//                 }
//                 else {
//                     last_messageId = message_id;
//                 }

//             }
//         }

//     }
//     const formData = new FormData();
//     formData.append(mds_config.csfr_token_name, $.cookie(mds_config.csfr_cookie_name));
//     if (last_messageId) {
//         formData.append('last_message_id', last_messageId)
//     }
//     else {
//         formData.append('last_message_id', message_id)
//     }

//     xhrs.open("POST", mds_config.base_url + "get-new-message");
//     formData.append('conversation_id', conversation_id)
//     xhrs.send(formData);
//     // consolefun(last_message_id)
//     messageTimeout = setTimeout(() => {
//         getNewMessage(last_messageId, conversation_id);
//     }, 5000);
// }

function append_send_message(message) {
    console.log("send function")
    if (message.auth_id == auth_user_details.auth_user.id) {
        console.log("send function if")
        appendSentMessage(message, message.sender_details)
    }

}
function append_receive_message(message) {
    console.log(message)
    if (message.id == auth_user_details.auth_user.id) {
        console.log("received function if")
        appendReceivedMessage(message, message.sender_details)
    }

}
function getNewMessage() {

}









//get_user_conversation_by_con_id(34) 
//get_user_conversation_by_user_id(34,3)


function scrollToBottom() {
    var messages_list_box = document.getElementById("chat-user-messages");
    if (messages_list_box) {
        messages_list_box.scrollTop = messages_list_box.scrollHeight;
    }

}
function displayUserList() {

    consolefun("display user")
    conversationList = document.getElementById('conversation-list')
    if (!conversationList.classList.add("displayNone")) { conversationList.classList.add("displayNone") }
    conversationBox = document.getElementById('conversation-box')
    if (conversationBox.classList.contains("active")) { conversationBox.classList.remove("active") }

    importantList = document.getElementById('important-list')
    if (!importantList.classList.contains("displayNone")) { importantList.classList.add("displayNone") }
    importantBox = document.getElementById('important-box')
    if (importantBox.classList.contains("active")) { importantBox.classList.remove("active") }

    userbox = document.getElementById('user-box')
    if (!userbox.classList.contains("active")) { userbox.classList.add("active") }
    userList = document.getElementById('user-list')
    userList.classList.remove("displayNone")
    document.getElementById('messenger-heading-text').innerHTML = "Users"
    search = document.getElementById('message-search-area-box')
    search.classList.remove("displayNone")

}
function displayConversationList() {

    consolefun("display conversation")
    user = document.getElementById('user-list')
    if (!user.classList.contains("displayNone")) { user.classList.add("displayNone") }
    search = document.getElementById('message-search-area-box')
    if (!search.classList.contains("displayNone")) { search.classList.add("displayNone") }
    userbox = document.getElementById('user-box')
    if (userbox.classList.contains("active")) { userbox.classList.remove("active") }

    importantList = document.getElementById('important-list')
    if (!importantList.classList.contains("displayNone")) { importantList.classList.add("displayNone") }
    importantBox = document.getElementById('important-box')
    if (importantBox.classList.contains("active")) { importantBox.classList.remove("active") }

    conversationList = document.getElementById('conversation-list')
    conversationList.classList.remove("displayNone")
    conversationBox = document.getElementById('conversation-box')
    conversationBox.classList.add("active")
    document.getElementById('messenger-heading-text').innerHTML = "Conversation"

}
function displayImportantList() {
   
    consolefun("display conversation")
    user = document.getElementById('user-list')
    if (!user.classList.contains("displayNone")) { user.classList.add("displayNone") }
    search = document.getElementById('message-search-area-box')
    if (!search.classList.contains("displayNone")) { search.classList.add("displayNone") }
    userbox = document.getElementById('user-box')
    if (userbox.classList.contains("active")) { userbox.classList.remove("active") }

    conversationList = document.getElementById('conversation-list')
    if (!conversationList.classList.contains("displayNone")) { conversationList.classList.add("displayNone") }
    conversationBox = document.getElementById('conversation-box')
    if (conversationBox.classList.contains("active")) { conversationBox.classList.remove("active") }

    importantList = document.getElementById('important-list')
    importantList.classList.remove("displayNone")
    importantBox = document.getElementById('important-box')
    importantBox.classList.add("active")
    document.getElementById('messenger-heading-text').innerHTML = "Important Conversation"
    setTimeout(() => {
        messageNotification()
    }, 5000);
}
function createMessageWidget(user_id = null) {
    setTimeout(() => {
        messageNotification()
    }, 2000); 
    setCookie('chatIsOpen', true, 2)
    if (checkdarkModeCookie()) {
        darkModeClass = 'dark-mode'
        sunIconDisplay = '';
        moonIconDisplay = 'displayNone'
    }
    else {
        darkModeClass = ''
        sunIconDisplay = 'displayNone';
        moonIconDisplay = ''
    }
    if (checkMINIScreenCookie()) {
        minScreenClass = 'min-screen'
        tabletIconDisplay = '';
        mobileIconDisplay = 'displayNone'
    }
    else {
        minScreenClass = ''
        tabletIconDisplay = 'displayNone';
        mobileIconDisplay = ''
    }
    messageWidget = document.createElement('div');
    messageWidget.className = 'message-box box-animation ' + darkModeClass + ' ' + minScreenClass;
    messageWidget.id = 'message-box'
    messageWidget.innerHTML = '<div style="width:100%;height:100%; background-color: #f2f2f2;">' +
        '<div class="top-menu">' +
        '<div class="top-left">' +
        '<i class="mess-icon-box fa-solid fa-mobile-screen-button ' + mobileIconDisplay + '" id ="mobile-icon" aria-hidden="true" onclick="minScreen()"></i>' +
        //    '<i class="fa-solid fa-mobile-screen-button"></i>'+
        ' <i class="mess-icon-box fa-solid fa-tablet-screen-button ' + tabletIconDisplay + '" id ="tablet-icon" aria-hidden="true" onclick="largeScreen()"></i>' +

        '<i class="mess-icon-box fa-solid fa-moon ' + moonIconDisplay + '" id ="dark-mode" aria-hidden="true" onclick="darkMode()"></i>' +
        '<i class="mess-icon-box fa-solid fa-sun ' + sunIconDisplay + '" id ="light-mode"  aria-hidden="true" onclick="lightMode()"></i>' +
        ' </div>' +
        ' <div class="top-right">' +
        ' <i class="mess-icon-box fa fa-close" aria-hidden="true" onclick="closeWidget()"></i>' +
        '</div>' +
        ' </div>' +
        '<div class="messenger-box" id="messenger-box">' +


        '</div>' +

        '</div>'
    document.getElementById('messenger_container').appendChild(messageWidget)
    if (user_id == null) {
        setTimeout(() => {
            createConversationListBox()

        }, 5);
    }

}





function openCoversationContactButtons(user_id) {
    chatIsOpen = getCookie('chatIsOpen')
    // console.log(chatIsOpen)
    if (chatIsOpen != "NotOpen") {
        document.getElementById('messenger-box').innerHTML = '';
        // createMessageWidget(user_id)
    }
    else {
        createMessageWidget(user_id)
        setCookie('chatIsOpen', true, 2)
    }

    setTimeout(() => {
        chat_div = document.createElement('div');
        chat_div.className = "user-chatBox box-animation";
        chat_div.id = 'user-chatBox';
        document.getElementById('messenger-box').appendChild(chat_div)

        get_user_conversation_by_user_id(auth_user_details.auth_user.id, user_id)
        createMessageTextarea(user_id)
    }, 50);
}




function closeWidget() {
    messageNotification();
    activeChat = null;
    setCookie('chatIsOpen', "NotOpen", 2)
    document.getElementById('message-box').remove();
}
function minScreen() {
    var messages_list_box = document.getElementById("chat-user-messages");
    if (messages_list_box) {
        if (messages_list_box.scrollTop == messages_list_box.scrollHeight) {
            messages_list_box.scrollTop = messages_list_box.scrollHeight
        }
    }
    document.cookie = "smallScreen=YES;"
    document.getElementById('message-box').classList.add('min-screen')
    document.getElementById('mobile-icon').classList.add('displayNone')
    document.getElementById('tablet-icon').classList.remove('displayNone')
}
function largeScreen() {
    document.cookie = "smallScreen=NO;"
    document.getElementById('message-box').classList.remove('min-screen')
    document.getElementById('mobile-icon').classList.remove('displayNone')
    document.getElementById('tablet-icon').classList.add('displayNone')
}
function darkMode() {

    document.cookie = "darkmode=YES;";
    document.getElementById('message-box').classList.add('dark-mode')
    document.getElementById('dark-mode').classList.add('displayNone')
    document.getElementById('light-mode').classList.remove('displayNone')
}

function lightMode() {
    document.cookie = "darkmode=NO;";
    document.getElementById('message-box').classList.remove('dark-mode')
    document.getElementById('dark-mode').classList.remove('displayNone')
    document.getElementById('light-mode').classList.add('displayNone')
}


function getdarkCookie(cname) {
    let name = cname + "=";
    let decodedCookie = decodeURIComponent(document.cookie);
    let ca = decodedCookie.split(';');
    for (let i = 0; i < ca.length; i++) {
        let c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}
function checkdarkModeCookie() {

    let darkmode = getdarkCookie("darkmode");
    if (darkmode == "NO") {
        return 0;
    } else {
        return 1;
    }
}
function checkMINIScreenCookie() {

    let smallScreen = getdarkCookie("smallScreen");
    if (smallScreen == "NO") {
        return 0;
    } else {
        return 1;
    }
}

function UserSearch() {
    searchInput = document.getElementById('message-search-area');
    searchInput.addEventListener('keydown', function (e) {


        if (!e.shiftKey && !e.ctrlKey && !e.altKey && !e.metaKey) {
            InputValue = e.target.value;
            trimmedValue = InputValue.trim();
            consolefun("else")

            if (e.key == "Enter") {
                if (trimmedValue == '') {
                    // document.getElementById('user-list').innerHTML='';
                    consolefun("trimmed")
                    setTimeout(() => {
                        // getAllUsers()
                    }, 200);
                }
                else {
                    const formData = new FormData();
                    formData.append(mds_config.csfr_token_name, $.cookie(mds_config.csfr_cookie_name));
                    formData.append('search_text', trimmedValue);
                    document.getElementById('user-list').innerHTML = '';
                    // POST(mds_config.base_url+ "search-users",formData,displayUsers)
                    setTimeout(() => {
                        searchPOST(formData)
                    }, 200);
                }
            }


        }

        else {
            consolefun("reserve key")
        }
    })
}
function searchPOST(data) {
    const xhrs = new XMLHttpRequest();
    xhrs.onreadystatechange = function () {
        if (xhrs.readyState === XMLHttpRequest.DONE) {

            if (xhrs.status === 200) {
                const responses = JSON.parse(xhrs.responseText);
                consolefun(responses)
                if (responses.searchedUsers == "no") {
                    document.getElementById('user-list').innerHTML = '<p style="text-align:center">No User Found</p>';
                }
                else {
                    responses.searchedUsers.forEach(searchedUsers => {

                        // formData.append("sender_id",all_conversations.sender_id);
                        // formData.append("receiver_id",all_conversations.receiver_id);
                        displayUsers(searchedUsers)

                    });
                }



            }
        }
    }
    xhrs.open("POST", mds_config.base_url + "search-users");
    xhrs.send(data);
}
// createMessageWidget()
let message_count;
function messageNotification() {
    if (auth_user_details) {
        const xhrs = new XMLHttpRequest();
        xhrs.onreadystatechange = function () {
            if (xhrs.readyState === XMLHttpRequest.DONE) {

                if (xhrs.status === 200) {
                    const responses = JSON.parse(xhrs.responseText);
                    // consolefun(responses)
                    if (responses.message_count > 0) {
                        responses.message_data.forEach(message_data => {
                            notify = document.getElementById("m-noti-" + message_data.message_id)
                            notify_im = document.getElementById("i-c-noti-" + message_data.message_id)
                            if (notify) {
                                if (notify.classList.contains("displayNone")) {
                                    notify.classList.remove("displayNone")
                                }
                            }
                            if (notify_im) {
                                if (notify_im.classList.contains("displayNone")) {
                                    notify_im.classList.remove("displayNone")
                                }
                            }
                        })
                    }
                    if (!message_count) {

                        document.getElementById("messages_count").innerHTML = responses.message_count;
                        document.getElementById("messages_count-2").innerHTML = '(' + responses.message_count + ')';
                        message_count = responses.message_count;
                        var element = document.getElementById('messages_count');
                        var element_two = document.getElementById('messages_count-2');

                        // Check if the element has a specific class
                        if (element.classList.contains('displayNone')) {
                            if (responses.message_count > 0) {
                                element.classList.remove('displayNone');
                                element_two.classList.remove('displayNone');
                            }
                        } else {
                            element.classList.add('displayNone');
                            element_two.classList.add('displayNone');
                        }

                    }
                    else if (message_count < responses.message_count) {
                        document.getElementById("messages_count").innerHTML = responses.message_count;
                        document.getElementById("messages_count-2").innerHTML = '(' + responses.message_count + ')';
                        message_count = responses.message_count;
                        var element = document.getElementById('messages_count');
                        var element_two = document.getElementById('messages_count-2');
                        if (element.classList.contains('displayNone')) {
                            element.classList.remove('displayNone');
                            element_two.classList.remove('displayNone');
                        } else {

                        }

                    }
                    else if (message_count > responses.message_count) {
                        document.getElementById("messages_count").innerHTML = responses.message_count;
                        document.getElementById("messages_count-2").innerHTML = '(' + responses.message_count + ')';
                        message_count = responses.message_count;
                        var element = document.getElementById('messages_count');
                        var element_two = document.getElementById('messages_count-2');
                        if (element.classList.contains('displayNone')) {
                            element.classList.remove('displayNone');
                            element_two.classList.remove('displayNone');

                        } else {
                            if (responses.message_count <= 0) {
                                element.classList.add('displayNone');
                                element_two.classList.add('displayNone');
                            }
                        }

                    }
                    else {
                        var element = document.getElementById('messages_count');
                        document.getElementById("messages_count").innerHTML = message_count;
                        document.getElementById("messages_count-2").innerHTML = '(' + message_count + ')';
                        var element_two = document.getElementById('messages_count-2');
                        if (element.classList.contains('displayNone')) {
                            element.classList.remove('displayNone');
                            element_two.classList.remove('displayNone');
                        } else {

                        }

                    }

                }
            }
        }
        xhrs.open("GET", mds_config.base_url + "unread-messages");
        xhrs.send();

        // setTimeout(() => {
        //     messageNotification();
        // }, 5000);

    }
    else {
        console.log("here")
    }
}
setTimeout(() => {
    messageNotification()
}, 2000);


// let conversationListDiv;
// function rightClick(){


//     conversationListDiv.addEventListener("contextmenu", function(e){
//         child = e.target;
//         const {clientX : mouseX,clientY:mouseY} =e;
//         contextMenu.style.top = `${mouseY}px`;
//         contextMenu.style.left = `${mouseX}px`;
//         consolefun(`${mouseY}px`)
//         contextMenu.classList.remove('displayNone')
//         document.getElementById("id").innerHTML = e.currentTarget.id;
//         // alert("hello");
//     e.preventDefault();
//     },false)

// }
document.addEventListener("click", function () {
    contextMenu = document.getElementById("context-menu");
    if (!contextMenu.classList.contains("displayNone")) {
        contextMenu.classList.add('displayNone')
    }
})



function deleteconversation(id) {
    let deleteConversation = document.getElementById(id);
    conId = deleteConversation.getAttribute("data-id");
    setTimeout(() => {


        const xhrs = new XMLHttpRequest();
        xhrs.onreadystatechange = function () {
            if (xhrs.readyState === XMLHttpRequest.DONE) {

                if (xhrs.status === 200) {
                    const responses = JSON.parse(xhrs.responseText);
                    if (responses.return == 1) {
                        document.getElementById("Conversationid-" + conId).remove();
                        if (document.getElementById("ImpConversationid-" + conId)) { document.getElementById("ImpConversationid-" + conId).remove(); }
                    }
                }
            }
        }
        formData = new FormData();
        formData.append(mds_config.csfr_token_name, $.cookie(mds_config.csfr_cookie_name));
        formData.append('con_id', conId);
        xhrs.open("POST", mds_config.base_url + 'delete-conversation');
        xhrs.send(formData);
    }, 300);
    console.log(conId)
}
function markAsImportant(id) {
    let element = document.getElementById(id);
    conId = element.getAttribute("data-id");
    value = element.getAttribute("data-imp");
    setTimeout(() => {


        const xhrs = new XMLHttpRequest();
        xhrs.onreadystatechange = function () {
            if (xhrs.readyState === XMLHttpRequest.DONE) {

                if (xhrs.status === 200) {
                    const responses = JSON.parse(xhrs.responseText);
                    consolefun(responses)
                    if (!document.getElementById("important-list").classList.contains('displayNone')) {

                        document.getElementById("ImpConversationid-" + conId).remove()

                    }
                    else {
                        document.getElementById("important-list").innerHTML = "";
                        setTimeout(() => {
                            get_all_important_conversation()
                        }, 30);
                    }


                }
            }
        }
        formData = new FormData();
        formData.append(mds_config.csfr_token_name, $.cookie(mds_config.csfr_cookie_name));
        formData.append('con_id', conId);
        formData.append('value', value);
        xhrs.open("POST", mds_config.base_url + 'mark-conversation-imp');
        xhrs.send(formData);
    }, 300);
    console.log(conId)

}
function conversationMarkImp(id) {
    console.log(id)
    nonImpStar = document.getElementById('non-imp-star')
    ImpStar = document.getElementById('imp-star')
    if (nonImpStar.classList.contains('imp-star-display')) {
        ImpStar.classList.remove('imp-star-display')
        nonImpStar.classList.add('imp-star-display')
        console.log("nonstar conatins display non")
    }
    else {
        ImpStar.classList.add('imp-star-display')
        nonImpStar.classList.remove('imp-star-display')
        console.log("nonstar not conatins display non")
    }
    setTimeout(() => {


        const xhrs = new XMLHttpRequest();
        xhrs.onreadystatechange = function () {
            if (xhrs.readyState === XMLHttpRequest.DONE) {

                if (xhrs.status === 200) {
                    const responses = JSON.parse(xhrs.responseText);
                    consolefun(responses)

                    nonImpStar = document.getElementById('non-imp-star')
                    ImpStar = document.getElementById('imp-star')
                    if (nonImpStar.classList.contains('imp-star-display')) {
                        document.getElementById('imp-star').classList.add('imp-star-display')
                        document.getElementById('non-imp-star').classList.remove('imp-star-display')
                        console.log("nonstar conatins display non")
                    }
                    else {
                        document.getElementById('imp-star').classList.remove('imp-star-display')
                        document.getElementById('non-imp-star').classList.add('imp-star-display')
                        console.log("nonstar not conatins display non")
                    }

                }
            }
        }
        formData = new FormData();
        formData.append(mds_config.csfr_token_name, $.cookie(mds_config.csfr_cookie_name));
        formData.append('userId', id);
        // formData.append('value',value);
        xhrs.open("POST", mds_config.base_url + 'mark-conversation-imp-user');
        xhrs.send(formData);
    }, 300);
    // console.log(conId)

}

function imageModal(imageUrl) {
    $('#image_modal').modal('show')
    document.getElementById('message_image').innerHTML = "<img src='" + imageUrl + "' style='width:100%; height:100%'>"
    console.log()
}
function linkify(text) {
    // Regular expression to find URLs in the text
    const urlRegex = /(https?:\/\/[^\s]+)/g;
    // Replace URLs with anchor tags
    // return text.replace(urlRegex, '<a style="background:transparent;color:#0000ff" href="$1" target="_blank">$1</a>');
    return text.replace(urlRegex, (url) => {
        const urlObj = new URL(url);
        const websiteName = urlObj.hostname.replace(/^www\./i, '');
        return `<a style="background:transparent;color:#0000ff" href="${url}" target="_blank">${websiteName}</a>`;
    });
}


function getCookie(cname) {
    let name = cname + "=";
    let decodedCookie = decodeURIComponent(document.cookie);
    let ca = decodedCookie.split(';');
    for (let i = 0; i < ca.length; i++) {
        let c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}

function setCookie(cname, cvalue, exdays) {
    const d = new Date();
    d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
    let expires = "expires=" + d.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}