<?php

# Ignore inline messages (via @)
if ($v->via_bot or $v->update['inline_query'] or $v->update['chosen_inline_result']) die;

# Commands for all chats
if ($v->chat_type == 'private') {
	# Private chat with Bot
	if ($v->command == 'start' or $v->query_data == 'start') {
		if ($bot->configs['database']['status'] and $user['status'] !== 'started') $db->setStatus($v->user_id, 'started');
		$buttons[][] = $bot->createInlineButton($tr->getTranslation('generateButton'), 'services');
		$buttons[][] = $bot->createInlineButton($tr->getTranslation('changeLanguageButton'), 'lang');
		$buttons[][] = $bot->createInlineButton($tr->getTranslation('aboutButton'), 'help');
		$t = $tr->getTranslation('startMessage');
		if ($v->query_id) {
			$bot->editText($v->chat_id, $v->message_id, $t, $buttons, 'def', 0);
			$bot->answerCBQ($v->query_id);
		} else {
			$bot->sendMessage($v->chat_id, $t, $buttons, 'def', 0);
		}
	} elseif ($v->query_data == 'services') {
		$t = $tr->getTranslation('generatorMessage');
		$gen = new KeyGenerator;
		foreach ($gen->types as $typeID => $typeName) {
			$buttons[][] = $bot->createInlineButton($typeName, 'generate-' . $typeID);
		}
		$buttons[][] = $bot->createInlineButton($tr->getTranslation('backButton'), 'start');
		$bot->editText($v->chat_id, $v->message_id, $t, $buttons, 'def', 0);
		$bot->answerCBQ($v->query_id);
	} elseif (strpos($v->query_data, 'generate-') === 0) {
		$gen = new KeyGenerator;
		$e = explode('-', $v->query_data);
		$key = $gen->generate($e[1]);
		if ($key['ok']) {
			$t = $tr->getTranslation('generatedCode', [$gen->types[$e[1]], $bot->specialchars($key['result'])]);
			$buttons[][] = $bot->createInlineButton($tr->getTranslation('reGenerateButton'), $v->query_data);
			$buttons[][] = $bot->createInlineButton($tr->getTranslation('backButton'), 'services');
			$bot->editText($v->chat_id, $v->message_id, $t, $buttons, 'def', 0);
			$bot->answerCBQ($v->query_id);
		} else {
			$bot->answerCBQ($v->query_id, $tr->getTranslation('processFailed'), 1);
		}
	} elseif ($v->command == 'help' or $v->query_data == 'help') {
		$buttons[][] = $bot->createInlineButton($tr->getTranslation('backButton'), 'start');
		$t = $tr->getTranslation('aboutMessage', [explode('-', phpversion(), 2)[0]]);
		if ($v->query_id) {
			$bot->editText($v->chat_id, $v->message_id, $t, $buttons, 'def', 0);
			$bot->answerCBQ($v->query_id);
		} else {
			$bot->sendMessage($v->chat_id, $t, $buttons, 'def', 0);
		}
	} elseif ($v->command == 'lang' or $v->query_data == 'lang' or strpos($v->query_data, 'changeLanguage-') === 0) {
		$langnames = [
			'de' => '🇩🇪 Deutsch',
			'en' => '🇬🇧 English',
			'es' => '🇪🇸 Español',
			'fr' => '🇫🇷 Français',
			'ms' => '🇲🇾 Malay',
			'it' => '🇮🇹 Italiano',
			'nap' => '🇮🇹 Napoletano'
		];
		if (strpos($v->query_data, 'changeLanguage-') === 0) {
			$select = str_replace('changeLanguage-', '', $v->query_data);
			if (in_array($select, array_keys($langnames))) {
				$tr->setLanguage($select);
				$user['lang'] = $select;
				$db->query('UPDATE users SET lang = ? WHERE id = ?', [$user['lang'], $user['id']]);
			}
		}
		$langnames[$user['lang']] .= ' ✅';
		$t = '🔡 Select your language';
		$formenu = 2;
		$mcount = 0;
		foreach ($langnames as $lang_code => $name) {
			if (isset($buttons[$mcount]) and count($buttons[$mcount]) >= $formenu) $mcount += 1;
			$buttons[$mcount][] = $bot->createInlineButton($name, 'changeLanguage-' . $lang_code);
		}
		$buttons[][] = $bot->createInlineButton($tr->getTranslation('backButton'), 'start');
		if ($v->query_id) {
			$bot->editText($v->chat_id, $v->message_id, $t, $buttons);
			$bot->answerCBQ($v->query_id);
		} else {
			$bot->sendMessage($v->chat_id, $t, $buttons);
		}
	}
}

?>