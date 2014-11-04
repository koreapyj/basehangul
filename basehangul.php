<?php
/*
 *	------------------------------------------------
 *	basehangul - Human-readable binary encoding
 *	------------------------------------------------
 *	works with EUC-KR. 
 *	Extended Wansung is not needed :D
 *
 *	2014/10/09 Harukana Sora (twitter.com/koreapyj)
 *	Last Change: 2014/11/05
 *
 */

class BaseHangul {
	private $padding, $charset;

	function __construct($charset = 'UTF-8') {
		$this->padding	= chr(0xC8).chr(0xE5);
		$this->charset	= $charset;
	}

	function encode($data) {
		$input = array(0,0,0,0,0);
		$output= array(0,0,0,0);
		$result = '';
		$len = strlen($data);
		for($i=0;$i<$len;$i++) {
			$index=$i%5;
			$input[$index] = ord(substr($data, $i, 1));

			if($index==4 || $i==$len-1) {
				$output[0] = (($input[0] & 0xFF) << 2) | (($input[1] & 0xC0) >> 6);
				$output[1] = (($input[1] & 0x3F) << 4) | (($input[2] & 0xF0) >> 4);
				$output[2] = (($input[2] & 0x0F) << 6) | (($input[3] & 0xFC) >> 2);
				$output[3] = (($input[3] & 0x03) << 8) | (($input[4] & 0xFF)     );

				$result.=$this->dechangul($output[0]);
				$result.=!$output[1] && $index <= 2?$this->padding:$this->dechangul($output[1]);
				$result.=!$output[2] && $index <= 3?$this->padding:$this->dechangul($output[2]);
				$result.=!$output[3] && $index <= 4?$this->padding:$this->dechangul($output[3]);
				unset($input);
			}
		}
		return iconv('EUC-KR', $this->charset, $result);
	}

	function decode($data) {
		$data = iconv($this->charset, 'EUC-KR', $data);
		$output = array(0, 0, 0, 0, 0);
		$result = '';
		for($i=0;$i<mb_strlen($data, 'EUC-KR');) {
			$output[0]=$output[1]=$output[2]=$output[3]=$output[4]=-1;
			for($j=$i;$j<$i+4;$j++) {
				$input[$j%4]=$this->hanguldec(mb_substr($data, $j, 1, 'EUC-KR'));
			}
			$i=$j;
			switch(0) {
				case 0:
					$output[0] = ($input[0] & 0x3FC) >> 2;
					$output[1] = (($input[0] & 0x3) << 6) | (($input[1] & 0x3F0) >> 4);
					if($input[1]===false) {
						if($output[1] == 0)
							$output[1] = -1;
						break;
					}
					$output[2] = (($input[1] & 0xF) << 4) | (($input[2] & 0x3C0) >> 6);
					if($input[2]===false) {
						if($output[2] == 0)
							$output[2] = -1;
						break;
					}
					$output[3] = (($input[2] & 0x3F) << 2) | (($input[3] & 0x300) >> 8);
					if($input[3]===false) {
						if($output[3] == 0)
							$output[3] = -1;
						break;
					}
					$output[4] = ($input[3] & 0xFF);
			}
			for($j=0;$j<5;$j++) {
				if($output[$j]==-1)
					continue;
				$result.=chr($output[$j]);
			}
		}
		return $result;
	}

	private function dechangul($num) {
		if($num>1023) {
			throw new Exception('Out of range');
			return false;
		}
		return chr($num/0x5E+0xB0).chr($num%0x5E+0xA1);
	}

	private function hanguldec($hangul) {
		if($hangul == $this->padding)
			return false;
		$code = hexdec(bin2hex($hangul));
		$num = ($code & 0xFF) - 0xA1 + (($code >> 8 & 0xFF)-0xB0)*0x5E;
		if($num > 1023 || $num < 0) {
			throw new Exception('Not a valid BaseHangul string');
			return false;
		}
		return $num;
	}
}