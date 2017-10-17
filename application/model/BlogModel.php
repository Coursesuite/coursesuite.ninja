<?php

class BlogModel extends Model
{
	CONST TABLE_NAME = "blogentries";
	CONST ID_ROW_NAME = "entry_id";

	protected $data_model;

	public function get_model()
	{
		return (array) $this->data_model;
	}

	public function set_model($data)
	{
		$this->data_model = $data;
	}

	public function __construct($entry_id = 0, $page_index = 0)
	{
		parent::__construct();
		if ($entry_id > 0) {
			self::load($entry_id);
		} else {
			self::load_summary($page_index);
		}
		return $this;
	}

	public function delete($id = 0)
	{
		if ($id > 0) {
			parent::Destroy(self::TABLE_NAME, self::ID_ROW_NAME . "=:id", array(":id" => $id));
		} else {
			$idname = self::ID_ROW_NAME;
			parent::Destroy(self::TABLE_NAME, self::ID_ROW_NAME . "=:id", array(":id" => $data_model->$idname));
		}
	}

	public function load($id)
	{
		$this->data_model = parent::Read(self::TABLE_NAME, self::ID_ROW_NAME . "=:id", array(":id" => $id))[0]; // 0th of a fetchall
		return $this;
	}

	public function load_summary($page, $pagesize = 10)
	{
		$start = ($page * $pagesize);
		$database = DatabaseFactory::getFactory()->getConnection();
		$sql = "SELECT * FROM " . self::TABLE_NAME . " WHERE 1=1";
        if (!Session::userIsAdmin()) {
			$sql .= " AND published=1";
		}
		$sql .= " ORDER BY entry_date DESC LIMIT :page, :pagesize";
		$query = $database->prepare($sql);
		$query->bindParam(':page', $start, PDO::PARAM_INT);
		$query->bindParam(':pagesize', $pagesize, PDO::PARAM_INT);
		$query->execute();
		$this->data_model = new stdClass();
		$this->data_model->Entries = $query->fetchAll();
		return $this;
	}

	public function make()
	{
		$this->data_model = parent::Create(self::TABLE_NAME);
		return $this;
	}

	public function save()
	{
		return parent::Update(self::TABLE_NAME, self::ID_ROW_NAME, $this->data_model);
	}

	public static function entry_count() {
		$database = DatabaseFactory::getFactory()->getConnection();
		$sql = "SELECT count(1) FROM " . self::TABLE_NAME;
        if (!Session::userIsAdmin()) {
			$sql .= " WHERE published=1";
		}
		$query = $database->prepare($sql);
		$query->execute();
		return (int) $query->fetchColumn(0);
	}

	// RECENT means "anything new this week"
	public static function recent_entry_count() {
		$database = DatabaseFactory::getFactory()->getConnection();
		$sql = "SELECT count(1) FROM " . self::TABLE_NAME;
		$sql .= " WHERE published=1 AND entry_date > NOW() - INTERVAL 1 WEEK";
		$query = $database->prepare($sql);
		$query->execute();
		return (int) $query->fetchColumn(0);
	}

}