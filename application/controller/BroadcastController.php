<?php

/**
 * The note controller: Just an example of simple create, read, update and delete (CRUD) actions.
 */

class BroadcastController extends Controller
{
    /**
     * Construct this object by extending the basic Controller class
     */
    public function __construct()
    {
        parent::__construct();

        // VERY IMPORTANT: All controllers/areas that should only be usable by logged-in users
        // need this line! Otherwise not-logged in users could do actions. If all of your pages should only
        // be usable by logged-in users: Put this line into libs/Controller->__construct
         
    }

    /**
     * This method controls what happens when you move to /note/index in your app.
     * Gets all notes (of the user).
     */
    public function index()
    {
        Auth::checkAdminAuthentication();
        $this->View->render('broadcast/index', array(
            'broadcasts' => BroadcastModel::getAllBroadcast()
        ));
    }
    
    public function broadcast()
    {
        Auth::checkAuthentication();
        $this->View->render('broadcast/broadcast', array(
            'broadcasts' => BroadcastModel::getEveryBroadcast()
        ));
    }

    /**
     * This method controls what happens when you move to /dashboard/create in your app.
     * Creates a new note. This is usually the target of form submit actions.
     * POST request.
     */
    public function create()
    { 
       BroadcastModel::createBroadcast(Request::post('broad_name'), Request::post('broad_desc'));
       Redirect::to('broadcast');
    }
    
    /**
     * This method controls what happens when you move to /note/edit(/XX) in your app.
     * Shows the current content of the note and an editing form.
     * @param $note_id int id of the note
     */
    public function edit($broadcast_id)
    {
        $this->View->render('broadcast/edit', array(
            'broadcast' => BroadcastModel::getBroadcast($broadcast_id)
        ));
    }
    
    /**
     * This method controls what happens when you move to /note/editSave in your app.
     * Edits a note (performs the editing after form submit).
     * POST request.
     */
    
    public function editSave()
    {
        BroadcastModel::updateBroadcast(Request::post('broadcast_id'), Request::post('broad_name'), Request::post('broad_desc'));
        Redirect::to('broadcast');
    }
    
    /**
     * This method controls what happens when you move to /note/delete(/XX) in your app.
     * Deletes a note. In a real application a deletion via GET/URL is not recommended, but for demo purposes it's
     * totally okay.
     * @param int $note_id id of the note
     */
    public function delete($broadcast_id)
    {
        BroadcastModel::deleteBroadcastUsers($broadcast_id);
        BroadcastModel::deleteBroadcast($broadcast_id);
        Redirect::to('broadcast');
    }
    
    public function mark($broadcast_id)
    {
        BroadcastModel::markBroadcast($broadcast_id);
        Redirect::to('broadcast/broadcast');
    }
    
}

