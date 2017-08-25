#!/usr/bin/env php
<?php

date_default_timezone_set("Australia/Sydney");

/**
*   CourseSuite Licencing Service
*   On Connect: decrements a counter for a given hash / key
*   On Disconnect: increments a counter for a given hash / key
*   Key management handled by CourseSuite web site
*
*/

require_once __DIR__ . '/class.WebSocketServer.php';

class echoServer extends WebSocketServer
{
    protected function write_out($value) {
        $stamp = date('Y-m-d H:i:s');
        $out = fopen('php://output', 'w'); //output handler
        fputs($out, "$stamp: $value\n"); //writing output operation
        fclose($out); //closing handler
    }

    protected function write_err($value) {
        $stamp = date('Y-m-d H:i:s');
        $out = fopen('php://stderr', 'w'); //output handler
        fputs($out, "$stamp: $value\n"); //writing output operation
        fclose($out); //closing handler
    }

    function process($user, $message)
    {
        $this->send($user, $message);
    }

    function connected($user)
    {
        $hash = substr($user->headers["get"], 1);
        if (!preg_match('/^[a-f0-9]{32}$/', $hash)) {
            $this->write_err("Connect failed [bad data]");
            return;
        }
        $this->write_out("Client connected: $hash");
        $redis = new Redis();
        if ($redis->connect('127.0.0.1', 6379)) {
            $redis->setOption(Redis::OPT_PREFIX, 'NinjaSuite:');
            if ($redis->exists($hash)) {
                $value = (int) $redis->get($hash);
                if ($value > 0) {
                    $redis->decr($hash);
                }
            }
            $redis->close();
        }
    }

    function closed($user)
    {
        $hash = substr($user->headers["get"], 1);
        if (!preg_match('/^[a-f0-9]{32}$/', $hash)) {
            $this->write_err("Close failed [bad data]");
            return;
        }
        $this->write_out("Client disconnected: $hash");
        $redis = new Redis();
        if ($redis->connect('127.0.0.1', 6379)) {
            $redis->setOption(Redis::OPT_PREFIX, 'NinjaSuite:');
            if ($redis->exists($hash)) {
                $value = (int) $redis->get($hash);
                $redis->incr($hash);
            }
            $redis->close();
        }
    }
}

$echo = new echoServer("0.0.0.0", "9000");

try {
    $echo->run();
} catch (Exception $e) {
    $echo->stdout($e->getMessage());
}
