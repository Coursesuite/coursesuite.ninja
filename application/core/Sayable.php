<?php

Class Sayable {
	// the different components of words
	private $phonetics = array(
		"Affricate" => array("ch","dg","j"),
		"Alveolar" => array("d","l","n","r","s","t","z"),
		"Bilabial" => array("b","m","p"),
		"BilabialStop" => array("b","p"),
		"Complex" => array("sch","thr"),
		"Consonant" => array("b","c","d","f","g","h","j","k","l","m","n","p","r","s","t","v","z"),
		"DoubleConsonant" => array("bb","dd","ff","ll","mm","nn","rr","ss","ss","tt"),
		"DoubleGlide" => array("wy"),
		"EndingNasal" => array("g","ng","r"),
		"Fricative" => array("ch","f","ph","s","sh","th","v","z"),
		"Glide" => array("w","y"),
		"Glottal" => array("h"),
		"Liquid" => array("l","r"),
		"MidConsonant" => array("ct","dr","dw","ft","mk","nd","ndr","nf","nj","nk","ns","nt","ny","pr","ps","rd","rg","rk","rm","rn","rz","sl","st","stl","wl"),
		"Nasal" => array("m","n"),
		"Palatal" => array("ch","dg","s","j","sh","y"),
		"Stop" => array("b","d","g","k","p","t"),
		"Velar" => array("g","k","ng","w"),
		"Dipthong" => array("ay","ea","ee","ei","oa","oe","ou","ow","oy","y"),
		"Vowel" => array("a","e","i","o","u"),
		"DoubleVowel" => array("ae","au","ea","ey","ie","io","oe","oi","oo","ou","ue","ui","ya","yo"),
		"Hyphen" => array("-")
	);
	// the combinations of letters that make up sayable portions of words
	private $fluency = array(
		array("Vowel", "Consonant", "Vowel"),
		array("Velar", "DoubleVowel"),
		array("Vowel", "MidConsonant", "Vowel"),
		array("Fricative", "DoubleVowel", "Nasal"),
		array("Fricative", "DoubleVowel", "Consonant"),
		array("Vowel", "DoubleConsonant", "Vowel"),
		array("Vowel", "MidConsonant", "DoubleVowel"),
		array("Consonant", "Vowel", "Consonant", "Vowel", "Consonant"),
		array("Nasal", "Vowel", "Fricative", "Vowel", "EndingNasal"),
		array("Fricative", "Vowel", "DoubleGlide", "Vowel", "EndingNasal"),
		array("Fricative", "Vowel", "DoubleGlide", "DoubleVowel", "EndingNasal"),
		array("Nasal", "Dipthong", "Fricative", "Vowel", "EndingNasal"),
		array("Vowel", "MidConsonant", "Vowel", "Fricative", "DoubleVowel", "Nasal"),
		array("Fricative", "DoubleVowel", "Consonant", "Vowel", "DoubleConsonant", "Vowel"),
		array("Nasal", "DoubleVowel", "Fricative", "Dipthong", "EndingNasal"),
		array("Consonant", "Dipthong", "Alveolar", "Vowel", "MidConsonant"),
		array("Consonant", "Vowel", "Stop", "Hyphen", "Stop", "Dipthong", "Complex", "Dipthong", "Velar")
	);
	// words you don't want appearing in your passwords
	private $banwords = array("shit","arse","fuck","cunt","penis","vagina","anus","twat","cock");
	private $length;
	// remove banned words
	private function remove_ban_words($input) {
		return str_replace($this->banwords, "", $input);
	}
	// pick a proper random item from the array
	private function pick_random_item($arr) {
		if (count($arr) === 0) return "";
		return $arr[mt_rand(0, count($arr) - 1)];
	}

	public function generate() {
		$result = [];
		$curr = 0;
		$attempts = 0;
		while ($curr < $this->length && $attempts < $this->length) {
			$fluency_row = $this->pick_random_item($this->fluency);
			$letters = [];
			foreach ($fluency_row as $phonetic_name) {
				$letters[] = $this->pick_random_item($this->phonetics[$phonetic_name]);
			}
			$result[] = $this->remove_ban_words(implode("", $letters));
			$curr = strlen(implode("", $result));
			$attempts = $attempts + 1;
		}
		$result = (string) substr(implode("", $result),0,$this->length);
		return $result;
	}
	public function __toString() {
		return $this->generate();
	}

	public function __construct($size = 8) {
		$this->length = $size;
	}
}