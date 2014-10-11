<?php
/*
 *	------------------------------------------------
 *	basehangul - Human-readable binary encoding
 *	------------------------------------------------
 *	works with EUC-KR. 
 *	Extended Wansung is not needed :D
 *
 *	2014/10/09 Harukana Sora (twitter.com/koreapyj)
 *	Last Change: 2014/10/11
 *
 */

class BaseHangul {
	private $cho, $jung, $jong, $padding, $encoding;

	function __construct($encoding = 'UTF-8') {
		$this->cho	= array(0,2,3,5,6,7,9,11,12,14,15,16,17,18);
		$this->jung	= array(0,2,4,6,8,12,13,17,18,20);
		$this->jong	= array(0,1,4,7,8,16,17,19);
		$this->padding	= chr(0xD7).chr(0x50);
		$this->encoding	= $encoding;
	}

	function encode($data) {
		$input = array(0,0,0,0,0);	// 1.0 byte  array
		$output= array(0,0,0,0);		// 1.5 bytes array
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
				$result.=$index==0?$this->padding:$this->dechangul($output[1]);
				$result.=$index< 2?$this->padding:$this->dechangul($output[2]);
				$result.=$index< 4?$this->padding:$this->dechangul($output[3]);
				unset($input);
			}
		}
		return iconv('UTF-16BE', $this->encoding, $result);
	}

	function decode($data) {
		$data = iconv($this->encoding, 'UTF-16BE', $data);
		$output = array(0, 0, 0, 0, 0);
		$result = '';
		for($i=0;$i<mb_strlen($data, 'UTF-16BE');) {
			$output[0]=$output[1]=$output[2]=$output[3]=$output[4]=-1;
			for($j=$i;$j<$i+4;$j++) {
				$input[$j%4]=$this->hanguldec(mb_substr($data, $j, 1, 'UTF-16BE'));
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
			throw new Exception('BaseHangul: Something went wrong (dechangul: Out of range)');
			return false;
		}
		$code = $this->jong[$num%8]+28*$this->jung[$num/8%10]+28*21*$this->cho[$num/8/10%14]+0xAC00;
		return chr($code >> 8 & 0xFF).chr($code & 0xFF);
	}

	private function hanguldec($hangul) {
		if($hangul == $this->padding)
			return false;
		$code = hexdec(bin2hex($hangul));
		if(	($cho = array_search(intval(($code-0xAC00)/(28*21)), $this->cho)) === false || 
				($jung= array_search(intval((($code-0xAC00)%(28*21))/28), $this->jung)) === false ||
				($jong= array_search(intval(($code-0xAC00)%28), $this->jong)) === false) {
			throw new Exception('BaseHangul: Something went wrong (Not a valid BaseHangul string.)');
			return false;
		}
		return $jong + $jung*8 + $cho*8*10;
	}
}