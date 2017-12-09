<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class LogServer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'log_server:start';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '开启日志服务器';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //echo $this->websocket_accept_key('x3JJHMbDL1EzLkh9GBhXDw==');die;
        set_time_limit(0);
//设置地址与端口
        $address = '0.0.0.0'; //服务端ip
        $port = 1113;
//创建socket：AF_INET=是ipv4 如果用ipv6，则参数为 AF_INET6 ， SOCK_STREAM为socket的tcp类型，如果是UDP则使用SOCK_DGRAM
        $sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP) or die("socket_create() failed : ".socket_strerror(socket_last_error()). "\n");
//阻塞模式
        socket_set_block($sock) or die("socket_set_block() failed : ".socket_strerror(socket_last_error()) ."\n");

//绑定到socket端口
        $result = socket_bind($sock, $address, $port) or die("socket_bind() failed : ". socket_strerror(socket_last_error()) . "\n");
//开始监听
        $result = socket_listen($sock, 4) or die("socket_listen() failed : ". socket_strerror(socket_last_error()) . "\n");
        echo "OK\nBinding the socket on $address:$port ...\n";
        echo "OK\nNow ready to accept connections.\nListening on the socket ...\n";

        do {//Never stop the daemon
            //它接收连接请求并调用一个子链接socket来处理客户端和服务器间的信息
            $msgsock = socket_accept($sock) or die("sock_accept() failed : ". socket_strerror(socket_last_error()) . "\n");

            //读取客户端数据
            $buf = socket_read($msgsock, 8192);
            echo "Received msg : $buf  \n";
            $key = '';
            if(preg_match("/Sec-WebSocket-Key: (.*)\r\n/",$buf,$match)){
                $key = $this->websocket_accept_key($match[1]);
            }

            //数据传输，向客户端写入返回结果
            $msg = "HTTP/1.1 101 Switching Protocols
Upgrade: websocket
Connection: Upgrade
Sec-WebSocket-Accept: $key
Sec-WebSocket-Version: 13
KeepAlive: off";
            echo "\n".$msg;

            socket_write($msgsock, $msg, strlen($msg)) or die("socket_write() failed : ". socket_strerror(socket_last_error()). "\n");
            //输出返回到客户端时，父/子socket都应通过socket_close来终止
            socket_close($msgsock);
        }while(true);

        socket_close($sock);
    }

    private function websocket_accept_key($key){
        if (0 === preg_match('#^[+/0-9A-Za-z]{21}[AQgw]==$#', $key)
            || 16 !== strlen(base64_decode($key))
        )
        {
            //Header Sec-WebSocket-Key is illegal;
            return false;
        }
        return base64_encode(sha1($key
            . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11',
            true));
    }
}
