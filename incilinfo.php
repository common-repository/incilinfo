<?php
/*
Plugin Name: İncil.info Ayetler
Plugin URI: http://incil.info/hakkimizda
Description: Ayetleri bulup link şekline çevirir
Version: trunk
Author: Caleb Maclennan
Author URI: http://alerque.com
License: GPL2
*/
/*  Copyright 2011  Caleb Maclennan  (email : caleb@incil.info)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

function incilinfo_init() {
	if (get_option('incilinfo_aracipucu')) {
		wp_register_script('incilinfo', WP_PLUGIN_URL . '/incilinfo/incilinfo_ayet.js');
		wp_enqueue_script('incilinfo');
		wp_register_style('incilinfo', WP_PLUGIN_URL . '/incilinfo/incilinfo_stil.css');
		wp_enqueue_style('incilinfo');
	}
}


function incilinfo_secenekler () {
	if (!current_user_can('manage_options')) {
		wp_die( __('Bu sayfaya görmek için gerekli izinin yok.') );
	}
	include (dirname(__FILE__).'/incilinfo_secenekler.php');
}

add_action('init', 'incilinfo_init');
add_action('admin_menu', 'incilinfo_secenek_menusu');
add_filter('the_content', '_incilinfo_ara');
add_filter('comment_text', '_incilinfo_ara');

function _incilinfo_karakterdonustur ($string) {
	$trchars = array(
		// Turkish letters
		'ı' => 'i', 'ç' => 'c', 'ş' => 's', 'ö' => 'o', 'ü' => 'u', 'â' => 'a', 'ğ' => 'g',
		'İ' => 'I', 'Ç' => 'C', 'Ş' => 'S', 'Ö' => 'O', 'Ü' => 'U', 'Â' => 'A', 'Ğ' => 'G',

		// Kill off single quotes in book names and urls
		"'" => "", "′" => "", "’" => "", "&#8217;" => ""
	);
	$regex = array('search' => array(), 'replace' => array());
	foreach ($trchars as $char => $tran) {
		$regex['search'][] = "!$char!";
		$regex['replace'][] = $tran;
	}
	$string = preg_replace($regex['search'], $regex['replace'], $string);
	return $string;
}

function _incilinfo_urlcevir ($matches) {
	$url = preg_replace("![']+!", '', $matches[0]);
	$url = preg_replace("!\s+!", '+', $url);
	$url = _incilinfo_karakterdonustur($url);
	$yenipencere = get_option('incilinfo_yenipencere') ? 'target="_blank"' : '';
	return "<a class=\"incil_ayet\" href=\"http://incil.info/arama/$url\" incilReferans=\"$url\" $yenipencere>$matches[0]</a>";
}

function _incilinfo_ara ($metin='') {
	$gormezdengel = array(
			'<a\s+href.*?<\/a>',
			'<pre>.*<\/pre>',
			'<code>.*<\/code>',
			'<(?:[^<>\s]*)(?:\s[^<>]*){0,1}>'
		);
	foreach ($gormezdengel as $k => $v) {
		$gormezdengel[$k] = "(?:$v)";
	}
	$spliton = '/('.join($gormezdengel, '|').')/i';
	$split = preg_split($spliton, $metin, -1, PREG_SPLIT_DELIM_CAPTURE);
	$cevirmis = '';
	foreach ($split as $k => $v) {
		$cevirmis .= (preg_match($spliton, $v)) ? $v : _incilinfo_cevir($v);
	}
	return $cevirmis;
}

function _incilinfo_cevir ($metin='') {
	$kitapregex = "(yarat(?:i|İ|ı)l(?:i|İ|ı)(?:s|ş|Ş)|m(?:i|İ|ı)s(?:i|İ|ı)r(?:'|’|&#8217;)?dan\s+(?:c|ç|Ç)(?:i|İ|ı)k(?:i|İ|ı)(?:s|ş|Ş)|lev(?:i|İ|ı)l(?:i|İ|ı)ler|(?:c|ç|Ç)(?:o|ö|Ö)lde\s+say(?:i|İ|ı)m|yasa(?:'|’|&#8217;)?n(?:i|İ|ı)n\s+tekrar(?:i|İ|ı)|ye(?:s|ş|Ş)u|hakimler|rut|(?:(?:[123]|bir(inci)?|iki(nci)?|(?:u|ü|Ü)c((?:u|ü|Ü)nc(?:u|ü|Ü))?)\.? ?)samuel|(?:(?:[123]|bir(inci)?|iki(nci)?|(?:u|ü|Ü)c((?:u|ü|Ü)nc(?:u|ü|Ü))?)\.? ?)krallar|(?:(?:[123]|bir(inci)?|iki(nci)?|(?:u|ü|Ü)c((?:u|ü|Ü)nc(?:u|ü|Ü))?)\.? ?)tarihler|ezra|nehemya|ester|ey(?:u|ü|Ü)p|mezmurlar|mezmur|s(?:u|ü|Ü)leyman(?:'|’|&#8217;)?(?:i|İ|ı)n\s+(?:o|ö|Ö)zdeyi(?:s|ş|Ş)leri|s(?:u|ü|Ü)leyman(?:'|’|&#8217;)?(?:i|İ|ı)n\s+meselleri|meseller|vaiz|ezgiler\s+ezgisi|ye(?:s|ş|Ş)aya|yeremya|a(?:g|ğ|Ğ)itlar|hezekiel|daniel|hosea|yoel|amos|ovadya|yunus|mika|nahum|habakkuk|sefanya|hagay|zekeriya|malaki|matta|markos|luka|yuhanna|el(?:c|ç|Ç)ilerin\s+(?:i|İ|ı)(?:s|ş|Ş)leri|romal(?:i|İ|ı)lar|(?:(?:[123]|bir(inci)?|iki(nci)?|(?:u|ü|Ü)c((?:u|ü|Ü)nc(?:u|ü|Ü))?)\.? ?)korintliler|(?:(?:[123]|bir(inci)?|iki(nci)?|(?:u|ü|Ü)c((?:u|ü|Ü)nc(?:u|ü|Ü))?)\.? ?)korintoslular|galatyal(?:i|İ|ı)lar|efesliler|filipililer|koloseliler|(?:(?:[123]|bir(inci)?|iki(nci)?|(?:u|ü|Ü)c((?:u|ü|Ü)nc(?:u|ü|Ü))?)\.? ?)selanikliler|(?:(?:[123]|bir(inci)?|iki(nci)?|(?:u|ü|Ü)c((?:u|ü|Ü)nc(?:u|ü|Ü))?)\.? ?)timoteos|titus|filimon|(?:i|İ|ı)braniler|yakup|(?:(?:[123]|bir(inci)?|iki(nci)?|(?:u|ü|Ü)c((?:u|ü|Ü)nc(?:u|ü|Ü))?)\.? ?)petrus|(?:(?:[123]|bir(inci)?|iki(nci)?|(?:u|ü|Ü)c((?:u|ü|Ü)nc(?:u|ü|Ü))?)\.? ?)yuhanna|yahuda|vahiy|cikis|tesniye|tekvin)";
	$ceviri_regex = "(?:YC1999|KM|TC|CAN|TSV|ESV|KJV|TAN|GNT|VUL)";
	
	$ayetregex = "[0-9]*:?[-,0-9]*[0-9]";
	$metin = preg_replace_callback("!($kitapregex\s+$ayetregex(?:\s*\($ceviri_regex\))?)!i", '_incilinfo_urlcevir', $metin);
	return $metin;
}

function incilinfo_secenek_menusu () {
		add_options_page('Ayet linkleri', 'Ayet linkleri', 'manage_options', 'incilinfo_secenek_menusu', 'incilinfo_secenekler');
		add_action('admin_init', 'incilinfo_secenekleri_kayityap');
}

function incilinfo_secenekleri_kayityap () {
	register_setting('incilinfo_secenekler', 'incilinfo_aracipucu');
	register_setting('incilinfo_secenekler', 'incilinfo_yenipencere');
}
