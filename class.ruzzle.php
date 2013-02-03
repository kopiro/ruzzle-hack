<?php

ini_set('memory_limit', '2G');
set_time_limit(-1);

class RuzzleHack
{
	protected $matrix = [];
	protected $matrix_string = [];
	protected $matrix_origins = [];
	protected $matrix_size = 4;
	protected $matrix_info = [];
	protected $is_near = [];
	protected $words = [];
	protected $dict_path = 'ITA.dict';

	public function __construct()
	{
		$this->dict_path = __DIR__."/".$this->dict_path;
	}

	public function init()
	{
		if (!file_exists($this->dict_path.'/check')) die("No dict available");
		$this->is_near = unserialize(file_get_contents(__DIR__."/cache/nearest_{$this->matrix_size}.dat"));
	}

	public function prepare_dict()
	{
		$len_sort = function($a,$b){ return strlen($a)>strlen($b); };

		$new_dict = [];
		$txts = glob($this->dict_path."/*.txt");
		foreach ($txts as $txt)
		{
			foreach (file($txt) as $word)
			{
				$info = $this->calc_word_info($word);
				$new_dict[$info['length']][$info['word']] = $info['chars'];
			}
		}
		ksort($new_dict);
		foreach ($new_dict as $len => $words) {
			uksort($words, $len_sort);
			file_put_contents($this->dict_path."/set_{$len}.dat", serialize($words));
		}
		unset($new_dict);

		file_put_contents($this->dict_path."/check", time());
	}

	public function prepare_nearest_map()
	{
		if (!is_dir(__DIR__."/cache")) mkdir(__DIR__."/cache");
		$is_near = array();
		for($y = 0; $y < $this->matrix_size; $y++ ){
			for($x = 0; $x < $this->matrix_size; $x++ ){
				for($y2 = 0; $y2 < $this->matrix_size; $y2++ ){
					for($x2 = 0; $x2 < $this->matrix_size; $x2++ ){
						$k = implode(',',[$x,$y,$x2,$y2]);
						if((($x!==$x2) || ($y!==$y2)) && abs($x2-$x)<2 && abs($y2-$y)<2)
							$is_near[$k] = true;
					}
				}
			}
		}
		file_put_contents(__DIR__."/cache/nearest_{$this->matrix_size}.dat", serialize($is_near));
	}

	protected function print_path($path)
	{
		foreach ($path as $p)
			echo sprintf("(%d,%d)",$p[0]+1,$p[1]+1);
	}

	protected function check_word($word, $start=0, $path=[])
	{
		static $found;

		if ($start==0) $found = false;
		if ($start==strlen($word)) return true;

		$c = $word[$start];
		if (!isset($c)) return;

		foreach ($this->matrix_origins[$c] as $origin)
		{
			if ($found) return $found;
			if (!in_array($origin, $path))
			{
				array_push($path, $origin);
				if ($this->is_endpath_valid($path))
				{
					if ( true===$this->check_word($word, $start+1, $path) ) $found = $path;
					array_pop($path);
				}
				else
				{
					array_pop($path);
				}
			}
		}

		if ($found) return $found;
		return false;
	}

	protected function calc_word_info($word)
	{
		$word = trim(strtolower($word));
		$info = array();
		$charset = str_split($word);
		asort($charset);
		$charset = array_count_values($charset);
		$info = [
		'word' => $word,
		'length' => count($charset),
		'chars' => $charset
		];
		return $info;
	}

	protected function inmatrix_word($charset)
	{
		foreach ($charset as $char => $count) {
			if ( !array_key_exists($char,$this->matrix_info['chars']) ) return false;
			if ( $count>$this->matrix_info['chars'][$char] ) return false;
		}
		return true;
	}

	public function calculate_words($print=false)
	{
		$this->words = [];
		for ($i=$this->matrix_info['length']; $i>1; $i--)
		{
			$dict_file = $this->dict_path."/set_{$i}.dat";
			if (!file_exists($dict_file)) { continue; }

			$words = unserialize(file_get_contents($dict_file));
			foreach ($words as $word => $charset)
			{
				if ( !$this->inmatrix_word($charset) ) continue;
				$path = $this->check_word($word);
				if ($path===false) continue;
				$word_info = ['word'=>$word, 'path'=>$path];
				$this->words[] = $word_info;
				if ($print===true) $this->print_word($word_info);
			}
		}
	}

	public function order_words()
	{
		usort($this->words, function($a,$b){
			return strlen($a['word'])>strlen($b['word']);
		});
	}

	public function load_set($matrix_string)
	{
		$this->matrix_string = strtolower($matrix_string);
		foreach (str_split($this->matrix_string, $this->matrix_size) as $y => $row) {
			foreach ( str_split($row) as $x => $letter ) {
				if (!isset($letter)) continue;
				$this->matrix[$y][$x] = $letter;
				$this->matrix_origins[$letter][] = array($x,$y);
			}
		}
		$this->matrix_info = $this->calc_word_info($matrix_string);
	}

	protected function is_endpath_valid($path)
	{
		if (count($path)<=1) return true;
		return $this->are_near($path[count($path)-2], $path[count($path)-1]);
	}

	protected function are_near($a, $b)
	{
		$k = implode(',', array($a[0],$a[1],$b[0],$b[1]));
		return isset($this->is_near[$k]);
	}

	public function print_matrix()
	{
		foreach ($this->matrix as $row) {
			foreach ($row as $letter) {
				echo $letter." ";
			}
			echo PHP_EOL;
		}
		echo PHP_EOL;
	}

	protected function print_word($word)
	{
		echo $word['word']." [";
		foreach ($word['path'] as $p) echo sprintf("(%d,%d)",$p[0]+1,$p[1]+1);
		echo "]",PHP_EOL;
	}

	protected function prettyprint_word($word)
	{
		echo chr(27)."[0;1m".$word['word'].chr(27).chr(27)."[0m".PHP_EOL;
		foreach ($word['path'] as $p) echo sprintf("(%d,%d)",$p[0]+1,$p[1]+1);
		echo PHP_EOL;
		foreach ($this->matrix as $y => $row) {
			foreach ($row as $x => $letter) {
				if (in_array([$x,$y], $word['path'])) {
					echo chr(27)."[0;31m{$letter}".chr(27).chr(27)."[0m";
				} else {
					echo $letter;
				}
				echo " ";
			}
			echo PHP_EOL;
		}
	}

	public function prettyprint_words()
	{
		foreach ($this->words as $w) {
			$this->prettyprint_word($w);
			echo PHP_EOL;
		}
	}

};
