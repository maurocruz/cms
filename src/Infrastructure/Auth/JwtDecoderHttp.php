<?php
namespace Plinct\Cms\Infrastructure\Auth;

class JwtDecoderHttp
{
	/**
	 * @param $token
	 * @return array
	 */
	public function decode($token): array
	{
		$tokenParts = explode('.', $token);
		if (count($tokenParts) !== 3) {
			return ['success' => false,'message'=>'Invalid token','data'=>$tokenParts];
		}
		$payload = base64_decode($tokenParts[1]);
		// Decodifica o Base64Url para JSON
		$jsonArray = json_decode($payload, true);;
		$name = $jsonArray['name'];
		$uid = $jsonArray['uid'];
		$exp = $jsonArray['exp'];
		// cookie
		setcookie('API_TOKEN', $token, $exp, '/');
		// session
		session_start();
		$_SESSION['userLogin']['name'] = $name;
		$_SESSION['userLogin']['uid'] = $uid;
		return ['success' => true, 'message'=>'Valid token','data'=>['token'=>$token]];
	}
}
