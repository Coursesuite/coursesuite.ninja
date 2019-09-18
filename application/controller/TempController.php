<?php
class TempController extends Controller
{

	public function __construct()
	{
		parent::__construct();
	}

	public function export() {

		$blog = new BlogModel(null,0,100);
		$path = Config::get("PATH_PUBLIC_ROOT") . "/blogentries";
		if (!file_exists($path)) mkdir($path);
		foreach ($blog->get_model()->Entries as $entry) {
			$file = [];
			$file[] = "title: " . $entry->title;
			$file[] = "----";
			$file[] = $entry->short_entry;
			$file[] = "====";
			$file[] = $entry->long_entry;

			$name = preg_replace("/[^0-9]/","-",$entry->entry_date) . ',' . $entry->published . ',' . $entry->slug . '.md';

			file_put_contents($path . "/" . $name, implode(PHP_EOL . PHP_EOL, $file));
			echo "<li>Wrote " . $name;
		}
	}

}