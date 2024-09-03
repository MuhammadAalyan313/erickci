<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
</head>

<body>
    <div class="container col-5" style="border-style: dashed; background-color: #666666; margin-top: 7%;">

        <form style="margin-top:1%" id="chatForm" onsubmit="sendMessage(event)">
            <input type="text" placeholder="Message Here" name="msg" id="msg">
            <input type="number" placeholder="ID Here" name="id" id="receiver_id_web">
            <button style="margin: 4%; background-color:#FA7348; color:white;" type="submit" class="btn">Submit</button>
        </form>
    </div>
    <script>    
        // var conn = new WebSocket('ws://localhost:5412');

        // conn.onopen = function(e) {
        //     if(e.readyState === WebSocket.open){
        //     console.log("Connection established!");
        //     }
        //     else{
        //         console.log('Connection Failed failed to open');
        //     }
        // };

        // conn.onmessage = function(e) {
        //     console.log(e.data);
        // };

        // function sendMessage(event) {
        //     event.preventDefault();
        //     var msg = document.getElementById('msg').value;
        //     var id = document.getElementById('receiver_id_web').value;
        //     if (msg.trim() === '' ) {
        //         alert('Please enter both message and ID');
        //         return;
        //     }
        //     var message = {
        //         msg: msg,
        //         id: id,
        //         channel: 'msg',
        //     };
        //     conn.send(JSON.stringify(message));
        //     document.getElementById('msg').value = '';
        // }

        // function attachUserIdToSocket() {
        //     conn.send(JSON.stringify({
        //         auth_id: <?php echo $this->auth_user->id; ?>,
        //         channel: 'authAttachment',
        //         msg: '',
        //     }));
        // }
        // setTimeout(() => {
        //     attachUserIdToSocket();
        // }, 1000);
    </script>

</body>

</html>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>