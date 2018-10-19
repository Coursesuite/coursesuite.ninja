<?php
// ini_set('open_basedir', dirname(__FILE__) . DIRECTORY_SEPARATOR);
ini_set('open_basedir', realpath("/img/") . DIRECTORY_SEPARATOR);

class fs
{
	protected $base = null;

	protected function real($path) {
		$temp = realpath($path);
		if(!$temp) { throw new Exception('Path does not exist: ' . $path); }
		if($this->base && strlen($this->base)) {
			if(strpos($temp, $this->base) !== 0) { throw new Exception('Path is not inside base ('.$this->base.'): ' . $temp); }
		}
		return $temp;
	}
	protected function path($id) {
		$id = str_replace('/', DIRECTORY_SEPARATOR, $id);
		$id = trim($id, DIRECTORY_SEPARATOR);
		$id = $this->real($this->base . DIRECTORY_SEPARATOR . $id);
		return $id;
	}
	protected function id($path) {
		$path = $this->real($path);
		$path = substr($path, strlen($this->base));
		$path = str_replace(DIRECTORY_SEPARATOR, '/', $path);
		$path = trim($path, '/');
		return strlen($path) ? $path : '/';
	}

	public function __construct($base) {
		$this->base = $this->real($base);
		if(!$this->base) { throw new Exception('Base directory does not exist'); }
	}
	public function lst($id, $with_root = false) {
		$dir = $this->path($id);
		$lst = @scandir($dir);
		if(!$lst) { throw new Exception('Could not list path: ' . $dir); }
		$res = array();
		foreach($lst as $item) {
			if($item == '.' || $item == '..' || $item === null) { continue; }
			$tmp = preg_match('([^ a-zа-я-_0-9.]+)ui', $item);
			if($tmp === false || $tmp === 1) { continue; }
			if(is_dir($dir . DIRECTORY_SEPARATOR . $item)) {
				$res[] = array('text' => $item, 'children' => true,  'id' => $this->id($dir . DIRECTORY_SEPARATOR . $item), 'icon' => 'folder');
			}
			else {
				$res[] = array('text' => $item, 'children' => false, 'id' => $this->id($dir . DIRECTORY_SEPARATOR . $item), 'type' => 'file', 'icon' => 'file file-'.substr($item, strrpos($item,'.') + 1));
			}
		}
		if($with_root && $this->id($dir) === '/') {
			$res = array(array('text' => basename($this->base), 'children' => $res, 'id' => '/', 'icon'=>'folder', 'state' => array('opened' => true, 'disabled' => true)));
		}
		return $res;
	}
	public function data($id) {
		if(strpos($id, ":")) {
			$id = array_map(array($this, 'id'), explode(':', $id));
			return array('type'=>'multiple', 'content'=> 'Multiple selected: ' . implode(' ', $id));
		}
		$dir = $this->path($id);
		if(is_dir($dir)) {
			if ($id === "img/thumb") {
				return array('type'=>'special', 'content'=> 'This is a special folder. Leave it alone.<br><b>Do not choose files from this folder.</b>');
			}
            // $files = array_diff(scandir($dir),['..','.']);
            $allowedExtensions = ['jpg','jpeg','png','gif','svg'];
            $iterator = new DirectoryIterator($dir);
            foreach ($iterator as $fileinfo) {
           	    if (!$fileinfo->isFile() || !in_array($fileinfo->getExtension(), $allowedExtensions, true)) continue;
           	    $details = $fileinfo->getFilename();
           	    if ($fileinfo->getExtension() === "svg") {
            		$json[] = array(
            			"src"=>"/{$id}/{$fileinfo->getFilename()}",
            			"details"=>$details
            		);
           	    } else {
            		$json[] = array(
            			"src"=>"/content/image/" . Text::base64_urlencode("/{$id}/{$fileinfo->getFilename()}"). "/100",
            			"details"=>$details
					);
            	}
            }
			return array('type'=>'folder', 'content'=> $json, 'id'=>$id); // $id
		}
		if(is_file($dir)) {
			$ext = strpos($dir, '.') !== FALSE ? substr($dir, strrpos($dir, '.') + 1) : '';
			$dat = array('type' => $ext, 'content' => '');
			switch($ext) {
				case 'txt':
				case 'text':
				case 'md':
				case 'js':
				case 'json':
				case 'vtt':
				case 'css':
				case 'html':
				case 'htm':
				case 'xml':
				case 'c':
				case 'cpp':
				case 'h':
				case 'sql':
				case 'log':
				case 'py':
				case 'rb':
				case 'htaccess':
				case 'php':
					$dat['content'] = file_get_contents($dir);
					break;
				case 'jpg':
				case 'jpeg':
				case 'gif':
				case 'png':
				case 'bmp':
					$dat['content'] = 'data:'.finfo_file(finfo_open(FILEINFO_MIME_TYPE), $dir).';base64,'.base64_encode(file_get_contents($dir));
					break;
				default:
					$dat['content'] = '<h3>No preview available</h3><p>'.$this->id($dir) . '<br><code>' . finfo_file(finfo_open(FILEINFO_MIME_TYPE), $dir) . '</code></p>';
					// $dat['content'] = 'data:'.finfo_file(finfo_open(FILEINFO_MIME_TYPE), $dir).';base64,'.base64_encode(file_get_contents($dir));
					break;
			}
			return $dat;
		}
		throw new Exception('Not a valid selection: ' . $dir);
	}
	public function create($id, $name, $mkdir = false) {
		$dir = $this->path($id);
		if(preg_match('([^ a-zа-я-_0-9.]+)ui', $name) || !strlen($name)) {
			throw new Exception('Invalid name: ' . $name);
		}
		if($mkdir) {
			mkdir($dir . DIRECTORY_SEPARATOR . $name);
		}
		else {
			file_put_contents($dir . DIRECTORY_SEPARATOR . $name, '');
		}
		return array('id' => $this->id($dir . DIRECTORY_SEPARATOR . $name));
	}
	public function rename($id, $name) {
		$dir = $this->path($id);
		if($dir === $this->base) {
			throw new Exception('Cannot rename root');
		}
		if(preg_match('([^ a-zа-я-_0-9.]+)ui', $name) || !strlen($name)) {
			throw new Exception('Invalid name: ' . $name);
		}
		$new = explode(DIRECTORY_SEPARATOR, $dir);
		array_pop($new);
		array_push($new, $name);
		$new = implode(DIRECTORY_SEPARATOR, $new);
		if($dir !== $new) {
			if(is_file($new) || is_dir($new)) { throw new Exception('Path already exists: ' . $new); }
			rename($dir, $new);
		}
		return array('id' => $this->id($new));
	}
	public function remove($id) {
		$dir = $this->path($id);
		if($dir === $this->base) {
			throw new Exception('Cannot remove root');
		}
		if(is_dir($dir)) {
			foreach(array_diff(scandir($dir), array(".", "..")) as $f) {
				$this->remove($this->id($dir . DIRECTORY_SEPARATOR . $f));
			}
			rmdir($dir);
		}
		if(is_file($dir)) {
			unlink($dir);
		}
		return array('status' => 'OK');
	}
	public function move($id, $par) {
		$dir = $this->path($id);
		$par = $this->path($par);
		$new = explode(DIRECTORY_SEPARATOR, $dir);
		$new = array_pop($new);
		$new = $par . DIRECTORY_SEPARATOR . $new;
		rename($dir, $new);
		return array('id' => $this->id($new));
	}
	public function copy($id, $par) {
		$dir = $this->path($id);
		$par = $this->path($par);
		$new = explode(DIRECTORY_SEPARATOR, $dir);
		$new = array_pop($new);
		$new = $par . DIRECTORY_SEPARATOR . $new;
		if(is_file($new) || is_dir($new)) { throw new Exception('Path already exists: ' . $new); }

		if(is_dir($dir)) {
			mkdir($new);
			foreach(array_diff(scandir($dir), array(".", "..")) as $f) {
				$this->copy($this->id($dir . DIRECTORY_SEPARATOR . $f), $this->id($new));
			}
		}
		if(is_file($dir)) {
			copy($dir, $new);
		}
		return array('id' => $this->id($new));
	}
}

if(isset($_GET['operation'])) {
	$fs = new fs(realpath("/img/"));
//	$fs = new fs(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'root' . DIRECTORY_SEPARATOR);
	try {
		$rslt = null;
		switch($_GET['operation']) {
			case 'get_node':
				$node = isset($_GET['id']) && $_GET['id'] !== '#' ? $_GET['id'] : '/';
				$rslt = $fs->lst($node, (isset($_GET['id']) && $_GET['id'] === '#'));
				break;
			case "get_content":
				$node = isset($_GET['id']) && $_GET['id'] !== '#' ? $_GET['id'] : '/';
				$rslt = $fs->data($node);
				break;
			case 'create_node':
				$node = isset($_GET['id']) && $_GET['id'] !== '#' ? $_GET['id'] : '/';
				$rslt = $fs->create($node, isset($_GET['text']) ? $_GET['text'] : '', (!isset($_GET['type']) || $_GET['type'] !== 'file'));
				break;
			case 'rename_node':
				$node = isset($_GET['id']) && $_GET['id'] !== '#' ? $_GET['id'] : '/';
				$rslt = $fs->rename($node, isset($_GET['text']) ? $_GET['text'] : '');
				break;
			case 'delete_node':
				$node = isset($_GET['id']) && $_GET['id'] !== '#' ? $_GET['id'] : '/';
				$rslt = $fs->remove($node);
				break;
			case 'move_node':
				$node = isset($_GET['id']) && $_GET['id'] !== '#' ? $_GET['id'] : '/';
				$parn = isset($_GET['parent']) && $_GET['parent'] !== '#' ? $_GET['parent'] : '/';
				$rslt = $fs->move($node, $parn);
				break;
			case 'copy_node':
				$node = isset($_GET['id']) && $_GET['id'] !== '#' ? $_GET['id'] : '/';
				$parn = isset($_GET['parent']) && $_GET['parent'] !== '#' ? $_GET['parent'] : '/';
				$rslt = $fs->copy($node, $parn);
				break;
			default:
				throw new Exception('Unsupported operation: ' . $_GET['operation']);
				break;
		}
		header('Content-Type: application/json; charset=utf-8');
		echo json_encode($rslt);
	}
	catch (Exception $e) {
		header($_SERVER["SERVER_PROTOCOL"] . ' 500 Server Error');
		header('Status:  500 Server Error');
		echo $e->getMessage();
	}
	die();
}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<title>CourseSuite 🌲 Browser</title>
		<meta name="viewport" content="width=device-width" />
		<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/jstree/3.3.3/themes/default/style.min.css" />
		<style>
		html,body{min-height:100vh}
		html {
			font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
		    font-size: 16px;
		    font-weight: normal;
		    line-height: 1.5;
		}
		*,*::before;*::after{
		  -webkit-box-sizing: border-box;
		     -moz-box-sizing: border-box;
		          box-sizing: border-box;
		}
		.split {
		  overflow-y: auto;
		  overflow-x: hidden;
		}
		body{
			margin:0; background:white;
		}
		#tree, #data {
			overflow:auto;
		}
		#container { display: flex; flex-direction: row; height: 100vh; }

		#tree .folder { background:url('/css/file_sprite.png') right bottom no-repeat; }
		#tree .file { background:url('/css/file_sprite.png') 0 0 no-repeat; }
		#tree .file-pdf { background-position: -32px 0 }
		#tree .file-as { background-position: -36px 0 }
		#tree .file-c { background-position: -72px -0px }
		#tree .file-iso { background-position: -108px -0px }
		#tree .file-htm, #tree .file-html, #tree .file-xml, #tree .file-xsl { background-position: -126px -0px }
		#tree .file-cf { background-position: -162px -0px }
		#tree .file-cpp { background-position: -216px -0px }
		#tree .file-cs { background-position: -236px -0px }
		#tree .file-sql { background-position: -272px -0px }
		#tree .file-xls, #tree .file-xlsx { background-position: -362px -0px }
		#tree .file-h { background-position: -488px -0px }
		#tree .file-crt, #tree .file-pem, #tree .file-cer { background-position: -452px -18px }
		#tree .file-php { background-position: -108px -18px }
		#tree .file-jpg, #tree .file-jpeg, #tree .file-png, #tree .file-gif, #tree .file-bmp { background-position: -126px -18px }
		#tree .file-ppt, #tree .file-pptx { background-position: -144px -18px }
		#tree .file-rb { background-position: -180px -18px }
		#tree .file-text, #tree .file-txt, #tree .file-md, #tree .file-log, #tree .file-htaccess { background-position: -254px -18px }
		#tree .file-doc, #tree .file-docx { background-position: -362px -18px }
		#tree .file-zip, #tree .file-gz, #tree .file-tar, #tree .file-rar { background-position: -416px -18px }
		#tree .file-js { background-position: -434px -18px }
		#tree .file-css { background-position: -144px -0px }
		#tree .file-fla { background-position: -398px -0px }

		#preview {
			display: flex;
			flex-direction: column;
		}
		#selection {
			padding: 1em;
			text-align: center;
		}
		#selection button {
			padding: 0 20px;
			min-width: 200px;
			border: 1px solid #999;
			color: #999;
			background-color: #eee;
			border-radius: 26px;
			line-height: 52px;
		}

		#data {
			flex: 1;
			position: relative;
		}
		#data img, figure img  {
			max-width: 100%;
		}
		#data.code {
			position: relative;
		}
		#data textarea {
			position: absolute;
			top: 0; left: 0;
			width: 100%; bottom: 0;
			font-size: inherit;
			font-family: monospace;
		}
		.content.image {
		}
		.content.image img {
			max-width: 100%;
		}
		.content {
			display: none;
		}
		.content.default {
			position: absolute;
			top: 50%; left: 50%;
			transform: translate(-50%, -50%);
		}
		.content.code {
			width: 90%;
			height: 90vh;
		}

		.gutter {
		    background-color: #eee;

		    background-repeat: no-repeat;
		    background-position: 50%;
		}
		.gutter.gutter-vertical {
		    cursor: ns-resize;
		    background-image:  url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAB4AAAAFAQMAAABo7865AAAABlBMVEVHcEzMzMzyAv2sAAAAAXRSTlMAQObYZgAAABBJREFUeF5jOAMEEAIEEFwAn3kMwcB6I2AAAAAASUVORK5CYII=')
		}

		.gutter.gutter-horizontal {
		    cursor: ew-resize;
		    background-image:  url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAUAAAAeCAYAAADkftS9AAAAIklEQVQoU2M4c+bMfxAGAgYYmwGrIIiDjrELjpo5aiZeMwF+yNnOs5KSvgAAAABJRU5ErkJggg==')
		}
		.content.grid {
			display: grid;
			grid-template-columns: repeat(5, 1fr);
			grid-gap: 1em;
		}
		figure {
			background-color: #eee;
			margin: 0; padding:  0;
		}
		figure > img {
			width: 100%;
		}
		figure > figcaption {
			text-align: center;
			width: 100%;
			background-color: rgba(0,0,0,.5);
			color: white;
			padding: 10px 0;
		}

		</style>
	</head>
	<body>
		<div id="container" role="main">
			<div id="tree"></div>
			<div id="preview">
				<div id="data">
					<div class="content code"><textarea id="code" readonly="readonly"></textarea></div>
					<div class="content folder"></div>
					<div class="content image"><img src="" alt="" /></div>
					<div class="content grid"></div>
					<div class="content default">Select a file from the tree.</div>
				</div>
				<div id="selection"><button onclick="selectFile()">Select ...</button></div>
			</div>
		</div>

		<script src="//ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
		<script src="//cdnjs.cloudflare.com/ajax/libs/jstree/3.3.3/jstree.min.js"></script>
		<script src="https://unpkg.com/split.js/split.min.js"></script>
		<script>
		function selectFile() {
			if (opener !== self) {
				var curr = "/" + document.querySelector("#tree").getAttribute("aria-activedescendant"),
					low = curr.toLowerCase();
				if (low.endsWith(".jpg")||low.endsWith(".jpeg")||low.endsWith(".png")||low.endsWith(".gif")||low.endsWith(".webp")||low.endsWith(".svg")) {
					curr = "![image description](" + curr + ")";
				} else if (low.endsWith(".zip")||low.endsWith(".tgz")||low.endsWith(".pdf")) {
					curr = "[file link text](" + curr + ")";
				}
				opener.replace_in_editor(curr);
			}
		}
		$(function () {
			var sizes = localStorage.getItem('splitjs')

			if (sizes) {
			    sizes = JSON.parse(sizes)
			} else {
			    sizes = [25, 75]  // default sizes
			}

			var split = Split(['#tree', '#preview'], {
			    sizes: sizes,
			    minSize: 200,
			    elementStyle: function (dimension, size, gutterSize) {
			        return {
			            'flex-basis': 'calc(' + size + '% - ' + gutterSize + 'px)'
			        }
			    },
			    gutterStyle: function (dimension, gutterSize) {
			        return {
			            'flex-basis':  gutterSize + 'px'
			        }
			    },
			    onDragEnd: function () {
			        localStorage.setItem('splitjs', JSON.stringify(split.getSizes()));
			    }
			});



			$('#tree')
				.jstree({
					'core' : {
						'data' : {
							'url' : '?operation=get_node',
							'data' : function (node) {
								return { 'id' : node.id };
							}
						},
						'check_callback' : function(o, n, p, i, m) {
							if(m && m.dnd && m.pos !== 'i') { return false; }
							if(o === "move_node" || o === "copy_node") {
								if(this.get_node(n).parent === this.get_node(p).id) { return false; }
							}
							return true;
						},
						'force_text' : true,
						'themes' : {
							'responsive' : false,
							'variant' : 'small',
							'stripes' : true
						}
					},
					'sort' : function(a, b) {
						return this.get_type(a) === this.get_type(b) ? (this.get_text(a) > this.get_text(b) ? 1 : -1) : (this.get_type(a) >= this.get_type(b) ? 1 : -1);
					},
					'contextmenu' : {
						'items' : function(node) {
							var tmp = $.jstree.defaults.contextmenu.items();
							delete tmp.create.action;
							tmp.create.label = "New";
							tmp.create.submenu = {
								"create_folder" : {
									"separator_after"	: true,
									"label"				: "Folder",
									"action"			: function (data) {
										var inst = $.jstree.reference(data.reference),
											obj = inst.get_node(data.reference);
										inst.create_node(obj, { type : "default" }, "last", function (new_node) {
											setTimeout(function () { inst.edit(new_node); },0);
										});
									}
								},
								"create_file" : {
									"label"				: "File",
									"action"			: function (data) {
										var inst = $.jstree.reference(data.reference),
											obj = inst.get_node(data.reference);
										inst.create_node(obj, { type : "file" }, "last", function (new_node) {
											setTimeout(function () { inst.edit(new_node); },0);
										});
									}
								}
							};
							if(this.get_type(node) === "file") {
								delete tmp.create;
							}
							return tmp;
						}
					},
					'types' : {
						'default' : { 'icon' : 'folder' },
						'file' : { 'valid_children' : [], 'icon' : 'file' }
					},
					'unique' : {
						'duplicate' : function (name, counter) {
							return name + ' ' + counter;
						}
					},
					'plugins' : ['state','dnd','sort','types','contextmenu','unique']
				})
				.on('delete_node.jstree', function (e, data) {
					$.get('?operation=delete_node', { 'id' : data.node.id })
						.fail(function () {
							data.instance.refresh();
						});
				})
				.on('create_node.jstree', function (e, data) {
					$.get('?operation=create_node', { 'type' : data.node.type, 'id' : data.node.parent, 'text' : data.node.text })
						.done(function (d) {
							data.instance.set_id(data.node, d.id);
						})
						.fail(function () {
							data.instance.refresh();
						});
				})
				.on('rename_node.jstree', function (e, data) {
					$.get('?operation=rename_node', { 'id' : data.node.id, 'text' : data.text })
						.done(function (d) {
							data.instance.set_id(data.node, d.id);
						})
						.fail(function () {
							data.instance.refresh();
						});
				})
				.on('move_node.jstree', function (e, data) {
					$.get('?operation=move_node', { 'id' : data.node.id, 'parent' : data.parent })
						.done(function (d) {
							//data.instance.load_node(data.parent);
							data.instance.refresh();
						})
						.fail(function () {
							data.instance.refresh();
						});
				})
				.on('copy_node.jstree', function (e, data) {
					$.get('?operation=copy_node', { 'id' : data.original.id, 'parent' : data.parent })
						.done(function (d) {
							//data.instance.load_node(data.parent);
							data.instance.refresh();
						})
						.fail(function () {
							data.instance.refresh();
						});
				})
				.on('changed.jstree', function (e, data) {
					if(data && data.selected && data.selected.length) {
						$.get('?operation=get_content&id=' + data.selected.join(':'), function (d) {
							if(d && typeof d.type !== 'undefined') {
								$('#data .content').hide();
								switch(d.type) {
									case 'text':
									case 'vtt':
									case 'txt':
									case 'md':
									case 'htaccess':
									case 'log':
									case 'sql':
									case 'php':
									case 'js':
									case 'json':
									case 'css':
									case 'html':
										$('#data .code').show();
										$('#code').val(d.content);
										break;
									case 'png':
									case 'jpg':
									case 'jpeg':
									case 'bmp':
									case 'gif':
										$('#data .image img').attr('src',d.content);
										$('#data .image').show();
										break;
									case 'folder':
										var html = [];
										[].forEach.call(d.content, function (i) {
											html.push("<figure><figcaption>" + i.details + "</figcaption><img src='" + i.src + "'></figure>");
										});
										$('#data .grid').html(html.join("")).show();

									default:
										$('#data .default').html(d.content).show();
										break;
								}
							}
						});
					}
					else {
						$('#data .content').hide();
						$('#data .default').html('Select a file from the tree.').show();
					}
				});
		});
		</script>
	</body>
</html>