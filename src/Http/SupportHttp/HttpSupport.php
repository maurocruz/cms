<?php
namespace Plinct\Cms\Http\SupportHttp;

class HttpSupport
{
	public static function decodeCript(string $string): false|string
	{
		return gzinflate(base64_decode($string));
	}

	public static function encodeCript(string $string): false|string
	{
		return base64_encode(gzdeflate($string));
	}
}
