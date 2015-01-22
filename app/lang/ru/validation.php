<?php

return array(

	/*
	|--------------------------------------------------------------------------
	| Validation Language Lines
	|--------------------------------------------------------------------------
	|
	| The following language lines contain the default error messages used by
	| the validator class. Some of these rules have multiple versions such
	| as the size rules. Feel free to tweak each of these messages here.
	|
	*/

	"accepted"             => "Поле :attribute должно быть обязательно принято.",
	"active_url"           => "Поле :attribute не является корректным URL.",
	"after"                => "Поле :attribute должно быть датой после :date.",
	"alpha"                => "Поле :attribute должно содержжать только буквы.",
	"alpha_dash"           => "Поле :attribute должно содержать только буквы, цифры и символы нижнего подчеркивания.",
	"alpha_num"            => "Поле :attribute должно содержать только буквы и цифры.",
	"array"                => "Поле :attribute должно быть таблицей.",
	"before"               => "Поле :attribute должно быть датой до :date.",
	"between"              => array(
		"numeric" => "Поле :attribute должно быть от :min до :max.",
		"file"    => "Поле :attribute должно быть от :min до :max килобайт.",
		"string"  => "Поле :attribute должно быть от :min до :max символов.",
		"array"   => "Поле :attribute должно содержать от :min до :max элементов.",
	),
	"boolean"              => "Поле :attribute должно быть истиной или ложью.",
	"confirmed"            => "Поле :attribute и его подтверждение не совпадают.",
	"date"                 => "Поле :attribute не явялется корректной датой.",
	"date_format"          => "Поле :attribute не соответсвует формату :format.",
	"different"            => "Поля :attribute и :other должны отличаться.",
	"digits"               => "Поле :attribute должно быть :digits значным.",
	"digits_between"       => "Поле :attribute должно быть от :min значного до :max значного.",
	"email"                => "Поле :attribute должно быть корректным адресом E-mail.",
	"exists"               => "Выбранное значение :attribute некорректно.",
	"image"                => "Поле :attribute должно быть изображением.",
	"in"                   => "Выбранное значение :attribute некорректно.",
	"integer"              => "Поле :attribute должно быть целым.",
	"ip"                   => "Поле :attribute должно быть корректным IP-адресом.",
	"max"                  => array(
		"numeric" => "Поле :attribute не должно быть больше :max.",
		"file"    => "Файл :attribute не должен быть больше :max килобайт.",
		"string"  => "Строка :attribute не должна быть длиннее :max символов.",
		"array"   => "Поле :attribute не должно содержать больше :max элементов	.",
	),
	"mimes"                => "Файл :attribute должен быть следуюещего типа: :values.",
	"min"                  => array(
		"numeric" => "Поле :attribute не должно быть не меньше :min.",
		"file"    => "Файл :attribute не должен быть меньше :min килобайт.",
		"string"  => "Строка :attribute не должна быть короче :min символов.",
		"array"   => "Поле :attribute должно содержать хотя бы :min значений.",
	),
	"not_in"               => "Выбранное поле :attribute некорректно.",
	"numeric"              => "Поле :attribute должно быть числом.",
	"regex"                => "Формат поля :attribute некорректен.",
	"required"             => "Поле :attribute обязательно для заполнения.",
	"required_if"          => "Поле :attribute обязательно для заполнения, когда :other принмает значение :value.",
	"required_with"        => "Поле :attribute обязательно для заполнения, когда :values присутствуют.",
	"required_with_all"    => "Поле :attribute обязательно для заполнения, когда :values присутствуют.",
	"required_without"     => "Поле :attribute обязательно для заполнения, когда :values не присутствуют.",
	"required_without_all" => "Поле :attribute обязательно для заполнения, когда ни одно из :values не присутсвует.",
	"same"                 => "Поля :attribute и :other должны совпадать.",
	"size"                 => array(
		"numeric" => ":attribute должен быть :size.",
		"file"    => ":attribute должен быть :size килобайт.",
		"string"  => ":attribute должна быть :size символов.",
		"array"   => ":attribute должен содержать :size элементов.",
	),
	"unique"               => ":attribute уже используется в системе.",
	"url"                  => "Формат :attribute некорректен.",
	"timezone"             => ":attribute должен быть корректным часовым поясом.",
	"alpha_spaces"     => "Поле :attribute должно содержать только буквы и пробелы.",

	/*
	|--------------------------------------------------------------------------
	| Custom Validation Language Lines
	|--------------------------------------------------------------------------
	|
	| Here you may specify custom validation messages for attributes using the
	| convention "attribute.rule" to name the lines. This makes it quick to
	| specify a specific custom language line for a given attribute rule.
	|
	*/

	'custom' => array(
		'attribute-name' => array(
			'rule-name' => 'custom-message',
		),
	),

	/*
	|--------------------------------------------------------------------------
	| Custom Validation Attributes
	|--------------------------------------------------------------------------
	|
	| The following language lines are used to swap attribute place-holders
	| with something more reader friendly such as E-Mail Address instead
	| of "email". This simply helps us make messages a little cleaner.
	|
	*/

	'attributes' => array(),

);
