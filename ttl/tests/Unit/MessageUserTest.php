<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\User;
use App\Message;


class MessageUserTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */

    
    public function getMessageReceivers($id)
    {
        $receivers = array();

        if (Message::find($id) == null )
        {
            return null;
        }

        $receivers = Message::find($id)->user_receive()->get();


        foreach ($receivers as $r)
        {
            array_push($receivers, $r);
        }

        return $receivers;
    }

    public function getMessageSent($userId)
    {
        $sent = array();

        if (User::find($userId) == null)
        {
            return null;
        }

        $sent = User::find($userId)->messageSent()->get();


        foreach ($sent as $s)
        {
            array_push($sent, $s);
        }

        return $sent;
    }


    public function testMessageReceivers()
    {
        $user = User::where('email','mike@gmail.com')->first();
        $message = Message::where('user_id',$user->id)->first();
        $receivers = $this->getMessageReceivers($message->id);
        $this->assertNotNull($receivers);
        $this->assertEquals($receivers[0]->email,'kamil@gmail.com');
        $this->assertNull($receivers[1]);

        $user = User::where('email','kamil@gmail.com')->first();
        $message = Message::where('user_id',$user->id)->first();
        $receivers = $this->getMessageReceivers($message->id);
        $this->assertNotNull($receivers);
        $this->assertEquals($receivers[0]->email,'mike@gmail.com');
        $this->assertEquals($receivers[1]->email,'tudor@gmail.com');
        $this->assertNull($receivers[2]);

        $user = User::where('email','cristian@gmail.com')->first();
        $message = Message::where('user_id',$user->id)->first();
        $receivers = $this->getMessageReceivers($message->id);
        $this->assertNotNull($receivers);
        $this->assertEquals($receivers[0]->email,'adrian@gmail.com');
        $this->assertNull($receivers[1]);



    }

    public function testMessageSender()
    {
        
        $user = User::where('email','mike@gmail.com')->first();
        $message = Message::where('user_id',$user->id)->first();
        $sent = $this->getMessageSent($user->id);
        $this->assertNotNull($sent);
        $this->assertEquals($message,$sent[0]);
        $this->assertNull($sent[1]);

        $user = User::where('email','kamil@gmail.com')->first();
        $message = Message::where('user_id',$user->id)->first();
        $sent = $this->getMessageSent($user->id);
        $this->assertNotNull($sent);
        $this->assertEquals($message,$sent[0]);
        $this->assertNull($sent[1]);

        $user = User::where('email','cristian@gmail.com')->first();
        $message = Message::where('user_id',$user->id)->first();
        $sent = $this->getMessageSent($user->id);
        $this->assertNotNull($sent);
        $this->assertEquals($message,$sent[0]);
        $this->assertEquals('Test3',$sent[0]->title);
        $this->assertEquals('Test4',$sent[1]->title);
        $this->assertNull($sent[2]);

    }
    
}
