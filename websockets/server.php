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

    private $active_users = 0;
    private $ping_every = 55; // seconds, since a websocket auto-closes at 60
    private $last_ping = 0;

    protected function write_out($value) {
        $stamp = date('Y-m-d H:i:s');
        $out = fopen('php://output', 'w'); //output handler
        fputs($out, "$stamp: $value\n"); //writing output operation
        fclose($out); //closing handler
    }

    protected function write_activity($direction = 1, $qs) {
        $this->active_users = max(0, $this->active_users + $direction);
        $log = "active.log";
        $date = new DateTime();
        $date = $date->format("Y:m:d H:i:s");
        $entry = implode("\t", [time(),$date,$qs,$this->active_users]) . PHP_EOL;
        $fl = fopen($log,'a');
        fwrite($fl, $entry);
        fclose($fl);
    }

    protected function write_err($value) {
        $stamp = date('Y-m-d H:i:s');
        $out = fopen('php://stderr', 'w'); //output handler
        fputs($out, "$stamp: $value\n"); //writing output operation
        fclose($out); //closing handler
    }

    protected function write_console($value) {
        $stamp = date('Y-m-d H:i:s');
        $out = fopen('php://stdout', 'w'); //output handler
        fputs($out, "$stamp: $value\n"); //writing output operation
        fclose($out); //closing handler
    }

    function process($user, $message) {
        $this->send($user, $message);
    }

    function connected($user)
    {
        $hash = substr($user->headers["get"], 1);
        $hash = str_replace("licence/", "", $hash);
        $this->write_activity(1, $hash);
        if (!preg_match('/(?:^[a-f0-9]{32}$)|(?:^[A-Z0-9]{5}(?:-[A-Z0-9]{5}){4}$)/', $hash)) {
            $this->write_err("Connect failed [bad data] $hash");
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
        $hash = str_replace("licence/", "", $hash);
        $this->write_activity(-1, $hash);
        if (!preg_match('/(?:^[a-f0-9]{32}$)|(?:^[A-Z0-9]{5}(?:-[A-Z0-9]{5}){4}$)/', $hash)) {
            $this->write_err("Close failed [bad data] $hash");
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

    // send a ping frame to each user slightly faster than once per minute to enable keepalives
    function tick() {
        $now = time();
        if ($now > $this->last_ping + $this->ping_every) {
            $this->last_ping = $now;
            $users = $this->getUserBySocket(9000);
            foreach ($this->users as $user) {
                if ($user->handshake) {
                    $message = $this->frame('ping', $user, 'ping');
                    $result = @socket_write($user->socket, $message, strlen($message));
                }
            }
        }
    }
}

$echo = new echoServer("0.0.0.0", "9000");

try {
    $echo->run();
} catch (Exception $e) {
    $echo->stdout($e->getMessage());
}

