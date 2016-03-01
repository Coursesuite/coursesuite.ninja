<?php

/**
 * NewsModel
 * This is basically a simple CRUD (Create/Read/Update/Delete) demonstration.
 */
class NewsModel
{
    /**
     * Get all news (news are just example data that the user has created)
     * @return array an array with several objects (the results)
     */
    public static function getAllNews()
    {
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT user_id, news_id, news_text, news_title FROM news WHERE user_id = :user_id";
        $query = $database->prepare($sql);
        $query->execute(array(':user_id' => Session::get('user_id')));

        // fetchAll() is the PDO method that gets all result rows
        return $query->fetchAll();
    }

    /**
     * Get a single news
     * @param int $news_id id of the specific news
     * @return object a single object (the result)
     */
    public static function getNews($news_id)
    {
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT user_id, news_id, news_text, news_title FROM news WHERE user_id = :user_id AND news_id = :news_id LIMIT 1";
        $query = $database->prepare($sql);
        $query->execute(array(':user_id' => Session::get('user_id'), ':news_id' => $news_id));

        // fetch() is the PDO method that gets a single result
        return $query->fetch();
    }

    /**
     * Set a news (create a new one)
     * @param string $news_text news text that will be created
     * @return bool feedback (was the news created properly ?)
     */
    public static function createNews($news_title, $news_text)
    {
        if (!$news_text || strlen($news_text) == 0) {
            Session::add('feedback_negative', Text::get('FEEDBACK_NOTE_CREATION_FAILED'));
            return false;
        }

        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "INSERT INTO news (news_text, news_title, user_id) VALUES (:news_text, :news_title, :user_id)";
        $query = $database->prepare($sql);
        $query->execute(array(':news_text' => $news_text, ':news_title' => $news_title, ':user_id' => Session::get('user_id')));

        if ($query->rowCount() == 1) {
            return true;
        }

        // default return
        Session::add('feedback_negative', Text::get('FEEDBACK_NOTE_CREATION_FAILED'));
        return false;
    }

    /**
     * Update an existing news
     * @param int $news_id id of the specific news
     * @param string $news_text new text of the specific news
     * @return bool feedback (was the update successful ?)
     */
    public static function updateNews($news_id, $news_text, $news_title)
    {
        if (!$news_id || !$news_text || !$news_title) {
            return false;
        }

        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "UPDATE news SET news_text = :news_text, news_title = :news_title WHERE news_id = :news_id AND user_id = :user_id LIMIT 1";
        $query = $database->prepare($sql);
        $query->execute(array(':news_id' => $news_id, ':news_text' => $news_text, ':news_title' => $news_title, ':user_id' => Session::get('user_id')));

        if ($query->rowCount() == 1) {
            return true;
        }

        Session::add('feedback_negative', Text::get('FEEDBACK_NOTE_EDITING_FAILED'));
        return false;
    }

    /**
     * Delete a specific news
     * @param int $news_id id of the news
     * @return bool feedback (was the news deleted properly ?)
     */
    public static function deleteNews($news_id)
    {
        if (!$news_id) {
            return false;
        }

        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "DELETE FROM news WHERE news_id = :news_id AND user_id = :user_id LIMIT 1";
        $query = $database->prepare($sql);
        $query->execute(array(':news_id' => $news_id, ':user_id' => Session::get('user_id')));

        if ($query->rowCount() == 1) {
            return true;
        }

        // default return
        Session::add('feedback_negative', Text::get('FEEDBACK_NOTE_DELETION_FAILED'));
        return false;
    }
}
