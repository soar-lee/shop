<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LogServerTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {
        do{
            $this->send('127.0.0.1', 1113, "hello");
            sleep(1);
        }while(1);
    }

    protected function send($host, $port = 1113, $message = '', $address = '/')
    {
        $url = 'http://' . $host . ':' . $port . $address;
        $ch  = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $message);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $headers = [
            "Content-Type: application/json;charset=UTF-8",
        ];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); //设置header
        return curl_exec($ch);
    }
}
