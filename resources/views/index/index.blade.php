<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Title</title>
  <link href="/css/app.css">
</head>
<body>
    <div class="scrollbox" id="box" style="height: 1000px; overflow: scroll">

    </div>
</body>
<script src="/js/app.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        if('WebSocket' in window){
// 创建websocket实例
            var socket = new WebSocket('ws://localhost:1113/chat');

//打开
            socket.onopen = function(event)
            {
// 发送
                socket.send('I am the client and I\'m listening!');

// 监听
                socket.onmessage = function(event) {
                    console.log('Client received a message',event);
                };

// 关闭监听
                socket.onclose = function(event) {
                    console.log('Client notified socket has closed',event);
                };

// 关闭
//                    socket.close()
            };
        }else{
            alert('本浏览器不支持WebSocket哦~');
        }
    });
    </script>
</html>