<?php 


class Funcoes {


	public static function formatarTamanho($bytes, $decimals = 2)
	{
		if ($bytes <= 0) return '0 B';
		$sizes = ['B', 'KB', 'MB', 'GB', 'TB'];
		$factor = floor((strlen($bytes) - 1) / 3);
		return sprintf("%.{$decimals}f %s", $bytes / pow(1024, $factor), $sizes[$factor]);
	}
	public static function tamPasta($dir)
	{
		$total = 0;

		if (!is_dir($dir)) {
			return 0;
		}

		$itens = scandir($dir);
		foreach ($itens as $item) {
			if ($item === '.' || $item === '..') continue;

			$path = $dir . DIRECTORY_SEPARATOR . $item;
			if (is_dir($path)) {
				$total += self::tamPasta($path); 
			} else {
				$total += filesize($path); 
			}
		}

		return $total;
	}



}


?>