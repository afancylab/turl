<?php
declare(strict_types=1);
namespace Turl;

use Illuminate\Support\Facades\DB;


class Turl
{


	/**
   * create token
   * 
   * @param string $url
	 * @param int $random_bytes (optional)
   * 
   * @return string token
   * 
   * @since   🌱 1.0.0
   * @version 🌴 1.0.0
   * @author  ✍ Muhammad Mahmudul Hasan Mithu
   */
  public static function create( string $url, int $random_bytes=8): string
  {
		$url = htmlspecialchars(trim($url));
		$token = bin2hex(random_bytes($random_bytes));
		if(self::get_url($token)) $token = $token.'_'.time().'_'.bin2hex(random_bytes(12));
		DB::table('turl_public')->insert([
			'token'=>$token,
			'url'=>$url,
			'counter'=>0,
			'created_at'=>time(),
			'ip'=>(string) request()->ip()
		]);
		return $token ?? '';
  }


  /**
   * get url
   * 
   * @param string $token
	 * @param bool $rawurlencode (optional)
   * 
   * @return false|string
   * 
   * @since   🌱 1.0.0
   * @version 🌴 1.0.0
   * @author  ✍ Muhammad Mahmudul Hasan Mithu
   */
  public static function get_url( string $token, bool $rawurlencode=true): false|string
  {
		$token = htmlspecialchars(trim($token));
		$url = DB::table('turl_public')->where('token', $token)->value('url');
		if($url){
			DB::table('turl_public')->where('token', $token)->increment('counter', 1);
			$url = htmlspecialchars_decode($url);
			if($rawurlencode) $url = rawurlencode($url);
			return $url;
		}
		return false;
  }


  /**
   * delete token record
   * 
   * @param string $token
   * 
   * @return void
   * 
   * @since   🌱 1.0.0
   * @version 🌴 1.0.0
   * @author  ✍ Muhammad Mahmudul Hasan Mithu
   */
  public static function delete( string $token): void
  {
		$token = htmlspecialchars(trim($token));
		DB::table('turl_public')->where('token', $token)->delete();
  }


}
