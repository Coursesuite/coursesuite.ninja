<?php
	
class Text
{
    private static $texts;

    public static function get($key, $data=NULL)
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
            self::$texts = require('../application/config/texts.php');
        }

	    // check if array key exists
	    if (!array_key_exists($key, self::$texts)) {
		    return null;
	    }

        return self::$texts[$key];
    }

    public static function output($ar, $key, $encode = FALSE) {
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
    
    public static function toHtml($string) {
	    $PDE = new ParsedownExtra();
	    return $PDE->text($string);
    }

	public static function base64enc($val) {
        return strtr(base64_encode($val), '+/=', '-_,');
    }
    
    public static function base64dec($val) {
        return base64_decode(strtr($val, '-_,', '+/='));
    }
    
    public static function StaticPageRenderer($route) {
	    $page = StaticPageModel::getRecordByKey($route);
	    if (isset($page)) {
		    $PDE = new ParsedownExtra();
		    return $PDE->text($page->content);
	    }
	    return "";
    }

}
