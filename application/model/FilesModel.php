<?php

class FilesModel {
	private $data = [];

	function __construct($area,$key) {
        $this->data = [];
        $rootpath = Config::get("PATH_ATTACHMENTS");
        $fpath =  "{$rootpath}{$area}/{$key}/";
        if (!file_exists($fpath)) return $this->data;
        $files = array_diff(scandir($fpath),['..','.']);
        foreach ($files as $entry) {
            $file = [
            	"path" => "/files/{$area}/{$key}/",
                "name" => str_replace(" ", "%20", $entry),
                "label" => preg_replace("/\.[^.]+$/", "", str_replace("_", " ", $entry)),
                "mime" => mime_content_type($fpath.$entry),
                "size" => Text::byteConvert(filesize($fpath.$entry)),
                "modified" => date ("M d Y H:i:s.",filemtime($fpath.$entry))
            ];
            if (strpos($file["mime"],"image/")!==false) {
                $file["thumb"] = "/content/image/" . Text::base64_urlencode("/files/{$area}/{$key}/{$entry}"). "/100";
                $gis = getimagesize($fpath.$entry);
                $file["info"] = $gis[0] . 'x' . $gis[1];
            }
            $this->data[] = $file;
        }
        return $this;
	}

	public function get_model() {
		return $this->data;
	}
}