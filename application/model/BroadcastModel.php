<?php

/**
 * NoteModel
 * This is basically a simple CRUD (Create/Read/Update/Delete) demonstration.
 */
class BroadcastModel
{
    /**
     * Get all notes (notes are just example data that the user has created)
     * @return array an array with several objects (the results)
     */
    public static function getAllBroadcast()
    {
        $database = DatabaseFactory::getFactory()->getConnection();
        
        $userid = Session::get('user_id');
        
        $sql = "SELECT broadcast_id, broadcast_name, broadcast_desc, user_id, broadcast_date FROM broadcast WHERE user_id = :user_id";
        $query = $database->prepare($sql);
        $query->bindParam(":user_id",$userid, PDO::PARAM_INT); 
        //$query->execute(array(':user_id' => Session::get('user_id')));
        $query->execute();
        // fetchAll() is the PDO method that gets all result rows
        return $query->fetchAll();
    }

    /**
     * Get a single note
     * @param int $note_id id of the specific note
     * @return object a single object (the result)
     */
    
    public static function getBroadcast($broadcast_id)
    {
        $database = DatabaseFactory::getFactory()->getConnection();

        $userid = Session::get('user_id');
        
        $sql = "SELECT broadcast_id, broadcast_name, broadcast_desc, broadcast_date FROM broadcast WHERE user_id = :user_id AND broadcast_id = :broadcast_id LIMIT 1";
        $query = $database->prepare($sql);
        $query->bindParam(":user_id",$userid, PDO::PARAM_INT); 
        $query->bindParam(":broadcast_id",$broadcast_id, PDO::PARAM_INT);
        //$query->execute(array(':user_id' => Session::get('user_id'), ':broadcast_id' => $broadcast_id));
        $query->execute();
        // fetch() is the PDO method that gets a single result
        return $query->fetch();
    }

    
    public static function getEveryBroadcast()
    {
        
        $database = DatabaseFactory::getFactory()->getConnection();
        
        $userid = Session::get('user_id');
        
        $sql = "SELECT * FROM broadcast LEFT JOIN broadcastmarkread ON broadcast.broadcast_id = broadcastmarkread.broadcast_id WHERE                                broadcastmarkread.user_id = :user_id and broadcastmarkread.markasread = 0";
        $query = $database->prepare($sql);
        $query->bindParam(":user_id",$userid, PDO::PARAM_INT);
        $query->execute();
        
        //$sql2 = "SELECT * FROM markread";
        //$query2 = $database->prepare($sql2);
        //$query2->execute(array());
        
        // fetchAll() is the PDO method that gets all result rows
        return $query->fetchAll();
    }
    
    /**
     * Set a note (create a new one)
     * @param string $note_text note text that will be created
     * @return bool feedback (was the note created properly ?)
     */
    
    public static function createBroadcast($broad_name, $broad_desc)
    {
        
        if (!$broad_name || strlen($broad_name) == 0 || !$broad_desc || strlen($broad_desc) == 0) 
        {
            Session::add('feedback_negative', "Something on your Broadcast was left empty");
            return false;
        }
        
        
        if (strlen($broad_name) < 8 || strlen($broad_name) > 128)
        {
            Session::add('feedback_negative', "Broadcast Title exceeded/lacked the right amount of characters");
            return false;
        }
        
        
        if (strlen($broad_desc) < 50|| strlen($broad_desc) > 528)
        {
            Session::add('feedback_negative', "Broadcast Description exceeded/lacked the right amount of characters");
            return false;
        }
        
        
        $database = DatabaseFactory::getFactory()->getConnection();
        
        $today = date("y.m.d");
        $userid = Session::get('user_id');
// $database->beginTransaction();
        $sql = "INSERT INTO broadcast (broadcast_name, broadcast_desc, user_id, broadcast_date) VALUES (:broadcast_name, :broadcast_desc, :user_id, :broadcast_date)";
        $query = $database->prepare($sql);
        $query->bindParam(":broadcast_name", $broad_name, PDO::PARAM_STR); 
        $query->bindParam(":broadcast_desc", $broad_desc, PDO::PARAM_STR); 
        $query->bindParam(":user_id",$userid, PDO::PARAM_INT); 
        $query->bindParam(":broadcast_date",$today); 
        
        //$query->execute(array(':broadcast_name' => $broad_name, ':broadcast_desc' => $broad_desc, ':user_id' => Session::get('user_id'), ':broadcast_date' => $today));
        $query->execute();

        BroadcastModel::addBroadcastUsers();
        
        if ($query->rowCount() == 1) {
             
            $broadcast_id = $database->lastInsertId();
            // printf('this is the broadcast id '.$broadcast_id);
            return true;
        }
    

        // default return
        Session::add('feedback_negative', "Broadcast creation failed");
        return false;
        
        
        
        
    }

    /**
     * Update an existing note
     * @param int $note_id id of the specific note
     * @param string $note_text new text of the specific note
     * @return bool feedback (was the update successful ?)
     */
    
    public static function updateBroadcast($broadcast_id, $broad_name, $broad_desc)
    {
        
        
        if (!$broadcast_id || !$broad_name || !$broad_desc) {
            return false;
        }

        $database = DatabaseFactory::getFactory()->getConnection();
        
        $today = date("y.m.d");
        $userid = Session::get('user_id');

        $sql = "UPDATE broadcast SET broadcast_name = :broadcast_name, broadcast_desc = :broadcast_desc, broadcast_date = :broadcast_date WHERE broadcast_id = :broadcast_id AND user_id = :user_id LIMIT 1";
        $query = $database->prepare($sql);
        $query->bindParam(":broadcast_id",$broadcast_id, PDO::PARAM_INT);
        $query->bindParam(":broadcast_name", $broad_name, PDO::PARAM_STR); 
        $query->bindParam(":broadcast_desc", $broad_desc, PDO::PARAM_STR); 
        $query->bindParam(":user_id",$userid, PDO::PARAM_INT); 
        $query->bindParam(":broadcast_date",$today); 
        $query->execute();
        //$query->execute(array(':broadcast_id' => $broadcast_id, ':broadcast_name' => $broad_name, ':broadcast_desc' => $broad_desc, 
        //                      ':user_id' => Session::get('user_id'), ':broadcast_date' => $today));
        
        if ($query->rowCount() == 1) {
            return true;
        }

        Session::add('feedback_negative', "editing a broadcast failed");
        return false;
    }

    /**
     * Delete a specific note
     * @param int $note_id id of the note
     * @return bool feedback (was the note deleted properly ?)
     */
    
    public static function deleteBroadcast($broadcast_id)
    {
        if (!$broadcast_id) {
            return false;
        }

        $database = DatabaseFactory::getFactory()->getConnection();

        $userid = Session::get('user_id');
        
        $sql = "DELETE FROM broadcast WHERE broadcast_id = :broadcast_id AND user_id = :user_id LIMIT 1";
        $query = $database->prepare($sql);
        $query->bindParam(":user_id",$userid, PDO::PARAM_INT); 
        $query->bindParam(":broadcast_id",$broadcast_id, PDO::PARAM_INT);
        $query->execute();
        //$query->execute(array(':broadcast_id' => $broadcast_id, ':user_id' => Session::get('user_id')));

        if ($query->rowCount() == 1) {
            return true;
        }

        // default return
        Session::add('feedback_negative', Text::get('FEEDBACK_NOTE_DELETION_FAILED'));
        return false;
    }
    
    
    public static function markBroadcast($broadcast_id)
    {
        
        if (!$broadcast_id) {
            return false;
        }
        
        $database = DatabaseFactory::getFactory()->getConnection();

        $userid = Session::get('user_id');
        
        $sql = "UPDATE broadcastmarkread SET broadcast_id = :broadcast_id, user_id = :user_id, markasread = 1 WHERE broadcast_id = :broadcast_id AND                    user_id = :user_id ";
        $query = $database->prepare($sql);
        $query->bindParam(":user_id",$userid, PDO::PARAM_INT); 
        $query->bindParam(":broadcast_id",$broadcast_id, PDO::PARAM_INT);
        $query->execute();
        //$query->execute(array(':broadcast_id' => $broadcast_id, ':user_id' => Session::get('user_id')));

            if ($query->rowCount() == 1) {
                return true;
            }

            // default return
            Session::add('feedback_negative', 'Mark as read failed');
            return false;
        
    }
    
    public static function addBroadcastUsers()
    {
        $database = DatabaseFactory::getFactory()->getConnection();
        
        $sql3 = "SELECT broadcast_id from broadcast";
        $query3 = $database->prepare($sql3);
        $query3->execute();
        $broadcast_id = $database->lastInsertId();
        
        $sql = "SELECT user_id from users";
        $query = $database->prepare($sql);
        $query->execute();
        
        $rows = $query->fetchAll();
        
        foreach($rows as $row)
        //foreach($this->query as $key => $value)
        {
            $sql2 = "INSERT INTO broadcastmarkread(broadcast_id, user_id, markasread) VALUES (:broadcast_id, :user_id, 0)"; 
            $query2 = $database->prepare($sql2);
            
            $query2->execute(array(':broadcast_id' => $broadcast_id, ':user_id' => $row->user_id ));
            
            //if ($query->rowCount() == 1) {
            //    return true;
            //}
        }
    }
    public static function deleteBroadcastUsers($broadcast_id)
    {
        $database = DatabaseFactory::getFactory()->getConnection();
        
        $sql = "SELECT user_id from users";
        $query = $database->prepare($sql);
        $query->execute();
        
        $rows = $query->fetchAll();
        
        foreach($rows as $row)
        //foreach($this->query as $key => $value)
        {
            $sql2 = "DELETE FROM broadcastmarkread WHERE broadcast_id = :broadcast_id AND user_id = :user_id"; 
            $query2 = $database->prepare($sql2);
            $query2->execute(array(':broadcast_id' => $broadcast_id, ':user_id' => $row->user_id ));
            
            //if ($query->rowCount() == 1) {
            //    return true;
            //}
        }
    }
}
