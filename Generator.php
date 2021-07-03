<?php

class KeyGenerator {
	public $types = [
		0	=> 'Telegram Bot',
		1	=> 'Telegram ID',
		2	=> 'Steam Key',
		3	=> 'Minecraft UUID',
		4	=> 'Pokémon TCG Online',
		5	=> 'Password'
	];
	
	public function generate ($typeID) {
		if (is_numeric($typeID) and in_array($typeID, array_keys($this->types))) {
			if ($typeID == 0) {
				return ['ok' => 1, 'result' => $this->TelegramBot()];
			} elseif ($typeID == 1) {
				return ['ok' => 1, 'result' => $this->TelegramID()];
			} elseif ($typeID == 2) {
				return ['ok' => 1, 'result' => $this->SteamKey()];
			} elseif ($typeID == 3) {
				return ['ok' => 1, 'result' => $this->MinecraftUUID()];
			} elseif ($typeID == 4) {
				return ['ok' => 1, 'result' => $this->PokemonTCGO()];
			} elseif ($typeID == 5) {
				return ['ok' => 1, 'result' => $this->PasswordGen()];
			} else {
				return ['ok' => 0, 'error' => 'Type ID not found'];
			}
		} else {
			return ['ok' => 0, 'error' => 'Type ID not found'];
		}
	}
	
	private function TelegramBot () {
		$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz';
		$key = substr(str_shuffle($chars), 0, 35);
		$r = rand(10, 35);
		if ($r < 20) {
			$key[$r] = '-';
		} else {
			$key[$r] = '_';
		}
		return $this->TelegramID() . ':' . $key;
	}
	
	private function TelegramID () {
		return rand(97777777, 2999999999);
	}
	
	private function SteamKey () {
		$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ123456789';
		return substr(str_shuffle($chars), 0, 5) . '-' . substr(str_shuffle($chars), 0, 5) . '-' . substr(str_shuffle($chars), 0, 5);
	}
	
	private function MinecraftUUID () {
		$chars = 'abcdef012345678901234567890123456789';
		return substr(str_shuffle($chars), 0, 8) . '-' . substr(str_shuffle($chars), 0, 4) . '-' . substr(str_shuffle($chars), 0, 4) . '-' . substr(str_shuffle($chars), 0, 4) . '-' . substr(str_shuffle($chars), 0, 12);
	}
	
	private function PokemonTCGO () {
		$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
		return substr(str_shuffle($chars), 0, 3) . '-' . substr(str_shuffle($chars), 0, 4) . '-' . substr(str_shuffle($chars), 0, 3) . '-' . substr(str_shuffle($chars), 0, 3);
	}
	
	private function PasswordGen () {
		$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
		$chars .= strtolower($chars);
		$chars .= $chars . '@#*/&-';
		return substr(str_shuffle($chars), 0, 64);
	}
	
}

?>