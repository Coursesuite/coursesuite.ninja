<?php

use LightnCandy\LightnCandy;

class Text
{
	private static $texts;

	public static function get($key, $data = null)
	{
		// if not $key
		if (!$key) {
			return null;
		}
		if ($data) {
			foreach ($data as $var => $value) {
				${$var} = $value;
			}
		}
		// load config file (this is only done once per application lifecycle)
		if (!self::$texts) {
			self::$texts = require '../application/config/texts.php';
		}

		// check if array key exists
		if (!array_key_exists($key, self::$texts)) {
			return null;
		}

		return self::$texts[$key];
	}

	public static function sprintf($key, $args) {
		if (!$key) return null;
		if (!self::$texts) {
			self::$texts = require '../application/config/texts.php';
		}
		if (!array_key_exists($key, self::$texts)) {
			return null;
		}
		return sprintf(self::$texts[key], $args);
	}

	public static function output($ar, $key, $encode = false)
	{
		if (is_array($ar)) {
			if (array_key_exists($key, $ar)) {
				$outp = $ar[$key];
				if (isset($outp) && !empty($outp)) {
					if ($encode) {
						$outp = htmlentities($outp, ENT_QUOTES, 'UTF-8');
					}
					echo $outp;
				}
			}
		}
	}

	public static function formatString($key, $data)
	{
		if (!$key) {
			return null;
		}
		if (!self::$texts) {
			self::$texts = require '../application/config/texts.php';
		}
		if (!array_key_exists($key, self::$texts)) {
			return null;
		}
		$string = self::$texts[$key];
		$needles = array_map(function($value) {
			return '{{' . $value . '}}';
		}, array_keys($data));
		$values = array_values($data);
		$out = str_replace($needles, $values, $string);
		return $out;
	}

	public static function toHtml($string)
	{

		// https://github.com/Daniel-KM/parsedown-extra
		// was downloaded to the core folder, since it's not yet in composer
		// and modified to fix the attribute quote bug
		// then
		// https://github.com/pagerange/metaparsedown
		// was required in composer
		// but erusrv/parsedown was manually deleted
		//  long term need to replace both with one fixed version that is in composer

		$PDE = new ParsedownExtra();
		return $PDE->text($string);
	}

	public static function toText($string) {
		return strip_tags($string, '<br>');
	}

	public static function compileHtml($template, $json)
	{
		$compiled = LightnCandy::compile($template);
		$content = LightnCandy::prepare($compiled);
		return ($content($json));
	}

	public static function base64enc($val)
	{
		return strtr(base64_encode($val), '+/=', '-_,');
	}

	public static function base64dec($val)
	{
		return base64_decode(strtr($val, '-_,', '+/='));
	}

	public static function StaticPageRenderer($route)
	{
		$page = new StaticPageModel("page_key", $route);
		if (isset($page->content)) {
			$PDE = new ParsedownExtra();
			return $PDE->text($page->content);
		}
		return "";
	}

	public static function formatBytes($size, $precision = 2)
	{
		$base = log($size, 1024);
		$suffixes = array('', 'K', 'M', 'G', 'T');
		return round(pow(1024, $base - floor($base)), $precision) . ' ' . $suffixes[floor($base)];
	}

    public static function byteConvert($bytes)
    {
        if ($bytes == 0)
            return "0.00 B";

        $s = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
        $e = floor(log($bytes, 1024));

        return round($bytes/pow(1024, $e), 2).$s[$e];
    }

	public static function formatTime($seconds)
	{
		return gmdate("H:i:s", $seconds);
	}

	public static function generatePassword()
	{
		return substr(str_replace(["O","o","0","i","j","l","I","L","S","5","1"],"", base64_encode(md5(mt_rand()))),5,10);
	}

	public static function Paginator($pagination, $options) {
		$type = $options["hash"]["type"] ?: 'middle';
		$ret = '';
		$pageSize = $pagination["size"];
		$pageCount = intval(ceil($pagination["total"] / $pageSize)); // round() rounds up, floor() rounds down
		$page = $pagination["page"];
		$limit = null;
		if (isset($options["hash"]["limit"])) {
			$limit = (int) $options["hash"]["limit"];
		}
		$newContext = array();
		switch ($type) {
			case 'middle':
				if (gettype($limit) === 'integer') {
					$i = 0;
					$leftCount = ceil($limit / 2) - 1;
					$rightCount = $limit - $leftCount - 1;
					if ($page + $rightCount > $pageCount) {
						$leftCount = $limit - $pageCount - $page - 1;
					}
					if ($page - $leftCount < 1) {
						$leftCount = $page - 1;
					}
					$start = $page - $leftCount;
					$active = false;
					while ($i < $limit && $i < $pageCount) {
						$newContext = array();
						$newContext['n'] = $start;
						if ($start === $page + 1) {
							$newContext['active'] = true;
							$active = true;
						} else if ($start === $pageCount && !$active) { // i couldn't be bothered debugging this corner
							$newContext['active'] = true;
						}
						$ret .= $options['fn']($newContext);
						$start++;
						$i++;
					}
				} else {
					for ($i = 1; $i <= $pageCount; $i++) {
						$newContext = array();
						$newContext['n'] = $i;
						if ($i === $page + 1) {
							$newContext['active'] = true;
						}
						$ret .= $options['fn']($newContext);
					}
				}
				break;
			case 'previous':
				if ($page + 1 === 1) {
					$newContext['disabled'] = true;
					$newContext['n'] = 1;
				} else {
					$newContext['n'] = $page - 1;
				}
				$ret .= $options['fn']($newContext);
				break;
			case 'next':
				if ($page + 1 === $pageCount) {
					$newContext['disabled'] = true;
					$newContext['n'] = $pageCount - 1;
				} else {
					$newContext['n'] = $page + 1;
				}
				$ret .= $options['fn']($newContext);
				break;
			case 'first':
				$newContext['n'] = 1;
				if ($page + 1 === 1) {
					$newContext['disabled'] = true;
				}
				$ret .= $options['fn']($newContext);
				break;
			case 'last':
				$newContext['n'] = $pageCount - 1;
				if ($page + 1 === $pageCount) {
					$newContext['disabled'] = true;
				}
				$ret .= $options['fn']($newContext);
				break;
		}
		return $ret;
	}

	public static function unescape($string) {
		if (is_null($string)) {
			return null;
		} else {
			return urldecode($string);
		}
	}

	public static function base64_urlencode($input) {
		return strtr(base64_encode($input), '+/=', '-_~');
	}

	public static function base64_urldecode($input) {
		return base64_decode(strtr($input, '-_~', '+/='));
	}

	public static function iif($value, $default) {

		// hmm
		// function get_default($value, FILTER_VALIDATE_INT, 0) {
		// 	return filter_var($value, FILTER_VALIDATE_INT, array('options' => array('default' => $default)));
		// }

		return (empty(trim($value))) ? $default : $value;
	}

}
